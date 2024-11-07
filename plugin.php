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
use RalfHortt\CustomPostTypeEvents\Blocks\Events\EventsBlock;
use RalfHortt\CustomPostTypeEvents\Meta\EventDate;
use RalfHortt\CustomPostTypeEvents\PostTypes\Events;
use RalfHortt\CustomPostTypeEvents\Taxonomies\EventCategories;
use RalfHortt\Plugin\PluginFactory;
use RalfHortt\TranslatorService\Translator;

// ------------------------------------------------------------------------------
// Prevent direct file access
// ------------------------------------------------------------------------------
if (! defined('WPINC')) {
    exit;
}

// ------------------------------------------------------------------------------
// Autoloader
// ------------------------------------------------------------------------------
$autoloader = dirname(__FILE__).'/vendor/autoload.php';

if (is_readable($autoloader)) {
    require_once $autoloader;
}

// ------------------------------------------------------------------------------
// Bootstrap
// ------------------------------------------------------------------------------
$eventsBlockAssets = require dirname(__FILE__).'/build/blocks/Events/index.asset.php';
$eventsSidebarAssets = require dirname(__FILE__).'/build/meta/index.asset.php';

PluginFactory::create()
    ->addService(Translator::class, 'custom-post-type-events', dirname(plugin_basename(__FILE__)).'/languages/')
    ->addService(Events::class)
    ->addService(EventCategories::class)
    ->addService(EventDate::class)
    ->addService(EventsBlock::class)
    ->addService(EditorScript::class, 'events-block', plugins_url('/build/blocks/Events/index.js', __FILE__), $eventsBlockAssets['dependencies'], $eventsBlockAssets['version'])
    ->addService(EditorScript::class, 'events-sidebar', plugins_url('/build/meta/index.js', __FILE__), $eventsSidebarAssets['dependencies'], $eventsSidebarAssets['version'])
    ->addService(EditorStyle::class, 'events-sidebar', plugins_url('/build/meta/index.css', __FILE__), [], $eventsSidebarAssets['version'])
    ->boot();
