<?php
/**
 * Editor Style Component.
 *
 * This file handles registration and integration of style files in the editor
 *
 * @see       https://developer.wordpress.org/reference/functions/wp_enqueue_style/
 *
 * @license   GPL-2.0+
 */

namespace RalfHortt\Assets;

class EditorStyle extends Style
{
    /**
     * Where should the assets be registered.
     *
     * @var string Hook to register
     */
    protected string $hook = 'enqueue_block_editor_assets';
}
