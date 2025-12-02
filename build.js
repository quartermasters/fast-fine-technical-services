#!/usr/bin/env node

/**
 * Fast and Fine Technical Services FZE - Build Script
 *
 * Combines and minifies CSS and JavaScript files for production
 *
 * Usage:
 *   npm run build          - Build both CSS and JS
 *   npm run build:css      - Build only CSS
 *   npm run build:js       - Build only JS
 *   npm run build:prod     - Build for production (max optimization)
 *   npm run watch          - Watch files and rebuild on change
 *
 * @package FastAndFine
 * @version 1.0.0
 */

const fs = require('fs');
const path = require('path');
const CleanCSS = require('clean-css');
const { minify: minifyJS } = require('terser');

// Configuration
const config = {
    sourceDir: path.join(__dirname, 'assets'),
    buildDir: path.join(__dirname, 'assets', 'build'),

    css: {
        files: [
            'css/main.css',
            'css/sections.css',
            'css/animations.css',
            'css/responsive.css'
        ],
        output: 'app.min.css',
        adminFiles: [
            'css/admin-dashboard.css'
        ],
        adminOutput: 'admin.min.css'
    },

    js: {
        files: [
            'js/main.js',
            'js/animations.js',
            'js/services.js',
            'js/portfolio.js',
            'js/testimonials.js',
            'js/booking.js'
        ],
        output: 'app.min.js'
    }
};

// Color codes for console output
const colors = {
    reset: '\x1b[0m',
    bright: '\x1b[1m',
    green: '\x1b[32m',
    yellow: '\x1b[33m',
    blue: '\x1b[34m',
    red: '\x1b[31m',
    cyan: '\x1b[36m'
};

/**
 * Log message with color
 */
function log(message, color = 'reset') {
    console.log(colors[color] + message + colors.reset);
}

/**
 * Format file size
 */
function formatSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return Math.round((bytes / Math.pow(k, i)) * 100) / 100 + ' ' + sizes[i];
}

/**
 * Create directory if it doesn't exist
 */
function ensureDir(dir) {
    if (!fs.existsSync(dir)) {
        fs.mkdirSync(dir, { recursive: true });
        log(`Created directory: ${dir}`, 'blue');
    }
}

/**
 * Read file content
 */
function readFile(filePath) {
    try {
        return fs.readFileSync(filePath, 'utf8');
    } catch (error) {
        log(`Error reading file ${filePath}: ${error.message}`, 'red');
        return '';
    }
}

/**
 * Write file content
 */
function writeFile(filePath, content) {
    try {
        fs.writeFileSync(filePath, content, 'utf8');
        return true;
    } catch (error) {
        log(`Error writing file ${filePath}: ${error.message}`, 'red');
        return false;
    }
}

/**
 * Build CSS files
 */
async function buildCSS() {
    log('\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━', 'cyan');
    log('Building CSS...', 'bright');
    log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━', 'cyan');

    ensureDir(config.buildDir);

    // Build main app CSS
    log('\n📦 Main Application CSS:', 'yellow');
    let combinedCSS = '';
    let originalSize = 0;

    for (const file of config.css.files) {
        const filePath = path.join(config.sourceDir, file);
        if (fs.existsSync(filePath)) {
            const content = readFile(filePath);
            const size = Buffer.byteLength(content, 'utf8');
            originalSize += size;
            combinedCSS += `\n/* ${file} */\n${content}\n`;
            log(`  ✓ ${file} (${formatSize(size)})`, 'green');
        } else {
            log(`  ✗ ${file} (not found)`, 'red');
        }
    }

    // Minify CSS
    const minified = new CleanCSS({
        level: {
            1: {
                all: true,
                normalizeUrls: false
            },
            2: {
                restructureRules: true,
                removeUnusedAtRules: true,
                mergeIntoShorthands: true,
                mergeMedia: true,
                mergeSemantically: true,
                removeEmpty: true,
                removeDuplicateFontRules: true,
                removeDuplicateMediaBlocks: true,
                removeDuplicateRules: true,
                removeUnusedAtRules: true
            }
        },
        sourceMap: true,
        sourceMapInlineSources: true
    }).minify(combinedCSS);

    if (minified.errors.length > 0) {
        log('\nMinification errors:', 'red');
        minified.errors.forEach(err => log(`  ${err}`, 'red'));
    }

    // Write minified CSS
    const outputPath = path.join(config.buildDir, config.css.output);
    const sourceMapPath = outputPath + '.map';

    // Add source map reference
    const cssWithSourceMap = minified.styles + `\n/*# sourceMappingURL=${config.css.output}.map */`;

    if (writeFile(outputPath, cssWithSourceMap)) {
        const minifiedSize = Buffer.byteLength(minified.styles, 'utf8');
        const savings = ((1 - minifiedSize / originalSize) * 100).toFixed(1);

        log(`\n✓ Output: ${config.css.output}`, 'green');
        log(`  Original: ${formatSize(originalSize)}`, 'blue');
        log(`  Minified: ${formatSize(minifiedSize)}`, 'blue');
        log(`  Saved: ${savings}% reduction`, 'green');

        // Write source map
        if (minified.sourceMap) {
            writeFile(sourceMapPath, minified.sourceMap.toString());
            log(`  Source map: ${config.css.output}.map`, 'blue');
        }
    }

    // Build admin CSS
    log('\n📦 Admin Dashboard CSS:', 'yellow');
    let adminCSS = '';
    let adminOriginalSize = 0;

    for (const file of config.css.adminFiles) {
        const filePath = path.join(config.sourceDir, file);
        if (fs.existsSync(filePath)) {
            const content = readFile(filePath);
            const size = Buffer.byteLength(content, 'utf8');
            adminOriginalSize += size;
            adminCSS += `\n/* ${file} */\n${content}\n`;
            log(`  ✓ ${file} (${formatSize(size)})`, 'green');
        }
    }

    if (adminCSS) {
        const adminMinified = new CleanCSS({ level: 2 }).minify(adminCSS);
        const adminOutputPath = path.join(config.buildDir, config.css.adminOutput);

        if (writeFile(adminOutputPath, adminMinified.styles)) {
            const adminMinifiedSize = Buffer.byteLength(adminMinified.styles, 'utf8');
            const adminSavings = ((1 - adminMinifiedSize / adminOriginalSize) * 100).toFixed(1);

            log(`\n✓ Output: ${config.css.adminOutput}`, 'green');
            log(`  Original: ${formatSize(adminOriginalSize)}`, 'blue');
            log(`  Minified: ${formatSize(adminMinifiedSize)}`, 'blue');
            log(`  Saved: ${adminSavings}% reduction`, 'green');
        }
    }

    log('\n✅ CSS build complete!', 'bright');
}

