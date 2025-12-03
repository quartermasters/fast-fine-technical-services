<?php
/**
 * Fast and Fine Technical Services FZE - Dubai Premium Hero Section
 *
 * Concept: Premium positioning as Dubai's most trusted technical service provider
 * Design: Clean, professional, trustworthy
 * Focus: Brand building, no pricing, service quality
 *
 * @package FastAndFine
 * @version 3.0.0 - Premium Dubai Edition
 */

if(!defined('FAST_FINE_APP')) {
    die('Direct access not permitted');
}

$currentLang = getCurrentLanguage();

// Define primary services
$primaryServices = [
    [
        'id' => 'cleaning',
        'name' => 'Deep Cleaning',
        'name_ar' => 'التنظيف العميق',
        'icon' => 'fa-solid fa-sparkles',
        'color' => '#10b981',
        'description' => 'Professional cleaning for homes & offices',
        'link' => '#services'
    ],
    [
        'id' => 'carpentry',
        'name' => 'Custom Carpentry',
        'name_ar' => 'النجارة المخصصة',
        'icon' => 'fa-solid fa-hammer',
        'color' => '#8b5cf6',
        'description' => 'Bespoke furniture & woodwork',
        'link' => '#services'
    ],
    [
        'id' => 'plumbing',
        'name' => 'Plumbing Solutions',
        'name_ar' => 'حلول السباكة',
        'icon' => 'fa-solid fa-faucet-drip',
        'color' => '#3b82f6',
        'description' => 'Emergency & scheduled plumbing',
        'link' => '#services'
    ],
    [
        'id' => 'painting',
        'name' => 'Professional Painting',
        'name_ar' => 'الدهان المحترف',
        'icon' => 'fa-solid fa-paint-roller',
        'color' => '#f59e0b',
        'description' => 'Interior & exterior painting',
        'link' => '#services'
    ],
    [
        'id' => 'ac',
        'name' => 'AC Maintenance',
        'name_ar' => 'صيانة المكيفات',
        'icon' => 'fa-solid fa-snowflake',
        'color' => '#06b6d4',
        'description' => 'Installation, repair & maintenance',
        'link' => '#services'
    ],
    [
        'id' => 'electrical',
        'name' => 'Electrical Work',
        'name_ar' => 'الأعمال الكهربائية',
        'icon' => 'fa-solid fa-bolt',
        'color' => '#eab308',
        'description' => 'Certified electrical services',
        'link' => '#services'
    ]
];
?>

