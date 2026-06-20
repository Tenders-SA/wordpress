<?php

namespace TendersaForWp\Shortcodes;

use TendersaForWp\Api\Client;
use TendersaForWp\Api\Endpoints;

class ClosingSoon
{
    public static function render($atts): string
    {
        $atts = shortcode_atts([
            'limit'          => 5,
            'days'           => 7,
            'show_countdown' => 'false',
            'province'       => '',
        ], $atts, 'tendersa_closing_soon');

        $client = new Client();
        $params = ['limit' => absint($atts['limit'])];
        if (!empty($atts['province'])) {
            $params['province'] = $atts['province'];
        }

        $result = $client->get(Endpoints::TENDERS_CLOSING_SOON, $params);

        ob_start();
        if (is_wp_error($result)) {
            echo '<p class="tendersa-error">' . esc_html($result->get_error_message()) . '</p>';
        } elseif (empty($result['data'])) {
            echo '<p>' . esc_html__('No tenders closing soon.', 'tendersa-for-wp') . '</p>';
        } else {
            echo '<ul class="tendersa-closing-soon">';
            foreach ($result['data'] as $tender) {
                $days_left = $atts['show_countdown'] === 'true' && !empty($tender['closing_date'])
                    ? self::days_remaining($tender['closing_date'])
                    : '';
                echo '<li>';
                echo '<strong>' . esc_html($tender['title'] ?? '') . '</strong>';
                if (!empty($tender['closing_date'])) {
                    echo '<span class="tendersa-date">' . esc_html($tender['closing_date']) . '</span>';
                    if ($days_left !== '') {
                        echo ' <span class="tendersa-countdown">(' . esc_html($days_left) . ')</span>';
                    }
                }
                echo '</li>';
            }
            echo '</ul>';
        }
        return ob_get_clean();
    }

    private static function days_remaining(string $date): string
    {
        $now = time();
        $closing = strtotime($date);
        if (!$closing || $closing < $now) return __('Closed', 'tendersa-for-wp');
        $days = ceil(($closing - $now) / DAY_IN_SECONDS);
        return sprintf(_n('%d day left', '%d days left', $days, 'tendersa-for-wp'), $days);
    }
}
