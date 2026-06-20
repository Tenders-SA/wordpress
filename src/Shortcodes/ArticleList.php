<?php

namespace TendersaForWp\Shortcodes;

use TendersaForWp\Api\Client;
use TendersaForWp\Api\Endpoints;

class ArticleList
{
    public static function render($atts): string
    {
        $atts = shortcode_atts([
            'limit' => 5,
        ], $atts, 'tendersa_articles');

        $client = new Client();
        $result = $client->get(Endpoints::ARTICLES_LIST, ['limit' => absint($atts['limit'])]);

        ob_start();
        if (is_wp_error($result)) {
            echo '<p class="tendersa-error">' . esc_html($result->get_error_message()) . '</p>';
        } elseif (!empty($result['data'])) {
            echo '<ul class="tendersa-articles">';
            foreach ($result['data'] as $article) {
                echo '<li>';
                echo '<strong>' . esc_html($article['title'] ?? '') . '</strong>';
                if (!empty($article['published_date'])) {
                    echo ' <span class="tendersa-date">' . esc_html($article['published_date']) . '</span>';
                }
                if (!empty($article['excerpt'])) {
                    echo '<p class="tendersa-excerpt">' . esc_html($article['excerpt']) . '</p>';
                }
                echo '</li>';
            }
            echo '</ul>';
        }
        return ob_get_clean();
    }
}
