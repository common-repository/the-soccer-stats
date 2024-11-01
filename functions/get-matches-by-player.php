<?php
/**
 * Retrieves matches by player to get latest where scored, booked etc..
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
 * @param int    $playerid Player ID that's requested.
 * @param string $playertable Database table thats queried.
 */
function tss_get_matches_by_player( $playerid, $playertable ) {
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

	$q = "SELECT DISTINCT p.ID AS id,
	pm.meta_value AS matchdate
	FROM $posts_metatable pm, $posts_table p, $playertable
	WHERE ($playertable.matchid = p.id AND $playertable.playerid = $playerid) AND
	(p.ID = pm.post_id AND pm.meta_key = 'tss-match-date') ORDER BY matchdate DESC LIMIT 5";

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

/**
 *
 * Echoes matches where player scored, booked etc..
 *
 * @since 1.0
 * @param int    $playerid Player ID that queried.
 * @param int    $title Title of the echoed section.
 * @param string $playertable Database table thats queried.
 */
function tss_e_latest_matches_by_player($playerid, $title, $playertable) {
	$json = tss_get_matches_by_player($playerid, $playertable);
	$data = json_decode( $json );
	?>
	<div class="row">
		<div class="col-sm-12">
			<h3><?php echo esc_html__( $title ); ?></h3>
		</div>
	</div>

	<div class="row">
		<div class="col-sm-12">

			<table class="table">
				<thead>
					<tr>
						<th><?php echo esc_html__( 'Date', 'tss' ) ?></th>
						<th class="text-center"><?php echo esc_html__( 'Hometeam', 'tss' ) ?></th>
						<th class="text-center"><?php echo esc_html__( 'Awayteam', 'tss' ) ?></th>
						<th class="text-center"><?php echo esc_html__( 'Matchtype', 'tss' ) ?></th>
						<th class="text-center"><?php echo esc_html__( 'Result', 'tss' ) ?></th>
					</tr>
				</thead>
				<tbody>
					<?php
					if($data) {
						foreach ($data as $match) {
							?>
							<tr>
								<td><?php echo esc_html( tss_e_match_date_and_time( $match->date, $match->datetime ) ) ?></td>
								<td class="text-center"><?php echo e_team_with_link( $match->hometeam, $match->{'opponent-id'} ) ?></td>
								<td class="text-center"><?php echo e_team_with_link( $match->awayteam, $match->{'opponent-id'} ) ?></td>
								<td class="text-center"><?php echo esc_html( tss_e_matchtype( $match->matchtype, $match->{'additional-matchtype'} ) ) ?></td>
								<td class="text-center"><a href="<?php echo esc_url( get_permalink( $match->id ) ) ?>"><?php echo esc_html( tss_get_result_string( $match->penalties, $match->overtime, $match->homegoals, $match->awaygoals, $match->homegoalspen, $match->awaygoalspen  ) ) ?></a></td>
							</tr>
							<?php
						}
					} else {
						echo '<tr><td colspan="5">' . esc_html__( 'None', 'tss' ) . '</td></tr>';
					}

					?>
				</tbody>
			</table>
		</div>
	</div>
	<?php
}
?>
