<?php

namespace TendersaForWp\Admin;

use TendersaForWp\Api\Auth;
use TendersaForWp\Api\Client;
use TendersaForWp\Cache\TendersaCache;

class SettingsPage
{
    private const PAGE_SLUG = 'tendersa-settings';
    private const OPTION_GROUP = 'tendersa_settings';

    public function register(): void
    {
        add_options_page(
            __('Tenders-SA', 'tendersa-for-wp'),
            __('Tenders-SA', 'tendersa-for-wp'),
            'manage_options',
            self::PAGE_SLUG,
            [$this, 'render']
        );

        add_action('admin_init', [$this, 'register_settings']);
        add_action('admin_post_tendersa_test_connection', [$this, 'handle_test_connection']);
        add_action('admin_post_tendersa_flush_cache', [$this, 'handle_flush_cache']);
    }

    public function register_settings(): void
    {
        register_setting(self::OPTION_GROUP, 'tendersa_apk', [
            'sanitize_callback' => [$this, 'sanitize_api_key'],
        ]);

        $fields = [
            'tendersa_cache_ttl'       => 'absint',
            'tendersa_default_limit'   => 'absint',
            'tendersa_default_sort'    => 'sanitize_text_field',
            'tendersa_province_filter' => 'sanitize_text_field',
            'tendersa_category_filter' => 'sanitize_text_field',
            'tendersa_show_dates'      => 'absint',
            'tendersa_show_value'      => 'absint',
            'tendersa_show_province'   => 'absint',
            'tendersa_show_excerpt'    => 'absint',
        ];

        foreach ($fields as $key => $cb) {
            register_setting(self::OPTION_GROUP, $key, ['sanitize_callback' => $cb]);
        }

        add_settings_section('tendersa_main', __('API Configuration', 'tendersa-for-wp'), null, self::PAGE_SLUG);
        add_settings_section('tendersa_display', __('Display Defaults', 'tendersa-for-wp'), null, self::PAGE_SLUG);
        add_settings_section('tendersa_cache_section', __('Cache', 'tendersa-for-wp'), null, self::PAGE_SLUG);

        add_settings_field('tendersa_apk', __('API Key', 'tendersa-for-wp'), [$this, 'field_api_key'], self::PAGE_SLUG, 'tendersa_main');
        add_settings_field('tendersa_default_limit', __('Default Items Per Page', 'tendersa-for-wp'), [$this, 'field_text'], self::PAGE_SLUG, 'tendersa_display', ['key' => 'tendersa_default_limit', 'type' => 'number', 'min' => 1, 'max' => 100]);
        add_settings_field('tendersa_default_sort', __('Default Sort Order', 'tendersa-for-wp'), [$this, 'field_sort'], self::PAGE_SLUG, 'tendersa_display');
        add_settings_field('tendersa_province_filter', __('Default Province Filter', 'tendersa-for-wp'), [$this, 'field_text'], self::PAGE_SLUG, 'tendersa_display', ['key' => 'tendersa_province_filter']);
        add_settings_field('tendersa_show_dates', __('Show Closing Dates', 'tendersa-for-wp'), [$this, 'field_checkbox'], self::PAGE_SLUG, 'tendersa_display', ['key' => 'tendersa_show_dates']);
        add_settings_field('tendersa_show_value', __('Show Estimated Value', 'tendersa-for-wp'), [$this, 'field_checkbox'], self::PAGE_SLUG, 'tendersa_display', ['key' => 'tendersa_show_value']);
        add_settings_field('tendersa_show_province', __('Show Province', 'tendersa-for-wp'), [$this, 'field_checkbox'], self::PAGE_SLUG, 'tendersa_display', ['key' => 'tendersa_show_province']);
        add_settings_field('tendersa_show_excerpt', __('Show Description Excerpt', 'tendersa-for-wp'), [$this, 'field_checkbox'], self::PAGE_SLUG, 'tendersa_display', ['key' => 'tendersa_show_excerpt']);
        add_settings_field('tendersa_cache_ttl', __('Cache TTL (seconds)', 'tendersa-for-wp'), [$this, 'field_text'], self::PAGE_SLUG, 'tendersa_cache_section', ['key' => 'tendersa_cache_ttl', 'type' => 'number', 'min' => 30, 'max' => 86400]);
    }

