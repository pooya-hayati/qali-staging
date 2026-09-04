# Qali.art — WordPress Project Report

**Scope:** Custom theme `themes/qali` (child-less, standalone theme, "Qali" by Mohammad Zare) running WooCommerce, with 16 plugins vendored under `modules/` and translation files under `languages/` and `themes/qali/lang/`.

> **Note on how this report was produced:** the working copy of this repository initially contained only empty (0‑byte) placeholder files — an interrupted SFTP sync. Real file contents were re-synced from the live `dev.qali.art` server before this review, so every finding below is based on actually reading the current theme/plugin source, not on file names alone.

---

## 1. Theme Structure

### Top-level folders

| Folder | What it actually does |
|---|---|
| **App/** | The project-specific code. PSR‑4, OOP (`namespace App`). Bootstrapped by `functions.php` → `App\Init::register_services()`, which instantiates `Controller\InitController`, `PostTypes\InitPostTypes`, `Setup\AppSetup`, `Setup\AppEnqueue`, `Setup\AppMenu`, `Setup\AppSettings`. Contains all business logic: WooCommerce customization (`Controller/Shop.php`), security/admin hardening (`Controller/WPCustom.php`, 1494 lines), page/post-type meta-box field definitions (`PostTypes/PagePost.php`), contact form, mail sending, wishlist, custom "build your own rug" pricing (`CustomPay.php`), etc. |
| **Core/** | A generic theme-framework base layer (looks like a boilerplate called "awps" — see the `@package awps` doc-comment in `App/Init.php`). Defines constants (`Core/Define.php`: `URL_ROOT`, `URL_ASSETS`, etc.), and abstract base classes (`Core\PostType`, `Core\Settings`, `Core\Setup`, `Core\Enqueue`, `Core\MenusCore`, `Core\Rewrite`, `Core\WidgetsCore`) that the `App/*` classes extend (e.g. `App\Setup\AppSetup extends Core\Setup`). Not project-specific — it's reusable scaffolding the theme author brings to every project. |
| **includes/** | Third-party/vendored helper libraries loaded procedurally via `includes/init.php` (required from `Core/Define.php:13`): `hashids/` (ID obfuscation library), `date_jalali/` (Persian/Jalali calendar conversion), `metabox.plugins/` (a set of small custom Meta Box field-type add-ons: image-serial, mb-post-serialize, mb-serial-advanced-serialize, mb-user-advanced-serialize, date_picker_jalali). |
| **templates/** | Reusable partials pulled into the top-level page templates via a custom `get_template_part_var()` helper: `templates/header/` (header-main, header-page, header-shop), `templates/footer/`, `templates/card/` (product/blog/collector cards), `templates/mail/` (transactional email HTML), `templates/navigation/`. |
| **assets/** | Static front-end assets: `css/`, `js/`, `fonts/`, `img/` (includes a large `img/art/` set of dozens of JPGs used only by `page-art.php`'s hardcoded slideshow). |
| **vendor/** | Composer-managed dependencies. Only one real package group is present: **`meta-box/*`** — meta-box, mb-settings-page, mb-term-meta, meta-box-columns, meta-box-conditional-logic, meta-box-group, meta-box-include-exclude, meta-box-tabs (see `vendor/composer/autoload_files.php`). **This is the theme's custom-fields engine — the project does not use Advanced Custom Fields (ACF) at all; it uses the "Meta Box" plugin/library instead** (more in §5). |
| **lang/** (`themes/qali/lang/`) | The theme's own translation catalog: `fa_IR.po` / `fa_IR.mo` (Persian). Loaded via `load_theme_textdomain(LANG_STRING, URL_ROOT_DIR . '/lang')` in `Core/Setup.php:26`, where `LANG_STRING = 'qali-lang'` (`App/Define.php:2`). |

### Page templates — full inventory

| Template | Renders | Content source |
|---|---|---|
| `.front-page.php` | The site's actual homepage route. Logged-in users see `page-home.php`; everyone else sees `under-construction.php` (line 1‑6). **The public site is currently gated behind login** — worth confirming this is intentional for the current launch stage. | Hardcoded routing logic |
| `under-construction.php` | A static "Under Construction" holding page (inline CSS, no theme assets loaded, logo `<img>` is commented out). | 100% hardcoded HTML, no translation |
| `page-home.php` | Home page: Hero, Intro, Featured products, Collection, Banner, Certificate, Blog sections. | **Dynamic** — reads `get_post_meta_all($post->ID)` fields defined in `PagePost.php` (Meta Box), plus live WooCommerce/blog queries |
| `page-about.php` | About Us: Hero, Intro, Mission, Gallery, Vision, Member, Service, CTA (Certificate section is present in the admin fields but its markup is commented out, see §6). | **Dynamic**, same mechanism as above |
| `page-contact.php` | Contact page: title + a contact form. | Title dynamic (meta box); form fields hardcoded markup |
| `page-faq.php` | FAQ: repeatable category→question groups + a CTA block. | **Dynamic** (Meta Box repeater fields) |
| `page-collection.php` | "Collections" landing page: intro + repeatable collection items (each links to a taxonomy term). | **Dynamic** |
| `page-art.php` (1207 lines) | "Rug is Art" — a bespoke, heavily animated storytelling/scroll page comparing rugs to classical paintings. | **100% hardcoded.** Every heading, paragraph, and of dozens of image paths (`assets/img/art/slide-*/*.jpg`) is written directly in the PHP/HTML. Zero translation-function calls (`grep` count: 0). This is by far the most hardcoded page in the project. |
| `page-custom.php` (433 lines) | A multi-step "Build Your Custom Rug" wizard/order form (`<form id="wizard-form">`). | **Mostly hardcoded.** Option lists for `$shape`, `$color` (grouped by "Neutral/Bold/Earthy/Pastel Tones"), and `$design` (grouped by "Traditional/Moroccan/…") are literal PHP arrays (lines 13‑90+), duplicating data that already exists as WooCommerce attribute taxonomy terms (`pa_shape`, `pa_color`, `pa_design`) elsewhere in the site — see §6. The surrounding wizard UI labels do use `__()` (38 calls), but the option arrays themselves do not. |
| `page-shopping.php` | A simple content page for cart/shopping-related copy; title + `the_content()` (standard WP editor content). | Dynamic (standard post content) |
| `page-wishlist.php` | Renders the logged-in user's wishlist (products saved via `_user_wishlist` user meta). | Dynamic |
| `page-account.php` | Account dashboard page. | Dynamic |
| `page-login.php` / `page-register.php` / `page-forgot.php` | Auth forms; use `get_option('settings')` for the logo, hardcoded form markup otherwise. | Mixed |
| `page.php` | Generic fallback for any WordPress Page not using one of the templates above. Hero via the global Hero meta box, body via `the_content()`. | Dynamic |
| `index.php` | Blog index page. | Dynamic (meta of `page_for_posts`, `WP_Query` of posts) — but note a hardcoded literal `<h3 class="filter-card-title">Blog Posts & News</h3>` (not wrapped in `__()`) |
| `single.php` | Single blog post. | Dynamic (post fields) |
| `single-product.php` (428 lines) | Single product ("rug") page: title, price, SKU, size/area, categories, attributes, a Meta-Box "Cards" content-block area, an AR "view in your room" widget, and a modal with rug care/spec descriptions pulled from taxonomy term descriptions. | Dynamic; the best-escaped template in the theme (consistent `esc_html`/`esc_attr`/`esc_url`/`wp_kses_post`) |
| `archive-product.php` / `taxonomy-product_cat.php` / `taxonomy-product_tag.php` | All three are byte-for-byte the same thin wrapper: shop header partial + product grid + pagination. Per-category/tag pages have **no unique content** beyond the shared shop header (`templates/header/header-shop.php`, itself driven by the "Shop" page's Hero meta fields + live attribute-taxonomy filter facets). | Dynamic, but generic (no category-specific copy) |

Other structural files: `header.php`/`footer.php` (load `templates/header/header-main.php` / `templates/footer/footer-main.php`, gated off on the 3 auth-page templates), `style.css` (theme header only, no actual CSS rules — real styles live in `assets/css/`).

### Hardcoded vs. dynamic — summary
- **Fully dynamic (CMS-editable via Meta Box fields):** `page-home.php`, `page-about.php`, `page-faq.php`, `page-collection.php`, `page.php`, all blog/product/blog templates, the global Hero block used on every Page.
- **Mostly hardcoded, effectively un-editable without a code deploy:** `page-art.php` (fully), `page-custom.php` (option lists), `under-construction.php`, auth pages (form markup), the shop/category header's static decorative bits (SVG background paths, `pattern-*.svg`).

---

## 2. Products and WooCommerce

### Custom "rug" data model

Rugs use **standard WooCommerce product data + WooCommerce product attribute taxonomies**, not a bespoke custom-fields schema. Confirmed attribute taxonomies (all `pa_*`, registered by WooCommerce itself, used throughout the theme — `App/Controller/Shop.php`, `single-product.php`, `templates/header/header-shop.php`):

- `pa_color`, `pa_design`, `pa_size`, `pa_origin`, `pa_material`, `pa_shape`, `pa_thickness`, `pa_feel`

Two of these get **extra custom term-meta fields** registered via Meta Box in `App/Controller/Shop.php:299‑336` (`register_meta()`, hooked to `rwmb_meta_boxes`):
- `pa_color` → a `color` field (color picker) — presumably drives swatch UI.
- `pa_design` → an `image` field (single image) — a representative pattern thumbnail.
- `pa_size` → a `subtitle` textarea field.

The rich per-attribute descriptions shown in the product page's "Insights and Styling Guides" modal (`single-product.php:385‑418`, features = design/feel/group/material/origin/shape/size/thickness) come from **the taxonomy term's own `description` field**, not a separate custom field — and `App/Controller/WPCustom.php:1347` (`replace_description_with_wysiwyg`) upgrades that native "Description" textarea into a full WYSIWYG editor in wp-admin so editors can format it with HTML (this is how the `<li><b>Title:</b><div>…</div></li>` markup parsed at `single-product.php:82` gets authored).

Two genuine **custom postmeta fields** exist for physical dimensions:
- `_length`, `_width` (centimeters) → read at `single-product.php:44‑53`, converted to feet via `cmToFt()` (`App/Helpers.php:68`) and combined into a "Size" / "Area" display.

A **Meta Box "Cards" repeater field** (`page.card`, registered on the `product` post type in `App/Controller/Shop.php:182‑339`) provides an admin-editable content-block area (image / text / quote / video / json / embed blocks) rendered on the product page as the "presentation-grid" section (`single-product.php:256‑329`).

### External link field (e.g. Noon.com)

**Does not exist.** A repo-wide search for `noon`, `external_link`, `external_url`, `buy_link`, `marketplace`, `affiliate` in `themes/qali` turns up only one unrelated match: `App/Controller/Shop.php:84`, which disables WooCommerce's own **"marketplace suggestions"** admin nag (`add_filter('woocommerce_allow_marketplace_suggestions', '__return_false', 999)`) — this has nothing to do with product listings on external marketplaces. There is no meta field, no product tab, and no admin UI anywhere for attaching an outbound link (Noon.com or otherwise) to a product. This would need to be added from scratch — either as a new Meta Box text/url field on the product post type (following the exact pattern already used in `Shop.php::register_meta()`), or as a WooCommerce custom product field.

### `product_cat` taxonomy structure

Plain, un-extended **default WooCommerce `product_cat`** taxonomy:
- No theme code registers extra term-meta fields for `product_cat` specifically (only `pa_color`/`pa_design`/`pa_size` get custom term fields, as above). Category "images" are whatever WooCommerce core's own built-in category Thumbnail field provides — nothing theme-added.
- No per-category template variation: `taxonomy-product_cat.php`, `taxonomy-product_tag.php`, and `archive-product.php` are all identical (product grid + pagination), and the shared header (`templates/header/header-shop.php`) is generic for every category/tag/shop page alike — it pulls its Hero text from the WooCommerce **Shop page's own** post meta (`get_option('woocommerce_shop_page_id')`), not from the category term itself. So editing a category currently gives you no way to add category-specific copy/banner — every category page looks the same aside from the products shown.
- `product_cat` is used as a filterable facet in the custom "Collection" content blocks on the Home and Collections pages (`taxonomy_advanced` Meta Box field type, `PagePost.php:131` and `:899`), alongside the `pa_*` attribute taxonomies.

### dressmycrib and products
`modules/dressmycrib` does **not** touch product post-meta or the product data model. It's a shortcode-based front-end AR/visualization widget invoked with only `sku` and `product_title` (see full write-up in §3) — it reads product data but doesn't add fields to it.

---

## 3. Installed Plugins (`modules/`)

- **akismet** (v5.4): Automattic's core anti-spam plugin — filters spam from comments/forms via the Akismet API.
- **awin-advertiser-tracking** (v2.0.1): Awin affiliate-network's official plugin — fires Awin conversion-tracking pixels on WooCommerce orders for affiliate commission attribution.
- **duracelltomi-google-tag-manager** ("GTM4WP", v1.21.1): Injects a Google Tag Manager container and exposes WooCommerce e-commerce data to the GTM `dataLayer`.
- **google-listings-and-ads** (v2.9.11, "Google for WooCommerce"): Official Google plugin syncing the WooCommerce catalog to Google Merchant Center and managing Google Shopping / Performance Max ads.
- **klaviyo** (v3.5.0): Official Klaviyo plugin — syncs customers, orders, and products to Klaviyo for email/SMS marketing automation (abandoned-cart flows, etc.).
- **microsoft-clarity** (v0.10.4): Embeds the Microsoft Clarity session-recording/heatmap analytics script.
- **pinterest-for-woocommerce** (v1.4.17): Official Pinterest plugin — syncs the product catalog to Pinterest, adds "Pin it" and the Pinterest conversion tag.
- **woocommerce** (v9.8.1): The core e-commerce engine the entire store is built on.
- **woocommerce-gateway-stripe** (v9.4.1): Official Stripe payment gateway for WooCommerce (cards, Apple/Google Pay).
- **woocommerce-payments** ("WooPayments", v9.2.0): Automattic/Stripe-backed built-in payments processor.
- **woocommerce-paypal-payments** (v3.0.3): Official PayPal checkout integration (PayPal, Pay Later, cards, wallets).
- **woocommerce-services** ("WooCommerce Shipping & Tax", v2.8.9): Automattic's hosted shipping-label/tax-calculation service. *Note: WooCommerce.com discontinued/folded most of this plugin's features into WooCommerce Shipping in 2024 — worth verifying this is still the actively-supported path rather than a legacy holdover (see §6).*
- **wordpress-importer** (v0.8.4): Core WP content importer (imports posts/pages/media from a WXR export file) — typically a one-time/maintenance tool, not something that needs to stay active long-term.
- **wordpress-seo** ("Yoast SEO", v24.9): On-page SEO analysis, XML sitemaps, schema markup.
- **wordpress-seo-premium** (v24.5): Yoast's paid add-on (redirects, internal linking, multiple focus keyphrases).

### `dressmycrib` — deep dive

This is **not a third-party plugin from the WordPress ecosystem** — it's a small, bespoke integration built specifically for this store. Its own header says so plainly (`modules/dressmycrib/dressmycrib-plugin.php:1‑6`):

```php
/**
 * Plugin Name: Dressmycrib-Qali
 * Description: Special Dressmycrib plugin for Qali.
 * Version: 1.001
 * Author: Dressmycrib
 */
