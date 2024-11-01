<?php
/**
 * Admin ajax
 *
 * Ajax function that are used in wp-admin.
 *
 * @package The Soccer Stats
 * @since 1.0
 */

/**
 *
 * Ajax: Add player to season.
 *
 * @since 1.0
 */
function tss_add_player_to_season() {
	$nonce = $_POST['nonce'];
	$playerid = $_POST['playerid'];
	$seasonid = $_POST['seasonid'];

	if( false === tss_sanitaze_ajax_input( [ $playerid, $seasonid ] ) ) {
		die( 'Data validation error!!' );
	}

	if ( ! wp_verify_nonce( $nonce, 'tss-player-seasons-nonce' ) ) {
			die( 'Try harder ;)' );
	}

	if ( current_user_can( 'edit_posts' ) ) {
		global $wpdb;

		$q = 'SELECT * FROM tss_player_seasons WHERE playerid = \'' . $playerid . '\' AND seasonid = \'' . $seasonid .'\'';
		$results = $wpdb->get_results( $q, OBJECT );

		if ( ! $results ) {
			$wpdb->insert(
				'tss_player_seasons',
				array(
				'playerid' => $playerid,
				'seasonid' => $seasonid,
				),
				array(
				'%d',
				'%d',
				)
			);
		}
		?>
		<strong><?php echo esc_html__( 'Player added to following seasons:', 'tss' ); ?><br/></strong>
		<?php
		tss_list_player_seasons( $playerid );
	}

	die(); // Stop executing script.
}
add_action( 'wp_ajax_add_player_to_season', 'tss_add_player_to_season' ); // Ajax for logged in users.
add_action( 'wp_ajax_nopriv_add_player_to_season', 'tss_add_player_to_season' ); // Ajax for not logged in users.

/**
 *
 * Ajax: Add substitution.
 *
 * @since 1.0
 */
function tss_add_substitution() {

	$nonce = $_POST['nonce'];
	$playerin = $_POST['playerin'];
	$playerout = $_POST['playerout'];
	$matchid = $_POST['matchid'];
	$minute = $_POST['minute'];

	if( false === tss_sanitaze_ajax_input( [ $playerin, $playerout, $matchid, $minute ] ) ) {
		die( 'Data validation error!!' );
	}

	if ( ! wp_verify_nonce( $nonce, 'tss-player-seasons-nonce' ) ) {
			die( 'Try harder ;)' );
	}

	if ( current_user_can( 'edit_posts' ) ) {
		global $wpdb;

		$q = "SELECT * FROM tss_substitutions WHERE playeridin = '$playerin' AND playeridout = '$playerout' AND matchid='$matchid'";
		$results = $wpdb->get_results( $q, OBJECT );

		if ( ! $results ) {
			$wpdb->insert(
				'tss_substitutions',
				array(
				'playeridin' => $playerin,
				'playeridout' => $playerout,
				'minute' => $minute,
				'matchid' => $matchid,
				),
				array(
				'%d',
				'%d',
				'%d',
				'%d',
				)
			);
		}
		?>
		<strong><?php echo esc_html__( 'Substitutions:', 'tss' ); ?><br/></strong>
		<?php
		get_substitutions( $matchid, 'tss_substitutions' );
	}

	die(); // Stop executing script.
}
add_action( 'wp_ajax_add_substitution', 'tss_add_substitution' ); // Ajax for logged in users.
add_action( 'wp_ajax_nopriv_add_substitution', 'tss_add_substitution' ); // Ajax for not logged in users.

/**
 *
 * Ajax: Add goal
 *
 * @since 1.0
 */
