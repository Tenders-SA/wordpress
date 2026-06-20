<?php

namespace TendersaForWp\Shortcodes;

use TendersaForWp\Api\Client;
use TendersaForWp\Api\Endpoints;

class IndustryBenchmarks
{
    public static function render($atts): string
    {
        $atts = shortcode_atts([
            'industry' => '',
        ], $atts, 'tendersa_benchmarks');

        $client = new Client();
        $params = !empty($atts['industry']) ? ['q' => $atts['industry']] : [];
        $result = $client->get(Endpoints::BENCHMARKS_LIST, $params);

        ob_start();
        if (is_wp_error($result)) {
            echo '<p class="tendersa-error">' . esc_html($result->get_error_message()) . '</p>';
        } elseif (!empty($result['data'])) {
            echo '<table class="tendersa-benchmarks"><thead><tr>
                <th>' . esc_html__('Industry', 'tendersa-for-wp') . '</th>
                <th>' . esc_html__('Metric', 'tendersa-for-wp') . '</th>
                <th>' . esc_html__('Value', 'tendersa-for-wp') . '</th>
            </tr></thead><tbody>';
            foreach ($result['data'] as $b) {
                echo '<tr>';
                echo '<td>' . esc_html($b['industry'] ?? '') . '</td>';
                echo '<td>' . esc_html($b['metric'] ?? $b['name'] ?? '') . '</td>';
                echo '<td>' . esc_html($b['value'] ?? $b['benchmark_value'] ?? '') . '</td>';
                echo '</tr>';
            }
            echo '</tbody></table>';
        }
        return ob_get_clean();
    }
}
