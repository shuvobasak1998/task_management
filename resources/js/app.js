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

const closeFlashMessage = (element) => {
    element.classList.add('pointer-events-none', 'opacity-0', '-translate-y-2');

    window.setTimeout(() => {
        element.remove();
    }, 300);
};

document.querySelectorAll('[data-flash-message]').forEach((element) => {
    const closeButton = element.querySelector('[data-flash-close]');

    if (closeButton) {
        closeButton.addEventListener('click', () => {
            closeFlashMessage(element);
        });
    }

    const timeout = Number.parseInt(element.dataset.flashTimeout ?? '0', 10);

    if (! Number.isNaN(timeout) && timeout > 0) {
        window.setTimeout(() => {
            if (document.body.contains(element)) {
                closeFlashMessage(element);
            }
        }, timeout);
    }
});

const progressStatusClasses = {
    pending: ['bg-stone-100', 'text-stone-700'],
    in_progress: ['bg-amber-100', 'text-amber-900'],
    completed: ['bg-emerald-100', 'text-emerald-900'],
};

const allProgressStatusClasses = Object.values(progressStatusClasses).flat();

const clampProgressValue = (value) => {
    if (Number.isNaN(value)) {
        return 0;
    }

    return Math.max(0, Math.min(100, value));
};

const calculateProgressFromPointer = (track, clientX) => {
    const { left, width } = track.getBoundingClientRect();

    if (width <= 0) {
        return 0;
    }

    return clampProgressValue(Math.round(((clientX - left) / width) * 100));
};

const applyProgressValue = (control, value) => {
    const nextValue = clampProgressValue(value);
    const fill = control.querySelector('[data-progress-fill]');
    const label = control.querySelector('[data-progress-value-label]');
    const track = control.querySelector('[data-progress-track]');

    control.dataset.progressValue = String(nextValue);

    if (fill) {
        fill.style.width = `${nextValue}%`;
    }

    if (label) {
        label.textContent = `${nextValue}%`;
    }

    if (track) {
        track.setAttribute('aria-valuenow', String(nextValue));
    }
};

const applyTaskStatus = (taskId, status, statusLabel) => {
    document.querySelectorAll(`[data-task-status="${taskId}"]`).forEach((element) => {
        element.classList.remove(...allProgressStatusClasses);
        element.classList.add(...(progressStatusClasses[status] ?? progressStatusClasses.pending));
        element.textContent = statusLabel;
    });
};

const persistProgressValue = async (control, value) => {
    const previousValue = Number.parseInt(control.dataset.progressLastCommitted ?? control.dataset.progressValue ?? '0', 10);

    if (control.dataset.progressSaving === 'true' || previousValue === value) {
        return;
    }

    control.dataset.progressSaving = 'true';

    try {
        const response = await window.fetch(control.dataset.progressUrl, {
            method: 'PATCH',
            headers: {
                'Accept': 'application/json',
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': control.dataset.progressCsrf ?? '',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify({
                target_progress: value,
            }),
        });

        if (! response.ok) {
            throw new Error('Failed to update task progress.');
        }

        const payload = await response.json();
        const task = payload.task ?? null;

        if (! task) {
            throw new Error('Missing task payload.');
        }

        applyProgressValue(control, task.progress_percent);
        control.dataset.progressLastCommitted = String(task.progress_percent);
        applyTaskStatus(task.id, task.status, task.status_label);
    } catch (error) {
        applyProgressValue(control, previousValue);
    } finally {
        control.dataset.progressSaving = 'false';
    }
};

document.querySelectorAll('[data-progress-control]').forEach((control) => {
    const track = control.querySelector('[data-progress-track]');

    if (! track) {
        return;
    }

    const initialValue = clampProgressValue(Number.parseInt(control.dataset.progressValue ?? '0', 10));
    control.dataset.progressLastCommitted = String(initialValue);
    control.dataset.progressSaving = 'false';
    applyProgressValue(control, initialValue);

    let isDragging = false;

    const updateFromPointer = (clientX) => {
        applyProgressValue(control, calculateProgressFromPointer(track, clientX));
    };

    track.addEventListener('pointerdown', (event) => {
        isDragging = true;
        track.setPointerCapture(event.pointerId);
        updateFromPointer(event.clientX);
    });

    track.addEventListener('pointermove', (event) => {
        if (! isDragging) {
            return;
        }

        updateFromPointer(event.clientX);
    });

    const commitPointerValue = (event) => {
        if (! isDragging) {
            return;
        }

        isDragging = false;

        if (track.hasPointerCapture(event.pointerId)) {
            track.releasePointerCapture(event.pointerId);
        }

        const value = clampProgressValue(Number.parseInt(control.dataset.progressValue ?? '0', 10));
        void persistProgressValue(control, value);
    };

    track.addEventListener('pointerup', commitPointerValue);
    track.addEventListener('pointercancel', commitPointerValue);

    track.addEventListener('keydown', (event) => {
        const currentValue = clampProgressValue(Number.parseInt(control.dataset.progressValue ?? '0', 10));
        let nextValue = currentValue;

        if (event.key === 'ArrowLeft' || event.key === 'ArrowDown') {
            nextValue = currentValue - 1;
        } else if (event.key === 'ArrowRight' || event.key === 'ArrowUp') {
            nextValue = currentValue + 1;
        } else if (event.key === 'Home') {
            nextValue = 0;
        } else if (event.key === 'End') {
            nextValue = 100;
        } else {
            return;
        }

        event.preventDefault();
        nextValue = clampProgressValue(nextValue);
        applyProgressValue(control, nextValue);
        void persistProgressValue(control, nextValue);
    });
});
