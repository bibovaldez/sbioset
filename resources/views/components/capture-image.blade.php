{{-- HTML --}}
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
                        <div class="border-4 border-dashed border-blue-600 dark:border-blue-400 rounded-md p-6 flex items-center justify-center">
                            <p class="text-gray-600 dark:text-gray-200">Capture Image</p>
                        </div>
                    </button>
                    <div>
                        <p class="text-gray-600 dark:text-gray-200">OR</p>
                    </div>
                    <label for="file" class="block w-full text-center bg-blue-500 dark:bg-blue-700 text-white font-bold py-2 px-4 rounded-md cursor-pointer hover:bg-blue-600 dark:hover:bg-blue-800">
                        Choose File
                    </label>
                    <input type="file" name="image" id="file" class="hidden" accept="image/*">
                </div>
            </div>
        </div>
        <div id="imagePreviewContainer" class="fixed inset-0 bg-gray-800 bg-opacity-75 flex justify-center items-center hidden z-50">
            <div class="bg-white dark:bg-gray-800 p-4 rounded-lg shadow-lg relative max-w-xl w-full mx-4">
                <button type="button" id="cancelImage" class="absolute top-2 right-2 bg-red-600 dark:bg-red-700 text-white p-2 rounded-full hover:bg-red-700 dark:hover:bg-red-800 transition duration-300">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
                <img id="preview" class="mx-auto rounded-md w-full max-w-xs md:max-w-md lg:max-w-lg" />
                <div class="flex justify-between mt-4">
                    <button id="retakeButton" type="button" class="bg-yellow-600 dark:bg-yellow-700 text-white px-4 py-2 hidden rounded-md">Capture</button>
                    <button id="rechooseButton" type="button" class="bg-blue-600 dark:bg-blue-700 text-white px-4 py-2 hidden rounded-md">Change</button>
                    <button type="submit" class="bg-green-600 dark:bg-green-700 text-white px-4 py-2 rounded-md">Upload</button>
                </div>
            </div>
        </div>
        <div class="mt-4">
            <section id="progress-area" class="w-full max-w-md mx-auto"></section>
            <section id="uploaded-area" class="scroll-smooth w-full max-w-md mx-auto"></section>
        </div>
    </div>
</form>

{{-- JavaScript --}}
<script>
document.addEventListener("DOMContentLoaded", () => {
    const elements = {
        form: document.getElementById("imageForm"),
        video: document.createElement("video"),
        canvas: document.createElement("canvas"),
        preview: document.getElementById("preview"),
        imagePreviewContainer: document.getElementById("imagePreviewContainer"),
        fileInput: document.getElementById("file"),
        retakeButton: document.getElementById("retakeButton"),
        rechooseButton: document.getElementById("rechooseButton"),
        progressArea: document.getElementById("progress-area"),
        uploadedArea: document.getElementById("uploaded-area"),
        cameraButton: document.getElementById("cameraButton"),
        wrapper: document.getElementById("wrapper"),
        cancelImage: document.getElementById("cancelImage"),
    };
    const context = elements.canvas.getContext("2d");
    let userClicked = false;
    let isUploading = false;

    function toggleCamera(action) {
        if (action === "start") {
            elements.imagePreviewContainer.classList.add("hidden");
            userClicked = true;
            navigator.mediaDevices.getUserMedia({ video: true })
                .then((stream) => {
                    elements.video.srcObject = stream;
                    elements.video.play();
                    document.body.appendChild(elements.video);
                })
                .catch(() => {
                    showAlert("Error", "Camera cannot be accessed, please allow access to the camera", "error");
                });
        } else if (action === "stop") {
            const stream = elements.video.srcObject;
            const tracks = stream.getTracks();
            tracks.forEach((track) => track.stop());
            elements.video.srcObject = null;
            document.body.removeChild(elements.video);
            userClicked = false;
        }
    }

    function captureImage() {
        elements.canvas.width = elements.video.videoWidth;
        elements.canvas.height = elements.video.videoHeight;
        context.drawImage(elements.video, 0, 0, elements.canvas.width, elements.canvas.height);

        elements.canvas.toBlob((blob) => {
            const file = new File([blob], "captured_image.png", { type: "image/png" });
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

    function handleFormSubmit(event) {
        event.preventDefault();
        if (isUploading) return;
        isUploading = true;

        grecaptcha.execute('{{ config('services.recaptcha.site_key') }}', { action: 'register' })
            .then((token) => {
                document.getElementById("recaptcha_token").value = token;
                const formData = new FormData(elements.form);
                const imageSize = formData.get("image").size;
                const fileSize = imageSize >= 1048576 ? `${(imageSize / 1048576).toFixed(2)} MB` : `${(imageSize / 1024).toFixed(2)} KB`;

                const xhr = new XMLHttpRequest();
                xhr.open("POST", elements.form.action, true);
                xhr.setRequestHeader("X-CSRF-TOKEN", "{{ csrf_token() }}");
                xhr.upload.addEventListener("progress", updateProgress);
                xhr.addEventListener("readystatechange", () => handleReadyStateChange(xhr, fileSize));
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
        const { loaded, total } = event;
        const fileLoaded = Math.floor((loaded / total) * 100);

        Swal.fire({
            title: "Uploading",
            text: 'Please wait while your file uploads...',
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

            const { icon, text, color } = getUploadStatus(xhr.status);
            displayUploadResult(icon, text, color, fileSize);

            const alertConfig = getAlertConfig(xhr.status);
            showAlert(alertConfig.title, alertConfig.text, alertConfig.icon);
        }
    }

    function getUploadStatus(status) {
        const statusMap = {
            404: { icon: "exclamation-circle", text: "No chicken detected", color: "red" },
            201: { icon: "check", text: "Image Uploaded", color: "green" },
            202: { icon: "exclamation-triangle", text: "Image Required", color: "red" },
            204: { icon: "exclamation-triangle", text: "Invalid Image Format", color: "red" },
            413: { icon: "exclamation-triangle", text: "File Too Large", color: "red" },
            500: { icon: "times", text: "Upload Failed, Please try again later.", color: "red" }
        };
        return statusMap[status] || statusMap[500];
    }

    function getAlertConfig(status) {
        const configMap = {
            404: { title: "Warning", text: "No chicken detected", icon: "warning" },
            201: { title: "Success", text: "Image uploaded successfully", icon: "success" },
            202: { title: "Warning", text: "Image is required", icon: "error" },
            204: { title: "Warning", text: "Invalid image format, please try again", icon: "error" },
            413: { title: "Warning", text: "The file is too large, please try again", icon: "warning" },
            500: { title: "Failed", text: "Upload Failed, Please try again later.", icon: "error" }
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
    elements.cameraButton.addEventListener("click", () => toggleCamera("start"));
    elements.video.addEventListener("click", captureImage);
    elements.fileInput.addEventListener("change", previewImage);
    elements.retakeButton.addEventListener("click", () => toggleCamera("start"));
    elements.rechooseButton.addEventListener("click", () => elements.fileInput.click());
    elements.cancelImage.addEventListener("click", cancelImage);
    elements.form.addEventListener("submit", handleFormSubmit);
});
</script>

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