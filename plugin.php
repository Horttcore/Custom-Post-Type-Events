<?php
/**
 * Plugin Name: Custom Post Type Events
 * Plugin URI: https://horttcore.de
 * Description: A custom post type for managing events
 * Version: 1.0.0
 * Author: Ralf Hortt
 * Author URI: https://horttcore.de
 * Text Domain: custom-post-type-events
 * Domain Path: /languages/
 * License: MIT.
 */

namespace RalfHortt\CustomPostTypeEvents;

use RalfHortt\Assets\EditorScript;
use RalfHortt\Assets\EditorStyle;
use RalfHortt\CustomPostTypeEvents\Meta\EventDate;
use RalfHortt\CustomPostTypeEvents\PostTypes\Events;
use RalfHortt\CustomPostTypeEvents\Taxonomies\EventCategories;
use RalfHortt\Plugin\PluginFactory;
use RalfHortt\WPBlock\BlockManifest;

// ------------------------------------------------------------------------------
// Prevent direct file access
// ------------------------------------------------------------------------------
if (!defined('WPINC')) {
    exit;
}

// ------------------------------------------------------------------------------
// Autoloader
// ------------------------------------------------------------------------------
$autoloader = dirname(__FILE__) . '/vendor/autoload.php';

if (is_readable($autoloader)) {
    require_once $autoloader;
}

// ------------------------------------------------------------------------------
// Activation
// ------------------------------------------------------------------------------
register_activation_hook(__FILE__, static function (): void {
    flush_rewrite_rules();
});

// ------------------------------------------------------------------------------
// Bootstrap
// ------------------------------------------------------------------------------
$eventsSidebarAssets = require dirname(__FILE__) . '/build/meta/index.asset.php';

PluginFactory::create()
    ->addService(Events::class)
    ->addService(EventCategories::class)
    ->addService(EventDate::class)
    ->addService(EditorScript::class, 'events-sidebar', plugins_url('/build/meta/index.js', __FILE__), $eventsSidebarAssets['dependencies'], $eventsSidebarAssets['version'])
    ->addService(EditorStyle::class, 'events-sidebar', plugins_url('/build/meta/index.css', __FILE__), [], $eventsSidebarAssets['version'])
    ->addService(BlockManifest::class, __DIR__ . '/build/blocks/blocks-manifest.php')
    ->boot();
