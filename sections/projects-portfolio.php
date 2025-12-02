<?php
/**
 * Fast and Fine Technical Services FZE - Projects Portfolio Section
 *
 * Features:
 * - Masonry grid layout with responsive columns
 * - Category filtering (All, Plumbing, Electrical, AC, Cleaning, etc.)
 * - Lightbox image gallery with navigation
 * - Before/After comparison slider
 * - Project detail modal with full info
 * - Load more functionality
 * - Professional project cards with hover effects
 *
 * @package FastAndFine
 * @version 1.0.0
 */

if(!defined('FAST_FINE_APP')) {
    die('Direct access not permitted');
}

// Get all projects from database
$projects = dbSelect("
    SELECT p.id, p.title_en, p.title_ar, p.description_en, p.description_ar,
           p.category, p.location, p.completion_date, p.client_name,
           p.project_duration, p.project_cost, p.main_image, p.gallery_images,
           p.before_image, p.after_image, p.featured, p.created_at,
           s.name_en as service_name_en, s.name_ar as service_name_ar, s.icon_class
    FROM projects p
    LEFT JOIN services s ON p.service_id = s.id
    WHERE p.is_published = 1
    ORDER BY p.featured DESC, p.completion_date DESC
");

// Group by category for filtering
$categories = [];
foreach ($projects as $project) {
    if (!in_array($project['category'], $categories)) {
        $categories[] = $project['category'];
    }
}
?>

<!-- Portfolio Section -->
<section id="portfolio" class="portfolio-section section-padding">
    <div class="container">
        <!-- Section Header -->
        <div class="section-header text-center scroll-reveal">
            <span class="section-label">
                <i class="fa-solid fa-briefcase"></i>
                <?php _e('our_work'); ?>
            </span>
            <h2 class="section-title"><?php _e('our_portfolio'); ?></h2>
            <p class="section-description">
                <?php _e('portfolio_description'); ?>
            </p>
        </div>

        <!-- Portfolio Controls -->
        <div class="portfolio-controls scroll-reveal delay-100">
            <!-- Category Filter -->
            <div class="portfolio-filter">
                <button class="filter-btn active" data-category="all">
                    <i class="fa-solid fa-th-large"></i>
                    <span><?php _e('all_projects'); ?></span>
                </button>
                <?php
                $categoryIcons = [
                    'plumbing' => 'fa-faucet-drip',
                    'electrical' => 'fa-bolt',
                    'ac' => 'fa-snowflake',
                    'cleaning' => 'fa-broom',
                    'carpentry' => 'fa-hammer',
                    'painting' => 'fa-paint-roller',
                    'electromechanical' => 'fa-gears',
                    'ceiling' => 'fa-border-all'
                ];

                foreach ($categories as $category):
                    $icon = $categoryIcons[$category] ?? 'fa-wrench';
                ?>
                    <button class="filter-btn" data-category="<?php echo $category; ?>">
                        <i class="fa-solid <?php echo $icon; ?>"></i>
                        <span><?php _e($category); ?></span>
                    </button>
                <?php endforeach; ?>
            </div>

            <!-- View Toggle -->
            <div class="view-toggle">
                <button class="view-btn active" data-view="grid" aria-label="<?php _e('grid_view'); ?>">
                    <i class="fa-solid fa-th"></i>
                </button>
                <button class="view-btn" data-view="masonry" aria-label="<?php _e('masonry_view'); ?>">
                    <i class="fa-solid fa-border-all"></i>
                </button>
            </div>
        </div>

        <!-- Portfolio Grid (Masonry Layout) -->
        <div class="portfolio-grid masonry-grid" id="portfolioGrid">
            <?php foreach ($projects as $index => $project):
                $title = $currentLang === 'ar' ? $project['title_ar'] : $project['title_en'];
                $description = $currentLang === 'ar' ? $project['description_ar'] : $project['description_en'];
                $serviceName = $currentLang === 'ar' ? $project['service_name_ar'] : $project['service_name_en'];
                $galleryImages = !empty($project['gallery_images']) ? json_decode($project['gallery_images'], true) : [];
                $hasBeforeAfter = !empty($project['before_image']) && !empty($project['after_image']);
                $delay = ($index % 3) * 100 + 100;
            ?>
                <div class="portfolio-item scroll-reveal delay-<?php echo $delay; ?>"
                     data-category="<?php echo escapeHTML($project['category']); ?>"
                     data-project-id="<?php echo $project['id']; ?>">

                    <div class="portfolio-card">
                        <!-- Project Image -->
                        <div class="portfolio-image">
                            <img src="<?php echo escapeHTML($project['main_image']); ?>"
                                 alt="<?php echo escapeHTML($title); ?>"
                                 loading="lazy"
                                 class="project-img">

                            <!-- Overlay -->
                            <div class="portfolio-overlay">
                                <div class="overlay-content">
                                    <h3 class="project-title"><?php echo escapeHTML($title); ?></h3>
                                    <p class="project-category">
                                        <i class="<?php echo escapeHTML($project['icon_class']); ?>"></i>
                                        <?php echo escapeHTML($serviceName); ?>
                                    </p>

                                    <div class="overlay-actions">
                                        <button class="btn-icon view-gallery-btn"
                                                data-project-id="<?php echo $project['id']; ?>"
                                                title="<?php _e('view_gallery'); ?>">
                                            <i class="fa-solid fa-images"></i>
                                        </button>

                                        <?php if ($hasBeforeAfter): ?>
                                            <button class="btn-icon view-before-after-btn"
                                                    data-project-id="<?php echo $project['id']; ?>"
                                                    title="<?php _e('before_after'); ?>">
                                                <i class="fa-solid fa-arrows-left-right"></i>
                                            </button>
                                        <?php endif; ?>

                                        <button class="btn-icon view-details-btn"
                                                data-project-id="<?php echo $project['id']; ?>"
                                                title="<?php _e('view_details'); ?>">
                                            <i class="fa-solid fa-info-circle"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Featured Badge -->
                            <?php if ($project['featured']): ?>
                                <div class="featured-badge">
                                    <i class="fa-solid fa-star"></i>
                                    <span><?php _e('featured'); ?></span>
                                </div>
                            <?php endif; ?>

                            <!-- Category Badge -->
                            <div class="category-badge badge-<?php echo escapeHTML($project['category']); ?>">
                                <?php _e($project['category']); ?>
                            </div>
                        </div>

                        <!-- Project Info -->
                        <div class="portfolio-info">
                            <h3 class="project-title"><?php echo escapeHTML($title); ?></h3>
                            <p class="project-meta">
                                <i class="fa-solid fa-map-marker-alt"></i>
                                <span><?php echo escapeHTML($project['location']); ?></span>
                                <span class="separator">•</span>
                                <i class="fa-regular fa-calendar"></i>
                                <span><?php echo date('M Y', strtotime($project['completion_date'])); ?></span>
                            </p>
                            <p class="project-excerpt"><?php echo escapeHTML(substr($description, 0, 100)) . '...'; ?></p>

                            <div class="project-actions">
                                <button class="btn btn-sm btn-outline view-details-btn"
                                        data-project-id="<?php echo $project['id']; ?>">
                                    <i class="fa-solid fa-arrow-right"></i>
                                    <span><?php _e('view_project'); ?></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- No Results Message -->
        <div class="no-results" id="noPortfolioResults" style="display: none;">
            <i class="fa-solid fa-folder-open"></i>
            <h3><?php _e('no_projects_found'); ?></h3>
            <p><?php _e('try_different_filter'); ?></p>
            <button class="btn btn-outline" id="resetPortfolioBtn">
                <i class="fa-solid fa-redo"></i>
                <span><?php _e('reset_filters'); ?></span>
            </button>
        </div>

        <!-- Load More Button -->
        <div class="load-more-container scroll-reveal" id="loadMoreContainer">
            <button class="btn btn-outline btn-lg" id="loadMoreBtn">
                <i class="fa-solid fa-plus-circle"></i>
                <span><?php _e('load_more_projects'); ?></span>
            </button>
        </div>

        <!-- CTA Section -->
        <div class="portfolio-cta scroll-reveal">
            <div class="cta-content">
                <h3><?php _e('impressed_by_work'); ?></h3>
                <p><?php _e('impressed_by_work_desc'); ?></p>
                <div class="cta-buttons">
                    <a href="#booking" class="btn btn-primary btn-lg">
                        <i class="fa-solid fa-calendar-check"></i>
                        <span><?php _e('start_your_project'); ?></span>
                    </a>
                    <a href="#contact" class="btn btn-outline btn-lg">
                        <i class="fa-solid fa-headset"></i>
                        <span><?php _e('discuss_project'); ?></span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Project Detail Modal -->
<div class="modal" id="projectModal" role="dialog" aria-labelledby="projectModalTitle" aria-hidden="true">
    <div class="modal-overlay" id="projectModalOverlay"></div>
    <div class="modal-container modal-lg">
        <div class="modal-header">
            <h3 class="modal-title" id="projectModalTitle"><?php _e('project_details'); ?></h3>
            <button class="modal-close" id="projectModalClose" aria-label="<?php _e('close'); ?>">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>

        <div class="modal-body" id="projectModalBody">
            <!-- Content loaded dynamically -->
            <div class="modal-loading">
                <div class="spinner"></div>
                <p><?php _e('loading'); ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Gallery Lightbox Modal -->
<div class="lightbox" id="galleryLightbox">
    <div class="lightbox-overlay" id="lightboxOverlay"></div>
    <div class="lightbox-container">
        <button class="lightbox-close" id="lightboxClose" aria-label="<?php _e('close'); ?>">
            <i class="fa-solid fa-times"></i>
        </button>

        <button class="lightbox-nav lightbox-prev" id="lightboxPrev" aria-label="<?php _e('previous'); ?>">
            <i class="fa-solid fa-chevron-left"></i>
        </button>

        <div class="lightbox-content" id="lightboxContent">
            <img src="" alt="" id="lightboxImage">
        </div>

        <button class="lightbox-nav lightbox-next" id="lightboxNext" aria-label="<?php _e('next'); ?>">
            <i class="fa-solid fa-chevron-right"></i>
        </button>

        <div class="lightbox-caption" id="lightboxCaption"></div>

        <div class="lightbox-counter" id="lightboxCounter">
            <span id="lightboxCurrentIndex">1</span> / <span id="lightboxTotalCount">1</span>
        </div>
    </div>
</div>

<!-- Before/After Comparison Modal -->
<div class="modal" id="beforeAfterModal" role="dialog" aria-hidden="true">
    <div class="modal-overlay" id="beforeAfterOverlay"></div>
    <div class="modal-container modal-lg">
        <div class="modal-header">
            <h3 class="modal-title"><?php _e('before_after_comparison'); ?></h3>
            <button class="modal-close" id="beforeAfterClose" aria-label="<?php _e('close'); ?>">
                <i class="fa-solid fa-times"></i>
            </button>
        </div>

        <div class="modal-body">
            <div class="before-after-slider" id="beforeAfterSlider">
                <div class="before-image-container">
                    <img src="" alt="<?php _e('before'); ?>" id="beforeImage" class="comparison-image">
                    <div class="image-label label-before"><?php _e('before'); ?></div>
                </div>
                <div class="after-image-container">
                    <img src="" alt="<?php _e('after'); ?>" id="afterImage" class="comparison-image">
                    <div class="image-label label-after"><?php _e('after'); ?></div>
                </div>
                <div class="slider-handle" id="sliderHandle">
                    <i class="fa-solid fa-arrows-left-right"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Hidden project data for JavaScript (JSON) -->
<script type="application/json" id="portfolioData">
<?php
$portfolioData = [];
foreach ($projects as $project) {
    $portfolioData[] = [
        'id' => $project['id'],
        'title_en' => $project['title_en'],
        'title_ar' => $project['title_ar'],
        'description_en' => $project['description_en'],
        'description_ar' => $project['description_ar'],
        'category' => $project['category'],
        'location' => $project['location'],
        'completion_date' => $project['completion_date'],
        'client_name' => $project['client_name'],
        'project_duration' => $project['project_duration'],
        'project_cost' => $project['project_cost'],
        'main_image' => $project['main_image'],
        'gallery_images' => !empty($project['gallery_images']) ? json_decode($project['gallery_images'], true) : [],
        'before_image' => $project['before_image'],
        'after_image' => $project['after_image'],
        'service_name_en' => $project['service_name_en'],
        'service_name_ar' => $project['service_name_ar'],
        'icon_class' => $project['icon_class'],
        'featured' => (bool)$project['featured']
    ];
}
echo json_encode($portfolioData, JSON_UNESCAPED_UNICODE);
?>
</script>
