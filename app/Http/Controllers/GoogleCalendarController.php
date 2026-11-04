<?php

namespace App\Http\Controllers;

use App\Exceptions\GoogleCalendarConfigurationException;
use App\Exceptions\GoogleCalendarTokenException;
use App\Services\GoogleCalendarService;
use Closure;
use DateTimeImmutable;
use DateTimeZone;
use Google\Service\Exception as GoogleServiceException;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use JsonException;

class GoogleCalendarController extends Controller
{
    public function __construct(private GoogleCalendarService $googleCalendar) {}

    public function redirect(Request $request): RedirectResponse
    {
        $state = Str::random(40);
        $request->session()->put('google_oauth_state', $state);
        $request->session()->put('calendar_username', $request->query('username', 'User'));

        try {
            return redirect()->away($this->googleCalendar->authorizationUrl($state));
        } catch (GoogleCalendarConfigurationException $exception) {
            report($exception);

            return $this->calendarRedirect($request)
                ->with('error', 'Kredensial Google Calendar belum dikonfigurasi.');
        }
    }

    public function callback(Request $request): RedirectResponse
    {
        $expectedState = $request->session()->pull('google_oauth_state');
        $returnedState = $request->query('state');

        if (
            ! is_string($expectedState)
            || ! is_string($returnedState)
            || ! hash_equals($expectedState, $returnedState)
        ) {
            return $this->calendarRedirect($request)
                ->with('error', 'Permintaan koneksi Google tidak valid. Silakan coba lagi.');
        }

        if ($request->filled('error')) {
            return $this->calendarRedirect($request)
                ->with('error', 'Koneksi Google Calendar dibatalkan.');
        }

        $validated = $request->validate([
            'code' => ['required', 'string'],
        ]);

        try {
            $token = $this->googleCalendar->exchangeCode(
                $validated['code'],
                $this->googleToken($request),
            );
        } catch (
            GoogleCalendarConfigurationException
            |GoogleCalendarTokenException
            |GoogleServiceException $exception
        ) {
            report($exception);

            return $this->calendarRedirect($request)
                ->with('error', 'Google Calendar gagal dihubungkan. Silakan coba lagi.');
        }

        $request->session()->regenerate();
        $this->storeGoogleToken($request, $token);

        return $this->calendarRedirect($request)
            ->with('success', 'Google Calendar berhasil dihubungkan.');
    }

    public function disconnect(Request $request): RedirectResponse
    {
        $request->session()->forget(['google_calendar_token', 'google_oauth_state']);

        return $this->calendarRedirect($request)
            ->with('success', 'Google Calendar berhasil diputuskan.');
    }

