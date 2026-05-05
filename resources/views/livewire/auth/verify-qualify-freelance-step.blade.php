@if ($showFreelance)
    <div class="verification-card" wire:key="verify-qualify-freelance">
        <div class="step-heading">
            <h2>Freelance Groomer</h2>
        </div>

        <form wire:submit="submitPersonalInfo" novalidate>
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

                {{-- Stored in freelance_details: service_home_address_line1/2, contact_email, contact_phone, id_verification_images --}}
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
                        <p>Please upload a clear photo or scan of a valid government-issued ID (e.g. passport or driving
                            licence) and a recent UK utility bill, bank statement, or official letter showing your
                            current
                            address. Both documents must be in English and dated within the last 3 months.</p>

                        @php
                            $__boSavedFileEntries = [];
                            foreach ($business_owner_id_images ?? [] as $__p) {
                                if (is_string($__p) && $__p !== '') {
                                    $__boSavedFileEntries[] = [
                                        'path' => $__p,
                                        'url' => route('groomer-spacer.business-owner-id-file', [
                                            't' => \Illuminate\Support\Facades\Crypt::encryptString($__p),
                                        ]),
                                    ];
                                }
                            }
                        @endphp
                        <input type="hidden" id="business-owner-saved-urls-json"
                            value="{{ htmlspecialchars(json_encode($__boSavedFileEntries), ENT_QUOTES, 'UTF-8') }}"
                            wire:key="business-owner-saved-urls-json-freelance">
                        <script>
                            window.__boSavedFileEntries = @json($__boSavedFileEntries);
                        </script>

                        <!-- Custom File Upload Interface (wire:ignore — prevents tab reset on Livewire morph; hidden URLs stay outside) -->
                        <div class="custom-file-upload" wire:ignore>
                            <!-- Tabs -->
                            <div class="upload-tabs">
                                <div>
                                    <button type="button" class="tab-btn active" data-tab="attach"><svg
                                            xmlns="http://www.w3.org/2000/svg" width="11" height="12"
                                            viewBox="0 0 11 12" fill="none">
                                            <path
                                                d="M10.5 6.04469L6.17551 10.5107C5.54818 11.1481 4.70239 11.5037 3.82235 11.5C2.94232 11.4963 2.09936 11.1336 1.47707 10.4909C0.854792 9.8483 0.503611 8.97775 0.500028 8.06891C0.496444 7.16008 0.840748 6.2866 1.45794 5.63874L5.78243 1.17272C5.98895 0.95944 6.23412 0.790259 6.50395 0.674834C6.77378 0.559409 7.06298 0.5 7.35504 0.5C7.64711 0.5 7.93631 0.559409 8.20614 0.674834C8.47597 0.790259 8.72114 0.95944 8.92766 1.17272C9.13418 1.386 9.298 1.63919 9.40977 1.91785C9.52153 2.19652 9.57906 2.49518 9.57906 2.7968C9.57906 3.09842 9.52153 3.39709 9.40977 3.67575C9.298 3.95441 9.13418 4.20761 8.92766 4.42089L4.60317 8.88691C4.3946 9.10231 4.1117 9.22333 3.81673 9.22333C3.52175 9.22333 3.23886 9.10231 3.03028 8.88691C2.8217 8.6715 2.70452 8.37935 2.70452 8.07472C2.70452 7.77009 2.8217 7.47794 3.03028 7.26254L6.96168 3.20304"
                                                stroke="#3B3731" stroke-linecap="round" />
                                        </svg>Attach</button>
                                    <button type="button" class="tab-btn" data-tab="upload"><svg
                                            xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 12 12" fill="none">
                                            <path
                                                d="M10.2778 0.5H1.72222C1.04721 0.5 0.5 1.04721 0.5 1.72222V10.2778C0.5 10.9528 1.04721 11.5 1.72222 11.5H10.2778C10.9528 11.5 11.5 10.9528 11.5 10.2778V1.72222C11.5 1.04721 10.9528 0.5 10.2778 0.5Z"
                                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                            <path
                                                d="M4.16662 5.38878C4.84163 5.38878 5.38884 4.84157 5.38884 4.16656C5.38884 3.49154 4.84163 2.94434 4.16662 2.94434C3.4916 2.94434 2.9444 3.49154 2.9444 4.16656C2.9444 4.84157 3.4916 5.38878 4.16662 5.38878Z"
                                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                            <path
                                                d="M11.5001 7.83358L9.61421 5.94769C9.38501 5.71856 9.07419 5.58984 8.7501 5.58984C8.42601 5.58984 8.11519 5.71856 7.88599 5.94769L2.33344 11.5002"
                                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>Upload</button>

                                </div>
                            </div>

                            <!-- Tab Content -->
                            <div class="tab-content">
                                <!-- Attach Tab -->
                                <div class="tab-pane active" id="attach-tab">
                                    <div class="file-list" id="business-owner-id-file-list" wire:ignore>
                                        <!-- Files will be dynamically added here -->
                                    </div>
                                    <p class="file-list-empty-msg" data-role="file-list-empty">No file attached.</p>
                                </div>

                                <!-- Upload Tab -->
                                <div class="tab-pane" id="upload-tab">
                                    <div class="upload-area" id="business-owner-id-upload-area">
                                        <div>
                                            <p>Choose a file or drag & drop it here.</p>
                                        </div>
                                        <div class="upload-icon">
                                            Browse File
                                        </div>
                                        <input type="file" wire:model="business_owner_id_images"
                                            id="business-owner-id-file-input" class="hidden-input"
                                            accept=".pdf,.jpg,.jpeg,.png" multiple>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                            $__insSavedFileEntries = [];
                            foreach ($insurance_certificate_paths ?? [] as $__p) {
                                if (is_string($__p) && $__p !== '') {
                                    $__insSavedFileEntries[] = [
                                        'path' => $__p,
                                        'url' => route('groomer-spacer.insurance-certificate-file', [
                                            't' => \Illuminate\Support\Facades\Crypt::encryptString($__p),
                                        ]),
                                    ];
                                }
                            }
                        @endphp
                        <input type="hidden" id="insurance-saved-urls-json"
                            value="{{ htmlspecialchars(json_encode($__insSavedFileEntries), ENT_QUOTES, 'UTF-8') }}"
                            wire:key="insurance-saved-urls-json-freelance">
                        <script>
                            window.__insSavedFileEntries = @json($__insSavedFileEntries);
                        </script>
                        <!-- Custom File Upload Interface (wire:ignore — same as registered flow) -->
                        <div class="custom-file-upload" wire:ignore>
                            <!-- Tabs -->
                            <div class="upload-tabs">
                                <div>
                                    <button type="button" class="tab-btn" data-tab="insurance-attach"><svg
                                            xmlns="http://www.w3.org/2000/svg" width="11" height="12"
                                            viewBox="0 0 11 12" fill="none">
                                            <path
                                                d="M10.5 6.04469L6.17551 10.5107C5.54818 11.1481 4.70239 11.5037 3.82235 11.5C2.94232 11.4963 2.09936 11.1336 1.47707 10.4909C0.854792 9.8483 0.503611 8.97775 0.500028 8.06891C0.496444 7.16008 0.840748 6.2866 1.45794 5.63874L5.78243 1.17272C5.98895 0.95944 6.23412 0.790259 6.50395 0.674834C6.77378 0.559409 7.06298 0.5 7.35504 0.5C7.64711 0.5 7.93631 0.559409 8.20614 0.674834C8.47597 0.790259 8.72114 0.95944 8.92766 1.17272C9.13418 1.386 9.298 1.63919 9.40977 1.91785C9.52153 2.19652 9.57906 2.49518 9.57906 2.7968C9.57906 3.09842 9.52153 3.39709 9.40977 3.67575C9.298 3.95441 9.13418 4.20761 8.92766 4.42089L4.60317 8.88691C4.3946 9.10231 4.1117 9.22333 3.81673 9.22333C3.52175 9.22333 3.23886 9.10231 3.03028 8.88691C2.8217 8.6715 2.70452 8.37935 2.70452 8.07472C2.70452 7.77009 2.8217 7.47794 3.03028 7.26254L6.96168 3.20304"
                                                stroke="#3B3731" stroke-linecap="round" />
                                        </svg>Attach</button>
                                    <button type="button" class="tab-btn active" data-tab="insurance-upload"><svg
                                            xmlns="http://www.w3.org/2000/svg" width="12" height="12"
                                            viewBox="0 0 12 12" fill="none">
                                            <path
                                                d="M10.2778 0.5H1.72222C1.04721 0.5 0.5 1.04721 0.5 1.72222V10.2778C0.5 10.9528 1.04721 11.5 1.72222 11.5H10.2778C10.9528 11.5 11.5 10.9528 11.5 10.2778V1.72222C11.5 1.04721 10.9528 0.5 10.2778 0.5Z"
                                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                            <path
                                                d="M4.16662 5.38878C4.84163 5.38878 5.38884 4.84157 5.38884 4.16656C5.38884 3.49154 4.84163 2.94434 4.16662 2.94434C3.4916 2.94434 2.9444 3.49154 2.9444 4.16656C2.9444 4.84157 3.4916 5.38878 4.16662 5.38878Z"
                                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                            <path
                                                d="M11.5001 7.83358L9.61421 5.94769C9.38501 5.71856 9.07419 5.58984 8.7501 5.58984C8.42601 5.58984 8.11519 5.71856 7.88599 5.94769L2.33344 11.5002"
                                                stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>Upload</button>

                                </div>
                            </div>

                            <!-- Tab Content -->
                            <div class="tab-content">
                                <!-- Attach Tab -->
                                <div class="tab-pane" id="insurance-attach-tab">
                                    <div class="file-list" id="insurance-file-list" wire:ignore>
                                        <!-- Files will be dynamically added here -->
                                    </div>
                                    <p class="file-list-empty-msg" data-role="file-list-empty">No file attached.</p>
                                </div>

                                <!-- Upload Tab -->
                                <div class="tab-pane active" id="insurance-upload-tab">
                                    <div class="upload-area" id="insurance-upload-area">
                                        <div>
                                            <p>Choose a file or drag & drop it here.</p>
                                            <span>JPEG, PNG, and PDF formats, up to 50 MB.</span>
                                        </div>
                                        <div class="upload-icon">
                                            Browse File
                                        </div>
                                        <input type="file" wire:model="insurance_certificate_upload"
                                            id="insurance-file-input" class="hidden-input"
                                            accept=".pdf,.jpg,.jpeg,.png" multiple>
                                    </div>
                                </div>
                            </div>
                        </div>
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
                <button type="button" class="back-btn" wire:click="goBack">
                    <span>Back</span>
                </button>
                <button type="submit"
                    class="submit-btn {{ $this->isPersonalInfoFormValid() ? 'btn-active' : 'btn-disabled' }}"
                    wire:loading.attr="disabled" wire:target="submitPersonalInfo">
                    <span wire:loading.remove wire:target="submitPersonalInfo">Submit</span>
                    <span wire:loading wire:target="submitPersonalInfo" class="btn-spinner"
                        aria-hidden="true"></span>
                </button>
            </div>
        </form>
    </div>
@endif
