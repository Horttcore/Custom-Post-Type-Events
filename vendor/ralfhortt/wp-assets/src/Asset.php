<?php

/**
 * Assets base component.
 *
 * This file handles registration and integration of asset files
 *
 * @license GPL-2.0+
 */

namespace RalfHortt\Assets;

abstract class Asset
{
    /**
     * Where should the assets be registered.
     *
     * @var string Hook to register
     */
    protected string $hook;

    /**
     * Conditional loading.
     *
     * @var callable|string|bool A callable or hook suffix
     */
    protected mixed $condition = true;

    /**
     * Asset handle.
     */
    protected string $handle;

    /**
     * Source URL.
     *
     * Absolute URL to the asset file.
     */
    protected string $source;

    /**
     * Asset dependencies.
     *
     * @var list<string>
     */
    protected array $dependencies = [];

    /**
     * Asset version.
     *
     * - string for explicit version
     * - bool true for dynamic cache busting
     * - bool false / null for WordPress default behaviour
     *
     * @var string|bool|null
     */
    protected string|bool|null $version = '';

    /**
     * Get asset path.
     **/
    public function getPath(): string
    {
        $map = [
            get_stylesheet_directory_uri() => get_stylesheet_directory(),
            get_template_directory_uri()   => get_template_directory(),
            content_url()                  => WP_CONTENT_DIR,
            plugins_url()                  => WP_PLUGIN_DIR,
        ];

        $path = $this->source;

        foreach ($map as $uri => $dir) {
            $uri = untrailingslashit((string) $uri);
            $dir = untrailingslashit((string) $dir);

            if ($uri === '') {
                continue;
            }

            foreach ($this->urlSchemeVariants($uri) as $variant) {
                if (str_starts_with($path, $variant)) {
                    return $dir.substr($path, strlen($variant));
                }
            }
        }

        return $this->pathFromUrlSuffix($path);
    }

    /**
     * Resolve a filesystem path when the URL host differs (domain mapping).
     */
    protected function pathFromUrlSuffix(string $url): string
    {
        $path = (string) (parse_url($url, PHP_URL_PATH) ?: '');

        if ($path === '') {
            return $url;
        }

        $suffixMap = [
            '#/wp-content/plugins/(.+)$#'                                      => WP_PLUGIN_DIR,
            '#/wp-content/themes/'.preg_quote(get_stylesheet(), '#').'/(.+)$#' => get_stylesheet_directory(),
            '#/wp-content/themes/'.preg_quote(get_template(), '#').'/(.+)$#'   => get_template_directory(),
            '#/wp-content/(.+)$#'                                              => WP_CONTENT_DIR,
        ];

        foreach ($suffixMap as $pattern => $dir) {
            if (preg_match($pattern, $path, $matches) !== 1) {
                continue;
            }

            return untrailingslashit((string) $dir).'/'.$matches[1];
        }

        return $url;
    }

    /**
     * Get asset URI.
     *
     * @return string Get URI for asset file
     **/
    public function getUri(): string
    {
        return $this->source;
    }

    /**
     * Setup WordPress hooks.
     *
     * @return void
     */
    public function register(): void
    {
        add_action('init', [$this, 'registerAsset']);
        add_action('admin_init', [$this, 'registerAsset']);
        add_action($this->hook, [$this, 'enqueueAsset']);
    }

    /**
     * Enqueue asset.
     */
    abstract public function enqueueAsset(): void;

    /**
     * Register asset.
     */
    abstract public function registerAsset(): void;

    /**
     * Is asset an external resource?
     *
     * Treats theme, plugin, content, site, home, and (on multisite)
     * network URLs as local, including http/https scheme mismatches.
     */
    protected function isExternal(): bool
    {
        $source = $this->source;

        if ($source === '') {
            return true;
        }

        $normalizedSource = $this->normalizeUrl($source);

        foreach ($this->localOrigins() as $origin) {
            if (str_starts_with($normalizedSource, $this->normalizeUrl($origin))) {
                return false;
            }
        }

        return true;
    }

    /**
     * Local URL origins that should not be treated as external CDNs.
     *
     * @return list<string>
     */
    protected function localOrigins(): array
    {
        $origins = [
            home_url(),
            site_url(),
            content_url(),
            plugins_url(),
            get_stylesheet_directory_uri(),
            get_template_directory_uri(),
        ];

        if (is_multisite()) {
            $origins[] = network_home_url();
            $origins[] = network_site_url();
        }

        $normalized = [];

        foreach ($origins as $origin) {
            $origin = untrailingslashit($origin);

            if ($origin === '') {
                continue;
            }

            $normalized[] = $origin;
        }

        return array_values(array_unique($normalized));
    }

    /**
     * Normalize URL for origin comparison (drop scheme, trailing slash).
     */
    protected function normalizeUrl(string $url): string
    {
        $url = untrailingslashit($url);

        return (string) preg_replace('#^https?:#i', '', $url);
    }

    /**
     * Same URL with http/https/protocol-relative variants.
     *
     * @return list<string>
     */
    protected function urlSchemeVariants(string $url): array
    {
        $url = untrailingslashit($url);

        if ($url === '') {
            return [];
        }

        $variants = [$url];

        if (preg_match('#^https?://(.+)$#i', $url, $matches) !== 1) {
            return $variants;
        }

        $rest = $matches[1];
        $variants[] = 'https://'.$rest;
        $variants[] = 'http://'.$rest;
        $variants[] = '//'.$rest;

        /** @var list<string> $variants */
        $variants = array_values(array_unique($variants));

        return $variants;
    }

    /**
     * Locate source.
     */
    protected function locateSource(): string
    {
        return $this->isExternal() ? $this->source : $this->getUri();
    }

    /**
     * Get source version.
     */
    public function version(): string|bool|null
    {
        if ($this->isExternal()) {
            return $this->version;
        }

        if ($this->version === false || $this->version === null || $this->version === '') {
            return null;
        }

        if (is_string($this->version)) {
            return $this->version;
        }

        $path = $this->getPath();

        if (!is_readable($path)) {
            trigger_error(sprintf('Asset %s not readable', $path), E_USER_WARNING);

            return null;
        }

        $mtime = filemtime($path);

        if ($mtime === false) {
            trigger_error(sprintf('Asset %s mtime unavailable', $path), E_USER_WARNING);

            return null;
        }

        return (string) $mtime;
    }
}
