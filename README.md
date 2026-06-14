# far_connect

A [Textpattern CMS](https://textpattern.com) plugin that extends [com_connect](https://github.com/textpattern/com_connect) with transactional mail delivery, spam protection, and form validation.

## Features

- **Mail delivery** — send form submissions through [Resend](https://resend.com), [Brevo](https://brevo.com), or SMTP (routes com_connect through TXP's mail adapter, enabling SMTP delivery that com_connect does not support natively)
- **Spam protection** — honeypot field, captcha (Cloudflare Turnstile, Google reCAPTCHA v3, hCaptcha), or both together. Works without JavaScript: the captcha is skipped and the honeypot protects the form
- **Form validation** — either/or rules that require at least one of a set of fields to be filled before the form can submit

All three features are optional and independent. You can use far_connect for mail delivery only, spam protection only, or any combination.

## Requirements

- Textpattern 4.9+
- PHP 8.0+
- PHP `curl` extension enabled
- [com_connect](https://github.com/textpattern/com_connect) plugin installed and active

## Installation

1. Download `far_connect.php` from the [latest release](../../releases/latest)
2. In Textpattern go to **Admin › Plugins**
3. Paste the plugin code into the Install plugin box and click **Upload**
4. Activate the plugin

## Quick start

1. Go to **Extensions › Far Connect**
2. To enable transactional mail, choose a **Mail Provider** and fill in the credentials. Leave it set to None if you only need spam protection
3. Optionally choose a **Captcha Provider** and enter your keys, or enable the **Honeypot spam filter**, or both
4. Optionally configure **Form Validation** rules
5. Click **Save**

Full documentation is available in the plugin's built-in help: click the **?** button next to any field in the admin panel, or go to **Admin › Plugins** and click the help icon next to far_connect.

## Mail providers

| Provider | Notes |
|---|---|
| None | far_connect does not intercept mail. com_connect sends via PHP `mail()` as normal |
| SMTP | Routes com_connect mail through TXP's mail adapter, enabling SMTP delivery |
| Resend | Transactional API. Requires an API key and a verified sending domain at resend.com |
| Brevo | Transactional API. Requires an API key and a verified sending domain at brevo.com |

## Spam protection

| Method | Notes |
|---|---|
| Honeypot | Hidden field injected into every form. Enabled by default, no account required, works without JavaScript |
| Cloudflare Turnstile | Privacy-friendly captcha, usually no user interaction needed |
| Google reCAPTCHA v3 | Score-based, fully invisible to users |
| hCaptcha | Privacy-focused alternative to reCAPTCHA |

## License

Released under the [GNU General Public License v2.0](LICENSE).

## Author

[Farhan Haddad](https://farhan.design)
