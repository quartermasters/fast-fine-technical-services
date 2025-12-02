<?php
/**
 * Fast and Fine Technical Services FZE - Contact Section
 *
 * Features:
 * - Multi-channel contact options (phone, email, WhatsApp)
 * - Advanced contact form with file upload for issue photos
 * - Google Maps integration with office location
 * - Operating hours display
 * - Emergency contact highlighting
 * - Social media links
 *
 * @package FastAndFine
 * @version 1.0.0
 */

if(!defined('FAST_FINE_APP')) {
    die('Direct access not permitted');
}
?>

<!-- Contact Section -->
<section id="contact" class="contact-section section-padding">
    <div class="container">
        <!-- Section Header -->
        <div class="section-header text-center scroll-reveal">
            <span class="section-label">
                <i class="fa-solid fa-headset"></i>
                <?php _e('get_in_touch'); ?>
            </span>
            <h2 class="section-title"><?php _e('contact_us'); ?></h2>
            <p class="section-description">
                <?php _e('contact_description'); ?>
            </p>
        </div>

        <div class="contact-grid">
            <!-- Contact Information Cards -->
            <div class="contact-info-wrapper">
                <div class="contact-info-grid">
                    <!-- Emergency Hotline -->
                    <div class="contact-info-card emergency-card scroll-reveal delay-100">
                        <div class="card-icon emergency">
                            <i class="fa-solid fa-phone-volume"></i>
                        </div>
                        <h3><?php _e('emergency_hotline'); ?></h3>
                        <p class="card-description"><?php _e('emergency_hotline_desc'); ?></p>
                        <a href="tel:<?php echo WHATSAPP_NUMBER; ?>" class="contact-value emergency-number">
                            <?php echo PHONE_DISPLAY; ?>
                        </a>
                        <span class="availability-badge">
                            <i class="fa-solid fa-clock"></i>
                            <?php _e('available_24_7'); ?>
                        </span>
                    </div>

                    <!-- Email Support -->
                    <div class="contact-info-card scroll-reveal delay-200">
                        <div class="card-icon">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <h3><?php _e('email_support'); ?></h3>
                        <p class="card-description"><?php _e('email_support_desc'); ?></p>
                        <a href="mailto:<?php echo ADMIN_EMAIL; ?>" class="contact-value">
                            <?php echo ADMIN_EMAIL; ?>
                        </a>
                        <span class="response-time">
                            <i class="fa-solid fa-reply"></i>
                            <?php _e('response_within_2_hours'); ?>
                        </span>
                    </div>

                    <!-- WhatsApp Chat -->
                    <div class="contact-info-card whatsapp-card scroll-reveal delay-300">
                        <div class="card-icon whatsapp">
                            <i class="fa-brands fa-whatsapp"></i>
                        </div>
                        <h3><?php _e('whatsapp_chat'); ?></h3>
                        <p class="card-description"><?php _e('whatsapp_chat_desc'); ?></p>
                        <a href="https://wa.me/<?php echo str_replace(['+', '-', ' '], '', WHATSAPP_NUMBER); ?>?text=<?php echo urlencode(__('hello_i_need_help')); ?>"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="btn btn-whatsapp">
                            <i class="fa-brands fa-whatsapp"></i>
                            <span><?php _e('start_chat'); ?></span>
                        </a>
                    </div>

                    <!-- Office Location -->
                    <div class="contact-info-card scroll-reveal delay-400">
                        <div class="card-icon">
                            <i class="fa-solid fa-map-marker-alt"></i>
                        </div>
                        <h3><?php _e('office_location'); ?></h3>
                        <p class="card-description"><?php _e('office_location_desc'); ?></p>
                        <address class="contact-value">
                            <?php echo COMPANY_ADDRESS; ?>
                        </address>
                        <a href="<?php echo GOOGLE_MAPS_LINK; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-outline btn-sm">
                            <i class="fa-solid fa-directions"></i>
                            <span><?php _e('get_directions'); ?></span>
                        </a>
                    </div>
                </div>

                <!-- Operating Hours -->
                <div class="operating-hours scroll-reveal delay-500">
                    <h3>
                        <i class="fa-solid fa-business-time"></i>
                        <?php _e('operating_hours'); ?>
                    </h3>
                    <div class="hours-grid">
                        <div class="hours-row">
                            <span class="day"><?php _e('saturday_thursday'); ?></span>
                            <span class="time">8:00 AM - 6:00 PM</span>
                        </div>
                        <div class="hours-row">
                            <span class="day"><?php _e('friday'); ?></span>
                            <span class="time"><?php _e('closed'); ?></span>
                        </div>
                        <div class="hours-row emergency-hours">
                            <span class="day"><?php _e('emergency_services'); ?></span>
                            <span class="time">
                                <i class="fa-solid fa-circle-check"></i>
                                <?php _e('available_24_7'); ?>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Social Media Links -->
                <div class="social-contact scroll-reveal delay-600">
                    <h3><?php _e('follow_us'); ?></h3>
                    <div class="social-links-large">
                        <a href="<?php echo SOCIAL_FACEBOOK; ?>" target="_blank" rel="noopener noreferrer" class="social-link facebook" aria-label="Facebook">
                            <i class="fa-brands fa-facebook-f"></i>
                            <span>Facebook</span>
                        </a>
                        <a href="<?php echo SOCIAL_INSTAGRAM; ?>" target="_blank" rel="noopener noreferrer" class="social-link instagram" aria-label="Instagram">
                            <i class="fa-brands fa-instagram"></i>
                            <span>Instagram</span>
                        </a>
                        <a href="<?php echo SOCIAL_LINKEDIN; ?>" target="_blank" rel="noopener noreferrer" class="social-link linkedin" aria-label="LinkedIn">
                            <i class="fa-brands fa-linkedin-in"></i>
                            <span>LinkedIn</span>
                        </a>
                        <a href="<?php echo SOCIAL_TWITTER; ?>" target="_blank" rel="noopener noreferrer" class="social-link twitter" aria-label="Twitter">
                            <i class="fa-brands fa-twitter"></i>
                            <span>Twitter</span>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Contact Form -->
            <div class="contact-form-wrapper scroll-reveal delay-200">
                <div class="contact-form-card">
                    <div class="form-header">
                        <h3><?php _e('send_message'); ?></h3>
                        <p><?php _e('send_message_desc'); ?></p>
                    </div>

                    <form id="contactForm" class="contact-form" enctype="multipart/form-data" novalidate>
                        <?php echo csrfField(); ?>

                        <!-- Name Field -->
                        <div class="form-group">
                            <label for="contactName" class="form-label">
                                <?php _e('full_name'); ?>
                                <span class="required">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fa-solid fa-user"></i>
                                <input
                                    type="text"
                                    id="contactName"
                                    name="name"
                                    class="form-control"
                                    placeholder="<?php _e('enter_name'); ?>"
                                    required
                                    autocomplete="name"
                                >
                            </div>
                            <span class="form-error" id="nameError"></span>
                        </div>

                        <!-- Email Field -->
                        <div class="form-group">
                            <label for="contactEmail" class="form-label">
                                <?php _e('email_address'); ?>
                                <span class="required">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fa-solid fa-envelope"></i>
                                <input
                                    type="email"
                                    id="contactEmail"
                                    name="email"
                                    class="form-control"
                                    placeholder="<?php _e('enter_email'); ?>"
                                    required
                                    autocomplete="email"
                                >
                            </div>
                            <span class="form-error" id="emailError"></span>
                        </div>

                        <!-- Phone Field -->
                        <div class="form-group">
                            <label for="contactPhone" class="form-label">
                                <?php _e('phone_number'); ?>
                                <span class="required">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fa-solid fa-phone"></i>
                                <input
                                    type="tel"
                                    id="contactPhone"
                                    name="phone"
                                    class="form-control"
                                    placeholder="<?php _e('enter_phone'); ?>"
                                    required
                                    autocomplete="tel"
                                >
                            </div>
                            <span class="form-error" id="phoneError"></span>
                        </div>

                        <!-- Service Selection -->
                        <div class="form-group">
                            <label for="contactService" class="form-label">
                                <?php _e('service_interested'); ?>
                            </label>
                            <div class="input-icon">
                                <i class="fa-solid fa-tools"></i>
                                <select id="contactService" name="service_id" class="form-control">
                                    <option value=""><?php _e('select_service'); ?></option>
                                    <?php
                                    $services = dbSelect("SELECT * FROM services WHERE is_active = 1 ORDER BY display_order ASC");
                                    foreach ($services as $service):
                                        $serviceName = $currentLang === 'ar' ? $service['name_ar'] : $service['name_en'];
                                    ?>
                                        <option value="<?php echo $service['id']; ?>">
                                            <?php echo escapeHTML($serviceName); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Subject Field -->
                        <div class="form-group">
                            <label for="contactSubject" class="form-label">
                                <?php _e('subject'); ?>
                                <span class="required">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fa-solid fa-tag"></i>
                                <input
                                    type="text"
                                    id="contactSubject"
                                    name="subject"
                                    class="form-control"
                                    placeholder="<?php _e('enter_subject'); ?>"
                                    required
                                >
                            </div>
                            <span class="form-error" id="subjectError"></span>
                        </div>

                        <!-- Message Field -->
                        <div class="form-group">
                            <label for="contactMessage" class="form-label">
                                <?php _e('message'); ?>
                                <span class="required">*</span>
                            </label>
                            <div class="input-icon">
                                <i class="fa-solid fa-message"></i>
                                <textarea
                                    id="contactMessage"
                                    name="message"
                                    class="form-control"
                                    rows="5"
                                    placeholder="<?php _e('enter_message'); ?>"
                                    required
                                ></textarea>
                            </div>
                            <span class="form-error" id="messageError"></span>
                        </div>

                        <!-- File Upload for Issue Photos -->
                        <div class="form-group">
                            <label for="contactPhotos" class="form-label">
                                <?php _e('attach_photos'); ?>
                                <span class="optional"><?php _e('optional'); ?></span>
                            </label>
                            <div class="file-upload-wrapper">
                                <input
                                    type="file"
                                    id="contactPhotos"
                                    name="photos[]"
                                    class="file-input"
                                    accept="image/jpeg,image/png,image/jpg,image/webp"
                                    multiple
                                    data-max-size="<?php echo MAX_UPLOAD_SIZE; ?>"
                                    data-max-files="<?php echo MAX_UPLOAD_FILES; ?>"
                                >
                                <label for="contactPhotos" class="file-upload-label">
                                    <i class="fa-solid fa-cloud-arrow-up"></i>
                                    <span class="file-upload-text"><?php _e('click_to_upload'); ?></span>
                                    <span class="file-upload-hint">
                                        <?php echo __('max_files_size', ['max' => MAX_UPLOAD_FILES, 'size' => formatBytes(MAX_UPLOAD_SIZE)]); ?>
                                    </span>
                                </label>
                                <div class="file-preview" id="filePreview"></div>
                            </div>
                            <span class="form-error" id="photosError"></span>
                        </div>

                        <!-- Preferred Contact Method -->
                        <div class="form-group">
                            <label class="form-label"><?php _e('preferred_contact'); ?></label>
                            <div class="radio-group">
                                <label class="radio-label">
                                    <input type="radio" name="contact_method" value="phone" checked>
                                    <span class="radio-custom"></span>
                                    <span><?php _e('phone_call'); ?></span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="contact_method" value="email">
                                    <span class="radio-custom"></span>
                                    <span><?php _e('email'); ?></span>
                                </label>
                                <label class="radio-label">
                                    <input type="radio" name="contact_method" value="whatsapp">
                                    <span class="radio-custom"></span>
                                    <span><?php _e('whatsapp'); ?></span>
                                </label>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <button type="submit" class="btn btn-primary btn-block btn-lg" id="contactSubmitBtn">
                            <i class="fa-solid fa-paper-plane"></i>
                            <span><?php _e('send_message'); ?></span>
                            <span class="btn-loading" style="display: none;">
                                <i class="fa-solid fa-spinner fa-spin"></i>
                            </span>
                        </button>

                        <!-- Form Info -->
                        <p class="form-info">
                            <i class="fa-solid fa-shield-halved"></i>
                            <?php _e('form_privacy_notice'); ?>
                        </p>
                    </form>
                </div>
            </div>
        </div>

        <!-- Google Maps -->
        <div class="map-section scroll-reveal delay-300">
            <div class="map-header">
                <h3>
                    <i class="fa-solid fa-location-dot"></i>
                    <?php _e('find_us_on_map'); ?>
                </h3>
                <p><?php _e('visit_us_description'); ?></p>
            </div>
            <div class="map-wrapper">
                <?php if(defined('GOOGLE_MAPS_API') && GOOGLE_MAPS_API): ?>
                    <!-- Google Maps Embed -->
                    <iframe
                        class="google-map"
                        loading="lazy"
                        allowfullscreen
                        referrerpolicy="no-referrer-when-downgrade"
                        src="https://www.google.com/maps/embed/v1/place?key=<?php echo GOOGLE_MAPS_API; ?>&q=<?php echo urlencode(COMPANY_ADDRESS); ?>&zoom=15"
                    ></iframe>
                <?php else: ?>
                    <!-- Fallback Static Map -->
                    <div class="map-placeholder">
                        <i class="fa-solid fa-map-location-dot"></i>
                        <p><?php _e('map_placeholder'); ?></p>
                        <a href="<?php echo GOOGLE_MAPS_LINK; ?>" target="_blank" rel="noopener noreferrer" class="btn btn-primary">
                            <i class="fa-solid fa-map-marker-alt"></i>
                            <span><?php _e('open_in_google_maps'); ?></span>
                        </a>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Service Areas -->
            <div class="service-areas">
                <h4>
                    <i class="fa-solid fa-map"></i>
                    <?php _e('service_areas'); ?>
                </h4>
                <div class="areas-grid">
                    <span class="area-badge">
                        <i class="fa-solid fa-location-dot"></i>
                        Dubai Marina
                    </span>
                    <span class="area-badge">
                        <i class="fa-solid fa-location-dot"></i>
                        Downtown Dubai
                    </span>
                    <span class="area-badge">
                        <i class="fa-solid fa-location-dot"></i>
                        Business Bay
                    </span>
                    <span class="area-badge">
                        <i class="fa-solid fa-location-dot"></i>
                        Jumeirah
                    </span>
                    <span class="area-badge">
                        <i class="fa-solid fa-location-dot"></i>
                        Palm Jumeirah
                    </span>
                    <span class="area-badge">
                        <i class="fa-solid fa-location-dot"></i>
                        Dubai Hills
                    </span>
                    <span class="area-badge">
                        <i class="fa-solid fa-location-dot"></i>
                        JBR
                    </span>
                    <span class="area-badge">
                        <i class="fa-solid fa-location-dot"></i>
                        <?php _e('all_dubai'); ?>
                    </span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- WhatsApp Quick Action (Mobile) -->
<div class="whatsapp-quick-action mobile-only">
    <a href="https://wa.me/<?php echo str_replace(['+', '-', ' '], '', WHATSAPP_NUMBER); ?>?text=<?php echo urlencode(__('hello_contact_form')); ?>"
       target="_blank"
       rel="noopener noreferrer"
       class="btn btn-whatsapp btn-lg btn-block">
        <i class="fa-brands fa-whatsapp"></i>
        <span><?php _e('chat_with_us_now'); ?></span>
    </a>
</div>
