<?php

namespace TendersaForWp\Cache;

class TendersaCache
{
    public static function get(string $key): ?array
    {
        $cached = get_transient($key);
        return $cached !== false ? $cached : null;
    }

    public static function set(string $key, array $data, ?int $ttl = null): bool
    {
        if ($ttl === null) {
            $ttl = (int)(get_option('tendersa_cache_ttl', 300));
        }
        return set_transient($key, $data, $ttl);
    }

    public static function delete(string $key): bool
    {
        return delete_transient($key);
    }

    public static function flush(): void
    {
        global $wpdb;
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s AND option_name NOT LIKE %s",
                $wpdb->esc_like('_transient_tendersa_') . '%',
                $wpdb->esc_like('_transient_timeout_tendersa_') . '%'
            )
        );
        wp_cache_flush();
    }

    public static function cleanup(): void
    {
        global $wpdb;
        $wpdb->query(
            $wpdb->prepare(
                "DELETE FROM {$wpdb->options} WHERE option_name LIKE %s AND option_value < %d",
                $wpdb->esc_like('_transient_timeout_tendersa_') . '%',
                time()
            )
        );
    }
}
