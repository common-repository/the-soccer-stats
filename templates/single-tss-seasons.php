<?php
/**
 * Season template
 *
 * Template file for showing season.
 *
 * @package The Soccer Stats
 * @since 1.0
 */

get_header();

?>
<div id="season-id" seasonid="<?php echo $post->ID ?>"></div>
<div class="bootstrap-wrapper">
  <div class="<?php echo tss_get_container() ?>">
    <div class="row">
      <div class="col-md-6">
        <h1><?php the_title() ?></h1>
				<?php

				$filter = new tssFilter('filter-options-season');
				$filter->Display();

				?>
      </div>
    </div>

		<div id="ajax-content">
			<?php
			$playerstatstable = new tssPlayerStatsTable( $post->ID, 0 );
			$playerstatstable->Display();

			$seasonlist = new tssSeasonMatchList($post->ID, 'all');
			$seasonlist->Display();
			?>
		</div>

  </div>
</div>

<?php
get_footer();
?>
