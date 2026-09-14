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
    const canvas = document.getElementById('canvas');
    const photo = document.getElementById('photo');
    const captureButton = document.getElementById('captureButton');
    const uploadButton = document.getElementById('upload-capture-button');
    captureButton.addEventListener('click', () => {
        photo.hidden = false;
        video.hidden = true;
        captureButton.hidden = true;
        uploadButton.hidden = false;
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);
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