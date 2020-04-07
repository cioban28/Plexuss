$(document).ready(function() {
	Plex.agencyDashboard = {
		copiedToClipboardSuccess : {
	        textColor: '#fff',
	        backGroundColor : 'green',
	        msg: 'Succesfully copied to clipboard',
	        type: 'soft',
	        dur : 1000,
    	},
		showLoader: function() {
			$('.manage-students-ajax-loader').show();
		},
		hideLoader: function() {
			$('.manage-students-ajax-loader').hide();
		},
	};

	$(document).on('click', '.unique-link-btn', function(event) {
		$('#unique-link-modal').foundation('reveal', 'open');
	});

	$(document).on('click', '.agency-manage-students', function(event){
		bucket = $(this).data('bucket');

		window.location.href = '/agency/inquiries/' + bucket;
	});

	$(document).on('click', '.toggle-monthly-overall .stats-btn:not(.active)', function(event) {
		var type = $(this).data('type');

		// Validate type. Only two choices allowed.
		if (type != 'month' && type != 'overall') { return; }

		var param = type == 'month' ? '' : 'true';

		$('.toggle-monthly-overall .stats-btn').removeClass('active');

		$(this).addClass('active');

		Plex.agencyDashboard.getDashboardReportingOne(param);
	});

	$(document).on('click', '.send-important-msg-btn', function(event) {
		$('#urgent-msg-modal').foundation('reveal', 'open');
	});

	$(document).on('click', '#urgent-msg-modal .submit-urgent-msg', function(event) {
		event.preventDefault();
		var data = { msg: $('#urgent-msg-modal textarea').val() };

		if ($('#urgent-msg-modal textarea').val() == '') {
			$('#urgent-msg-modal textarea').focus();
		} else {
			Plex.agencyDashboard.showLoader();

			$.ajax({
				url:  '/agency/ajax/sendAdminUrgentMatterMsg',
				type: 'POST',
				data: data,
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},

			}).done(function(response){
				Plex.agencyDashboard.hideLoader();
				$('#urgent-msg-modal').foundation('reveal', 'close');
				$('#urgent-msg-modal textarea').val('');

			});
		}
	});

	Plex.agencyDashboard.formatMemberSinceDate = function(moment) {
		console.log($('#agency-member-since').data('date'))
		var element = $('#agency-member-since'),
			date = element.data('date'),
			moment = moment(date),
			day = moment.format('D'),
			year = moment.format('YYYY'),
			month = moment.format('MMM'),

			formattedDate = month + ' ' + day + ', ' + year;

		element.html(formattedDate);
	}

	Plex.agencyDashboard.getDashboardReportingOne = function(param) {
		var parent = $('.statistics-wrapper.application-stats'),
			applications = parent.find('.apps-status.applications .apps-status-number'),
			accepted = parent.find('.apps-status.accepted .apps-status-number'),
			rejected = parent.find('.apps-status.rejected .apps-status-number'),
			enrolled = parent.find('.apps-status.enrolled .apps-status-number');

		accepted.hide().html('<div class="medium loader"></div>').fadeIn(400);
		rejected.hide().html('<div class="medium loader"></div>').fadeIn(400);
		enrolled.hide().html('<div class="medium loader"></div>').fadeIn(400);
		applications.hide().html('<div class="medium loader"></div>').fadeIn(400);

		$.ajax({
			url: '/agency/ajax/getDashboardReportingOne/' + param,
			type: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		}).done(function(response) {
			response = JSON.parse(response);
			applications.html(response.applications).fadeIn(400);
			accepted.html(response.opportunities).fadeIn(400);
			rejected.html(response.removed).fadeIn(400);
			enrolled.html(response.enrolled).fadeIn(400);
		});
	}

	Plex.agencyDashboard.getDashboardReportingTwo = function() {
		var table = $('.statistics-wrapper.monthly-stats table'),
			year_element = table.find('.table-header .year');

			year_element.hide().html('<div class="small loader"></div>').fadeIn(400);

		$.ajax({
			url: '/agency/ajax/getDashboardReportingTwo/',
			type: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		}).done(function(data) {
			data = JSON.parse(data);

			year = data.year;
			delete data.year;

			year_element.hide().html(year).fadeIn(400);;

			for (var key in data) {
				// Extra data that is not needed
				if (key.includes('pacing')) continue;

				// ORDER MATTERS here
				// Month | Completed | Pacing | Accepted | Rejected | Enrolled
				var tableRow = 
					'<tr>' +
						'<td>' + key + '</td>' +
						'<td>' + data[key].applications + '</td>' +
						'<td>' + data[key].pacing + '</td>' +
						'<td>' + data[key].opportunities + '</td>' +
						'<td>' + data[key].removed + '</td>' +
						'<td>' + data[key].enrolled + '</td>' +
					'</tr>';

				table.append(tableRow);
			}
		});
	}

	Plex.agencyDashboard.getAllBucketTotals = function() {
		var buckets = {
			leads: {
				number: $('.stats-container.leads .new-number'),
				total: $('.stats-container.leads .total-number'),
			},
			opportunities: {
				number: $('.stats-container.opportunities .new-number'),
				total: $('.stats-container.opportunities .total-number'),
			},
			applications: {
				number: $('.stats-container.applications .new-number'),
				total: $('.stats-container.applications .total-number'),
			}
		};

		for (var bucket in buckets) {
			bucket = buckets[bucket];
			bucket.number.hide().html('<div class="medium loader"></div>').fadeIn(400);
			bucket.total.hide().html('Total').fadeIn(400);
		}

		$.ajax({
			url: 'agency/ajax/getDashboardBoxesNumbers',
			type: 'GET',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		}).done(function(response) {
			response = JSON.parse(response);
			for (var bucket in buckets) {
				buckets[bucket].number.hide().html(response[bucket + '_new']).fadeIn(400);
				buckets[bucket].total.hide().html(response[bucket] + ' Total').fadeIn(400);
			}
		});
	}

	Plex.agencyDashboard.getReviews = function() {
		var agency_id = $('#agency_dashboard').data('agency_id');
			reviews_container = $('.user-reviews-container .user-reviews');

		reviews_container.html('<div class="medium loader"></div>');
		reviews_container.addClass('loading');

		$.ajax({
			url: '/agency-profile/getReviews',
			type: 'POST',
			data: { agency_id: agency_id },
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		}).done(function(data) {
			if (Array.isArray(data) && data.length) {
				reviews_container.removeClass('loading');
				reviews_container.html('');
				data.forEach(function(review) {
					reviews_container.append(Plex.agencyDashboard.buildReviewUI(review));
				});
			} else {
				reviews_container.html('No reviews yet.');
			}

		}).fail(function(response) {
			reviews_container.html('No reviews yet.');
		});
	}

	Plex.agencyDashboard.buildReviewUI = function(review) {
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
	
	// Initial load functions
	$('.owl-carousel').owlCarousel({
		items: 1,
		startPosition: 1,
		singleItem: true,
		itemsScaleUp : false,
		slideSpeed: 500,
	});

	$('.statistics-wrapper.monthly-stats').attr("style", "display: block !important");

	new Clipboard('.copy-clipboard-btn');
	
	Plex.agencyDashboard.getDashboardReportingOne('');
	Plex.agencyDashboard.getDashboardReportingTwo();
	Plex.agencyDashboard.getAllBucketTotals();
	Plex.agencyDashboard.getReviews();
	Plex.agencyDashboard.formatMemberSinceDate(moment);
	//
});

