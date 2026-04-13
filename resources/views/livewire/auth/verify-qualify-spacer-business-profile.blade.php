<div class="business-basics-wrap" wire:key="verify-qualify-spacer-business-profile">
    <h1 class="business-basics-title">Spacer Business Profile</h1>

    <form wire:submit="submitSpacerBusinessProfile" class="business-basics-form">
        <div class="basics-card">
            <div class="basics-field">
                <label class="form-label" for="space-location">Space Location</label>
                <input id="space-location" type="text" wire:model.live="space_location" class="form-input"
                    placeholder="Enter city or area where your space is located.">
                @error('space_location')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="basics-field">
                <label class="form-label" for="space-capacity">Capacity</label>
                <input id="space-capacity" type="text" wire:model.live="space_capacity" class="form-input"
                    placeholder="How many groomers/pets can your space support at once?">
                @error('space_capacity')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>

            <div class="basics-field">
                <label class="form-label" for="space-amenities">Amenities (optional)</label>
                <textarea id="space-amenities" wire:model.live="space_amenities" class="form-input basics-textarea"
                    placeholder="List available equipment, parking, wash stations, drying stations, etc."
                    style="resize: none; overflow: hidden; height: 90px; width: 100%;"></textarea>
                @error('space_amenities')
                    <span class="error-text">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="basics-actions">
            <button type="submit"
                class="submit-btn {{ $this->isSpacerBusinessProfileContinueEnabled() ? 'btn-active' : 'btn-disabled' }}"
                wire:loading.attr="disabled" wire:target="submitSpacerBusinessProfile"
                @if (!$this->isSpacerBusinessProfileContinueEnabled()) disabled @endif>
                <span wire:loading.remove wire:target="submitSpacerBusinessProfile">Continue</span>
                <span wire:loading wire:target="submitSpacerBusinessProfile">Saving…</span>
            </button>
        </div>
    </form>
</div>
