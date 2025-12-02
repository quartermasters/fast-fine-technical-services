<?php
/**
 * Fast and Fine Technical Services FZE - Booking Calculator Section
 *
 * Multi-step wizard interface for service booking with real-time quote calculation
 *
 * Features:
 * - 6-step wizard (Service, Date/Time, Location, Details, Contact, Review)
 * - Progress indicator with step validation
 * - Real-time price calculation
 * - Form validation at each step
 * - Session storage for progress saving
 * - Photo upload for issue documentation
 * - Calendar integration
 * - Responsive design
 *
 * @package FastAndFine
 * @version 1.0.0
 */

// Prevent direct access
if(!defined('FAST_FINE_APP')) {
    die('Direct access not permitted');
}

// Get services data for dropdown
$servicesData = [
    [
        'id' => 1,
        'name_en' => 'Building Cleaning',
        'name_ar' => 'تنظيف المباني',
        'icon' => 'fa-broom',
        'base_price' => 150,
        'price_per_hour' => 50,
        'category' => 'residential'
    ],
    [
        'id' => 2,
        'name_en' => 'Carpentry',
        'name_ar' => 'النجارة',
        'icon' => 'fa-hammer',
        'base_price' => 200,
        'price_per_hour' => 80,
        'category' => 'residential'
    ],
    [
        'id' => 3,
        'name_en' => 'Plumbing',
        'name_ar' => 'السباكة',
        'icon' => 'fa-faucet-drip',
        'base_price' => 180,
        'price_per_hour' => 75,
        'category' => 'residential'
    ],
    [
        'id' => 4,
        'name_en' => 'Air Conditioning',
        'name_ar' => 'التكييف',
        'icon' => 'fa-snowflake',
        'base_price' => 250,
        'price_per_hour' => 90,
        'category' => 'residential'
    ],
    [
        'id' => 5,
        'name_en' => 'Electromechanical',
        'name_ar' => 'الكهروميكانيكية',
        'icon' => 'fa-gears',
        'base_price' => 300,
        'price_per_hour' => 100,
        'category' => 'commercial'
    ],
    [
        'id' => 6,
        'name_en' => 'Painting',
        'name_ar' => 'الدهان',
        'icon' => 'fa-paint-roller',
        'base_price' => 120,
        'price_per_hour' => 60,
        'category' => 'residential'
    ],
    [
        'id' => 7,
        'name_en' => 'Electrical',
        'name_ar' => 'الكهرباء',
        'icon' => 'fa-bolt',
        'base_price' => 180,
        'price_per_hour' => 70,
        'category' => 'residential'
    ],
    [
        'id' => 8,
        'name_en' => 'Gypsum & Partition',
        'name_ar' => 'الجبس والقواطع',
        'icon' => 'fa-border-all',
        'base_price' => 220,
        'price_per_hour' => 85,
        'category' => 'commercial'
    ],
    [
        'id' => 9,
        'name_en' => 'Tiling',
        'name_ar' => 'البلاط',
        'icon' => 'fa-table-cells',
        'base_price' => 200,
        'price_per_hour' => 75,
        'category' => 'residential'
    ]
];

// Emirates for location dropdown
$emirates = [
    'dubai' => ['en' => 'Dubai', 'ar' => 'دبي'],
    'abu_dhabi' => ['en' => 'Abu Dhabi', 'ar' => 'أبو ظبي'],
    'sharjah' => ['en' => 'Sharjah', 'ar' => 'الشارقة'],
    'ajman' => ['en' => 'Ajman', 'ar' => 'عجمان'],
    'ras_al_khaimah' => ['en' => 'Ras Al Khaimah', 'ar' => 'رأس الخيمة'],
    'fujairah' => ['en' => 'Fujairah', 'ar' => 'الفجيرة'],
    'umm_al_quwain' => ['en' => 'Umm Al Quwain', 'ar' => 'أم القيوين']
];

