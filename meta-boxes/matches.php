<?php
/**
 * Matches metaboxes
 *
 * Metabox functions related to matches.
 *
 * @package The Soccer Stats
 * @since 1.0
 */

/**
 *
 * Adds a meta boxes to match edit screen
 *
 * @since 1.0
 */
function add_matches_metaboxes() {
	global $post;

	if ( ! isset( $post ) ) {
		return;
	}

	add_meta_box( 'match-details', __( 'Match details', 'tss' ), 'tss_match_details_callback', 'tss-matches', 'normal' );
	add_meta_box( 'opponent-details', __( 'Opponent details', 'tss' ), 'tss_opponent_details_callback', 'tss-matches', 'normal' );

	if ( 'publish' === $post->post_status ) {
		add_meta_box( 'team-stats', __( 'Your team stats', 'tss' ), 'tss_team_stats_callback', 'tss-matches', 'normal' );
	}
}
add_action( 'add_meta_boxes', 'add_matches_metaboxes' );

/**
 *
 * Match edit screen metabox callback for match details
 *
 * @since 1.0
 *
 * @param object $post current post.
 */
function tss_match_details_callback( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'matchdetails_nonce' );
	$meta = get_post_meta( $post->ID );
	?>

  <table class="form-table">
    <tbody>
      <tr>
        <th scope="row"><label for="tss-match-date"><?php echo esc_html__( 'Date', 'tss' ); ?>:</label></th>
        <td><input name="tss-match-date" type="date" id="tss-match-date" value="<?php if ( isset( $meta['tss-match-date'] ) ) { echo esc_html( $meta['tss-match-date'][0] ); } ?>" class="regular-text"></td>
      </tr>

			<tr>
        <th scope="row"><label for="tss-match-datetime"><?php echo esc_html__( 'Time', 'tss' ); ?>:</label></th>
        <td><input name="tss-match-datetime" type="text" id="tss-match-datetimo" value="<?php if ( isset( $meta['tss-match-datetime'] ) ) { echo esc_html( $meta['tss-match-datetime'][0] ); } ?>" class="small-text"></td>
      </tr>

      <tr>
        <th scope="row"><label for="tss-match-location"><?php echo esc_html__( 'Location', 'tss' ) ?>:</label></th>
        <td>
          <select name="tss-match-location" id="tss-match-location">
			<?php
			$locations = array(
			 array( 'id' => '1', 'value' => __( 'Home', 'tss' ) ),
			 array( 'id' => '2', 'value' => __( 'Away', 'tss' ) ),
			 array( 'id' => '3', 'value' => __( 'Neutral', 'tss' ) ),
			);

			foreach ( $locations as $location ) {
				$selected = '';
				if ( isset( $meta['tss-match-location'] ) ) {
					if ( $meta['tss-match-location'][0] === $location['id'] ) {
						$selected = ' SELECTED';
					}
				}

				?>
			  <option value="<?php echo esc_html( $location['id'] ); ?>"<?php echo esc_html( $selected ); ?>><?php echo esc_html( $location['value'] ); ?></option>
				<?php
			}
			?>
         </select>
        </td>
      </tr>

      <tr>
        <th scope="row"><label for="tss-match-season"><?php echo esc_html__( 'Season', 'tss' ); ?>:</label></th>
        <td>
			<?php
			if ( ! isset( $meta['tss-match-season'] ) ) {
				$param = 0;
			} else {
				$param = $meta['tss-match-season'][0];
			}
			tss_e_select_with_parameter( 'tss-match-season', 'tss-seasons', $param );
			?>
        </td>
      </tr>

      <tr>
        <th scope="row"><label for="tss-match-matchtype"><?php echo esc_html__( 'Match type', 'tss' ); ?>:</label></th>
        <td>
			<?php
			if ( ! isset( $meta['tss-match-matchtype'] ) ) {
				$param = 0;
			} else {
				$param = $meta['tss-match-matchtype'][0];
			}
			tss_e_select_with_parameter( 'tss-match-matchtype', 'tss-matchtypes', $param );
			?>
        </td>
      </tr>

      <tr>
        <th scope="row"><label for="tss-match-additional-matchtype"><?php echo esc_html__( 'Additional match type', 'tss' ); ?>:</label></th>
          <td>
            <input name="tss-match-additional-matchtype" type="text" id="tss-match-additional-matchtype" value="<?php if ( isset( $meta['tss-match-additional-matchtype'] ) ) { echo esc_html( $meta['tss-match-additional-matchtype'][0] ); } ?>" class="regular-text">
          </td>
      </tr>

      <tr>
        <th scope="row"><label for="tss-match-attendance"><?php echo esc_html__( 'Attendance', 'tss' ); ?>:</label></th>
          <td>
            <input name="tss-match-attendance" type="number" id="tss-match-attendance" value="<?php if ( isset( $meta['tss-match-attendance'] ) ) { echo esc_html( $meta['tss-match-attendance'][0] ); } ?>" class="regular-text">
          </td>
      </tr>

      <tr>
        <th scope="row"><label for="tss-match-opponent"><?php echo esc_html__( 'Opponent', 'tss' ); ?>:</label></th>
        <td>
			<?php
			if ( ! isset( $meta['tss-match-opponent'] ) ) {
				$param = 0;
			} else {
				$param = $meta['tss-match-opponent'][0];
			}
			tss_e_select_with_parameter( 'tss-match-opponent', 'tss-opponents', $param );
			?>
        </td>
      </tr>

      <tr>
        <th scope="row"><label for="tss-match-goals-for"><?php echo esc_html__( 'Goals for', 'tss' ); ?>:</label></th>
        <td><input name="tss-match-goals-for" type="number" id="tss-match-goals-for" value="<?php if ( isset( $meta['tss-match-goals-for'] ) ) { echo esc_html( $meta['tss-match-goals-for'][0] ); } ?>" class="small-text"></td>
      </tr>

      <tr>
        <th scope="row"><label for="tss-match-goals-against"><?php echo esc_html__( 'Goals against', 'tss' ); ?>:</label></th>
        <td><input name="tss-match-goals-against" type="number" id="tss-match-goals-against" value="<?php if ( isset( $meta['tss-match-goals-against'] ) ) { echo esc_html( $meta['tss-match-goals-against'][0] ); } ?>" class="small-text"></td>
      </tr>

      <tr>
        <th scope="row"><label for="tss-match-overtime"><?php echo esc_html__( 'Overtime', 'tss' ); ?>?</label></th>
        <td><input name="tss-match-overtime" type="checkbox" id="tss-match-overtime" <?php echo tss_is_checked( 'tss-match-overtime', $meta ) ?>></td>
      </tr>

      <tr>
        <th scope="row"><label for="tss-match-penalties"><?php echo esc_html__( 'Penalties', 'tss' ); ?>?</label></th>
        <td><input name="tss-match-penalties" type="checkbox" id="tss-match-penalties" <?php echo tss_is_checked( 'tss-match-penalties', $meta ) ?>> <span class="btn show-penalties-content"><?php echo esc_html__( 'Show/hide pen. goals', 'tss' ) ?></span></td>
      </tr>


        <tr class="penalty-goals-content">
          <th scope="row"><label for="tss-match-goals-for-pen"><?php echo esc_html__( 'Goals for (pen.)', 'tss' ); ?>:</label></th>
          <td><input name="tss-match-goals-for-pen" type="number" id="tss-match-goals-for-pen" value="<?php if ( isset( $meta['tss-match-goals-for-pen'] ) ) { echo esc_html( $meta['tss-match-goals-for-pen'][0] ); } ?>" class="small-text"></td>
        </tr>

        <tr class="penalty-goals-content">
          <th scope="row"><label for="tss-match-goals-against-pen"><?php echo esc_html__( 'Goals against (pen.)', 'tss' ); ?>:</label></th>
          <td><input name="tss-match-goals-against-pen" type="number" id="tss-match-goals-against-pen" value="<?php if ( isset( $meta['tss-match-goals-against-pen'] ) ) { echo esc_html( $meta['tss-match-goals-against-pen'][0] ); } ?>" class="small-text"></td>
        </tr>

    </tbody>
  </table>

	<?php
}

