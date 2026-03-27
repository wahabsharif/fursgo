/**
 * FursGo Custom Dropdown
 * Drop-in replacement for <select> elements.
 * Usage: new FursDropdown(selectElement, options?)
 *
 * Auto-init: add data-furs-dropdown to any <select> to auto-initialize on DOMContentLoaded.
 */

(function () {
  "use strict";

  /* ─── Inject shared styles once ─────────────────────────────────── */
  if (!document.getElementById("furs-dropdown-styles")) {
    const style = document.createElement("style");
    style.id = "furs-dropdown-styles";
    style.textContent = `
      /* ── Wrapper ── */
      .furs-dd {
        width: 100%;
        position: relative;
        display: block;
        font-family: Lato, sans-serif;
        user-select: none;
      }

      /* ── Trigger button ── */
      .furs-dd__trigger {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 10px;
        width: 100%;
        height: 48px;
        padding: 0 14px;
        border-radius: 10px;
        border: 1px solid #D4D4D4;
        background: #FFF;
        cursor: pointer;
        box-sizing: border-box;
        transition: border-color .18s, box-shadow .18s;
        outline: none;
      }
      .furs-dd__trigger:focus-visible {
        border-color: #FFC97A;
        box-shadow: 0 0 0 3px rgba(255, 201, 122, .18);
      }
      .furs-dd.is-open .furs-dd__trigger {
        border-color: #FFC97A;
        border-radius: 10px 10px 0 0;
        box-shadow: 0 2px 10px rgba(0,0,0,.06);
      }

      /* Trigger text */
      .furs-dd__label {
        flex: 1;
        overflow: hidden;
        white-space: nowrap;
        text-overflow: ellipsis;
        color: #3B3731;
        font-size: 16px;
        font-weight: 400;
        line-height: 1;
        text-align: left;
      }
      .furs-dd__label.is-placeholder {
        color: #D4D4D4;
      }

      /* Chevron arrow */
      .furs-dd__arrow {
        flex-shrink: 0;
        width: 15px;
        height: 8px;
        transition: transform .25s cubic-bezier(.4,0,.2,1);
        pointer-events: none;
      }
      .furs-dd.is-open .furs-dd__arrow {
        transform: rotate(180deg);
      }

      /* ── Panel ── */
      .furs-dd__panel {
        position: absolute;
        top: calc(100% - 1px);
        left: 0;
        right: 0;
        z-index: 9999;
        background: #FFF;
        border: 1px solid #FFC97A;
        border-top: none;
        border-radius: 0 0 10px 10px;
        box-shadow: 0 8px 24px rgba(0,0,0,.1);
        overflow: hidden;

        /* animation */
        max-height: 0;
        opacity: 0;
        pointer-events: none;
        transition: max-height .25s cubic-bezier(.4,0,.2,1),
                    opacity   .2s ease;
      }
      .furs-dd.is-open .furs-dd__panel {
        max-height: 500px;
        opacity: 1;
        pointer-events: auto;
      }

      /* Inner scroll container */
      .furs-dd__list {
        max-height: 280px;
        overflow-y: auto;
        padding: 6px 0;
        scrollbar-width: thin;
        scrollbar-color: #FFC97A #f8f8f8;
      }
      .furs-dd__list::-webkit-scrollbar { width: 5px; }
      .furs-dd__list::-webkit-scrollbar-track { background: #f8f8f8; }
      .furs-dd__list::-webkit-scrollbar-thumb { background: #FFC97A; border-radius: 3px; }

      /* ── Option ── */
      .furs-dd__option {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 11px 16px;
        cursor: pointer;
        transition: background .12s;
        position: relative;
      }
      .furs-dd__option:hover {
        background: #FFF9F0;
      }
      .furs-dd__option.is-selected {
        background: #FFF9F0;
      }
      .furs-dd__option.is-disabled {
        opacity: .45;
        cursor: not-allowed;
        pointer-events: none;
      }

      /* Radio dot */
      .furs-dd__dot {
        flex-shrink: 0;
        width: 20px;
        height: 20px;
        border-radius: 50%;
        border: 2px solid #D4D4D4;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: border-color .15s;
      }
      .furs-dd__dot::after {
        content: '';
        width: 10px;
        height: 10px;
        border-radius: 50%;
        background: #FFD88C;
        transform: scale(0);
        transition: transform .18s cubic-bezier(.34,1.56,.64,1);
      }
      .furs-dd__option.is-selected .furs-dd__dot {
        border-color: #FFD88C;
      }
      .furs-dd__option.is-selected .furs-dd__dot::after {
        transform: scale(1);
      }

      /* Option text */
      .furs-dd__text {
        flex: 1;
        color: #3B3731;
        font-family: Lato, sans-serif;
        font-size: 15px;
        font-weight: 400;
        line-height: 1.4;
      }
      .furs-dd__option.is-placeholder-option .furs-dd__text {
        color: #D4D4D4;
      }

      /* Optional badge / price slot */
      .furs-dd__badge {
        font-size: 13px;
        font-weight: 500;
        color: #9D9B98;
        white-space: nowrap;
      }

      /* Divider between groups */
      .furs-dd__divider {
        height: 1px;
        background: #F0EEEB;
        margin: 4px 0;
      }

      /* ── Search box ── */
      .furs-dd__search-wrap {
        padding: 8px 12px 4px;
        border-bottom: 1px solid #F0EEEB;
      }
      .furs-dd__search {
        width: 100%;
        box-sizing: border-box;
        height: 36px;
        padding: 0 12px;
        border-radius: 8px;
        border: 1px solid #D4D4D4;
        font-family: Lato, sans-serif;
        font-size: 14px;
        color: #3B3731;
        outline: none;
        transition: border-color .15s;
      }
      .furs-dd__search::placeholder { color: #D4D4D4; }
      .furs-dd__search:focus { border-color: #FFC97A; }

      /* Empty state */
      .furs-dd__empty {
        padding: 16px;
        text-align: center;
        color: #9D9B98;
        font-size: 14px;
        font-family: Lato, sans-serif;
      }

      /* ── Hidden native select (accessibility) ── */
      .furs-dd__native {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        opacity: 0 !important;
        pointer-events: none !important;
        overflow: hidden !important;
      }
    `;
    document.head.appendChild(style);
  }

  /* ─── Helper ─────────────────────────────────────────────────────── */
  const SVG_ARROW = `<svg class="furs-dd__arrow" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 15 8" fill="none">
    <path d="M13.5105 0.5L6.95017 7.06033L0.499971 0.610127" stroke="#3B3731" stroke-linecap="round" stroke-linejoin="round"/>
  </svg>`;

  /* ─── Class ──────────────────────────────────────────────────────── */
  class FursDropdown {
    /**
     * @param {HTMLSelectElement} selectEl  - The original <select> to replace.
     * @param {object} [opts]
     * @param {boolean} [opts.searchable=false]  - Show a search/filter input.
     * @param {string}  [opts.placeholder]       - Overrides the first disabled option text.
     * @param {boolean} [opts.showDot=true]      - Show the radio dot indicator.
     */
    constructor(selectEl, opts = {}) {
      if (!(selectEl instanceof HTMLSelectElement))
        throw new TypeError("FursDropdown: first arg must be a <select>");
      if (selectEl._fursDD) return; // already initialized

      this.select = selectEl;
      this.opts = Object.assign({ searchable: false, showDot: true }, opts);
      // boolean state renamed to avoid shadowing method names
      this._isOpen = false;
      this._filter = "";

      selectEl._fursDD = this;
      this._build();
      // if build failed (no parent node etc.) stop
      if (!this.wrap) return;
      this._syncFromNative();
      this._attachEvents();
    }

    /* ── DOM construction ─────────────────────────────────────────── */
    _build() {
      const sel = this.select;

      // wrapper
      const wrap = document.createElement("div");
      wrap.className = "furs-dd";
      // inherit any extra classes from a parent .input-wrap if needed
      this.wrap = wrap;

      // trigger
      const trigger = document.createElement("button");
      trigger.type = "button";
      trigger.className = "furs-dd__trigger";
      trigger.setAttribute("aria-haspopup", "listbox");
      trigger.setAttribute("aria-expanded", "false");
      trigger.innerHTML = `<span class="furs-dd__label is-placeholder"></span>${SVG_ARROW}`;
      this.trigger = trigger;
      this.label = trigger.querySelector(".furs-dd__label");

      // panel
      const panel = document.createElement("div");
      panel.className = "furs-dd__panel";
      panel.setAttribute("role", "listbox");
      this.panel = panel;

      // optional search
      if (this.opts.searchable) {
        const sw = document.createElement("div");
        sw.className = "furs-dd__search-wrap";
        const si = document.createElement("input");
        si.type = "text";
        si.className = "furs-dd__search";
        si.placeholder = "Search…";
        si.setAttribute("autocomplete", "off");
        si.addEventListener("input", () => {
          this._filter = si.value.toLowerCase();
          this._renderList();
        });
        sw.appendChild(si);
        panel.appendChild(sw);
        this.searchInput = si;
      }

      // list container
      const list = document.createElement("div");
      list.className = "furs-dd__list";
      panel.appendChild(list);
      this.list = list;

      // assemble
      wrap.appendChild(trigger);
      wrap.appendChild(panel);

      // hide native select, insert wrapper
      // guard: ensure select has a parentNode
      if (!sel.parentNode) {
        console.warn(
          "FursDropdown: cannot initialize - select has no parentNode",
          sel,
        );
        this.wrap = null;
        return;
      }
      sel.classList.add("furs-dd__native");
      sel.parentNode.insertBefore(wrap, sel);
      wrap.appendChild(sel); // keep select inside wrapper for layout

      this._renderList();
    }

    _renderList() {
      const list = this.list;
      const sel = this.select;
      const query = this._filter;

      list.innerHTML = "";

      // collect all <option> and <optgroup> children
      let hasVisible = false;
      const children = Array.from(sel.children);

      children.forEach((child) => {
        if (child.tagName === "OPTGROUP") {
          const groupOpts = Array.from(child.querySelectorAll("option")).filter(
            (o) => !query || o.textContent.toLowerCase().includes(query),
          );
          if (!groupOpts.length) return;

          const div = document.createElement("div");
          div.className = "furs-dd__divider";
          list.appendChild(div);

          groupOpts.forEach((o) => {
            const node = this._makeOption(o);
            if (node) {
              list.appendChild(node);
              hasVisible = true;
            }
          });
        } else if (child.tagName === "OPTION") {
          if (query && !child.textContent.toLowerCase().includes(query)) return;
          const node = this._makeOption(child);
          if (node) {
            list.appendChild(node);
            hasVisible = true;
          }
        }
      });

      if (!hasVisible) {
        const empty = document.createElement("div");
        empty.className = "furs-dd__empty";
        empty.textContent = "No options found";
        list.appendChild(empty);
      }
    }

    _makeOption(optEl) {
      if (!optEl) return null;
      const isPlaceholder = optEl.disabled && optEl.index === 0;
      const item = document.createElement("div");
      item.className =
        "furs-dd__option" +
        (optEl.selected ? " is-selected" : "") +
        (optEl.disabled ? " is-disabled" : "") +
        (isPlaceholder ? " is-placeholder-option" : "");
      item.setAttribute("role", "option");
      item.setAttribute("aria-selected", optEl.selected ? "true" : "false");
      item.dataset.value = optEl.value;

      // make option focusable for keyboard focus()
      item.tabIndex = -1;

      // dot
      if (this.opts.showDot) {
        const dot = document.createElement("span");
        dot.className = "furs-dd__dot";
        item.appendChild(dot);
      }

      // text
      const text = document.createElement("span");
      text.className = "furs-dd__text";
      text.textContent = optEl.textContent.trim();
      item.appendChild(text);

      // badge (data-badge attr on option)
      if (optEl.dataset && optEl.dataset.badge) {
        const badge = document.createElement("span");
        badge.className = "furs-dd__badge";
        badge.textContent = optEl.dataset.badge;
        item.appendChild(badge);
      }

      item.addEventListener("click", () => {
        if (optEl.disabled) return;
        this._selectValue(optEl.value);
        this._close();
      });

      return item;
    }

    /* ── State sync ───────────────────────────────────────────────── */
    _syncFromNative() {
      const sel = this.select;
      const chosen = sel.options[sel.selectedIndex];
      const isPlaceholder =
        chosen && chosen.disabled && sel.selectedIndex === 0;
      const ph =
        this.opts.placeholder ||
        (sel.options[0] && sel.options[0].disabled
          ? sel.options[0].textContent.trim()
          : "Select…");

      if (!chosen || isPlaceholder) {
        if (this.label) {
          this.label.textContent = ph;
          this.label.classList.add("is-placeholder");
        }
      } else {
        if (this.label) {
          this.label.textContent = chosen.textContent.trim();
          this.label.classList.remove("is-placeholder");
        }
      }
    }

    _selectValue(val) {
      this.select.value = val;
      this._syncFromNative();
      this._renderList(); // refresh dot states
      // fire native change event so existing code continues working
      this.select.dispatchEvent(new Event("change", { bubbles: true }));
    }

    /* ── Open / Close ─────────────────────────────────────────────── */
    _open() {
      this._isOpen = true;
      if (this.wrap) this.wrap.classList.add("is-open");
      if (this.trigger) this.trigger.setAttribute("aria-expanded", "true");
      if (this.searchInput) {
        this.searchInput.value = "";
        this._filter = "";
        this._renderList();
        this.searchInput.focus();
      }
    }

    _close() {
      this._isOpen = false;
      if (this.wrap) this.wrap.classList.remove("is-open");
      if (this.trigger) this.trigger.setAttribute("aria-expanded", "false");
    }

    _toggle() {
      this._isOpen ? this._close() : this._open();
    }

    /* ── Events ───────────────────────────────────────────────────── */
    _attachEvents() {
      if (!this.trigger) return;

      this.trigger.addEventListener("click", (e) => {
        e.stopPropagation();
        this._toggle();
      });

      // close on outside click
      document.addEventListener("click", (e) => {
        if (!this.wrap) return;
        if (!this.wrap.contains(e.target)) this._close();
      });

      // keyboard
      this.trigger.addEventListener("keydown", (e) => {
        if (e.key === "Enter" || e.key === " ") {
          e.preventDefault();
          this._toggle();
        }
        if (e.key === "Escape") this._close();
        if (e.key === "ArrowDown") {
          e.preventDefault();
          this._open();
          this._focusFirst();
        }
      });

      this.list.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
          this._close();
          this.trigger.focus();
        }
      });

      // if native select changes externally, sync
      this.select.addEventListener("change", () => this._syncFromNative());
    }

    _focusFirst() {
      const first = this.list.querySelector(
        ".furs-dd__option:not(.is-disabled)",
      );
      if (first) first.focus();
    }

    /* ── Public API ───────────────────────────────────────────────── */
    /** Programmatically set the value */
    setValue(val) {
      this._selectValue(val);
    }

    /** Refresh options from the native select (call after adding <option> elements) */
    refresh() {
      this._renderList();
      this._syncFromNative();
    }

    /** Destroy: restore native select */
    destroy() {
      this.select.classList.remove("furs-dd__native");
      if (this.wrap && this.wrap.parentNode) {
        this.wrap.parentNode.insertBefore(this.select, this.wrap);
        this.wrap.remove();
      }
      delete this.select._fursDD;
    }
  }

  /* ─── Auto-init ──────────────────────────────────────────────────── */
  function autoInit() {
    document.querySelectorAll("select[data-furs-dropdown]").forEach((sel) => {
      const searchable = sel.hasAttribute("data-furs-searchable");
      new FursDropdown(sel, { searchable });
    });
  }

  if (document.readyState === "loading") {
    document.addEventListener("DOMContentLoaded", autoInit);
  } else {
    autoInit();
  }

  /* ─── Export ─────────────────────────────────────────────────────── */
  window.FursDropdown = FursDropdown;
})();
