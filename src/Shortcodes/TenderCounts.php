<?php

namespace TendersaForWp\Shortcodes;

use TendersaForWp\Api\Client;
use TendersaForWp\Api\Endpoints;

class TenderCounts
{
    private const ENDPOINTS = [
        'province'     => Endpoints::TENDERS_COUNTS_PROV,
        'category'     => Endpoints::TENDERS_COUNTS_CAT,
        'organization' => Endpoints::TENDERS_COUNTS_ORG,
        'status'       => Endpoints::TENDERS_COUNTS_STATUS,
    ];

    public static function render($atts): string
    {
        $atts = shortcode_atts([
            'type'         => 'province',
            'max'          => 10,
            'show_numbers' => 'true',
        ], $atts, 'tendersa_counts');

        $type = in_array($atts['type'], array_keys(self::ENDPOINTS), true) ? $atts['type'] : 'province';
        $endpoint = self::ENDPOINTS[$type];

        $client = new Client();
        $result = $client->get($endpoint);

        ob_start();
        if (is_wp_error($result)) {
            echo '<p class="tendersa-error">' . esc_html($result->get_error_message()) . '</p>';
        } elseif (!empty($result['data'])) {
            $items = array_slice($result['data'], 0, absint($atts['max']));
            echo '<ul class="tendersa-counts tendersa-counts-' . esc_attr($type) . '">';
            foreach ($items as $item) {
                $label = $item['name'] ?? __('Unknown', 'tendersa-for-wp');
                $count = $item['count'] ?? 0;
                echo '<li>';
                echo '<span class="tendersa-count-label">' . esc_html($label) . '</span>';
                if ($atts['show_numbers'] !== 'false') {
                    echo ' <span class="tendersa-count-number">(' . esc_html($count) . ')</span>';
                }
                echo '</li>';
            }
            echo '</ul>';
        }
        return ob_get_clean();
    }
}
