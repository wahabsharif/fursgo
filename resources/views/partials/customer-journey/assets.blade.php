@push('styles')
    <link rel="stylesheet" href="{{ asset('css/customer_journey.css') }}">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
@endpush

@push('script')
    <script>
        (function () {
            var meta = document.querySelector('meta[name="asset-base-url"]');
            window.BASE_URL = (meta && meta.content) ? meta.content.replace(/\/?$/, '/') : '/';
            window.CARTO_API_KEY = @json(config('services.carto.api_key'));
        })();
    </script>
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script src="{{ asset('js/customer_journey.js') }}"></script>

    <script>
        // Filter pill sync — matches custom search_results.php
        document.addEventListener('click', (e) => {
            const pill = e.target.closest('.selected-item');
            if (!pill) return;

            const group = pill.dataset.group;
            if (group === 'groomer-venue[]' || group === 'space-venue[]') return;
            if (group === 'groomer-sort' || group === 'space-sort') return;

            const value = pill.dataset.value;
            pill.remove();

            if (value) {
                document
                    .querySelectorAll(`input[type="checkbox"][value="${CSS.escape(value)}"]`)
                    .forEach(input => { input.checked = false; });
            }
        });

        function syncModalToPills(modalSelector, targetBox) {
            if (!targetBox) return;
            const checkboxes = [...document.querySelectorAll(
                `${modalSelector} .filter-options-section input[type="checkbox"]`
            )];

            const modalGroups = new Set(checkboxes.map(input => input.name));
            const modalValues = new Set(checkboxes.map(input => input.value));

            targetBox.querySelectorAll('.selected-item').forEach(el => {
                if (el.dataset.group === 'groomer-venue[]' || el.dataset.group === 'space-venue[]') return;
                if (el.dataset.group === 'groomer-sort' || el.dataset.group === 'space-sort') return;

                if (el.dataset.dynamic === 'true') {
                    el.remove();
                    return;
                }
                if (el.dataset.group && modalGroups.has(el.dataset.group)) {
                    el.remove();
                    return;
                }
                if (!el.dataset.group && el.dataset.value && modalValues.has(el.dataset.value)) {
                    el.remove();
                }
            });

            checkboxes.forEach(input => {
                if (input.checked) createModalPill(input, targetBox);
            });
        }

        function createModalPill(input, box) {
            const value = input.value;
            if ([...box.querySelectorAll('.selected-item')].some(el => el.dataset.value === value)) return;

            const div = document.createElement('div');
            div.className = 'selected-item cursor d-flex align-items-center gap-10';
            div.dataset.value = value;
            div.dataset.group = input.name;
            div.dataset.dynamic = 'true';
            div.innerHTML = `
                <p>${value}</p>
                <img src="{{ asset('icons/cross.svg') }}" class="cross svg" alt="remove">
            `;
            box.appendChild(div);
        }

        const groomModal = document.querySelector('#groomModal');
        const spaceModal = document.querySelector('#spaceModal');
        const groomApplyBtn = document.querySelector('#groomModal .modal-footer-btn.apply');
        const spaceApplyBtn = document.querySelector('#spaceModal .modal-footer-btn.apply');
        const groomSelectedSection = document.querySelector('#groomerSelectedSection');
        const spaceSelectedSection = document.querySelector('#spaceSelectedSection');

        groomApplyBtn?.addEventListener('click', () => {
            syncModalToPills('#groomModal', groomSelectedSection);
            if (groomModal) groomModal.style.display = 'none';
        });

        spaceApplyBtn?.addEventListener('click', () => {
            syncModalToPills('#spaceModal', spaceSelectedSection);
            if (spaceModal) spaceModal.style.display = 'none';
        });
    </script>
@endpush
