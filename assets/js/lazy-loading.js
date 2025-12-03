/**
 * Fast and Fine Technical Services FZE - Advanced Lazy Loading System
 *
 * Features:
 * - Intersection Observer API for images
 * - Progressive image loading with blur-up effect
 * - Background image lazy loading
 * - Responsive image loading based on viewport
 * - Fallback for older browsers
 *
 * @package FastAndFine
 * @version 1.0.0
 */

(function() {
    'use strict';

    // Configuration
    const config = {
        rootMargin: '50px 0px', // Load images 50px before they enter viewport
        threshold: 0.01,
        enableBlurEffect: true,
        placeholderClass: 'lazy-placeholder',
        loadedClass: 'lazy-loaded',
        errorClass: 'lazy-error'
    };

    /**
     * Check if Intersection Observer is supported
     */
    const supportsIntersectionObserver = 'IntersectionObserver' in window;

    /**
     * Lazy load images using Intersection Observer
     */
    function lazyLoadImages() {
        const lazyImages = document.querySelectorAll('img[loading="lazy"]');

        if (!supportsIntersectionObserver) {
            // Fallback: Load all images immediately
            lazyImages.forEach(img => {
                if (img.dataset.src) {
                    img.src = img.dataset.src;
                }
                if (img.dataset.srcset) {
                    img.srcset = img.dataset.srcset;
                }
            });
            return;
        }

        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    loadImage(img);
                    observer.unobserve(img);
                }
            });
        }, {
            rootMargin: config.rootMargin,
            threshold: config.threshold
        });

        lazyImages.forEach(img => {
            // Add placeholder class for blur effect
            if (config.enableBlurEffect) {
                img.classList.add(config.placeholderClass);
            }
            imageObserver.observe(img);
        });
    }

    /**
     * Load individual image
     */
    function loadImage(img) {
        const src = img.dataset.src || img.src;
        const srcset = img.dataset.srcset;

        // Create a new image to preload
        const tempImg = new Image();

        tempImg.onload = function() {
            // Set the actual source
            if (srcset) {
                img.srcset = srcset;
            }
            if (src && img.dataset.src) {
                img.src = src;
            }

            // Add loaded class for fade-in effect
            img.classList.remove(config.placeholderClass);
            img.classList.remove(config.errorClass);
            img.classList.add(config.loadedClass);

            // Remove data attributes to prevent reloading
            delete img.dataset.src;
            delete img.dataset.srcset;
        };

        tempImg.onerror = function() {
            img.classList.remove(config.placeholderClass);
            img.classList.add(config.errorClass);
            console.warn('Failed to load image:', src);

            // Set alt text as visible if available
            if (img.alt) {
                img.setAttribute('title', 'Image failed to load: ' + img.alt);
            }
        };

        // Start loading
        tempImg.src = src;
        if (srcset) {
            tempImg.srcset = srcset;
        }
    }

    /**
     * Lazy load background images
     */
    function lazyLoadBackgrounds() {
        const lazyBackgrounds = document.querySelectorAll('[data-bg]');

        if (!supportsIntersectionObserver) {
            // Fallback: Load all backgrounds immediately
            lazyBackgrounds.forEach(element => {
                element.style.backgroundImage = `url('${element.dataset.bg}')`;
                delete element.dataset.bg;
            });
            return;
        }

        const bgObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    element.style.backgroundImage = `url('${element.dataset.bg}')`;
                    element.classList.add(config.loadedClass);
                    delete element.dataset.bg;
                    observer.unobserve(element);
                }
            });
        }, {
            rootMargin: config.rootMargin,
            threshold: config.threshold
        });

        lazyBackgrounds.forEach(bg => {
            bgObserver.observe(bg);
        });
    }

    /**
     * Preload critical images (above the fold)
     */
    function preloadCriticalImages() {
        const criticalImages = document.querySelectorAll('img[loading="eager"]');

        criticalImages.forEach(img => {
            if (img.dataset.src) {
                img.src = img.dataset.src;
                delete img.dataset.src;
            }
            if (img.dataset.srcset) {
                img.srcset = img.dataset.srcset;
                delete img.dataset.srcset;
            }
        });
    }

    /**
     * Handle responsive images based on viewport
     */
    function handleResponsiveImages() {
        const responsiveImages = document.querySelectorAll('img[data-src-mobile][data-src-tablet][data-src-desktop]');

        responsiveImages.forEach(img => {
            const viewportWidth = window.innerWidth;
            let src;

            if (viewportWidth < 640) {
                src = img.dataset.srcMobile;
            } else if (viewportWidth < 1024) {
                src = img.dataset.srcTablet;
            } else {
                src = img.dataset.srcDesktop;
            }

            if (src) {
                img.dataset.src = src;
            }
        });
    }

    /**
     * Add CSS for lazy loading effects
     */
    function addLazyLoadingStyles() {
        if (document.getElementById('lazy-loading-styles')) {
            return; // Styles already added
        }

        const style = document.createElement('style');
        style.id = 'lazy-loading-styles';
        style.textContent = `
            /* Lazy loading placeholder */
            .lazy-placeholder {
                filter: blur(10px);
                transform: scale(1.05);
                transition: filter 0.3s ease-out, transform 0.3s ease-out;
            }

            /* Lazy loaded state */
            .lazy-loaded {
                filter: blur(0);
                transform: scale(1);
                animation: fadeIn 0.3s ease-out;
            }

            /* Error state */
            .lazy-error {
                opacity: 0.5;
                filter: grayscale(100%);
            }

            /* Fade in animation */
            @keyframes fadeIn {
                from {
                    opacity: 0;
                }
                to {
                    opacity: 1;
                }
            }

            /* Background images lazy loading */
            [data-bg] {
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
            }

            /* Reduce motion for users who prefer it */
            @media (prefers-reduced-motion: reduce) {
                .lazy-placeholder {
                    filter: none;
                    transform: none;
                    transition: none;
                }
                .lazy-loaded {
                    animation: none;
                }
            }
        `;
        document.head.appendChild(style);
    }

    /**
     * Check existing images for load errors
     */
    function checkExistingImages() {
        const allImages = document.querySelectorAll('img');

        allImages.forEach(img => {
            // Check if image failed to load
            if (!img.complete || img.naturalWidth === 0) {
                img.addEventListener('error', function() {
                    this.classList.add(config.errorClass);
                    console.warn('Image failed to load:', this.src || this.getAttribute('src'));
                });
            }
        });
    }

    /**
     * Initialize lazy loading system
     */
    function init() {
        // Add CSS styles
        addLazyLoadingStyles();

        // Check existing images for errors
        checkExistingImages();

        // Preload critical images first
        preloadCriticalImages();

        // Handle responsive images
        handleResponsiveImages();

        // Initialize lazy loading
        lazyLoadImages();
        lazyLoadBackgrounds();

        // Re-initialize on dynamic content load
        document.addEventListener('contentLoaded', () => {
            checkExistingImages();
            lazyLoadImages();
            lazyLoadBackgrounds();
        });

        // Handle window resize for responsive images
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                handleResponsiveImages();
                lazyLoadImages();
            }, 250);
        });
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Export for manual triggering
    window.LazyLoad = {
        init: init,
        loadImage: loadImage,
        refresh: function() {
            lazyLoadImages();
            lazyLoadBackgrounds();
        }
    };

})();
