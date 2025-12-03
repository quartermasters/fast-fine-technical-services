<?php
/**
 * Create Placeholder Technician Avatars
 */

// Avatar configurations
$avatars = [
    [
        'filename' => 'assets/images/tech-1.jpg',
        'bg_color' => [0, 45, 87],  // Navy
        'text' => 'AH',
        'text_color' => [255, 255, 255]
    ],
    [
        'filename' => 'assets/images/tech-2.jpg',
        'bg_color' => [0, 159, 227],  // Cyan
        'text' => 'MK',
        'text_color' => [255, 255, 255]
    ],
    [
        'filename' => 'assets/images/tech-3.jpg',
        'bg_color' => [251, 191, 36],  // Yellow
        'text' => 'SR',
        'text_color' => [0, 45, 87]  // Navy text for yellow bg
    ]
];

$size = 200;

foreach ($avatars as $avatar) {
    // Create image
    $image = imagecreatetruecolor($size, $size);

    // Allocate colors
    $bgColor = imagecolorallocate($image, $avatar['bg_color'][0], $avatar['bg_color'][1], $avatar['bg_color'][2]);
    $textColor = imagecolorallocate($image, $avatar['text_color'][0], $avatar['text_color'][1], $avatar['text_color'][2]);

    // Fill background
    imagefill($image, 0, 0, $bgColor);

    // Add text (initials)
    $fontSize = 60;
    $font = 5; // Built-in font

    // Calculate text position (center)
    $textWidth = imagefontwidth($font) * strlen($avatar['text']);
    $textHeight = imagefontheight($font);
    $x = ($size - $textWidth) / 2;
    $y = ($size - $textHeight) / 2;

    // Use TrueType font if available, otherwise use built-in
    if (function_exists('imagettftext')) {
        // Try to use system font
        $fontFile = null;
        $possibleFonts = [
            '/System/Library/Fonts/Helvetica.ttc',
            '/usr/share/fonts/truetype/dejavu/DejaVuSans-Bold.ttf',
            '/Windows/Fonts/arial.ttf'
        ];

        foreach ($possibleFonts as $font) {
            if (file_exists($font)) {
                $fontFile = $font;
                break;
            }
        }

        if ($fontFile) {
            $bbox = imagettfbbox($fontSize, 0, $fontFile, $avatar['text']);
            $x = ($size - ($bbox[2] - $bbox[0])) / 2;
            $y = ($size - ($bbox[1] - $bbox[7])) / 2 + abs($bbox[7]);
            imagettftext($image, $fontSize, 0, $x, $y, $textColor, $fontFile, $avatar['text']);
        } else {
            // Fallback to built-in font
            imagestring($image, 5, $x, $y, $avatar['text'], $textColor);
        }
    } else {
        imagestring($image, 5, $x, $y, $avatar['text'], $textColor);
    }

    // Save as JPEG
    imagejpeg($image, $avatar['filename'], 90);
    imagedestroy($image);

    echo "Created: " . $avatar['filename'] . "\n";
}

echo "\nAll avatar placeholders created successfully!\n";
