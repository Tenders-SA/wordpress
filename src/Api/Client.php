<?php

namespace TendersaForWp\Api;

use TendersaForWp\Cache\TendersaCache;

class Client
{
    private string $api_key;
    private string $base_url = 'https://api.tenders-sa.org/v2';
    private int $timeout;

    private const RATE_LIMIT_TRANSIENT = 'tendersa_rate_limit';

    public function __construct(?string $api_key = null, int $timeout = 15)
    {
        $this->api_key = $api_key ?? Auth::resolve();
        $this->timeout = $timeout;
    }

    public function get(string $path, array $params = []): array|\WP_Error
    {
        $cache_key = 'tendersa_api_' . md5($path . '?' . http_build_query($params));
        $cached = TendersaCache::get($cache_key);
        if ($cached !== null) return $cached;

        if (!$this->rate_limit_check()) {
            return new \WP_Error('rate_limited', 'API rate limit nearly exceeded. Try again later.');
        }

        $url = add_query_arg(array_filter($params, fn($v) => $v !== null && $v !== ''), "$this->base_url/$path");

        $response = wp_remote_get($url, [
            'headers' => [
                'Authorization' => "Bearer $this->api_key",
                'Accept'        => 'application/json',
                'User-Agent'    => 'tendersa-wp-plugin/' . TENDERSA_VERSION,
            ],
            'timeout'  => $this->timeout,
            'sslverify' => true,
        ]);

        if (is_wp_error($response)) return $response;

        $status = wp_remote_retrieve_response_code($response);
        $body   = json_decode(wp_remote_retrieve_body($response), true);

        if ($status !== 200 || empty($body['success'])) {
            return new \WP_Error(
                'api_error',
                $body['error'] ?? 'Unknown API error',
                ['status' => $status, 'code' => $body['code'] ?? '']
            );
        }

        $remaining = wp_remote_retrieve_header($response, 'X-RateLimit-Remaining');
        if ($remaining !== '') {
            set_transient(self::RATE_LIMIT_TRANSIENT, [
                'remaining' => (int)$remaining,
                'limit'     => (int)(wp_remote_retrieve_header($response, 'X-RateLimit-Limit') ?: 0),
                'reset'     => wp_remote_retrieve_header($response, 'X-RateLimit-Reset'),
            ], 60);
        }

        TendersaCache::set($cache_key, $body);

        return $body;
    }

    public function get_data(string $path, array $params = []): ?array
    {
        $result = $this->get($path, $params);
        if (is_wp_error($result)) return null;
        return $result['data'] ?? null;
    }

    public function get_meta(string $path, array $params = []): ?array
    {
        $result = $this->get($path, $params);
        if (is_wp_error($result)) return null;
        return $result['meta'] ?? null;
    }

    public static function test_connection(string $key): array|\WP_Error
    {
        $url = 'https://api.tenders-sa.org/v2/meta/status';
        $response = wp_remote_get($url, [
            'headers' => [
                'Authorization' => "Bearer $key",
                'Accept'        => 'application/json',
                'User-Agent'    => 'tendersa-wp-plugin/' . TENDERSA_VERSION,
            ],
            'timeout'  => 10,
            'sslverify' => true,
        ]);

        if (is_wp_error($response)) return $response;

        $status = wp_remote_retrieve_response_code($response);
        $body   = json_decode(wp_remote_retrieve_body($response), true);

        if ($status === 200 && !empty($body['success'])) {
            return ['status' => 'ok', 'data' => $body['data'] ?? []];
        }

        return new \WP_Error(
            'connection_failed',
            $body['error'] ?? "HTTP $status — check your API key"
        );
    }

    private function rate_limit_check(): bool
    {
        $rate = get_transient(self::RATE_LIMIT_TRANSIENT);
        if (!$rate || empty($rate['limit'])) return true;

        $ratio = $rate['remaining'] / $rate['limit'];
        if ($ratio <= 0) return false;
        if ($ratio < 0.1) usleep(500000);

        return true;
    }

    public function get_base_url(): string
    {
        return $this->base_url;
    }
}
