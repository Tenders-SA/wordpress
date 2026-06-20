<div class="tendersa-card">
    <h3 class="tendersa-card-title">
        <a href="#tender-<?php echo esc_attr($tender['id'] ?? ''); ?>">
            <?php echo esc_html($tender['title'] ?? ''); ?>
        </a>
    </h3>
    <div class="tendersa-card-meta">
        <?php if (!empty($tender['province'])) : ?>
            <span class="tendersa-badge"><?php echo esc_html($tender['province']); ?></span>
        <?php endif; ?>
        <?php if (!empty($tender['primary_category_name'])) : ?>
            <span class="tendersa-badge"><?php echo esc_html($tender['primary_category_name']); ?></span>
        <?php endif; ?>
        <?php if (!empty($tender['closing_date'])) : ?>
            <span class="tendersa-date">
                <?php echo esc_html__('Closes:', 'tendersa-for-wp') . ' ' . esc_html($tender['closing_date']); ?>
            </span>
        <?php endif; ?>
        <?php if (!empty($tender['estimated_value'])) : ?>
            <span class="tendersa-value">R <?php echo esc_html(number_format_i18n((float)$tender['estimated_value'])); ?></span>
        <?php endif; ?>
        <?php if (!empty($tender['status'])) : ?>
            <span class="tendersa-status"><?php echo esc_html(ucfirst($tender['status'])); ?></span>
        <?php endif; ?>
    </div>
    <?php if (!empty($tender['description'])) : ?>
        <p class="tendersa-excerpt"><?php echo esc_html(wp_trim_words($tender['description'], 25)); ?></p>
    <?php endif; ?>
</div>
