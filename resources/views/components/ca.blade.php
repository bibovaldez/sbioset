<form id="imageForm" action="{{ route('image.upload') }}" method="post" enctype="multipart/form-data"
    class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    @csrf
    @honeypot
    <input type="hidden" class="g-recaptcha" name="recaptcha_token" id="recaptcha_token">
    <div class="text-center w-full max-w-md mx-auto">
        <h1 class="text-4xl font-bold my-6 dark:text-white">BioseT v1.0</h1>
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
            <div class="flex flex-col space-y-4">
                <button id="cameraButton" type="button"
                    class="bg-gray-200 dark:bg-gray-600 p-4 rounded-md hover:bg-gray-300 dark:hover:bg-gray-500 transition duration-300">
                    <div
                        class="border-4 border-dashed border-blue-600 dark:border-blue-400 rounded-md p-6 flex items-center justify-center">
                        <p class="text-gray-600 dark:text-gray-200">Capture Image</p>
                    </div>
                </button>
                <p class="text-gray-600 dark:text-gray-200">OR</p>
                <label for="file"
                    class="block w-full text-center bg-blue-500 dark:bg-blue-700 text-white font-bold py-2 px-4 rounded-md cursor-pointer hover:bg-blue-600 dark:hover:bg-blue-800 transition duration-300">
                    Choose File
                </label>
                <input type="file" name="image" id="file" class="hidden" accept="image/*">
            </div>
        </div>
    </div>

    <div id="cameraModal" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex justify-center items-center hidden z-50">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-lg max-w-xl w-full mx-4">
            <video id="video" autoplay class="rounded-md w-full h-auto max-h-[70vh] object-contain"></video>
            <canvas id="canvas" class="hidden"></canvas>
            <div class="mt-4 flex flex-col sm:flex-row justify-between gap-2">
                <button type="button" id="closeCamera" class="btn btn-blue">Close</button>
                <button type="button" id="captureImage" class="btn btn-indigo">Capture</button>
            </div>
        </div>
    </div>

    <div id="imagePreviewContainer"
        class="fixed inset-0 bg-gray-800 bg-opacity-75 flex justify-center items-center hidden z-50">
        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-lg relative max-w-xl w-full mx-4">
            <button type="button" id="cancelImage"
class="absolute top-2 right-2 bg-red-600 dark:bg-red-700 text-white p-2 rounded-full hover:bg-red-700 dark:hover:bg-red-800 transition duration-300">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                    stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <img id="preview" class="mx-auto rounded-md w-full max-w-xs md:max-w-md lg:max-w-lg"
                alt="Image preview" />
            <div class="flex justify-between mt-4">
                    <button id="retakeButton" type="button"
                        class="bg-yellow-600 dark:bg-yellow-700 text-white px-4 py-2 hidden rounded-md">Capture</button>
                    <button id="rechooseButton" type="button"
                        class="bg-blue-600 dark:bg-blue-700 text-white px-4 py-2 hidden rounded-md">Change</button>
                    <button type="submit"
                        class="bg-green-600 dark:bg-green-700 text-white px-4 py-2 rounded-md">Upload</button>
                </div>
            </div>
        </div>
    </div>

    <div class="mt-4">
        <section id="uploaded-area" class="scroll-smooth w-full max-w-md mx-auto"></section>
    </div>
    <div id="progress-area" class="mt-4"></div>
</form>



