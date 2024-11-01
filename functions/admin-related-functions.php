<?php
/**
 * WP-Admin related functions
 *
 * All the wp-admin related functions in this file.
 *
 * @package The Soccer Stats
 * @since 1.0
 */

/**
 *
 * Lists seasons where player is added.
 *
 * @since 1.0
 * @param int $playerid player id (player post id).
 */
function tss_list_player_seasons( $playerid ) {
	global $wpdb;

	$q = 'SELECT
         p.post_title AS seasonname,
         ps.seasonid AS seasonid
         FROM ' . $wpdb->prefix . 'posts p, tss_player_seasons ps
         WHERE p.ID = ps.seasonid AND
         ps.playerid =  \'' . $playerid . '\' ORDER BY seasonname DESC';

	$results = $wpdb->get_results( $q, OBJECT );

	if ( $results ) {
		foreach ( $results as $row ) {
			?>
 		  <div class="tss-admin-list-item"><?php echo esc_html( $row->seasonname ) ?> <span class="btn tss-delete-player-season" playerid="<?php echo esc_html( $playerid ) ?>" seasonid="<?php echo esc_html( $row->seasonid ) ?>"><?php echo esc_html( __( 'Delete', 'tss' ) ) ?> <img src="<?php echo esc_html( admin_url( 'images/wpspin_light.gif' ) ); ?>"></span></div>
 		<?php
		}
	} else {
		echo esc_html( __( 'Nothing found from database', 'tss' ) );
	}

}

/**
 *
 * Lists player from a certain season to select -> option
 *
 * @since 1.0
 * @param object $post post object.
 */
function tss_list_players_in_season( $post ) {
	global $wpdb;
	$meta = get_post_meta( $post->ID );
	$seasonid = $meta['tss-match-season'][0];

	$q = 'SELECT
         p.post_title AS playername,
         p.id AS playerid
         FROM ' . $wpdb->prefix . 'posts p, tss_player_seasons ps
         WHERE p.ID = ps.playerid AND p.post_type = \'tss-players\' AND ps.seasonid = \'' . $seasonid . '\' ORDER BY playername';

	$results = $wpdb->get_results( $q, OBJECT );

	if ( $results ) {
		foreach ( $results as $row ) {
			?>
 		  <option value="<?php echo esc_html( $row->playerid ) ?>"><?php echo esc_html( $row->playername ) ?></option>
 		<?php
		}
	} else {
		echo '<option>' . esc_html( __( 'Nothing found from database', 'tss' ) ) . '</option>';
	}
}

/**
 *
 * Echoes select box and selects parameter value
 *
 * @since 1.0
 * @param string $class_name Name of the select field.
 * @param string $post_type Post type.
 * @param int    $selected_id Post ID of the selected post.
 */
function tss_e_select_with_parameter( $class_name, $post_type, $selected_id ) {
	?>
   <select name="<?php echo esc_html( $class_name ) ?>" id="<?php echo esc_html( $class_name ) ?>">
 	<?php
	$posts = get_posts( array( 'posts_per_page' => -1, 'post_type' => $post_type, 'orderby' => 'post_title', 'order' => 'ASC' ) );

	foreach ( $posts as $post ) {
		if ( $post->ID == $selected_id ) {
			$selected = ' SELECTED';
		} else {
			$selected = '';
		}
		?>
     <option value="<?php echo esc_html( $post->ID ) ?>"<?php echo esc_html( $selected ) ?>><?php echo esc_html( $post->post_title ) ?></option>
		<?php
	}
	?>
   </select>
 	<?php
}

/**
 *
 * Lists players in a match
 *
 * @since 1.0
 * @param int    $matchid Match ID.
 * @param string $table TSS-database table what is queried.
 */
function tss_list_players_in_match( $matchid, $table ) {
	global $wpdb;

	$posts_table = $wpdb->prefix . 'posts';
	$posts_metatable = $wpdb->prefix . 'postmeta';

	$q = "SELECT
    p.post_title AS playername,
    p.id AS playerid,
    pl.id AS id,
    m.meta_value AS position
    FROM $posts_table p, $table pl, $posts_metatable m
    WHERE p.ID = pl.playerid AND pl.matchid = '$matchid' AND m.post_id = p.id AND m.meta_key = 'tss-position'
    ORDER BY position";

	$results = $wpdb->get_results( $q, OBJECT );

	if ( $results ) {
		foreach ( $results as $row ) {
			?>
 		  <div class="tss-admin-list-item"><?php echo esc_html( $row->playername ) ?> <span class="btn tss-delete-team-stats" table="<?php echo esc_html( $table ) ?>" id="<?php echo esc_html( $row->id ) ?>" action="delete_from_team_stats" matchid="<?php echo esc_html( $matchid ) ?>"><?php echo esc_html__( 'Delete', 'tss' ); ?> <img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>"></span></div>
 		<?php
		}
	} else {
		echo  esc_html__( 'Nothing found from database', 'tss' );
	}
}

