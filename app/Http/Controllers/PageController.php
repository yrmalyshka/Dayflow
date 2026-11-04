<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    // Data aktivitas sementara
    private function getTasks()
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
            [
                'id' => 6,
                'title' => 'Review Project Report',
                'description' => 'Review the latest project documentation.',
                'category' => 'Work',
                'priority' => 'Medium',
                'status' => 'In Progress',
                'date' => '2026-11-05',
                'time' => '14:00',
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
    public function dashboard(Request $request)
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

        $pendingTasks = $totalTasks - $completedTasks;

        $progress = $totalTasks > 0
            ? round(($completedTasks / $totalTasks) * 100)
            : 0;

        $todayTasks = collect($tasks)
            ->where('date', now()->format('Y-m-d'))
            ->sortBy('time')
            ->values();

        $priorityTasks = collect($tasks)
            ->where('priority', 'High')
            ->where('status', '!=', 'Completed')
            ->values();

        return view('dashboard', compact(
            'username',
            'totalTasks',
            'completedTasks',
            'pendingTasks',
            'progress',
            'todayTasks',
            'priorityTasks'
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
            'googleConnected' => $request->session()->has('google_calendar_token'),
        ]);
    }

    // Logout simulasi
    public function logout()
    {
        return redirect()->route('login.show');
    }
}
