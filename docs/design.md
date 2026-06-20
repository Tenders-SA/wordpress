# WordPress Plugin: Tenders-SA for WordPress

> **Status**: Design Draft v1.0
> **API Base**: `https://api.tenders-sa.org/v2`
> **Auth**: Bearer token (`tsa_prod_<key>`)
> **Plugin Slug**: `tendersa-for-wp`

---

## 1. Overview

A WordPress plugin that integrates the full Tenders-SA Developer API (v2) into WordPress. Publishers can display South African tender data anywhere on their site — sidebar widgets, full-page listings, single tender detail pages — using shortcodes, widgets, and template tags.

The plugin handles its own API key lifecycle: users sign up at `tenders-sa.org`, create an API key via our developer dashboard (which syncs to the Cloudflare `dev-api-worker` D1), and paste it into the plugin settings. No key is stored in WordPress without explicit user action.

---

## 2. Architecture

```
┌─────────────────────────────┐
│      WordPress Site          │
│  ┌───────────────────────┐  │
│  │  tendersa-for-wp       │  │
│  │  ┌─────────────────┐  │  │
│  │  │ Settings Page    │  │  │  Admin: API key, cache TTL, templates
│  │  ├─────────────────┤  │  │
│  │  │ API Client       │  │  │  HTTP transport, rate-limit aware
│  │  ├─────────────────┤  │  │
│  │  │ Cache Layer      │  │  │  Transient-based (wp_cache)
│  │  ├─────────────────┤  │  │
│  │  │ Shortcodes       │  │  │  [tendersa_list], [tendersa_detail], etc.
│  │  ├─────────────────┤  │  │
│  │  │ Widget (WP_Widget)│  │  │  Sidebar widget
│  │  ├─────────────────┤  │  │
│  │  │ Template Tags    │  │  │  tendersa_render_list(), etc.
│  │  ├─────────────────┤  │  │
│  │  │ Gutenberg Blocks │  │  │  (phase 2)
│  │  └─────────────────┘  │  │
│  └───────────────────────┘  │
│              │               │
│              ▼               │
│    ┌──────────────────┐     │
│    │ PHP HTTP API     │     │
│    │ (wp_remote_get)  │     │
│    └────────┬─────────┘     │
└─────────────┼───────────────┘
              │
              ▼
  ┌────────────────────────┐
  │  api.tenders-sa.org    │  Cloudflare dev-api-worker (D1)
  │  /v2/*                 │
  │  Auth: Bearer tsa_*    │
  └────────────────────────┘
```

---

## 3. Plugin Structure

```
tendersa-for-wp/
├── tendersa-for-wp.php          # Plugin header + bootstrap
├── readme.txt                   # WordPress.org readme
├── assets/
│   ├── icon-128x128.png
│   ├── icon-256x256.png
│   ├── banner-772x250.png
│   └── screenshot-1.png
├── src/
│   ├── Plugin.php               # Main plugin class
│   ├── Admin/
│   │   ├── SettingsPage.php     # Settings page (API key, cache, defaults)
│   │   └── HelpPage.php         # Usage docs + shortcode generator
│   ├── Api/
│   │   ├── Client.php           # HTTP client (wp_remote_get wrapper)
│   │   ├── Auth.php             # API key management
│   │   └── Endpoints.php        # Endpoint constants + parameter builders
│   ├── Cache/
│   │   └── TendersaCache.php    # Transient-based cache layer
│   ├── Shortcodes/
│   │   ├── TenderList.php       # [tendersa_list]
│   │   ├── TenderDetail.php     # [tendersa_detail id="..."]
│   │   ├── TenderSearch.php     # [tendersa_search]
│   │   ├── TenderCounts.php     # [tendersa_counts type="province"]
│   │   ├── TenderMap.php        # [tendersa_map] (provinces)
│   │   ├── AwardList.php        # [tendersa_awards]
│   │   ├── AwardAnalytics.php   # [tendersa_award_analytics]
│   │   ├── CompanyProfile.php   # [tendersa_company name="..."]
│   │   ├── OrganizationList.php # [tendersa_organizations]
│   │   ├── DirectorSearch.php   # [tendersa_directors]
│   │   ├── RestrictedSupplier.php# [tendersa_restricted_supplier]
│   │   ├── IntelItems.php       # [tendersa_intel]
│   │   ├── CipcProfile.php      # [tendersa_cipc]
│   │   ├── ServiceList.php      # [tendersa_services]
│   │   ├── IndustryBenchmarks.php# [tendersa_benchmarks]
│   │   ├── ArticleList.php      # [tendersa_articles]
│   │   └── NewsletterList.php   # [tendersa_newsletters]
│   ├── Widgets/
│   │   ├── TendersaWidget.php   # Sidebar widget (WP_Widget)
│   │   └── TendersaStatsWidget.php # Stats widget
│   ├── Templates/
│   │   ├── tender-list.php      # Default list template (overridable)
│   │   ├── tender-detail.php    # Default detail template
│   │   ├── award-list.php
│   │   └── company-profile.php
│   └── shortcode-processor.php  # Shared rendering helpers
└── languages/                   # i18n / .pot files
```

