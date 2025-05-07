import './bootstrap';

// import Alpine from 'alpinejs'; 
import { initializeFavoriteButtons } from './favoriteButtonHandler';
import { initializeReservationForm } from './reservationFormHandler';
import { initializeReviewLoader } from './reviewLoader';
import { initializeReviewForm } from './reviewFormHandler';
import { initializeModal } from './modalHandler';


document.addEventListener('DOMContentLoaded', () => {
    initializeFavoriteButtons();
    initializeReservationForm();
    initializeReviewLoader();
    initializeReviewForm();
    initializeModal();

    const formsToWatch = document.querySelectorAll('#header-search-form, #modal-search-form');
    const loadingOverlay = document.getElementById('page-loading-overlay');

    if (formsToWatch.length > 0 && loadingOverlay) {
        formsToWatch.forEach(form => {
            form.addEventListener('submit', function () {
                loadingOverlay.classList.add('is-active');
            });
        });
    }

    window.addEventListener('load', () => {
        if (loadingOverlay) {
            loadingOverlay.classList.remove('is-active');
        }
    });

    function applyInvalidClassToInputFields() {
        const errorMessages = document.querySelectorAll('.c-input-error');
        errorMessages.forEach(errorMsg => {
            const formGroup = errorMsg.closest('.c-form-group');
            if (formGroup) {
                const inputField = formGroup.querySelector('.c-input, .c-textarea, .c-select');
                if (inputField) {
                    inputField.classList.add('is-invalid');
                }
            }
        });
    }
    applyInvalidClassToInputFields();
});