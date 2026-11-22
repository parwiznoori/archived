(() => {
    'use strict';
    const root = document.getElementById('archiveGallery');
    const data = document.getElementById('agData');
    if (!root || !data) return;
    const pages = JSON.parse(data.textContent);
    const stage = document.getElementById('agStage');
    const canvas = document.getElementById('agCanvas');
    const status = document.getElementById('agStatus');
    const error = document.getElementById('agError');
    const input = document.getElementById('agPageInput');
    const notice = document.getElementById('agNotice');
    const play = document.getElementById('agPlay');
    const buttons = [...root.querySelectorAll('[data-page-index]')];
    let image = document.getElementById('agImage');
    let index = 0, zoom = 1, rotation = 0, timer = null, playing = false, nextPreview = null, generation = 0;

    function schedule() {
        clearTimeout(timer);
        if (playing && !document.hidden) timer = setTimeout(() => show((index + 1) % pages.length), 5000);
    }
    function setPlaying(value) {
        playing = value;
        play.textContent = playing ? 'توقف اسلایدشو' : 'شروع اسلایدشو';
        play.setAttribute('aria-pressed', String(playing));
        clearTimeout(timer);
        if (playing && image.complete && image.naturalWidth) schedule();
    }
    function transform() {
        image.style.transform = `rotate(${rotation}deg)`;
        if (zoom === 1 && rotation === 0) {
            image.style.width = ''; image.style.maxWidth = ''; image.style.maxHeight = '';
            canvas.style.width = ''; canvas.style.height = '';
            return;
        }
        const rotated = rotation % 180 !== 0;
        const availableWidth = Math.max(120, stage.clientWidth - 48);
        const availableHeight = Math.max(120, stage.clientHeight - 48);
        const ratio = Math.min(availableWidth / (rotated ? image.naturalHeight : image.naturalWidth), availableHeight / (rotated ? image.naturalWidth : image.naturalHeight));
        const width = image.naturalWidth * ratio * zoom, height = image.naturalHeight * ratio * zoom;
        image.style.maxWidth = 'none'; image.style.maxHeight = 'none'; image.style.width = `${width}px`;
        canvas.style.width = `${Math.max(stage.clientWidth, (rotated ? height : width) + 48)}px`;
        canvas.style.height = `${Math.max(stage.clientHeight, (rotated ? width : height) + 48)}px`;
    }
    function bindImage(target, version) {
        let finished = false;
        const loaded = () => {
            if (version !== generation || finished) return;
            finished = true;
            status.hidden = true; error.hidden = true;
            stage.classList.remove('is-loading'); stage.setAttribute('aria-busy', 'false');
            transform(); schedule();
            // Only one adjacent preview, after the visible image has finished loading.
            if (index + 1 < pages.length && !navigator.connection?.saveData) {
                nextPreview = new Image(); nextPreview.decoding = 'async'; nextPreview.fetchPriority = 'low';
                nextPreview.src = pages[index + 1].preview;
            }
        };
        target.onload = loaded;
        target.onerror = () => {
            if (version !== generation || finished) return;
            finished = true;
            status.hidden = true; error.hidden = false; target.hidden = true;
            stage.classList.remove('is-loading'); stage.setAttribute('aria-busy', 'false'); setPlaying(false);
        };
        if (target.complete) {
            if (target.naturalWidth) loaded();
            else if (target.getAttribute('src')) target.onerror();
        }
    }
    function show(newIndex) {
        if (newIndex < 0 || newIndex >= pages.length) return;
        clearTimeout(timer); generation++; index = newIndex; zoom = 1; rotation = 0;
        if (nextPreview) { nextPreview.removeAttribute('src'); nextPreview = null; }
        image.onload = image.onerror = null; image.removeAttribute('src');
        image = new Image(); image.id = 'agImage'; image.alt = `صفحه ${pages[index].page}`; image.decoding = 'async'; image.fetchPriority = 'high';
        canvas.replaceChildren(image); canvas.style.width = ''; canvas.style.height = '';
        status.hidden = false; error.hidden = true; notice.textContent = '';
        stage.classList.add('is-loading'); stage.setAttribute('aria-busy', 'true'); stage.scrollTop = 0; stage.scrollLeft = 0;
        document.getElementById('agPage').textContent = pages[index].page;
        document.getElementById('agPosition').textContent = `${index + 1} از ${pages.length}`;
        document.getElementById('agOriginal').href = pages[index].original;
        input.value = pages[index].page;
        buttons.forEach((button, i) => { button.classList.toggle('is-active', i === index); button.setAttribute('aria-current', i === index ? 'page' : 'false'); });
        root.querySelectorAll('[data-action="first"],[data-action="previous"]').forEach(button => { button.disabled = index === 0; });
        root.querySelectorAll('[data-action="last"],[data-action="next"]').forEach(button => { button.disabled = index === pages.length - 1; });
        image.src = pages[index].preview;
        bindImage(image, generation);
    }
    root.addEventListener('click', async event => {
        const pageButton = event.target.closest('[data-page-index]');
        if (pageButton) { show(Number(pageButton.dataset.pageIndex)); return; }
        const action = event.target.closest('[data-action]')?.dataset.action;
        if (!action) return;
        if (action === 'first') show(0);
        if (action === 'last') show(pages.length - 1);
        if (action === 'previous') show(index - 1);
        if (action === 'next') show(index + 1);
        if (action === 'retry') show(index);
        if (action === 'play') setPlaying(!playing);
        if (image.naturalWidth) {
            if (action === 'zoom-in') { zoom = Math.min(4, zoom + .25); transform(); }
            if (action === 'zoom-out') { zoom = Math.max(.5, zoom - .25); transform(); }
            if (action === 'reset') { zoom = 1; rotation = 0; transform(); }
            if (action === 'rotate') { rotation = (rotation + 90) % 360; transform(); }
        }
        if (action === 'fullscreen') {
            try {
                if (document.fullscreenElement) await document.exitFullscreen();
                else await root.querySelector('.ag-reader').requestFullscreen();
            } catch (_) { notice.textContent = 'مرورگر اجازه نمایش تمام‌صفحه را نداد.'; }
        }
    });
    document.getElementById('agJump').addEventListener('submit', event => {
        event.preventDefault();
        const found = pages.findIndex(page => Number(page.page) === Number(input.value));
        if (found < 0) notice.textContent = 'این شماره صفحه در کتاب موجود نیست.';
        else show(found);
    });
    root.addEventListener('keydown', event => {
        if (event.target.matches('input,textarea,select') || event.ctrlKey || event.altKey || event.metaKey) return;
        if (event.key === 'ArrowLeft') { event.preventDefault(); show(index + 1); }
        if (event.key === 'ArrowRight') { event.preventDefault(); show(index - 1); }
        if (event.key === 'Home') { event.preventDefault(); show(0); }
        if (event.key === 'End') { event.preventDefault(); show(pages.length - 1); }
        if (event.key === 'Escape') setPlaying(false);
    });
    document.addEventListener('visibilitychange', () => { if (document.hidden) clearTimeout(timer); else if (image.complete && image.naturalWidth) schedule(); });
    window.addEventListener('resize', () => { if (image.naturalWidth) transform(); });
    document.addEventListener('fullscreenchange', () => { if (image.naturalWidth) transform(); });
    stage.classList.add('is-loading');
    root.querySelectorAll('[data-action="first"],[data-action="previous"]').forEach(button => { button.disabled = true; });
    if (pages.length === 1) root.querySelectorAll('[data-action="last"],[data-action="next"],[data-action="play"]').forEach(button => { button.disabled = true; });
    bindImage(image, generation);
})();
