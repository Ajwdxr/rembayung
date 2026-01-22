/**
 * Rembayung - Main JavaScript
 * Includes theme switching system
 */

document.addEventListener('DOMContentLoaded', function () {
    initThemeSwitcher();
    initNavbar();
    initMobileMenu();
    initScrollAnimations();
    initBookingForm();
    initGallery();
});

/**
 * Navbar scroll effect
 */
function initNavbar() {
    const navbar = document.getElementById('navbar');
    if (!navbar) return;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 50) {
            navbar.classList.add('navbar-scrolled');
        } else {
            navbar.classList.remove('navbar-scrolled');
        }
    });
}

/**
 * Mobile menu toggle
 */
function initMobileMenu() {
    const menuBtn = document.getElementById('mobileMenuBtn');
    const mobileMenu = document.getElementById('mobileMenu');

    if (!menuBtn || !mobileMenu) return;

    menuBtn.addEventListener('click', () => {
        mobileMenu.classList.toggle('hidden');
    });

    // Close menu when clicking on a link
    mobileMenu.querySelectorAll('a').forEach(link => {
        link.addEventListener('click', () => {
            mobileMenu.classList.add('hidden');
        });
    });
}

/**
 * Scroll animations using Intersection Observer
 */
/**
 * Scroll animations using Intersection Observer
 */
function initScrollAnimations() {
    const observerOptions = {
        threshold: 0.15,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                // Add 'revealed' class to trigger CSS transitions
                entry.target.classList.add('revealed');

                // Handle legacy animate-on-scroll elements
                if (entry.target.classList.contains('animate-on-scroll')) {
                    entry.target.classList.add('animate-fadeInUp');
                }

                // Optional: Stop observing once revealed
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    // Select all elements to animate
    const animatedElements = document.querySelectorAll('.animate-on-scroll, .scroll-reveal, .scroll-reveal-left, .scroll-reveal-right, .scroll-reveal-scale');

    animatedElements.forEach(el => {
        // For legacy support
        if (el.classList.contains('animate-on-scroll')) {
            el.style.opacity = '0';
        }
        observer.observe(el);
    });
}

/**
 * Booking form handling
 * Note: Form submission is handled by booking.php's inline script
 * This function only sets up date input constraints and real-time validation
 */
function initBookingForm() {
    const form = document.getElementById('bookingForm');
    if (!form) return;

    // Set minimum date to today (for non-calendar based date inputs)
    const dateInput = document.getElementById('booking_date');
    if (dateInput && dateInput.type === 'date') {
        const today = new Date().toISOString().split('T')[0];
        dateInput.setAttribute('min', today);

        // Set max date to 30 days from now
        const maxDate = new Date();
        maxDate.setDate(maxDate.getDate() + 30);
        dateInput.setAttribute('max', maxDate.toISOString().split('T')[0]);
    }

    // Real-time validation only (form submission handled by booking.php)
    form.querySelectorAll('.form-input').forEach(input => {
        input.addEventListener('blur', function () {
            validateField(this);
        });

        input.addEventListener('input', function () {
            if (this.classList.contains('error')) {
                validateField(this);
            }
        });
    });
}

/**
 * Validate booking form
 */
function validateBookingForm() {
    const form = document.getElementById('bookingForm');
    let isValid = true;

    // Required fields - updated for session-based time slots
    const requiredFields = ['booking_date', 'session_id', 'time_slot_id', 'pax', 'name', 'phone', 'email'];

    requiredFields.forEach(fieldName => {
        const field = form.querySelector(`[name="${fieldName}"]`);
        if (field && !validateField(field)) {
            isValid = false;
        }
    });

    return isValid;
}

/**
 * Validate individual field
 */
function validateField(field) {
    if (!field) return true; // Skip if field doesn't exist
    const value = field.value.trim();
    let isValid = true;
    let errorMessage = '';

    // Remove previous error state
    field.classList.remove('error');
    const existingError = field.parentElement.querySelector('.error-message');
    if (existingError) existingError.remove();

    // Check required - only for fields with required attribute
    if (!value && field.hasAttribute('required')) {
        isValid = false;
        errorMessage = 'This field is required';
    }

    // Email validation
    if (field.name === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            errorMessage = 'Please enter a valid email address';
        }
    }

    // Phone validation
    if (field.name === 'phone' && value) {
        const phoneRegex = /^[\d\s\-\+\(\)]{8,}$/;
        if (!phoneRegex.test(value)) {
            isValid = false;
            errorMessage = 'Please enter a valid phone number';
        }
    }

    // Pax validation
    if (field.name === 'pax' && value) {
        const pax = parseInt(value);
        if (pax < 2 || pax > 8) {
            isValid = false;
            errorMessage = 'Party size must be between 2 and 8';
        }
    }

    // Date validation
    if (field.name === 'booking_date' && value) {
        const selectedDate = new Date(value);
        const today = new Date();
        today.setHours(0, 0, 0, 0);

        if (selectedDate < today) {
            isValid = false;
            errorMessage = 'Please select a future date';
        }
    }

    // Show error if invalid
    if (!isValid) {
        field.classList.add('error');
        const errorEl = document.createElement('p');
        errorEl.className = 'error-message text-red-500 text-sm mt-1';
        errorEl.textContent = errorMessage;
        field.parentElement.appendChild(errorEl);
    }

    return isValid;
}

/**
 * Gallery lightbox
 */
