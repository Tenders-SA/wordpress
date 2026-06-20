<nav class="tendersa-pagination">
    <?php if ($current > 1) : ?>
        <a href="<?php echo esc_url(add_query_arg('tendersa_page', $current - 1)); ?>" class="tendersa-page-link">&laquo; <?php esc_html_e('Previous', 'tendersa-for-wp'); ?></a>
    <?php endif; ?>
    <span class="tendersa-page-info"><?php echo esc_html(sprintf(__('Page %d of %d', 'tendersa-for-wp'), $current, $total)); ?></span>
    <?php if ($current < $total) : ?>
        <a href="<?php echo esc_url(add_query_arg('tendersa_page', $current + 1)); ?>" class="tendersa-page-link"><?php esc_html_e('Next', 'tendersa-for-wp'); ?> &raquo;</a>
    <?php endif; ?>
</nav>
