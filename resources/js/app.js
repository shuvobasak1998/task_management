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
