/**
 * Fast and Fine Technical Services FZE - Portfolio Interactive Script
 *
 * Features:
 * - Category filtering
 * - Masonry grid layout with dynamic repositioning
 * - Lightbox image gallery with keyboard navigation
 * - Before/After comparison slider
 * - Project detail modal
 * - Load more functionality
 * - View toggle (grid/masonry)
 *
 * @package FastAndFine
 * @version 1.0.0
 */

(function() {
    'use strict';

    // State
    const state = {
        currentCategory: 'all',
        currentView: 'masonry',
        visibleItems: 9,
        itemsPerPage: 6,
        portfolioData: [],
        currentLanguage: document.documentElement.lang || 'en',
        lightboxImages: [],
        lightboxCurrentIndex: 0,
        beforeAfterDragging: false
    };

    // DOM Elements
    const elements = {
        portfolioGrid: document.getElementById('portfolioGrid'),
        filterBtns: document.querySelectorAll('.portfolio-filter .filter-btn'),
        viewBtns: document.querySelectorAll('.view-toggle .view-btn'),
        noResults: document.getElementById('noPortfolioResults'),
        resetBtn: document.getElementById('resetPortfolioBtn'),
        loadMoreBtn: document.getElementById('loadMoreBtn'),
        loadMoreContainer: document.getElementById('loadMoreContainer'),

        // Project Modal
        projectModal: document.getElementById('projectModal'),
        projectModalOverlay: document.getElementById('projectModalOverlay'),
        projectModalClose: document.getElementById('projectModalClose'),
        projectModalBody: document.getElementById('projectModalBody'),
        projectModalTitle: document.getElementById('projectModalTitle'),

        // Lightbox
        lightbox: document.getElementById('galleryLightbox'),
        lightboxOverlay: document.getElementById('lightboxOverlay'),
        lightboxClose: document.getElementById('lightboxClose'),
        lightboxImage: document.getElementById('lightboxImage'),
        lightboxContent: document.getElementById('lightboxContent'),
        lightboxCaption: document.getElementById('lightboxCaption'),
        lightboxPrev: document.getElementById('lightboxPrev'),
        lightboxNext: document.getElementById('lightboxNext'),
        lightboxCurrentIndex: document.getElementById('lightboxCurrentIndex'),
        lightboxTotalCount: document.getElementById('lightboxTotalCount'),

        // Before/After
        beforeAfterModal: document.getElementById('beforeAfterModal'),
        beforeAfterOverlay: document.getElementById('beforeAfterOverlay'),
        beforeAfterClose: document.getElementById('beforeAfterClose'),
        beforeAfterSlider: document.getElementById('beforeAfterSlider'),
        beforeImage: document.getElementById('beforeImage'),
        afterImage: document.getElementById('afterImage'),
        sliderHandle: document.getElementById('sliderHandle')
    };

    // Initialize
    function init() {
        loadPortfolioData();
        attachEventListeners();
        initMasonryLayout();
        updateVisibility();
    }

    // Load portfolio data from JSON script tag
    function loadPortfolioData() {
        const portfolioDataElement = document.getElementById('portfolioData');
        if (portfolioDataElement) {
            try {
                state.portfolioData = JSON.parse(portfolioDataElement.textContent);
            } catch (error) {
                console.error('Error parsing portfolio data:', error);
            }
        }
    }

    // Attach event listeners
    function attachEventListeners() {
        // Category filter buttons
        elements.filterBtns.forEach(btn => {
            btn.addEventListener('click', handleCategoryFilter);
        });

        // View toggle buttons
        elements.viewBtns.forEach(btn => {
            btn.addEventListener('click', handleViewToggle);
        });

        // Reset button
        if (elements.resetBtn) {
            elements.resetBtn.addEventListener('click', resetFilters);
        }

        // Load more button
        if (elements.loadMoreBtn) {
            elements.loadMoreBtn.addEventListener('click', loadMoreProjects);
        }

        // Attach view buttons
        attachViewButtons();

        // Project modal
        if (elements.projectModalClose) {
            elements.projectModalClose.addEventListener('click', closeProjectModal);
        }
        if (elements.projectModalOverlay) {
            elements.projectModalOverlay.addEventListener('click', closeProjectModal);
        }

        // Lightbox
        if (elements.lightboxClose) {
            elements.lightboxClose.addEventListener('click', closeLightbox);
        }
        if (elements.lightboxOverlay) {
            elements.lightboxOverlay.addEventListener('click', closeLightbox);
        }
        if (elements.lightboxPrev) {
            elements.lightboxPrev.addEventListener('click', () => navigateLightbox(-1));
        }
        if (elements.lightboxNext) {
            elements.lightboxNext.addEventListener('click', () => navigateLightbox(1));
        }

        // Before/After modal
        if (elements.beforeAfterClose) {
            elements.beforeAfterClose.addEventListener('click', closeBeforeAfterModal);
        }
        if (elements.beforeAfterOverlay) {
            elements.beforeAfterOverlay.addEventListener('click', closeBeforeAfterModal);
        }
        if (elements.sliderHandle) {
            initBeforeAfterSlider();
        }

        // Keyboard navigation
        document.addEventListener('keydown', handleKeyboardNavigation);

        // Window resize for masonry
        let resizeTimer;
        window.addEventListener('resize', () => {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                if (state.currentView === 'masonry') {
                    initMasonryLayout();
                }
            }, 250);
        });
    }

    // Attach view buttons dynamically
    function attachViewButtons() {
        // View details buttons
        const detailsBtns = document.querySelectorAll('.view-details-btn');
        detailsBtns.forEach(btn => {
            btn.removeEventListener('click', handleViewDetails);
            btn.addEventListener('click', handleViewDetails);
        });

        // View gallery buttons
        const galleryBtns = document.querySelectorAll('.view-gallery-btn');
        galleryBtns.forEach(btn => {
            btn.removeEventListener('click', handleViewGallery);
            btn.addEventListener('click', handleViewGallery);
        });

        // Before/After buttons
        const beforeAfterBtns = document.querySelectorAll('.view-before-after-btn');
        beforeAfterBtns.forEach(btn => {
            btn.removeEventListener('click', handleViewBeforeAfter);
            btn.addEventListener('click', handleViewBeforeAfter);
        });
    }

    // Handle category filter
    function handleCategoryFilter(e) {
        const category = this.getAttribute('data-category');
        state.currentCategory = category;
        state.visibleItems = 9; // Reset pagination

        // Update active state
        elements.filterBtns.forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');

        // Filter projects
        filterProjects();

        // Track event
        if (typeof trackEvent === 'function') {
            trackEvent('portfolio_filter', { category: category });
        }
    }

    // Handle view toggle
    function handleViewToggle(e) {
        const view = this.getAttribute('data-view');
        state.currentView = view;

        // Update active state
        elements.viewBtns.forEach(btn => btn.classList.remove('active'));
        this.classList.add('active');

        // Update grid class
        if (elements.portfolioGrid) {
            if (view === 'masonry') {
                elements.portfolioGrid.classList.add('masonry-grid');
                elements.portfolioGrid.classList.remove('standard-grid');
                setTimeout(() => initMasonryLayout(), 100);
            } else {
                elements.portfolioGrid.classList.remove('masonry-grid');
                elements.portfolioGrid.classList.add('standard-grid');
            }
        }
    }

    // Filter projects
    function filterProjects() {
        const items = document.querySelectorAll('.portfolio-item');
        let visibleCount = 0;
        let shownCount = 0;

        items.forEach((item, index) => {
            const category = item.getAttribute('data-category');
            const categoryMatch = state.currentCategory === 'all' || category === state.currentCategory;

            if (categoryMatch) {
                visibleCount++;
                if (visibleCount <= state.visibleItems) {
                    item.style.display = 'block';
                    setTimeout(() => {
                        item.style.opacity = '1';
                        item.style.transform = 'translateY(0)';
                    }, shownCount * 50);
                    shownCount++;
                } else {
                    item.style.display = 'none';
                }
            } else {
                item.style.display = 'none';
                item.style.opacity = '0';
                item.style.transform = 'translateY(20px)';
            }
        });

        // Show/hide no results message
        if (elements.noResults) {
            if (visibleCount === 0) {
                elements.noResults.style.display = 'flex';
                if (elements.portfolioGrid) {
                    elements.portfolioGrid.style.display = 'none';
                }
                if (elements.loadMoreContainer) {
                    elements.loadMoreContainer.style.display = 'none';
                }
            } else {
                elements.noResults.style.display = 'none';
                if (elements.portfolioGrid) {
                    elements.portfolioGrid.style.display = 'grid';
                }
            }
        }

        // Update load more button
        updateLoadMoreButton(visibleCount);

        // Re-initialize masonry if needed
        if (state.currentView === 'masonry') {
            setTimeout(() => initMasonryLayout(), 100);
        }

        // Re-attach event listeners
        attachViewButtons();
    }

    // Update load more button visibility
    function updateLoadMoreButton(totalVisible) {
        if (elements.loadMoreContainer) {
            if (totalVisible > state.visibleItems) {
                elements.loadMoreContainer.style.display = 'flex';
            } else {
                elements.loadMoreContainer.style.display = 'none';
            }
        }
    }

    // Load more projects
    function loadMoreProjects() {
        state.visibleItems += state.itemsPerPage;
        filterProjects();

        // Track event
        if (typeof trackEvent === 'function') {
            trackEvent('portfolio_load_more', { items: state.visibleItems });
        }
    }

    // Reset filters
    function resetFilters() {
        state.currentCategory = 'all';
        state.visibleItems = 9;

        // Reset filter buttons
        elements.filterBtns.forEach(btn => btn.classList.remove('active'));
        const allBtn = document.querySelector('.portfolio-filter .filter-btn[data-category="all"]');
        if (allBtn) {
            allBtn.classList.add('active');
        }

        // Filter projects
        filterProjects();
    }

    // Update visibility based on pagination
    function updateVisibility() {
        filterProjects();
    }

    // Initialize masonry layout
    function initMasonryLayout() {
        if (!elements.portfolioGrid || !elements.portfolioGrid.classList.contains('masonry-grid')) {
            return;
        }

        const items = Array.from(elements.portfolioGrid.querySelectorAll('.portfolio-item:not([style*="display: none"])'));
        const gap = 30; // Gap between items
        const columns = getColumnCount();
        const columnHeights = Array(columns).fill(0);
        const gridWidth = elements.portfolioGrid.offsetWidth;
        const itemWidth = (gridWidth - (gap * (columns - 1))) / columns;

        items.forEach(item => {
            // Find shortest column
            const shortestColumn = columnHeights.indexOf(Math.min(...columnHeights));

            // Calculate position
            const x = shortestColumn * (itemWidth + gap);
            const y = columnHeights[shortestColumn];

            // Set position
            item.style.position = 'absolute';
            item.style.left = `${x}px`;
            item.style.top = `${y}px`;
            item.style.width = `${itemWidth}px`;

            // Update column height
            columnHeights[shortestColumn] += item.offsetHeight + gap;
        });

        // Set grid height
        const tallestColumn = Math.max(...columnHeights);
        elements.portfolioGrid.style.height = `${tallestColumn}px`;
        elements.portfolioGrid.style.position = 'relative';
    }

    // Get column count based on viewport width
    function getColumnCount() {
        const width = window.innerWidth;
        if (width >= 1024) return 3;
        if (width >= 768) return 2;
        return 1;
    }

    // Handle view details
    function handleViewDetails(e) {
        e.preventDefault();
        e.stopPropagation();
        const projectId = this.getAttribute('data-project-id');
        openProjectModal(projectId);
    }

    // Handle view gallery
    function handleViewGallery(e) {
        e.preventDefault();
        e.stopPropagation();
        const projectId = this.getAttribute('data-project-id');
        openGalleryLightbox(projectId);
    }

    // Handle view before/after
    function handleViewBeforeAfter(e) {
        e.preventDefault();
        e.stopPropagation();
        const projectId = this.getAttribute('data-project-id');
        openBeforeAfterModal(projectId);
    }

    // Open project modal
    function openProjectModal(projectId) {
        const project = state.portfolioData.find(p => p.id == projectId);
        if (!project) {
            console.error('Project not found:', projectId);
            return;
        }

        const title = state.currentLanguage === 'ar' ? project.title_ar : project.title_en;
        const description = state.currentLanguage === 'ar' ? project.description_ar : project.description_en;
        const serviceName = state.currentLanguage === 'ar' ? project.service_name_ar : project.service_name_en;

        const modalContent = `
            <div class="project-detail">
                <div class="project-detail-header">
                    <img src="${escapeHTML(project.main_image)}" alt="${escapeHTML(title)}" class="project-main-image">
                </div>

                <div class="project-detail-body">
                    <div class="project-meta-grid">
                        <div class="meta-item">
                            <i class="${escapeHTML(project.icon_class)}"></i>
                            <div>
                                <span class="meta-label">${getTranslation('service')}</span>
                                <span class="meta-value">${escapeHTML(serviceName)}</span>
                            </div>
                        </div>
                        <div class="meta-item">
                            <i class="fa-solid fa-map-marker-alt"></i>
                            <div>
                                <span class="meta-label">${getTranslation('location')}</span>
                                <span class="meta-value">${escapeHTML(project.location)}</span>
                            </div>
                        </div>
                        <div class="meta-item">
                            <i class="fa-regular fa-calendar"></i>
                            <div>
                                <span class="meta-label">${getTranslation('completed')}</span>
                                <span class="meta-value">${formatDate(project.completion_date)}</span>
                            </div>
                        </div>
                        ${project.project_duration ? `
                            <div class="meta-item">
                                <i class="fa-solid fa-clock"></i>
                                <div>
                                    <span class="meta-label">${getTranslation('duration')}</span>
                                    <span class="meta-value">${escapeHTML(project.project_duration)}</span>
                                </div>
                            </div>
                        ` : ''}
                    </div>

                    <div class="project-description">
                        <h4><i class="fa-solid fa-info-circle"></i> ${getTranslation('project_description')}</h4>
                        <p>${escapeHTML(description)}</p>
                    </div>

                    ${project.gallery_images && project.gallery_images.length > 0 ? `
                        <div class="project-gallery-preview">
                            <h4><i class="fa-solid fa-images"></i> ${getTranslation('project_gallery')}</h4>
                            <div class="gallery-grid">
                                ${project.gallery_images.slice(0, 4).map((img, index) => `
                                    <img src="${escapeHTML(img)}" alt="${escapeHTML(title)} - ${index + 1}"
                                         class="gallery-thumb" data-index="${index}">
                                `).join('')}
                                ${project.gallery_images.length > 4 ? `
                                    <div class="gallery-more">
                                        <span>+${project.gallery_images.length - 4} more</span>
                                    </div>
                                ` : ''}
                            </div>
                            <button class="btn btn-outline view-all-photos-btn" data-project-id="${project.id}">
                                <i class="fa-solid fa-expand"></i>
                                <span>${getTranslation('view_all_photos')}</span>
                            </button>
                        </div>
                    ` : ''}

                    <div class="project-cta">
                        <p>${getTranslation('interested_similar_project')}</p>
                        <div class="cta-buttons">
                            <a href="#booking" class="btn btn-primary">
                                <i class="fa-solid fa-calendar-check"></i>
                                <span>${getTranslation('book_now')}</span>
                            </a>
                            <a href="#quote" class="btn btn-outline">
                                <i class="fa-solid fa-calculator"></i>
                                <span>${getTranslation('get_quote')}</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        `;

        if (elements.projectModalTitle) {
            elements.projectModalTitle.textContent = title;
        }

        if (elements.projectModalBody) {
            elements.projectModalBody.innerHTML = modalContent;

            // Attach gallery thumbnail clicks
            const galleryThumbs = elements.projectModalBody.querySelectorAll('.gallery-thumb');
            galleryThumbs.forEach(thumb => {
                thumb.addEventListener('click', () => {
                    const index = parseInt(thumb.getAttribute('data-index'));
                    openGalleryLightbox(projectId, index);
                });
            });

            // Attach view all photos button
            const viewAllBtn = elements.projectModalBody.querySelector('.view-all-photos-btn');
            if (viewAllBtn) {
                viewAllBtn.addEventListener('click', () => openGalleryLightbox(projectId));
            }
        }

        if (elements.projectModal) {
            elements.projectModal.classList.add('active');
            document.body.style.overflow = 'hidden';

            // Track event
            if (typeof trackEvent === 'function') {
                trackEvent('project_detail_view', {
                    project_id: projectId,
                    project_title: title
                });
            }
        }
    }

    // Close project modal
    function closeProjectModal() {
        if (elements.projectModal) {
            elements.projectModal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    // Open gallery lightbox
    function openGalleryLightbox(projectId, startIndex = 0) {
        const project = state.portfolioData.find(p => p.id == projectId);
        if (!project || !project.gallery_images || project.gallery_images.length === 0) {
            console.error('No gallery images found for project:', projectId);
            return;
        }

        state.lightboxImages = project.gallery_images;
        state.lightboxCurrentIndex = startIndex;

        showLightboxImage();

        if (elements.lightbox) {
            elements.lightbox.classList.add('active');
            document.body.style.overflow = 'hidden';
        }

        // Track event
        if (typeof trackEvent === 'function') {
            trackEvent('gallery_view', { project_id: projectId });
        }
    }

    // Show lightbox image
    function showLightboxImage() {
        if (!state.lightboxImages || state.lightboxImages.length === 0) return;

        const currentImage = state.lightboxImages[state.lightboxCurrentIndex];

        if (elements.lightboxImage) {
            elements.lightboxImage.src = currentImage;
            elements.lightboxImage.alt = `Image ${state.lightboxCurrentIndex + 1}`;
        }

        if (elements.lightboxCurrentIndex) {
            elements.lightboxCurrentIndex.textContent = state.lightboxCurrentIndex + 1;
        }

        if (elements.lightboxTotalCount) {
            elements.lightboxTotalCount.textContent = state.lightboxImages.length;
        }

        // Update nav buttons visibility
        if (elements.lightboxPrev) {
            elements.lightboxPrev.style.display = state.lightboxCurrentIndex > 0 ? 'flex' : 'none';
        }

        if (elements.lightboxNext) {
            elements.lightboxNext.style.display =
                state.lightboxCurrentIndex < state.lightboxImages.length - 1 ? 'flex' : 'none';
        }
    }

    // Navigate lightbox
    function navigateLightbox(direction) {
        const newIndex = state.lightboxCurrentIndex + direction;

        if (newIndex >= 0 && newIndex < state.lightboxImages.length) {
            state.lightboxCurrentIndex = newIndex;
            showLightboxImage();
        }
    }

    // Close lightbox
    function closeLightbox() {
        if (elements.lightbox) {
            elements.lightbox.classList.remove('active');
            document.body.style.overflow = '';
        }

        state.lightboxImages = [];
        state.lightboxCurrentIndex = 0;
    }

    // Open before/after modal
    function openBeforeAfterModal(projectId) {
        const project = state.portfolioData.find(p => p.id == projectId);
        if (!project || !project.before_image || !project.after_image) {
            console.error('Before/after images not found for project:', projectId);
            return;
        }

        if (elements.beforeImage) {
            elements.beforeImage.src = project.before_image;
        }

        if (elements.afterImage) {
            elements.afterImage.src = project.after_image;
        }

        if (elements.beforeAfterModal) {
            elements.beforeAfterModal.classList.add('active');
            document.body.style.overflow = 'hidden';

            // Reset slider position
            setTimeout(() => {
                updateSliderPosition(50);
            }, 100);
        }

        // Track event
        if (typeof trackEvent === 'function') {
            trackEvent('before_after_view', { project_id: projectId });
        }
    }

    // Close before/after modal
    function closeBeforeAfterModal() {
        if (elements.beforeAfterModal) {
            elements.beforeAfterModal.classList.remove('active');
            document.body.style.overflow = '';
        }
    }

    // Initialize before/after slider
    function initBeforeAfterSlider() {
        if (!elements.beforeAfterSlider || !elements.sliderHandle) return;

        const slider = elements.beforeAfterSlider;
        const handle = elements.sliderHandle;

        // Mouse events
        handle.addEventListener('mousedown', startDragging);
        document.addEventListener('mousemove', drag);
        document.addEventListener('mouseup', stopDragging);

        // Touch events
        handle.addEventListener('touchstart', startDragging);
        document.addEventListener('touchmove', drag);
        document.addEventListener('touchend', stopDragging);

        // Click on slider to jump
        slider.addEventListener('click', (e) => {
            if (e.target !== handle && !handle.contains(e.target)) {
                const rect = slider.getBoundingClientRect();
                const x = e.clientX - rect.left;
                const percent = (x / rect.width) * 100;
                updateSliderPosition(percent);
            }
        });
    }

    function startDragging(e) {
        e.preventDefault();
        state.beforeAfterDragging = true;
    }

    function drag(e) {
        if (!state.beforeAfterDragging) return;

        const slider = elements.beforeAfterSlider;
        if (!slider) return;

        const rect = slider.getBoundingClientRect();
        const x = (e.clientX || e.touches[0].clientX) - rect.left;
        const percent = Math.max(0, Math.min(100, (x / rect.width) * 100));

        updateSliderPosition(percent);
    }

    function stopDragging() {
        state.beforeAfterDragging = false;
    }

    function updateSliderPosition(percent) {
        if (!elements.sliderHandle || !elements.beforeAfterSlider) return;

        const afterContainer = elements.beforeAfterSlider.querySelector('.after-image-container');
        if (afterContainer) {
            afterContainer.style.clipPath = `inset(0 ${100 - percent}% 0 0)`;
        }

        elements.sliderHandle.style.left = `${percent}%`;
    }

    // Handle keyboard navigation
    function handleKeyboardNavigation(e) {
        // Lightbox navigation
        if (elements.lightbox && elements.lightbox.classList.contains('active')) {
            if (e.key === 'Escape') {
                closeLightbox();
            } else if (e.key === 'ArrowLeft') {
                navigateLightbox(-1);
            } else if (e.key === 'ArrowRight') {
                navigateLightbox(1);
            }
        }

        // Project modal
        if (elements.projectModal && elements.projectModal.classList.contains('active')) {
            if (e.key === 'Escape') {
                closeProjectModal();
            }
        }

        // Before/After modal
        if (elements.beforeAfterModal && elements.beforeAfterModal.classList.contains('active')) {
            if (e.key === 'Escape') {
                closeBeforeAfterModal();
            }
        }
    }

    // Utility: Escape HTML
    function escapeHTML(str) {
        const div = document.createElement('div');
        div.textContent = str || '';
        return div.innerHTML;
    }

    // Utility: Get translation
    function getTranslation(key) {
        const translations = {
            'service': state.currentLanguage === 'ar' ? 'الخدمة' : 'Service',
            'location': state.currentLanguage === 'ar' ? 'الموقع' : 'Location',
            'completed': state.currentLanguage === 'ar' ? 'تم الإنجاز' : 'Completed',
            'duration': state.currentLanguage === 'ar' ? 'المدة' : 'Duration',
            'project_description': state.currentLanguage === 'ar' ? 'وصف المشروع' : 'Project Description',
            'project_gallery': state.currentLanguage === 'ar' ? 'معرض المشروع' : 'Project Gallery',
            'view_all_photos': state.currentLanguage === 'ar' ? 'عرض جميع الصور' : 'View All Photos',
            'interested_similar_project': state.currentLanguage === 'ar' ?
                'مهتم بمشروع مماثل؟' : 'Interested in a similar project?',
            'book_now': state.currentLanguage === 'ar' ? 'احجز الآن' : 'Book Now',
            'get_quote': state.currentLanguage === 'ar' ? 'احصل على عرض سعر' : 'Get Quote'
        };

        return translations[key] || key;
    }

    // Utility: Format date
    function formatDate(dateString) {
        const date = new Date(dateString);
        const options = { year: 'numeric', month: 'short' };
        return date.toLocaleDateString(state.currentLanguage === 'ar' ? 'ar-AE' : 'en-US', options);
    }

    // Initialize on DOM ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

    // Export to global scope
    window.PortfolioModule = {
        openProjectModal,
        closeProjectModal,
        openGalleryLightbox,
        closeLightbox,
        openBeforeAfterModal,
        closeBeforeAfterModal,
        filterProjects,
        resetFilters
    };
})();
