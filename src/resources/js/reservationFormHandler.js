export function initializeReservationForm() {
    const dateInput = document.getElementById('reservation_date');
    const timeInput = document.getElementById('reservation_time');
    const peopleInput = document.getElementById('number_of_people');
    const confirmDate = document.getElementById('confirm_date');
    const confirmTime = document.getElementById('confirm_time');
    const confirmPeople = document.getElementById('confirm_people');

    if (!dateInput || !timeInput || !peopleInput || !confirmDate || !confirmTime || !confirmPeople) {
        return;
    }

    console.log('Initializing reservation form handler...');

    function updateConfirmation() {
        confirmDate.textContent = dateInput.value || '未選択';
        confirmTime.textContent = timeInput.value || '未選択';
        confirmPeople.textContent = peopleInput.value || '未入力';
    }

    dateInput.addEventListener('change', updateConfirmation);
    timeInput.addEventListener('change', updateConfirmation);
    peopleInput.addEventListener('input', updateConfirmation);

    updateConfirmation();
} 