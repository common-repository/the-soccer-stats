<?php
/**
 * Player template
 *
 * Template file for showing player.
 *
 * @package The Soccer Stats
 * @since 1.0
 */

get_header();

$data = tss_get_player_details();

if ( '' != $data[0]['imageurl'] ) {
	$details_class = 'col-sm-8';
} else {
	$details_class = 'col-sm-12';
}

?>
<div id="player-id" playerid="<?php echo $post->ID ?>"></div>
<div class="bootstrap-wrapper">
  <div class="<?php echo tss_get_container() ?>">
    <div class="row">
      <div class="<?php echo esc_html( $details_class ) ?>">
        <h1><?php echo esc_html( $post->post_title ) ?><?php if ( '' != $data[0]['number'] ) {  echo esc_html( ' #' . $data[0]['number'] ); } ?></h1>
        <?php if ( '' != $data[0]['dob'] ) { echo '<strong>' . esc_html__( 'Date of birth: ', 'tss' ) . '</strong>' . esc_html( $data[0]['dob'] ) . '</br>'; } ?>
        <?php if ( '' != $data[0]['pob'] ) { echo '<strong>' . esc_html__( 'Place of birth: ', 'tss' ) . '</strong>' . esc_html( $data[0]['pob'] ) . '</br>'; } ?>
        <?php if ( '' != $data[0]['position'] ) { echo '<strong>' . esc_html__( 'Position: ', 'tss' ) . '</strong>' . esc_html( $data[0]['position'] ) . '</br>'; } ?>
        <?php if ( '' != $data[0]['height'] ) { echo '<strong>' . esc_html__( 'Height: ', 'tss' ) . '</strong>' . esc_html( $data[0]['height'] ) . '</br>'; } ?>
        <?php if ( '' != $data[0]['weight'] ) { echo '<strong>' . esc_html__( 'Weight: ', 'tss' ) . '</strong>' . esc_html( $data[0]['weight'] ) . '</br>'; } ?>
        <?php if ( '' != $data[0]['previous-clubs'] ) { echo '<strong>' . esc_html__( 'Previous clubs: ', 'tss' ) . '</strong>' . esc_html( $data[0]['previous-clubs'] ) . '</br>'; } ?>
        <?php if ( '' != $data[0]['description'] ) { echo  '<h2>' . esc_html__( 'Player description', 'tss' ) . '</h2>' . $data[0]['description']; } ?>
      </div>
		<?php
		if ( '' != $data[0]['imageurl'] ) {
			?>
		  <div class="col-sm-4">
			<img src="<?php echo esc_url( $data[0]['imageurl'] ); ?>" class="img-thumbnail" />
        </div>
        <?php
		}
		?>
    </div>

    <div class="row">
      <div class="col-md-6">
        <h1><?php echo esc_html__( 'Stats', 'tss' ); ?></h1>
				<?php

				$filter = new tssFilter( 'filter-options-season-player' );
				$filter->Display();

				?>
      </div>
    </div>

		<div id="ajax-content">
			<?php
			$seasonalstats = new tssPlayerSeasonalStatsTable( $post->ID, 0 );
			$seasonalstats->Display();
			?>
		</div>
		<?php

		$latest = array(
			array('title' => __( 'Latest matches in starting 11', 'tss' ), 'table' => 'tss_starters'),
			array('title' => __( 'Latest matches where scored', 'tss' ), 'table' => 'tss_goals'),
			array('title' => __( 'Latest matches where booked', 'tss' ), 'table' => 'tss_yellowcards'),
			array('title' => __( 'Latest matches where sent off', 'tss' ), 'table' => 'tss_redcards')
		);

		foreach ($latest as $row) {
			tss_e_latest_matches_by_player($post->ID, $row['title'], $row['table']);
		}
		?>

  </div>
</div>

<?php
get_footer();
?>
