<?php
/**
 * Fast and Fine Technical Services FZE - High-Conversion Hero Section
 *
 * Features:
 * - Split-screen service showcase
 * - Instant quote calculator
 * - Real-time social proof
 * - Urgency mechanics
 * - Zero-friction booking
 *
 * @package FastAndFine
 * @version 2.0.0 - Conversion Optimized
 */

if(!defined('FAST_FINE_APP')) {
    die('Direct access not permitted');
}

$currentLang = getCurrentLanguage();
?>

<!-- High-Conversion Hero Section -->
<section id="home" class="hero-conversion">

    <!-- Floating Availability Badge -->
    <div class="availability-badge animate-slide-down">
        <div class="technician-avatars">
            <img src="<?php echo assetUrl('images/tech-1.jpg'); ?>" alt="Technician" class="tech-avatar">
            <img src="<?php echo assetUrl('images/tech-2.jpg'); ?>" alt="Technician" class="tech-avatar">
            <img src="<?php echo assetUrl('images/tech-3.jpg'); ?>" alt="Technician" class="tech-avatar">
        </div>
        <div class="availability-text">
            <span class="pulse-dot"></span>
            <strong>12 Technicians</strong> Online Now
        </div>
    </div>

    <!-- Split Hero Container -->
    <div class="hero-split-container">

        <!-- LEFT PANEL: Deep Cleaning Service -->
        <div class="hero-panel hero-cleaning" data-service="cleaning">
            <div class="panel-background">
                <img src="<?php echo assetUrl('images/hero-deep-cleaning.jpg'); ?>"
                     alt="Professional Deep Cleaning Services in Dubai"
                     class="panel-bg-image"
                     loading="eager">
                <div class="panel-overlay"></div>
            </div>

            <div class="panel-content">
                <div class="service-badge">
                    <i class="fa-solid fa-star"></i>
                    <span>Most Popular</span>
                </div>

                <h1 class="panel-title">
                    <span class="title-highlight">Deep Cleaning</span>
                    <span class="title-main">Transform Your Space</span>
                </h1>

                <div class="price-showcase">
                    <div class="price-label">Villa Deep Clean</div>
                    <div class="price-amount">
                        <span class="currency">AED</span>
                        <span class="amount">299</span>
                        <span class="period">/service</span>
                    </div>
                    <div class="price-savings">Save 20% - Limited Offer!</div>
                </div>

                <ul class="service-includes">
                    <li><i class="fa-solid fa-check-circle"></i> All rooms + Kitchen</li>
                    <li><i class="fa-solid fa-check-circle"></i> Bathroom deep clean</li>
                    <li><i class="fa-solid fa-check-circle"></i> Eco-friendly products</li>
                    <li><i class="fa-solid fa-check-circle"></i> 2-hour service</li>
                </ul>

                <div class="panel-cta">
                    <button class="btn btn-primary btn-xl pulse-animation" onclick="openQuickBooking('cleaning')">
                        <i class="fa-brands fa-whatsapp"></i>
                        <span>Book Now on WhatsApp</span>
                        <span class="btn-glow"></span>
                    </button>
                    <button class="btn btn-outline-light btn-lg" onclick="openCalculator('cleaning')">
                        <i class="fa-solid fa-calculator"></i>
                        <span>Get Instant Quote</span>
                    </button>
                </div>

                <div class="trust-indicators">
                    <div class="trust-item">
                        <i class="fa-solid fa-shield-check"></i>
                        <span>Certified Professionals</span>
                    </div>
                    <div class="trust-item">
                        <i class="fa-solid fa-clock"></i>
                        <span>Same-Day Service</span>
                    </div>
                    <div class="trust-item">
                        <i class="fa-solid fa-award"></i>
                        <span>100% Satisfaction</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT PANEL: Carpentry/Wood-Joinery Service -->
        <div class="hero-panel hero-carpentry" data-service="carpentry">
            <div class="panel-background">
                <img src="<?php echo assetUrl('images/hero-carpentry.jpg'); ?>"
                     alt="Custom Wood-Joinery and Carpentry Services in Dubai"
                     class="panel-bg-image"
                     loading="eager">
                <div class="panel-overlay"></div>
            </div>

            <div class="panel-content">
                <div class="service-badge">
                    <i class="fa-solid fa-hammer"></i>
                    <span>Custom Craftsmanship</span>
                </div>

                <h2 class="panel-title">
                    <span class="title-highlight">Wood-Joinery & Carpentry</span>
                    <span class="title-main">Bespoke Solutions</span>
                </h2>

                <div class="price-showcase">
                    <div class="price-label">Custom Kitchen Cabinets</div>
                    <div class="price-amount">
                        <span class="currency">From</span>
                        <span class="amount">AED 2,500</span>
                        <span class="period">/project</span>
                    </div>
                    <div class="price-savings">Free Design Consultation!</div>
                </div>

                <ul class="service-includes">
                    <li><i class="fa-solid fa-check-circle"></i> Custom designs & 3D visualization</li>
                    <li><i class="fa-solid fa-check-circle"></i> Premium wood materials</li>
                    <li><i class="fa-solid fa-check-circle"></i> Professional installation</li>
                    <li><i class="fa-solid fa-check-circle"></i> 5-year warranty</li>
                </ul>

                <div class="panel-cta">
                    <button class="btn btn-primary btn-xl pulse-animation" onclick="openQuickBooking('carpentry')">
                        <i class="fa-brands fa-whatsapp"></i>
                        <span>Get Free Quote</span>
                        <span class="btn-glow"></span>
                    </button>
                    <button class="btn btn-outline-light btn-lg" onclick="openCalculator('carpentry')">
                        <i class="fa-solid fa-pencil-ruler"></i>
                        <span>Design Consultation</span>
                    </button>
                </div>

                <div class="trust-indicators">
                    <div class="trust-item">
                        <i class="fa-solid fa-ruler-combined"></i>
                        <span>Precision Craftsmanship</span>
                    </div>
                    <div class="trust-item">
                        <i class="fa-solid fa-swatchbook"></i>
                        <span>Wide Material Selection</span>
                    </div>
                    <div class="trust-item">
                        <i class="fa-solid fa-award"></i>
                        <span>Master Carpenters</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Center Divider with CTA -->
        <div class="hero-divider">
            <div class="divider-content">
                <div class="divider-icon">
                    <i class="fa-solid fa-bolt"></i>
                </div>
                <div class="divider-text">OR</div>
            </div>
        </div>

    </div>

    <!-- Live Social Proof Notifications -->
    <div class="social-proof-notifications" id="socialProofContainer">
        <!-- Dynamically generated by JavaScript -->
    </div>

    <!-- Urgency Banner -->
    <div class="urgency-banner">
        <div class="container">
            <div class="urgency-content">
                <i class="fa-solid fa-fire flame-animation"></i>
                <span class="urgency-text">
                    <strong>Limited Slots Today:</strong> Only <span id="slotsRemaining" class="highlight">3</span> bookings available
                </span>
                <div class="countdown-timer">
                    <i class="fa-solid fa-clock"></i>
                    <span>Offer expires in:</span>
                    <span id="countdownTimer" class="timer-digits">14:59</span>
                </div>
            </div>
        </div>
    </div>

