<?php
/*
Plugin Name: Custom Post Type Events
Plugin URI: http://horttcore.de
Description: Custom Post Type Events
Version: 0.3
Author: Ralf Hortt
Author URI: http://horttcore.de
License: GPL2
*/



/**
 *
 *  Custom Post Type Events
 *
 */
final class Custom_Post_Type_Events_Admin
{



	/**
	 * Plugin constructor
	 *
	 * @access public
	 * @return void
	 * @since v2.0
	 * @author Ralf Hortt <me@horttcore.de>
	 **/
	public function __construct()
	{

		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'admin_enqueue_scripts' ) );
		add_action( 'admin_print_styles-post.php', array( $this, 'admin_enqueue_styles' ), 1000 );
		add_action( 'admin_print_styles-post-new.php', array( $this, 'admin_enqueue_styles' ), 1000 );
		add_filter( 'manage_event_posts_columns' , array( $this, 'manage_event_posts_columns' ) );
		add_action( 'manage_event_posts_custom_column' , array($this,'manage_event_posts_custom_column'), 10, 2 );
		add_filter( 'post_updated_messages', array( $this, 'post_updated_messages' ) );
		add_action( 'save_post', array( $this, 'save_event' ) );
		add_action( 'save_post', array( $this, 'save_location' ) );
		add_action( 'wp_ajax_get_event_lat_long', array( $this, 'ajax_get_event_lat_long' ) );

	} // END __construct



	/**
	 * Add meta boxes
	 *
	 * @access public
	 * @return void
	 * @since v2.0
	 * @author Ralf Hortt <me@horttcore.de>
	 **/
	public function add_meta_boxes()
	{

		add_meta_box( 'event-info', __( 'Event', 'custom-post-type-events' ), array( $this, 'meta_box_time' ), 'event' );
		add_meta_box( 'event-location', __( 'Location', 'custom-post-type-events' ), array( $this, 'meta_box_location' ), 'event' );

	} // END add_meta_boxes



	/**
	 * Register scripts
	 *
	 * @access public
	 * @return void
	 * @since v2.0
	 * @author Ralf Hortt <me@horttcore.de>
	 **/
	public function admin_enqueue_scripts()
	{

		wp_register_script( 'custom-post-type-events-admin', plugins_url( dirname( plugin_basename( __FILE__ ) ) . '/../scripts/custom-post-type-events.admin.js' ), array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ), FALSE, TRUE );
		wp_localize_script( 'custom-post-type-events-admin', 'cptEvents', array(
			'addLocation' => __( 'Add Location', 'custom-post-type-events' ),
			'removeLocation' => __( 'Remove Location', 'custom-post-type-events' )
		) );

	} // END admin_enqueue_scripts



	/**
	 * Register styles
	 *
	 * @access public
	 * @return void
	 * @since v2.0
	 * @author Ralf Hortt <me@horttcore.de>
	 **/
	public function admin_enqueue_styles()
	{

		wp_register_style( 'custom-post-type-events-admin', plugins_url( dirname( plugin_basename( __FILE__ ) ) . '/css/custom-post-type-events-admin.css' ) );
		wp_enqueue_style( 'custom-post-type-events-admin' );

	} // END admin_enqueue_styles



	/**
	 * undocumented function
	 *
	 * @access public
	 * @return void
	 * @since v2.0
	 * @author Ralf Hortt <me@horttcore.de>
	 **/
	public function ajax_get_event_lat_long()
	{

		$latlong = $this->get_latitude_longitude( array(
			$_POST['street'],
			$_POST['streetnumber'],
			$_POST['zip'],
			$_POST['city'],
			$_POST['country'],
		) );

		die( json_encode( $latlong ) );

	} // END ajax_get_event_lat_long



	/**
	 * Get latitude/longitude
	 * @access public
	 * @param str/array $address Address
	 * @return array Latitude Longitude
	 * @since v2.0
	 * @author Ralf Hortt <me@horttcore.de>
	 */
	public function get_latitude_longitude( $address )
	{

		if ( is_array( $address ) )
			$address = implode( ' ', $address );

		$address = urlencode( strtolower( trim( $address ) ) );
		$url = 'http://maps.googleapis.com/maps/api/geocode/json?address=' . $address . '&sensor=false';
		$data = wp_remote_get( $url );
		$data = json_decode( $data['body'] );

		if ( isset($data->results[0]->geometry->location) ) :

			return array(
				'latitude' => $data->results[0]->geometry->location->lat,
				'longitude' => $data->results[0]->geometry->location->lng,
			);

		else :

			return array(
				'latitude' => '',
				'longitude' => '',
			);

		endif;

	} // END get_latitude_longitude



	/**
	 * Add management columns
	 *
	 * @access public
	 * @param str $column Column name
	 * @param int $post_id Post ID
	 * @return void
	 * @since v2.0
	 * @author Ralf Hortt <me@horttcore.de>
	 **/
	public function manage_event_posts_custom_column( $column, $post_id )
	{
		switch ( $column ) :

			case 'event-date' :

				$allday = ( get_post_meta( $post_id, '_allday', TRUE ) ) ? TRUE : FALSE;

				$start = get_post_meta( $post_id, '_event-start', TRUE );

				if ( $start ) :

					$end = get_post_meta( $post_id, '_event-end', TRUE );

					if ( $allday ) :

						echo date_i18n( 'd.m.Y', $start );

						if ( date_i18n( 'd.m.Y', $start) != date_i18n( 'd.m.Y', $end ) ) :

							echo ' - ' . date_i18n( 'd.m.Y', $end );

						endif;

					else :

						echo date_i18n( 'd.m.Y H:i', $start );

						if ( date_i18n( 'd.m.Y', $start) != date_i18n( 'd.m.Y', $end ) ) :

							echo ' - ' . date_i18n( 'd.m.Y', $end );

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
	 * @since v2.0
	 * @author Ralf Hortt <me@horttcore.de>
	 **/
	public function manage_event_posts_columns( $columns )
	{

		$columns['event-date'] = __( 'Event Date', 'custom-post-type-events' );
		return $columns;

	} // END manage_event_posts_columns



	/**
	 * Event info meta box
	 *
	 * @access public
	 * @param obj $post Post object
	 * @return void
	 * @since v2.0
	 * @author Ralf Hortt <me@horttcore.de>
	 **/
	public function meta_box_time( $post )
	{

		wp_enqueue_script( 'custom-post-type-events-admin' );

		$info = get_post_meta( $post->ID, '_event-info', TRUE );
		$date_start = ( $timestamp = get_post_meta( $post->ID, '_event-date-start', TRUE ) ) ? absint( $timestamp ) : time();
		$date_end = ( $timestamp = get_post_meta( $post->ID, '_event-date-end', TRUE ) ) ? absint( $timestamp ) : time();
		$from_hour = ( $timestamp = get_post_meta( $post->ID, '_event-time-start', TRUE ) ) ? absint( $timestamp ) : time();
		$to_hour = ( $timestamp = get_post_meta( $post->ID, '_event-time-end', TRUE ) ) ? absint( $timestamp ) : time() + 3600;
		?>

		<table class="form-table">

			<tr>
				<th><label for="event-date"><?php _e( 'Date', 'custom-post-type-events'  ); ?></label></th>
				<td>
					<input type="text" name="event-date-start" id="event-date-start" value="<?php echo date_i18n( 'd.m.Y', $date_start ) ?>" /> <span class="multi-day">-
					<input type="text" name="event-date-end" id="event-date-end" value="<?php echo date_i18n( 'd.m.Y', $date_end ) ?>" /></span>
					<small><?php _e( 'DD.MM.YYYY', 'custom-post-type-events' ); ?></small><br>
				</td>
			</tr>

			<tr class="event-time">
				<th><label for="event-from-hour"><?php _e( 'Time', 'custom-post-type-events' ); ?></label></th>
				<td>
					<input type="text" name="event-from-hour" size="2" id="event-from-hour" value="<?php echo date_i18n( 'H', $from_hour ) ?>" /> : <input type="text" size="2" name="event-from-minute" id="event-from-minute" value="<?php echo date_i18n( 'i', $from_hour ) ?>" /> h -
					<input type="text" name="event-to-hour" size="2" id="event-to-hour" value="<?php echo date_i18n( 'H', $to_hour ) ?>" /> : <input type="text" size="2" name="event-to-minute" id="event-to-minute" value="<?php echo date_i18n( 'i', $to_hour ) ?>" /> h
				</td>
			</tr>

			<tr>
				<td colspan="2">
					<label><input <?php checked( TRUE, $info['multi-day'] ) ?> type="checkbox" name="event-multi-day" id="event-multi-day"> <?php _e( 'Multi-day', 'custom-post-type-events' ); ?></label>
					<label><input <?php checked( TRUE, $info['time'] ) ?> type="checkbox" name="event-time" id="event-time"> <?php _e( 'Time', 'custom-post-type-events' ); ?></label>
				</td>
			</tr>

		</table>

		<?php

		wp_nonce_field( 'save-event-info', 'event-info-nonce' );

	} // END meta_box_time



	/**
	 * Event info meta box
	 *
	 * @access public
	 * @param obj $post Post object
	 * @return void
	 * @since v2.0
	 * @author Ralf Hortt <me@horttcore.de>
	 **/
	public function meta_box_location( $post )
	{

		$location = get_event_location( $post->ID );

		wp_enqueue_script( 'custom-post-type-events-admin' );

		?>

		<table class="form-table">

			<tr>
				<th><label for="event-street"><?php _e( 'Street', 'custom-post-type-events' ); ?></label> / <label for="event-street-number"><?php _e( 'Nr.', 'custom-post-type-events' ); ?></label></th>
				<td>
					<input type="text" class="regular-text" name="event-street" id="event-street" value="<?php if ( isset( $location['street'] ) ) echo $location['street'] ?>">
					<input type="text" class="regular-text" name="event-street-number" id="event-street-number" value="<?php if ( isset( $location['street-number'] ) ) echo $location['street-number'] ?>">
				</td>
			</tr>

			<tr>
				<th><?php _e( 'Addition to address', 'custom-post-type-events' ) ?></th>
				<td>
					<input type="text" class="regular-text"  name="event-addition-to-address" id="event-addition-to-address" value="<?php if ( isset( $location['addition-to-address'] ) ) echo $location['addition-to-address'] ?>">
				</td>
			</tr>

			<tr>
				<th><label for="event-zip"><?php _e( 'ZIP', 'custom-post-type-events' ); ?></label> / <label for="event-city"><?php _e( 'City', 'custom-post-type-events' ); ?></label></th>
				<td>
					<input type="text" class="regular-text" name="event-zip" id="event-zip" value="<?php if ( isset( $location['zip'] ) ) echo $location['zip'] ?>">
					<input type="text" class="regular-text" name="event-city" id="event-city" value="<?php if ( isset( $location['city'] ) ) echo $location['city'] ?>">
				</td>
			</tr>

			<tr>
				<th><label for="event-country"><?php _e( 'Country', 'custom-post-type-events'  ); ?></label></th>
				<td><input type="text" class="regular-text" name="event-country" id="event-country" value="<?php if ( isset( $location['country'] ) ) echo $location['country'] ?>" /></td>
			</tr>

			<tr>
				<th><label for="event-zip"><?php _e( 'Latitude', 'custom-post-type-events' ); ?></label> / <label for="event-city"><?php _e( 'Longitude', 'custom-post-type-events' ); ?></label></th>
				<td>
					<input type="text" class="regular-text" name="event-latitude" id="event-latitude" value="<?php if ( isset( $location['latitude'] ) ) echo $location['latitude'] ?>">
					<input type="text" class="regular-text" name="event-longitude" id="event-longitude" value="<?php if ( isset( $location['longitude'] ) ) echo $location['longitude'] ?>">
					<a href="#" class="button get-lat-long"><?php _e( 'Get by address', 'custom-post-type-events' ) ?></a>
				</td>
			</tr>

		</table>

		<?php

		wp_nonce_field( 'save-event-location', 'event-location-nonce' );

	} // END meta_box_location



	/**
	 * Update messages
	 *
	 * @access public
	 * @param array $messages Messages
	 * @return array Messages
	 * @author Ralf Hortt
	 **/
	public function post_updated_messages( $messages )
	{

		$post             = get_post();
		$post_type        = get_post_type( $post );
		$post_type_object = get_post_type_object( $post_type );

		$messages['event'] = array(
			0  => '', // Unused. Messages start at index 1.
			1  => __( 'Event updated.', 'custom-post-type-events' ),
			2  => __( 'Custom field updated.' ),
			3  => __( 'Custom field deleted.' ),
			4  => __( 'Event updated.', 'custom-post-type-events' ),
			5  => isset( $_GET['revision'] ) ? sprintf( __( 'Event restored to revision from %s', 'custom-post-type-events' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
			6  => __( 'Event published.', 'custom-post-type-events' ),
			7  => __( 'Event saved.', 'custom-post-type-events' ),
			8  => __( 'Event submitted.', 'custom-post-type-events' ),
			9  => sprintf( __( 'Event scheduled for: <strong>%1$s</strong>.', 'custom-post-type-events' ), date_i18n( __( 'M j, Y @ G:i', 'custom-post-type-events' ), strtotime( $post->post_date ) ) ),
			10 => __( 'Event draft updated.', 'custom-post-type-events' )
		);

		if ( $post_type_object->publicly_queryable ) :

			$permalink = get_permalink( $post->ID );

			$view_link = sprintf( ' <a href="%s">%s</a>', esc_url( $permalink ), __( 'View event', 'custom-post-type-events' ) );
			$messages[ $post_type ][1] .= $view_link;
			$messages[ $post_type ][6] .= $view_link;
			$messages[ $post_type ][9] .= $view_link;

			$preview_permalink = add_query_arg( 'preview', 'true', $permalink );
			$preview_link = sprintf( ' <a target="_blank" href="%s">%s</a>', esc_url( $preview_permalink ), __( 'Preview event', 'custom-post-type-events' ) );
			$messages[ $post_type ][8]  .= $preview_link;
			$messages[ $post_type ][10] .= $preview_link;

		endif;

		return $messages;

	} // END post_updated_messages



	/**
	 * Save post callback
	 *
	 * @access public
	 * @param int $post_id Post id
	 * @return void
	 * @since v2.0
	 * @author Ralf Hortt <me@horttcore.de>
	 **/
	public function save_event( $post_id )
	{

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		if ( !isset( $_POST['event-info-nonce'] ) || !wp_verify_nonce( $_POST['event-info-nonce'], 'save-event-info' ) )
			return;

		// Info
		$multiday = ( $_POST['event-multi-day'] ) ? TRUE : FALSE;
		$hastime = ( $_POST['event-time'] ) ? TRUE : FALSE;
		update_post_meta( $post_id, '_event-info', array(
			'multi-day' => $multiday,
			'time' => $hastime,
		) );

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
			$date_end = mktime( 23, 59, 59, $date_end[1], $date_end[0], $date_end[2] );
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

	} // END save_event



	/**
	 * Save post callback
	 *
	 * @access public
	 * @param int $post_id Post id
	 * @return void
	 * @since v2.0
	 * @author Ralf Hortt <me@horttcore.de>
	 **/
	public function save_location( $post_id )
	{

		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
			return;

		if ( !isset( $_POST['event-location-nonce'] ) || !wp_verify_nonce( $_POST['event-location-nonce'], 'save-event-location' ) )
			return;

		// Save lat/long
		update_post_meta( $post_id, '_event-location', array(
			'street' => sanitize_text_field( $_POST['event-street'] ),
			'street-number' => sanitize_text_field( $_POST['event-street-number'] ),
			'addition-to-address' => sanitize_text_field( $_POST['event-addition-to-address'] ),
			'zip' => sanitize_text_field( $_POST['event-zip'] ),
			'city' => sanitize_text_field( $_POST['event-city'] ),
			'country' => sanitize_text_field( $_POST['event-country'] ),
			'latitude' => sanitize_text_field( $_POST['event-latitude'] ),
			'longitude' => sanitize_text_field( $_POST['event-longitude'] ),
		) );

	} // END save_location



} // END final class Custom_Post_Type_Events_Admin

new Custom_Post_Type_Events_Admin;