```

What it does, confirmed by reading the ~110-line main file plus its actual usage in the theme:

1. It registers a `[dressmycrib-viewer sku='…' product_title='…']` shortcode (`inject_react_scripts()`), which is called live from `single-product.php:289‑296` inside a "Use AR to See this Rug in Your Space" section. **This is the site's "view this rug in your room" AR/visualization feature.**
2. The shortcode loads **React 18 from `unpkg.com`** — and notably loads the **development build** (`react.development.js` / `react-dom.development.js`), not the production build. Development builds are larger, slower, and print console warnings; this should be swapped for `react.production.min.js` (or bundled locally) before/for production traffic.
3. It then loads a bundled script `dist/dressmycrib_v1.0.0.js` and calls `DressMyCrib.renderRugsInRooms(elementId, sku, productTitle, '', termsText, clientId)` — the bundle's `dist/` folder (React components named `RugInRoom`, `RugInARViewer`, `insert-rug-in-image/CarpetControl`, `QRCodeModal`, etc.) confirms this is a proper componentized "photograph your room → composite the rug into it, optionally view in AR via a QR-code handoff to your phone" widget.
4. `clientId = 2` is hardcoded as "Qali client" — meaning DressMyCrib is a multi-tenant SaaS-style widget vendor and Qali is one of their clients (client #2), not a generic WordPress.org plugin.
5. There is **dead/incomplete code** in the same file: `cpib_add_button_to_gallery()` is defined but its `add_action` registration is commented out (`// add_action('woocommerce_before_single_product_summary', 'cpib_add_button_to_gallery', 20);`), and a `[product_image_button]` shortcode renders only a hidden, non-functional placeholder `<a>` tag with the literal text "Custom Button". These look like an earlier iteration of the same feature (naming prefix `cpib` — possibly "Custom Product Image Button") that was superseded by the shortcode approach above but never cleaned up.

