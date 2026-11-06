function initializeTaskFilters() {
    const searchInput = document.getElementById('searchTask');
    const categoryFilter = document.getElementById('filterCategory');
    const priorityFilter = document.getElementById('filterPriority');
    const statusFilter = document.getElementById('filterStatus');
    const resetButton = document.getElementById('resetFilter');
    const emptyState = document.getElementById('emptyState');
    const taskCount = document.getElementById('taskCount');
    const taskRows = Array.from(document.querySelectorAll('.task-row'));

    function taskMatchesFilters(taskRow) {
        const searchText = searchInput.value.toLowerCase();
        const matchesSearch = taskRow.dataset.title.includes(searchText);
        const matchesCategory =
            !categoryFilter.value
            || taskRow.dataset.category === categoryFilter.value;
        const matchesPriority =
            !priorityFilter.value
            || taskRow.dataset.priority === priorityFilter.value;
        const matchesStatus =
            !statusFilter.value
            || taskRow.dataset.status === statusFilter.value;

        return matchesSearch
            && matchesCategory
            && matchesPriority
            && matchesStatus;
    }

    function updateTaskList() {
        let visibleTaskCount = 0;

        taskRows.forEach(function (taskRow) {
            const matchesFilters = taskMatchesFilters(taskRow);

            taskRow.classList.toggle('d-none', !matchesFilters);

            if (matchesFilters) {
                visibleTaskCount += 1;
            }
        });

        emptyState.classList.toggle('d-none', visibleTaskCount > 0);
        taskCount.textContent = `${visibleTaskCount} Tasks`;
    }

    function resetFilters() {
        searchInput.value = '';
        categoryFilter.value = '';
        priorityFilter.value = '';
        statusFilter.value = '';

        updateTaskList();
    }

    searchInput.addEventListener('input', updateTaskList);
    categoryFilter.addEventListener('change', updateTaskList);
    priorityFilter.addEventListener('change', updateTaskList);
    statusFilter.addEventListener('change', updateTaskList);
    resetButton.addEventListener('click', resetFilters);

    updateTaskList();
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeTaskFilters);
} else {
    initializeTaskFilters();
}
