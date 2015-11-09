<?php
if ( !function_exists( 'has_event_location' ) ) :
/**
 * Conditional if event location is set
 *
 * @param int $post_id Post ID
 * @return bool
 * @author Ralf Hortt
 **/
function has_event_location( $field = FALSE, $post_id = FALSE )
{

	$post_id = ( FALSE !== $post_id ) ? $post_id : get_the_ID();
	$location = array_filter(get_event_location());


	if ( FALSE === $field )
		return ( $location ) ? TRUE : FALSE;

	return ( isset( $location[$field] ) ) ? TRUE : FALSE;

}
endif;



if ( !function_exists( 'get_event_location' ) ) :
/**
 * Get event location
 *
 * @param int $post_id Post ID
 * @param str $field Location field
 * @return str/array Location
 * @author Ralf Hortt
 **/
function get_event_location( $post_id = FALSE, $field = FALSE )
{
	$post_id = ( FALSE === $post_id ) ? get_the_ID() : $post_id;
	$location = get_post_meta( $post_id, '_event-location', TRUE );

	if ( FALSE !== $field )
		return $location[$field];

	return $location;
}
endif;



if ( !function_exists( 'get_event_date' ) ) :
/**
 * Get event location
 *
 * @param int $post_id Post ID
 * @param str $field Location field
 * @return str/array Location
 * @author Ralf Hortt
 **/
function get_event_info( $post_id = FALSE )
{

	$post_id = ( FALSE !== $post_id ) ? $post_id : get_the_ID();

	$info = get_post_meta( $post_id, '_event-info', TRUE );
	$start = get_post_meta( $post_id, '_event-date-start', TRUE );
	$end = get_post_meta( $post_id, '_event-date-end', TRUE );

	$time_start = ( TRUE === $info['time'] ) ? get_post_meta( $post_id, '_event-time-start', TRUE ) : FALSE;
	$time_end = ( TRUE === $info['time'] ) ? get_post_meta( $post_id, '_event-time-end', TRUE ) : FALSE;

	return array(
		'multi-day' => $info['multi-day'],
		'time' => $info['time'],
		'start' => $start,
		'time-start' => $time_start,
		'end' => $end,
		'time-end' => $time_end,
	);

}
endif;



if ( !function_exists( 'get_event_datetime' ) ) :
/**
 * Get event location
 *
 * @param int $post_id Post ID
 * @param str $field Location field
 * @return str/array Location
 * @author Ralf Hortt
 **/
function get_event_datetime( $format = FALSE, $post_id = FALSE )
{

	$format = ( FALSE !== $format ) ? $format : get_option( 'date_format' );
	$post_id = ( FALSE !== $post_id ) ? $post_id : get_the_ID();

	$event = get_event_info( $post_id );
	$output = get_event_date( $format, $post_id );

	if ( $event['time'] )
		$output .= apply_filters( 'event_datetime_seperator', ' | ' ) . get_event_time();

	return $output;

}
endif;



if ( !function_exists( 'the_event_datetime' ) ) :
/**
 * Get event location
 *
 * @param int $post_id Post ID
 * @param str $field Location field
 * @return str/array Location
 * @author Ralf Hortt
 **/
function the_event_datetime( $format = FALSE, $post_id = FALSE )
{

	$format = ( FALSE !== $format ) ? $format : get_option( 'date_format' );
	$post_id = ( FALSE !== $post_id ) ? $post_id : get_the_ID();

	echo get_event_datetime();

}
endif;



if ( !function_exists( 'has_event_date' ) ) :
/**
 * Conditional if event location is set
 *
 * @param int $post_id Post ID
 * @return bool
 * @author Ralf Hortt
 **/
function has_event_date( $post_id = FALSE )
{

	$post_id = ( FALSE !== $post_id ) ? $post_id : get_the_ID();
	$data = get_event_info( $post_id );

	return ( $data['start'] ) ? TRUE : FALSE;

}
endif;



if ( !function_exists( 'get_event_date' ) ) :
/**
 * Get event location
 *
 * @param int $post_id Post ID
 * @param str $field Location field
 * @return str/array Location
 * @author Ralf Hortt
 **/
function get_event_date( $format = FALSE, $post_id = FALSE )
{

	$format = ( FALSE !== $format ) ? $format : get_option( 'date_format' );
	$post_id = ( FALSE !== $post_id ) ? $post_id : get_the_ID();

	$event = get_event_info();

	if ( $event['multi-day'] )
		$output = sprintf( '%s - %s', date_i18n( $format, $event['start'] ), date_i18n( $format, $event['end'] ) );
	else
		$output = date_i18n( $format, $event['start'] );

	return $output;

}
endif;



if ( !function_exists( 'the_event_date' ) ) :
/**
 * Get event location
 *
 * @param int $post_id Post ID
 * @param str $field Location field
 * @return str/array Location
 * @author Ralf Hortt
 **/
function the_event_date( $format = FALSE, $post_id = FALSE )
{

	$format = ( FALSE !== $format ) ? $format : get_option( 'date_format' );
	$post_id = ( FALSE !== $post_id ) ? $post_id : get_the_ID();

	echo get_event_date();

}
endif;



if ( !function_exists( 'has_event_time' ) ) :
/**
 * Conditional if event time is set
 *
 * @param int $post_id Post ID
 * @return bool
 * @author Ralf Hortt
 **/
function has_event_time( $post_id = FALSE )
{

	$post_id = ( FALSE !== $post_id ) ? $post_id : get_the_ID();
	return ( '' !== get_event_time( 'H:i', $post_id ) ) ? TRUE : FALSE;

}
endif;



if ( !function_exists( 'get_event_time' ) ) :
/**
 * Get event location
 *
 * @param int $post_id Post ID
 * @param str $field Location field
 * @return str/array Location
 * @author Ralf Hortt
 **/
function get_event_time( $format = 'H:i', $post_id = FALSE )
{

	$format = ( FALSE !== $format ) ? $format : get_option( 'date_format' );
	$post_id = ( FALSE !== $post_id ) ? $post_id : get_the_ID();

	$event = get_event_info();

	if ( !$event['time'] )
		return '';

	if ( $event['time-end'] )
		return sprintf( _x( '%sh - %sh', 'Time range', 'custom-post-type-events' ), date_i18n( $format, $event['time-start'] ), date_i18n( $format, $event['time-end'] ) );
	else
		return sprintf( _x( '%sh', 'Singe time', 'custom-post-type-events' ), date_i18n( 'H:i', $event['time-start'] ) );

}
endif;



if ( !function_exists( 'the_event_time' ) ) :
/**
 * Get event location
 *
 * @param int $post_id Post ID
 * @param str $field Location field
 * @return str/array Location
 * @author Ralf Hortt
 **/
function the_event_time( $format = 'H:i', $post_id = FALSE )
{

	$format = ( FALSE !== $format ) ? $format : get_option( 'date_format' );
	$post_id = ( FALSE !== $post_id ) ? $post_id : get_the_ID();

	echo get_event_time( $format, $post_id );

}
endif;
