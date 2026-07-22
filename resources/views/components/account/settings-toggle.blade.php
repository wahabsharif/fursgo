@props(['setting', 'label', 'description'])

<div class="toggle-button-content d-flex align-items-center justify-content-between mt-5">
    <div class="d-flex flex-column gap-25">
        <p class="bold-font">{{ $label }}</p>
        <p style="color: #9D9B98">{{ $description }}</p>
    </div>

    <button type="button" class="toggle-switch" :class="{ 'on': preferences.{{ $setting }} }"
        :aria-pressed="preferences.{{ $setting }} ? 'true' : 'false'"
        @click.stop="togglePreference(@js($setting))">
        <span class="toggle-circle">
            <svg xmlns="http://www.w3.org/2000/svg" width="27" height="27" viewBox="0 0 27 27" fill="none"
                aria-hidden="true">
                <path
                    d="M13.3333 0C6 0 0 6 0 13.3333C0 20.6667 6 26.6667 13.3333 26.6667C20.6667 26.6667 26.6667 20.6667 26.6667 13.3333C26.6667 6 20.6667 0 13.3333 0ZM11.2222 19.4444C10.9154 19.7513 10.4179 19.7513 10.1111 19.4444L4.94065 14.274C4.42115 13.7545 4.42115 12.9122 4.94066 12.3927C5.45965 11.8737 6.30093 11.8731 6.82065 12.3914L10.6667 16.2267L19.84 7.05334C20.3623 6.53105 21.2095 6.53255 21.73 7.05668C22.2478 7.5782 22.2463 8.42032 21.7267 8.94001L11.2222 19.4444Z"
                    fill="white" />
            </svg>
        </span>
    </button>
</div>