**In short: "dressmycrib" is the AR/room-visualization widget vendor for the "see this rug in your room" feature on the product page, custom-wired for Qali.**

---

## 4. Current Multilingual Status

### `languages/` folder (top level)
All files here are **WordPress/WooCommerce core translations for Arabic (`ar`) only**, auto-downloaded by WordPress when the Arabic language pack is installed — not project-authored content:
- `admin-ar.*`, `admin-network-ar.*` — wp-admin UI strings
- `ar.*` — WordPress core front-end strings
- `continents-cities-ar.*` — WP's city/timezone list
- `languages/plugins/woocommerce-ar-*.json` (59 files) — WooCommerce's own Arabic JS/Jed translation catalog, split per-script-hash by WP's translation loader

**No other locale (besides Arabic core/WooCommerce strings) has files here**, and none of it covers the theme's own custom strings.

### `themes/qali/lang/` (the theme's own translations)
- `fa_IR.po` / `fa_IR.mo` — **Persian**, the theme's own catalog, text domain `qali-lang` (`LANG_STRING` constant, loaded via `load_theme_textdomain()` in `Core/Setup.php:26`).
- `fa_IR.po` contains **94 translated strings** (`msgid`/`msgstr` pairs) — e.g. `"Options"` → `"تنظیمات"`.
- The theme's PHP code calls translation functions on **381 unique strings** (612 total call sites) using the `qali-lang` domain. **Roughly 75% of the theme's translatable strings have no Persian translation yet** and will fall back to whatever English/Persian text is hardcoded as the `__()` default argument.
- Minor gap: `style.css` does not declare a `Text Domain:` header (WordPress best practice for tooling like Loco Translate / translate.wordpress.org auto-discovery) — functionally harmless here since the domain is set explicitly in code, but worth adding.

