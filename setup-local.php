<?php
/**
 * Fast and Fine Technical Services FZE - Local Development Setup
 *
 * This script initializes the local SQLite database and creates test data
 *
 * Usage: php setup-local.php
 *
 * @package FastAndFine
 * @version 1.0.0
 */

// Define application constant
define('FAST_FINE_APP', true);

// Load configuration
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/includes/db-connect.php';

echo "\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "  Fast & Fine - Local Development Setup\n";
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
echo "\n";

try {
    $db = Database::getInstance()->getConnection();

    // Check if database already exists and has tables
    $tables = $db->query("SELECT name FROM sqlite_master WHERE type='table'")->fetchAll(PDO::FETCH_COLUMN);

    if (!empty($tables)) {
        echo "⚠️  Database already exists with " . count($tables) . " tables.\n";
        echo "\n";
        echo "Do you want to:\n";
        echo "  [1] Keep existing database and add test data only\n";
        echo "  [2] Drop all tables and recreate (WARNING: All data will be lost)\n";
        echo "  [3] Cancel setup\n";
        echo "\n";
        echo "Enter choice (1-3): ";

        $handle = fopen("php://stdin", "r");
        $choice = trim(fgets($handle));
        fclose($handle);

        if ($choice === '2') {
            echo "\n🗑️  Dropping existing tables...\n";
            foreach ($tables as $table) {
                $db->exec("DROP TABLE IF EXISTS {$table}");
                echo "  ✓ Dropped table: {$table}\n";
            }
            echo "\n";
        } elseif ($choice === '3') {
            echo "\n❌ Setup cancelled.\n\n";
            exit(0);
        } elseif ($choice !== '1') {
            echo "\n❌ Invalid choice. Setup cancelled.\n\n";
            exit(1);
        }
    }

    // Create tables from schema
    if (empty($tables) || $choice === '2') {
        echo "📦 Creating database schema...\n";

        $schemaFile = __DIR__ . '/database/schema-sqlite.sql';

        if (!file_exists($schemaFile)) {
            throw new Exception("Schema file not found: {$schemaFile}");
        }

        $schema = file_get_contents($schemaFile);

        // Remove comments
        $schema = preg_replace('/--[^\n]*\n/', '', $schema);

        // Split by semicolon and execute each statement
        $statements = array_filter(
            array_map('trim', explode(';', $schema)),
            function($stmt) {
                return !empty($stmt);
            }
        );

        foreach ($statements as $statement) {
            try {
                if (stripos($statement, 'CREATE TABLE') !== false) {
                    preg_match('/CREATE TABLE.*?(\w+)\s*\(/i', $statement, $matches);
                    $tableName = $matches[1] ?? 'unknown';
                    echo "  ✓ Creating table: {$tableName}\n";
                } elseif (stripos($statement, 'CREATE INDEX') !== false) {
                    preg_match('/CREATE INDEX.*?idx_(\w+)/i', $statement, $matches);
                    $indexName = $matches[1] ?? 'unknown';
                    echo "  ✓ Creating index: idx_{$indexName}\n";
                } elseif (stripos($statement, 'PRAGMA') !== false) {
                    echo "  ✓ Setting pragma\n";
                }
                $db->exec($statement);
            } catch (PDOException $e) {
                echo "  ⚠️  Warning: " . $e->getMessage() . "\n";
                // Continue with other statements
            }
        }

        echo "\n✅ Database schema created successfully!\n\n";
    }

    // Create test admin user
    echo "👤 Creating test admin user...\n";

    $existingAdmin = $db->query("SELECT id FROM users WHERE username = 'admin' LIMIT 1")->fetch();

    if ($existingAdmin) {
        echo "  ℹ️  Admin user already exists (username: admin)\n";
    } else {
        $passwordHash = password_hash('admin123', PASSWORD_BCRYPT, ['cost' => 12]);

        $db->exec("
            INSERT INTO users (username, email, password_hash, full_name, role, status)
            VALUES ('admin', 'admin@fastandfine.com', '{$passwordHash}', 'Admin User', 'admin', 'active')
        ");

        echo "  ✓ Admin user created successfully\n";
        echo "    Username: admin\n";
        echo "    Password: admin123\n";
        echo "    Email: admin@fastandfine.com\n";
    }

    echo "\n";

    // Add sample services
    echo "🛠️  Adding sample services...\n";

    $services = [
        [
            'name_en' => 'Building Cleaning',
            'name_ar' => 'تنظيف المباني',
            'slug' => 'building-cleaning',
            'short_desc_en' => 'Professional cleaning services for buildings',
            'short_desc_ar' => 'خدمات تنظيف احترافية للمباني',
            'long_desc_en' => 'Comprehensive building cleaning services including deep cleaning, sanitization, and maintenance. Our experienced team uses professional equipment and eco-friendly products.',
            'long_desc_ar' => 'خدمات تنظيف شاملة للمباني تشمل التنظيف العميق والتعقيم والصيانة. فريقنا ذو الخبرة يستخدم معدات احترافية ومنتجات صديقة للبيئة.',
            'icon_class' => 'fa-broom',
            'starting_price' => 150.00,
            'category' => 'cleaning',
            'features_en' => 'Deep Cleaning|Sanitization|Eco-Friendly Products|Professional Equipment',
            'features_ar' => 'تنظيف عميق|تعقيم|منتجات صديقة للبيئة|معدات احترافية',
            'image_url' => 'assets/images/services/building-cleaning.jpg'
        ],
        [
            'name_en' => 'Carpentry Services',
            'name_ar' => 'خدمات النجارة',
            'slug' => 'carpentry',
            'short_desc_en' => 'Expert carpentry and woodwork services',
            'short_desc_ar' => 'خدمات نجارة وأعمال خشبية متخصصة',
            'long_desc_en' => 'Custom carpentry and woodwork services including furniture making, door installation, and repairs. Quality craftsmanship guaranteed.',
            'long_desc_ar' => 'خدمات نجارة وأعمال خشبية مخصصة تشمل صناعة الأثاث وتركيب الأبواب والإصلاحات. نضمن جودة الصنعة.',
            'icon_class' => 'fa-hammer',
            'starting_price' => 200.00,
            'category' => 'construction',
            'features_en' => 'Custom Furniture|Door Installation|Repairs|Quality Materials',
            'features_ar' => 'أثاث مخصص|تركيب الأبواب|إصلاحات|مواد عالية الجودة',
            'image_url' => 'assets/images/services/carpentry.jpg'
        ],
        [
            'name_en' => 'Plumbing Services',
            'name_ar' => 'خدمات السباكة',
            'slug' => 'plumbing',
            'short_desc_en' => 'Professional plumbing repairs and installations',
            'short_desc_ar' => 'إصلاحات وتركيبات سباكة احترافية',
            'long_desc_en' => 'Complete plumbing solutions including leak repairs, pipe installation, and drainage systems. Available 24/7 for emergency services.',
            'long_desc_ar' => 'حلول سباكة شاملة تشمل إصلاح التسريبات وتركيب الأنابيب وأنظمة الصرف. متاح 24/7 لخدمات الطوارئ.',
            'icon_class' => 'fa-faucet-drip',
            'starting_price' => 180.00,
            'category' => 'maintenance',
            'features_en' => 'Leak Repairs|Pipe Installation|24/7 Emergency|Drainage Systems',
            'features_ar' => 'إصلاح التسريبات|تركيب الأنابيب|طوارئ 24/7|أنظمة الصرف',
            'image_url' => 'assets/images/services/plumbing.jpg'
        ],
        [
            'name_en' => 'Air Conditioning',
            'name_ar' => 'تكييف الهواء',
            'slug' => 'air-conditioning',
            'short_desc_en' => 'AC installation, repair and maintenance',
            'short_desc_ar' => 'تركيب وإصلاح وصيانة المكيفات',
            'long_desc_en' => 'Professional AC services including installation, repair, maintenance, and cleaning. We service all brands and types of air conditioning units.',
            'long_desc_ar' => 'خدمات مكيفات احترافية تشمل التركيب والإصلاح والصيانة والتنظيف. نخدم جميع العلامات التجارية وأنواع وحدات التكييف.',
            'icon_class' => 'fa-snowflake',
            'starting_price' => 250.00,
            'category' => 'hvac',
            'features_en' => 'Installation|Repair|Maintenance|All Brands',
            'features_ar' => 'تركيب|إصلاح|صيانة|جميع العلامات التجارية',
            'image_url' => 'assets/images/services/ac.jpg'
        ],
        [
            'name_en' => 'Electromechanical',
            'name_ar' => 'الكهروميكانيك',
            'slug' => 'electromechanical',
            'short_desc_en' => 'Electromechanical systems and services',
            'short_desc_ar' => 'أنظمة وخدمات كهروميكانيكية',
            'long_desc_en' => 'Specialized electromechanical services for industrial and commercial facilities. Expert maintenance and installation.',
            'long_desc_ar' => 'خدمات كهروميكانيكية متخصصة للمرافق الصناعية والتجارية. صيانة وتركيب متخصص.',
            'icon_class' => 'fa-gears',
            'starting_price' => 300.00,
            'category' => 'industrial',
            'features_en' => 'Industrial Systems|Commercial Services|Expert Maintenance|Installation',
            'features_ar' => 'أنظمة صناعية|خدمات تجارية|صيانة متخصصة|تركيب',
            'image_url' => 'assets/images/services/electromechanical.jpg'
        ],
        [
            'name_en' => 'Painting Services',
            'name_ar' => 'خدمات الدهان',
            'slug' => 'painting',
            'short_desc_en' => 'Interior and exterior painting services',
            'short_desc_ar' => 'خدمات دهان داخلي وخارجي',
            'long_desc_en' => 'Professional painting services for residential and commercial properties. High-quality finishes with premium paints.',
            'long_desc_ar' => 'خدمات دهان احترافية للعقارات السكنية والتجارية. تشطيبات عالية الجودة مع دهانات فاخرة.',
            'icon_class' => 'fa-paint-roller',
            'starting_price' => 120.00,
            'category' => 'finishing',
            'features_en' => 'Interior Painting|Exterior Painting|Premium Paints|Quality Finishes',
            'features_ar' => 'دهان داخلي|دهان خارجي|دهانات فاخرة|تشطيبات عالية الجودة',
            'image_url' => 'assets/images/services/painting.jpg'
        ],
        [
            'name_en' => 'Electrical Services',
            'name_ar' => 'الخدمات الكهربائية',
            'slug' => 'electrical',
            'short_desc_en' => 'Electrical installations and repairs',
            'short_desc_ar' => 'تركيبات وإصلاحات كهربائية',
            'long_desc_en' => 'Complete electrical services including wiring, installations, repairs, and maintenance. Licensed and certified electricians.',
            'long_desc_ar' => 'خدمات كهربائية شاملة تشمل التوصيلات والتركيبات والإصلاحات والصيانة. كهربائيون مرخصون ومعتمدون.',
            'icon_class' => 'fa-bolt',
            'starting_price' => 220.00,
            'category' => 'electrical',
            'features_en' => 'Wiring|Installations|Repairs|Licensed Electricians',
            'features_ar' => 'توصيلات|تركيبات|إصلاحات|كهربائيون مرخصون',
            'image_url' => 'assets/images/services/electrical.jpg'
        ],
        [
            'name_en' => 'Gypsum & Partition',
            'name_ar' => 'الجبس والفواصل',
            'slug' => 'gypsum-partition',
            'short_desc_en' => 'Gypsum board and partition services',
            'short_desc_ar' => 'خدمات ألواح الجبس والفواصل',
            'long_desc_en' => 'Professional gypsum board installation, false ceiling, and partition work for residential and commercial spaces.',
            'long_desc_ar' => 'تركيب احترافي لألواح الجبس والأسقف المستعارة وأعمال الفواصل للمساحات السكنية والتجارية.',
            'icon_class' => 'fa-ruler-combined',
            'starting_price' => 180.00,
            'category' => 'construction',
            'features_en' => 'Gypsum Board|False Ceiling|Partitions|Professional Installation',
            'features_ar' => 'ألواح جبس|أسقف مستعارة|فواصل|تركيب احترافي',
            'image_url' => 'assets/images/services/gypsum.jpg'
        ],
        [
            'name_en' => 'Tiling Services',
            'name_ar' => 'خدمات التبليط',
            'slug' => 'tiling',
            'short_desc_en' => 'Floor and wall tiling services',
            'short_desc_ar' => 'خدمات تبليط الأرضيات والجدران',
            'long_desc_en' => 'Expert tiling services for floors, walls, and outdoor spaces. Wide selection of tiles and patterns available.',
            'long_desc_ar' => 'خدمات تبليط متخصصة للأرضيات والجدران والمساحات الخارجية. مجموعة واسعة من البلاط والأنماط المتاحة.',
            'icon_class' => 'fa-grip',
            'starting_price' => 160.00,
            'category' => 'finishing',
            'features_en' => 'Floor Tiling|Wall Tiling|Outdoor Tiling|Pattern Selection',
            'features_ar' => 'تبليط أرضيات|تبليط جدران|تبليط خارجي|اختيار الأنماط',
            'image_url' => 'assets/images/services/tiling.jpg'
        ]
    ];

    foreach ($services as $index => $service) {
        $existing = $db->query("SELECT id FROM services WHERE slug = '{$service['slug']}' LIMIT 1")->fetch();

        if (!$existing) {
            $stmt = $db->prepare("
                INSERT INTO services (
                    name_en, name_ar, slug, short_desc_en, short_desc_ar,
                    long_desc_en, long_desc_ar, icon_class, starting_price,
                    category, features_en, features_ar, image_url,
                    is_active, display_order
                ) VALUES (
                    :name_en, :name_ar, :slug, :short_desc_en, :short_desc_ar,
                    :long_desc_en, :long_desc_ar, :icon_class, :starting_price,
                    :category, :features_en, :features_ar, :image_url,
                    1, :display_order
                )
            ");

            $stmt->execute([
                ':name_en' => $service['name_en'],
                ':name_ar' => $service['name_ar'],
                ':slug' => $service['slug'],
                ':short_desc_en' => $service['short_desc_en'],
                ':short_desc_ar' => $service['short_desc_ar'],
                ':long_desc_en' => $service['long_desc_en'],
                ':long_desc_ar' => $service['long_desc_ar'],
                ':icon_class' => $service['icon_class'],
                ':starting_price' => $service['starting_price'],
                ':category' => $service['category'],
                ':features_en' => $service['features_en'],
                ':features_ar' => $service['features_ar'],
                ':image_url' => $service['image_url'],
                ':display_order' => $index
            ]);

            echo "  ✓ Added service: {$service['name_en']}\n";
        }
    }

    echo "\n";

    // Add sample testimonials
    echo "⭐ Adding sample testimonials...\n";

    $testimonials = [
        [
            'John Smith',
            'CEO',
            'ABC Company',
            5,
            'Excellent service! The team was professional and completed the job on time. Highly recommended for any technical services in Dubai.'
        ],
        [
            'Sarah Johnson',
            'Facility Manager',
            'XYZ Corp',
            5,
            'Fast and Fine provided outstanding building cleaning services. Their attention to detail is impressive, and the pricing is very competitive.'
        ],
        [
            'Mohammed Ahmed',
            'Property Owner',
            '',
            4,
            'Great plumbing and AC services. The technicians were knowledgeable and fixed all issues quickly. Will definitely use them again.'
        ],
        [
            'Emily Chen',
            'Operations Director',
            'Tech Solutions LLC',
            5,
            'We have been using Fast and Fine for all our maintenance needs. They are reliable, professional, and always deliver quality work.'
        ]
    ];

    foreach ($testimonials as $testimonial) {
        list($name, $position, $company, $rating, $text) = $testimonial;

        $db->exec("
            INSERT INTO testimonials (client_name, client_position, client_company, rating, review_text, review_source, verified, featured, is_published)
            VALUES ('{$name}', '{$position}', '{$company}', {$rating}, '{$text}', 'website', 1, 1, 1)
        ");
        echo "  ✓ Added testimonial from: {$name}\n";
    }

    echo "\n";

    // Add sample projects
    echo "📸 Adding sample projects...\n";

    $projects = [
        [
            'service_id' => 1, // Building Cleaning
            'title_en' => 'Dubai Marina Tower Complete Cleaning',
            'title_ar' => 'تنظيف كامل لبرج دبي مارينا',
            'description_en' => 'Complete deep cleaning service for a 40-story residential tower in Dubai Marina including all common areas and facilities.',
            'description_ar' => 'خدمة تنظيف عميق كاملة لبرج سكني من 40 طابقاً في دبي مارينا تشمل جميع المناطق المشتركة والمرافق.',
            'category' => 'Commercial',
            'location' => 'Dubai Marina',
            'completion_date' => '2024-11-15',
            'client_name' => 'Marina Properties LLC',
            'project_duration' => '2 weeks',
            'project_cost' => 15000.00,
            'main_image' => 'assets/images/projects/cleaning-1.jpg',
            'featured' => 1
        ],
        [
            'service_id' => 3, // Plumbing
            'title_en' => 'Commercial Building Plumbing Renovation',
            'title_ar' => 'تجديد السباكة للمبنى التجاري',
            'description_en' => 'Complete plumbing system renovation for a commercial building in Business Bay including new pipes and fixtures.',
            'description_ar' => 'تجديد كامل لنظام السباكة لمبنى تجاري في الخليج التجاري يشمل أنابيب وتجهيزات جديدة.',
            'category' => 'Commercial',
            'location' => 'Business Bay',
            'completion_date' => '2024-10-20',
            'client_name' => 'Bay Business Center',
            'project_duration' => '3 weeks',
            'project_cost' => 25000.00,
            'main_image' => 'assets/images/projects/plumbing-1.jpg',
            'featured' => 1
        ],
        [
            'service_id' => 4, // AC
            'title_en' => 'Villa AC Installation - Palm Jumeirah',
            'title_ar' => 'تركيب مكيفات فيلا - نخلة جميرا',
            'description_en' => 'Installation of central AC system for luxury villa in Palm Jumeirah with smart climate control.',
            'description_ar' => 'تركيب نظام تكييف مركزي لفيلا فاخرة في نخلة جميرا مع تحكم ذكي بالمناخ.',
            'category' => 'Residential',
            'location' => 'Palm Jumeirah',
            'completion_date' => '2024-09-30',
            'client_name' => 'Private Client',
            'project_duration' => '1 week',
            'project_cost' => 35000.00,
            'main_image' => 'assets/images/projects/ac-1.jpg',
            'featured' => 1
        ]
    ];

    foreach ($projects as $project) {
        $stmt = $db->prepare("
            INSERT INTO projects (
                service_id, title_en, title_ar, description_en, description_ar,
                category, location, completion_date, client_name,
                project_duration, project_cost, main_image, featured, is_published
            ) VALUES (
                :service_id, :title_en, :title_ar, :description_en, :description_ar,
                :category, :location, :completion_date, :client_name,
                :project_duration, :project_cost, :main_image, :featured, 1
            )
        ");

        $stmt->execute([
            ':service_id' => $project['service_id'],
            ':title_en' => $project['title_en'],
            ':title_ar' => $project['title_ar'],
            ':description_en' => $project['description_en'],
            ':description_ar' => $project['description_ar'],
            ':category' => $project['category'],
            ':location' => $project['location'],
            ':completion_date' => $project['completion_date'],
            ':client_name' => $project['client_name'],
            ':project_duration' => $project['project_duration'],
            ':project_cost' => $project['project_cost'],
            ':main_image' => $project['main_image'],
            ':featured' => $project['featured']
        ]);

        echo "  ✓ Added project: {$project['title_en']}\n";
    }

    echo "\n";

    // Display statistics
    $userCount = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    $serviceCount = $db->query("SELECT COUNT(*) FROM services")->fetchColumn();
    $projectCount = $db->query("SELECT COUNT(*) FROM projects")->fetchColumn();
    $testimonialCount = $db->query("SELECT COUNT(*) FROM testimonials")->fetchColumn();

    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "  ✅ Setup Complete!\n";
    echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n";
    echo "\n";
    echo "📊 Database Statistics:\n";
    echo "  • Users: {$userCount}\n";
    echo "  • Services: {$serviceCount}\n";
    echo "  • Projects: {$projectCount}\n";
    echo "  • Testimonials: {$testimonialCount}\n";
    echo "\n";
    echo "🚀 Next Steps:\n";
    echo "  1. Start the development server:\n";
    echo "     php -S localhost:8000\n";
    echo "\n";
    echo "  2. Open in browser:\n";
    echo "     http://localhost:8000\n";
    echo "\n";
    echo "  3. Access admin panel:\n";
    echo "     http://localhost:8000/admin/\n";
    echo "     Username: admin\n";
    echo "     Password: admin123\n";
    echo "\n";
    echo "📝 Note: Email sending is disabled in local development mode.\n";
    echo "\n";

} catch (Exception $e) {
    echo "\n";
    echo "❌ Setup failed: " . $e->getMessage() . "\n";
    echo "\n";
    if (isDebugMode()) {
        echo "Stack trace:\n";
        echo $e->getTraceAsString() . "\n";
        echo "\n";
    }
    exit(1);
}
