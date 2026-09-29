{{-- Page-specific styles from my_bookings.php head --}}
<style>
/* .hint {
            font-size: 13px;
            color: #a09080;
            margin-bottom: 16px;
            letter-spacing: .02em;
            min-height: 20px;
        } */
        .sort-by {
            position: relative;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: 1px solid #FBAC83;
            color: #FBAC83;
            padding: 8px 16px;
            border-radius: 100px;
            cursor: pointer;
        }

        .sort-dropdown {
            position: absolute;
            top: 120%;
            right: 0;
            background: #fff;
            border: 1px solid #ccc;
            border-radius: 10px;
            min-width: 230px;
            display: none;
            z-index: 1000;
        }

        .sort-dropdown ul {
            list-style: none;
            margin: 0;
            padding: 0;
        }

        .sort-dropdown li {
            padding: 12px 16px;
            border-bottom: 1px solid #eee;
            color: #3b3731;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 400;
            line-height: normal;
        }

        .sort-dropdown li:last-child {
            border-bottom: none;
        }

        .sort-dropdown label {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;

        }

        .sort-dropdown input {
            display: none;
        }

        .check-circle {
            width: 20px;
            height: 20px;
            border: 1px solid #FBAC83;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            box-sizing: border-box;
        }

        .check-circle::after {
            content: "";
            width: 12px;
            height: 12px;
            background: #FBAC83;
            border-radius: 50%;
            display: none;
        }

        input:checked+.check-circle::after {
            display: block;
        }

        .sort-dropdown.show {
            display: block;
        }

        .calendar-wrapper {
            position: relative;
            display: inline-block;
        }

        .card.show {
            display: block;
        }

        .card {
            background: #fff;
            border-radius: 20px;
            padding: 8px 8px 8px;
            width: 300px;
            user-select: none;
            border-radius: 10px;
            border: 1px solid #D4D4D4;
            background: #FFF;
            position: absolute;
            top: 100%;
            /* below button */
            right: 0;
            margin-top: 8px;
            z-index: 999;
            display: none;
        }

        .header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 18px;
        }

        .pill {
            background: #fff;
            padding: 7px 14px;
            color: #9D9B98;
            text-align: center;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            border-radius: 5px;
            border: 1px solid #F7F7F7;
            background: #FFF;
        }

        .arrows {
            margin-left: auto;
            display: flex;
            gap: 4px;
        }

        .nav {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            border: none;
            background: #f3ede8;
            color: #7a6e66;
            font-size: 18px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .15s;
        }

        .nav:hover {
            background: #e8dfd7;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
        }

        .lbl {
            text-align: center;
            color: #9C9790;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
            padding-bottom: 10px;
        }

        .cell {
            position: relative;
            height: 42px;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
        }

        .cell.empty {
            pointer-events: none;
        }

        /* range strip */
        .cell::before {
            content: '';
            position: absolute;
            top: 0px;
            bottom: 0px;
            left: 0;
            right: 0;
            background: transparent;
            z-index: 0;
            pointer-events: none;
        }

        .cell.in-range::before {
            background: rgba(255, 201, 122, 0.25);
        }

        .cell.rng-s::before {
            background: #fdf0e4;
            left: 50%;
        }

        .cell.rng-e::before {
            background: #fdf0e4;
            right: 50%;
        }

        .num {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 1;
            pointer-events: none;
            transition: background .1s, color .1s;

            color: #3B3731;
            font-family: Lato;
            font-size: 14px;
            font-style: normal;
            font-weight: 600;
            line-height: normal;
        }

        .cell:not(.empty):hover .num {
            background: #f5e6d6;
        }

        .cell.sel-s .num,
        .cell.sel-e .num {
            background: #FFC97A !important;
            color: #fff;
        }

        .cell.empty .num {
            color: transparent;
        }

        .furs-addons-root .radio {
            width: 16px !important;
            height: 16px !important;
        }

        .furs-addons-root .radio::after {
            width: 10px !important;
            height: 10px !important;
        }
</style>
