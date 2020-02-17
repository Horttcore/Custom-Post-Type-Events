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

use RalfHortt\CustomPostTypeEvents\Events;
use RalfHortt\CustomPostTypeEvents\EventCategories;
use RalfHortt\CustomPostTypeEvents\Blocks\EventsBlock;
use RalfHortt\CustomPostTypeEvents\Meta\EventDate;
use Horttcore\Plugin\PluginFactory;

// ------------------------------------------------------------------------------
// Prevent direct file access
// ------------------------------------------------------------------------------
if (!defined('WPINC')) :
    die;
endif;

// ------------------------------------------------------------------------------
// Autoloader
// ------------------------------------------------------------------------------
$autoloader = dirname(__FILE__).'/vendor/autoload.php';

if (is_readable($autoloader)) :
    require_once $autoloader;
endif;

// ------------------------------------------------------------------------------
// Bootstrap
// ------------------------------------------------------------------------------
PluginFactory::create()
    ->addTranslation('custom-post-type-events', dirname(plugin_basename(__FILE__)).'/languages/')
    ->addService(Events::class)
    ->addService(EventCategories::class)
    ->addService(EventDate::class)
    // ->addService(EventLocation::class)
    ->addService(EventsBlock::class)
    ->boot();

// Hook scripts function into block editor hook
add_action('enqueue_block_editor_assets', function () {
    $editorStylePath = '/dist/css/editor.css';
    wp_enqueue_style(
        'events-editor-styles',
        plugins_url($editorStylePath, __FILE__),
        [],
        filemtime(plugin_dir_path(__FILE__) . $editorStylePath)
    );
});
