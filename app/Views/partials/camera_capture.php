<style>
    .camera-capture {
        border: 1px solid #dee2e6;
        border-radius: 0.5rem;
        margin-top: 0.75rem;
        padding: 0.75rem;
    }

    .camera-capture__media,
    .camera-capture__preview {
        background: #f8f9fa;
        border-radius: 0.375rem;
        display: none;
        margin-top: 0.75rem;
        max-height: 260px;
        object-fit: contain;
        width: 100%;
    }

    .camera-capture__actions {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('[data-camera-capture]').forEach(function (cameraBox) {
            var input = document.getElementById(cameraBox.dataset.input);
            var startButton = cameraBox.querySelector('[data-camera-start]');
            var snapButton = cameraBox.querySelector('[data-camera-snap]');
            var stopButton = cameraBox.querySelector('[data-camera-stop]');
            var video = cameraBox.querySelector('[data-camera-video]');
            var canvas = cameraBox.querySelector('[data-camera-canvas]');
            var preview = cameraBox.querySelector('[data-camera-preview]');
            var status = cameraBox.querySelector('[data-camera-status]');
            var stream = null;

            if (!input || !navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                if (startButton) {
                    startButton.disabled = true;
                }
                if (status) {
                    status.textContent = 'Camera capture is not supported in this browser. You can still choose an image file.';
                }
                return;
            }

            function setStatus(message, isError) {
                if (!status) {
                    return;
                }

                status.textContent = message || '';
                status.classList.toggle('text-danger', !!isError);
                status.classList.toggle('text-muted', !isError);
            }

            function stopCamera() {
                if (stream) {
                    stream.getTracks().forEach(function (track) {
                        track.stop();
                    });
                    stream = null;
                }

                video.pause();
                video.srcObject = null;
                video.style.display = 'none';
                snapButton.style.display = 'none';
                stopButton.style.display = 'none';
                startButton.style.display = '';
            }

            startButton.addEventListener('click', function () {
                navigator.mediaDevices.getUserMedia({
                    video: { facingMode: 'environment' },
                    audio: false
                }).then(function (mediaStream) {
                    stream = mediaStream;
                    video.srcObject = mediaStream;
                    video.style.display = 'block';
                    preview.style.display = 'none';
                    snapButton.style.display = '';
                    stopButton.style.display = '';
                    startButton.style.display = 'none';
                    setStatus('Camera ready. Capture when the image is clear.', false);
                    return video.play();
                }).catch(function () {
                    setStatus('Unable to access camera. Please allow camera permission or choose an image file.', true);
                });
            });

            snapButton.addEventListener('click', function () {
                if (!stream || !video.videoWidth) {
                    setStatus('Camera is not ready yet. Please try again.', true);
                    return;
                }

                canvas.width = video.videoWidth;
                canvas.height = video.videoHeight;
                canvas.getContext('2d').drawImage(video, 0, 0, canvas.width, canvas.height);

                canvas.toBlob(function (blob) {
                    if (!blob) {
                        setStatus('Could not capture image. Please try again.', true);
                        return;
                    }

                    var fileName = input.id + '-' + Date.now() + '.jpg';
                    var file = new File([blob], fileName, { type: 'image/jpeg' });
                    var transfer = new DataTransfer();
                    transfer.items.add(file);
                    input.files = transfer.files;
                    input.dispatchEvent(new Event('change', { bubbles: true }));

                    preview.src = URL.createObjectURL(blob);
                    preview.style.display = 'block';
                    setStatus('Image captured and ready to save.', false);
                    stopCamera();
                }, 'image/jpeg', 0.92);
            });

            stopButton.addEventListener('click', function () {
                stopCamera();
                setStatus('', false);
            });

            input.addEventListener('change', function () {
                var label = input.closest('.custom-file') ? input.closest('.custom-file').querySelector('.custom-file-label') : null;
                if (label && input.files.length) {
                    label.textContent = input.files[0].name;
                }
            });
        });
    });
</script>
