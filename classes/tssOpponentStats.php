<?php
/**
 * Class for tssOpponentStats
 *
 *
 * @package The Soccer Stats
 * @since 1.05
 */

 class tssOpponentStats {

	public $opponentid;
  public $matchtype;

 	function __construct($opponent, $matchtype) {
		$this->opponentid = $opponent;
 		$this->matchtype = $matchtype;
   }

	function Display() {
		$data = json_decode( $this->getOpponentData() );
		?>

		<div class="row">
			<div class="col-sm-12">
				<table class="table">
					<thead>
						<tr>
							<th><?php echo esc_html__( 'Location', 'tss' ) ?></th>
							<th class="text-center"><?php echo esc_html__( 'W', 'tss' ) ?></th>
							<th class="text-center"><?php echo esc_html__( 'D', 'tss' ) ?></th>
							<th class="text-center"><?php echo esc_html__( 'L', 'tss' ) ?></th>
							<th class="text-center"><?php echo esc_html__( 'GD', 'tss' ) ?></th>
						</tr>
					</thead>
					<tbody>

						<tr>
							<td><strong><?php echo esc_html__( 'Home', 'tss' ) ?></strong></td>
							<td class="text-center"><?php echo esc_html( $data->stats[0]->homewins ) ?></td>
							<td class="text-center"><?php echo esc_html( $data->stats[0]->homedraws ) ?></td>
							<td class="text-center"><?php echo esc_html( $data->stats[0]->homeloses ) ?></td>
							<td class="text-center"><?php echo esc_html( $data->stats[0]->homegoals . '-' . $data->stats[0]->homegoals_against ) ?></td>
						</tr>

						<tr>
							<td><strong><?php echo esc_html__( 'Away', 'tss' ) ?></strong></td>
							<td class="text-center"><?php echo esc_html( $data->stats[0]->awaywins ) ?></td>
							<td class="text-center"><?php echo esc_html( $data->stats[0]->awaydraws ) ?></td>
							<td class="text-center"><?php echo esc_html( $data->stats[0]->awayloses ) ?></td>
							<td class="text-center"><?php echo esc_html( $data->stats[0]->awaygoals . '-' . $data->stats[0]->awaygoals_against ) ?></td>
						</tr>

						<tr>
							<td><strong><?php echo esc_html__( 'Neutral', 'tss' ) ?></strong></td>
							<td class="text-center"><?php echo esc_html( $data->stats[0]->neutralwins ) ?></td>
							<td class="text-center"><?php echo esc_html( $data->stats[0]->neutraldraws ) ?></td>
							<td class="text-center"><?php echo esc_html( $data->stats[0]->neutralloses ) ?></td>
							<td class="text-center"><?php echo esc_html( $data->stats[0]->neutralgoals . '-' . $data->stats[0]->neutralgoals_against ) ?></td>
						</tr>

					</tbody>

					<tfoot>
						<tr>
							<td><strong><?php echo esc_html__('Total', 'tss') ?></strong></td>
							<td class="text-center"><?php echo esc_html( $data->stats[0]->totalwins ) ?></td>
							<td class="text-center"><?php echo esc_html( $data->stats[0]->totaldraws ) ?></td>
							<td class="text-center"><?php echo esc_html( $data->stats[0]->totalloses ) ?></td>
							<td class="text-center"><?php echo esc_html( $data->stats[0]->totalgoals . '-' . $data->stats[0]->totalgoals_against ) ?></td>
						</tr>
					</tfoot>
				</table>
			</div>
		</div>

		<div class="row">
			<div class="col-sm-12">
				<h2><?php echo esc_html__( 'Matches against this opponent', 'tss' ) ?></h2>
				<table class="table">
					<thead>
						<tr>
							<th><?php echo esc_html__( 'Date', 'tss' ) ?></th>
							<th class="text-center"><?php echo esc_html__( 'Hometeam', 'tss' ) ?></th>
							<th class="text-center"><?php echo esc_html__( 'Awayteam', 'tss' ) ?></th>
							<th class="text-center"><?php echo esc_html__( 'Matchtype', 'tss' ) ?></th>
							<th class="text-center"><?php echo esc_html__( 'Result', 'tss' ) ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
						foreach ($data->matches as $match) {
							?>
							<tr>
								<td><?php echo esc_html( tss_e_match_date_and_time( $match->date, $match->datetime ) ) ?></td>
								<td class="text-center"><?php echo esc_html( $match->hometeam ) ?></td>
								<td class="text-center"><?php echo esc_html( $match->awayteam ) ?></td>
								<td class="text-center"><?php echo esc_html( tss_e_matchtype( $match->matchtype, $match->{'additional-matchtype'} ) ) ?></td>
								<td class="text-center"><a href="<?php echo esc_url( get_permalink( $match->id ) ) ?>"><?php echo esc_html( tss_get_result_string( $match->penalties, $match->overtime, $match->homegoals, $match->awaygoals, $match->homegoalspen, $match->awaygoalspen  ) ) ?></a></td>
							</tr>
							<?php
						}
						?>
					</tbody>
				</table>
			</div>
		</div>
		<?php
	}

	private function getOpponentData() {
		global $wpdb;

		$data['matches'] = array(); // Data thats returned
		$data['stats'] = array(); // Data thats returned

		$stats['homewins'] = 0;
		$stats['homedraws'] = 0;
		$stats['homeloses'] = 0;
		$stats['homegoals'] = 0;
		$stats['homegoals_against'] = 0;
		$stats['awaywins'] = 0;
		$stats['awaydraws'] = 0;
		$stats['awayloses'] = 0;
		$stats['awaygoals'] = 0;
		$stats['awaygoals_against'] = 0;
		$stats['neutralwins'] = 0;
		$stats['neutraldraws'] = 0;
		$stats['neutralloses'] = 0;
		$stats['neutralgoals'] = 0;
		$stats['neutralgoals_against'] = 0;
		$stats['totalwins'] = 0;
		$stats['totaldraws'] = 0;
		$stats['totalloses'] = 0;
		$stats['totalgoals'] = 0;
		$stats['totalgoals_against'] = 0;

		// My team.
		$options = get_option( 'tss_options' );
		$myteam = get_the_title( $options['my_team'] );

		$metafields = array(
			'tss-match-goals-for',
			'tss-match-goals-against',
			'tss-match-goals-for-pen',
			'tss-match-goals-against-pen',
			'tss-match-opponent',
			'tss-match-date',
			'tss-match-datetime',
			'tss-match-location',
			'tss-match-matchtype',
			'tss-match-additional-matchtype',
			'tss-match-overtime',
			'tss-match-penalties',
			'tss-match-calculate-stats'
		);

		$posts_table = $wpdb->prefix . 'posts';
		$posts_metatable = $wpdb->prefix . 'postmeta';

		$q = "SELECT p.ID AS id,
		p.post_title AS title,
		pm.meta_value AS matchdate
		FROM wp_posts p, wp_postmeta pm, wp_postmeta pm2
		WHERE (p.ID = pm.post_id AND pm.meta_key = 'tss-match-date') AND
		(p.ID = pm2.post_id AND pm2.meta_key = 'tss-match-opponent' AND pm2.meta_value = $this->opponentid) ORDER BY matchdate DESC";

		$results = $wpdb->get_results( $q, OBJECT );

		/*
		 * Loops Matches
		 */
		if ( $results ) {
			foreach ( $results as $row ) {
				$meta = get_post_meta( $row->id );
				$singlerow = array();
				$singlerow[ 'id' ] = $row->id;

				foreach ( $metafields as $metafield ) {
					$short_index = str_replace( 'tss-match-', '', $metafield );
					if ( isset( $meta[ $metafield ] ) ) {
						if ( 'tss-match-opponent' == $metafield ) { // Special check if opponent.
							$singlerow[ $short_index ] = get_the_title( $meta[ $metafield ][0] );
						} elseif ( 'tss-match-matchtype' == $metafield ) { // Special check if matchtype.
							$singlerow[ $short_index ] = get_the_title( $meta[ $metafield ][0] );
						} elseif ( 'tss-match-location' == $metafield ) { // Special check if location.
							if ( '1' == $meta[ $metafield ][0] || '3' == $meta[ $metafield ][0] ) { // If home or neutral match.
								$singlerow['hometeam'] = $myteam;
								$singlerow['awayteam'] = $singlerow['opponent'];
								$singlerow['homegoals'] = $meta['tss-match-goals-for'][0];
								$singlerow['awaygoals'] = $meta['tss-match-goals-against'][0];
								$singlerow['homegoalspen'] = $singlerow['goals-for-pen'];
								$singlerow['awaygoalspen'] = $singlerow['goals-against-pen'];
							} else { // If away match.
								$singlerow['hometeam'] = $singlerow['opponent'];
								$singlerow['awayteam'] = $myteam;
								$singlerow['homegoals'] = $meta['tss-match-goals-against'][0];
								$singlerow['awaygoals'] = $meta['tss-match-goals-for'][0];
								$singlerow['homegoalspen'] = $singlerow['goals-against-pen'];
								$singlerow['awaygoalspen'] = $singlerow['goals-for-pen'];
							}
							$singlerow[ $short_index ] = $meta['tss-match-location'][0];
						} else {
							$singlerow[ $short_index ] = $meta[ $metafield ][0];
						}
					} else {
						$singlerow[ $short_index ] = '';
					}
				}

				if ( '' != $singlerow['date'] ) {
					$singlerow['date'] = date_i18n( get_option( 'date_format' ), strtotime( $singlerow['date'] ) );
				} else {
					$singlerow['date'] = '';
				}

				//Push data to array, if match type 'any'
				if( 'all' === $this->matchtype) {
					array_push( $data['matches'],
						$singlerow
					);

					// Calculate stats.
					if ( '1' == $singlerow['calculate-stats']) {
						if ( '1' == $singlerow['location']) {

							if ($singlerow['goals-for'] > $singlerow['goals-against'] ) {
								$stats['homewins']++;
							}

							if ($singlerow['goals-for'] < $singlerow['goals-against'] ) {
								$stats['homeloses']++;
							}

							if ($singlerow['goals-for'] == $singlerow['goals-against'] ) {
								$stats['homedraws']++;
							}

							$stats['homegoals'] = $stats['homegoals'] + $singlerow['goals-for'];
							$stats['homegoals_against'] = $stats['homegoals_against'] + $singlerow['goals-against'];

						} elseif ( '2' == $singlerow['location'] ) {

							if ($singlerow['goals-for'] > $singlerow['goals-against'] ) {
								$stats['awaywins']++;
							}

							if ($singlerow['goals-for'] < $singlerow['goals-against'] ) {
								$stats['awayloses']++;
							}

							if ($singlerow['goals-for'] == $singlerow['goals-against'] ) {
								$stats['awaydraws']++;
							}

							$stats['awaygoals'] = $stats['awaygoals'] + $singlerow['goals-for'];
							$stats['awaygoals_against'] = $stats['awaygoals_against'] + $singlerow['goals-against'];

						} elseif ( '3' == $singlerow['location'] ) {

							if ($singlerow['goals-for'] > $singlerow['goals-against'] ) {
								$stats['neutralwins']++;
							}

							if ($singlerow['goals-for'] < $singlerow['goals-against'] ) {
								$stats['neutralloses']++;
							}

							if ($singlerow['goals-for'] == $singlerow['goals-against'] ) {
								$stats['neutraldraws']++;
							}

							$stats['neutralgoals'] = $stats['neutralgoals'] + $singlerow['goals-for'];
							$stats['neutralgoals_against'] = $stats['neutralgoals_against'] + $singlerow['goals-against'];

						}
					}

				} else { //else push to table if match type matches..
					if( $this->matchtype === $singlerow[ 'matchtype' ] ) {
						array_push( $data['matches'],
							$singlerow
						);

						// Calculate stats.
						if ( '1' == $singlerow['calculate-stats']) {
							if ( '1' == $singlerow['location']) {

								if ($singlerow['goals-for'] > $singlerow['goals-against'] ) {
									$stats['homewins']++;
								}

								if ($singlerow['goals-for'] < $singlerow['goals-against'] ) {
									$stats['homeloses']++;
								}

								if ($singlerow['goals-for'] == $singlerow['goals-against'] ) {
									$stats['homedraws']++;
								}

								$stats['homegoals'] = $stats['homegoals'] + $singlerow['goals-for'];
								$stats['homegoals_against'] = $stats['homegoals_against'] + $singlerow['goals-against'];

							} elseif ( '2' == $singlerow['location'] ) {

								if ($singlerow['goals-for'] > $singlerow['goals-against'] ) {
									$stats['awaywins']++;
								}

								if ($singlerow['goals-for'] < $singlerow['goals-against'] ) {
									$stats['awayloses']++;
								}

								if ($singlerow['goals-for'] == $singlerow['goals-against'] ) {
									$stats['awaydraws']++;
								}

								$stats['awaygoals'] = $stats['awaygoals'] + $singlerow['goals-for'];
								$stats['awaygoals_against'] = $stats['awaygoals_against'] + $singlerow['goals-against'];

							} elseif ( '3' == $singlerow['location'] ) {

								if ($singlerow['goals-for'] > $singlerow['goals-against'] ) {
									$stats['neutralwins']++;
								}

								if ($singlerow['goals-for'] < $singlerow['goals-against'] ) {
									$stats['neutralloses']++;
								}

								if ($singlerow['goals-for'] == $singlerow['goals-against'] ) {
									$stats['neutraldraws']++;
								}

								$stats['neutralgoals'] = $stats['neutralgoals'] + $singlerow['goals-for'];
								$stats['neutralgoals_against'] = $stats['neutralgoals_against'] + $singlerow['goals-against'];

							}
						}

					}
				}

			}
		}

		$stats['totalwins'] = $stats['homewins'] + $stats['awaywins'] + $stats['neutralwins'];
		$stats['totalloses'] = $stats['homeloses'] + $stats['awayloses'] + $stats['neutralloses'];
		$stats['totaldraws'] = $stats['homedraws'] + $stats['awaydraws'] + $stats['neutraldraws'];
		$stats['totalgoals'] = $stats['homegoals'] + $stats['awaygoals'] + $stats['neutralgoals'];
		$stats['totalgoals_against'] = $stats['homegoals_against'] + $stats['awaygoals_against'] + $stats['neutralgoals_against'];



		array_push( $data['stats'],
			$stats
		);

		return json_encode( $data );
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
