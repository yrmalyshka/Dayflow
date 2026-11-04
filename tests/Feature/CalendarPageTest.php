<?php

namespace Tests\Feature;

use App\Services\GoogleCalendarService;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class CalendarPageTest extends TestCase
{
    public function test_google_authorization_url_includes_calendar_scope_and_state(): void
    {
        config([
            'services.google.client_id' => 'test-client-id',
            'services.google.client_secret' => 'test-client-secret',
            'services.google.redirect' => 'https://dayflow.test/auth/google/callback',
        ]);

        $authorizationUrl = app(GoogleCalendarService::class)->authorizationUrl('csrf-state');
        parse_str(parse_url($authorizationUrl, PHP_URL_QUERY), $query);

        $this->assertSame('accounts.google.com', parse_url($authorizationUrl, PHP_URL_HOST));
        $this->assertSame('csrf-state', $query['state']);
        $this->assertStringContainsString(
            'https://www.googleapis.com/auth/calendar.events',
            $query['scope'],
        );
    }

    public function test_calendar_route_renders_the_calendar_page(): void
    {
        $response = $this->get('/calendar?username=Tester');

        $response->assertOk();
        $response->assertViewIs('calender');
        $response->assertSee('My Calendar');
        $response->assertSee('Connect Google');
        $response->assertDontSee('UX Design Practice');
        $response->assertSee('aria-controls="primary-navigation"', false);
        $response->assertSee('aria-expanded="false"', false);
        $response->assertSee('class="desktop-nav-toggle"', false);
        $response->assertSee('aria-label="Collapse navigation"', false);
        $response->assertSee('id="primary-navigation"', false);
    }

    public function test_calendar_route_shows_the_connected_google_account(): void
    {
        $response = $this->withSession([
            'google_calendar_token' => $this->encryptedToken([
                'access_token' => 'current-access-token',
            ]),
        ])->get('/calendar');
        $response->assertOk();
        $response->assertSee('Google Calendar connected');
        $response->assertSee('Google Calendar connected');
    }

    public function test_calendar_events_require_a_google_connection(): void
    {
        $response = $this->getJson('/api/calendar/events?start=2026-11-01T00:00:00Z&end=2026-12-01T00:00:00Z');

        $response->assertUnauthorized();
        $response->assertJsonPath(
            'message',
            'Hubungkan kembali akun Google Calendar untuk melanjutkan.',
        );
    }

    public function test_calendar_events_are_loaded_from_google(): void
    {
        $token = ['access_token' => 'current-access-token'];
        $this->mock(GoogleCalendarService::class)
            ->shouldReceive('listEvents')
            ->once()
            ->with(
                $token,
                '2026-11-01T00:00:00Z',
                '2026-12-01T00:00:00Z',
            )
            ->andReturn([
                'events' => [
                    [
                        'id' => 'google-event-id',
                        'title' => 'Planning session',
                        'start' => '2026-11-04T09:00:00+07:00',
                        'end' => '2026-11-04T10:00:00+07:00',
                        'allDay' => false,
                        'extendedProps' => [
                            'category' => 'Work',
                            'description' => '',
                        ],
                    ],
                ],
                'token' => $token,
            ]);

        $response = $this->withSession([
            'google_calendar_token' => $this->encryptedToken($token),
        ])->getJson('/api/calendar/events?start=2026-11-01T00:00:00Z&end=2026-12-01T00:00:00Z');

        $response->assertOk();
        $response->assertJsonPath('events.0.title', 'Planning session');
        $response->assertJsonPath('events.0.extendedProps.category', 'Work');
    }

    public function test_new_all_day_events_are_created_in_google_calendar(): void
    {
        $token = ['access_token' => 'current-access-token'];
        $event = [
            'id' => 'google-event-id',
            'title' => 'Personal day',
            'start' => '2026-11-08',
            'end' => '2026-11-09',
            'allDay' => true,
            'extendedProps' => [
                'category' => 'Personal',
                'description' => 'A day off',
            ],
        ];

        $this->mock(GoogleCalendarService::class)
            ->shouldReceive('createEvent')
            ->once()
            ->with($token, [
                'title' => 'Personal day',
                'start_date' => '2026-11-08',
                'end_date' => '2026-11-08',
                'start_time' => null,
                'end_time' => null,
                'all_day' => true,
                'category' => 'Personal',
                'description' => 'A day off',
            ])
            ->andReturn([
                'event' => $event,
                'token' => $token,
            ]);

        $response = $this->withSession([
            'google_calendar_token' => $this->encryptedToken($token),
        ])->postJson('/api/calendar/events', [
            'title' => 'Personal day',
            'start_date' => '2026-11-08',
            'end_date' => '2026-11-08',
            'start_time' => null,
            'end_time' => null,
            'all_day' => true,
            'category' => 'Personal',
            'description' => 'A day off',
        ]);

        $response->assertCreated();
        $response->assertJsonPath('event.id', 'google-event-id');
        $response->assertJsonPath('event.allDay', true);
    }

    public function test_existing_events_are_updated_in_google_calendar(): void
    {
        $token = ['access_token' => 'current-access-token'];
        $event = [
            'id' => 'google-event-id',
            'title' => 'Updated meeting',
            'start' => '2026-11-08T10:00:00+07:00',
            'end' => '2026-11-08T11:00:00+07:00',
            'allDay' => false,
            'extendedProps' => [
                'category' => 'Work',
                'description' => 'Updated notes',
            ],
        ];

        $this->mock(GoogleCalendarService::class)
            ->shouldReceive('updateEvent')
            ->once()
            ->with($token, 'google-event-id', [
                'title' => 'Updated meeting',
                'start_date' => '2026-11-08',
                'end_date' => '2026-11-08',
                'start_time' => '10:00',
                'end_time' => '11:00',
                'all_day' => false,
                'category' => 'Work',
                'description' => 'Updated notes',
            ])
            ->andReturn(['event' => $event, 'token' => $token]);

        $response = $this->withSession([
            'google_calendar_token' => $this->encryptedToken($token),
        ])->patchJson('/api/calendar/events/google-event-id', [
            'title' => 'Updated meeting',
            'start_date' => '2026-11-08',
            'end_date' => '2026-11-08',
            'start_time' => '10:00',
            'end_time' => '11:00',
            'all_day' => false,
            'category' => 'Work',
            'description' => 'Updated notes',
        ]);

        $response->assertOk();
        $response->assertJsonPath('event.title', 'Updated meeting');
    }

    public function test_timed_events_are_rejected_when_the_end_is_not_after_the_start(): void
    {
        $this->mock(GoogleCalendarService::class)
            ->shouldNotReceive('createEvent');

        $response = $this->withSession([
            'google_calendar_token' => $this->encryptedToken([
                'access_token' => 'current-access-token',
            ]),
        ])->postJson('/api/calendar/events', [
            'title' => 'Invalid meeting',
            'start_date' => '2026-11-08',
            'end_date' => '2026-11-08',
            'start_time' => '12:00',
            'end_time' => '11:00',
            'all_day' => false,
            'category' => 'Work',
            'description' => '',
        ]);

        $response->assertUnprocessable();
        $response->assertJsonValidationErrors('end_time');
    }

    public function test_calendar_events_can_be_deleted_from_google(): void
    {
        $token = ['access_token' => 'current-access-token'];
        $this->mock(GoogleCalendarService::class)
            ->shouldReceive('deleteEvent')
            ->once()
            ->with($token, 'google-event-id')
            ->andReturn(['token' => $token]);

        $response = $this->withSession([
            'google_calendar_token' => $this->encryptedToken($token),
        ])->deleteJson('/api/calendar/events/google-event-id');

        $response->assertNoContent();
    }

    public function test_google_callback_rejects_an_invalid_oauth_state(): void
    {
        $this->mock(GoogleCalendarService::class)
            ->shouldNotReceive('exchangeCode');

        $response = $this->get('/auth/google/callback?state=invalid&code=authorization-code');

        $response->assertRedirect(route('calendar', ['username' => 'User']));
        $response->assertSessionHas(
            'error',
            'Permintaan koneksi Google tidak valid. Silakan coba lagi.',
        );
    }

    public function test_google_account_can_be_connected_with_a_valid_oauth_state(): void
    {
        $oauthState = null;
        $token = [
            'access_token' => 'current-access-token',
            'refresh_token' => 'refresh-token',
        ];

        $googleCalendar = $this->mock(GoogleCalendarService::class);
        $googleCalendar->shouldReceive('authorizationUrl')
            ->once()
            ->andReturnUsing(function (string $state) use (&$oauthState): string {
                $oauthState = $state;

                return 'https://accounts.google.com/o/oauth2/auth?state='.$state;
            });
        $googleCalendar->shouldReceive('exchangeCode')
            ->once()
            ->with('authorization-code', null)
            ->andReturn($token);

        $redirect = $this->get('/auth/google/redirect?username=Tester');
        $redirect->assertRedirect('https://accounts.google.com/o/oauth2/auth?state='.$oauthState);

        $callback = $this->get('/auth/google/callback?state='.$oauthState.'&code=authorization-code');

        $callback->assertRedirect(route('calendar', ['username' => 'Tester']));
        $callback->assertSessionHas('google_calendar_token');
    }

    public function test_google_connection_reports_missing_oauth_configuration(): void
    {
        config([
            'services.google.client_id' => null,
            'services.google.client_secret' => null,
            'services.google.redirect' => null,
        ]);

        $response = $this->get('/auth/google/redirect');

        $response->assertRedirect(route('calendar', ['username' => 'User']));
        $response->assertSessionHas(
            'error',
            'Kredensial Google Calendar belum dikonfigurasi.',
        );
    }

    /**
     * @param  array<string, mixed>  $token
     */
    private function encryptedToken(array $token): string
    {
        return Crypt::encryptString(json_encode($token, JSON_THROW_ON_ERROR));
    }
}
