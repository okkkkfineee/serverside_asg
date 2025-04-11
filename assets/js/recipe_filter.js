document.addEventListener('DOMContentLoaded', function () {
    // Get the filter form and inputs
    const filterForm = document.getElementById('filterForm');
    const titleInput = document.getElementById('titleType');
    const cuisineSelect = document.getElementById('cuisineType');
    const difficultySelect = document.getElementById('difficultyLevel');

    // Listen for changes on filter inputs
    titleInput.addEventListener('input', function () {
        filterForm.submit();
    });
    cuisineSelect.addEventListener('change', function () {
        filterForm.submit();
    });
    difficultySelect.addEventListener('change', function () {
        filterForm.submit();
    });
});
