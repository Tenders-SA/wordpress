<?php

namespace TendersaForWp\Shortcodes;

use TendersaForWp\Api\Client;
use TendersaForWp\Api\Endpoints;

class NewsletterList
{
    public static function render($atts): string
    {
        $atts = shortcode_atts([
            'limit' => 5,
        ], $atts, 'tendersa_newsletters');

        $client = new Client();
        $result = $client->get(Endpoints::NEWSLETTERS_LIST, ['limit' => absint($atts['limit'])]);

        ob_start();
        if (is_wp_error($result)) {
            echo '<p class="tendersa-error">' . esc_html($result->get_error_message()) . '</p>';
        } elseif (!empty($result['data'])) {
            echo '<ul class="tendersa-newsletters">';
            foreach ($result['data'] as $nl) {
                echo '<li>';
                echo '<strong>' . esc_html($nl['title'] ?? $nl['edition'] ?? '') . '</strong>';
                if (!empty($nl['published_date'])) {
                    echo ' <span class="tendersa-date">' . esc_html($nl['published_date']) . '</span>';
                }
                echo '</li>';
            }
            echo '</ul>';
        }
        return ob_get_clean();
    }
}
