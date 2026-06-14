<?php
// Copyright (C) 2026 Farhan Haddad
// Released under the GNU General Public License, version 2.
// https://www.gnu.org/licenses/old-licenses/gpl-2.0.html

$plugin['name']        = 'far_connect';
$plugin['version']     = '0.1.3-beta';
$plugin['author']      = 'Farhan Haddad';
$plugin['author_uri']  = 'https://farhan.design';
$plugin['description'] = 'Mail delivery and captcha addon for com_connect';
$plugin['type']        = '1';
$plugin['order']       = '5';
$plugin['flags']       = '0';

$plugin['textpack'] = <<<'EOT'
#@public
#@language en, en-gb, en-us
far_connect_settings_title => Far Connect
far_connect_mail_section => Mail Delivery
far_connect_captcha_section => Spam Protection
far_connect_mail_provider => Mail Provider
far_connect_captcha_provider => Captcha Provider
far_connect_from_email => From Email
far_connect_from_name => From Name
far_connect_resend_api_key => Resend API Key
far_connect_brevo_api_key => Brevo API Key
far_connect_turnstile_site_key => Turnstile Site Key
far_connect_turnstile_secret_key => Turnstile Secret Key
far_connect_recaptcha_site_key => reCAPTCHA Site Key
far_connect_recaptcha_secret_key => reCAPTCHA Secret Key
far_connect_hcaptcha_site_key => hCaptcha Site Key
far_connect_hcaptcha_secret_key => hCaptcha Secret Key
far_connect_api_key_valid => API key is valid.
far_connect_not_configured_key => API key is not configured.
far_connect_not_configured_email => From email is not configured.
far_connect_eitheror_section => Form Validation
far_connect_eitheror_class => Form Class
far_connect_eitheror_fields => Either/Or Fields
far_connect_eitheror_add => Add Rule
far_connect_eitheror_remove => Remove rule
far_connect_eitheror_msg => Please fill in at least one of: {fields}.
far_connect_eitheror_legend => Fields marked * are required. Fields marked † require at least one to be filled.
far_connect_required_legend => Fields marked * are required.
far_connect_eitheror_class_label => Form class
far_connect_eitheror_fields_label => Either/or field names
far_connect_auto_markers_label => Auto markers
far_connect_eitheror_rules_label => Either/or rules
far_connect_captcha_label => Security verification
far_connect_captcha_failed => Security verification failed. Please try again.
far_connect_smtp_error => Mail could not be sent. Check your settings in Admin › Preferences › Mail.
far_connect_provider_none => None
far_connect_provider_smtp => SMTP
far_connect_provider_resend => Resend
far_connect_provider_brevo => Brevo
far_connect_provider_turnstile => Cloudflare Turnstile
far_connect_provider_recaptcha => Google reCAPTCHA v3
far_connect_provider_hcaptcha => hCaptcha
far_connect_saved => Settings saved.
far_connect_reset => Reset to defaults
far_connect_reset_confirm => This will permanently delete all settings and the far_connect stylesheet. Use this before deleting the plugin. Are you sure?
far_connect_reset_done => All settings and the stylesheet have been reset to defaults.
far_connect_nav_label => Settings
far_connect_stylesheet_section => Stylesheet
far_connect_css_inject => Injection method
far_connect_css_inject_buffer => Automatic
far_connect_css_inject_js => Deferred
far_connect_css_inject_none => Manual
far_connect_auto_theme_label => Detect background color and apply light or dark styles?
far_connect_honeypot_label => Honeypot spam filter
far_connect_honeypot_field_label => Honeypot field label
EOT;

if (0) {
?>
# --- BEGIN PLUGIN HELP ---

h1. far_connect

h2. Introduction

far_connect extends "com_connect":https://github.com/textpattern/com_connect with three capabilities that the core plugin does not provide:

* *Mail delivery*: send form submissions through a transactional email API (Resend or Brevo) or your existing SMTP server, instead of relying on PHP's built-in @mail()@. Transactional providers offer better deliverability, sender reputation management, and a delivery log in their dashboard.

* *Spam protection*: protect forms from spam bots with a captcha (Cloudflare Turnstile, Google reCAPTCHA v3, or hCaptcha), a honeypot field, or both together. The submit button is disabled until the captcha is solved, and tokens are verified server-side on every submission.

* *Form validation*: add either/or rules that require at least one of a set of fields to be filled before the form can submit. Useful when you want to accept either an email address or a phone number, but not allow both to be left blank.

All three features are optional and independent. You can use far_connect for mail delivery only, spam protection only, or any combination.

h2. Requirements

* Textpattern 4.9+
* PHP 8.0+
* PHP @curl@ extension enabled
* com_connect plugin installed and active

h2. Quick start

# Install and activate both @com_connect@ and @far_connect@.
# Go to *Extensions › Far Connect*.
# To enable transactional mail, choose a *Mail Provider* and fill in the credentials. Leave it set to None if you only need spam protection.
# Optionally choose a *Captcha Provider* and enter your keys, or enable the *Honeypot spam filter*, or both.
# Optionally configure *Form Validation* rules.
# Click *Save*.

h2. Mail delivery

When a provider is selected, far_connect intercepts com_connect form submissions and routes them through that provider. When set to None, far_connect does not intercept mail and com_connect sends via PHP's built-in @mail()@ directly, bypassing any SMTP settings configured in Textpattern.

|_. Provider |_. Notes |
| None | Disables far_connect mail delivery. com_connect sends via PHP's built-in @mail()@ regardless of TXP's mail settings |
| Resend | Sends via the Resend API. Requires an API key and a verified sending domain at resend.com |
| Brevo | Sends via the Brevo API. Requires an API key and a verified sending domain at brevo.com |
| SMTP | Routes com_connect mail through TXP's mail adapter, enabling SMTP delivery configured in Admin › Preferences › Mail |

*From Email* is the address recipients see in the From field. The domain must be verified with your provider (Resend or Brevo). For SMTP it should match your sending account configured in Admin › Preferences › Mail.

*From Name* is the display name shown alongside the From Email in the recipient's inbox. If left blank, only the email address is shown.

h3. Fallback behaviour

If the selected provider fails to deliver (for example due to a network timeout or an invalid API key), far_connect falls back silently to com_connect's built-in PHP @mail()@. This means the form will still work in degraded conditions, but the email will arrive from your server's default mail address rather than the configured From Email. Check your provider dashboard if you suspect this is happening.

Note: com_connect uses PHP's built-in @mail()@ directly regardless of Textpattern's mail preferences. If you have SMTP configured in Admin › Preferences › Mail, it will not apply to com_connect unless far_connect is active and set to SMTP or a transactional provider.

h3. API key security

API keys are stored in the Textpattern database without encryption. A stolen mail API key could allow an attacker to send emails from your domain, potentially damaging your sender reputation or running up your usage.

To limit the risk, use a *restricted API key* scoped only to sending email, not account management or billing. Both Resend and Brevo support this in their dashboards. If a key is ever compromised, revoke it immediately from your provider dashboard and generate a new one.

h2. Spam Protection

Protects forms from spam using a captcha, a honeypot field, or both. Each mechanism works independently: you can use either one without the other.

h3. Captcha

A visible or invisible challenge that requires JavaScript. The submit button is disabled until the challenge is solved, and the token is verified server-side on every submission.

|_. Provider |_. Notes |
| Cloudflare Turnstile | Privacy-friendly, usually no user interaction needed |
| Google reCAPTCHA v3 | Score-based, fully invisible to users |
| hCaptcha | Privacy-focused alternative to reCAPTCHA |

Each provider requires a *Site Key* (public, embedded in your page) and a *Secret Key* (private, used server-side to verify tokens). Get both from your provider's dashboard.

h3. Honeypot

A hidden field added to every form that real users never see. Spam bots automatically fill in every field they find, including hidden ones. Any submission with the honeypot field filled is rejected immediately, before any captcha check runs.

The honeypot requires no third-party account and works without JavaScript. When JavaScript is disabled in the browser, the captcha widget never loads; the honeypot is the only server-side protection in that case. This means the form continues to work for users with JavaScript off: no captcha appears, but any bot that fills the honeypot is still blocked.

Using both together gives layered protection: the captcha blocks JavaScript-enabled bots that would pass the honeypot, and the honeypot blocks no-JS bots that never trigger the captcha.

The *Honeypot field label* setting controls the text on the hidden input as it appears in the page HTML. Choose a label that looks like a real field to bots but is not a standard autofill target for password managers. The default "Referral code" works well. Avoid common field names like "Website" or "Phone" (password managers may fill them) and avoid obvious decoy phrases like "Leave blank" (sophisticated bots skip them).

h2. Form validation

Adds per-form either/or rules: at least one of the specified fields must be filled before the form can submit. Useful when you want to accept either an email address or a phone number, but require at least one.

*Form Class:* the CSS class on your @<txp:com_connect>@ tag. Example: @hero__form@.

*Either/Or Fields:* comma-separated field @name@ values. Example: @email, phone@.

Multiple rules can be added for different forms on the same site.

The submit button is disabled on page load and re-enables as soon as one of the specified fields is filled. If both captcha and either/or are active, *both* conditions must be met before the button enables.

h2. Auto markers

When enabled, the plugin automatically injects field requirement indicators into your forms:

* @*@ is appended to the label of any field with @required="1"@
* @†@ is appended to the label of any field in an either/or rule
* A legend is injected at the top of the form (after any heading) explaining what each symbol means

You do not need to add @*@ or @†@ manually to your form labels. The plugin handles it automatically.

h2. Stylesheet

On install, the plugin creates a stylesheet called @far_connect@ in *Presentation › Styles*. It contains commented-out CSS for every plugin class, with both light and dark background variants.

Edits you make in *Presentation › Styles › far_connect* are reflected immediately.

h3. Injection method

Controls how the far_connect stylesheet is loaded on pages that contain a com_connect form.

* *Automatic* (recommended): writes a @<link rel="stylesheet">@ tag directly into the form HTML on the server. Works on all themes with no template changes. Use this unless it does not work for your setup.
* *Deferred*: uses JavaScript to create the @<link>@ element dynamically after the page loads. Try this if Automatic does not load the stylesheet, for example if your server has a strict Content Security Policy that blocks link tags injected into the page body.
* *Manual*: does not load the stylesheet automatically. You control loading by adding this tag to your page template: @<txp:css name="far_connect" />@

h3. Background color detection

When enabled, the plugin detects the background color of each form using JavaScript and sets @data-far-com-theme="light"@ or @data-far-com-theme="dark"@ on the form element. All CSS rules use this attribute, so colors adapt to any theme automatically.

To override a value, add your own rule after the far_connect stylesheet:

@form[data-far-com-theme="dark"] .far-com-required { color: red; }@

h3. CSS classes

|_. Class |_. What it styles |
| @.far-com-sr-only@ | Screen reader only text. Always active, do not remove |
| @.far-com-required-legend@ | The legend at the top of the form explaining * and † |
| @.far-com-required@ | The * marker on required field labels |
| @.far-com-dagger@ | The † marker on either/or field labels |
| @.far-com-eitheror-error@ | The inline error message shown on failed submission |
| @.far-com-captcha-group@ | Wrapper div around the captcha widget |
| @[type="submit"][disabled]@ | The submit button while disabled |

h2. Accessibility

far_connect is built to WCAG 2.1 AA standards:

* Field legends are readable by screen readers
* @*@ and @†@ symbols are @aria-hidden@; meaning is conveyed programmatically instead
* Either/or fields are grouped with @role="group"@ and @aria-labelledby@
* Submit button uses both @disabled@ and @aria-disabled@ attributes
* Validation errors use @role="alert"@ and @aria-live="assertive"@
* Focus moves to the first empty field on submission failure
* Form validation table inputs use @aria-label@ for screen reader identification
* @aria-describedby@ only references visible, non-hidden elements

h2. Testing your setup

After saving your settings, submit the form on your site and check that:

# The email arrives in the recipient inbox with the correct From name and address.
# The captcha (if enabled) blocks the form until solved.
# Either/or rules (if configured) prevent submission unless at least one of the specified fields is filled.
# The form submits correctly with JavaScript disabled (captcha is skipped, honeypot remains active if enabled).

If you use Resend or Brevo, check the *Sent* log in your provider dashboard to confirm delivery. This is more reliable than checking your inbox, which may apply its own filtering.

h2. Troubleshooting

h3. Emails are not arriving

# Confirm the *From Email* domain is verified in your provider account (Resend or Brevo). Sending from an unverified domain will be silently rejected.
# Check the sent/activity log in your provider dashboard. Bounces and rejections are recorded there even when no error is shown on the form.
# Check that the *API Key* is correct and has sending permissions. Use the green ✓ / red ✗ status indicator on the settings panel to test it.
# If using SMTP, confirm *Use enhanced mail features* is enabled in Admin › Preferences › Mail and that your SMTP credentials are correct.

h3. Emails are arriving but sent by the wrong provider

far_connect processes mail through the selected provider first. If the provider call fails (wrong key, network timeout, unverified domain), it falls back silently to com_connect's own PHP @mail()@. If you are receiving emails but suspect they are going through PHP mail instead of your API provider, check the provider dashboard for any failed delivery attempts.

h3. Captcha is not appearing on the form

# Confirm the *Site Key* is entered correctly for the chosen provider.
# Confirm JavaScript is enabled in the browser. All three captcha providers require JS.
# If the captcha widget appears briefly then disappears, the widget's external script may be blocked by a Content Security Policy. Check the browser console for errors.

h3. Stylesheet is not loading

Switch to a different *Injection method* in the Stylesheet section:

* If *Automatic* is not working, try *Deferred*.
* If neither works, switch to *Manual* and add @<txp:css name="far_connect" />@ to your page template.

h3. Captcha is not blocking spam

Ensure the *Secret Key* is correct. It is separate from the site key and is not visible in the browser. An incorrect secret key causes every verification to fail silently, which means far_connect treats every submission as verified. Copy it directly from your provider dashboard.

Consider also enabling the *Honeypot spam filter*. Bots that bypass captcha (for example by submitting without JavaScript) will still be blocked by the honeypot.

h3. Spam is getting through with JavaScript disabled

Enable the *Honeypot spam filter* in the Spam Protection section. When JavaScript is off, the captcha widget never loads and its token is never submitted. The honeypot is the only server-side check in that case.

h3. The plugin stopped working after an update

Go to *Extensions › Far Connect* and click *Save* once. This refreshes the stylesheet in the database and ensures all settings are written with their correct defaults.

h2. Uninstalling

# Go to *Extensions › Far Connect*.
# Click *Reset to defaults* (beside the Save button). This clears all settings and the far_connect stylesheet from the database.
# Go to *Admin › Plugins*.
# Delete the plugin.

Do not disable the plugin before deleting it. If the plugin is disabled when deleted, the automatic uninstaller will not run. The *Reset to defaults* button works regardless of plugin state and is the recommended way to ensure a clean removal.

Alternatively, deleting the plugin while it is still *active* (enabled) will trigger the uninstaller automatically without needing to reset first.

h2. Changelog

h3. 0.1.3-beta

* Added: Honeypot spam filter. A hidden field is injected into every form; any submission with it filled is rejected before the captcha check runs. Off/On toggle in Admin under Spam Protection, enabled by default.
* Added: Honeypot field label setting. Controls the label text on the hidden honeypot input. Defaults to @Referral code@. Configurable so you can choose a label that looks realistic to bots but is not a standard autofill target for password managers.
* Added: No-JS fallback. When JavaScript is disabled, the captcha widget never loads and its token is never submitted. The plugin detects the absent token and skips captcha verification, relying on the honeypot instead. The form continues to work.
* Changed: Mail Provider default changed from SMTP to None. The plugin no longer intercepts mail delivery until a provider is explicitly chosen, so installing without configuration leaves com_connect behaviour unchanged.
* Changed: Added None option to Mail Provider. From Email and From Name fields are hidden in the admin panel when None is selected, as they have no effect without an active provider.
* Fixed: SMTP provider description clarified. com_connect uses PHP's built-in @mail()@ directly and ignores Textpattern's mail preferences. The SMTP option routes com_connect mail through TXP's mail adapter, which is the only way to enable SMTP delivery for com_connect forms.
* Changed: Captcha section renamed to Spam Protection to reflect the broader scope.
* Added: @far_connect_honeypot_label@ and @far_connect_honeypot_field_label@ Textpack strings.

h3. 0.1.2-beta

* Fixed: All CSS and JavaScript identifiers renamed from @far-@ to @far-com-@ prefix to avoid conflicts with other plugins
* Fixed: Stylesheet injection switched from @ob_start@ to @comconnect.form@ hook, which is more reliable across all TXP themes and output buffering configurations
* Fixed: Double-encoded URLs in both injection methods (@parse()@ already returns an HTML-safe URL; was being escaped a second time)
* Fixed: Protocol mismatch on HTTPS sites behind Cloudflare reverse proxy. Stylesheet URL now uses a protocol-relative @//@ scheme
* Fixed: Stale stylesheet in database not regenerating after class prefix rename. A staleness check now updates existing rows automatically
* Fixed: Theme detection broken when form does not use the default @comConnectForm@ class. Selector changed to @[name=com_connect_nonce]@, which is always present
* Fixed: Legend injected above the form heading when an @<h1>@-@<h6>@ element is present. DOM walker now skips text nodes correctly
* Fixed: Captcha widget overflow on narrow mobile screens. The @.far-com-captcha-group@ wrapper now constrains width with @overflow: hidden@; both Turnstile and hCaptcha use @data-size="normal"@
* Fixed: Duplicate ARIA label IDs on pages with more than one form. Captcha label now uses a per-instance counter
* Fixed: Captcha failure gave no user-facing message. Now calls @add_comconnect_reason()@ with a translatable error string
* Changed: Stylesheet injection method options renamed: Inline link to Automatic, JavaScript to Deferred, Disabled to Manual
* Added: @far_connect_captcha_failed@ Textpack string for the captcha error message shown on failed submission
* Added: Introduction, Testing your setup, Troubleshooting, and Fallback behaviour sections in plugin help
* Removed: 9 unused Textpack strings and two dead code items (@$sr_only@ variable, unused @$extra@ parameter)

h3. 0.1.1-beta

* Fixed: Reset to defaults confirmation dialog never appeared due to onclick handler passed to wrong @fInput()@ parameter
* Fixed: Help popup (?) sometimes hung or did nothing because the XML file was being rewritten on every admin page load. Now only written on install, enable, or upgrade
* Fixed: Admin panel layout now matches TXP native Preferences structure exactly
* Fixed: Removed custom admin CSS block; admin panel now uses TXP theme styles entirely
* Changed: Save button label changed from "Save Settings" to "Save" to match TXP conventions
* Changed: Reset to defaults button moved beside Save
* Changed: Sidebar nav heading changed to "Settings"
* Changed: Auto theme field changed from checkbox to Yes/No radio buttons
* Added: Help popup for From Name field
* Added: From Email help popup now includes domain verification example

h3. 0.1.0-beta

* Initial beta release
* Mail delivery via Resend, Brevo, and SMTP
* Captcha support: Cloudflare Turnstile, Google reCAPTCHA v3, hCaptcha
* Fixed: "Add Rule" button not working when no existing either/or rules were saved
* Fixed: Either/or rules table expanding width when a new row was added
* Fixed: Submit button incorrectly enabled when captcha widget replaced its DOM node before @DOMContentLoaded@
* Fixed: Admin panel JS not executing because inline @<script>@ relied on @DOMContentLoaded@ which had already fired
* Added: Help popups for all fields
* Added: API key security guidance in help doc and key field popups

h2. License

Released under the "GNU General Public License v2.0":https://www.gnu.org/licenses/old-licenses/gpl-2.0.html. You are free to use, modify, and distribute this plugin under the same terms.

# --- END PLUGIN HELP ---
<?php
}

