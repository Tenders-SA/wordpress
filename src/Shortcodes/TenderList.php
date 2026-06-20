<?php

namespace TendersaForWp\Shortcodes;

use TendersaForWp\Api\Client;
use TendersaForWp\Api\Endpoints;

class TenderList
{
    public static function render($atts): string
    {
        $atts = shortcode_atts([
            'limit'          => get_option('tendersa_default_limit', 10),
            'province'       => get_option('tendersa_province_filter', ''),
            'category'       => get_option('tendersa_category_filter', ''),
            'status'         => '',
            'sort'           => get_option('tendersa_default_sort', '-closing_date'),
            'q'              => '',
            'closing_after'  => '',
            'closing_before' => '',
            'min_value'      => '',
            'max_value'      => '',
            'publication_type' => '',
            'show_filters'   => 'false',
            'show_pagination' => 'true',
            'template'       => '',
        ], $atts, 'tendersa_list');

        $page = max(1, get_query_var('tendersa_page', 1));
        $params = array_filter([
            'page'          => $page,
            'limit'         => absint($atts['limit']),
            'province'      => $atts['province'],
            'category'      => $atts['category'],
            'status'        => $atts['status'],
            'sort'          => $atts['sort'],
            'q'             => $atts['q'],
            'closingAfter'  => $atts['closing_after'],
            'closingBefore' => $atts['closing_before'],
            'minValue'      => $atts['min_value'],
            'maxValue'      => $atts['max_value'],
            'publicationType' => $atts['publication_type'],
        ], fn($v) => $v !== '');

        $client = new Client();
        $result = $client->get(Endpoints::TENDERS_LIST, $params);
        if (is_wp_error($result)) {
            return self::error_html($result);
        }

        $tenders = $result['data'] ?? [];
        $meta    = $result['meta'] ?? [];
        $total   = $meta['totalCount'] ?? 0;
        $pages   = $total > 0 ? ceil($total / absint($atts['limit'])) : 1;

        $template = self::locate_template('tender-list');
        ob_start();
        if (!empty($atts['show_filters']) && $atts['show_filters'] !== 'false') {
            self::render_filter_bar($atts, $total);
        }
        if (empty($tenders)) {
            echo '<p>' . esc_html__('No tenders found.', 'tendersa-for-wp') . '</p>';
        } else {
            foreach ($tenders as $tender) {
                include $template;
            }
        }
        if (!empty($atts['show_pagination']) && $atts['show_pagination'] !== 'false' && $pages > 1) {
            self::render_pagination($page, $pages);
        }
        return ob_get_clean();
    }

    private static function render_filter_bar(array $atts, int $total): void
    {
        ?>
        <div class="tendersa-filter-bar">
            <span class="tendersa-count"><?php echo esc_html(sprintf(__('%d tenders found', 'tendersa-for-wp'), $total)); ?></span>
            <?php if (!empty($atts['province'])) : ?>
                <span class="tendersa-badge"><?php echo esc_html($atts['province']); ?></span>
            <?php endif; ?>
            <?php if (!empty($atts['category'])) : ?>
                <span class="tendersa-badge"><?php echo esc_html($atts['category']); ?></span>
            <?php endif; ?>
            <?php if (!empty($atts['status'])) : ?>
                <span class="tendersa-badge"><?php echo esc_html($atts['status']); ?></span>
            <?php endif; ?>
        </div>
        <?php
    }

    private static function render_pagination(int $current, int $total): void
    {
        $template = self::locate_template('parts/pagination');
        include $template;
    }

    private static function locate_template(string $relative): string
    {
        $theme_dir = get_stylesheet_directory() . '/tendersa-templates/';
        $plugin_dir = TENDERSA_PLUGIN_DIR . 'src/Templates/';

        $theme_file = $theme_dir . $relative . '.php';
        if (file_exists($theme_file)) return $theme_file;

        return $plugin_dir . $relative . '.php';
    }

    private static function error_html($error): string
    {
        return '<div class="tendersa-error">' . esc_html($error->get_error_message()) . '</div>';
    }
}