**Bottom line:** Arabic support exists only for WordPress/WooCommerce's own built-in strings (not the theme). Persian support exists for the theme's custom strings but is ~25% complete. No other language is present at all.

### Translation-readiness of the theme's own code — real examples

The theme is **inconsistently** translation-ready: well-disciplined in some templates, entirely hardcoded in others.

**Good (translation-ready) examples:**
```php
// single-product.php:115
echo esc_html__('One of One', LANG_STRING);

// templates/card/card-product.php:83
echo esc_html__('Sold Out', LANG_STRING);

// App/Controller/Shop.php:433 — correctly reuses WooCommerce's own domain
$units['ft'] = __('Foot (ft)', 'woocommerce');
```

**Hardcoded (not translatable) examples:**
```php
// page-art.php:91 — literal English, no __() at all (whole 1207-line file has zero translation calls)
<div class="slide-desc">'Rug is Art' is a deep dive into Persian rugs and classical paintings…</div>

// page-custom.php:20-41 — option labels are bare literals, not wrapped in __()
'color' => ['Neutral Tones' => ['Beige', 'Ivory', 'Gray'], 'Bold Tones' => ['Burgundy', 'Navy', 'Emerald'], …]

// index.php — hardcoded literal sitting next to translated neighbors
<h3 class="filter-card-title">Blog Posts & News</h3>
```
`page-art.php` and `page-custom.php`'s data arrays are the two biggest concentrations of non-translatable text in the theme.