# --- BEGIN PLUGIN CODE ---

if (!defined('txpinterface')) {
    die('txpinterface is not defined.');
}

// -------------------------------------------------------------------------
// Lifecycle
// -------------------------------------------------------------------------

register_callback('far_connect_install',   'plugin_lifecycle.far_connect', 'installed');
register_callback('far_connect_uninstall', 'plugin_lifecycle.far_connect', 'deleted');


function far_connect_install()
{
    $prefs = [
        'far_connect_mail_provider'        => 'none',
        'far_connect_from_email'           => '',
        'far_connect_from_name'            => get_pref('sitename', ''),
        'far_connect_resend_api_key'       => '',
        'far_connect_brevo_api_key'        => '',
        'far_connect_captcha_provider'     => 'none',
        'far_connect_turnstile_site_key'   => '',
        'far_connect_turnstile_secret_key' => '',
        'far_connect_recaptcha_site_key'   => '',
        'far_connect_recaptcha_secret_key' => '',
        'far_connect_hcaptcha_site_key'    => '',
        'far_connect_hcaptcha_secret_key'  => '',
        'far_connect_eitheror_rules'       => '[]',
        'far_connect_auto_markers'         => '0',
        'far_connect_css_inject'           => 'buffer',
        'far_connect_auto_theme'           => '1',
        'far_connect_honeypot'             => '1',
        'far_connect_honeypot_field_label' => 'Referral code',
    ];
    foreach ($prefs as $name => $default) {
        if (get_pref($name) === false) {
            set_pref($name, $default, 'far_connect', PREF_PLUGIN, 'text_input', 50);
        }
    }

    far_connect_ensure_css(true);
}

function far_connect_uninstall()
{
    safe_delete('txp_prefs', "name LIKE 'far_connect_%'");
    safe_delete('txp_css',   "name = 'far_connect'");
}

