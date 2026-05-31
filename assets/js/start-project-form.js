/* ======================================================
   START YOUR PROJECT - DYNAMIC FORM LOGIC
   Multi-step, service-based conditional rendering
   ====================================================== */

const SPForm = {
    currentStep: 1,
    formData: {},
    uploadedFiles: [],
    
    // Service configuration with dynamic fields
    services: {
        'website-development': {
            name: 'Website Development',
            icon: '🌐',
            subcategories: [
                'Landing Page Development',
                'Business Website Development',
                'E-Commerce Website Development',
                'Portfolio Website Design',
                'Custom Web Applications',
                'Admin Panel & Dashboard Development',
                'Online Booking & Registration Systems'
            ],
            fields: [
                { name: 'website_purpose', label: 'Website Purpose', type: 'textarea', required: true, helper: 'e.g., Sell products, showcase portfolio, gather leads' },
                { name: 'num_pages', label: 'Estimated Number of Pages', type: 'number', required: true, helper: 'Rough estimate of pages needed' },
                { name: 'style_preference', label: 'Preferred Style/Theme', type: 'select', required: true, options: ['Modern & Minimal', 'Corporate & Professional', 'Creative & Bold', 'Elegant & Luxury', 'Playful & Casual'] },
                { name: 'features_needed', label: 'Key Features Needed', type: 'textarea', required: true, helper: 'e.g., Contact form, Payment gateway, Blog, etc.' },
                { name: 'budget', label: 'Budget Range', type: 'select', required: true, options: ['₹10K - ₹50K', '₹50K - ₹150K', '₹150K - ₹500K', '₹500K+'] },
                { name: 'deadline', label: 'Expected Delivery Timeline', type: 'select', required: true, options: ['2-4 weeks', '1-3 months', '3-6 months', 'Flexible'] },
                { name: 'reference_sites', label: 'Reference Websites', type: 'text', required: false, helper: 'URLs of sites you like' },
                { name: 'colors', label: 'Preferred Colors', type: 'text', required: false, helper: 'e.g., Blue, Gold, Minimalist' },
                { name: 'assets', label: 'Existing Branding Assets', type: 'file', required: false, helper: 'Logo, brand guidelines, etc.' }
            ]
        },
        'website-design': {
            name: 'Website Design & Optimization',
            icon: '🎨',
            subcategories: [
                'Website Redesign & UI/UX Improvement',
                'Responsive Mobile-Friendly Websites',
                'Website Speed & Security Optimization',
                'Website Maintenance & Management'
            ],
            fields: [
                { name: 'current_site', label: 'Current Website URL', type: 'text', required: true, helper: 'Your existing website' },
                { name: 'design_goals', label: 'Design Goals', type: 'textarea', required: true, helper: 'What improvements do you want?' },
                { name: 'budget', label: 'Budget Range', type: 'select', required: true, options: ['₹10K - ₹50K', '₹50K - ₹150K', '₹150K - ₹500K', '₹500K+'] },
                { name: 'deadline', label: 'Timeline', type: 'select', required: true, options: ['2-4 weeks', '1-3 months', '3-6 months', 'Flexible'] }
            ]
        },
        'social-media': {
            name: 'Social Media Management',
            icon: '📱',
            subcategories: [
                'Social Media Page Handling',
                'Instagram Page Management',
                'Facebook Page Management',
                'Content Creation for Social Media',
                'Social Media Post Design'
            ],
            fields: [
                { name: 'platforms', label: 'Platforms Needed', type: 'checkbox', required: true, options: ['Instagram', 'Facebook', 'LinkedIn', 'TikTok', 'YouTube'] },
                { name: 'business_niche', label: 'Business Niche/Industry', type: 'text', required: true, helper: 'e.g., Fashion, Food, Tech, etc.' },
                { name: 'posting_frequency', label: 'Desired Posting Frequency', type: 'select', required: true, options: ['Daily', '3-4 times/week', '2-3 times/week', '1-2 times/week', 'Custom'] },
                { name: 'goal', label: 'Primary Goal', type: 'select', required: true, options: ['Increase Sales', 'Build Brand Awareness', 'Engage Community', 'Generate Leads', 'All of the Above'] },
                { name: 'existing_links', label: 'Existing Social Links', type: 'text', required: false, helper: 'URLs to your current profiles' }
            ]
        },
        'video-production': {
            name: 'Video Production & Editing',
            icon: '🎬',
            subcategories: [
                'Reel Editing & Short Video Editing',
                'Promotional Video Editing',
                'Advertisement Video Creation'
            ],
            fields: [
                { name: 'video_type', label: 'Video Type', type: 'select', required: true, options: ['Product Promo', 'Brand Story', 'Tutorial/How-to', 'Testimonial', 'Social Media Reel', 'Ad Campaign', 'Event Highlight'] },
                { name: 'duration', label: 'Video Duration', type: 'select', required: true, options: ['15-30 seconds', '30-60 seconds', '1-2 minutes', '2-5 minutes', 'Custom'] },
                { name: 'platform', label: 'Target Platform', type: 'select', required: true, options: ['YouTube', 'Instagram', 'Facebook', 'TikTok', 'LinkedIn', 'Website', 'Multiple'] },
                { name: 'style', label: 'Editing Style', type: 'select', required: true, options: ['Modern & Fast', 'Professional & Corporate', 'Creative & Bold', 'Cinematic', 'Minimal & Clean'] },
                { name: 'budget', label: 'Budget', type: 'select', required: true, options: ['₹5K - ₹20K', '₹20K - ₹50K', '₹50K - ₹150K', '₹150K+'] },
                { name: 'references', label: 'Sample References', type: 'text', required: false, helper: 'YouTube/Vimeo links to editing styles you like' },
                { name: 'footage', label: 'Upload Raw Footage', type: 'file', required: false, helper: 'If you have raw footage' }
            ]
        },
        'digital-marketing': {
            name: 'Digital Marketing & Advertising',
            icon: '📊',
            subcategories: [
                'Facebook & Instagram Ads',
                'Digital Marketing Services',
                'SEO & Online Visibility Optimization',
                'WhatsApp Marketing Integration',
                'Email Marketing Campaigns',
                'Brand Advertisement Campaigns'
            ],
            fields: [
                { name: 'marketing_goal', label: 'Marketing Goal', type: 'select', required: true, options: ['Increase Sales', 'Generate Leads', 'Build Brand Awareness', 'Drive Traffic', 'Engagement'] },
                { name: 'audience', label: 'Target Audience Description', type: 'textarea', required: true, helper: 'Age, location, interests, demographics' },
                { name: 'platforms', label: 'Advertising Platforms', type: 'checkbox', required: true, options: ['Google Ads', 'Facebook/Instagram Ads', 'LinkedIn Ads', 'Email Marketing', 'WhatsApp Marketing'] },
                { name: 'monthly_budget', label: 'Monthly Ad Budget', type: 'select', required: true, options: ['₹5K - ₹20K', '₹20K - ₹50K', '₹50K - ₹150K', '₹150K+'] },
                { name: 'timeline', label: 'Campaign Duration', type: 'select', required: true, options: ['1 month', '3 months', '6 months', 'Ongoing'] },
                { name: 'existing_accounts', label: 'Do you have existing ad accounts?', type: 'select', required: false, options: ['Yes', 'No', 'Not Sure'] }
            ]
        },
        'branding-design': {
            name: 'Branding & Graphic Design',
            icon: '✨',
            subcategories: [
                'Branding & Identity Design',
                'Logo Design',
                'Brochure Design',
                'Flyer & Pamphlet Design',
                'Banner & Poster Design',
                'Business Card Design',
                'Product Catalog Design'
            ],
            fields: [
                { name: 'design_type', label: 'Design Type Needed', type: 'select', required: true, options: ['Logo Only', 'Full Branding Package', 'Specific Collateral', 'Rebranding'] },
                { name: 'brand_name', label: 'Brand Name', type: 'text', required: true, helper: 'Your company/product name' },
                { name: 'brand_style', label: 'Brand Style', type: 'select', required: true, options: ['Modern', 'Classic', 'Minimalist', 'Playful', 'Luxury', 'Eco-friendly'] },
                { name: 'colors', label: 'Preferred Colors', type: 'text', required: true, helper: 'e.g., Blue & Gold, Black & White' },
                { name: 'budget', label: 'Budget', type: 'select', required: true, options: ['₹5K - ₹20K', '₹20K - ₹50K', '₹50K - ₹150K', '₹150K+'] },
                { name: 'references', label: 'Inspiration References', type: 'text', required: false, helper: 'Brands or logos you admire' }
            ]
        },
        'hosting-technical': {
            name: 'Hosting & Technical Services',
            icon: '⚙️',
            subcategories: [
                'Website Hosting & Domain Setup',
                'Technical Support & IT Consultation',
                'Startup Online Presence Setup',
                'Creative Marketing Solutions'
            ],
            fields: [
                { name: 'service_type', label: 'Technical Service Needed', type: 'select', required: true, options: ['Domain & Hosting Setup', 'Migration to New Hosting', 'Technical Support Plan', 'Infrastructure Setup', 'Security Audit'] },
                { name: 'existing_hosting', label: 'Current Hosting Status', type: 'select', required: true, options: ['New Project - No Hosting', 'Have Existing Hosting', 'Moving from Another Provider'] },
                { name: 'problem_description', label: 'Problem Description / Requirements', type: 'textarea', required: true, helper: 'Technical details or requirements' },
                { name: 'budget', label: 'Budget', type: 'select', required: true, options: ['₹500 - ₹5K/month', '₹5K - ₹20K/month', '₹20K - ₹50K/month', '₹50K+/month'] }
            ]
        }
    },

    init() {
        this.bindEvents();
        this.loadDraft();
        this.updateProgress();
    },

    bindEvents() {
        // Service category selection
        document.getElementById('sp-main-service').addEventListener('change', (e) => {
            this.formData.main_service = e.target.value;
            this.renderServiceFields();
            this.updateProgress();
        });

        // Step navigation
        document.getElementById('sp-btn-next-step1').addEventListener('click', () => this.goToStep(2));
        document.getElementById('sp-btn-prev-step2').addEventListener('click', () => this.goToStep(1));

        // Form submission
        document.getElementById('sp-form').addEventListener('submit', (e) => {
            e.preventDefault();
            this.submitForm();
        });

        // File upload
        const uploadArea = document.getElementById('sp-file-upload');
        uploadArea.addEventListener('click', () => document.getElementById('sp-file-input').click());
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        uploadArea.addEventListener('dragleave', () => uploadArea.classList.remove('dragover'));
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            this.handleFileUpload(e.dataTransfer.files);
        });

        document.getElementById('sp-file-input').addEventListener('change', (e) => {
            this.handleFileUpload(e.target.files);
        });

        // Save draft
        document.getElementById('sp-save-draft-btn').addEventListener('click', () => this.saveDraft());

        // Close success modal
        document.getElementById('sp-modal-close-btn').addEventListener('click', () => {
            document.getElementById('sp-success-modal').classList.remove('show');
            // Redirect or reset
            window.location.reload();
        });
    },

    renderServiceFields() {
        const service = this.services[this.formData.main_service];
        const fieldsContainer = document.getElementById('sp-service-fields');
        
        if (!service) {
            fieldsContainer.innerHTML = '';
            return;
        }

        let html = `<div class="sp-service-title"><span class="sp-service-icon">${service.icon}</span> ${service.name}</div>`;

        // Subcategory selector
        html += `
            <div class="sp-form-row">
                <div class="sp-form-group">
                    <label>Specific Service <span class="sp-required">*</span></label>
                    <select id="sp-specific-service" required>
                        <option value="">-- Select a Service --</option>
                        ${service.subcategories.map(cat => `<option value="${cat}">${cat}</option>`).join('')}
                    </select>
                </div>
            </div>
        `;

        // Dynamic fields
        html += '<div class="sp-form-row">';
        service.fields.forEach((field, idx) => {
            if (field.type === 'text' || field.type === 'email' || field.type === 'number' || field.type === 'file') {
                html += this.renderTextInput(field);
            } else if (field.type === 'textarea') {
                html += this.renderTextarea(field);
            } else if (field.type === 'select') {
                html += this.renderSelect(field);
            } else if (field.type === 'checkbox') {
                html += this.renderCheckbox(field);
            }
            if ((idx + 1) % 2 === 0) {
                html += '</div><div class="sp-form-row">';
            }
        });
        html += '</div>';

        fieldsContainer.innerHTML = html;

        // Rebind specific service listener
        document.getElementById('sp-specific-service')?.addEventListener('change', (e) => {
            this.formData.specific_service = e.target.value;
            this.updateProgress();
        });

        // Rebind dynamic field listeners
        this.bindDynamicFields();
    },

    renderTextInput(field) {
        const id = field.name;
        const value = this.formData[id] || '';
        const req = field.required ? '<span class="sp-required">*</span>' : '';
        const helper = field.helper ? `<div class="sp-helper-text">${field.helper}</div>` : '';
        return `
            <div class="sp-form-group">
                <label>${field.label} ${req}</label>
                <input type="${field.type}" id="${id}" name="${id}" value="${value}" ${field.required ? 'required' : ''} placeholder="${field.label}">
                ${helper}
            </div>
        `;
    },

    renderTextarea(field) {
        const id = field.name;
        const value = this.formData[id] || '';
        const req = field.required ? '<span class="sp-required">*</span>' : '';
        const helper = field.helper ? `<div class="sp-helper-text">${field.helper}</div>` : '';
        return `
            <div class="sp-form-group" style="grid-column: span 2;">
                <label>${field.label} ${req}</label>
                <textarea id="${id}" name="${id}" ${field.required ? 'required' : ''} placeholder="${field.label}">${value}</textarea>
                ${helper}
            </div>
        `;
    },

    renderSelect(field) {
        const id = field.name;
        const value = this.formData[id] || '';
        const req = field.required ? '<span class="sp-required">*</span>' : '';
        const helper = field.helper ? `<div class="sp-helper-text">${field.helper}</div>` : '';
        return `
            <div class="sp-form-group">
                <label>${field.label} ${req}</label>
                <select id="${id}" name="${id}" ${field.required ? 'required' : ''}>
                    <option value="">-- Select --</option>
                    ${field.options.map(opt => `<option value="${opt}" ${value === opt ? 'selected' : ''}>${opt}</option>`).join('')}
                </select>
                ${helper}
            </div>
        `;
    },

    renderCheckbox(field) {
        const id = field.name;
        const selected = (this.formData[id] && Array.isArray(this.formData[id])) ? this.formData[id] : [];
        const req = field.required ? '<span class="sp-required">*</span>' : '';
        let html = `<div class="sp-form-group" style="grid-column: span 2;"><label>${field.label} ${req}</label><div class="sp-checkbox-group">`;
        field.options.forEach(opt => {
            const checked = selected.includes(opt) ? 'checked' : '';
            html += `
                <div class="sp-checkbox-item">
                    <input type="checkbox" id="${id}_${opt}" name="${id}" value="${opt}" ${checked}>
                    <label for="${id}_${opt}">${opt}</label>
                </div>
            `;
        });
        html += `</div></div>`;
        return html;
    },

    bindDynamicFields() {
        // Use event delegation on the form to catch all field changes
        const form = document.getElementById('sp-form');
        if (!form) return;
        
        form.addEventListener('change', (e) => {
            const target = e.target;
            const name = target.name || target.id;
            
            if (!name) return;
            
            if (target.type === 'checkbox') {
                // Collect all checked checkboxes with the same name
                const checked = Array.from(document.querySelectorAll(`[name="${name}"]:checked`)).map(el => el.value);
                this.formData[name] = checked;
            } else if (target.type === 'radio') {
                this.formData[name] = target.value;
            } else {
                this.formData[name] = target.value;
            }
            this.updateProgress();
        });
    },

    handleFileUpload(files) {
        Array.from(files).forEach(file => {
            const reader = new FileReader();
            reader.onload = (e) => {
                const fileObj = { name: file.name, type: file.type, data: e.target.result };
                this.uploadedFiles.push(fileObj);
                this.renderFileList();
            };
            reader.readAsDataURL(file);
        });
    },

    renderFileList() {
        const list = document.getElementById('sp-file-list');
        list.innerHTML = this.uploadedFiles.map((file, idx) => `
            <div class="sp-file-item">
                <span class="sp-file-item-icon">📎</span>
                <span class="sp-file-item-name">${file.name}</span>
                <button type="button" class="sp-file-item-remove" onclick="SPForm.removeFile(${idx})">✕</button>
            </div>
        `).join('');
    },

    removeFile(idx) {
        this.uploadedFiles.splice(idx, 1);
        this.renderFileList();
    },

    goToStep(step) {
        // Validate current step before moving
        if (this.currentStep === 1 && step === 2) {
            if (!this.validateStep1()) {
                alert('Please fill required fields.');
                return;
            }
        }
        this.currentStep = step;
        this.renderSteps();
        window.scrollTo({ top: 0, behavior: 'smooth' });
    },

    renderSteps() {
        document.querySelectorAll('.sp-form-step').forEach(el => el.classList.remove('active'));
        document.getElementById(`sp-step-${this.currentStep}`).classList.add('active');
    },

    validateStep1() {
        const name = document.getElementById('sp-client-name')?.value;
        const email = document.getElementById('sp-client-email')?.value;
        const phone = document.getElementById('sp-client-phone')?.value;
        const mainService = document.getElementById('sp-main-service')?.value;

        if (!name || !email || !mainService) {
            return false;
        }
        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            return false;
        }
        return true;
    },

    updateProgress() {
        // Count all form fields that should be filled
        const step1Fields = document.querySelectorAll('#sp-step-1 [name]');
        const step2Fields = document.querySelectorAll('#sp-step-2 [name]');
        const allFields = document.querySelectorAll('[id^="sp-"][name]');
        
        let filledCount = 0;
        let totalCount = 0;
        
        // Count filled required fields
        allFields.forEach(field => {
            // Skip file inputs and optional fields (those without required attribute or data-required)
            if (field.type === 'file') return;
            
            totalCount++;
            
            if (field.type === 'checkbox') {
                // For checkboxes, count if at least one is checked
                const checkedBoxes = document.querySelectorAll(`[name="${field.name}"]:checked`);
                if (checkedBoxes.length > 0) filledCount++;
            } else if (field.type === 'radio') {
                // For radio buttons, count if one is selected
                const selectedRadios = document.querySelectorAll(`[name="${field.name}"]:checked`);
                if (selectedRadios.length > 0) filledCount++;
            } else {
                // For text, email, number, select, textarea
                if (field.value && field.value.trim() !== '') {
                    filledCount++;
                }
            }
        });
        
        // Calculate percentage (allow up to 100%)
        const percent = totalCount > 0 ? Math.min((filledCount / totalCount) * 100, 100) : 0;
        
        // Update progress bar
        const progressFill = document.getElementById('sp-progress-fill');
        const progressText = document.getElementById('sp-progress-text');
        
        if (progressFill) {
            progressFill.style.width = percent.toFixed(0) + '%';
        }
        if (progressText) {
            progressText.textContent = Math.round(percent);
        }
    },

    saveDraft() {
        // Collect current form data
        document.querySelectorAll('[id^="sp-"]').forEach(field => {
            const name = field.name || field.id;
            if (field.type === 'checkbox') {
                this.formData[name] = Array.from(document.querySelectorAll(`[name="${name}"]:checked`)).map(el => el.value);
            } else {
                this.formData[name] = field.value;
            }
        });
        localStorage.setItem('sp-draft', JSON.stringify(this.formData));
        alert('Draft saved! You can return and continue later.');
    },

    loadDraft() {
        const draft = localStorage.getItem('sp-draft');
        if (draft) {
            this.formData = JSON.parse(draft);
            // Restore fields
            Object.entries(this.formData).forEach(([key, value]) => {
                const el = document.getElementById(key) || document.querySelector(`[name="${key}"]`);
                if (el) {
                    if (el.type === 'checkbox') {
                        // Handle checkboxes
                        if (Array.isArray(value)) {
                            value.forEach(v => {
                                const checkbox = document.querySelector(`[name="${key}"][value="${v}"]`);
                                if (checkbox) checkbox.checked = true;
                            });
                        }
                    } else {
                        el.value = value;
                    }
                }
            });
        }
    },

    submitForm() {
        // Collect all form data
        const form = document.getElementById('sp-form');
        const formData = new FormData(form);

        // Add files
        this.uploadedFiles.forEach((file, idx) => {
            formData.append(`file_${idx}`, file.data);
            formData.append(`file_${idx}_name`, file.name);
        });

        const btn = form.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.textContent = 'Submitting...';

        fetch('ajax/start_project_submit.php', {
            method: 'POST',
            body: formData
        })
        .then(r => r.json())
        .then(res => {
            if (res.success) {
                this.showSuccessModal();
                localStorage.removeItem('sp-draft');
            } else {
                alert(res.message || 'Failed to submit');
            }
        })
        .catch(err => {
            console.error(err);
            alert('An error occurred');
        })
        .finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<span>Submit Project Idea</span>';
        });
    },

    showSuccessModal() {
        document.getElementById('sp-success-modal').classList.add('show');
    }
};

document.addEventListener('DOMContentLoaded', () => SPForm.init());
