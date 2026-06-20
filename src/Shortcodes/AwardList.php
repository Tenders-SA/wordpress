<?php

namespace TendersaForWp\Shortcodes;

use TendersaForWp\Api\Client;
use TendersaForWp\Api\Endpoints;

class AwardList
{
    public static function render($atts): string
    {
        $atts = shortcode_atts([
            'limit'    => 10,
            'supplier' => '',
            'province' => '',
            'category' => '',
        ], $atts, 'tendersa_awards');

        $client = new Client();
        $params = array_filter([
            'limit'    => absint($atts['limit']),
            'supplier' => $atts['supplier'],
            'province' => $atts['province'],
            'category' => $atts['category'],
        ], fn($v) => $v !== '');

        $result = $client->get(Endpoints::AWARDS_LIST, $params);

        ob_start();
        if (is_wp_error($result)) {
            echo '<p class="tendersa-error">' . esc_html($result->get_error_message()) . '</p>';
        } elseif (empty($result['data'])) {
            echo '<p>' . esc_html__('No awards found.', 'tendersa-for-wp') . '</p>';
        } else {
            echo '<table class="tendersa-awards-table"><thead><tr>
                <th>' . esc_html__('Supplier', 'tendersa-for-wp') . '</th>
                <th>' . esc_html__('Value', 'tendersa-for-wp') . '</th>
                <th>' . esc_html__('Date', 'tendersa-for-wp') . '</th>
            </tr></thead><tbody>';
            foreach ($result['data'] as $award) {
                echo '<tr>';
                echo '<td>' . esc_html($award['supplier_name'] ?? '') . '</td>';
                echo '<td>' . esc_html(!empty($award['award_value']) ? 'R ' . number_format_i18n((float)$award['award_value']) : '') . '</td>';
                echo '<td>' . esc_html($award['award_date'] ?? '') . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        }
        return ob_get_clean();
    }
}