<!-- Dubai Premium Hero Section -->
<section id="home" class="hero-premium-dubai">

    <!-- Dubai Skyline Decoration -->
    <div class="dubai-skyline-container">
        <svg class="dubai-skyline" viewBox="0 0 1200 120" preserveAspectRatio="xMidYMid slice" xmlns="http://www.w3.org/2000/svg">
            <!-- Burj Khalifa -->
            <polygon points="600,10 605,100 595,100" fill="#002D57" opacity="0.9"/>
            <polygon points="600,5 602,10 598,10" fill="#009FE3"/>

            <!-- Emirates Towers -->
            <rect x="520" y="40" width="15" height="60" fill="#002D57" opacity="0.8"/>
            <polygon points="527.5,40 520,40 520,35 535,35 535,40" fill="#009FE3"/>

            <!-- Burj Al Arab (sail shape) -->
            <path d="M 450 100 Q 450 50, 470 30 L 470 100 Z" fill="#002D57" opacity="0.85"/>
            <circle cx="470" cy="30" r="3" fill="#FDB913"/>

            <!-- Additional buildings -->
            <rect x="380" y="55" width="20" height="45" fill="#002D57" opacity="0.7"/>
            <rect x="410" y="65" width="15" height="35" fill="#002D57" opacity="0.75"/>
            <rect x="550" y="60" width="18" height="40" fill="#002D57" opacity="0.7"/>
            <rect x="650" y="50" width="22" height="50" fill="#002D57" opacity="0.8"/>
            <rect x="680" y="65" width="16" height="35" fill="#002D57" opacity="0.75"/>
            <rect x="720" y="55" width="20" height="45" fill="#002D57" opacity="0.7"/>

            <!-- More background buildings -->
            <rect x="300" y="70" width="12" height="30" fill="#002D57" opacity="0.5"/>
            <rect x="320" y="75" width="10" height="25" fill="#002D57" opacity="0.55"/>
            <rect x="340" y="72" width="14" height="28" fill="#002D57" opacity="0.5"/>
            <rect x="750" y="70" width="12" height="30" fill="#002D57" opacity="0.5"/>
            <rect x="770" y="75" width="10" height="25" fill="#002D57" opacity="0.55"/>
            <rect x="790" y="72" width="14" height="28" fill="#002D57" opacity="0.5"/>
        </svg>
    </div>

    <!-- Main Hero Content -->
    <div class="container">
        <div class="hero-content-premium">

            <!-- Main Headline -->
            <h1 class="hero-title-main" data-aos="fade-up">
                <?php echo $currentLang === 'ar' ? 'خدمات دبي التقنية الأكثر ثقة' : "Dubai's Most Trusted"; ?>
                <br>
                <span class="title-highlight">
                    <?php echo $currentLang === 'ar' ? 'خدمات تقنية' : 'Technical Services'; ?>
                </span>
            </h1>

            <!-- Sub-headline -->
            <p class="hero-subtitle-premium" data-aos="fade-up" data-aos-delay="100">
                <?php echo $currentLang === 'ar' ? 'نخدم أكثر من 120 مجتمع في دبي' : 'Serving 120+ Communities Across Dubai'; ?>
            </p>

            <!-- Service Icons Grid -->
            <div class="services-grid-premium" data-aos="fade-up" data-aos-delay="200">
                <?php foreach ($primaryServices as $index => $service): ?>
                    <div class="service-icon-card"
                         data-service="<?php echo $service['id']; ?>"
                         data-aos="zoom-in"
                         data-aos-delay="<?php echo 300 + ($index * 50); ?>"
                         style="--service-color: <?php echo $service['color']; ?>">
                        <a href="<?php echo $service['link']; ?>" class="service-card-link">
                            <div class="service-icon-large">
                                <i class="<?php echo $service['icon']; ?>"></i>
                            </div>
                            <h3 class="service-name">
                                <?php echo $currentLang === 'ar' ? $service['name_ar'] : $service['name']; ?>
                            </h3>
                            <p class="service-description">
                                <?php echo $service['description']; ?>
                            </p>
                            <div class="service-arrow">
                                <i class="fa-solid fa-arrow-right"></i>
                            </div>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Trust Indicators Bar -->
            <div class="trust-bar-premium" data-aos="fade-up" data-aos-delay="600">
                <div class="trust-item-premium">
                    <i class="fa-solid fa-shield-check"></i>
                    <span><?php echo $currentLang === 'ar' ? 'محترفون معتمدون' : 'Certified Professionals'; ?></span>
                </div>
                <div class="trust-divider">•</div>
                <div class="trust-item-premium">
                    <i class="fa-solid fa-star"></i>
                    <span><?php echo $currentLang === 'ar' ? 'تقييم 4.9' : '4.9★ Rating'; ?></span>
                </div>
                <div class="trust-divider">•</div>
                <div class="trust-item-premium">
                    <i class="fa-solid fa-clock"></i>
                    <span><?php echo $currentLang === 'ar' ? 'متاح 24/7' : '24/7 Available'; ?></span>
                </div>
            </div>

            <!-- Call to Action Buttons -->
            <div class="hero-actions-premium" data-aos="fade-up" data-aos-delay="700">
                <a href="#services" class="btn-premium btn-premium-primary">
                    <i class="fa-solid fa-th-large"></i>
                    <span><?php echo $currentLang === 'ar' ? 'تصفح الخدمات' : 'Browse Services'; ?></span>
                </a>
                <a href="#contact" class="btn-premium btn-premium-secondary">
                    <i class="fa-solid fa-calendar-check"></i>
                    <span><?php echo $currentLang === 'ar' ? 'احجز زيارة' : 'Book a Visit'; ?></span>
                </a>
            </div>

            <!-- Featured In Section -->
            <div class="featured-in-premium" data-aos="fade-up" data-aos-delay="800">
                <p class="featured-label">
                    <?php echo $currentLang === 'ar' ? 'ظهرت في:' : 'As Featured In:'; ?>
                </p>
                <div class="media-logos">
                    <div class="media-logo" title="Dubai Eye 103.8">
                        <i class="fa-solid fa-radio"></i>
                        <span>Dubai Eye 103.8</span>
                    </div>
                    <div class="media-logo" title="Gulf News">
                        <i class="fa-solid fa-newspaper"></i>
                        <span>Gulf News</span>
                    </div>
                    <div class="media-logo" title="Khaleej Times">
                        <i class="fa-solid fa-file-lines"></i>
                        <span>Khaleej Times</span>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Scroll Indicator -->
    <div class="scroll-indicator-premium" data-aos="fade-in" data-aos-delay="1000">
        <div class="scroll-text">
            <?php echo $currentLang === 'ar' ? 'اسحب للأسفل' : 'Scroll to explore'; ?>
        </div>
        <div class="scroll-arrow-animated">
            <i class="fa-solid fa-chevron-down"></i>
        </div>
    </div>

