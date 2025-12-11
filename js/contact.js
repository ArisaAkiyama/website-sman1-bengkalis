/**
 * Contact Page Scripts
 * Used on contact.php for form interaction
 */

document.addEventListener('DOMContentLoaded', function () {
    // Auto-hide success alert after 3 seconds
    const successAlert = document.getElementById('successAlert');

    if (successAlert) {
        setTimeout(() => {
            successAlert.classList.add('fade-out');
            setTimeout(() => {
                successAlert.style.display = 'none';
            }, 500);
        }, 3000);
    }
});