function tss_add_goal() {

	$nonce = $_POST['nonce'];
	$playerid = $_POST['playerid'];
	$matchid = $_POST['matchid'];
	$is_penalty = $_POST['penalty'];
	$is_own = $_POST['own'];
	$minute = $_POST['minute'];
	$ownscorer = sanitize_text_field( $_POST['ownscorer'] );

	if( false === tss_sanitaze_ajax_input( [ $playerid, $matchid, $minute ] ) ) {
		die( 'Data validation error!!' );
	}

	if( ( 1 != $is_penalty && 0 != $is_penalty ) && ( 1 != $is_own && 0 != $is_own ) ) {
		die( 'Data validation error!!' );
	}

	if ( ! wp_verify_nonce( $nonce, 'tss-player-seasons-nonce' ) ) {
			die( 'Try harder ;)' );
	}

	if ( current_user_can( 'edit_posts' ) ) {
		global $wpdb;

		$wpdb->insert(
			'tss_goals',
			array(
			'playerid' => $playerid,
			'matchid' => $matchid,
			'minute' => $minute,
			'own' => $is_own,
			'penalty' => $is_penalty,
			'ownscorer' => $ownscorer,
			),
			array(
			'%d',
			'%d',
			'%d',
			'%d',
			'%d',
			'%s',
			)
		);

		?>
		<strong><?php echo esc_html__( 'Goals:', 'tss' ); ?><br/></strong>
		<?php
		tss_list_goals( $matchid, 'tss_goals' );
	}

	die(); // Stop executing script.
}
add_action( 'wp_ajax_add_goal', 'tss_add_goal' ); // Ajax for logged in users.
add_action( 'wp_ajax_nopriv_add_goal', 'tss_add_goal' ); // Ajax for not logged in users.

/**
 *
 * Ajax: Add card
 *
 * @since 1.0
 */
function tss_add_card() {

	$nonce = $_POST['nonce'];
	$playerid = $_POST['playerid'];
	$matchid = $_POST['matchid'];
	$minute = $_POST['minute'];
	$color = $_POST['color'];
	$table = '';

	if( false === tss_sanitaze_ajax_input( [ $playerid, $matchid, $minute ] ) ) {
		die( 'Data validation error!!' );
	}

	if ( 'yellow' == $color ) {
		$table = 'tss_yellowcards';
	} else {
		$table = 'tss_redcards';
	}

	if ( ! wp_verify_nonce( $nonce, 'tss-player-seasons-nonce' ) ) {
			die( 'Try harder ;)' );
	}

	if ( current_user_can( 'edit_posts' ) ) {
		global $wpdb;

		$wpdb->insert(
			$table,
			array(
			'playerid' => $playerid,
			'matchid' => $matchid,
			'minute' => $minute,
			),
			array(
			'%d',
			'%d',
			'%d',
			)
		);

		?>
		<strong>
			<?php
			if ( 'yellow' == $color ) {
				echo esc_html__( 'Yellow cards:', 'tss' );
			} else {
				echo esc_html__( 'Red cards:', 'tss' );
			}

			?>
		<br/></strong>
		<?php
		tss_list_cards( $matchid, $table );
	}

	die(); // Stop executing script.
}
add_action( 'wp_ajax_add_card', 'tss_add_card' ); // Ajax for logged in users.
add_action( 'wp_ajax_nopriv_add_card', 'tss_add_card' ); // Ajax for not logged in users.

/**
 *
 * Ajax: delete player from season.
 *
 * @since 1.0
 */
function tss_delete_player_from_season() {

	$nonce = $_POST['nonce'];
	$playerid = $_POST['playerid'];
	$seasonid = $_POST['seasonid'];

	if( false === tss_sanitaze_ajax_input( [ $playerid, $seasonid ] ) ) {
		die( 'Data validation error!!' );
	}

	if ( ! wp_verify_nonce( $nonce, 'tss-player-seasons-nonce' ) ) {
			die( 'Try harder ;)' );
	}

	if ( current_user_can( 'edit_posts' ) ) {
		global $wpdb;

		$wpdb->delete( 'tss_player_seasons', array( 'seasonid' => $seasonid, 'playerid' => $playerid ) );

		?>
		<strong><?php echo esc_html__( 'Player added to following seasons:', 'tss' ); ?><br/></strong>
		<?php
		tss_list_player_seasons( $playerid );
	}

	die(); // Stop executing script.
}
add_action( 'wp_ajax_delete_player_from_season', 'tss_delete_player_from_season' ); // Ajax for logged in users.
add_action( 'wp_ajax_nopriv_delete_player_from_season', 'tss_delete_player_from_season' ); // Ajax for not logged in users.

