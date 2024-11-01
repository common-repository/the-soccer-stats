<?php
/**
 * Class for tssPlayerStatsTable
 *
 *
 * @package The Soccer Stats
 * @since 1.05
 */

 class tssPlayerStatsTable {

	public $seasonid;
  public $matchtypeid;

 	function __construct($season, $matchtype) {
         $this->seasonid = $season;
 				$this->matchtypeid = $matchtype;
   }

	function Display() {
		?>
		<div class="row">
      <div class="col-sm-12">
        <h2><?php echo esc_html__( 'Players', 'tss' ) ?></h2>
				<table class="table" id="player-stats-table">
					<thead>
						<tr>
							<th>Player <i class="fa fa-caret-down"></i></th>
							<th class="text-center"><i class="fa fa-user" data-toggle="tooltip" title="<?php echo esc_html__( 'Starts', 'tss' ) ?>"></i> <i class="fa fa-caret-down"></i></th>
							<th class="text-center"><i class="fa fa-exchange" data-toggle="tooltip" title="<?php echo esc_html__( 'Substitutions in', 'tss' ) ?>"></i> <i class="fa fa-caret-down"></i></th>
							<th class="text-center"><i class="fa fa-futbol-o" data-toggle="tooltip" title="<?php echo esc_html__( 'Goals', 'tss' ) ?>"></i> <i class="fa fa-caret-down"></i></th>
							<th class="text-center"><i class="fa fa-exclamation-triangle" data-toggle="tooltip" title="<?php echo esc_html__( 'Yellow cards', 'tss' ) ?>"></i> <i class="fa fa-caret-down"></i></th>
							<th class="text-center"><i class="fa fa-ban" data-toggle="tooltip" title="<?php echo esc_html__( 'Red cards', 'tss' ) ?>"></i> <i class="fa fa-caret-down"></i></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$this->getStatsRows( );
						?>
					</tbody>
				</table>
			</div>
		</div>

		<?php
	}

	private function getStatsRows( ) {
		global $wpdb;

		$posts_table = $wpdb->prefix . 'posts';
		$postsmeta_table = $wpdb->prefix . 'postmeta';


		$q = "SELECT p.post_title AS playername,
		p.ID AS playerid,
		CAST(pm.meta_value AS UNSIGNED) AS playernumber
		FROM $posts_table p, $postsmeta_table pm, tss_player_seasons ps
		WHERE p.id = ps.playerid AND ps.seasonid = $this->seasonid AND
		(pm.post_id = p.id AND pm.meta_key = 'tss-shirtnumber')
		ORDER BY playernumber ASC";


		$posts = $wpdb->get_results( $q );

		foreach ( $posts as $player ) {
			?>

	 		<?php
			$seasondata = tss_get_player_stats_by_season( $player->playerid, $this->seasonid, $this->matchtypeid );
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
 }
