@extends('layouts.app')

@section('title', 'Dashboard | Dayflow')

@section('content')

<x-navbar :username="$username" active="dashboard" />

<main class="main-content">

    <!-- Page Heading -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="page-title">
            Good to see you, {{ $username }}!
            </h1>
            <p class="page-subtitle mb-0">
            Here's your productivity overview today.
            </p>
        </div>

        <a href="{{ route('management', ['username' => $username]) }}"
           class="btn btn-primary"
           style="border-radius: 9px; background: #9182BD; border: none;">
            <i class="bi bi-plus-lg me-1"></i> Manage Tasks
        </a>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4 summary-cards">

        <div class="col-12 col-sm-6 col-xl-3">
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

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card-custom">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="page-subtitle mb-2">In Progress</p>
                        <div class="stat-number">{{ $inProgressTasks }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-hourglass-split"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
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

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card-custom">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="page-subtitle mb-2">Pending Tasks</p>
                        <div class="stat-number">{{ $pendingTasks }}</div>
                    </div>
                    <div class="stat-icon">
                        <i class="bi bi-clock-history"></i>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Main Dashboard Content -->
    <div class="row g-3 mb-4">

        <!-- Weekly Activity -->
        <div class="col-lg-7">
            <div class="card-custom">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">Weekly Activity</h5>
                        <p class="page-subtitle mb-0">
                            Your scheduled tasks this week
                        </p>
                    </div>

                    <i class="bi bi-bar-chart-line text-secondary fs-4"></i>
                </div>

                <div class="d-flex align-items-end justify-content-around"
                     style="height: 175px; gap: 15px;">

                    @foreach ($weeklyActivity as $activity)
                        <div class="d-flex flex-column align-items-center justify-content-end h-100 flex-fill">

                            <div class="d-flex align-items-end justify-content-center w-100"
                                 style="height: 140px;">

                                <div style="
                                    width: 28px;
                                    max-width: 100%;
                                    height: {{ max(8, ($activity['count'] / $maxActivity) * 100) }}%;
                                    background: {{ $activity['count'] > 0 ? '#9182BD' : '#EAE6F5' }};
                                    border-radius: 20px 20px 8px 8px;
                                "
                                title="{{ $activity['count'] }} tasks">
                                </div>

                            </div>

                            <small class="text-secondary mt-2">
                                {{ $activity['day'] }}
                            </small>

                        </div>
                    @endforeach

                </div>

            </div>
        </div>

        <!-- Overall Progress -->
        <div class="col-lg-5">
            <div class="card-custom h-100">

                <h5 class="fw-bold mb-1">Overall Progress</h5>
                <p class="page-subtitle">
                    Your productivity summary
                </p>

                <div class="d-flex justify-content-center align-items-center my-4">

                    <div style="
                        width: 155px;
                        height: 155px;
                        border-radius: 50%;
                        background: conic-gradient(
                            #9182BD {{ $progress }}%,
                            #EAE6F5 {{ $progress }}%
                        );
                        display: flex;
                        align-items: center;
                        justify-content: center;
                    ">

                        <div style="
                            width: 125px;
                            height: 125px;
                            border-radius: 50%;
                            background: white;
                            display: flex;
                            flex-direction: column;
                            align-items: center;
                            justify-content: center;
                        ">
                            <div class="fw-bold" style="font-size: 28px; color: #373247;">
                                {{ $progress }}%
                            </div>

                            <small class="text-secondary">
                                Completed
                            </small>
                        </div>

                    </div>

                </div>

                <div class="d-flex justify-content-center gap-3 flex-wrap">
                    <small class="text-secondary">
                        <i class="bi bi-circle-fill" style="color: #9182BD;"></i>
                        Completed
                    </small>

                    <small class="text-secondary">
                        <i class="bi bi-circle-fill" style="color: #EAE6F5;"></i>
                        Remaining
                    </small>
                </div>

                <p class="page-subtitle text-center mt-3 mb-0">
                    {{ $completedTasks }} of {{ $totalTasks }} tasks completed.
                </p>

            </div>
        </div>

    </div>

    <!-- Priority Tasks and Today's Activities -->
    <div class="row g-3 mb-4">

        <!-- Priority Tasks -->
        <div class="col-lg-5">
            <div class="card-custom h-100">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">Priority Tasks</h5>
                        <p class="page-subtitle mb-0">
                            Tasks that need your attention
                        </p>
                    </div>

                    <i class="bi bi-flag text-secondary fs-4"></i>
                </div>

                @forelse ($priorityTasks as $task)

                    <div class="d-flex align-items-center justify-content-between py-3 border-bottom gap-2">

                        <div class="me-2">
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

                    <p class="page-subtitle mt-3">
                        No priority tasks at the moment.
                    </p>

                @endforelse

                <div class="mt-3">
                    <a href="{{ route('management', ['username' => $username]) }}"
                       class="text-decoration-none small fw-semibold"
                       style="color: #9182BD;">
                        View all tasks <i class="bi bi-arrow-right"></i>
                    </a>
                </div>

            </div>
        </div>

        <!-- Today's Activities -->
        <div class="col-lg-7">
            <div class="card-custom h-100">

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">Today's Activities</h5>
                        <p class="page-subtitle mb-0">
                            Your schedule for today
                        </p>
                    </div>

                    <a href="{{ route('management', ['username' => $username]) }}"
                       class="text-decoration-none small fw-semibold"
                       style="color: #9182BD;">
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

                        <div class="flex-grow-1">
                            <div class="fw-semibold" style="font-size: 13px;">
                                {{ $task['title'] }}
                            </div>

                            <small class="text-secondary">
                                {{ $task['category'] }}
                            </small>
                        </div>

                        @if ($task['status'] === 'Completed')
                            <span class="badge-soft badge-status">
                                Completed
                            </span>
                        @elseif ($task['status'] === 'In Progress')
                            <span class="badge-soft badge-medium">
                                In Progress
                            </span>
                        @else
                            <span class="badge-soft badge-low">
                                To Do
                            </span>
                        @endif

                    </div>

                @empty

                    <p class="page-subtitle mt-3">
                        No activities scheduled for today.
                    </p>

                @endforelse

            </div>
        </div>

    </div>

    <x-footer />

</main>

@endsection
