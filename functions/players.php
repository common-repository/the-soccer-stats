<?php
/**
 * Player functions
 *
 * All the functions related to player handling.
 *
 * @package The Soccer Stats
 * @since 1.0
 */

require_once( 'get-matches-by-player.php' );

/**
 * Retrieves player details to an array
 *
 * @since 1.0
 * @return array
 */
function tss_get_player_details() {
	global $post;

	$meta = get_post_meta( $post->ID );

	$content = get_post_field( 'post_content', $post->ID );
	$content = apply_filters( 'the_content', $content );

	if ( has_post_thumbnail( $post->ID ) ) {
		$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'single-post-thumbnail' );
		$image_url = $image[0];
	} else {
		$image_url = '';
	}

	$metafields = array( 'tss-shirtnumber', 'tss-position', 'tss-dob', 'tss-pob', 'tss-dob', 'tss-height', 'tss-weight', 'tss-previous-clubs' );

	foreach ( $metafields as $metafield ) {
		if ( isset( $meta[ $metafield ] ) ) {
			$metafields[ $metafield ] = $meta[ $metafield ][0];
		} else {
			$metafields[ $metafield ] = '';
		}
	}

	$data = array();

	if ( '' != $metafields['tss-dob'] ) {
		$date = date_i18n( get_option( 'date_format' ), strtotime( $metafields['tss-dob'] ) );
	} else {
		$date = '';
	}

	array_push( $data,
		array(
		'number' => $metafields['tss-shirtnumber'],
		'dob' => $date,
		'pob' => $metafields['tss-pob'],
		'weight' => $metafields['tss-weight'],
		'height' => $metafields['tss-height'],
		'previous-clubs' => $metafields['tss-previous-clubs'],
		'position' => tss_get_player_position( $metafields['tss-position'] ),
		'description' => $content,
		'imageurl' => $image_url,
		)
	);

	return $data;
}

/**
 * Retrieves player position based on ID
 *
 * @since 1.0
 * @param int $i Position value (1/2/3/4).
 */
function tss_get_player_position( $i ) {

	switch ( $i ) {
		case 1:
			$string = __( 'Goalkeeper', 'tss' );
			break;
		case 2:
			$string = __( 'Defender', 'tss' );
			break;
		case 3:
			$string = __( 'Midfield', 'tss' );
			break;
		case 4:
			$string = __( 'Striker', 'tss' );
			break;
		default:
			$string = '';
			break;
	}

	return $string;
}

/**
 * Echoes player stats to table rows
 *
 * @since 1.0
 * @param int $playerid Player post id.
 */
function tss_e_player_stats_rows( $playerid ) {
	global $wpdb;

	$posts_table = $wpdb->prefix . 'posts';

	$q = "SELECT p.post_title AS post_title,
	p.ID AS ID
	FROM $posts_table p, tss_player_seasons ps
	WHERE p.post_type = 'tss-seasons' AND
	ps.seasonid = p.ID AND ps.playerid = $playerid
	ORDER BY post_title DESC";

	$posts = $wpdb->get_results( $q );

	$totals = array();
	$totals['starts'] = 0;
	$totals['subs'] = 0;
	$totals['goals'] = 0;
	$totals['yellows'] = 0;
	$totals['reds'] = 0;

	foreach ( $posts as $season ) {
		?>

 		<?php
		$seasondata = tss_get_player_stats_by_season( $playerid, $season->ID );
		$totals['starts'] += $seasondata->starts;
		$totals['subs'] += $seasondata->subs;
		$totals['goals'] += $seasondata->goals;
		$totals['yellows'] += $seasondata->yellows;
		$totals['reds'] += $seasondata->reds;
		?>

 		<tr>
		<td><a href="<?php echo esc_url( get_permalink( $season->ID ) ) ?>"><?php echo esc_html( $season->post_title ) ?></a></td>
       <td class="text-center"><?php echo esc_html( $seasondata->starts ) ?></td>
       <td class="text-center"><?php echo esc_html( $seasondata->subs ) ?></td>
       <td class="text-center"><?php echo esc_html( $seasondata->goals ) ?></td>
       <td class="text-center"><?php echo esc_html( $seasondata->yellows ) ?></td>
       <td class="text-center"><?php echo esc_html( $seasondata->reds ) ?></td>
     </tr>

		<?php
	}
	?>
	<tfoot>
		<tr>
			<td><strong><?php echo esc_html__( 'Total', 'tss' ) ?></strong></td>
			<td class="text-center"><?php echo esc_html( $totals['starts'] ) ?></td>
			<td class="text-center"><?php echo esc_html( $totals['subs'] ) ?></td>
			<td class="text-center"><?php echo esc_html( $totals['goals'] ) ?></td>
			<td class="text-center"><?php echo esc_html( $totals['yellows'] ) ?></td>
			<td class="text-center"><?php echo esc_html( $totals['reds'] ) ?></td>
		</tr>
	</tfoot>
	<?php
}

