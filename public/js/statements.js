document.addEventListener('DOMContentLoaded', function () {
    const optionsMenu = document.getElementById('options-menu');
    const optionsMenuContent = document.querySelector('[aria-labelledby="options-menu"]');

    if (optionsMenu) {
        optionsMenu.addEventListener('click', function () {
            optionsMenuContent.classList.toggle('hidden');
        });
    }

    const bulkOptionsMenu = document.getElementById('bulk-actions-menu');
    const bulkOptionsMenuContent = document.querySelector('[aria-labelledby="bulk-actions-menu"]');

    if (bulkOptionsMenu) {
        bulkOptionsMenu.addEventListener('click', function () {
            bulkOptionsMenuContent.classList.toggle('hidden');
        });
    }
});
