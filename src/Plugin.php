<?php

namespace TendersaForWp;

use TendersaForWp\Admin\SettingsPage;
use TendersaForWp\Api\Auth;

class Plugin
{
    private static ?Plugin $instance = null;
    private ?SettingsPage $settings_page = null;

    public static function init(): void
    {
        if (self::$instance) return;
        self::$instance = new self();
        self::$instance->register_hooks();
    }

    public static function activate(): void
    {
        $defaults = [
            'tendersa_cache_ttl'          => 300,
            'tendersa_default_limit'      => 10,
            'tendersa_default_sort'       => '-closing_date',
            'tendersa_province_filter'    => '',
            'tendersa_category_filter'    => '',
            'tendersa_show_dates'         => 1,
            'tendersa_show_value'         => 1,
            'tendersa_show_province'      => 1,
            'tendersa_show_excerpt'       => 1,
        ];
        foreach ($defaults as $key => $value) {
            if (get_option($key) === false) {
                update_option($key, $value);
            }
        }
    }

    public static function deactivate(): void
    {
        wp_unschedule_hook('tendersa_daily_cache_cleanup');
    }

    private function register_hooks(): void
    {
        add_action('admin_menu', [$this, 'register_admin_menu']);
        add_action('admin_enqueue_scripts', [$this, 'admin_styles']);
        add_action('admin_notices', [$this, 'maybe_show_key_notice']);

        if (!Auth::resolve()) {
            return;
        }

        $this->register_shortcodes();
        $this->register_widgets();

        add_action('wp_enqueue_scripts', [$this, 'frontend_styles']);

        if (!wp_next_scheduled('tendersa_daily_cache_cleanup')) {
            wp_schedule_event(time(), 'daily', 'tendersa_daily_cache_cleanup');
        }
        add_action('tendersa_daily_cache_cleanup', [Cache\TendersaCache::class, 'cleanup']);
    }

    public function register_admin_menu(): void
    {
        $this->settings_page = new SettingsPage();
        $this->settings_page->register();
    }

    public function admin_styles(string $hook): void
    {
        if (strpos($hook, 'tendersa') === false) return;
        wp_enqueue_style('tendersa-admin', TENDERSA_PLUGIN_URL . 'assets/admin.css', [], TENDERSA_VERSION);
    }

    public function frontend_styles(): void
    {
        if (has_shortcode(get_post()->post_content ?? '', 'tendersa_list') ||
            has_shortcode(get_post()->post_content ?? '', 'tendersa_detail') ||
            has_shortcode(get_post()->post_content ?? '', 'tendersa_search') ||
            has_shortcode(get_post()->post_content ?? '', 'tendersa_closing_soon') ||
            is_active_widget(false, false, 'tendersa_widget', true)) {
            wp_enqueue_style('tendersa-frontend', TENDERSA_PLUGIN_URL . 'assets/frontend.css', [], TENDERSA_VERSION);
        }
    }

    public function maybe_show_key_notice(): void
    {
        if (!current_user_can('manage_options')) return;

        $screen = get_current_screen();
        if ($screen && strpos($screen->id, 'tendersa') !== false) return;

        if (!Auth::resolve()) {
            $url = admin_url('options-general.php?page=tendersa-settings');
            echo '<div class="notice notice-warning is-dismissible"><p>';
            printf(
                wp_kses_post(__('Tenders-SA: Enter your <a href="%s">API key</a> to start displaying tender data.', 'tendersa-for-wp')),
                esc_url($url)
            );
            echo '</p></div>';
        }
    }

    private function register_shortcodes(): void
    {
        $shortcodes = [
            'tendersa_list'             => Shortcodes\TenderList::class,
            'tendersa_detail'           => Shortcodes\TenderDetail::class,
            'tendersa_search'           => Shortcodes\TenderSearch::class,
            'tendersa_counts'           => Shortcodes\TenderCounts::class,
            'tendersa_closing_soon'     => Shortcodes\ClosingSoon::class,
            'tendersa_awards'           => Shortcodes\AwardList::class,
            'tendersa_award_analytics'  => Shortcodes\AwardAnalytics::class,
            'tendersa_company'          => Shortcodes\CompanyProfile::class,
            'tendersa_organizations'    => Shortcodes\OrganizationList::class,
            'tendersa_directors'        => Shortcodes\DirectorSearch::class,
            'tendersa_restricted_supplier' => Shortcodes\RestrictedSupplier::class,
            'tendersa_intel'            => Shortcodes\IntelItems::class,
            'tendersa_cipc'             => Shortcodes\CipcProfile::class,
            'tendersa_services'         => Shortcodes\ServiceList::class,
            'tendersa_benchmarks'       => Shortcodes\IndustryBenchmarks::class,
            'tendersa_articles'         => Shortcodes\ArticleList::class,
            'tendersa_newsletters'      => Shortcodes\NewsletterList::class,
            'tendersa_provinces'        => Shortcodes\ProvinceList::class,
            'tendersa_pipeline'         => Shortcodes\TenderPipeline::class,
        ];

        foreach ($shortcodes as $tag => $class) {
            add_shortcode($tag, [$class, 'render']);
        }
    }

    private function register_widgets(): void
    {
        add_action('widgets_init', function () {
            register_widget(Widgets\TendersaWidget::class);
            register_widget(Widgets\TendersaStatsWidget::class);
        });
    }
}
