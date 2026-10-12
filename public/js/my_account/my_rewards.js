(function () {
    const root = document.getElementById('rewards-root');
    if (!root || root.dataset.rwdReady === '1') return;
    root.dataset.rwdReady = '1';

    root.querySelectorAll('[data-rwd-copy]').forEach(function (button) {
        const box = button.closest('.rwd-copy');
        const input = box && box.querySelector('input');
        if (!input) return;

        let resetTimer;

        button.addEventListener('click', async function () {
            try {
                await navigator.clipboard.writeText(input.value);
            } catch (err) {
                input.removeAttribute('readonly');
                input.select();
                document.execCommand('copy');
                input.setAttribute('readonly', 'readonly');
                input.blur();
            }

            clearTimeout(resetTimer);
            button.textContent = 'Copied';
            resetTimer = setTimeout(function () {
                button.textContent = 'Copy';
            }, 1600);
        });
    });
})();

