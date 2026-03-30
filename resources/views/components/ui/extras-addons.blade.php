@props([
'addons' => [],
'currency' => '£',
'title' => 'Extras & Add-ons',
'instanceId' => null,
'defaultSelected' => [],
'onChangeJs' => '',
'background' => false,
])

@php
$defaultAddons = [
['id' => 1, 'name' => 'Flea & Tick Treatment', 'price' => 14, 'col' => 'left'],
['id' => 2, 'name' => 'Hypoallergenic Shampoo Upgrade', 'price' => 20, 'col' => 'left'],
['id' => 3, 'name' => 'Tear-Stain Treatment', 'price' => 5, 'col' => 'left'],
['id' => 4, 'name' => 'Coat Shine Spray', 'price' => 10, 'col' => 'left'],
['id' => 5, 'name' => 'Nail Grinding', 'price' => 25, 'col' => 'left'],
['id' => 6, 'name' => 'Coat Colour Enhancing Shampoo', 'price' => 10, 'col' => 'left'],
['id' => 7, 'name' => 'Fast-Dry Service (express grooming)', 'price' => 8, 'col' => 'left'],
['id' => 8, 'name' => 'Breath Freshner Gel', 'price' => 2, 'col' => 'left'],
['id' => 9, 'name' => 'Deep Conditioning Mask', 'price' => 20, 'col' => 'right'],
['id' => 10, 'name' => 'Shed-Control Shampoo', 'price' => 10, 'col' => 'right'],
['id' => 11, 'name' => 'Deodorising Treatment', 'price' => 2, 'col' => 'right'],
['id' => 12, 'name' => 'Anti-Itch Treatment', 'price' => 10, 'col' => 'right'],
['id' => 13, 'name' => 'Soft-Claws / Nail Caps Application', 'price' => 60, 'col' => 'right'],
['id' => 14, 'name' => 'Premium Fragrance Upgrade', 'price' => 10, 'col' => 'right'],
['id' => 15, 'name' => 'Paw Fur Shaping', 'price' => 23, 'col' => 'right'],
];

$addonsToUse = !empty($addons) ? array_values($addons) : $defaultAddons;
$instanceId = $instanceId ?: 'furs-' . uniqid();
@endphp

