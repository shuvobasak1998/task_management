const formatCountdown = (seconds) => {
    const hours = Math.floor(seconds / 3600);
    const minutes = Math.floor((seconds % 3600) / 60);
    const remainingSeconds = seconds % 60;

    return [hours, minutes, remainingSeconds]
        .map((value) => String(value).padStart(2, '0'))
        .join(':');
};

const countdownElements = document.querySelectorAll('[data-countdown-seconds]');

countdownElements.forEach((element) => {
    let remaining = Number.parseInt(element.dataset.countdownSeconds ?? '0', 10);

    if (Number.isNaN(remaining) || remaining <= 0) {
        element.textContent = '00:00:00';

        return;
    }

    const intervalId = window.setInterval(() => {
        remaining = Math.max(0, remaining - 1);
        element.textContent = formatCountdown(remaining);

        if (remaining === 0) {
            const statusLabel = element.nextElementSibling;

            if (statusLabel) {
                statusLabel.textContent = 'Time expired';
                statusLabel.classList.remove('text-amber-700');
                statusLabel.classList.add('text-rose-700');
            }

            window.clearInterval(intervalId);
        }
    }, 1000);
});

const openModal = (modal) => {
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.classList.add('overflow-hidden');
};

const closeModal = (modal) => {
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.classList.remove('overflow-hidden');
};

document.querySelectorAll('[data-modal-trigger]').forEach((button) => {
    button.addEventListener('click', () => {
        const target = button.getAttribute('data-modal-trigger');
        const modal = document.querySelector(`[data-modal="${target}"]`);

        if (modal) {
            openModal(modal);
        }
    });
});

document.querySelectorAll('[data-modal-close]').forEach((button) => {
    button.addEventListener('click', () => {
        const target = button.getAttribute('data-modal-close');
        const modal = document.querySelector(`[data-modal="${target}"]`);

        if (modal) {
            closeModal(modal);
        }
    });
});

document.querySelectorAll('[data-modal]').forEach((modal) => {
    if (modal.getAttribute('data-open-on-load') === 'true') {
        openModal(modal);
    }
});

window.addEventListener('keydown', (event) => {
    if (event.key !== 'Escape') {
        return;
    }

    document.querySelectorAll('[data-modal]').forEach((modal) => {
        if (! modal.classList.contains('hidden')) {
            closeModal(modal);
        }
    });
});
