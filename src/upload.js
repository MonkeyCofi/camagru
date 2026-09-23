const video = document.getElementById('video');

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

(async() => {
    const stream = await startCamera();
    const canvas = document.getElementById('upload-canvas');
    const photo = document.getElementById('photo');
    const captureButton = document.getElementById('captureButton');
    const uploadButton = document.getElementById('upload-capture-button');
    captureButton.addEventListener('click', () => {
        photo.hidden = false;
        video.hidden = true;
        captureButton.hidden = true;
        uploadButton.hidden = false;
        const width = video.videoWidth;
        const height = video.videoHeight;
        const ctx = canvas.getContext("2d");
        canvas.width = width;
        canvas.height = height;
        const imageWidth = width / 1.05;
        const imageHeight = height / 1.05;
        ctx.fillRect(0, 0, width, height);
        ctx.fill();
        ctx.drawImage(video, 0, 0, imageWidth, imageHeight);
        const formData = new FormData();
        canvas.toBlob((blob) => {
            const url = URL.createObjectURL(blob);
            photo.src = url;
            uploadButton.addEventListener('click', (e) => {
                e.preventDefault();
                console.log("upload was clicked");
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
                uploadButton.hidden = true;
                captureButton.hidden = false;
                
            })
        });
        stream.getTracks()[0].stop();
    });
})();