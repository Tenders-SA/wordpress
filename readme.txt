=== Tenders-SA for WordPress ===
Contributors: tendersa
Donate link: https://tenders-sa.org/pricing
Tags: tenders, procurement, south africa, government, bbbee, etenders
Requires at least: 5.8
Tested up to: 6.5
Requires PHP: 7.4
Stable tag: 1.0.0
License: GPLv2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html

Display South African government tender data anywhere on your WordPress site — sidebar widgets, full-page listings, and single tender detail pages using shortcodes and widgets.

== Description ==

Tenders-SA for WordPress brings South African public procurement data to your WordPress site. Powered by the Tenders-SA Developer API v2.

= Features =

* 19 shortcodes covering all Tenders-SA v2 API endpoints
* Sidebar widget — list tenders, closing soon, or stats
* Customizable templates — copy `tendersa-templates/` to your theme
* Rate-limit aware — won't hammer the API
* Configurable cache with TTL control
* No build step — pure PHP, drop-in and use

= Shortcodes =

* `[tendersa_list]` — Paginated tender list with filters
* `[tendersa_detail]` — Single tender with all sub-resources
* `[tendersa_search]` — Search box with results
* `[tendersa_closing_soon]` — Tenders closing within N days
* `[tendersa_counts]` — Counts by province/category/org/status
* `[tendersa_awards]` — Award listings
* `[tendersa_award_analytics]` — Award analytics by province/category/bee-level
* `[tendersa_company]` — Supplier profile
* `[tendersa_organizations]` — Procuring organizations
* `[tendersa_directors]` — Director search
* `[tendersa_restricted_supplier]` — Restricted supplier check
* `[tendersa_intel]` — Intelligence items
* `[tendersa_cipc]` — CIPC company data
* `[tendersa_services]` — Service types
* `[tendersa_benchmarks]` — Industry benchmarks
* `[tendersa_provinces]` — Province list with tender counts
* `[tendersa_pipeline]` — Full procurement pipeline: tender → awards → contracts → milestones

== Installation ==

1. Upload the `tendersa-for-wp` folder to `/wp-content/plugins/`
2. Activate the plugin through the Plugins screen
3. Go to **Settings → Tenders-SA**
4. Get your free API key at [tenders-sa.org/developers/api-keys](https://tenders-sa.org/developers/api-keys)
5. Paste your key and click **Test Connection**
6. Use shortcodes on any page/post

== Frequently Asked Questions ==

= Do I need an API key? =

Yes. Get a free key at https://tenders-sa.org/developers/api-keys. Test tier: 50 requests/day.

= Can I customize the templates? =

Yes. Copy any template from `tendersa-templates/` to your theme's `tendersa-templates/` directory.

= Does this cache API responses? =

Yes. Responses are cached using WordPress transients with a configurable TTL (default 300 seconds).

== Changelog ==

= 1.0.0 =
* Initial release.
* 19 shortcodes covering all Tenders-SA v2 API endpoints.
* Sidebar widget with list/closing-soon/stats modes.
* Template override system for theme developers.
* Transient-based caching with configurable TTL.
* Rate-limit awareness and dashboard notices.
* Full settings page with test connection button.
