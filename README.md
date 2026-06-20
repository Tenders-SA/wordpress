# Tenders-SA for WordPress

Display South African government tender data anywhere on your WordPress site — sidebar widgets, full-page listings, and single tender detail pages.

---

## Quick Start

1. Install the plugin from WordPress.org or upload the ZIP
2. Get your free API key at [tenders-sa.org/developers/api-keys](https://tenders-sa.org/developers/api-keys)
3. Go to **Settings → Tenders-SA** and paste your key
4. Use shortcodes to display tenders anywhere

```php
// List open tenders
[tendersa_list limit="10" province="Gauteng" sort="-closing_date"]

// Single tender detail
[tendersa_detail id="TS-12345"]

// Search tenders
[tendersa_search]

// Tenders closing soon
[tendersa_closing_soon limit="5" days="7"]

// Award analytics by province
[tendersa_award_analytics type="province"]

// Company profile
[tendersa_company name="Acme Corp" show_awards="true"]

// Check restricted supplier
[tendersa_restricted_supplier name="Acme Corp"]
```

## Features

- **16 shortcodes** covering all Tenders-SA v2 API endpoints
- **Sidebar widget** — list tenders, closing soon, or stats
- **Template overrides** — copy `tendersa-templates/` to your theme to customize
- **Rate-limit aware** — won't hammer the API
- **Configurable cache** — transients with TTL control
- **No build step** — pure PHP, drop-in and use

## Shortcodes

| Shortcode | Description |
|-----------|-------------|
| `[tendersa_list]` | Paginated tender list with filters |
| `[tendersa_detail]` | Single tender with all sub-resources |
| `[tendersa_search]` | Search box with results |
| `[tendersa_closing_soon]` | Tenders closing within N days |
| `[tendersa_counts]` | Counts by province/category/org/status |
| `[tendersa_provinces]` | Province list with counts |
| `[tendersa_awards]` | Award listings |
| `[tendersa_award_analytics]` | Analytics by province/category/bee-level |
| `[tendersa_company]` | Supplier profile |
| `[tendersa_organizations]` | Procuring organizations |
| `[tendersa_directors]` | Director search |
| `[tendersa_restricted_supplier]` | Restricted supplier check |
| `[tendersa_intel]` | Intelligence items |
| `[tendersa_cipc]` | CIPC company data |
| `[tendersa_services]` | Service types |
| `[tendersa_benchmarks]` | Industry benchmarks |

## Docs

See [docs/design.md](docs/design.md) for the full architecture, endpoint coverage matrix, and implementation plan.