/**
 *
 * Ajax: delete player from match details
 * receives table via post
 *
 * @since 1.0
 */
function tss_delete_player_from_team_stats() {

	$nonce = $_POST['nonce'];
	$id = $_POST['id'];
	$table = sanitize_text_field( $_POST['table'] );
	$matchid = $_POST['matchid'];

	if( false === tss_sanitaze_ajax_input( [ $id, $matchid ] ) ) {
		die( 'Data validation error!!' );
	}

	global $post;

	if ( ! wp_verify_nonce( $nonce, 'tss-player-seasons-nonce' ) ) {
			die( 'Try harder ;)' );
	}

	if ( current_user_can( 'edit_posts' ) ) {
		global $wpdb;

		$wpdb->delete( $table, array( 'id' => $id ) );

		if ( 'tss_starters' == $table ) {
			echo esc_html__( 'Current opening lineup:', 'tss' ) . '<br/>';
			tss_list_players_in_match( $matchid, $table );
		} elseif ( 'tss_substitutes' == $table ) {
			echo esc_html__( 'Substitutes:', 'tss' ) . '<br/>';
			tss_list_players_in_match( $matchid, $table );
		} elseif ( 'tss_substitutions' == $table ) {
			echo esc_html__( 'Substitutions:', 'tss' ) . '<br/>';
			get_substitutions( $matchid, $table );
		} elseif ( 'tss_goals' == $table ) {
			echo esc_html__( 'Goals:', 'tss' ) . '<br/>';
			tss_list_goals( $matchid, $table );
		} elseif ( 'tss_yellowcards' == $table ) {
			echo esc_html__( 'Yellow cards:', 'tss' ) . '<br/>';
			tss_list_cards( $matchid, $table );
		} elseif ( 'tss_redcards' == $table ) {
			echo esc_html__( 'Red cards:', 'tss' ) . '<br/>';
			tss_list_cards( $matchid, $table );
		}
	}

	die(); // Stop executing script.
}
add_action( 'wp_ajax_delete_from_team_stats', 'tss_delete_player_from_team_stats' ); // Ajax for logged in users.
add_action( 'wp_ajax_nopriv_delete_from_team_stats', 'tss_delete_player_from_team_stats' ); // Ajax for not logged in users.

/**
 *
 * Ajax: Add starters to match
 *
 * @since 1.0
 */
function tss_add_team_stats() {

	$nonce = $_POST['nonce'];
	$starters = $_POST['players'];
	$matchid = $_POST['matchid'];
	$table = 'tss_' . $_POST['table'];
	$table = sanitize_text_field( $table );

	if( false === tss_sanitaze_ajax_input( [ $matchid ] ) ) {
		die( 'Data validation error!!' );
	}

	if ( ! wp_verify_nonce( $nonce, 'tss-player-seasons-nonce' ) ) {
			die( 'Try harder ;)' );
	}

	if ( current_user_can( 'edit_posts' ) ) {
		global $wpdb;

		foreach ( $starters as $starter ) {

			if( false === tss_sanitaze_ajax_input( [ $starter ] ) ) {
				die( 'Data validation error!!' );
			}

			$q = "SELECT * FROM $table WHERE playerid = '$starter' AND matchid = '$matchid'";
			$results = $wpdb->get_results( $q, OBJECT );

			if ( ! $results ) {
				$wpdb->insert(
					$table,
					array(
					'playerid' => $starter,
					'matchid' => $matchid,
					),
					array(
					'%d',
					'%d',
					)
				);
			}
		}

		if ( 'tss_starters' == $table ) {
			echo esc_html__( 'Current opening lineup:', 'tss' ) . '<br/>';
		} elseif ( 'tss_substitutes' == $table ) {
			echo esc_html__( 'Substitutes:', 'tss' ) . '<br/>';
		}

		tss_list_players_in_match( $matchid, $table );

	}

	die(); // Stop executing script.
}
add_action( 'wp_ajax_add_team_stats', 'tss_add_team_stats' ); // Ajax for logged in users.
add_action( 'wp_ajax_nopriv_add_team_stats', 'tss_add_team_stats' ); // Ajax for not logged in users.

