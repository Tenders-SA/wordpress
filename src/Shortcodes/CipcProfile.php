<?php

namespace TendersaForWp\Shortcodes;

use TendersaForWp\Api\Client;
use TendersaForWp\Api\Endpoints;

class CipcProfile
{
    public static function render($atts): string
    {
        $atts = shortcode_atts([
            'registration' => '',
        ], $atts, 'tendersa_cipc');

        $reg = sanitize_text_field($atts['registration']);
        if (empty($reg)) {
            return '<p class="tendersa-error">' . esc_html__('Registration number is required.', 'tendersa-for-wp') . '</p>';
        }

        $client = new Client();
        $result = $client->get(Endpoints::build(Endpoints::CIPC_ENRICHMENT_GET, $reg));

        ob_start();
        if (is_wp_error($result)) {
            echo '<p class="tendersa-error">' . esc_html($result->get_error_message()) . '</p>';
        } elseif (!empty($result['data'])) {
            $data = $result['data'];
            echo '<div class="tendersa-cipc">';
            echo '<h3>' . esc_html($data['company_name'] ?? $reg) . '</h3>';
            echo '<p><strong>' . esc_html__('Registration:', 'tendersa-for-wp') . '</strong> ' . esc_html($data['registration_number'] ?? $reg) . '</p>';
            if (!empty($data['status'])) {
                echo '<p><strong>' . esc_html__('Status:', 'tendersa-for-wp') . '</strong> ' . esc_html($data['status']) . '</p>';
            }
            if (!empty($data['directors'])) {
                echo '<h4>' . esc_html__('Directors', 'tendersa-for-wp') . '</h4><ul>';
                foreach ($data['directors'] as $d) {
                    echo '<li>' . esc_html($d['name'] ?? $d['director_name'] ?? '') . '</li>';
                }
                echo '</ul>';
            }
            echo '</div>';
        }
        return ob_get_clean();
    }
}
