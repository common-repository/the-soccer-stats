<?php
/**
 * Functions file for filtering
 *
 * The Soccer Stats filtering functions.
 *
 * @package The Soccer Stats
 * @since 1.05
 */

class tssFilter {

	public $selectId;

	function __construct( $selector ) {
    $this->selectId = $selector;
  }

	function Display() {
		?>
			<select class="form-control" id="<?php echo $this->selectId ?>">
				<option><?php echo esc_html__( 'Filter by match type..', 'tss' ) ?></option>
				<?php $this->getMatchtypeOptions(); ?>
			</select>
		<?php
	}

	private function getMatchtypeOptions() {
		$args = array(
			'posts_per_page' => -1,
			'post_type' => 'tss-matchtypes',
			'orderby' => 'title',
			'order' => 'ASC'
		);

		$the_query = new WP_Query( $args );

		// The Loop
		if ( $the_query->have_posts() ) {
			while ( $the_query->have_posts() ) {
				$the_query->the_post();
				echo '<option value="' . get_the_id() . '">' . get_the_title() . '</option>';
			}
			wp_reset_postdata();
		}

	}
}
