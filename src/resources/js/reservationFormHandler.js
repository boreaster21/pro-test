/**
 * Initializes the reservation form confirmation display logic.
 */
export function initializeReservationForm() {
    const dateInput = document.getElementById('reservation_date');
    const timeInput = document.getElementById('reservation_time');
    const peopleInput = document.getElementById('number_of_people');
    const confirmDate = document.getElementById('confirm_date');
    const confirmTime = document.getElementById('confirm_time');
    const confirmPeople = document.getElementById('confirm_people');

    // Check if all required elements exist on the page
    if (!dateInput || !timeInput || !peopleInput || !confirmDate || !confirmTime || !confirmPeople) {
        // Silently exit if the form elements aren't present (e.g., on other pages)
        // console.log('Reservation form elements not found, skipping initialization.');
        return;
    }

    console.log('Initializing reservation form handler...'); // Log initialization

    function updateConfirmation() {
        confirmDate.textContent = dateInput.value || '未選択';
        confirmTime.textContent = timeInput.value || '未選択';
        confirmPeople.textContent = peopleInput.value || '未入力';
    }

    // Attach event listeners
    dateInput.addEventListener('change', updateConfirmation);
    timeInput.addEventListener('change', updateConfirmation);
    peopleInput.addEventListener('input', updateConfirmation);

    // Initial update on page load
    updateConfirmation();
} 