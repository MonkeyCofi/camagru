const video = document.getElementById('video');
let height = 0;
let width = 0;
const canvas = document.getElementById('upload-canvas');
const photo = document.getElementById('photo');
const defaultW = 1024;
const defaultH = 768;
video.addEventListener("loadedmetadata", () => {
    height = video.videoHeight;
    width = video.videoWidth;
});
canvas.height = defaultH;
canvas.width = defaultW;

// array of objects with properties: { src, x, y, w, h, image }
let overlays = [];
let baseImage = null;
let selectedOverlay = null;
let interaction = null;

async function startCamera() {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        video.srcObject = stream;
        return stream;
    } catch (err) {
        console.error('Error accessing the camera', err);
    }
    return null;
}

function print_dimensions(canvas) {
    console.log(`height ${canvas.height} width ${canvas.width}`);
}

function setupCanvas(canvas, stream, video) {
    canvas.height = video ? video.videoHeight : defaultH;
    canvas.width = video ? video.videoWidth : defaultW;
    return canvas.getContext("2d");
}

async function setup() {
    const stream = await startCamera();
    return stream;
}

function print_overlays(overlays) {
    Array.from(overlays).forEach(overlay => {
        console.log(`src: ${overlay.src}, x: ${overlay.x}, y: ${overlay.y}, w: ${overlay.w}, h: ${overlay.h}`);
    })
}

const clear = document.getElementById("clear-stickers");
clear.addEventListener('click', () => {
    overlays.length = 0;
    selectedOverlay = null;
    interaction = null;
    renderCanvas();
})

function clamp(value, min, max) {
    return Math.min(Math.max(value, min), max);
}

function fitCanvasToPreview() {
    if (!baseImage || canvas.hidden)
        return;
    const previewWidth = preview.clientWidth;
    const previewHeight = preview.clientHeight;
    if (!previewWidth || !previewHeight)
        return;
    const scale = Math.min(previewWidth / canvas.width, previewHeight / canvas.height);
    canvas.style.position = 'absolute';
    canvas.style.width = `${canvas.width * scale}px`;
    canvas.style.height = `${canvas.height * scale}px`;
    canvas.style.left = `${(previewWidth - canvas.width * scale) / 2}px`;
    canvas.style.top = `${(previewHeight - canvas.height * scale) / 2}px`;
    canvas.style.touchAction = 'none';
}

function drawSelection(ctx, overlay) {
    const handleSize = Math.max(8, Math.min(18, canvas.width * 0.012));
    ctx.save();
    ctx.strokeStyle = '#0d00ff';
    ctx.lineWidth = Math.max(2, canvas.width * 0.002);
    ctx.setLineDash([handleSize, handleSize / 1.5]);
    ctx.strokeRect(overlay.x, overlay.y, overlay.w, overlay.h);
    ctx.setLineDash([]);

    // resize handle (bottom-right)
    ctx.fillStyle = '#0d00ff';
    ctx.fillRect(
        overlay.x + overlay.w - handleSize / 2,
        overlay.y + overlay.h - handleSize / 2,
        handleSize,
        handleSize
    );

    // delete handle (top-right)
    const cx = overlay.x + overlay.w;
    const cy = overlay.y;
    const radius = handleSize * 0.75;
    ctx.beginPath();
    ctx.arc(cx, cy, radius, 0, Math.PI * 2);
    ctx.fillStyle = '#ff2d2d';
    ctx.fill();
    ctx.strokeStyle = '#ffffff';
    ctx.lineWidth = Math.max(1.5, handleSize * 0.15);
    ctx.beginPath();
    ctx.moveTo(cx - radius * 0.45, cy - radius * 0.45);
    ctx.lineTo(cx + radius * 0.45, cy + radius * 0.45);
    ctx.moveTo(cx + radius * 0.45, cy - radius * 0.45);
    ctx.lineTo(cx - radius * 0.45, cy + radius * 0.45);
    ctx.stroke();
    ctx.restore();
}

function renderCanvas(showSelection = true) {
    if (!baseImage || !baseImage.naturalWidth)
        return;
    const ctx = canvas.getContext("2d");
    if (canvas.width !== baseImage.naturalWidth || canvas.height !== baseImage.naturalHeight) {
        canvas.width = baseImage.naturalWidth;
        canvas.height = baseImage.naturalHeight;
    }
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.drawImage(baseImage, 0, 0, canvas.width, canvas.height);
    overlays.forEach(overlay => {
        if (overlay.image && overlay.image.complete && overlay.image.naturalWidth)
            ctx.drawImage(overlay.image, overlay.x, overlay.y, overlay.w, overlay.h);
    });
    if (showSelection && selectedOverlay && overlays.includes(selectedOverlay))
        drawSelection(ctx, selectedOverlay);
}