---

## 5. Readiness for Admin-Panel Editing

**ACF is not used anywhere in this project** (`grep` for `get_field(`, `have_rows(`, `acf_add_local_field_group` across the whole theme: zero matches). The theme uses the **Meta Box plugin/library suite** (vendored via Composer under `vendor/meta-box/`) as its custom-fields engine instead, which is functionally equivalent to ACF for this purpose — everything below runs through it.

The WordPress **Customizer** (`get_theme_mod`) is also **not used** (zero matches) — no site identity/color/layout options live in `Appearance → Customize`.

**What IS already admin-editable** (confirmed by tracing each field from its Meta Box registration through to the template that reads it via `get_post_meta_all()`):
- **Home page** (`page-home.php`) — Intro, Featured, Collection, Banner, Certificate, Blog sections, plus the site-wide Hero block, all via tabs in the WP page editor.
- **About page** (`page-about.php`) — Intro, Mission, Gallery, Vision, Member, Service, CTA (Certificate tab exists in admin but its template output is commented out — see §6).
- **Contact, FAQ, Collections pages** — titles/FAQ groups/collection items, per §1.
- **Site-wide Theme Settings** (`App/Setup/AppSettings.php`, registered as its own wp-admin menu page "Theme Settings") — logo/logomark/footer logo, About text, copyright, full contact info (manager email, address, phone, postal code, fax, email), and 10 social network URLs. **Confirmed actually consumed** by `templates/header/header-main.php`, `templates/footer/footer-main.php`, the contact form, and transactional emails — not dead fields.
- **Product rug specs** — via the `pa_*` attribute taxonomy terms' description fields (WYSIWYG-enabled) and the two color/design term-meta fields, editable from `Products → Attributes` in wp-admin.

