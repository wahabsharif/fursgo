    <div class="container mb-5 mt-5">
        <div class="row">
            <div class="col-lg-1"></div>
            <div class="col-lg-10">
                <div class="row">
                    <div class="col-lg-12">
                        <div class="top-head d-flex flex-column align-items-center justify-content-center">
                            <h1 class="large-font">Help & Support Center</h1>
                            <form action="{{ route('search') }}">
                                <div class="search-wrapper">
                                    <input
                                        type="text"
                                        placeholder="Search for topics like refunds, bookings, payments." name="search_results"
                                        value="{{ request('search_results') }}"
                                        class="normal-font-weight">
                                    <button class="search-btn">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="42" height="42" viewBox="0 0 42 42" fill="none">
                                            <circle cx="21" cy="21" r="21" fill="#FFC97A" />
                                            <path d="M19.7354 14.75C22.4886 14.75 24.7207 16.9821 24.7207 19.7354C24.7207 21.1492 24.1329 22.4248 23.1865 23.333C22.2901 24.1932 21.0751 24.7207 19.7354 24.7207C16.9821 24.7207 14.75 22.4886 14.75 19.7354C14.75 16.982 16.982 14.75 19.7354 14.75Z" stroke="white" stroke-width="1.5" />
                                            <path d="M28.4697 29.5303C28.7626 29.8232 29.2374 29.8232 29.5303 29.5303C29.8232 29.2374 29.8232 28.7626 29.5303 28.4697L29 29L28.4697 29.5303ZM23.7059 23.7059L23.1755 24.2362L28.4697 29.5303L29 29L29.5303 28.4697L24.2362 23.1755L23.7059 23.7059Z" fill="white" />
                                        </svg>
                                    </button>
                                </div>
                            </form>
                            <div class="common-topics d-flex align-items-center justify-content-center gap-20 mb-3">
                                <p>Common Topics</p>
                                <p class="bg cursor">Bookings</p>
                                <p class="bg cursor">Payments</p>
                                <p class="bg cursor">Account</p>
                                <p class="bg cursor">Pets</p>
                                <p class="bg cursor">Policies</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-1"></div>
                    <div class="col-lg-10">
                        <div class="d-flex align-items-center mt-5">
                            <h1 class="large-font">Results</h1>
                        </div>
                        <div class="bg-div d-flex flex-column gap-30 mt-5">
                            <div>
                                <p class="normal-font-bold">How do I book a grooming appointment?</p>
                                <p class="simple-font mt-3">Booking through FursGo is straightforward. Simply choose your location, select the service you’re looking for, and pick a time...</p>
                            </div>
                            <a href="" class="underline normal-font-bold">Read more</a>
                        </div>
                        <div class="no-bg bg-div d-flex flex-column gap-30 mt-5">
                            <div>
                                <p class="normal-font-bold">Can I reschedule or cancel a booking?</p>
                                <p class="simple-font mt-3">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae.</p>
                            </div>
                        </div>
                        <div class="no-bg bg-div d-flex flex-column gap-30 mt-5">
                            <div>
                                <p class="normal-font-bold">Where can I see my upcoming bookings?</p>
                                <p class="simple-font mt-3">Sed ut perspiciatis unde omnis iste natus error sit voluptatem accusantium doloremque laudantium, totam rem aperiam, eaque ipsa quae ab illo inventore veritatis et quasi architecto beatae.</p>
                            </div>
                        </div>

                        <div class="d-flex justify-content-center mt-5">
                            <button class="btn-custom btn-no-bg">Load More</button>
                        </div>
                    </div>
                    <div class="col-lg-1"></div>
                </div>
            </div>
            <div class="col-lg-1"></div>
            <div class="col-lg-1"></div>
            <div class="col-lg-10">
                <x-ui.contact-support />
            </div>
            <div class="col-lg-1"></div>

        </div>
    </div>

@push('styles')
<link rel="stylesheet" href="{{ asset('css/company_information.css') }}">
<style>
    #request-submitted-modal .modal-content.size {
        width: 645px;
    }
</style>
@endpush

