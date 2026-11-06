<div class="d-flex align-items-center justify-content-between py-2 border-bottom gap-2">
    <div class="me-2">
        <div class="fw-semibold dashboard-task-title">
            {{ $task['title'] }}
        </div>
        <small class="text-secondary">{{ $task['category'] }}</small>
    </div>

    <span class="badge-soft {{ $badgeClass }}">{{ $badgeLabel }}</span>
</div>
