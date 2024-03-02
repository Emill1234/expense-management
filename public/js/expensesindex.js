function toggleFilter() {
    var filterForm = document.getElementById('filterForm');
    filterForm.style.display = (filterForm.style.display === 'none' || filterForm.style.display === '') ? 'block' : 'none';
}

function toggleExport() {
    var exportForm = document.getElementById('exportForm');
    exportForm.style.display = (exportForm.style.display === 'none' || exportForm.style.display === '') ? 'block' : 'none';
}

function clearFilters() {
    document.getElementById('filterAmountFrom').value = '';
    document.getElementById('filterAmountTo').value = '';
    document.getElementById('categoryFilter').value = '';
}

document.addEventListener("DOMContentLoaded", function() {
    var hasFilters = window.location.search.includes('filterAmountFrom') || 
                    window.location.search.includes('filterAmountTo') || 
                    window.location.search.includes('categoryFilter');

    if (hasFilters) {
        toggleFilter();
    }
});