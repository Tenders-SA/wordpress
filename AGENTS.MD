# Tenders-SA for WordPress — Agent Instructions

**Path**: `wordpress/` | **Published**: [WordPress.org](https://wordpress.org/plugins/tendersa-for-wp/) | **Repo**: [github.com/Tenders-SA/wordpress](https://github.com/Tenders-SA/wordpress)

## What It Is
Native WordPress plugin wrapping the Tenders-SA Developer API v2 (`api.tenders-sa.org`). 16 shortcodes, sidebar widget, template override system, and full API coverage across all endpoints.

## Quick Start
```bash
# Development: symlink or clone into wp-content/plugins/tendersa-for-wp
# No build step required — pure PHP
```

## Key Files
| File | Purpose |
|------|---------|
| `tendersa-for-wp.php` | Plugin header, constants, autoloader, bootstrap |
| `src/Plugin.php` | Main plugin class: hooks, init, i18n, asset registration |
| `src/Api/Client.php` | HTTP client wrapping `wp_remote_get` with Bearer auth |
| `src/Api/Auth.php` | API key resolver (constant > option) |
| `src/Api/Endpoints.php` | Endpoint constants, parameter builders |
| `src/Cache/TendersaCache.php` | Transient-based response cache |
| `src/Admin/SettingsPage.php` | Settings page with test connection |
| `src/shortcode-processor.php` | Shared rendering helpers (template locate, error, sanitize) |
| `src/Shortcodes/TenderList.php` | `[tendersa_list]` |
| `src/Shortcodes/TenderDetail.php` | `[tendersa_detail]` |
| `src/Shortcodes/TenderSearch.php` | `[tendersa_search]` |
| `src/Shortcodes/TenderCounts.php` | `[tendersa_counts]` |
| `src/Shortcodes/ProvinceList.php` | `[tendersa_provinces]` |
| `src/Shortcodes/AwardList.php` | `[tendersa_awards]` |
| `src/Shortcodes/AwardAnalytics.php` | `[tendersa_award_analytics]` |
| `src/Shortcodes/CompanyProfile.php` | `[tendersa_company]` |
| `src/Shortcodes/OrganizationList.php` | `[tendersa_organizations]` |
| `src/Shortcodes/DirectorSearch.php` | `[tendersa_directors]` |
| `src/Shortcodes/RestrictedSupplier.php` | `[tendersa_restricted_supplier]` |
| `src/Shortcodes/IntelItems.php` | `[tendersa_intel]` |
| `src/Shortcodes/CipcProfile.php` | `[tendersa_cipc]` |
| `src/Shortcodes/ServiceList.php` | `[tendersa_services]` |
| `src/Shortcodes/IndustryBenchmarks.php` | `[tendersa_benchmarks]` |
| `src/Shortcodes/ArticleList.php` | `[tendersa_articles]` |
| `src/Shortcodes/NewsletterList.php` | `[tendersa_newsletters]` |
| `src/Widgets/TendersaWidget.php` | Sidebar WP_Widget |
| `src/Widgets/TendersaStatsWidget.php` | Stats dashboard widget |
| `src/Templates/tender-list.php` | Default list template |
| `src/Templates/tender-detail.php` | Default detail template |
| `src/Templates/award-list.php` | Default award template |
| `src/Templates/company-profile.php` | Default company template |
| `src/Templates/closing-soon.php` | Closing-soon list template |
| `src/Templates/counts.php` | Counts display template |
| `src/Templates/search-form.php` | Search form template |
| `src/Templates/search-results.php` | Search results template |
| `src/Templates/intel-item.php` | Intel item template |
| `src/Templates/restricted-supplier.php` | Restricted supplier template |
| `src/Templates/parts/tender-card.php` | Reusable card partial |
| `src/Templates/parts/pagination.php` | Pagination partial |
| `src/Templates/parts/filter-bar.php` | Filter bar partial |
| `src/Templates/parts/rate-limit-notice.php` | Rate limit notice partial |
| `docs/design.md` | Full plugin design document |
| `readme.txt` | WordPress.org plugin readme |

## Architecture Notes
- **Pure PHP** — no npm, no Node, no build step. WordPress OOP standards.
- **Vanilla OOP PHP 7.4+** — namespaced classes, no framework dependency
- **3 render modes**: shortcode (inline), widget (sidebar), template tag (theme PHP)
- **Theme override**: `theme/tendersa-templates/` — same pattern as WooCommerce
- **Transient cache** — `wp_cache` / `set_transient`, configurable TTL
- **Rate-limit aware** — reads `X-RateLimit-Remaining`, throttles before 429
- **API key**: Stored in `wp_options` (or `TENDERSA_API_KEY` constant in `wp-config.php`)
- **No JavaScript framework** — progressive enhancement, optional vanilla JS for Ajax search
- **Security**: nonces on all admin forms, escaped output, sanitized inputs

## API Dependencies
- Base URL: `https://api.tenders-sa.org/v2`
- Auth: `Authorization: Bearer tsa_prod_<key>`
- Public endpoints (no key needed): `/v2/meta/status`, `/v2/meta/health`, `/v2/meta/provinces`, `/v2/meta/categories`
- Rate limits: tier-dependent (test: 50/day, professional: 500/day, enterprise: 10000/day)
- See `docs/design.md` for full endpoint coverage matrix

## Key Design Decisions
1. No PHP framework — vanilla OOP with WordPress coding standards
2. Shortcodes over blocks first — Gutenberg blocks wrap shortcodes in Phase 2
3. Transients over custom DB tables — simpler, uses WordPress built-in caching
4. Template override via `tendersa-templates/` directory — standard WP pattern
5. API key from main app (tenders-sa.org/developers/api-keys), not direct from Cloudflare

## Plugin States
- **Not configured**: Shows admin notice "Enter your Tenders-SA API key to get started"
- **Configured + active**: All shortcodes, widgets, and template tags functional
- **Rate-limited**: Dashboard notice with reset time; throttled requests
- **Invalid key**: Single admin notice; all API calls return clear error

## Published
- v1.0.0 (planned) — 16 shortcodes, widget, template system, full API coverage
