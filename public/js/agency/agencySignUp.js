$(document).ready(function() {
	if (typeof Plex === 'undefined') Plex = {};

	Plex.agencySignUps = {
		// step_0: $('.agency-step-signup.step-0'),
		step_1: $('.agency-step-signup.step-1'),
		step_2: $('.agency-step-signup.step-2'),
		step_3: $('.agency-step-signup.step-3'),
		completed_step: $('.agency-steps-complete'),
		showLoader: function() {
			$('.manage-students-ajax-loader').show();
		},
		hideLoader: function() {
			$('.manage-students-ajax-loader').hide();
		},
		verifyPhoneTimeout: null,
	};

	Plex.agencySignUps.previewIMGUpload = function(img_input) {
		if (img_input.files && img_input.files[0]) {
	        var reader = new FileReader();
	        $('#img-preview').fadeOut(0);

	        reader.onload = function (e) {
	            $('#img-preview').attr('src', e.target.result);
	        	$('#img-preview').fadeIn(400);
	        }

	        reader.readAsDataURL(img_input.files[0]);
	    }
	}

	Plex.agencySignUps.validateForm = function(form, submit_clicked) {
		var valid = true,
			required_inputs = form.find('[required]');

		required_inputs.each(function() {
            // // Special case manual 'visual feedback' for profile picture validation
            // if ($(this).prop('name') == 'agency-profile-photo') {
            //     $(this).siblings('label').find('.upload-photo-icon').css('border', '0');
            //     $('.profile-picture-upload-error').fadeOut(250);
            // }

			// Verify no inputs are invalid
			if ($('.error').is(':visible') && !$('.error').closest('.alert.alert-danger').length) {
				if (submit_clicked) { $('.error').siblings('input').focus(); }
				valid = false;
			}

			// Verify all inputs have values
			if ($(this).val() === '' || ( $(this).val() == 'on' && !$(this).is(':checked') )) {
				if (submit_clicked) { $(this).focus(); }
                
                // if ($(this).prop('name') == 'agency-profile-photo') {
                //     $(this).siblings('label').find('.upload-photo-icon').css('border', '2px solid red');
                //     $('.profile-picture-upload-error').fadeIn(250);
                // }

				valid = false;
				return false;
			}
		});

		return valid;
	}

	Plex.agencySignUps.validateHours = function(select_input) {
		var open_select = $('.hours-select.open-hour'),
			close_select = $('.hours-select.close-hour'),
			open = open_select.val(),
			close = close_select.val(),

			moment_open = moment(open, ["h:mm A"]),
			moment_close = moment(close, ["h:mm A"]),

			type = select_input.hasClass('open-hour') ? 'open' : 'close';

		if (moment_open >= moment_close) {
			$('.day.active').data('hours')[type] = 'choose';
			select_input.val('choose');
			return false;
		}

		return true;
	}

	Plex.agencySignUps.validatePhone = function() {
		var full_phone = $('#country-code-select').val() + $('#phone-number').val(),
    		valid = true;

    	$.ajax({
    		url: '/phone/validatePhoneNumber',
    		type: 'POST',
			data: {phone: full_phone},
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    	}).done(function(response) {
    		if (response.error == false) {
				$('.phone-error-msg.error').fadeOut(200);
    			return;
    		}
			$('.phone-error-msg.error').fadeIn(200);
    	}).fail(function() {
			$('.phone-error-msg.error').fadeIn(200);
    	});
	}

	$('#datepicker').datepicker({
        constrainInput: false,
        changeMonth: true,
        changeYear: true,
        maxDate: moment().format('MM/DD/YYYY'),
    });

    $('.agency-step-signup.step-2 #datepicker').val(moment().format('MM/DD/YYYY'));

    $("select[name='country_code'] option").each(function() {
        $(this).attr("data-label", $(this).text());
    });
    $("select[name='country_code']").on("focus", function() {
        $(this).find("option").each(function() {
            $(this).text($(this).attr("data-label"));
        });
    }).on("change mouseleave", function() {
        $(this).focus();
        $(this).find("option:selected").text($(this).val());
        $(this).blur();
    }).on("blur", function() {
        $(this).find("option:selected").text($(this).val());
    }).change();

	// $(document).on('click', '.agency-step-signup.step-0 .continue-to-plexuss-signup', function(event) {
	// 	var signed_in = $(this).data('signed_in');

	// 	Plex.agencySignUps.step_0.fadeOut(0);

	// 	if (signed_in)
	// 		Plex.agencySignUps.step_2.fadeIn(200);
	// 	else 
	// 		Plex.agencySignUps.step_1.fadeIn(200);
 //    });

	$(document).on('click', '.agency-signup-button.step-1', function(event) {
		event.preventDefault();

		var form = $(this).closest('form'),
			valid = Plex.agencySignUps.validateForm(form, true),
			formData = null;

		if (valid) {
			formData = new FormData(form[0]);

			Plex.agencySignUps.showLoader();

			$.ajax({
	            url: '/postAgencySignup',
	            type: 'POST',
	            data: formData,
	            cache: false,
	            contentType: false,
	            processData: false,
	            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	        }).done(function(response){
	        	if (response == 'success') {
	        		Plex.agencySignUps.step_1.fadeOut(0);
					Plex.agencySignUps.step_3.fadeOut(0);

					$('.agency-signup-steps-icon .step-text.active').removeClass('active');
					$('.agency-signup-steps-icon .agency-step-icon.step-1 > .step-checkmark').addClass('active');

					$('.agency-signup-steps-icon .agency-step-icon.step-2 > .sprite').addClass('active');
					$('.agency-signup-steps-icon .agency-step-icon.step-2 > .step-text').addClass('active');

					Plex.agencySignUps.step_2.fadeIn(200);
	        	} else {
	        		$('.agency-step-signup.step-1').html(response);
	        	}
				
				Plex.agencySignUps.hideLoader();
	        });
		}
	});

	$(document).on('click', '.agency-signup-button.step-2', function(event) {
		event.preventDefault();
		var form = $(this).closest('form'),
			valid = Plex.agencySignUps.validateForm(form, true);

		if (valid) {
			Plex.agencySignUps.step_1.fadeOut(0);
			Plex.agencySignUps.step_2.fadeOut(0);

			$('.agency-signup-steps-icon .step-text.active').removeClass('active');
			$('.agency-signup-steps-icon .agency-step-icon.step-2 > .step-checkmark').addClass('active');

			$('.agency-signup-steps-icon .agency-step-icon.step-3 > .sprite').addClass('active');
			$('.agency-signup-steps-icon .agency-step-icon.step-3 > .step-text').addClass('active');
			
			Plex.agencySignUps.step_3.fadeIn(200);
		}
	});

	$(document).on('click', '.agency-signup-button.step-3', function(event) {
		event.preventDefault();

		var form = $(this).closest('form'),
			form_step_2 = $('.agency-step-signup.step-2 form'),
			formData = new FormData(form[0]),
			formData_step_2 = new FormData(form_step_2[0]),
			service_checkboxes = $('.agency-step-signup.step-3 .service-checkboxes :checkbox'),
			services = [],
			days_of_operation = {};


		valid = Plex.agencySignUps.validateForm(form, true);

		if (valid) {
			// Ensure atleast one service checkbox checked
			service_checkboxes.prop('required', true);
			if (service_checkboxes.is(':checked')) {
				service_checkboxes.prop('required', false);				
			} else {
				service_checkboxes.prop('required', false);		
				service_checkboxes.first().focus();
				return;
			}

            Plex.agencySignUps.showLoader();

			// Grab all checked services
			$.each($('.service-checkboxes > .service'), function() {
				if ($(this).find(':checkbox').is(':checked')) {
					var label = $(this).find('label').text().trim();
					services.push(label);
				}
			});

			formData.append('services', JSON.stringify(services));

			// Grab all days of operation
			$.each($('.days-container .day'), function() {
				var day = $(this).data('day'),
					start = $(this).data('hours').open,
					end = $(this).data('hours').close;

				// Set null if closed or only partially filled out
				if (!parseInt(start) || !parseInt(end)) { 
					start = null; end = null;
				}

				days_of_operation[day] = {
					start: start,
					end: end,
				}
			});

			formData.append('days_of_operation', JSON.stringify(days_of_operation));

			for(var pair of formData_step_2.entries()) {
				if (pair[0] == '_token') { continue; } // First form already contains token.
			    formData.append(pair[0], pair[1]);
			}

			formData.delete('new-service-name'); // Do not send new-service-name text field.

			$.ajax({
				url: '/postAgencyApplication',
	            type: 'POST',
	            data: formData,
	            cache: false,
	            contentType: false,
	            processData: false,
	            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			}).done(function(response) {
				if (response == 'success') {
					Plex.agencySignUps.step_1.fadeOut(0);
					Plex.agencySignUps.step_2.fadeOut(0);
					Plex.agencySignUps.step_3.fadeOut(0);

					$('.agency-signup-steps-icon .step-text.active').removeClass('active');
					$('.agency-signup-steps-icon .agency-step-icon.step-3 > .step-checkmark').addClass('active');

					Plex.agencySignUps.completed_step.fadeIn(200);
				} else {
					alert('Failed submitting application, try again later.');
				}

				Plex.agencySignUps.hideLoader();
			});
		}

	});

	// Hours of operation events
	$(document).on('click', '.business-hours-container .days-container .day', function(event) {
		event.preventDefault();

		var open = $(this).data('hours').open,
			close = $(this).data('hours').close,
			closed_checkbox = $('#not-open-check'),
			normal_hours_checkbox = $('#normal-business-hours-check');

		$('.business-hours-container .days-container .day').removeClass('active');
		$(this).addClass('active');

		// Check if closed
		if (open == 'closed' || close == 'closed')
			closed_checkbox.prop('checked', true);
		else
			closed_checkbox.prop('checked', false);

		// Check if normal business hours
		if (open == '9:00 AM' && close == '5:00 PM')
			normal_hours_checkbox.prop('checked', true);
		else
			normal_hours_checkbox.prop('checked', false);

		$('.hours-container .hours-select.open-hour').val(open);
		$('.hours-container .hours-select.close-hour').val(close);
	});

	$(document).on('change', '.hours-container .hours-select', function(event) {
		var type = $(this).hasClass('open-hour') ? 'open' : ( $(this).hasClass('close-hour') ? 'close' : null ),
			closed_checkbox = $('#not-open-check'),
			normal_hours_checkbox = $('#normal-business-hours-check');

		// If type returns null, do not continue.
		if (!type) return;

		closed_checkbox.prop('checked', false);
		normal_hours_checkbox.prop('checked', false);

		$('.day.active').data('hours')[type] = $(this).val();

		if (parseInt($('.day.active').data('hours').open) && parseInt($('.day.active').data('hours').close)) {
			if (Plex.agencySignUps.validateHours($(this))) {
				$('.day.active').addClass('open');
			} else {
				$('.day.active').removeClass('open');
				$('.day.active').removeClass('closed');
			}
		} else {
			$('.day.active').removeClass('open');
			$('.day.active').removeClass('closed');
		}

		if ($(this).val() == 'closed') {
			$('.day.active').data('hours').open = 'closed';
			$('.day.active').data('hours').close = 'closed';

			$('.hours-container .hours-select.open-hour').val('closed');
			$('.hours-container .hours-select.close-hour').val('closed');
			$('.day.active').addClass('closed');
		}
	});

	$(document).on('click', '.checkbox-options-container .checkbox input[type="checkbox"]', function(event) {
		var id = $(this).prop('id'),
			closed_checkbox = $('#not-open-check'),
			normal_hours_checkbox = $('#normal-business-hours-check'),
			other_checkbox = id.includes('normal-business-hours-check') ? closed_checkbox : normal_hours_checkbox;

		if (id.includes('not-open-check')) {
			$('.day.active').addClass('closed');
			$('.day.active').removeClass('open');

			$('.day.active').data('hours').open = 'closed';
			$('.day.active').data('hours').close = 'closed';

			$('.hours-container .hours-select.open-hour').val('closed');
			$('.hours-container .hours-select.close-hour').val('closed');

		} else if (id.includes('normal-business-hours-check')) {
			$('.day.active').removeClass('closed');
			$('.day.active').addClass('open');
			
			$('.day.active').data('hours').open = '9:00 AM';
			$('.day.active').data('hours').close = '5:00 PM';

			$('.hours-container .hours-select.open-hour').val('9:00 AM');
			$('.hours-container .hours-select.close-hour').val('5:00 PM');

		}

		other_checkbox.prop('checked', false);
	});

	$(document).on('change', '#agency-profile-photo', function(event) {
		event.preventDefault();
		if ($(this).val() !== '') {
			$('#agency-profile-photo-checkmark').addClass('active');
			Plex.agencySignUps.previewIMGUpload(this);
		} else {
			$('#agency-profile-photo-checkmark').removeClass('active');
			$('#img-preview').attr('src', '');
		}
	});

	$(document).on('keypress change', '.agency-step-signup form input', function(event) {
		var form = $(this).closest('form'),
			button = form.find('.agency-signup-button'),
			valid = Plex.agencySignUps.validateForm(form);
	});

	$(document).on('keypress', 'form input:not(#new-service-name)', function(e) {
	    return e.which !== 13;
	});

	$(document).on('keypress', '#new-service-name', function (event) {
        if (event.which === 13) {
        	$('.add-agency-service-btn').first().click();
         	return false;
        }
    });

	$(document).on('keypress', '.hours-container .hours-select', function (event) {
        if (event.which === 13) {
        	return false;
        }
    });

	$(document).on('click', '.add-agency-service-btn', function(event) {
		var field = $('#new-service-name'),
			checkbox_container = $(this).siblings('.service-checkboxes'),
			service_name = field.val().trim(),
			parsed_id = null,
			count = 0; 

		if (service_name == '') {
			field.focus();
			return;
		}

		parsed_id = 'agency-service-' + service_name.toLowerCase().split(/\s+/).join('-');

		// Incase of duplicate IDs, we will increment a counter.
		while($('#' + parsed_id).length > 0) {
			count++;
			parsed_id = 'agency-service-' + service_name.toLowerCase().split(/\s+/).join('-') + '-' + count;
		}

		checkbox_container.append(
			"<div class='user-added-service mt10 service'>" +
				"<input id='" + parsed_id + "' type='checkbox' checked />" +
				"<label for='" + parsed_id + "'>" + service_name + "</label>" +
				"<div class='new-service-remove-btn'>Remove</div>" +
			"</div>" );

		field.val('');
	});

	$(document).on('click', '.agency-step-signup.step-3 .new-service-remove-btn', function(event) {
		event.preventDefault();
		var parent = $(this).closest('.user-added-service');
		parent.remove();
	});

	$(document).on('change', '#country-code-select', Plex.agencySignUps.validatePhone);

	$(document).on('change input', '#phone-number', function() {
		clearInterval(Plex.agencySignUps.verifyPhoneTimeout);

		Plex.agencySignUps.verifyPhoneTimeout = setTimeout(function() {
			Plex.agencySignUps.validatePhone();
		}, 500);
	});

	$(document).on('click', '.continue-to-plexuss-btn', function(event) {
		window.location = '/'; 
	});

	// Validation checker
	$(document).foundation({
		abide: {
			patterns: {
				name: /^([a-zA-Z\'\-. ])+$/,
				passwordpattern: /^(?=[^\d_].*?\d)\w(\w|[!@#$%*]){7,20}/,
				date: /^\d{2}\/\d{2}\/\d{4}$/,
			},
			validators: {
	            monthChecker: function(el, required, parent) {
	                var value = $(el).val(),
		                form = $(el).closest('form'),
						button = form.find('.agency-signup-button');

	                if ( ($.isNumeric(value) && value <= 12 && value > 0 )|| value.trim() === '' ) {
	                    $('.datedMonthError').css('display', 'none');
	                    return true;
	                } else{
	                    $('.datedMonthError').css({
	                        'display': 'inline-block',
	                        'margin-bottom': '2px'
	                    });
	                    return false;
	                };
	            },
	            dayChecker: function(el, required, parent) {
	                var value = $(el).val(),
		                form = $(el).closest('form'),
						button = form.find('.agency-signup-button');

	                if ( ($.isNumeric(value) && value <= 31 && value >= 1)  || value.trim() === '' ) {
	                    $('.datedDayError').css('display', 'none');
	                    return true;
	                } else{
	                    $('.datedDayError').css({
	                        'display': 'inline-block',
	                        'margin-bottom': '2px'
	                    });
	                    return false;
	                };
	            },
	            yearChecker: function(el, required, parent) {
	                var value = $(el).val();
	                var currentDate = (new Date).getFullYear();
	                var minAgeAllowed = currentDate - 13;
					var form = $(el).closest('form');
					var button = form.find('.agency-signup-button');

	                if( value.trim() === ''){
	                    $('.datedYearError').css('display', 'none');
	                    $('.datedUnderAge').css('display', 'none');
	                    return true;
	                }
	                else if( !$.isNumeric(value) || value > currentDate || value <=  currentDate - 100 ){
	                    $('.datedYearError').css({
	                        'display': 'inline-block',
	                        'margin-bottom': '2px'
	                    });
	                    return false;
	                } else if ( value > minAgeAllowed) {
	                    $('.datedUnderAge').css({
	                        'display': 'inline-block',
	                        'margin-bottom': '2px'
	                    });

	                    $('.agenotice').css({
	                        'color':'#FF7F00',
	                        'font-weight':'bold'
	                    });
	                    return false;
	                }else{
	                    $('.datedYearError').css('display', 'none');
	                    $('.datedUnderAge').css('display', 'none');
	                    return true;
	                };
	            },
	        }
		}
	});
    
    $(document).foundation('tooltip', 'reflow');
});