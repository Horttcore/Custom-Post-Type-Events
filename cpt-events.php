<?php
/*
Plugin Name: Custom Post Type Events
Plugin URI: http://horttcore.de
Description: Custom Post Type Events
Version: 0.2
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
		wp_localize_script( 'cpt-events-admin', 'cptEvents', array(
			'addLocation' => __( 'Add Location', 'cpt-events' ),
			'removeLocation' => __( 'Remove Location', 'cpt-events' )
		) );
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
		$start = ( $start = get_post_meta( $post->ID, '_event-date-start', TRUE ) ) ? $start : time();
		$end = ( $end = get_post_meta( $post->ID, '_event-date-end', TRUE ) ) ? $end : '';
		$time_start = ( $time = get_post_meta( $post->ID, '_event-time-start', TRUE ) ) ? $time : '';
		$time_end = ( $time = get_post_meta( $post->ID, '_event-time-end', TRUE ) ) ? $time : '';
		$location = get_post_meta( $post->ID, '_event-location', TRUE );

		$date_start = ( $start ) ? date( 'd.m.Y', $start ) : date( 'd.m.Y') ;
		$date_end = ( $end ) ? date( 'd.m.Y', $end ) : '' ;

		wp_enqueue_script( 'cpt-events-admin' );
		?>

		<table class="form-table">
			<tr>
				<th><label for="event-date"><?php _e( 'Date', 'cpt-events'  ); ?></label></th>
				<td>
					<input type="text" name="event-date-start" id="event-date-start" value="<?php if ( isset( $start ) ) echo date( 'd.m.Y', $start ) ?>" /> <span <?php if ( !$date_end ) echo 'style="display: none"'  ?> class="multi-day">- <input type="text" name="event-date-end" id="event-date-end" value="<?php if ( isset( $end ) ) echo date( 'd.m.Y', $end ) ?>" /></span> <small><?php _e( 'DD.MM.YYYY', 'cpt-events' ); ?></small><br>
					<label><input <?php if ( $end ) echo 'checked="checked"' ?> type="checkbox" id="event-multi-day"> <?php _e( 'Multi-day', 'cpt-events' ); ?></label> <label><input <?php if ($time_start || $time_end ) echo 'checked="checked"' ?> type="checkbox" id="event-time"> <?php _e( 'Time', 'cpt-events' ); ?></label>
				</td>
			</tr>
			<tr class="event-time" <?php if ( !$time_start && !$time_end ) echo 'style="display: none"' ?>>
				<th><label for="event-from-hour"><?php _e( 'Time', 'cpt-events' ); ?></label></th>
				<td><input type="text" name="event-from-hour" size="2" id="event-from-hour" value="<?php if ( '' != $time_start ) echo date( 'H', $time_start ) ?>" /> : <input type="text" size="2" name="event-from-minute" id="event-from-minute" value="<?php if ( '' != $time_start ) echo date( 'i', $time_start ) ?>" /> h - <input type="text" name="event-to-hour" size="2" id="event-to-hour" value="<?php if ( isset( $time_end ) ) echo date( 'H', $time_end ) ?>" /> : <input type="text" size="2" name="event-to-minute" id="event-to-minute" value="<?php if ( isset( $time_end ) ) echo date( 'i', $time_end ) ?>" /> h</td>
			</tr>

			<?php $style = ( is_array( $location ) && !empty( $location ) ) ? '' : 'style="display: none"' ?>

			<?php if ( post_type_supports( 'event', 'location' ) ) : ?>

				<tr class="location" <?php echo $style ?>>
					<th><label for="event-street"><?php _e( 'Street', 'cpt-events' ); ?></label> / <label for="event-street-number"><?php _e( 'Nr.', 'cpt-events' ); ?></label></th>
					<td><input type="text" name="event-street" id="event-street" value="<?php if ( isset( $location['street'] ) ) echo $location['street'] ?>"><input type="text" name="event-street-number" id="event-street-number" value="<?php if ( isset( $location['street-number'] ) ) echo $location['street-number'] ?>"></td>
				</tr>

				<tr class="location" <?php echo $style ?>>
					<th><label for="event-zip"><?php _e( 'ZIP', 'cpt-events' ); ?></label> / <label for="event-city"><?php _e( 'City', 'cpt-events' ); ?></label></th>
					<td><input type="text" name="event-zip" id="event-zip" value="<?php if ( isset( $location['zip'] ) ) echo $location['zip'] ?>"><input type="text" name="event-city" id="event-city" value="<?php if ( isset( $location['city'] ) ) echo $location['city'] ?>"></td>
				</tr>

				<tr class="location" <?php echo $style ?>>
					<th><label for="event-country"><?php _e( 'Country', 'cpt-events'  ); ?></label></th>
					<td><input type="text" name="event-country" id="event-country" value="<?php if ( isset( $location['country'] ) ) echo $location['country'] ?>" /></td>
				</tr>

			<?php endif; ?>

		</table>

		<?php if ( post_type_supports( 'event', 'location' ) ) : ?>
			<a href="#" data-status="<?php echo ( is_array( $location ) && !empty( $location ) ) ? 'opened' : 'closed' ?>" class="toggle-location button"><?php if ( is_array( $location ) && !empty( $location ) ) _e( 'Remove Location', 'cpt-events' ); else _e( 'Add Location', 'cpt-events' ); ?></a>
		<?php endif; ?>

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

		add_post_type_support( 'event', 'location' );
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

		// Delete unused stuff
		if ( '' == $_POST['event-date-start'] ) :
			delete_post_meta( $post_id, '_event-date-start' );
			delete_post_meta( $post_id, '_event-date-end' );
			delete_post_meta( $post_id, '_event-time-start' );
			delete_post_meta( $post_id, '_event-time-end' );
			return;
		endif;

		// Event date
		$date_start = explode( '.', $_POST['event-date-start'] );
		$date_start = mktime( 0, 0, 0, $date_start[1], $date_start[0], $date_start[2] );
		update_post_meta( $post_id, '_event-date-start', $date_start );

		if ( $_POST['event-date-end'] ) :
			$date_end = ( $_POST['event-date-end'] ) ? explode( '.', $_POST['event-date-end'] ) : $date_start;
			$date_end = mktime( 23, 59, 0, $date_end[1], $date_end[0], $date_end[2] );
			update_post_meta( $post_id, '_event-date-end', $date_end );
		else :
			delete_post_meta( $post_id, '_event-date-end' );
		endif;

		// Event time
		if ( $_POST['event-from-hour'] && $_POST['event-from-minute'] ) :
			$date_start = explode( '.', $_POST['event-date-start'] );
			$date_start = mktime( $_POST['event-from-hour'], $_POST['event-from-minute'], 0, $date_start[1], $date_start[0], $date_start[2] );
			update_post_meta( $post_id, '_event-time-start', $date_start );
		else :
			delete_post_meta( $post_id, '_event-time-start' );
		endif;

		if ( $_POST['event-to-hour'] && $_POST['event-to-minute'] ) :
			$date_end = ( $_POST['event-date-end'] ) ? explode( '.', $_POST['event-date-end'] ) : $date_start;
			$date_end = mktime( $_POST['event-to-hour'], $_POST['event-to-minute'], 0, $date_end[1], $date_end[0], $date_end[2] );
			update_post_meta( $post_id, '_event-time-end', $date_end );
		else :
			delete_post_meta( $post_id, '_event-time-end' );
		endif;

		// Location
		if ( post_type_supports( 'event', 'location' ) ) :

			if ( $_POST['event-city'] ) :

				update_post_meta( $post_id, '_event-location', array(
					'street' => $_POST['event-street'],
					'street-number' => $_POST['event-street-number'],
					'zip' => $_POST['event-zip'],
					'city' => $_POST['event-city'],
					'country' => $_POST['event-country']
				) );

			else :

				delete_post_meta( $post_id, '_event-location' );

			endif;

		endif;

	}



}

new Custom_Post_Type_Events;
