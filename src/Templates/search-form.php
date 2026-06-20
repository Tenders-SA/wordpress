<form method="get" class="tendersa-search-form">
    <input type="search" name="tendersa_q"
           value="<?php echo esc_attr($search_query ?? ''); ?>"
           placeholder="<?php echo esc_attr($placeholder ?? __('Search tenders...', 'tendersa-for-wp')); ?>"
           class="tendersa-search-input" />
    <button type="submit" class="tendersa-search-btn">
        <?php esc_html_e('Search', 'tendersa-for-wp'); ?>
    </button>
</form>
