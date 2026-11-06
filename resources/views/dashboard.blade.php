@extends('layouts.app')

@section('title', 'Dashboard | Dayflow')

@section('content')

<x-navbar :username="$username" active="dashboard" />

<main class="main-content">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="page-title">
                Good morning, {{ $username }}!
            </h1>
            <p class="page-subtitle mb-0">
                Here's your productivity overview today.
            </p>
        </div>

    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">

        <div class="col-md-4">
            <div class="card-custom">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="page-subtitle mb-2">Total Tasks</p>
                        <div class="stat-number">{{ $totalTasks }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-list-task"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-custom">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="page-subtitle mb-2">Pending Tasks</p>
                        <div class="stat-number">{{ $pendingTasks }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card-custom">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="page-subtitle mb-2">Completed Tasks</p>
                        <div class="stat-number">{{ $completedTasks }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row g-3">

        <!-- Daily Progress -->
        <div class="col-lg-5">
            <div class="card-custom">
                <h5 class="fw-bold mb-1">Overall Progress</h5>
                <p class="page-subtitle">Your productivity summary</p>

                <div class="d-flex align-items-center justify-content-between mt-4 mb-2">
                    <span class="page-subtitle">Tasks completed</span>
                    <strong>{{ $progress }}%</strong>
                </div>

                <div class="progress mb-3">
                    <div class="progress-bar"
                         style="width: {{ $progress }}%">
                    </div>
                </div>

                <p class="page-subtitle mb-0">
                    {{ $completedTasks }} of {{ $totalTasks }} tasks completed.
                    Keep up the good work!
                </p>
            </div>
        </div>

        <!-- Priority Tasks -->
        <div class="col-lg-7">
            <div class="card-custom">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">Priority Tasks</h5>
                        <p class="page-subtitle mb-0">Tasks that need your attention</p>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <a href="{{ route('management', ['username' => $username]) }}"
                        class="text-decoration-none small fw-semibold"
                        style="color: #9182BD;">
                            View All <i class="bi bi-arrow-up-right"></i>
                        </a>

                        <i class="bi bi-flag text-secondary fs-4"></i>
                    </div>
                </div>

                @forelse ($priorityTasks as $task)
                    <div class="d-flex align-items-center justify-content-between py-3 border-bottom">

                        <div class="me-3">
                            <div class="fw-semibold" style="font-size: 13px;">
                                {{ $task['title'] }}
                            </div>

                            <small class="text-secondary">
                                {{ $task['category'] }}
                            </small>
                        </div>

                        <span class="badge-soft badge-high">
                            {{ $task['priority'] }}
                        </span>

                    </div>
                @empty
                    <p class="page-subtitle">
                        No priority tasks at the moment.
                    </p>
                @endforelse
            </div>
        </div>

        <!-- Today's Activities -->
        <div class="col-12">
            <div class="card-custom">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">Today's Activities</h5>
                        <p class="page-subtitle mb-0">
                            Your schedule for today
                        </p>
                    </div>

                    <a href="{{ route('management', ['username' => $username]) }}"
                       class="text-decoration-none"
                       style="color: var(--primary-dark); font-size: 13px;">
                        View all <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

                @forelse ($todayTasks as $task)
                    <div class="d-flex align-items-center gap-3 py-3 border-bottom">

                        <div class="text-center" style="min-width: 65px;">
                            <div class="fw-semibold" style="font-size: 13px;">
                                {{ date('h:i A', strtotime($task['time'])) }}
                            </div>
                        </div>

                        <div class="grow">
                            <div class="fw-semibold" style="font-size: 13px;">
                                {{ $task['title'] }}
                            </div>
                            <small class="text-secondary">
                                {{ $task['category'] }}
                            </small>
                        </div>

                        <span class="badge-soft badge-status">
                            {{ $task['status'] }}
                        </span>

                    </div>
                @empty
                    <p class="page-subtitle mb-0">
                        No activities scheduled for today.
                    </p>
                @endforelse

            </div>
        </div>

    </div>

    <x-footer />

</main>

@endsection