function showBaseImage(src) {
    const image = new Image();
    image.onload = () => {
        baseImage = image;
        overlays.length = 0;
        selectedOverlay = null;
        interaction = null;
        canvas.width = image.naturalWidth;
        canvas.height = image.naturalHeight;
        canvas.hidden = false;
        canvas.style.display = 'block';
        photo.hidden = true;
        video.hidden = true;
        fitCanvasToPreview();
        renderCanvas();
    };
    image.onerror = () => console.error('Failed to load image', src);
    image.src = src;
}

function getCanvasPoint(event) {
    const rect = canvas.getBoundingClientRect();
    return {
        x: (event.clientX - rect.left) * (canvas.width / rect.width),
        y: (event.clientY - rect.top) * (canvas.height / rect.height)
    };
}

function overlayAt(x, y) {
    for (let i = overlays.length - 1; i >= 0; i--) {
        const overlay = overlays[i];
        if (x >= overlay.x && x <= overlay.x + overlay.w &&
            y >= overlay.y && y <= overlay.y + overlay.h)
            return overlay;
    }
    return null;
}

function getSelectionHandles(overlay) {
    const size = Math.max(8, Math.min(18, canvas.width * 0.012));
    return {
        deleteX: overlay.x + overlay.w,
        deleteY: overlay.y,
        deleteRadius: size * 0.9,
        resizeX: overlay.x + overlay.w,
        resizeY: overlay.y + overlay.h,
        resizeRadius: size
    };
}

function removeSelectedOverlay() {
    if (!selectedOverlay)
        return;
    const index = overlays.indexOf(selectedOverlay);
    if (index !== -1)
        overlays.splice(index, 1);
    selectedOverlay = null;
    interaction = null;
    renderCanvas();
}

function addSticker(src, dropX, dropY) {
    const sticker = new Image();
    sticker.onload = () => {
        const ratio = sticker.naturalHeight / sticker.naturalWidth;
        let w = canvas.width * 0.25;
        let h = w * ratio;
        const maxW = canvas.width * 0.4;
        const maxH = canvas.height * 0.4;
        if (w > maxW) {
            w = maxW;
            h = w * ratio;
        }
        if (h > maxH) {
            h = maxH;
            w = h / ratio;
        }
        const overlay = {
            src,
            x: clamp(dropX - w / 2, 0, Math.max(0, canvas.width - w)),
            y: clamp(dropY - h / 2, 0, Math.max(0, canvas.height - h)),
            w,
            h,
            image: sticker
        };
        overlays.push(overlay);
        selectedOverlay = overlay;
        renderCanvas();
    };
    sticker.onerror = () => console.error('Failed to load sticker', src);
    sticker.src = src;
}

const preview = document.getElementById("preview");
const filters = document.getElementsByClassName("draggable");
Array.from(filters).forEach((filter) => {
    filter.addEventListener("dragstart", (e) => {
        e.dataTransfer.setData("text/plain", filter.dataset.src);
        e.dataTransfer.effectAllowed = "copy";
    });
});

preview.addEventListener("dragover", (e) => {
    e.preventDefault();
    e.dataTransfer.dropEffect = "copy";
});

preview.addEventListener("drop", (e) => {
    e.preventDefault();
    if (!baseImage)
        return;
    const point = getCanvasPoint(e);
    if (point.x < 0 || point.y < 0 || point.x > canvas.width || point.y > canvas.height)
        return;
    const data = e.dataTransfer.getData("text/plain");
    if (data)
        addSticker(data, point.x, point.y);
});

canvas.addEventListener("pointerdown", (e) => {
    if (!baseImage || e.button !== 0)
        return;
    const point = getCanvasPoint(e);

    if (selectedOverlay) {
        const handles = getSelectionHandles(selectedOverlay);
        const deleteDistance = Math.hypot(point.x - handles.deleteX, point.y - handles.deleteY);
        if (deleteDistance <= handles.deleteRadius) {
            removeSelectedOverlay();
            return;
        }
        const onResizeHandle = Math.abs(point.x - handles.resizeX) <= handles.resizeRadius &&
            Math.abs(point.y - handles.resizeY) <= handles.resizeRadius;
        if (onResizeHandle) {
            interaction = {
                mode: 'resize',
                overlay: selectedOverlay,
                startW: selectedOverlay.w,
                startH: selectedOverlay.h,
                startX: point.x,
                startY: point.y
            };
            canvas.setPointerCapture(e.pointerId);
            e.preventDefault();
            return;
        }
    }

    const hit = overlayAt(point.x, point.y);
    if (!hit) {
        selectedOverlay = null;
        renderCanvas();
        return;
    }

    // Bring the selected sticker to the front.
    const index = overlays.indexOf(hit);
    if (index !== overlays.length - 1) {
        overlays.splice(index, 1);
        overlays.push(hit);
    }
    selectedOverlay = hit;
    interaction = {
        mode: 'move',
        overlay: hit,
        offsetX: point.x - hit.x,
        offsetY: point.y - hit.y
    };
    canvas.setPointerCapture(e.pointerId);
    canvas.style.cursor = 'grabbing';
    e.preventDefault();
    renderCanvas();
});