/**
 * Get player stats by season
 *
 * @since 1.0
 * @param int $playerid Player post ID.
 * @param int $seasonid Season ID.
 */
function tss_get_player_stats_by_season( $playerid, $seasonid, $matchtypeid ) {

	global $wpdb;

	$posts_table = $wpdb->prefix . 'posts';

	$postsmeta_table = $wpdb->prefix . 'postmeta';

	if( 0 !== $matchtypeid ) { // set matchtype query if matchtype is set
		$matchtype_query = "AND (p.id = pmet.post_id AND pmet.meta_key = 'tss-match-matchtype' AND pmet.meta_value = '$matchtypeid') ";
		$from_wptables = "$posts_table p, $postsmeta_table pm, $postsmeta_table pme, $postsmeta_table pmet";
	} else {
		$matchtype_query = '';
		$from_wptables = "$posts_table p, $postsmeta_table pm, $postsmeta_table pme";
	}

	$q = "SELECT
     (SELECT COUNT(st.playerid)
     from tss_starters st, $from_wptables
     WHERE st.playerid = $playerid AND
     st.matchid = p.id AND
     (p.id = pm.post_id AND pm.meta_key = 'tss-match-season' AND pm.meta_value = '$seasonid') AND
	  (p.id = pme.post_id AND pme.meta_key = 'tss-match-calculate-stats' AND pme.meta_value = '1') $matchtype_query )  AS starts,

     (SELECT COUNT(st.playeridin)
     from tss_substitutions st, $from_wptables
     WHERE st.playeridin = $playerid AND
     st.matchid = p.id AND
     (p.id = pm.post_id AND pm.meta_key = 'tss-match-season' AND pm.meta_value = '$seasonid') AND
	  (p.id = pme.post_id AND pme.meta_key = 'tss-match-calculate-stats' AND pme.meta_value = '1') $matchtype_query ) AS subs,

     (SELECT COUNT(g.playerid)
     from tss_goals g, $from_wptables
     WHERE g.playerid = $playerid AND
     g.matchid = p.id AND
		 g.own = '0' AND
     (p.id = pm.post_id AND pm.meta_key = 'tss-match-season' AND pm.meta_value = '$seasonid') AND
	  (p.id = pme.post_id AND pme.meta_key = 'tss-match-calculate-stats' AND pme.meta_value = '1') $matchtype_query ) AS goals,

     (SELECT COUNT(y.playerid)
     from tss_yellowcards y, $from_wptables
     WHERE y.playerid = $playerid AND
     y.matchid = p.id AND
     (p.id = pm.post_id AND pm.meta_key = 'tss-match-season' AND pm.meta_value = '$seasonid') AND
	  (p.id = pme.post_id AND pme.meta_key = 'tss-match-calculate-stats' AND pme.meta_value = '1') $matchtype_query ) AS yellows,

     (SELECT COUNT(r.playerid)
     from tss_redcards r, $from_wptables
     WHERE r.playerid = $playerid AND
     r.matchid = p.id AND
     (p.id = pm.post_id AND pm.meta_key = 'tss-match-season' AND pm.meta_value = '$seasonid') AND
	  (p.id = pme.post_id AND pme.meta_key = 'tss-match-calculate-stats' AND pme.meta_value = '1') $matchtype_query ) AS reds";

	$results = $wpdb->get_row( $q );

	return $results;

}

/**
 * Echoes all player stats to table rows
 *
 * @since 1.0
 * @param int $playerid Player post id.
 */
function tss_e_all_player_stats_rows( $seasonid ) {
	global $wpdb;

	$posts_table = $wpdb->prefix . 'posts';
	$postsmeta_table = $wpdb->prefix . 'postmeta';

	$q = "SELECT p.post_title AS playername,
	p.ID AS playerid,
	CAST(pm.meta_value AS UNSIGNED) AS playernumber
	FROM $posts_table p, $postsmeta_table pm, tss_player_seasons ps
	WHERE p.id = ps.playerid AND ps.seasonid = $seasonid AND
	(pm.post_id = p.id AND pm.meta_key = 'tss-shirtnumber')
	ORDER BY playernumber ASC";

	$posts = $wpdb->get_results( $q );

	foreach ( $posts as $player ) {
		?>

 		<?php
		$seasondata = tss_get_player_stats_by_season( $player->playerid, $seasonid );
		?>

 		<tr>
			<td><a href="<?php echo esc_url( get_permalink( $player->playerid ) ) ?>"><?php echo esc_html( '#' . $player->playernumber . ' ' . $player->playername ) ?></a></td>
			<td class="text-center"><?php echo esc_html( $seasondata->starts ) ?></td>
			<td class="text-center"><?php echo esc_html( $seasondata->subs ) ?></td>
			<td class="text-center"><?php echo esc_html( $seasondata->goals ) ?></td>
			<td class="text-center"><?php echo esc_html( $seasondata->yellows ) ?></td>
			<td class="text-center"><?php echo esc_html( $seasondata->reds ) ?></td>
     </tr>

		<?php
	}
}



?>
