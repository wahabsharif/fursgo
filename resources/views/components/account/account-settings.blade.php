@props([
    'settings',
    'passwordUpdatedLabel' => 'Never updated.',
    'showPasswordModal' => false,
    'activeSessions' => [],
    'blockedUsers' => [],
])

@php
    use App\Support\AccountLanguages;
    $navItems = [
        'general' => [
            'label' => 'General',
            'icon' => 'general.svg',
        ],
        'notification' => [
            'label' => 'Notifications',
            'icon' => 'notifications.svg',
        ],
        'login_and_security' => [
            'label' => 'Login & Security',
            'icon' => 'login-security.svg',
        ],
        'privacy_and_permissions' => [
            'label' => 'Privacy & Permissions',
            'icon' => 'privacy.svg',
        ],
        'app_and_system' => [
            'label' => 'App & System Preferences',
            'icon' => 'preferences.svg',
        ],
        'account_linking' => [
            'label' => 'Account Linking',
            'icon' => 'linking.svg',
        ],
        'data_and_legal' => [
            'label' => 'Data & Legal',
            'icon' => 'legal.svg',
        ],
    ];

    $initialTab = request()->query('tab', 'general');
    if (!array_key_exists($initialTab, $navItems)) {
        $initialTab = 'general';
    }

    $timezones = DateTimeZone::listIdentifiers(DateTimeZone::ALL);

    $currencySymbols = [
        'AED' => 'د.إ',
        'AFN' => '؋',
        'ALL' => 'L',
        'AMD' => '֏',
        'ANG' => 'ƒ',
        'AOA' => 'Kz',
        'ARS' => '$',
        'AUD' => 'A$',
        'AWG' => 'ƒ',
        'AZN' => '₼',
        'BAM' => 'KM',
        'BBD' => '$',
        'BDT' => '৳',
        'BGN' => 'лв',
        'BHD' => '.د.ب',
        'BIF' => 'FBu',
        'BMD' => '$',
        'BND' => '$',
        'BOB' => 'Bs.',
        'BRL' => 'R$',
        'BSD' => '$',
        'BTN' => 'Nu.',
        'BWP' => 'P',
        'BYN' => 'Br',
        'BZD' => '$',
        'CAD' => 'C$',
        'CDF' => 'FC',
        'CHF' => 'CHF',
        'CLP' => '$',
        'CNY' => '¥',
        'COP' => '$',
        'CRC' => '₡',
        'CUP' => '$',
        'CVE' => '$',
        'CZK' => 'Kč',
        'DJF' => 'Fdj',
        'DKK' => 'kr',
        'DOP' => '$',
        'DZD' => 'دج',
        'EGP' => 'E£',
        'ERN' => 'Nfk',
        'ETB' => 'Br',
        'EUR' => '€',
        'FJD' => '$',
        'FKP' => '£',
        'GBP' => '£',
        'GEL' => '₾',
        'GHS' => '₵',
        'GIP' => '£',
        'GMD' => 'D',
        'GNF' => 'FG',
        'GTQ' => 'Q',
        'GYD' => '$',
        'HKD' => 'HK$',
        'HNL' => 'L',
        'HRK' => '€',
        'HTG' => 'G',
        'HUF' => 'Ft',
        'IDR' => 'Rp',
        'ILS' => '₪',
        'INR' => '₹',
        'IQD' => 'ع.د',
        'IRR' => '﷼',
        'ISK' => 'kr',
        'JMD' => '$',
        'JOD' => 'د.ا',
        'JPY' => '¥',
        'KES' => 'KSh',
        'KGS' => 'с',
        'KHR' => '៛',
        'KMF' => 'CF',
        'KPW' => '₩',
        'KRW' => '₩',
        'KWD' => 'د.ك',
        'KYD' => '$',
        'KZT' => '₸',
        'LAK' => '₭',
        'LBP' => 'ل.ل',
        'LKR' => 'Rs',
        'LRD' => '$',
        'LSL' => 'L',
        'LYD' => 'ل.د',
        'MAD' => 'د.م.',
        'MDL' => 'L',
        'MGA' => 'Ar',
        'MKD' => 'ден',
        'MMK' => 'K',
        'MNT' => '₮',
        'MOP' => 'P',
        'MRU' => 'UM',
        'MUR' => '₨',
        'MVR' => 'Rf',
        'MWK' => 'MK',
        'MXN' => 'Mex$',
        'MYR' => 'RM',
        'MZN' => 'MT',
        'NAD' => '$',
        'NGN' => '₦',
        'NIO' => 'C$',
        'NOK' => 'kr',
        'NPR' => '₨',
        'NZD' => 'NZ$',
        'OMR' => 'ر.ع.',
        'PAB' => 'B/.',
        'PEN' => 'S/',
        'PGK' => 'K',
        'PHP' => '₱',
        'PKR' => '₨',
        'PLN' => 'zł',
        'PYG' => '₲',
        'QAR' => 'ر.ق',
        'RON' => 'lei',
        'RSD' => 'дин.',
        'RUB' => '₽',
        'RWF' => 'FRw',
        'SAR' => 'ر.س',
        'SBD' => '$',
        'SCR' => '₨',
        'SDG' => 'ج.س.',
        'SEK' => 'kr',
        'SGD' => 'S$',
        'SHP' => '£',
        'SLE' => 'Le',
        'SOS' => 'Sh',
        'SRD' => '$',
        'SSP' => '£',
        'STN' => 'Db',
        'SYP' => '£',
        'SZL' => 'L',
        'THB' => '฿',
        'TJS' => 'ЅМ',
        'TMT' => 'm',
        'TND' => 'د.ت',
        'TOP' => 'T$',
        'TRY' => '₺',
        'TTD' => '$',
        'TWD' => 'NT$',
        'TZS' => 'TSh',
        'UAH' => '₴',
        'UGX' => 'USh',
        'USD' => '$',
        'UYU' => '$',
        'UZS' => 'soʻm',
        'VES' => 'Bs.',
        'VND' => '₫',
        'VUV' => 'VT',
        'WST' => 'T',
        'XAF' => 'FCFA',
        'XCD' => '$',
        'XOF' => 'CFA',
        'XPF' => '₣',
        'YER' => '﷼',
        'ZAR' => 'R',
        'ZMW' => 'ZK',
        'ZWL' => '$',
    ];

    $currencies = [];
    foreach ($currencySymbols as $code => $symbol) {
        $currencies[] = [
            'code' => $code,
            'label' => $symbol . ' - ' . $code,
        ];
    }

    $languages = AccountLanguages::options();
    $selectedLanguageLabel = AccountLanguages::labelFor($settings['language'] ?? 'en_GB');
    $selectedTimezone = $settings['timezone'] ?? 'Europe/London';
    $selectedCurrencyCode = $settings['currency'] ?? 'GBP';
    $selectedCurrencyLabel =
        ($currencySymbols[$selectedCurrencyCode] ?? $selectedCurrencyCode) . ' - ' . $selectedCurrencyCode;
