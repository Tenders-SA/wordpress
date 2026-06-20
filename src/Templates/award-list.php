<table class="tendersa-awards-table">
    <thead>
        <tr>
            <th><?php esc_html_e('Supplier', 'tendersa-for-wp'); ?></th>
            <th><?php esc_html_e('Tender', 'tendersa-for-wp'); ?></th>
            <th><?php esc_html_e('Value', 'tendersa-for-wp'); ?></th>
            <th><?php esc_html_e('Date', 'tendersa-for-wp'); ?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($awards as $award) : ?>
        <tr>
            <td><?php echo esc_html($award['supplier_name'] ?? ''); ?></td>
            <td><?php echo esc_html($award['tender_title'] ?? $award['tender_id'] ?? ''); ?></td>
            <td><?php echo !empty($award['award_value']) ? 'R ' . esc_html(number_format_i18n((float)$award['award_value'])) : ''; ?></td>
            <td><?php echo esc_html($award['award_date'] ?? ''); ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
