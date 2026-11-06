@extends('layouts.app')

@section('title', 'Task Management | Dayflow')

@section('content')
<x-navbar :username="$username" active="management" />

<main class="main-content management-page">
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3">
        <div>
            <h1 class="page-title">Task Management</h1>
            <p class="page-subtitle mb-0">Organize and manage all your activities.</p>
        </div>
    </div>

    <section class="card-custom mb-4" aria-label="Task filters">
        <div class="row g-3">
            <div class="col-lg-4">
                <label for="searchTask" class="form-label small fw-semibold">Search Task</label>
                <div class="input-group">
                    <span class="input-group-text bg-white management-search-icon">
                        <i class="bi bi-search text-secondary"></i>
                    </span>
                    <input
                        type="text"
                        id="searchTask"
                        class="form-control"
                        placeholder="Search activities..."
                    >
                </div>
            </div>

            <div class="col-lg-2 col-md-4">
                <label for="filterCategory" class="form-label small fw-semibold">Category</label>
                <select id="filterCategory" class="form-select">
                    <option value="">All Categories</option>
                    <option value="Study">Study</option>
                    <option value="Work">Work</option>
                    <option value="Personal">Personal</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-4">
                <label for="filterPriority" class="form-label small fw-semibold">Priority</label>
                <select id="filterPriority" class="form-select">
                    <option value="">All Priorities</option>
                    <option value="High">High</option>
                    <option value="Medium">Medium</option>
                    <option value="Low">Low</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-4">
                <label for="filterStatus" class="form-label small fw-semibold">Status</label>
                <select id="filterStatus" class="form-select">
                    <option value="">All Status</option>
                    <option value="To Do">To Do</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Completed">Completed</option>
                </select>
            </div>

            <div class="col-lg-2 d-flex align-items-end">
                <button
                    type="button"
                    id="resetFilter"
                    class="btn btn-outline-secondary w-100 management-reset-button"
                >
                    <i class="bi bi-arrow-counterclockwise me-1"></i>Reset
                </button>
            </div>
        </div>
    </section>

    <section class="card-custom" aria-labelledby="activityListTitle">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h2 id="activityListTitle" class="h5 fw-bold mb-1">All Activities</h2>
                <p class="page-subtitle mb-0">Manage your tasks and track progress.</p>
            </div>

            <span class="badge-soft badge-status" id="taskCount">{{ count($tasks) }} Tasks</span>
        </div>

        <div class="table-responsive">
            <table class="table" id="taskTable">
                <thead>
                    <tr>
                        <th scope="col">ACTIVITY</th>
                        <th scope="col">CATEGORY</th>
                        <th scope="col">DATE &amp; TIME</th>
                        <th scope="col">PRIORITY</th>
                        <th scope="col">STATUS</th>
                    </tr>
                </thead>
                <tbody id="taskList">
                    @foreach ($tasks as $task)
                        <tr
                            class="task-row"
                            data-title="{{ strtolower($task['title']) }}"
                            data-category="{{ $task['category'] }}"
                            data-priority="{{ $task['priority'] }}"
                            data-status="{{ $task['status'] }}"
                        >
                            <td>
                                <div class="fw-semibold task-title">{{ $task['title'] }}</div>
                                <small class="text-secondary">{{ $task['description'] }}</small>
                            </td>
                            <td>{{ $task['category'] }}</td>
                            <td>
                                <div>{{ date('d M Y', strtotime($task['date'])) }}</div>
                                <small class="text-secondary">{{ date('h:i A', strtotime($task['time'])) }}</small>
                            </td>
                            <td>
                                <span @class([
                                    'badge-soft',
                                    'badge-high' => $task['priority'] === 'High',
                                    'badge-medium' => $task['priority'] === 'Medium',
                                    'badge-low' => $task['priority'] === 'Low',
                                ])>
                                    {{ $task['priority'] }}
                                </span>
                            </td>
                            <td>
                                <span class="badge-soft badge-status status-label">{{ $task['status'] }}</span>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div id="emptyState" class="text-center py-5 d-none">
                <i class="bi bi-inbox fs-1 text-secondary"></i>
                <h3 class="h6 fw-bold mt-3">No activities found</h3>
                <p class="page-subtitle">Try changing your search or filters.</p>
            </div>
        </div>
    </section>
    <x-footer />
</main>

@push('styles')
    @vite('resources/css/management.css')
@endpush

@push('scripts')
    @vite('resources/js/management.js')
@endpush
@endsection
