<?php
/*
Plugin Name: Custom Post Type Events
Plugin URI: http://horttcore.de
Description: Custom Post Type Events
Version: 2.0
Author: Ralf Hortt
Author URI: http://horttcore.de
License: GPL2
*/



/**
 *
 *  Custom Post Type Events
 *
 */
final class Custom_Post_Type_Events
{



	/**
	 * Plugin constructor
	 *
	 * @access public
	 * @return void
	 * @since v2.0
	 * @author Ralf Hortt
	 **/
	public function __construct()
	{

		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_taxonomy' ) );
		add_filter( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
		add_action( 'plugins_loaded', array( $this, 'load_plugin_textdomain' ) );
		add_filter( 'query_vars', array( $this, 'query_vars' ) );
		add_filter( 'page_rewrite_rules', array( $this, 'rewrite_rules' ) );

	} // END __construct



	/**
	 * Theme query vars
	 *
	 * @param array $vars Query variables
	 * @return array Query variables
	 * @since v2.0
	 * @author Ralf Hortt
	 **/
	public function query_vars( $vars )
	{

	    $add = array(
			'event-year',
			'event-month',
			'event-day',
	    );

	    return array_merge( $theme_vars, $vars );

	} // END query_vars



	/**
	 * Load plugin translation
	 *
	 * @access public
	 * @return void
	 * @author Ralf Hortt <me@horttcore.de>
	 * @since v1.0.0
	 **/
	public function load_plugin_textdomain()
	{

		load_plugin_textdomain( 'custom-post-type-events', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/'  );

	} // END load_plugin_textdomain



	/**
	 * Reorder event archive
	 *
	 * @access public
	 * @param obj $query WP_Query object
	 * @return void
	 * @since v0.1
	 * @author Ralf Hortt
	 **/
	public function pre_get_posts( $query )
	{

		if ( ( is_post_type_archive( 'event' ) || is_tax( 'event-category' ) ) && $query->is_main_query() && !is_admin() ) :

			$query->set( 'order', 'ASC' );
			$query->set( 'orderby', 'meta_value_num' );
			$query->set( 'meta_key', '_event-date-start' );

			if ( get_query_var( 'event-year' ) && get_query_var( 'event-month' ) && get_query_var( 'event-day' ) ) :

				$query->set( 'meta_query', array(
					array(
						'key' => '_event-date-start',
						'value' => mktime( 12, 0, 0, get_query_var( 'event-month' ), get_query_var( 'event-day' ), get_query_var( 'event-year' ) ),
						'compare' => '<=',
						'type' => 'numeric',
					),
					array(
						'key' => '_event-date-end',
						'value' => mktime( 12, 0, 0, get_query_var( 'event-month' ), get_query_var( 'event-day' ), get_query_var( 'event-year' ) ),
						'compare' => '>=',
						'type' => 'numeric',
					)
				));

			elseif ( get_query_var( 'event-year' ) && get_query_var( 'event-month' ) ) :

				$query->set( 'meta_query', array(
					array(
						'key' => '_event-date-end',
						'value' => array( mktime( 0, 0, 0, get_query_var( 'event-month' ), 1, get_query_var( 'event-year' ) ), mktime( 0, 0, 0, get_query_var( 'event-month' ), date( 't', mktime( 0, 0, 0, get_query_var( 'event-month' ), 1, get_query_var( 'event-year' )) ), get_query_var( 'event-year' ) ) ),
						'compare' => 'BETWEEN',
						'type' => 'numeric'
					)
				));

			elseif ( get_query_var( 'event-year' ) ) :

				$query->set( 'meta_query', array(
					array(
						'key' => '_event-date-end',
						'value' => array( mktime( 0, 0, 0, 1, 1, get_query_var( 'event-year' ) ), mktime( 0, 0, 0, 12, 31, get_query_var( 'event-year' ) ) ),
						'compare' => 'BETWEEN',
						'type' => 'numeric'
					),
				));

			else :

				$query->set( 'meta_query', array( array(
						'key' => '_event-date-start',
						'value' => time(),
						'compare' => '>=',
						'type' => 'NUMERIC'
				) ) );

			endif;

		endif;

		return $query;

	} // END pre_get_posts



	/**
	 * Register post type
	 *
	 * @access public
	 * @return void
	 * @since v0.1
	 * @author Ralf Hortt
	 */
	public function register_post_type()
	{

		register_post_type( 'event', array(
			'labels' => array(
				'name' => _x( 'Events', 'post type general name', 'custom-post-type-events' ),
				'singular_name' => _x( 'Event', 'post type singular name', 'custom-post-type-events' ),
				'add_new' => _x( 'Add New', 'Event', 'custom-post-type-events' ),
				'add_new_item' => __( 'Add New Event', 'custom-post-type-events' ),
				'edit_item' => __( 'Edit Event', 'custom-post-type-events' ),
				'new_item' => __( 'New Event', 'custom-post-type-events' ),
				'view_item' => __( 'View Event', 'custom-post-type-events' ),
				'search_items' => __( 'Search Event', 'custom-post-type-events' ),
				'not_found' =>  __( 'No Events found', 'custom-post-type-events' ),
				'not_found_in_trash' => __( 'No Events found in Trash', 'custom-post-type-events' ),
				'parent_item_colon' => '',
				'menu_name' => __( 'Events', 'custom-post-type-events' )
			),
			'public' => TRUE,
			'publicly_queryable' => TRUE,
			'show_ui' => TRUE,
			'show_in_menu' => TRUE,
			'query_var' => TRUE,
			'rewrite' => array( 'slug' => _x( 'events', 'Post Type Slug', 'custom-post-type-events' ) ),
			'capability_type' => 'post',
			'has_archive' => TRUE,
			'hierarchical' => FALSE,
			'menu_position' => NULL,
			'menu_icon' => 'dashicons-calendar-alt',
			'supports' => array( 'title', 'editor', 'thumbnail' )
		));

	} // END register_post_type



	/**
	 * Register taxonomy
	 *
	 * @access public
	 * @return void
	 * @since v2.0
	 * @author Ralf Hortt
	 */
	public function register_taxonomy()
	{

		register_taxonomy( 'event-category', array('event'), array(
			'hierarchical' => TRUE,
			'labels' => array(
				'name' => _x( 'Categories', 'taxonomy general name', 'custom-post-type-events' ),
				'singular_name' => _x( 'Category', 'taxonomy singular name', 'custom-post-type-events' ),
				'search_items' =>  __( 'Search Categories', 'custom-post-type-events' ),
				'all_items' => __( 'All Categories', 'custom-post-type-events' ),
				'parent_item' => __( 'Parent Category', 'custom-post-type-events' ),
				'parent_item_colon' => __( 'Parent Category:', 'custom-post-type-events' ),
				'edit_item' => __( 'Edit Category', 'custom-post-type-events' ),
				'update_item' => __( 'Update Category', 'custom-post-type-events' ),
				'add_new_item' => __( 'Add New Category', 'custom-post-type-events' ),
				'new_item_name' => __( 'New Category Name', 'custom-post-type-events' ),
				'menu_name' => __( 'Categories', 'custom-post-type-events' ),
			),
			'show_ui' => TRUE,
			'query_var' => TRUE,
			'rewrite' => array( 'slug' => _x('event-category', 'Event Category Slug', 'custom-post-type-events') ),
			'show_admin_column' => TRUE,
		));

	} // END register_taxonomy



	/**
	 * Theme rewrite rules
	 *
	 * @access public
	 * @param array $rules Rewrite rules
	 * @return array Rewrite rules
	 * @author Ralf Hortt
	 **/
	public function rewrite_rules( $rules )
	{

	    global $wp_rewrite;

		return array_merge( array(
				_x( 'events', 'Post Type Slug', 'custom-post-type-events' ) . '/(.+)/(.+)/(.+)/?$' => 'index.php?post_type=event&event-year=$matches[1]&event-month=$matches[2]&event-day=$matches[3]',
				_x( 'events', 'Post Type Slug', 'custom-post-type-events' ) . '/(.+)/(.+)/?$' => 'index.php?post_type=event&event-year=$matches[1]&event-month=$matches[2]',
				_x( 'events', 'Post Type Slug', 'custom-post-type-events' ) . '/(.+)/?$' => 'index.php?post_type=event&event-year=$matches[1]',
			),
		$rules );

	} // END theme_rewrite_rules



}

new Custom_Post_Type_Events;
