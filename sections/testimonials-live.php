<?php
/**
 * Fast and Fine Technical Services FZE - Live Testimonials Section
 *
 * Features:
 * - Carousel/slider with autoplay
 * - Google Reviews integration
 * - Star ratings display
 * - Client photos
 * - Verified badge
 * - Responsive design
 * - Touch/swipe support
 *
 * @package FastAndFine
 * @version 1.0.0
 */

if(!defined('FAST_FINE_APP')) {
    die('Direct access not permitted');
}

// Get testimonials from database
$testimonials = dbSelect("
    SELECT id, client_name, client_position, client_company, client_photo,
           rating, review_text, review_source, verified, created_at,
           featured
    FROM testimonials
    WHERE is_published = 1
    ORDER BY featured DESC, created_at DESC
    LIMIT 20
");

// Calculate average rating
$avgRating = 0;
$totalReviews = count($testimonials);
if ($totalReviews > 0) {
    $sumRatings = array_sum(array_column($testimonials, 'rating'));
    $avgRating = round($sumRatings / $totalReviews, 1);
}
?>

<!-- Testimonials Section -->
<section id="testimonials" class="testimonials-section section-padding">
    <div class="container">
        <!-- Section Header -->
        <div class="section-header text-center scroll-reveal">
            <span class="section-label">
                <i class="fa-solid fa-star"></i>
                <?php _e('client_feedback'); ?>
            </span>
            <h2 class="section-title"><?php _e('what_clients_say'); ?></h2>
            <p class="section-description">
                <?php _e('testimonials_description'); ?>
            </p>
        </div>

        <!-- Rating Summary -->
        <div class="rating-summary scroll-reveal delay-100">
            <div class="rating-overview">
                <div class="rating-number"><?php echo number_format($avgRating, 1); ?></div>
                <div class="rating-stars">
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                        <?php if ($i <= floor($avgRating)): ?>
                            <i class="fa-solid fa-star"></i>
                        <?php elseif ($i - 0.5 <= $avgRating): ?>
                            <i class="fa-solid fa-star-half-stroke"></i>
                        <?php else: ?>
                            <i class="fa-regular fa-star"></i>
                        <?php endif; ?>
                    <?php endfor; ?>
                </div>
                <div class="rating-text">
                    <?php echo translate('based_on_reviews', ['count' => $totalReviews]); ?>
                </div>
            </div>

            <div class="rating-sources">
                <a href="<?php echo GOOGLE_BUSINESS_URL; ?>" target="_blank" rel="noopener noreferrer" class="source-badge google">
                    <i class="fa-brands fa-google"></i>
                    <span><?php _e('google_reviews'); ?></span>
                </a>
                <a href="<?php echo FACEBOOK_URL; ?>" target="_blank" rel="noopener noreferrer" class="source-badge facebook">
                    <i class="fa-brands fa-facebook"></i>
                    <span><?php _e('facebook_reviews'); ?></span>
                </a>
            </div>
        </div>

        <!-- Testimonials Carousel -->
        <div class="testimonials-carousel-wrapper scroll-reveal delay-200">
            <!-- Navigation Arrows -->
            <button class="carousel-nav carousel-prev" id="testimonialsPrev" aria-label="<?php _e('previous'); ?>">
                <i class="fa-solid fa-chevron-left"></i>
            </button>

            <button class="carousel-nav carousel-next" id="testimonialsNext" aria-label="<?php _e('next'); ?>">
                <i class="fa-solid fa-chevron-right"></i>
            </button>

            <!-- Carousel Container -->
            <div class="testimonials-carousel" id="testimonialsCarousel">
                <div class="carousel-track" id="carouselTrack">
                    <?php foreach ($testimonials as $testimonial): ?>
                        <div class="testimonial-card">
                            <!-- Card Header -->
                            <div class="testimonial-header">
                                <div class="client-avatar">
                                    <?php if (!empty($testimonial['client_photo'])): ?>
                                        <img src="<?php echo escapeHTML($testimonial['client_photo']); ?>"
                                             alt="<?php echo escapeHTML($testimonial['client_name']); ?> - Verified Customer"
                                             loading="lazy"
                                             width="80"
                                             height="80">
                                    <?php else: ?>
                                        <div class="avatar-placeholder">
                                            <?php echo strtoupper(substr($testimonial['client_name'], 0, 1)); ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($testimonial['verified']): ?>
                                        <div class="verified-badge" title="<?php _e('verified_customer'); ?>">
                                            <i class="fa-solid fa-circle-check"></i>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="client-info">
                                    <h4 class="client-name"><?php echo escapeHTML($testimonial['client_name']); ?></h4>
                                    <?php if (!empty($testimonial['client_position']) && !empty($testimonial['client_company'])): ?>
                                        <p class="client-position">
                                            <?php echo escapeHTML($testimonial['client_position']); ?> at
                                            <?php echo escapeHTML($testimonial['client_company']); ?>
                                        </p>
                                    <?php elseif (!empty($testimonial['client_company'])): ?>
                                        <p class="client-position"><?php echo escapeHTML($testimonial['client_company']); ?></p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Rating Stars -->
                            <div class="testimonial-rating">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $testimonial['rating']): ?>
                                        <i class="fa-solid fa-star"></i>
                                    <?php else: ?>
                                        <i class="fa-regular fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                            </div>

                            <!-- Review Text -->
                            <div class="testimonial-text">
                                <i class="fa-solid fa-quote-left quote-icon"></i>
                                <p><?php echo nl2br(escapeHTML($testimonial['review_text'])); ?></p>
                            </div>

                            <!-- Card Footer -->
                            <div class="testimonial-footer">
                                <div class="review-source">
                                    <?php
                                    $sourceIcon = '';
                                    $sourceClass = '';
                                    switch ($testimonial['review_source']) {
                                        case 'google':
                                            $sourceIcon = 'fa-brands fa-google';
                                            $sourceClass = 'source-google';
                                            break;
                                        case 'facebook':
                                            $sourceIcon = 'fa-brands fa-facebook';
                                            $sourceClass = 'source-facebook';
                                            break;
                                        case 'website':
                                            $sourceIcon = 'fa-solid fa-globe';
                                            $sourceClass = 'source-website';
                                            break;
                                        default:
                                            $sourceIcon = 'fa-solid fa-star';
                                            $sourceClass = 'source-other';
                                    }
                                    ?>
                                    <i class="<?php echo $sourceIcon; ?> <?php echo $sourceClass; ?>"></i>
                                    <span><?php _e($testimonial['review_source']); ?></span>
                                </div>

                                <div class="review-date">
                                    <?php echo timeAgo($testimonial['created_at']); ?>
                                </div>
                            </div>

                            <?php if ($testimonial['featured']): ?>
                                <div class="featured-star">
                                    <i class="fa-solid fa-star"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Carousel Indicators -->
            <div class="carousel-indicators" id="carouselIndicators">
                <!-- Generated by JavaScript -->
            </div>
        </div>

        <!-- Carousel Controls -->
        <div class="carousel-controls scroll-reveal">
            <button class="control-btn" id="autoplayToggle" aria-label="<?php _e('toggle_autoplay'); ?>">
                <i class="fa-solid fa-pause"></i>
            </button>
        </div>

        <!-- Write Review CTA -->
        <div class="testimonial-cta scroll-reveal">
            <div class="cta-content">
                <h3><?php _e('share_experience'); ?></h3>
                <p><?php _e('share_experience_desc'); ?></p>
                <div class="cta-buttons">
                    <a href="<?php echo GOOGLE_BUSINESS_URL; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary btn-lg">
                        <i class="fa-brands fa-google"></i>
                        <span><?php _e('review_on_google'); ?></span>
                    </a>
                    <a href="#contact" class="btn btn-outline btn-lg">
                        <i class="fa-solid fa-comment"></i>
                        <span><?php _e('send_feedback'); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Hidden testimonials data for JavaScript (JSON) -->
<script type="application/json" id="testimonialsData">
<?php
$testimonialsData = [];
foreach ($testimonials as $testimonial) {
    $testimonialsData[] = [
        'id' => $testimonial['id'],
        'client_name' => $testimonial['client_name'],
        'client_position' => $testimonial['client_position'],
        'client_company' => $testimonial['client_company'],
        'client_photo' => $testimonial['client_photo'],
        'rating' => $testimonial['rating'],
        'review_text' => $testimonial['review_text'],
        'review_source' => $testimonial['review_source'],
        'verified' => (bool)$testimonial['verified'],
        'featured' => (bool)$testimonial['featured'],
        'created_at' => $testimonial['created_at']
    ];
}
echo json_encode($testimonialsData, JSON_UNESCAPED_UNICODE);
?>
</script>
