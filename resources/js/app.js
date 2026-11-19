document.addEventListener('click', (event) => {
    const opener = event.target.closest('[data-dialog-open]');

    if (opener) {
        document.getElementById(opener.dataset.dialogOpen)?.showModal();

        return;
    }

    const closer = event.target.closest('[data-dialog-close]');

    if (closer) {
        closer.closest('dialog')?.close();
    }
});

document.addEventListener('submit', (event) => {
    const message = event.target.dataset.confirm;

    if (message && ! window.confirm(message)) {
        event.preventDefault();
    }
});

document.querySelectorAll('dialog[data-open-on-load]').forEach((dialog) => dialog.showModal());
