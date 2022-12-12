/*!
 * CookieConsent v2.8.8
 * https://www.github.com/orestbida/cookieconsent
 * Author Orest Bida
 * Released under the MIT License
 */
// obtain plugin
var cc = initCookieConsent();

// run plugin with your configuration
var currentLanguage = document.documentElement.getAttribute('lang');

cc.run({
    current_lang: currentLanguage,
    autoclear_cookies: true,                   // default: false
    page_scripts: true,                        // default: false

    // mode: 'opt-in'                          // default: 'opt-in'; value: 'opt-in' or 'opt-out'
    // delay: 0,                               // default: 0
    // auto_language: null                     // default: null; could also be 'browser' or 'document'
    // autorun: true,                          // default: true
    // force_consent: false,                   // default: false
    // hide_from_bots: false,                  // default: false
    remove_cookie_tables: true,             // default: false
    // cookie_name: 'cc_cookie',               // default: 'cc_cookie'
    cookie_expiration: 30,                 // default: 182 (days)
    // cookie_necessary_only_expiration: 182   // default: disabled
    // cookie_domain: location.hostname,       // default: current domain
    // cookie_path: '/',                       // default: root
    // cookie_same_site: 'Lax',                // default: 'Lax'
    // use_rfc_cookie: false,                  // default: false
    // revision: 0,                            // default: 0

    onFirstAction: function(user_preferences, cookie){
        // callback triggered only once
    },

    onAccept: function (cookie) {
        updateData()
    },

    onChange: function (cookie, changed_preferences) {
        updateData()
    },

    languages: {
        'cs': {
            consent_modal: {
                title: 'Používáme cookie!',
                description: 'Ahoj, tyto webové stránky používají základní soubory cookie k zajištění svého správného fungování a sledovací soubory cookie k pochopení toho, jak s nimi pracujete. Ty se nastavují pouze po souhlasu. <button type="button" data-cc="c-settings" class="cc-link">Upřesnit nastavení</button>',
                primary_btn: {
                    text: 'Příjmout vše',
                    role: 'accept_all'              // 'accept_selected' or 'accept_all'
                },
                secondary_btn: {
                    text: 'Odmítnout vše',
                    role: 'accept_necessary'        // 'settings' or 'accept_necessary'
                }
            },
            settings_modal: {
                title: 'Nastavení cookies',
                save_settings_btn: 'Uložit nastavení',
                accept_all_btn: 'Příjmout vše',
                reject_all_btn: 'Odmítnout vše',
                close_btn_label: 'Zavřít',
                cookie_table_headers: [
                    {col1: 'Jméno'},
                    {col2: 'Doména'},
                    {col3: 'Expirace'},
                    {col4: 'Popis'}
                ],
                blocks: [
                    {
                        title: 'Použití cookies',
                        description: 'Soubory cookie používáme k zajištění základních funkcí webových stránek a ke zlepšení vašeho online zážitku. U každé kategorie si můžete zvolit, zda se chcete přihlásit nebo odhlásit, kdykoli budete chtít. Další podrobnosti týkající se souborů cookie a dalších citlivých údajů naleznete v úplném znění zde: <a href="/cs/ochrana-osobnich-udaju" class="cc-link">Ochrana osobních údajů</a>.'
                    }, {
                        title: 'Nezbytné technické cookies',
                        description: 'Tyto soubory cookie jsou nezbytné pro správné fungování mých webových stránek. Bez těchto souborů cookie by webové stránky nefungovaly správně.',
                        toggle: {
                            value: 'necessary',
                            enabled: true,
                            readonly: true          // cookie categories with readonly=true are all treated as "necessary cookies"
                        }
                    }, {
                        title: 'Výkonnostní a analitické cookies',
                        description: 'Tyto soubory cookie umožňují webové stránce zapamatovat si volby, které jste provedli v minulosti.',
                        toggle: {
                            value: 'analytics',     // your cookie category
                            enabled: false,
                            readonly: false
                        },
                        cookie_table: [             // list of all expected cookies
                            {
                                col1: '^_ga',       // match all cookies starting with "_ga"
                                col2: 'google.com',
                                col3: '2 years',
                                col4: 'description ...',
                                is_regex: true
                            },
                            {
                                col1: '_gid',
                                col2: 'google.com',
                                col3: '1 day',
                                col4: 'description ...',
                            }
                        ]
                    }, {
                        title: 'Reklamní a personalizované cookies',
                        description: 'Tyto soubory cookie shromažďují informace o tom, jak webové stránky používáte, které stránky jste navštívili a na které odkazy jste klikli. Všechny údaje jsou anonymizované a nelze je použít k vaší identifikaci.',
                        toggle: {
                            value: 'targeting',
                            enabled: false,
                            readonly: false
                        }
                    }, {
                        title: 'Více informací',
                        description: 'V případě jakýchkoli dotazů týkajících se našich zásad o souborech cookie <a class="cc-link" href="#yourcontactpage">nás kontaktujte</a>.',
                    }
                ]
            }
        },
        'en': {
            consent_modal: {
                title: 'We use cookies!',
                description: 'Hi, this website uses essential cookies to ensure its proper operation and tracking cookies to understand how you interact with it. The latter will be set only after consent. <button type="button" data-cc="c-settings" class="cc-link">Let me choose</button>',
                primary_btn: {
                    text: 'Accept all',
                    role: 'accept_all'              // 'accept_selected' or 'accept_all'
                },
                secondary_btn: {
                    text: 'Reject all',
                    role: 'accept_necessary'        // 'settings' or 'accept_necessary'
                }
            },
            settings_modal: {
                title: 'Cookie preferences',
                save_settings_btn: 'Save settings',
                accept_all_btn: 'Accept all',
                reject_all_btn: 'Reject all',
                close_btn_label: 'Close',
                cookie_table_headers: [
                    {col1: 'Name'},
                    {col2: 'Domain'},
                    {col3: 'Expiration'},
                    {col4: 'Description'}
                ],
                blocks: [
                    {
                        title: 'Cookie usage',
                        description: 'I use cookies to ensure the basic functionalities of the website and to enhance your online experience. You can choose for each category to opt-in/out whenever you want. For more details relative to cookies and other sensitive data, please read the full <a href="/en/privacy-policy" class="cc-link">privacy policy</a>.'
                    }, {
                        title: 'Strictly necessary cookies',
                        description: 'These cookies are essential for the proper functioning of my website. Without these cookies, the website would not work properly',
                        toggle: {
                            value: 'necessary',
                            enabled: true,
                            readonly: true          // cookie categories with readonly=true are all treated as "necessary cookies"
                        }
                    }, {
                        title: 'Performance and Analytics cookies',
                        description: 'These cookies allow the website to remember the choices you have made in the past',
                        toggle: {
                            value: 'analytics',     // your cookie category
                            enabled: false,
                            readonly: false
                        },
                        cookie_table: [             // list of all expected cookies
                            {
                                col1: '^_ga',       // match all cookies starting with "_ga"
                                col2: 'google.com',
                                col3: '2 years',
                                col4: 'description ...',
                                is_regex: true
                            },
                            {
                                col1: '_gid',
                                col2: 'google.com',
                                col3: '1 day',
                                col4: 'description ...',
                            }
                        ]
                    }, {
                        title: 'Advertisement and Targeting cookies',
                        description: 'These cookies collect information about how you use the website, which pages you visited and which links you clicked on. All of the data is anonymized and cannot be used to identify you',
                        toggle: {
                            value: 'targeting',
                            enabled: false,
                            readonly: false
                        }
                    }, {
                        title: 'More information',
                        description: 'For any queries in relation to our policy on cookies and your choices, please <a class="cc-link" href="#yourcontactpage">contact us</a>.',
                    }
                ]
            }
        }
    }
});

function updateData() {
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('consent', 'update', {
        analytics_storage: cc.allowedCategory('analytics') ? 'granted' : 'denied',
        ad_storage: cc.allowedCategory('advertising') ? 'granted': 'denied',
        personalization_storage: cc.allowedCategory('advertising') ? 'granted': 'denied'
    })
}
