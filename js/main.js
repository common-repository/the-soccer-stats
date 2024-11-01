(function($) {

	$(document).ready(function() {
		$('[data-toggle="tooltip"]').tooltip();

		$('#player-season-by-season').tablesorter();
		$('#player-stats-table').tablesorter();


		$('#filter-options-season').on('change', function() {
			var value = this.value;
			var seasonid = $('#season-id').attr('seasonid');

			if( ! isNaN( value ) ) {
				//$('#player-stats-content').html('<p class="text-center" style="margin: 10rem 0;"><i class="fa fa-spinner fa-spin fa-3x" aria-hidden="true"></i></p>');
				$('#ajax-content').html('<p class="text-center" style="margin: 10rem 0;"><i class="fa fa-spinner fa-spin fa-3x" aria-hidden="true"></i></p>');

				$.ajax({
            url: ajaxurl,
            type: 'POST',
            data: ( {
                action: 'update_seasonal_stats',
                season: seasonid,
                matchtype: value
            }
            ),
            success: function (response) {
                $('#ajax-content').html(response);
								$('#player-stats-table').tablesorter();
            }
        });

			}
		});

		$('#filter-options-opponent').on('change', function() {
			var value = this.value;
			var opponentid = $('#opponent-id').attr('opponentid');

			if( ! isNaN( value ) ) {
				//$('#player-stats-content').html('<p class="text-center" style="margin: 10rem 0;"><i class="fa fa-spinner fa-spin fa-3x" aria-hidden="true"></i></p>');
				$('#ajax-content').html('<p class="text-center" style="margin: 10rem 0;"><i class="fa fa-spinner fa-spin fa-3x" aria-hidden="true"></i></p>');

				$.ajax({
            url: ajaxurl,
            type: 'POST',
            data: ( {
                action: 'update_opponent_stats',
                opponent: opponentid,
                matchtype: value
            }
            ),
            success: function (response) {
                $('#ajax-content').html(response);
            }
        });

			}
		});

		$('#filter-options-season-player').on('change', function() {
			var value = this.value;
			var playerid = $('#player-id').attr('playerid');

			if( ! isNaN( value ) ) {
				//$('#player-stats-content').html('<p class="text-center" style="margin: 10rem 0;"><i class="fa fa-spinner fa-spin fa-3x" aria-hidden="true"></i></p>');
				$('#ajax-content').html('<p class="text-center" style="margin: 10rem 0;"><i class="fa fa-spinner fa-spin fa-3x" aria-hidden="true"></i></p>');

				$.ajax({
            url: ajaxurl,
            type: 'POST',
            data: ( {
                action: 'update_seasonal_stats_player',
                player: playerid,
                matchtype: value
            }
            ),
            success: function (response) {
                $('#ajax-content').html(response);
								$('#player-season-by-season').tablesorter();
            }
        });

			}
		});


	});

})( jQuery );
