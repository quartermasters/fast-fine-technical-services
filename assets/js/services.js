/**
 * Fast and Fine Technical Services FZE - Services Interactive Script
 *
 * Features:
 * - Category filtering
 * - Search functionality
 * - 3D flip card interactions
 * - Service detail modal
 * - Responsive behavior
 *
 * @package FastAndFine
 * @version 1.0.0
 */

(function() {
    'use strict';

    // State
    const state = {
        currentCategory: 'all',
        searchQuery: '',
        servicesData: [],
        currentLanguage: document.documentElement.lang || 'en'
    };

    // DOM Elements
    const elements = {
        servicesGrid: document.getElementById('servicesGrid'),
        filterBtns: document.querySelectorAll('.filter-btn'),
        searchInput: document.getElementById('serviceSearchInput'),
        searchClearBtn: document.getElementById('searchClearBtn'),
        noResults: document.getElementById('noResults'),
        resetSearchBtn: document.getElementById('resetSearchBtn'),
        serviceModal: document.getElementById('serviceModal'),
        serviceModalOverlay: document.getElementById('serviceModalOverlay'),
        serviceModalClose: document.getElementById('serviceModalClose'),
        serviceModalBody: document.getElementById('serviceModalBody'),
        serviceModalTitle: document.getElementById('serviceModalTitle'),
        viewDetailsBtns: document.querySelectorAll('.view-details-btn')
    };

    // Initialize
    function init() {
        loadServicesData();
        attachEventListeners();
        initFlipCards();
    }

    // Load services data from JSON script tag
    function loadServicesData() {
        const servicesDataElement = document.getElementById('servicesData');
        if (servicesDataElement) {
            try {
                state.servicesData = JSON.parse(servicesDataElement.textContent);
            } catch (error) {
                console.error('Error parsing services data:', error);
            }
        }
    }

    // Attach event listeners
    function attachEventListeners() {
        // Category filter buttons
        elements.filterBtns.forEach(btn => {
            btn.addEventListener('click', handleCategoryFilter);
        });

        // Search input
        if (elements.searchInput) {
            elements.searchInput.addEventListener('input', debounce(handleSearch, 300));
            elements.searchInput.addEventListener('keyup', function(e) {
                if (e.key === 'Escape') {
                    clearSearch();
                }
            });
        }

        // Search clear button
        if (elements.searchClearBtn) {
            elements.searchClearBtn.addEventListener('click', clearSearch);
        }

        // Reset search button
        if (elements.resetSearchBtn) {
            elements.resetSearchBtn.addEventListener('click', resetFilters);
        }

        // View details buttons
        elements.viewDetailsBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const serviceId = this.getAttribute('data-service-id');
                openServiceModal(serviceId);
            });
        });

        // Re-attach after dynamic updates
        attachViewDetailsListeners();

        // Modal close
        if (elements.serviceModalClose) {
            elements.serviceModalClose.addEventListener('click', closeServiceModal);
        }

        if (elements.serviceModalOverlay) {
            elements.serviceModalOverlay.addEventListener('click', closeServiceModal);
        }

        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && elements.serviceModal && elements.serviceModal.classList.contains('active')) {
                closeServiceModal();
            }
        });
    }

    // Attach view details listeners (for dynamically loaded cards)
    function attachViewDetailsListeners() {
        const viewDetailsBtns = document.querySelectorAll('.view-details-btn');
        viewDetailsBtns.forEach(btn => {
            btn.removeEventListener('click', handleViewDetails); // Remove duplicate
            btn.addEventListener('click', handleViewDetails);
        });
    }

    function handleViewDetails(e) {
        e.preventDefault();
        e.stopPropagation();
        const serviceId = this.getAttribute('data-service-id');
        openServiceModal(serviceId);
    }

    // Initialize flip cards
    function initFlipCards() {
        const flipCards = document.querySelectorAll('.service-flip-card');

        flipCards.forEach(card => {
            // Add touch support for mobile
            card.addEventListener('touchstart', function(e) {
                if (!e.target.closest('.view-details-btn') && !e.target.closest('.btn')) {
                    this.classList.toggle('flipped');
                }
            });

            // Keyboard accessibility
            card.setAttribute('tabindex', '0');
            card.addEventListener('keydown', function(e) {
                if (e.key === 'Enter' || e.key === ' ') {
                    e.preventDefault();
                    this.classList.toggle('flipped');
                }
            });
        });
    }

    // Handle category filter
    function handleCategoryFilter(e) {
        const category = this.getAttribute('data-category');
        state.currentCategory = category;

        // Update active state
        elements.filterBtns.forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');

        // Filter services
        filterServices();

        // Track event
        if (typeof trackEvent === 'function') {
            trackEvent('service_filter', { category: category });
        }
    }

    // Handle search
    function handleSearch(e) {
        state.searchQuery = e.target.value.toLowerCase().trim();

        // Show/hide clear button
        if (elements.searchClearBtn) {
            elements.searchClearBtn.style.display = state.searchQuery ? 'flex' : 'none';
        }

        // Filter services
        filterServices();

        // Track event
        if (typeof trackEvent === 'function' && state.searchQuery) {
            trackEvent('service_search', { query: state.searchQuery });
        }
    }

    // Clear search
    function clearSearch() {
        if (elements.searchInput) {
            elements.searchInput.value = '';
            state.searchQuery = '';
        }

        if (elements.searchClearBtn) {
            elements.searchClearBtn.style.display = 'none';
        }

        filterServices();
    }

    // Reset all filters
    function resetFilters() {
        state.currentCategory = 'all';
        state.searchQuery = '';

        // Reset filter buttons
        elements.filterBtns.forEach(btn => btn.classList.remove('active'));
        const allBtn = document.querySelector('.filter-btn[data-category="all"]');
        if (allBtn) {
            allBtn.classList.add('active');
        }

        // Reset search
        if (elements.searchInput) {
            elements.searchInput.value = '';
        }

        if (elements.searchClearBtn) {
            elements.searchClearBtn.style.display = 'none';
        }

        // Filter services
        filterServices();
    }

    // Filter services
    function filterServices() {
        const cards = document.querySelectorAll('.service-flip-card');
        let visibleCount = 0;

        cards.forEach(card => {
            const category = card.getAttribute('data-category');
            const serviceName = card.getAttribute('data-service-name').toLowerCase();

            // Check category filter
            const categoryMatch = state.currentCategory === 'all' || category === state.currentCategory;

            // Check search filter
            const searchMatch = !state.searchQuery || serviceName.includes(state.searchQuery);

            // Show/hide card
            if (categoryMatch && searchMatch) {
                card.style.display = 'block';
                // Remove flipped state when filtering
                card.classList.remove('flipped');
                // Add fade-in animation
                setTimeout(() => {
                    card.style.opacity = '1';
                    card.style.transform = 'translateY(0)';
                }, visibleCount * 50);
                visibleCount++;
            } else {
                card.style.display = 'none';
                card.style.opacity = '0';
                card.style.transform = 'translateY(20px)';
            }
        });

        // Show/hide no results message
        if (elements.noResults) {
            if (visibleCount === 0) {
                elements.noResults.style.display = 'flex';
                elements.servicesGrid.style.display = 'none';
            } else {
                elements.noResults.style.display = 'none';
                elements.servicesGrid.style.display = 'grid';
            }
        }
    }

    // Open service modal
    function openServiceModal(serviceId) {
        const service = state.servicesData.find(s => s.id == serviceId);

        if (!service) {
            console.error('Service not found:', serviceId);
            return;
        }

        // Get language-specific content
        const name = state.currentLanguage === 'ar' ? service.name_ar : service.name_en;
        const longDesc = state.currentLanguage === 'ar' ? service.long_desc_ar : service.long_desc_en;
        const features = state.currentLanguage === 'ar' ? service.features_ar : service.features_en;

        // Build modal content
        const modalContent = `
            <div class="service-detail">
                <div class="service-detail-header">
                    <div class="service-icon-modal">
                        <i class="${escapeHTML(service.icon_class)}"></i>
                    </div>
                    <div class="service-detail-title">
                        <h2>${escapeHTML(name)}</h2>
                        <span class="category-badge badge-${escapeHTML(service.category)}">
                            ${escapeHTML(service.category)}
                        </span>
                    </div>
                </div>

                <div class="service-detail-body">
                    <div class="service-description">
                        <h4><i class="fa-solid fa-info-circle"></i> ${getTranslation('description')}</h4>
                        <p>${escapeHTML(longDesc)}</p>
                    </div>

                    ${features && features.length > 0 ? `
                        <div class="service-features-full">
                            <h4><i class="fa-solid fa-list-check"></i> ${getTranslation('features_included')}</h4>
                            <ul class="features-grid">
                                ${features.map(feature => `
                                    <li>
                                        <i class="fa-solid fa-check-circle"></i>
                                        <span>${escapeHTML(feature)}</span>
                                    </li>
                                `).join('')}
                            </ul>
                        </div>
                    ` : ''}

                    <div class="service-pricing">
                        <h4><i class="fa-solid fa-dollar-sign"></i> ${getTranslation('pricing')}</h4>
                        <div class="price-info">
                            <span class="price-label">${getTranslation('starting_from')}:</span>
                            <span class="price-value">${formatPrice(service.starting_price)}</span>
                            <p class="price-note">${getTranslation('price_varies_note')}</p>
                        </div>
                    </div>

                    <div class="service-cta-box">
                        <i class="fa-solid fa-headset"></i>
                        <div class="cta-box-content">
                            <h4>${getTranslation('ready_to_book')}</h4>
                            <p>${getTranslation('ready_to_book_desc')}</p>
                        </div>
                    </div>
                </div>
            </div>
        `;

        // Update modal
        if (elements.serviceModalTitle) {
            elements.serviceModalTitle.textContent = name;
        }

        if (elements.serviceModalBody) {
            elements.serviceModalBody.innerHTML = modalContent;
        }

        // Update modal footer buttons with service ID
        const modalBookBtn = document.getElementById('modalBookNowBtn');
        const modalQuoteBtn = document.getElementById('modalGetQuoteBtn');

        if (modalBookBtn) {
            modalBookBtn.href = `#booking?service=${serviceId}`;
        }

        if (modalQuoteBtn) {
            modalQuoteBtn.href = `#quote?service=${serviceId}`;
        }

        // Show modal
        if (elements.serviceModal) {
            elements.serviceModal.classList.add('active');
            document.body.style.overflow = 'hidden';

            // Track event
            if (typeof trackEvent === 'function') {
                trackEvent('service_detail_view', {
                    service_id: serviceId,
                    service_name: name
                });
            }
        }
    }

    // Close service modal
    function closeServiceModal() {
        if (elements.serviceModal) {
            elements.serviceModal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    // Utility: Format price
    function formatPrice(price) {
        return new Intl.NumberFormat('en-AE', {
            style: 'currency',
            currency: 'AED',
            minimumFractionDigits: 0
        }).format(price);
    }

    // Utility: Escape HTML
    function escapeHTML(str) {
        const div = document.createElement('div');
        div.textContent = str || '';
        return div.innerHTML;
    }

    // Utility: Get translation
    function getTranslation(key) {
        // This would integrate with your translation system
        const translations = {
            'description': state.currentLanguage === 'ar' ? 'الوصف' : 'Description',
            'features_included': state.currentLanguage === 'ar' ? 'المميزات المتضمنة' : 'Features Included',
            'pricing': state.currentLanguage === 'ar' ? 'التسعير' : 'Pricing',
            'starting_from': state.currentLanguage === 'ar' ? 'تبدأ من' : 'Starting from',
            'price_varies_note': state.currentLanguage === 'ar' ? 'قد تختلف الأسعار حسب حجم المشروع ومتطلباته' : 'Prices may vary based on project size and requirements',
            'ready_to_book': state.currentLanguage === 'ar' ? 'هل أنت مستعد للحجز؟' : 'Ready to Book?',
            'ready_to_book_desc': state.currentLanguage === 'ar' ? 'فريقنا متاح على مدار الساعة لمساعدتك' : 'Our team is available 24/7 to assist you'
        };

        return translations[key] || key;
    }

    // Utility: Debounce
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func.apply(this, args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Export to global scope
    window.ServicesModule = {
        openServiceModal,
        closeServiceModal,
        filterServices,
        resetFilters
    };
})();
