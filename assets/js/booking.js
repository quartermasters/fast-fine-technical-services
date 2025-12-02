/**
 * Fast and Fine Technical Services FZE - Booking Wizard Script
 *
 * Features:
 * - Multi-step wizard navigation
 * - Form validation at each step
 * - Real-time price calculation
 * - Progress indicator updates
 * - Session storage for progress saving
 * - File upload with preview
 * - Summary generation
 * - Form submission handling
 *
 * @package FastAndFine
 * @version 1.0.0
 */

(function() {
    'use strict';

    // State
    const state = {
        currentStep: 1,
        totalSteps: 6,
        servicesData: [],
        formData: {},
        pricing: {
            basePrice: 0,
            hourlyRate: 0,
            duration: 0,
            urgencyMultiplier: 1,
            subtotal: 0,
            vat: 0,
            total: 0
        },
        uploadedFiles: []
    };

    // DOM Elements
    const elements = {
        form: document.getElementById('bookingForm'),
        steps: null,
        progressSteps: null,
        progressFill: document.getElementById('progressFill'),
        prevBtn: document.getElementById('prevBtn'),
        nextBtn: document.getElementById('nextBtn'),
        submitBtn: document.getElementById('submitBtn'),
        bookingSuccess: document.getElementById('bookingSuccess'),
        bookingSummary: document.getElementById('bookingSummary'),
        newBookingBtn: document.getElementById('newBookingBtn'),
        quickQuote: document.getElementById('quickQuote'),
        closeQuote: document.getElementById('closeQuote'),
        fileUploadArea: document.getElementById('fileUploadArea'),
        fileInput: document.getElementById('photos'),
        filePreview: document.getElementById('filePreview')
    };

    // Initialize
    function init() {
        if (!elements.form) return;

        loadServicesData();
        loadSavedProgress();

        elements.steps = Array.from(document.querySelectorAll('.wizard-step'));
        elements.progressSteps = Array.from(document.querySelectorAll('.progress-step'));

        attachEventListeners();
        updateUI();
        setupFileUpload();
    }

    // Load services data from JSON script tag
    function loadServicesData() {
        const dataElement = document.getElementById('bookingServicesData');
        if (dataElement) {
            try {
                state.servicesData = JSON.parse(dataElement.textContent);
            } catch (error) {
                console.error('Error parsing booking services data:', error);
            }
        }
    }

    // Attach event listeners
    function attachEventListeners() {
        // Navigation buttons
        if (elements.prevBtn) {
            elements.prevBtn.addEventListener('click', previousStep);
        }

        if (elements.nextBtn) {
            elements.nextBtn.addEventListener('click', nextStep);
        }

        if (elements.submitBtn) {
            elements.submitBtn.addEventListener('click', submitForm);
        }

        if (elements.newBookingBtn) {
            elements.newBookingBtn.addEventListener('click', resetForm);
        }

        // Service selection
        const serviceInputs = document.querySelectorAll('input[name="service_id"]');
        serviceInputs.forEach(input => {
            input.addEventListener('change', handleServiceChange);
        });

        // Duration and urgency for price calculation
        const durationInput = document.getElementById('estimated_duration');
        if (durationInput) {
            durationInput.addEventListener('change', calculatePrice);
        }

        const urgencyInput = document.getElementById('urgency');
        if (urgencyInput) {
            urgencyInput.addEventListener('change', handleUrgencyChange);
        }

        // Save progress on input change
        elements.form.addEventListener('input', debounce(saveProgress, 500));

        // Quick quote sidebar
        if (elements.closeQuote) {
            elements.closeQuote.addEventListener('click', () => {
                elements.quickQuote.classList.remove('active');
            });
        }

        // Form submission
        elements.form.addEventListener('submit', (e) => {
            e.preventDefault();
            submitForm();
        });

        // File input change
        if (elements.fileInput) {
            elements.fileInput.addEventListener('change', handleFileSelect);
        }
    }

    // Handle service selection
    function handleServiceChange(e) {
        const selectedService = e.target;
        state.pricing.basePrice = parseFloat(selectedService.dataset.basePrice) || 0;
        state.pricing.hourlyRate = parseFloat(selectedService.dataset.hourlyPrice) || 0;
        state.formData.serviceName = selectedService.dataset.name;

        calculatePrice();
        updateQuickQuote();

        // Track event
        if (typeof trackEvent === 'function') {
            trackEvent('booking_service_selected', {
                service_id: selectedService.value,
                service_name: state.formData.serviceName
            });
        }
    }

    // Handle urgency change
    function handleUrgencyChange(e) {
        const urgency = e.target.value;

        switch (urgency) {
            case 'priority':
                state.pricing.urgencyMultiplier = 1.25; // 25% extra
                break;
            case 'emergency':
                state.pricing.urgencyMultiplier = 1.5; // 50% extra
                break;
            default:
                state.pricing.urgencyMultiplier = 1;
        }

        calculatePrice();
        updateQuickQuote();
    }

    // Calculate total price
    function calculatePrice() {
        const duration = parseFloat(document.getElementById('estimated_duration')?.value) || 0;
        state.pricing.duration = duration;

        // Calculate subtotal
        const baseCharge = state.pricing.basePrice;
        const hourlyCharge = state.pricing.hourlyRate * duration;
        const subtotalBeforeUrgency = baseCharge + hourlyCharge;
        const urgencyCharge = subtotalBeforeUrgency * (state.pricing.urgencyMultiplier - 1);

        state.pricing.subtotal = subtotalBeforeUrgency + urgencyCharge;
        state.pricing.vat = state.pricing.subtotal * 0.05; // 5% VAT
        state.pricing.total = state.pricing.subtotal + state.pricing.vat;

        updatePriceDisplay();
        updateQuickQuote();
    }

    // Update price display in review step
    function updatePriceDisplay() {
        const basePriceEl = document.getElementById('basePrice');
        const hourlyChargeEl = document.getElementById('hourlyCharge');
        const urgencyChargeEl = document.getElementById('urgencyCharge');
        const urgencyChargeItem = document.getElementById('urgencyChargeItem');
        const subtotalEl = document.getElementById('subtotal');
        const vatEl = document.getElementById('vatAmount');
        const totalEl = document.getElementById('totalPrice');

        if (basePriceEl) basePriceEl.textContent = `AED ${formatPrice(state.pricing.basePrice)}`;
        if (hourlyChargeEl) {
            const hourlyTotal = state.pricing.hourlyRate * state.pricing.duration;
            hourlyChargeEl.textContent = `AED ${formatPrice(hourlyTotal)}`;
        }

        // Show urgency charge if applicable
        if (state.pricing.urgencyMultiplier > 1) {
            const urgencyAmount = (state.pricing.basePrice + (state.pricing.hourlyRate * state.pricing.duration)) * (state.pricing.urgencyMultiplier - 1);
            if (urgencyChargeEl) urgencyChargeEl.textContent = `AED ${formatPrice(urgencyAmount)}`;
            if (urgencyChargeItem) urgencyChargeItem.style.display = 'flex';
        } else {
            if (urgencyChargeItem) urgencyChargeItem.style.display = 'none';
        }

        if (subtotalEl) subtotalEl.textContent = `AED ${formatPrice(state.pricing.subtotal)}`;
        if (vatEl) vatEl.textContent = `AED ${formatPrice(state.pricing.vat)}`;
        if (totalEl) totalEl.textContent = `AED ${formatPrice(state.pricing.total)}`;
    }

    // Update quick quote sidebar
    function updateQuickQuote() {
        const quoteService = document.getElementById('quoteService');
        const quoteDuration = document.getElementById('quoteDuration');
        const quoteUrgency = document.getElementById('quoteUrgency');
        const quoteTotal = document.getElementById('quoteTotal');

        if (quoteService && state.formData.serviceName) {
            quoteService.textContent = state.formData.serviceName;
        }

        if (quoteDuration && state.pricing.duration > 0) {
            quoteDuration.textContent = `${state.pricing.duration} hour${state.pricing.duration > 1 ? 's' : ''}`;
        }

        const urgencyValue = document.getElementById('urgency')?.value;
        if (quoteUrgency && urgencyValue) {
            const urgencyLabels = {
                'regular': 'Regular',
                'priority': 'Priority (+25%)',
                'emergency': 'Emergency (+50%)'
            };
            quoteUrgency.textContent = urgencyLabels[urgencyValue] || '-';
        }

        if (quoteTotal && state.pricing.total > 0) {
            quoteTotal.textContent = `AED ${formatPrice(state.pricing.total)}`;
            elements.quickQuote.classList.add('active');
        }
    }

    // Format price
    function formatPrice(price) {
        return price.toFixed(2).replace(/\B(?=(\d{3})+(?!\d))/g, ',');
    }

    // Next step
    function nextStep() {
        if (!validateCurrentStep()) {
            return;
        }

        if (state.currentStep < state.totalSteps) {
            state.currentStep++;
            updateUI();
            saveProgress();

            // Track step progression
            if (typeof trackEvent === 'function') {
                trackEvent('booking_step_completed', { step: state.currentStep - 1 });
            }

            // Generate summary on review step
            if (state.currentStep === 6) {
                generateSummary();
            }
        }
    }

    // Previous step
    function previousStep() {
        if (state.currentStep > 1) {
            state.currentStep--;
            updateUI();
        }
    }

    // Validate current step
    function validateCurrentStep() {
        const currentStepEl = elements.steps[state.currentStep - 1];
        const inputs = currentStepEl.querySelectorAll('input[required], select[required], textarea[required]');
        let isValid = true;

        inputs.forEach(input => {
            clearError(input);

            if (!input.value.trim()) {
                showError(input, getTranslation('field_required'));
                isValid = false;
            } else if (input.type === 'email' && !isValidEmail(input.value)) {
                showError(input, getTranslation('invalid_email'));
                isValid = false;
            } else if (input.type === 'tel' && !isValidPhone(input.value)) {
                showError(input, getTranslation('invalid_phone'));
                isValid = false;
            } else if (input.type === 'date') {
                const selectedDate = new Date(input.value);
                const today = new Date();
                today.setHours(0, 0, 0, 0);

                if (selectedDate < today) {
                    showError(input, getTranslation('invalid_date_past'));
                    isValid = false;
                }
            }
        });

        // Special validation for step 1 (service selection)
        if (state.currentStep === 1) {
            const serviceSelected = document.querySelector('input[name="service_id"]:checked');
            if (!serviceSelected) {
                showNotification(getTranslation('please_select_service'), 'error');
                isValid = false;
            }
        }

        // Special validation for step 6 (terms acceptance)
        if (state.currentStep === 6) {
            const termsCheckbox = document.getElementById('terms_accepted');
            if (termsCheckbox && !termsCheckbox.checked) {
                showError(termsCheckbox, getTranslation('must_accept_terms'));
                isValid = false;
            }
        }

        return isValid;
    }

    // Show error
    function showError(input, message) {
        const formGroup = input.closest('.form-group') || input.closest('.checkbox-label');
        if (formGroup) {
            const errorEl = formGroup.querySelector('.form-error');
            if (errorEl) {
                errorEl.textContent = message;
                errorEl.style.display = 'block';
            }
            input.classList.add('error');
        }
    }

    // Clear error
    function clearError(input) {
        const formGroup = input.closest('.form-group') || input.closest('.checkbox-label');
        if (formGroup) {
            const errorEl = formGroup.querySelector('.form-error');
            if (errorEl) {
                errorEl.textContent = '';
                errorEl.style.display = 'none';
            }
            input.classList.remove('error');
        }
    }

    // Validate email
    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email);
    }

    // Validate phone
    function isValidPhone(phone) {
        const re = /^[\+]?[0-9]{10,15}$/;
        return re.test(phone.replace(/[\s\-]/g, ''));
    }

    // Update UI
    function updateUI() {
        // Update steps visibility
        elements.steps.forEach((step, index) => {
            if (index + 1 === state.currentStep) {
                step.classList.add('active');
                step.style.display = 'block';
            } else {
                step.classList.remove('active');
                step.style.display = 'none';
            }
        });

        // Update progress steps
        elements.progressSteps.forEach((step, index) => {
            if (index + 1 < state.currentStep) {
                step.classList.add('completed');
                step.classList.remove('active');
            } else if (index + 1 === state.currentStep) {
                step.classList.add('active');
                step.classList.remove('completed');
            } else {
                step.classList.remove('active', 'completed');
            }
        });

        // Update progress bar
        const progress = ((state.currentStep - 1) / (state.totalSteps - 1)) * 100;
        if (elements.progressFill) {
            elements.progressFill.style.width = `${progress}%`;
        }

        // Update navigation buttons
        updateNavigationButtons();

        // Scroll to top of form
        const bookingSection = document.getElementById('booking');
        if (bookingSection) {
            const headerOffset = 100;
            const elementPosition = bookingSection.getBoundingClientRect().top;
            const offsetPosition = elementPosition + window.pageYOffset - headerOffset;

            window.scrollTo({
                top: offsetPosition,
                behavior: 'smooth'
            });
        }
    }

    // Update navigation buttons
    function updateNavigationButtons() {
        // Previous button
        if (elements.prevBtn) {
            if (state.currentStep === 1) {
                elements.prevBtn.style.display = 'none';
            } else {
                elements.prevBtn.style.display = 'inline-flex';
            }
        }

        // Next button
        if (elements.nextBtn) {
            if (state.currentStep === state.totalSteps) {
                elements.nextBtn.style.display = 'none';
            } else {
                elements.nextBtn.style.display = 'inline-flex';
            }
        }

        // Submit button
        if (elements.submitBtn) {
            if (state.currentStep === state.totalSteps) {
                elements.submitBtn.style.display = 'inline-flex';
            } else {
                elements.submitBtn.style.display = 'none';
            }
        }
    }

    // Generate summary
    function generateSummary() {
        if (!elements.bookingSummary) return;

        const formData = new FormData(elements.form);
        let summaryHTML = '';

        // Service Details
        const serviceId = formData.get('service_id');
        const service = state.servicesData.find(s => s.id == serviceId);
        const urgency = formData.get('urgency');

        summaryHTML += `
            <div class="summary-section">
                <h4><i class="fa-solid fa-wrench"></i> ${getTranslation('service_details')}</h4>
                <div class="summary-item">
                    <span>${getTranslation('service')}:</span>
                    <strong>${state.formData.serviceName || '-'}</strong>
                </div>
                <div class="summary-item">
                    <span>${getTranslation('urgency')}:</span>
                    <strong>${capitalizeFirst(urgency || '-')}</strong>
                </div>
            </div>
        `;

        // Date & Time
        const bookingDate = formData.get('booking_date');
        const bookingTime = formData.get('booking_time');
        const duration = formData.get('estimated_duration');

        summaryHTML += `
            <div class="summary-section">
                <h4><i class="fa-solid fa-calendar"></i> ${getTranslation('date_time')}</h4>
                <div class="summary-item">
                    <span>${getTranslation('date')}:</span>
                    <strong>${formatDate(bookingDate)}</strong>
                </div>
                <div class="summary-item">
                    <span>${getTranslation('time')}:</span>
                    <strong>${bookingTime || '-'}</strong>
                </div>
                <div class="summary-item">
                    <span>${getTranslation('duration')}:</span>
                    <strong>${duration} ${getTranslation('hours')}</strong>
                </div>
            </div>
        `;

        // Location
        const emirate = formData.get('emirate');
        const area = formData.get('area');
        const propertyType = formData.get('property_type');
        const address = formData.get('address');

        summaryHTML += `
            <div class="summary-section">
                <h4><i class="fa-solid fa-map-marker-alt"></i> ${getTranslation('location')}</h4>
                <div class="summary-item">
                    <span>${getTranslation('emirate')}:</span>
                    <strong>${capitalizeFirst(emirate || '-')}</strong>
                </div>
                <div class="summary-item">
                    <span>${getTranslation('area')}:</span>
                    <strong>${area || '-'}</strong>
                </div>
                <div class="summary-item">
                    <span>${getTranslation('property_type')}:</span>
                    <strong>${capitalizeFirst(propertyType || '-')}</strong>
                </div>
                <div class="summary-item">
                    <span>${getTranslation('address')}:</span>
                    <strong>${address || '-'}</strong>
                </div>
            </div>
        `;

        // Contact Information
        const clientName = formData.get('client_name');
        const clientEmail = formData.get('client_email');
        const clientPhone = formData.get('client_phone');
        const contactMethod = formData.get('contact_method');

        summaryHTML += `
            <div class="summary-section">
                <h4><i class="fa-solid fa-user"></i> ${getTranslation('contact_information')}</h4>
                <div class="summary-item">
                    <span>${getTranslation('name')}:</span>
                    <strong>${clientName || '-'}</strong>
                </div>
                <div class="summary-item">
                    <span>${getTranslation('email')}:</span>
                    <strong>${clientEmail || '-'}</strong>
                </div>
                <div class="summary-item">
                    <span>${getTranslation('phone')}:</span>
                    <strong>${clientPhone || '-'}</strong>
                </div>
                <div class="summary-item">
                    <span>${getTranslation('preferred_contact')}:</span>
                    <strong>${capitalizeFirst(contactMethod || '-')}</strong>
                </div>
            </div>
        `;

        // Issue Description
        const issueDescription = formData.get('issue_description');
        if (issueDescription) {
            summaryHTML += `
                <div class="summary-section">
                    <h4><i class="fa-solid fa-clipboard-list"></i> ${getTranslation('issue_description')}</h4>
                    <p class="summary-description">${escapeHtml(issueDescription)}</p>
                </div>
            `;
        }

        // Photos
        if (state.uploadedFiles.length > 0) {
            summaryHTML += `
                <div class="summary-section">
                    <h4><i class="fa-solid fa-images"></i> ${getTranslation('uploaded_photos')}</h4>
                    <div class="summary-photos">
                        ${state.uploadedFiles.map(file => `
                            <div class="summary-photo">
                                <img src="${file.preview}" alt="${file.name}">
                            </div>
                        `).join('')}
                    </div>
                </div>
            `;
        }

        elements.bookingSummary.innerHTML = summaryHTML;
        calculatePrice(); // Ensure price is calculated
    }

    // Submit form
    async function submitForm() {
        if (!validateCurrentStep()) {
            return;
        }

        const formData = new FormData(elements.form);

        // Add uploaded files
        state.uploadedFiles.forEach((file, index) => {
            formData.append(`photos[${index}]`, file.file);
        });

        // Add pricing information
        formData.append('total_price', state.pricing.total);
        formData.append('vat_amount', state.pricing.vat);
        formData.append('subtotal', state.pricing.subtotal);

        // Show loading state
        elements.submitBtn.disabled = true;
        elements.submitBtn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> <span>' + getTranslation('submitting') + '...</span>';

        try {
            const response = await fetch(siteUrl('handlers/booking-handler.php'), {
                method: 'POST',
                body: formData
            });

            const result = await response.json();

            if (result.success) {
                // Show success message
                showSuccessMessage(result.booking_reference);

                // Clear saved progress
                clearSavedProgress();

                // Track conversion
                if (typeof trackEvent === 'function') {
                    trackEvent('booking_submitted', {
                        booking_reference: result.booking_reference,
                        service_id: formData.get('service_id'),
                        total_price: state.pricing.total
                    });
                }
            } else {
                throw new Error(result.message || 'Booking submission failed');
            }
        } catch (error) {
            console.error('Booking submission error:', error);
            showNotification(error.message || getTranslation('booking_error'), 'error');

            // Reset button
            elements.submitBtn.disabled = false;
            elements.submitBtn.innerHTML = '<i class="fa-solid fa-paper-plane"></i> <span>' + getTranslation('submit_booking') + '</span>';
        }
    }

    // Show success message
    function showSuccessMessage(bookingReference) {
        // Hide form
        elements.form.style.display = 'none';

        // Show success message
        elements.bookingSuccess.style.display = 'block';

        // Set booking reference
        const referenceEl = document.getElementById('bookingReference');
        if (referenceEl) {
            referenceEl.textContent = bookingReference;
        }

        // Scroll to success message
        elements.bookingSuccess.scrollIntoView({ behavior: 'smooth', block: 'center' });
    }

    // Reset form for new booking
    function resetForm() {
        // Reset state
        state.currentStep = 1;
        state.formData = {};
        state.pricing = {
            basePrice: 0,
            hourlyRate: 0,
            duration: 0,
            urgencyMultiplier: 1,
            subtotal: 0,
            vat: 0,
            total: 0
        };
        state.uploadedFiles = [];

        // Reset form
        elements.form.reset();

        // Clear file preview
        if (elements.filePreview) {
            elements.filePreview.innerHTML = '';
        }

        // Hide success, show form
        elements.bookingSuccess.style.display = 'none';
        elements.form.style.display = 'block';

        // Update UI
        updateUI();
        clearSavedProgress();

        // Scroll to top
        const bookingSection = document.getElementById('booking');
        if (bookingSection) {
            bookingSection.scrollIntoView({ behavior: 'smooth' });
        }
    }

    // File upload setup
    function setupFileUpload() {
        if (!elements.fileUploadArea) return;

        // Drag and drop
        elements.fileUploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            elements.fileUploadArea.classList.add('drag-over');
        });

        elements.fileUploadArea.addEventListener('dragleave', () => {
            elements.fileUploadArea.classList.remove('drag-over');
        });

        elements.fileUploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            elements.fileUploadArea.classList.remove('drag-over');

            const files = e.dataTransfer.files;
            handleFiles(files);
        });
    }

    // Handle file selection
    function handleFileSelect(e) {
        const files = e.target.files;
        handleFiles(files);
    }

    // Handle files
    function handleFiles(files) {
        const maxFiles = 5;
        const maxSize = 5 * 1024 * 1024; // 5MB
        const allowedTypes = ['image/jpeg', 'image/png', 'image/webp'];

        Array.from(files).forEach(file => {
            // Check file count
            if (state.uploadedFiles.length >= maxFiles) {
                showNotification(getTranslation('max_files_exceeded'), 'warning');
                return;
            }

            // Check file size
            if (file.size > maxSize) {
                showNotification(`${file.name}: ${getTranslation('file_too_large')}`, 'warning');
                return;
            }

            // Check file type
            if (!allowedTypes.includes(file.type)) {
                showNotification(`${file.name}: ${getTranslation('invalid_file_type')}`, 'warning');
                return;
            }

            // Create preview
            const reader = new FileReader();
            reader.onload = (e) => {
                const fileData = {
                    file: file,
                    name: file.name,
                    preview: e.target.result
                };
                state.uploadedFiles.push(fileData);
                addFilePreview(fileData);
            };
            reader.readAsDataURL(file);
        });
    }

    // Add file preview
    function addFilePreview(fileData) {
        if (!elements.filePreview) return;

        const previewItem = document.createElement('div');
        previewItem.className = 'file-preview-item';
        previewItem.innerHTML = `
            <img src="${fileData.preview}" alt="${fileData.name}">
            <div class="file-info">
                <span class="file-name">${fileData.name}</span>
            </div>
            <button type="button" class="remove-file" data-name="${fileData.name}">
                <i class="fa-solid fa-times"></i>
            </button>
        `;

        const removeBtn = previewItem.querySelector('.remove-file');
        removeBtn.addEventListener('click', () => {
            removeFile(fileData.name);
            previewItem.remove();
        });

        elements.filePreview.appendChild(previewItem);
    }

    // Remove file
    function removeFile(fileName) {
        state.uploadedFiles = state.uploadedFiles.filter(f => f.name !== fileName);
    }

    // Save progress to session storage
    function saveProgress() {
        const formData = new FormData(elements.form);
        const data = {};

        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }

        data.currentStep = state.currentStep;
        data.pricing = state.pricing;

        sessionStorage.setItem('bookingProgress', JSON.stringify(data));
    }

    // Load saved progress
    function loadSavedProgress() {
        const saved = sessionStorage.getItem('bookingProgress');
        if (!saved) return;

        try {
            const data = JSON.parse(saved);

            // Restore form values
            Object.keys(data).forEach(key => {
                if (key === 'currentStep' || key === 'pricing') return;

                const input = elements.form.elements[key];
                if (input) {
                    if (input.type === 'radio') {
                        const radioInput = elements.form.querySelector(`input[name="${key}"][value="${data[key]}"]`);
                        if (radioInput) radioInput.checked = true;
                    } else if (input.type === 'checkbox') {
                        input.checked = data[key] === 'on';
                    } else {
                        input.value = data[key];
                    }
                }
            });

            // Restore step
            if (data.currentStep) {
                state.currentStep = data.currentStep;
            }

            // Restore pricing
            if (data.pricing) {
                state.pricing = data.pricing;
            }

            updateUI();
        } catch (error) {
            console.error('Error loading saved progress:', error);
        }
    }

    // Clear saved progress
    function clearSavedProgress() {
        sessionStorage.removeItem('bookingProgress');
    }

    // Utility functions
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function capitalizeFirst(str) {
        if (!str) return '';
        return str.charAt(0).toUpperCase() + str.slice(1).replace(/_/g, ' ');
    }

    function formatDate(dateStr) {
        if (!dateStr) return '-';
        const date = new Date(dateStr);
        return date.toLocaleDateString('en-GB', {
            day: '2-digit',
            month: 'short',
            year: 'numeric'
        });
    }

    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    function getTranslation(key) {
        // This would ideally use the translation system
        // For now, return the key as fallback
        return key.replace(/_/g, ' ').replace(/\b\w/g, l => l.toUpperCase());
    }

    function showNotification(message, type = 'info') {
        // Use existing notification system if available
        if (typeof window.showNotification === 'function') {
            window.showNotification(message, type);
        } else {
            alert(message);
        }
    }

    function siteUrl(path) {
        // Get site URL from config or use relative path
        return window.SITE_URL ? window.SITE_URL + '/' + path : path;
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Export to global scope
    window.BookingModule = {
        nextStep,
        previousStep,
        resetForm,
        calculatePrice
    };
})();