/**
 * Match edit screen metabox callback for your team stats
 *
 * @since 1.0
 */
function tss_team_stats_callback() {
	global $post;
	$meta = get_post_meta( $post->ID );
	?>
   <table class="form-table">
 <tbody>
	<tr>
	<th scope="row"><label for="tss-match-calculate-stats"><?php echo esc_html__( 'Calculate stats from this match', 'tss' ); ?>?</label></th>
	<td><input name="tss-match-calculate-stats" type="checkbox" id="tss-match-calculate-stats" <?php echo tss_is_checked( 'tss-match-calculate-stats', $meta ) ?>></td>
	</tr>

   <tr>
	 <th scope="row"><label><?php echo esc_html__( 'Starters', 'tss' ); ?>:</label></th>
	 <td>
	   <select multiple id="tss-starters">
			<?php
			tss_list_players_in_season( $post );
			?>
		</select> <span class="btn tss-add-team-stats" table="starters" matchid="<?php echo esc_html( $post->ID ) ?>"><?php echo  esc_html__( 'Add', 'tss' ); ?> <img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>"></span>
           <div class="tss-hidden-content">
            <?php
			echo esc_html__( 'Current opening lineup:', 'tss' ) . '<br/>';
			tss_list_players_in_match( $post->ID, 'tss_starters' );
			?>
           </div>
         </td>
       </tr>

       <tr>
         <th scope="row"><label><?php echo esc_html__( 'Substitutes', 'tss' ); ?>:</label></th>
         <td>
           <select multiple id="tss-substitutes">
			<?php
			tss_list_players_in_season( $post );
			?>
		</select> <span class="btn tss-add-team-stats" table="substitutes" matchid="<?php echo esc_html( $post->ID ) ?>"><?php echo  esc_html__( 'Add', 'tss' ); ?> <img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>"></span>
           <div class="tss-hidden-content">
            <?php
			echo esc_html__( 'Substitutes:', 'tss' ) . '<br/>';
			tss_list_players_in_match( $post->ID, 'tss_substitutes' );
			?>
           </div>
         </td>
       </tr>

       <tr>
         <th scope="row"><label><?php echo esc_html__( 'Substitutions', 'tss' ); ?>:</label></th>
         <td>
           <?php echo esc_html__( 'In', 'tss' ) ?>: <select id="tss-substitutions-in">
			<?php
			tss_list_players_in_season( $post );
			?>
           </select><br/>

           <?php echo esc_html__( 'Out', 'tss' ) ?>: <select id="tss-substitutions-out">
			<?php
			tss_list_players_in_season( $post );
			?>
           </select><br/>
           <?php echo esc_html__( 'Minute', 'tss' ) ?>: <input min="1" max="120" name="tss-substitutions-minute" type="number" id="tss-substitutions-minute" class="small-text">
           <span class="btn tss-add-substitutions" matchid="<?php echo esc_html( $post->ID ) ?>"><?php echo  esc_html__( 'Add', 'tss' ); ?> <img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>"></span>
           <div class="tss-hidden-content">
            <?php
			echo esc_html__( 'Substitutions:', 'tss' );
			get_substitutions( $post->ID, 'tss_substitutions' );
			?>
           </div>
         </td>
       </tr>

       <tr>
         <th scope="row"><label><?php echo esc_html__( 'Goals', 'tss' ); ?>:</label></th>
         <td>
			<?php echo esc_html__( 'Goal scorer:', 'tss' ) ?> <select id="tss-goals">
			<?php
			tss_list_players_in_season( $post );
			?>
           </select><br/>
			<?php echo esc_html__( 'Minute', 'tss' ) ?>: <input min="1" max="120" name="tss-goals-minute" type="number" id="tss-goals-minute" class="small-text"><br/>
			<?php echo esc_html__( 'Penalty', 'tss' ) ?>? <input name="tss-goals-penalty" type="checkbox" id="tss-goals-penalty"><br/>
			<?php echo esc_html__( 'Own goal', 'tss' ) ?>? <input name="tss-goals-own" type="checkbox" id="tss-goals-own"><br/>
			<?php echo esc_html__( 'Own scorer', 'tss' ) ?>? <input name="tss-goals-own-scorer" type="text" id="tss-goals-own-scorer" class="regular-text">
           <span class="btn tss-add-goal" table="goals" matchid="<?php echo esc_html( $post->ID ) ?>"><?php echo esc_html__( 'Add', 'tss' ); ?> <img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>"></span>
           <div class="tss-hidden-content">
            <?php
			echo esc_html__( 'Goals:', 'tss' ) . '<br/>';
			tss_list_goals( $post->ID, 'tss_goals' );
			?>
           </div>
         </td>
       </tr>

       <tr>
         <th scope="row"><label><?php echo esc_html__( 'Yellow cards', 'tss' ); ?>:</label></th>
         <td>
           <select id="tss-yellowcards">
			<?php
			tss_list_players_in_season( $post );
			?>
           </select><br/>
			<?php echo esc_html__( 'Minute', 'tss' ) ?>: <input min="1" max="120" name="tss-yellowcards-minute" type="number" id="tss-yellowcards-minute" class="small-text"><br/>
           <span class="btn tss-add-card" color="yellow" table="goals" matchid="<?php echo esc_html( $post->ID ) ?>"><?php echo  esc_html__( 'Add', 'tss' ); ?> <img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>"></span>
           <div class="tss-hidden-content">
            <?php
			echo esc_html__( 'Yellow cards:', 'tss' ) . '<br/>';
			tss_list_cards( $post->ID, 'tss_yellowcards' );
			?>
           </div>
         </td>
       </tr>

       <tr>
         <th scope="row"><label><?php echo esc_html__( 'Red cards', 'tss' ); ?>:</label></th>
         <td>
           <select id="tss-redcards">
			<?php
			tss_list_players_in_season( $post );
			?>
           </select><br/>
			<?php echo esc_html__( 'Minute', 'tss' ) ?>: <input min="1" max="120" name="tss-redcards-minute" type="number" id="tss-redcards-minute" class="small-text"><br/>
           <span class="btn tss-add-card" color="red" table="goals" matchid="<?php echo esc_html( $post->ID ) ?>"><?php echo  esc_html__( 'Add', 'tss' ); ?> <img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>"></span>
           <div class="tss-hidden-content">
            <?php
			echo esc_html__( 'Red cards:', 'tss' ) . '<br/>';
			tss_list_cards( $post->ID, 'tss_redcards' );
			?>
           </div>
         </td>
       </tr>
     </tbody>
   </table>
	<?php
}