**What is NOT editable and would need the most work:**
1. **`page-art.php` ("Rug is Art")** — the single biggest gap. 1207 lines of fully hardcoded copy and ~80+ hardcoded image paths, zero admin fields. Making this editable would essentially mean designing a whole new Meta Box field schema (a repeatable "slide" group with image/text/type fields, similar to the existing "Cards" pattern already used elsewhere) and rewriting the template to consume it.
2. **`page-custom.php` (custom-rug builder)** — the shape/color/design option lists are hardcoded PHP arrays that duplicate data already modeled as `pa_shape`/`pa_color`/`pa_design` taxonomy terms elsewhere. These should be replaced with a live `get_terms()` query against those taxonomies so they can't drift out of sync (see §6) — that also happens to make them "admin-editable" for free, since the taxonomy terms already are.
3. **Category (`product_cat`) pages** — currently have no per-category editable content at all (no banner, no description-driven layout beyond the shared generic shop header). Adding category-specific fields would require a new Meta Box registration with `'taxonomies' => 'product_cat'` (the exact pattern already used for `pa_color`/`pa_design`/`pa_size` in `Shop.php`).
4. **Decorative/structural bits scattered everywhere** — background SVG paths (`pattern-1.svg`, `pattern-2.svg`, etc.), fixed slug lookups like `get_page_by_path('rug-is-art')` / `get_page_by_path('collections')` in `page-home.php` — these aren't "content" in the editorial sense, but they are fragile: renaming or deleting those pages' slugs silently breaks the links.
5. **The FAQ/About "Certificate" sections** — fields exist in the admin already but the matching template markup is commented out, so editing them currently has no visible effect (see §6).