@once
<style>
    .furs-addons-root {
        margin-bottom: 2rem;
    }

    .furs-addons-root h1 {
        color: #3B3731;
        font-family: 'Playfair Display';
        font-size: 28px;
        font-weight: 800;
        margin-bottom: 30px;
    }

    .furs-addons-root .selected-furs-addons-tags {
        display: none;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 28px;
        min-height: 32px;
        margin-top: 1rem;
    }

    .furs-addons-root .selected-furs-addons-tags.has-background {
        background: #F8F8F8;
        padding: 15px;
        border-radius: 10px;
        border: none;
    }

    .furs-addons-root .furs-addons-tag {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #FFC97A;
        font-size: 14px;
        font-family: Lato;
        font-weight: 500;
        padding: 5px 12px;
        border-radius: 20px;
        color: #FFF;
        animation: fursTagPop 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .furs-addons-root .furs-addons-tag span {
        color: #fff !important;
    }

    @keyframes fursTagPop {
        from {
            transform: scale(0.7);
            opacity: 0;
        }

        to {
            transform: scale(1);
            opacity: 1;
        }
    }

    .furs-addons-root .furs-addons-tag-remove {
        background: none;
        border: none;
        color: #fff;
        cursor: pointer;
        font-size: 0.9rem;
        line-height: 1;
        padding: 0;
        opacity: 0.85;
    }

    .furs-addons-root .furs-addons-tag-remove:hover {
        opacity: 1;
    }

    .furs-addons-root .options-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 0;
    }

    .furs-addons-root .options-col {
        display: flex;
        flex-direction: column;
    }

    .furs-addons-root .option {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 4px;
        cursor: pointer;
        border-radius: 8px;
        transition: background 0.12s;
        user-select: none;
    }

    .furs-addons-root .option:hover {
        background: rgba(0, 0, 0, 0.03);
    }

    .furs-addons-root .radio {
        width: 22px;
        height: 22px;
        border-radius: 50%;
        border: 2px solid #ccc;
        flex-shrink: 0;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: border-color 0.15s, background 0.15s;
        position: relative;
    }

    .furs-addons-root .radio::after {
        content: '';
        width: 0.9rem;
        height: 0.9rem;
        border-radius: 50%;
        background: #FFD88C;
        transform: scale(0);
        transition: transform 0.18s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    .furs-addons-root .option.selected .radio {
        border-color: #FFD88C;
        background: #fff;
    }

    .furs-addons-root .option.selected .radio::after {
        transform: scale(1);
    }

    .furs-addons-root .option-name {
        flex: 1;
        color: #9D9B98;
        font-family: Lato;
        font-size: 18px;
        font-weight: 400;
    }

    .furs-addons-root .option-price {
        color: #3B3731;
        text-align: right;
        font-family: Lato;
        font-size: 16px;
        font-weight: 400;
    }

    .furs-addons-root .options-col:first-child {
        padding-right: 32px;
    }

    .furs-addons-root .options-col:last-child {
        padding-left: 32px;
    }
</style>
@endonce

<div class="furs-addons-root" id="furs-addons-{{ $instanceId }}">
    <h1 style="margin-bottom: 30px;">{{ $title }}</h1>

    <div class="selected-furs-addons-tags" id="furs-tags-{{ $instanceId }}"></div>

    <div class="options-grid">
        <div class="options-col" id="furs-col-left-{{ $instanceId }}"></div>
        <div class="options-col" id="furs-col-right-{{ $instanceId }}"></div>
    </div>
</div>

<script>
    (function() {
        const id = @json($instanceId);
        const addons = @json($addonsToUse, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT);
        const currency = @json($currency);
        const onChangeCb = @json($onChangeJs);
        const hasBackground = @json($background);
        const selected = new Set(@json($defaultSelected));

        const root = document.getElementById(`furs-addons-${id}`);
        if (!root) return;

        function fmt(price) {
            return `${currency}${price}`;
        }

        function getSelectedAddons() {
            return addons.filter(a => selected.has(a.id));
        }

        function getTotal() {
            return getSelectedAddons().reduce((sum, a) => sum + Number(a.price || 0), 0);
        }

        function renderOptions() {
            const left = document.getElementById(`furs-col-left-${id}`);
            const right = document.getElementById(`furs-col-right-${id}`);
            if (!left || !right) return;

            left.innerHTML = '';
            right.innerHTML = '';

            addons.forEach(a => {
                const el = document.createElement('div');
                el.className = 'option' + (selected.has(a.id) ? ' selected' : '');
                el.innerHTML = `
                    <div class="radio"></div>
                    <span class="option-name"></span>
                    <span class="option-price"></span>
                `;

                el.querySelector('.option-name').textContent = a.name;
                el.querySelector('.option-price').textContent = fmt(a.price);

                el.addEventListener('click', () => toggle(a.id));
                (a.col === 'left' ? left : right).appendChild(el);
            });
        }

        function renderTags() {
            const container = document.getElementById(`furs-tags-${id}`);
            if (!container) return;

            container.innerHTML = '';
            const selectedItems = getSelectedAddons();

            if (hasBackground) {
                container.classList.toggle('has-background', selectedItems.length > 0);
            }

            container.style.display = selectedItems.length > 0 ? 'flex' : 'none';

            selectedItems.forEach(a => {
                const tag = document.createElement('span');
                tag.className = 'furs-addons-tag';

                const text = document.createElement('span');
                text.textContent = a.name;

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'furs-addons-tag-remove';
                btn.textContent = '✕';
                btn.addEventListener('click', (e) => {
                    e.stopPropagation();
                    toggle(a.id);
                });

                tag.appendChild(text);
                tag.appendChild(btn);
                container.appendChild(tag);
            });
        }

        function fireChange() {
            const selectedAddons = getSelectedAddons();
            const total = getTotal();

            if (onChangeCb && typeof window[onChangeCb] === 'function') {
                window[onChangeCb](Array.from(selected), total, selectedAddons);
            }

            root.dispatchEvent(new CustomEvent('furs:addons:change', {
                detail: {
                    selectedIds: Array.from(selected),
                    total,
                    selectedAddons
                }
            }));
        }

        function toggle(addonId) {
            if (selected.has(addonId)) {
                selected.delete(addonId);
            } else {
                selected.add(addonId);
            }

            renderOptions();
            renderTags();
            fireChange();
        }

        root.fursAddons = {
            getSelected: () => Array.from(selected),
            getAddons: () => getSelectedAddons(),
            getTotal: () => getTotal(),
            setSelected: (ids = []) => {
                selected.clear();
                ids.forEach(id => selected.add(id));
                renderOptions();
                renderTags();
                fireChange();
            }
        };

        renderOptions();
        renderTags();
    })();
</script>
