@extends('layouts.app')

@section('title', 'Calendar - Dayflow')

@section('content')
<x-navbar :username="$username" active="calendar" />

<main class="main-content calendar-page">
    <div class="calendar-container">
        <div class="calendar-header">
            <div>
                <h1 class="page-title mb-2">My Calendar</h1>
                <p class="page-subtitle mb-0">Organize your schedule and stay on track.</p>
            </div>
        </div>

        <div class="calendar-stats">
            <div class="stat-card">
                <div class="stat-icon purple">
                    <i class="bi bi-calendar-event"></i>
                </div>
                <div>
                    <span>Total Items</span>
                    <h3 id="totalEvents">0</h3>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon blue">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div>
                    <span>Today's Items</span>
                    <h3 id="todayEvents">0</h3>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon orange">
                    <i class="bi bi-clock-history"></i>
                </div>
                <div>
                    <span>Upcoming Items</span>
                    <h3 id="upcomingEvents">0</h3>
                </div>
            </div>
        </div>

        {{-- CALENDAR CONTENT --}}
        <div class="calendar-layout">

            <div class="calendar-main">
                <div class="calendar-toolbar">
                    <div class="calendar-navigation">
                        <button id="previousPeriod" class="nav-calendar-btn"
                            aria-label="Previous period">
                            <i class="bi bi-chevron-left"></i>
                        </button>

                        <button id="nextPeriod" class="nav-calendar-btn"
                            aria-label="Next period">
                            <i class="bi bi-chevron-right"></i>
                        </button>

                        <button id="todayBtn" class="today-btn">Today</button>
                        <h2 id="calendarTitle">Calendar</h2>
                    </div>

                    <div class="view-switch">
                        <button data-view="dayGridMonth" class="active">Month</button>
                        <button data-view="timeGridWeek">Week</button>
                        <button data-view="timeGridDay">Day</button>
                    </div>
                </div>

                <div
                    id="calendar"
                    data-events-url="{{ route('calendar.events.index') }}"
                ></div>

                <div class="calendar-legend">
                    <span><i class="legend-dot personal"></i> Personal</span>
                    <span><i class="legend-dot work"></i> Work</span>
                    <span><i class="legend-dot study"></i> Study</span>
                </div>

                <div id="calendarNotice" class="calendar-notice" role="alert"
                    hidden></div>
            </div>

            <aside class="upcoming-panel">
                <div class="panel-heading">
                    <div>
                        <h3>Upcoming Schedule</h3>
                        <p>Your next activities</p>
                    </div>
                    <i class="bi bi-calendar3"></i>
                </div>

                <div id="upcomingList" class="upcoming-list"></div>

                <div class="filter-section">
                    <h4>Event Categories</h4>

                    <label>
                        <input type="checkbox" checked
                            data-category="Personal">
                        <span class="filter-dot personal"></span>
                        Personal
                    </label>

                    <label>
                        <input type="checkbox" checked
                            data-category="Work">
                        <span class="filter-dot work"></span>
                        Work
                    </label>

                    <label>
                        <input type="checkbox" checked
                            data-category="Study">
                        <span class="filter-dot study"></span>
                        Study
                    </label>
                </div>
            </aside>
        </div>

    </div>

    <x-footer />
</main>

@push('styles')
    <link
        href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.css"
        rel="stylesheet"
    >
    @vite('resources/css/calendar.css')
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js"></script>
    @vite('resources/js/calendar.js')
@endpush

@endsection
