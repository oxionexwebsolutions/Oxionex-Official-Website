document.addEventListener('DOMContentLoaded', function(){
    const refFiles = document.getElementById('refFiles');
    const previewWrapper = document.querySelector('.ref-preview .mySwiper');
    let swiperInstance = null;

    function ensureSwiper() {
        if (!swiperInstance) {
            previewWrapper.style.display = 'block';
            swiperInstance = new Swiper('.mySwiper', {
                loop: false,
                pagination: { el: '.swiper-pagination', clickable: true },
                navigation: { nextEl: '.swiper-button-next', prevEl: '.swiper-button-prev' },
                slidesPerView: 1,
                spaceBetween: 12
            });
        }
    }

    refFiles.addEventListener('change', function(e){
        const files = Array.from(e.target.files || []);
        if (!files.length) return;
        ensureSwiper();
        swiperInstance.removeAllSlides();
        files.forEach((file) => {
            if (!file.type.startsWith('image/')) return;
            const reader = new FileReader();
            reader.onload = function(ev) {
                const slide = document.createElement('div');
                slide.className = 'swiper-slide';
                const img = document.createElement('img');
                img.src = ev.target.result;
                slide.appendChild(img);
                swiperInstance.appendSlide(slide);
            };
            reader.readAsDataURL(file);
        });
    });

    // Handle form submit with AJAX (multipart)
    const form = document.getElementById('startProjectForm');
    form.addEventListener('submit', function(e){
        e.preventDefault();
        const btn = form.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.textContent = 'Submitting...';

        const fd = new FormData(form);
        // Append files (they are already in the input)

        fetch('ajax/start_project_submit.php', {
            method: 'POST',
            body: fd
        }).then(r => r.json()).then(res => {
            if (res.success) {
                alert(res.message || 'Submission received.');
                form.reset();
                if (swiperInstance) {
                    swiperInstance.removeAllSlides();
                    swiperInstance = null;
                    previewWrapper.style.display = 'none';
                }
            } else {
                alert(res.message || 'Failed to submit.');
            }
        }).catch(err => {
            console.error(err);
            alert('An error occurred.');
        }).finally(() => {
            btn.disabled = false;
            btn.innerHTML = '<span>Submit Project Idea</span>';
        });
    });
});