---

## 4. API Client (`src/Api/Client.php`)

- Uses `wp_remote_get()` with `Authorization: Bearer {key}` header
- Respects `X-RateLimit-Remaining`, `X-RateLimit-Reset` headers
- Returns parsed JSON or `WP_Error`
- Supports pagination (page/limit params)
- Timeout: 15s default, configurable
- User-Agent: `tendersa-wp-plugin/{version}`

```php
class Client {
    private string $api_key;
    private string $base_url = 'https://api.tenders-sa.org/v2';
    private int $timeout = 15;

    public function get(string $path, array $params = []): array|\WP_Error {
        $url = add_query_arg($params, "$this->base_url/$path");
        $response = wp_remote_get($url, [
            'headers' => [
                'Authorization' => "Bearer $this->api_key",
                'Accept' => 'application/json',
                'User-Agent' => 'tendersa-wp-plugin/' . TENDERSA_VERSION,
            ],
            'timeout' => $this->timeout,
        ]);
        // check WP_Error, parse JSON, check success field
        // update rate-limit transient from headers
    }
}
```

### Rate-Limit Awareness

After every request, store `X-RateLimit-Remaining` in a site transient. When remaining < 10% of limit, throttle requests with `wp_sleep()` or queue them. Display a dashboard notice when the key is near its limit.

---

## 5. API Key Registration Flow

The plugin does **NOT** handle user registration or key creation itself. Instead:

1. User clicks "Get API Key" on the plugin settings page
2. Redirected to `https://tenders-sa.org/developers/api-keys` (main app)
3. User signs up / logs in on the Tenders-SA platform
4. User creates an API key in our developer dashboard
   - Main app stores it in `UserApiKey` (PostgreSQL)
   - Main app syncs it via `api-key-sync.service.ts` → `POST /api/v2/sync/keys` to Cloudflare D1
5. User copies the key and pastes it into the WordPress plugin settings
6. Plugin stores it in `wp_options` (option name: `tendersa_api_key`)

**Security**: The API key is stored as plaintext in `wp_options` — same as every other WP plugin that needs a credential (Mailchimp, Stripe, etc.). We recommend users set `WP_CONFIG_TENDERSA_KEY` in `wp-config.php` for site-level setups.

```php
// In wp-config.php:
define('TENDERSA_API_KEY', 'tsa_prod_xxxx');
// Plugin checks constant first, falls back to wp_options.
```

---

## 6. Settings Page (`src/Admin/SettingsPage.php`)

Registered under **Settings → Tenders-SA**.

| Field | Type | Default | Description |
|-------|------|---------|-------------|
| API Key | password | — | `tsa_prod_*` key from developer dashboard |
| Cache TTL | number (seconds) | 300 | How long to cache API responses |
| Default limit | number | 10 | Items per page/shortcode |
| Default sort | select | `-closing_date` | `title`, `-closing_date`, `+closing_date`, `estimated_value` |
| Province filter | select | (all) | Filter tenders by province |
| Category filter | select | (all) | Filter tenders by category |
| Display: show dates | checkbox | true | Show closing date |
| Display: show value | checkbox | true | Show estimated value |
| Display: show province | checkbox | true | Show province badge |
| Display: show excerpt | checkbox | true | Show description excerpt |

