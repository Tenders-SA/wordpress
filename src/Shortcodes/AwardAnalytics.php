<?php

namespace TendersaForWp\Shortcodes;

use TendersaForWp\Api\Client;
use TendersaForWp\Api\Endpoints;

class AwardAnalytics
{
    private const DIMENSIONS = [
        'province'       => Endpoints::AWARDS_ANALYTICS_PROV,
        'category'       => Endpoints::AWARDS_ANALYTICS_CAT,
        'bee-level'      => Endpoints::AWARDS_ANALYTICS_BEE,
        'enterprise-type' => Endpoints::AWARDS_ANALYTICS_ENT,
    ];

    public static function render($atts): string
    {
        $atts = shortcode_atts([
            'type'  => 'province',
            'limit' => 10,
        ], $atts, 'tendersa_award_analytics');

        $dimension = in_array($atts['type'], array_keys(self::DIMENSIONS), true) ? $atts['type'] : 'province';
        $endpoint = self::DIMENSIONS[$dimension];

        $client = new Client();
        $result = $client->get($endpoint);

        ob_start();
        if (is_wp_error($result)) {
            echo '<p class="tendersa-error">' . esc_html($result->get_error_message()) . '</p>';
        } elseif (!empty($result['data'])) {
            echo '<table class="tendersa-analytics-table"><thead><tr>
                <th>' . esc_html(ucwords(str_replace('-', ' ', $dimension))) . '</th>
                <th>' . esc_html__('Award Value', 'tendersa-for-wp') . '</th>
                <th>' . esc_html__('Count', 'tendersa-for-wp') . '</th>
            </tr></thead><tbody>';
            foreach (array_slice($result['data'], 0, absint($atts['limit'])) as $row) {
                echo '<tr>';
                echo '<td>' . esc_html($row['name'] ?? $row['label'] ?? '') . '</td>';
                echo '<td>' . esc_html(!empty($row['total_value']) ? 'R ' . number_format_i18n((float)$row['total_value']) : '') . '</td>';
                echo '<td>' . esc_html($row['count'] ?? 0) . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        }
        return ob_get_clean();
    }
}
