<?php
/**
 * Script component.
 *
 * This file handles registration and integration of script files
 *
 * @see       https://developer.wordpress.org/reference/functions/wp_enqueue_script/
 *
 * @license   GPL-2.0+
 */

namespace RalfHortt\Assets;

class Script extends Asset
{
    /**
     * Where should the assets be registered.
     */
    protected string $hook = 'wp_enqueue_scripts';

    /**
     * Class constructor.
     *
     * @param string[] $dependencies Array of script dependencies
     */
    public function __construct(
        protected string $handle,
        protected string $source = '',
        protected array $dependencies = [],
        protected mixed $version = null,
        protected array $args = [],
    ) {
    }

    /**
     * Enqueue script.
     */
    public function enqueueAsset(): void
    {
        wp_enqueue_script($this->handle);
    }

    /**
     * Register script.
     */
    public function registerAsset(): void
    {
        if ((bool) wp_scripts()->query($this->handle, 'registered')) {
            return;
        }

        wp_register_script($this->handle, $this->locateSource(), $this->dependencies, $this->version(), $this->args);
    }
}
