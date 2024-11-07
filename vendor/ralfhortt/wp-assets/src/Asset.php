<?php
/**
 * Assets base component.
 *
 * This file handles registration and integration of asset files
 *
 * @license GPL-2.0+
 */

namespace RalfHortt\Assets;

use Exception;

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
     * @var mixed<callable|string> A callable or Hook suffix
     */
    protected mixed $condition = true;

    /**
     * Asset handle.
     */
    protected string $handle;

    /**
     * Source URL.
     *
     * Use relative or absolute url
     * relative url are starting in theme folder
     */
    protected string $source;

    /**
     * Asset dependencies.
     *
     * List of asset handlers
     */
    protected array $dependencies = [];

    /**
     * Asset version.
     *
     * @var mixed
     *            - string for explicit version
     *            - bool:true for dynamic cache busting
     *            - bool:false for default behaviour
     */
    protected mixed $version = '';

    /**
     * Get asset path.
     **/
    public function getPath(): string
    {
        return str_replace(
            [
                get_stylesheet_directory_uri(),
                get_template_directory_uri(),
                WP_PLUGIN_URL,
            ],
            [
                get_stylesheet_directory(),
                get_template_directory(),
                WP_PLUGIN_DIR,
            ],
            $this->source
        );
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
     */
    protected function isExternal(): bool
    {
        if (str_contains($this->source, home_url())) {
            return false;
        }

        return true;
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
     *
     * @throws Exception
     */
    public function version(): ?string
    {
        if ($this->isExternal()) {
            return $this->version;
        }

        if (! $this->version) {
            return null;
        }

        if (is_string($this->version)) {
            return $this->version;
        }

        if (! is_readable($this->getPath())) {
            throw new \Exception(sprintf('Asset %s not readable', $this->getPath()));
        }

        return filemtime($this->getPath());
    }
}
