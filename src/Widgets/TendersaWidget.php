<?php

namespace TendersaForWp\Widgets;

use TendersaForWp\Api\Client;
use TendersaForWp\Api\Endpoints;
use TendersaForWp\Api\Auth;

class TendersaWidget extends \WP_Widget
{
    public function __construct()
    {
        parent::__construct(
            'tendersa_widget',
            __('Tenders-SA', 'tendersa-for-wp'),
            ['description' => __('Display South African tender data in your sidebar.', 'tendersa-for-wp')]
        );
    }

    public function widget($args, $instance): void
    {
        if (!Auth::is_configured()) return;

        echo $args['before_widget'];

        $title = apply_filters('widget_title', $instance['title'] ?? '');
        if (!empty($title)) {
            echo $args['before_title'] . esc_html($title) . $args['after_title'];
        }

        $mode = $instance['mode'] ?? 'list';
        $limit = min(20, max(1, (int)($instance['limit'] ?? 5)));

        $client = new Client();

        switch ($mode) {
            case 'closing_soon':
                $this->render_closing_soon($client, $limit, $instance);
                break;
            case 'stats':
                $this->render_stats($client);
                break;
            case 'counts':
                $this->render_counts($client, $instance);
                break;
            default:
                $this->render_list($client, $limit, $instance);
                break;
        }

        echo $args['after_widget'];
    }

