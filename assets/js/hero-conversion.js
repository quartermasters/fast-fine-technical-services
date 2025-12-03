/**
 * Fast and Fine Technical Services FZE - Hero Conversion JavaScript
 *
 * Interactive functionality for high-conversion landing page
 *
 * @package FastAndFine
 * @version 2.0.0
 */

(function() {
    'use strict';

    // Configuration
    const config = {
        whatsappNumber: '+971501234567', // Update with actual number
        prices: {
            cleaning: {
                studio: 199,
                '1br': 249,
                '2br': 299,
                '3br': 399,
                villa: 599,
                office: 0 // Custom quote
            },
            carpentry: {
                'kitchen-cabinets': 2500,
                'wardrobe': 3500,
                'tv-unit': 1800,
                'shelving': 800,
                'doors': 1200,
                'furniture': 0 // Custom quote
            }
        },
        urgencyMultipliers: {
            today: 1.15,      // +15%
            tomorrow: 1.0,    // Standard
            'this-week': 0.9  // -10% discount
        }
    };

    /**
     * Open Quote Calculator
     */
    window.openCalculator = function(service) {
        const modal = document.getElementById('quoteCalculator');
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Pre-select service
        const serviceRadio = document.querySelector(`input[name="service"][value="${service}"]`);
        if (serviceRadio) {
            serviceRadio.checked = true;
        }

        // Reset to step 1
        resetCalculatorSteps();
        updateCalculatedPrice();
    };

    /**
     * Close Quote Calculator
     */
    window.closeCalculator = function() {
        const modal = document.getElementById('quoteCalculator');
        modal.classList.remove('active');
        document.body.style.overflow = '';
    };

    /**
     * Reset Calculator Steps
     */
    function resetCalculatorSteps() {
        const steps = document.querySelectorAll('.form-step');
        steps.forEach((step, index) => {
            step.classList.toggle('active', index === 0);
        });
    }

    /**
     * Update Calculated Price
     */
    function updateCalculatedPrice() {
        const form = document.getElementById('quoteForm');
        if (!form) return;

        const service = form.querySelector('input[name="service"]:checked')?.value || 'cleaning';
        const propertyType = form.querySelector('select[name="property_type"]')?.value || '2br';
        const urgency = form.querySelector('input[name="urgency"]:checked')?.value || 'tomorrow';

        // Filter dropdown options based on selected service
        filterPropertyOptions(service);

        let basePrice = config.prices[service][propertyType] || 299;
        const urgencyMultiplier = config.urgencyMultipliers[urgency] || 1.0;

        let finalPrice = Math.round(basePrice * urgencyMultiplier);

        // Update display
        const priceElement = document.getElementById('priceAmount');
        if (priceElement) {
            animateNumber(priceElement, parseInt(priceElement.textContent), finalPrice, 500);
        }
    }

    /**
     * Filter property type options based on service
     */
    function filterPropertyOptions(service) {
        const select = document.getElementById('propertyTypeSelect');
        const label = document.getElementById('step2Label');

        if (!select || !label) return;

        const options = select.querySelectorAll('option');

        // Update label
        if (service === 'carpentry') {
            label.textContent = 'Project Type';
        } else {
            label.textContent = 'Property Type';
        }

        // Show/hide options based on service
        options.forEach(option => {
            if (option.value === '') return; // Keep default option

            const optionService = option.getAttribute('data-service');
            if (optionService === service) {
                option.style.display = '';
            } else {
                option.style.display = 'none';
            }
        });

        // Reset selection
        select.value = '';
    }

    /**
     * Animate Number Change
     */
    function animateNumber(element, from, to, duration) {
        const start = performance.now();
        const difference = to - from;

        function update(currentTime) {
            const elapsed = currentTime - start;
            const progress = Math.min(elapsed / duration, 1);

            const current = Math.round(from + (difference * easeOutQuad(progress)));
            element.textContent = current;

            if (progress < 1) {
                requestAnimationFrame(update);
            }
        }

        requestAnimationFrame(update);
    }

    /**
     * Easing Function
     */
    function easeOutQuad(t) {
        return t * (2 - t);
    }

    /**
     * Handle Quote Form Submission
     */
    function handleQuoteSubmit(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        const service = formData.get('service');
        const propertyType = formData.get('property_type');
        const urgency = formData.get('urgency');
        const name = formData.get('name');
        const phone = formData.get('phone');
        const price = document.getElementById('priceAmount').textContent;

        // Build WhatsApp message
        const message = `Hello! I'd like to request a quote:\n\n` +
            `Name: ${name}\n` +
            `Phone: ${phone}\n` +
            `Service: ${service.charAt(0).toUpperCase() + service.slice(1)}\n` +
            `Property: ${propertyType.toUpperCase()}\n` +
            `Urgency: ${urgency}\n` +
            `Estimated Price: AED ${price}\n\n` +
            `Please confirm availability.`;

        // Open WhatsApp
        const whatsappURL = `https://wa.me/${config.whatsappNumber.replace(/[^0-9]/g, '')}?text=${encodeURIComponent(message)}`;
        window.open(whatsappURL, '_blank');

        // Close modal
        closeCalculator();

        // Track conversion
        if (typeof gtag !== 'undefined') {
            gtag('event', 'quote_request', {
                'event_category': 'conversion',
                'event_label': service,
                'value': parseInt(price)
            });
        }
    }

    /**
     * Open Quick Booking Modal
     */
    window.openQuickBooking = function(service) {
        const modal = document.getElementById('quickBooking');
        modal.classList.add('active');
        document.body.style.overflow = 'hidden';

        // Set service
        document.getElementById('bookingService').value = service;
    };

    /**
     * Close Quick Booking Modal
     */
    window.closeQuickBooking = function() {
        const modal = document.getElementById('quickBooking');
        modal.classList.remove('active');
        document.body.style.overflow = '';
    };

    /**
     * Handle Quick Booking Submission
     */
    function handleQuickBooking(e) {
        e.preventDefault();

        const form = e.target;
        const formData = new FormData(form);

        const service = formData.get('booking_service');
        const name = formData.get('booking_name');
        const phone = formData.get('booking_phone');
        const date = formData.get('booking_date');

        // Build WhatsApp message
        const message = `Hello! I'd like to book a service:\n\n` +
            `Name: ${name}\n` +
            `Phone: ${phone}\n` +
            `Service: ${service.charAt(0).toUpperCase() + service.slice(1)}\n` +
            `Preferred Date: ${date}\n\n` +
            `Please confirm my booking.`;

        // Open WhatsApp
        const whatsappURL = `https://wa.me/${config.whatsappNumber.replace(/[^0-9]/g, '')}?text=${encodeURIComponent(message)}`;
        window.open(whatsappURL, '_blank');

        // Close modal
        closeQuickBooking();

        // Track conversion
        if (typeof gtag !== 'undefined') {
            gtag('event', 'booking_request', {
                'event_category': 'conversion',
                'event_label': service
            });
        }
    }

    /**
     * Animate Stats Counter
     */
    function animateStatsCounters() {
        const statNumbers = document.querySelectorAll('.stat-number[data-count]');

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const element = entry.target;
                    const target = parseInt(element.getAttribute('data-count'));

                    animateNumber(element, 0, target, 2000);
                    observer.unobserve(element);
                }
            });
        }, { threshold: 0.5 });

        statNumbers.forEach(stat => observer.observe(stat));
    }

    /**
     * Panel Parallax Effect
     */
    function initPanelParallax() {
        const panels = document.querySelectorAll('.hero-panel');

        panels.forEach(panel => {
            panel.addEventListener('mousemove', (e) => {
                const rect = panel.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const y = e.clientY - rect.top;

                const centerX = rect.width / 2;
                const centerY = rect.height / 2;

                const deltaX = (x - centerX) / centerX;
                const deltaY = (y - centerY) / centerY;

                const content = panel.querySelector('.panel-content');
                if (content) {
                    content.style.transform = `translate(${deltaX * 10}px, ${deltaY * 10}px)`;
                }
            });

            panel.addEventListener('mouseleave', () => {
                const content = panel.querySelector('.panel-content');
                if (content) {
                    content.style.transform = 'translate(0, 0)';
                }
            });
        });
    }

    /**
     * Initialize All Event Listeners
     */
    function initEventListeners() {
        // Quote form
        const quoteForm = document.getElementById('quoteForm');
        if (quoteForm) {
            quoteForm.addEventListener('submit', handleQuoteSubmit);

            // Update price on any change
            quoteForm.addEventListener('change', updateCalculatedPrice);
        }

        // Quick booking form
        const bookingForm = document.getElementById('quickBookingForm');
        if (bookingForm) {
            bookingForm.addEventListener('submit', handleQuickBooking);
        }

        // Close modals on overlay click
        document.addEventListener('click', (e) => {
            if (e.target.classList.contains('calculator-overlay')) {
                closeCalculator();
            }
            if (e.target.classList.contains('booking-overlay')) {
                closeQuickBooking();
            }
        });

        // Close modals on Escape key
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                closeCalculator();
                closeQuickBooking();
            }
        });

        // Set minimum date for booking (today)
        const dateInput = document.querySelector('input[name="booking_date"]');
        if (dateInput) {
            const today = new Date().toISOString().split('T')[0];
            dateInput.setAttribute('min', today);
            dateInput.value = today;
        }
    }

    /**
     * Initialize on DOM Ready
     */
    function init() {
        console.log('Hero Conversion v2.0 Initialized');

        initEventListeners();
        animateStatsCounters();
        initPanelParallax();

        // Initialize price on load
        updateCalculatedPrice();
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();
