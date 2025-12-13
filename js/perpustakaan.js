/* ============================================
   Perpustakaan Digital - JavaScript
   SMAN 1 Bengkalis
   ============================================ */

document.addEventListener('DOMContentLoaded', function () {
    // Elements
    const mobileToggle = document.getElementById('mobileToggle');
    const searchForm = document.getElementById('searchForm');
    const booksGrid = document.getElementById('booksGrid');

    // Mobile Navigation Toggle
    if (mobileToggle) {
        mobileToggle.addEventListener('click', function () {
            const navMenu = document.querySelector('.nav-menu');
            const btnBack = document.querySelector('.btn-back');

            if (navMenu) {
                navMenu.style.display = navMenu.style.display === 'flex' ? 'none' : 'flex';
                navMenu.style.position = 'absolute';
                navMenu.style.top = '100%';
                navMenu.style.left = '0';
                navMenu.style.right = '0';
                navMenu.style.background = 'white';
                navMenu.style.flexDirection = 'column';
                navMenu.style.padding = '15px';
                navMenu.style.boxShadow = '0 5px 20px rgba(0,0,0,0.1)';
            }
        });
    }

    // Search Form Submit
    if (searchForm) {
        searchForm.addEventListener('submit', function (e) {
            e.preventDefault();

            const title = document.getElementById('searchTitle').value;
            const author = document.getElementById('searchAuthor').value;
            const category = document.getElementById('searchCategory').value;

            // Show loading
            if (booksGrid) {
                booksGrid.innerHTML = `
                    <div style="grid-column: 1/-1; text-align: center; padding: 50px;">
                        <i class="fas fa-spinner fa-spin" style="font-size: 2rem; color: #3498db;"></i>
                        <p style="margin-top: 15px; color: #95a5a6;">Mencari buku...</p>
                    </div>
                `;

                // Simulate search (in real app, this would be an API call)
                setTimeout(function () {
                    if (title || author || category) {
                        showSearchResults(title, author, category);
                    } else {
                        resetBookGrid();
                    }
                }, 1000);
            }
        });
    }

    // Book Action Buttons
    document.querySelectorAll('.btn-read').forEach(btn => {
        btn.addEventListener('click', function () {
            showNotification('Membuka buku...', 'info');
        });
    });

    document.querySelectorAll('.btn-download').forEach(btn => {
        btn.addEventListener('click', function () {
            showNotification('Memulai download...', 'success');
        });
    });

    // Category Links - Only prevent default on toggle, not on actual category links
    document.querySelectorAll('.category-list a').forEach(link => {
        link.addEventListener('click', function (e) {
            // Only prevent default for the toggle button (javascript:void(0))
            if (this.classList.contains('category-toggle')) {
                // Toggle button - don't navigate
                return;
            }

            // For actual category links with href, allow navigation
            // Remove active from all and add to clicked
            document.querySelectorAll('.category-list a').forEach(l => l.classList.remove('active'));
            this.classList.add('active');

            // Let the link navigate normally (don't call e.preventDefault())
        });
    });

    // Pagination
    document.querySelectorAll('.page-btn:not(:disabled)').forEach(btn => {
        btn.addEventListener('click', function () {
            document.querySelectorAll('.page-btn').forEach(b => b.classList.remove('active'));
            if (!this.querySelector('i')) {
                this.classList.add('active');
            }

            // Scroll to top of books section
            const booksSection = document.querySelector('.books-section');
            if (booksSection) {
                booksSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    });
});

// Show search results
function showSearchResults(title, author, category) {
    const booksGrid = document.getElementById('booksGrid');

    // Sample filtered result
    booksGrid.innerHTML = `
        <div class="book-card">
            <div class="book-cover">
                <div class="book-cover-placeholder gradient-1">
                    <i class="fas fa-search"></i>
                    <span>HASIL PENCARIAN</span>
                </div>
                <div class="book-actions">
                    <button class="btn-read" title="Baca"><i class="fas fa-eye"></i></button>
                    <button class="btn-download" title="Download"><i class="fas fa-download"></i></button>
                </div>
            </div>
            <div class="book-info">
                <h3 class="book-title">Hasil Pencarian: ${title || 'Semua'}</h3>
                <p class="book-author"><i class="fas fa-user"></i> Berbagai Penulis</p>
            </div>
        </div>
    `;

    showNotification('Ditemukan 1 buku', 'success');
}

// Reset book grid (show original)
function resetBookGrid() {
    location.reload();
}

// Show notification
function showNotification(message, type = 'info') {
    // Remove existing notification
    const existing = document.querySelector('.notification');
    if (existing) existing.remove();

    // Create notification
    const notification = document.createElement('div');
    notification.className = 'notification';
    notification.style.cssText = `
        position: fixed;
        bottom: 30px;
        right: 30px;
        padding: 15px 25px;
        background: ${type === 'success' ? '#2ecc71' : type === 'error' ? '#e74c3c' : '#3498db'};
        color: white;
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.2);
        font-size: 0.95rem;
        font-weight: 500;
        z-index: 1000;
        animation: slideIn 0.3s ease;
    `;
    notification.innerHTML = `<i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i> ${message}`;

    // Add animation style
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    `;
    document.head.appendChild(style);

    document.body.appendChild(notification);

    // Auto remove after 3 seconds
    setTimeout(() => {
        notification.style.animation = 'slideIn 0.3s ease reverse';
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

// Intersection Observer for book cards animation
const observeBooks = () => {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });

    document.querySelectorAll('.book-card').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.5s ease';
        observer.observe(card);
    });
};

// Run animation observer
setTimeout(observeBooks, 100);