    public function form($instance): void
    {
        $title = $instance['title'] ?? __('Tenders', 'tendersa-for-wp');
        $mode = $instance['mode'] ?? 'list';
        $limit = $instance['limit'] ?? 5;
        $province = $instance['province'] ?? '';
        $category = $instance['category'] ?? '';
        ?>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('title')); ?>"><?php esc_html_e('Title:', 'tendersa-for-wp'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('title')); ?>" name="<?php echo esc_attr($this->get_field_name('title')); ?>" type="text" value="<?php echo esc_attr($title); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('mode')); ?>"><?php esc_html_e('Display:', 'tendersa-for-wp'); ?></label>
            <select class="widefat" id="<?php echo esc_attr($this->get_field_id('mode')); ?>" name="<?php echo esc_attr($this->get_field_name('mode')); ?>">
                <option value="list" <?php selected($mode, 'list'); ?>><?php esc_html_e('Tender List', 'tendersa-for-wp'); ?></option>
                <option value="closing_soon" <?php selected($mode, 'closing_soon'); ?>><?php esc_html_e('Closing Soon', 'tendersa-for-wp'); ?></option>
                <option value="stats" <?php selected($mode, 'stats'); ?>><?php esc_html_e('Quick Stats', 'tendersa-for-wp'); ?></option>
                <option value="counts" <?php selected($mode, 'counts'); ?>><?php esc_html_e('Counts by Province', 'tendersa-for-wp'); ?></option>
            </select>
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('limit')); ?>"><?php esc_html_e('Number of items:', 'tendersa-for-wp'); ?></label>
            <input class="tiny-text" id="<?php echo esc_attr($this->get_field_id('limit')); ?>" name="<?php echo esc_attr($this->get_field_name('limit')); ?>" type="number" min="1" max="20" value="<?php echo esc_attr($limit); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('province')); ?>"><?php esc_html_e('Province filter:', 'tendersa-for-wp'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('province')); ?>" name="<?php echo esc_attr($this->get_field_name('province')); ?>" type="text" value="<?php echo esc_attr($province); ?>" placeholder="<?php esc_attr_e('e.g. Gauteng', 'tendersa-for-wp'); ?>" />
        </p>
        <p>
            <label for="<?php echo esc_attr($this->get_field_id('category')); ?>"><?php esc_html_e('Category filter:', 'tendersa-for-wp'); ?></label>
            <input class="widefat" id="<?php echo esc_attr($this->get_field_id('category')); ?>" name="<?php echo esc_attr($this->get_field_name('category')); ?>" type="text" value="<?php echo esc_attr($category); ?>" placeholder="<?php esc_attr_e('e.g. Construction', 'tendersa-for-wp'); ?>" />
        </p>
        <?php
    }

    public function update($new_instance, $old_instance): array
    {
        $instance = $old_instance;
        $instance['title']    = sanitize_text_field($new_instance['title'] ?? '');
        $instance['mode']     = in_array($new_instance['mode'] ?? '', ['list', 'closing_soon', 'stats', 'counts'], true) ? $new_instance['mode'] : 'list';
        $instance['limit']    = min(20, max(1, (int)($new_instance['limit'] ?? 5)));
        $instance['province'] = sanitize_text_field($new_instance['province'] ?? '');
        $instance['category'] = sanitize_text_field($new_instance['category'] ?? '');
        return $instance;
    }

    private function render_list(Client $client, int $limit, array $instance): void
    {
        $params = ['limit' => $limit];
        if (!empty($instance['province'])) $params['province'] = $instance['province'];
        if (!empty($instance['category'])) $params['category'] = $instance['category'];

        $result = $client->get(Endpoints::TENDERS_LIST, $params);
        if (is_wp_error($result) || empty($result['data'])) {
            echo '<p>' . esc_html__('No tenders available.', 'tendersa-for-wp') . '</p>';
            return;
        }

        echo '<ul class="tendersa-widget-list">';
        foreach ($result['data'] as $tender) {
            echo '<li>';
            echo '<strong>' . esc_html($tender['title'] ?? '') . '</strong>';
            if (!empty($tender['closing_date'])) {
                echo '<br><span class="tendersa-date">' . esc_html($tender['closing_date']) . '</span>';
            }
            echo '</li>';
        }
        echo '</ul>';
    }

    private function render_closing_soon(Client $client, int $limit, array $instance): void
    {
        $result = $client->get(Endpoints::TENDERS_CLOSING_SOON, ['limit' => $limit]);
        if (is_wp_error($result) || empty($result['data'])) {
            echo '<p>' . esc_html__('No tenders closing soon.', 'tendersa-for-wp') . '</p>';
            return;
        }

        echo '<ul class="tendersa-widget-closing">';
        foreach ($result['data'] as $tender) {
            echo '<li>';
            echo '<strong>' . esc_html($tender['title'] ?? '') . '</strong>';
            if (!empty($tender['closing_date'])) {
                echo '<br><span class="tendersa-date">' . esc_html($tender['closing_date']) . '</span>';
            }
            echo '</li>';
        }
        echo '</ul>';
    }

    private function render_stats(Client $client): void
    {
        $result = $client->get(Endpoints::META_STATUS);
        if (is_wp_error($result)) {
            echo '<p>' . esc_html__('Stats unavailable.', 'tendersa-for-wp') . '</p>';
            return;
        }

        $data = $result['data'] ?? [];
        echo '<ul class="tendersa-widget-stats">';
        if (isset($data['total_tenders'])) {
            echo '<li><strong>' . esc_html__('Total Tenders:', 'tendersa-for-wp') . '</strong> ' . esc_html(number_format_i18n((int)$data['total_tenders'])) . '</li>';
        }
        if (isset($data['active_tenders'])) {
            echo '<li><strong>' . esc_html__('Active:', 'tendersa-for-wp') . '</strong> ' . esc_html(number_format_i18n((int)$data['active_tenders'])) . '</li>';
        }
        if (isset($data['total_awards'])) {
            echo '<li><strong>' . esc_html__('Awards:', 'tendersa-for-wp') . '</strong> ' . esc_html(number_format_i18n((int)$data['total_awards'])) . '</li>';
        }
        echo '</ul>';
    }

    private function render_counts(Client $client, array $instance): void
    {
        $result = $client->get(Endpoints::META_PROVINCES);
        if (is_wp_error($result) || empty($result['data'])) {
            echo '<p>' . esc_html__('No data.', 'tendersa-for-wp') . '</p>';
            return;
        }

        echo '<ul class="tendersa-widget-counts">';
        foreach (array_slice($result['data'], 0, 9) as $province) {
            $name = $province['name'] ?? __('Unknown', 'tendersa-for-wp');
            $count = $province['count'] ?? 0;
            echo '<li>' . esc_html($name) . ' (' . esc_html($count) . ')</li>';
        }
        echo '</ul>';
    }
}
