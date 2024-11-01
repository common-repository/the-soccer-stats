<?php
/**
 * Retrieves matches in a single season
 *
 * Get matchdetails to array
 *
 * @package The Soccer Stats
 * @since 1.0
 */

/**
 *
 * Retrives matches data to arrays
 *
 * @since 1.0
 * @param int $seasonid Season ID that's requested.
 */
function tss_get_matches_in_season( $seasonid ) {
	global $wpdb;

	$data = array(); // Data thats returned

	// My team.
	$options = get_option( 'tss_options' );
	$myteam = get_the_title( $options['my_team'] );

	$metafields = array(
	'tss-match-goals-for',
	'tss-match-goals-against',
	'tss-match-goals-for-pen',
	'tss-match-goals-against-pen',
	'tss-match-opponent',
	'tss-match-date',
	'tss-match-datetime',
	'tss-match-location',
	'tss-match-matchtype',
	'tss-match-additional-matchtype',
	'tss-match-overtime',
	'tss-match-penalties',
	);

	$posts_table = $wpdb->prefix . 'posts';
	$posts_metatable = $wpdb->prefix . 'postmeta';

	$q = "SELECT p.ID AS id,
	pm2.meta_value AS matchdate
		FROM $posts_metatable pm, $posts_metatable pm2, $posts_table p
		WHERE (p.ID = pm.post_id AND pm.meta_value = $seasonid AND pm.meta_key = 'tss-match-season') AND
		(p.ID = pm2.post_id AND pm2.meta_key = 'tss-match-date') ORDER BY matchdate";

	$results = $wpdb->get_results( $q, OBJECT );

	/*
	 * Loops Matches
	 */
	if ( $results ) {
		foreach ( $results as $row ) {
			$meta = get_post_meta( $row->id );
			$singlerow = array();
			$singlerow[ 'id' ] = $row->id;

			foreach ( $metafields as $metafield ) {
				$short_index = str_replace( 'tss-match-', '', $metafield );
				if ( isset( $meta[ $metafield ] ) ) {
					if ( 'tss-match-opponent' == $metafield ) { // Special check if opponent.
						$singlerow[ $short_index ] = get_the_title( $meta[ $metafield ][0] );
						$singlerow[ $short_index . '-id' ] = $meta[ $metafield ][0];
					} elseif ( 'tss-match-matchtype' == $metafield ) { // Special check if matchtype.
						$singlerow[ $short_index ] = get_the_title( $meta[ $metafield ][0] );
					} elseif ( 'tss-match-location' == $metafield ) { // Special check if location.
						if ( '1' == $meta[ $metafield ][0] || '3' == $meta[ $metafield ][0] ) { // If home or neutral match.
							$singlerow['hometeam'] = $myteam;
							$singlerow['awayteam'] = $singlerow['opponent'];
							$singlerow['homegoals'] = $meta['tss-match-goals-for'][0];
							$singlerow['awaygoals'] = $meta['tss-match-goals-against'][0];
							$singlerow['homegoalspen'] = $singlerow['goals-for-pen'];
							$singlerow['awaygoalspen'] = $singlerow['goals-against-pen'];
						} else { // If away match.
							$singlerow['hometeam'] = $singlerow['opponent'];
							$singlerow['awayteam'] = $myteam;
							$singlerow['homegoals'] = $meta['tss-match-goals-against'][0];
							$singlerow['awaygoals'] = $meta['tss-match-goals-for'][0];
							$singlerow['homegoalspen'] = $singlerow['goals-against-pen'];
							$singlerow['awaygoalspen'] = $singlerow['goals-for-pen'];
						}
						$singlerow[ $short_index ] = $meta['tss-match-location'][0];
					} else {
						$singlerow[ $short_index ] = $meta[ $metafield ][0];
					}
				} else {
					$singlerow[ $short_index ] = '';
				}
			}

			if ( '' != $singlerow['date'] ) {
				$singlerow['date'] = date_i18n( get_option( 'date_format' ), strtotime( $singlerow['date'] ) );
			} else {
				$singlerow['date'] = '';
			}

			array_push( $data,
				$singlerow
			);

		}
	}

	return json_encode( $data );
}

?>
