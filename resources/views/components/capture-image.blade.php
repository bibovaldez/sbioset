<form id="imageForm" action="{{ route('image.upload') }}" method="post" enctype="multipart/form-data">
    @csrf
    @honeypot
    <input type="hidden" class="g-recaptcha" name="recaptcha_token" id="recaptcha_token">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="text-center w-full max-w-md mx-auto">
            <h1 class="text-4xl font-bold mt-3 mb-1 dark:text-white">BioseT v1.0</h1>
            <div id="wrapper" class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-lg">
                <div class="flex flex-col space-y-4">
                    <button id="cameraButton" type="button" class="bg-gray-200 dark:bg-gray-600 p-4 rounded-md">
                        <div
                            class="border-4 border-dashed border-blue-600 dark:border-blue-400 rounded-md p-6 flex items-center justify-center">
                            <p class="text-gray-600 dark:text-gray-200">Capture Image</p>
                        </div>
                    </button>
                    <div>
                        <p class="text-gray-600 dark:text-gray-200">OR</p>
                    </div>
                    <label for="file"
                        class="block w-full text-center bg-blue-500 dark:bg-blue-700 text-white font-bold py-2 px-4 rounded-md cursor-pointer hover:bg-blue-600 dark:hover:bg-blue-800">
                        Choose File
                    </label>
                    <input type="file" name="image" id="file" class="hidden" accept="image/*">
                    <div id="cameraModal"
                        class="fixed inset-0 bg-gray-800 bg-opacity-75 flex justify-center items-center hidden">
                        <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-lg max-w-xl w-full mx-4">
                            <video id="video" autoplay
                                class="rounded-md w-full h-auto max-h-[70vh] object-contain"></video>
                            <canvas id="canvas" class="hidden"></canvas>
                            <div class="mt-4 flex flex-col sm:flex-row justify-between gap-2">
                                <button type="button" id="closeCamera"
                                    class="bg-blue-600 dark:bg-blue-700 text-white px-4 py-2 rounded-md w-full sm:w-auto">Close</button>
                                <button type="button" id="captureImage"
                                    class="bg-indigo-600 dark:bg-indigo-700 text-white px-4 py-2 rounded-md w-full sm:w-auto">Capture</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="imagePreviewContainer"
            class="fixed inset-0 bg-gray-800 bg-opacity-75 flex justify-center items-center hidden z-50 ma">
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-lg relative max-w-xl w-full mx-4">
                <button type="button" id="cancelImage"
                    class="absolute top-2 right-2 bg-red-600 dark:bg-red-700 text-white p-2 rounded-full hover:bg-red-700 dark:hover:bg-red-800 transition duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24"
                        stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <img id="preview" class="mx-auto rounded-md w-full max-w-xs md:max-w-md lg:max-w-lg" />
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
        {{-- upload area --}}
        <div class="mt-4">
            {{-- <h2 class="text-2xl font-bold text-center dark:text-white">Uploaded Images</h2> --}}
            <section id="uploaded-area" class="scroll-smooth w-full max-w-md mx-auto"></section>
        </div>
        {{-- Add progress area --}}
        <!-- Central Progress Area Modal -->
        <div id="progress-area-wrapper"
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50 hidden">
            <div class="relative w-96">
                <div id="progress-area" class="bg-white rounded-lg p-6 w-96 shadow-lg"></div>
            </div>
        </div>

    </div>
</form>