</section>

<!-- Instant Quote Calculator Modal -->
<div class="quote-calculator-modal" id="quoteCalculator">
    <div class="calculator-overlay" onclick="closeCalculator()"></div>
    <div class="calculator-container">
        <button class="calculator-close" onclick="closeCalculator()">
            <i class="fa-solid fa-times"></i>
        </button>

        <div class="calculator-header">
            <h3>Get Instant Quote</h3>
            <p>Answer 3 simple questions</p>
        </div>

        <div class="calculator-body">
            <form id="quoteForm" class="quote-form">

                <!-- Step 1: Service Type -->
                <div class="form-step active" data-step="1">
                    <label class="form-label">Select Service</label>
                    <div class="service-options">
                        <label class="service-option">
                            <input type="radio" name="service" value="cleaning" checked>
                            <div class="option-card">
                                <i class="fa-solid fa-broom"></i>
                                <span>Deep Cleaning</span>
                            </div>
                        </label>
                        <label class="service-option">
                            <input type="radio" name="service" value="carpentry">
                            <div class="option-card">
                                <i class="fa-solid fa-hammer"></i>
                                <span>Wood-Joinery</span>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Step 2: Property Size / Project Type -->
                <div class="form-step" data-step="2">
                    <label class="form-label" id="step2Label">Property Type</label>
                    <select name="property_type" class="form-select" id="propertyTypeSelect" required>
                        <option value="">Select property type</option>
                        <option value="studio" data-service="cleaning">Studio (AED 199)</option>
                        <option value="1br" data-service="cleaning">1 Bedroom (AED 249)</option>
                        <option value="2br" data-service="cleaning">2 Bedroom (AED 299)</option>
                        <option value="3br" data-service="cleaning">3 Bedroom (AED 399)</option>
                        <option value="villa" data-service="cleaning">Villa (AED 599)</option>
                        <option value="office" data-service="cleaning">Office (Custom Quote)</option>

                        <option value="kitchen-cabinets" data-service="carpentry">Kitchen Cabinets (From AED 2,500)</option>
                        <option value="wardrobe" data-service="carpentry">Custom Wardrobe (From AED 3,500)</option>
                        <option value="tv-unit" data-service="carpentry">TV Unit (From AED 1,800)</option>
                        <option value="shelving" data-service="carpentry">Wall Shelving (From AED 800)</option>
                        <option value="doors" data-service="carpentry">Custom Doors (From AED 1,200)</option>
                        <option value="furniture" data-service="carpentry">Custom Furniture (Custom Quote)</option>
                    </select>
                </div>

                <!-- Step 3: Urgency -->
                <div class="form-step" data-step="3">
                    <label class="form-label">When do you need service?</label>
                    <div class="urgency-options">
                        <label class="urgency-option">
                            <input type="radio" name="urgency" value="today" checked>
                            <div class="option-card">
                                <i class="fa-solid fa-bolt"></i>
                                <span>Today</span>
                                <small>+15% express fee</small>
                            </div>
                        </label>
                        <label class="urgency-option">
                            <input type="radio" name="urgency" value="tomorrow">
                            <div class="option-card">
                                <i class="fa-solid fa-calendar-day"></i>
                                <span>Tomorrow</span>
                                <small>Standard rate</small>
                            </div>
                        </label>
                        <label class="urgency-option">
                            <input type="radio" name="urgency" value="this-week">
                            <div class="option-card">
                                <i class="fa-solid fa-calendar-week"></i>
                                <span>This Week</span>
                                <small>Save 10%</small>
                            </div>
                        </label>
                    </div>
                </div>

                <!-- Price Display -->
                <div class="price-display" id="calculatedPrice">
                    <div class="price-label">Estimated Price:</div>
                    <div class="price-value">
                        <span class="currency">AED</span>
                        <span id="priceAmount">299</span>
                    </div>
                    <div class="price-note">Final price confirmed after inspection</div>
                </div>

                <!-- Contact Form -->
                <div class="contact-fields">
                    <input type="text" name="name" placeholder="Your Name *" class="form-input" required>
                    <input type="tel" name="phone" placeholder="Phone Number *" class="form-input" required>
                </div>

                <!-- Submit Buttons -->
                <div class="calculator-actions">
                    <button type="button" class="btn btn-outline" onclick="closeCalculator()">
                        Cancel
                    </button>
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fa-brands fa-whatsapp"></i>
                        <span>Send Quote to WhatsApp</span>
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>