@push('captured_image')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const elements = {
                form: document.getElementById("imageForm"),
                video: document.getElementById("video"),
                canvas: document.getElementById("canvas"),
                cameraModal: document.getElementById("cameraModal"),
                preview: document.getElementById("preview"),
                imagePreviewContainer: document.getElementById("imagePreviewContainer"),
                fileInput: document.getElementById("file"),
                retakeButton: document.getElementById("retakeButton"),
                rechooseButton: document.getElementById("rechooseButton"),
                progressArea: document.getElementById("progress-area"),
                uploadedArea: document.getElementById("uploaded-area"),
                cameraButton: document.getElementById("cameraButton"),
                wrapper: document.getElementById("wrapper"),
                closeCamera: document.getElementById("closeCamera"),
                captureImage: document.getElementById("captureImage"),
                cancelImage: document.getElementById("cancelImage"),
            };

            const context = elements.canvas.getContext("2d");
            let userClicked = false;
            let isUploading = false;
            let uploadDebounceTimer;

            const isMobileDevice = () => /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
                navigator.userAgent);

            async function toggleCamera(action) {
                if (action === "start") {
                    elements.imagePreviewContainer.classList.add("hidden");
                    userClicked = true;
                    try {
                        const stream = await navigator.mediaDevices.getUserMedia({
                            video: {
                                facingMode: isMobileDevice() ? "environment" : "user"
                            }
                        });
                        elements.video.srcObject = stream;
                        elements.cameraModal.classList.remove("hidden");
                    } catch (error) {
                        showAlert("Error", "Camera access denied. Please allow camera access.", "error");
                    }
                } else if (action === "stop") {
                    const stream = elements.video.srcObject;
                    if (stream) {
                        stream.getTracks().forEach((track) => track.stop());
                    }
                    elements.video.srcObject = null;
                    elements.cameraModal.classList.add("hidden");
                    userClicked = false;
                }
            }

            function captureImage() {
                elements.canvas.width = elements.video.videoWidth;
                elements.canvas.height = elements.video.videoHeight;
                context.drawImage(elements.video, 0, 0, elements.canvas.width, elements.canvas.height);

                elements.canvas.toBlob((blob) => {
                    const file = new File([blob], "captured_image.png", {
                        type: "image/png"
                    });
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(file);
                    elements.fileInput.files = dataTransfer.files;
                    elements.preview.src = URL.createObjectURL(file);
                    showImagePreview();
                    toggleCamera("stop");
                }, "image/png");
            }

            function previewImage(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = () => {
                        elements.preview.src = reader.result;
                        showImagePreview();
                    };
                    reader.readAsDataURL(file);
                    elements.rechooseButton.classList.remove("hidden");
                }
            }

            function showImagePreview() {
                if (userClicked) {
                    elements.retakeButton.classList.remove("hidden");
                }
                elements.imagePreviewContainer.classList.remove("hidden");
            }

            function cancelImage() {
                elements.imagePreviewContainer.classList.add("hidden");
                elements.fileInput.value = "";
                elements.preview.src = "";
                elements.retakeButton.classList.add("hidden");
                elements.rechooseButton.classList.add("hidden");
                userClicked = false;
            }

            async function handleFormSubmit(event) {
                event.preventDefault();
                if (isUploading) return;

                clearTimeout(uploadDebounceTimer);
                uploadDebounceTimer = setTimeout(async () => {
                    isUploading = true;
                    try {
                        const token = await grecaptcha.execute(
                            '{{ config('services.recaptcha.site_key') }}', {
                                action: 'register'
                            });
                        document.getElementById("recaptcha_token").value = token;

                        const formData = new FormData(elements.form);
                        const originalImage = formData.get("image");
                        const optimizedImage = await optimizeImage(originalImage);
                        formData.set("image", optimizedImage);

                        const imageSize = optimizedImage.size;
                        const fileSize = imageSize >= 1048576 ?
                            `${(imageSize / 1048576).toFixed(2)} MB` :
                            `${(imageSize / 1024).toFixed(2)} KB`;

                        if (typeof Livewire !== 'undefined') {
                            Livewire.emit('uploadImage', formData);
                        } else {
                            await uploadWithXHR(formData, fileSize);
                        }

                        cancelImage();
                    } catch (error) {
                        console.error("Upload error:", error);
                        isUploading = false;
                        showAlert("Error", "Upload failed. Please try again.", "error");
                    }
                }, 500); // Increased debounce time to 500ms to further prevent spamming
            }

            async function uploadWithXHR(formData, fileSize) {
                return new Promise((resolve, reject) => {
                    const xhr = new XMLHttpRequest();
                    xhr.open("POST", elements.form.action, true);
                    xhr.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    xhr.upload.addEventListener("progress", updateProgress);
                    xhr.addEventListener("readystatechange", () => handleReadyStateChange(xhr,
                        fileSize));
                    xhr.onerror = () => {
                        handleUploadError();
                        reject(new Error("XHR upload failed"));
                    };
                    xhr.onload = () => resolve();
                    xhr.send(formData);
                });
            }
            function updateProgress(event) {
                setUIState(true);
                const {
                    loaded,
                    total
                } = event;
                const fileLoaded = Math.floor((loaded / total) * 100);
                Swal.fire({
                    title: "Uploading",
                    text: `Upload progress: ${fileLoaded}%`,
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false
                });
            }

            function handleReadyStateChange(xhr, fileSize) {
                if (xhr.readyState === XMLHttpRequest.DONE) {
                    elements.progressArea.innerHTML = "";
                    setUIState(false);
                    isUploading = false;

                    const {
                        icon,
                        text,
                        color
                    } = getUploadStatus(xhr.status);
                    displayUploadResult(icon, text, color, fileSize);

                    const alertConfig = getAlertConfig(xhr.status);
                    showAlert(alertConfig.title, alertConfig.text, alertConfig.icon);
                }
            }

            const uploadStatusMap = {
                404: {
                    icon: "exclamation-circle",
                    text: "No chicken detected",
                    color: "red"
                },
                201: {
                    icon: "check",
                    text: "Image Uploaded",
                    color: "green"
                },
                202: {
                    icon: "exclamation-triangle",
                    text: "Image Required",
                    color: "red"
                },
                204: {
                    icon: "exclamation-triangle",
                    text: "Invalid Image Format",
                    color: "red"
                },
                413: {
                    icon: "exclamation-triangle",
                    text: "File Too Large",
                    color: "red"
                },
                500: {
                    icon: "times",
                    text: "Upload Failed",
                    color: "red"
                }
            };

            const getUploadStatus = (status) => uploadStatusMap[status] || uploadStatusMap[500];

            const alertConfigMap = {
                404: {
                    title: "Warning",
                    text: "No chicken detected",
                    icon: "warning"
                },
                201: {
                    title: "Success",
                    text: "Image uploaded successfully",
                    icon: "success"
                },
                202: {
                    title: "Warning",
                    text: "Image is required",
                    icon: "error"
                },
                204: {
                    title: "Warning",
                    text: "Invalid image format",
                    icon: "error"
                },
                413: {
                    title: "Warning",
                    text: "File is too large",
                    icon: "warning"
                },
                500: {
                    title: "Failed",
                    text: "Upload failed. Please try again later.",
                    icon: "error"
                }
            };

            const getAlertConfig = (status) => alertConfigMap[status] || alertConfigMap[500];

            function displayUploadResult(icon, text, color, fileSize) {
                const html = `
            <li class="list-none column-2 bg-white p-3 rounded-lg shadow-lg">
                <div class="content flex flex-col justify-center">
                    <div class="details flex justify-between items-center">
                        <div class="details ml-4 flex flex-col">
                            <span class="name"><i class="fas fa-image text-${color}-500 text-xl"></i> ${text}</span>
                            <span class="size">${fileSize}</span>
                        </div>
                        <i class="fas fa-${icon} text-${color}-500"></i>
                    </div>
                </div>
            </li>`;
                elements.uploadedArea.insertAdjacentHTML("afterbegin", html);
            }

            function showAlert(title, text, icon) {
                Swal.fire({
                    title,
                    text,
                    icon,
                    confirmButtonText: "OK",
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                });
            }

            function setUIState(isUploading) {
                elements.cameraButton.disabled = isUploading;
                elements.fileInput.disabled = isUploading;
                elements.wrapper.classList.toggle("opacity-50", isUploading);
            }

            function handleUploadError() {
                elements.progressArea.innerHTML = "";
                setUIState(false);
                isUploading = false;
                showAlert("Failed", "Image upload failed. Please try again.", "error");
            }

            async function optimizeImage(file) {
                return new Promise((resolve) => {
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const img = new Image();
                        img.onload = () => {
                            const canvas = document.createElement('canvas');
                            const ctx = canvas.getContext('2d');
                            const maxWidth = 1920;
                            const maxHeight = 1080;
                            let {
                                width,
                                height
                            } = img;

                            if (width > height) {
                                if (width > maxWidth) {
                                    height *= maxWidth / width;
                                    width = maxWidth;
                                }
                            } else {
                                if (height > maxHeight) {
                                    width *= maxHeight / height;
                                    height = maxHeight;
                                }
                            }

                            canvas.width = width;
                            canvas.height = height;
                            ctx.drawImage(img, 0, 0, width, height);

                            canvas.toBlob((blob) => {
                                resolve(new File([blob], file.name, {
                                    type: 'image/jpeg',
                                    lastModified: Date.now()
                                }));
                            }, 'image/jpeg', 0.7);
                        };
                        img.src = e.target.result;
                    };
                    reader.readAsDataURL(file);
                });
            }

            // Drag and drop functionality
            const dropZone = elements.wrapper;
            ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, preventDefaults, false);
            });

            function preventDefaults(e) {
                e.preventDefault();
                e.stopPropagation();
            }

            ['dragenter', 'dragover'].forEach(eventName => {
                dropZone.addEventListener(eventName, highlight, false);
            });

            ['dragleave', 'drop'].forEach(eventName => {
                dropZone.addEventListener(eventName, unhighlight, false);
            });

            function highlight() {
                dropZone.classList.add('border-blue-500', 'border-4');
            }

            function unhighlight() {
                dropZone.classList.remove('border-blue-500', 'border-4');
            }

            dropZone.addEventListener('drop', handleDrop, false);

            function handleDrop(e) {
                const dt = e.dataTransfer;
                const file = dt.files[0];
                elements.fileInput.files = dt.files;
                previewImage({
                    target: {
                        files: [file]
                    }
                });
            }

            // Event listeners
            elements.cancelImage.addEventListener("click", cancelImage);
            elements.form.addEventListener("submit", handleFormSubmit);
            elements.cameraButton.addEventListener("click", () => toggleCamera("start"));
            elements.closeCamera.addEventListener("click", () => toggleCamera("stop"));
            elements.captureImage.addEventListener("click", captureImage);
            elements.fileInput.addEventListener("change", previewImage);
            elements.retakeButton.addEventListener("click", () => toggleCamera("start"));
            elements.rechooseButton.addEventListener("click", () => elements.fileInput.click());

            // Livewire integration
            if (typeof Livewire !== 'undefined') {
                Livewire.on('imageUploaded', (data) => {
                    isUploading = false;
                    setUIState(false);
                    const {
                        status,
                        fileSize
                    } = data;
                    const {
                        icon,
                        text,
                        color
                    } = getUploadStatus(status);
                    displayUploadResult(icon, text, color, fileSize);
                    const alertConfig = getAlertConfig(status);
                    showAlert(alertConfig.title, alertConfig.text, alertConfig.icon);
                });

                Livewire.on('uploadError', (error) => {
                    handleUploadError();
                });
            }
        });
    </script>
@endpush

{{-- CSS --}}
<style>
    #uploaded-area.onprogress {
        max-height: 150px;
    }

    #uploaded-area .row .content {
        display: flex;
        align-items: center;
    }

    #uploaded-area .row .details {
        display: flex;
        margin-left: 15px;
        flex-direction: column;
    }

    #uploaded-area .row .details .size {
        color: #404040;
        font-size: 11px;
    }

    #uploaded-area i.fa-check {
        font-size: 16px;
    }
</style>
