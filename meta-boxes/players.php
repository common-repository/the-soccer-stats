<?php
/**
 * Players metaboxes
 *
 * Metabox functions related to players in this file.
 *
 * @package The Soccer Stats
 * @since 1.0
 */

/**
 *
 * Adds a meta boxes to player edit screen
 *
 * @since 1.0
 */
function add_players_metaboxes() {
	global $post;

	if ( ! isset( $post ) ) {
		return;
	}

	add_meta_box( 'player-details', __( 'Player details', 'tss' ), 'tss_player_details_callback', 'tss-players', 'normal' );

	if ( 'publish' === $post->post_status ) {
		add_meta_box( 'players-player-seasons', __( 'Add player to season', 'tss' ), 'tss_playerseasons_callback', 'tss-players', 'side' );
	}
}
add_action( 'add_meta_boxes', 'add_players_metaboxes' );

/**
 *
 * Player details metabox callback
 *
 * @since 1.0
 * @param object $post Post object.
 */
function tss_player_details_callback( $post ) {
	wp_nonce_field( basename( __FILE__ ), 'playerdetails_nonce' );
	$meta = get_post_meta( $post->ID );

	?>
 <table class="form-table">
   <tbody>
     <tr>
       <th scope="row"><label for="tss-shirtnumber"><?php echo esc_html__( 'Shirt number', 'tss' ) ?>:</label></th>
       <td><input type="number" name="tss-shirtnumber" id="tss-shirtnumber" value="<?php if ( isset( $meta['tss-shirtnumber'] ) ) { echo esc_html( $meta['tss-shirtnumber'][0] ); } ?>" class="small-text" /></td>
     </tr>

     <tr>
       <th scope="row"><label for="tss-pob"><?php echo esc_html__( 'Place of birth', 'tss' ) ?>:</label></th>
       <td><input type="text" name="tss-pob" id="tss-pob" value="<?php if ( isset( $meta['tss-pob'] ) ) { echo esc_html( $meta['tss-pob'][0] ); } ?>" class="regular-text" /></td>
     </tr>

     <tr>
       <th scope="row"><label for="tss-height"><?php echo esc_html__( 'Height', 'tss' ) ?>:</label></th>
       <td><input type="text" name="tss-height" id="tss-height" value="<?php if ( isset( $meta['tss-height'] ) ) { echo esc_html( $meta['tss-height'][0] ); } ?>" class="medium-text" /></td>
     </tr>

     <tr>
       <th scope="row"><label for="tss-weight"><?php echo esc_html__( 'Weight', 'tss' ) ?>:</label></th>
       <td><input type="text" name="tss-weight" id="tss-weight" value="<?php if ( isset( $meta['tss-weight'] ) ) { echo esc_html( $meta['tss-weight'][0] ); } ?>" class="medium-text" /></td>
     </tr>

     <tr>
       <th scope="row"><label for="tss-dob"><?php echo esc_html__( 'Date of birth', 'tss' ) ?>:</label></th>
       <td>
         <input type="date" name="tss-dob" id="tss-dob" value="<?php if ( isset( $meta['tss-dob'] ) ) { echo esc_html( $meta['tss-dob'][0] ); } ?>" />
       </td>
     </tr>

     <tr>
       <th scope="row"><label for="tss-position"><?php echo esc_html__( 'Player position', 'tss' ) ?>:</label></th>
       <td>
         <select name="tss-position" id="tss-position">
     			<?php
	 			$positions = array(
	 				array( 'id' => '1', 'value' => 'Goalkeeper' ),
	 				array( 'id' => '2', 'value' => 'Defender' ),
	 				array( 'id' => '3', 'value' => 'Midfield' ),
	 				array( 'id' => '4', 'value' => 'Striker' ),
	 			);

	 			foreach ( $positions as $position ) {

					switch ( $position['id'] ) {
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

	 				$selected = '';
	 				if ( isset( $meta['tss-position'] ) ) {
	 					if ( $meta['tss-position'][0] === $position['id'] ) {
	 						$selected = ' SELECTED';
	 					}
	 				}

	 				?>
     				<option value="<?php echo esc_html( $position['id'] ); ?>"<?php echo esc_html( $selected ); ?>><?php echo esc_html( $string ) ?></option>
     				<?php
	 			}
	 			?>
     		</select>
       </td>
     </tr>

     <tr>
       <th scope="row"><label for="tss-previous-clubs"><?php echo esc_html__( 'Previous clubs', 'tss' ); ?>:</label></th>
       <td><textarea name="tss-previous-clubs" rows="6" id="tss-previous-clubs" class="large-text"><?php if ( isset( $meta['tss-previous-clubs'] ) ) { echo esc_html( $meta['tss-previous-clubs'][0] ); } ?></textarea></td>
     </tr>
   </tbody>
 </table>
	<?php
}

/**
 *
 * Player edit screen metabox callback for player in seasons
 *
 * @since 1.0
 * @param object $post Post object.
 */
function tss_playerseasons_callback( $post ) {
	?>

	<p>
		<?php echo esc_html__( 'Add or remover player to season.', 'tss' ) ?>

    <?php

	$seasons = get_posts(
		array(
		'post_type' => 'tss-seasons',
		'orderby'   => 'post_title',
		'order'     => 'DESC',
		)
	);

	echo '<select id="tss-player-seasons-value">';

	foreach ( $seasons as $season ) {
		?>

        <option value="<?php echo esc_html( $season->ID ); ?>"><?php echo esc_html( $season->post_title ); ?></option>

		<?php

	}
	?>
</select> <span class="btn" id="tss-add-player-season"><?php echo  esc_html__( 'Add', 'tss' ); ?> <img src="<?php echo esc_url( admin_url( 'images/wpspin_light.gif' ) ); ?>"></span>

	</p>
  <div id="player-season-content">
    <strong><?php echo esc_html__( 'Player added to following seasons:', 'tss' ); ?><br/></strong>
    <?php

	tss_list_player_seasons( $post->ID );

	?>
  </div>

	<?php
}

/**
 *
 * Player edit screen metaboxes saving
 *
 * @since 1.0
 * @param int $post_id Post ID.
 */
function tss_players_meta_save( $post_id ) {

	global $post;
	$slug = 'tss-players';

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

	update_post_meta( $post_id, 'tss-shirtnumber', sanitize_text_field( $_POST['tss-shirtnumber'] ) );
	update_post_meta( $post_id, 'tss-position', sanitize_text_field( $_POST['tss-position'] ) );
	update_post_meta( $post_id, 'tss-dob', sanitize_text_field( $_POST['tss-dob'] ) );
	update_post_meta( $post_id, 'tss-pob', sanitize_text_field( $_POST['tss-pob'] ) );
	update_post_meta( $post_id, 'tss-height', sanitize_text_field( $_POST['tss-height'] ) );
	update_post_meta( $post_id, 'tss-weight', sanitize_text_field( $_POST['tss-weight'] ) );
	update_post_meta( $post_id, 'tss-previous-clubs', sanitize_text_field( $_POST['tss-previous-clubs'] ) );
}
add_action( 'save_post', 'tss_players_meta_save' );

?>