function far_connect_default_css(): string
{
    return <<<'CSS'
/* =============================================================
   far_connect plugin stylesheet
   Presentation → Styles → far_connect

   Styles are applied automatically. No editing required.

   The plugin JS detects each form's background colour and sets
   data-far-com-theme="light" or data-far-com-theme="dark" on the form
   element. All rules below use that attribute so colours adapt
   to any theme without manual configuration.

   To override a value, add your own rule with higher specificity
   after this stylesheet, e.g.:
       form[data-far-com-theme="dark"] .far-com-required { color: red; }
   ============================================================= */


/* -------------------------------------------------------------
   .far-com-sr-only
   Visually hidden text for screen readers.
   Required for accessibility. Do not remove or modify.
   ------------------------------------------------------------- */
.far-com-sr-only {
    position: absolute;
    width: 1px;
    height: 1px;
    padding: 0;
    margin: -1px;
    overflow: hidden;
    clip: rect(0, 0, 0, 0);
    white-space: nowrap;
    border: 0;
}


/* -------------------------------------------------------------
   .far-com-required-legend
   Legend shown at the top of the form explaining * and †.
   Uses black/white at reduced opacity so it reads as secondary
   text against any background colour.
   ------------------------------------------------------------- */
form[data-far-com-theme="light"] .far-com-required-legend {
    font-size: 0.875em;
    color: rgba(0, 0, 0, 0.5);
    margin-bottom: 1em;
}

form[data-far-com-theme="dark"] .far-com-required-legend {
    font-size: 0.875em;
    color: rgba(255, 255, 255, 0.6);
    margin-bottom: 1em;
}


/* -------------------------------------------------------------
   .far-com-required  /  .far-com-dagger
   The * and † markers appended to field labels.
   By default no colour is set; they inherit from the label so
   they always match the theme without any configuration.
   Uncomment a block below if you want to colour them explicitly.
   ------------------------------------------------------------- */
.far-com-required,
.far-com-dagger {
    font-weight: bold;
    margin-left: 2px;
}

/* Optional: colour * and † on light backgrounds:
form[data-far-com-theme="light"] .far-com-required { color: #cc0000; }
form[data-far-com-theme="light"] .far-com-dagger   { color: #0066cc; }
*/

/* Optional: colour * and † on dark backgrounds:
form[data-far-com-theme="dark"] .far-com-required { color: #ff6b6b; }
form[data-far-com-theme="dark"] .far-com-dagger   { color: #66aaff; }
*/


/* -------------------------------------------------------------
   .far-com-eitheror-error
   Inline validation error shown on failed submission.
   Higher opacity than the legend since this is important feedback.
   ------------------------------------------------------------- */
form[data-far-com-theme="light"] .far-com-eitheror-error {
    color: rgba(0, 0, 0, 0.75);
    border-left: 3px solid rgba(0, 0, 0, 0.25);
    padding: 0.5em 0.75em;
    margin-bottom: 1em;
    font-size: 0.9em;
}

form[data-far-com-theme="dark"] .far-com-eitheror-error {
    color: rgba(255, 255, 255, 0.85);
    border-left: 3px solid rgba(255, 255, 255, 0.35);
    padding: 0.5em 0.75em;
    margin-bottom: 1em;
    font-size: 0.9em;
}


/* -------------------------------------------------------------
   .far-com-captcha-group
   Wrapper div around the captcha widget.
   Rules are applied unconditionally (no data-far-com-theme prefix) so the
   overflow clip is in place before JS detects the background colour.
   ------------------------------------------------------------- */
.far-com-captcha-group {
    margin-top: 1em;
    margin-bottom: 1em;
    width: 100%;
    max-width: 100%;
    overflow: hidden;
    box-sizing: border-box;
}



/* -------------------------------------------------------------
   Disabled submit button
   Shown while captcha is unsolved or either/or fields are empty.
   Opacity only, no colour override, so the button keeps its
   theme colour and simply appears dimmed.
   Includes :hover and :focus so theme hover styles cannot fire.
   !important beats any theme selector regardless of specificity.
   ------------------------------------------------------------- */
form[data-far-com-theme="light"] [type="submit"][disabled],
form[data-far-com-theme="light"] [type="submit"][disabled]:hover,
form[data-far-com-theme="light"] [type="submit"][disabled]:focus,
form[data-far-com-theme="light"] [type="submit"][aria-disabled="true"],
form[data-far-com-theme="light"] [type="submit"][aria-disabled="true"]:hover,
form[data-far-com-theme="light"] [type="submit"][aria-disabled="true"]:focus {
    opacity: 0.4 !important;
    cursor: not-allowed !important;
    pointer-events: none !important;
}

form[data-far-com-theme="dark"] [type="submit"][disabled],
form[data-far-com-theme="dark"] [type="submit"][disabled]:hover,
form[data-far-com-theme="dark"] [type="submit"][disabled]:focus,
form[data-far-com-theme="dark"] [type="submit"][aria-disabled="true"],
form[data-far-com-theme="dark"] [type="submit"][aria-disabled="true"]:hover,
form[data-far-com-theme="dark"] [type="submit"][aria-disabled="true"]:focus {
    opacity: 0.3 !important;
    cursor: not-allowed !important;
    pointer-events: none !important;
}
CSS;
}

// -------------------------------------------------------------------------
// Stylesheet: create in Presentation → Styles for every installed skin
// -------------------------------------------------------------------------

/**
 * In TXP 4.7+, txp_css has a `skin` column and the Styles panel filters
 * by the active theme. We must insert a row for each skin so the stylesheet
 * appears in every theme and can be served by css.php.
 *
 * The uninstaller (safe_delete "name = 'far_connect'") removes all rows
 * regardless of skin, so no extra cleanup is needed there.
 */
function far_connect_ensure_css(bool $force = false): void
{
    $css = far_connect_default_css();

    // Collect all installed skin names from txp_skin (TXP 4.7+).
    $skins = safe_column('name', 'txp_skin', '1=1');

    // Fallback: get the skin assigned to the default section.
    if (empty($skins)) {
        $s = safe_field('skin', 'txp_sections', "name = 'default'");
        $skins = $s ? [$s] : [''];
    }

    foreach ($skins as $skin_name) {
        $where = "name = 'far_connect' AND skin = '" . doSlash($skin_name) . "'";
        $existing = safe_field('css', 'txp_css', $where);

        if ($existing === false) {
            // Row does not exist yet: insert fresh.
            safe_insert('txp_css',
                "name    = 'far_connect',"
                . " skin = '" . doSlash($skin_name) . "',"
                . " css  = '" . doSlash($css) . "'"
            );
        } elseif ($force || strpos($existing, 'far-com-') === false || strpos($existing, 'overflow: hidden') === false) {
            // Row exists but is stale: either from before the 'far-com-' rename
            // (0.1.1-beta), missing the mobile captcha overflow fix, or a
            // forced refresh was requested (install/upgrade lifecycle).
            // Update to the current stylesheet.
            safe_update('txp_css',
                "css = '" . doSlash($css) . "'",
                $where
            );
        }
    }
}

// Run on every page load: creates missing rows and migrates stale ones.
far_connect_ensure_css();

// -------------------------------------------------------------------------
// com_connect hooks
// -------------------------------------------------------------------------

// Intercept mail delivery and send via chosen provider.
register_callback('far_connect_deliver', 'comconnect.deliver');

function far_connect_deliver($event, $step, &$payload)
{
    if (get_pref('far_connect_mail_provider', 'none') === 'none') {
        return null;
    }

    $to      = $payload['to']      ?? '';
    $subject = $payload['subject'] ?? '';
    $body    = $payload['body']    ?? '';
    $headers = $payload['headers'] ?? [];

    $reply_to = !empty($headers['reply']) ? $headers['reply'] : null;

    $result = far_connect_mail_send([
        'to'       => $to,
        'subject'  => $subject,
        'text'     => $body,
        'reply_to' => $reply_to,
    ]);

    if ($result['ok']) {
        return 'comconnect.skip';
    }

    // Fall back to com_connect's PHP mail() on failure.
    return null;
}

// Register stylesheet injection based on admin setting.
// Both 'buffer' and 'js' inject via comconnect.form. The difference:
//   buffer: emits a plain <link> tag directly into the form output.
//            HTML5 allows <link rel="stylesheet"> in <body> ("body-ok" link type).
//            More reliable than ob_start because it does not depend on TXP's
//            internal output buffering order.
//   js:    dynamically creates the <link> element via JavaScript.
//            Useful if your theme has a strict Content Security Policy that
//            disallows inline <link> injections, or if you prefer late loading.
$_far_css_method = get_pref('far_connect_css_inject', 'buffer');

if ($_far_css_method === 'buffer') {
    register_callback('far_connect_styles_link', 'comconnect.form');
} elseif ($_far_css_method === 'js') {
    register_callback('far_connect_styles_js', 'comconnect.form');
}

unset($_far_css_method);

function far_connect_styles_link()
{
    static $done = false;
    if ($done) return '';
    $done = true;

    // parse() already returns an HTML-safe URL (& encoded as &amp;).
    // Strip the protocol to produce a protocol-relative URL (//example.com/...)
    // so the browser uses the same protocol as the page; works on both HTTP
    // and HTTPS sites regardless of what TXP's site URL preference is set to.
    $url = preg_replace('/^https?:/', '', trim(parse('<txp:css name="far_connect" />')));
    if (!$url) return '';

    return '<link rel="stylesheet" media="screen" href="' . $url . '">';
}

function far_connect_styles_js()
{
    static $done = false;
    if ($done) return '';
    $done = true;

    // parse() returns an HTML-safe URL; decode entities before embedding in JS
    // so the href gets a plain URL with & rather than the HTML-encoded &amp;.
    // Also strip the protocol to produce a protocol-relative URL (//example.com/...)
    // so the browser uses the same protocol as the page; avoids mixed-content
    // blocks when TXP's site URL preference uses http:// on an https:// site.
    $url = preg_replace('/^https?:/', '', html_entity_decode(trim(parse('<txp:css name="far_connect" />')), ENT_HTML5 | ENT_QUOTES, 'UTF-8'));
    if (!$url) return '';

    return '<script>(function(){'
         . 'if(document.getElementById("far-com-connect-css"))return;'
         . 'var l=document.createElement("link");'
         . 'l.id="far-com-connect-css";'
         . 'l.rel="stylesheet";'
         . 'l.media="screen";'
         . 'l.href=' . json_encode($url) . ';'
         . 'document.head.appendChild(l);'
         . '})();</script>';
}

// Detect each form's background colour and set data-far-com-theme="light|dark".
// CSS uses this attribute to apply the right colour variant automatically.
register_callback('far_connect_theme_detect', 'comconnect.form');

function far_connect_theme_detect()
{
    static $done = false;
    if ($done) return '';
    $done = true;

    if (!get_pref('far_connect_auto_theme', '1')) return '';

    return '<script>(function(){'
        // Walk up from an element to find the first ancestor with a real
        // (non-transparent) background colour. Falls back to white.
        . 'function farGetBg(el){'
        .   'while(el&&el!==document.documentElement){'
        .     'var bg=window.getComputedStyle(el).backgroundColor;'
        .     'if(bg&&bg!=="rgba(0, 0, 0, 0)"&&bg!=="transparent")return bg;'
        .     'el=el.parentElement;'
        .   '}'
        .   'return "rgb(255,255,255)";'
        . '}'
        // WCAG relative luminance: > 0.5 = light background, ≤ 0.5 = dark.
        . 'function farComIsLight(bg){'
        .   'var m=bg.match(/[\d.]+/g);'
        .   'if(!m)return true;'
        .   'return(0.299*+m[0]+0.587*+m[1]+0.114*+m[2])/255>0.5;'
        . '}'
        // Target com_connect forms by their always-present hidden nonce field.
        // This works regardless of what class= the user set on <txp:com_connect>.
        . 'function farSetTheme(){'
        .   'document.querySelectorAll("[name=com_connect_nonce]").forEach(function(el){'
        .     'var form=el.closest("form");'
        .     'if(!form)return;'
        .     'var bg=farGetBg(form.parentElement||form);'
        .     'form.setAttribute("data-far-com-theme",farComIsLight(bg)?"light":"dark");'
        .   '});'
        . '}'
        // Run immediately (elements above the form are already in the DOM)
        // and again on DOMContentLoaded in case of late-loading backgrounds.
        . 'farSetTheme();'
        . 'document.addEventListener("DOMContentLoaded",farSetTheme);'
        . '})();</script>';
}

// Inject captcha widget into the form.
register_callback('far_connect_form_widget', 'comconnect.form');

function far_connect_form_widget()
{
    return far_connect_captcha_html();
}

// Inject honeypot field into the form (always present in HTML when enabled; no JS involvement).
register_callback('far_connect_honeypot_field', 'comconnect.form');

function far_connect_honeypot_field()
{
    if (!get_pref('far_connect_honeypot', '0')) {
        return '';
    }

    // Positioned off-screen so real users never see or reach it.
    // Bots read the raw HTML and fill every input they find.
    // aria-hidden hides it from screen readers; tabindex="-1" removes it from
    // keyboard navigation so real users cannot accidentally tab into it.
    // Inline style is intentional: a honeypot must never depend on a stylesheet
    // to stay hidden. If CSS fails to load, the field would become visible to
    // real users. The inline style guarantees it is always off-screen.
    $decoy_label = get_pref('far_connect_honeypot_field_label', 'Referral code');

    return tag(
        tag(txpspecialchars($decoy_label), 'label', array('for' => 'far-hp')) .
        tag_void('input', array(
            'type'         => 'text',
            'id'           => 'far-hp',
            'name'         => 'far_hp',
            'value'        => '',
            'tabindex'     => '-1',
            'autocomplete' => 'off',
        )),
        'div', array(
            'style'       => 'position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden',
            'aria-hidden' => 'true',
        )
    );
}


// Inject field marker script (* and †) into the form.
register_callback('far_connect_markers_script', 'comconnect.form');

