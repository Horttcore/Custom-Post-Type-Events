<?php
/*
Plugin Name: Custom Post Type Events
Plugin URI: http://horttcore.de
Description: Custom Post Type Events
Version: 0.1.1
Author: Ralf Hortt
Author URI: http://horttcore.de
License: GPL2
*/



/**
 *
 *  Custom Post Type Produts
 *
 */
class Custom_Post_Type_Events
{



	/**
	 * Plugin constructor
	 *
	 * @access public
	 * @return void
	 * @author Ralf Hortt
	 **/
	public function __construct()
	{
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_print_styles-post.php', array( $this, 'admin_enqueue_styles' ), 1000 );
		add_action( 'admin_print_styles-post-new.php', array( $this, 'admin_enqueue_styles' ), 1000 );
		add_action( 'init', array( $this, 'register_post_type' ) );
		add_action( 'init', array( $this, 'register_taxonomy' ) );
		add_filter( 'manage_event_posts_columns' , array( $this, 'manage_event_posts_columns' ) );
		add_action( 'manage_event_posts_custom_column' , array($this,'manage_event_posts_custom_column'), 10, 2 );
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_filter( 'pre_get_posts', array( $this, 'pre_get_posts' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );

		load_plugin_textdomain( 'cpt-events', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/'  );
	}



	/**
	 * Add meta boxes
	 *
	 * @access public
	 * @return void
	 * @author Ralf Hortt
	 **/
	public function add_meta_boxes()
	{
		add_meta_box( 'event-info', __( 'Event Info', 'cpt-events' ), array( $this, 'metabox_event_info' ), 'event' );
	}



	/**
	 * Register scripts
	 *
	 * @access public
	 * @return void
	 * @author Ralf Hortt
	 **/
	public function admin_enqueue_scripts()
	{
		wp_register_script( 'cpt-events-admin', plugins_url( dirname( plugin_basename( __FILE__ ) ) . '/javascript/cpt-events-admin.js' ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ), FALSE, TRUE );
	}



	/**
	 * Register styles
	 *
	 * @access public
	 * @return void
	 * @author Ralf Hortt
	 **/
	public function admin_enqueue_styles()
	{
		wp_register_style( 'cpt-events-admin', plugins_url( dirname( plugin_basename( __FILE__ ) ) . '/css/cpt-events-admin.css' ) );
		wp_enqueue_style( 'cpt-events-admin' );
	}



	/**
	 * Add management columns
	 *
	 * @access public
	 * @param str $column Column name
	 * @param int $post_id Post ID
	 * @return void
	 * @author Ralf Hortt
	 **/
	public function manage_event_posts_custom_column( $column, $post_id )
	{
		switch ( $column ) :

			case 'event-category' :
				echo strip_tags( get_the_term_list( $post_id, 'event-category', '', ', ', '' ) );
			break;

			case 'event-date' :

				$allday = ( get_post_meta( $post_id, '_allday', TRUE ) ) ? TRUE : FALSE;

				$start = get_post_meta( $post_id, '_event-start', TRUE );

				if ( $start ) :

					$end = get_post_meta( $post_id, '_event-end', TRUE );

					if ( $allday ) :

						echo date( 'd.m.Y', $start );

						if ( date( 'd.m.Y', $start) != date( 'd.m.Y', $end ) ) :

							echo ' - ' . date( 'd.m.Y', $end );

						endif;

					else :

						echo date( 'd.m.Y H:i', $start );

						if ( date( 'd.m.Y', $start) != date( 'd.m.Y', $end ) ) :

							echo ' - ' . date( 'd.m.Y', $end );

						endif;

					endif;

				endif;

			break;

		endswitch;
	}



	/**
	 * Add management columns
	 *
	 * @access public
	 * @param array $columns Columns
	 * @return array
	 * @author Ralf Hortt
	 **/
	public function manage_event_posts_columns( $columns )
	{
		$columns['event-category'] = __( 'Category', 'cpt-events' );
		$columns['event-date'] = __( 'Event Date', 'cpt-events' );
		return $columns;
	}



	/**
	 * Event info meta box
	 *
	 * @access public
	 * @param obj $post Post object
	 * @return void
	 * @author Ralf Hortt
	 **/
	public function metabox_event_info( $post )
	{
		$start = get_post_meta( $post->ID, '_event-start', true );
		$end = get_post_meta( $post->ID, '_event-end', true );

		$checked = ( 1 == get_post_meta( $post->ID, '_allday', true )) ? 'checked="checked"' : '';

		$date_start = ( $start ) ? date( 'd.m.Y', $start ) : date( 'd.m.Y') ;
		$date_end = ( $start ) ? date( 'd.m.Y', $end ) : date( 'd.m.Y') ;

		$fromhour = ( $start ) ? date( 'H', $start ) : date( 'H' );
		$frommin = ( $start ) ? date( 'i', $start ) : '00';

		$tohour = ( $end ) ? date( 'H', $end ) : date( 'H' ) + 1;
		$tomin = ( $end ) ? date( 'i', $end ) : '00';

		wp_enqueue_script( 'cpt-events-admin' );
		?>

		<p>
			<label for="date"><?php _e( 'Date', 'cpt-events'  ); ?></label>
			<input type="text" name="date-start" id="date-start" value="<?php echo $date_start ?>" format="DD.MM.YYYY" /> - 
			<input type="text" name="date-end" id="date-end" value="<?php echo $date_end ?>" />

			<input <?php echo $checked ?> type="checkbox" name="all-day" id="all-day" value="true" />
			<label for="all-day"><?php _e( 'all-day', 'cpt-events'  ); ?></label>
		</p>

		<div class="date-time">
			<div class="alignleft">
				<label for="from-hour"><?php _e( 'From', 'cpt-events'  ); ?></label>
				<input type="text" name="from-hour" size="2" id="from-hour" value="<?php echo $fromhour ?>" /> : <input type="text" size="2" name="from-minute" id="from-minute" value="<?php echo $frommin ?>" /> h<br />
			</div>

			<div class="alignleft">
				<label for="to-hour"><?php _e( 'to', 'cpt-events'  ); ?></label>
				<input type="text" name="to-hour" size="2" id="to-hour" value="<?php echo $tohour ?>" /> : <input type="text" size="2" name="to-minute" id="to-minute" value="<?php echo $tomin ?>" /> h
			</div>
		</div>

		<div class="clear"></div>
		<?php

		wp_nonce_field( 'save-event-info', 'event-info-nonce' );

	}



	/**
	 * Update messages
	 *
	 * @access public
	 * @return void
	 * @author Ralf Hortt
	 **/
	public function post_updated_messages( $messages ) {
		global $post, $post_ID;

		$messages['event'] = array(
			0 => '', // Unused. Messages start at index 1.
			1 => sprintf( __('Event updated. <a href="%s">View Event</a>', 'cpt-events'), esc_url( get_permalink($post_ID) ) ),
			2 => __('Custom field updated.', 'cpt-events'),
			3 => __('Custom field deleted.', 'cpt-events'),
			4 => __('Event updated.', 'cpt-events'),
			/* translators: %s: date and time of the revision */
			5 => isset($_GET['revision']) ? sprintf( __('Event restored to revision from %s', 'cpt-events'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6 => sprintf( __('Event published. <a href="%s">View Event</a>', 'cpt-events'), esc_url( get_permalink($post_ID) ) ),
			7 => __('Event saved.', 'cpt-events'),
			8 => sprintf( __('Event submitted. <a target="_blank" href="%s">Preview Event</a>', 'cpt-events'), esc_url( add_query_arg( 'preview', 'TRUE', get_permalink($post_ID) ) ) ),
			9 => sprintf( __('Event scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview Event</a>', 'cpt-events'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
			10 => sprintf( __('Event draft updated. <a target="_blank" href="%s">Preview Event</a>', 'cpt-events'), esc_url( add_query_arg( 'preview', 'TRUE', get_permalink($post_ID) ) ) ),
		);

		return $messages;
	}



	/**
	 * Reorder event archive
	 *
	 * @return void
	 * @author Ralf Hortt
	 **/
	public function pre_get_posts( $query )
	{
		if ( is_archive( 'event' ) && $query->is_main_query() && !is_admin() ) :
			$query->query_vars['order'] = 'asc';
			$query->query_vars['orderby'] = 'meta_key';
			$query->query_vars['meta_key'] = '_event-start';
			$query->query_vars['meta_value'] = time();
			$query->query_vars['meta_compare'] = '>=';
		endif;
	}


	/**
	 *
	 * POST TYPES
	 *
	 * @access public
	 * @return void
	 * @author Ralf Hortt
	 */
	public function register_post_type()
	{
		$labels = array(
			'name' => _x( 'Events', 'post type general name', 'cpt-events' ),
			'singular_name' => _x( 'Event', 'post type singular name', 'cpt-events' ),
			'add_new' => _x( 'Add New', 'Event', 'cpt-events' ),
			'add_new_item' => __( 'Add New Event', 'cpt-events' ),
			'edit_item' => __( 'Edit Event', 'cpt-events' ),
			'new_item' => __( 'New Event', 'cpt-events' ),
			'view_item' => __( 'View Event', 'cpt-events' ),
			'search_items' => __( 'Search Event', 'cpt-events' ),
			'not_found' =>  __( 'No Events found', 'cpt-events' ),
			'not_found_in_trash' => __( 'No Events found in Trash', 'cpt-events' ),
			'parent_item_colon' => '',
			'menu_name' => __( 'Events', 'cpt-events' )
		);

		$args = array(
			'labels' => $labels,
			'public' => TRUE,
			'publicly_queryable' => TRUE,
			'show_ui' => TRUE,
			'show_in_menu' => TRUE,
			'query_var' => TRUE,
			'rewrite' => array( 'slug' => _x( 'event', 'Post Type Slug', 'cpt-events' ) ),
			'capability_type' => 'post',
			'has_archive' => TRUE,
			'hierarchical' => FALSE,
			'menu_position' => NULL,
			'supports' => array('title', 'editor', 'thumbnail' )
		);

		register_post_type( 'event', $args);
	}



	/**
	 *
	 * CUSTOM TAXONOMY
	 *
	 * @access public
	 * @return void
	 * @author Ralf Hortt
	 */
	public function register_taxonomy()
	{
		$labels = array(
			'name' => _x( 'Categories', 'taxonomy general name', 'cpt-events' ),
			'singular_name' => _x( 'Category', 'taxonomy singular name', 'cpt-events' ),
			'search_items' =>  __( 'Search Categories', 'cpt-events' ),
			'all_items' => __( 'All Categories', 'cpt-events' ),
			'parent_item' => __( 'Parent Category', 'cpt-events' ),
			'parent_item_colon' => __( 'Parent Category:', 'cpt-events' ),
			'edit_item' => __( 'Edit Category', 'cpt-events' ),
			'update_item' => __( 'Update Category', 'cpt-events' ),
			'add_new_item' => __( 'Add New Category', 'cpt-events' ),
			'new_item_name' => __( 'New Category Name', 'cpt-events' ),
			'menu_name' => __( 'Categories', 'cpt-events' ),
		);

		register_taxonomy('event-category',array('event'), array(
			'hierarchical' => TRUE,
			'labels' => $labels,
			'show_ui' => TRUE,
			'query_var' => TRUE,
			'rewrite' => array( 'slug' => _x('event-category', 'Event Category Slug', 'cpt-events') )
		));
	}



	/**
	 * Save post callback
	 *
	 * @access public
	 * @param int $post_id Post id
	 * @return void
	 * @author Ralf Hortt
	 **/
	public function save_post( $post_id )
	{

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		if ( !isset( $_POST['event-info-nonce'] ) || !wp_verify_nonce( $_POST['event-info-nonce'], 'save-event-info' ) )
			return;

		// Date
		if ( $_POST['date-start'] ) :

			$date_start = explode( '.', $_POST['date-start'] );
			$date_end = explode( '.', $_POST['date-end'] );
			$allday = ( $_POST['all-day'] ) ? true : false;

			update_post_meta( $post_id, '_allday', $allday );
			update_post_meta( $post_id, '_event-start', mktime( $_POST['from-hour'], $_POST['from-minute'], 0, $date_start[1], $date_start[0], $date_start[2] ) );
			update_post_meta( $post_id, '_event-end', mktime( $_POST['to-hour'], $_POST['to-minute'], 0, $date_end[1], $date_end[0], $date_end[2] ) );

		else :

			delete_post_meta( $post_id, '_event-start' );
			delete_post_meta( $post_id, '_event-end' );

		endif;
	}



}

new Custom_Post_Type_Events;
