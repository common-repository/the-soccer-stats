<?php
/**
 * Functions file for season match list
 *
 * The Soccer Stats season match list class.
 *
 * @package The Soccer Stats
 * @since 1.05
 */

class tssSeasonMatchList {

	public $seasonid;
  public $matchtype;

	function __construct($season, $matchtype) {
        $this->seasonid = $season;
				$this->matchtype = $matchtype;
  }

	function Display() {

		$json = $this->GetMatchData();
		$data = json_decode( $json );

		?>
		<div class="row">
      <div class="col-sm-12">
        <h2><?php echo esc_html__( 'Matches', 'tss' ) ?></h2>
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
						foreach ($data as $match) {
							?>
							<tr>
								<td><?php echo esc_html( tss_e_match_date_and_time( $match->date, $match->datetime ) ) ?></td>
	              <td class="text-center"><?php echo e_team_with_link( $match->hometeam, $match->{'opponent-id'} ) ?></td>
	              <td class="text-center"><?php echo e_team_with_link( $match->awayteam, $match->{'opponent-id'} ) ?></td>
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

	private function GetMatchData() {
		global $wpdb;

		$data = array(); // Data thats returned

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
		);

		$posts_table = $wpdb->prefix . 'posts';
		$posts_metatable = $wpdb->prefix . 'postmeta';

		$q = "SELECT p.ID AS id,
		pm2.meta_value AS matchdate
			FROM $posts_metatable pm, $posts_metatable pm2, $posts_table p
			WHERE (p.ID = pm.post_id AND pm.meta_value = $this->seasonid AND pm.meta_key = 'tss-match-season') AND
			(p.ID = pm2.post_id AND pm2.meta_key = 'tss-match-date') ORDER BY matchdate";

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
							$singlerow[ $short_index . '-id' ] = $meta[ $metafield ][0];
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
					array_push( $data,
						$singlerow
					);
				} else { //else push to table if match type matches..
					if( $this->matchtype === $singlerow[ 'matchtype' ] ) {
						array_push( $data,
							$singlerow
						);
					}
				}

			}
		}

		return json_encode( $data );
	}


}