Plus a **Test Connection** button that hits `GET /v2/meta/status` and shows the result.

---

## 7. Shortcodes

### 7.1 `[tendersa_list]`

Display a paginated list of tenders.

```
[tendersa_list limit="10" province="Gauteng" category="Construction"
  status="active" sort="-closing_date" show_filters="true"
  closing_after="2026-01-01" template="compact"]
```

**Attributes**: All `GET /v2/tenders` params + `template`, `show_filters`, `show_pagination`.

### 7.2 `[tendersa_detail id="..."]`

Render a single tender detail page.

```
[tendersa_detail id="TS-12345" show_analysis="true" show_documents="true"]
```

Uses `GET /v2/tenders/{id}` + sub-resources inline (awards, documents, analysis, timeline, value-estimate). Renders as a self-contained detail card.

### 7.3 `[tendersa_search]`

Embed a search box + results. Ajax-driven (optional) or on-submit redirect.

```
[tendersa_search placeholder="Search tenders..." ajax="true"]
```

### 7.4 `[tendersa_counts type="province"]`

Show tender counts grouped by province, category, organization, or status.

```
[tendersa_counts type="province" max="9" show_numbers="true"]
[tendersa_counts type="category" max="10"]
```

### 7.5 `[tendersa_closing_soon]`

Tenders closing within 7 days.

```
[tendersa_closing_soon limit="5" days="14" show_countdown="true"]
```

### 7.6 `[tendersa_awards]`

List awards with filtering.

```
[tendersa_awards limit="10" supplier="Acme Corp" province="Gauteng"]
```

### 7.7 `[tendersa_award_analytics type="province"]`

Award analytics breakdown by province, category, B-BBEE level, or enterprise type.

### 7.8 `[tendersa_company name="..."]`

Display a company/supplier profile.

```
[tendersa_company name="Acme Corp" show_awards="true" show_directors="true"]
```

### 7.9 `[tendersa_organizations]`

List procuring organizations.

```
[tendersa_organizations limit="20" type="municipality"]
```

### 7.10 `[tendersa_directors]`

Search or list directors.

```
[tendersa_directors search="John" limit="5"]
```

### 7.11 `[tendersa_restricted_supplier name="..."]`

Check a supplier against the restricted suppliers database.

```
[tendersa_restricted_supplier name="Acme Corp" show_details="true"]
```

### 7.12 `[tendersa_intel]`

Display intelligence items (market intel).

```
[tendersa_intel limit="5" source="government-gazette"]
```

### 7.13 `[tendersa_cipc registration="..."]`

Display CIPC company enrichment data.

```
[tendersa_cipc registration="2020/123456/07"]
```

### 7.14 `[tendersa_services]`

List available service types.

### 7.15 `[tendersa_benchmarks]`

Industry benchmarks.

```
[tendersa_benchmarks industry="Construction"]
```

### 7.16 `[tendersa_articles]`

SEO articles from the Tenders-SA content database.

### 7.17 `[tendersa_newsletters]`

Newsletter archive.

### 7.18 `[tendersa_provinces]`

Interactive province list with tender counts.

### 7.19 `[tendersa_pipeline]`

Full procurement-pipeline view: tender → awards → contracts → milestones (single tender).

---

## 8. Widget

Register a `Tendersa_Widget` extending `WP_Widget`.

**Widget form fields**:
- Title
- Display mode: List / Closing Soon / Stats / Counts by Province
- Limit (1-20)
- Province filter
- Category filter
- Show excerpt
- Cache TTL override

**Renders** in sidebar using the same template system as shortcodes.

Register a second `Tendersa_Stats_Widget` for quick stats (total tenders, total awards, active tenders).

