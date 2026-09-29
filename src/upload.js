const video = document.getElementById('video');
let height = 0;
let width = 0;
const canvas = document.getElementById('upload-canvas');
const photo = document.getElementById('photo');
const preview = document.getElementById('preview');

video.addEventListener("loadedmetadata", () => {
    height = video.videoHeight;
    width = video.videoWidth;
});

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

function setupCanvas(canvas, stream, video) {
    canvas.height = height || 500;
    canvas.width = width || 500;
    return canvas.getContext("2d");
}

// array of objects with the properties [src, x, y, w, h, el] where x, y, w, h
// are in CSS pixels relative to the preview box
let overlays = [];

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
    const src = e.dataTransfer.getData("text/plain");
    if (!src) return;
    const rect = preview.getBoundingClientRect();
    const cx = e.clientX - rect.left;
    const cy = e.clientY - rect.top;
    const el = document.createElement("img");
    el.className = "filter-overlay";
    el.src = src;
    preview.appendChild(el);
    const overlay = { src, x: 0, y: 0, w: 0, h: 0, el };
    overlays.push(overlay);
    el.onload = () => {
        const w = preview.clientWidth * 0.2;
        const h = w * el.naturalHeight / el.naturalWidth;
        overlay.x = Math.min(Math.max(cx - w / 2, 0), preview.clientWidth - w);
        overlay.y = Math.min(Math.max(cy - h / 2, 0), preview.clientHeight - h);
        overlay.w = w;
        overlay.h = h;
        el.style.left = overlay.x + "px";
        el.style.top = overlay.y + "px";
        el.style.width = overlay.w + "px";
        el.style.height = overlay.h + "px";
    };
});

function composite() {
    const ctx = canvas.getContext("2d");
    ctx.clearRect(0, 0, canvas.width, canvas.height);
    ctx.drawImage(photo, 0, 0, canvas.width, canvas.height);
    const scaleX = canvas.width / preview.clientWidth;
    const scaleY = canvas.height / preview.clientHeight;
    overlays.forEach((overlay) => {
        ctx.drawImage(overlay.el, overlay.x * scaleX, overlay.y * scaleY, overlay.w * scaleX, overlay.h * scaleY);
    });
}

(async() => {
    const stream = await startCamera();
    const captureButton = document.getElementById('captureButton');
    const uploadButton = document.getElementById('upload-capture-button');
    const photoInput = document.getElementById("upload-image");
    const ctx = setupCanvas(canvas, stream, video);
    photoInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            video.hidden = true;
            photo.hidden = false;
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            photo.src = URL.createObjectURL(file);
        }
    });
    captureButton.addEventListener('click', () => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        photo.src = canvas.toDataURL();
        photo.hidden = false;
        video.hidden = true;
        captureButton.hidden = true;
        uploadButton.hidden = false;
        stream.getTracks()[0].stop();
    });
    uploadButton.addEventListener('click', (e) => {
        e.preventDefault();
        composite();
        canvas.toBlob((blob) => {
            if (!blob) {
                console.error("no blob");
                return;
            }
            const formData = new FormData();
            formData.append('upload', blob, "uploaded.png");
            fetch('/upload', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                console.log('Success:', data);
            })
            .catch(error => {
                console.error('Error:', error);
            });
            overlays.forEach((overlay) => overlay.el.remove());
            overlays = [];
            uploadButton.hidden = true;
            captureButton.hidden = false;
        });
    });
})();