<div class="accuracy-confirm-wrap">
    <label class="accuracy-confirm-row">
        <input type="checkbox" wire:model.live="information_accuracy_confirmed" class="accuracy-confirm-input">
        <span class="accuracy-confirm-ui" aria-hidden="true"></span>
        <span class="accuracy-confirm-text">I confirm the information provided is accurate and current.</span>
    </label>
    @error('information_accuracy_confirmed')
        <span class="error-text">{{ $message }}</span>
    @enderror
</div>
