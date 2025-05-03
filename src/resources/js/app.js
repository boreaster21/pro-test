import './bootstrap';

import Alpine from 'alpinejs';
import { initializeFavoriteButtons } from './favoriteButtonHandler';
import { initializeReservationForm } from './reservationFormHandler';
import { initializeReviewLoader } from './reviewLoader';
import { initializeReviewForm } from './reviewFormHandler';
import { initializeModal } from './modalHandler';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    initializeFavoriteButtons();
    initializeReservationForm();
    initializeReviewLoader();
    initializeReviewForm();
    initializeModal();
});