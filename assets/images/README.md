# Fast & Fine Images Directory

This directory contains all image assets for the Fast and Fine Technical Services FZE website.

## Required Images

### 1. Brand Assets
- **logo.png** (300x100px, transparent) - Main logo for header
- **logo-white.png** (300x100px, transparent) - White version for footer/dark backgrounds
- **favicon-16x16.png** (16x16px) - Browser favicon
- **favicon-32x32.png** (32x32px) - Browser favicon
- **apple-touch-icon.png** (180x180px) - iOS home screen icon

### 2. Hero Section
- **hero-video.mp4** (1920x1080px, max 5MB) - Background video for hero section
- **hero-poster.jpg** (1920x1080px) - Fallback poster image for video
- **hero-bg.jpg** (1920x1080px) - Alternative static background

### 3. Services (9 images)
- **service-building-cleaning.jpg** (800x600px)
- **service-carpentry.jpg** (800x600px)
- **service-plumbing.jpg** (800x600px)
- **service-air-conditioning.jpg** (800x600px)
- **service-electromechanical.jpg** (800x600px)
- **service-painting.jpg** (800x600px)
- **service-electrical.jpg** (800x600px)
- **service-gypsum-partition.jpg** (800x600px)
- **service-tiling.jpg** (800x600px)

### 4. Portfolio/Projects (12-20 images)
- **project-{number}-before.jpg** (1200x800px) - Before photos
- **project-{number}-after.jpg** (1200x800px) - After photos
- **project-{number}-main.jpg** (1200x800px) - Main project photo

Recommended: 12-20 project images showcasing completed work

### 5. Team Members (Optional, 4-8 images)
- **team-{name}.jpg** (400x400px, square) - Team member photos

### 6. Testimonials (Optional, 8-12 images)
- **client-{number}.jpg** (200x200px, square) - Client avatars/company logos

### 7. About Section
- **about-company.jpg** (1200x800px) - Company/office photo
- **about-team.jpg** (1200x800px) - Team photo
- **certifications.png** (variable) - Certificates/awards

### 8. PWA/Social Media
- **og-image.jpg** (1200x630px) - Open Graph image for social sharing
- **pwa-icon-192.png** (192x192px) - PWA icon
- **pwa-icon-512.png** (512x512px) - PWA icon

## Image Optimization

After adding your images to this directory, run the optimization script:

```bash
./optimize-images.sh
```

This will:
1. Convert all PNG/JPG images to WebP format (85% quality)
2. Create optimized versions in `assets/images/webp/`
3. Backup originals to `assets/images/original/`
4. Generate optimization report with file size savings

### Expected Results
- **25-35% file size reduction** compared to JPEG
- **Better quality** at smaller file sizes
- **Faster page load times**

## Image Guidelines

### File Format Preferences
1. **Photos** → JPEG/WebP (services, portfolio, team)
2. **Graphics/Logos** → PNG/WebP (transparent backgrounds)
3. **Icons** → SVG (scalable, no optimization needed)

### Size Recommendations
- **Hero/Full-width images:** 1920px width, 80-90% quality
- **Service thumbnails:** 800px width, 80% quality
- **Portfolio images:** 1200px width, 85% quality
- **Avatars/Icons:** 200-400px square, 85% quality

### Performance Tips
1. Always optimize images before uploading
2. Use WebP format with fallbacks for older browsers
3. Implement lazy loading for images below the fold
4. Compress videos (H.264, max 5MB)
5. Use responsive images with `srcset` attribute

## Lazy Loading Implementation

The website automatically lazy-loads images using the `loading="lazy"` attribute:

```html
<img src="<?php echo assetUrl('images/service-plumbing.jpg'); ?>"
     alt="Plumbing Services"
     loading="lazy">
```

### WebP with Fallback

Use the `<picture>` element for WebP with fallback:

```html
<picture>
    <source srcset="<?php echo assetUrl('images/webp/service-plumbing.webp'); ?>" type="image/webp">
    <source srcset="<?php echo assetUrl('images/service-plumbing.jpg'); ?>" type="image/jpeg">
    <img src="<?php echo assetUrl('images/service-plumbing.jpg'); ?>"
         alt="Plumbing Services"
         loading="lazy">
</picture>
```

## Current Status

**Directory is empty** - Please add your images according to the requirements above, then run the optimization script.

## Quick Start

1. **Gather your images** following the requirements above
2. **Place them in this directory** (`assets/images/`)
3. **Run optimization:**
   ```bash
   ./optimize-images.sh
   ```
4. **Update image paths** in your PHP files to use WebP versions
5. **Test** in multiple browsers to ensure fallbacks work

## Tools for Image Optimization

### Online Tools
- **TinyPNG** - https://tinypng.com/ (PNG/JPEG compression)
- **Squoosh** - https://squoosh.app/ (Google's image optimizer)
- **ImageOptim** - https://imageoptim.com/mac (macOS app)

### Command Line Tools
```bash
# Install ImageMagick (resize/convert)
brew install imagemagick

# Install WebP encoder
brew install webp

# Example: Resize image to 800px width
convert large-image.jpg -resize 800x service-image.jpg

# Example: Convert to WebP with 85% quality
cwebp -q 85 input.jpg -o output.webp
```

## Browser Support

### WebP Format
- ✅ Chrome 23+ (2012)
- ✅ Firefox 65+ (2019)
- ✅ Edge 18+ (2018)
- ✅ Safari 14+ (2020)
- ✅ Opera 12.1+ (2012)

**Coverage:** 95%+ of global users

### Lazy Loading
- ✅ Chrome 77+ (2019)
- ✅ Firefox 75+ (2020)
- ✅ Edge 79+ (2020)
- ✅ Safari 15.4+ (2022)

**Coverage:** 85%+ of global users

For older browsers, the website includes polyfills.

## Notes

- Keep original high-resolution images archived separately
- Update images seasonally or when services change
- Maintain consistent visual style across all images
- Use professional photography for best results
- Consider hiring a professional photographer for key images

---

**Last Updated:** December 2, 2025
**Generated by:** Claude Code - Fast & Fine Build System
