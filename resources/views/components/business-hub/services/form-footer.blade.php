<div class="service-form-footer" :class="{ 'is-dirty': dirtyCount > 0 }" x-data="{
    dirtyCount: 0,
    formRoot() {
        return this.$el.closest('[wire\\:id]') || this.$el.closest('form') || this.$el;
    },
    store() {
        const form = this.formRoot();
        if (!form._serviceDirty) {
            form._serviceDirty = new Set();
        }
        return form._serviceDirty;
    },
    sync() {
        this.dirtyCount = this.store().size;
    },
    markSaved() {
        this.store().clear();
        this.sync();
    },
    init() {
        window.initServiceFormDirtyTracking?.();
        this.sync();
        this.$el.addEventListener('service-footer-sync', () => this.sync());
    },
}" x-on:service-form-baseline.window="markSaved()">
    <p class="service-form-saved"
        x-html="dirtyCount === 0
            ? 'All changes saved <span>· just now</span>'
            : (dirtyCount === 1 ? '1 unsaved changes' : dirtyCount + ' unsaved changes')"></p>
    <div class="service-form-actions">
        <button type="button" class="service-form-btn service-form-btn-cancel" :disabled="dirtyCount === 0"
            @click="$dispatch('service-form-cancel')">Cancel</button>
        <button type="submit" class="service-form-btn service-form-btn-save" :disabled="dirtyCount === 0"
            wire:loading.attr="disabled" wire:target="save">
            <span class="save-btn-text" wire:loading.class="hidden" wire:target="save">Save Changes</span>
            <span class="save-btn-loading hidden" wire:loading.class.remove="hidden" wire:target="save">
                <span class="save-spinner"></span>
            </span>
        </button>
    </div>
</div>
