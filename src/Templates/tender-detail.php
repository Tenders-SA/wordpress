<div class="tendersa-detail">
    <h2 class="tendersa-detail-title"><?php echo esc_html($tender['title'] ?? ''); ?></h2>

    <div class="tendersa-detail-meta">
        <?php if (!empty($tender['reference_number'])) : ?>
            <p><strong><?php esc_html_e('Reference:', 'tendersa-for-wp'); ?></strong> <?php echo esc_html($tender['reference_number']); ?></p>
        <?php endif; ?>
        <?php if (!empty($tender['source_organization'])) : ?>
            <p><strong><?php esc_html_e('Organization:', 'tendersa-for-wp'); ?></strong> <?php echo esc_html($tender['source_organization']); ?></p>
        <?php endif; ?>
        <?php if (!empty($tender['province'])) : ?>
            <p><strong><?php esc_html_e('Province:', 'tendersa-for-wp'); ?></strong> <?php echo esc_html($tender['province']); ?></p>
        <?php endif; ?>
        <?php if (!empty($tender['closing_date'])) : ?>
            <p><strong><?php esc_html_e('Closing Date:', 'tendersa-for-wp'); ?></strong> <?php echo esc_html($tender['closing_date']); ?></p>
        <?php endif; ?>
        <?php if (!empty($tender['estimated_value'])) : ?>
            <p><strong><?php esc_html_e('Estimated Value:', 'tendersa-for-wp'); ?></strong> R <?php echo esc_html(number_format_i18n((float)$tender['estimated_value'])); ?></p>
        <?php endif; ?>
        <?php if (!empty($tender['status'])) : ?>
            <p><strong><?php esc_html_e('Status:', 'tendersa-for-wp'); ?></strong> <?php echo esc_html(ucfirst($tender['status'])); ?></p>
        <?php endif; ?>
        <?php if (!empty($tender['publication_type'])) : ?>
            <p><strong><?php esc_html_e('Type:', 'tendersa-for-wp'); ?></strong> <?php echo esc_html($tender['publication_type']); ?></p>
        <?php endif; ?>
        <?php if (!empty($tender['locality'])) : ?>
            <p><strong><?php esc_html_e('Locality:', 'tendersa-for-wp'); ?></strong> <?php echo esc_html($tender['locality']); ?></p>
        <?php endif; ?>
    </div>

    <?php if (!empty($tender['description'])) : ?>
        <div class="tendersa-detail-description">
            <h3><?php esc_html_e('Description', 'tendersa-for-wp'); ?></h3>
            <p><?php echo esc_html($tender['description']); ?></p>
        </div>
    <?php endif; ?>

    <?php if (!empty($tender['ai_summary'])) : ?>
        <div class="tendersa-detail-ai-summary">
            <h3><?php esc_html_e('AI Summary', 'tendersa-for-wp'); ?></h3>
            <p><?php echo esc_html($tender['ai_summary']); ?></p>
        </div>
    <?php endif; ?>
</div>
