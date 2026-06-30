@props(['file', 'status' => 'Uploaded', 'tone' => 'success'])

@php
    $extension = strtolower(
        (string) ($file['extension'] ?? pathinfo((string) ($file['name'] ?? ''), PATHINFO_EXTENSION)),
    );
    $isPdf = $extension === 'pdf';
    $isImage = (bool) ($file['is_image'] ?? false);
    $displayStatus =
        $file['expired'] ?? false
            ? 'Insurance document expired'
            : ($file['available'] ?? true
                ? $status
                : 'Unavailable');
@endphp

<div class="business-details-file">
    <div class="business-details-file__top">
        @if ($isPdf)
            <svg class="business-details-file__icon" xmlns="http://www.w3.org/2000/svg" width="21" height="25"
                viewBox="0 0 21 25" fill="none" aria-hidden="true">
                <path
                    d="M5.04074 24.501H15.9593C17.1635 24.501 18.3185 24.0226 19.1701 23.1711C20.0216 22.3195 20.5 21.1646 20.5 19.9603V12.7859C20.5004 11.5818 20.0226 10.4268 19.1715 9.57499L11.4276 1.82979C11.0059 1.40815 10.5053 1.0737 9.95439 0.845536C9.40346 0.61737 8.81297 0.499957 8.21666 0.5H5.04074C3.83646 0.5 2.6815 0.978398 1.82995 1.82995C0.978398 2.6815 0.5 3.83646 0.5 5.04074V19.9603C0.5 21.1646 0.978398 22.3195 1.82995 23.1711C2.6815 24.0226 3.83646 24.501 5.04074 24.501Z"
                    stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                <path
                    d="M10.0952 0.966797V8.30982C10.0952 8.99798 10.3686 9.65795 10.8552 10.1446C11.3418 10.6312 12.0018 10.9045 12.6899 10.9045H20.0355"
                    stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
                <path
                    d="M4.33759 18.3383V17.041M4.33759 17.041V14.4463H5.63494C5.97902 14.4463 6.30901 14.583 6.55231 14.8263C6.79561 15.0696 6.93229 15.3996 6.93229 15.7436C6.93229 16.0877 6.79561 16.4177 6.55231 16.661C6.30901 16.9043 5.97902 17.041 5.63494 17.041H4.33759ZM14.7164 18.3383V16.7167M14.7164 16.7167V14.4463H16.6624M14.7164 16.7167H16.6624M9.527 18.3383V14.4463H10.1757C10.6918 14.4463 11.1868 14.6513 11.5517 15.0163C11.9167 15.3812 12.1217 15.8762 12.1217 16.3923C12.1217 16.9084 11.9167 17.4034 11.5517 17.7684C11.1868 18.1333 10.6918 18.3383 10.1757 18.3383H9.527Z"
                    stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
            </svg>
        @elseif ($isImage)
            <svg class="business-details-file__icon" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                viewBox="0 0 24 24" fill="none" stroke="#9D9B98" stroke-width="1.5" stroke-linecap="round"
                stroke-linejoin="round" aria-hidden="true">
                <rect x="3" y="4" width="18" height="16" rx="2" />
                <circle cx="8.5" cy="9.5" r="1.5" />
                <path d="M21 16l-5-5L5 20" />
            </svg>
        @else
            <svg class="business-details-file__icon" xmlns="http://www.w3.org/2000/svg" width="20" height="20"
                viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round"
                stroke-linejoin="round" aria-hidden="true">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" />
                <path d="M14 2v6h6" />
                <path d="M16 13H8" />
                <path d="M16 17H8" />
                <path d="M10 9H8" />
            </svg>
        @endif
        <span>
            <span class="business-details-file__name">{{ $file['name'] ?? 'Document' }}</span>
            <span class="business-details-file__meta">{{ $file['size'] ?? 'Size unavailable' }}</span>
        </span>
        @if (!empty($file['url']))
            <a class="business-details-file__download" href="{{ $file['url'] }}" download
                aria-label="Download {{ $file['name'] ?? 'document' }}">
                <x-radix-download style="color: #3B3731;" aria-hidden="true" />
            </a>
        @endif
    </div>
    <div @class([
        'business-details-file__status-group',
        'business-details-file__status-group--warning' => $tone === 'warning',
    ])>
        @if ($tone === 'warning')
            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none"
                aria-hidden="true">
                <circle cx="9.5" cy="9.5" r="9.5" fill="#FFCA7D" />
                <path
                    d="M10.2528 13.4198C10.4547 13.2172 10.5557 12.9663 10.5557 12.6672C10.5557 12.3681 10.4544 12.1176 10.2517 11.9157C10.0491 11.7137 9.79854 11.6124 9.50017 11.6117C9.2018 11.611 8.95128 11.7123 8.74861 11.9157C8.54594 12.119 8.44461 12.3695 8.44461 12.6672C8.44461 12.9649 8.54594 13.2157 8.74861 13.4198C8.95128 13.6239 9.2018 13.7249 9.50017 13.7228C9.79854 13.7207 10.0494 13.6204 10.2528 13.4198ZM10.2528 10.2521C10.4547 10.0501 10.5557 9.79962 10.5557 9.50055V6.33388C10.5557 6.03481 10.4544 5.78429 10.2517 5.58232C10.0491 5.38036 9.79854 5.27903 9.50017 5.27832C9.2018 5.27762 8.95128 5.37895 8.74861 5.58232C8.54594 5.78569 8.44461 6.03621 8.44461 6.33388V9.50055C8.44461 9.79962 8.54594 10.0505 8.74861 10.2532C8.95128 10.4558 9.2018 10.5568 9.50017 10.5561C9.79854 10.5554 10.0494 10.4541 10.2528 10.2521Z"
                    fill="white" />
            </svg>
        @else
            <svg xmlns="http://www.w3.org/2000/svg" width="19" height="19" viewBox="0 0 19 19" fill="none"
                aria-hidden="true">
                <path
                    d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                    fill="#C9DDA0" />
            </svg>
        @endif
        <span>
            <span class="business-details-file__status">Uploaded: {{ $file['uploaded'] ?? 'date unavailable' }}</span>
            <span class="business-details-file__status" style="font-weight: 700;">Status: {{ $displayStatus }}</span>
        </span>
    </div>
</div>