</section>

<!-- Quick Stats Section (below hero) -->
<section class="stats-premium">
    <div class="container">
        <div class="stats-grid-premium">

            <div class="stat-card-premium" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-icon-premium">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="stat-content-premium">
                    <div class="stat-number-premium" data-count="2847">0</div>
                    <div class="stat-label-premium">
                        <?php echo $currentLang === 'ar' ? 'المنازل تم تنظيفها' : 'Happy Customers'; ?>
                    </div>
                </div>
            </div>

            <div class="stat-card-premium" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-icon-premium">
                    <i class="fa-solid fa-star"></i>
                </div>
                <div class="stat-content-premium">
                    <div class="stat-number-premium">4.9<span class="stat-decimal">/5</span></div>
                    <div class="stat-label-premium">
                        <?php echo $currentLang === 'ar' ? 'متوسط التقييم' : 'Average Rating'; ?>
                    </div>
                </div>
            </div>

            <div class="stat-card-premium" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-icon-premium">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <div class="stat-content-premium">
                    <div class="stat-number-premium" data-count="2">0</div>
                    <div class="stat-label-premium">
                        <?php echo $currentLang === 'ar' ? 'ساعات وقت الاستجابة' : 'Hour Response Time'; ?>
                    </div>
                </div>
            </div>

            <div class="stat-card-premium" data-aos="fade-up" data-aos-delay="400">
                <div class="stat-icon-premium">
                    <i class="fa-solid fa-building"></i>
                </div>
                <div class="stat-content-premium">
                    <div class="stat-number-premium" data-count="156">0</div>
                    <div class="stat-label-premium">
                        <?php echo $currentLang === 'ar' ? 'المشاريع المكتملة' : 'Projects This Month'; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</section>

<!-- Initialize AOS (Animate On Scroll) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize AOS if not already done
    if (typeof AOS !== 'undefined') {
        AOS.init({
            duration: 800,
            easing: 'ease-out-cubic',
            once: true,
            offset: 50
        });
    }

    // Animate stats counter
    function animateCounter(element) {
        const target = parseInt(element.getAttribute('data-count'));
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;

        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                element.textContent = target;
                clearInterval(timer);
            } else {
                element.textContent = Math.floor(current);
            }
        }, 16);
    }

    // Observe stats section
    const statsObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const counters = entry.target.querySelectorAll('[data-count]');
                counters.forEach(counter => {
                    if (!counter.classList.contains('counted')) {
                        counter.classList.add('counted');
                        animateCounter(counter);
                    }
                });
                statsObserver.unobserve(entry.target);
            }
        });
    }, { threshold: 0.5 });

    const statsSection = document.querySelector('.stats-premium');
    if (statsSection) {
        statsObserver.observe(statsSection);
    }

    // Service card hover effects
    const serviceCards = document.querySelectorAll('.service-icon-card');
    serviceCards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-12px)';
        });
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });

    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#') {
                e.preventDefault();
                const target = document.querySelector(href);
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            }
        });
    });
});
</script>
