@extends('layouts.app')

@section('title', 'My Profile | Dayflow')

@section('content')
    <x-navbar :username="$username" active="profile" />

    <main class="main-content">
        <div class="mb-4">
            <h1 class="page-title">My Profile</h1>
            <p class="page-subtitle">Manage your personal information and productivity.</p>
        </div>

        <div class="row g-3">
            <div class="col-lg-4">
                <div class="card-custom text-center">
                    <div
                        class="rounded-circle mx-auto d-flex align-items-center justify-content-center mb-3"
                        style="width: 85px; height: 85px; background: #EAE6F5; color: #7666A5; font-size: 32px;"
                    >
                        <i class="bi bi-person-fill"></i>
                    </div>

                    <h5 class="fw-bold mb-1">{{ $username }}</h5>
                    <p class="page-subtitle">Dayflow Member</p>

                    <div class="border-top pt-3 mt-4 text-start">
                        <div class="mb-3">
                            <small class="text-secondary">Username</small>
                            <div class="fw-semibold mt-1">{{ $username }}</div>
                        </div>

                        <div class="mb-3">
                            <small class="text-secondary">Account Type</small>
                            <div class="fw-semibold mt-1">Personal Account</div>
                        </div>

                        <div>
                            <small class="text-secondary">Member Since</small>
                            <div class="fw-semibold mt-1">November 2026</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-8">
                <div class="card-custom mb-3">
                    <h5 class="fw-bold mb-1">Productivity Overview</h5>
                    <p class="page-subtitle">Your overall task completion summary.</p>

                    <div class="row g-3 mt-2">
                        <div class="col-md-4">
                            <div class="p-3 rounded-3" style="background: #F8F7FC;">
                                <small class="text-secondary">Total Tasks</small>
                                <div class="stat-number mt-2">{{ $totalTasks }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 rounded-3" style="background: #F8F7FC;">
                                <small class="text-secondary">Completed</small>
                                <div class="stat-number mt-2">{{ $completedTasks }}</div>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="p-3 rounded-3" style="background: #F8F7FC;">
                                <small class="text-secondary">Completion</small>
                                <div class="stat-number mt-2">{{ $progress }}%</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="page-subtitle">Overall completion</span>
                            <strong>{{ $progress }}%</strong>
                        </div>

                        <div class="progress">
                            <div class="progress-bar" style="width: {{ $progress }}%"></div>
                        </div>
                    </div>
                </div>

                <div class="card-custom">
                    <h5 class="fw-bold mb-2">About Dayflow</h5>
                    <p class="page-subtitle mb-0" style="line-height: 1.8;">
                        Dayflow is your personal daily planner designed to
                        help you organize activities, manage priorities,
                        and build better productivity habits.
                        Plan your day, find your flow.
                    </p>
                </div>
            </div>
        </div>
        <x-footer />
    </main>
@endsection