canvas.addEventListener("pointermove", (e) => {
    if (!baseImage)
        return;
    const point = getCanvasPoint(e);

    if (interaction) {
        const overlay = interaction.overlay;
        if (interaction.mode === 'move') {
            overlay.x = clamp(point.x - interaction.offsetX, 0, Math.max(0, canvas.width - overlay.w));
            overlay.y = clamp(point.y - interaction.offsetY, 0, Math.max(0, canvas.height - overlay.h));
        } else {
            const dx = point.x - interaction.startX;
            const dy = point.y - interaction.startY;
            const ratioW = (interaction.startW + dx) / interaction.startW;
            const ratioH = (interaction.startH + dy) / interaction.startH;
            const minScale = Math.max(20 / interaction.startW, 20 / interaction.startH);
            const maxScale = Math.min(
                (canvas.width - overlay.x) / interaction.startW,
                (canvas.height - overlay.y) / interaction.startH
            );
            let scale = Math.max(ratioW, ratioH);
            scale = Math.max(scale, Math.min(minScale, maxScale));
            scale = Math.min(scale, maxScale);
            overlay.w = interaction.startW * scale;
            overlay.h = interaction.startH * scale;
        }
        renderCanvas();
        e.preventDefault();
        return;
    }

    let cursor = 'default';
    if (selectedOverlay) {
        const handles = getSelectionHandles(selectedOverlay);
        const deleteDistance = Math.hypot(point.x - handles.deleteX, point.y - handles.deleteY);
        const onResizeHandle = Math.abs(point.x - handles.resizeX) <= handles.resizeRadius &&
            Math.abs(point.y - handles.resizeY) <= handles.resizeRadius;
        if (deleteDistance <= handles.deleteRadius)
            cursor = 'pointer';
        else if (onResizeHandle)
            cursor = 'nwse-resize';
    }
    if (cursor === 'default' && overlayAt(point.x, point.y))
        cursor = 'move';
    canvas.style.cursor = cursor;
});

function endInteraction(e) {
    if (!interaction)
        return;
    interaction = null;
    canvas.style.cursor = 'default';
    if (e && canvas.hasPointerCapture(e.pointerId))
        canvas.releasePointerCapture(e.pointerId);
    renderCanvas();
}

canvas.addEventListener("pointerup", endInteraction);
canvas.addEventListener("pointercancel", endInteraction);

canvas.addEventListener("dblclick", (e) => {
    if (!baseImage)
        return;
    const point = getCanvasPoint(e);
    const hit = overlayAt(point.x, point.y);
    if (hit) {
        selectedOverlay = hit;
        removeSelectedOverlay();
    }
});

document.addEventListener("keydown", (e) => {
    if (!selectedOverlay || (e.key !== 'Delete' && e.key !== 'Backspace'))
        return;
    const tag = document.activeElement ? document.activeElement.tagName : '';
    if (tag === 'INPUT' || tag === 'TEXTAREA' || tag === 'SELECT')
        return;
    e.preventDefault();
    removeSelectedOverlay();
});

window.addEventListener('resize', fitCanvasToPreview);

// preview.addEventListener("mousemove", (e) => {
//     const rect = preview.getBoundingClientRect();
//     console.log(`mouse at ${e.x - rect.x}, ${e.y - rect.y}`);
//     console.log('rect: ', rect);
// });

function canvasToBlob() {
    renderCanvas(false);
    return new Promise(resolve => canvas.toBlob(resolve, "image/png"));
}

(async() => {
    const captureButton = document.getElementById('captureButton');
    const uploadButton = document.getElementById('upload-capture-button');
    const photoInput = document.getElementById("upload-image");
    const ctx = canvas.getContext("2d");

    // Register this before waiting for the camera so selecting a file always works.
    photoInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            const url = URL.createObjectURL(file);
            photo.src = url;
            showBaseImage(url);
        }
    });

    // Upload the canvas (image + stickers) instead of the original file.
    const uploadForm = photoInput.form;
    if (uploadForm) {
        uploadForm.addEventListener('submit', async (e) => {
            if (!baseImage)
                return;
            e.preventDefault();
            const blob = await canvasToBlob();
            renderCanvas();
            if (!blob)
                return;
            const transfer = new DataTransfer();
            transfer.items.add(new File([blob], "uploaded.png", { type: "image/png" }));
            photoInput.files = transfer.files;
            uploadForm.submit();
        });
    }

    const stream = await startCamera();
    captureButton.addEventListener('click', () => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        video.hidden = true;
        showBaseImage(canvas.toDataURL());
        captureButton.hidden = true;
        uploadButton.hidden = false;
        stream.getTracks()[0].stop();

        uploadButton.addEventListener('click', async (e) => {
            e.preventDefault();
            const blob = await canvasToBlob();
            renderCanvas();
            if (!blob) {
                console.log("no blob");
                return;
            }
            const formData = new FormData();
            formData.append('upload', blob, "uploaded.png");
            fetch('/upload', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                console.log('Success:', data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
            uploadButton.hidden = true;
            captureButton.hidden = false;
        }, { once: true });
    });
})();