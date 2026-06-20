<?php

namespace TendersaForWp\Shortcodes;

use TendersaForWp\Api\Client;
use TendersaForWp\Api\Endpoints;

class RestrictedSupplier
{
    public static function render($atts): string
    {
        $atts = shortcode_atts([
            'name'         => '',
            'show_details' => 'true',
        ], $atts, 'tendersa_restricted_supplier');

        $name = sanitize_text_field($atts['name'] ?: ($_GET['tendersa_supplier'] ?? ''));
        if (empty($name)) {
            return '<form method="get" class="tendersa-supplier-form">
                <input type="search" name="tendersa_supplier" placeholder="' . esc_attr__('Check supplier name...', 'tendersa-for-wp') . '" />
                <button type="submit">' . esc_html__('Check', 'tendersa-for-wp') . '</button>
            </form>';
        }

        $client = new Client();
        $result = $client->get(Endpoints::FORENSIC_CHECK, ['q' => $name]);

        ob_start();
        if (is_wp_error($result)) {
            echo '<p class="tendersa-error">' . esc_html($result->get_error_message()) . '</p>';
        } else {
            $matched = $result['data']['matched'] ?? $result['data']['is_restricted'] ?? false;
            $details = $result['data'] ?? [];

            if ($matched) {
                echo '<div class="tendersa-alert tendersa-alert-warning">';
                echo '<strong>' . esc_html__('RESTRICTED', 'tendersa-for-wp') . '</strong> &mdash; ';
                echo esc_html(sprintf(__('"%s" appears on the restricted suppliers list.', 'tendersa-for-wp'), $name));
                echo '</div>';
            } else {
                echo '<div class="tendersa-alert tendersa-alert-success">';
                echo esc_html(sprintf(__('"%s" was not found on the restricted suppliers list.', 'tendersa-for-wp'), $name));
                echo '</div>';
            }

            if ($atts['show_details'] !== 'false' && !empty($details['restrictions'])) {
                echo '<ul>';
                foreach ($details['restrictions'] as $r) {
                    echo '<li>' . esc_html($r['restriction_type'] ?? $r['type'] ?? '') . ': ' . esc_html($r['description'] ?? '') . '</li>';
                }
                echo '</ul>';
            }
        }
        return ob_get_clean();
    }
}
