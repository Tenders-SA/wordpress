<?php

namespace TendersaForWp\Shortcodes;

use TendersaForWp\Api\Client;
use TendersaForWp\Api\Endpoints;

class IntelItems
{
    public static function render($atts): string
    {
        $atts = shortcode_atts([
            'limit'  => 5,
            'source' => '',
        ], $atts, 'tendersa_intel');

        $client = new Client();
        $params = array_filter([
            'limit'  => absint($atts['limit']),
            'source' => $atts['source'],
        ], fn($v) => $v !== '');

        $result = $client->get(Endpoints::INTEL_ITEMS_LIST, $params);

        ob_start();
        if (is_wp_error($result)) {
            echo '<p class="tendersa-error">' . esc_html($result->get_error_message()) . '</p>';
        } elseif (empty($result['data'])) {
            echo '<p>' . esc_html__('No intelligence items.', 'tendersa-for-wp') . '</p>';
        } else {
            echo '<ul class="tendersa-intel">';
            foreach ($result['data'] as $item) {
                echo '<li>';
                echo '<strong>' . esc_html($item['title'] ?? '') . '</strong>';
                if (!empty($item['published_date'])) {
                    echo ' <span class="tendersa-date">' . esc_html($item['published_date']) . '</span>';
                }
                if (!empty($item['summary'])) {
                    echo '<p class="tendersa-excerpt">' . esc_html(wp_trim_words($item['summary'], 30)) . '</p>';
                }
                echo '</li>';
            }
            echo '</ul>';
        }
        return ob_get_clean();
    }
}
