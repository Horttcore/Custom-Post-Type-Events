<?php
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