// Property types
$propertyTypes = [
    'apartment' => ['en' => 'Apartment', 'ar' => 'شقة'],
    'villa' => ['en' => 'Villa', 'ar' => 'فيلا'],
    'office' => ['en' => 'Office', 'ar' => 'مكتب'],
    'warehouse' => ['en' => 'Warehouse', 'ar' => 'مستودع'],
    'shop' => ['en' => 'Shop', 'ar' => 'محل تجاري'],
    'building' => ['en' => 'Building', 'ar' => 'مبنى'],
    'other' => ['en' => 'Other', 'ar' => 'أخرى']
];

$currentLang = getCurrentLanguage();
?>

<!-- Booking Calculator Section -->
<section id="booking" class="booking-section">
    <div class="container">
        <!-- Section Header -->
        <div class="section-header">
            <h2 class="section-title scroll-reveal">
                <i class="fa-solid fa-calendar-check"></i>
                <?php _e('book_now'); ?>
            </h2>
            <p class="section-description scroll-reveal delay-100">
                <?php _e('booking_description'); ?>
            </p>
        </div>

        <!-- Booking Wizard -->
        <div class="booking-wizard scroll-reveal delay-200">
            <!-- Progress Indicator -->
            <div class="wizard-progress">
                <div class="progress-bar">
                    <div class="progress-fill" id="progressFill"></div>
                </div>
                <div class="progress-steps">
                    <div class="progress-step active" data-step="1">
                        <div class="step-circle">
                            <i class="fa-solid fa-wrench"></i>
                            <span class="step-number">1</span>
                        </div>
                        <span class="step-label"><?php _e('select_service'); ?></span>
                    </div>
                    <div class="progress-step" data-step="2">
                        <div class="step-circle">
                            <i class="fa-solid fa-calendar"></i>
                            <span class="step-number">2</span>
                        </div>
                        <span class="step-label"><?php _e('date_time'); ?></span>
                    </div>
                    <div class="progress-step" data-step="3">
                        <div class="step-circle">
                            <i class="fa-solid fa-map-marker-alt"></i>
                            <span class="step-number">3</span>
                        </div>
                        <span class="step-label"><?php _e('location'); ?></span>
                    </div>
                    <div class="progress-step" data-step="4">
                        <div class="step-circle">
                            <i class="fa-solid fa-clipboard-list"></i>
                            <span class="step-number">4</span>
                        </div>
                        <span class="step-label"><?php _e('details'); ?></span>
                    </div>
                    <div class="progress-step" data-step="5">
                        <div class="step-circle">
                            <i class="fa-solid fa-user"></i>
                            <span class="step-number">5</span>
                        </div>
                        <span class="step-label"><?php _e('contact'); ?></span>
                    </div>
                    <div class="progress-step" data-step="6">
                        <div class="step-circle">
                            <i class="fa-solid fa-check"></i>
                            <span class="step-number">6</span>
                        </div>
                        <span class="step-label"><?php _e('review'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Booking Form -->
            <form id="bookingForm" class="booking-form" novalidate>
                <?php echo csrfField(); ?>

                <!-- Step 1: Service Selection -->
                <div class="wizard-step active" data-step="1">
                    <div class="step-header">
                        <h3><?php _e('select_service'); ?></h3>
                        <p><?php _e('select_service_description'); ?></p>
                    </div>

                    <div class="service-selection">
                        <?php foreach ($servicesData as $service): ?>
                            <div class="service-option">
                                <input
                                    type="radio"
                                    name="service_id"
                                    value="<?php echo $service['id']; ?>"
                                    id="service-<?php echo $service['id']; ?>"
                                    data-base-price="<?php echo $service['base_price']; ?>"
                                    data-hourly-price="<?php echo $service['price_per_hour']; ?>"
                                    data-name="<?php echo $currentLang === 'ar' ? $service['name_ar'] : $service['name_en']; ?>"
                                    required
                                >
                                <label for="service-<?php echo $service['id']; ?>" class="service-card">
                                    <div class="service-icon">
                                        <i class="fa-solid <?php echo $service['icon']; ?>"></i>
                                    </div>
                                    <div class="service-info">
                                        <h4><?php echo $currentLang === 'ar' ? $service['name_ar'] : $service['name_en']; ?></h4>
                                        <p class="service-price">
                                            <?php _e('starting_from'); ?>
                                            <span class="price-value">AED <?php echo number_format($service['base_price']); ?></span>
                                        </p>
                                    </div>
                                    <div class="service-check">
                                        <i class="fa-solid fa-circle-check"></i>
                                    </div>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fa-solid fa-layer-group"></i>
                            <?php _e('service_urgency'); ?>
                        </label>
                        <select name="urgency" id="urgency" class="form-control" required>
                            <option value=""><?php _e('select_urgency'); ?></option>
                            <option value="regular"><?php _e('regular'); ?> (<?php _e('within_24_48_hours'); ?>)</option>
                            <option value="priority"><?php _e('priority'); ?> (<?php _e('within_12_hours'); ?>)</option>
                            <option value="emergency"><?php _e('emergency'); ?> (<?php _e('immediate'); ?>)</option>
                        </select>
                        <div class="form-error"></div>
                    </div>
                </div>

                <!-- Step 2: Date & Time Selection -->
                <div class="wizard-step" data-step="2">
                    <div class="step-header">
                        <h3><?php _e('select_date_time'); ?></h3>
                        <p><?php _e('select_date_time_description'); ?></p>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="booking_date">
                                <i class="fa-solid fa-calendar-days"></i>
                                <?php _e('preferred_date'); ?>
                            </label>
                            <input
                                type="date"
                                name="booking_date"
                                id="booking_date"
                                class="form-control"
                                min="<?php echo date('Y-m-d'); ?>"
                                required
                            >
                            <div class="form-error"></div>
                        </div>

                        <div class="form-group">
                            <label for="booking_time">
                                <i class="fa-solid fa-clock"></i>
                                <?php _e('preferred_time'); ?>
                            </label>
                            <select name="booking_time" id="booking_time" class="form-control" required>
                                <option value=""><?php _e('select_time'); ?></option>
                                <option value="08:00">08:00 AM</option>
                                <option value="09:00">09:00 AM</option>
                                <option value="10:00">10:00 AM</option>
                                <option value="11:00">11:00 AM</option>
                                <option value="12:00">12:00 PM</option>
                                <option value="13:00">01:00 PM</option>
                                <option value="14:00">02:00 PM</option>
                                <option value="15:00">03:00 PM</option>
                                <option value="16:00">04:00 PM</option>
                                <option value="17:00">05:00 PM</option>
                                <option value="18:00">06:00 PM</option>
                            </select>
                            <div class="form-error"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="estimated_duration">
                            <i class="fa-solid fa-hourglass-half"></i>
                            <?php _e('estimated_duration'); ?>
                        </label>
                        <select name="estimated_duration" id="estimated_duration" class="form-control" required>
                            <option value=""><?php _e('select_duration'); ?></option>
                            <option value="1">1 <?php _e('hour'); ?></option>
                            <option value="2">2 <?php _e('hours'); ?></option>
                            <option value="3">3 <?php _e('hours'); ?></option>
                            <option value="4">4 <?php _e('hours'); ?></option>
                            <option value="5">5 <?php _e('hours'); ?></option>
                            <option value="6">6 <?php _e('hours'); ?></option>
                            <option value="8">8 <?php _e('hours'); ?></option>
                        </select>
                        <div class="form-error"></div>
                    </div>

                    <div class="info-box">
                        <i class="fa-solid fa-info-circle"></i>
                        <p><?php _e('booking_time_note'); ?></p>
                    </div>
                </div>

                <!-- Step 3: Location -->
                <div class="wizard-step" data-step="3">
                    <div class="step-header">
                        <h3><?php _e('location_details'); ?></h3>
                        <p><?php _e('location_description'); ?></p>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="emirate">
                                <i class="fa-solid fa-map"></i>
                                <?php _e('emirate'); ?>
                            </label>
                            <select name="emirate" id="emirate" class="form-control" required>
                                <option value=""><?php _e('select_emirate'); ?></option>
                                <?php foreach ($emirates as $key => $emirate): ?>
                                    <option value="<?php echo $key; ?>">
                                        <?php echo $currentLang === 'ar' ? $emirate['ar'] : $emirate['en']; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-error"></div>
                        </div>

                        <div class="form-group">
                            <label for="area">
                                <i class="fa-solid fa-location-dot"></i>
                                <?php _e('area'); ?>
                            </label>
                            <input
                                type="text"
                                name="area"
                                id="area"
                                class="form-control"
                                placeholder="<?php _e('enter_area'); ?>"
                                required
                            >
                            <div class="form-error"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="property_type">
                            <i class="fa-solid fa-building"></i>
                            <?php _e('property_type'); ?>
                        </label>
                        <select name="property_type" id="property_type" class="form-control" required>
                            <option value=""><?php _e('select_property_type'); ?></option>
                            <?php foreach ($propertyTypes as $key => $type): ?>
                                <option value="<?php echo $key; ?>">
                                    <?php echo $currentLang === 'ar' ? $type['ar'] : $type['en']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-error"></div>
                    </div>

                    <div class="form-group">
                        <label for="address">
                            <i class="fa-solid fa-location-arrow"></i>
                            <?php _e('full_address'); ?>
                        </label>
                        <textarea
                            name="address"
                            id="address"
                            class="form-control"
                            rows="3"
                            placeholder="<?php _e('enter_full_address'); ?>"
                            required
                        ></textarea>
                        <div class="form-error"></div>
                    </div>
                </div>

                <!-- Step 4: Additional Details -->
                <div class="wizard-step" data-step="4">
                    <div class="step-header">
                        <h3><?php _e('additional_details'); ?></h3>
                        <p><?php _e('additional_details_description'); ?></p>
                    </div>

                    <div class="form-group">
                        <label for="issue_description">
                            <i class="fa-solid fa-pen-to-square"></i>
                            <?php _e('describe_issue'); ?>
                        </label>
                        <textarea
                            name="issue_description"
                            id="issue_description"
                            class="form-control"
                            rows="5"
                            placeholder="<?php _e('describe_issue_placeholder'); ?>"
                            required
                        ></textarea>
                        <div class="form-error"></div>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fa-solid fa-images"></i>
                            <?php _e('upload_photos'); ?> <span class="optional">(<?php _e('optional'); ?>)</span>
                        </label>
                        <div class="file-upload-area" id="fileUploadArea">
                            <input
                                type="file"
                                name="photos[]"
                                id="photos"
                                class="file-input"
                                accept="image/jpeg,image/png,image/webp"
                                multiple
                            >
                            <label for="photos" class="file-upload-label">
                                <i class="fa-solid fa-cloud-arrow-up"></i>
                                <span class="upload-text"><?php _e('drag_drop_photos'); ?></span>
                                <span class="upload-button"><?php _e('choose_files'); ?></span>
                            </label>
                        </div>
                        <div class="file-preview" id="filePreview"></div>
                        <div class="form-hint">
                            <i class="fa-solid fa-info-circle"></i>
                            <?php _e('photo_upload_hint'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fa-solid fa-bell"></i>
                            <?php _e('special_requirements'); ?> <span class="optional">(<?php _e('optional'); ?>)</span>
                        </label>
                        <div class="checkbox-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="requirements[]" value="tools_required">
                                <span><?php _e('bring_tools_materials'); ?></span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="requirements[]" value="parking_available">
                                <span><?php _e('parking_available'); ?></span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="requirements[]" value="access_restrictions">
                                <span><?php _e('access_restrictions'); ?></span>
                            </label>
                            <label class="checkbox-label">
                                <input type="checkbox" name="requirements[]" value="safety_equipment">
                                <span><?php _e('safety_equipment_needed'); ?></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Step 5: Contact Information -->
                <div class="wizard-step" data-step="5">
                    <div class="step-header">
                        <h3><?php _e('contact_information'); ?></h3>
                        <p><?php _e('contact_information_description'); ?></p>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="client_name">
                                <i class="fa-solid fa-user"></i>
                                <?php _e('full_name'); ?>
                            </label>
                            <input
                                type="text"
                                name="client_name"
                                id="client_name"
                                class="form-control"
                                placeholder="<?php _e('enter_full_name'); ?>"
                                required
                            >
                            <div class="form-error"></div>
                        </div>

                        <div class="form-group">
                            <label for="client_email">
                                <i class="fa-solid fa-envelope"></i>
                                <?php _e('email_address'); ?>
                            </label>
                            <input
                                type="email"
                                name="client_email"
                                id="client_email"
                                class="form-control"
                                placeholder="<?php _e('enter_email'); ?>"
                                required
                            >
                            <div class="form-error"></div>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="client_phone">
                                <i class="fa-solid fa-phone"></i>
                                <?php _e('phone_number'); ?>
                            </label>
                            <input
                                type="tel"
                                name="client_phone"
                                id="client_phone"
                                class="form-control"
                                placeholder="+971 XX XXX XXXX"
                                pattern="[\+]?[0-9]{10,15}"
                                required
                            >
                            <div class="form-error"></div>
                        </div>

                        <div class="form-group">
                            <label for="alternate_phone">
                                <i class="fa-solid fa-mobile"></i>
                                <?php _e('alternate_phone'); ?> <span class="optional">(<?php _e('optional'); ?>)</span>
                            </label>
                            <input
                                type="tel"
                                name="alternate_phone"
                                id="alternate_phone"
                                class="form-control"
                                placeholder="+971 XX XXX XXXX"
                                pattern="[\+]?[0-9]{10,15}"
                            >
                            <div class="form-error"></div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>
                            <i class="fa-solid fa-comments"></i>
                            <?php _e('preferred_contact_method'); ?>
                        </label>
                        <div class="radio-group">
                            <label class="radio-label">
                                <input type="radio" name="contact_method" value="phone" checked>
                                <span><i class="fa-solid fa-phone"></i> <?php _e('phone_call'); ?></span>
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="contact_method" value="whatsapp">
                                <span><i class="fa-brands fa-whatsapp"></i> <?php _e('whatsapp'); ?></span>
                            </label>
                            <label class="radio-label">
                                <input type="radio" name="contact_method" value="email">
                                <span><i class="fa-solid fa-envelope"></i> <?php _e('email'); ?></span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Step 6: Review & Confirm -->
                <div class="wizard-step" data-step="6">
                    <div class="step-header">
                        <h3><?php _e('review_booking'); ?></h3>
                        <p><?php _e('review_booking_description'); ?></p>
                    </div>

                    <div class="booking-summary" id="bookingSummary">
                        <!-- Summary will be populated by JavaScript -->
                    </div>

                    <!-- Price Breakdown -->
                    <div class="price-breakdown">
                        <h4><?php _e('price_breakdown'); ?></h4>
                        <div class="price-item">
                            <span><?php _e('service_charge'); ?>:</span>
                            <span id="basePrice">AED 0</span>
                        </div>
                        <div class="price-item">
                            <span><?php _e('hourly_rate'); ?>:</span>
                            <span id="hourlyCharge">AED 0</span>
                        </div>
                        <div class="price-item" id="urgencyChargeItem" style="display: none;">
                            <span><?php _e('urgency_charge'); ?>:</span>
                            <span id="urgencyCharge">AED 0</span>
                        </div>
                        <div class="price-item subtotal">
                            <span><?php _e('subtotal'); ?>:</span>
                            <span id="subtotal">AED 0</span>
                        </div>
                        <div class="price-item tax">
                            <span><?php _e('vat'); ?> (5%):</span>
                            <span id="vatAmount">AED 0</span>
                        </div>
                        <div class="price-item total">
                            <span><?php _e('total_estimate'); ?>:</span>
                            <span id="totalPrice">AED 0</span>
                        </div>
                    </div>

                    <div class="info-box warning">
                        <i class="fa-solid fa-exclamation-triangle"></i>
                        <p><?php _e('price_estimate_note'); ?></p>
                    </div>

                    <div class="form-group">
                        <label class="checkbox-label terms-checkbox">
                            <input type="checkbox" name="terms_accepted" id="terms_accepted" required>
                            <span>
                                <?php _e('i_agree_to'); ?>
                                <a href="#terms-conditions" target="_blank"><?php _e('terms_conditions'); ?></a>
                            </span>
                        </label>
                        <div class="form-error"></div>
                    </div>
                </div>

                <!-- Navigation Buttons -->
                <div class="wizard-navigation">
                    <button type="button" class="btn btn-outline" id="prevBtn" style="display: none;">
                        <i class="fa-solid fa-arrow-left"></i>
                        <span><?php _e('previous'); ?></span>
                    </button>

                    <button type="button" class="btn btn-primary" id="nextBtn">
                        <span><?php _e('next'); ?></span>
                        <i class="fa-solid fa-arrow-right"></i>
                    </button>

                    <button type="submit" class="btn btn-success" id="submitBtn" style="display: none;">
                        <i class="fa-solid fa-paper-plane"></i>
                        <span><?php _e('submit_booking'); ?></span>
                    </button>
                </div>
            </form>

            <!-- Success Message (Hidden) -->
            <div class="booking-success" id="bookingSuccess" style="display: none;">
                <div class="success-icon">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
                <h3><?php _e('booking_confirmed'); ?></h3>
                <p><?php _e('booking_confirmation_message'); ?></p>
                <div class="booking-reference">
                    <strong><?php _e('booking_reference'); ?>:</strong>
                    <span id="bookingReference"></span>
                </div>
                <div class="success-actions">
                    <a href="#home" class="btn btn-primary">
                        <i class="fa-solid fa-home"></i>
                        <span><?php _e('back_to_home'); ?></span>
                    </a>
                    <button type="button" class="btn btn-outline" id="newBookingBtn">
                        <i class="fa-solid fa-plus"></i>
                        <span><?php _e('new_booking'); ?></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Quick Quote Sidebar -->
        <div class="quick-quote-sidebar" id="quickQuote">
            <div class="quote-header">
                <h4>
                    <i class="fa-solid fa-calculator"></i>
                    <?php _e('quick_quote'); ?>
                </h4>
                <button type="button" class="close-quote" id="closeQuote">
                    <i class="fa-solid fa-times"></i>
                </button>
            </div>
            <div class="quote-body">
                <div class="quote-item">
                    <span><?php _e('service'); ?>:</span>
                    <strong id="quoteService">-</strong>
                </div>
                <div class="quote-item">
                    <span><?php _e('duration'); ?>:</span>
                    <strong id="quoteDuration">-</strong>
                </div>
                <div class="quote-item">
                    <span><?php _e('urgency'); ?>:</span>
                    <strong id="quoteUrgency">-</strong>
                </div>
                <div class="quote-total">
                    <span><?php _e('estimated_total'); ?>:</span>
                    <strong id="quoteTotal">AED 0</strong>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Embed services data for JavaScript -->
<script type="application/json" id="bookingServicesData">
<?php echo json_encode($servicesData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT); ?>
</script>