    public function events(Request $request): JsonResponse|Response
    {
        $range = $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after:start'],
        ]);

        return $this->withGoogleToken($request, function (array $token) use ($range): array {
            $result = $this->googleCalendar->listEvents(
                $token,
                $range['start'],
                $range['end'],
            );

            return [
                'data' => ['events' => $result['events']],
                'token' => $result['token'],
            ];
        });
    }

    public function store(Request $request): JsonResponse|Response
    {
        $data = $this->validatedEvent($request);

        return $this->withGoogleToken($request, function (array $token) use ($data): array {
            $result = $this->googleCalendar->createEvent($token, $data);

            return [
                'data' => ['event' => $result['event']],
                'token' => $result['token'],
            ];
        }, 201);
    }

    public function update(Request $request, string $eventId): JsonResponse|Response
    {
        $data = $this->validatedEvent($request);

        return $this->withGoogleToken($request, function (array $token) use ($data, $eventId): array {
            $result = $this->googleCalendar->updateEvent($token, $eventId, $data);

            return [
                'data' => ['event' => $result['event']],
                'token' => $result['token'],
            ];
        });
    }

    public function destroy(Request $request, string $eventId): JsonResponse|Response
    {
        return $this->withGoogleToken($request, function (array $token) use ($eventId): array {
            $result = $this->googleCalendar->deleteEvent($token, $eventId);

            return [
                'data' => [],
                'token' => $result['token'],
            ];
        }, 204);
    }

    /**
     * @return array<string, mixed>
     */
    private function validatedEvent(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'start_date' => ['required', 'date_format:Y-m-d'],
            'end_date' => ['required', 'date_format:Y-m-d', 'after_or_equal:start_date'],
            'start_time' => ['nullable', 'required_if:all_day,false', 'date_format:H:i'],
            'end_time' => ['nullable', 'required_if:all_day,false', 'date_format:H:i'],
            'all_day' => ['required', 'boolean'],
            'category' => ['required', 'in:Personal,Work,Study'],
            'description' => ['nullable', 'string', 'max:5000'],
        ]);

        $data['all_day'] = (bool) $data['all_day'];

        if (! $data['all_day']) {
            $timezone = new DateTimeZone(config('app.timezone'));
            $start = new DateTimeImmutable(
                "{$data['start_date']} {$data['start_time']}",
                $timezone,
            );
            $end = new DateTimeImmutable(
                "{$data['end_date']} {$data['end_time']}",
                $timezone,
            );

            if ($end <= $start) {
                throw ValidationException::withMessages([
                    'end_time' => 'Waktu selesai harus setelah waktu mulai.',
                ]);
            }
        }

        return $data;
    }

    private function withGoogleToken(
        Request $request,
        Closure $operation,
        int $successStatus = 200,
    ): JsonResponse|Response {
        $token = $this->googleToken($request);

        if ($token === null) {
            return response()->json([
                'message' => 'Hubungkan kembali akun Google Calendar untuk melanjutkan.',
            ], 401);
        }

        try {
            $result = $operation($token);
            $this->storeGoogleToken($request, $result['token']);
        } catch (GoogleCalendarTokenException) {
            $request->session()->forget('google_calendar_token');

            return response()->json([
                'message' => 'Sesi Google Calendar berakhir. Hubungkan kembali akun Google Anda.',
            ], 401);
        } catch (GoogleCalendarConfigurationException $exception) {
            report($exception);

            return response()->json([
                'message' => 'Integrasi Google Calendar belum dikonfigurasi.',
            ], 503);
        } catch (GoogleServiceException $exception) {
            report($exception);

            if ($exception->getCode() === 401) {
                $request->session()->forget('google_calendar_token');

                return response()->json([
                    'message' => 'Sesi Google Calendar berakhir. Hubungkan kembali akun Google Anda.',
                ], 401);
            }

            if ($exception->getCode() === 403) {
                return response()->json([
                    'message' => 'Google menolak akses kalender. Hubungkan ulang akun dan setujui izinnya.',
                ], 403);
            }

            if ($exception->getCode() === 404) {
                return response()->json([
                    'message' => 'Event Google Calendar ini tidak ditemukan.',
                ], 404);
            }

            return response()->json([
                'message' => 'Google Calendar tidak dapat diakses sekarang. Silakan coba lagi.',
            ], 502);
        }

        if ($successStatus === 204) {
            return response()->noContent();
        }

        return response()->json($result['data'], $successStatus);
    }

    /**
     * @return array<string, mixed>|null
     */
    private function googleToken(Request $request): ?array
    {
        $encryptedToken = $request->session()->get('google_calendar_token');

        if (! is_string($encryptedToken)) {
            return null;
        }

        try {
            $token = json_decode(
                Crypt::decryptString($encryptedToken),
                true,
                flags: JSON_THROW_ON_ERROR,
            );
        } catch (DecryptException|JsonException) {
            $request->session()->forget('google_calendar_token');

            return null;
        }

        if (! is_array($token) || ! isset($token['access_token'])) {
            $request->session()->forget('google_calendar_token');

            return null;
        }

        return $token;
    }

    /**
     * @param  array<string, mixed>  $token
     */
    private function storeGoogleToken(Request $request, array $token): void
    {
        $request->session()->put(
            'google_calendar_token',
            Crypt::encryptString(json_encode($token, JSON_THROW_ON_ERROR)),
        );
    }

    private function calendarRedirect(Request $request): RedirectResponse
    {
        return redirect()->route('calendar', [
            'username' => $request->session()->get('calendar_username', 'User'),
        ]);
    }
}
