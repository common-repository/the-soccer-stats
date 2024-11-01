(function($) {

	$(document).ready(function() {
		$("select#tss-goals, select#tss-redcards, select#tss-yellowcards, select#tss-substitutions-in, select#tss-substitutions-out, select#tss-match-opponent, select#tss-match-matchtype, select#tss_my_team_name_field").select2();

		$('#tss-add-player-season').on('click', function () {
	    $(this).find("img").show();
			var post_id = $('input#post_ID').val(),
			season_id = $('#tss-player-seasons-value').val();
			$.post(ajax_object.ajaxurl, {
				action: 'add_player_to_season',
				nonce: ajax_object.tssNonce,
				playerid: post_id,
				seasonid: season_id

			}, function(data) {
	      $('#tss-add-player-season').find("img").hide();
				$('#player-season-content').html(data);
			});
		});

		$('.tss-add-team-stats').on('click', function() {
			var spinner = $(this).find("img");
			spinner.show();
			var players = $(this).prev().val(), //value of select
			matchid = $(this).attr('matchid'),
			table = $(this).attr('table'),
			current = $(this);

			$.post(ajax_object.ajaxurl, {
				action: 'add_team_stats',
				nonce: ajax_object.tssNonce,
				matchid: matchid,
				players: players,
				table: table
			}, function(data) {
	      spinner.hide();
				current.next(".tss-hidden-content").html(data);
			});

		});

		$(document).on('click', '.tss-delete-player-season', function() {
			$(this).find("img").show();
			var season_id = $(this).attr('seasonid'),
			player_id = $(this).attr('playerid');

			$.post(ajax_object.ajaxurl, {
				action: 'delete_player_from_season',
				nonce: ajax_object.tssNonce,
				playerid: player_id,
				seasonid: season_id

			}, function(data) {
				$('#player-season-content').html(data);
				$('.tss-delete-player-season').find("img").hide();
			});
		});

		$(document).on('click', '.tss-add-substitutions', function() {
			var matchid = $(this).attr('matchid'),
			current = $(this),
			playerin = $('select#tss-substitutions-in').val(),
			playerout = $('select#tss-substitutions-out').val(),
			minute = $('input#tss-substitutions-minute').val();
			current.find("img").show();

			$.post(ajax_object.ajaxurl, {
				action: 'add_substitution',
				nonce: ajax_object.tssNonce,
				playerin: playerin,
				playerout: playerout,
				matchid: matchid,
				minute: minute

			}, function(data) {
				current.next(".tss-hidden-content").html(data);
				current.find("img").hide();
			});
		});

		$(document).on('click', '.tss-add-goal', function() {
			var matchid = $(this).attr('matchid'),
			current = $(this),
			playerid = $('select#tss-goals').val(),
			penalty = $('#tss-goals-penalty'),
			own = $('#tss-goals-own'),
			ownscorer = $('input#tss-goals-own-scorer').val(),
			minute = $('input#tss-goals-minute').val(),
			penalty_v = 0,
			own_v = 0;
			current.find("img").show();

			if(penalty.is(':checked')){
				penalty_v = 1;
			}

			if(own.is(':checked')){
				own_v = 1;
			}

			$.post(ajax_object.ajaxurl, {
				action: 'add_goal',
				nonce: ajax_object.tssNonce,
				playerid: playerid,
				matchid: matchid,
				penalty: penalty_v,
				own: own_v,
				ownscorer: ownscorer,
				minute: minute

			}, function(data) {
				current.next(".tss-hidden-content").html(data);
				current.find("img").hide();
			});
		});

		$(document).on('click', '.tss-add-card', function() {
			var matchid = $(this).attr('matchid'),
			color = $(this).attr('color'),
			current = $(this);

			current.find("img").show();

			if(color == 'yellow') {
				var playerid = $('select#tss-yellowcards').val(),
				minute = $('input#tss-yellowcards-minute').val();
			}
			else {
				var playerid = $('select#tss-redcards').val(),
				minute = $('input#tss-redcards-minute').val();
			}

			$.post(ajax_object.ajaxurl, {
				action: 'add_card',
				nonce: ajax_object.tssNonce,
				playerid: playerid,
				matchid: matchid,
				minute: minute,
				color: color

			}, function(data) {
				current.next(".tss-hidden-content").html(data);
				current.find("img").hide();
			});
		});


		$(document).on('click', '.tss-delete-team-stats', function() {
			var id = $(this).attr('id'),
		 	action = $(this).attr('action'),
			matchid = $(this).attr('matchid'),
			table = $(this).attr('table'),
			spinner = $(this).find("img"),
			current = $(this);
			spinner.show();

			$.post(ajax_object.ajaxurl, {
				action: action,
				nonce: ajax_object.tssNonce,
				id: id,
				matchid: matchid,
				table: table

			}, function(data) {
				current.closest('.tss-hidden-content').html(data);
				spinner.hide();
			});


		});

		$('#tss-rebuild-match-titles').on('click', function() {
			var spinner = $(this).find("img");
			spinner.show();

			$.post(ajax_object.ajaxurl, {
				action: 'rebuild_match_titles',
				nonce: ajax_object.tssNonce
			}, function(data) {
	      spinner.hide();
			});

		});

		$('.show-penalties-content').on('click', function() {
			$('.penalty-goals-content').slideToggle();
		});

	});

})( jQuery );
