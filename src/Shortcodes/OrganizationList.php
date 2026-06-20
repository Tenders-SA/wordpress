<?php

namespace TendersaForWp\Shortcodes;

use TendersaForWp\Api\Client;
use TendersaForWp\Api\Endpoints;

class OrganizationList
{
    public static function render($atts): string
    {
        $atts = shortcode_atts([
            'limit' => 20,
            'type'  => '',
        ], $atts, 'tendersa_organizations');

        $client = new Client();
        $params = array_filter([
            'limit' => absint($atts['limit']),
            'type'  => $atts['type'],
        ], fn($v) => $v !== '');

        $result = $client->get(Endpoints::ORGS_LIST, $params);

        ob_start();
        if (is_wp_error($result)) {
            echo '<p class="tendersa-error">' . esc_html($result->get_error_message()) . '</p>';
        } elseif (empty($result['data'])) {
            echo '<p>' . esc_html__('No organizations found.', 'tendersa-for-wp') . '</p>';
        } else {
            echo '<ul class="tendersa-orgs">';
            foreach ($result['data'] as $org) {
                echo '<li><strong>' . esc_html($org['name'] ?? '') . '</strong>';
                if (!empty($org['tender_count'])) {
                    echo ' <span class="tendersa-count">(' . esc_html($org['tender_count']) . ' ' . esc_html__('tenders', 'tendersa-for-wp') . ')</span>';
                }
                echo '</li>';
            }
            echo '</ul>';
        }
        return ob_get_clean();
    }
}
