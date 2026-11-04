<?php

namespace App\Services;

use App\Exceptions\GoogleCalendarConfigurationException;
use App\Exceptions\GoogleCalendarTokenException;
use DateTimeImmutable;
use DateTimeInterface;
use DateTimeZone;
use Google\Client;
use Google\Service\Calendar as GoogleCalendar;
use Google\Service\Calendar\Event;
use Google\Service\Calendar\EventDateTime;
use Google\Service\Calendar\EventExtendedProperties;
use Google\Service\Exception as GoogleServiceException;

class GoogleCalendarService
{
    public function authorizationUrl(string $state): string
    {
        $client = $this->newClient();
        $client->setState($state);

        return $client->createAuthUrl();
    }

    /**
     * @param  array<string, mixed>|null  $existingToken
     * @return array<string, mixed>
     */
    public function exchangeCode(string $code, ?array $existingToken = null): array
    {
        $token = $this->newClient()->fetchAccessTokenWithAuthCode($code);

        if (! isset($token['access_token'])) {
            throw new GoogleCalendarTokenException('Google did not return an access token.');
        }

        if (! isset($token['refresh_token']) && isset($existingToken['refresh_token'])) {
            $token['refresh_token'] = $existingToken['refresh_token'];
        }

        return $token;
    }

    /**
     * @param  array<string, mixed>  $token
     * @return array{events: list<array<string, mixed>>, token: array<string, mixed>}
     *
     * @throws GoogleServiceException
     */
    public function listEvents(array $token, string $start, string $end): array
    {
        [$calendar, $updatedToken] = $this->calendarWithToken($token);
        $events = [];
        $pageToken = null;

        do {
            $options = [
                'timeMin' => $start,
                'timeMax' => $end,
                'singleEvents' => true,
                'orderBy' => 'startTime',
                'maxResults' => 2500,
            ];

            if ($pageToken !== null) {
                $options['pageToken'] = $pageToken;
            }

            $page = $calendar->events->listEvents('primary', $options);

            foreach ($page->getItems() ?? [] as $event) {
                $events[] = $this->formatEvent($event);
            }

            $pageToken = $page->getNextPageToken();
        } while ($pageToken !== null);

        return [
            'events' => $events,
            'token' => $updatedToken,
        ];
    }

    /**
     * @param  array<string, mixed>  $token
     * @param  array<string, mixed>  $data
     * @return array{event: array<string, mixed>, token: array<string, mixed>}
     *
     * @throws GoogleServiceException
     */
    public function createEvent(array $token, array $data): array
    {
        [$calendar, $updatedToken] = $this->calendarWithToken($token);
        $event = $calendar->events->insert('primary', $this->makeEvent($data));

        return [
            'event' => $this->formatEvent($event),
            'token' => $updatedToken,
        ];
    }

    /**
     * @param  array<string, mixed>  $token
     * @param  array<string, mixed>  $data
     * @return array{event: array<string, mixed>, token: array<string, mixed>}
     *
     * @throws GoogleServiceException
     */
    public function updateEvent(array $token, string $eventId, array $data): array
    {
        [$calendar, $updatedToken] = $this->calendarWithToken($token);
        $event = $calendar->events->patch('primary', $eventId, $this->makeEvent($data));

        return [
            'event' => $this->formatEvent($event),
            'token' => $updatedToken,
        ];
    }

    /**
     * @param  array<string, mixed>  $token
     * @return array{token: array<string, mixed>}
     *
     * @throws GoogleServiceException
     */
    public function deleteEvent(array $token, string $eventId): array
    {
        [$calendar, $updatedToken] = $this->calendarWithToken($token);
        $calendar->events->delete('primary', $eventId);

        return ['token' => $updatedToken];
    }

    private function newClient(): Client
    {
        $clientId = config('services.google.client_id');
        $clientSecret = config('services.google.client_secret');
        $redirectUri = config('services.google.redirect');

        if (
            ! is_string($clientId) || trim($clientId) === ''
            || ! is_string($clientSecret) || trim($clientSecret) === ''
            || ! is_string($redirectUri) || trim($redirectUri) === ''
        ) {
            throw new GoogleCalendarConfigurationException(
                'Google Calendar OAuth credentials are not configured.',
            );
        }

        $client = new Client;
        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->setRedirectUri($redirectUri);
        $client->setScopes([
            'https://www.googleapis.com/auth/calendar.events',
        ]);
        $client->setAccessType('offline');
        $client->setPrompt('consent select_account');

        return $client;
    }

    /**
     * @param  array<string, mixed>  $token
     * @return array{0: GoogleCalendar, 1: array<string, mixed>}
     */
    private function calendarWithToken(array $token): array
    {
        $client = $this->newClient();
        $client->setAccessToken($token);

        if ($client->isAccessTokenExpired()) {
            $refreshToken = $token['refresh_token'] ?? null;

            if (! is_string($refreshToken) || $refreshToken === '') {
                throw new GoogleCalendarTokenException('The Google access token has expired.');
            }

            $refreshedToken = $client->fetchAccessTokenWithRefreshToken($refreshToken);

            if (! isset($refreshedToken['access_token'])) {
                throw new GoogleCalendarTokenException('The Google access token could not be refreshed.');
            }
        }

        return [
            new GoogleCalendar($client),
            $client->getAccessToken(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     */
    private function makeEvent(array $data): Event
    {
        $event = new Event;
        $event->setSummary($data['title']);
        $event->setDescription($data['description'] ?? '');
        $event->setExtendedProperties(new EventExtendedProperties([
            'private' => ['dayflowCategory' => $data['category']],
        ]));

        if ($data['all_day']) {
            $timezone = new DateTimeZone(config('app.timezone'));
            $exclusiveEnd = (new DateTimeImmutable($data['end_date'], $timezone))
                ->modify('+1 day')
                ->format('Y-m-d');

            $event->setStart(new EventDateTime(['date' => $data['start_date']]));
            $event->setEnd(new EventDateTime(['date' => $exclusiveEnd]));

            return $event;
        }

        $timezone = new DateTimeZone(config('app.timezone'));
        $start = new DateTimeImmutable(
            "{$data['start_date']} {$data['start_time']}",
            $timezone,
        );
        $end = new DateTimeImmutable(
            "{$data['end_date']} {$data['end_time']}",
            $timezone,
        );

        $event->setStart(new EventDateTime([
            'dateTime' => $start->format(DateTimeInterface::RFC3339),
            'timeZone' => $timezone->getName(),
        ]));
        $event->setEnd(new EventDateTime([
            'dateTime' => $end->format(DateTimeInterface::RFC3339),
            'timeZone' => $timezone->getName(),
        ]));

        return $event;
    }

    /**
     * @return array<string, mixed>
     */
    private function formatEvent(Event $event): array
    {
        $start = $event->getStart();
        $end = $event->getEnd();
        $privateProperties = $event->getExtendedProperties()?->getPrivate() ?? [];
        $category = $privateProperties['dayflowCategory'] ?? 'Personal';

        if (! in_array($category, ['Personal', 'Work', 'Study'], true)) {
            $category = 'Personal';
        }

        return [
            'id' => $event->getId(),
            'title' => $event->getSummary() ?? '(Untitled event)',
            'start' => $start?->getDateTime() ?? $start?->getDate(),
            'end' => $end?->getDateTime() ?? $end?->getDate(),
            'allDay' => $start?->getDate() !== null,
            'extendedProps' => [
                'category' => $category,
                'description' => $event->getDescription() ?? '',
            ],
        ];
    }
}
