<div class="tendersa-company-profile">
    <h3><?php echo esc_html($company['name'] ?? __('Company Profile', 'tendersa-for-wp')); ?></h3>

    <dl class="tendersa-company-details">
        <?php if (!empty($company['registration_number'])) : ?>
            <dt><?php esc_html_e('Registration', 'tendersa-for-wp'); ?></dt>
            <dd><?php echo esc_html($company['registration_number']); ?></dd>
        <?php endif; ?>
        <?php if (!empty($company['bbbee_level'])) : ?>
            <dt><?php esc_html_e('B-BBEE Level', 'tendersa-for-wp'); ?></dt>
            <dd><?php echo esc_html($company['bbbee_level']); ?></dd>
        <?php endif; ?>
        <?php if (!empty($company['email'])) : ?>
            <dt><?php esc_html_e('Email', 'tendersa-for-wp'); ?></dt>
            <dd><?php echo esc_html($company['email']); ?></dd>
        <?php endif; ?>
        <?php if (!empty($company['phone'])) : ?>
            <dt><?php esc_html_e('Phone', 'tendersa-for-wp'); ?></dt>
            <dd><?php echo esc_html($company['phone']); ?></dd>
        <?php endif; ?>
        <?php if (!empty($company['address'])) : ?>
            <dt><?php esc_html_e('Address', 'tendersa-for-wp'); ?></dt>
            <dd><?php echo esc_html($company['address']); ?></dd>
        <?php endif; ?>
        <?php if (!empty($company['total_award_value'])) : ?>
            <dt><?php esc_html_e('Total Award Value', 'tendersa-for-wp'); ?></dt>
            <dd>R <?php echo esc_html(number_format_i18n((float)$company['total_award_value'])); ?></dd>
        <?php endif; ?>
    </dl>
</div>