/**
 *
 * Ajax: Rebuild match titles
 *
 * @since 1.02
 */
function tss_rebuild_match_titles() {
	$posts = get_posts(
		array(
			'post_type' => 'tss-matches',
			'posts_per_page' => -1
		)
	);

	foreach ($posts as $post) {
		$meta = get_post_meta( $post->ID );

		// Update posts only if opponent and date are set.
		if( '' != $meta[ 'tss-match-opponent' ][0] && '' != $meta[ 'tss-match-date' ] [ 0 ] ) {
			$opponent = get_the_title( $meta[ 'tss-match-opponent' ][0] );
			$date = date_i18n( get_option( 'date_format' ), strtotime( $meta[ 'tss-match-date' ] [ 0 ] ) );
			$new_title = $date . ' vs. ' . $opponent;
			$new_slug = sanitize_title( $new_title );
			wp_update_post(
				array (
				    'ID'        => $post->ID,
				    'post_name' => $new_slug,
						'post_title' => $new_title
				)
	    );
		}
	}

	die(); // Stop executing script.
}
add_action( 'wp_ajax_rebuild_match_titles', 'tss_rebuild_match_titles' ); // Ajax for logged in users.
add_action( 'wp_ajax_nopriv_rebuild_match_titles', 'tss_rebuild_match_titles' ); // Ajax for not logged in users.

/**
 *
 * Ajax: Update seasonal stats based by match type id
 *
 * @since 1.05
 */
function tss_update_seasonal_stats() {
	$seasonid = $_POST[ 'season' ];
	$matchtypeid = $_POST[ 'matchtype' ];

	$matchtypename = get_the_title( $matchtypeid );

	$playerstatstable = new tssPlayerStatsTable( $seasonid, $matchtypeid );
	$playerstatstable->Display();

	$seasonlist = new tssSeasonMatchList( $seasonid, $matchtypename );
	$seasonlist->Display();

	die(); // Stop executing script.


}
add_action( 'wp_ajax_update_seasonal_stats', 'tss_update_seasonal_stats' ); // Ajax for logged in users.
add_action( 'wp_ajax_nopriv_update_seasonal_stats', 'tss_update_seasonal_stats' ); // Ajax for not logged in users.

/**
 *
 * Ajax: Update player stats in player template
 *
 * @since 1.05
 */
function tss_update_seasonal_stats_player() {
	$playerid = $_POST[ 'player' ];
	$matchtypeid = $_POST[ 'matchtype' ];

	$playerstatstable = new tssPlayerSeasonalStatsTable( $playerid, $matchtypeid );
	$playerstatstable->Display();

	die(); // Stop executing script.


}
add_action( 'wp_ajax_update_seasonal_stats_player', 'tss_update_seasonal_stats_player' ); // Ajax for logged in users.
add_action( 'wp_ajax_nopriv_update_seasonal_stats_player', 'tss_update_seasonal_stats_player' ); // Ajax for not logged in users.


/**
 *
 * Ajax: Update seasonal stats based by match type id
 *
 * @since 1.05
 */
function tss_update_opponent_stats() {
	$opponentid = $_POST[ 'opponent' ];
	$matchtypeid = $_POST[ 'matchtype' ];

	$matchtypename = get_the_title( $matchtypeid );

	$opponentpage = new tssOpponentStats($opponentid, $matchtypename);
	$opponentpage->Display();
	die(); // Stop executing script.


}
add_action( 'wp_ajax_update_opponent_stats', 'tss_update_opponent_stats' ); // Ajax for logged in users.
add_action( 'wp_ajax_nopriv_update_opponent_stats', 'tss_update_opponent_stats' ); // Ajax for not logged in users.
?>