/**
 *
 * Lists substitutions
 *
 * @since 1.0
 * @param int    $postid Get substitutions from this post (match).
 * @param string $table TSS-database table name.
 */
function get_substitutions( $postid, $table ) {
	global $wpdb;

	$posts_table = $wpdb->prefix . 'posts';

	$q = "
     SELECT
     p.post_title AS playerin,
     p2.post_title AS playerout,
     s.id AS id,
     s.minute AS minute
     FROM $posts_table p, $posts_table p2, tss_substitutions s
     WHERE p.id = s.playeridin AND p2.id = s.playeridout AND s.matchid = $postid
     ORDER BY minute
     ";

	$results = $wpdb->get_results( $q, OBJECT );

	if ( $results ) {
		foreach ( $results as $row ) {
			?>
 		  <div class="tss-admin-list-item">
 			<?php echo esc_html__( 'In: ', 'tss' ) ?><?php echo esc_html( $row->playerin ) ?>,
 			<?php echo esc_html__( 'Out: ', 'tss' ) ?><?php echo esc_html( $row->playerout ) ?>,
 			<?php echo esc_html__( 'Minute: ', 'tss' ) ?><?php echo esc_html( $row->minute ) ?>
            <span class="btn tss-delete-team-stats" table="<?php echo esc_html( $table ) ?>" id="<?php echo esc_html( $row->id ) ?>" action="delete_from_team_stats" matchid="<?php echo esc_html( $postid ) ?>">
 				<?php echo esc_html__( 'Delete', 'tss' ) ?> <img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>">
            </span>
          </div>
			<?php
		}
	} else {
		echo  esc_html__( 'Nothing found from database', 'tss' );
	}

}

/**
 * Lists goals
 *
 * @since 1.0
 * @param int    $postid Match ID (post id of the match).
 * @param string $table TSS-database table name.
 */
function tss_list_goals( $postid, $table ) {
	global $wpdb;

	$posts_table = $wpdb->prefix . 'posts';

	$q = "
      SELECT
      p.post_title AS playername,
      g.id AS id,
      g.minute AS minute,
      g.penalty AS is_penalty,
      g.own AS is_own,
      g.ownscorer AS ownscorer
      FROM $posts_table p, tss_goals g
      WHERE p.id = g.playerid AND g.matchid = $postid
      ORDER BY minute
      ";

	$results = $wpdb->get_results( $q, OBJECT );

	if ( $results ) {
		foreach ( $results as $row ) {
			$scorer_row = $row->playername . ' (' . $row->minute . ')';

			if ( 1 == $row->is_penalty ) {
				$scorer_row = $scorer_row . ' (' . __( 'pen.', 'tss' ) . ')';
			}

			if ( 1 == $row->is_own ) {
				$scorer_row = $row->ownscorer . ' (' . $row->minute . ')' . ' (' . __( 'O.G.', 'tss' ) . ')';
			}

			?>
 		  <div class="tss-admin-list-item">
 			<?php echo esc_html( $scorer_row ) ?>
             <span class="btn tss-delete-team-stats" table="<?php echo esc_html( $table ) ?>" id="<?php echo esc_html( $row->id ) ?>" action="delete_from_team_stats" matchid="<?php echo esc_html( $postid ) ?>">
 				<?php echo esc_html__( 'Delete', 'tss' ) ?> <img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>">
             </span>
 		   </div>
 			<?php
		}
	} else {
		echo  esc_html__( 'Nothing found from database', 'tss' );
	}

}

/**
 * Lists cards
 *
 * @since 1.0
 * @param int    $postid Match ID (post id).
 * @param string $table TSS-database table tss-yellowcards or tss-redcards.
 */
function tss_list_cards( $postid, $table ) {
	global $wpdb;

	$posts_table = $wpdb->prefix . 'posts';

	$q = "
       SELECT
       p.post_title AS playername,
       c.id AS id,
       c.minute AS minute
       FROM $posts_table p, $table c
       WHERE p.id = c.playerid AND c.matchid = $postid
       ORDER BY minute
       ";

	$results = $wpdb->get_results( $q, OBJECT );

	if ( $results ) {
		foreach ( $results as $row ) {
			?>
           <div class="tss-admin-list-item">
			<?php echo esc_html( $row->playername ) . ' (' . esc_html( $row->minute ) . ')'; ?>
              <span class="btn tss-delete-team-stats" table="<?php echo esc_html( $table ) ?>" id="<?php echo esc_html( $row->id ) ?>" action="delete_from_team_stats" matchid="<?php echo esc_html( $postid ) ?>">
 				<?php echo esc_html__( 'Delete', 'tss' ) ?> <img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>">
              </span>
            </div>
 			<?php
		}
	} else {
		echo  esc_html__( 'Nothing found from database', 'tss' );
	}

}

?>
