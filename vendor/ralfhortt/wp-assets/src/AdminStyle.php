<?php
/**
 * Admin Style component.
 *
 * This file handles registration and integration of style files in wp-admin
 *
 * @see       https://developer.wordpress.org/reference/functions/wp_enqueue_style/
 *
 * @license   GPL-2.0+
 */

namespace RalfHortt\Assets;

class AdminStyle extends Style
{
    /**
     * Where should the assets be registered.
     *
     * @var string Hook to register
     */
    protected string $hook = 'admin_print_styles';
}
