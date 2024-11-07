<?php
/**
 * Admin Script Component.
 *
 * This file handles the registration and integration of script files in wp-admin
 *
 * @see       https://developer.wordpress.org/reference/functions/wp_enqueue_script/
 *
 * @license   GPL-2.0+
 */

namespace RalfHortt\Assets;

class AdminScript extends Script
{
    /**
     * Where should the assets be registered.
     *
     * @var string Hook to register
     */
    protected string $hook = 'admin_enqueue_scripts';
}
