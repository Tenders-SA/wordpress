<?php

namespace TendersaForWp;

class ShortcodeProcessor
{
    public static function locate_template(string $relative): string
    {
        $theme_dir = get_stylesheet_directory() . '/tendersa-templates/';
        $plugin_dir = TENDERSA_PLUGIN_DIR . 'src/Templates/';

        $theme_file = $theme_dir . $relative . '.php';
        if (file_exists($theme_file)) {
            return $theme_file;
        }

        return $plugin_dir . $relative . '.php';
    }

    public static function render_error(string $message): string
    {
        return '<div class="tendersa-error">' . esc_html($message) . '</div>';
    }

    public static function render_empty(string $message = ''): string
    {
        if (empty($message)) {
            $message = __('No data available.', 'tendersa-for-wp');
        }
        return '<p class="tendersa-empty">' . esc_html($message) . '</p>';
    }

    public static function sanitize_shortcode_atts(string $tag, array $defaults, $atts): array
    {
        return shortcode_atts($defaults, $atts, $tag);
    }

    public static function pagination_param(): int
    {
        return max(1, get_query_var('tendersa_page', 1));
    }
}
