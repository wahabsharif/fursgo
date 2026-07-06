window.VqDocUpload =
    window.VqDocUpload ||
    (function () {
        const instances = {};
        let delegated = false;
        let morphHooked = false;

        window.__uploadSimCompletedFps =
            window.__uploadSimCompletedFps || new Set();
        window.__uploadSimIntervalByFp =
            window.__uploadSimIntervalByFp || new Map();

        function fileFingerprint(file) {
            return file.name + "\0" + file.size + "\0" + file.lastModified;
        }

        function formatFileSize(bytes) {
            if (!bytes) return "0 Bytes";
            const k = 1024;
            const sizes = ["Bytes", "KB", "MB", "GB"];
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            return (
                parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i]
            );
        }

        const MAX_BYTES_DEFAULT = 51200 * 1024;

        function formatMaxSizeLabel(bytes) {
            const mb = bytes / (1024 * 1024);
            if (mb >= 1 && Math.abs(mb - Math.round(mb)) < 0.01) {
                return Math.round(mb) + " MB";
            }
            return formatFileSize(bytes);
        }

        function maxBytesFromWidget(widget) {
            if (!widget) return MAX_BYTES_DEFAULT;
            const raw = widget.getAttribute("data-max-bytes");
            const parsed = raw ? parseInt(raw, 10) : NaN;
            return Number.isFinite(parsed) && parsed > 0
                ? parsed
                : MAX_BYTES_DEFAULT;
        }

        function isAllowedMime(file, accept) {
            if (!accept) return true;
            const parts = accept
                .split(",")
                .map((s) => s.trim().toLowerCase())
                .filter(Boolean);
            if (!parts.length) return true;
            const ext = "." + (file.name.split(".").pop() || "").toLowerCase();
            const type = (file.type || "").toLowerCase();
            return parts.some((part) => {
                if (part.startsWith(".")) return ext === part;
                if (part.endsWith("/*")) {
                    return type.startsWith(part.slice(0, -1));
                }
                return type === part;
            });
        }

        function partitionPickedFiles(files, widget, input) {
            const maxBytes = maxBytesFromWidget(widget);
            const maxLabel = formatMaxSizeLabel(maxBytes);
            const accept =
                input?.getAttribute("accept") ||
                widget
                    ?.querySelector('input[type="file"]')
                    ?.getAttribute("accept") ||
                "";
            const valid = [];
            const errors = [];
            files.forEach((file) => {
                if (file.size > maxBytes) {
                    errors.push(
                        `"${file.name}" must not be greater than ${maxLabel}.`,
                    );
                    return;
                }
                if (!isAllowedMime(file, accept)) {
                    errors.push(
                        `"${file.name}" must be a PDF, JPG, JPEG, or PNG file.`,
                    );
                    return;
                }
                valid.push(file);
            });
            return { valid, errors };
        }

        function showUploadErrors(uploadId, messages) {
            const errorEl = document.getElementById(uploadId + "-upload-error");
            const widget = els(uploadId).widget;
            if (!errorEl) return;
            if (!messages.length) {
                errorEl.hidden = true;
                errorEl.textContent = "";
                widget?.classList.remove("vq-doc-upload--has-error");
                return;
            }
            errorEl.hidden = false;
            errorEl.textContent = messages.join(" ");
            widget?.classList.add("vq-doc-upload--has-error");
        }

        function resetInputWithoutInvalid(uploadId) {
            const state = getInstance(uploadId);
            const input = els(uploadId).input;
            if (!input) return;
            if (state.rawFiles.length) {
                syncInputFiles(uploadId);
                return;
            }
            input.value = "";
        }

        function processNewFiles(uploadId, picked) {
            const widget = els(uploadId).widget;
            const input = els(uploadId).input;
            const { valid, errors } = partitionPickedFiles(
                picked,
                widget,
                input,
            );
            if (errors.length) {
                showUploadErrors(uploadId, errors);
            } else {
                showUploadErrors(uploadId, []);
            }
            if (!valid.length) {
                resetInputWithoutInvalid(uploadId);
                return { synced: false };
            }
            const synced = mergePicks(uploadId, valid);
            rebuildListUI(uploadId);
            if (synced && valid.length) {
                queueLivewireUpload(uploadId, valid, input);
            }
            return { synced, valid };
        }

        function getFileExtension(name) {
            return name.split(".").pop().toUpperCase();
        }

        function pdfIconSvgHtml() {
            return `<svg xmlns="http://www.w3.org/2000/svg" width="21" height="25" viewBox="0 0 21 25" fill="none"
    aria-hidden="true">
    <path
        d="M5.04074 24.501H15.9593C17.1635 24.501 18.3185 24.0226 19.1701 23.1711C20.0216 22.3195 20.5 21.1646 20.5 19.9603V12.7859C20.5004 11.5818 20.0226 10.4268 19.1715 9.57499L11.4276 1.82979C11.0059 1.40815 10.5053 1.0737 9.95439 0.845536C9.40346 0.61737 8.81297 0.499957 8.21666 0.5H5.04074C3.83646 0.5 2.6815 0.978398 1.82995 1.82995C0.978398 2.6815 0.5 3.83646 0.5 5.04074V19.9603C0.5 21.1646 0.978398 22.3195 1.82995 23.1711C2.6815 24.0226 3.83646 24.501 5.04074 24.501Z"
        stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
    <path
        d="M10.0952 0.966797V8.30982C10.0952 8.99798 10.3686 9.65795 10.8552 10.1446C11.3418 10.6312 12.0018 10.9045 12.6899 10.9045H20.0355"
        stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
    <path
        d="M4.33759 18.3383V17.041M4.33759 17.041V14.4463H5.63494C5.97902 14.4463 6.30901 14.583 6.55231 14.8263C6.79561 15.0696 6.93229 15.3996 6.93229 15.7436C6.93229 16.0877 6.79561 16.4177 6.55231 16.661C6.30901 16.9043 5.97902 17.041 5.63494 17.041H4.33759ZM14.7164 18.3383V16.7167M14.7164 16.7167V14.4463H16.6624M14.7164 16.7167H16.6624M9.527 18.3383V14.4463H10.1757C10.6918 14.4463 11.1868 14.6513 11.5517 15.0163C11.9167 15.3812 12.1217 15.8762 12.1217 16.3923C12.1217 16.9084 11.9167 17.4034 11.5517 17.7684C11.1868 18.1333 10.6918 18.3383 10.1757 18.3383H9.527Z"
        stroke="#9D9B98" stroke-linecap="round" stroke-linejoin="round" />
</svg>`;
        }

        function getFileIcon(file) {
            if (file.type.startsWith("image/")) {
                return `<img src="${URL.createObjectURL(file)}" class="file-thumbnail" alt="${file.name}">`;
            }
            if (
                file.type === "application/pdf" ||
                getFileExtension(file.name) === "PDF"
            ) {
                return `<div class="file-icon file-icon--pdf">${pdfIconSvgHtml()}</div>`;
            }
            return `<div class="file-icon">${getFileExtension(file.name)}</div>`;
        }

        function storagePathFromPublicUrl(url) {
            try {
                const u = new URL(url, window.location.origin);
                const i = u.pathname.indexOf("/storage/");
                if (i === -1) return "";
                return decodeURIComponent(
                    u.pathname.slice(i + "/storage/".length),
                );
            } catch (e) {
                return "";
            }
        }

        function getInstance(uploadId) {
            if (!instances[uploadId]) {
                instances[uploadId] = {
                    rawFiles: [],
                    uploadedFiles: [],
                    ignoreNextChange: false,
                    dialogOpening: false,
                    savedSnap: "",
                };
            }
            return instances[uploadId];
        }

        function els(uploadId) {
            const widget = document.getElementById(uploadId + "-upload-widget");
            return elsForWidget(widget);
        }

        function elsForWidget(widget) {
            const uploadId = widget?.dataset?.uploadId || "";
            const input =
                (uploadId
                    ? document.getElementById(uploadId + "-file-input")
                    : null) ||
                widget?.querySelector('input[type="file"]') ||
                null;
            return {
                widget,
                uploadId,
                trigger:
                    widget?.querySelector(".vq-doc-upload__header") || null,
                area: widget?.querySelector(".vq-doc-upload__body") || null,
                empty: widget?.querySelector(".vq-doc-upload__empty") || null,
                list:
                    widget?.querySelector(".vq-doc-upload__file-list") || null,
                input,
            };
        }

        function configFromWidget(widget) {
            if (!widget) return null;
            return {
                uploadId: widget.dataset.uploadId,
                savedJsonId: widget.dataset.savedJsonId || "",
                savedWindowKey: widget.dataset.savedWindowKey || "",
                removeStoredFn: widget.dataset.removeStoredFn || "",
                wireModel: widget.dataset.wireModel || "",
            };
        }

        function widgetForWireModel(wireModel) {
            if (!wireModel) return null;
            return document.querySelector(
                `[data-vq-doc-upload][data-wire-model="${wireModel}"]`,
            );
        }

        function parseSavedEntriesRaw(raw) {
            if (!Array.isArray(raw)) return [];
            return raw
                .map((item) => {
                    if (typeof item === "string") {
                        const path = storagePathFromPublicUrl(item);
                        return {
                            path: path || "",
                            url: item,
                        };
                    }
                    if (item && typeof item.url === "string") {
                        return {
                            path:
                                item.path ||
                                storagePathFromPublicUrl(item.url) ||
                                "",
                            url: item.url,
                            name:
                                typeof item.name === "string"
                                    ? item.name.trim()
                                    : "",
                        };
                    }
                    return null;
                })
                .filter(Boolean);
        }

        function readWidgetSavedRaw(widget) {
            if (!widget) return [];
            const encoding = widget.getAttribute("data-saved-encoding");
            const encoded = widget.getAttribute("data-saved-entries");
            if (!encoded) return [];
            try {
                const json = encoding === "base64" ? atob(encoded) : encoded;
                const parsed = JSON.parse(json);
                return Array.isArray(parsed) ? parsed : [];
            } catch (e) {
                return [];
            }
        }

        function decodeHtmlEntities(str) {
            if (!str || typeof str !== "string") {
                return "";
            }
            const ta = document.createElement("textarea");
            ta.innerHTML = str;
            return ta.value;
        }

        function normalizePathForLookup(path) {
            return String(path || "")
                .trim()
                .replace(/\\/g, "/")
                .replace(/^\/+/, "");
        }

        function storagePathsMatch(left, right) {
            return (
                normalizePathForLookup(left) === normalizePathForLookup(right)
            );
        }

        function normalizedStoragePath(path, url) {
            const fromPath = typeof path === "string" ? path.trim() : "";
            if (fromPath !== "") {
                return fromPath.replace(/\\/g, "/");
            }

            return storagePathFromPublicUrl(url || "").replace(/\\/g, "/");
        }

        function dedupeSavedEntries(entries) {
            const seen = new Set();
            const out = [];
            (entries || []).forEach((entry) => {
                if (!entry || typeof entry.url !== "string") {
                    return;
                }

                const path = normalizedStoragePath(entry.path, entry.url);
                const key = path !== "" ? path : entry.url;
                if (seen.has(key)) {
                    return;
                }

                seen.add(key);
                out.push({
                    path: path !== "" ? path : entry.path || "",
                    url: entry.url,
                    name:
                        typeof entry.name === "string" ? entry.name.trim() : "",
                });
            });

            return out;
        }

        function readSavedEntriesFromJson(cfg) {
            if (!cfg?.savedJsonId) {
                return null;
            }
            const hid = document.getElementById(cfg.savedJsonId);
            if (!hid) {
                return null;
            }
            try {
                const raw = decodeHtmlEntities(hid.value || "[]");
                const parsed = JSON.parse(raw);
                return Array.isArray(parsed)
                    ? parseSavedEntriesRaw(parsed)
                    : [];
            } catch (e) {
                return [];
            }
        }

        function readSavedEntries(cfg, widget) {
            if (cfg?.savedJsonId) {
                const hid = document.getElementById(cfg.savedJsonId);
                if (hid) {
                    try {
                        const raw = decodeHtmlEntities(hid.value || "[]");
                        const parsed = JSON.parse(raw);
                        const entries = Array.isArray(parsed)
                            ? parseSavedEntriesRaw(parsed)
                            : [];
                        return dedupeSavedEntries(entries);
                    } catch (e) {
                        return [];
                    }
                }
            }

            const fromWidget = parseSavedEntriesRaw(readWidgetSavedRaw(widget));
            if (fromWidget.length > 0) {
                return dedupeSavedEntries(fromWidget);
            }

            if (cfg?.savedWindowKey) {
                const fromWin = window[cfg.savedWindowKey];
                if (Array.isArray(fromWin) && fromWin.length > 0) {
                    return dedupeSavedEntries(parseSavedEntriesRaw(fromWin));
                }
            }

            return [];
        }

        function syncWidgetSavedState(cfg, widget) {
            if (!cfg || !widget) {
                return;
            }
            const entries = readSavedEntriesFromJson(cfg);
            if (entries === null) {
                return;
            }
            const raw = entries.map((entry) => ({
                path: entry.path,
                url: entry.url,
                name: entry.name || "",
            }));
            widget.setAttribute(
                "data-saved-entries",
                btoa(JSON.stringify(raw)),
            );
            if (cfg.savedWindowKey) {
                window[cfg.savedWindowKey] = raw;
            }
        }

        function optimisticallyRemoveFromSavedJson(cfg, widget, storagePath) {
            if (!cfg?.savedJsonId || !storagePath) {
                return;
            }
            const hid = document.getElementById(cfg.savedJsonId);
            if (!hid) {
                return;
            }
            try {
                let parsed = JSON.parse(decodeHtmlEntities(hid.value || "[]"));
                if (!Array.isArray(parsed)) {
                    parsed = [];
                }
                parsed = parsed.filter((item) => {
                    if (typeof item === "string") {
                        return !storagePathsMatch(item, storagePath);
                    }
                    if (item && typeof item === "object") {
                        return !storagePathsMatch(item.path || "", storagePath);
                    }
                    return true;
                });
                hid.value = JSON.stringify(parsed);
                syncWidgetSavedState(cfg, widget);
            } catch (e) {
                /* ignore */
            }
        }

        function livewireComponent(el) {
            if (!window.Livewire) {
                return null;
            }
            const root = el
                ? el.closest("[wire\\:id]")
                : document.querySelector("[wire\\:id]");
            if (!root) {
                return null;
            }
            const wid = root.getAttribute("wire:id");
            return wid ? Livewire.find(wid) : null;
        }

        function invokeLivewire(wire, method, ...args) {
            if (!wire) {
                return null;
            }
            const callFn = wire.$call || wire.call;
            if (typeof callFn !== "function") {
                return null;
            }
            return callFn.call(wire, method, ...args);
        }

        function callLivewire(method, contextEl, ...args) {
            let el = null;
            let callArgs = args;
            if (contextEl && contextEl.nodeType === 1) {
                el = contextEl;
            } else {
                callArgs = [contextEl, ...args];
            }
            return invokeLivewire(livewireComponent(el), method, ...callArgs);
        }

        function pushFilesToLivewire(uploadId, pickedFiles) {
            if (!pickedFiles || !pickedFiles.length) {
                return false;
            }
            const widget = els(uploadId).widget;
            const input = els(uploadId).input;
            const cfg = configFromWidget(widget);
            if (!cfg?.wireModel) {
                return false;
            }
            const wire = livewireComponent(input || widget);
            if (!wire) {
                return false;
            }
            const uploadFn = wire.$upload || wire.upload;
            const uploadMultipleFn =
                wire.$uploadMultiple || wire.uploadMultiple;
            const model = cfg.wireModel;
            if (isSingleMode(widget)) {
                if (typeof uploadFn !== "function") {
                    return false;
                }
                uploadFn.call(wire, model, pickedFiles[0]);
                return true;
            }
            if (typeof uploadMultipleFn === "function") {
                uploadMultipleFn.call(wire, model, pickedFiles);
                return true;
            }
            if (typeof uploadFn === "function") {
                pickedFiles.forEach((file) => uploadFn.call(wire, model, file));
                return true;
            }
            return false;
        }

        function clearUploadSimForFile(file) {
            if (!file) return;
            const fp = fileFingerprint(file);
            const iv = window.__uploadSimIntervalByFp.get(fp);
            if (iv != null) {
                clearInterval(iv);
                window.__uploadSimIntervalByFp.delete(fp);
            }
            window.__uploadSimCompletedFps.delete(fp);
        }

        function simulateUpload(fileItemData) {
            const file = fileItemData.file;
            if (!file) return;
            const fp = fileFingerprint(file);
            const total = file.size;

            if (window.__uploadSimCompletedFps.has(fp)) {
                const pt = fileItemData.element.querySelector(
                    ".file-progress-text",
                );
                if (pt) {
                    pt.textContent = `${formatFileSize(total)} • Uploaded`;
                }
                return;
            }

            const prevIv = window.__uploadSimIntervalByFp.get(fp);
            if (prevIv != null) clearInterval(prevIv);

            let uploaded = 0;
            const iv = setInterval(() => {
                const pt = fileItemData.element.querySelector(
                    ".file-progress-text",
                );
                if (!pt || !pt.isConnected) {
                    clearInterval(iv);
                    window.__uploadSimIntervalByFp.delete(fp);
                    return;
                }
                uploaded += Math.random() * total * 0.1;
                if (uploaded >= total) {
                    clearInterval(iv);
                    window.__uploadSimIntervalByFp.delete(fp);
                    window.__uploadSimCompletedFps.add(fp);
                    pt.textContent = `${formatFileSize(total)} • Uploaded`;
                } else {
                    pt.textContent = `${formatFileSize(uploaded)} of ${formatFileSize(total)} • Uploading...`;
                }
            }, 200);
            window.__uploadSimIntervalByFp.set(fp, iv);
        }

        function animateRemoveFileItem(fileItem, done) {
            if (!fileItem || fileItem.classList.contains("file-item--removing"))
                return;
            const height = fileItem.offsetHeight;
            fileItem.style.maxHeight = height + "px";
            fileItem.classList.add("file-item--removing");
            requestAnimationFrame(() => {
                fileItem.style.maxHeight = "0";
            });
            let finished = false;
            const finish = () => {
                if (finished) return;
                finished = true;
                fileItem.removeEventListener("transitionend", onTransitionEnd);
                if (fileItem.parentNode) {
                    fileItem.remove();
                }
                if (typeof done === "function") {
                    done();
                }
            };
            const onTransitionEnd = (e) => {
                if (e.target !== fileItem) return;
                finish();
            };
            fileItem.addEventListener("transitionend", onTransitionEnd);
            setTimeout(finish, 360);
        }

        function handleStoredFileRemove(removeBtn, widget, cfg) {
            const fileItem = removeBtn.closest(".file-item");
            const uploadId = widget && widget.dataset.uploadId;
            const storagePath = removeBtn.getAttribute("data-storage-path");
            if (!fileItem || !storagePath || !cfg || !cfg.removeStoredFn)
                return;
            if (fileItem.classList.contains("file-item--removing")) return;

            removeBtn.disabled = true;

            const fn = window[cfg.removeStoredFn];
            const onSuccess = () => {
                optimisticallyRemoveFromSavedJson(cfg, widget, storagePath);
                if (uploadId) {
                    getInstance(uploadId).savedSnap = normalizeSavedFingerprint(
                        cfg,
                        widget,
                    );
                }
                animateRemoveFileItem(fileItem, () => {
                    if (uploadId) {
                        updateUIForWidget(widget);
                    }
                });
            };
            const onFailure = () => {
                removeBtn.disabled = false;
            };

            if (typeof fn !== "function") {
                onFailure();
                return;
            }

            try {
                const result = fn(storagePath);
                if (result && typeof result.then === "function") {
                    result.then(onSuccess).catch(onFailure);
                } else {
                    onSuccess();
                }
            } catch (error) {
                onFailure();
            }
        }

        function createFileItem(file, uploadId, onRemove) {
            const id =
                Date.now() + "_" + Math.random().toString(36).substr(2, 9);
            const div = document.createElement("div");
            div.className = "file-item";
            div.dataset.fileId = id;
            div.innerHTML = `
<div class="file-info">
    ${getFileIcon(file)}
    <div class="file-details">
        <div class="file-name">${file.name}</div>
        <div class="file-progress-text">0 KB of ${formatFileSize(file.size)} • Uploading...</div>
    </div>
</div>
<button class="file-remove" type="button" aria-label="Remove">
    <svg width="16" height="16" viewBox="0 0 16 16" fill="none">
        <path d="M12 4L4 12M4 4L12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
    </svg>
</button>`;
            div.querySelector(".file-remove").addEventListener("click", (e) => {
                e.preventDefault();
                e.stopPropagation();
                if (div.classList.contains("file-item--removing")) return;
                const btn = e.currentTarget;
                btn.disabled = true;

                const onFailure = () => {
                    btn.disabled = false;
                };

                const onSuccess = () => {
                    animateRemoveFileItem(div, () => {
                        if (uploadId) {
                            updateUI(uploadId);
                        }
                    });
                };

                try {
                    const result = onRemove(id);
                    if (result && typeof result.then === "function") {
                        result.then(onSuccess).catch(onFailure);
                    } else if (result === false) {
                        onFailure();
                    } else {
                        onSuccess();
                    }
                } catch (error) {
                    onFailure();
                }
            });
            return {
                element: div,
                id,
                file,
            };
        }

        function appendSavedRows(cfg, listEl, widget, entriesOverride) {
            const entries = Array.isArray(entriesOverride)
                ? entriesOverride
                : readSavedEntries(cfg, widget);
            if (!entries.length || !listEl) return;

            const existingPaths = new Set();
            listEl.querySelectorAll("[data-saved-path]").forEach((row) => {
                const p = row.getAttribute("data-saved-path");
                if (p) {
                    existingPaths.add(p.replace(/\\/g, "/"));
                }
            });

            entries.forEach((entry) => {
                const storagePath = normalizedStoragePath(
                    entry.path,
                    entry.url,
                );
                if (storagePath !== "") {
                    const normalized = storagePath.replace(/\\/g, "/");
                    if (
                        existingPaths.has(normalized) ||
                        existingPaths.has(storagePath)
                    ) {
                        return;
                    }
                    existingPaths.add(normalized);
                }

                const url = entry.url;
                const displayName =
                    (typeof entry.name === "string" && entry.name.trim() !== ""
                        ? entry.name.trim()
                        : "") ||
                    decodeURIComponent(
                        (storagePath && storagePath.split("/").pop()) ||
                            (url.split("/").pop() || "file").split("?")[0],
                    );
                const div = document.createElement("div");
                div.className = "file-item file-item--saved";
                if (storagePath) {
                    div.setAttribute("data-saved-path", storagePath);
                }
                const isImg = /\.(jpe?g|png|gif|webp|bmp|heic)$/i.test(
                    displayName,
                );
                const isPdf =
                    /\.pdf$/i.test(displayName) ||
                    getFileExtension(displayName) === "PDF";
                const thumb = isImg
                    ? `<img src="${url}" class="file-thumbnail" alt="" loading="lazy">`
                    : isPdf
                      ? `<div class="file-icon file-icon--pdf">${pdfIconSvgHtml()}</div>`
                      : `<div class="file-icon">${getFileExtension(displayName)}</div>`;
                div.innerHTML = `
<div class="file-info">
    ${thumb}
    <div class="file-details">
        <div class="file-name"><a href="${url}" target="_blank" rel="noopener noreferrer">${displayName}</a></div>
        <div class="file-progress-text">Uploaded</div>
    </div>
</div>`;
                if (storagePath && cfg.removeStoredFn) {
                    const btn = document.createElement("button");
                    btn.type = "button";
                    btn.className = "file-remove";
                    btn.setAttribute("data-storage-path", storagePath);
                    btn.title = "Remove";
                    btn.setAttribute("aria-label", "Remove");
                    btn.innerHTML = `<svg width="16" height="16" viewBox="0 0 16 16" fill="none">
    <path d="M12 4L4 12M4 4L12 12" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" />
</svg>`;
                    div.appendChild(btn);
                }
                listEl.appendChild(div);
            });
        }

        function updateUI(uploadId) {
            updateUIForWidget(els(uploadId).widget);
        }

        function inputMatchesRaw(input, rawFiles) {
            const current = Array.from(input?.files || []);
            if (current.length !== rawFiles.length) return false;
            const rawFps = new Set(rawFiles.map(fileFingerprint));
            return current.every((f) => rawFps.has(fileFingerprint(f)));
        }

        function syncInputFiles(uploadId) {
            const state = getInstance(uploadId);
            const input = els(uploadId).input;
            if (!input) return false;
            if (inputMatchesRaw(input, state.rawFiles)) return false;
            const dt = new DataTransfer();
            state.rawFiles.forEach((f) => dt.items.add(f));
            state.ignoreNextChange = true;
            input.files = dt.files;
            return true;
        }

        function isSingleMode(widget) {
            return widget?.getAttribute("data-single") === "1";
        }

        function clearRawUploadState(uploadId) {
            const state = getInstance(uploadId);
            state.rawFiles.forEach((file) => clearUploadSimForFile(file));
            state.rawFiles = [];
            state.uploadedFiles = [];
        }

        function appendPicks(uploadId, newlyPickedFiles) {
            const state = getInstance(uploadId);
            const widget = els(uploadId).widget;
            const single = isSingleMode(widget);
            if (single) {
                clearRawUploadState(uploadId);
                const file = newlyPickedFiles[0];
                if (file) {
                    state.rawFiles = [file];
                }
                return;
            }
            const seen = new Set(state.rawFiles.map(fileFingerprint));
            newlyPickedFiles.forEach((f) => {
                const fp = fileFingerprint(f);
                if (!seen.has(fp)) {
                    seen.add(fp);
                    state.rawFiles.push(f);
                }
            });
        }

        function mergePicks(uploadId, newlyPickedFiles) {
            appendPicks(uploadId, newlyPickedFiles);
            return syncInputFiles(uploadId);
        }

        function rebuildListUI(uploadId) {
            const widget = els(uploadId).widget;
            rebuildListUIForWidget(widget);
        }

        function rebuildListUIForWidget(widget) {
            if (!widget) return;
            const e = elsForWidget(widget);
            const cfg = configFromWidget(widget);
            const uploadId = e.uploadId;
            const state = getInstance(uploadId);
            const listEl = e.list;
            if (!listEl || !cfg) return;
            listEl.replaceChildren();
            state.uploadedFiles = [];
            const single = isSingleMode(widget);
            if (!single || state.rawFiles.length === 0) {
                appendSavedRows(cfg, listEl, widget);
            }
            state.rawFiles.forEach((file) => {
                const item = createFileItem(file, uploadId, (removedId) => {
                    const removed = state.uploadedFiles.find(
                        (x) => x.id === removedId,
                    );
                    if (!removed || !removed.file) {
                        return Promise.reject(new Error("Missing file"));
                    }

                    const pendingClearCall =
                        widget?.dataset?.pendingClearCall || "";
                    let livewireCall = null;
                    if (isSingleMode(widget) && pendingClearCall) {
                        livewireCall = callLivewire(
                            pendingClearCall,
                            widget,
                            false,
                        );
                    } else if (cfg.wireModel && removed.file) {
                        livewireCall = callLivewire(
                            "removePendingDocUpload",
                            widget,
                            cfg.wireModel,
                            removed.file.name,
                            0,
                        );
                    }

                    const applyLocalRemoval = () => {
                        clearUploadSimForFile(removed.file);
                        const removedFingerprint = fileFingerprint(
                            removed.file,
                        );
                        state.rawFiles = state.rawFiles.filter(
                            (f) => fileFingerprint(f) !== removedFingerprint,
                        );
                        state.uploadedFiles = state.uploadedFiles.filter(
                            (x) => x.id !== removedId,
                        );
                        const input = e.input;
                        if (input) {
                            syncInputFiles(uploadId);
                        }
                    };

                    if (!livewireCall) {
                        applyLocalRemoval();
                        return Promise.resolve();
                    }

                    const promise =
                        livewireCall && typeof livewireCall.then === "function"
                            ? livewireCall
                            : Promise.resolve(livewireCall);

                    return promise.then(applyLocalRemoval);
                });
                listEl.appendChild(item.element);
                state.uploadedFiles.push(item);
                simulateUpload(item);
            });
            updateUIForWidget(widget);
        }

        function renderSavedRowsForWidget(widget) {
            if (!widget) return;
            const e = elsForWidget(widget);
            const cfg = configFromWidget(widget);
            const uploadId = e.uploadId;
            const state = getInstance(uploadId);
            const listEl = e.list;
            if (!listEl || !cfg) return;

            const entries = readSavedEntries(cfg, widget);
            const input = e.input;

            if (state.rawFiles.length > 0) {
                rebuildListUIForWidget(widget);
                return;
            }

            listEl.replaceChildren();
            if (entries.length > 0) {
                appendSavedRows(cfg, listEl, widget, entries);
            }
            updateUIForWidget(widget);
        }

        function renderSavedRows(uploadId) {
            renderSavedRowsForWidget(els(uploadId).widget);
        }

        function renderListIfNeeded(uploadId) {
            renderSavedRows(uploadId);
        }

        function updateUIForWidget(widget) {
            const e = elsForWidget(widget);
            if (!e.list || !e.empty) return;
            const hasFiles = e.list.children.length > 0;
            e.empty.hidden = hasFiles;
            e.list.hidden = !hasFiles;
            if (e.widget) {
                e.widget.classList.toggle("vq-doc-upload--has-files", hasFiles);
            }
        }

        function queueLivewireUpload(uploadId, pickedFiles, input) {
            const run = () => {
                if (!pushFilesToLivewire(uploadId, pickedFiles) && input) {
                    input.dispatchEvent(new Event("change", { bubbles: true }));
                }
            };
            queueMicrotask(run);
        }

        function notifyLivewireFileInput(input) {
            if (!input || !input.id || !input.id.endsWith("-file-input")) {
                return;
            }
            const uploadId = input.id.replace(/-file-input$/, "");
            queueLivewireUpload(uploadId, Array.from(input.files || []), input);
        }

        function handleFileInputChange(input, e) {
            if (!input || !input.id || !input.id.endsWith("-file-input"))
                return;
            if (e && e.isTrusted === false) {
                return;
            }
            const uploadId = input.id.replace(/-file-input$/, "");
            const state = getInstance(uploadId);
            if (state.ignoreNextChange) {
                state.ignoreNextChange = false;
                return;
            }
            const picked = Array.from(input.files || []);
            if (!picked.length) return;

            const { valid, errors } = partitionPickedFiles(
                picked,
                els(uploadId).widget,
                input,
            );
            if (errors.length) {
                showUploadErrors(uploadId, errors);
            } else {
                showUploadErrors(uploadId, []);
            }
            if (!valid.length) {
                input.value = "";
                return;
            }
            if (valid.length < picked.length) {
                const dt = new DataTransfer();
                valid.forEach((file) => dt.items.add(file));
                input.files = dt.files;
            }

            mergePicks(uploadId, Array.from(input.files || []));
            syncInputFiles(uploadId);
            rebuildListUI(uploadId);
            queueLivewireUpload(uploadId, valid, input);
            if (e?.isTrusted) {
                e.stopImmediatePropagation();
            }
        }

        function handleInputChange(input) {
            handleFileInputChange(input, null);
        }

        function openFileDialog(uploadId) {
            const state = getInstance(uploadId);
            const input = els(uploadId).input;
            const widget = els(uploadId).widget;
            if (!input || state.dialogOpening) return;
            state.dialogOpening = true;
            input.value = "";
            if (isSingleMode(widget)) {
                input.removeAttribute("multiple");
            } else {
                input.setAttribute("multiple", "");
            }
            input.click();
            setTimeout(() => {
                state.dialogOpening = false;
            }, 500);
        }

        function bindDragDrop(uploadId) {
            const area = els(uploadId).area;
            if (!area || area.dataset.vqBound) return;
            area.dataset.vqBound = "1";
            area.addEventListener("dragover", (e) => {
                e.preventDefault();
                area.classList.add("dragover");
            });
            area.addEventListener("dragleave", (e) => {
                e.preventDefault();
                area.classList.remove("dragover");
            });
            area.addEventListener("drop", (e) => {
                e.preventDefault();
                area.classList.remove("dragover");
                const input = els(uploadId).input;
                if (!input) return;
                const state = getInstance(uploadId);
                const beforeLen = state.rawFiles.length;
                const picked = Array.from(e.dataTransfer.files);
                const { synced } = processNewFiles(uploadId, picked);
                if (!synced && state.rawFiles.length > beforeLen) {
                    notifyLivewireFileInput(input);
                }
            });
        }

        function storagePathAndCfgOk(btn, cfg) {
            const storagePath = btn.getAttribute("data-storage-path");
            return storagePath && cfg && cfg.removeStoredFn;
        }

        function bindDelegatedHandlers() {
            if (delegated) return;
            delegated = true;

            document.addEventListener(
                "change",
                (e) => {
                    const input = e.target;
                    if (
                        !input ||
                        !input.id ||
                        !input.id.endsWith("-file-input")
                    ) {
                        return;
                    }
                    handleFileInputChange(input, e);
                },
                true,
            );

            document.addEventListener(
                "click",
                (e) => {
                    const removeBtn = e.target.closest(
                        "[data-vq-doc-upload] .file-remove[data-storage-path]",
                    );
                    if (removeBtn) {
                        const widget = removeBtn.closest(
                            "[data-vq-doc-upload]",
                        );
                        const cfg = configFromWidget(widget);
                        if (storagePathAndCfgOk(removeBtn, cfg)) {
                            e.preventDefault();
                            e.stopPropagation();
                            handleStoredFileRemove(removeBtn, widget, cfg);
                        }
                        return;
                    }

                    const widget =
                        e.target.closest &&
                        e.target.closest("[data-vq-doc-upload]");
                    if (!widget) return;
                    const uploadId = widget.dataset.uploadId;
                    if (!uploadId) return;
                    if (
                        e.target.closest(
                            'label[for="' + uploadId + '-file-input"]',
                        )
                    ) {
                        return;
                    }
                    const trigger = e.target.closest(
                        "#" + uploadId + "-upload-trigger",
                    );
                    const area = e.target.closest(
                        "#" + uploadId + "-upload-area",
                    );
                    if (!trigger && !area) return;
                    if (
                        area &&
                        (e.target.closest(".file-item") ||
                            e.target.closest(".file-remove"))
                    )
                        return;
                    if (e.target.closest("#" + uploadId + "-file-input"))
                        return;
                    e.preventDefault();
                    openFileDialog(uploadId);
                },
                true,
            );
        }

        function normalizeSavedFingerprint(cfg, widget) {
            const parts = [];
            if (cfg && cfg.savedJsonId) {
                const hid = document.getElementById(cfg.savedJsonId);
                const jsonStr = hid ? decodeHtmlEntities(hid.value || "") : "";
                if (jsonStr !== "") {
                    try {
                        const a = JSON.parse(jsonStr);
                        if (!Array.isArray(a)) {
                            parts.push(jsonStr);
                        } else {
                            dedupeSavedEntries(parseSavedEntriesRaw(a))
                                .map(
                                    (entry) =>
                                        "p:" +
                                        normalizedStoragePath(
                                            entry.path,
                                            entry.url,
                                        ),
                                )
                                .filter(Boolean)
                                .sort()
                                .forEach((p) => parts.push(p));
                        }
                    } catch (e) {
                        parts.push(jsonStr);
                    }
                }
            }
            if (
                widget &&
                widget.dataset.savedEntries &&
                !(cfg && cfg.savedJsonId)
            ) {
                parts.push("d:" + widget.getAttribute("data-saved-entries"));
            }
            parts.sort();
            return parts.join("\n");
        }

        function scheduleAfterMorph() {
            clearTimeout(window.__vqDocUploadMorphTimer);
            window.__vqDocUploadMorphTimer = setTimeout(afterMorph, 50);
        }

        function init() {
            bindDelegatedHandlers();
            document
                .querySelectorAll("[data-vq-doc-upload]")
                .forEach((widget) => {
                    const uploadId = widget.dataset.uploadId;
                    if (!uploadId) return;
                    bindDragDrop(uploadId);
                    const cfg = configFromWidget(widget);
                    if (cfg) {
                        getInstance(uploadId).savedSnap =
                            normalizeSavedFingerprint(cfg, widget);
                    }
                    renderSavedRowsForWidget(widget);
                });
        }

        function afterMorph() {
            if (window.__vqDocUploadMorphRunning) {
                return;
            }
            window.__vqDocUploadMorphRunning = true;
            try {
                document
                    .querySelectorAll("[data-vq-doc-upload]")
                    .forEach((widget) => {
                        const uploadId = widget.dataset.uploadId;
                        if (!uploadId) return;
                        bindDragDrop(uploadId);
                        const cfg = configFromWidget(widget);
                        const state = getInstance(uploadId);
                        const listEl = elsForWidget(widget).list;
                        const entries = cfg
                            ? readSavedEntries(cfg, widget)
                            : [];
                        if (
                            listEl &&
                            cfg &&
                            state.rawFiles.length === 0 &&
                            entries.length > 0 &&
                            listEl.querySelectorAll("[data-saved-path]")
                                .length !== entries.length
                        ) {
                            renderSavedRowsForWidget(widget);
                            state.savedSnap = normalizeSavedFingerprint(
                                cfg,
                                widget,
                            );
                            return;
                        }
                        if (cfg) {
                            const fp = normalizeSavedFingerprint(cfg, widget);
                            if (fp !== state.savedSnap) {
                                state.savedSnap = fp;
                                syncWidgetSavedState(cfg, widget);
                                renderSavedRowsForWidget(widget);
                                return;
                            }
                        }
                        if (
                            listEl &&
                            state.rawFiles.length > 0 &&
                            listEl.children.length === 0
                        ) {
                            rebuildListUIForWidget(widget);
                            return;
                        }
                        const savedRowCount = listEl
                            ? listEl.querySelectorAll("[data-saved-path]")
                                  .length
                            : 0;
                        if (
                            listEl &&
                            state.rawFiles.length === 0 &&
                            entries.length > 0 &&
                            savedRowCount !== entries.length
                        ) {
                            renderSavedRowsForWidget(widget);
                            return;
                        }
                        if (
                            listEl &&
                            listEl.children.length === 0 &&
                            entries.length > 0 &&
                            state.rawFiles.length === 0
                        ) {
                            renderSavedRowsForWidget(widget);
                            return;
                        }
                        updateUIForWidget(widget);
                    });
            } finally {
                window.__vqDocUploadMorphRunning = false;
            }
        }

        function observeWidgets() {
            if (window.__vqDocUploadObserver) return;
            window.__vqDocUploadObserver = new MutationObserver((mutations) => {
                let found = false;
                mutations.forEach((mutation) => {
                    mutation.addedNodes.forEach((node) => {
                        if (node.nodeType !== 1) return;
                        if (
                            node.matches &&
                            node.matches("[data-vq-doc-upload]")
                        ) {
                            found = true;
                            return;
                        }
                        if (
                            node.querySelector &&
                            node.querySelector("[data-vq-doc-upload]") &&
                            !node.closest?.("[data-vq-doc-upload]")
                        ) {
                            found = true;
                        }
                    });
                });
                if (found) scheduleAfterMorph();
            });
            window.__vqDocUploadObserver.observe(document.body, {
                childList: true,
                subtree: true,
            });
        }

        function findServerErrorNearField(field) {
            if (!field) return null;
            let sibling = field.nextElementSibling;
            while (sibling) {
                if (sibling.matches?.(".error-text")) {
                    return sibling;
                }
                if (
                    sibling.matches?.(".vq-doc-upload-field") ||
                    sibling.matches?.("[data-vq-doc-upload]")
                ) {
                    break;
                }
                sibling = sibling.nextElementSibling;
            }
            return field.parentElement?.querySelector(".error-text");
        }

        function hookMorph() {
            if (morphHooked) return;
            morphHooked = true;
            observeWidgets();
            const registerMorphHook = () => {
                if (!window.Livewire) return;
                if (typeof Livewire.hook === "function") {
                    Livewire.hook("morph.updated", scheduleAfterMorph);
                    Livewire.hook("commit", ({ succeed }) => {
                        if (typeof succeed === "function") {
                            succeed(scheduleAfterMorph);
                        }
                    });
                }
            };
            const registerUploadErrorListener = () => {
                window.addEventListener("livewire-upload-error", (e) => {
                    const property = (e.detail?.property || "").replace(
                        /\.\d+$/,
                        "",
                    );
                    const widget = widgetForWireModel(property);
                    if (!widget) return;
                    const uploadId = widget.dataset.uploadId;
                    if (!uploadId) return;
                    window.setTimeout(() => {
                        const field = widget.closest(".vq-doc-upload-field");
                        const serverError = findServerErrorNearField(field);
                        if (serverError?.textContent?.trim()) {
                            showUploadErrors(uploadId, [
                                serverError.textContent.trim(),
                            ]);
                            return;
                        }
                        showUploadErrors(uploadId, [
                            `The file must not be greater than ${formatMaxSizeLabel(maxBytesFromWidget(widget))}.`,
                        ]);
                    }, 50);
                });
            };
            if (window.Livewire) {
                registerMorphHook();
            } else {
                document.addEventListener("livewire:init", registerMorphHook);
            }
            registerUploadErrorListener();
        }

        function isDropTarget(el) {
            return el && el.closest && el.closest("[data-vq-doc-upload]");
        }

        hookMorph();
        if (document.readyState === "loading") {
            document.addEventListener("DOMContentLoaded", init);
        } else {
            init();
        }
        document.addEventListener("livewire:navigated", init);

        document.addEventListener("dragover", (e) => {
            if (isDropTarget(e.target)) return;
            e.preventDefault();
        });
        document.addEventListener("drop", (e) => {
            if (isDropTarget(e.target)) return;
            e.preventDefault();
        });

        return {
            init,
            afterMorph,
            rebuildListUI,
            renderListIfNeeded,
            updateUI,
            callMethod: callLivewire,
            pushFiles: pushFilesToLivewire,
        };
    })();