function far_connect_markers_script()
{
    if (!get_pref('far_connect_auto_markers', '0')) {
        return '';
    }

    $has_eitheror = !empty(json_decode(get_pref('far_connect_eitheror_rules', '[]'), true));

    // Visual legend for sighted users.
    // NOT aria-hidden: WCAG requires the legend to be readable by AT too (H90).
    // Screen readers will read it as they navigate past it.
    // Symbols (* †) are kept because screen readers announce them as "asterisk" / "dagger"
    // which, combined with the legend text, is intelligible.
    $legend_text = $has_eitheror
        ? gTxt('far_connect_eitheror_legend')  // "* Required · † At least one of these is required"
        : gTxt('far_connect_required_legend'); // "* Required"

    // When no either/or rule exists the either/or script won't run,
    // so this script also injects the legend before the first required field.
    return '<script>(function(){'
         . 'var farRequiredLegend=' . json_encode($legend_text) . ';'
         . 'var farHasEitherOr=' . ($has_eitheror ? 'true' : 'false') . ';'
         . 'document.addEventListener("DOMContentLoaded",function(){'
         // Add * to every required field label (aria-hidden: the required attribute
         // already causes AT to announce "required"; the * is purely visual).
         .   'document.querySelectorAll("form [required]").forEach(function(el){'
         .     'var lbl=null;'
         .     'if(el.id)lbl=document.querySelector("label[for=\\""+el.id+"\\"]");'
         .     'if(!lbl)lbl=el.closest("label");'
         .     'if(lbl&&!lbl.querySelector(".far-com-required")){'
         .       'var s=document.createElement("span");'
         .       's.className="far-com-required";'
         .       's.setAttribute("aria-hidden","true");'
         .       's.textContent=" *";'
         .       'lbl.appendChild(s);'
         .     '}'
         .   '});'
         // When no either/or rule is active, inject the "* Required" legend
         // after any <legend>, hidden <input>, and <h1>-<h6> elements so it
         // appears below the form heading, not above it.
         .   'if(!farHasEitherOr){'
         .     'document.querySelectorAll("form").forEach(function(form){'
         .       'if(!form.querySelector("[required]")||form.querySelector(".far-com-required-legend"))return;'
         .       'var container=form.querySelector("fieldset")||form;'
         .       'var leg=document.createElement("p");'
         .       'leg.className="far-com-required-legend";'
         .       'leg.textContent=farRequiredLegend;'
         .       'var anchor=null,child=container.firstChild;'
         .       'while(child){'
         .         'if(child.nodeType!==1){child=child.nextSibling;continue;}'
         .         'var t=child.tagName.toUpperCase();'
         .         'if(t==="LEGEND"||(t==="INPUT"&&child.type==="hidden")||/^H[1-6]$/.test(t)){'
         .           'anchor=child.nextSibling;child=child.nextSibling;continue;'
         .         '}'
         .         'break;'
         .       '}'
         .       'container.insertBefore(leg,anchor);'
         .     '});'
         .   '}'
         . '});'
         . '})();</script>';
}

// Inject either/or validation script into the form.
register_callback('far_connect_eitheror_script', 'comconnect.form');

function far_connect_eitheror_script()
{
    $rules = json_decode(get_pref('far_connect_eitheror_rules', '[]'), true);
    if (empty($rules)) {
        return '';
    }

    $js_rules = [];
    foreach ($rules as $rule) {
        $cls    = trim($rule['class'] ?? '');
        $fields = array_values(array_filter(array_map('trim', explode(',', $rule['fields'] ?? ''))));
        if ($cls && count($fields) >= 2) {
            $js_rules[] = [
                'cls'    => $cls,
                'fields' => $fields,
                'msg'    => gTxt('far_connect_eitheror_msg', ['{fields}' => implode(', ', $fields)]),
                'legend' => gTxt('far_connect_eitheror_legend'),
            ];
        }
    }

    if (empty($js_rules)) {
        return '';
    }

    return '<script>(function(){'
         . 'document.addEventListener("DOMContentLoaded",function(){'
         .   'var rules=' . json_encode($js_rules) . ';'
         .   'rules.forEach(function(r){'
         .     'var form=document.querySelector("form."+r.cls);'
         .     'if(!form)return;'
         .     'var slug=r.cls.replace(/[^a-z0-9]/gi,"-");'

         // ------------------------------------------------------------------
         // 1. Collect the either/or field elements and their containers.
         // ------------------------------------------------------------------
         .     'var fieldEls=[];'
         .     'var containers=[];'
         .     'r.fields.forEach(function(n){'
         .       'var el=form.querySelector("[name=\\""+n+"\\"]");'
         .       'if(el){fieldEls.push(el);containers.push(el.closest("p,div,fieldset")||el);}'
         .     '});'
         .     'if(!fieldEls.length)return;'
         .     'var anchor=containers[0];'

         // ------------------------------------------------------------------
         // 2. Wrap the either/or containers in role="group" + aria-labelledby.
         //    This is the primary AT grouping mechanism (WCAG H71 / ARIA).
         //    We wrap only if not already wrapped.
         // ------------------------------------------------------------------
         .     'var groupId="far-com-group-"+slug;'
         .     'if(!form.querySelector("#"+groupId)){'
         .       'var group=document.createElement("div");'
         .       'group.id=groupId;'
         .       'group.setAttribute("role","group");'
         .       'group.setAttribute("aria-labelledby","far-com-grouplbl-"+slug);'
         // Insert the group before the first container, then move all containers into it.
         .       'anchor.parentNode.insertBefore(group,anchor);'
         .       'containers.forEach(function(c){group.appendChild(c);});'
         .     '}'

         // ------------------------------------------------------------------
         // 3. Group label: visible, readable by AT (no aria-hidden).
         //    Screen readers announce it when entering the group.
         //    "* Required · † At least one of these is required"
         // ------------------------------------------------------------------
         .     'if(!form.querySelector("#far-com-grouplbl-"+slug)){'
         .       'var groupLbl=document.createElement("p");'
         .       'groupLbl.id="far-com-grouplbl-"+slug;'
         .       'groupLbl.className="far-com-eitheror-legend far-com-required-legend";'
         .       'groupLbl.textContent=r.legend;' // from textpack; readable by AT
         // Insert after any <legend>, hidden <input>, and <h1>-<h6> so the
         // legend appears below the form heading, not above it.
         .       'var fsContainer=form.querySelector("fieldset")||form;'
         .       'var fsAnchor=null,fsChild=fsContainer.firstChild;'
         .       'while(fsChild){'
         .         'if(fsChild.nodeType!==1){fsChild=fsChild.nextSibling;continue;}'
         .         'var ft=fsChild.tagName.toUpperCase();'
         .         'if(ft==="LEGEND"||(ft==="INPUT"&&fsChild.type==="hidden")||/^H[1-6]$/.test(ft)){'
         .           'fsAnchor=fsChild.nextSibling;fsChild=fsChild.nextSibling;continue;'
         .         '}'
         .         'break;'
         .       '}'
         .       'fsContainer.insertBefore(groupLbl,fsAnchor);'
         .     '}'

         // ------------------------------------------------------------------
         // 4. Live error region: inserted before the group, hidden until needed.
         // ------------------------------------------------------------------
         .     'var errId="far-com-err-"+slug;'
         .     'var errEl=document.createElement("div");'
         .     'errEl.id=errId;'
         .     'errEl.setAttribute("role","alert");'
         .     'errEl.setAttribute("aria-live","assertive");'
         .     'errEl.setAttribute("aria-atomic","true");'
         .     'errEl.className="far-com-eitheror-error";'
         .     'errEl.hidden=true;'
         .     'var grpEl=form.querySelector("#"+groupId);'
         .     'grpEl.parentNode.insertBefore(errEl,grpEl);'

         // ------------------------------------------------------------------
         // 5. Add † to each either/or field label (aria-hidden; group label
         //    and aria-describedby cover the AT explanation).
         //    Also link each field to the error region via aria-describedby.
         // ------------------------------------------------------------------
         .     'fieldEls.forEach(function(el){'
         .       'var lbl=null;'
         .       'if(el.id)lbl=form.querySelector("label[for=\\""+el.id+"\\"]");'
         .       'if(!lbl)lbl=el.closest("label");'
         .       'if(lbl&&!lbl.querySelector(".far-com-dagger")){'
         .         'var d=document.createElement("span");'
         .         'd.className="far-com-dagger";'
         .         'd.setAttribute("aria-hidden","true");'
         .         'd.textContent=" †";'
         .         'lbl.appendChild(d);'
         .       '}'
         // aria-describedby → error region only (legend is handled by role=group).
         // WCAG: aria-describedby must NOT point to aria-hidden elements.
         .       'var cur=el.getAttribute("aria-describedby")||"";'
         .       'if(cur.indexOf(errId)===-1){'
         .         'el.setAttribute("aria-describedby",(cur+" "+errId).trim());'
         .       '}'
         .     '});'
         // Register this form in the either/or gate state and disable submit.
         .     'window.farEitherOrState=window.farEitherOrState||{};'
         .     'window.farEitherOrState[form.className]=false;'
         .     'function farCheckFilled(){'
         .       'var filled=r.fields.some(function(n){'
         .         'var el=form.querySelector("[name=\\""+n+"\\"]");'
         .         'return el&&el.value.trim()!=="";'
         .       '});'
         .       'window.farEitherOrState[form.className]=filled;'
         // Call unified gate: decides whether to enable based on ALL conditions.
         .       'if(window.farGate)farGate(form);'
         // Fallback: if no captcha script loaded, manage button directly.
         .       'else{'
         .         'var btn=form.querySelector("[type=submit]");'
         .         'if(btn){btn.disabled=!filled;btn.setAttribute("aria-disabled",filled?"false":"true");}'
         .       '}'
         .       'return filled;'
         .     '}'
         .     'farCheckFilled();'
         // Re-check whenever the user types in any of the either/or fields.
         .     'r.fields.forEach(function(n){'
         .       'var el=form.querySelector("[name=\\""+n+"\\"]");'
         .       'if(el)el.addEventListener("input",farCheckFilled);'
         .     '});'
         // Validate on submit (belt-and-suspenders in case JS gate was bypassed).
         .     'form.addEventListener("submit",function(e){'
         .       'var filled=farCheckFilled();'
         .       'if(!filled){'
         .         'e.preventDefault();'
         .         'errEl.textContent=r.msg;'
         .         'errEl.hidden=false;'
         // Focus the first empty field so keyboard/screen reader users land on it.
         .         'var firstEmpty=null;'
         .         'r.fields.forEach(function(n){'
         .           'if(!firstEmpty){'
         .             'var el=form.querySelector("[name=\\""+n+"\\"]");'
         .             'if(el&&!el.value.trim())firstEmpty=el;'
         .           '}'
         .         '});'
         .         'if(firstEmpty)firstEmpty.focus();'
         .       '}else{'
         .         'errEl.textContent="";'
         .         'errEl.hidden=true;'
         .       '}'
         .     '});'
         .   '});'
         . '});'
         . '})();</script>';
}

// Verify captcha token on submission.
register_callback('far_connect_submit', 'comconnect.submit');

function far_connect_submit()
{
    // Honeypot: reject silently if the hidden field was filled. Real users
    // never see or reach it; bots fill every input they find in the HTML.
    if (get_pref('far_connect_honeypot', '0') === '1' && ps('far_hp') !== '') {
        $evaluator =& get_comconnect_evaluator();
        $evaluator->add_comconnect_status(1);
        return;
    }

    $provider = get_pref('far_connect_captcha_provider', 'none');
    if ($provider === 'none') {
        return;
    }

    // If no token was submitted, JavaScript was off when the form was rendered.
    // The captcha widget never loaded, so skip verification and rely on the
    // honeypot alone. The form continues to work for no-JS users.
    $has_token = match($provider) {
        'turnstile' => ps('cf-turnstile-response') !== '',
        'recaptcha' => ps('far_connect_recaptcha_token') !== '',
        'hcaptcha'  => ps('h-captcha-response') !== '',
        default     => false,
    };

    if (!$has_token) {
        return;
    }

    $verified = far_connect_captcha_verify();
    if (!$verified) {
        $evaluator =& get_comconnect_evaluator();
        $evaluator->add_comconnect_status(1);
        $evaluator->add_comconnect_reason(gTxt('far_connect_captcha_failed'));
    }
}

