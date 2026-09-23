@once
    <style>
        .pet-compatibility-fieldset {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .pet-compatibility-fieldset .service-chip-row {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 2rem;
            width: 100%;
            flex-wrap: wrap;
        }

        .pet-compatibility-fieldset .service-chip-group {
            display: flex;
            flex-direction: column;
            gap: 13px;
        }

        .pet-compatibility-fieldset .service-chip-group-chips {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
        }

        .pet-compatibility-fieldset .service-chip-group>span {
            display: inline-block;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .pet-compatibility-fieldset .service-chip {
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            border: none;
            border-radius: 22px;
            background: #F7F7F7;
            color: #D4D4D4;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 400;
            line-height: 25px;
            padding: 0 16px 0 15px;
            cursor: pointer;
            transition: background-color 220ms ease, color 220ms ease;
        }

        .pet-compatibility-fieldset .service-chip--size {
            font-size: 16px;
            font-weight: 600;
            line-height: normal;
            padding: 0 16px;
        }

        .pet-compatibility-fieldset .service-chip.is-active {
            background: rgba(216, 232, 183, 0.20);
            color: #A4C560;
        }

        .pet-compatibility-fieldset .service-chip:active {
            transform: scale(0.98);
        }

        .pet-compatibility-fieldset .service-chip-icon {
            color: #D4D4D4;
            flex-shrink: 0;
            transition: color 220ms ease;
        }

        .pet-compatibility-fieldset .service-chip.is-active .service-chip-icon {
            color: #A4C560;
        }

        .pet-compatibility-fieldset .service-chip-tick {
            flex-shrink: 0;
            width: 0;
            height: 9px;
            overflow: hidden;
            opacity: 0;
            transform: scale(0.6);
            transition: opacity 180ms ease, transform 180ms ease, width 180ms ease;
        }

        .pet-compatibility-fieldset .service-chip.is-active .service-chip-tick {
            width: 12px;
            opacity: 1;
            transform: scale(1);
        }

        .service-duration-fieldset .service-duration-advanced-wrap,
        .service-price-fieldset .service-price-advanced-wrap {
            display: flex;
            align-items: flex-end;
            padding-bottom: 0.4rem;
        }

        .service-duration-fieldset .service-duration-advanced-btn,
        .service-price-fieldset .service-price-advanced-btn {
            border: 0;
            background: transparent;
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            display: inline-flex;
            align-items: center;
            gap: 0.3rem;
            cursor: pointer;
            padding: 0;
        }

        .service-duration-fieldset .service-duration-advanced-btn span:last-child,
        .service-price-fieldset .service-price-advanced-btn span:last-child {
            text-decoration-line: underline;
            text-underline-offset: 4px;
        }

        .service-duration-fieldset .service-duration-by-size,
        .service-price-fieldset .service-price-by-size {
            margin-top: 2rem;
        }

        .service-duration-fieldset .service-duration-by-size {
            width: 28%;
            overflow: visible;
        }

        .service-duration-fieldset .service-duration-by-size-head,
        .service-price-fieldset .service-price-by-size-head {
            display: grid;
            margin-bottom: 0.8rem;
        }

        .service-duration-fieldset .service-duration-by-size-head {
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }

        .service-price-fieldset .service-price-by-size {
            width: 422px;
        }

        .service-price-fieldset .service-price-by-size-head {
            grid-template-columns: 1fr 205px;
            gap: 0.8rem;
            margin-bottom: 0.6rem;
        }

        .service-duration-fieldset .service-duration-by-size-head p,
        .service-price-fieldset .service-price-by-size-head p {
            margin: 0;
            color: #1F1F1F;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .service-duration-fieldset .service-duration-by-size-card,
        .service-price-fieldset .service-price-by-size-card {
            border-radius: 12px;
            background: #FAFAFA;
            display: flex;
            flex-direction: column;
        }

        .service-duration-fieldset .service-duration-by-size-card {
            padding: 1.2rem 1.25rem;
            gap: 1.2rem;
            overflow: visible;
        }

        .service-price-fieldset .service-price-by-size-card {
            width: 100%;
            padding: 1rem 0.9rem;
            gap: 0.9rem;
        }

        .service-duration-fieldset .service-duration-by-size-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 1rem;
        }

        .service-price-fieldset .service-price-by-size-row {
            display: grid;
            grid-template-columns: 1fr 190px;
            align-items: center;
            gap: 0.8rem;
        }

        .service-duration-fieldset .service-duration-by-size-row p,
        .service-price-fieldset .service-price-by-size-row p {
            margin: 0;
            color: #000;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .service-duration-fieldset .service-duration-by-size-value {
            position: relative;
            width: 165px;
            height: 48px;
            flex-shrink: 0;
        }

        .service-price-fieldset .service-price-by-size-value {
            position: relative;
            width: 85px;
            height: 48px;
            justify-self: end;
        }

        .service-duration-fieldset .service-duration-by-size-value>div,
        .service-duration-fieldset .service-duration-by-size-value>.service-duration-none,
        .service-price-fieldset .service-price-by-size-value>div,
        .service-price-fieldset .service-price-by-size-value>.service-duration-none {
            position: absolute;
            inset: 0;
        }

        .service-duration-fieldset .service-duration-none,
        .service-price-fieldset .service-duration-none {
            height: 48px;
            display: flex;
            align-items: center;
            color: #3B3731;
            font-family: Lato;
            font-size: 24px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .service-duration-fieldset .service-duration-none {
            width: 165px;
            padding-left: 2rem;
        }

        .service-price-fieldset .service-price-by-size-value .service-duration-none {
            width: 85px;
            padding-left: 1.25rem;
        }

        .service-price-fieldset .service-price-top-row,
        .service-price-fieldset .service-price-layout {
            display: flex;
            align-items: flex-start;
            gap: 1.5rem;
            margin: 1rem 0;
        }

        .service-price-fieldset .service-overtime-wrap {
            width: 280px;
            padding-top: 0.2rem;
        }

        .service-duration-fieldset [x-cloak],
        .service-price-fieldset [x-cloak] {
            display: none !important;
        }

        .service-duration-fieldset .service-custom-select,
        .service-price-fieldset .service-custom-select {
            position: relative;
            width: 190px;
        }

        .service-duration-fieldset .service-custom-select-duration,
        .service-duration-fieldset .service-custom-select-duration .service-custom-trigger {
            width: 165px;
        }

        .service-price-fieldset .service-custom-select-overtime,
        .service-price-fieldset .service-custom-select-overtime .service-custom-trigger {
            width: 145px;
        }

        .service-duration-fieldset .service-custom-trigger,
        .service-price-fieldset .service-custom-trigger {
            width: 190px;
            height: 48px;
            border-radius: 10px;
            border: 1px solid #DDD;
            background: #fff;
            color: #3B3731;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-weight: 400;
            line-height: 25px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 1rem;
        }

        .service-duration-fieldset .service-custom-select.is-open .service-custom-trigger,
        .service-price-fieldset .service-custom-select.is-open .service-custom-trigger {
            border-bottom-left-radius: 0;
            border-bottom-right-radius: 0;
        }

        .service-duration-fieldset .service-custom-menu,
        .service-price-fieldset .service-custom-menu {
            position: absolute;
            top: 100%;
            left: 0;
            width: 100%;
            background: #F8F8F8;
            border: 1px solid #DDD;
            border-top: none;
            border-radius: 0 0 10px 10px;
            z-index: 40;
            overflow: hidden;
        }

        .service-duration-fieldset .service-custom-option,
        .service-price-fieldset .service-custom-option {
            width: 100%;
            border: 0;
            border-bottom: 2px solid #e6e6e5;
            background: #FFF;
            padding: 0.9rem 1rem;
            text-align: left;
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            font-weight: 400;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .service-duration-fieldset .service-custom-option:last-child,
        .service-price-fieldset .service-custom-option:last-child {
            border-bottom: none;
        }

        .service-duration-fieldset .service-custom-option.is-active,
        .service-price-fieldset .service-custom-option.is-active {
            background: rgba(216, 232, 183, 0.20);
            color: #A4C560;
        }

        .service-price-fieldset .service-number-input-wrap {
            position: relative;
            width: 100%;
        }

        .service-price-fieldset .service-number-input-wrap input[type="number"] {
            width: 100%;
            height: 48px;
            border-radius: 10px;
            border: 1px solid #d9d9d9;
            background: #fff;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-weight: 400;
            padding-right: 1.5rem;
            -moz-appearance: textfield;
        }

        .service-price-fieldset .service-number-input-wrap-currency::before {
            content: "£";
            position: absolute;
            left: 0.95rem;
            top: 50%;
            transform: translateY(-50%);
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            pointer-events: none;
        }

        .service-price-fieldset .service-number-input-wrap-currency input[type="number"] {
            padding-left: 1.45rem;
        }

        .service-price-fieldset .service-number-input-controls {
            position: absolute;
            top: 50%;
            right: 0.7rem;
            transform: translateY(-50%);
            display: flex;
            flex-direction: column;
            gap: 0.7rem;
        }

        .service-price-fieldset .service-number-step-btn {
            border: 0;
            background: transparent;
            cursor: pointer;
            width: 12px;
            height: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0;
        }

        .service-list-wrapper {
            margin-top: 0;
        }

        .section-container:has(.section-panel.section-active .services-dashboard) {
            overflow: visible;
        }

        .service-list-table-shell {
            width: 100%;
            overflow-x: auto;
            background: #FDFDFD;
            border: 1px solid #F6F5F5;
            border-radius: 10px;
            box-shadow: 0px 0px 15px 2px rgba(59, 55, 49, 0.1);
        }

        .service-list-table {
            width: 100%;
            max-width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
            min-width: 820px;
        }

        .service-list-table thead {
            background: #F6F5F5;
        }

        .service-list-table thead th {
            background: #F6F5F5;
            color: #948F88;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            text-align: left;
            height: 50px;
            padding: 0 8px;
            border: 0;
            vertical-align: middle;
            white-space: nowrap;
        }

        .service-list-table thead th:first-child,
        .service-list-table tbody td:first-child {
            padding-left: 20px;
        }

        .service-list-table thead th:last-child,
        .service-list-table tbody td:last-child {
            padding-right: 20px;
        }

        .service-list-table thead th:first-child {
            border-top-left-radius: 10px;
        }

        .service-list-table thead th:last-child {
            border-top-right-radius: 10px;
        }

        .service-list-table tbody td {
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
            text-align: left;
            height: 56px;
            padding: 10px 8px;
            border: 0;
            border-bottom: 1px solid #F6F5F5;
            background: #FDFDFD;
            vertical-align: middle;
        }

        .service-list-table tbody tr:last-child td {
            border-bottom: 0;
        }

        .service-list-table tbody td.service-duration-col {
            font-weight: 600;
        }

        .service-list-table .service-name-col {
            width: 20%;
        }

        .service-list-table .service-applies-col {
            width: 20%;
        }

        .service-list-table .service-duration-col {
            width: 19%;
        }

        .service-list-table .service-price-col {
            width: 16%;
        }

        .service-list-table .service-active-col {
            width: 13%;
            white-space: nowrap;
        }

        .service-list-table.is-addons .service-name-col {
            width: 33%;
        }

        .service-list-table.is-addons .service-applies-col {
            width: 26%;
        }

        .service-list-table.is-addons .service-price-col {
            width: 16%;
        }

        .service-list-table.is-addons .service-active-col {
            width: 13%;
        }

        .service-list-table tr.is-muted td:not(.service-action-col) {
            opacity: 0.5;
        }

        .service-list-table tr.is-newly-added td {
            animation: service-row-highlight-blink 2s ease-in-out;
        }

        @keyframes service-row-highlight-blink {

            0%,
            100% {
                background-color: #FDFDFD;
            }

            25%,
            75% {
                background-color: rgba(216, 232, 183, 0.55);
            }
        }

        .service-list-empty {
            text-align: center;
            color: #9D9B98;
            padding: 2rem 1.25rem !important;
        }

        .service-action-col {
            width: 122px;
            white-space: nowrap;
        }

        .service-action-btns {
            display: inline-flex;
            align-items: center;
            gap: 10px;
        }

        .service-action-btn {
            width: 36px;
            height: 36px;
            padding: 0;
            border: 0;
            background: transparent;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .service-action-btn svg {
            display: block;
            width: 36px;
            height: 36px;
        }

        .service-more {
            position: relative;
            display: inline-flex;
            width: 36px;
            height: 36px;
        }

        .service-more-menu {
            width: 130px;
            box-sizing: border-box;
            background: #FFF;
            border: 1px solid #D9D9D9;
            border-radius: 5px;
            overflow: hidden;
            z-index: 99999;
        }

        .service-more-menu button {
            width: 100%;
            height: 36px;
            border: 0;
            border-bottom: 1px solid #D9D9D9;
            background: #FFF;
            padding: 4px;
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .service-more-menu button:last-child {
            border-bottom: 0;
        }

        .service-more-menu button span {
            display: flex;
            align-items: center;
            width: 122px;
            height: 28px;
            padding: 0 6px;
            border-radius: 5px;
            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            font-weight: 500;
            line-height: normal;
            white-space: nowrap;
        }

        .service-more-menu button:hover {
            background: transparent;
        }

        .service-more-menu button:hover span {
            background: #FAF8F4;
        }

        .service-more-menu button.is-danger span {
            color: #FF6E6E;
        }

        .service-toggle {
            width: 44px;
            height: 24px;
            border-radius: 999px;
            border: none;
            background: #D9D9D9;
            position: relative;
            display: inline-block;
            cursor: pointer;
            vertical-align: middle;
            transition: background-color 0.24s ease;
        }

        .service-toggle::after {
            content: "";
            position: absolute;
            top: 2px;
            left: 2px;
            width: 20px;
            height: 20px;
            border-radius: 999px;
            background: #fff;
            z-index: 1;
            transition: left 0.24s ease, background 0.24s ease;
        }

        .service-toggle.is-on {
            background: #D8E8B7;
        }

        .service-toggle.is-on::after {
            left: 22px;
            background: transparent url('/images/business-hub/icon-toggle-tick.svg') center / 20px 20px no-repeat;
        }

        .service-toggle.is-on::before {
            content: none;
        }

        .service-list-table .service-toggle {
            width: 44px;
            height: 24px;
            background: #D9D9D9;
        }

        .service-list-table .service-toggle::after {
            top: 2px;
            left: 2px;
            width: 20px;
            height: 20px;
            background: #fff;
        }

        .service-list-table .service-toggle.is-on {
            background: #D8E8B7;
        }

        .service-list-table .service-toggle.is-on::after {
            left: 22px;
            background: transparent url('/images/business-hub/icon-toggle-tick.svg') center / 20px 20px no-repeat;
        }

        .service-list-table .service-toggle.is-on::before {
            content: none;
        }

        .service-load-more-wrap {
            display: flex;
            justify-content: center;
            margin-top: 2.5rem;
        }

        .service-load-more-btn {
            width: 133px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 75px;
            border: 1px solid #3B3731;
            background: #FFF;
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease, transform 0.15s ease;
        }

        .service-load-more-btn:hover {
            background: #FFC97A;
            color: #FFF;
            border-color: #FFC97A;
            transform: translateY(-1px);
        }

        .service-form-wrapper {
            margin-top: 0.5rem;
        }

        .service-form {
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .service-section-title {
            margin: 0;
            padding: 0;
            border: 0;
            color: #3B3731;
            font-family: "Playfair Display";
            font-size: 28px;
            font-weight: 600;
            line-height: normal;
        }

        .service-form-card {
            background: #fdfdfd;
            border: 1px solid #f6f5f5;
            border-radius: 10px;
            box-shadow: 0 0 15px 2px rgba(59, 55, 49, 0.1);
            padding: 1.5rem 1.25rem;
            overflow: visible;
        }

        .service-form-card .pet-compatibility-fieldset .service-chip-group>span {
            margin-top: 0;
        }

        .service-form-card .service-duration-fieldset>h4,
        .service-form-card .service-price-fieldset>h4 {
            display: none;
        }

        .service-form-card .service-price-top-row {
            margin-top: 1.25rem;
        }

        .service-form-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.25rem;
            align-items: end;
        }

        .service-form-grid .service-field {
            width: 100%;
        }

        .service-settings-card h4 {
            margin: 0 0 1.15rem;
            padding: 0;
            border: 0;
            color: #3B3731;
            font-family: "Playfair Display";
            font-size: 28px;
            font-weight: 600;
            line-height: normal;
        }

        .service-setting-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            min-height: 84px;
            padding: 1.15rem 1.25rem;
            border-radius: 10px;
            border: 1px solid #ededed;
            background: #f5f5f5;
            margin-bottom: 0.65rem;
        }

        .service-setting-row:last-child {
            margin-bottom: 0;
        }

        .service-setting-row.is-active {
            background: #f1f5e9;
        }

        .service-setting-row strong {
            display: block;
            color: #3B3731;
            font-family: Lato;
            font-size: 18px;
            font-weight: 600;
        }

        .service-setting-row p {
            margin: 0.2rem 0 0;
            color: #9c9790;
            font-family: Lato;
            font-size: 16px;
            font-weight: 400;
        }

        .service-form-footer {
            position: sticky;
            bottom: 0;
            z-index: 8;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 1rem;
            min-height: 80px;
            margin: 0;
            padding: 19px 20px;
            background: rgba(186, 207, 142, 0.2);
            border-top: 1px solid #B5DB65;
            backdrop-filter: blur(10px);
            box-sizing: border-box;
        }

        .service-form-saved {
            margin: 0;
            color: #AFCD6F !important;
            font-family: Lato;
            font-size: 18px;
            font-weight: 600 !important;
            line-height: 22px;
        }

        .service-form-saved span {
            color: #9C9790;
            font-weight: 600;
        }

        .service-form-footer .service-form-actions {
            display: flex;
            justify-content: flex-end;
            gap: 20px;
            margin: 0;
        }

        .service-form-footer .service-form-btn {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 138px;
            height: 42px;
            border-radius: 100px;
            border: none;
            text-align: center;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            cursor: pointer;
            transition: opacity 0.15s ease;
        }

        .service-form-footer .service-form-btn:hover {
            opacity: 0.92;
        }

        .service-form-footer .service-form-btn-cancel {
            border: 1px solid #D9D9D9;
            background: #fff;
            color: #9D9B98;
            box-shadow: none;
        }

        .service-form-footer .service-form-btn-save {
            border: none;
            background: #BACF8E;
            color: #fff;
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.10);
        }

        .service-form-footer.is-dirty {
            background: #FFFCF6;
            border-top-color: #FFAE37;
        }

        .service-form-footer.is-dirty .service-form-saved {
            color: #FFAE37 !important;
        }

        .service-form-footer .service-form-btn:disabled {
            opacity: 0.5;
            cursor: default;
            pointer-events: none;
        }

        .services-header-pill-delete {
            min-width: 162px;
            height: 42px;
            gap: 8px;
            border-color: #ff6e6e;
            color: #ff6e6e;
            box-shadow: 0 0 5px 2px rgba(59, 55, 49, 0.1);
        }

        .services-header-pill-delete svg {
            flex-shrink: 0;
        }

        @media (max-width: 768px) {

            .service-form-grid,
            .service-form-footer {
                grid-template-columns: 1fr;
                display: flex;
                flex-direction: column;
                align-items: stretch;
            }

            .service-form-footer .service-form-actions {
                width: 100%;
            }

            .service-form-btn {
                flex: 1;
            }
        }
    </style>
    <script>
        window.applyServiceFormState = function(wire, state) {
            if (!wire || !state || typeof state !== 'object') {
                return;
            }
            Object.keys(state).forEach((key) => {
                try {
                    if (typeof wire.set === 'function') {
                        wire.set(key, state[key], false);
                    } else {
                        wire[key] = state[key];
                    }
                } catch (error) {}
            });
        };

        window.initServiceFormDirtyTracking = function() {
            if (window.__serviceFormDirtyTracking) {
                return;
            }
            window.__serviceFormDirtyTracking = true;

            const footerOf = (el) => el?.closest?.('.service-form-footer');
            const formOf = (el) => el?.closest?.('form');
            const rootOf = (el) => el?.closest?.('[wire\\:id]') || formOf(el);
            const hasFooter = (root) => !!root?.querySelector?.('.service-form-footer');
            const store = (root) => {
                if (!root._serviceDirty) {
                    root._serviceDirty = new Set();
                }
                return root._serviceDirty;
            };
            const tick = (root) => {
                root?.querySelectorAll?.('.service-form-footer').forEach((el) => {
                    el.dispatchEvent(new Event('service-footer-sync'));
                });
            };
            const mark = (el, source) => {
                const root = rootOf(el);
                if (!root) {
                    return;
                }
                store(root).add(String(source || 'change'));
                tick(root);
            };
            const fieldKey = (el) => el?.getAttribute?.('wire:model') ||
                el?.getAttribute?.('wire:model.live') ||
                el?.getAttribute?.('x-model') ||
                el?.getAttribute?.('name') ||
                el?.id ||
                null;

            document.addEventListener('input', (event) => {
                if (footerOf(event.target)) {
                    return;
                }
                const root = rootOf(event.target);
                if (!hasFooter(root)) {
                    return;
                }
                mark(event.target, fieldKey(event.target) || 'field');
            }, true);

            document.addEventListener('change', (event) => {
                if (footerOf(event.target)) {
                    return;
                }
                const root = rootOf(event.target);
                if (!hasFooter(root)) {
                    return;
                }
                mark(event.target, fieldKey(event.target) || 'field');
            }, true);

            document.addEventListener('click', (event) => {
                if (footerOf(event.target)) {
                    return;
                }
                const root = rootOf(event.target);
                if (!hasFooter(root)) {
                    return;
                }

                const option = event.target.closest('.service-custom-option');
                if (option) {
                    const select = option.closest('.service-custom-select');
                    const label = select?.closest('.service-field')?.querySelector('span')?.textContent?.trim();
                    mark(option, 'select:' + (label || 'duration'));
                    return;
                }

                const toggle = event.target.closest('.service-toggle');
                if (toggle) {
                    const label = toggle.closest('.service-setting-row')?.querySelector('strong')?.textContent?.trim();
                    mark(toggle, 'toggle:' + (label || 'setting'));
                    return;
                }

                const chip = event.target.closest('.service-chip');
                if (chip) {
                    mark(chip, 'chip:' + (chip.textContent || 'chip').replace(/\s+/g, ' ').trim());
                    return;
                }

                const addOther = event.target.closest('.service-other-pet-add-btn');
                if (addOther) {
                    mark(addOther, 'other-pet-add');
                    return;
                }

                const removeOther = event.target.closest('.service-other-pet-chip-remove');
                if (removeOther) {
                    mark(removeOther, 'other-pet-remove');
                }
            }, true);

            const resetAll = () => {
                document.querySelectorAll('[wire\\:id]').forEach((root) => {
                    if (!hasFooter(root)) {
                        return;
                    }
                    root._serviceDirty = new Set();
                    tick(root);
                });
            };

            window.addEventListener('service-form-baseline', resetAll);
            document.addEventListener('livewire:commit', () => {
                document.querySelectorAll('.service-form-footer').forEach((el) => {
                    el.dispatchEvent(new Event('service-footer-sync'));
                });
            });
        };

        window.initServiceFormDirtyTracking();
    </script>
@endonce
