<?php
/**
 * Match functions
 *
 * All the functions related to match handling.
 *
 * @package The Soccer Stats
 * @since 1.0
 */

require_once( 'get-players-in-match.php' );
require_once( 'get-matches-in-season.php' );

/**
 * Retrieves match details to an array
 *
 * @since 1.0
 * @return array
 */
function tss_get_match_details() {
	global $post;

	$meta = get_post_meta( $post->ID );

	// My team.
	$options = get_option( 'tss_options' );
	$myteam = get_the_title( $options['my_team'] );

	$content = get_post_field( 'post_content', $post->ID );
	$content = apply_filters( 'the_content', $content );

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
		'tss-match-attendance',
	 	'tss-match-overtime',
		'tss-match-penalties',
		'tss-match-goals-for-pen',
		'tss-match-goals-against-pen',
		'tss-match-calculate-stats',
		'tss-match-show-opponent-stats',
		'tss-match-opponent-starters',
		'tss-match-opponent-substitutes',
		'tss-match-opponent-substitutions',
		'tss-match-opponent-goals',
		'tss-match-opponent-yellows',
		'tss-match-opponent-reds',
	);

	$data = array();

	foreach ( $metafields as $metafield ) {
		$short_index = str_replace( 'tss-match-', '', $metafield );
		if ( isset( $meta[ $metafield ] ) ) {
			if ( 'tss-match-opponent' == $metafield ) { // Special check if opponent.
				$data[ $short_index ] = get_the_title( $meta[ $metafield ][0] );
			} elseif ( 'tss-match-matchtype' == $metafield ) { // Special check if matchtype.
				$data[ $short_index ] = get_the_title( $meta[ $metafield ][0] );
			} elseif ( 'tss-match-location' == $metafield ) { // Special check if location.
				if ( '1' == $meta[ $metafield ][0] || '3' == $meta[ $metafield ][0] ) { // If home or neutral match.
					$data['hometeam'] = $myteam;
					$data['hometeam_logo_src'] = tss_retrieve_featured_image_url( $options['my_team'] );
					$data['awayteam'] = $data['opponent'];
					$data['awayteam_logo_src'] = tss_retrieve_featured_image_url( $meta['tss-match-opponent'][0] );
					$data['homegoals'] = $meta['tss-match-goals-for'][0];
					$data['awaygoals'] = $meta['tss-match-goals-against'][0];
					$data['homegoalspen'] = $data['goals-for-pen'];
					$data['awaygoalspen'] = $data['goals-against-pen'];
				} else { // If away match.
					$data['hometeam_logo_src'] = tss_retrieve_featured_image_url( $meta['tss-match-opponent'][0] );
					$data['hometeam'] = $data['opponent'];
					$data['awayteam'] = $myteam;
					$data['awayteam_logo_src'] = tss_retrieve_featured_image_url( $options['my_team'] );
					$data['homegoals'] = $meta['tss-match-goals-against'][0];
					$data['awaygoals'] = $meta['tss-match-goals-for'][0];
					$data['homegoalspen'] = $data['goals-against-pen'];
					$data['awaygoalspen'] = $data['goals-for-pen'];
				}
				$data[ $short_index ] = $meta['tss-match-location'][0];
			} else {
				$data[ $short_index ] = $meta[ $metafield ][0];
			}
		} else {
			$data[ $short_index ] = '';
		}
	}

	if ( '' != $data['date'] ) {
		$data['date'] = date_i18n( get_option( 'date_format' ), strtotime( $data['date'] ) );
	} else {
		$data['date'] = '';
	}

	$data['my_team'] = tss_get_players_in_match( $post->ID );

	return json_encode( $data );
}

/**
 * Echoes my team details in match
 *
 * @since 1.0
 * @param array $data Includes all the data in match.
 */
function tss_e_myteam_details( $data ) {
	// Print my team.
	echo '<h3>' . esc_html__( 'Starters', 'tss' ) . '</h3>';
	foreach ( $data->my_team->starters as $player ) {
		echo '<a href="' . get_permalink( $player->id ) . '">' . esc_html( $player->name ) . '</a><br/>';
	}
	echo '<h3>' . esc_html__( 'Substitutes', 'tss' ) . '</h3>';
	foreach ( $data->my_team->substitutes as $player ) {
		echo '<a href="' . get_permalink( $player->id ) . '">' . esc_html( $player->name ) . '</a><br/>';
	}
	echo '<h3>' . esc_html__( 'Substitutions', 'tss' ) . '</h3>';
	foreach ( $data->my_team->substitutions as $player ) {
		echo '<a href="' . get_permalink( $player->id_in ) . '">' . esc_html( $player->name_in ) . '</a> <i class="fa fa-exchange"></i> <a href="' . get_permalink( $player->id_out ) . '">' . esc_html( $player->name_out ) . '</a> (' . $player->minute . ')<br/>';
	}
	echo '<h3>' . esc_html__( 'Goals', 'tss' ) . '</h3>';
	foreach ( $data->my_team->goals as $player ) {
		$scorer_row = '<a href="' . get_permalink( $player->id ) . '">' . $player->name . '</a> (' . $player->minute . ')';

		if ( 1 == $player->is_penalty ) {
			$scorer_row = $scorer_row . ' (' . __( 'pen.', 'tss' ) . ')';
		}

		if ( 1 == $player->is_own ) {
			$scorer_row = $player->ownscorer . ' (' . $player->minute . ')' . ' (' . __( 'O.G.', 'tss' ) . ')';
		}
		echo $scorer_row . '<br/>';
	}
	echo '<h3>' . esc_html__( 'Yellow cards', 'tss' ) . '</h3>';
	foreach ( $data->my_team->yellows as $player ) {
		echo '<a href="' . get_permalink( $player->id ) . '">' . esc_html( $player->name ) . '</a> (' . $player->minute . ')<br/>';
	}
	echo '<h3>' . esc_html__( 'Red cards', 'tss' ) . '</h3>';
	foreach ( $data->my_team->reds as $player ) {
		echo '<a href="' . get_permalink( $player->id ) . '">' . esc_html( $player->name ) . '</a> (' . $player->minute . ')<br/>';
	}
}

