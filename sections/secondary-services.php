<?php
/**
 * Fast and Fine Technical Services FZE - Secondary Services Section
 *
 * Showcase of additional service offerings beyond core Cleaning & Carpentry
 *
 * @package FastAndFine
 * @version 2.0.0
 */

if(!defined('FAST_FINE_APP')) {
    die('Direct access not permitted');
}

$currentLang = getCurrentLanguage();

// Secondary services configuration
$secondaryServices = [
    [
        'name' => 'Professional Painting',
        'icon' => 'fa-solid fa-paint-roller',
        'price' => 'AED 15/sqm',
        'description' => 'Interior & exterior painting with premium brands',
        'features' => ['2-year warranty', 'Color consultation', 'Surface prep'],
        'color' => '#f59e0b'
    ],
    [
        'name' => 'Plumbing Services',
        'icon' => 'fa-solid fa-faucet-drip',
        'price' => 'From AED 150',
        'description' => 'Emergency & scheduled plumbing solutions',
        'features' => ['24/7 emergency', 'Licensed plumbers', 'Quality parts'],
        'color' => '#3b82f6'
    ],
    [
        'name' => 'Air Conditioning',
        'icon' => 'fa-solid fa-snowflake',
        'price' => 'From AED 200',
        'description' => 'AC installation, maintenance & repair',
        'features' => ['AMC contracts', 'All brands', 'Fast service'],
        'color' => '#06b6d4'
    ],
    [
        'name' => 'Electrical Work',
        'icon' => 'fa-solid fa-bolt',
        'price' => 'From AED 120',
        'description' => 'Complete electrical services & installations',
        'features' => ['Certified electricians', 'Safety first', 'Code compliant'],
        'color' => '#eab308'
    ],
    [
        'name' => 'Gypsum & Partitions',
        'icon' => 'fa-solid fa-border-all',
        'price' => 'From AED 25/sqm',
        'description' => 'False ceilings & partition walls',
        'features' => ['Modern designs', 'Soundproof options', 'LED integration'],
        'color' => '#8b5cf6'
    ],
    [
        'name' => 'Tiling Services',
        'icon' => 'fa-solid fa-th',
        'price' => 'From AED 20/sqm',
        'description' => 'Floor & wall tiling for all spaces',
        'features' => ['Waterproofing', 'All tile types', 'Perfect finishing'],
        'color' => '#ec4899'
    ]
];
?>

<!-- Secondary Services Section -->
<section class="secondary-services-section">
    <div class="container">
        <!-- Section Header -->
        <div class="section-header-secondary">
            <span class="section-badge">
                <i class="fa-solid fa-wrench"></i>
                Additional Services
            </span>
            <h2 class="section-title-secondary">
                Complete Home & Office Solutions
            </h2>
            <p class="section-description-secondary">
                Professional technical services to meet all your maintenance needs
            </p>
        </div>

        <!-- Services Grid -->
        <div class="secondary-services-grid">
            <?php foreach ($secondaryServices as $index => $service): ?>
                <div class="secondary-service-card"
                     data-aos="fade-up"
                     data-aos-delay="<?php echo $index * 100; ?>"
                     style="--service-color: <?php echo $service['color']; ?>">

                    <div class="service-card-icon">
                        <i class="<?php echo $service['icon']; ?>"></i>
                    </div>

                    <div class="service-card-content">
                        <h3 class="service-card-name"><?php echo $service['name']; ?></h3>
                        <p class="service-card-description"><?php echo $service['description']; ?></p>

                        <div class="service-card-price">
                            <span class="price-tag"><?php echo $service['price']; ?></span>
                        </div>

                        <ul class="service-card-features">
                            <?php foreach ($service['features'] as $feature): ?>
                                <li>
                                    <i class="fa-solid fa-check"></i>
                                    <span><?php echo $feature; ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>

                    <div class="service-card-action">
                        <button class="btn-secondary-service" onclick="requestServiceQuote('<?php echo strtolower(str_replace(' ', '-', $service['name'])); ?>')">
                            <i class="fa-brands fa-whatsapp"></i>
                            <span>Get Quote</span>
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- CTA Section -->
        <div class="secondary-services-cta">
            <div class="cta-content-secondary">
                <h3>Need Multiple Services?</h3>
                <p>Get a bundled quote and save up to 20% on combined projects</p>
                <button class="btn btn-primary btn-lg" onclick="openBundleQuote()">
                    <i class="fa-solid fa-calculator"></i>
                    <span>Calculate Bundle Quote</span>
                </button>
            </div>
        </div>
    </div>
</section>