<!-- Quick Booking WhatsApp Modal -->
<div class="quick-booking-modal" id="quickBooking">
    <div class="booking-overlay" onclick="closeQuickBooking()"></div>
    <div class="booking-container">
        <button class="booking-close" onclick="closeQuickBooking()">
            <i class="fa-solid fa-times"></i>
        </button>

        <div class="booking-header">
            <i class="fa-brands fa-whatsapp whatsapp-icon-large"></i>
            <h3>Book on WhatsApp</h3>
            <p>Connect with us instantly</p>
        </div>

        <div class="booking-body">
            <form id="quickBookingForm" class="booking-form">
                <input type="hidden" name="booking_service" id="bookingService">

                <div class="form-group">
                    <label>Your Name</label>
                    <input type="text" name="booking_name" placeholder="Enter your name" class="form-input" required>
                </div>

                <div class="form-group">
                    <label>Phone Number</label>
                    <input type="tel" name="booking_phone" placeholder="+971 XX XXX XXXX" class="form-input" required>
                </div>

                <div class="form-group">
                    <label>Preferred Date</label>
                    <input type="date" name="booking_date" class="form-input" required>
                </div>

                <button type="submit" class="btn btn-success btn-xl btn-block pulse-animation">
                    <i class="fa-brands fa-whatsapp"></i>
                    <span>Continue to WhatsApp</span>
                </button>

                <div class="booking-guarantee">
                    <i class="fa-solid fa-shield-check"></i>
                    <span>We respond within 2 minutes • 100% Secure</span>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Stats Counter Section -->
