@if ($showFreelance)
    <div class="verification-card" wire:key="verify-qualify-freelance">
        <div class="step-heading">
            <h2>Freelance Groomer</h2>
        </div>

        <form wire:submit="submitPersonalInfo" novalidate wire:init="initVerifyQualifyDocUploads">
            <div class="form-grid">
                <!-- Full Name -->
                <div class="form-group full-width">
                    <div>
                        <label class="form-label">Full Name <span>(must match ID)</span>
                        </label>
                        <div class="input-container">
                            <div class="input-field-wrap">
                                <input type="text" wire:model.live="full_name" class="form-input" placeholder=" "
                                    required>
                                <span class="input-valid-icon" aria-hidden="true"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                        viewBox="0 0 19 19" fill="none">
                                        <path
                                            d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                            fill="#C9DDA0" />
                                    </svg></span>
                            </div>
                        </div>
                    </div>
                    @error('full_name')
                        <span class="error-text">{{ $message }}</span>
                    @enderror
                </div>

                {{-- Stored in freelance_details: service_home_address_line1/2, contact_email, contact_phone, government_id --}}
                <div class="form-group full-width">
                    <h3>Freelance Details</h3>
                    <div>
                        <label class="form-label">Service / Home Address</label>
                        <div class="input-container">
                            <div class="input-field-wrap">
                                <input type="text" wire:model.live="freelance_service_home_address_line1"
                                    class="form-input" placeholder="Address line 1">
                                <span class="input-valid-icon" aria-hidden="true"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                        viewBox="0 0 19 19" fill="none">
                                        <path
                                            d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                            fill="#C9DDA0" />
                                    </svg></span>
                            </div>
                            <div class="input-field-wrap">
                                <input type="text" wire:model.live="freelance_service_home_address_line2"
                                    class="form-input" placeholder="Address line 2">
                                <span class="input-valid-icon" aria-hidden="true"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                        viewBox="0 0 19 19" fill="none">
                                        <path
                                            d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                            fill="#C9DDA0" />
                                    </svg></span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Phone Number</label>
                        <div class="input-container">
                            <div class="input-field-wrap">
                                <input type="tel" wire:model.live="business_phone" class="form-input"
                                    placeholder=" " required>
                                <span class="input-valid-icon" aria-hidden="true"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                        viewBox="0 0 19 19" fill="none">
                                        <path
                                            d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                            fill="#C9DDA0" />
                                    </svg></span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Email Address</label>
                        <div class="input-container">
                            <div class="input-field-wrap">
                                <input type="email" wire:model.live="business_email" class="form-input"
                                    placeholder=" " required>
                                <span class="input-valid-icon" aria-hidden="true"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                        viewBox="0 0 19 19" fill="none">
                                        <path
                                            d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                            fill="#C9DDA0" />
                                    </svg></span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Government ID</label>
                        <p class="business-owner-id-help">Please upload a clear photo or scan of a valid
                            government-issued ID (e.g. passport or driving
                            licence) and a recent UK utility bill, bank statement, or official letter showing your
                            current address. Both documents must be in English and dated within the last 3 months.</p>

                        @php
                            $__boSavedFileEntries = $this->savedDocUploadEntriesForPaths(
                                is_array($government_id_paths ?? null) ? $government_id_paths : [],
                                is_array($government_id_file_names ?? null) ? $government_id_file_names : [],
                                'groomer-spacer.business-owner-id-file',
                            );
                            $__boSavedKey = md5(implode("\0", array_column($__boSavedFileEntries, 'path')));
                        @endphp
                        <input type="hidden" id="government-id-saved-urls-json"
                            value="{{ htmlspecialchars(json_encode($__boSavedFileEntries), ENT_QUOTES, 'UTF-8') }}"
                            wire:key="government-id-saved-urls-json">
                        <script>
                            window.__govSavedFileEntries = @json($__boSavedFileEntries);
                        </script>

                        <x-common.doc-upload upload-id="government-id" wire-model="government_id" :saved-entries="$__boSavedFileEntries"
                            :saved-entries-key="$__boSavedKey" saved-json-id="government-id-saved-urls-json"
                            saved-window-key="__govSavedFileEntries" remove-stored-fn="removeBusinessOwnerStoredFile"
                            empty-title="Choose files or drag & drop them here." browse-label="Browse Files" />
                    </div>
                </div>

                <!-- payout_details col data in json -->
                <div class="form-group full-width">
                    <h3>Payout Details</h3>
                    <div>
                        <label class="form-label">Account Holder Name</label>
                        <div class="input-container">
                            <div class="input-field-wrap">
                                <input type="text" wire:model.live="account_holder_name" class="form-input"
                                    placeholder=" " required>
                                <span class="input-valid-icon" aria-hidden="true"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                        viewBox="0 0 19 19" fill="none">
                                        <path
                                            d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                            fill="#C9DDA0" />
                                    </svg></span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Account Number</label>
                        <div class="input-container">
                            <div class="input-field-wrap">
                                <input type="text" wire:model.live="account_number" class="form-input"
                                    placeholder=" " required>
                                <span class="input-valid-icon" aria-hidden="true"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                        viewBox="0 0 19 19" fill="none">
                                        <path
                                            d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                            fill="#C9DDA0" />
                                    </svg></span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">Sort Code</label>
                        <div class="input-container">
                            <div class="input-field-wrap">
                                <input type="text" wire:model.live="sort_code" class="form-input" placeholder=" "
                                    required>
                                <span class="input-valid-icon" aria-hidden="true"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                        viewBox="0 0 19 19" fill="none">
                                        <path
                                            d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                            fill="#C9DDA0" />
                                    </svg></span>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="form-label">IBAN</label>
                        <div class="input-container">
                            <div class="input-field-wrap">
                                <input type="text" wire:model.live="iban" class="form-input" placeholder=" "
                                    required>
                                <span class="input-valid-icon" aria-hidden="true"><svg
                                        xmlns="http://www.w3.org/2000/svg" width="19" height="19"
                                        viewBox="0 0 19 19" fill="none">
                                        <path
                                            d="M9.5 0C4.275 0 0 4.275 0 9.5C0 14.725 4.275 19 9.5 19C14.725 19 19 14.725 19 9.5C19 4.275 14.725 0 9.5 0ZM7.6 14.25L2.85 9.5L4.1895 8.1605L7.6 11.5615L14.8105 4.351L16.15 5.7L7.6 14.25Z"
                                            fill="#C9DDA0" />
                                    </svg></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- insurance_details col data in json -->
                <div class="form-group full-width">
                    <h3>Insurance Details</h3>
                    <div>
                        <label class="form-label">Insurance Certificate <span>(Optional)</span></label>
                        @php
                            $__insSavedFileEntries = $this->savedDocUploadEntriesForPaths(
                                is_array($insurance_certificate_paths ?? null) ? $insurance_certificate_paths : [],
                                is_array($insurance_certificate_file_names ?? null)
                                    ? $insurance_certificate_file_names
                                    : [],
                                'groomer-spacer.insurance-certificate-file',
                            );
                            $__insSavedKey = md5(implode("\0", array_column($__insSavedFileEntries, 'path')));
                        @endphp
                        <input type="hidden" id="insurance-saved-urls-json"
                            value="{{ htmlspecialchars(json_encode($__insSavedFileEntries), ENT_QUOTES, 'UTF-8') }}"
                            wire:key="insurance-saved-urls-json-freelance">
                        <script>
                            window.__insSavedFileEntries = @json($__insSavedFileEntries);
                        </script>
                        <x-common.doc-upload upload-id="insurance" wire-model="insurance_certificate_upload"
                            :saved-entries="$__insSavedFileEntries" :saved-entries-key="$__insSavedKey" saved-json-id="insurance-saved-urls-json"
                            saved-window-key="__insSavedFileEntries" remove-stored-fn="removeInsuranceStoredFile"
                            empty-title="Choose files or drag & drop them here." browse-label="Browse Files" />
                        @error('insurance_certificate_upload')
                            <span class="error-text">{{ $message }}</span>
                        @enderror
                        @if ($errors->has('insurance_certificate_upload.*'))
                            <span class="error-text">{{ $errors->first('insurance_certificate_upload.*') }}</span>
                        @endif
                    </div>

                </div>
            </div>

            @include('livewire.auth.verify-qualify-accuracy-confirm')

            <!-- Buttons -->
            <div class="form-buttons">
                <x-common.button type="button" label="Back" width="105px" bg-color="#FFFFFF"
                    text-color="#9D9B98" border="1px solid rgba(59, 55, 49, 0.10)" :shadow="false"
                    wire:click="goBack" />
                <x-common.button type="submit" label="Submit" width="105px"
                    bg-color="{{ $this->isPersonalInfoFormValid() ? '#FFC97A' : '#e5e7eb' }}"
                    text-color="{{ $this->isPersonalInfoFormValid() ? '#FFFFFF' : '#9ca3af' }}"
                    loading-target="submitPersonalInfo,government_id,insurance_certificate_upload" :disabled="!$this->isPersonalInfoFormValid()" />
            </div>
        </form>
    </div>
@endif
