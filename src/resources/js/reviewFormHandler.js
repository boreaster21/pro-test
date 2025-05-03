/**
 * Initializes interactive elements for the review form,
 * including star rating, character counter, and image upload/drag & drop.
 */
export function initializeReviewForm() {
    const starRatingDiv = document.getElementById('star-rating');
    const ratingInput = document.getElementById('rating');
    const commentTextarea = document.getElementById('comment');
    const commentCounter = document.getElementById('comment-counter');
    const imageInput = document.getElementById('image');
    const imageFormatError = document.getElementById('image-format-error');
    const submitButton = document.querySelector('.review-form button[type="submit"]'); // More specific selector
    const fileNameDisplay = document.getElementById('file-name-display');
    const dropZone = document.getElementById('drop-zone');

    // Exit if the essential form elements are not found
    if (!starRatingDiv || !ratingInput || !commentTextarea || !imageInput || !dropZone) {
        // console.log('Review form elements not found, skipping initialization.');
        return;
    }

    console.log('Initializing review form handler...');

    const stars = starRatingDiv.querySelectorAll('.c-review-form__rating-star');

    // --- Star Rating Logic --- 
    function updateStars(value) {
        stars.forEach(star => {
            const starValue = parseInt(star.dataset.value);
            const isActive = starValue <= value;
            star.classList.toggle('is-active', isActive);
            star.textContent = isActive ? '★' : '☆';
        });
    }

    const initialRating = ratingInput.value || 0;
    updateStars(initialRating);

    stars.forEach(star => {
        star.addEventListener('click', function () {
            const value = this.dataset.value;
            ratingInput.value = value;
            updateStars(value);
        });

        star.addEventListener('mouseover', function () {
            const hoverValue = this.dataset.value;
            stars.forEach(s => {
                s.classList.toggle('is-active', parseInt(s.dataset.value) <= hoverValue);
            });
        });
    });

    starRatingDiv.addEventListener('mouseout', function () {
        const selectedRating = ratingInput.value || 0;
        updateStars(selectedRating);
    });

    // --- Comment Counter Logic --- 
    if (commentCounter) {
        const maxChars = commentTextarea.getAttribute('maxlength') || 400;
        const updateCounter = () => {
            const currentLength = commentTextarea.value.length;
            commentCounter.textContent = `${currentLength} / ${maxChars} 文字`;
        };
        commentTextarea.addEventListener('input', updateCounter);
        updateCounter(); // Initial update
    }

    // --- Image Upload & Drag/Drop Logic --- 
    function handleFile(file) {
        if (imageFormatError) imageFormatError.textContent = '';
        if (submitButton) submitButton.disabled = false;
        if (fileNameDisplay) fileNameDisplay.textContent = '';

        if (file) {
            if (fileNameDisplay) fileNameDisplay.textContent = `選択中のファイル: ${file.name}`;
            const allowedExtensions = /(\.jpg|\.jpeg|\.png)$/i;

            if (!allowedExtensions.exec(file.name)) {
                if (imageFormatError) imageFormatError.textContent = 'JPEGまたはPNG形式の画像を選択してください。';
                if (submitButton) submitButton.disabled = true;
                if (fileNameDisplay) fileNameDisplay.textContent = '';
                imageInput.value = ''; // Clear the file input
                return false;
            }
            // File is valid
            if (dropZone) dropZone.classList.add('is-valid');
            return true;
        } else {
            // No file selected/dropped
            imageInput.value = '';
            if (dropZone) dropZone.classList.remove('is-valid', 'is-dragging');
            return false;
        }
    }

    imageInput.addEventListener('change', function (event) {
        const file = event.target.files[0];
        handleFile(file);
    });

    dropZone.addEventListener('dragover', (event) => {
        event.stopPropagation();
        event.preventDefault();
        dropZone.classList.add('is-dragging');
        dropZone.classList.remove('is-valid');
        event.dataTransfer.dropEffect = 'copy';
    });

    dropZone.addEventListener('dragleave', (event) => {
        event.stopPropagation();
        event.preventDefault();
        if (event.relatedTarget === null || !dropZone.contains(event.relatedTarget)) {
            dropZone.classList.remove('is-dragging');
        }
    });

    dropZone.addEventListener('drop', (event) => {
        event.stopPropagation();
        event.preventDefault();
        dropZone.classList.remove('is-dragging');

        const files = event.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            if (handleFile(file)) {
                // Create a new FileList/DataTransfer object to assign to input
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                imageInput.files = dataTransfer.files;
                // Optionally trigger the 'change' event manually if needed
                // imageInput.dispatchEvent(new Event('change', { bubbles: true }));
            }
        }
    });
} 