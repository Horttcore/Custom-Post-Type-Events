<?php
/**
 * Login Script Component.
 *
 * This file handles registration and integration of script files in the editor
 *
 * @see       https://developer.wordpress.org/reference/functions/wp_enqueue_script/
 *
 * @license   GPL-2.0+
 */

namespace RalfHortt\Assets;

class LoginScript extends Script
{
    /**
     * Where should the assets be registered.
     *
     * @var string Hook to register
     */
    protected string $hook = 'login_enqueue_scripts';
}
