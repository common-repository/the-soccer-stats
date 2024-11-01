<?php
/**
 * Opponent functions
 *
 * All the functions related to opponent handling.
 *
 * @package The Soccer Stats
 * @since 1.0
 */

require_once( 'get-matches-by-opponent.php' );

/**
 * Return thumbnail logo in img-tag if found..
 *
 * @param $teamName string Team name
 * @since 1.06
 * @return string
 */
function tss_get_team_logo_thumbnail( $teamName ) {
	$post = get_page_by_title( $teamName, OBJECT, 'tss-opponents' );

	$image = get_the_post_thumbnail( $post->ID, 'thumbnail' );

	return $image;
}

?>
