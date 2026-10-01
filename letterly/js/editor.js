const form = document.querySelector('#letter-form');
const preview = document.querySelector('#letter-preview');
const previewTitle = document.querySelector('#preview-title');
const previewContent = document.querySelector('#preview-content');
const stickerLayer = document.querySelector('#sticker-layer');
const stickerSymbols = { flower: '✿', heart: '♥', sparkle: '✳', sun: '☼' };

if (form && preview && stickerLayer) {
    const titleInput = form.elements.title;
    const contentInput = form.elements.content;
    const wordCount = document.querySelector('#word-count');
    const countWords = () => contentInput.value.trim().split(/\s+/).filter(Boolean).length;
    const updateWords = () => {
        const total = countWords();
        wordCount.textContent = `${total} ${total === 1 ? 'word' : 'words'}`;
    };
    const updateContent = () => {
        previewContent.textContent = contentInput.value || 'Your words will find their way here.';
        previewContent.style.whiteSpace = 'pre-wrap';
        updateWords();
    };
    titleInput.addEventListener('input', () => {
        previewTitle.textContent = titleInput.value || 'A letter to someone...';
    });
    contentInput.addEventListener('input', updateContent);
    updateContent();

    const fontSelect = document.querySelector('#font-select');
    const templateSelect = document.querySelector('#template-select');
    const sizeSelect = document.querySelector('#size-select');
    const sizeOutput = document.querySelector('#size-output');
    const colorSelect = document.querySelector('#color-select');
    const paperSelect = document.querySelector('#paper-select');
    const updateFont = () => {
        previewContent.classList.remove('font-serif', 'font-handwritten', 'font-sans');
        previewContent.classList.add(`font-${fontSelect.value}`);
    };
    fontSelect.addEventListener('change', updateFont);
    updateFont();
    templateSelect.addEventListener('change', () => {
        preview.classList.remove('paper-rose', 'paper-sunshine', 'paper-midnight');
        preview.classList.add(`paper-${templateSelect.value}`);
        const templatePaper = { rose: '#fff7f1', sunshine: '#fff9e8', midnight: '#f3f1eb' };
        preview.style.setProperty('--paper-color', templatePaper[templateSelect.value]);
        paperSelect.value = templatePaper[templateSelect.value];
    });
    sizeSelect.addEventListener('input', () => {
        preview.style.setProperty('--letter-size', `${sizeSelect.value}px`);
        sizeOutput.value = `${sizeSelect.value}px`;
    });
    colorSelect.addEventListener('input', () => preview.style.setProperty('--ink-color', colorSelect.value));
    paperSelect.addEventListener('change', () => preview.style.setProperty('--paper-color', paperSelect.value));
    form.querySelectorAll('input[name="text_align"]').forEach((input) => input.addEventListener('change', () => {
        preview.style.setProperty('--letter-align', input.value);
    }));

    const photoInput = form.querySelector('input[name="photo"]');
    photoInput.addEventListener('change', () => {
        const [file] = photoInput.files;
        if (!file) return;
        const reader = new FileReader();
        reader.addEventListener('load', () => {
            const photo = document.querySelector('#preview-photo');
            photo.src = reader.result;
            photo.hidden = false;
        });
        reader.readAsDataURL(file);
    });

    const setStickerPosition = (sticker, x, y) => {
        const bounds = stickerLayer.getBoundingClientRect();
        const positionX = Math.max(3, Math.min(97, ((x - bounds.left) / bounds.width) * 100));
        const positionY = Math.max(3, Math.min(97, ((y - bounds.top) / bounds.height) * 100));
        sticker.style.left = `${positionX}%`;
        sticker.style.top = `${positionY}%`;
        sticker.querySelector('[name="sticker_x[]"]').value = positionX.toFixed(2);
        sticker.querySelector('[name="sticker_y[]"]').value = positionY.toFixed(2);
    };
    const addSticker = (key, x = 82, y = 18) => {
        const sticker = document.createElement('button');
        sticker.type = 'button';
        sticker.className = 'placed-sticker';
        sticker.dataset.sticker = key;
        sticker.setAttribute('aria-label', `Move or remove ${key} sticker`);
        sticker.style.left = `${x}%`;
        sticker.style.top = `${y}%`;
        const symbol = document.createElement('span');
        symbol.className = 'sticker-symbol';
        symbol.textContent = stickerSymbols[key];
        const resizeHandle = document.createElement('span');
        resizeHandle.className = 'sticker-resize';
        resizeHandle.setAttribute('aria-hidden', 'true');
        sticker.append(symbol, resizeHandle);
        const hiddenSticker = document.createElement('input');
        hiddenSticker.type = 'hidden'; hiddenSticker.name = 'sticker[]'; hiddenSticker.value = key;
        const hiddenX = document.createElement('input');
        hiddenX.type = 'hidden'; hiddenX.name = 'sticker_x[]'; hiddenX.value = x;
        const hiddenY = document.createElement('input');
        hiddenY.type = 'hidden'; hiddenY.name = 'sticker_y[]'; hiddenY.value = y;
        const hiddenWidth = document.createElement('input');
        hiddenWidth.type = 'hidden'; hiddenWidth.name = 'sticker_w[]'; hiddenWidth.value = 48;
        const hiddenHeight = document.createElement('input');
        hiddenHeight.type = 'hidden'; hiddenHeight.name = 'sticker_h[]'; hiddenHeight.value = 48;
        sticker.append(hiddenSticker, hiddenX, hiddenY, hiddenWidth, hiddenHeight);
        sticker.style.width = '48px';
        sticker.style.height = '48px';
        stickerLayer.append(sticker);
        wireSticker(sticker);
    };
    const wireSticker = (sticker) => {
        let moved = false;
        sticker.addEventListener('pointerdown', (event) => {
            if (event.target.closest('.sticker-resize')) {
                event.preventDefault();
                event.stopPropagation();
                const handle = event.target;
                const startX = event.clientX;
                const startY = event.clientY;
                const startWidth = sticker.offsetWidth;
                const startHeight = sticker.offsetHeight;
                handle.setPointerCapture(event.pointerId);
                const resize = (resizeEvent) => {
                    const delta = Math.max(resizeEvent.clientX - startX, resizeEvent.clientY - startY);
                    const width = Math.max(28, Math.min(100, startWidth + delta));
                    const height = Math.max(28, Math.min(100, startHeight + delta));
                    sticker.style.width = `${width}px`;
                    sticker.style.height = `${height}px`;
                    sticker.querySelector('[name="sticker_w[]"]').value = width.toFixed(2);
                    sticker.querySelector('[name="sticker_h[]"]').value = height.toFixed(2);
                };
                const finishResize = () => {
                    handle.removeEventListener('pointermove', resize);
                    handle.removeEventListener('pointerup', finishResize);
                };
                handle.addEventListener('pointermove', resize);
                handle.addEventListener('pointerup', finishResize, { once: true });
                return;
            }
            moved = false;
            sticker.setPointerCapture(event.pointerId);
            const move = (moveEvent) => {
                moved = true;
                setStickerPosition(sticker, moveEvent.clientX, moveEvent.clientY);
            };
            const stop = () => {
                sticker.removeEventListener('pointermove', move);
                sticker.removeEventListener('pointerup', stop);
            };
            sticker.addEventListener('pointermove', move);
            sticker.addEventListener('pointerup', stop, { once: true });
        });
        sticker.addEventListener('click', (event) => {
            if (!event.target.closest('.sticker-resize') && !moved && sticker.isConnected) sticker.remove();
        });
    };
    stickerLayer.querySelectorAll('.placed-sticker').forEach(wireSticker);
    document.querySelectorAll('[data-sticker]').forEach((button) => {
        if (button.classList.contains('sticker-add')) {
            button.addEventListener('click', () => {
                if (stickerLayer.querySelectorAll('.placed-sticker').length >= 12) return;
                addSticker(button.dataset.sticker, 76 + Math.random() * 14, 14 + Math.random() * 68);
            });
        }
    });
}