/**
 * Echoes opponent details in match
 *
 * @since 1.0
 * @param array $data Includes all the data in match.
 */
function tss_e_opponent_details( $data ) {
	// Print opponent.
	echo '<h3>' . esc_html__( 'Starters', 'tss' ) . '</h3>';
	echo nl2br( $data->{'opponent-starters'} );
	echo '<h3>' . esc_html__( 'Substitutes', 'tss' ) . '</h3>';
	echo nl2br( $data->{'opponent-substitutes'} );
	echo '<h3>' . esc_html__( 'Substitutions', 'tss' ) . '</h3>';
	echo nl2br( str_replace( '->', '<i class="fa fa-exchange"></i>', $data->{'opponent-substitutions'} ) );
	echo '<h3>' . esc_html__( 'Goals', 'tss' ) . '</h3>';
	echo nl2br( $data->{'opponent-goals'} );
	echo '<h3>' . esc_html__( 'Yellow cards', 'tss' ) . '</h3>';
	echo nl2br( $data->{'opponent-yellows'} );
	echo '<h3>' . esc_html__( 'Red cards', 'tss' ) . '</h3>';
	echo nl2br( $data->{'opponent-reds'} );
}

/**
 * Echoes matchtype
 *
 * @since 1.0
 * @param string $primary Primary matchtype.
 * @param string $secondary Secondary matchtype.
 * @return string
 */
function tss_e_matchtype( $primary, $secondary ) {
if ( '' != $secondary ) {
	$string = $primary . ' / ' . $secondary;
} else {
	$string = $primary;
}
	return $string;
}

/**
 * Returns string with info abuot the game (FT, AET or PEN)..
 *
 * @since 1.0
 * @param int $overtime Whether overtime.
 * @param int $penalties Whether penalties.
 * @return string
 */
function tss_get_match_time( $overtime, $penalties ) {
	$string = __( 'FT', 'tss' );

	if ( '1' == $overtime ) {
		$string = __( 'AET', 'tss' );
	}

	if ( '1' == $penalties ) {
		$string = __( 'PEN.', 'tss' );
	}

	return $string;
}

/**
 * Returns string with goal (inc. penalties or not)
 *
 * @since 1.0
 * @param int $penalties Whether penalties.
 * @param int $normal_goals Normal goals.
 * @param int $pen_goals Penalty goals.
 * @return string
 */
function tss_get_goal_in_match( $penalties, $normal_goals, $pen_goals ) {
	$string = '';
	if ( '1' == $penalties ) {
		$string = $pen_goals . ' (' . $normal_goals . ')';
	} else {
		$string = $normal_goals;
	}

	return $string;
}

/**
 * Returns string with result
 *
 * @since 1.0
 * @param int $matchid Match ID.
 * @param int $penalties Whether penalties.
 * @param int $penalties Whether overtime.
 * @param int $homegoals Homegoals.
 * @param int $awaygoals Awaygoals.
 * @param int $homegoalspen Homegoals pen.
 * @param int $awaygoalspen Awaygoals pen.
 * @return string
 */
function tss_get_result_string( $penalties, $overtime, $homegoals, $awaygoals, $homegoalspen, $awaygoalspen  ) {
	$string = $homegoals . '-' . $awaygoals;
	if ( '1' == $overtime ) {
		$string = $string . ' ' . __( 'AET', 'tss' );
	} elseif ( '1' == $penalties ) {
		$string = $string . ' (' . $homegoalspen . '-' . $awaygoalspen . ')' . ' ' . __( 'PEN.', 'tss' );
	}

	if ( '' == $homegoals || '' == $awaygoals ) {
		$string = '';
	}

	return $string;
}

/**
 * Returns string with team or link to Team
 *
 * @since 1.0
 * @param $team string Team name.
 * @param $teamid int Team ID.
 * @return string
 */
function e_team_with_link( $team, $teamid ) {
	$options = get_option( 'tss_options' );
	$myteam = get_the_title( $options['my_team'] );

	if( $team == $myteam ) {
		return $team;
	} else {
		return '<a href="' . get_permalink( $teamid ) . '">' . $team . '</a>';
	}
}

/**
 * Returns string with date and time or only with date
 *
 * @since 1.04
 * @param $date string Date of match.
 * @param $time string Time of match.
 * @return string
 */
function tss_e_match_date_and_time( $date, $time ) {
	if( '' == $time ) {
		return $date;
	} else {
		return $date . ' @ ' . $time;
	}
}
?>
