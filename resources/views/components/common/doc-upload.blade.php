@props([
    'uploadId',
    'wireModel',
    'accept' => '.pdf,.jpg,.jpeg,.png',
    'hint' => 'JPEG, PNG, and PDF formats, up to 50 MB.',
    'headerLabel' => 'Upload',
    'browseLabel' => 'Browse Files',
    'emptyTitle' => 'Choose files or drag & drop them here.',
    'multiple' => true,
    'maxSizeKb' => 51200,
    'savedJsonId' => null,
    'savedWindowKey' => null,
    'removeStoredFn' => null,
    'pendingClearCall' => null,
    'savedEntries' => [],
    'savedEntriesKey' => '',
])

@php
    $savedEntriesList = is_array($savedEntries) ? array_values($savedEntries) : [];
    $hasSavedEntries = count($savedEntriesList) > 0;
    $savedEntriesB64 = base64_encode(json_encode($savedEntriesList));
    $maxBytes = (int) $maxSizeKb * 1024;
@endphp

<div class="vq-doc-upload-field"
    @if ($savedEntriesKey !== '') wire:key="vq-doc-field-{{ $uploadId }}-{{ $savedEntriesKey }}" @endif>
    <input type="file" wire:model="{{ $wireModel }}" id="{{ $uploadId }}-file-input" class="hidden-input"
        accept="{{ $accept }}" @if ($multiple) multiple @endif>
    <div {{ $attributes->class(['vq-doc-upload', 'custom-file-upload', 'vq-doc-upload--has-files' => $hasSavedEntries]) }}
        wire:ignore data-vq-doc-upload data-upload-id="{{ $uploadId }}" data-saved-entries="{{ $savedEntriesB64 }}"
        data-saved-encoding="base64" data-max-bytes="{{ $maxBytes }}" data-wire-model="{{ $wireModel }}"
        @if ($savedJsonId) data-saved-json-id="{{ $savedJsonId }}" @endif
        @if ($savedWindowKey) data-saved-window-key="{{ $savedWindowKey }}" @endif
        @if ($removeStoredFn) data-remove-stored-fn="{{ $removeStoredFn }}" @endif
        @unless ($multiple) data-single="1" @endunless
        @if ($pendingClearCall) data-pending-clear-call="{{ $pendingClearCall }}" @endif
        id="{{ $uploadId }}-upload-widget">
        <label for="{{ $uploadId }}-file-input" class="vq-doc-upload__header"
            id="{{ $uploadId }}-upload-trigger" role="button" tabindex="0" aria-label="Upload files">
            <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" fill="none"
                aria-hidden="true">
                <path
                    d="M10.2778 0.5H1.72222C1.04721 0.5 0.5 1.04721 0.5 1.72222V10.2778C0.5 10.9528 1.04721 11.5 1.72222 11.5H10.2778C10.9528 11.5 11.5 10.9528 11.5 10.2778V1.72222C11.5 1.04721 10.9528 0.5 10.2778 0.5Z"
                    stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                <path
                    d="M4.16662 5.38878C4.84163 5.38878 5.38884 4.84157 5.38884 4.16656C5.38884 3.49154 4.84163 2.94434 4.16662 2.94434C3.4916 2.94434 2.9444 3.49154 2.9444 4.16656C2.9444 4.84157 3.4916 5.38878 4.16662 5.38878Z"
                    stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                <path
                    d="M11.5001 7.83358L9.61421 5.94769C9.38501 5.71856 9.07419 5.58984 8.7501 5.58984C8.42601 5.58984 8.11519 5.71856 7.88599 5.94769L2.33344 11.5002"
                    stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
            <span>{{ $headerLabel }}</span>
        </label>

        <div class="vq-doc-upload__body" id="{{ $uploadId }}-upload-area">
            <div class="vq-doc-upload__content">
                <label for="{{ $uploadId }}-file-input" class="vq-doc-upload__empty"
                    id="{{ $uploadId }}-upload-empty" @if ($hasSavedEntries) hidden @endif>
                    <p class="vq-doc-upload__empty-title">{{ $emptyTitle }}</p>
                    <p class="vq-doc-upload__empty-hint">{{ $hint }}</p>
                    <span class="vq-doc-upload__browse">{{ $browseLabel }}</span>
                </label>
                <div class="file-list vq-doc-upload__file-list" id="{{ $uploadId }}-file-list" hidden>
                </div>
            </div>
        </div>
    </div>
    <div id="{{ $uploadId }}-upload-error" class="vq-doc-upload__error" role="alert" hidden></div>
</div>

@pushOnce('styles')
    <link rel="stylesheet" href="{{ asset('css/vq-doc-upload.css') }}">
@endPushOnce

@pushOnce('script')
    <script src="{{ asset('js/vq-doc-upload.js') }}"></script>
@endPushOnce
