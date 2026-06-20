<div class="tendersa-filter-bar">
    <span class="tendersa-count">
        <?php echo esc_html(sprintf(
            __('%d tenders found', 'tendersa-for-wp'),
            $total_count
        )); ?>
    </span>
    <?php foreach ($active_filters as $label) : ?>
        <span class="tendersa-badge"><?php echo esc_html($label); ?></span>
    <?php endforeach; ?>
</div>
