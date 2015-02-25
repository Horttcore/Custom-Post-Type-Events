<?php
/**
 * Plugin Name: Custom Post Type Events
 * Plugin URI: http://horttcore.de
 * Description: Manage events
 * Version: 2.0
 * Author: Ralf Hortt
 * Author URI: http://horttcore.de
 * Text Domain: custom-post-type-events
 * Domain Path: /languages/
 * License: GPL2
 */

require( 'classes/custom-post-type-events.php' );
require( 'classes/custom-post-type-events.widget.php' );
require( 'inc/template-tags.php' );

if ( is_admin() )
	require( 'classes/custom-post-type-events.admin.php' );
