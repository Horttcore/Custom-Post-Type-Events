<?php
/**
 * Style component.
 *
 * This file handles registration and integration of style files
 *
 * @see       https://developer.wordpress.org/reference/functions/wp_enqueue_style/
 *
 * @license   GPL-2.0+
 */

namespace RalfHortt\Assets;

use Exception;

class Style extends Asset
{
    /**
     * Where should the assets be registered.
     *
     * @var string Hook to register
     */
    protected string $hook = 'wp_enqueue_scripts';

    /**
     * Class constructor.
     *
     * @param string[] $dependencies Array of style dependencies
     */
    public function __construct(
        protected string $handle,
        protected string $source = '',
        protected array $dependencies = [],
        protected mixed $version = true,
        protected string $media = 'all'
    )
    {
    }

    /**
     * Enqueue style.
     */
    public function enqueueAsset(): void
    {
        wp_enqueue_style($this->handle);
    }

    /**
     * Register style.
     *
     * @throws Exception
     */
    public function registerAsset(): void
    {
        if ((bool) wp_styles()->query($this->handle, 'registered')) {
            return;
        }

        wp_register_style($this->handle, $this->locateSource(), $this->dependencies, $this->version(), $this->media);
    }
}
