// ============================================
// WeldTrack - Main JavaScript
// ============================================

document.addEventListener('DOMContentLoaded', function() {

    // --- Navbar Scroll Effect ---
    const navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 50);
        });
    }

    // --- Mobile Nav Toggle ---
    const navToggle = document.getElementById('navToggle');
    const navMenu = document.getElementById('navMenu');
    if (navToggle && navMenu) {
        navToggle.addEventListener('click', () => {
            navMenu.classList.toggle('show');
            navToggle.classList.toggle('active');
        });
        // Close menu on link click
        navMenu.querySelectorAll('.nav-link').forEach(link => {
            link.addEventListener('click', () => {
                navMenu.classList.remove('show');
                navToggle.classList.remove('active');
            });
        });
    }

    // --- Scroll Fade In Animation ---
    const fadeElements = document.querySelectorAll('.fade-in');
    if (fadeElements.length) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

        fadeElements.forEach(el => observer.observe(el));
    }

    // --- Counter Animation ---
    const counters = document.querySelectorAll('[data-count]');
    if (counters.length) {
        const countObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const target = parseInt(el.getAttribute('data-count'));
                    const suffix = el.getAttribute('data-suffix') || '';
                    const duration = 2000;
                    const step = Math.ceil(target / (duration / 16));
                    let current = 0;

                    const updateCounter = () => {
                        current += step;
                        if (current >= target) {
                            el.textContent = target.toLocaleString('id-ID') + suffix;
                        } else {
                            el.textContent = current.toLocaleString('id-ID') + suffix;
                            requestAnimationFrame(updateCounter);
                        }
                    };
                    updateCounter();
                    countObserver.unobserve(el);
                }
            });
        }, { threshold: 0.5 });

        counters.forEach(el => countObserver.observe(el));
    }

    // --- Portfolio Filter ---
    const filterBtns = document.querySelectorAll('.filter-btn');
    const portfolioCards = document.querySelectorAll('.portfolio-card');
    if (filterBtns.length && portfolioCards.length) {
        filterBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                const filter = btn.getAttribute('data-filter');

                filterBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');

                portfolioCards.forEach(card => {
                    const category = card.getAttribute('data-category');
                    if (filter === 'all' || category === filter) {
                        card.style.display = '';
                        card.style.animation = 'fadeInUp 0.5s ease';
                    } else {
                        card.style.display = 'none';
                    }
                });
            });
        });
    }

    // --- Auto-dismiss flash alerts ---
    const flashAlert = document.getElementById('flashAlert');
    if (flashAlert) {
        setTimeout(() => {
            flashAlert.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            flashAlert.style.opacity = '0';
            flashAlert.style.transform = 'translateY(-20px)';
            setTimeout(() => flashAlert.remove(), 500);
        }, 5000);
    }

    // --- Smooth Scroll for Anchor Links ---
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function(e) {
            const target = document.querySelector(this.getAttribute('href'));
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
            }
        });
    });
});
