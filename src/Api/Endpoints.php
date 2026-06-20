<?php

namespace TendersaForWp\Api;

class Endpoints
{
    // Meta / Public
    public const META_STATUS           = 'meta/status';
    public const META_PROVINCES        = 'meta/provinces';
    public const META_CATEGORIES       = 'meta/categories';
    public const META_USAGE            = 'meta/usage';
    public const META_INDUSTRIES       = 'meta/industries';

    // Tenders
    public const TENDERS_LIST          = 'tenders';
    public const TENDERS_SEARCH        = 'tenders/search';
    public const TENDERS_CLOSING_SOON  = 'tenders/closing-soon';
    public const TENDERS_NEW           = 'tenders/new';
    public const TENDERS_BBBEE         = 'tenders/bbbee-required';
    public const TENDERS_VALUE_RANGE   = 'tenders/value-range';
    public const TENDERS_BY_PROVINCE   = 'tenders/by-province/%s';
    public const TENDERS_BY_ORG        = 'tenders/by-organization/%s';
    public const TENDERS_BY_CATEGORY   = 'tenders/by-category/%s';
    public const TENDERS_BY_TYPE       = 'tenders/by-publication-type/%s';
    public const TENDERS_COUNTS_PROV   = 'tenders/counts/province';
    public const TENDERS_COUNTS_CAT    = 'tenders/counts/category';
    public const TENDERS_COUNTS_ORG    = 'tenders/counts/organization';
    public const TENDERS_COUNTS_STATUS = 'tenders/counts/status';
    public const TENDER_GET            = 'tenders/%s';
    public const TENDER_AWARDS         = 'tenders/%s/awards';
    public const TENDER_CONTRACTS      = 'tenders/%s/contracts';
    public const TENDER_MILESTONES     = 'tenders/%s/milestones';
    public const TENDER_DOCUMENTS      = 'tenders/%s/documents';
    public const TENDER_BIDDERS        = 'tenders/%s/bidders';
    public const TENDER_SUBMISSION     = 'tenders/%s/submission-requirements';
    public const TENDER_TIMELINE       = 'tenders/%s/timeline';
    public const TENDER_ANALYSIS       = 'tenders/%s/analysis';
    public const TENDER_VALUE_ESTIMATE = 'tenders/%s/value-estimate';
    public const TENDER_SLUG           = 'tenders/%s/slug';
    public const TENDER_RELATED        = 'tenders/%s/related';

    // Awards
    public const AWARDS_LIST              = 'awards';
    public const AWARDS_ANALYTICS         = 'awards/analytics';
    public const AWARDS_ANALYTICS_PROV    = 'awards/analytics/province';
    public const AWARDS_ANALYTICS_CAT     = 'awards/analytics/category';
    public const AWARDS_ANALYTICS_BEE     = 'awards/analytics/bee-level';
    public const AWARDS_ANALYTICS_ENT     = 'awards/analytics/enterprise-type';
    public const AWARDS_BY_TENDER         = 'awards/by-tender/%s';
    public const AWARDS_BY_SUPPLIER       = 'awards/by-supplier/%s';
    public const AWARDS_BY_DATE_RANGE     = 'awards/by-date-range';
    public const AWARD_GET                = 'awards/%s';
    public const AWARD_SUBCONTRACTORS     = 'awards/%s/subcontractors';

    // Organizations
    public const ORGS_LIST         = 'organizations';
    public const ORGS_SEARCH       = 'organizations/search';
    public const ORGS_COUNTS_TYPE  = 'organizations/counts-by-type';
    public const ORGS_BY_REG       = 'organizations/by-registration/%s';
    public const ORGS_BY_SLUG      = 'organizations/by-slug/%s';
    public const ORG_GET           = 'organizations/%s';
    public const ORG_TENDERS       = 'organizations/%s/tenders';
    public const ORG_DIRECTORS     = 'organizations/%s/directors';

    // Companies
    public const COMPANIES_LIST    = 'companies';
    public const COMPANIES_SEARCH  = 'companies/search';
    public const COMPANIES_TOP     = 'companies/top';
    public const COMPANIES_BY_REG  = 'companies/by-registration/%s';
    public const COMPANY_GET       = 'companies/%s';
    public const COMPANY_AWARDS    = 'companies/%s/awards';
    public const COMPANY_CONTRACTS = 'companies/%s/contracts';
    public const COMPANY_TENDERS   = 'companies/%s/tenders';
    public const COMPANY_DIRECTORS = 'companies/%s/directors';

    // Directors
    public const DIRECTORS_LIST          = 'directors';
    public const DIRECTORS_SEARCH        = 'directors/search';
    public const DIRECTORS_BY_ORG        = 'directors/by-organization/%s';
    public const DIRECTOR_GET            = 'directors/%s';

    // Categories
    public const CATEGORIES_LIST = 'categories';
    public const CATEGORY_GET    = 'categories/%s';
    public const CATEGORY_BY_SLUG = 'categories/by-slug/%s';

    // Provinces
    public const PROVINCES_LIST      = 'provinces';
    public const PROVINCE_GET        = 'provinces/%s';
    public const PROVINCE_HEALTH     = 'provinces/%s/health-scores';

    // SEO
    public const SEO_CATEGORY   = 'seo/category/%s';
    public const SEO_PROVINCE   = 'seo/province/%s';
    public const ARTICLES_LIST  = 'articles';
    public const ARTICLE_GET    = 'articles/%s';
    public const AUTHOR_GET     = 'authors/%s';

    // Industry
    public const BENCHMARKS_LIST = 'industry/benchmarks';
    public const BENCHMARK_GET   = 'industry/benchmarks/%s';

    // Services
    public const SERVICES_LIST = 'services';
    public const SERVICE_GET   = 'services/%s';

    // OCDS
    public const OCDS_PARTIES_LIST = 'ocds/parties';
    public const OCDS_PARTY_GET    = 'ocds/parties/%s';

    // Intel
    public const INTEL_SOURCES_LIST = 'intel/sources';
    public const INTEL_SOURCE_GET   = 'intel/sources/%s';
    public const INTEL_ITEMS_LIST   = 'intel/items';
    public const INTEL_ITEM_GET     = 'intel/items/%s';

    // Forensic
    public const FORENSIC_LIST   = 'forensic/restricted-suppliers';
    public const FORENSIC_MATCH  = 'forensic/restricted-suppliers/match';
    public const FORENSIC_CHECK  = 'forensic/restricted-suppliers/check';
    public const FORENSIC_GET    = 'forensic/restricted-suppliers/%s';

    // CIPC
    public const CIPC_ENRICHMENTS_LIST = 'cipc/enrichments';
    public const CIPC_ENRICHMENT_GET   = 'cipc/enrichments/%s';
    public const CIPC_DIRECTORS_LIST   = 'cipc/directors';
    public const CIPC_DIRECTOR_GET     = 'cipc/directors/%s';

    // Newsletters
    public const NEWSLETTERS_LIST = 'newsletters';
    public const NEWSLETTER_GET   = 'newsletters/%s';

    // Documents
    public const DOCUMENT_GET        = 'documents/%s';
    public const DOCUMENT_DOWNLOAD   = 'documents/%s/download-url';

    public static function build(string $pattern, ...$args): string
    {
        if (empty($args)) return $pattern;
        return sprintf($pattern, ...array_map('rawurlencode', $args));
    }
}
