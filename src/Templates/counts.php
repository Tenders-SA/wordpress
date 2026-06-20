<ul class="tendersa-counts tendersa-counts-<?php echo esc_attr($type); ?>">
    <?php foreach ($items as $item) : ?>
    <li>
        <span class="tendersa-count-label"><?php echo esc_html($item['name'] ?? __('Unknown', 'tendersa-for-wp')); ?></span>
        <span class="tendersa-count-number"><?php echo esc_html($item['count'] ?? 0); ?></span>
    </li>
    <?php endforeach; ?>
</ul>