<style>
/* Secondary Services Section Styling */
.secondary-services-section {
    padding: 80px 0;
    background: linear-gradient(to bottom, #f8fafc 0%, #ffffff 100%);
}

.section-header-secondary {
    text-align: center;
    margin-bottom: 60px;
}

.section-badge {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    background: var(--primary-color);
    color: white;
    padding: 8px 20px;
    border-radius: 30px;
    font-size: 0.9rem;
    font-weight: 600;
    margin-bottom: 15px;
}

.section-title-secondary {
    font-size: 2.5rem;
    font-weight: 800;
    color: var(--text-primary);
    margin-bottom: 15px;
}

.section-description-secondary {
    font-size: 1.15rem;
    color: var(--text-secondary);
    max-width: 600px;
    margin: 0 auto;
}

.secondary-services-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 30px;
    margin-bottom: 60px;
}

.secondary-service-card {
    background: white;
    border-radius: 16px;
    padding: 30px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    border: 2px solid transparent;
    position: relative;
    overflow: hidden;
}

.secondary-service-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: var(--service-color);
}

.secondary-service-card:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 40px rgba(0, 0, 0, 0.12);
    border-color: var(--service-color);
}

.service-card-icon {
    width: 70px;
    height: 70px;
    background: linear-gradient(135deg, var(--service-color), color-mix(in srgb, var(--service-color) 80%, black));
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 20px;
    transition: all 0.3s ease;
}

.secondary-service-card:hover .service-card-icon {
    transform: scale(1.1) rotate(5deg);
}

.service-card-icon i {
    font-size: 2rem;
    color: white;
}

.service-card-content {
    margin-bottom: 20px;
}

.service-card-name {
    font-size: 1.4rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 10px;
}

.service-card-description {
    color: var(--text-secondary);
    font-size: 0.95rem;
    margin-bottom: 15px;
    line-height: 1.6;
}

.service-card-price {
    margin-bottom: 15px;
}

.price-tag {
    display: inline-block;
    background: color-mix(in srgb, var(--service-color) 10%, white);
    color: var(--service-color);
    padding: 6px 16px;
    border-radius: 20px;
    font-weight: 700;
    font-size: 1.05rem;
}

.service-card-features {
    list-style: none;
    padding: 0;
    margin: 0 0 20px 0;
}

.service-card-features li {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 6px 0;
    font-size: 0.9rem;
    color: var(--text-secondary);
}

.service-card-features i {
    color: var(--service-color);
    font-size: 0.85rem;
}

.service-card-action {
    margin-top: auto;
}

.btn-secondary-service {
    width: 100%;
    padding: 12px 24px;
    background: var(--service-color);
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 600;
    font-size: 1rem;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.btn-secondary-service:hover {
    background: color-mix(in srgb, var(--service-color) 85%, black);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px color-mix(in srgb, var(--service-color) 30%, transparent);
}

.secondary-services-cta {
    background: linear-gradient(135deg, var(--primary-color), #002D57);
    border-radius: 20px;
    padding: 50px 40px;
    text-align: center;
    color: white;
}

.cta-content-secondary h3 {
    font-size: 2rem;
    font-weight: 800;
    margin-bottom: 15px;
}

.cta-content-secondary p {
    font-size: 1.15rem;
    margin-bottom: 30px;
    opacity: 0.9;
}

.cta-content-secondary .btn {
    background: white;
    color: var(--primary-color);
}

.cta-content-secondary .btn:hover {
    background: #f0f0f0;
    transform: translateY(-2px);
}

/* Responsive */
@media (max-width: 768px) {
    .secondary-services-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }

    .section-title-secondary {
        font-size: 2rem;
    }

    .secondary-services-cta {
        padding: 40px 25px;
    }

    .cta-content-secondary h3 {
        font-size: 1.5rem;
    }
}
</style>

<script>
/**
 * Request quote for secondary service
 */
function requestServiceQuote(serviceName) {
    const whatsappNumber = '+971501234567'; // Update with actual number
    const message = `Hello! I'd like to request a quote for ${serviceName.replace(/-/g, ' ')}. Please provide details and pricing.`;
    const whatsappURL = `https://wa.me/${whatsappNumber.replace(/[^0-9]/g, '')}?text=${encodeURIComponent(message)}`;
    window.open(whatsappURL, '_blank');

    // Track conversion
    if (typeof gtag !== 'undefined') {
        gtag('event', 'secondary_service_quote', {
            'event_category': 'conversion',
            'event_label': serviceName
        });
    }
}

/**
 * Open bundle quote calculator
 */
function openBundleQuote() {
    // For now, open the main calculator
    // Can be enhanced with a dedicated bundle calculator
    if (typeof openCalculator === 'function') {
        openCalculator('cleaning');
    }

    // Track event
    if (typeof gtag !== 'undefined') {
        gtag('event': 'bundle_quote_opened', {
            'event_category': 'engagement'
        });
    }
}
</script>
