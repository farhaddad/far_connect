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

---

## Changelog

### 0.1.3-beta

- Added: Honeypot spam filter. A hidden field is injected into every form; any submission with it filled is rejected before the captcha check runs. Off/On toggle in Admin under Spam Protection, enabled by default.
- Added: Honeypot field label setting. Controls the label text on the hidden honeypot input. Defaults to `Referral code`. Configurable so you can choose a label that looks realistic to bots but is not a standard autofill target for password managers.
- Added: No-JS fallback. When JavaScript is disabled, the captcha widget never loads and its token is never submitted. The plugin detects the absent token and skips captcha verification, relying on the honeypot instead. The form continues to work.
- Changed: Mail Provider default changed from SMTP to None. The plugin no longer intercepts mail delivery until a provider is explicitly chosen, so installing without configuration leaves com_connect behaviour unchanged.
- Changed: Added None option to Mail Provider. From Email and From Name fields are hidden in the admin panel when None is selected, as they have no effect without an active provider.
- Fixed: SMTP provider description clarified. com_connect uses PHP's built-in `mail()` directly and ignores Textpattern's mail preferences. The SMTP option routes com_connect mail through TXP's mail adapter, which is the only way to enable SMTP delivery for com_connect forms.
- Changed: Captcha section renamed to Spam Protection to reflect the broader scope.
- Added: `far_connect_honeypot_label` and `far_connect_honeypot_field_label` Textpack strings.

### 0.1.2-beta

- Fixed: All CSS and JavaScript identifiers renamed from `far-` to `far-com-` prefix to avoid conflicts with other plugins.
- Fixed: Stylesheet injection switched from `ob_start` to `comconnect.form` hook, which is more reliable across all TXP themes and output buffering configurations.
- Fixed: Double-encoded URLs in both injection methods.
- Fixed: Protocol mismatch on HTTPS sites behind Cloudflare reverse proxy. Stylesheet URL now uses a protocol-relative `//` scheme.
- Fixed: Stale stylesheet in database not regenerating after class prefix rename. A staleness check now updates existing rows automatically.
- Fixed: Theme detection broken when form does not use the default `comConnectForm` class. Selector changed to `[name=com_connect_nonce]`, which is always present.
- Fixed: Legend injected above the form heading when an `<h1>`–`<h6>` element is present. DOM walker now skips text nodes correctly.
- Fixed: Captcha widget overflow on narrow mobile screens. The `.far-com-captcha-group` wrapper now constrains width with `overflow: hidden`; both Turnstile and hCaptcha use `data-size="normal"`.
- Fixed: Duplicate ARIA label IDs on pages with more than one form. Captcha label now uses a per-instance counter.
- Fixed: Captcha failure gave no user-facing message. Now calls `add_comconnect_reason()` with a translatable error string.
- Changed: Stylesheet injection method options renamed: Inline link to Automatic, JavaScript to Deferred, Disabled to Manual.
- Added: `far_connect_captcha_failed` Textpack string for the captcha error message shown on failed submission.
- Added: Introduction, Testing your setup, Troubleshooting, and Fallback behaviour sections in plugin help.
- Removed: 9 unused Textpack strings and two dead code items.

### 0.1.1-beta

- Fixed: Reset to defaults confirmation dialog never appeared due to onclick handler passed to wrong `fInput()` parameter.
- Fixed: Help popup (?) sometimes hung or did nothing because the XML file was being rewritten on every admin page load. Now only written on install, enable, or upgrade.
- Fixed: Admin panel layout now matches TXP native Preferences structure exactly.
- Fixed: Removed custom admin CSS block; admin panel now uses TXP theme styles entirely.
- Changed: Save button label changed from "Save Settings" to "Save" to match TXP conventions.
- Changed: Reset to defaults button moved beside Save.
- Changed: Sidebar nav heading changed to "Settings".
- Changed: Auto theme field changed from checkbox to Yes/No radio buttons.
- Added: Help popup for From Name field.
- Added: From Email help popup now includes domain verification example.

### 0.1.0-beta

- Initial beta release.
- Mail delivery via Resend, Brevo, and SMTP.
- Captcha support: Cloudflare Turnstile, Google reCAPTCHA v3, hCaptcha.
- Fixed: "Add Rule" button not working when no existing either/or rules were saved.
- Fixed: Either/or rules table expanding width when a new row was added.
- Fixed: Submit button incorrectly enabled when captcha widget replaced its DOM node before `DOMContentLoaded`.
- Fixed: Admin panel JS not executing because inline `<script>` relied on `DOMContentLoaded` which had already fired.
- Added: Help popups for all fields.
- Added: API key security guidance in help doc and key field popups.
