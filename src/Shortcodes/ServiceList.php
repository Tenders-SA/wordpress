<?php

namespace TendersaForWp\Shortcodes;

use TendersaForWp\Api\Client;
use TendersaForWp\Api\Endpoints;

class ServiceList
{
    public static function render($atts): string
    {
        $client = new Client();
        $result = $client->get(Endpoints::SERVICES_LIST);

        ob_start();
        if (is_wp_error($result)) {
            echo '<p class="tendersa-error">' . esc_html($result->get_error_message()) . '</p>';
        } elseif (!empty($result['data'])) {
            echo '<ul class="tendersa-services">';
            foreach ($result['data'] as $s) {
                echo '<li>' . esc_html($s['name'] ?? $s['service_type'] ?? '') . '</li>';
            }
            echo '</ul>';
        }
        return ob_get_clean();
    }
}
