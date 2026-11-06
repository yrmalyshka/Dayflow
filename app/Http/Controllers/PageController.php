<?php

namespace App\Http\Controllers;

use DateTimeImmutable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PageController extends Controller
{
    // Data aktivitas sementara
    private function getTasks(): array
    {
        return [
            [
                'id' => 1,
                'title' => 'Finish UI/UX Assignment',
                'description' => 'Complete the mobile app interface design.',
                'category' => 'Study',
                'priority' => 'High',
                'status' => 'In Progress',
                'date' => '2026-11-03',
                'time' => '09:00',
            ],
            [
                'id' => 2,
                'title' => 'Team Project Meeting',
                'description' => 'Discuss project progress with the team.',
                'category' => 'Work',
                'priority' => 'Medium',
                'status' => 'To Do',
                'date' => '2026-11-03',
                'time' => '13:00',
            ],
            [
                'id' => 3,
                'title' => 'Read a Book',
                'description' => 'Read at least one chapter.',
                'category' => 'Personal',
                'priority' => 'Low',
                'status' => 'Completed',
                'date' => '2026-11-03',
                'time' => '16:00',
            ],
            [
                'id' => 4,
                'title' => 'Prepare Presentation',
                'description' => 'Prepare slides for the next presentation.',
                'category' => 'Study',
                'priority' => 'High',
                'status' => 'To Do',
                'date' => '2026-11-04',
                'time' => '10:00',
            ],
            [
                'id' => 5,
                'title' => 'Morning Workout',
                'description' => 'Do a 30-minute exercise session.',
                'category' => 'Personal',
                'priority' => 'Medium',
                'status' => 'Completed',
                'date' => '2026-11-04',
                'time' => '07:00',
            ],
        ];
    }

    // Halaman login
    public function login()
    {
        return view('login');
    }

    // Proses login sederhana
    public function authenticate(Request $request)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:30'],
        ]);

        return redirect()->route('dashboard', [
            'username' => $validated['username'],
        ]);
    }

    // Dashboard
    // Dashboard
    public function dashboard(Request $request)
    {
        $username = $request->query('username');

        if (! $username) {
            return redirect()->route('login.show');
        }

        $tasks = $this->getTasks();

        // Summary
        $totalTasks = count($tasks);

        $completedTasks = collect($tasks)
            ->where('status', 'Completed')
            ->count();

        $inProgressTasks = collect($tasks)
            ->where('status', 'In Progress')
            ->count();

        $pendingTasks = $totalTasks - $completedTasks;

        $progress = $totalTasks > 0
            ? round(($completedTasks / $totalTasks) * 100)
            : 0;

        // Today's Activities
        $todayTasks = collect($tasks)
            ->where('date', now()->format('Y-m-d'))
            ->sortBy('time')
            ->values();

        // Priority Tasks
        $priorityTasks = collect($tasks)
            ->where('priority', 'High')
            ->where('status', '!=', 'Completed')
            ->values();

        // Weekly Activity
        $startOfWeek = now()->startOfWeek();

        $weeklyActivity = [];

        for ($i = 0; $i < 7; $i++) {
            $date = $startOfWeek->copy()->addDays($i);

            $weeklyActivity[] = [
                'day' => $date->format('D'),
                'count' => collect($tasks)
                    ->where('date', $date->format('Y-m-d'))
                    ->count(),
            ];
        }

        $maxActivity = max(
            1,
            collect($weeklyActivity)->max('count')
        );

        return view('dashboard', compact(
            'username',
            'tasks',
            'totalTasks',
            'completedTasks',
            'inProgressTasks',
            'pendingTasks',
            'progress',
            'todayTasks',
            'priorityTasks',
            'weeklyActivity',
            'maxActivity'
        ));
    }

    // Pengelolaan aktivitas
    public function management(Request $request)
    {
        $username = $request->query('username');

        if (! $username) {
            return redirect()->route('login.show');
        }

        $tasks = $this->getTasks();

        return view('management', compact(
            'username',
            'tasks'
        ));
    }

    // Profile pengguna
    public function profile(Request $request)
    {
        $username = $request->query('username');

        if (! $username) {
            return redirect()->route('login.show');
        }

        $tasks = $this->getTasks();

        $totalTasks = count($tasks);

        $completedTasks = collect($tasks)
            ->where('status', 'Completed')
            ->count();

        $progress = $totalTasks > 0
            ? round(($completedTasks / $totalTasks) * 100)
            : 0;

        return view('profile', compact(
            'username',
            'totalTasks',
            'completedTasks',
            'progress'
        ));
    }

    // Kalender
    public function calendar(Request $request)
    {
        return view('calender', [
            'username' => $request->query('username', 'User'),
        ]);
    }

    public function calendarEvents(Request $request): JsonResponse
    {
        $range = $request->validate([
            'start' => ['required', 'date'],
            'end' => ['required', 'date', 'after:start'],
        ]);
        $startDate = (new DateTimeImmutable($range['start']))->format('Y-m-d');
        $endDate = (new DateTimeImmutable($range['end']))->format('Y-m-d');

        $tasks = collect($this->getTasks())
            ->filter(fn (array $task): bool => isset($task['date'], $task['time'])
                && $task['date'] >= $startDate
                && $task['date'] < $endDate)
            ->map(fn (array $task): array => $this->formatTaskEvent($task));

        $events = collect(session('calendar_events', []))
            ->filter(fn (array $event): bool => $event['start_date'] >= $startDate
                && $event['start_date'] < $endDate)
            ->map(fn (array $event): array => $this->formatCalendarEvent($event));

        return response()->json([
            'events' => $tasks->concat($events)->values(),
        ]);
    }

    /**
     * @param  array<string, mixed>  $task
     * @return array<string, mixed>
     */
    private function formatTaskEvent(array $task): array
    {
        $start = new DateTimeImmutable("{$task['date']} {$task['time']}");

        return [
            'id' => 'task-'.$task['id'],
            'title' => $task['title'],
            'start' => $start->format(DATE_ATOM),
            'end' => $start->modify('+1 hour')->format(DATE_ATOM),
            'allDay' => false,
            'extendedProps' => [
                'type' => 'task',
                'category' => $task['category'] ?? 'Personal',
                'description' => $task['description'] ?? '',
                'priority' => $task['priority'] ?? 'Medium',
                'status' => $task['status'] ?? 'To Do',
            ],
        ];
    }

    /**
     * @param  array<string, mixed>  $event
     * @return array<string, mixed>
     */
    private function formatCalendarEvent(array $event): array
    {
        $start = $event['all_day']
            ? $event['start_date']
            : "{$event['start_date']}T{$event['start_time']}:00";
        $end = $event['all_day']
            ? (new DateTimeImmutable($event['end_date']))->modify('+1 day')->format('Y-m-d')
            : "{$event['end_date']}T{$event['end_time']}:00";

        return [
            'id' => $event['id'],
            'title' => $event['title'],
            'start' => $start,
            'end' => $end,
            'allDay' => $event['all_day'],
            'extendedProps' => [
                'type' => 'event',
                'category' => $event['category'],
                'description' => $event['description'] ?? '',
            ],
        ];
    }

    // Logout simulasi
    public function logout()
    {
        return redirect()->route('login.show');
    }
}
