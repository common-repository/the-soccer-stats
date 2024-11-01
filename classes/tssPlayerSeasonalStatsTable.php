<?php
/**
 * Class for tssPlayerSeasonalStatsTable
 *
 *
 * @package The Soccer Stats
 * @since 1.05
 */

 class tssPlayerSeasonalStatsTable {

	public $playerid;
  public $matchtypeid;

 	function __construct($player, $matchtype) {
		$this->playerid = $player;
 		$this->matchtypeid = $matchtype;
   }

	function Display() {
		?>
		<div class="row">
			<div class="col-sm-12">
				<table class="table" id="player-season-by-season">
					<thead>
						<tr>
							<th><?php echo esc_html__( 'Season', 'tss' ) ?> <i class="fa fa-caret-down"></i></th>
							<th class="text-center"><i class="fa fa-user" data-toggle="tooltip" title="<?php echo esc_html__( 'Starts', 'tss' ) ?>"></i> <i class="fa fa-caret-down"></i></th>
							<th class="text-center"><i class="fa fa-exchange" data-toggle="tooltip" title="<?php echo esc_html__( 'Substitutions in', 'tss' ) ?>"></i> <i class="fa fa-caret-down"></i></th>
							<th class="text-center"><i class="fa fa-futbol-o" data-toggle="tooltip" title="<?php echo esc_html__( 'Goals', 'tss' ) ?>"></i> <i class="fa fa-caret-down"></i></th>
							<th class="text-center"><i class="fa fa-exclamation-triangle" data-toggle="tooltip" title="<?php echo esc_html__( 'Yellow cards', 'tss' ) ?>"></i> <i class="fa fa-caret-down"></i></th>
							<th class="text-center"><i class="fa fa-ban" data-toggle="tooltip" title="<?php echo esc_html__( 'Red cards', 'tss' ) ?>"></i> <i class="fa fa-caret-down"></i></th>
						</tr>
					</thead>
					<tbody>
						<?php
						$this->getStatsRows();
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

		$q = "SELECT p.post_title AS post_title,
		p.ID AS ID
		FROM $posts_table p, tss_player_seasons ps
		WHERE p.post_type = 'tss-seasons' AND
		ps.seasonid = p.ID AND ps.playerid = $this->playerid
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
			$seasondata = tss_get_player_stats_by_season( $this->playerid, $season->ID, $this->matchtypeid );
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
 }
