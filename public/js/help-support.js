/**
 * Help & Support — contact request attachments + submitted-modal helpers
 * Ported from D:\fursgo\support_and_assistance\contact_support.php
 */
(function () {
    function initAttachments() {
        const fileInput = document.getElementById('fileInput');
        const attachBtn = document.getElementById('attachBtn');
        const attachBtnText = document.getElementById('attachBtnText');
        const uploadBox = document.getElementById('uploadBox');
        const fileList = document.getElementById('fileList');
        const fileItemTemplate = document.getElementById('fileItemTemplate');
        const submitRequestBtn = document.getElementById('submitRequestBtn');

        if (!fileInput || !attachBtn || !attachBtnText || !uploadBox || !fileList || !fileItemTemplate) {
            return;
        }

        let attachedFiles = [];
        let fileId = 0;

        function syncAttachState() {
            const hasFiles = attachedFiles.length > 0;
            uploadBox.classList.toggle('has-file', hasFiles);
            attachBtnText.textContent = hasFiles ? 'Attach' : 'Add attachment';
        }

        function resetAttachments() {
            attachedFiles = [];
            fileList.innerHTML = '';
            fileInput.value = '';
            syncAttachState();
        }

        function addFile(file) {
            const id = 'file-' + (++fileId);
            attachedFiles.push({ id: id, file: file });

            const item = fileItemTemplate.content.firstElementChild.cloneNode(true);
            item.dataset.fileId = id;
            // Laravel company_information.css hides .file-item until .is-visible
            item.classList.add('is-visible');
            item.querySelector('.file-name').textContent = file.name;

            const sizeEl = item.querySelector('.file-size');
            const totalKb = Math.max(1, Math.round(file.size / 1024));
            sizeEl.textContent = Math.round(totalKb / 2) + ' KB of ' + totalKb + ' KB • Uploading...';

            setTimeout(function () {
                if (!item.isConnected) return;
                sizeEl.textContent = totalKb + ' KB of ' + totalKb + ' KB';
            }, 1500);

            item.querySelector('.remove-btn').addEventListener('click', function () {
                attachedFiles = attachedFiles.filter(function (entry) {
                    return entry.id !== id;
                });
                item.remove();
                syncAttachState();
            });

            fileList.appendChild(item);
        }

        attachBtn.addEventListener('click', function (e) {
            e.preventDefault();
            e.stopPropagation();
            fileInput.click();
        });

        fileInput.addEventListener('change', function () {
            Array.from(fileInput.files).forEach(addFile);
            fileInput.value = '';
            syncAttachState();
        });

        if (submitRequestBtn) {
            submitRequestBtn.addEventListener('click', function () {
                const subject = document.querySelector('input[placeholder="A short summary of your issue."]');
                const bookingRef = document.querySelector('input[placeholder="Enter your booking ID"]');
                const description = document.querySelector('#bio');
                const category = document.querySelector('input[name="category"]');

                if (
                    subject &&
                    category &&
                    description &&
                    subject.value.trim() !== '' &&
                    category.value.trim() !== '' &&
                    description.value.trim() !== ''
                ) {
                    const openSubmitted = document.querySelector('[data-modal-open="request-submitted-modal"]');
                    if (openSubmitted) openSubmitted.click();

                    const submittedModal = document.getElementById('request-submitted-modal');
                    const requestModal = document.getElementById('request_modal');
                    if (submittedModal) submittedModal.style.display = 'flex';
                    if (requestModal) {
                        requestModal.style.display = 'none';
                        requestModal.classList.remove('active');
                    }

                    subject.value = '';
                    if (bookingRef) bookingRef.value = '';
                    description.value = '';
                    category.value = '';
                    const selectedText = document.querySelector('.selected-text');
                    if (selectedText) selectedText.textContent = 'Select Category';
                    resetAttachments();
                } else {
                    alert('Please fill all required fields.');
                }
            });
        }
    }

    function initSubmittedModal() {
        const requestModal = document.getElementById('request-submitted-modal');
        const helpCentreLink = document.getElementById('helpCentreLink');

        function closeRequestModal() {
            if (requestModal) {
                requestModal.style.display = 'none';
            }
        }

        if (helpCentreLink) {
            helpCentreLink.addEventListener('click', function (e) {
                closeRequestModal();

                const path = window.location.pathname || '';
                if (path.includes('help-and-support') || path.includes('help_and_support.php')) {
                    e.preventDefault();
                    window.scrollTo({ top: 0, behavior: 'smooth' });
                }
            });
        }

        document.querySelectorAll('#request-submitted-modal [data-open-chat]').forEach(function (link) {
            link.addEventListener('click', closeRequestModal);
        });
    }

    function initSearchResults() {
        document.querySelectorAll('.result-item').forEach(function (item) {
            var answer = item.querySelector('.result-answer');
            var readMore = item.querySelector('.result-read-more');
            if (!answer || !readMore) return;

            answer.classList.add('is-collapsed');

            if (answer.scrollHeight > answer.clientHeight + 1) {
                readMore.classList.add('is-visible');
            } else {
                answer.classList.remove('is-collapsed');
            }

            readMore.addEventListener('click', function () {
                answer.classList.remove('is-collapsed');
                readMore.classList.remove('is-visible');
            });
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initAttachments();
        initSubmittedModal();
        initSearchResults();
    });
})();
