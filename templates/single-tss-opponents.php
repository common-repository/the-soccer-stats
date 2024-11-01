<?php
/**
 * Opponent template
 *
 * Template file for showing opponent.
 *
 * @package The Soccer Stats
 * @since 1.0
 */

get_header();
?>

<?php
//$json = tss_get_matches_by_opponent($post->ID);
//$data = json_decode( $json );
?>
<div id="opponent-id" opponentid="<?php echo $post->ID ?>"></div>
<div class="bootstrap-wrapper">
	<div class="<?php echo tss_get_container() ?>">
		<div class="row">
			<div class="col-sm-12">
				<h1><?php echo esc_html__( 'Stats against this opponent', 'tss' ) ?> <?php echo tss_get_team_logo_thumbnail( $post->post_title ) ?></h1>
			</div>
			<div class="col-md-6">
				<?php
				$filter = new tssFilter( 'filter-options-opponent' );
				$filter->Display();
				?>
			</div>
		</div>
		<div id="ajax-content">
			<?php
			$opponentpage = new tssOpponentStats($post->ID, 'all');
			$opponentpage->Display();
			?>
		</div>
	</div>
</div>

<?php
get_footer();
?>
