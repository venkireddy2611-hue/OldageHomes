// Minimal lightbox for gallery images
(function () {
    function openLightbox(imgSrc, alt) {
        const overlay = document.createElement('div');
        overlay.className = 'site-lightbox-overlay';
        overlay.innerHTML = `
            <div class="site-lightbox">
                <button class="site-lightbox-close" aria-label="Close">&times;</button>
                <img src="${imgSrc}" alt="${alt||''}" />
            </div>`;
        overlay.addEventListener('click', (e) => {
            if (e.target === overlay || e.target.classList.contains('site-lightbox-close')) {
                overlay.remove();
            }
        });
        document.body.appendChild(overlay);
    }

    document.addEventListener('click', (e) => {
        const item = e.target.closest('.gallery-item img');
        if (!item) return;
        e.preventDefault();
        openLightbox(item.src, item.alt || item.title || 'Gallery Image');
    });
})();

/* Small CSS injected so we don't need to modify CSS files */
(function(){
    const css = `
    .site-lightbox-overlay{position:fixed;inset:0;background:rgba(0,0,0,0.85);display:flex;align-items:center;justify-content:center;z-index:2000}
    .site-lightbox{position:relative;max-width:95%;max-height:90%}
    .site-lightbox img{max-width:100%;max-height:90vh;border-radius:8px}
    .site-lightbox-close{position:absolute;right:-10px;top:-10px;background:#fff;border-radius:50%;width:40px;height:40px;border:0;font-size:20px;cursor:pointer}
    `;
    const s = document.createElement('style'); s.textContent = css; document.head.appendChild(s);
})();
