#!/bin/bash

###############################################################################
# Fast and Fine Technical Services FZE - Image Optimization Script
#
# This script converts all PNG, JPG, and JPEG images to WebP format
# with optimal compression for web performance.
#
# Requirements:
#   - cwebp (WebP encoder) - Install: brew install webp (macOS) or apt-get install webp (Ubuntu)
#   - ImageMagick (optional, for resizing) - Install: brew install imagemagick
#
# Usage:
#   chmod +x optimize-images.sh
#   ./optimize-images.sh
#
# @package FastAndFine
# @version 1.0.0
###############################################################################

# Color codes for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
CYAN='\033[0;36m'
NC='\033[0m' # No Color

# Configuration
SOURCE_DIR="assets/images"
OUTPUT_DIR="assets/images/webp"
QUALITY=85
BACKUP_DIR="assets/images/original"

# Statistics
TOTAL_FILES=0
CONVERTED_FILES=0
ORIGINAL_SIZE=0
OPTIMIZED_SIZE=0

echo -e "${CYAN}"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo "  Fast & Fine Image Optimization Script v1.0.0"
echo "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
echo -e "${NC}"

# Check if cwebp is installed
if ! command -v cwebp &> /dev/null; then
    echo -e "${RED}Error: cwebp is not installed${NC}"
    echo -e "${YELLOW}Install it using:${NC}"
    echo -e "  macOS:   brew install webp"
    echo -e "  Ubuntu:  sudo apt-get install webp"
    echo -e "  Windows: Download from https://developers.google.com/speed/webp/download"
    exit 1
fi

echo -e "${GREEN}✓ cwebp found${NC}"
echo ""

# Create output directories
mkdir -p "$OUTPUT_DIR"
mkdir -p "$BACKUP_DIR"

echo -e "${BLUE}Source directory: ${NC}$SOURCE_DIR"
echo -e "${BLUE}Output directory: ${NC}$OUTPUT_DIR"
echo -e "${BLUE}Quality setting:  ${NC}$QUALITY"
echo ""

# Function to convert image to WebP
convert_to_webp() {
    local input_file="$1"
    local relative_path="${input_file#$SOURCE_DIR/}"
    local filename=$(basename "$input_file")
    local filename_noext="${filename%.*}"
    local dirname=$(dirname "$relative_path")

    # Skip if already in webp directory
    if [[ "$input_file" == *"/webp/"* ]] || [[ "$input_file" == *"/original/"* ]]; then
        return
    fi

    # Create subdirectories in output
    mkdir -p "$OUTPUT_DIR/$dirname"

    local output_file="$OUTPUT_DIR/$dirname/${filename_noext}.webp"

    # Get original file size
    local orig_size=$(stat -f%z "$input_file" 2>/dev/null || stat -c%s "$input_file" 2>/dev/null)
    ORIGINAL_SIZE=$((ORIGINAL_SIZE + orig_size))
    TOTAL_FILES=$((TOTAL_FILES + 1))

    echo -e "${YELLOW}Converting:${NC} $relative_path"

    # Convert to WebP
    if cwebp -q $QUALITY "$input_file" -o "$output_file" 2>/dev/null; then
        # Get optimized file size
        local opt_size=$(stat -f%z "$output_file" 2>/dev/null || stat -c%s "$output_file" 2>/dev/null)
        OPTIMIZED_SIZE=$((OPTIMIZED_SIZE + opt_size))
        CONVERTED_FILES=$((CONVERTED_FILES + 1))

        # Calculate savings
        local savings=$((100 - (opt_size * 100 / orig_size)))
        local orig_kb=$((orig_size / 1024))
        local opt_kb=$((opt_size / 1024))

        echo -e "${GREEN}  ✓ ${NC}${filename_noext}.webp ${BLUE}(${orig_kb}KB → ${opt_kb}KB, -${savings}%)${NC}"

        # Backup original file
        mkdir -p "$BACKUP_DIR/$dirname"
        cp "$input_file" "$BACKUP_DIR/$relative_path"
    else
        echo -e "${RED}  ✗ Failed to convert${NC}"
    fi

    echo ""
}

# Export function for use in find
export -f convert_to_webp
export SOURCE_DIR OUTPUT_DIR QUALITY BACKUP_DIR
export TOTAL_FILES CONVERTED_FILES ORIGINAL_SIZE OPTIMIZED_SIZE
export RED GREEN YELLOW BLUE CYAN NC

# Find and convert PNG images
echo -e "${CYAN}━━━ Converting PNG images ━━━${NC}"
while IFS= read -r -d '' file; do
    convert_to_webp "$file"
