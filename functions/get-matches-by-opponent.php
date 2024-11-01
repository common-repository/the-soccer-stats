<?php
/**
 * Retrieves matches by opponent
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
 * @param int    $opponentid Opponent ID that's requested.
 */
function tss_get_matches_by_opponent( $opponentid ) {
	global $wpdb;

	$data['matches'] = array(); // Data thats returned
	$data['stats'] = array(); // Data thats returned

	$stats['homewins'] = 0;
	$stats['homedraws'] = 0;
	$stats['homeloses'] = 0;
	$stats['homegoals'] = 0;
	$stats['homegoals_against'] = 0;
	$stats['awaywins'] = 0;
	$stats['awaydraws'] = 0;
	$stats['awayloses'] = 0;
	$stats['awaygoals'] = 0;
	$stats['awaygoals_against'] = 0;
	$stats['neutralwins'] = 0;
	$stats['neutraldraws'] = 0;
	$stats['neutralloses'] = 0;
	$stats['neutralgoals'] = 0;
	$stats['neutralgoals_against'] = 0;
	$stats['totalwins'] = 0;
	$stats['totaldraws'] = 0;
	$stats['totalloses'] = 0;
	$stats['totalgoals'] = 0;
	$stats['totalgoals_against'] = 0;

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
		'tss-match-calculate-stats'
	);

	$posts_table = $wpdb->prefix . 'posts';
	$posts_metatable = $wpdb->prefix . 'postmeta';

	$q = "SELECT p.ID AS id,
	p.post_title AS title,
	pm.meta_value AS matchdate
	FROM wp_posts p, wp_postmeta pm, wp_postmeta pm2
	WHERE (p.ID = pm.post_id AND pm.meta_key = 'tss-match-date') AND
	(p.ID = pm2.post_id AND pm2.meta_key = 'tss-match-opponent' AND pm2.meta_value = $opponentid) ORDER BY matchdate DESC";

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

			// Calculate stats.
			if ( '1' == $singlerow['calculate-stats']) {
				if ( '1' == $singlerow['location']) {

					if ($singlerow['goals-for'] > $singlerow['goals-against'] ) {
						$stats['homewins']++;
					}

					if ($singlerow['goals-for'] < $singlerow['goals-against'] ) {
						$stats['homeloses']++;
					}

					if ($singlerow['goals-for'] == $singlerow['goals-against'] ) {
						$stats['homedraws']++;
					}

					$stats['homegoals'] = $stats['homegoals'] + $singlerow['goals-for'];
					$stats['homegoals_against'] = $stats['homegoals_against'] + $singlerow['goals-against'];

				} elseif ( '2' == $singlerow['location'] ) {

					if ($singlerow['goals-for'] > $singlerow['goals-against'] ) {
						$stats['awaywins']++;
					}

					if ($singlerow['goals-for'] < $singlerow['goals-against'] ) {
						$stats['awayloses']++;
					}

					if ($singlerow['goals-for'] == $singlerow['goals-against'] ) {
						$stats['awaydraws']++;
					}

					$stats['awaygoals'] = $stats['awaygoals'] + $singlerow['goals-for'];
					$stats['awaygoals_against'] = $stats['awaygoals_against'] + $singlerow['goals-against'];

				} elseif ( '3' == $singlerow['location'] ) {

					if ($singlerow['goals-for'] > $singlerow['goals-against'] ) {
						$stats['neutralwins']++;
					}

					if ($singlerow['goals-for'] < $singlerow['goals-against'] ) {
						$stats['neutralloses']++;
					}

					if ($singlerow['goals-for'] == $singlerow['goals-against'] ) {
						$stats['neutraldraws']++;
					}

					$stats['neutralgoals'] = $stats['neutralgoals'] + $singlerow['goals-for'];
					$stats['neutralgoals_against'] = $stats['neutralgoals_against'] + $singlerow['goals-against'];

				}
			}


			array_push( $data['matches'],
				$singlerow
			);

		}
	}

	$stats['totalwins'] = $stats['homewins'] + $stats['awaywins'] + $stats['neutralwins'];
	$stats['totalloses'] = $stats['homeloses'] + $stats['awayloses'] + $stats['neutralloses'];
	$stats['totaldraws'] = $stats['homedraws'] + $stats['awaydraws'] + $stats['neutraldraws'];
	$stats['totalgoals'] = $stats['homegoals'] + $stats['awaygoals'] + $stats['neutralgoals'];
	$stats['totalgoals_against'] = $stats['homegoals_against'] + $stats['awaygoals_against'] + $stats['neutralgoals_against'];



	array_push( $data['stats'],
		$stats
	);

	return json_encode( $data );
}

?>