@push('script')
<script>
    document.addEventListener('click', (e) => {
        const openTrigger = e.target.closest('[data-modal-open]');
        if (openTrigger) {
            const modal = document.getElementById(openTrigger.dataset.modalOpen);
            if (modal) modal.style.display = 'flex';
        }

        if (e.target.closest('[data-modal-close]') || e.target.closest('[data-modal-submit-close]')) {
            const modal = e.target.closest('.modal');
            if (modal) modal.style.display = 'none';
        }

        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
        }
    });

    function initHelpCentreFileUpload() {
        const fileInput = document.getElementById('fileInput');
        const attachBtn = document.getElementById('attachBtn');
        const uploadBtn = document.getElementById('uploadBtn');
        const fileItem = document.getElementById('fileItem');
        const fileName = document.getElementById('fileName');
        const fileSize = document.getElementById('fileSize');
        const removeBtn = document.getElementById('removeBtn');

        if (!attachBtn || !uploadBtn || !fileInput || attachBtn.dataset.helpUploadBound === '1') return;

        attachBtn.dataset.helpUploadBound = '1';
        uploadBtn.dataset.helpUploadBound = '1';

        attachBtn.onclick = uploadBtn.onclick = () => fileInput.click();

        fileInput.onchange = () => {
            const file = fileInput.files[0];
            if (!file || !fileItem || !fileName || !fileSize) return;

            fileItem.style.display = 'flex';
            fileName.textContent = file.name;
            fileSize.textContent = Math.round(file.size / 1024) + ' KB • Uploading...';

            setTimeout(() => {
                fileSize.textContent = Math.round(file.size / 1024) + ' KB of ' + Math.round(file.size / 1024) + ' KB';
            }, 1500);
        };

        if (removeBtn) {
            removeBtn.onclick = () => {
                fileInput.value = '';
                if (fileItem) fileItem.style.display = 'none';
            };
        }
    }

    document.addEventListener('DOMContentLoaded', initHelpCentreFileUpload);
    document.addEventListener('livewire:navigated', initHelpCentreFileUpload);

    function initContactSupportRequest() {
        const submitBtn = document.getElementById('submitRequestBtn');
        if (!submitBtn || submitBtn.dataset.bound === '1') return;
        submitBtn.dataset.bound = '1';

        submitBtn.addEventListener('click', function () {
            const subject = document.querySelector('input[placeholder="A short summary of your issue."]');
            const bookingRef = document.querySelector('input[placeholder="Enter your booking ID"]');
            const description = document.querySelector('#bio');
            const category = document.querySelector('input[name="category"]');

            if (
                subject && category && description &&
                subject.value.trim() !== '' &&
                category.value.trim() !== '' &&
                description.value.trim() !== ''
            ) {
                const successModal = document.getElementById('request-submitted-modal');
                const requestModal = document.getElementById('request_modal');

                if (successModal) successModal.style.display = 'flex';
                if (requestModal) {
                    requestModal.style.display = 'none';
                    requestModal.classList.remove('active');
                }

                subject.value = '';
                if (bookingRef) bookingRef.value = '';
                description.value = '';
                category.value = '';

                const selectedText = document.querySelector('#request_modal .selected-text');
                if (selectedText) selectedText.textContent = 'Select Category';

                const fileInput = document.getElementById('fileInput');
                const fileItem = document.getElementById('fileItem');
                if (fileInput) fileInput.value = '';
                if (fileItem) fileItem.style.display = 'none';
            } else {
                alert('Please fill all required fields.');
            }
        });

        const requestModal = document.getElementById('request-submitted-modal');
        const helpCentreLink = document.getElementById('helpCentreLink');

        function closeRequestModal() {
            if (requestModal) requestModal.style.display = 'none';
        }

        if (helpCentreLink && helpCentreLink.dataset.bound !== '1') {
            helpCentreLink.dataset.bound = '1';
            helpCentreLink.addEventListener('click', function (e) {
                closeRequestModal();
                if (window.location.pathname.includes('help-and-support')) {
                    e.preventDefault();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
        }

        document.querySelectorAll('#request-submitted-modal [data-open-chat]').forEach(function (link) {
            if (link.dataset.bound === '1') return;
            link.dataset.bound = '1';
            link.addEventListener('click', closeRequestModal);
        });
    }

    document.addEventListener('DOMContentLoaded', initContactSupportRequest);
    document.addEventListener('livewire:navigated', initContactSupportRequest);
</script>
@endpush
