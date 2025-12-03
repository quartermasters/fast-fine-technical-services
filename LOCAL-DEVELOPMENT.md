# Fast & Fine - Local Development Guide

## Quick Start

The local development environment is now running! ✅

### Access the Website

- **Homepage:** http://localhost:8000
- **Admin Panel:** http://localhost:8000/admin/

### Admin Credentials

```
Username: admin
Password: admin123
Email: admin@fastandfine.com
```

## What's Configured

✅ **SQLite Database** - Located at `database/fastfine.db`
✅ **Test Admin User** - Ready to login
✅ **Sample Services** - 9 services pre-loaded
✅ **Sample Testimonials** - 4 testimonials pre-loaded
✅ **Debug Mode** - Enabled for development
✅ **Email** - Disabled (no actual emails sent)

## Server Management

### Start Server

```bash
php -S localhost:8000
```

### Stop Server

Press `Ctrl+C` in the terminal where the server is running, or:

```bash
# Find the PHP server process
ps aux | grep "php -S"

# Kill it
kill <PID>
```

### Check Server Status

```bash
curl http://localhost:8000
```

## Database Management

### View Database

```bash
# Install sqlite3 if not already installed
brew install sqlite3  # macOS

# Open database
sqlite3 database/fastfine.db

# List tables
.tables

# View users
SELECT * FROM users;

# Exit
.quit
```

### Reset Database

```bash
# Delete database
rm database/fastfine.db

# Recreate
php setup-local.php
```

### Add More Test Data

Edit `setup-local.php` and add data to the respective sections, then run:

```bash
php setup-local.php
```

## Testing Different Features

### 1. Homepage (Hero Section)

Visit: http://localhost:8000

**Test:**
- Hero video/image background
- Animated typing effect
- Statistics counter
- Language switcher (EN/AR)
- Theme toggle (Light/Dark)

### 2. Services Section

Scroll to services or visit: http://localhost:8000/#services

**Test:**
- 9 service cards with 3D flip effect
- Service filtering
- Service search
- Modal popup with details
- "Book Now" buttons

### 3. Portfolio Section

Visit: http://localhost:8000/#portfolio

**Test:**
- Masonry grid layout
- Lightbox image viewer
- Before/after slider
- Portfolio filtering

### 4. Testimonials Section

Visit: http://localhost:8000/#testimonials

**Test:**
- Carousel navigation
- Auto-play functionality
- Star ratings
- Touch/swipe on mobile

### 5. Booking Calculator

Visit: http://localhost:8000/#booking

**Test:**
- Multi-step wizard (6 steps)
- Form validation
- Date/time picker
- Price calculation
- Form submission (saves to database)

### 6. Contact Form

Visit: http://localhost:8000/#contact

**Test:**
- Form validation
- File upload (images, max 5MB)
- Google Maps embed
- Form submission (saves to database)

### 7. Admin Panel

Visit: http://localhost:8000/admin/

**Test:**
- Login system (username: admin, password: admin123)
- Dashboard with statistics
- Recent bookings list
- Recent contacts list
- Popular services
- Account lockout (try 5 wrong passwords)
- Logout functionality

## Development Workflow

### 1. Make Code Changes

Edit files in your favorite editor:
- PHP files: `includes/`, `sections/`, `handlers/`
- CSS files: `assets/css/`
- JS files: `assets/js/`

### 2. Refresh Browser

Changes to PHP, CSS, and JS are reflected immediately. Just refresh the browser.

### 3. Rebuild Assets (if needed)

If you modify CSS or JS and want to test production builds:

```bash
npm run build
```

Then change `config.php`:

```php
define('ENVIRONMENT', 'production');
```

## Common Tasks

### Add a New Service

```sql
sqlite3 database/fastfine.db

INSERT INTO services (name, slug, description, icon, base_price, is_active, display_order)
VALUES ('New Service', 'new-service', 'Description here', 'fa-icon-name', 200.00, 1, 10);

.quit
```

### Add a New Testimonial

```sql
sqlite3 database/fastfine.db

INSERT INTO testimonials (client_name, client_position, client_company, rating, testimonial, is_approved, is_featured)
VALUES ('Client Name', 'Position', 'Company', 5, 'Great service!', 1, 1);

.quit
```

### View Form Submissions

```sql
sqlite3 database/fastfine.db

# View bookings
SELECT * FROM bookings ORDER BY created_at DESC LIMIT 10;

# View contacts
SELECT * FROM contacts ORDER BY created_at DESC LIMIT 10;

.quit
```

### Clear Form Submissions

```sql
sqlite3 database/fastfine.db

DELETE FROM bookings;
DELETE FROM contacts;

.quit
```

## Troubleshooting

### Port Already in Use

```bash
# Find what's using port 8000
lsof -i :8000

# Kill it
kill -9 <PID>

# Or use a different port
php -S localhost:8001
```

### Database Locked Error

```bash
# Close any SQLite connections
# Then restart the server
```

### Cannot Access Admin Panel

1. Check admin user exists:
```bash
sqlite3 database/fastfine.db "SELECT * FROM users WHERE username='admin';"
```

2. Reset admin password:
```bash
sqlite3 database/fastfine.db "UPDATE users SET password_hash='$2y$12$Lm5qZK0xK9qYZXqYZXqYZuVwK0xK9qYZXqYZXqYZXqYZXqYZXq' WHERE username='admin';"
```

### Changes Not Showing

1. Clear browser cache (Cmd+Shift+R / Ctrl+Shift+R)
2. Check browser console for errors (F12)
3. Check PHP error log

## File Structure

```
fast-fine-website/
├── config.php              # Local development configuration
├── setup-local.php         # Database setup script
├── index.php               # Homepage
├── database/
│   ├── fastfine.db        # SQLite database
│   └── schema-sqlite.sql  # Database schema
├── includes/              # Core PHP files
│   ├── config.php
│   ├── db-connect.php     # Database connection (SQLite support)
│   ├── functions.php
│   ├── security.php
│   ├── translations.php
│   ├── header.php
│   └── footer.php
├── sections/              # Page sections
│   ├── hero.php
│   ├── services-interactive.php
│   ├── projects-portfolio.php
│   ├── testimonials-live.php
│   ├── booking-calculator.php
│   └── contact.php
├── admin/                 # Admin panel
│   ├── auth.php
│   ├── login.php
│   └── dashboard.php
├── handlers/              # Form handlers
│   ├── contact-handler.php
│   └── booking-handler.php
└── assets/                # Static assets
    ├── css/               # Stylesheets
    ├── js/                # JavaScript
    ├── images/            # Images
    └── build/             # Minified assets
```

## Next Steps

1. ✅ **Local Development Running** - You can now test the website
2. 📝 **Add Images** - Place images in `assets/images/` per `assets/images/README.md`
3. 🎨 **Customize** - Edit colors, content, translations
4. 🧪 **Test Everything** - Follow `TESTING.md` checklist
5. 🚀 **Deploy** - Follow `DEPLOYMENT.md` when ready

## Need Help?

- **Documentation:** Check `README.md`, `DEPLOYMENT.md`, `TESTING.md`
- **Code Comments:** All files have detailed inline comments
- **Git History:** `git log` shows implementation details

## Switching to Production

When ready to deploy:

1. Update `config.php` with production database credentials (MySQL)
2. Change `ENVIRONMENT` to `'production'`
3. Set `DEBUG_MODE` to `false`
4. Enable `EMAIL_ENABLED` and add API keys
5. Run `npm run build` to create production assets
6. Follow `DEPLOYMENT.md` guide

---

**Last Updated:** December 2, 2025
**Status:** Local Development Ready ✅