/**
 * Build JavaScript files
 */
async function buildJS() {
    log('\n━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━', 'cyan');
    log('Building JavaScript...', 'bright');
    log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━', 'cyan');

    ensureDir(config.buildDir);

    log('\n📦 Application JavaScript:', 'yellow');
    let combinedJS = '';
    let originalSize = 0;

    for (const file of config.js.files) {
        const filePath = path.join(config.sourceDir, file);
        if (fs.existsSync(filePath)) {
            const content = readFile(filePath);
            const size = Buffer.byteLength(content, 'utf8');
            originalSize += size;
            combinedJS += `\n/* ${file} */\n${content}\n`;
            log(`  ✓ ${file} (${formatSize(size)})`, 'green');
        } else {
            log(`  ✗ ${file} (not found)`, 'red');
        }
    }

    // Minify JavaScript with Terser
    try {
        const minified = await minifyJS(combinedJS, {
            compress: {
                dead_code: true,
                drop_console: process.env.NODE_ENV === 'production',
                drop_debugger: true,
                pure_funcs: ['console.log'],
                passes: 2
            },
            mangle: {
                toplevel: false,
                keep_classnames: true,
                keep_fnames: false
            },
            format: {
                comments: false,
                preamble: '/* Fast and Fine Technical Services FZE - Generated by build script */'
            },
            sourceMap: {
                filename: config.js.output,
                url: config.js.output + '.map'
            }
        });

        // Write minified JS
        const outputPath = path.join(config.buildDir, config.js.output);
        const sourceMapPath = outputPath + '.map';

        if (writeFile(outputPath, minified.code)) {
            const minifiedSize = Buffer.byteLength(minified.code, 'utf8');
            const savings = ((1 - minifiedSize / originalSize) * 100).toFixed(1);

            log(`\n✓ Output: ${config.js.output}`, 'green');
            log(`  Original: ${formatSize(originalSize)}`, 'blue');
            log(`  Minified: ${formatSize(minifiedSize)}`, 'blue');
            log(`  Saved: ${savings}% reduction`, 'green');

            // Write source map
            if (minified.map) {
                writeFile(sourceMapPath, minified.map);
                log(`  Source map: ${config.js.output}.map`, 'blue');
            }
        }

        log('\n✅ JavaScript build complete!', 'bright');

    } catch (error) {
        log(`\n❌ JavaScript minification failed: ${error.message}`, 'red');
        if (error.line) {
            log(`  Line ${error.line}, Column ${error.col}`, 'red');
        }
    }
}

/**
 * Watch files for changes
 */
async function watch() {
    log('\n👀 Watching files for changes...', 'yellow');
    log('Press Ctrl+C to stop\n', 'blue');

    try {
        const chokidar = require('chokidar');

        // Watch CSS files
        const cssWatcher = chokidar.watch(
            config.css.files.map(f => path.join(config.sourceDir, f)),
            { ignoreInitial: true }
        );

        cssWatcher.on('change', (path) => {
            log(`\n📝 Changed: ${path}`, 'yellow');
            buildCSS();
        });

        // Watch JS files
        const jsWatcher = chokidar.watch(
            config.js.files.map(f => path.join(config.sourceDir, f)),
            { ignoreInitial: true }
        );

        jsWatcher.on('change', (path) => {
            log(`\n📝 Changed: ${path}`, 'yellow');
            buildJS();
        });

    } catch (error) {
        log(`\n❌ Watch failed: ${error.message}`, 'red');
        log('Install chokidar: npm install chokidar', 'yellow');
    }
}

/**
 * Main build function
 */
async function build(target = 'all') {
    const startTime = Date.now();

    log('\n╔═══════════════════════════════════════════╗', 'cyan');
    log('║   Fast & Fine Build System v1.0.0        ║', 'bright');
    log('╚═══════════════════════════════════════════╝', 'cyan');

    try {
        if (target === 'css') {
            await buildCSS();
        } else if (target === 'js') {
            await buildJS();
        } else if (target === 'watch') {
            await buildCSS();
            await buildJS();
            await watch();
            return; // Don't show completion message for watch
        } else {
            await buildCSS();
            await buildJS();
        }

        const duration = ((Date.now() - startTime) / 1000).toFixed(2);
        log(`\n⏱️  Build completed in ${duration}s`, 'green');
        log('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━\n', 'cyan');

    } catch (error) {
        log(`\n❌ Build failed: ${error.message}`, 'red');
        process.exit(1);
    }
}

// Run build
const target = process.argv[2] || 'all';
build(target);