---

## 6. Potential Issues and Bugs

Ordered roughly by severity/impact:

1. **Wide-reaching data-corruption bug: `sanitize_text_field` is silently rewired to run everything through `htmlspecialchars()`.**
   `App/Controller/WPCustom.php:47`: `add_filter('sanitize_text_field', [$this, 'sanitize_sql_injection'], 10, 2);`, where `sanitize_sql_injection()` (line 325) is just:
   ```php
   public function sanitize_sql_injection($value, $context) {
       return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
   }
   ```
   `sanitize_text_field()` is one of the most-called functions in all of WordPress and WooCommerce — used on checkout fields, form inputs, admin option fields, REST payloads, etc. This filter means **every one of those values gets HTML-entity-encoded on top of whatever sanitization it already had** (e.g. a customer name containing an apostrophe becomes literal `&#039;` text, ampersands become `&amp;`, and so on) — and it does **not** protect against SQL injection at all (WordPress already parameterizes queries via `$wpdb->prepare()`; `htmlspecialchars()` is the wrong tool for that job regardless). This should be removed; if genuine extra hardening is wanted it belongs on specific inputs, not globally on a core sanitization function.

2. **Fatal-error risk: call to an undefined function on the product page.**
   `single-product.php:216`: `echo wp_kses_post(wrap_non_farsi_start($attribute_data['value']));` — `wrap_non_farsi_start()` is called but is **not defined anywhere** in the theme (confirmed by a full-repo grep). This code path only executes for a non-taxonomy custom product attribute, so it may not have been hit yet in production, but any product with such an attribute will currently throw a PHP fatal error (`Call to undefined function`) and break the entire product page.

3. **Abandoned "Collector" feature — dead code left in place.**
   `page-home.php` queries `get_posts(['post_type' => 'collector', …])` (line 18), but no `collector` post type is ever registered anywhere in the theme — the query always returns empty. Its corresponding `<section class="section-collector">…</section>` markup is commented out (lines 155‑177), and its admin field tab ("Collector", title/subtitle) in `PagePost.php` is inert since nothing renders it. This looks like a half-finished or removed feature that was never fully cleaned up.

4. **"Certificate" section on the About page is also dead:** the About page's admin editor still has a "Certificate" tab with title/items fields (`PagePost.php:552‑598`), but the matching markup in `page-about.php` is entirely commented out (lines 165‑190) — editors can fill in fields that have no visible effect.

5. **Duplicated, drift-prone data: hardcoded option lists vs. live taxonomies.**
   `page-custom.php` hardcodes shape/color/design option arrays that duplicate the `pa_shape`/`pa_color`/`pa_design` attribute taxonomies used everywhere else on the site. If a color or design term is added/renamed/removed in `Products → Attributes`, this custom-rug-builder form will silently fall out of sync.

6. **Possible duplicate Google Tag Manager / GA4 injection.**
   `header.php` hardcodes a full GTM snippet (`GTM-M7RWW8NW`) and a GA4 `gtag.js` snippet (`G-9Z19TJJLV4`, matching the `GOOGLE_TAG_ID` constant in `App/Define.php`) directly in the `<head>` — **and** the `duracelltomi-google-tag-manager` (GTM4WP) plugin is also installed, whose entire purpose is to inject a GTM container itself. If GTM4WP is configured with a container ID, the site may be loading GTM twice (once hardcoded, once via the plugin), which can double-count analytics events. Worth checking the plugin's active configuration against the hardcoded snippet.

