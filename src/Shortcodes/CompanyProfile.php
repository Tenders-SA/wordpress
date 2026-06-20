<?php

namespace TendersaForWp\Shortcodes;

use TendersaForWp\Api\Client;
use TendersaForWp\Api\Endpoints;

class CompanyProfile
{
    public static function render($atts): string
    {
        $atts = shortcode_atts([
            'name'          => '',
            'show_awards'   => 'true',
            'show_contracts' => 'false',
            'show_tenders'  => 'false',
            'show_directors' => 'true',
        ], $atts, 'tendersa_company');

        if (empty($atts['name'])) {
            return '<p class="tendersa-error">' . esc_html__('Company name is required.', 'tendersa-for-wp') . '</p>';
        }

        $client = new Client();
        $name = sanitize_text_field($atts['name']);
        $result = $client->get(Endpoints::build(Endpoints::COMPANY_GET, $name));

        ob_start();
        if (is_wp_error($result)) {
            echo '<p class="tendersa-error">' . esc_html($result->get_error_message()) . '</p>';
        } elseif (!empty($result['data'])) {
            $company = $result['data'];
            echo '<div class="tendersa-company">';
            echo '<h3>' . esc_html($company['name'] ?? $name) . '</h3>';
            if (!empty($company['registration_number'])) {
                echo '<p><strong>' . esc_html__('Reg:', 'tendersa-for-wp') . '</strong> ' . esc_html($company['registration_number']) . '</p>';
            }
            if (!empty($company['bbbee_level'])) {
                echo '<p><strong>' . esc_html__('B-BBEE:', 'tendersa-for-wp') . '</strong> Level ' . esc_html($company['bbbee_level']) . '</p>';
            }
            echo '</div>';

            if ($atts['show_directors'] !== 'false') {
                $directors = $client->get_data(Endpoints::build(Endpoints::COMPANY_DIRECTORS, $name));
                if ($directors) {
                    echo '<h4>' . esc_html__('Directors', 'tendersa-for-wp') . '</h4><ul class="tendersa-directors">';
                    foreach ($directors as $d) {
                        echo '<li>' . esc_html($d['name'] ?? '') . '</li>';
                    }
                    echo '</ul>';
                }
            }
        }
        return ob_get_clean();
    }
}
