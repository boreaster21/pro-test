console.log('app.js is executing!');
import './bootstrap';

import Alpine from 'alpinejs';
import { initializeFavoriteButtons } from './favoriteButtonHandler';
import { initializeReservationForm } from './reservationFormHandler';
import { initializeReviewLoader } from './reviewLoader';
import { initializeReviewForm } from './reviewFormHandler';

window.Alpine = Alpine;

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM fully loaded and parsed');

    initializeFavoriteButtons();
    initializeReservationForm();
    initializeReviewLoader();
    initializeReviewForm();
});