---

## 9. Template System

Templates live in `src/Templates/`. They are plain PHP files that receive an array of data.

Users can override templates by creating `tendersa-templates/` in their theme directory:

```
theme/tendersa-templates/
├── tender-list.php
├── tender-detail.php
├── award-list.php
├── company-profile.php
├── closing-soon.php
├── counts.php
├── search-form.php
├── search-results.php
├── intel-item.php
├── restricted-supplier.php
└── parts/
    ├── tender-card.php       # Reusable single-tender card
    ├── pagination.php
    ├── filter-bar.php
    └── rate-limit-notice.php
```

Plugin checks `theme/tendersa-templates/` first, falls back to its own `src/Templates/`.

---

## 10. Caching Strategy

| Scope | Key | TTL (default) | Storage |
|-------|-----|--------------|---------|
| Shortcode output | `tendersa_sc_{hash(params)}` | 300s | Transient |
| API response | `tendersa_api_{path}_{hash(params)}` | 300s | Transient |
| Rate-limit snapshot | `tendersa_rate_limit` | 60s | Transient |
| Province list | `tendersa_provinces` | 3600s | Transient |
| Category list | `tendersa_categories` | 3600s | Transient |

Cache is invalidated when:
- API key changes (full flush)
- Cache TTL setting changes
- Manual "Flush Cache" button on settings page
- `wp cron` daily cleanup of expired tendersa transients

---

## 11. Endpoint Coverage Matrix

| # | Resource | Endpoints | Shortcode(s) | Phase |
|---|----------|-----------|-------------|-------|
| 1 | Tenders | List, search, closing-soon, new, by-province, by-org, by-category, by-type, value-range, detail + 11 sub-resources (awards, contracts, milestones, docs, bidders, submission-reqs, timeline, analysis, value-estimate, seo, slug, related) + 4 counts | `[tendersa_list]`, `[tendersa_detail]`, `[tendersa_search]`, `[tendersa_closing_soon]`, `[tendersa_counts]`, `[tendersa_pipeline]` | P1 |
| 2 | Awards | List, detail, analytics (4 dims), by-tender, by-supplier, by-party, by-date-range, subcontractors | `[tendersa_awards]`, `[tendersa_award_analytics]` | P1 |
| 3 | Organizations | List, search, detail, by-registration, by-slug, tenders, directors, counts-by-type | `[tendersa_organizations]` | P1 |
| 4 | Companies | List, search, top, by-registration, detail + awards, contracts, tenders, directors | `[tendersa_company]` | P1 |
| 5 | Directors | List, search, detail, by-organization | `[tendersa_directors]` | P1 |
| 6 | Categories | List, detail, by-slug | (inline in `[tendersa_list]`) | P2 |
| 7 | Provinces | List, detail, health-scores | `[tendersa_provinces]` | P2 |
| 8 | SEO / Articles | List articles, article detail, author detail, category-seo, province-seo | `[tendersa_articles]` | P2 |
| 9 | Industry Benchmarks | List, detail | `[tendersa_benchmarks]` | P2 |
| 10 | Services | List, detail | `[tendersa_services]` | P2 |
| 11 | OCDS | List parties, party detail | — | P3 |
| 12 | Intel | Sources, items (list, detail) | `[tendersa_intel]` | P2 |
| 13 | Forensic | List restricted, detail, match, check | `[tendersa_restricted_supplier]` | P2 |
| 14 | CIPC | Enrichments, directors (list, detail) | `[tendersa_cipc]` | P3 |
| 15 | Newsletters | List, detail | `[tendersa_newsletters]` | P3 |
| 16 | Documents | Detail, download-url | (inline in `[tendersa_detail]`) | P2 |
| 17 | Meta / Public | Status, health, provinces, categories, OpenAPI spec, docs | — | P1 |

---

## 12. Security

