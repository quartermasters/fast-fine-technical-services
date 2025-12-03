<?php
/**
 * Fast and Fine Technical Services FZE - Breadcrumb Component
 *
 * Displays breadcrumb navigation with Schema.org markup
 *
 * @package FastAndFine
 * @version 1.0.0
 */

if(!defined('FAST_FINE_APP')) {
    die('Direct access not permitted');
}

/**
 * Generate and display breadcrumb navigation
 *
 * @param array $items Breadcrumb items [['name' => 'Name', 'url' => 'URL'], ...]
 * @param bool $showHome Whether to show home link
 */
function displayBreadcrumb($items = [], $showHome = true) {
    $currentLang = getCurrentLanguage();

    // Always start with home
    $breadcrumbs = [];

    if ($showHome) {
        $breadcrumbs[] = [
            'name' => __('home'),
            'url' => siteUrl()
        ];
    }

    // Add custom items
    $breadcrumbs = array_merge($breadcrumbs, $items);

    // Don't show breadcrumb if only home
    if (count($breadcrumbs) <= 1) {
        return;
    }

    // Generate Schema.org markup
    $schemaItems = [];
    $position = 1;

    foreach ($breadcrumbs as $item) {
        $schemaItems[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => $item['name'],
            'item' => $item['url']
        ];
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $schemaItems
    ];

    ?>
    <!-- Breadcrumb Navigation -->
    <nav class="breadcrumb" aria-label="<?php _e('breadcrumb'); ?>" role="navigation">
        <div class="container">
            <ol class="breadcrumb-list" itemscope itemtype="https://schema.org/BreadcrumbList">
                <?php foreach ($breadcrumbs as $index => $item): ?>
                    <?php $isLast = ($index === count($breadcrumbs) - 1); ?>
                    <li class="breadcrumb-item <?php echo $isLast ? 'active' : ''; ?>"
                        itemprop="itemListElement"
                        itemscope
                        itemtype="https://schema.org/ListItem">
                        <?php if (!$isLast): ?>
                            <a href="<?php echo escapeHTML($item['url']); ?>"
                               itemprop="item"
                               class="breadcrumb-link">
                                <span itemprop="name"><?php echo escapeHTML($item['name']); ?></span>
                            </a>
                        <?php else: ?>
                            <span itemprop="name" aria-current="page"><?php echo escapeHTML($item['name']); ?></span>
                        <?php endif; ?>
                        <meta itemprop="position" content="<?php echo $index + 1; ?>">
                        <?php if (!$isLast): ?>
                            <span class="breadcrumb-separator" aria-hidden="true">
                                <i class="fa-solid fa-chevron-right"></i>
                            </span>
                        <?php endif; ?>
                    </li>
                <?php endforeach; ?>
            </ol>
        </div>
    </nav>

    <!-- Schema.org JSON-LD (alternative format) -->
    <script type="application/ld+json">
    <?php echo json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE); ?>
    </script>
    <?php
}

/**
 * Get breadcrumb from current URL/section
 *
 * @return array Breadcrumb items
 */
function getBreadcrumbFromUrl() {
    $items = [];
    $section = isset($_GET['section']) ? sanitizeInput($_GET['section']) : '';

    if (!empty($section)) {
        $sectionNames = [
            'services' => __('services'),
            'portfolio' => __('portfolio'),
            'testimonials' => __('testimonials'),
            'about' => __('about'),
            'contact' => __('contact'),
            'booking' => __('booking')
        ];

        if (isset($sectionNames[$section])) {
            $items[] = [
                'name' => $sectionNames[$section],
                'url' => siteUrl('#' . $section)
            ];
        }
    }

    return $items;
}
