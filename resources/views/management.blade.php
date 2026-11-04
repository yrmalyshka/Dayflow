
@extends('layouts.app')

@section('title', 'Task Management | Dayflow')

@section('content')

<x-navbar :username="$username" active="management" />

<main class="main-content">

    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="page-title">Task Management</h1>
            <p class="page-subtitle mb-0">
                Organize and manage all your activities.
            </p>
        </div>

        <button class="btn-primary-custom"
                data-bs-toggle="modal"
                data-bs-target="#addTaskModal">
            <i class="bi bi-plus-lg me-1"></i>
            Add New Task
        </button>
    </div>

    <!-- Filters -->
    <div class="card-custom mb-4">

        <div class="row g-3">

            <div class="col-lg-4">
                <label class="form-label small fw-semibold">Search Task</label>
                <div class="input-group">
                    <span class="input-group-text bg-white"
                          style="border-color: var(--border);">
                        <i class="bi bi-search text-secondary"></i>
                    </span>

                    <input type="text"
                           id="searchTask"
                           class="form-control"
                           placeholder="Search activities...">
                </div>
            </div>

            <div class="col-lg-2 col-md-4">
                <label class="form-label small fw-semibold">Category</label>
                <select id="filterCategory" class="form-select">
                    <option value="">All Categories</option>
                    <option value="Study">Study</option>
                    <option value="Work">Work</option>
                    <option value="Personal">Personal</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-4">
                <label class="form-label small fw-semibold">Priority</label>
                <select id="filterPriority" class="form-select">
                    <option value="">All Priorities</option>
                    <option value="High">High</option>
                    <option value="Medium">Medium</option>
                    <option value="Low">Low</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-4">
                <label class="form-label small fw-semibold">Status</label>
                <select id="filterStatus" class="form-select">
                    <option value="">All Status</option>
                    <option value="To Do">To Do</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>

            <div class="col-lg-2 d-flex align-items-end">
                <button id="resetFilter"
                        class="btn btn-outline-secondary w-100"
                        style="border-radius: 10px; padding: 11px;">
                    <i class="bi bi-arrow-counterclockwise me-1"></i>
                    Reset
                </button>
            </div>

        </div>
    </div>

    <!-- Task Table -->
    <div class="card-custom">

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h5 class="fw-bold mb-1">All Activities</h5>
                <p class="page-subtitle mb-0">
                    Manage your tasks and track progress.
                </p>
            </div>

            <span class="badge-soft badge-status" id="taskCount">
                {{ count($tasks) }} Tasks
            </span>
        </div>

        <div class="table-responsive">
            <table class="table" id="taskTable">

                <thead>
                    <tr>
                        <th>ACTIVITY</th>
                        <th>CATEGORY</th>
                        <th>DATE & TIME</th>
                        <th>PRIORITY</th>
                        <th>STATUS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>

                <tbody id="taskList">

                    @foreach ($tasks as $task)
                        <tr class="task-row"
                            data-title="{{ strtolower($task['title']) }}"
                            data-category="{{ $task['category'] }}"
                            data-priority="{{ $task['priority'] }}"
                            data-status="{{ $task['status'] }}">

                            <td>
                                <div class="fw-semibold task-title">
                                    {{ $task['title'] }}
                                </div>
                                <small class="text-secondary">
                                    {{ $task['description'] }}
                                </small>
                            </td>

                            <td>{{ $task['category'] }}</td>

                            <td>
                                <div>{{ date('d M Y', strtotime($task['date'])) }}</div>
                                <small class="text-secondary">
                                    {{ date('h:i A', strtotime($task['time'])) }}
                                </small>
                            </td>

                            <td>
                                <span class="badge-soft
                                    {{ $task['priority'] === 'High' ? 'badge-high' : ($task['priority'] === 'Medium' ? 'badge-medium' : 'badge-low') }}">
                                    {{ $task['priority'] }}
                                </span>
                            </td>

                            <td>
                                <span class="badge-soft badge-status status-label">
                                    {{ $task['status'] }}
                                </span>
                            </td>

                            <td>
                                <button type="button"
                                        class="btn btn-sm btn-outline-secondary toggle-status"
                                        style="border-radius: 8px;">
                                    <i class="bi bi-arrow-repeat"></i>
                                </button>
                            </td>

                        </tr>
                    @endforeach

                </tbody>
            </table>

            <div id="emptyState"
                 class="text-center py-5 d-none">
                <i class="bi bi-inbox fs-1 text-secondary"></i>
                <h6 class="fw-bold mt-3">No activities found</h6>
                <p class="page-subtitle">
                    Try changing your search or filters.
                </p>
            </div>

        </div>

    </div>

    <x-footer />

</main>