done < <(find "$SOURCE_DIR" -type f -name "*.png" -print0)

# Find and convert JPG/JPEG images
echo -e "${CYAN}━━━ Converting JPG/JPEG images ━━━${NC}"
while IFS= read -r -d '' file; do
    convert_to_webp "$file"
done < <(find "$SOURCE_DIR" -type f \( -name "*.jpg" -o -name "*.jpeg" \) -print0)

# Calculate totals
if [ $TOTAL_FILES -eq 0 ]; then
    echo -e "${YELLOW}No images found in $SOURCE_DIR${NC}"
    echo -e "${BLUE}Place your images in the assets/images directory and run this script again.${NC}"
    exit 0
fi

TOTAL_SAVINGS=$((100 - (OPTIMIZED_SIZE * 100 / ORIGINAL_SIZE)))
ORIGINAL_MB=$(echo "scale=2; $ORIGINAL_SIZE / 1024 / 1024" | bc)
OPTIMIZED_MB=$(echo "scale=2; $OPTIMIZED_SIZE / 1024 / 1024" | bc)
SAVED_MB=$(echo "scale=2; ($ORIGINAL_SIZE - $OPTIMIZED_SIZE) / 1024 / 1024" | bc)

# Print summary
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo -e "${GREEN}Optimization Complete!${NC}"
echo -e "${CYAN}━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━${NC}"
echo ""
echo -e "${BLUE}Files processed:    ${NC}$TOTAL_FILES"
echo -e "${BLUE}Successfully converted: ${NC}$CONVERTED_FILES"
echo -e "${BLUE}Original size:      ${NC}${ORIGINAL_MB} MB"
echo -e "${BLUE}Optimized size:     ${NC}${OPTIMIZED_MB} MB"
echo -e "${GREEN}Total savings:      ${NC}${SAVED_MB} MB (${TOTAL_SAVINGS}% reduction)"
echo ""
echo -e "${BLUE}WebP images:        ${NC}$OUTPUT_DIR"
echo -e "${BLUE}Original backups:   ${NC}$BACKUP_DIR"
echo ""

# Generate HTML usage examples
cat > "$OUTPUT_DIR/README.md" <<EOF
# WebP Images

This directory contains WebP-optimized versions of all images.

## Usage in HTML

### Method 1: Picture Element (Recommended)
\`\`\`html
<picture>
    <source srcset="<?php echo assetUrl('images/webp/logo.webp'); ?>" type="image/webp">
    <source srcset="<?php echo assetUrl('images/logo.png'); ?>" type="image/png">
    <img src="<?php echo assetUrl('images/logo.png'); ?>" alt="Logo" loading="lazy">
</picture>
\`\`\`

### Method 2: JavaScript Detection
\`\`\`javascript
// Check WebP support
function supportsWebP() {
    const elem = document.createElement('canvas');
    if (!!(elem.getContext && elem.getContext('2d'))) {
        return elem.toDataURL('image/webp').indexOf('data:image/webp') === 0;
    }
    return false;
}

// Use appropriate image format
const imageExt = supportsWebP() ? '.webp' : '.png';
document.getElementById('myImage').src = '/assets/images/webp/logo' + imageExt;
\`\`\`

## Statistics

- **Total files processed:** $TOTAL_FILES
- **Successfully converted:** $CONVERTED_FILES
- **Original size:** ${ORIGINAL_MB} MB
- **Optimized size:** ${OPTIMIZED_MB} MB
- **Savings:** ${SAVED_MB} MB (${TOTAL_SAVINGS}% reduction)

## Browser Support

WebP is supported by:
- Chrome 23+
- Firefox 65+
- Edge 18+
- Opera 12.1+
- Safari 14+ (macOS 11+, iOS 14+)

For older browsers, use the \`<picture>\` element with fallback images.

## Optimization Settings

- **Quality:** ${QUALITY}
- **Format:** WebP (lossy)
- **Encoder:** libwebp (cwebp)

Generated on: $(date)
EOF

echo -e "${GREEN}✓ README.md created in $OUTPUT_DIR${NC}"
echo ""
echo -e "${CYAN}Next steps:${NC}"
echo -e "1. Review optimized images in ${BLUE}$OUTPUT_DIR${NC}"
echo -e "2. Update image paths in your code to use WebP format"
echo -e "3. Implement <picture> elements for browser compatibility"
echo -e "4. Original files are backed up in ${BLUE}$BACKUP_DIR${NC}"
echo ""