    public function render(): void
    {
        if (!current_user_can('manage_options')) return;
        ?>
        <div class="wrap">
            <h1><?php esc_html_e('Tenders-SA for WordPress', 'tendersa-for-wp'); ?></h1>
            <form action="options.php" method="post">
                <?php
                settings_fields(self::OPTION_GROUP);
                do_settings_sections(self::PAGE_SLUG);
                submit_button();
                ?>
            </form>
            <hr>
            <h2><?php esc_html_e('Actions', 'tendersa-for-wp'); ?></h2>
            <p>
                <a href="<?php echo esc_url(admin_url('admin-post.php?action=tendersa_test_connection')); ?>" class="button">
                    <?php esc_html_e('Test Connection', 'tendersa-for-wp'); ?>
                </a>
                <a href="<?php echo esc_url(admin_url('admin-post.php?action=tendersa_flush_cache')); ?>" class="button">
                    <?php esc_html_e('Flush Cache', 'tendersa-for-wp'); ?>
                </a>
                <a href="https://tenders-sa.org/developers/api-keys" target="_blank" class="button button-secondary">
                    <?php esc_html_e('Get API Key', 'tendersa-for-wp'); ?> &rarr;
                </a>
            </p>
            <?php $this->render_shortcode_reference(); ?>
        </div>
        <?php
    }

    public function field_api_key(): void
    {
        $key = Auth::resolve();
        $placeholder = $key ? str_repeat('•', 8) . substr($key, -4) : '';
        ?>
        <input type="password" name="tendersa_apk" value="<?php echo $key ? esc_attr($key) : ''; ?>"
               placeholder="<?php echo esc_attr($placeholder); ?>" class="regular-text" autocomplete="off" />
        <p class="description">
            <?php esc_html_e('Your API key from', 'tendersa-for-wp'); ?>
            <a href="https://tenders-sa.org/developers/api-keys" target="_blank">tenders-sa.org/developers/api-keys</a>.
            <?php esc_html_e('Or set', 'tendersa-for-wp'); ?> <code>define('TENDERSA_API_KEY', 'tsa_prod_...');</code>
            <?php esc_html_e('in wp-config.php', 'tendersa-for-wp'); ?>
        </p>
        <?php
    }

    public function field_text(array $args): void
    {
        $key = $args['key'] ?? '';
        $type = $args['type'] ?? 'text';
        $min  = $args['min'] ?? null;
        $max  = $args['max'] ?? null;
        $value = get_option($key, '');
        $attrs = 'min="' . esc_attr($min) . '" max="' . esc_attr($max) . '"';
        echo '<input type="' . esc_attr($type) . '" name="' . esc_attr($key) . '" value="' . esc_attr($value) . '" class="small-text" ' . $attrs . ' />';
    }

    public function field_checkbox(array $args): void
    {
        $key = $args['key'] ?? '';
        $value = get_option($key, 0);
        echo '<input type="checkbox" name="' . esc_attr($key) . '" value="1" ' . checked(1, $value, false) . ' />';
    }

    public function field_sort(): void
    {
        $current = get_option('tendersa_default_sort', '-closing_date');
        $options = [
            '-closing_date'   => __('Closing Date (newest first)', 'tendersa-for-wp'),
            '+closing_date'   => __('Closing Date (oldest first)', 'tendersa-for-wp'),
            '-publication_date' => __('Publication Date (newest)', 'tendersa-for-wp'),
            '+publication_date' => __('Publication Date (oldest)', 'tendersa-for-wp'),
            'title'           => __('Title (A-Z)', 'tendersa-for-wp'),
            '-estimated_value' => __('Value (highest first)', 'tendersa-for-wp'),
            '+estimated_value' => __('Value (lowest first)', 'tendersa-for-wp'),
        ];
        echo '<select name="tendersa_default_sort">';
        foreach ($options as $val => $label) {
            echo '<option value="' . esc_attr($val) . '" ' . selected($current, $val, false) . '>' . esc_html($label) . '</option>';
        }
        echo '</select>';
    }

    public function sanitize_api_key($value): string
    {
        $trimmed = trim($value);
        if (empty($trimmed)) return '';
        if (!str_starts_with($trimmed, 'tsa_prod_')) {
            add_settings_error('tendersa_apk', 'invalid_key', __('API key must start with tsa_prod_', 'tendersa-for-wp'));
            return get_option('tendersa_apk', '');
        }
        return sanitize_text_field($trimmed);
    }

    public function handle_test_connection(): void
    {
        if (!wp_verify_nonce($_REQUEST['_wpnonce'] ?? '', 'tendersa_test_connection')) {
            wp_die('Invalid request');
        }

        $key = Auth::resolve();
        if (!$key) {
            wp_redirect(add_query_arg('tendersa_message', 'no_key', wp_get_referer()));
            exit;
        }

        $result = Client::test_connection($key);
        $status = is_wp_error($result) ? 'connection_failed&error=' . urlencode($result->get_error_message()) : 'connection_ok';

        wp_redirect(add_query_arg('tendersa_message', $status, wp_get_referer()));
        exit;
    }