@endphp

<div class="account-settings-page" x-data="{
    activeTab: @js($initialTab),
    isSwitching: false,
    loadingTimer: null,
    openSelect: null,
    timezones: @js($timezones),
    currencies: @js($currencies),
    languages: @js($languages),
    preferences: @entangle('settings').live,
    languageLabel() {
        if (!this.preferences?.language) {
            return '';
        }

        const match = this.languages.find((item) => item.code === this.preferences.language);
        return match ? match.label : this.preferences.language;
    },
    currencyLabel() {
        if (!this.preferences?.currency) {
            return '';
        }

        const match = this.currencies.find((item) => item.code === this.preferences.currency);
        return match ? match.label : this.preferences.currency;
    },
    setTab(tab) {
        if (tab === this.activeTab) {
            return;
        }

        this.activeTab = tab;
        this.openSelect = null;
        this.isSwitching = true;

        clearTimeout(this.loadingTimer);
        this.loadingTimer = setTimeout(() => {
            this.isSwitching = false;
        }, 280);
    },
    toggleSelect(name) {
        this.openSelect = this.openSelect === name ? null : name;
    },
    persistGeneral(key, value, rollback) {
        this.$wire.updateGeneralSetting(key, value)
            .catch(() => rollback());
    },
    selectLanguage(code) {
        const previous = this.preferences.language;
        this.preferences.language = code;
        this.openSelect = null;
        this.persistGeneral('language', code, () => {
            this.preferences.language = previous;
        });
    },
    selectTimezone(value) {
        const previous = this.preferences.timezone;
        this.preferences.timezone = value;
        this.openSelect = null;
        this.persistGeneral('timezone', value, () => {
            this.preferences.timezone = previous;
        });
    },
    selectCurrency(code) {
        const previous = this.preferences.currency;
        this.preferences.currency = code;
        this.openSelect = null;
        this.persistGeneral('currency', code, () => {
            this.preferences.currency = previous;
        });
    },
    togglePreference(key) {
        const previous = Boolean(this.preferences[key]);
        this.preferences[key] = !previous;

        this.$wire.updateBooleanSetting(key, this.preferences[key])
            .catch(() => this.preferences[key] = previous);
    },
    setTheme(theme) {
        const previous = this.preferences.theme;
        this.preferences.theme = theme;
        this.persistGeneral('theme', theme, () => this.preferences.theme = previous);
    },
}" @click.outside="openSelect = null">
    <div class="account-settings-loading-bar" :class="{ 'is-tab-loading': isSwitching }"
        wire:loading.class="is-modal-loading" wire:target="openPasswordModal,closePasswordModal,updatePassword" x-cloak
        aria-hidden="true">
        <span class="account-settings-loading-bar__sweep"></span>
    </div>

    <div class="account-settings-layout">
        <aside class="account-settings-sidebar" aria-label="Account settings navigation">
            <nav class="account-settings-nav">
                @foreach ($navItems as $tabId => $item)
                    <button type="button" class="account-settings-nav__item"
                        :class="{ 'is-active': activeTab === @js($tabId) }"
                        @click="setTab(@js($tabId))">
                        <img src="{{ asset('images/account-settings/' . $item['icon']) }}" alt=""
                            class="account-settings-nav__icon" width="16" height="16">
                        <span>{{ $item['label'] }}</span>
                    </button>
                @endforeach
            </nav>
        </aside>

        <div class="account-settings-main">
            <header class="account-settings-page-header">
                <h1 class="account-settings-page-title">Account Settings</h1>
                <p class="account-settings-page-subtitle">
                    Manage your preferences, privacy, and account controls.
                </p>
            </header>

            <section class="account-settings-panel" x-show="activeTab === 'general'"
                x-transition:enter="account-settings-panel-enter"
                x-transition:enter-start="account-settings-panel-enter-start"
                x-transition:enter-end="account-settings-panel-enter-end"
                x-transition:leave="account-settings-panel-leave"
                x-transition:leave-start="account-settings-panel-leave-start"
                x-transition:leave-end="account-settings-panel-leave-end" x-cloak>
                <h2 class="account-settings-section-title">General Settings</h2>

                <div class="account-settings-fields">
                    <div class="account-settings-field">
                        <label for="account-language">Language</label>
                        <div class="account-settings-select">
                            <button id="account-language" type="button"
                                class="account-settings-select__trigger has-value"
                                @click.stop="toggleSelect('language')" :aria-expanded="openSelect === 'language'">
                                <span x-text="languageLabel()">{{ $selectedLanguageLabel }}</span>
                                <img src="{{ asset('images/account-settings/chevron.svg') }}" alt=""
                                    class="account-settings-select__chevron" width="13" height="13">
                            </button>
                            <ul class="account-settings-select__options" x-show="openSelect === 'language'" x-cloak>
                                <template x-for="item in languages" :key="item.code">
                                    <li class="account-settings-select__option"
                                        :class="{ 'is-selected': preferences.language === item.code }"
                                        @click="selectLanguage(item.code)" x-text="item.label"></li>
                                </template>
                            </ul>
                        </div>
                    </div>

                    <div class="account-settings-field">
                        <label for="account-timezone">Time Zone</label>
                        <div class="account-settings-select">
                            <button id="account-timezone" type="button"
                                class="account-settings-select__trigger has-value"
                                @click.stop="toggleSelect('timezone')" :aria-expanded="openSelect === 'timezone'">
                                <span x-text="preferences.timezone">{{ $selectedTimezone }}</span>
                                <img src="{{ asset('images/account-settings/chevron.svg') }}" alt=""
                                    class="account-settings-select__chevron" width="13" height="13">
                            </button>
                            <ul class="account-settings-select__options" x-show="openSelect === 'timezone'" x-cloak>
                                <template x-for="tz in timezones" :key="tz">
                                    <li class="account-settings-select__option"
                                        :class="{ 'is-selected': preferences.timezone === tz }"
                                        @click="selectTimezone(tz)" x-text="tz"></li>
                                </template>
                            </ul>
                        </div>
                    </div>

                    <div class="account-settings-field">
                        <label for="account-currency">Currency Preference</label>
                        <div class="account-settings-select">
                            <button id="account-currency" type="button"
                                class="account-settings-select__trigger has-value"
                                @click.stop="toggleSelect('currency')" :aria-expanded="openSelect === 'currency'">
                                <span x-text="currencyLabel()">{{ $selectedCurrencyLabel }}</span>
                                <img src="{{ asset('images/account-settings/chevron.svg') }}" alt=""
                                    class="account-settings-select__chevron" width="13" height="13">
                            </button>
                            <ul class="account-settings-select__options" x-show="openSelect === 'currency'" x-cloak>
                                <template x-for="item in currencies" :key="item.code">
                                    <li class="account-settings-select__option"
                                        :class="{ 'is-selected': preferences.currency === item.code }"
                                        @click="selectCurrency(item.code)" x-text="item.label"></li>
                                </template>
                            </ul>
                        </div>
                    </div>
                </div>
            </section>

            @foreach (array_keys($navItems) as $tabId)
                @if ($tabId !== 'general')
                    <section class="account-settings-panel account-settings-legacy-panel"
                        x-show="activeTab === @js($tabId)"
                        x-transition:enter="account-settings-panel-enter"
                        x-transition:enter-start="account-settings-panel-enter-start"
                        x-transition:enter-end="account-settings-panel-enter-end"
                        x-transition:leave="account-settings-panel-leave"
                        x-transition:leave-start="account-settings-panel-leave-start"
                        x-transition:leave-end="account-settings-panel-leave-end" x-cloak>
                        @include('components.account.settings-panels.' . $tabId)
                    </section>
                @endif
            @endforeach
        </div>
    </div>
</div>

@push('script')
    <script src="{{ asset('js/common.js') }}"></script>
@endpush