// -------------------------------------------------------------------------
// Mail: dispatch
// -------------------------------------------------------------------------

function far_connect_mail_send(array $p): array
{
    $provider = get_pref('far_connect_mail_provider', 'none');

    switch ($provider) {
        case 'resend': return far_connect_send_resend($p);
        case 'brevo':  return far_connect_send_brevo($p);
        case 'smtp':   return far_connect_send_smtp($p);
        default:       return ['ok' => false, 'error' => 'No mail provider configured.'];
    }
}

// -------------------------------------------------------------------------
// Mail: Resend
// -------------------------------------------------------------------------

function far_connect_send_resend(array $p): array
{
    if (!function_exists('curl_init')) {
        return ['ok' => false, 'error' => 'PHP cURL extension is required.'];
    }

    $api_key    = get_pref('far_connect_resend_api_key', '');
    $from_email = get_pref('far_connect_from_email', '');
    $from_name  = get_pref('far_connect_from_name', '');

    if (empty($api_key)) {
        return ['ok' => false, 'error' => gTxt('far_connect_not_configured_key')];
    }
    if (empty($from_email)) {
        return ['ok' => false, 'error' => gTxt('far_connect_not_configured_email')];
    }

    $from = $from_name ? "{$from_name} <{$from_email}>" : $from_email;

    $body = array_filter([
        'from'     => $from,
        'to'       => (array) $p['to'],
        'subject'  => $p['subject'] ?? '',
        'text'     => $p['text']    ?? null,
        'html'     => $p['html']    ?? null,
        'reply_to' => !empty($p['reply_to']) ? (array) $p['reply_to'] : null,
    ], fn($v) => $v !== null);

    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($body),
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . $api_key,
            'Content-Type: application/json',
        ],
        CURLOPT_TIMEOUT => 15,
    ]);

    $response = curl_exec($ch);
    $http     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err      = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return ['ok' => false, 'error' => "cURL error: {$err}"];
    }

    $data = json_decode($response, true);

    if ($http === 200 || $http === 201) {
        return ['ok' => true, 'id' => $data['id'] ?? null, 'error' => null];
    }

    return ['ok' => false, 'error' => $data['message'] ?? $data['name'] ?? "HTTP {$http}"];
}

function far_connect_test_resend(string $api_key): array
{
    $ch = curl_init('https://api.resend.com/domains');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['Authorization: Bearer ' . $api_key],
        CURLOPT_TIMEOUT        => 10,
    ]);
    $response = curl_exec($ch);
    $http     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err      = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return ['ok' => false, 'error' => "cURL error: {$err}"];
    }
    if ($http === 200) {
        return ['ok' => true];
    }
    $data = json_decode($response, true);
    return ['ok' => false, 'error' => $data['message'] ?? "HTTP {$http}"];
}

// -------------------------------------------------------------------------
// Mail: Brevo
// -------------------------------------------------------------------------

function far_connect_send_brevo(array $p): array
{
    if (!function_exists('curl_init')) {
        return ['ok' => false, 'error' => 'PHP cURL extension is required.'];
    }

    $api_key    = get_pref('far_connect_brevo_api_key', '');
    $from_email = get_pref('far_connect_from_email', '');
    $from_name  = get_pref('far_connect_from_name', '');

    if (empty($api_key)) {
        return ['ok' => false, 'error' => gTxt('far_connect_not_configured_key')];
    }
    if (empty($from_email)) {
        return ['ok' => false, 'error' => gTxt('far_connect_not_configured_email')];
    }

    // Build 'to' as array of objects required by Brevo.
    $to_list = [];
    foreach ((array) $p['to'] as $addr) {
        $to_list[] = ['email' => trim($addr)];
    }

    $body = array_filter([
        'sender'       => ['name' => $from_name, 'email' => $from_email],
        'to'           => $to_list,
        'subject'      => $p['subject']      ?? '',
        'textContent'  => $p['text']         ?? null,
        'htmlContent'  => $p['html']         ?? null,
        'replyTo'      => !empty($p['reply_to'])
                            ? ['email' => is_array($p['reply_to']) ? reset($p['reply_to']) : $p['reply_to']]
                            : null,
    ], fn($v) => $v !== null);

    $ch = curl_init('https://api.brevo.com/v3/smtp/email');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => json_encode($body),
        CURLOPT_HTTPHEADER     => [
            'api-key: ' . $api_key,
            'Content-Type: application/json',
        ],
        CURLOPT_TIMEOUT => 15,
    ]);

    $response = curl_exec($ch);
    $http     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err      = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return ['ok' => false, 'error' => "cURL error: {$err}"];
    }

    $data = json_decode($response, true);

    if ($http === 201) {
        return ['ok' => true, 'id' => $data['messageId'] ?? null, 'error' => null];
    }

    return ['ok' => false, 'error' => $data['message'] ?? "HTTP {$http}"];
}

function far_connect_test_brevo(string $api_key): array
{
    $ch = curl_init('https://api.brevo.com/v3/account');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_HTTPHEADER     => ['api-key: ' . $api_key],
        CURLOPT_TIMEOUT        => 10,
    ]);
    $response = curl_exec($ch);
    $http     = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err      = curl_error($ch);
    curl_close($ch);

    if ($err) {
        return ['ok' => false, 'error' => "cURL error: {$err}"];
    }
    if ($http === 200) {
        return ['ok' => true];
    }
    $data = json_decode($response, true);
    return ['ok' => false, 'error' => $data['message'] ?? "HTTP {$http}"];
}

// -------------------------------------------------------------------------
// Mail: SMTP (via TXP's built-in mail adapter)
// -------------------------------------------------------------------------

function far_connect_send_smtp(array $p): array
{
    $from_email = get_pref('far_connect_from_email', '');
    $from_name  = get_pref('far_connect_from_name', '');

    $to       = is_array($p['to']) ? reset($p['to']) : $p['to'];
    $subject  = $p['subject'] ?? '';
    $body     = $p['text']    ?? $p['html'] ?? '';
    $reply_to = !empty($p['reply_to'])
                    ? (is_array($p['reply_to']) ? reset($p['reply_to']) : $p['reply_to'])
                    : null;

    // Build $from for txpMail: [email, name] or just email string.
    $from = $from_email
        ? ($from_name ? [$from_email, $from_name] : $from_email)
        : null;

    // txpMail() respects TXP's mail settings (Admin › Preferences › Mail)
    // including PHPMailer/SMTP when enhanced mail is enabled.
    $result = txpMail($to, $subject, $body, $reply_to, $from);

    return $result !== false
        ? ['ok' => true,  'error' => null]
        : ['ok' => false, 'error' => gTxt('far_connect_smtp_error')];
}

// -------------------------------------------------------------------------
// Captcha: HTML widget
// -------------------------------------------------------------------------

function far_connect_captcha_html(): string
{
    $provider = get_pref('far_connect_captcha_provider', 'none');

    // Unique label ID per invocation so multiple forms on the same page
    // each get a distinct id="far-com-captcha-label-N", avoiding duplicate IDs.
    static $instance = 0;
    $instance++;
    $label_id = 'far-com-captcha-label-' . $instance;

    // Shared JS: unified gate. Button enabled only when captcha solved AND either/or filled (if applicable).
    // farCaptchaState / farEitherOrState are keyed by form.className so multiple forms work independently.
    //
    // Capture the parent form via document.currentScript immediately (synchronous, no timing issues).
    // This avoids the race where Turnstile/hCaptcha's async script replaces the widget div before
    // DOMContentLoaded fires, making querySelector(".cf-turnstile") return null and leaving
    // farCaptchaState[key] as undefined (which !== false → treated as "solved").
    $disable_js = '<script>(function(){'
        // Grab the form that contains this script tag right now, before any async code runs.
        . 'var farForm=document.currentScript?document.currentScript.closest("form"):null;'
        . 'window.farCaptchaState=window.farCaptchaState||{};'
        . 'window.farEitherOrState=window.farEitherOrState||{};'
        // Register captcha as unsolved immediately, before DOMContentLoaded and before the widget renders.
        // !==false: undefined (not registered) = passes; false (registered+unsolved) = blocks; true = passes.
        . 'if(farForm)window.farCaptchaState[farForm.className]=false;'
        . 'window.farGate=function(form){'
        .   'if(!form)return;'
        .   'var key=form.className;'
        .   'var captchaOk=window.farCaptchaState[key]!==false;'
        .   'var eitherOrOk=window.farEitherOrState[key]!==false;'
        .   'var on=captchaOk&&eitherOrOk;'
        .   'form.querySelectorAll("[type=submit]").forEach(function(b){'
        .     'b.disabled=!on;'
        .     'b.setAttribute("aria-disabled",on?"false":"true");'
        .   '});'
        . '};'
        // Disable the submit button once the DOM is ready.
        . 'document.addEventListener("DOMContentLoaded",function(){'
        .   'if(farForm)farGate(farForm);'
        . '});'
        // Captcha solved. Called by the widget's data-callback.
        . 'window.farCaptchaOk=function(){'
        .   'if(!farForm)return;'
        .   'window.farCaptchaState[farForm.className]=true;'
        .   'farGate(farForm);'
        . '};'
        // Captcha expired. Re-lock.
        . 'window.farCaptchaExpired=function(){'
        .   'if(!farForm)return;'
        .   'window.farCaptchaState[farForm.className]=false;'
        .   'farGate(farForm);'
        . '};'
        . '})();</script>';

    switch ($provider) {
        case 'turnstile':
            $site_key = get_pref('far_connect_turnstile_site_key', '');
            return $disable_js
                 . '<script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>'
                 . '<div role="group" aria-labelledby="' . $label_id . '" class="far-com-captcha-group">'
                 .   '<span id="' . $label_id . '" class="far-com-sr-only">' . gTxt('far_connect_captcha_label') . '</span>'
                 .   '<div class="cf-turnstile"'
                 .     ' data-sitekey="' . txpspecialchars($site_key) . '"'
                 .     ' data-size="normal"'
                 .     ' data-callback="farCaptchaOk"'
                 .     ' data-expired-callback="farCaptchaExpired">'
                 .   '</div>'
                 . '</div>';

        case 'recaptcha':
            // reCAPTCHA v3 is invisible. Token is set silently; no button disabling needed.
            $site_key = get_pref('far_connect_recaptcha_site_key', '');
            return '<script src="https://www.google.com/recaptcha/api.js?render=' . txpspecialchars($site_key) . '"></script>'
                 . '<input type="hidden" id="far_connect_recaptcha_token" name="far_connect_recaptcha_token"'
                 .   ' aria-hidden="true">'
                 . '<script>grecaptcha.ready(function(){'
                 .   'grecaptcha.execute(' . json_encode($site_key) . ',{action:"contact"})'
                 .   '.then(function(token){document.getElementById("far_connect_recaptcha_token").value=token;});'
                 . '});</script>';

        case 'hcaptcha':
            $site_key = get_pref('far_connect_hcaptcha_site_key', '');
            return $disable_js
                 . '<script src="https://js.hcaptcha.com/1/api.js" async defer></script>'
                 . '<div role="group" aria-labelledby="' . $label_id . '" class="far-com-captcha-group">'
                 .   '<span id="' . $label_id . '" class="far-com-sr-only">' . gTxt('far_connect_captcha_label') . '</span>'
                 .   '<div class="h-captcha"'
                 .     ' data-sitekey="' . txpspecialchars($site_key) . '"'
                 .     ' data-size="normal"'
                 .     ' data-callback="farCaptchaOk"'
                 .     ' data-expired-callback="farCaptchaExpired">'
                 .   '</div>'
                 . '</div>';

        default:
            return '';
    }
}

// -------------------------------------------------------------------------
// Captcha: verification
// -------------------------------------------------------------------------