{{-- JavaScript --}}
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
        const isMobileDevice = () => /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(
            navigator.userAgent);

        async function compressImage(file, maxWidth = 1024, maxHeight = 1024, quality = 0.8) {
            return new Promise((resolve) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = (event) => {
                    const img = new Image();
                    img.src = event.target.result;
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        let width = img.width;
                        let height = img.height;

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
                        const ctx = canvas.getContext('2d');
                        ctx.drawImage(img, 0, 0, width, height);

                        canvas.toBlob((blob) => {
                            resolve(new File([blob], file.name, {
                                type: 'image/jpeg',
                                lastModified: Date.now()
                            }));
                        }, 'image/jpeg', quality);
                    };
                };
            });
        }

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

            elements.canvas.toBlob(async function(blob) {
                const file = new File([blob], "captured_image.jpg", {
                    type: "image/jpeg",
                });
                const optimizedFile = await compressImage(file);
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(optimizedFile);
                elements.fileInput.files = dataTransfer.files;
                elements.preview.src = URL.createObjectURL(optimizedFile);
                showImagePreview();
                toggleCamera("stop");
            }, 'image/jpeg');
        }

        async function previewImage(event) {
            const file = event.target.files[0];
            if (file) {
                const optimizedFile = await compressImage(file);
                const reader = new FileReader();
                reader.onload = () => {
                    elements.preview.src = reader.result;
                    showImagePreview();
                };
                reader.readAsDataURL(optimizedFile);
                elements.rechooseButton.classList.remove("hidden");

                // Replace the original file with the optimized one
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(optimizedFile);
                elements.fileInput.files = dataTransfer.files;
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
            isUploading = true;

            grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', {
                    action: 'register'
                })
                .then((token) => {
                    document.getElementById("recaptcha_token").value = token;
                    const formData = new FormData(elements.form);
                    const originalImage = formData.get("image");

                    const fileSize = originalImage.size >= 1048576 ?
                        `${(originalImage.size / 1048576).toFixed(2)} MB` :
                        `${(originalImage.size / 1024).toFixed(2)} KB`;
                    // if image size is greater than 10MB show upload status as 413
                    if (originalImage.size > 10485760) {
                        const {
                            icon,
                            text,
                            color
                        } = getUploadStatus(413);
                        displayUploadResult(icon, text, color, fileSize);
                        showAlert("Warning", "The file is too large, please try again", "warning");
                        isUploading = false;
                        // reset evrything
                        cancelImage();
                        return;
                    }


                    const xhr = new XMLHttpRequest();
                    xhr.open("POST", elements.form.action, true);
                    xhr.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}");
                    xhr.upload.addEventListener("progress", updateProgress);
                    xhr.addEventListener("readystatechange", () => handleReadyStateChange(xhr,
                        fileSize));
                    xhr.onerror = handleUploadError;
                    xhr.send(formData);
                    cancelImage();
                })
                .catch(error => {
                    console.error("reCAPTCHA error:", error);
                    isUploading = false;
                    showAlert("Error", "Failed to verify reCAPTCHA. Please try again.", "error");
                });
        }


        function updateProgress(event) {
            setUIState(true);
            const {
                loaded,
                total
            } = event;
            const fileLoaded = Math.floor((loaded / total) * 100);
            // Select the modal wrapper and progress area
            const progressAreaWrapper = document.getElementById("progress-area-wrapper");
            const progressArea = document.getElementById("progress-area");
            // Show the modal by removing "hidden" class
            progressAreaWrapper.style.display = 'flex';
            // Update the progress area content
            progressArea.innerHTML = `
                    <div class="text-lg font-semibold mb-4">Uploading... ${fileLoaded}%</div>
                    <div class="progress-bar-container">
                        <div class="progress-bar" style="width: ${fileLoaded}%;"></div>
                    </div>`;
            // Hide the modal once upload is complete (100%)
            if (fileLoaded === 100) {
                setTimeout(() => {
                    progressAreaWrapper.style.display = 'none';
                }, 1000); // Hide after 1 second
                // show sweet alert deteing chicken
                Swal.fire({
                    title: 'Detecting Chicken',
                    text: 'Please wait while we detect the chicken status',
                    imageUrl: '{{ asset('images/loading.gif') }}',
                    showConfirmButton: false,
                    allowOutsideClick: false,
                    allowEscapeKey: false,
                });
            }
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

        function getUploadStatus(status) {
            const statusMap = {
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
                    text: "Upload Failed, Please try again later.",
                    color: "red"
                }
            };
            return statusMap[status] || statusMap[500];
        }

        function getAlertConfig(status) {
            const configMap = {
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
                    text: "Invalid image format, please try again",
                    icon: "error"
                },
                413: {
                    title: "Warning",
                    text: "The file is too large, please try again",
                    icon: "warning"
                },
                500: {
                    title: "Failed",
                    text: "Upload Failed, Please try again later.",
                    icon: "error"
                }
            };
            return configMap[status] || configMap[500];
        }

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
            showAlert("Failed", "Image upload failed, please try again", "error");
        }

        // Event Listeners
        elements.cameraButton.addEventListener("click", () =>
            toggleCamera("start")
        );
        elements.closeCamera.addEventListener("click", () => toggleCamera("stop"));
        elements.captureImage.addEventListener("click", captureImage);
        elements.fileInput.addEventListener("change", previewImage);
        elements.retakeButton.addEventListener("click", () =>
            toggleCamera("start")
        );
        elements.rechooseButton.addEventListener("click", () =>
            elements.fileInput.click()
        );
        elements.cancelImage.addEventListener("click", cancelImage);
        elements.form.addEventListener("submit", handleFormSubmit);
    });
</script>

{{-- CSS --}}
<style>
    /* Wrapper for modal background */
    #progress-area-wrapper {
        display: none;
        /* Initially hidden */
    }

    /* Progress area styling */
    #progress-area {
        width: 80%;
        /* Set a fixed width for the progress area */
        max-width: 400px;
        /* Limit the max width */
        background-color: white;
        /* White background for progress bar container */
        border-radius: 8px;
        padding: 20px;
        box-shadow: 0px 2px 10px rgba(0, 0, 0, 0.1);
        /* Shadow for the box */
        text-align: center;
        /* Center align text and content */
    }

    /* Progress bar container */
    .progress-bar-container {
        background-color: rgba(229, 231, 235, 1);
        /* Tailwind bg-gray-200 equivalent */
        height: 10px;
        border-radius: 5px;
        overflow: hidden;
    }

    /* Progress bar itself */
    .progress-bar {
        background-color: #48BB78;
        /* Tailwind bg-green-500 equivalent */
        height: 100%;
        width: 0;
        border-radius: 5px;
        transition: width 0.4s ease;
        /* Smooth transition */
    }


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