/**
 *
 * Match edit screen metabox callback for opponent details
 *
 * @since 1.0
 * @param object $post Post object.
 */
function tss_opponent_details_callback( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'matchdetails_opponent_nonce' );
	$meta = get_post_meta( $post->ID );
	?>
  <table class="form-table">
	<tbody>
	  <tr>
	<th scope="row"><label for="tss-match-show-opponent-stats"><?php echo esc_html__( 'Want to show opponent stats', 'tss' ); ?>?</label></th>
	<td><input name="tss-match-show-opponent-stats" type="checkbox" id="tss-match-show-opponent-stats" <?php echo tss_is_checked( 'tss-match-show-opponent-stats', $meta ) ?>></td>
  </tr>

  <tr>
	<th scope="row"><label for="tss-match-opponent-starters"><?php echo esc_html__( 'Starters', 'tss' ); ?>:</label></th>
	<td><textarea name="tss-match-opponent-starters" rows="11" id="tss-match-opponent-starters" class="large-text"><?php if ( isset( $meta['tss-match-opponent-starters'] ) ) { echo esc_html( $meta['tss-match-opponent-starters'][0] ); } ?></textarea></td>
  </tr>

  <tr>
	<th scope="row"><label for="tss-match-opponent-substitutes"><?php echo esc_html__( 'Substitutes', 'tss' ); ?>:</label></th>
	<td><textarea name="tss-match-opponent-substitutes" rows="6" id="tss-match-opponent-substitutes" class="large-text"><?php if ( isset( $meta['tss-match-opponent-substitutes'] ) ) { echo esc_html( $meta['tss-match-opponent-substitutes'][0] ); } ?></textarea></td>
  </tr>

  <tr>
	<th scope="row"><label for="tss-match-opponent-substitutions"><?php echo esc_html__( 'Substitutions', 'tss' ); ?>:</label></th>
	<td><textarea name="tss-match-opponent-substitutions" rows="3" id="tss-match-opponent-substitutions" class="large-text"><?php if ( isset( $meta['tss-match-opponent-substitutions'] ) ) { echo esc_html( $meta['tss-match-opponent-substitutions'][0] ); } ?></textarea></td>
  </tr>

  <tr>
	<th scope="row"><label for="tss-match-opponent-goals"><?php echo esc_html__( 'Goals', 'tss' ); ?>:</label></th>
	<td><textarea name="tss-match-opponent-goals" rows="3" id="tss-match-opponent-goals" class="large-text"><?php if ( isset( $meta['tss-match-opponent-goals'] ) ) { echo esc_html( $meta['tss-match-opponent-goals'][0] ); } ?></textarea></td>
  </tr>

  <tr>
	<th scope="row"><label for="tss-match-opponent-yellows"><?php echo esc_html__( 'Yellow cards', 'tss' ); ?>:</label></th>
	<td><textarea name="tss-match-opponent-yellows" rows="3" id="tss-match-opponent-yellows" class="large-text"><?php if ( isset( $meta['tss-match-opponent-yellows'] ) ) { echo esc_html( $meta['tss-match-opponent-yellows'][0] ); } ?></textarea></td>
  </tr>

  <tr>
	<th scope="row"><label for="tss-match-opponent-reds"><?php echo esc_html__( 'Red cards', 'tss' ); ?>:</label></th>
	<td><textarea name="tss-match-opponent-reds" rows="3" id="tss-match-opponent-reds" class="large-text"><?php if ( isset( $meta['tss-match-opponent-reds'] ) ) { echo esc_html( $meta['tss-match-opponent-reds'][0] ); } ?></textarea></td>
  </tr>
    </tbody>
  </table>
	<?php
}