function far_connect_captcha_verify(): bool
{
    $provider = get_pref('far_connect_captcha_provider', 'none');

    switch ($provider) {
        case 'turnstile':
            return far_connect_verify_turnstile(ps('cf-turnstile-response'));

        case 'recaptcha':
            return far_connect_verify_recaptcha(ps('far_connect_recaptcha_token'));

        case 'hcaptcha':
            return far_connect_verify_hcaptcha(ps('h-captcha-response'));

        default:
            return true;
    }
}

function far_connect_verify_turnstile(string $token): bool
{
    $secret = get_pref('far_connect_turnstile_secret_key', '');
    if (empty($token) || empty($secret)) {
        return false;
    }
    $data = far_connect_post_verify('https://challenges.cloudflare.com/turnstile/v0/siteverify', [
        'secret'   => $secret,
        'response' => $token,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
    ]);
    return !empty($data['success']);
}

function far_connect_verify_recaptcha(string $token): bool
{
    $secret = get_pref('far_connect_recaptcha_secret_key', '');
    if (empty($token) || empty($secret)) {
        return false;
    }
    $data = far_connect_post_verify('https://www.google.com/recaptcha/api/siteverify', [
        'secret'   => $secret,
        'response' => $token,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
    ]);
    // reCAPTCHA v3 is score-based; 0.5 is the standard threshold.
    return !empty($data['success']) && ($data['score'] ?? 0) >= 0.5;
}

function far_connect_verify_hcaptcha(string $token): bool
{
    $secret = get_pref('far_connect_hcaptcha_secret_key', '');
    if (empty($token) || empty($secret)) {
        return false;
    }
    $data = far_connect_post_verify('https://hcaptcha.com/siteverify', [
        'secret'   => $secret,
        'response' => $token,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
    ]);
    return !empty($data['success']);
}

function far_connect_post_verify(string $url, array $fields): array
{
    $ch = curl_init($url);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => http_build_query($fields),
        CURLOPT_TIMEOUT        => 10,
    ]);
    $response = curl_exec($ch);
    curl_close($ch);
    return json_decode($response, true) ?? [];
}

// -------------------------------------------------------------------------
// Admin panel
// -------------------------------------------------------------------------

if (txpinterface === 'admin') {
    add_privs('tab.extensions', '1,2');
    add_privs('far_connect', '1,2');
    register_tab('extensions', 'far_connect', gTxt('far_connect_settings_title', null, 'Far Connect'));
    register_callback('far_connect_panel', 'far_connect');
    register_callback('far_connect_lifecycle', 'plugin_lifecycle.far_connect');
}

function far_connect_lifecycle($event, $step)
{
    if (in_array($step, array('enabled', 'installed', 'upgraded'))) {
        far_connect_install_pophelp();
        far_connect_ensure_css(true);
    } elseif ($step === 'deleted') {
        far_connect_remove_pophelp();
    }
}

function far_connect_install_pophelp()
{
    $dir = PLUGINPATH . DS . 'far_connect' . DS . 'lang';

    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    $xml = '<?xml version="1.0" encoding="utf-8"?>' . n
        . '<resources>' . n
        . '<help lang="en">' . n
        . '    <group id="far_connect" title="Far Connect">' . n

        . '        <item id="far_connect_mail_provider" title="Mail Provider"><![CDATA[' . n
        . '<h2>Mail Provider</h2>' . n
        . '<p><strong>None</strong> disables far_connect mail delivery. com_connect falls back to PHP\'s built-in <code>mail()</code> directly, bypassing any SMTP settings in Textpattern. Use this if you only need the spam protection features and PHP mail is acceptable.</p>' . n
        . '<p><strong>SMTP</strong> routes com_connect mail through Textpattern\'s mail adapter (<a href="index.php?event=prefs#prefs_group_mail">Admin &rsaquo; Preferences &rsaquo; Mail</a>), enabling SMTP delivery via PHPMailer. Without this option active, com_connect bypasses Textpattern\'s SMTP settings entirely and sends via PHP\'s built-in <code>mail()</code>.</p>' . n
        . '<p><strong>Resend</strong> is a transactional email API. Requires an API key and a verified sending domain at <a href="https://resend.com" target="_blank">resend.com</a>.</p>' . n
        . '<p><strong>Brevo</strong> is a transactional email API. Requires an API key and a verified sending domain at <a href="https://brevo.com" target="_blank">brevo.com</a>.</p>' . n
        . ']]></item>' . n

        . '        <item id="far_connect_from_email" title="From Email"><![CDATA[' . n
        . '<h2>From Email</h2>' . n
        . '<p>The email address recipients see in the <strong>From</strong> field of every message sent through this plugin.</p>' . n
        . '<p>The domain part (after the @) must be verified in your mail provider account. For example, if you use <code>noreply@example.com</code>, you must have verified <code>example.com</code> with your provider before sending will work.</p>' . n
        . ']]></item>' . n

        . '        <item id="far_connect_from_name" title="From Name"><![CDATA[' . n
        . '<h2>From Name</h2>' . n
        . '<p>The display name recipients see alongside the From Email address, for example <em>Footprint Shoes</em>.</p>' . n
        . '<p>If left blank, only the email address is shown.</p>' . n
        . ']]></item>' . n

        . '        <item id="far_connect_resend_api_key" title="Resend API Key"><![CDATA[' . n
        . '<h2>Resend API Key</h2>' . n
        . '<p>Found in your <a href="https://resend.com/api-keys" target="_blank">Resend dashboard</a> under API Keys.</p>' . n
        . '<p><strong>Use a restricted key</strong> scoped to <em>Sending access</em> only, not full access. This way, if the key is ever exposed, an attacker cannot access your account settings or billing.</p>' . n
        . '<p>API keys are stored in the Textpattern database without encryption. If you suspect a key has been compromised, revoke it immediately from the Resend dashboard and generate a new one.</p>' . n
        . ']]></item>' . n

        . '        <item id="far_connect_brevo_api_key" title="Brevo API Key"><![CDATA[' . n
        . '<h2>Brevo API Key</h2>' . n
        . '<p>Found in your <a href="https://app.brevo.com/settings/keys/api" target="_blank">Brevo dashboard</a> under Settings &rsaquo; API Keys.</p>' . n
        . '<p><strong>Use a restricted key</strong> with only the permissions needed for sending transactional email. This limits what an attacker could do if the key were ever exposed.</p>' . n
        . '<p>API keys are stored in the Textpattern database without encryption. If you suspect a key has been compromised, revoke it immediately from the Brevo dashboard and generate a new one.</p>' . n
        . ']]></item>' . n

        . '        <item id="far_connect_captcha_provider" title="Captcha Provider"><![CDATA[' . n
        . '<h2>Captcha Provider</h2>' . n
        . '<p><strong>Cloudflare Turnstile</strong> is privacy-friendly and usually invisible. No user puzzle to solve. Get keys at <a href="https://dash.cloudflare.com/?to=/:account/turnstile" target="_blank">dash.cloudflare.com</a>.</p>' . n
        . '<p><strong>Google reCAPTCHA v3</strong> is fully invisible and scores traffic in the background. Get keys at <a href="https://www.google.com/recaptcha/admin" target="_blank">google.com/recaptcha</a>.</p>' . n
        . '<p><strong>hCaptcha</strong> is a privacy-focused alternative to reCAPTCHA. Get keys at <a href="https://dashboard.hcaptcha.com" target="_blank">dashboard.hcaptcha.com</a>.</p>' . n
        . '<p><strong>None</strong> disables captcha entirely. Not recommended for public-facing forms.</p>' . n
        . ']]></item>' . n

        . '        <item id="far_connect_turnstile_site_key" title="Turnstile Site Key"><![CDATA[' . n
        . '<h2>Turnstile Site Key</h2>' . n
        . '<p>The public key embedded in your page. Found in your <a href="https://dash.cloudflare.com/?to=/:account/turnstile" target="_blank">Cloudflare Turnstile dashboard</a> under the widget you created for this site.</p>' . n
        . '<p>The site key is safe to expose publicly. It identifies your widget but cannot be used to verify tokens.</p>' . n
        . ']]></item>' . n

        . '        <item id="far_connect_turnstile_secret_key" title="Turnstile Secret Key"><![CDATA[' . n
        . '<h2>Turnstile Secret Key</h2>' . n
        . '<p>The private key used server-side to verify that a captcha token is genuine. Keep this secret. Never paste it into templates or expose it publicly.</p>' . n
        . '<p>Found in the same <a href="https://dash.cloudflare.com/?to=/:account/turnstile" target="_blank">Cloudflare Turnstile dashboard</a> as the site key.</p>' . n
        . ']]></item>' . n

        . '        <item id="far_connect_recaptcha_site_key" title="reCAPTCHA Site Key"><![CDATA[' . n
        . '<h2>reCAPTCHA Site Key</h2>' . n
        . '<p>The public key embedded in your page. Found in the <a href="https://www.google.com/recaptcha/admin" target="_blank">reCAPTCHA admin console</a> after registering your site with v3.</p>' . n
        . '<p>Safe to expose publicly. It identifies your site but cannot verify tokens on its own.</p>' . n
        . ']]></item>' . n

        . '        <item id="far_connect_recaptcha_secret_key" title="reCAPTCHA Secret Key"><![CDATA[' . n
        . '<h2>reCAPTCHA Secret Key</h2>' . n
        . '<p>The private key used server-side to verify tokens. Keep this secret. Never expose it in templates or client-side code.</p>' . n
        . '<p>Found alongside the site key in the <a href="https://www.google.com/recaptcha/admin" target="_blank">reCAPTCHA admin console</a>.</p>' . n
        . ']]></item>' . n

        . '        <item id="far_connect_hcaptcha_site_key" title="hCaptcha Site Key"><![CDATA[' . n
        . '<h2>hCaptcha Site Key</h2>' . n
        . '<p>The public key embedded in your page. Found in your <a href="https://dashboard.hcaptcha.com" target="_blank">hCaptcha dashboard</a> under Sites.</p>' . n
        . '<p>Safe to expose publicly.</p>' . n
        . ']]></item>' . n

        . '        <item id="far_connect_hcaptcha_secret_key" title="hCaptcha Secret Key"><![CDATA[' . n
        . '<h2>hCaptcha Secret Key</h2>' . n
        . '<p>The private key used server-side to verify tokens. Keep this secret. Never expose it in templates or client-side code.</p>' . n
        . '<p>Found in your <a href="https://dashboard.hcaptcha.com" target="_blank">hCaptcha dashboard</a> under Settings.</p>' . n
        . ']]></item>' . n

        . '        <item id="far_connect_honeypot" title="Honeypot spam filter"><![CDATA[' . n
        . '<h2>Honeypot spam filter</h2>' . n
        . '<p>Adds a hidden field to every form that is invisible to real users but filled automatically by spam bots. Any submission with the field filled is silently rejected before any captcha check runs.</p>' . n
        . '<p>The honeypot works without JavaScript and requires no provider account. When JavaScript is disabled in the browser, the captcha widget never loads; the honeypot is the only server-side protection in that case.</p>' . n
        . '<p>Using both together gives layered protection: the captcha blocks JavaScript-enabled bots, and the honeypot blocks no-JS bots that never trigger the captcha.</p>' . n
        . ']]></item>' . n

        . '        <item id="far_connect_honeypot_field_label" title="Honeypot field label"><![CDATA[' . n
        . '<h2>Honeypot field label</h2>' . n
        . '<p>The label text on the hidden honeypot input. This text appears in the page HTML but is never visible to real users.</p>' . n
        . '<p>Choose a label that looks like a real form field so spam bots will fill it, but that no password manager or browser autofill tool would recognise. Avoid generic phrases like "Leave blank" or "Do not fill" as sophisticated bots are programmed to skip them.</p>' . n
        . '<p>Good choices: <em>Referral code</em>, <em>Promo code</em>, <em>Invite code</em>. Avoid: <em>Website</em>, <em>Phone</em>, <em>Email</em> (autofill risk), or <em>Leave blank</em> (bot detection risk).</p>' . n
        . ']]></item>' . n

        . '        <item id="far_connect_css_inject" title="Injection method"><![CDATA[' . n
        . '<h2>Injection method</h2>' . n
        . '<p><strong>Automatic</strong> (recommended) writes a <code>&lt;link rel="stylesheet"&gt;</code> tag directly into the form HTML on the server. Works on all themes with no template changes. Use this unless it does not work for your setup.</p>' . n
        . '<p><strong>Deferred</strong> uses JavaScript to create the <code>&lt;link&gt;</code> element dynamically after the page loads. Try this if Automatic does not load the stylesheet, for example if your server has a strict Content Security Policy that blocks link tags injected into the page body.</p>' . n
        . '<p><strong>Manual</strong> does not load the stylesheet automatically. You control loading by adding this tag to your page template: <code>&lt;txp:css name="far_connect" /&gt;</code></p>' . n
        . ']]></item>' . n

        . '        <item id="far_connect_auto_theme" title="Theme detection"><![CDATA[' . n
        . '<h2>Theme detection</h2>' . n
        . '<p>Detects the form\'s background colour via JavaScript and sets <code>data-far-com-theme="light"</code> or <code>"dark"</code> on the form element. Disable if you prefer to set the theme attribute manually in your template.</p>' . n
        . ']]></item>' . n

        . '        <item id="far_connect_auto_markers" title="Auto markers"><![CDATA[' . n
        . '<h2>Auto markers</h2>' . n
        . '<p>Automatically adds <strong>*</strong> next to required fields and <strong>&#8224;</strong> next to either/or fields in the form, along with a matching legend at the bottom.</p>' . n
        . ']]></item>' . n

        . '        <item id="far_connect_eitheror_rules" title="Either/or rules"><![CDATA[' . n
        . '<h2>Either/or rules</h2>' . n
        . '<p>For each rule, enter the form class and a comma-separated list of field names. At least one of those fields must be filled before the form can submit.</p>' . n
        . ']]></item>' . n

        . '    </group>' . n
        . '</help>' . n
        . '</resources>';

    file_put_contents($dir . DS . 'en_pophelp.xml', $xml);
}

