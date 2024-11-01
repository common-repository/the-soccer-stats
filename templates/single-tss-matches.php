<?php
/**
 * Match template
 *
 * Template file for showing match.
 *
 * @package The Soccer Stats
 * @since 1.0
 */

get_header();

$json = tss_get_match_details();
$data = json_decode( $json );
$content = get_post_field( 'post_content', $post->ID );
$content = apply_filters( 'the_content', $content );


?>

<div class="bootstrap-wrapper">
  <div class="<?php echo tss_get_container() ?>">
    <div class="row">
			<div class="col-sm-12">
				<h4><?php echo esc_html( $data->date ) ?> <span class="pull-right"><?php echo esc_html( tss_e_matchtype( $data->matchtype, $data->{'additional-matchtype'} ) ) ?></span></h4>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<h1><?php echo esc_html( $data->hometeam ) ?> <span class="pull-right"><?php echo esc_html( tss_get_goal_in_match( $data->penalties, $data->homegoals, $data->homegoalspen ) ) ?></span></h1>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<h1><?php echo esc_html( $data->awayteam ) ?> <span class="pull-right"><?php echo esc_html( tss_get_goal_in_match( $data->penalties, $data->awaygoals, $data->awaygoalspen ) ) ?></span></h1>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12" style="margin-bottom: 20px;">
				<h4><?php echo esc_html( tss_get_match_time( $data->overtime, $data->penalties ) ) ?> <span class="pull-right"><?php if ( '' != $data->attendance ) { echo esc_html__( 'Attendance', 'tss' ) . ': ' . esc_html( $data->attendance ); } ?></span></h4>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<ul class="nav nav-tabs">
				  <li class="active"><a data-toggle="tab" href="#matchreport"><?php echo esc_html__( 'Match report', 'tss' ) ?></a></li>
				  <li><a data-toggle="tab" href="#teams"><?php echo esc_html__( 'Team details', 'tss' ) ?></a></li>
				</ul>

				<div class="tab-content">

				  <div id="matchreport" class="tab-pane fade in active">
				    <?php echo $content ?>
				  </div>
				  <div id="teams" class="tab-pane fade">
						<?php
						if ( '1' == $data->{'show-opponent-stats'} ) {
						?>
						<div class="col-sm-6">
							<h2><?php echo esc_html( $data->hometeam ) ?></h2>
							<?php
							if ( '1' == $data->location || '3' == $data->location ) {
								// Print my team.
								tss_e_myteam_details($data);
							} else {
								// Print opponent.
								tss_e_opponent_details($data);
							}
							?>
						</div>

						<div class="col-sm-6">
							<h2><?php echo esc_html( $data->awayteam ) ?></h2>
							<?php
							if ( '1' == $data->location || '3' == $data->location ) {
								// Print opponent.
								tss_e_opponent_details($data);
							} else {
								tss_e_myteam_details($data);
							}
							?>
						</div>
						<?php
						} else {
							?>
							<div class="col-sm-12">
								<?php

								tss_e_myteam_details($data);

								?>
							</div>
							<?php
						}
						?>
				  </div>

				</div>
			</div>
		</div>
	</div>
</div>

<?php
	get_footer();
?>
