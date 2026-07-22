<h1 class="large-font">Data & Legal</h1>

<div class="settings-section-content d-flex flex-column justify-content-between mt-5 gap-25">
    <p class="bold-font">Download Data</p>

    <div class="d-flex align-items-center justify-content-between gap-25">
        <p style="color: #9D9B98">Download a copy of your account data.</p>
        <a href="{{ route('account-settings.download-data') }}" class="small-link-tag">Download Account Data</a>
    </div>
</div>

<div class="settings-section-content d-flex flex-column justify-content-between mt-5 gap-25">
    <p class="bold-font">Delete Data</p>

    <div class="d-flex align-items-center justify-content-between gap-25">
        <p style="color: #9D9B98">Remove all stored personal data.</p>
        <a href="#" wire:click.prevent="deletePersonalData"
            onclick="return confirm('Delete your personal data and sign out? This action cannot be undone.');"
            class="small-link-tag">Delete Personal Data</a>
    </div>
</div>
