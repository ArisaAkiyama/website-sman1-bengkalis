/**
 * SMAN 1 Bengkalis - Main JavaScript
 * Interactive functionality for the website
 */

document.addEventListener('DOMContentLoaded', function () {

    // ========== MOBILE NAVIGATION ==========
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    const navItems = document.querySelectorAll('.nav-item.has-dropdown');

    // Toggle mobile menu
    if (navToggle) {
        navToggle.addEventListener('click', function () {
            navMenu.classList.toggle('active');
            this.classList.toggle('active');
            document.body.classList.toggle('menu-open');
        });
    }

    // Handle dropdown on mobile
    navItems.forEach(item => {
        const link = item.querySelector('.nav-link');
        link.addEventListener('click', function (e) {
            if (window.innerWidth <= 768) {
                e.preventDefault();
                item.classList.toggle('open');
            }
        });
    });

    // Close menu when clicking outside
    document.addEventListener('click', function (e) {
        if (!navMenu.contains(e.target) && !navToggle.contains(e.target)) {
            navMenu.classList.remove('active');
            navToggle.classList.remove('active');
            document.body.classList.remove('menu-open');
        }
    });

    // ========== HEADER SCROLL EFFECT ==========
    const header = document.getElementById('header');

    window.addEventListener('scroll', function () {
        if (window.scrollY > 100) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });

    // ========== HERO SLIDER ==========
    const slides = document.querySelectorAll('.slide');
    const dots = document.querySelectorAll('.dot');
    const prevBtn = document.querySelector('.slider-prev');
    const nextBtn = document.querySelector('.slider-next');
    let currentSlide = 0;
    let slideInterval;

    function showSlide(index) {
        // Remove active class from all slides and dots
        slides.forEach(slide => slide.classList.remove('active'));
        dots.forEach(dot => dot.classList.remove('active'));

        // Handle index bounds
        if (index >= slides.length) currentSlide = 0;
        else if (index < 0) currentSlide = slides.length - 1;
        else currentSlide = index;

        // Add active class to current slide and dot
        slides[currentSlide].classList.add('active');
        dots[currentSlide].classList.add('active');
    }

    function nextSlide() {
        showSlide(currentSlide + 1);
    }

    function prevSlide() {
        showSlide(currentSlide - 1);
    }

    function startSlider() {
        slideInterval = setInterval(nextSlide, 5000);
    }

    function stopSlider() {
        clearInterval(slideInterval);
    }

    // Event listeners for slider controls
    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            prevSlide();
            stopSlider();
            startSlider();
        });
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            nextSlide();
            stopSlider();
            startSlider();
        });
    }

    // Click on dots
    dots.forEach((dot, index) => {
        dot.addEventListener('click', function () {
            showSlide(index);
            stopSlider();
            startSlider();
        });
    });

    // Start auto-slide
    if (slides.length > 0) {
        startSlider();
    }

    // ========== COUNTER ANIMATION ==========
    const counters = document.querySelectorAll('.stat-number');
    let countersAnimated = false;

    function animateCounters() {
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-count'));
            const duration = 2000;
            const step = target / (duration / 16);
            let current = 0;

            const updateCounter = () => {
                current += step;
                if (current < target) {
                    counter.textContent = Math.floor(current) + '+';
                    requestAnimationFrame(updateCounter);
                } else {
                    counter.textContent = target + '+';
                }
            };

            updateCounter();
        });
    }

    // Check if stats section is in viewport
    function isInViewport(element) {
        const rect = element.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }

    // Trigger counter animation when visible
    const statsSection = document.querySelector('.stats-section');

    window.addEventListener('scroll', function () {
        if (statsSection && isInViewport(statsSection) && !countersAnimated) {
            animateCounters();
            countersAnimated = true;
        }
    });

    // ========== BACK TO TOP BUTTON ==========
    const backToTop = document.getElementById('backToTop');

    window.addEventListener('scroll', function () {
        if (window.scrollY > 500) {
            backToTop.classList.add('visible');
        } else {
            backToTop.classList.remove('visible');
        }
    });

    if (backToTop) {
        backToTop.addEventListener('click', function () {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }

    // ========== SMOOTH SCROLL FOR ANCHOR LINKS ==========
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');

            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);

                if (target) {
                    const headerHeight = header.offsetHeight;
                    const targetPosition = target.getBoundingClientRect().top + window.pageYOffset - headerHeight;

                    window.scrollTo({
                        top: targetPosition,
                        behavior: 'smooth'
                    });

                    // Close mobile menu if open
                    navMenu.classList.remove('active');
                    navToggle.classList.remove('active');
                    document.body.classList.remove('menu-open');
                }
            }
        });
    });

    // ========== SEARCH BUTTON ==========
    const searchBtn = document.getElementById('searchBtn');

    if (searchBtn) {
        // Create search modal
        const searchModal = document.createElement('div');
        searchModal.id = 'searchModal';
        searchModal.innerHTML = `
            <div class="search-modal-overlay"></div>
            <div class="search-modal-content">
                <button class="search-modal-close">&times;</button>
                <h3><i class="fas fa-search"></i> Cari Berita</h3>
                <form id="searchForm" action="news.php" method="GET">
                    <div class="search-input-wrapper">
                        <input type="text" name="q" id="searchInput" placeholder="Ketik kata kunci berita..." autocomplete="off" required>
                        <button type="submit"><i class="fas fa-search"></i></button>
                    </div>
                    <p class="search-hint">Tekan Enter atau klik tombol untuk mencari</p>
                </form>
            </div>
        `;
        document.body.appendChild(searchModal);

        // Add styles for modal
        const searchStyles = document.createElement('style');
        searchStyles.textContent = `
            #searchModal {
                display: none;
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                z-index: 99999;
            }
            #searchModal.active {
                display: block;
            }
            .search-modal-overlay {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0,64,41,0.9);
                backdrop-filter: blur(5px);
            }
            .search-modal-content {
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
                background: white;
                padding: 40px;
                border-radius: 20px;
                width: 90%;
                max-width: 600px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                animation: modalSlideIn 0.3s ease;
            }
            @keyframes modalSlideIn {
                from { opacity: 0; transform: translate(-50%, -60%); }
                to { opacity: 1; transform: translate(-50%, -50%); }
            }
            .search-modal-close {
                position: absolute;
                top: 15px;
                right: 20px;
                background: none;
                border: none;
                font-size: 30px;
                cursor: pointer;
                color: #666;
                transition: color 0.3s;
            }
            .search-modal-close:hover {
                color: #004029;
            }
            .search-modal-content h3 {
                text-align: center;
                color: #004029;
                font-size: 24px;
                margin-bottom: 25px;
            }
            .search-modal-content h3 i {
                margin-right: 10px;
                color: #D4A84B;
            }
            .search-input-wrapper {
                display: flex;
                border: 2px solid #004029;
                border-radius: 50px;
                overflow: hidden;
            }
            .search-input-wrapper input {
                flex: 1;
                padding: 18px 25px;
                border: none;
                font-size: 16px;
                outline: none;
            }
            .search-input-wrapper button {
                padding: 18px 30px;
                background: linear-gradient(135deg, #004029, #006644);
                border: none;
                color: white;
                cursor: pointer;
                transition: background 0.3s;
            }
            .search-input-wrapper button:hover {
                background: linear-gradient(135deg, #006644, #008855);
            }
            .search-hint {
                text-align: center;
                color: #999;
                font-size: 14px;
                margin-top: 15px;
            }
        `;
        document.head.appendChild(searchStyles);

        // Open modal
        searchBtn.addEventListener('click', function () {
            searchModal.classList.add('active');
            document.getElementById('searchInput').focus();
            document.body.style.overflow = 'hidden';
        });

        // Close modal
        const closeBtn = searchModal.querySelector('.search-modal-close');
        const overlay = searchModal.querySelector('.search-modal-overlay');

        closeBtn.addEventListener('click', closeSearchModal);
        overlay.addEventListener('click', closeSearchModal);

        function closeSearchModal() {
            searchModal.classList.remove('active');
            document.body.style.overflow = '';
        }

        // Close on ESC key
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && searchModal.classList.contains('active')) {
                closeSearchModal();
            }
        });
    }

    // ========== ANIMATION ON SCROLL ==========
    const animatedElements = document.querySelectorAll('.program-card, .achievement-card, .news-card');

    const observerOptions = {
        root: null,
        rootMargin: '0px',
        threshold: 0.1
    };

    const observer = new IntersectionObserver(function (entries, observer) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    animatedElements.forEach(element => {
        element.style.opacity = '0';
        element.style.transform = 'translateY(30px)';
        element.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(element);
    });

    // ========== PRELOAD IMAGES ==========
    function preloadImages() {
        const images = document.querySelectorAll('img[src]');
        images.forEach(img => {
            const src = img.getAttribute('src');
            if (src) {
                const preloader = new Image();
                preloader.src = src;
            }
        });
    }

    preloadImages();

    console.log('SMAN 1 Bengkalis website initialized successfully!');
});
