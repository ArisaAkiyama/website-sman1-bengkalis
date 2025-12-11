/**
 * Floating Action Button (FAB) Component
 * Used on index.php for quick contact options
 */

document.addEventListener('DOMContentLoaded', function () {
    const fabContainer = document.getElementById('fabContainer');
    const fabButton = document.getElementById('fabButton');

    if (fabButton && fabContainer) {
        // Toggle FAB menu on click
        fabButton.addEventListener('click', function () {
            fabContainer.classList.toggle('active');
        });

        // Close FAB menu when clicking outside
        document.addEventListener('click', function (e) {
            if (!fabContainer.contains(e.target)) {
                fabContainer.classList.remove('active');
            }
        });
    }
});
