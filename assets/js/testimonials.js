/**
 * Fast and Fine Technical Services FZE - Testimonials Carousel Script
 *
 * Features:
 * - Auto-rotating carousel
 * - Touch/swipe support
 * - Keyboard navigation
 * - Responsive slides per view
 * - Auto-pause on hover
 * - Indicator dots
 * - Previous/Next navigation
 *
 * @package FastAndFine
 * @version 1.0.0
 */

(function() {
    'use strict';

    // State
    const state = {
        currentIndex: 0,
        testimonialsData: [],
        autoplayInterval: null,
        autoplayDelay: 5000,
        isAutoplayActive: true,
        slidesPerView: 3,
        slideGap: 30,
        isDragging: false,
        startPos: 0,
        currentTranslate: 0,
        prevTranslate: 0,
        animationID: 0
    };

    // DOM Elements
    const elements = {
        carousel: document.getElementById('testimonialsCarousel'),
        track: document.getElementById('carouselTrack'),
        prevBtn: document.getElementById('testimonialsPrev'),
        nextBtn: document.getElementById('testimonialsNext'),
        indicators: document.getElementById('carouselIndicators'),
        autoplayToggle: document.getElementById('autoplayToggle'),
        cards: null
    };

    // Initialize
    function init() {
        if (!elements.carousel || !elements.track) return;

        loadTestimonialsData();
        calculateSlidesPerView();
        elements.cards = Array.from(elements.track.querySelectorAll('.testimonial-card'));

        if (elements.cards.length === 0) return;

        createIndicators();
        attachEventListeners();
        updateCarousel();
        startAutoplay();
    }

    // Load testimonials data from JSON script tag
    function loadTestimonialsData() {
        const dataElement = document.getElementById('testimonialsData');
        if (dataElement) {
            try {
                state.testimonialsData = JSON.parse(dataElement.textContent);
            } catch (error) {
                console.error('Error parsing testimonials data:', error);
            }
        }
    }

    // Calculate slides per view based on viewport
    function calculateSlidesPerView() {
        const width = window.innerWidth;
        if (width >= 1024) {
            state.slidesPerView = 3;
        } else if (width >= 768) {
            state.slidesPerView = 2;
        } else {
            state.slidesPerView = 1;
        }
    }

    // Create indicator dots
    function createIndicators() {
        if (!elements.indicators) return;

        const totalSlides = elements.cards.length;
        const totalPages = Math.ceil(totalSlides / state.slidesPerView);

        elements.indicators.innerHTML = '';
        for (let i = 0; i < totalPages; i++) {
            const dot = document.createElement('button');
            dot.className = 'indicator-dot';
            dot.setAttribute('aria-label', `Go to slide ${i + 1}`);
            dot.addEventListener('click', () => goToSlide(i * state.slidesPerView));
            elements.indicators.appendChild(dot);
        }
    }

    // Attach event listeners
    function attachEventListeners() {
        // Previous/Next buttons
        if (elements.prevBtn) {
            elements.prevBtn.addEventListener('click', prevSlide);
        }

        if (elements.nextBtn) {
            elements.nextBtn.addEventListener('click', nextSlide);
        }

        // Autoplay toggle
        if (elements.autoplayToggle) {
            elements.autoplayToggle.addEventListener('click', toggleAutoplay);
        }

        // Pause on hover
        if (elements.carousel) {
            elements.carousel.addEventListener('mouseenter', pauseAutoplay);
            elements.carousel.addEventListener('mouseleave', resumeAutoplay);
        }

        // Touch/Swipe support
        if (elements.track) {
            // Touch events
            elements.track.addEventListener('touchstart', touchStart);
            elements.track.addEventListener('touchmove', touchMove);
            elements.track.addEventListener('touchend', touchEnd);

            // Mouse events
            elements.track.addEventListener('mousedown', touchStart);
            elements.track.addEventListener('mousemove', touchMove);
            elements.track.addEventListener('mouseup', touchEnd);
            elements.track.addEventListener('mouseleave', touchEnd);

            // Prevent context menu
            elements.track.addEventListener('contextmenu', e => e.preventDefault());
        }

        // Keyboard navigation
        document.addEventListener('keydown', handleKeyboard);

        // Window resize
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                calculateSlidesPerView();
                createIndicators();
                updateCarousel();
            }, 250);
        });
    }

    // Previous slide
    function prevSlide() {
        state.currentIndex = Math.max(0, state.currentIndex - state.slidesPerView);
        updateCarousel();
        resetAutoplay();

        // Track event
        if (typeof trackEvent === 'function') {
            trackEvent('testimonials_nav', { direction: 'previous' });
        }
    }

    // Next slide
    function nextSlide() {
        const maxIndex = elements.cards.length - state.slidesPerView;
        state.currentIndex = Math.min(maxIndex, state.currentIndex + state.slidesPerView);

        // Loop to beginning if at end
        if (state.currentIndex >= maxIndex) {
            state.currentIndex = 0;
        }

        updateCarousel();
        resetAutoplay();

        // Track event
        if (typeof trackEvent === 'function') {
            trackEvent('testimonials_nav', { direction: 'next' });
        }
    }

    // Go to specific slide
    function goToSlide(index) {
        state.currentIndex = Math.max(0, Math.min(index, elements.cards.length - state.slidesPerView));
        updateCarousel();
        resetAutoplay();
    }

    // Update carousel position
    function updateCarousel() {
        if (!elements.track || elements.cards.length === 0) return;

        const cardWidth = elements.cards[0].offsetWidth;
        const offset = -(state.currentIndex * (cardWidth + state.slideGap));

        elements.track.style.transform = `translateX(${offset}px)`;
        state.currentTranslate = offset;
        state.prevTranslate = offset;

        // Update indicators
        updateIndicators();

        // Update navigation buttons
        updateNavButtons();
    }

    // Update indicator dots
    function updateIndicators() {
        if (!elements.indicators) return;

        const dots = elements.indicators.querySelectorAll('.indicator-dot');
        const currentPage = Math.floor(state.currentIndex / state.slidesPerView);

        dots.forEach((dot, index) => {
            if (index === currentPage) {
                dot.classList.add('active');
            } else {
                dot.classList.remove('active');
            }
        });
    }

    // Update navigation buttons
    function updateNavButtons() {
        if (elements.prevBtn) {
            if (state.currentIndex === 0) {
                elements.prevBtn.disabled = true;
                elements.prevBtn.style.opacity = '0.5';
            } else {
                elements.prevBtn.disabled = false;
                elements.prevBtn.style.opacity = '1';
            }
        }

        if (elements.nextBtn) {
            const maxIndex = elements.cards.length - state.slidesPerView;
            if (state.currentIndex >= maxIndex) {
                elements.nextBtn.style.opacity = '0.5';
            } else {
                elements.nextBtn.style.opacity = '1';
            }
        }
    }

    // Touch/Swipe handlers
    function touchStart(e) {
        if (e.type === 'mousedown') {
            e.preventDefault();
        }

        state.isDragging = true;
        state.startPos = getPositionX(e);
        state.animationID = requestAnimationFrame(animation);

        elements.track.style.cursor = 'grabbing';
    }

    function touchMove(e) {
        if (!state.isDragging) return;

        const currentPosition = getPositionX(e);
        const diff = currentPosition - state.startPos;
        state.currentTranslate = state.prevTranslate + diff;
    }

    function touchEnd() {
        if (!state.isDragging) return;

        state.isDragging = false;
        cancelAnimationFrame(state.animationID);

        const movedBy = state.currentTranslate - state.prevTranslate;
        const threshold = 100;

        // Check if moved enough to trigger slide change
        if (movedBy < -threshold && state.currentIndex < elements.cards.length - state.slidesPerView) {
            nextSlide();
        } else if (movedBy > threshold && state.currentIndex > 0) {
            prevSlide();
        } else {
            // Snap back to current position
            updateCarousel();
        }

        elements.track.style.cursor = 'grab';
    }

    function getPositionX(e) {
        return e.type.includes('mouse') ? e.pageX : e.touches[0].clientX;
    }

    function animation() {
        if (state.isDragging) {
            setSliderPosition();
            requestAnimationFrame(animation);
        }
    }

    function setSliderPosition() {
        elements.track.style.transform = `translateX(${state.currentTranslate}px)`;
    }

    // Keyboard navigation
    function handleKeyboard(e) {
        if (!elements.carousel) return;

        const isCarouselVisible = isInViewport(elements.carousel);
        if (!isCarouselVisible) return;

        if (e.key === 'ArrowLeft') {
            prevSlide();
        } else if (e.key === 'ArrowRight') {
            nextSlide();
        }
    }

    // Check if element is in viewport
    function isInViewport(el) {
        const rect = el.getBoundingClientRect();
        return (
            rect.top >= 0 &&
            rect.left >= 0 &&
            rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
            rect.right <= (window.innerWidth || document.documentElement.clientWidth)
        );
    }

    // Autoplay functions
    function startAutoplay() {
        if (!state.isAutoplayActive) return;

        state.autoplayInterval = setInterval(() => {
            nextSlide();
        }, state.autoplayDelay);
    }

    function stopAutoplay() {
        if (state.autoplayInterval) {
            clearInterval(state.autoplayInterval);
            state.autoplayInterval = null;
        }
    }

    function pauseAutoplay() {
        stopAutoplay();
    }

    function resumeAutoplay() {
        if (state.isAutoplayActive) {
            startAutoplay();
        }
    }

    function resetAutoplay() {
        stopAutoplay();
        if (state.isAutoplayActive) {
            startAutoplay();
        }
    }

    function toggleAutoplay() {
        state.isAutoplayActive = !state.isAutoplayActive;

        if (state.isAutoplayActive) {
            startAutoplay();
            if (elements.autoplayToggle) {
                elements.autoplayToggle.innerHTML = '<i class="fa-solid fa-pause"></i>';
            }
        } else {
            stopAutoplay();
            if (elements.autoplayToggle) {
                elements.autoplayToggle.innerHTML = '<i class="fa-solid fa-play"></i>';
            }
        }

        // Track event
        if (typeof trackEvent === 'function') {
            trackEvent('testimonials_autoplay', { active: state.isAutoplayActive });
        }
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Export to global scope
    window.TestimonialsModule = {
        nextSlide,
        prevSlide,
        goToSlide,
        toggleAutoplay,
        pauseAutoplay,
        resumeAutoplay
    };
})();
