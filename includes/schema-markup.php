<?php
/**
 * Fast and Fine Technical Services FZE - Schema.org Structured Data
 *
 * Implements JSON-LD structured data for SEO enhancement
 * Includes: Organization, LocalBusiness, Service, AggregateRating schemas
 *
 * @package FastAndFine
 * @version 1.0.0
 */

if(!defined('FAST_FINE_APP')) {
    die('Direct access not permitted');
}

/**
 * Generate Organization Schema
 *
 * @return string JSON-LD markup
 */
function getOrganizationSchema() {
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'name' => SITE_NAME,
        'url' => SITE_URL,
        'logo' => SITE_URL . '/assets/images/logo.png',
        'description' => SEO_DESCRIPTION,
        'telephone' => WHATSAPP_NUMBER,
        'email' => ADMIN_EMAIL,
        'address' => [
            '@type' => 'PostalAddress',
            'addressLocality' => 'Dubai',
            'addressRegion' => 'Dubai',
            'addressCountry' => 'AE',
            'streetAddress' => BUSINESS_ADDRESS
        ],
        'geo' => [
            '@type' => 'GeoCoordinates',
            'latitude' => '25.2048',
            'longitude' => '55.2708'
        ],
        'areaServed' => [
            '@type' => 'City',
            'name' => 'Dubai'
        ],
        'sameAs' => array_filter([
            FACEBOOK_URL,
            INSTAGRAM_URL,
            TWITTER_URL,
            LINKEDIN_URL,
            GOOGLE_BUSINESS_URL
        ])
    ];

    return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

/**
 * Generate LocalBusiness Schema
 *
 * @return string JSON-LD markup
 */