function far_connect_remove_pophelp()
{
    $dir  = PLUGINPATH . DS . 'far_connect' . DS . 'lang';
    $file = $dir . DS . 'en_pophelp.xml';

    if (is_file($file)) {
        @unlink($file);
    }

    if (is_dir($dir) && count(glob($dir . DS . '*')) === 0) {
        @rmdir($dir);
    }
}

function far_connect_panel($event, $step)
{
    $available_steps = [
        'list'  => false,
        'save'  => true,
    ];

    // Both Save and Reset submit to step=save; reset is detected by its named button.
    if ($step === 'save' && gps('far_do_reset')) {
        far_connect_step_reset();
        return;
    }

    if (!$step || !bouncer($step, $available_steps)) {
        $step = 'list';
    }

    call_user_func('far_connect_step_' . $step);
}

function far_connect_step_reset()
{
    far_connect_uninstall();
    far_connect_install();
    header('Location: index.php?event=far_connect&reset=1');
    exit;
}

function far_connect_step_save()
{
    $prefs = [
        'far_connect_mail_provider',
        'far_connect_from_email',
        'far_connect_from_name',
        'far_connect_resend_api_key',
        'far_connect_brevo_api_key',
        'far_connect_captcha_provider',
        'far_connect_turnstile_site_key',
        'far_connect_turnstile_secret_key',
        'far_connect_recaptcha_site_key',
        'far_connect_recaptcha_secret_key',
        'far_connect_hcaptcha_site_key',
        'far_connect_hcaptcha_secret_key',
        'far_connect_honeypot_field_label',
    ];

    foreach ($prefs as $pref) {
        set_pref($pref, ps($pref));
    }

    // Checkbox: only present in POST when checked, so default to '0'.
    set_pref('far_connect_auto_markers', ps('far_connect_auto_markers') ? '1' : '0');

    // Stylesheet injection method.
    $inject = ps('far_connect_css_inject');
    set_pref('far_connect_css_inject', in_array($inject, ['buffer', 'js', 'none']) ? $inject : 'buffer');

    // Auto theme detection.
    set_pref('far_connect_auto_theme', ps('far_connect_auto_theme') ? '1' : '0');

    // Honeypot spam filter.
    set_pref('far_connect_honeypot', ps('far_connect_honeypot') ? '1' : '0');

    // Save either/or rules as JSON.
    $classes = ps('far_eitheror_class');
    $fields  = ps('far_eitheror_fields');
    $rules   = [];
    if (is_array($classes)) {
        foreach ($classes as $i => $cls) {
            $cls   = trim($cls);
            $field = trim($fields[$i] ?? '');
            if ($cls && $field) {
                $rules[] = ['class' => $cls, 'fields' => $field];
            }
        }
    }
    set_pref('far_connect_eitheror_rules', json_encode($rules));

    header('Location: index.php?event=far_connect&saved=1');
    exit;
}