function initGallery() {
    const galleryItems = document.querySelectorAll('.gallery-item');
    if (!galleryItems.length) return;

    // Create lightbox element
    const lightbox = document.createElement('div');
    lightbox.className = 'lightbox';
    lightbox.innerHTML = `
        <button class="lightbox-close">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
            </svg>
        </button>
        <img src="" alt="">
    `;
    document.body.appendChild(lightbox);

    const lightboxImg = lightbox.querySelector('img');
    const closeBtn = lightbox.querySelector('.lightbox-close');

    // Open lightbox
    galleryItems.forEach(item => {
        item.addEventListener('click', () => {
            const img = item.querySelector('img');
            if (img) {
                lightboxImg.src = img.src;
                lightboxImg.alt = img.alt;
                lightbox.classList.add('active');
                document.body.style.overflow = 'hidden';
            }
        });
    });

    // Close lightbox
    function closeLightbox() {
        lightbox.classList.remove('active');
        document.body.style.overflow = '';
    }

    closeBtn.addEventListener('click', closeLightbox);
    lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox) closeLightbox();
    });

    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') closeLightbox();
    });
}

/**
 * Toast notification
 */
function showToast(type, message) {
    // Remove existing toasts
    document.querySelectorAll('.toast').forEach(t => t.remove());

    const toast = document.createElement('div');
    toast.className = `toast toast-${type}`;
    toast.innerHTML = `
        <div class="flex items-center space-x-3">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${type === 'success'
            ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>'
            : type === 'error'
                ? '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>'
                : '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>'
        }
            </svg>
            <span>${message}</span>
        </div>
    `;

    document.body.appendChild(toast);

    // Trigger animation
    setTimeout(() => toast.classList.add('show'), 10);

    // Auto remove
    setTimeout(() => {
        toast.classList.remove('show');
        setTimeout(() => toast.remove(), 300);
    }, 5000);
}

/**
 * Smooth scroll to element
 */
function scrollToElement(selector) {
    const element = document.querySelector(selector);
    if (element) {
        element.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

/**
 * Theme Switcher System
 * Supports 5 themes with localStorage persistence
 */
function initThemeSwitcher() {
    const STORAGE_KEY = 'rembayung-theme';
    const DEFAULT_THEME = 'modern-malaysian';

    // Theme display names for toast notifications
    const themeNames = {
        'modern-malaysian': 'Modern Malaysian',
        'rustic-elegance': 'Rustic Elegance',
        'editorial-food': 'Editorial Food',
        'dark-moody': 'Dark & Moody',
        'signature-rembayung': 'Signature Rembayung'
    };

    // Get stored theme or default
    const storedTheme = localStorage.getItem(STORAGE_KEY) || DEFAULT_THEME;

    // Desktop theme switcher elements
    const themeSwitcherBtn = document.getElementById('themeSwitcherBtn');
    const themePanel = document.getElementById('themePanel');
    const themeOptions = document.querySelectorAll('.theme-option');

    // Mobile theme buttons
    const mobileThemeOptions = document.querySelectorAll('.mobile-theme-btn');

    /**
     * Apply theme to document
     * @param {string} theme - Theme identifier
     * @param {boolean} doShowToast - Whether to show toast notification
     */
    function applyTheme(theme, doShowToast = false) {
        // Apply theme attribute
        document.body.setAttribute('data-theme', theme);

        // Save to localStorage
        localStorage.setItem(STORAGE_KEY, theme);

        // Update active states
        themeOptions.forEach(opt => {
            opt.classList.toggle('active', opt.dataset.theme === theme);
        });
        mobileThemeOptions.forEach(btn => {
            btn.classList.toggle('active', btn.dataset.theme === theme);
        });

        // Show toast notification (only when user switches)
        if (doShowToast && typeof window.showToast === 'function') {
            window.showToast('info', `Theme: ${themeNames[theme] || theme}`);
        }
    }

    // Apply theme immediately on load (after variables are declared)
    applyTheme(storedTheme, false);

    // Toggle theme panel (desktop)
    if (themeSwitcherBtn && themePanel) {
        themeSwitcherBtn.addEventListener('click', (e) => {
            e.stopPropagation();
            themePanel.classList.toggle('active');
        });

        // Close panel when clicking outside
        document.addEventListener('click', (e) => {
            if (!e.target.closest('.theme-switcher')) {
                themePanel.classList.remove('active');
            }
        });

        // Close panel on escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                themePanel.classList.remove('active');
            }
        });
    }

    // Desktop theme option clicks
    themeOptions.forEach(option => {
        option.addEventListener('click', () => {
            const theme = option.dataset.theme;
            applyTheme(theme, true);

            // Update active state
            themeOptions.forEach(opt => opt.classList.remove('active'));
            option.classList.add('active');

            // Update mobile buttons too
            mobileThemeOptions.forEach(btn => {
                btn.classList.toggle('active', btn.dataset.theme === theme);
            });

            // Close panel
            if (themePanel) themePanel.classList.remove('active');
        });
    });

    // Mobile theme button clicks
    mobileThemeOptions.forEach(btn => {
        btn.addEventListener('click', () => {
            const theme = btn.dataset.theme;
            applyTheme(theme, true);

            // Update active state
            mobileThemeOptions.forEach(b => b.classList.remove('active'));
            btn.classList.add('active');

            // Update desktop options too
            themeOptions.forEach(opt => {
                opt.classList.toggle('active', opt.dataset.theme === theme);
            });
        });
    });
}
