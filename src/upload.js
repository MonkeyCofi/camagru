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

// array of objects with properties: [src, x, y, w, h]
let overlays = [];

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

function render_overlays(overlays, canvas, ctx, img) {
    let image = new Image();
    image.src = img;
    canvas.width = image.width;
    // canvas.height = image.height;
    canvas.width = image.naturalWidth;
    canvas.height = image.naturalHeight;
    console.log(`canvaswidth ${canvas.width} height ${canvas.height}`);
    ctx.drawImage(image, 0, 0);
    Array.from(overlays).forEach(overlay => {
        console.log('sticker widht: ', overlay.w, 'sticker height: ', overlay.h);
        image = new Image();
        image.width = overlay.w;
        image.height = overlay.h;
        image.src = overlay.src;
        console.log('drawing: ', overlay);
        ctx.drawImage(image, overlay.x, overlay.y, overlay.width, overlay.height);
        // ctx.drawImage(image, overlay.x, overlay.y);
    });
    const url = canvas.toDataURL();
    const photo = document.getElementById("photo");
    photo.src = url;
}

const preview = document.getElementById("preview");
const filters = document.getElementsByClassName("draggable");
Array.from(filters).forEach((filter) => {
    // console.log(filter);
    filter.addEventListener("dragstart", (e) => {
        e.dataTransfer.setData("text/plain", filter.dataset.src);
        e.dataTransfer.effectAllowed = "copy";
    });
});

preview.addEventListener("dragover", (e) => {
    e.preventDefault();
    e.dataTransfer.dropEffect = "copy";
    // console.log(e.x);
});

preview.addEventListener("drop", (e) => {
    e.preventDefault();
    const ctx = canvas.getContext("2d");
    const img = photo;
    const imgSrc = photo.src;
    const stickerCanvas = document.getElementById("upload-canvas");

    const rect = preview.getBoundingClientRect();
    const x = (e.clientX - rect.left) * (stickerCanvas.width / rect.width);
    const y = (e.clientY - rect.top) * (stickerCanvas.height / rect.height);
    console.log('test: ', e.x, 'test: ', e.y);
    const data = e.dataTransfer.getData("text/plain");
    console.log(`placing at ${x}, ${y}`);
    // const overlay = {src: data, x: x, y: y, w: stickerW, h: stickerH}
    const stickerImg = new Image();
    stickerImg.onload = () => {
        const stickerW = stickerCanvas.width * 0.25;
        const stickerH = stickerW * (stickerImg.naturalHeight / stickerImg.naturalWidth);
        console.log('stickerW: ', stickerW, ' stickerH: ', stickerH);
        const overlay = {src: data, x: x, y: y, w: stickerW, h: stickerH}
        overlays.push(overlay);
        render_overlays(overlays, canvas, ctx, imgSrc);
    };
    stickerImg.src = data;
});

// preview.addEventListener("mousemove", (e) => {
//     const rect = preview.getBoundingClientRect();
//     console.log(`mouse at ${e.x - rect.x}, ${e.y - rect.y}`);
//     console.log('rect: ', rect);
// });

(async() => {
    const stream = await startCamera();
    const captureButton = document.getElementById('captureButton');
    const uploadButton = document.getElementById('upload-capture-button');
    const photoInput = document.getElementById("upload-image");
    // const ctx = setupCanvas(canvas, stream, video);
    const ctx = canvas.getContext("2d");
    photoInput.addEventListener('change', (event) => {
        // temporarily open the photo and store it in a blob
        const file = event.target.files[0];
        console.log(file);
        if (file) {
            video.hidden = true;
            photo.hidden = false;
            // canvas.width = video.videoWidth;
            // canvas.height = video.videoHeight;
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            const url = URL.createObjectURL(file);
            photo.src = url;
        }
    })
    captureButton.addEventListener('click', () => {
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
        photo.hidden = false;
        video.hidden = true;
        captureButton.hidden = true;
        uploadButton.hidden = false;
        const formData = new FormData();
        canvas.toBlob((blob) => {
            if (!blob) {
                console.log("no blob");
            }
            const url = URL.createObjectURL(blob);
            photo.src = url;
            uploadButton.addEventListener('click', async (e) => {
                e.preventDefault();
                console.log("upload was clicked");
                formData.append('upload', blob, "uploaded.png");
                // try {
                //     const res = await fetch("/upload", {
                //         method: "POST",
                //         body: formData
                //     });
                //     if (res.ok) {
                //         const data = res.json();
                //         console.log(data);
                //     }
                // } catch (err) {
                //     console.log(err);
                // }

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
                uploadButton.hidden = true;
                captureButton.hidden = false;
            })
        });
        stream.getTracks()[0].stop();
    });
})();