7. **REST API is globally locked down to logged-in admins** (`App/Controller/WPCustom.php:31,241`: `disable_rest_api()` returns a 403 for anyone who isn't `manage_options` and logged in, hooked to `rest_authentication_errors`). This is a very broad restriction — it will also block the **WooCommerce Store API** (used by the cart/checkout blocks) and any REST-dependent functionality in **Google Listings & Ads** and **Pinterest for WooCommerce**, both of which are installed and typically rely on REST endpoints/OAuth callbacks. Worth confirming this is intentional and that those integrations still work end-to-end.

8. **Mislabeled/ineffective security control.** `restrict_php_file_access()` (`WPCustom.php:302`) tries to block direct PHP execution under `/wp-content/uploads/` and `/wp-includes/` via a WordPress `template_redirect` hook — but a request for `evil.php` sitting in `uploads/` is served directly by the web server and never reaches WordPress's request lifecycle, so this hook can never actually fire for its intended purpose. Real protection for this needs to happen at the web-server level (`.htaccess`/nginx rule), not in a WordPress action hook.

9. **Hardcoded username-based access control (`'manager'`).** Both `Core/Setup.php` (`remove_menus()`) and `App/Helpers.php:125` (`hide_manager_user()`) special-case a literal username `'manager'` instead of checking a role/capability. If that account is ever renamed, recreated, or a second admin needs the same treatment, this logic silently stops working with no error.

10. **Third-party CDN dependency loads the *development* build of React in production.** `modules/dressmycrib/dressmycrib-plugin.php` loads `https://unpkg.com/react@18/umd/react.development.js` (and `react-dom`) — the development build is larger, slower, and console-noisy; it should be the `production.min.js` build (or self-hosted) for live traffic. This also introduces an unpinned external CDN dependency (`@18` floats to the latest 18.x) on the product page.

11. **`woocommerce-services` (WooCommerce Shipping & Tax, v2.8.9) may be running against a discontinued/superseded service** — WooCommerce.com restructured this offering in 2024 in favor of the standalone "WooCommerce Shipping" plugin. Worth verifying this plugin is still actively supported/functional rather than a legacy holdover nobody has revisited.

12. **`.vscode/sftp.json` contains a plaintext SFTP password** for the live `dev.qali.art` deployment account, sitting in the project's working directory (currently untracked by git, but on disk in the clear). **Recommend rotating this credential and never committing this file** — this was flagged and addressed as a side-finding during this review, independent of the code itself.

13. **Minor**: no `Text Domain:`/`Domain Path:` header in `style.css` (see §4) — cosmetic, doesn't break functionality since the domain is hardcoded correctly elsewhere, but is a WordPress packaging best-practice gap.

14. **Hardcoded application secrets committed to source.** Two live-looking credentials sit directly in PHP source rather than in an environment variable or wp-config constant (values intentionally not reproduced here):
    - `App/Controller/CustomPay.php:8` — a `private const SECRET_KEY` used at line 403 to HMAC-sign custom-rug payment payloads (`hash_hmac('sha256', $payload, self::SECRET_KEY)`). Anyone with source access can forge valid signatures.
    - `App/Controller/Notification.php:30` — `$this->sms_password` hardcoded, used at line ~1393 as the password field in an SMS-gateway API request.
    Recommend rotating both and moving them to `wp-config.php` constants or environment variables.

15. **Dead/no-op logic in `Core/Setup.php`.**
    - Line 111: `if (strpos('page-id-', $class) === true)` — the needle/haystack arguments are swapped (should be `strpos($class, 'page-id-')`), and `strpos()` never returns the boolean `true` (only an integer offset or `false`), so this condition can never be true and the branch never executes. A `preg_match`-based loop directly below appears to cover the same intent, so the visible impact is likely masked, but the line itself is broken.
    - Line 33: `remove_action('wp_head', 'ls_meta_generator', 9);` — `ls_meta_generator` isn't a real WordPress core hook callback (core's is `wp_generator`); this looks like leftover boilerplate from a different theme/framework and does nothing here.

16. **Two vendored helper libraries in `includes/` are loaded on every single request but never used.** `includes/date_jalali/` (Gregorian↔Jalali date conversion — `gregorian_to_jalali()`/`jalali_to_gregorian()`) and `includes/hashids/` (`hash_id()` helper) are both required unconditionally via `includes/init.php`, but a full-repo grep finds no call sites for either outside their own definitions. `Core/Helpers.php` has its own separate `miladi_to_shamsi()`/`shamsi_to_mildai()` wrappers that appear to be used instead — these two vendored libraries are pure dead weight and can likely be removed.