- API key stored in `wp_options` (option name obfuscated: `tendersa_apk`)
- Input validation: all shortcode attributes sanitized via `sanitize_text_field()`, `absint()`, etc.
- Output escaping: all tender data passed through `esc_html()`, `esc_url()`, `esc_attr()`
- Nonce on settings form (`wp_nonce_field('tendersa_settings')`)
- No unfiltered HTML from the API — all rich content (AI summaries) is escaped
- Rate-limit awareness prevents 429 abuse
- `wp_remote_get` timeout and SSL verification enabled (`sslverify` = `true`)

---

## 13. Publishing to WordPress.org

### Requirements
- Readme compliant with WordPress.org standards (`readme.txt`)
- Plugin slug: `tendersa-for-wp`
- Tags: `tenders`, `procurement`, `south-africa`, `government`, `ggbee`
- i18n: `.pot` file, all strings wrapped in `__()`, `_e()`
- SVN: Plugin submitted via WordPress.org plugin submission
- Assets: icon (256×256, 128×128), banner (772×250), screenshot (1200×900)

### readme.txt sections
- Contributors: `tendersa`
- Donate link: `https://tenders-sa.org/pricing`
- Tags: `tenders`, `procurement`, `south africa`, `government`, `ggbee`, `etenders`
- Requires at least: `5.8`
- Tested up to: `6.5`
- Stable tag: `1.0.0`
- License: GPLv2+
- License URI: https://www.gnu.org/licenses/gpl-2.0.html

### Changelog
```
= 1.0.0 =
* Initial release
* 16 shortcodes covering all Tenders-SA API v2 endpoints
* Sidebar widget with list/closing-soon/stats modes
* Template override system for theme developers
* Transient-based caching with configurable TTL
* Rate-limit awareness and dashboard notices
* Full settings page with test connection button
* Gutenberg block support (phase 2)
```

---

## 14. Build & Release Pipeline

```
npm run build:wp-plugin
  ├── Compile PHP (copy src/ → build/)
  ├── Minify assets (CSS/JS in assets/)
  ├── Generate .pot file
  ├── Package as .zip
  └── Ready for manual upload or SVN commit
```

For SVN deployment:
```
svn co https://plugins.svn.wordpress.org/tendersa-for-wp svn/
cp -r build/* svn/trunk/
svn add svn/trunk/* --force
svn ci -m "v1.0.0"
svn cp svn/trunk svn/tags/1.0.0
svn ci -m "Tag v1.0.0"
```

---

## 15. Implementation Phases

| Phase | Scope | Est. Effort |
|-------|-------|-------------|
| P1 | Plugin skeleton, settings, API client, cache, `[tendersa_list]`, `[tendersa_detail]`, `[tendersa_closing_soon]`, `[tendersa_search]`, `[tendersa_counts]`, sidebar widget, template system | ~40h |
| P2 | Awards, companies, organizations, directors, provinces, articles, intel, forensic, documents, industry benchmarks, services shortcodes + widgets | ~30h |
| P3 | CIPC, newsletters, OCDS, Gutenberg blocks, full-page template integration (page-builder + theme JSON), SEO schema | ~20h |
| P4 | Multi-site compat, WP-CLI commands (`wp tendersa list`, `wp tendersa flush`), advanced cache (object cache drop-in), i18n, WP.org submission | ~15h |

---

## 16. Key Technical Decisions

1. **No PHP framework** — Vanilla OOP PHP with WordPress coding standards (compatible with PHP 7.4+)
2. **No npm/Node dependency** — Pure PHP plugin; CSS uses WordPress admin styles
3. **Shortcodes over blocks first** — Blocks can wrap shortcodes in phase 2
4. **Transients over custom DB tables** — Simpler, uses WordPress's built-in caching infrastructure
5. **Theme template override via `tendersa-templates/` directory** — Common WordPress pattern (cf. WooCommerce, EDD)
6. **No JavaScript framework** — Progressive enhancement. Search shortcode offers native form submit (no JS) or optional Ajax (vanilla JS, no React/Vue)
7. **API key from main app, not direct from Cloudflare** — The main app handles user auth, tier limits, and syncs to the Cloudflare Worker D1. The plugin doesn't need to know about Cloudflare at all.
