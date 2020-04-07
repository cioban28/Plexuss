//Some Default Settigns for Modal.
$(document).ready(function(e) {

	$('#container-box').imagesLoaded(function(){
		$('#container-box').masonry();
	});
		
	$("#owl-news").owlCarousel({
		navigation : true,
		items : 4,
		itemsDesktop : [1199,3],
		itemsDesktopSmall : [979,3]
	});

	/* For First Time Homepage Modal
	 * JS for switching form visibility when a user changes
	 * his or her user type this will need to re-initialize Abide
	 * when forms are changed.
	 */
	$('#user_type').change(function(){
		var user_type = $(this).val();
		
		if ($('.ft_modal_container').is(":visible")) {
			// IF THERE IS A FORM ALREADY VISIBLE
			$('.ft_modal_container:visible').slideUp( 250, 'easeInOutExpo', function(){
				$('#ft_modal_' + user_type).slideDown(250, 'easeInOutExpo', function(){
					userTypeFormReset();
				});
			});
		} else {
			// IF THERE IS NO FORM VISIBLE
			$('#ft_modal_' + user_type).slideDown(250, 'easeInOutExpo', function(){
				userTypeFormReset();
			});
		};
	});

	/* For First Time Homepage Modal
	 * JS for switching form visibility for when a user changes
	 * Country type. Also enables the school_type select box if 
	 * a country other than USA is selected.
	 */
	$('.ft_country').change(function(){
		var user_type = $('#user_type').val();
		var zip_box = $('#' + user_type + '_zipcode_container');
		var zip_field = $('#' + user_type + '_zipcode');
		var school_type = $('#' + user_type + '_school_type');
		var grad_year = $("#" + user_type + "_grad_year")
		var school_name = $('#' + user_type + '_school_name');
		var homeschooled = $('#' + user_type + '_homeschooled');

		// Reset fields regardless of selection
		zip_field.val('');
		school_name.val('');
		school_type.val('');
		grad_year.val('');
		// grad_year.prop('disabled', true);
		// homeschooled.prop({ 'checked' : false, 'disabled': true });
		// school_name.prop('disabled', true);

		// If user is Foreign
		if( $(this).val() != 1 ){
			zip_box.slideUp(250, 'easeInOutExpo');
			// Reset Fields
			// zip_field.removeAttr('required');
			// // school_type.prop('disabled', false);
			// // grad_year.prop('disabled', false);
		}
		// If user from US
		else{
			zip_box.slideDown( 250, 'easeInOutExpo');
			// Reset Fields
			// zip_field.attr('required', true);
			// school_type.prop('disabled', true);
			// grad_year.prop('disabled', true);
		}

	});

	/* For First Time Homepage Modal
	 * Function used to clear and reset the modal form when the user
	 * Changes their user_type from the dropdown.
	 */
	function userTypeFormReset(){
		var user_type = $('#user_type').val();
		//var form = $('#modalSchoolInfoForm');
		var input_rows = $('.ftm_' + user_type);
		
		/* ==================================================
		 * ===================RESET ALL FORMS================
		 * ==================================================
		 */
		/* REMOVE REQUIRED FOR HIDDEN FORMS
		 * Loops through user type options and removes the required attr from
		 * fields that are not part of the current selected user_type field set
		 */
		$('#user_type option').each(function(){
			if( $(this).val() != '' && $(this).val() != user_type ){
				$('.ftm_' + $(this).val()).find('.ft_required').removeAttr('required');
			}
		});
		// SHOW ALL SHOW BY DEFAULT FORMS
		input_rows.show();
		// RESET ALL FORMS
		input_rows.find('.ft_reset[type="text"], select.ft_reset').val('');
		input_rows.find('.ft_reset[type="checkbox"]').prop('checked', false);
		// RESET SPECIAL FORMS
		$('#' + user_type + '_country').val('1');
		// RE-ENABLE VALIDATION
		input_rows.find('.ft_required').attr('required', 'required');
		// DISABLE DISABLED FORMS
		// input_rows.find('.ft_disabled').prop('disabled', true);
		// Reset hidden #school_id form

		// Re-init foundation
		$(document).foundation('reflow');
	}

	/* For the First Time Homepage Modal
	 * Hides the school name box if EITHER homeschooled or international are checked
	 */
	$('.ft_hide_school_name').change(function(){
		var user_type = $('#user_type').val();
		var school_name_container = $('#' + user_type + '_school_name_container');
		var school_name_field = $('#' + user_type + '_school_name');

		if($('.ft_hide_school_name:visible').is(':checked')){
			school_name_container.stop().slideUp(250, 'easeInOutExpo');
			school_name_field.removeAttr('required');
			school_name_field.val('');
		}
		else{
			school_name_container.stop().slideDown(250, 'easeInOutExpo');
			school_name_field.attr('required', 'required');
		}
		//Re-init foundation Abide
		$(document).foundation('reflow');
	});
	function init_custom_fndtn(){
		$(document).foundation({
			reveal: {
				animation: 'fadeAndPop',
				delay: 200,
				animation_speed: 800,
				close_on_background_click: false,
				close_on_esc: false,
				css: {
					open: {
						'opacity': 0,
						'visibility': 'visible',
						'display': 'block'
					},
					close: {
						'opacity': 1,
						'visibility': 'hidden',
						'display': 'none'
					}
				}
			},
			abide: {
				patterns: {
					zip: /^\d{5}(-\d{4})?$/,
					school_name: /^([0-9a-zA-Z\.\(\),\-'"!@#& ])+$/,
					phone: /^1?\-?\(?([0-9]){3}\)?([\.\-])?([0-9]){3}([\.\-])?([0-9]){4}$/,
				}
			}
		});
	}
	init_custom_fndtn();
});


$(function() {
	if(Plex.reqruitUrl) {
        $('#recruitmeModal').foundation('reveal', 'open', Plex.reqruitUrl);
    }
 //    else if( Plex.inviteContacts.getUrlParameter('param') === 'invite' ){
	// 	Plex.inviteContacts.openInviteModal();
	// }
	// else if(Plex.showFirstTimeHomepageModal && Plex.modalAvailable) {
 //        resetForm();
 //        setupFirstTimeHomeModal();
 //        $('#firstTimeMessagemodal').foundation('reveal', 'open');
 //    }

    // if (Plex.showPrepSchoolModal) {
    // 	$('#prepSchoolForm').on('valid', function () {
    // 		$.post('/college-prep', $('#prepSchoolForm').serialize(), function(data) {
    //             $('#prepSchoolModalThanks').foundation('reveal', 'open');
	   //      });
  		// })
    // 	$('#prepSchoolModal').foundation('reveal', 'open');
    // };
});

function setupFirstTimeHomeModal() {
	console.log( "setting up modal!" );
    /*
    THIS CODE BELOW NEEDS A GOOD SCRUBBING.
    */
  //   $('.ft_zipcode').on('valid', function() {
		// var user_type = $('#user_type').val();
  //       $("#" + user_type + "_school_type").prop('disabled', false);
  //       $("#" + user_type + "_grad_year").prop('disabled', false);
  //   });

    $("#zipcode").val("");
    var formSchoolPickedName1 = "";
    var formSchoolPickedName2 = "";
    var formSchoolPickedName3 = "";
    var formSchoolPickedName4 = "";

    $('#zipcode').focusin(function() {
        resetForm();
    });

    //Auto complete functions
	// disables/un-disables school_name form field
    $(".ft_school_type").change(function() {
		var user_type = $('#user_type').val();
        var school_type = $(this).val();
        var zipcode = $('#' + user_type + '_zipcode').val();
		var school_name_field = $("#" + user_type + "_school_name");
		var homeschooled_field = $('#' + user_type + '_homeschooled');
		var homeschooled_container = $('#' + user_type + '_homeschooled_container');

		// Enable school_name if user selects an option
		if(school_type != ''){
			school_name_field.autocomplete("option", "source", "getAutoCompleteData?zipcode=" + zipcode + "&type=" + school_type);
			// school_name_field.prop('disabled', false);
			school_name_field.val("");
			// Hide the homeschool column
			if(school_type == 'college'){
				homeschooled_container.slideUp(250, 'easeInOutExpo', function(){
					homeschooled_field.prop({
						'checked': false
						// 'disabled': true
					});
				});
			}
			// Show the homeschool column
			else{
				homeschooled_container.slideDown(250, 'easeInOutExpo');
				// homeschooled_field.prop('disabled', false);
			}
		}
		// Disable school_name if user doesn't select an option
		else{
			// school_name_field.prop('disabled', true);
			school_name_field.val("");
		}
    });

	// Autocomplete to fetch school name
    $(".ft_school_name").autocomplete({
        source: "getAutoCompleteData",
        minLength: 1,
		change: function(event, ui){
			var user_type = $('#user_type').val();
			var input = $('#' + user_type + '_school_name');
			var autocomp_list = $('#' + user_type + '_school_name_container .ui-autocomplete > li');
			var match = false;
			// Set default val for input's data val if it is not found/set in the DOM
			var data_val = typeof input.data( 'school' ) == 'undefined' ? '' : input.data( 'school' ).toLowerCase();
			var user_val = input.val().toLowerCase();

			// Loop through the autocomplete list to find matches
			autocomp_list.each(function(loop_count){
				var val = $(this).html();
				var li_val = val.toLowerCase();
				var indexOf = li_val.indexOf(user_val);
				/* If a match is found in the autocomplete list but
				 * the values don't match, clear the field
				 * For example, when a user types something quickly
				 * but does not let autocomplete load results, or if
				 * a user is not specific enough: eg. there are 3
				 * piedmont high schools
				 */
				if( indexOf > -1 && data_val != user_val){
					input.val('');
					input.data('school', '');
					$('#school_id').val('');
					match = true;
					return false;
				}
				/* If there's a match between user input and item
				 * selected from the autocomplete list, close the 
				 * country box
				 */
				else if( indexOf > -1 ){
					/* Hide Country Box since we already know the country of the
					 * school that's in our DB, duh!
					 */
					match = true;
				}
			});
			// END .each() LOOP

			/* If the user's input is a school that is not found in autocomplete, (we don't have it)
			 * then clear the #school_id value, and input's data field
			 */
			if( match == false && data_val != user_val ){
				input.data('school', '');
				$('#school_id').val('');
			}
		},
        select: function(event, ui) {
			var user_type = $('#user_type').val();
			var school_name_field = $('#' + user_type + '_school_name');
			var school_id_field = $('#school_id');

			// Set form field values on autocomplete select
			school_name_field.data('school', ui.item.value);
			school_id_field.val(ui.item.id);
        }
    });

    $("#autocomplete2").change(function() {
        if ($(this).val() !== window.formSchoolPickedName2) {
            $("#autocomplete2").val("");
            $('#schoolinterested1').val('');
        }
    });
    $("#autocomplete3").change(function() {
        if ($(this).val() !== window.formSchoolPickedName3) {
            $("#autocomplete3").val("");
            $('#schoolinterested2').val('');
        }
    });
    $("#autocomplete4").change(function() {
        if ($(this).val() !== window.formSchoolPickedName4) {
            $("#autocomplete4").val("");
            $('#schoolinterested3').val('');
        }
    });

    $("#autocomplete2").autocomplete({
        source: "getAutoCompleteData?type=college",
        minLength: 1,
        select: function(event, ui) {
            window.formSchoolPickedName2 = ui.item.value;
            $('#schoolinterested1').val(ui.item.id);
        }
    });

    $("#autocomplete3").autocomplete({
        source: "getAutoCompleteData?type=college",
        minLength: 1,
        select: function(event, ui) {
            window.formSchoolPickedName3 = ui.item.value;
            $('#schoolinterested2').val(ui.item.id);
        }
    });

    $("#autocomplete4").autocomplete({
        source: "getAutoCompleteData?type=college",
        minLength: 1,
        select: function(event, ui) {
            window.formSchoolPickedName4 = ui.item.value;
            $('#schoolinterested3').val(ui.item.id);
        }
    });

    //z-index changes to help auto complete not go under each others form.
    $(".ui-front input").focusin(function() {
        $(this).parent().css('z-index', '800')
    });

    $(".ui-front input").focusout(function() {
        $(this).parent().css('z-index', '100')
    });

    /* skip and submit*/
    $('#modalSchoolInfoForm').on('valid', function() {
		console.log( 'binding first-time-modal form' );

        var token = Plex.ajaxtoken;
        
        $.ajax({
            url: '/ajax/modalForm/schoolInfo/' + token,
            data: $('#modalSchoolInfoForm').serialize(),
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'POST'
        }).done(function() {
        	// console.log('done saving');
	        Plex.inviteContacts.openInviteModal(); //--> uncomment when returning invite feature
	        // $('.start-plexuss-btn').trigger('click'); remove this when returning invite feature
	    });
    });
}

// -- invite modal when start plex button is clicked
$(document).on('click', '.start-plexuss-btn', function(e){
	e.preventDefault();
	var reqruitUrl = Plex.reqruitUrl;

    //Added a url change so when users fill the modal and hit the back button they dont get the modal from cache.
    var state = {};
    history.replaceState({}, 'Plexuss Home', "/home?lightbox=0");
    // window.location.replace('/home');

	if (reqruitUrl) {
        $('#recruitmeModal').foundation('reveal', 'open', reqruitUrl);
    } else if(Plex.redirect){
    	window.location.replace(Plex.redirect);
    } else {
        // $('#firstTimeMessagemodal').foundation('reveal', 'close');
        // window.location.replace('/home');
        // uncomment below, comment the two lines above when returning invite feature
        Plex.inviteContacts.closeInviteModal();
    }
});

function resetForm() {
    $("#user_type").val("");
    $(".ft_zipcode").val("");
    $(".ft_hide_school_name").prop("checked", false);
    $(".ft_school_type").val("");
    $(".ft_school_name").val("");
    $('.ft_grad_year').val('');
    $("#schoolinterested1").val("");
    $("#schoolinterested2").val("");
    $("#schoolinterested3").val("");
    $("#autocomplete2").val("");
    $("#autocomplete3").val("");
    $("#autocomplete4").val("");
}

function setResizeBox() {
    $('#container-box').masonry({
        itemSelector: '.box-div'
    });
}