function getLocalBusinessSchema() {
    global $db;

    // Get testimonials for aggregate rating
    $testimonials = dbSelect("
        SELECT AVG(rating) as avg_rating, COUNT(*) as review_count
        FROM testimonials
        WHERE is_published = 1
    ");

    $avgRating = isset($testimonials[0]['avg_rating']) ? round($testimonials[0]['avg_rating'], 1) : 5.0;
    $reviewCount = isset($testimonials[0]['review_count']) ? $testimonials[0]['review_count'] : 0;

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'LocalBusiness',
        'name' => SITE_NAME,
        'image' => SITE_URL . '/assets/images/logo.png',
        'url' => SITE_URL,
        'telephone' => WHATSAPP_NUMBER,
        'email' => ADMIN_EMAIL,
        'priceRange' => 'AED 100 - AED 5000',
        'address' => [
            '@type' => 'PostalAddress',
            'streetAddress' => BUSINESS_ADDRESS,
            'addressLocality' => 'Dubai',
            'addressRegion' => 'Dubai',
            'postalCode' => '',
            'addressCountry' => 'AE'
        ],
        'geo' => [
            '@type' => 'GeoCoordinates',
            'latitude' => 25.2048,
            'longitude' => 55.2708
        ],
        'openingHoursSpecification' => [
            '@type' => 'OpeningHoursSpecification',
            'dayOfWeek' => [
                'Monday',
                'Tuesday',
                'Wednesday',
                'Thursday',
                'Friday',
                'Saturday',
                'Sunday'
            ],
            'opens' => '00:00',
            'closes' => '23:59'
        ],
        'sameAs' => array_filter([
            FACEBOOK_URL,
            INSTAGRAM_URL,
            TWITTER_URL,
            LINKEDIN_URL,
            GOOGLE_BUSINESS_URL
        ])
    ];

    // Add aggregate rating if reviews exist
    if ($reviewCount > 0) {
        $schema['aggregateRating'] = [
            '@type' => 'AggregateRating',
            'ratingValue' => $avgRating,
            'reviewCount' => $reviewCount,
            'bestRating' => '5',
            'worstRating' => '1'
        ];
    }

    return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

/**
 * Generate Service Schema for all services
 *
 * @return string JSON-LD markup
 */
function getServicesSchema() {
    $currentLang = getCurrentLanguage();

    $services = dbSelect("
        SELECT id, name_en, name_ar, slug, short_desc_en, short_desc_ar,
               long_desc_en, long_desc_ar, icon_class, starting_price, category
        FROM services
        WHERE is_active = 1
        ORDER BY display_order ASC
    ");

    $serviceSchemas = [];

    foreach ($services as $service) {
        $name = $currentLang === 'ar' ? $service['name_ar'] : $service['name_en'];
        $description = $currentLang === 'ar' ? $service['short_desc_ar'] : $service['short_desc_en'];
        $longDesc = $currentLang === 'ar' ? $service['long_desc_ar'] : $service['long_desc_en'];

        $serviceSchemas[] = [
            '@context' => 'https://schema.org',
            '@type' => 'Service',
            'serviceType' => $name,
            'name' => $name,
            'description' => $description,
            'provider' => [
                '@type' => 'LocalBusiness',
                'name' => SITE_NAME,
                'telephone' => WHATSAPP_NUMBER,
                'url' => SITE_URL
            ],
            'areaServed' => [
                '@type' => 'City',
                'name' => 'Dubai'
            ],
            'offers' => [
                '@type' => 'Offer',
                'price' => $service['starting_price'],
                'priceCurrency' => 'AED',
                'availability' => 'https://schema.org/InStock',
                'url' => SITE_URL . '/#services'
            ],
            'category' => ucfirst($service['category'])
        ];
    }

    return json_encode($serviceSchemas, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

/**
 * Generate Review Schema for testimonials
 *
 * @param int $limit Number of reviews to include
 * @return string JSON-LD markup
 */
function getReviewsSchema($limit = 10) {
    $testimonials = dbSelect("
        SELECT client_name, rating, review_text, created_at
        FROM testimonials
        WHERE is_published = 1 AND verified = 1
        ORDER BY created_at DESC
        LIMIT " . (int)$limit
    );

    if (empty($testimonials)) {
        return '';
    }

    $reviews = [];

    foreach ($testimonials as $testimonial) {
        $reviews[] = [
            '@type' => 'Review',
            'author' => [
                '@type' => 'Person',
                'name' => $testimonial['client_name']
            ],
            'reviewRating' => [
                '@type' => 'Rating',
                'ratingValue' => $testimonial['rating'],
                'bestRating' => '5',
                'worstRating' => '1'
            ],
            'reviewBody' => $testimonial['review_text'],
            'datePublished' => date('Y-m-d', $testimonial['created_at'])
        ];
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Product',
        'name' => SITE_NAME . ' Services',
        'description' => SEO_DESCRIPTION,
        'brand' => [
            '@type' => 'Brand',
            'name' => SITE_NAME
        ],
        'review' => $reviews
    ];

    return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

/**
 * Generate BreadcrumbList Schema
 *
 * @param array $breadcrumbs Array of breadcrumb items ['name' => 'Name', 'url' => 'URL']
 * @return string JSON-LD markup
 */
function getBreadcrumbSchema($breadcrumbs = []) {
    if (empty($breadcrumbs)) {
        // Default homepage breadcrumb
        $breadcrumbs = [
            ['name' => 'Home', 'url' => SITE_URL]
        ];
    }

    $itemListElement = [];
    $position = 1;

    foreach ($breadcrumbs as $breadcrumb) {
        $itemListElement[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => $breadcrumb['name'],
            'item' => $breadcrumb['url']
        ];
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => $itemListElement
    ];

    return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

/**
 * Generate WebSite Schema with SearchAction
 *
 * @return string JSON-LD markup
 */
function getWebSiteSchema() {
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'WebSite',
        'name' => SITE_NAME,
        'url' => SITE_URL,
        'description' => SEO_DESCRIPTION,
        'potentialAction' => [
            '@type' => 'SearchAction',
            'target' => [
                '@type' => 'EntryPoint',
                'urlTemplate' => SITE_URL . '/search?q={search_term_string}'
            ],
            'query-input' => 'required name=search_term_string'
        ],
        'inLanguage' => ['en-US', 'ar-AE']
    ];

    return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

/**
 * Output all Schema.org markup
 *
 * @param bool $includeServices Include service schemas
 * @param bool $includeReviews Include review schemas
 * @return void
 */
function outputSchemaMarkup($includeServices = true, $includeReviews = true) {
    echo '<!-- Schema.org Structured Data (JSON-LD) -->' . "\n";

    // Organization Schema
    echo '<script type="application/ld+json">' . "\n";
    echo getOrganizationSchema();
    echo "\n</script>\n\n";

    // LocalBusiness Schema
    echo '<script type="application/ld+json">' . "\n";
    echo getLocalBusinessSchema();
    echo "\n</script>\n\n";

    // WebSite Schema
    echo '<script type="application/ld+json">' . "\n";
    echo getWebSiteSchema();
    echo "\n</script>\n\n";

    // Breadcrumb Schema
    echo '<script type="application/ld+json">' . "\n";
    echo getBreadcrumbSchema();
    echo "\n</script>\n\n";

    // Services Schema
    if ($includeServices) {
        echo '<script type="application/ld+json">' . "\n";
        echo getServicesSchema();
        echo "\n</script>\n\n";
    }

    // Reviews Schema
    if ($includeReviews) {
        $reviewsSchema = getReviewsSchema(10);
        if (!empty($reviewsSchema)) {
            echo '<script type="application/ld+json">' . "\n";
            echo $reviewsSchema;
            echo "\n</script>\n\n";
        }
    }
}