<!-- Add Task Modal -->
<div class="modal fade" id="addTaskModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0" style="border-radius: 16px;">

            <form id="addTaskForm">

                <div class="modal-header border-0 p-4">
                    <div>
                        <h5 class="fw-bold mb-1">Create New Task</h5>
                        <p class="page-subtitle mb-0">
                            Add a new activity to your planner.
                        </p>
                    </div>

                    <button type="button"
                            class="btn-close"
                            data-bs-dismiss="modal">
                    </button>
                </div>

                <div class="modal-body px-4">

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            Task Name
                        </label>
                        <input type="text"
                               id="newTitle"
                               class="form-control"
                               placeholder="Enter task name"
                               required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label small fw-semibold">
                            Description
                        </label>
                        <textarea id="newDescription"
                                  class="form-control"
                                  rows="2"
                                  placeholder="Describe your task"></textarea>
                    </div>

                    <div class="row g-3">

                        <div class="col-6">
                            <label class="form-label small fw-semibold">
                                Category
                            </label>
                            <select id="newCategory" class="form-select">
                                <option>Study</option>
                                <option>Work</option>
                                <option>Personal</option>
                            </select>
                        </div>

                        <div class="col-6">
                            <label class="form-label small fw-semibold">
                                Priority
                            </label>
                            <select id="newPriority" class="form-select">
                                <option>Low</option>
                                <option>Medium</option>
                                <option>High</option>
                            </select>
                        </div>

                        <div class="col-6">
                            <label class="form-label small fw-semibold">
                                Date
                            </label>
                            <input type="date"
                                   id="newDate"
                                   class="form-control"
                                   required>
                        </div>

                        <div class="col-6">
                            <label class="form-label small fw-semibold">
                                Time
                            </label>
                            <input type="time"
                                   id="newTime"
                                   class="form-control"
                                   required>
                        </div>

                    </div>

                </div>

                <div class="modal-footer border-0 px-4 pb-4">
                    <button type="button"
                            class="btn btn-light"
                            data-bs-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                            class="btn-primary-custom">
                        Create Task
                    </button>
                </div>

            </form>

        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
    const searchInput = document.getElementById('searchTask');
    const categoryFilter = document.getElementById('filterCategory');
    const priorityFilter = document.getElementById('filterPriority');
    const statusFilter = document.getElementById('filterStatus');
    const taskList = document.getElementById('taskList');
    const emptyState = document.getElementById('emptyState');
    const taskCount = document.getElementById('taskCount');

    function filterTasks() {
        const search = searchInput.value.toLowerCase();
        const category = categoryFilter.value;
        const priority = priorityFilter.value;
        const status = statusFilter.value;

        const rows = document.querySelectorAll('.task-row');
        let visibleCount = 0;

        rows.forEach(row => {
            const title = row.dataset.title;
            const rowCategory = row.dataset.category;
            const rowPriority = row.dataset.priority;
            const rowStatus = row.dataset.status;

            const matches =
                title.includes(search) &&
                (!category || rowCategory === category) &&
                (!priority || rowPriority === priority) &&
                (!status || rowStatus === status);

            row.classList.toggle('d-none', !matches);

            if (matches) visibleCount++;
        });

        emptyState.classList.toggle('d-none', visibleCount !== 0);
        taskCount.textContent = visibleCount + ' Tasks';
    }

    searchInput.addEventListener('input', filterTasks);
    categoryFilter.addEventListener('change', filterTasks);
    priorityFilter.addEventListener('change', filterTasks);
    statusFilter.addEventListener('change', filterTasks);

    document.getElementById('resetFilter').addEventListener('click', () => {
        searchInput.value = '';
        categoryFilter.value = '';
        priorityFilter.value = '';
        statusFilter.value = '';
        filterTasks();
    });

    // Toggle status secara lokal
    taskList.addEventListener('click', event => {
        const button = event.target.closest('.toggle-status');

        if (!button) return;

        const row = button.closest('.task-row');
        const label = row.querySelector('.status-label');

        const currentStatus = row.dataset.status;

        const nextStatus = {
            'To Do': 'In Progress',
            'In Progress': 'Completed',
            'Completed': 'To Do'
        };

        const updatedStatus = nextStatus[currentStatus];

        row.dataset.status = updatedStatus;
        label.textContent = updatedStatus;

        filterTasks();
    });

    // Menambahkan task secara lokal ke tabel
    document.getElementById('addTaskForm').addEventListener('submit', event => {
        event.preventDefault();

        const title = document.getElementById('newTitle').value.trim();
        const description = document.getElementById('newDescription').value.trim();
        const category = document.getElementById('newCategory').value;
        const priority = document.getElementById('newPriority').value;
        const date = document.getElementById('newDate').value;
        const time = document.getElementById('newTime').value;

        if (!title || !date || !time) return;

        const row = document.createElement('tr');
        row.className = 'task-row';

        row.dataset.title = title.toLowerCase();
        row.dataset.category = category;
        row.dataset.priority = priority;
        row.dataset.status = 'To Do';

        const priorityClass = {
            High: 'badge-high',
            Medium: 'badge-medium',
            Low: 'badge-low'
        }[priority];

        const safe = value => {
            const element = document.createElement('span');
            element.textContent = value;
            return element.innerHTML;
        };

        const formattedDate = new Date(date + 'T00:00:00')
            .toLocaleDateString('en-GB', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });

        const formattedTime = new Date('2000-01-01T' + time)
            .toLocaleTimeString('en-US', {
                hour: '2-digit',
                minute: '2-digit'
            });

        row.innerHTML = `
            <td>
                <div class="fw-semibold task-title">${safe(title)}</div>
                <small class="text-secondary">${safe(description)}</small>
            </td>
            <td>${safe(category)}</td>
            <td>
                <div>${formattedDate}</div>
                <small class="text-secondary">${formattedTime}</small>
            </td>
            <td>
                <span class="badge-soft ${priorityClass}">
                    ${priority}
                </span>
            </td>
            <td>
                <span class="badge-soft badge-status status-label">
                    To Do
                </span>
            </td>
            <td>
                <button type="button"
                        class="btn btn-sm btn-outline-secondary toggle-status"
                        style="border-radius: 8px;">
                    <i class="bi bi-arrow-repeat"></i>
                </button>
            </td>
        `;

        taskList.appendChild(row);

        document.getElementById('addTaskForm').reset();

        bootstrap.Modal.getInstance(
            document.getElementById('addTaskModal')
        ).hide();

        filterTasks();
    });
</script>
@endpush
