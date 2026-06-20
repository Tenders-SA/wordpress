<?php

namespace TendersaForWp\Shortcodes;

use TendersaForWp\Api\Client;
use TendersaForWp\Api\Endpoints;

class ProvinceList
{
    public static function render($atts): string
    {
        $atts = shortcode_atts([
            'show_health' => 'false',
        ], $atts, 'tendersa_provinces');

        $client = new Client();
        $result = $client->get(Endpoints::META_PROVINCES);

        ob_start();
        if (is_wp_error($result)) {
            echo '<p class="tendersa-error">' . esc_html($result->get_error_message()) . '</p>';
        } elseif (!empty($result['data'])) {
            echo '<ul class="tendersa-provinces">';
            foreach ($result['data'] as $province) {
                echo '<li>';
                echo '<strong>' . esc_html($province['name'] ?? '') . '</strong>';
                if (isset($province['count'])) {
                    echo ' <span class="tendersa-count">(' . esc_html($province['count']) . ' ' . esc_html__('tenders', 'tendersa-for-wp') . ')</span>';
                }
                echo '</li>';
            }
            echo '</ul>';
        }
        return ob_get_clean();
    }
}