    public function handle_flush_cache(): void
    {
        if (!wp_verify_nonce($_REQUEST['_wpnonce'] ?? '', 'tendersa_flush_cache')) {
            wp_die('Invalid request');
        }
        TendersaCache::flush();
        wp_redirect(add_query_arg('tendersa_message', 'cache_flushed', wp_get_referer()));
        exit;
    }

    private function render_shortcode_reference(): void
    {
        ?>
        <hr>
        <h2><?php esc_html_e('Shortcode Reference', 'tendersa-for-wp'); ?></h2>
        <table class="widefat striped" style="max-width:800px">
            <thead><tr><th><?php esc_html_e('Shortcode', 'tendersa-for-wp'); ?></th><th><?php esc_html_e('Description', 'tendersa-for-wp'); ?></th></tr></thead>
            <tbody>
            <tr><td><code>[tendersa_list]</code></td><td><?php esc_html_e('Paginated tender list with filters (province, category, status, search)', 'tendersa-for-wp'); ?></td></tr>
            <tr><td><code>[tendersa_detail id="..."]</code></td><td><?php esc_html_e('Single tender with awards, documents, analysis, timeline', 'tendersa-for-wp'); ?></td></tr>
            <tr><td><code>[tendersa_search]</code></td><td><?php esc_html_e('Search box for tenders', 'tendersa-for-wp'); ?></td></tr>
            <tr><td><code>[tendersa_closing_soon]</code></td><td><?php esc_html_e('Tenders closing within N days', 'tendersa-for-wp'); ?></td></tr>
            <tr><td><code>[tendersa_counts]</code></td><td><?php esc_html_e('Tender counts by province, category, organization, or status', 'tendersa-for-wp'); ?></td></tr>
            <tr><td><code>[tendersa_awards]</code></td><td><?php esc_html_e('Awards list with filtering', 'tendersa-for-wp'); ?></td></tr>
            <tr><td><code>[tendersa_award_analytics]</code></td><td><?php esc_html_e('Award analytics by province, category, B-BBEE level, enterprise type', 'tendersa-for-wp'); ?></td></tr>
            <tr><td><code>[tendersa_company name="..."]</code></td><td><?php esc_html_e('Supplier profile with awards, contracts, tenders, directors', 'tendersa-for-wp'); ?></td></tr>
            <tr><td><code>[tendersa_organizations]</code></td><td><?php esc_html_e('Procuring organizations list', 'tendersa-for-wp'); ?></td></tr>
            <tr><td><code>[tendersa_directors]</code></td><td><?php esc_html_e('Director search', 'tendersa-for-wp'); ?></td></tr>
            <tr><td><code>[tendersa_restricted_supplier]</code></td><td><?php esc_html_e('Check supplier against restricted list', 'tendersa-for-wp'); ?></td></tr>
            <tr><td><code>[tendersa_intel]</code></td><td><?php esc_html_e('Intelligence items', 'tendersa-for-wp'); ?></td></tr>
            <tr><td><code>[tendersa_cipc]</code></td><td><?php esc_html_e('CIPC company data', 'tendersa-for-wp'); ?></td></tr>
            <tr><td><code>[tendersa_services]</code></td><td><?php esc_html_e('Service types', 'tendersa-for-wp'); ?></td></tr>
            <tr><td><code>[tendersa_benchmarks]</code></td><td><?php esc_html_e('Industry benchmarks', 'tendersa-for-wp'); ?></td></tr>
            <tr><td><code>[tendersa_provinces]</code></td><td><?php esc_html_e('Province list with tender counts', 'tendersa-for-wp'); ?></td></tr>
            <tr><td><code>[tendersa_pipeline]</code></td><td><?php esc_html_e('Full procurement pipeline: tender → awards → contracts → milestones', 'tendersa-for-wp'); ?></td></tr>
            </tbody>
        </table>
        <?php
    }

    public static function handle_admin_notices(): void
    {
        if (!isset($_GET['tendersa_message'])) return;

        $messages = [
            'connection_ok'       => ['type' => 'success', 'msg' => __('Connection successful! API is responding.', 'tendersa-for-wp')],
            'connection_failed'   => ['type' => 'error',   'msg' => __('Connection failed: ', 'tendersa-for-wp') . esc_html($_GET['error'] ?? '')],
            'cache_flushed'       => ['type' => 'success', 'msg' => __('Cache flushed.', 'tendersa-for-wp')],
            'no_key'              => ['type' => 'warning', 'msg' => __('No API key configured.', 'tendersa-for-wp')],
        ];

        $m = $messages[$_GET['tendersa_message']] ?? null;
        if ($m) {
            printf('<div class="notice notice-%s is-dismissible"><p>%s</p></div>', esc_attr($m['type']), esc_html($m['msg']));
        }
    }
}
