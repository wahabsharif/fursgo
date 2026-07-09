@once
    <style>
        .pet-compatibility-fieldset .service-chip-row {
            display: flex;
            align-items: center;
            justify-content: start;
            gap: 5rem;
        }

        .pet-compatibility-fieldset .service-chip-row>div>div {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin: 1rem 0;
            flex-wrap: wrap;
        }

        .pet-compatibility-fieldset .service-chip-row>div>span {
            display: inline-block;
            color: #3B3731;
            font-family: Lato;
            font-size: 16px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            margin: 1rem 0 0;
        }

        .pet-compatibility-fieldset .service-chip {
            min-width: 103px;
            max-width: fit-content;
            height: 42px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.45rem;
            border: none;
            border-radius: 22px;
            background: #F7F7F7;
            color: #D4D4D4;
            font-family: Lato;
            font-size: 18px;
            font-style: normal;
            font-weight: 400;
            line-height: 25px;
            padding: 0.5rem 1.5rem;
            cursor: pointer;
            transition: background-color 220ms ease, color 220ms ease, transform 180ms ease;
            will-change: transform;
        }

        .pet-compatibility-fieldset .service-chip.is-active {
            background: rgba(216, 232, 183, 0.20);
            color: #A4C560;
            transform: translateY(-1px) scale(1.02);
        }

        .pet-compatibility-fieldset .service-chip:active {
            transform: scale(0.98);
        }

        .pet-compatibility-fieldset .service-chip-icon {
            color: #D4D4D4;
            flex-shrink: 0;
            transition: color 220ms ease, transform 220ms ease;
        }

        .pet-compatibility-fieldset .service-chip.is-active .service-chip-icon {
            color: #A4C560;
            transform: scale(1.06);
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
    </style>
@endonce
