@extends('layouts.app')
@section('title', 'Calendar - Dayflow')

@section('content')
<x-navbar :username="$username" active="calendar" />

<div class="calendar-page">
    <div class="calendar-container">

        {{-- PAGE HEADER --}}
        <div class="calendar-header">
            <div>
                <span class="page-label">PLAN YOUR TIME</span>
                <h1>My Calendar</h1>
                <p>Organize your schedule and stay on track.</p>
            </div>

        </div>

        {{-- SUMMARY CARDS --}}
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
                        <button id="prevMonth" class="nav-calendar-btn"
                            aria-label="Previous period">
                            <i class="bi bi-chevron-left"></i>
                        </button>

                        <button id="nextMonth" class="nav-calendar-btn"
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

                <div id="calendar"></div>

                <div class="calendar-legend">
                    <span><i class="legend-dot personal"></i> Personal</span>
                    <span><i class="legend-dot work"></i> Work</span>
                    <span><i class="legend-dot study"></i> Study</span>
                </div>

                <div id="calendarNotice" class="calendar-notice" role="alert"
                    hidden></div>
            </div>

            {{-- UPCOMING EVENTS --}}
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
</div>

<x-footer />

<style>
.calendar-page {
    background: #F8F7FC;
    flex: 1;
    min-width: 0;
    padding: 4px 0 24px;
    color: #373247;
}

.calendar-container {
    width: 100%;
    max-width: 1540px;
    min-width: 0;
    margin-inline: auto;
}

.calendar-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 20px;
    margin-bottom: 24px;
}

.page-label {
    color: #9182BD;
    font-size: 11px;
    font-weight: 700;
    letter-spacing: 1.5px;
}

.calendar-header h1 {
    font-size: 28px;
    font-weight: 700;
    margin: 7px 0;
}

.calendar-header p,
.panel-heading p {
    color: #9691A5;
    font-size: 13px;
    margin: 0;
}

.header-actions {
    display: flex;
    align-items: center;
    justify-content: flex-end;
    flex-wrap: wrap;
    gap: 10px;
}

