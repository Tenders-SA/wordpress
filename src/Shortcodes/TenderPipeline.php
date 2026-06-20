<?php

namespace TendersaForWp\Shortcodes;

use TendersaForWp\Api\Client;
use TendersaForWp\Api\Endpoints;

class TenderPipeline
{
    public static function render($atts): string
    {
        $atts = shortcode_atts([
            'id' => '',
        ], $atts, 'tendersa_pipeline');

        if (empty($atts['id'])) {
            return '<p class="tendersa-error">' . esc_html__('Tender ID is required.', 'tendersa-for-wp') . '</p>';
        }

        $client = new Client();
        $id = sanitize_text_field($atts['id']);

        $tender = $client->get_data(Endpoints::build(Endpoints::TENDER_GET, $id));
        if (!$tender) {
            return '<p class="tendersa-error">' . esc_html__('Tender not found.', 'tendersa-for-wp') . '</p>';
        }

        $awards = $client->get_data(Endpoints::build(Endpoints::TENDER_AWARDS, $id)) ?? [];
        $contracts = $client->get_data(Endpoints::build(Endpoints::TENDER_CONTRACTS, $id)) ?? [];
        $milestones = $client->get_data(Endpoints::build(Endpoints::TENDER_MILESTONES, $id)) ?? [];
        $timeline = $client->get_data(Endpoints::build(Endpoints::TENDER_TIMELINE, $id)) ?? [];

        ob_start();
        ?>
        <div class="tendersa-pipeline">
            <div class="tendersa-pipeline-step tendersa-step-tender">
                <h3><?php esc_html_e('Tender', 'tendersa-for-wp'); ?></h3>
                <p><strong><?php echo esc_html($tender['title'] ?? ''); ?></strong></p>
                <?php if (!empty($tender['closing_date'])) : ?>
                    <p><?php echo esc_html__('Closing:', 'tendersa-for-wp') . ' ' . esc_html($tender['closing_date']); ?></p>
                <?php endif; ?>
            </div>

            <?php if (!empty($awards)) : ?>
            <div class="tendersa-pipeline-step tendersa-step-awards">
                <h3><?php esc_html_e('Awards', 'tendersa-for-wp'); ?></h3>
                <ul>
                <?php foreach ($awards as $award) : ?>
                    <li><strong><?php echo esc_html($award['supplier_name'] ?? ''); ?></strong>
                    <?php echo !empty($award['award_value']) ? ' — R ' . esc_html(number_format_i18n((float)$award['award_value'])) : ''; ?>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <?php if (!empty($contracts)) : ?>
            <div class="tendersa-pipeline-step tendersa-step-contracts">
                <h3><?php esc_html_e('Contracts', 'tendersa-for-wp'); ?></h3>
                <ul>
                <?php foreach ($contracts as $c) : ?>
                    <li><?php echo esc_html($c['title'] ?? $c['contract_id'] ?? ''); ?></li>
                <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <?php if (!empty($milestones)) : ?>
            <div class="tendersa-pipeline-step tendersa-step-milestones">
                <h3><?php esc_html_e('Milestones', 'tendersa-for-wp'); ?></h3>
                <ul>
                <?php foreach ($milestones as $m) : ?>
                    <li><?php echo esc_html($m['title'] ?? $m['description'] ?? ''); ?>
                    <?php echo !empty($m['date']) ? ' — ' . esc_html($m['date']) : ''; ?>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>

            <?php if (!empty($timeline)) : ?>
            <div class="tendersa-pipeline-step tendersa-step-timeline">
                <h3><?php esc_html_e('Timeline', 'tendersa-for-wp'); ?></h3>
                <ul>
                <?php foreach ($timeline as $event) : ?>
                    <li><?php echo esc_html($event['label'] ?? $event['event'] ?? ''); ?>
                    <?php echo !empty($event['date']) ? ' — ' . esc_html($event['date']) : ''; ?>
                    </li>
                <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
        <?php
        return ob_get_clean();
    }
}
