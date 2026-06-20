<div class="tendersa-restricted">
    <?php if ($matched) : ?>
        <div class="tendersa-alert tendersa-alert-warning">
            <strong><?php esc_html_e('RESTRICTED', 'tendersa-for-wp'); ?></strong> &mdash;
            <?php echo esc_html(sprintf(
                __('"%s" appears on the restricted suppliers list.', 'tendersa-for-wp'),
                $supplier_name
            )); ?>
        </div>
    <?php else : ?>
        <div class="tendersa-alert tendersa-alert-success">
            <?php echo esc_html(sprintf(
                __('"%s" was not found on the restricted suppliers list.', 'tendersa-for-wp'),
                $supplier_name
            )); ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($restrictions)) : ?>
        <h4><?php esc_html_e('Restriction Details', 'tendersa-for-wp'); ?></h4>
        <ul>
            <?php foreach ($restrictions as $r) : ?>
            <li>
                <strong><?php echo esc_html($r['restriction_type'] ?? $r['type'] ?? ''); ?>:</strong>
                <?php echo esc_html($r['description'] ?? ''); ?>
                <?php if (!empty($r['start_date'])) : ?>
                    <br><span class="tendersa-date"><?php echo esc_html($r['start_date']); ?>
                    <?php echo !empty($r['end_date']) ? ' &rarr; ' . esc_html($r['end_date']) : ''; ?>
                    </span>
                <?php endif; ?>
            </li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>
</div>