<section class="stats-conversion">
    <div class="container">
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number" data-count="2847">0</div>
                    <div class="stat-label">Homes Cleaned This Month</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-star"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">4.9<span class="stat-small">/5</span></div>
                    <div class="stat-label">Average Rating</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number" data-count="24">0</div>
                    <div class="stat-label">Hour Response Time</div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number" data-count="156">0</div>
                    <div class="stat-label">Projects Completed</div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Social proof notifications data
const socialProofMessages = [
    { name: 'Ahmed K.', service: 'villa deep cleaning', area: 'Dubai Marina', minutes: 2 },
    { name: 'Sara M.', service: 'custom kitchen cabinets', area: 'Downtown Dubai', minutes: 5 },
    { name: 'Mohammed A.', service: 'apartment cleaning', area: 'JBR', minutes: 8 },
    { name: 'Fatima H.', service: 'wardrobe installation', area: 'Business Bay', minutes: 12 },
    { name: 'Ali R.', service: 'office deep cleaning', area: 'Palm Jumeirah', minutes: 15 },
    { name: 'Hassan J.', service: 'TV unit carpentry', area: 'Arabian Ranches', minutes: 18 }
];

// Initialize social proof
let proofIndex = 0;
function showSocialProof() {
    const container = document.getElementById('socialProofContainer');
    const proof = socialProofMessages[proofIndex];

    const notification = document.createElement('div');
    notification.className = 'social-proof-notification slide-in';
    notification.innerHTML = `
        <div class="proof-avatar">
            <i class="fa-solid fa-user-circle"></i>
        </div>
        <div class="proof-content">
            <strong>${proof.name}</strong> booked ${proof.service}
            <div class="proof-meta">
                <i class="fa-solid fa-map-marker-alt"></i>
                ${proof.area} • ${proof.minutes} min ago
            </div>
        </div>
        <div class="proof-icon">
            <i class="fa-solid fa-check-circle"></i>
        </div>
    `;

    container.appendChild(notification);

    setTimeout(() => {
        notification.classList.add('slide-out');
        setTimeout(() => notification.remove(), 500);
    }, 4000);

    proofIndex = (proofIndex + 1) % socialProofMessages.length;
}

// Show social proof every 8 seconds
setInterval(showSocialProof, 8000);
setTimeout(showSocialProof, 2000); // First one after 2 seconds

// Countdown timer
function startCountdown() {
    let time = 15 * 60; // 15 minutes
    const timerElement = document.getElementById('countdownTimer');

    setInterval(() => {
        const minutes = Math.floor(time / 60);
        const seconds = time % 60;
        timerElement.textContent = `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        time--;
        if (time < 0) time = 15 * 60; // Reset
    }, 1000);
}

startCountdown();

// Slots remaining animation
function animateSlots() {
    const slotsElement = document.getElementById('slotsRemaining');
    let slots = 3;

    setInterval(() => {
        slots = Math.max(1, slots - (Math.random() > 0.7 ? 1 : 0));
        slotsElement.textContent = slots;
        if (slots === 1) slots = 3; // Reset
    }, 30000); // Every 30 seconds
}

animateSlots();
</script>
