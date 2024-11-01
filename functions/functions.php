<?php
/**
 * Functions file
 *
 * The Soccer Stats functions.
 *
 * @package The Soccer Stats
 * @since 1.0
 */

require_once( 'admin-related-functions.php' );
require_once( 'players.php' );
require_once( 'matches.php' );
require_once( 'opponents.php' );

/**
 * Checks if checkbox should be checked or not.
 *
 * @since 1.0
 * @return string checked.
 */
function tss_is_checked( $meta_index, $meta ) {
	$checked = '';
	if ( isset( $meta[$meta_index] ) ) {
		if ( '1' == $meta[$meta_index][0] ) {
			$checked = 'checked="checked"';
		}
	}
	return $checked;
}

/**
 * Returns the container used in templates
 *
 * @since 1.0
 * @return string container.
 */
function tss_get_container() {
	$options = get_option( 'tss_options' );

	if( ! isset( $options['container'] ) ) {
		$options['container'] = 'container-fluid';
	}

	return $options['container'];
}

/**
 * Checks if custom db tables exists
 *
 * @since 1.0
 * @return boolean IF tables are found or not.
 */
function tss_check_custom_db_tables() {
	global $wpdb;

	$tables_found = true;

	$tables = array( 'tss_goals', 'tss_starters', 'tss_substitutes', 'tss_substitutions', 'tss_redcards', 'tss_yellowcards', 'tss_player_seasons' );

	foreach ( $tables as $table ) {
		$q = "SHOW TABLES LIKE '$table'";
		$results = $wpdb->get_results( $q, OBJECT );

		if ( ! $results ) {
			$tables_found = false;
		}
	}

	return $tables_found;
}

/**
 *
 * Retrieves featured image url of the post_id.
 *
 * @since 1.0
 * @param int $post_id Post ID.
 * @return string Image URL.
 */
function tss_retrieve_featured_image_url( $post_id ) {
	if ( has_post_thumbnail( $post_id ) ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'single-post-thumbnail' );
		$image_url = $image[0];
	} else {
		$image_url = '';
	}

	return $image_url;
}

/**
 *
 * Checks if integers are really integers.
 *
 * @since 1.0
 * @param array $array Array of input.
 * @return true/false.
 */
function tss_sanitaze_ajax_input($array) {
	$valid_data = true;

	foreach ( $array as $value ) {
		$safe = intval( $value );

		// If input is not integers.
		if ( ! $safe ) {
		  $valid_data = false;
		}
	}

	return $valid_data;
}
?>
