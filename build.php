<?php
/**
 * Fast and Fine Technical Services FZE - Build Script
 *
 * Minifies and bundles CSS and JavaScript files for production
 *
 * Usage: php build.php
 *
 * @package FastAndFine
 * @version 1.0.0
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

// Build configuration
$config = [
    'css_files' => [
        'assets/css/main.css',
        'assets/css/sections.css',
        'assets/css/breadcrumb.css',
        'assets/css/animations.css',
        'assets/css/responsive.css'
    ],
    'js_files' => [
        'assets/js/lazy-loading.js',
        'assets/js/main.js',
        'assets/js/services.js',
        'assets/js/portfolio.js',
        'assets/js/testimonials.js',
        'assets/js/booking.js'
    ],
    'output_dir' => 'assets/build',
    'css_output' => 'app.min.css',
    'js_output' => 'app.min.js'
];

echo "========================================\n";
echo "Fast & Fine - Production Build Script\n";
echo "========================================\n\n";

/**
 * Minify CSS content
 */
function minifyCSS($css) {
    // Remove comments
    $css = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $css);

    // Remove whitespace
    $css = str_replace(["\r\n", "\r", "\n", "\t"], '', $css);
    $css = preg_replace('/\s+/', ' ', $css);

    // Remove unnecessary spaces
    $css = preg_replace('/\s*([{}:;,>~+])\s*/', '$1', $css);
    $css = preg_replace('/;}/', '}', $css);

    return trim($css);
}

/**
 * Minify JavaScript content
 */
function minifyJS($js) {
    // Remove comments (simple approach)
    $js = preg_replace('!/\*.*?\*/!s', '', $js);
    $js = preg_replace('/\/\/.*?[\r\n]/', "\n", $js);

    // Remove extra whitespace
    $js = preg_replace('/\s+/', ' ', $js);

    // Remove spaces around operators
    $js = preg_replace('/\s*([=+\-*\/%<>!&|^~?:,;{}()\[\]])\s*/', '$1', $js);

    return trim($js);
}

/**
 * Bundle and minify CSS files
 */
function buildCSS($files, $outputDir, $outputFile) {
    echo "Building CSS...\n";

    $combinedCSS = '';
    $totalOriginalSize = 0;

    foreach ($files as $file) {
        if (!file_exists($file)) {
            echo "  ⚠️  Warning: File not found: $file\n";
            continue;
        }

        $content = file_get_contents($file);
        $size = strlen($content);
        $totalOriginalSize += $size;

        echo "  ✓ Including: $file (" . formatBytes($size) . ")\n";

        $combinedCSS .= "\n/* Source: $file */\n";
        $combinedCSS .= $content . "\n";
    }

    // Minify combined CSS
    $minifiedCSS = minifyCSS($combinedCSS);
    $minifiedSize = strlen($minifiedCSS);

    // Create output directory if it doesn't exist
    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0755, true);
    }

    // Write minified CSS
    $outputPath = "$outputDir/$outputFile";
    file_put_contents($outputPath, $minifiedCSS);

    $reduction = 100 - (($minifiedSize / $totalOriginalSize) * 100);

    echo "\n  📦 Output: $outputPath\n";
    echo "  📊 Original size: " . formatBytes($totalOriginalSize) . "\n";
    echo "  📊 Minified size: " . formatBytes($minifiedSize) . "\n";
    echo "  💰 Reduction: " . round($reduction, 1) . "%\n\n";

    return true;
}

/**
 * Bundle and minify JavaScript files
 */
function buildJS($files, $outputDir, $outputFile) {
    echo "Building JavaScript...\n";

    $combinedJS = '';
    $totalOriginalSize = 0;

    foreach ($files as $file) {
        if (!file_exists($file)) {
            echo "  ⚠️  Warning: File not found: $file\n";
            continue;
        }

        $content = file_get_contents($file);
        $size = strlen($content);
        $totalOriginalSize += $size;

        echo "  ✓ Including: $file (" . formatBytes($size) . ")\n";

        $combinedJS .= "\n/* Source: $file */\n";
        $combinedJS .= $content . "\n;\n"; // Add semicolon to prevent issues
    }

    // Minify combined JavaScript
    $minifiedJS = minifyJS($combinedJS);
    $minifiedSize = strlen($minifiedJS);

    // Create output directory if it doesn't exist
    if (!is_dir($outputDir)) {
        mkdir($outputDir, 0755, true);
    }

    // Write minified JavaScript
    $outputPath = "$outputDir/$outputFile";
    file_put_contents($outputPath, $minifiedJS);

    $reduction = 100 - (($minifiedSize / $totalOriginalSize) * 100);

    echo "\n  📦 Output: $outputPath\n";
    echo "  📊 Original size: " . formatBytes($totalOriginalSize) . "\n";
    echo "  📊 Minified size: " . formatBytes($minifiedSize) . "\n";
    echo "  💰 Reduction: " . round($reduction, 1) . "%\n\n";

    return true;
}

/**
 * Format bytes to human readable format
 */
function formatBytes($bytes, $precision = 2) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $bytes = max($bytes, 0);
    $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow = min($pow, count($units) - 1);
    $bytes /= pow(1024, $pow);
    return round($bytes, $precision) . ' ' . $units[$pow];
}

/**
 * Create build info file
 */
function createBuildInfo($outputDir) {
    $buildInfo = [
        'build_date' => date('Y-m-d H:i:s'),
        'version' => '1.0.0',
        'php_version' => PHP_VERSION,
        'environment' => 'production'
    ];

    $outputPath = "$outputDir/build-info.json";
    file_put_contents($outputPath, json_encode($buildInfo, JSON_PRETTY_PRINT));

    echo "Build info saved to: $outputPath\n\n";
}

// Main build process
try {
    $startTime = microtime(true);

    // Build CSS
    buildCSS($config['css_files'], $config['output_dir'], $config['css_output']);

    // Build JavaScript
    buildJS($config['js_files'], $config['output_dir'], $config['js_output']);

    // Create build info
    createBuildInfo($config['output_dir']);

    $duration = round(microtime(true) - $startTime, 2);

    echo "========================================\n";
    echo "✅ Build completed successfully!\n";
    echo "⏱️  Duration: {$duration} seconds\n";
    echo "========================================\n\n";

    echo "To use production build:\n";
    echo "1. Set ENVIRONMENT='production' in config.php\n";
    echo "2. Upload the assets/build/ directory to your server\n\n";

} catch (Exception $e) {
    echo "\n❌ Build failed: " . $e->getMessage() . "\n";
    exit(1);
}
