$(document).ready(function() {
	if (typeof Plex === 'undefined') Plex = {};

	Plex.agencyProfile = {
		reviewPostSuccess: {
	        textColor: '#fff',
	        backGroundColor : 'green',
	        msg: 'Successfully submitted your review. Thank you!',
	        type: 'soft',
	        dur : 5000
	    },

	    reviewPostFail: {
			textColor: '#fff',
	        bkg: '#DD1144',
	        msg: 'An error occured, try again later',
	        type: 'soft',
	        dur: 5000
	    }
	}

	$(document).on('click', '.detailed-profile-container .tab-btns-container .tab-btn', function(event) {
		var selection = $(this).data('tab'),

			reviews_btn = $('.detailed-profile-container .reviews-tab-btn'),
			reviews_tab = $('#reviews-tab'),

			about_btn = $('.detailed-profile-container .about-tab-btn'),
			about_tab = $('#about-tab'),

			current_btn = $('.detailed-profile-container .tab-btn.active'),
			current_tab = $('.detailed-profile-container .tab:not(.hidden)'),

			selected_btn = selection == 'reviews' ? reviews_btn : about_btn,
			selected_tab = selection == 'reviews' ? reviews_tab : about_tab;

		// Same tab selected, do nothing.
		if (current_btn.data('tab') == selection) return;

		current_btn.removeClass('active');
		selected_btn.addClass('active');

		current_tab.addClass('hidden');
		selected_tab.removeClass('hidden');

	});

	$(document).on('click', '.review-tab-span-btn', function(event) {
		var reviews_btn = $('.detailed-profile-container .reviews-tab-btn'),
			reviews_tab = $('#reviews-tab'),
			about_btn = $('.detailed-profile-container .about-tab-btn'),
			about_tab = $('#about-tab');

		reviews_btn.addClass('active');
		reviews_tab.removeClass('hidden');

		about_btn.removeClass('active');
		about_tab.addClass('hidden');
	});

	$(document).on('click', '.submit-review-btn', function(event) {
		event.preventDefault();
		var parent = $(this).closest('.write-review-container'),
			rating = parent.find('.star-review-container').data('current-rating'),
			agency_id =	$('.basic-profile-container .profile-info').data('agency_id'),
			comment = parent.find('textarea'),
			star_container = $(this).closest('.review-form').siblings('.star-review-container');

		// Validate star ratings
		if (star_container.data('current-rating') == 0) {
			Plex.agencyProfile.scaleStarUpAndDown($('.star-review-container .star-icon'));
			return;
		}
		// Validate text field
		if (comment.val() == '') {
			comment.focus();
			return;
		}

		// Missing data, do not continue with ajax call.
		if (!rating || !agency_id || !comment) return;

		$.ajax({
			url: '/agency-profile/addReview',
			type: 'POST',
			data: { 
				agency_id: agency_id, 
				rating: rating, 
				comment: comment.val()
			},
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
		}).done(function(response) {
			if (response == 'success') {
				comment.val('');
				$('.tab-btns-container .reviews-tab-btn').click();
				Plex.agencyProfile.getReviews();
				topAlert(Plex.agencyProfile.reviewPostSuccess);
			} else {
				topAlert(Plex.agencyProfile.reviewPostFail);
			}
		}).fail(function(response) {
			topAlert(Plex.agencyProfile.reviewPostFail);
		})
	});

	$(document).on('click', '.back-to-search-btn', function() {
		window.location.href = '/agency-search';
	});

	$(document).on('click', '.engage-container .message-btn', function() {
		var slug = $(this).data('slug');

		window.open(slug, '_blank');
	});

	// Star rating events
	$(document).on('click', '.star-review-container .star-icon', function(event){
		var star_rating = $(this).data('rating');

		Plex.agencyProfile.scaleStarUpAndDown($(this));

		$('.star-review-container .star-icon').removeClass('active');

		Plex.agencyProfile.setStarRatingUI(star_rating);

		$('.star-review-container').data('current-rating', star_rating);
	});

	$(document).on('mouseenter', '.star-review-container .star-icon', function(event) {
		var star_rating = $(this).data('rating');

		Plex.agencyProfile.setStarRatingUI(star_rating);
	});

	$(document).on('mouseleave', '.star-review-container .star-icon', function(event) {
		var star_rating = $('.star-review-container').data('current-rating');

		$('.star-review-container .star-icon').removeClass('active');

		Plex.agencyProfile.setStarRatingUI(star_rating);
	});

	Plex.agencyProfile.scaleStarUpAndDown = function(stars) {
   		stars.css('transform', 'scale(1.3)');

   		setTimeout(function() {
   		stars.css('transform', 'scale(1.0)');

   		}, 200);

	}

	Plex.agencyProfile.setStarRatingUI = function(star_rating) {
		for (var i = 1; i <= Math.ceil(star_rating); i++) {
			$('.star-review-container .star-icon[data-rating="' + i +'"]').addClass('active');
		}
	}
	// End Star rating events

	Plex.agencyProfile.getReviews = function() {
		var agency_id =	$('.basic-profile-container .profile-info').data('agency_id'),
			bucket_url = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/';
			review_container = $('#reviews-tab .student-reviews-container'),
			reviews = '';

		review_container.html('<div class="spin-loader"></div>');

		$.ajax({
			url: '/agency-profile/getReviews',
			type: 'POST',
			data: { agency_id: agency_id },
			headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
		}).done(function(data) {
			data.forEach(function(review) {
				reviews += Plex.agencyProfile.buildReviewUI(review);
			});

			review_container.html(reviews);
		});
	}

	Plex.agencyProfile.buildReviewUI = function(review) {
		var bucket_url = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/',
			profile_img = bucket_url + ( review.profile_img_url ? review.profile_img_url : 'default.png' ),
			stars = '';

		for (var i = 1; i <= 5; i++) {
			if (i <= Math.ceil(review.rating))
				stars += "<div class='star-icon active'></div>";
			else 
				stars += "<div class='star-icon'></div>";
		}

		return (
			"<div class='student-review mt40'>" + 
				"<div class='profile-picture'>" +
					"<img src='" + profile_img + "'>" +
				"</div>" +
				"<div class='review-content'>" +
					"<div class='basic-info'>" +
						"<div class='name'>" +
							review.name +
						"</div>" +
						"<div class='rating-and-date'>" +
							"<div class='rating'>" +
								stars +
						"</div>" + 
							"<div class='date'>" + review.date + "</div>" +
						"</div>" +
					"</div>" +
					"<div class='location'>" + review.location + "</div>" +
					"<div class='review-text-content mt10'>" +
						review.comment +
					"</div>" +
				"</div>" +						
			"</div>"
		);
	}

	Plex.agencyProfile.getReviews();
});