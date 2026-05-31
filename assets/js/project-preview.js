/**
 * Project Preview Modal with Dive-In Animation
 * Creates an immersive preview experience for projects
 */

class ProjectPreview {
    constructor() {
        this.modal = null;
        this.currentProject = null;
        this.init();
    }

    init() {
        this.createModal();
        this.attachEventListeners();
    }

    createModal() {
        const modalHTML = `
            <div class="project-preview-modal" id="projectPreviewModal">
                <div class="preview-backdrop"></div>
                <div class="preview-container">
                    <div class="preview-header">
                        <div class="preview-title">
                            <h3 id="previewProjectTitle">Project Preview</h3>
                            <span class="preview-type-badge" id="previewTypeBadge">Website</span>
                        </div>
                        <div class="preview-controls">
                            <button class="preview-btn" id="previewDesktopBtn" title="Desktop View">
                                <i class="fas fa-desktop"></i>
                            </button>
                            <button class="preview-btn" id="previewTabletBtn" title="Tablet View">
                                <i class="fas fa-tablet-alt"></i>
                            </button>
                            <button class="preview-btn" id="previewMobileBtn" title="Mobile View">
                                <i class="fas fa-mobile-alt"></i>
                            </button>
                            <a class="preview-btn" id="previewOpenNewTab" href="#" target="_blank">
                                <i class="fas fa-external-link-alt"></i> Open
                            </a>
                            <button class="preview-btn preview-close" id="previewCloseBtn">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                    <div class="preview-content">
                        <div class="preview-loading" id="previewLoading">
                            <div class="preview-loading-spinner"></div>
                            <p>Loading preview...</p>
                        </div>
                        <div class="preview-iframe-wrapper" id="previewIframeWrapper">
                            <!-- Content will be injected here -->
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        document.body.insertAdjacentHTML('beforeend', modalHTML);
        this.modal = document.getElementById('projectPreviewModal');
    }

    attachEventListeners() {
        // Close button
        document.getElementById('previewCloseBtn').addEventListener('click', () => this.close());
        
        // Backdrop click
        this.modal.querySelector('.preview-backdrop').addEventListener('click', () => this.close());
        
        // Device view buttons
        document.getElementById('previewDesktopBtn').addEventListener('click', () => this.setDeviceView('desktop'));
        document.getElementById('previewTabletBtn').addEventListener('click', () => this.setDeviceView('tablet'));
        document.getElementById('previewMobileBtn').addEventListener('click', () => this.setDeviceView('mobile'));
        
        // ESC key to close
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape' && this.modal.classList.contains('active')) {
                this.close();
            }
        });
    }

    open(projectData) {
        this.currentProject = projectData;
        
        // Update modal content
        document.getElementById('previewProjectTitle').textContent = projectData.title;
        document.getElementById('previewTypeBadge').textContent = this.getProjectType(projectData);
        
        // Set open link
        const openBtn = document.getElementById('previewOpenNewTab');
        if (projectData.live_link) {
            openBtn.href = projectData.live_link;
            openBtn.style.display = 'inline-flex';
        } else {
            openBtn.style.display = 'none';
        }
        
        // Animate the project card diving in
        this.animateDiveIn(projectData.cardElement);
        
        // Load preview content
        setTimeout(() => {
            this.loadPreviewContent(projectData);
        }, 300);
    }

    animateDiveIn(cardElement) {
        if (!cardElement) {
            this.modal.classList.add('active');
            return;
        }

        // Get card position
        const rect = cardElement.getBoundingClientRect();
        const clone = cardElement.cloneNode(true);
        
        // Position clone at card location
        clone.style.position = 'fixed';
        clone.style.top = rect.top + 'px';
        clone.style.left = rect.left + 'px';
        clone.style.width = rect.width + 'px';
        clone.style.height = rect.height + 'px';
        clone.style.zIndex = '9998';
        clone.style.transition = 'all 0.6s cubic-bezier(0.34, 1.56, 0.64, 1)';
        
        document.body.appendChild(clone);
        
        // Hide original card
        cardElement.style.opacity = '0';
        
        // Trigger animation
        requestAnimationFrame(() => {
            this.modal.classList.add('active');
            
            setTimeout(() => {
                clone.style.top = '50%';
                clone.style.left = '50%';
                clone.style.transform = 'translate(-50%, -50%) scale(3)';
                clone.style.opacity = '0';
                
                setTimeout(() => {
                    clone.remove();
                    cardElement.style.opacity = '1';
                }, 600);
            }, 50);
        });
    }

    loadPreviewContent(projectData) {
        const wrapper = document.getElementById('previewIframeWrapper');
        const loading = document.getElementById('previewLoading');
        
        loading.style.display = 'block';
        wrapper.innerHTML = '';
        
        // Determine content type
        const contentType = this.getContentType(projectData);
        
        setTimeout(() => {
            loading.style.display = 'none';
            
            if (contentType === 'website' && projectData.live_link) {
                this.loadWebsitePreview(projectData.live_link, wrapper);
            } else if (contentType === 'instagram') {
                this.loadInstagramPreview(projectData.instagram_url, wrapper);
            } else if (contentType === 'video') {
                this.loadVideoPreview(projectData.video_url, wrapper);
            } else if (contentType === 'image') {
                this.loadImagePreview(projectData.image, wrapper);
            } else {
                this.loadPlaceholder(projectData, wrapper);
            }
        }, 800);
    }

    loadWebsitePreview(url, wrapper) {
        const frame = document.createElement('div');
        frame.className = 'preview-device-frame';
        frame.innerHTML = `<iframe src="${url}" class="preview-iframe" sandbox="allow-same-origin allow-scripts allow-popups allow-forms"></iframe>`;
        wrapper.appendChild(frame);
    }

    loadInstagramPreview(url, wrapper) {
        const container = document.createElement('div');
        container.className = 'preview-instagram-embed';
        container.innerHTML = `
            <blockquote class="instagram-media" data-instgrm-permalink="${url}" data-instgrm-version="14"></blockquote>
            <script async src="//www.instagram.com/embed.js"></script>
        `;
        wrapper.appendChild(container);
    }

    loadVideoPreview(url, wrapper) {
        const container = document.createElement('div');
        container.className = 'preview-media-container';
        container.innerHTML = `<video src="${url}" controls autoplay muted style="max-width: 100%; max-height: 100%;"></video>`;
        wrapper.appendChild(container);
    }

    loadImagePreview(url, wrapper) {
        const container = document.createElement('div');
        container.className = 'preview-media-container';
        container.innerHTML = `<img src="${url}" alt="Project Preview" style="max-width: 100%; max-height: 100%; object-fit: contain;">`;
        wrapper.appendChild(container);
    }

    loadPlaceholder(projectData, wrapper) {
        const container = document.createElement('div');
        container.className = 'preview-media-container';
        container.innerHTML = `
            <div style="text-align: center; color: #a0aec0;">
                <i class="fas fa-image" style="font-size: 80px; margin-bottom: 20px; color: #00d4ff;"></i>
                <h3 style="color: #fff; margin-bottom: 10px;">${projectData.title}</h3>
                <p>${projectData.short_description || 'Preview not available'}</p>
                ${projectData.live_link ? `<a href="${projectData.live_link}" target="_blank" class="preview-btn" style="margin-top: 20px;">Visit Live Site</a>` : ''}
            </div>
        `;
        wrapper.appendChild(container);
    }

    setDeviceView(device) {
        const frame = this.modal.querySelector('.preview-device-frame');
        if (!frame) return;
        
        frame.className = 'preview-device-frame ' + device;
        
        // Update active button
        this.modal.querySelectorAll('.preview-controls .preview-btn').forEach(btn => {
            btn.classList.remove('active');
        });
        
        if (device === 'desktop') document.getElementById('previewDesktopBtn').classList.add('active');
        if (device === 'tablet') document.getElementById('previewTabletBtn').classList.add('active');
        if (device === 'mobile') document.getElementById('previewMobileBtn').classList.add('active');
    }

    getProjectType(projectData) {
        if (projectData.category === 'web' || projectData.category === 'ecommerce') return 'Website';
        if (projectData.category === 'app') return 'Application';
        if (projectData.category === 'branding') return 'Design';
        if (projectData.instagram_url) return 'Social Media';
        if (projectData.video_url) return 'Video';
        return 'Project';
    }

    getContentType(projectData) {
        if (projectData.live_link && (projectData.category === 'web' || projectData.category === 'ecommerce')) {
            return 'website';
        }
        if (projectData.instagram_url) return 'instagram';
        if (projectData.video_url) return 'video';
        if (projectData.image) return 'image';
        return 'placeholder';
    }

    close() {
        this.modal.classList.remove('active');
        this.currentProject = null;
    }
}

// Initialize preview system
let projectPreview;

document.addEventListener('DOMContentLoaded', () => {
    projectPreview = new ProjectPreview();
});

// Global function to open preview
function openProjectPreview(projectId, cardElement) {
    // Fetch project data (you can pass it directly or fetch from server)
    const projectCard = cardElement || document.querySelector(`[data-project-id="${projectId}"]`);
    
    // Get project data from card attributes or make AJAX call
    const projectData = {
        id: projectId,
        title: projectCard?.querySelector('h4')?.textContent || 'Project',
        short_description: projectCard?.querySelector('.project-category')?.textContent || '',
        live_link: projectCard?.querySelector('a[href]')?.href || '',
        image: projectCard?.querySelector('img')?.src || '',
        category: projectCard?.dataset.category || 'web',
        cardElement: projectCard
    };
    
    projectPreview.open(projectData);
}
