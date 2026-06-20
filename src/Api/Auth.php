<?php

namespace TendersaForWp\Api;

class Auth
{
    private const OPTION_KEY = 'tendersa_apk';

    public static function resolve(): ?string
    {
        if (defined('TENDERSA_API_KEY') && TENDERSA_API_KEY) {
            return TENDERSA_API_KEY;
        }

        $key = get_option(self::OPTION_KEY);
        if (!empty($key) && str_starts_with($key, 'tsa_prod_')) {
            return $key;
        }

        return null;
    }

    public static function set(string $key): bool
    {
        if (!str_starts_with($key, 'tsa_prod_')) return false;
        return update_option(self::OPTION_KEY, sanitize_text_field($key));
    }

    public static function clear(): bool
    {
        return delete_option(self::OPTION_KEY);
    }

    public static function is_configured(): bool
    {
        return self::resolve() !== null;
    }
}
