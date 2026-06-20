<ul class="tendersa-search-list">
    <?php foreach ($results as $tender) : ?>
    <li>
        <strong><?php echo esc_html($tender['title'] ?? ''); ?></strong>
        <?php if (!empty($tender['closing_date'])) : ?>
            <span class="tendersa-date"><?php echo esc_html($tender['closing_date']); ?></span>
        <?php endif; ?>
        <?php if (!empty($tender['province'])) : ?>
            <span class="tendersa-badge"><?php echo esc_html($tender['province']); ?></span>
        <?php endif; ?>
        <?php if (!empty($tender['description'])) : ?>
            <p class="tendersa-excerpt"><?php echo esc_html(wp_trim_words($tender['description'], 20)); ?></p>
        <?php endif; ?>
    </li>
    <?php endforeach; ?>
</ul>
