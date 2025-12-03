<?php
/**
 * Logo Optimization Script
 *
 * Optimizes the logo.png file by:
 * - Reducing dimensions if too large
 * - Compressing PNG with optimal quality
 * - Creating web-optimized version
 */

$sourceFile = __DIR__ . '/assets/images/logo.png';
$optimizedFile = __DIR__ . '/assets/images/logo-optimized.png';

if (!file_exists($sourceFile)) {
    die("Error: Source file not found: $sourceFile\n");
}

echo "Loading image...\n";
$image = imagecreatefrompng($sourceFile);

if (!$image) {
    die("Error: Failed to load PNG image\n");
}

// Get original dimensions
$originalWidth = imagesx($image);
$originalHeight = imagesy($image);

echo "Original size: {$originalWidth}x{$originalHeight}\n";

// Target width for logo (reasonable for web use)
$targetWidth = 800; // Reduced from 1834

// Calculate proportional height
$targetHeight = (int)(($targetWidth / $originalWidth) * $originalHeight);

echo "Target size: {$targetWidth}x{$targetHeight}\n";

// Create new image with target dimensions
$optimized = imagecreatetruecolor($targetWidth, $targetHeight);

// Enable alpha blending for transparency
imagealphablending($optimized, false);
imagesavealpha($optimized, true);

// Set transparent background
$transparent = imagecolorallocatealpha($optimized, 0, 0, 0, 127);
imagefill($optimized, 0, 0, $transparent);

// Enable alpha blending for resampling
imagealphablending($optimized, true);

// Resample image with high quality
imagecopyresampled(
    $optimized,
    $image,
    0, 0, 0, 0,
    $targetWidth,
    $targetHeight,
    $originalWidth,
    $originalHeight
);

// Save optimized image with compression
// PNG compression level: 0 (no compression) to 9 (max compression)
// Using level 6 for good balance between size and quality
imagepng($optimized, $optimizedFile, 6);

echo "Optimized image saved to: $optimizedFile\n";

// Get file sizes
$originalSize = filesize($sourceFile);
$optimizedSize = filesize($optimizedFile);
$reduction = 100 - (($optimizedSize / $originalSize) * 100);

echo "\nOriginal file size: " . formatBytes($originalSize) . "\n";
echo "Optimized file size: " . formatBytes($optimizedSize) . "\n";
echo "Reduction: " . round($reduction, 1) . "%\n";

// Clean up
imagedestroy($image);
imagedestroy($optimized);

echo "\nOptimization complete!\n";
echo "To use the optimized version, run:\n";
echo "mv assets/images/logo-optimized.png assets/images/logo.png\n";

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
