export function initializeModal() {
    const triggers = document.querySelectorAll('.js-modal-trigger');
    const closeModalButtons = document.querySelectorAll('.js-modal-close');
    const modals = document.querySelectorAll('.js-modal');

    if (triggers.length === 0 || modals.length === 0) {
        return;
    }

    const openModal = (modal) => {
        if (!modal) return;
        modal.setAttribute('aria-hidden', 'false');
        modal.classList.add('is-open');
        document.body.style.overflow = 'hidden';
        const focusableElement = modal.querySelector('.js-modal-close') || modal.querySelector('a[href], button, textarea, input[type="text"], input[type="radio"], input[type="checkbox"], select');
        if (focusableElement) {
            focusableElement.focus();
        }
    };

    const closeModal = (modal) => {
        if (!modal) return;
        modal.setAttribute('aria-hidden', 'true');
        modal.classList.remove('is-open');
        document.body.style.overflow = '';
    };

    triggers.forEach(trigger => {
        const modalId = trigger.getAttribute('data-modal-target') || 'search-sort-modal';
        const targetModal = document.getElementById(modalId);
        if (targetModal) {
            trigger.addEventListener('click', () => openModal(targetModal));
        }
    });

    closeModalButtons.forEach(button => {
        const modal = button.closest('.js-modal');
        button.addEventListener('click', () => closeModal(modal));
    });

    modals.forEach(modal => {
        const overlay = modal.querySelector('.c-modal__overlay');
        if (overlay) {
            overlay.addEventListener('click', (event) => {
                if (event.target === overlay) {
                    closeModal(modal);
                }
            });
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            const openModalElement = document.querySelector('.js-modal.is-open');
            if (openModalElement) {
                closeModal(openModalElement);
            }
        }
    });
} 