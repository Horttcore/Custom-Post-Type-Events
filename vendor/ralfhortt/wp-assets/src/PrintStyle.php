<?php
/**
 * PrintStyle component.
 *
 * This file handles registration and integration of print stylesheets.
 *
 * @see       https://developer.wordpress.org/reference/functions/wp_enqueue_style/
 *
 * @license   GPL-2.0+
 */

namespace RalfHortt\Assets;

class PrintStyle extends Style
{
    /**
     * For which media is the stylesheet valid.
     *
     * @var string Media
     */
    protected mixed $media = 'print';
}