function far_connect_step_list()
{
    // Regenerate the pophelp XML if it is missing or contains stale content
    // from a previous version (e.g. old option names like "Inline link").
    // Reading the file is fast; the write only happens when content is outdated.
    $pophelp_path = PLUGINPATH . DS . 'far_connect' . DS . 'lang' . DS . 'en_pophelp.xml';
    if (!is_file($pophelp_path)) {
        far_connect_install_pophelp();
    } else {
        $pophelp_content = file_get_contents($pophelp_path);
        if (strpos($pophelp_content, 'Automatic') === false
            || strpos($pophelp_content, 'Deferred') === false
            || strpos($pophelp_content, 'far_connect_honeypot_field_label') === false) {
            far_connect_install_pophelp();
        }
    }

    pagetop(gTxt('far_connect_settings_title'));

    if (gps('saved')) {
        echo announce(gTxt('far_connect_saved'));
    }

    if (gps('reset')) {
        echo announce(gTxt('far_connect_reset_done'));
    }

    $mail_provider    = get_pref('far_connect_mail_provider', 'smtp');
    $from_email       = get_pref('far_connect_from_email', '');
    $from_name        = get_pref('far_connect_from_name', '');
    $resend_key       = get_pref('far_connect_resend_api_key', '');
    $brevo_key        = get_pref('far_connect_brevo_api_key', '');
    $captcha_provider  = get_pref('far_connect_captcha_provider', 'none');
    $eitheror_rules    = json_decode(get_pref('far_connect_eitheror_rules', '[]'), true) ?: [];
    $auto_markers      = (bool) get_pref('far_connect_auto_markers', '0');
    $css_inject        = get_pref('far_connect_css_inject', 'buffer');
    $auto_theme        = (bool) get_pref('far_connect_auto_theme', '1');
    $ts_site          = get_pref('far_connect_turnstile_site_key', '');
    $ts_secret        = get_pref('far_connect_turnstile_secret_key', '');
    $rc_site          = get_pref('far_connect_recaptcha_site_key', '');
    $rc_secret        = get_pref('far_connect_recaptcha_secret_key', '');
    $hc_site          = get_pref('far_connect_hcaptcha_site_key', '');
    $hc_secret        = get_pref('far_connect_hcaptcha_secret_key', '');
    $honeypot             = (bool) get_pref('far_connect_honeypot', '1');
    $honeypot_field_label = get_pref('far_connect_honeypot_field_label', 'Referral code');

    // Connection status badges.
    $resend_status = '';
    if (!empty($resend_key)) {
        if (!function_exists('curl_init')) {
            $resend_status = '<span style="color:orange;">&#9888; cURL not available</span>';
        } else {
            $r = far_connect_test_resend($resend_key);
            $resend_status = $r['ok']
                ? '<span style="color:green;">&#10003; ' . gTxt('far_connect_api_key_valid') . '</span>'
                : '<span style="color:red;">&#10007; ' . txpspecialchars($r['error']) . '</span>';
        }
    }

    $brevo_status = '';
    if (!empty($brevo_key)) {
        if (!function_exists('curl_init')) {
            $brevo_status = '<span style="color:orange;">&#9888; cURL not available</span>';
        } else {
            $r = far_connect_test_brevo($brevo_key);
            $brevo_status = $r['ok']
                ? '<span style="color:green;">&#10003; ' . gTxt('far_connect_api_key_valid') . '</span>'
                : '<span style="color:red;">&#10007; ' . txpspecialchars($r['error']) . '</span>';
        }
    }

    // Mail provider select.
    $mail_select = Txp::get('\Textpattern\UI\Select',
        'far_connect_mail_provider',
        array(
            'none'   => gTxt('far_connect_provider_none'),
            'resend' => gTxt('far_connect_provider_resend'),
            'brevo'  => gTxt('far_connect_provider_brevo'),
            'smtp'   => gTxt('far_connect_provider_smtp'),
        ),
        $mail_provider
    )->setAtt('id', 'far_connect_mail_provider');

    // Captcha provider select.
    $captcha_select = Txp::get('\Textpattern\UI\Select',
        'far_connect_captcha_provider',
        array(
            'none'      => gTxt('far_connect_provider_none'),
            'turnstile' => gTxt('far_connect_provider_turnstile'),
            'recaptcha' => gTxt('far_connect_provider_recaptcha'),
            'hcaptcha'  => gTxt('far_connect_provider_hcaptcha'),
        ),
        $captcha_provider
    )->setAtt('id', 'far_connect_captcha_provider');

    // Admin JS: queued via script_js(…, false, true) so TXP flushes it at end_page()
    // after the full DOM is rendered. script_js wraps the content in $(function(){})
    // automatically, so no DOMContentLoaded or IIFE needed here.
    script_js(
        'var farComL10n={'
        .   'cls:'    . json_encode(gTxt('far_connect_eitheror_class'))        . ','
        .   'fld:'    . json_encode(gTxt('far_connect_eitheror_fields'))       . ','
        .   'clsA:'   . json_encode(gTxt('far_connect_eitheror_class_label'))  . ','
        .   'fldA:'   . json_encode(gTxt('far_connect_eitheror_fields_label')) . ','
        .   'remove:' . json_encode(gTxt('far_connect_eitheror_remove'))
        . '};'
        . 'function farComToggle(pfx,groups,val){'
        .   'groups.forEach(function(p){'
        .     'document.querySelectorAll("."+pfx+p).forEach(function(el){'
        .       'el.hidden=p!==val;'
        .     '});'
        .   '});'
        . '}'
        . 'function farComBuildRow(idx){'
        .   'var tr=document.createElement("tr");'
        .   'tr.innerHTML='
        .     '"<td><input type=\"text\" id=\"far-com-cls-"+idx+"\" name=\"far_eitheror_class[]\" aria-label=\""+farComL10n.cls+" "+(idx+1)+"\"></td>'
        .     '<td><input type=\"text\" id=\"far-com-fld-"+idx+"\" name=\"far_eitheror_fields[]\" aria-label=\""+farComL10n.fld+" "+(idx+1)+"\"></td>'
        .     '<td><button type=\"button\" class=\"far-com-remove-rule\" aria-label=\""+farComL10n.remove+"\">&#10005;</button></td>";'
        .   'return tr;'
        . '}'
        . 'var mailSel=document.getElementById("far_connect_mail_provider");'
        . 'var captSel=document.getElementById("far_connect_captcha_provider");'
        . 'function farComToggleFrom(val){'
        .   'document.querySelectorAll(".far-com-mail-from").forEach(function(el){el.hidden=val==="none";});'
        . '}'
        . 'if(mailSel){'
        .   'farComToggle("far-com-mail-",["resend","brevo","smtp","none"],mailSel.value);'
        .   'farComToggleFrom(mailSel.value);'
        .   'mailSel.addEventListener("change",function(){'
        .     'farComToggle("far-com-mail-",["resend","brevo","smtp","none"],this.value);'
        .     'farComToggleFrom(this.value);'
        .   '});'
        . '}'
        . 'if(captSel){'
        .   'farComToggle("far-com-captcha-",["turnstile","recaptcha","hcaptcha"],captSel.value);'
        .   'captSel.addEventListener("change",function(){farComToggle("far-com-captcha-",["turnstile","recaptcha","hcaptcha"],this.value);});'
        . '}'
        . 'var addLink=document.getElementById("far-com-add-rule");'
        . 'var table=document.getElementById("far-com-eitheror-table");'
        . 'if(addLink&&table){'
        .   'addLink.addEventListener("click",function(e){'
        .     'e.preventDefault();'
        .     'var tbody=table.querySelector("tbody")||table.createTBody();'
        .     'var idx="new-"+tbody.rows.length;'
        .     'var row=farComBuildRow(idx);'
        .     'tbody.appendChild(row);'
        .     'row.querySelector("input").focus();'
        .   '});'
        . '}'
        . 'if(table){'
        .   'table.addEventListener("click",function(e){'
        .     'if(e.target.classList.contains("far-com-remove-rule")){'
        .       'e.target.closest("tr").remove();'
        .     '}'
        .   '});'
        . '}',
    false, true);

    $token = function($pane) {
        return md5($pane . 'far_connect' . form_token() . get_pref('blog_uid'));
    };

    // Helper: build a field using TXP's official InputLabel class.
    // $name    = pref key (also used as input id and label gTxt key)
    // $input   = input HTML or UI object
    // $label   = label gTxt key; null = same as $name; '' = no <label> tag (radio/checkbox groups)
    // $extra   = extra CSS class on the outer div
    // $help    = true = XML pophelp entry exists for this field; false = no (?) icon
    $field = function($name, $input, $label = null, $extra = '', $help = false) {
        if ($label === null) {
            $label = $name;
        }
        $il = Txp::get('\Textpattern\UI\InputLabel', $name, $input, $label)
            ->setHelp(array($help ? 'far_connect:' . $name : '', ''))
            ->setAtts(array('class' => trim('txp-form-field ' . $extra), 'id' => 'prefs-' . $name))
            ->setWrap('label', null, array('id' => 'prefs-' . $name . '-label'));
        return $il->render();
    };

    echo form(
        tag_start('div', array('class' => 'txp-layout')) .

        tag(
            hed(gTxt('far_connect_settings_title'), 1, array('class' => 'txp-heading')),
            'div', array('class' => 'txp-layout-1col')
        ) .

        // ---- Sidebar nav ----
        tag_start('div', array('class' => 'txp-layout-4col-alt')) .
        wrapGroup(
            'far_connect_nav',
            n . tag(
                tag(href(gTxt('far_connect_mail_section'),       '#prefs_group_far_mail',       array('data-txp-pane' => 'far_mail',       'data-txp-token' => $token('far_mail'))),       'li') .
                tag(href(gTxt('far_connect_captcha_section'),    '#prefs_group_far_captcha',    array('data-txp-pane' => 'far_captcha',    'data-txp-token' => $token('far_captcha'))),    'li') .
                tag(href(gTxt('far_connect_stylesheet_section'), '#prefs_group_far_stylesheet', array('data-txp-pane' => 'far_stylesheet', 'data-txp-token' => $token('far_stylesheet'))), 'li') .
                tag(href(gTxt('far_connect_eitheror_section'),   '#prefs_group_far_validation', array('data-txp-pane' => 'far_validation', 'data-txp-token' => $token('far_validation'))), 'li'),
                'ul', array('class' => 'switcher-list')
            ),
            'far_connect_nav_label'
        ) .
        graf(
            fInput('submit', 'Submit', gTxt('save'), 'publish') . sp . sp .
            fInput('submit', 'far_do_reset', gTxt('far_connect_reset'), '', '', 'return verify(\'' . escape_js(gTxt('far_connect_reset_confirm')) . '\')'),
            array('class' => 'txp-save')
        ) .
        tag_end('div') .

        // ---- Content column ----
        tag_start('div', array('class' => 'txp-layout-4col-3span')) .

        // ---- Mail Delivery ----
        tag_start('section', array('class' => 'txp-tabs-vertical-group', 'id' => 'prefs_group_far_mail', 'aria-labelledby' => 'prefs_group_far_mail-label')) .
        hed(gTxt('far_connect_mail_section'), 2, array('id' => 'prefs_group_far_mail-label')) .

        $field('far_connect_mail_provider', $mail_select, null, '', true) .

        $field('far_connect_from_email',
            fInput('email', 'far_connect_from_email', $from_email, '', '', '', INPUT_REGULAR, 'far_connect_from_email'),
            null, 'far-com-mail-from', true) .

        $field('far_connect_from_name',
            fInput('text', 'far_connect_from_name', $from_name, '', '', '', INPUT_REGULAR, 'far_connect_from_name'),
            null, 'far-com-mail-from', true) .

        $field('far_connect_resend_api_key',
            fInput('password', 'far_connect_resend_api_key', $resend_key, '', '', '', INPUT_REGULAR, 'far_connect_resend_api_key') .
            ($resend_status ? n . $resend_status : ''),
            'far_connect_resend_api_key', 'far-com-mail-resend', true) .

        $field('far_connect_brevo_api_key',
            fInput('password', 'far_connect_brevo_api_key', $brevo_key, '', '', '', INPUT_REGULAR, 'far_connect_brevo_api_key') .
            ($brevo_status ? n . $brevo_status : ''),
            'far_connect_brevo_api_key', 'far-com-mail-brevo', true) .

        tag_end('section') .

        // ---- Captcha ----
        tag_start('section', array('class' => 'txp-tabs-vertical-group', 'id' => 'prefs_group_far_captcha', 'aria-labelledby' => 'prefs_group_far_captcha-label')) .
        hed(gTxt('far_connect_captcha_section'), 2, array('id' => 'prefs_group_far_captcha-label')) .

        $field('far_connect_captcha_provider', $captcha_select, null, '', true) .

        $field('far_connect_turnstile_site_key',
            fInput('text', 'far_connect_turnstile_site_key', $ts_site, '', '', '', INPUT_REGULAR, 'far_connect_turnstile_site_key'),
            'far_connect_turnstile_site_key', 'far-com-captcha-turnstile', true) .

        $field('far_connect_turnstile_secret_key',
            fInput('password', 'far_connect_turnstile_secret_key', $ts_secret, '', '', '', INPUT_REGULAR, 'far_connect_turnstile_secret_key'),
            'far_connect_turnstile_secret_key', 'far-com-captcha-turnstile', true) .

        $field('far_connect_recaptcha_site_key',
            fInput('text', 'far_connect_recaptcha_site_key', $rc_site, '', '', '', INPUT_REGULAR, 'far_connect_recaptcha_site_key'),
            'far_connect_recaptcha_site_key', 'far-com-captcha-recaptcha', true) .

        $field('far_connect_recaptcha_secret_key',
            fInput('password', 'far_connect_recaptcha_secret_key', $rc_secret, '', '', '', INPUT_REGULAR, 'far_connect_recaptcha_secret_key'),
            'far_connect_recaptcha_secret_key', 'far-com-captcha-recaptcha', true) .

        $field('far_connect_hcaptcha_site_key',
            fInput('text', 'far_connect_hcaptcha_site_key', $hc_site, '', '', '', INPUT_REGULAR, 'far_connect_hcaptcha_site_key'),
            'far_connect_hcaptcha_site_key', 'far-com-captcha-hcaptcha', true) .

        $field('far_connect_hcaptcha_secret_key',
            fInput('password', 'far_connect_hcaptcha_secret_key', $hc_secret, '', '', '', INPUT_REGULAR, 'far_connect_hcaptcha_secret_key'),
            'far_connect_hcaptcha_secret_key', 'far-com-captcha-hcaptcha', true) .

        $field('far_connect_honeypot',
            Txp::get('\Textpattern\UI\OnOffRadioSet', 'far_connect_honeypot', $honeypot ? '1' : '0'),
            'far_connect_honeypot_label', '', true) .

        $field('far_connect_honeypot_field_label',
            fInput('text', 'far_connect_honeypot_field_label', $honeypot_field_label, '', '', '', INPUT_REGULAR, 'far_connect_honeypot_field_label'),
            null, '', true) .

        tag_end('section') .

        // ---- Stylesheet ----
        tag_start('section', array('class' => 'txp-tabs-vertical-group', 'id' => 'prefs_group_far_stylesheet', 'aria-labelledby' => 'prefs_group_far_stylesheet-label')) .
        hed(gTxt('far_connect_stylesheet_section'), 2, array('id' => 'prefs_group_far_stylesheet-label')) .

        $field('far_connect_css_inject',
            Txp::get('\Textpattern\UI\RadioSet', 'far_connect_css_inject', array(
                'buffer' => gTxt('far_connect_css_inject_buffer'),
                'js'     => gTxt('far_connect_css_inject_js'),
                'none'   => gTxt('far_connect_css_inject_none'),
            ), $css_inject),
            null, '', true) .

        $field('far_connect_auto_theme',
            Txp::get('\Textpattern\UI\YesNoRadioSet', 'far_connect_auto_theme', $auto_theme ? '1' : '0'),
            'far_connect_auto_theme_label', '', true) .

        // ---- Form Validation ----
        tag_end('section') .
        tag_start('section', array('class' => 'txp-tabs-vertical-group', 'id' => 'prefs_group_far_validation', 'aria-labelledby' => 'prefs_group_far_validation-label')) .
        hed(gTxt('far_connect_eitheror_section'), 2, array('id' => 'prefs_group_far_validation-label')) .

        $field('far_connect_auto_markers',
            Txp::get('\Textpattern\UI\OnOffRadioSet', 'far_connect_auto_markers', $auto_markers ? '1' : '0'),
            'far_connect_auto_markers_label', '', true) .

        $field('far_connect_eitheror_rules',
            tag(
                '<colgroup><col style="width:42%"><col style="width:42%"><col style="width:16%"></colgroup>' .
                tag(tag(
                    tag(gTxt('far_connect_eitheror_class'),  'th', array('scope' => 'col')) .
                    tag(gTxt('far_connect_eitheror_fields'), 'th', array('scope' => 'col')) .
                    '<th scope="col"></th>',
                'tr'), 'thead') .
                tag(
                    n . (function() use ($eitheror_rules) {
                        $rows = '';
                        foreach ($eitheror_rules as $i => $rule) {
                            $ci = 'far-com-cls-' . $i;
                            $fi = 'far-com-fld-' . $i;
                            $rows .= tag(
                                td(fInput('text', 'far_eitheror_class[]',  $rule['class']  ?? '', '', '', '', INPUT_REGULAR, $ci, '', '', 'aria-label="' . gTxt('far_connect_eitheror_class')  . ' ' . ($i+1) . '"')) .
                                td(fInput('text', 'far_eitheror_fields[]', $rule['fields'] ?? '', '', '', '', INPUT_REGULAR, $fi, '', '', 'aria-label="' . gTxt('far_connect_eitheror_fields') . ' ' . ($i+1) . '"')) .
                                td(tag('&#10005;', 'button', array('type' => 'button', 'class' => 'far-com-remove-rule', 'aria-label' => gTxt('far_connect_eitheror_remove')))),
                            'tr');
                        }
                        return $rows;
                    })() . n,
                'tbody'),
            'table', array('id' => 'far-com-eitheror-table', 'style' => 'table-layout:fixed;width:100%')) .
            graf(tag('+ ' . gTxt('far_connect_eitheror_add'), 'button', array('type' => 'button', 'id' => 'far-com-add-rule'))),
            'far_connect_eitheror_rules_label', '', true) .

        tag_end('section') .   // close Form Validation
        tag_end('div') .       // close txp-layout-4col-3span

        sInput('save') .
        eInput('far_connect') .
        tInput() .

        tag_end('div'),        // close txp-layout
        '', '', 'post', 'prefs-form', '', 'far-com-connect-form'
    );

}

# --- END PLUGIN CODE ---