/**
 *
 * Match edit screen metaboxes saving
 *
 * @since 1.0
 * @param int $post_id post id.
 */
function tss_matches_meta_save( $post_id ) {
	global $post;
	$slug = 'tss-matches';

	if ( get_post_type( $post ) == false ) {
		return;
	}

	// If this isn't a 'tss-matches' post, don't update it.
	if ( $slug != $post->post_type ) {
		return;
	}

	// Checks save status.
	$is_autosave = wp_is_post_autosave( $post_id );
	$is_revision = wp_is_post_revision( $post_id );
	$is_valid_nonce = ( isset( $_POST['prfx_nonce'] ) && wp_verify_nonce( $_POST['prfx_nonce'], basename( __FILE__ ) ) ) ? 'true' : 'false';

	// Exits script depending on save status.
	if ( $is_autosave || $is_revision || ! $is_valid_nonce ) {
		return;
	}

	// Sanitaze overtime, penalties, show_opponent_stats.
	if ( isset( $_POST['tss-match-overtime'] ) ) {
		$overtime = 1;
	} else {
		$overtime = 0;
	}

	if ( isset( $_POST['tss-match-penalties'] ) ) {
		$penalties = 1;
	} else {
		$penalties = 0;
	}

	if ( isset( $_POST['tss-match-show-opponent-stats'] ) ) {
		$show_opponent_stats = 1;
	} else {
		$show_opponent_stats = 0;
	}

	if ( isset( $_POST['tss-match-calculate-stats'] ) ) {
		$calculate_stats = 1;
	} else {
		$calculate_stats = 0;
	}

	update_post_meta( $post_id, 'tss-match-date', sanitize_text_field( $_POST['tss-match-date'] ) );
	update_post_meta( $post_id, 'tss-match-datetime', sanitize_text_field( $_POST['tss-match-datetime'] ) );
	update_post_meta( $post_id, 'tss-match-location', sanitize_text_field( $_POST['tss-match-location'] ) );
	update_post_meta( $post_id, 'tss-match-season', sanitize_text_field( $_POST['tss-match-season'] ) );
	update_post_meta( $post_id, 'tss-match-opponent', sanitize_text_field( $_POST['tss-match-opponent'] ) );
	update_post_meta( $post_id, 'tss-match-attendance', sanitize_text_field( $_POST['tss-match-attendance'] ) );
	update_post_meta( $post_id, 'tss-match-matchtype', sanitize_text_field( $_POST['tss-match-matchtype'] ) );
	update_post_meta( $post_id, 'tss-match-additional-matchtype', sanitize_text_field( $_POST['tss-match-additional-matchtype'] ) );
	update_post_meta( $post_id, 'tss-match-goals-for', sanitize_text_field( $_POST['tss-match-goals-for'] ) );
	update_post_meta( $post_id, 'tss-match-goals-against', sanitize_text_field( $_POST['tss-match-goals-against'] ) );
	update_post_meta( $post_id, 'tss-match-goals-for-pen', sanitize_text_field( $_POST['tss-match-goals-for-pen'] ) );
	update_post_meta( $post_id, 'tss-match-goals-against-pen', sanitize_text_field( $_POST['tss-match-goals-against-pen'] ) );
	update_post_meta( $post_id, 'tss-match-overtime', $overtime );
	update_post_meta( $post_id, 'tss-match-penalties', $penalties );
	update_post_meta( $post_id, 'tss-match-show-opponent-stats', $show_opponent_stats );
	update_post_meta( $post_id, 'tss-match-calculate-stats', $calculate_stats );
	update_post_meta(
		$post_id,
		'tss-match-opponent-starters',
		implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST['tss-match-opponent-starters'] ) ) )
	);
	update_post_meta(
		$post_id,
		'tss-match-opponent-substitutes',
		implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST['tss-match-opponent-substitutes'] ) ) )
	);
	update_post_meta(
		$post_id,
		'tss-match-opponent-substitutions',
		implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST['tss-match-opponent-substitutions'] ) ) )
	);
	update_post_meta(
		$post_id,
		'tss-match-opponent-goals',
		implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST['tss-match-opponent-goals'] ) ) )
	);
	update_post_meta(
		$post_id,
		'tss-match-opponent-yellows',
		implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST['tss-match-opponent-yellows'] ) ) )
	);
	update_post_meta(
		$post_id,
		'tss-match-opponent-reds',
		implode( "\n", array_map( 'sanitize_text_field', explode( "\n", $_POST['tss-match-opponent-reds'] ) ) )
	);
}
	add_action( 'save_post', 'tss_matches_meta_save' );

?>
