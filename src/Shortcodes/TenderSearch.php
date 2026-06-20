<?php

namespace TendersaForWp\Shortcodes;

use TendersaForWp\Api\Client;
use TendersaForWp\Api\Endpoints;

class TenderSearch
{
    public static function render($atts): string
    {
        $atts = shortcode_atts([
            'placeholder' => __('Search tenders...', 'tendersa-for-wp'),
            'ajax'        => 'false',
            'limit'       => 10,
        ], $atts, 'tendersa_search');

        $search = sanitize_text_field($_GET['tendersa_q'] ?? '');
        ob_start();
        ?>
        <div class="tendersa-search">
            <form method="get" class="tendersa-search-form">
                <input type="search" name="tendersa_q" value="<?php echo esc_attr($search); ?>"
                       placeholder="<?php echo esc_attr($atts['placeholder']); ?>"
                       class="tendersa-search-input" />
                <button type="submit" class="tendersa-search-btn"><?php esc_html_e('Search', 'tendersa-for-wp'); ?></button>
            </form>
            <?php if ($search) : ?>
                <div class="tendersa-search-results">
                    <?php
                    $client = new Client();
                    $result = $client->get(Endpoints::TENDERS_SEARCH, ['q' => $search, 'limit' => absint($atts['limit'])]);
                    if (is_wp_error($result)) {
                        echo '<p class="tendersa-error">' . esc_html($result->get_error_message()) . '</p>';
                    } elseif (empty($result['data'])) {
                        echo '<p>' . esc_html__('No results found.', 'tendersa-for-wp') . '</p>';
                    } else {
                        echo '<ul class="tendersa-search-list">';
                        foreach ($result['data'] as $tender) {
                            echo '<li><a href="#tender-' . esc_attr($tender['id'] ?? '') . '">'
                                . esc_html($tender['title'] ?? '')
                                . '</a> <span class="tendersa-date">' . esc_html($tender['closing_date'] ?? '') . '</span></li>';
                        }
                        echo '</ul>';
                    }
                    ?>
                </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
