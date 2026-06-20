<?php

namespace TendersaForWp\Widgets;

use TendersaForWp\Api\Client;
use TendersaForWp\Api\Endpoints;
use TendersaForWp\Api\Auth;

class TendersaStatsWidget extends \WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'tendersa_stats_widget',
            __('Tenders-SA Stats', 'tendersa-for-wp'),
            ['description' => __('Quick stats from the Tenders-SA platform.', 'tendersa-for-wp')]
        );
    }

    public function widget($args, $instance): void
    {
        if (!Auth::is_configured()) return;

        echo $args['before_widget'];

        $title = apply_filters('widget_title', $instance['title'] ?? __('Tenders-SA Stats', 'tendersa-for-wp'));
        if (!empty($title)) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }

        $client = new Client();
        $result = $client->get(Endpoints::META_STATUS);
        $data = !is_wp_error($result) ? ($result['data'] ?? []) : [];

        echo '<div class="tendersa-stats-grid">';

        $stats = [
            'total_tenders' => __('Total Tenders', 'tendersa-for-wp'),
            'active_tenders' => __('Active', 'tendersa-for-wp'),
            'total_awards' => __('Awards', 'tendersa-for-wp'),
            'total_organizations' => __('Organizations', 'tendersa-for-wp'),
            'total_companies' => __('Suppliers', 'tendersa-for-wp'),
        ];

        foreach ($stats as $key => $label) {
            $val = $data[$key] ?? null;
            if ($val !== null) {
                echo '<div class="tendersa-stat">';
                echo '<span class="tendersa-stat-value">' . esc_html(number_format_i18n((int)$val)) . '</span>';
                echo '<span class="tendersa-stat-label">' . esc_html($label) . '</span>';
                echo '</div>';
            }
        }

        echo '</div>';
        echo $args['after_widget'];
    }

    public function form($instance): void
    {
        $title = $instance['title'] ?? __('Tenders-SA Stats', 'tendersa-for-wp');
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'tendersa-for-wp'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <?php
    }

    public function update($new_instance, $old_instance): array
    {
        $instance = $old_instance;
        $instance['title'] = sanitize_text_field($new_instance['title'] ?? '');
        return $instance;
    }
}
