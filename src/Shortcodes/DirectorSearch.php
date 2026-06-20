<?php

namespace TendersaForWp\Shortcodes;

use TendersaForWp\Api\Client;
use TendersaForWp\Api\Endpoints;

class DirectorSearch
{
    public static function render($atts): string
    {
        $atts = shortcode_atts([
            'search' => '',
            'limit'  => 10,
        ], $atts, 'tendersa_directors');

        $search = sanitize_text_field($atts['search'] ?: ($_GET['tendersa_director'] ?? ''));
        $client = new Client();

        ob_start();

        echo '<form method="get" class="tendersa-director-form">';
        echo '<input type="search" name="tendersa_director" value="' . esc_attr($search) . '" placeholder="' . esc_attr__('Search directors...', 'tendersa-for-wp') . '" />';
        echo '<button type="submit">' . esc_html__('Search', 'tendersa-for-wp') . '</button>';
        echo '</form>';

        if ($search) {
            $result = $client->get(Endpoints::DIRECTORS_SEARCH, ['q' => $search, 'limit' => absint($atts['limit'])]);
            if (is_wp_error($result)) {
                echo '<p class="tendersa-error">' . esc_html($result->get_error_message()) . '</p>';
            } elseif (empty($result['data'])) {
                echo '<p>' . esc_html__('No directors found.', 'tendersa-for-wp') . '</p>';
            } else {
                echo '<ul class="tendersa-directors">';
                foreach ($result['data'] as $d) {
                    echo '<li>' . esc_html($d['name'] ?? '') . '</li>';
                }
                echo '</ul>';
            }
        }

        return ob_get_clean();
    }
}
