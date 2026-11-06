document.addEventListener('DOMContentLoaded', initializeCalendar);

function initializeCalendar() {
    const calendarElement = document.getElementById('calendar');
    const calendarTitle = document.getElementById('calendarTitle');
    const upcomingList = document.getElementById('upcomingList');
    const calendarNotice = document.getElementById('calendarNotice');
    const totalItems = document.getElementById('totalEvents');
    const todaysItems = document.getElementById('todayEvents');
    const upcomingItemsCount = document.getElementById('upcomingEvents');
    const eventsUrl = calendarElement.dataset.eventsUrl;

    const categoryColors = {
        Personal: '#B8A9DF',
        Work: '#8DA9D5',
        Study: '#E8B77E'
    };

    let upcomingEvents = [];

    async function fetchEvents(url) {
        const response = await fetch(url, {
            headers: {
                Accept: 'application/json'
            }
        });
        const result = await response.json();

        if (!response.ok) {
            throw new Error(result.message || 'Could not load calendar events.');
        }

        return result.events;
    }

    function showNotice(message) {
        calendarNotice.textContent = message;
        calendarNotice.hidden = false;
    }

    function hideNotice() {
        calendarNotice.textContent = '';
        calendarNotice.hidden = true;
    }

    function formatDate(date) {
        const year = date.getFullYear();
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const day = String(date.getDate()).padStart(2, '0');

        return `${year}-${month}-${day}`;
    }

    function getSelectedCategories() {
        return new Set(
            Array.from(document.querySelectorAll('[data-category]:checked'))
                .map(function (filter) {
                    return filter.dataset.category;
                })
        );
    }

    async function loadCalendarEvents(fetchInfo, successCallback, failureCallback) {
        const dateRange = new URLSearchParams({
            start: fetchInfo.startStr,
            end: fetchInfo.endStr
        });

        try {
            const events = await fetchEvents(`${eventsUrl}?${dateRange}`);

            successCallback(events);
            hideNotice();
        } catch (error) {
            showNotice(error.message);
            failureCallback(error);
        }
    }

    function updateCalendarSummary() {
        const selectedCategories = getSelectedCategories();
        const visibleEvents = calendar.getEvents().filter(function (event) {
            return event.display !== 'none';
        });
        const today = formatDate(new Date());
        const currentTime = new Date();
        const todayItemCount = visibleEvents.filter(function (event) {
            return formatDate(event.start) === today;
        }).length;
        const upcomingItemCount = upcomingEvents.filter(function (event) {
            const eventDate = String(event.start).slice(0, 10);
            const isUpcoming = event.allDay
                ? eventDate >= today
                : new Date(event.start) >= currentTime;

            return isUpcoming && selectedCategories.has(
                event.extendedProps.category
            );
        }).length;

        totalItems.textContent = visibleEvents.length;
        todaysItems.textContent = todayItemCount;
        upcomingItemsCount.textContent = upcomingItemCount;
    }

    function formatUpcomingDate(event) {
        const eventDate = event.allDay
            ? new Date(`${event.start}T12:00:00`)
            : new Date(event.start);

        return eventDate.toLocaleDateString('en-US', {
            month: 'short',
            day: 'numeric'
        });
    }

    function formatUpcomingTime(event) {
        if (event.allDay) {
            return 'All day';
        }

        return new Date(event.start).toLocaleTimeString('en-US', {
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function createUpcomingItem(event) {
        const category = event.extendedProps.category;
        const color = categoryColors[category] || categoryColors.Personal;
        const type = event.extendedProps.type === 'task' ? 'Task' : 'Event';
        const date = formatUpcomingDate(event);
        const time = formatUpcomingTime(event);

        return `
            <div class="upcoming-item">
                <div class="upcoming-color" style="background:${color}"></div>
                <div>
                    <h4>${escapeHtml(event.title)}</h4>
                    <p>${type} · ${date} · ${time}</p>
                </div>
            </div>
        `;
    }

    function renderUpcomingList() {
        const selectedCategories = getSelectedCategories();
        const eventsToShow = upcomingEvents
            .filter(function (event) {
                return selectedCategories.has(event.extendedProps.category);
            })
            .sort(function (firstEvent, secondEvent) {
                return new Date(firstEvent.start) - new Date(secondEvent.start);
            })
            .slice(0, 4);

        if (eventsToShow.length === 0) {
            upcomingList.innerHTML =
                '<p class="text-muted small">No upcoming items.</p>';
            return;
        }

        upcomingList.innerHTML = eventsToShow
            .map(createUpcomingItem)
            .join('');
    }

    async function loadUpcomingEvents() {
        const startDate = new Date();
        const endDate = new Date(startDate);
        endDate.setDate(endDate.getDate() + 90);

        const dateRange = new URLSearchParams({
            start: startDate.toISOString(),
            end: endDate.toISOString()
        });

        try {
            upcomingEvents = await fetchEvents(`${eventsUrl}?${dateRange}`);
            renderUpcomingList();
            updateCalendarSummary();
        } catch (error) {
            showNotice(error.message);
        }
    }

    function changeCalendarView(button) {
        document.querySelectorAll('.view-switch button').forEach(
            function (viewButton) {
                viewButton.classList.toggle('active', viewButton === button);
            }
        );

        calendar.changeView(button.dataset.view);
    }

    function filterEventsByCategory(filter) {
        const category = filter.dataset.category;
        const display = filter.checked ? 'auto' : 'none';

        calendar.getEvents().forEach(function (event) {
            if (event.extendedProps.category === category) {
                event.setProp('display', display);
            }
        });

        renderUpcomingList();
        updateCalendarSummary();
    }

    function escapeHtml(value) {
        const escapedCharacters = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };

        return String(value).replace(/[&<>"']/g, function (character) {
            return escapedCharacters[character];
        });
    }

    const calendar = new FullCalendar.Calendar(calendarElement, {
        initialView: 'dayGridMonth',
        height: 'auto',
        headerToolbar: false,
        dayMaxEvents: 3,
        editable: false,
        events: loadCalendarEvents,
        datesSet: function (info) {
            calendarTitle.textContent = info.view.title;
        },
        eventDataTransform: function (event) {
            const color = categoryColors[event.extendedProps.category]
                || categoryColors.Personal;

            return {
                ...event,
                backgroundColor: color,
                borderColor: color
            };
        },
        eventsSet: updateCalendarSummary
    });

    document.getElementById('previousPeriod').addEventListener('click', function () {
        calendar.prev();
    });
    document.getElementById('nextPeriod').addEventListener('click', function () {
        calendar.next();
    });
    document.getElementById('todayBtn').addEventListener('click', function () {
        calendar.today();
    });

    document.querySelectorAll('.view-switch button').forEach(
        function (button) {
            button.addEventListener('click', function () {
                changeCalendarView(button);
            });
        }
    );

    document.querySelectorAll('[data-category]').forEach(function (filter) {
        filter.addEventListener('change', function () {
            filterEventsByCategory(filter);
        });
    });

    calendar.render();
    updateCalendarSummary();
    renderUpcomingList();
    loadUpcomingEvents();
}
