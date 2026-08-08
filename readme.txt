=== Login Canvas – Customize Login Page ===
Contributors: mehrdadsa1
Tags: customize login page, custom login page, login page customizer, login branding, wordpress login
Requires at least: 6.4
Tested up to: 7.0
Requires PHP: 7.4
Stable tag: 1.5.3
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Design a modern, responsive, and branded WordPress login page without changing authentication or front-end login forms.

== Description ==

Login Canvas helps you replace the standard WordPress login screen with a polished, brand-focused layout using familiar controls inside the WordPress dashboard.

Add your own logo, edit the login content, customize colors and backgrounds, and create a responsive split-panel design for both RTL and LTR websites. Login Canvas only changes the appearance of the default WordPress login screen. It does not replace WordPress authentication or change the login URL.

= Main features =

* Responsive split-panel login layout.
* Custom logo, logo link, and logo title.
* Editable login heading and supporting text.
* Editable visual-panel heading and description.
* Custom background image or background color.
* Custom form, text, link, button, and accent colors.
* Adjustable form width and corner radius.
* Automatic RTL and LTR support.
* Optional Estedad font for Persian and Inter for other languages.
* Option to hide the back-to-site link.
* Option to hide the WordPress language selector.
* Live preview inside the settings screen.
* No tracking, analytics, or user profiling.

= Designed to avoid login conflicts =

Login Canvas does not modify the authentication process, login URL, user credentials, redirects, or sessions. It only styles the default WordPress login screen.

The plugin does not intentionally modify:

* WooCommerce My Account login forms.
* Digits login and registration forms.
* Membership plugin login forms.
* Custom front-end login forms.
* REST API or AJAX authentication.

= Privacy =

Login Canvas does not collect, store, or transmit personal data.

Google Fonts integration is optional and disabled by default. When it is disabled, the plugin uses local system font fallbacks and does not request font files from Google.

== Installation ==

1. Upload the plugin ZIP from Plugins > Add New > Upload Plugin, or copy the `login-canvas` folder to `/wp-content/plugins/`.
2. Activate Login Canvas from the Plugins screen.
3. Go to Settings > Login Canvas.
4. Add your logo and customize the layout, content, colors, and visibility options.
5. Save the settings and open the standard WordPress login page to review the result.

== Frequently Asked Questions ==

= Does Login Canvas change the WordPress login URL? =

No. The plugin only changes the visual design of the standard WordPress login page.

= Does it change how users are authenticated? =

No. Authentication, sessions, passwords, redirects, and user permissions continue to be handled by WordPress.

= Does it affect WooCommerce or Digits login forms? =

No. Login Canvas targets the default WordPress login screen and does not intentionally style WooCommerce My Account, Digits, membership, or other front-end login forms.

= Is the plugin compatible with Persian and other RTL languages? =

Yes. Login Canvas uses the current WordPress text direction and supports both RTL and LTR layouts.

= Can I use a custom logo? =

Yes. Select a logo from the WordPress Media Library and optionally set its destination URL and title.

= Can I use a background image? =

Yes. You can select an image from the Media Library or use a solid background color.

= Which fonts does the plugin use? =

Local system font fallbacks are used by default. Administrators may optionally enable Google Fonts to load Estedad on Persian pages and Inter on other languages.

= Why are Google Fonts disabled by default? =

Keeping the option disabled avoids external font requests and provides a more privacy-friendly default. The administrator must explicitly enable the integration.

= Will my settings be removed when I delete the plugin? =

Yes. Deleting the plugin through the WordPress Plugins screen removes its saved options.

== External services ==

Login Canvas includes an optional Google Fonts integration for loading Estedad and Inter. This feature is disabled by default and is only used after an administrator enables it in Settings > Login Canvas.

When enabled, a visitor's browser connects to Google servers to request the font stylesheet and font files. The request may expose technical information such as the visitor's IP address, browser user agent, and referring page to Google, subject to Google's policies.

Service documentation: https://developers.google.com/fonts/docs/getting_started
Privacy policy: https://policies.google.com/privacy
Terms of service: https://policies.google.com/terms

== Screenshots ==

1. Login Canvas settings with branding and design controls.
2. Responsive split-panel login page in an RTL language.
3. Login page in an LTR language.
4. Mobile login layout.

== Changelog ==

= 1.5.3 =
* Initial release.
