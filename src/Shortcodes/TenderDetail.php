<?php

namespace TendersaForWp\Shortcodes;

use TendersaForWp\Api\Client;
use TendersaForWp\Api\Endpoints;

class TenderDetail
{
    public static function render($atts): string
    {
        $atts = shortcode_atts([
            'id'             => '',
            'show_analysis'  => 'true',
            'show_documents' => 'true',
            'show_awards'    => 'true',
            'show_timeline'  => 'false',
        ], $atts, 'tendersa_detail');

        if (empty($atts['id'])) {
            return '<p class="tendersa-error">' . esc_html__('Tender ID is required.', 'tendersa-for-wp') . '</p>';
        }

        $client = new Client();
        $id = sanitize_text_field($atts['id']);
        $result = $client->get(Endpoints::build(Endpoints::TENDER_GET, $id));

        if (is_wp_error($result) || empty($result['data'])) {
            return '<p class="tendersa-error">' . esc_html__('Tender not found.', 'tendersa-for-wp') . '</p>';
        }

        $tender = $result['data'];
        $has_analysis = !empty($atts['show_analysis']) && $atts['show_analysis'] !== 'false';
        $has_docs = !empty($atts['show_documents']) && $atts['show_documents'] !== 'false';
        $has_awards = !empty($atts['show_awards']) && $atts['show_awards'] !== 'false';
        $has_timeline = !empty($atts['show_timeline']) && $atts['show_timeline'] !== 'false';

        ob_start();
        $template = self::locate_template('tender-detail');
        include $template;

        if ($has_awards) {
            $awards = $client->get_data(Endpoints::build(Endpoints::TENDER_AWARDS, $id));
            if ($awards) {
                echo '<h3>' . esc_html__('Awards', 'tendersa-for-wp') . '</h3>';
                foreach ($awards as $award) {
                    echo '<div class="tendersa-award">';
                    echo '<strong>' . esc_html($award['supplier_name'] ?? '') . '</strong>';
                    if (!empty($award['award_date'])) {
                        echo ' &mdash; ' . esc_html($award['award_date']);
                    }
                    echo '</div>';
                }
            }
        }

        if ($has_analysis) {
            $analysis = $client->get_data(Endpoints::build(Endpoints::TENDER_ANALYSIS, $id));
            if ($analysis) {
                echo '<h3>' . esc_html__('AI Analysis', 'tendersa-for-wp') . '</h3>';
                echo '<div class="tendersa-analysis">' . esc_html($analysis['summary'] ?? '') . '</div>';
            }
        }

        if ($has_docs) {
            $docs = $client->get_data(Endpoints::build(Endpoints::TENDER_DOCUMENTS, $id));
            if ($docs) {
                echo '<h3>' . esc_html__('Documents', 'tendersa-for-wp') . '</h3><ul class="tendersa-docs">';
                foreach ($docs as $doc) {
                    $url = !empty($doc['url']) ? $doc['url'] : '';
                    echo '<li><a href="' . esc_url($url) . '" target="_blank">' . esc_html($doc['title'] ?? $doc['filename'] ?? __('Document', 'tendersa-for-wp')) . '</a></li>';
                }
                echo '</ul>';
            }
        }

        return ob_get_clean();
    }

    private static function locate_template(string $relative): string
    {
        $theme = get_stylesheet_directory() . '/tendersa-templates/' . $relative . '.php';
        return file_exists($theme) ? $theme : TENDERSA_PLUGIN_DIR . 'src/Templates/' . $relative . '.php';
    }
}
