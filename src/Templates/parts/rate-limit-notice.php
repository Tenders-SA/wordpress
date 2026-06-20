<div class="tendersa-alert tendersa-alert-warning">
    <strong><?php esc_html_e('API Rate Limit', 'tendersa-for-wp'); ?>:</strong>
    <?php echo esc_html(sprintf(
        __('%d of %d requests remaining. Resets at %s.', 'tendersa-for-wp'),
        $remaining,
        $limit,
        $reset_time
    )); ?>
</div>
