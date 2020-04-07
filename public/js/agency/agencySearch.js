$(document).ready(function(event) {
	if (typeof Plex === 'undefined') Plex = {};

	Plex.agencySearch = {
	}

	Plex.agencySearch.search = function(search_type, search_string) {
		var results_container = $('.search-results-container');

		if (!search_type || !search_string) return;

		results_container.html('<div class="spin-loader"></div>');

		$.ajax({
			url: '/agency-search/' + search_type + '/' + search_string,
			type: 'GET',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		}).done(function(response) {
			if (response == 'fail') {
				results_container.html('');
			} else {
				Plex.agencySearch.buildSearchResultsUI(response);
			}
		}).fail(function(response) {
			results_container.html('');
		})
	}

	// Iniitial load
	
	Plex.agencySearch.search('all', 'all',);
	
	/**/

	$(document).on('click', '.profile-actions .message-agent-btn', function() {
		var slug = $(this).data('slug');

		window.open(slug, '_blank');
	});

	$(document).on('click', '.agency-search-bar .dropdown-content li', function(event) {
		var search_type = $(this).closest('.dropdown.tab').data('tab'),
			search_string = $(this).attr('value');

			Plex.agencySearch.search(search_type, search_string);
	});

	$(document).on('click', '.profile-actions .view-agent-btn, .profile-info .profile-agency-name, .search-result .profile-image', function(event) {
		window.location.href = $(this).data('slug');
	});

	Plex.agencySearch.buildSearchResultsUI = function(agents) {
		var container = $('.search-results-container');

		container.html('');

		agents.forEach(function(agent) {
			var services = agent.services.length > 0 ? agent.services.join(', ') : 'Contact agent for services',
				stars = '';

			for (var i = 1; i <= 5; i++) {
				if (i <= Math.ceil(agent.review_avg))
					stars += "<div class='star-icon active'></div>";
				else 
					stars += "<div class='star-icon'></div>";
			}

			container.append(
				"<div class='search-result mt10' data-agency_id='" + agent.agency_id + "'>" +
					"<div class='profile-image' data-slug='" + agent.profile_slug + "'>" +
						"<img src='" + agent.logo_url + "'>" +
					"</div>" +

					"<div class='profile-info'>" + 
						"<div class='row profile-header'>" +
							"<h5 class='profile-agency-name' data-slug='" + agent.profile_slug + "'><span data-tooltip aria-haspopup='true' title='" + agent.agent_full_name + "'>" + agent.agent_name + "</span></h5>" +
							"<div class='star-rating'>" +
								stars +
							"</div>" +
							"<small><span class='num-of-reviews'>" + agent.review_count + "</span> Reviews</small>" +
						"</div>" +
						"<div class='row location'>" +
							agent.location +
						"</div>" +
						"<br>" +
						"<div class='row services-header'><b>Services Offered:</b></div>" +
						"<div class='row services'>" +
							services +
						"</div>" +
					"</div>" +

					"<div class='profile-actions'>" +
						"<div class='message-agent-btn' data-slug='" + agent.message_slug + "'>Message</div>" +
						"<div class='view-agent-btn mt10' data-slug='" + agent.profile_slug + "'>View Profile</div>" +
					"</div>" +
				"</div>"
			);
		});

		$(document).foundation('tooltip', 'reflow');
	}

});