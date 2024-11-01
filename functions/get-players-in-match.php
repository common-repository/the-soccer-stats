<?php
/**
 * Retrieves players in a single match
 *
 * Get starters, subs, goals etc from single match to an array
 *
 * @package The Soccer Stats
 * @since 1.0
 */

/**
 *
 * Gets Players from a single match
 *
 * @since 1.0
 * @param int $matchid Match ID that's requested.
 */
function tss_get_players_in_match( $matchid ) {
	global $wpdb;

	$data = array(); // Data thats returned
	$starters = array();
	$substitutes = array();
	$substitutions = array();
	$goals = array();
	$yellows = array();
	$reds = array();

	$posts_table = $wpdb->prefix . 'posts';
	$posts_metatable = $wpdb->prefix . 'postmeta';

	$q = "SELECT
   p.post_title AS playername,
   p.id AS playerid,
   pl.id AS id,
   m.meta_value AS position
   FROM $posts_table p, tss_starters pl, $posts_metatable m
   WHERE p.ID = pl.playerid AND pl.matchid = '$matchid' AND m.post_id = p.id AND m.meta_key = 'tss-position'
   ORDER BY position";

	$results = $wpdb->get_results( $q, OBJECT );

	if ( $results ) {
		foreach ( $results as $row ) {
			array_push( $starters, array( 'id' => $row->playerid, 'name' => $row->playername ) );
		}
	}

	$data['starters'] = $starters;

	$q = "SELECT
   p.post_title AS playername,
   p.id AS playerid,
   pl.id AS id,
   m.meta_value AS position
   FROM $posts_table p, tss_substitutes pl, $posts_metatable m
   WHERE p.ID = pl.playerid AND pl.matchid = '$matchid' AND m.post_id = p.id AND m.meta_key = 'tss-position'
   ORDER BY position";

	$results = $wpdb->get_results( $q, OBJECT );

	if ( $results ) {
		foreach ( $results as $row ) {
			array_push( $substitutes, array( 'id' => $row->playerid, 'name' => $row->playername ) );
		}
	}

	$data['substitutes'] = $substitutes;

	$q = "SELECT
    p.post_title AS playerin,
		p.id AS playeridin,
    p2.post_title AS playerout,
		p2.id AS playeridout,
    s.id AS id,
    s.minute AS minute
    FROM $posts_table p, $posts_table p2, tss_substitutions s
    WHERE p.id = s.playeridin AND p2.id = s.playeridout AND s.matchid = $matchid
    ORDER BY minute
    ";

	$results = $wpdb->get_results( $q, OBJECT );

	if ( $results ) {
		foreach ( $results as $row ) {
			array_push( $substitutions, array(
				'id_in' => $row->playeridin,
				'name_in' => $row->playerin,
				'id_out' => $row->playeridout,
				'name_out' => $row->playerout,
				'minute' => $row->minute,
			) );
		}
	}

	$data['substitutions'] = $substitutions;

	$q = "SELECT
     p.post_title AS playername,
		 p.id AS playerid,
     g.id AS id,
     g.minute AS minute,
     g.penalty AS is_penalty,
     g.own AS is_own,
     g.ownscorer AS ownscorer
     FROM $posts_table p, tss_goals g
     WHERE p.id = g.playerid AND g.matchid = $matchid
     ORDER BY minute
     ";

	$results = $wpdb->get_results( $q, OBJECT );

	if ( $results ) {
		foreach ( $results as $row ) {
			array_push( $goals, array(
				'id' => $row->playerid,
				'name' => $row->playername,
				'minute' => $row->minute,
				'is_own' => $row->is_own,
				'ownscorer' => $row->ownscorer,
				'is_penalty' => $row->is_penalty,
			) );
		}
	}

	$data['goals'] = $goals;

	$q = "SELECT
      p.post_title AS playername,
			p.id AS playerid,
      c.minute AS minute
      FROM $posts_table p, tss_yellowcards c
      WHERE p.id = c.playerid AND c.matchid = $matchid
      ORDER BY minute
      ";

	$results = $wpdb->get_results( $q, OBJECT );

	if ( $results ) {
		foreach ( $results as $row ) {
			array_push( $yellows, array(
				'id' => $row->playerid,
				'name' => $row->playername,
				'minute' => $row->minute,
			) );
		}
	}

	$data['yellows'] = $yellows;

	$q = "SELECT
      p.post_title AS playername,
			p.id AS playerid,
      c.minute AS minute
      FROM $posts_table p, tss_redcards c
      WHERE p.id = c.playerid AND c.matchid = $matchid
      ORDER BY minute
      ";

	$results = $wpdb->get_results( $q, OBJECT );

	if ( $results ) {
		foreach ( $results as $row ) {
			array_push( $reds, array(
				'id' => $row->playerid,
				'name' => $row->playername,
				'minute' => $row->minute,
			) );
		}
	}

	$data['reds'] = $reds;

	return $data;
}

?>
