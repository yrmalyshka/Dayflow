
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

@endsection

@push('scripts')
<script>
    const searchInput = document.getElementById('searchTask');
    const categoryFilter = document.getElementById('filterCategory');
    const priorityFilter = document.getElementById('filterPriority');
    const statusFilter = document.getElementById('filterStatus');
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

</script>
@endpush
