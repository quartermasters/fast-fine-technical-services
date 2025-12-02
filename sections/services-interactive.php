<?php
/**
 * Fast and Fine Technical Services FZE - Interactive Services Section
 *
 * Features:
 * - 3D flip cards with front/back design
 * - Category filtering (All, Residential, Commercial, Emergency)
 * - Search functionality
 * - Service detail modal with gallery
 * - Pricing packages display
 * - Book service CTA
 *
 * @package FastAndFine
 * @version 1.0.0
 */

if(!defined('FAST_FINE_APP')) {
    die('Direct access not permitted');
}

// Get all services from database
$services = dbSelect("
    SELECT id, name_en, name_ar, slug, short_desc_en, short_desc_ar,
           long_desc_en, long_desc_ar, icon_class, starting_price,
           category, features_en, features_ar, image_url
    FROM services
    WHERE is_active = 1
    ORDER BY display_order ASC
");
?>

<!-- Enhanced Services Section -->
<section id="services" class="services-section section-padding">
    <div class="container">
        <!-- Section Header -->
        <div class="section-header text-center scroll-reveal">
            <span class="section-label">
                <i class="fa-solid fa-tools"></i>
                <?php _e('what_we_offer'); ?>
            </span>
            <h2 class="section-title"><?php _e('our_services'); ?></h2>
            <p class="section-description">
                <?php _e('services_description'); ?>
            </p>
        </div>

        <!-- Services Controls -->
        <div class="services-controls scroll-reveal delay-100">
            <!-- Category Filter -->
            <div class="category-filter">
                <button class="filter-btn active" data-category="all">
                    <i class="fa-solid fa-th"></i>
                    <span><?php _e('all_services'); ?></span>
                </button>
                <button class="filter-btn" data-category="residential">
                    <i class="fa-solid fa-home"></i>
                    <span><?php _e('residential'); ?></span>
                </button>
                <button class="filter-btn" data-category="commercial">
                    <i class="fa-solid fa-building"></i>
                    <span><?php _e('commercial'); ?></span>
                </button>
                <button class="filter-btn" data-category="emergency">
                    <i class="fa-solid fa-exclamation-triangle"></i>
                    <span><?php _e('emergency'); ?></span>
                </button>
            </div>

            <!-- Search Bar -->
            <div class="service-search">
                <div class="search-wrapper">
                    <i class="fa-solid fa-search"></i>
                    <input
                        type="text"
                        id="serviceSearchInput"
                        class="search-input"
                        placeholder="<?php _e('search_services'); ?>"
                        autocomplete="off"
                    >
                    <button class="search-clear" id="searchClearBtn" style="display: none;">
                        <i class="fa-solid fa-times"></i>
                    </button>
                </div>
            </div>
        </div>

        <!-- Services Grid with 3D Flip Cards -->
        <div class="services-flip-grid" id="servicesGrid">
            <?php foreach ($services as $index => $service):
                $name = $currentLang === 'ar' ? $service['name_ar'] : $service['name_en'];
                $shortDesc = $currentLang === 'ar' ? $service['short_desc_ar'] : $service['short_desc_en'];
                $longDesc = $currentLang === 'ar' ? $service['long_desc_ar'] : $service['long_desc_en'];
                $features = $currentLang === 'ar' ? $service['features_ar'] : $service['features_en'];
                $featuresArray = !empty($features) ? json_decode($features, true) : [];
                $delay = ($index % 3) * 100 + 100;
            ?>
                <div class="service-flip-card scroll-reveal delay-<?php echo $delay; ?>"
                     data-category="<?php echo escapeHTML($service['category']); ?>"
                     data-service-id="<?php echo $service['id']; ?>"
                     data-service-name="<?php echo escapeHTML($name); ?>">

                    <div class="flip-card-inner">
                        <!-- Front Side -->
                        <div class="flip-card-front">
                            <div class="service-icon-large">
                                <i class="<?php echo escapeHTML($service['icon_class']); ?>"></i>
                            </div>
                            <h3 class="service-card-title"><?php echo escapeHTML($name); ?></h3>
                            <p class="service-card-desc"><?php echo escapeHTML($shortDesc); ?></p>
                            <div class="service-price-tag">
                                <span class="price-label"><?php _e('starting_from'); ?></span>
                                <span class="price-amount"><?php echo formatPrice($service['starting_price']); ?></span>
                            </div>
                            <div class="flip-hint">
                                <i class="fa-solid fa-sync-alt"></i>
                                <span><?php _e('flip_to_learn_more'); ?></span>
                            </div>
                        </div>

                        <!-- Back Side -->
                        <div class="flip-card-back">
                            <div class="back-content">
                                <h4><?php _e('key_features'); ?></h4>
                                <?php if (!empty($featuresArray)): ?>
                                    <ul class="features-list">
                                        <?php foreach (array_slice($featuresArray, 0, 4) as $feature): ?>
                                            <li>
                                                <i class="fa-solid fa-check-circle"></i>
                                                <span><?php echo escapeHTML($feature); ?></span>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                <?php else: ?>
                                    <p class="no-features"><?php _e('features_coming_soon'); ?></p>
                                <?php endif; ?>

                                <div class="back-actions">
                                    <button class="btn btn-primary btn-sm view-details-btn"
                                            data-service-id="<?php echo $service['id']; ?>">
                                        <i class="fa-solid fa-info-circle"></i>
                                        <span><?php _e('view_details'); ?></span>
                                    </button>
                                    <a href="#booking?service=<?php echo $service['id']; ?>"
                                       class="btn btn-outline btn-sm">
                                        <i class="fa-solid fa-calendar-check"></i>
                                        <span><?php _e('book_now'); ?></span>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Category Badge -->
                    <span class="category-badge badge-<?php echo escapeHTML($service['category']); ?>">
                        <?php _e($service['category']); ?>
                    </span>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- No Results Message -->
        <div class="no-results" id="noResults" style="display: none;">
            <i class="fa-solid fa-search"></i>
            <h3><?php _e('no_services_found'); ?></h3>
            <p><?php _e('try_different_search'); ?></p>
            <button class="btn btn-outline" id="resetSearchBtn">
                <i class="fa-solid fa-redo"></i>
                <span><?php _e('reset_filters'); ?></span>
            </button>
        </div>

        <!-- View All CTA -->
        <div class="section-cta scroll-reveal">
            <p class="cta-text"><?php _e('need_custom_solution'); ?></p>
            <div class="cta-buttons">
                <a href="#contact" class="btn btn-primary btn-lg">
                    <i class="fa-solid fa-headset"></i>
                    <span><?php _e('contact_expert'); ?></span>
                </a>
                <a href="#quote" class="btn btn-outline btn-lg">
                    <i class="fa-solid fa-calculator"></i>
                    <span><?php _e('get_free_quote'); ?></span>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Service Detail Modal -->
<div class="modal" id="serviceModal" role="dialog" aria-labelledby="serviceModalTitle" aria-hidden="true">
    <div class="modal-overlay" id="serviceModalOverlay"></div>
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title" id="serviceModalTitle"><?php _e('service_details'); ?></h3>
            <button class="modal-close" id="serviceModalClose" aria-label="<?php _e('close'); ?>">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>

        <div class="modal-body" id="serviceModalBody">
            <!-- Content loaded dynamically -->
            <div class="modal-loading">
                <div class="spinner"></div>
                <p><?php _e('loading'); ?></p>
            </div>
        </div>

        <div class="modal-footer">
            <a href="#quote" class="btn btn-outline" id="modalGetQuoteBtn">
                <i class="fa-solid fa-calculator"></i>
                <span><?php _e('get_quote'); ?></span>
            </a>
            <a href="#booking" class="btn btn-primary" id="modalBookNowBtn">
                <i class="fa-solid fa-calendar-check"></i>
                <span><?php _e('book_now'); ?></span>
            </a>
        </div>
    </div>
</div>

<!-- Hidden service data for modal (JSON) -->
<script type="application/json" id="servicesData">
<?php
$servicesData = [];
foreach ($services as $service) {
    $servicesData[] = [
        'id' => $service['id'],
        'name_en' => $service['name_en'],
        'name_ar' => $service['name_ar'],
        'short_desc_en' => $service['short_desc_en'],
        'short_desc_ar' => $service['short_desc_ar'],
        'long_desc_en' => $service['long_desc_en'],
        'long_desc_ar' => $service['long_desc_ar'],
        'icon_class' => $service['icon_class'],
        'starting_price' => $service['starting_price'],
        'category' => $service['category'],
        'features_en' => !empty($service['features_en']) ? json_decode($service['features_en'], true) : [],
        'features_ar' => !empty($service['features_ar']) ? json_decode($service['features_ar'], true) : [],
        'image_url' => $service['image_url']
    ];
}
echo json_encode($servicesData, JSON_UNESCAPED_UNICODE);
?>
</script>