.btn-add {
    border-radius: 10px;
    padding: 11px 16px;
    font-size: 13px;
    font-weight: 600;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-add, .btn-save {
    background: #9182BD;
    color: white;
    border: none;
}

.btn-add:hover, .btn-save:hover {
    background: #7969A9;
    color: white;
}

.btn-add-secondary {
    background: white;
    color: #514968;
    border: 1px solid #E8E4F0;
}

.btn-add-secondary:hover {
    background: #F8F7FC;
    color: #514968;
}

.calendar-stats {
    display: grid;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    gap: 18px;
    margin-bottom: 22px;
}

.stat-card {
    background: white;
    border: 1px solid #EEEAF5;
    border-radius: 15px;
    padding: 20px;
    display: flex;
    align-items: center;
    gap: 15px;
}

.stat-icon {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: grid;
    place-items: center;
    font-size: 20px;
}

.stat-icon.purple { background: #F0ECF9; color: #9182BD; }
.stat-icon.blue { background: #EAF0FA; color: #7797C9; }
.stat-icon.orange { background: #FFF1E6; color: #D99A67; }

.stat-card span {
    color: #9691A5;
    font-size: 12px;
}

.stat-card h3 {
    font-size: 23px;
    font-weight: 700;
    margin: 3px 0 0;
}

.calendar-layout {
    display: grid;
    grid-template-columns: minmax(0, 1fr) minmax(250px, 300px);
    gap: 20px;
    align-items: start;
}

.calendar-main, .upcoming-panel {
    min-width: 0;
    background: white;
    border: 1px solid #EEEAF5;
    border-radius: 16px;
    padding: 22px;
}

.calendar-main {
    overflow: hidden;
}

.calendar-toolbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 15px;
    margin-bottom: 20px;
}

.calendar-navigation {
    display: flex;
    align-items: center;
    gap: 9px;
    min-width: 0;
}

.calendar-navigation h2 {
    font-size: 19px;
    font-weight: 700;
    margin: 0 0 0 10px;
    white-space: nowrap;
}

.nav-calendar-btn, .today-btn {
    background: white;
    border: 1px solid #EAE6F2;
    border-radius: 8px;
    height: 34px;
    padding: 0 11px;
    color: #625A75;
}

.today-btn {
    font-size: 12px;
    font-weight: 600;
}

.view-switch {
    display: flex;
    padding: 4px;
    background: #F5F3F9;
    border-radius: 9px;
}

.view-switch button {
    border: none;
    background: transparent;
    padding: 7px 12px;
    border-radius: 7px;
    font-size: 12px;
    color: #817A92;
}

.view-switch button.active {
    background: white;
    color: #7969A9;
    box-shadow: 0 1px 4px #00000010;
    font-weight: 600;
}

#calendar {
    font-size: 13px;
    min-width: 0;
}

.calendar-notice:not([hidden]) {
    margin-top: 16px;
    padding: 11px 14px;
    border: 1px solid #F3CCCC;
    border-radius: 9px;
    background: #FFF4F4;
    color: #A84444;
    font-size: 12px;
}

.fc .fc-toolbar {
    display: none;
}

.fc .fc-col-header-cell {
    background: #FAF9FC;
    padding: 11px 0;
    color: #898397;
    font-weight: 600;
    border-color: #F0EDF5;
}

.fc .fc-daygrid-day {
    border-color: #F0EDF5;
}

.fc .fc-daygrid-day-number {
    padding: 10px;
    color: #625A75;
    text-decoration: none;
}

.fc .fc-day-today {
    background: #F7F4FD !important;
}

.fc .fc-daygrid-day.fc-day-today .fc-daygrid-day-number {
    background: #9182BD;
    color: white;
    border-radius: 50%;
    margin: 5px;
    padding: 5px 9px;
}

.fc .fc-event {
    border: none;
    border-radius: 5px;
    padding: 3px 5px;
    font-size: 11px;
    cursor: pointer;
}

.fc .fc-daygrid-event-dot {
    display: none;
}

.fc .fc-daygrid-more-link {
    color: #9182BD;
    font-size: 11px;
}

.calendar-legend {
    display: flex;
    gap: 20px;
    margin-top: 20px;
    color: #898397;
    font-size: 11px;
}

.calendar-legend span, .filter-section label {
    display: flex;
    align-items: center;
    gap: 7px;
}

.legend-dot, .filter-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    display: inline-block;
}

.personal { background: #B8A9DF; }
.work { background: #8DA9D5; }
.study { background: #E8B77E; }

.panel-heading {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 22px;
}

.panel-heading h3 {
    font-size: 15px;
    font-weight: 700;
    margin-bottom: 5px;
}

.panel-heading > i {
    color: #9182BD;
    font-size: 20px;
}

.upcoming-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.upcoming-item {
    display: flex;
    gap: 11px;
    padding-bottom: 15px;
    border-bottom: 1px solid #F0EDF5;
}

.upcoming-color {
    width: 4px;
    min-height: 43px;
    border-radius: 5px;
}

.upcoming-item h4 {
    font-size: 12px;
    font-weight: 700;
    margin: 0 0 5px;
}

.upcoming-item p {
    font-size: 11px;
    color: #9691A5;
    margin: 0;
}

.filter-section {
    border-top: 1px solid #F0EDF5;
    margin-top: 25px;
    padding-top: 20px;
}

.filter-section h4 {
    font-size: 13px;
    font-weight: 700;
    margin-bottom: 15px;
}

.filter-section label {
    font-size: 12px;
    color: #817A92;
    margin-bottom: 13px;
    cursor: pointer;
}

.filter-section input {
    accent-color: #9182BD;
}

.event-modal {
    border: none;
    border-radius: 16px;
    overflow: hidden;
}

.event-modal .modal-header {
    padding: 22px 24px 15px;
    border-bottom: 1px solid #F0EDF5;
}

.event-modal .modal-header h5 {
    font-size: 17px;
    font-weight: 700;
    color: #373247;
}

.event-modal .modal-header p {
    color: #9691A5;
    font-size: 12px;
    margin: 4px 0 0;
}

.event-modal .modal-body {
    padding: 22px 24px;
}

.form-label {
    font-size: 12px;
    font-weight: 600;
    color: #514968;
}

.form-control, .form-select {
    border: 1px solid #EAE6F2;
    border-radius: 8px;
    padding: 10px 12px;
    font-size: 12px;
}

.form-control:focus, .form-select:focus {
    border-color: #B8A9DF;
    box-shadow: 0 0 0 3px #9182BD20;
}

.modal-footer {
    padding: 15px 24px;
    border-top: 1px solid #F0EDF5;
}

.btn-cancel, .btn-delete, .btn-save {
    border-radius: 8px;
    padding: 9px 15px;
    font-size: 12px;
    font-weight: 600;
}

.btn-cancel {
    background: white;
    border: 1px solid #EAE6F2;
    color: #625A75;
}

.btn-delete {
    margin-right: auto;
    background: #FFF0F0;
    color: #D66B6B;
    border: none;
    display: none;
}

@media (max-width: 1100px) {
    .calendar-layout {
        grid-template-columns: 1fr;
    }

    .upcoming-panel {
        display: block;
    }
}

@media (max-width: 700px) {
    .calendar-page {
        padding: 0 0 18px;
    }

    .calendar-header {
        align-items: flex-start;
        flex-direction: column;
        gap: 14px;
    }

    .header-actions {
        width: 100%;
        justify-content: stretch;
    }

    .header-actions > * {
        flex: 1;
        justify-content: center;
    }

    .calendar-stats {
        grid-template-columns: 1fr;
        gap: 10px;
        margin-bottom: 14px;
    }

    .calendar-main, .upcoming-panel {
        padding: 12px;
    }

    .calendar-toolbar {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }

    .calendar-navigation {
        flex-wrap: wrap;
        gap: 6px;
    }

    .calendar-navigation h2 {
        flex: 1 1 100%;
        margin: 5px 0 0;
        font-size: 16px;
    }

    .view-switch {
        width: 100%;
    }

    .view-switch button {
        flex: 1;
    }

    .calendar-legend {
        flex-wrap: wrap;
    }

    .fc .fc-col-header-cell {
        padding: 7px 0;
    }

    .fc .fc-daygrid-day-number {
        padding: 6px;
    }
}
</style>

{{-- FULLCALENDAR --}}
<link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.css"
    rel="stylesheet">

<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.19/index.global.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const calendarEl = document.getElementById('calendar');
    const eventsUrl = @json(route('calendar.events.index'));

    const categoryColors = {
        Personal: '#B8A9DF',
        Work: '#8DA9D5',
        Study: '#E8B77E'
    };
    let upcomingEvents = [];

    async function requestJson(url, options = {}) {
        const response = await fetch(url, {
            ...options,
            headers: {
                Accept: 'application/json',
                ...options.headers
            }
        });
        const result = response.status === 204 ? {} : await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'The request could not be completed.');
        }

        return result;
    }

    function showCalendarNotice(message) {
        const notice = document.getElementById('calendarNotice');
        notice.textContent = message;
        notice.hidden = false;
    }

    function hideCalendarNotice() {
        const notice = document.getElementById('calendarNotice');
        notice.textContent = '';
        notice.hidden = true;
    }

    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        height: 'auto',
        headerToolbar: false,
        dayMaxEvents: 3,
        editable: false,
        events: function(fetchInfo, successCallback, failureCallback) {
            const params = new URLSearchParams({
                start: fetchInfo.startStr,
                end: fetchInfo.endStr
            });

            requestJson(`${eventsUrl}?${params}`).then(result => {
                successCallback(result.events);
                hideCalendarNotice();
            }).catch(error => {
                showCalendarNotice(error.message);
                failureCallback(error);
            });
        },

        datesSet: function(info) {
            document.getElementById('calendarTitle').textContent =
                info.view.title;
        },

        eventDataTransform: function(event) {
            const color = categoryColors[event.extendedProps.category] ||
                categoryColors.Personal;

            return {
                ...event,
                backgroundColor: color,
                borderColor: color
            };
        },

        eventsSet: function() {
            updateSummary();
        }
    });

    calendar.render();

    // NAVIGATION
    document.getElementById('prevMonth').onclick = () => calendar.prev();
    document.getElementById('nextMonth').onclick = () => calendar.next();
    document.getElementById('todayBtn').onclick = () => calendar.today();

    document.querySelectorAll('.view-switch button').forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.view-switch button')
                .forEach(btn => btn.classList.remove('active'));

            this.classList.add('active');
            calendar.changeView(this.dataset.view);
        });
    });

    // FILTER
    document.querySelectorAll('[data-category]').forEach(input => {
        input.addEventListener('change', function() {
            const category = this.dataset.category;

            calendar.getEvents().forEach(event => {
                if (event.extendedProps.category === category) {
                    event.setProp('display', this.checked ? 'auto' : 'none');
                }
            });
            renderUpcoming();
            updateSummary();
        });
    });

    function getCurrentEvents() {
        return calendar.getEvents().filter(event => event.display !== 'none');
    }

    // SUMMARY
    function updateSummary() {
        const currentEvents = getCurrentEvents();
        const today = formatDate(new Date());

        const todayCount = currentEvents.filter(event =>
            formatDate(event.start) === today
        ).length;

        const now = new Date();
        const upcomingCount = upcomingEvents.filter(event => {
            const eventDate = String(event.start).slice(0, 10);
            const isUpcoming = event.allDay
                ? eventDate >= today
                : new Date(event.start) >= now;

            return isUpcoming && getVisibleCategories().has(
                event.extendedProps.category
            );
        }).length;

        document.getElementById('totalEvents').textContent = currentEvents.length;
        document.getElementById('todayEvents').textContent = todayCount;
        document.getElementById('upcomingEvents').textContent = upcomingCount;
    }

    // UPCOMING LIST
    function renderUpcoming() {
        const container = document.getElementById('upcomingList');
        const visibleCategories = getVisibleCategories();

        const upcoming = upcomingEvents
            .filter(event => visibleCategories.has(event.extendedProps.category))
            .sort((a, b) => new Date(a.start) - new Date(b.start))
            .slice(0, 4);

        if (!upcoming.length) {
            container.innerHTML =
                '<p class="text-muted small">No upcoming items.</p>';
            return;
        }

        container.innerHTML = upcoming.map(event => {
            const category = event.extendedProps.category;
            const eventDate = event.allDay
                ? new Date(`${event.start}T12:00:00`)
                : new Date(event.start);
            const date = eventDate.toLocaleDateString('en-US', {
                month: 'short',
                day: 'numeric'
            });

            const time = event.allDay
                ? 'All day'
                : eventDate.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit'
                });

            return `
                <div class="upcoming-item">
                    <div class="upcoming-color"
                        style="background:${categoryColors[category]}"></div>
                    <div>
                        <h4>${escapeHtml(event.title)}</h4>
                        <p>${event.extendedProps.type === 'task' ? 'Task' : 'Event'} · ${date} · ${time}</p>
                    </div>
                </div>
            `;
        }).join('');
    }

    function getVisibleCategories() {
        return new Set(
            [...document.querySelectorAll('[data-category]:checked')]
                .map(input => input.dataset.category)
        );
    }

    async function loadUpcomingEvents() {
        const start = new Date();
        const end = new Date(start);
        end.setDate(end.getDate() + 90);
        const params = new URLSearchParams({
            start: start.toISOString(),
            end: end.toISOString()
        });

        try {
            const result = await requestJson(`${eventsUrl}?${params}`);
            upcomingEvents = result.events;
            renderUpcoming();
            updateSummary();
        } catch (error) {
            showCalendarNotice(error.message);
        }
    }

    function escapeHtml(value) {
        return String(value).replace(/[&<>"']/g, char => ({
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        })[char]);
    }

    updateSummary();
    renderUpcoming();
    loadUpcomingEvents();

});
</script>
@endsection
