<div class="tendersa-intel-item">
    <h4><?php echo esc_html($item['title'] ?? ''); ?></h4>
    <?php if (!empty($item['published_date'])) : ?>
        <span class="tendersa-date"><?php echo esc_html($item['published_date']); ?></span>
    <?php endif; ?>
    <?php if (!empty($item['source'])) : ?>
        <span class="tendersa-badge"><?php echo esc_html($item['source']); ?></span>
    <?php endif; ?>
    <?php if (!empty($item['summary'])) : ?>
        <p class="tendersa-excerpt"><?php echo esc_html(wp_trim_words($item['summary'], 40)); ?></p>
    <?php endif; ?>
    <?php if (!empty($item['url'])) : ?>
        <a href="<?php echo esc_url($item['url']); ?>" target="_blank" class="tendersa-read-more">
            <?php esc_html_e('Read more', 'tendersa-for-wp'); ?> &rarr;
        </a>
    <?php endif; ?>
</div>
