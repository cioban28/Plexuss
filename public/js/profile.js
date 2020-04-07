if (!Plex.profile) Plex.profile = {};

//Some Default Settigns for Modal.
var months = {
    Jan:1,
    Feb:2,
	Mar:3,
    Apr:4,
	May:5,
    Jun:6,
	Jul:7,
    Aug:8,
	Sep:9,
    Oct:10,
	Nov:11,
    Dec:12
};

var majorsList = new MajorCrumbList;



    function dynamicActivePage( currentPage, activeTab ){

        var activeMenuItem = '.mobile_nav_tab_' + activeTab;
        var profileTab = $('.mobile_nav_tab_profile');
        var accomplishmentsTab = $('.mobile_nav_tab_accomplishments');
        var accomplishmentsList = ['experience', 'skills', 'interests', 'clubOrgs', 'honorsAwards', 'languages', 'certifications', 'patents', 'publications'];
        //find all the list items that can be active and remove the active class
        var allMobileNavListItems = $('.topLevelListMenu').find('li a:not(.mobileMenu_companyInfoMenuItem a, .mobileMenu_signInOutMenuItem a, li.title.back.js-generated)').removeClass('active_mobile_nav_tab');

        //dynamically updating the mobile nav current page header
        $('.mobile-menu-current_page_indicator').html(currentPage);
        //add the active class to the correct page
        $(activeMenuItem).addClass('active_mobile_nav_tab');
        
        if( currentPage == 'profile'){
            $(profileTab).addClass('active_mobile_nav_tab');
            
            for (var i = 0; i <= accomplishmentsList.length; i++) {
                if( accomplishmentsList[i] == activeTab ){
                    $(accomplishmentsTab).addClass('active_mobile_nav_tab');
                }
            };
        }
    }

    /********************************************
    *   handler for adding majors to the objective 
    *   section of User Profile
    *
    *********************************************/
    function addMajors(e, majorsList){


        //cannot add more than four
        if(majorsList.length() === 4){
            return;

        }

        var el = $(e.target);

        var major = '';
        //get the text in the box
        if(el.hasClass('major-name')){
             major = el.text();   
        }else if(el.hasClass('major-plus')){
          
             major = el.parent().find('.major-name').text();
        }else{
             major = el.find('.major-name').text();
        }

        //if empty still, just return
        if(major === ''){
            return;
        }

         //if crumb exist already, return
        if( majorsList.findCrumb(major) != -1){

            //display message
            $('#duplicate_crumb_error').css('display','inline');

            $(document).one('click', '#objMajor', function(){
                $('#duplicate_crumb_error').hide();
            });

            return;
        }

        //inject major into text string as 'crumb' with remove button
        var crumb = new MajorCrumb(major);
     
        majorsList.addCrumb(crumb);

        //if list is = 4 now -- let users know max has been reached
        if(majorsList.length() === 4){
            $('#max-note').css('display', 'block');

        }

        $('#majors_crumb_list').append(crumb.getCrumb());

    };


    //MajorCrumbList
    /**********************************************
    ***********************************************
    *   class majorCrumbList is a list containing 
    *   objects of type MajorCrumb
    *
    ***********************************************/
    function MajorCrumbList(){
        this.crumbs = [];
    }
    MajorCrumbList.prototype.length = function(){
        return this.crumbs.length;
    }; 
    MajorCrumbList.prototype.popCrumb = function(){
        return this.crumbs.pop();
    };
    MajorCrumbList.prototype.addTextCrumb = function(crumbText){
        //check if unique
        var crumb = new MajorCrumb(crumbText.trim());
        return this.crumbs.push(crumb);
    };
    MajorCrumbList.prototype.addCrumb = function(crumb){
        //check if unique
        return this.crumbs.push(crumb);
    };
    MajorCrumbList.prototype.removeCrumb = function(crumbText){
        
        //if found -- remove and return new list-- else return null
        
        var i = this.findCrumb(crumbText);
        
        if(i != -1)
            this.crumbs.splice(i, 1);
        else
            return null;

        return this.crumbs;
       
    };
    MajorCrumbList.prototype.findCrumb = function(crumbText){
      
        for(var i in this.crumbs){
            if(this.crumbs[i].major === crumbText.trim()){   
                return i;
            }
        }
        return -1;
    };



    //MajorCrumb
    /**********************************************
    ***********************************************
    *   MajorCrumb object is a crumb with major information
    *
    ***********************************************/
    function MajorCrumb(pmajor){
        this.major = pmajor.trim();
    }
    MajorCrumb.prototype.getCrumb = function(){
         return '<li id="'+ this.major +'" class="major-crumb">' +
                 '<span class="crumb-name">' +
                    this.major+' '+
                 '</span><span class="obj-close-btn">&times;</span></li>'; 
    }



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
			// school_type.prop('disabled', false);
			// grad_year.prop('disabled', false);
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
		var form = $('#modalSchoolInfoForm');
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
		init_custom_fndtn();
	}

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
					school_name: /^([0-9a-zA-Z\.\(\),\-'"!@#& ])+$/
				}
			}
		});
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
		init_custom_fndtn();
	});

$(document).foundation({
    reveal: {
        animation: 'fadeAndPop',
        delay: 100,
        animation_speed: 300,
        close_on_background_click: true,
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
    }
});


// $(function() {
//     if (Plex.profile_page_lock_modal && Plex.modalAvailable) {
//         // resetUnlockModalForm();
//         // setupProfilePageUnlockForm();
//         // $('#profileUnlockmodal').foundation('reveal', 'open');
//         loadProfileInfo(DefaultSection);
//     } else {
//         loadProfileInfo(DefaultSection);
//     }

// 	// Commented for now... getNotifications already called in the footer
//     //getNotifications();

// });

/***********************************************************************
 *===================== REMOVES DIVS FROM THE PAGE =====================
 ***********************************************************************
 * This function added due to foundation 5.5's new behavior with modals. The problem
 * is that we're getting multiple form IDs because modals used to be contained
 * within the divs they were coded in. Now, foundation seems to put them at the bottom
 * of the page, so when we do a .html(), the modal does not get removed. This function
 * does that 'garbage collection.'
 */
function cleanupAjaxModals(){
	$('.remove_before_ajax').remove();
}
//Handles all Ajax form loading for now. needs lots of work.
//
//Load the profile info of the Id supplied.
function loadProfileInfo(id, from_redirect) {
    var redirected_from = (typeof from_redirect !== 'undefined') ? from_redirect : null;

	cleanupAjaxModals();
    clearSelectedFromleftnav();
    $('.profilePanel').hide();

    $.ajax({
        url: '/ajax/profile/' + id + '/' + Plex.ajaxtoken,
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).always(function(data) {

        //I dont like this very much.
        $('#' + id).html(data);
		$('#' + id).data('boxMode', 'ready');
		$('#' + id).slideDown( 500, 'easeInOutExpo' );
        if( id === 'personalInfo' || id === 'objective' || id === 'financialinfo' || id === 'scores' || id === 'uploadcenter' || id === 'highschoolInfo' || id === 'collegeInfo'){
            $('.side_nav li').find('.' + id).parent().addClass('selected');
        }else{
            $('.accomplishments-nav').find('.' + id).addClass('active');
        }

        //if redirected from Financial Info tab, then scroll window down to Financial Info section of uploadcenter
        if( id === 'uploadcenter' && redirected_from === 'from-financial-tab' ){
            //scroll down to financial tab
            scrollToFinancialDocs();
        }


        dynamicActivePage('profile', id);
        // $(document).foundation('abide', 'reflow');

    });
}

//Switches the element submitted from view to edit mode.
function switchProfileBoxToEdit(editClicked, editWindowNum) {
    var mainWrapper = $(editClicked).parents('.profilePanel');
    mainWrapper.children('.viewmode').hide(500);
    mainWrapper.children('.editmode' + editWindowNum).show(500);
}

function clearSelectedFromleftnav() {
    $('.side_nav li').removeClass('selected');
    $('.accomplishments-nav li').removeClass('active');
}

function setupProfilePageUnlockForm() {
    /*
    THIS CODE BELOW NEEDS A GOOD SCRUBBING.
    */
    // $('.ft_zipcode').on('valid', function() {
    //     var user_type = $('#user_type').val();
    //     $("#" + user_type + "_school_type").prop('disabled', false);
    //     $("#" + user_type + "_grad_year").prop('disabled', false);
    // });

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
    
    $('#modalSchoolInfoForm').on('valid.fndtn.abide', function() {
        var token = Plex.ajaxtoken;
        var reqruitUrl = Plex.reqruitUrl;
        //Added a url change so when users fill the modal and hit the back button they dont get the modal from cache.
        var state = {};
        history.replaceState(state, 'Plexuss Home', "/home?lightbox=0");
        $.ajax({
            url: '/ajax/modalForm/schoolInfo/' + token,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: $('#modalSchoolInfoForm').serialize(),
            type: 'POST'
        }).done(function() {
            if (reqruitUrl) {
                $('#recruitmeModal').foundation('reveal', 'open', reqruitUrl);
            } else if(Plex.redirect){
                window.location.replace(Plex.redirect);
            } else {
                $('#profileUnlockmodal').foundation('reveal', 'close');
                loadProfileInfo('personalInfo');
            };
        });
    })

}

/* Checks to see if current workplace checkbox is checked. If it is, remove the
 * required and data-invalid attributes, hide the un-needed month_to and
 * year_to fields, and hide the errors. Finally, re-initialize foundation
 * with the custom validators.
 * *This function is used both on click of checkbox, AND on population of modal
 * @param		checkbox		jquery obj			checkbox we're checking to see if is checked
 * @param		end_month		jquery obj			ending month dropdown
 * @param		end_year		jquery obj			ending year dropdown
 * @param		validatorName	string				name of the abide validator these use
 * @param		end_date_box	jquery obj			.row that contains the two input items
 */
function toggle_current_workplace(checkbox, end_month, end_year, validatorName, end_date_box){
	end_month_error = end_month.next();
	end_year_error = end_year.next();

	if( checkbox.prop('checked') ) {
		// remove required etc attributes
		end_month.removeAttr("required data-invalid");
		end_year.removeAttr("required data-invalid data-abide-validator");
		// hide errors
		end_month_error.slideUp(250, function(){ end_month_error.remove(); });
		end_year_error.slideUp(250, function(){ end_year_error.remove(); });
		// hide input boxes
		end_date_box.slideUp( 250, 'easeInOutExpo' );
		// re-init foundation
		init_foundation_custom();
	}
	else {
		// Show input boxes
		end_date_box.slideDown( 250, 'easeInOutExpo', function(){
			// add required attributes
			end_month.attr("required","required");
			end_year.attr({
				'required': 'required',
				'data-abide-validator': validatorName
			});
		})
		// add error messages
		end_month.after("<small class='error'>Please select a month</small>");
		end_year.after("<small class='error'>Please enter a year</small>");
		// re-init foundation
		init_foundation_custom();
	}
}

function resetUnlockModalForm() {
    $("#schoolType").val("");
    // $("#schoolType").val("").attr('disabled', 'disabled');
    // $("#autocomplete1").val("").attr('disabled', 'disabled');
    $('#schoolPicked').val('');
    $('#gradyear').val('');
}


/* Personal Info Modal controls */
function personalInfoEdit(e) {
    $('#personalInfoEdit').foundation('reveal', 'open');
	$(document).on('opened.fndtn.reveal', '[data-reveal]', function () {
		school_group_fndtn();
		init_new_school_autocomp();
	});
}

/* Function for loading Image Changer Modal */
function changeProfilePic(e) {
    $('#changeProfilePic').foundation('reveal', 'open');
    bind_remove_profile_picture();
    bind_profile_picture_submit();
}

/* Function for editing Objective Content */
function editObjectiveContent(e) {
    $('#editObjectiveContent').foundation('reveal', 'open');

    //should initialize crumb list with crumbs from server -- 
    //lucky for us -- list already exist in span with id majors_list_objective 
    //add those crumbs to major list in objective's modal's view

    var items = $('#majors_list_objective').children('.major-item');

    $('#majors_crumb_list').html('');
    for(var i = 0;  i < items.length; i++){
     
        var crumb = new MajorCrumb(items[i].innerHTML.trim());
        majorsList.addCrumb(crumb);             //add to MajorsList -- used to save to server

        $('#majors_crumb_list').append(crumb.getCrumb());  //add to view
    }
    
    //show max majors reached feedback
    if(majorsList.length() === 4)
        $('#max-note').css('display', 'block');

    /* bind event handlers here because ajaxed in*/
    $(document).on('click', '#objMajorContainer .majors-list-select .major-listing-cont', function(e){ addMajors(e, majorsList); });
    $(document).on('click', '#objMajor', function(){
        $(this).val('');
        showMajorsList();
    }); 
    $(document).on('keyup', '#objMajor', function(e){
        showMajorsList();
        getMajors(e);
    });
    $(document).on('click', '.obj-close-btn', function(e){
        
        //remove from crumb list' -- should return new list or null if not able to remove/value not found
        if(majorsList.removeCrumb( e.target.parentNode.getElementsByClassName('crumb-name')[0].innerHTML ) != null){
            
            //if removed from list successfully, remove rendered crumb from view
            var crumb = e.target.parentNode;
            crumb.remove();
        }

        //user cannot add more than 4 majors -- but hide if removing fourth
        //if(majorsList.length() < 4){
            $('#max-note').hide();
         
        

    });
   
        

}

function showMajorsList(){
    var listCon = $("#objMajorContainer").find('.majors-list-select');
    listCon.show();

    $(document).one('click', function(e){
            if(!$(e.target).hasClass('.majors-list-select') && !$(e.target).hasClass('#objMajor'))
                listCon.hide();
        });
}

/************************************************************************
*   opens the dropdown for some user feedback and starts to 
*   get majors via ajax
*************************************************************************/
function getMajors(e){

        var listCon = $("#objMajorContainer").find('.majors-list-select');
        var  plus, major;
        var pop = listCon.find('.popular');
        var other = listCon.find('.other');
        
        //show loading feedback if loading
        if($('#objMajor').val() === ''){
            pop.html('');
            other.html('');
        }else{
            pop.html('&nbsp;&nbsp;&nbsp;Loading...');
            other.html('&nbsp;&nbsp;&nbsp;Loading...');
        }

        ///// ajax to get majors
        $.ajax({
            url : '/ajax/profile/objective/searchFor/major',
            data: {input: $('#objMajor').val()},
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).done(function(data){
            //console.log(data);

            var listing = '';

            //empty list to repopulate
            pop.html('');
            other.html('');

            if( data.popular_majors.length === 0){
                 listing = $('<div>', {'class': 'major-listing-cont'});
                 listing.text('No results.');
            }
            else{

                listing = '';
                for(var i in data.popular_majors){
                    listing +=  '<div class="major-listing-cont">' + 
                                '<span class="major-plus"> + </span>' +
                                '<span class="major-name">' + data.popular_majors[i].name + '</span></div>'; 
                }
            }
            pop.html(listing);

            if( data.other_majors.length === 0){
                 listing = $('<div>', {'class': 'major-listing-cont'});
                 listing.text('No results.');
            }else{
                
                listing = '';
                for(var i in data.other_majors){
                     listing += '<div class="major-listing-cont">' + 
                                '<span class="major-plus"> + </span>' +
                                '<span class="major-name">' + data.other_majors[i].name + '</span></div>'; 
                }
            }
            other.html(listing);

        });
}

/* Function for adding new Publications */
function addNewPublications(e) {
	removeBullets();
	$('#PublicationsInfoForm').trigger('reset');
	$('#publicationId').removeAttr('value');
    $('#addNewPublications').foundation('reveal', 'open');
}

/* Function for adding new Patents */
function addNewPatents(e) {
	removeBullets();
	$('#PatentsInfoForm').trigger('reset');
	$('#patentId').removeAttr('value');
    $('#addNewPatents').foundation('reveal', 'open');
}

/* Function for adding new Certificates */
function addNewCertificates(e) {
	removeBullets();
	$('#CertificatesInfoForm').trigger('reset');
	$('#certiId').removeAttr('value');
    $('#addNewCertificates').foundation('reveal', 'open');
}

/* Function to open experience form in modal window*/
function AddEditExpForm() {
	$('#ExperienceInfoForm').trigger('reset');
	$('#expId').removeAttr('value');
	removeBullets();
    $('#AddEditExpForm').foundation('reveal', 'open');
}
/* Function to open skill form in modal window*/
function AddEditSkillForm() {
    $('#AddEditSkillForm').foundation('reveal', 'open');
}
/* Function to open interests form in modal window*/
function AddEditInterestForm() {
    $('#AddEditInterestForm').foundation('reveal', 'open');
}
/* Function to open club and orgs form in modal window*/
function AddEditClubOrgForm() {
	removeBullets();
	$('#ClubOrgInfoForm').trigger('reset');
	$('#clubId').removeAttr('value');
    $('#AddEditClubOrgForm').foundation('reveal', 'open');
}
/* Function to open honors and awards form in modal window*/
function AddEditHonorAwardForm() {
	removeBullets();
	$('#HonorAwardInfoForm').trigger('reset');
	$('#honorId').removeAttr('value');
    $('#AddEditHonorAwardForm').foundation('reveal', 'open');
}
/* Function to open languages form in modal window*/
function AddEditLanguageForm() {
    $('#AddEditLanguageForm').foundation('reveal', 'open');
}
/* Function to open College Score form in modal window*/
function AddEditCollegeScoreForm() {
    $('#AddEditCollegeScore').foundation('reveal', 'open');
}


/* High school Modal controls */
function hsEditSchool(e) {
    var data = $(e).data('hsInfo');
    var box = $('#hsEditSchool');
    //fill in hidden elements.
    box.find("input[name='postType']").val('editSchool');
    box.find("input[name='originalSchoolId']").val(data.school_id);
    box.find("#hsSchoolPickedId").val(data.school_id);


    box.find('.deleteWarning').hide();
	box.find('#hs_info_change_new_school_row').hide();
	box.find('.edit_school_merge_warning').hide();
	box.find('#hs_info_change_hs_attended').val(data.school_id);

    if (data['latest']) {
        $('#hsInfoSchoolCurrent').prop('checked', true);
    } else {
        $('#hsInfoSchoolCurrent').prop('checked', false);
    }

    //add data to the remove school button so it can be checked if allowed.
    var allowremove = false;
    if (!data.courseCount) {
        allowremove = true;
    } else {
    }
    box.find('.btn-remove-school').data('RemoveAllowed', allowremove);

    box.foundation('reveal', 'open');
}

function hsAddNewCourse(e) {
    //reset the new course form incase user messed with it before.
    var box = $('#hsEditCourse');
    //box.find("input[name='postType']").val('newandEditCourse');
    box.find("input[name='courseId']").val('');

    //need to get the current school abd school id and set the form to it incase user changed it while looking at courses.
    //This is located in the data-default-hs of this form.
    var data = box.data('defaultHs');
    if (0 == data.length) {
        data.label = 'Search for your school';
        data.id = '';
    };

    //reset the button and hide the textform.
    box.find('#hsCourseSchoolAutoCompeteLabel').html(data.label).show();
    //box.find('#hsCourseSchoolAutoCompeteText').hide();
    //box.find('#hsCourseSchoolAutoCompete').val(data.label);
	box.find('#hsCourseSchoolAutoCompete').attr('placeholder',data.label);
    box.find('#ChangeSchoolEv').show();

    //reset the hidden form values.
    box.find('#hsSchoolId').val(data.id);
    box.find('#hsclassLevel').val('1');
    box.find('#hsclassGrade').val('');
    box.find('#hsclassGrade').val('');
    box.find('#hsclassGradeSub').val('');

	// set school dropdown to user's default school
	box.find('#hs_info_hs_attended').val(data.id);

    //Set the drop dowm menus with proper data for Classes.
    //Class subject and name are not worked out yet or in our DB.
    box.find("[name='hsInfoSubject']").val('');
    box.find("[name='hsInfoClassName']").val('');

    //reset the class level highlighted button.
    //btn-toggler-selected or btn-toggler
    box.find('.gradeoptsmall').removeClass('btn-toggler-selected').addClass('btn-toggler');
    box.find('#hslevelOpt1').removeClass('btn-toggler').addClass('btn-toggler-selected');

    //Set the Units drop down
    box.find("[name='hsInfoUnits']").val('0');

    //Set the Course Level and Semester.
    box.find("[name='hsInfoEducationlevel']").val('');
    box.find("[name='hsInfoSemster']").val('');

	// Hide the new custom class text input
	var new_custom_class = box.find("#hs_info_new_class_row");
		new_custom_class.val('');
		new_custom_class.hide();

    //reset the grade select area.
    box.find('.gradeGroup').hide();
    box.find('#hsGradeRow').find('.btn-toggler-selected').removeClass('btn-toggler-selected').addClass('btn-toggler');
    box.find('#hsGradeRow').show();
    box.find('#hsSubOpt1 .btn-toggler1-selected').removeClass('btn-toggler1-selected').addClass('btn-toggler1');
    box.find('[data-grade="fail"]').attr('src', '../images/fail.png');
    box.find('[data-grade="pass"]').attr('src', '../images/pass.png');
    $('#slider-range-max').slider({
        value: 0
    });
    $('#slider-range-max').find('.ui-slider-handle').html('0');

    //Set the button to Add since it might of been changed to edit in a past call.
    box.find('.editCourseButtons').hide();
    box.find('.newCourseButtons').show();

	box.foundation('reveal', 'open');
}

function hsEditCourse(e) {
    var data = $(e).data('hsInfo');
    var box = $('#hsEditCourse');	
    //Set the hidden values first and reset the form if user hit the change school button in a past cycle.
    //box.find("input[name='postType']").val('newandEditCourse');
    box.find('#hs_info_hs_attended').val(data.school_id);
	box.find('#hs_info_new_school_row').hide();
    box.find('#hsclassLevel').val(data.class_level);
    box.find('#hsclassGrade').val(data.course_grade_type);
    box.find('#hsclassGradeSub').val(data.course_grade);
    box.find("input[name='courseId']").val(data.id);

    //Set the School name for this course.
    box.find('#ChangeSchoolEv').show();


    //Set the drop dowm menus with proper data for Classes.
    //Class subject and name are not worked out yet or in our DB.	
	getClasses( '#hsInfoSubject', '#hsInfoClassName', data.class_type, data.class_name );
    box.find("[name='hsInfoSubject']").val(data.class_type);
    box.find("[name='hsInfoClassName']").val(data.class_name);


    //Set the Units drop down
    box.find("[name='hsInfoUnits']").val(data.units);

    //Set the Course Level and Semester.
    box.find("[name='hsInfoEducationlevel']").val(data.school_year);
    box.find("[name='hsInfoSemster']").val(data.semester);

    //set the class level highlighted button.
    //btn-toggler-selected or btn-toggler
    box.find('.gradeoptsmall').removeClass('btn-toggler-selected').addClass('btn-toggler');
    switch (data.class_level) {
        case '1':
            box.find('#hslevelOpt1').addClass('btn-toggler-selected');
            break;
        case '2':
            box.find('#hslevelOpt2').addClass('btn-toggler-selected');
            break;
        default:
            box.find('#hslevelOpt3').addClass('btn-toggler-selected');
    }

    //Switch the grades mode and select the grade that submitted.
    //show the SUB item group and item selected. highligjt W or IN istead if those were picked.
    switch (data.course_grade_type) {
        case 'A-F':
            box.find('.gradeGroup').hide();
            box.find('.changeGrade').show();
            box.find('#hsSubOpt1').show();
            box.find('.btn-toggler1-selected').removeClass('btn-toggler1-selected').addClass('btn-toggler1');
            box.find('[data-grade="' + data.course_grade + '"]').removeClass('btn-toggler1').addClass('btn-toggler1-selected');
            break;
        case 'P/F':
            box.find('.gradeGroup').hide();
            if (data.course_grade == 'Pass') {
                box.find('[data-grade="pass"]').attr('src', '../images/pass_hover.png');
                box.find('[data-grade="fail"]').attr('src', '../images/fail.png');
            } else {
                box.find('[data-grade="pass"]').attr('src', '../images/pass.png');
                box.find('[data-grade="fail"]').attr('src', '../images/fail_hover.png');
            }

            box.find('#hsSubOpt2').show();
            break;
        case '0-100':
            box.find('.gradeGroup').hide();
            $('#slider-range-max').slider({
                value: data.course_grade
            });
            $('#slider-range-max').find('.ui-slider-handle').html(data.course_grade);
            box.find('#hsSubOpt3').show();
            box.find('.changeGrade').show();
            break;
        case 'W':
            box.find('.gradeGroup').hide();
            box.find('.changeGrade').hide();
            box.find('#hsGradeRow').show();
            SetGradeOpt(4, 'W');
            break;
        case 'In':
            box.find('.gradeGroup').hide();
            box.find('.changeGrade').hide();
            box.find('#hsGradeRow').show();
            SetGradeOpt(5, 'In');
            break;
        default:
    }

    //Set the button to Edit since it might of been changed to add in a past call.
    box.find('.editCourseButtons').show();
    box.find('.newCourseButtons').hide();

    box.foundation('reveal', 'open');
}

/***********************************************************************
 *============================= AUTOCOMPLETE ===========================
 *============================== ADD COURSE ============================
 ***********************************************************************
 * Autocomplete for the add/edit course modal
 * This autocomplete only shows when a user selects the
 * 'search for your school...' option in the 'schools_attended'
 * dropdown above it
 * @param		string			text_input				the id of the text input that will
 * 														have autocomplete applied to it
 * @param		string			school_id_input			the id of the hidden school id element
 * 														this takes the form of: '#element_id'
 * @param		string			school_type				url parameter: either 'highschool'
 * 														or 'college'
 * @param		boolean			unverified				switches the showing of unverified schools
 * 														(only the user's) as results
 * 														in the autocomplete on or off
 */
function make_school_autocomp( text_input,  school_id_input, school_type , unverified ){
	unverified = unverified ? '1' : '0';
	$( text_input ).autocomplete({
		source:"getAutoCompleteData?zipcode=" + '95376' + "&type=" + school_type + "&unverified=" + unverified,
		minLength: 1,
		change: function(event, ui){
			var input = $(this);
			var autocomp_list = $(text_input + '_container .ui-autocomplete > li');
			var match = false;
			// Set default val for input's data val if it is not found/set in the DOM
			var data_val = typeof input.data( 'hsname' ) == 'undefined' ? '' : input.data( 'hsname' ).toLowerCase();
			var user_val = input.val().toLowerCase();

			// Loop through the autocomplete list to find matches
			autocomp_list.each(function(){
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
					input.data('hsname', '');
					$( school_id_input ).val('');
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
				input.data('hsname', '');
				$( school_id_input ).val('');
			}
		},
		select: function(event, ui) {
			$(this).data('hsname', ui.item.label)
			$( school_id_input ).val(ui.item.id);
		}
	});
}

/***********************************************************************
 *===============SHOW/HIDE NEW_SCHOOL DROPDOWN SECTION==================
 * =========================== EDIT SCHOOL =============================
 ***********************************************************************
 * These blocks show/hide the new_school autocomplete when the user 
 * selects the 'search for another school' option 
 * @param			string			trigger_input			the id of the select style input. When its
 * 															value is 'new' the toggled row is shown,
 * 															otherwise, the toggled row is hidden
 * @param			string			toggled_row				the id of the row which is to be toggled hidden
 * 															or shown. This row contains both the label
 * 															and the input element (which has autocomplete).
 * @param			string			autocomp				the id of the autocomplete element. This is used by
 * 															fndtn function to find the correct element to
 * 															add/remove validation on
 * @param			string			profile_section			the name of the profile page's custom foundation
 * 															function
 */
function init_autocomp_toggle( trigger_input, toggled_row, autocomp, profile_section ){
	$( trigger_input ).change(function(){
		selection = $(this).val();
		original_school_id = $('#originalSchoolId').val();

		if( selection != original_school_id ){
			$('.edit_school_merge_warning').slideDown( 250, 'easeInOutExpo' );
		}
		else{
			$('.edit_school_merge_warning').slideUp( 250, 'easeInOutExpo' );
		}

		if( selection == 'new' ){
			$( toggled_row ).slideDown( 250, 'easeInOutExpo', function(){
				new_school_fndtn( autocomp, profile_section );
			});
		}
		else{
			$( toggled_row ).slideUp( 250, 'easeInOutExpo', function(){
				new_school_fndtn( autocomp, profile_section );
			});
		}
	});
}
/***********************************************************************/

/***********************************************************************
 *====================== ADD/REMOVE REQUIRED ATTR ======================
 *============================ EDIT SCHOOL ==============================
 ***********************************************************************
 * Add/remove the required attribute to/from school autocomplete element
 * depending on whether the element is visible
 * !!! This function should only be called by init_autocomp_toggle !!!
 * @param		string			autocomp			the id of the input which has autocomplete applied to it
 * @param		string			profile_section		the name of the profile page's custom foundation function
 */
function new_school_fndtn( autocomp, profile_section ){
	hidden = $(autocomp + ':hidden');
	visible = $(autocomp + ':visible');

	hidden.removeAttr('required');
	hidden.removeAttr('data-invalid');
	/* We can't do hidden.val('') because this resets default values
	 * as this function is called when the modal is shown
	 */
	$( autocomp ).val('');

	visible.attr('required', 'required');

	switch( profile_section ){
		case 'high_school_info':
			init_hsi_fndtn();
			break;
		case 'college_info':
			init_college_info_fndtn();
			break;
	}
}
/***********************************************************************/

/***********************************************************************
 *=================== SHOW/HIDE ADD COURSE TEXT BOX ====================
 ***********************************************************************
 * Toggles visibility of the add custom course text box in the add/edit course modal
 */
function init_add_course_toggle( select_input, text_input_row, text_input, profile_section ){
	//hide row on page load
	if( $( select_input ).val() != 'new' ){
		$( text_input_row ).hide();
	}
	// bind change event
	$( select_input ).change(function(){
		var selection = $(this).val();
		if( selection == 'new' ){
			$( text_input_row ).slideDown( 250, 'easeInOutExpo', function(){
				new_school_fndtn( text_input, profile_section );
			} );
		}
		else{
			$( text_input_row ).slideUp( 250, 'easeInOutExpo', function(){
				new_school_fndtn( text_input, profile_section );
			} );
		}
	});
}
/***********************************************************************/

/***********************************************************************
 *========================== INIT SUBJECT AJAX =========================
 ***********************************************************************
 * Binds a .change event to a passed-in element. The event triggers an ajax call
 * which gets a list of classes based on the subject the user has selected
 * @param		string		subject_string		the id of the subject dropdown to bind
 * @param		string		class_string		the id of the class string to append to
 */
function init_subject_ajax( subject_string, class_string ){
	$( subject_string ).change(function(){
		var subject_id = $(this).val();
		getClasses( subject_string, class_string, subject_id, 0 );
	});
}
/***********************************************************************/

/***********************************************************************
 *===================== AJAX CALL TO FETCH CLASSES =====================
 ***********************************************************************
 * Gets a list of classes based on the option selected in the subject dropdown
 * Does not take a subject id, but a class id. Sending a class id sets that as
 * the selected value.
 */
function getClasses( subject_string, class_string, subject_id, class_id ){
	var subject_dd = $( subject_string );
	var class_dd = $( class_string );

	// If user selects 'Select an option'
	if( subject_id == '' ){
		class_dd.val('');
		return false;
	}

	$.ajax({
		url: '/ajax/profile/DropDownData',
		data: {
			Type: 'classes',
			SubjectId: subject_id,
			ClassId: class_id,
			newClass: 1
		},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		success: function( data ){
			class_dd.html(data);
		},
		cache: false
	});
}
/***********************************************************************/

/***********************************************************************
 *=================== BIND PROFILE PICTURE REMOVE ======================
 ***********************************************************************
 * Bind click event to remove profile picture button
 */
function bind_remove_profile_picture( token ){
    $('#remove-picture').off();

	$( document ).on('click', '#remove-picture', function(){
		$.ajax({
			url: '/ajax/profile/personalInfoPhoto/' + token,
			type: 'POST',
			data: { remove: 1 },
			dataType: 'json',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success: function( data ){
				$( '#changeProfilePic' ).foundation( 'reveal', 'close' );
				// Reload page content via ajax
				loadProfileInfo( 'personalInfo' );
				// Show top Alert
				topAlert({
					img: data.img,
					bkg: data.bkg,
					textColor: data.textColor,
					type: 'soft',
					dur: data.dur,
					msg: data.msg
				});
			}
		});
	} );
}

/***********************************************************************
 *==================== BIND PROFILE PICTURE UPLOAD =====================
 ***********************************************************************
 * On valid submit of profile image form, we submit via ajax
 */
function bind_profile_picture_submit( token ){
	$( document ).on( 'valid.fndtn.abide', '#uploadProfilePictureForm', function( event ){
		$.ajax({
			url: '/ajax/profile/personalInfoPhoto/' + token,
			data: new FormData( this ),
			method: 'POST',
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success: function( data ){
				$( '#changeProfilePic' ).foundation( 'reveal', 'close' );
				// Reload page content via ajax
				loadProfileInfo( 'personalInfo' );
				// Show top Alert
				topAlert({
					img: data.img,
					bkg: data.bkg,
					textColor: data.textColor,
					type: 'soft',
					dur: data.dur,
					msg: data.msg
				});
			}
		});
	} );

}
/***********************************************************************
 *==================== UPLOAD TRANSCRIPT CLICK BIND ===================
 ***********************************************************************
 * Binds a .click event to the 'add a transcript' button. This is needed because
 * the foundation reveal modal appears at the top of the page, out of view of the
 * user. My solution is to bind the click event, then run a scrolltop and open
 * the modal manually without a callback.
 * @param		string		element_name		id of the element that we're binding
 * @param		string		modal_name			id of the modal to be revealed
 */
function bind_transcript_reveal( element_name, modal_name ){
	$( element_name ).click(function(){
		$( modal_name ).foundation('reveal', 'open');
	});
}
/***********************************************************************/

/***********************************************************************
 *================== BIND TRANSCRIPT SUBMIT EVENT ======================
 ***********************************************************************
 * Binds a .submit event for when a transcript is submitted. Submits file and other
 * form parameters. On success, closes the modal, re-loads current profile page
 * via AJAX, and displays a success topAlert message.
 * @param		string		element_name		id of the element we're binding
 * @param		string		modal_name			id of the modal to be closed
 * @param		string		school_type			transcript type we're binding
 * @param		string		token				ajax token
 */
function bind_transcript_submit( element_name, modal_name, school_type, token ){
	// used for URL and as a param for resetting profile page via AJAX
    // console.log('in transcript submit');
	var school_info = school_type == 'college' ? 'collegeInfo' : 'highschoolInfo';

	$( element_name ).on('valid.fndtn.abide', function( event ) {
        // console.log('about to send ajax transcript submit');
		$.ajax({
			url: '/ajax/profile/' + school_info + '/' + token,
			data: new FormData( this ),
			method: 'POST',
			cache: false,
			contentType: false,
			processData: false,
			dataType: 'json',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success: function( data ){
                // console.log(data);
				$( modal_name ).foundation('reveal', 'close');
				// Get page content via ajax
				loadProfileInfo( school_info );
				// Show top Alert
				topAlert({
					img: '/images/topAlert/checkmark.png',
					backGroundColor: '#a0db39',
					textColor: '#fff',
					type: 'soft',
					dur: '3500',
					msg: data.msg
				});
			}
		});
	});
}
/***********************************************************************/

/***********************************************************************
 *=============== BIND TRANSCRIPT SHOW CONFIRM CLICK EVENT =============
 ***********************************************************************
 * Bind a click event to the 'X' button for remove transcripts
 * @param		string		close_button_class			the class that uses the close-x button image
 * @param		string		confirm_row					The partial id of the row to be shown
 */
function bind_transcript_show_confirm( close_button_class, confirm_row ){
	$( close_button_class ).click(function(){
		var id = $(this).data('id');
		$( confirm_row + id ).slideDown( 250, 'easeInOutExpo' );
	});
}
/***********************************************************************/

/***********************************************************************
 *=============== BIND TRANSCRIPT HIDE CONFIRM CLICK EVENT =============
 ***********************************************************************
 * Bind a click event to the 'X' button for remove transcripts
 * @param		string		close_button_class			the class that uses the close-x button image
 * @param		string		confirm_row					The partial id of the row to be shown
 */
function bind_transcript_hide_confirm( cancel_button_class, confirm_row ){
	$( cancel_button_class ).click(function(){
		var id = $(this).data('id');
		$( confirm_row + id ).slideUp( 250, 'easeInOutExpo' );
	});
}
/***********************************************************************/

/***********************************************************************
 *================= BIND TRANSCRIPT DELETE CLICK EVENT =================
 ***********************************************************************
 * Bind a click event to the confirm/delete button to fire an AJAX call
 * to delete the specified transcript.
 * @param		string		confirm_button_class		the name of the delete button's class
 * @param		string		transcript_row				the prefix of the transcript's row's 
 * 														id. This is suffixed by the transcript's
 * 														db ID
 * @param		string		school_type					college/highschool
 * @param		string		token						ajax token
 */
function bind_transcript_delete( confirm_button_class, transcript_row, school_type, token ){
	$( confirm_button_class ).click(function(){
		var id = $(this).data('id');
		var school_info = school_type == 'college' ? 'collegeInfo' : 'highschoolInfo';
		// make AJAX call with settigns
		$.ajax({
			method: 'POST',
			url: '/ajax/profile/' + school_info + '/' + token,
			data: {
				TransId: id,
				postType: 'transcriptremove'
			},
			dataType: 'json',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success: function( data ){
				// Hide, then remove the transcript's corresponding row
				$( transcript_row + id ).slideUp( 250, 'easeInOutExpo', function(){
					$(this).remove();
				} );
				// send topAlert
				topAlert({
					img: '/images/topAlert/checkmark.png',
					backGroundColor: '#a0db39',
					textColor: '#fff',
					type: 'soft',
					dur: '3500',
					msg: data.msg
				});
			}
		});
	});
}
/*
	$.post("ajax/profile/highschoolInfo/{{$data['ajaxtoken']}}", { TransId:TransId,postType:'transcriptremove'})
	.done(function( data ) {
		topAlert({
			img: '/images/topAlert/checkmark.png',
			backGroundColor: '#a0db39',
			textColor: '#fff',
			type: 'soft',
			dur: '4000',
			msg: 'Transcript Removed!'
		});
	});
*/
/***********************************************************************/

function hsremoveSchool(e) {
    var button = $(e);
    var removeAllowed = button.data('RemoveAllowed');
    //check if user can remove with data attr.
    if (removeAllowed) {
        //Set the postType of this form to delete school mode.
        $('#highschoolInfoForm').find("input[name='postType']").val('deleteSchool');
        var input = $('#highschoolInfoForm').serialize();

        $.ajax({
            url: '/ajax/profile/highschoolInfo/' + Plex.ajaxtoken,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: input,
            type: 'POST'
        }).done(function(data, textStatus, xhr) {
            $('#hsEditSchool').foundation('reveal', 'close');
            loadProfileInfo("highschoolInfo");
        });

    } else {
        $('#highschoolInfoForm').find('.deleteWarning').slideDown( 250, 'easeInOutExpo' );
    }
}

function hsPostSchool(){
	$("#highschoolInfoForm").on("valid",function(){
		var input = $('#highschoolInfoForm').serialize();
		$.ajax({
            url: '/ajax/profile/highschoolInfo/' + Plex.ajaxtoken,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: input,
            type: 'POST'
        }).done(function(data, textStatus, xhr) {
            $('#hsEditSchool').foundation('reveal', 'close');
            loadProfileInfo("highschoolInfo");
        });
	});
	return false;
}

function hsPostCourse() {
	$("#highschoolInfoCourseForm").on("valid",function() {
		var input = $('#highschoolInfoCourseForm').serialize();
		var counter = 0;
        $.ajax({
            url: '/ajax/profile/highschoolInfo/' + Plex.ajaxtoken,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: input,
            type: 'POST'
        }).done(function(data, textStatus, xhr) {
            $('#hsEditCourse').foundation('reveal', 'close');
            loadProfileInfo("highschoolInfo");
            getNotifications();
        });
	});
	return false;
}

function hsremoveCourse() {

    $('#highschoolInfoCourseForm').find("input[name='postType']").val('deleteCourse');
    var input = $('#highschoolInfoCourseForm').serialize();
    $.ajax({
        url: '/ajax/profile/highschoolInfo/' + Plex.ajaxtoken,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: input,
        type: 'POST'
    }).done(function(data, textStatus, xhr) {
        $('#hsEditCourse').foundation('reveal', 'close');
        loadProfileInfo("highschoolInfo");
    });
}

/* College Modal controls */
function collegeEditSchool(e) {
    var data = $(e).data('collegeInfo');
    var box = $('#collegeEditSchool');
    //fill in hidden elements.
    box.find("input[name='postType']").val('editCollege');
    box.find("input[name='originalCollegeId']").val(data.school_id);
    box.find("#CollegePickedId").val(data.school_id);

	// Return modal to default state, hide items
    box.find('.deleteWarning').hide();
	box.find('#col_info_change_new_school_row').hide();
	box.find('.edit_school_merge_warning').hide();
	box.find('#col_info_change_col_attended').val(data.school_id);

    if (data['latest']) {
        $('#collegeInfoSchoolCurrent').prop('checked', true);
    } else {
        $('#collegeInfoSchoolCurrent').prop('checked', false);
    }

    //add data to the remove school button so it can be checked if allowed.
    var allowremove = false;
    if (!data.courseCount) {
        allowremove = true;
    } else {
    }
    box.find('.btn-remove-school').data('RemoveAllowed', allowremove);

    box.foundation('reveal', 'open');
}

function collegeAddNewCourse(e) {
    //reset the new course form incase user messed with it before.
    var box = $('#collegeEditCourse');
    //box.find("input[name='postType']").val('newandEditCourse');
    box.find("input[name='courseId']").val('');

    //need to get the current school abd school id and set the form to it incase user changed it while looking at courses.
    //This is located in the data-default-college of this form.
    var data = box.data('defaultCollege');
    if (0 == data.length) {
        data.label = 'Search for your school';
        data.id = '';
    };

    //reset the button and hide the textform.
    box.find('#collegeCourseSchoolAutoCompeteLabel').html(data.label).show();
    //box.find('#collegeCourseSchoolAutoCompeteText').hide();
    //box.find('#collegeCourseSchoolAutoCompete').val(data.label);
	box.find('#collegeCourseSchoolAutoCompete').attr('placeholder',data.label);
    box.find('#ChangeCollegeEv').show();

    //reset the hidden form values.
    box.find('#collegeSchoolId').val(data.id);
    box.find('#classLevel').val('1');
    box.find('#classGrade').val('');
    box.find('#classGrade').val('');
    box.find('#classGradeSub').val('');

	// set school dropdown to user's default school
	box.find('#col_info_col_attended').val(data.id);

    //Set the drop dowm menus with proper data for Classes.
    //Class subject and name are not worked out yet or in our DB.
    box.find("[name='collegeInfoSubject']").val('');
    box.find("[name='collegeInfoClassName']").val('');

    //reset the class level highlighted button.
    //btn-toggler-selected or btn-toggler
    box.find('.gradeoptsmall').removeClass('btn-toggler-selected').addClass('btn-toggler');
    box.find('#levelOpt1').removeClass('btn-toggler').addClass('btn-toggler-selected');

    //Set the Units drop down
    box.find("[name='collegeInfoUnits']").val('0');

    //Set the Course Level and Semester.
    box.find("[name='collegeInfoEducationlevel']").val('');
    box.find("[name='collegeInfoSemster']").val('');

	// Hide the new custom class text input
	var new_custom_class = box.find("#col_info_new_class_row");
		new_custom_class.val('');
		new_custom_class.hide();

    //reset the grade select area.
    box.find('.gradeGroup').hide();
    box.find('#GradeRow').find('.btn-toggler-selected').removeClass('btn-toggler-selected').addClass('btn-toggler');
    box.find('#GradeRow').show();
    box.find('#SubOpt1 .btn-toggler1-selected').removeClass('btn-toggler1-selected').addClass('btn-toggler1');
    box.find('[data-grade="fail"]').attr('src', '../images/fail.png');
    box.find('[data-grade="pass"]').attr('src', '../images/pass.png');
    $('#slider-range-max').slider({
        value: 0
    });
    $('#slider-range-max').find('.ui-slider-handle').html('0');

    //Set the button to Add since it might of been changed to edit in a past call.
    box.find('.editCourseButtons').hide();
    box.find('.newCourseButtons').show();

    box.foundation('reveal', 'open');
}

function collegeEditCourse(e) {
    var data = $(e).data('collegeInfo');
    var box = $('#collegeEditCourse');

    //Set the hidden values first and reset the form if user hit the change school button in a past cycle.
    //box.find("input[name='postType']").val('newandEditCourse');
    box.find('#col_info_col_attended').val(data.school_id);
	box.find('#col_info_new_school_row').hide();
    box.find('#classLevel').val(data.class_level);
    box.find('#classGrade').val(data.course_grade_type);
    box.find('#classGradeSub').val(data.course_grade);	
    box.find("input[name='courseId']").val(data.id);
    //Set the School name for this course.
    box.find('#ChangeCollegeEv').show();


    //Set the drop dowm menus with proper data for Classes.
    //Class subject and name are not worked out yet or in our DB.
	getClasses( '#collegeInfoSubject', '#collegeInfoClassName', data.class_type, data.class_name );
    box.find("[name='collegeInfoSubject']").val(data.class_type);
    box.find("[name='collegeInfoClassName']").val(data.class_name);


    //Set the Units drop down
    box.find("[name='collegeInfoUnits']").val(data.units);

    //Set the Course Level and Semester.
    box.find("[name='collegeInfoEducationlevel']").val(data.school_year);
    box.find("[name='collegeInfoSemster']").val(data.semester);

    //set the class level highlighted button.
    //btn-toggler-selected or btn-toggler
    box.find('.gradeoptsmall').removeClass('btn-toggler-selected').addClass('btn-toggler');
    switch (data.class_level) {
        case '1':
            box.find('#levelOpt1').addClass('btn-toggler-selected');
            break;
        case '2':
            box.find('#levelOpt2').addClass('btn-toggler-selected');
            break;
        default:
            box.find('#levelOpt3').addClass('btn-toggler-selected');
    }

    //Switch the grades mode and select the grade that submitted.
    //show the SUB item group and item selected. highligjt W or IN istead if those were picked.
    switch (data.course_grade_type) {
        case 'A-F':
            box.find('.gradeGroup').hide();
            box.find('.changeGrade').show();
            box.find('#SubOpt1').show();
            box.find('.btn-toggler1-selected').removeClass('btn-toggler1-selected').addClass('btn-toggler1');
            box.find('[data-grade="' + data.course_grade + '"]').removeClass('btn-toggler1').addClass('btn-toggler1-selected');
            break;
        case 'P/F':
            box.find('.gradeGroup').hide();
            if (data.course_grade == 'Pass') {
                box.find('[data-grade="pass"]').attr('src', '../images/pass_hover.png');
                box.find('[data-grade="fail"]').attr('src', '../images/fail.png');
            } else {
                box.find('[data-grade="pass"]').attr('src', '../images/pass.png');
                box.find('[data-grade="fail"]').attr('src', '../images/fail_hover.png');
            }

            box.find('#SubOpt2').show();
            break;
        case '0-100':
			// console.log('triggered 0-100 grade etype in switch!');
            box.find('.gradeGroup').hide();
            $('#slider-range-max-col').slider({
                value: data.course_grade
            });
            $('#slider-range-max-col').find('.ui-slider-handle').html(data.course_grade);
            box.find('#SubOpt3').show();
            box.find('.changeGrade').show();
            break;
        case 'W':
            box.find('.gradeGroup').hide();
            box.find('.changeGrade').hide();
            box.find('#GradeRow').show();
            CollegeSetGradeOpt(4, 'W');
            break;
        case 'In':
            box.find('.gradeGroup').hide();
            box.find('.changeGrade').hide();
            box.find('#GradeRow').show();
            CollegeSetGradeOpt(5, 'In');
            break;
        default:
    }

    //Set the button to Edit since it might of been changed to add in a past call.
    box.find('.editCourseButtons').show();
    box.find('.newCourseButtons').hide();

    box.foundation('reveal', 'open');
}

function collegeremoveSchool(e) {
    var button = $(e);
    var removeAllowed = button.data('RemoveAllowed');
    //check if user can remove with data attr.
    if (removeAllowed) {
        //Set the postType of this form to delete school mode.
        $('#collegeInfoForm').find("input[name='postType']").val('deleteSchool');
        var input = $('#collegeInfoForm').serialize();

        $.ajax({
            url: '/ajax/profile/collegeInfo/' + Plex.ajaxtoken,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: input,
            type: 'POST'
        }).done(function(data, textStatus, xhr) {
            $('#collegeEditSchool').foundation('reveal', 'close');
            loadProfileInfo("collegeInfo");
        });

    } else {
        $('#collegeInfoForm').find('.deleteWarning').slideDown( 250, 'easeInOutExpo' );
    }
}

function collegePostSchool() {
	$("#collegeInfoForm").on("valid",function()
	{
		var input = $('#collegeInfoForm').serialize();
		$.ajax({
            url: '/ajax/profile/collegeInfo/' + Plex.ajaxtoken,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: input,
            type: 'POST'
        }).done(function(data, textStatus, xhr) {
            $('#collegeEditSchool').foundation('reveal', 'close');
            // Remove modal and loadProfileInfo after modal is closed
            loadProfileInfo("collegeInfo");
        });

	});
	return false;
}

function collegePostCourse()
{
	$("#collegeInfoCourseForm").on("valid",function()
	{
		var input = $('#collegeInfoCourseForm').serialize();
		$.ajax({
            url: '/ajax/profile/collegeInfo/' + Plex.ajaxtoken,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: input,
            type: 'POST'
        }).done(function(data, textStatus, xhr) {
            $('#collegeEditCourse').foundation('reveal', 'close');
            loadProfileInfo("collegeInfo");
            getNotifications();
        });

	});
	return false;
}

function collegeremoveCourse() {

    $('#collegeInfoCourseForm').find("input[name='postType']").val('deleteCourse');
    var input = $('#collegeInfoCourseForm').serialize();
    $.ajax({
        url: '/ajax/profile/collegeInfo/' + Plex.ajaxtoken,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: input,
        type: 'POST'
    }).done(function(data, textStatus, xhr) {
        $('#collegeEditCourse').foundation('reveal', 'close');
        loadProfileInfo("collegeInfo");
        getNotifications();
    });
}
/* College Modal controls */

function PostPersonalInfo()
{
	$("#personalInfoForm").on("valid",function(){
		var input = $('#personalInfoForm').serialize();
		$.ajax({
            url: '/ajax/profile/personalInfo/' + Plex.ajaxtoken,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: input,
            type: 'POST'
        }).done(function(data, textStatus, xhr) {
            $('#personalInfoEdit').foundation('reveal', 'close');
            loadProfileInfo("personalInfo");
            getNotifications();
        });
	});    
	return false;
}

/*function PostProfilePhoto() {   
	var options = { 
			target:   '',   // target element(s) to be updated with server response 
			beforeSubmit:  '',  // pre-submit callback 
			success:       '',  // post-submit callback 
			resetForm: true        // reset the form after successful submit 
		}; 
		
	 $('#uploadPhotoForm').submit(function() { 
			$(this).ajaxSubmit(options);  			
			// always return false to prevent standard browser submit and page navigation 
			return false; 
		}); 
}*/

function PostObjectiveInfo() {
	$("#objectiveForm").on("valid",function()
	{
		
        var $this = $(this);

        var input  ={
            _token : $this.find('input[name="_token"]').val(),
            ajaxtoken :  $this.find('input[name="ajaxtoken"]').val(),
            whocansee: $this.find('input[name="whocansee"]').val(),
            objDegree: $this.find('select[name="objDegree"]').val(),
            objProfession: $this.find('input[name="objProfession"]').val(),
            objPersonalObj: $this.find('#ObjPersonalObj').val()

        };
       

        var majorArray = [];    //for array of majors as strings
        var length = majorsList.crumbs.length;
        
        //if no majors chosen -- error shown
        if(length === 0){
            $('.majors-error').show();                      //show error message
            $('#objMajor').attr('data-invalid', '');
            //bind handler to hide message -- fires only once
            $('input#objMajor').one('click',function(){
                 $('.majors-error').hide();             //hide error  
                 $$('#objMajor').removeAttr('data-invalid');
            });
            return false;
        }

        /** get array of majors from array of major crumbs ***/
        for(var i =0; i < length; i++){
            majorArray.push(majorsList.crumbs[i].major);
        }
       
        //contains serialized form and majors
        var data = {
             formInput: input,
             majors: majorArray
        }

		$.ajax({
            url: '/ajax/profile/objective/' + Plex.ajaxtoken,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: data,
            type: 'POST'
        }).done(function(data, textStatus, xhr) {
            $('#editObjectiveContent').foundation('reveal', 'close');
            loadProfileInfo("objective");
            getNotifications();
        });

	});
	return false;
}

function PostExperienceInfo()
{
	$("#ExperienceInfoForm").on("valid",function()
	{
		var input = $('#ExperienceInfoForm').serialize();
        $.ajax({
            url: '/ajax/profile/experience/' + Plex.ajaxtoken,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: input,
            type: 'POST'
        }).done(function(data, textStatus, xhr) {
            $('#AddEditExpForm').foundation('reveal', 'close');
            hideRemoveButton();
            loadProfileInfo("experience");
            getNotifications();
        });
   });
   return false;
}

function PostSkillInfo()
{
	$("#SkillInfoForm").on("valid",function()
	{
		var input = $('#SkillInfoForm').serialize();
		$.ajax({
            url: '/ajax/profile/skills/' + Plex.ajaxtoken,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: input,
            type: 'POST'
        }).done(function(data, textStatus, xhr) {
            $('#AddEditSkillForm').foundation('reveal', 'close');
            loadProfileInfo("skills");
            getNotifications();
        });
	});
	return false;
}

function PostInterestInfo()
{
	$("#InterestInfoForm").on("valid",function()
	{
		var input = $('#InterestInfoForm').serialize();
		$.ajax({
            url: '/ajax/profile/interests/' + Plex.ajaxtoken,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: input,
            type: 'POST'
        }).done(function(data, textStatus, xhr) {
            $('#AddEditInterestForm').foundation('reveal', 'close');
            loadProfileInfo("interests");
            getNotifications();
        });
	});
	return false;
}

function PostLanguageInfo()
{
	/*$("#LanguageInfoForm").on("invalid",function()
	{
		alert('abc');
		return false;
	});	*/	
	$("#LanguageInfoForm").on("valid",function()
	{
		//alert('hello');
		var input = $('#LanguageInfoForm').serialize();
		$.ajax({
            url: '/ajax/profile/languages/' + Plex.ajaxtoken,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: input,
            type: 'POST'
        }).done(function(data, textStatus, xhr) {
            $('#AddEditLanguageForm').foundation('reveal', 'close');
            loadProfileInfo("languages");
            getNotifications();
        });
	});	
}

/* Decodes HTML Special Chars into normal chars
 * in order to append them correctly in the description
 * textarea. These are encoded before being injected
 */
function unescapeHTML(safe) {
    return $('<div />').html(safe).text();
}

/* Reorders bullet indices starting from zero within
 * a given id
 * @param			id			string			the id with # sign in string format
 * 												for which to rebuild the bullet index
 */
function rebuildBulletIndex(id){
	//This will rebuild the index so when the users deletes a row there will be no double index numnbers.
	var scoreRows = $(id).find('.bullet-parent');
	$.each(scoreRows, function(index, val) {
		$(val).find('.bullet-input').attr('name', 'bullets['+ index +'][value]');
		$(val).find('.bullet-closex').attr('onclick', 'removeBullet('+ index +', "'+id+'")');
	});
}

/* Removes a bullet. Specified by index number
 * and form id
 * @param		num		int			0 indexed bullet number (found in the name)
 * @param		id		string		the id string (for jQuery) that will be
 * 									searched in
 */
function removeBullet(num, id){
	//Get the count of how many rows there are save the count.
	var bullets = $(id).find('.bullet-parent');
	var bullet_count = bullets.length;

	if (bullet_count > 1) {
		bullets.eq(num).remove();
	} else {
		bullets.eq(num).find('input').val('');
	};
	doBulletValidation();
	rebuildBulletIndex(id);
}

/*
// Removes all bullets except one, which is cleared of input
function resetBullets(){
	//Removes all but one empty bullet
	var bullets = $('.bullet-parent');

	$.each(bullets, function(index, val){
		var bullet_count = bullets.length;

		if(bullet_count > 1){
			$('.bullet-parent:last').remove();
		}
		else{
			bullets.find('.bullet-input').val('');
		}
		bullets = $('.bullet-parent');
	});
}

$(document).on('close.fndtn.reveal', '[data-reveal]', function () {
	resetBullets();
});
*/


function EditExperience(e) {
    var data = $(e).data('exp-info');
    var box = $('#AddEditExpForm');

    box.find("#expId").val(data.id);
    box.find("select[name='whocansee']").val(data.whocanseethis);
    box.find('#company_name').val(data.company);
    box.find("#title").val(data.title);
    box.find('#location').val(data.location);
    box.find("select[name='month_from']").val(data.month_from);
    box.find("input[name='year_from']").val(data.year_from);   
    if (data.currentlyworkhere == 1)
	{
        box.find('#icurrentlyworkhere_exp').attr('checked', true);
		toggle_current_workplace( $('#icurrentlyworkhere_exp'), $('#month_to_exp'), $('#year_to_exp'), 'YearCheckExp', $('.end_date_exp'));
    }
	else
	{
		box.find("select[name='month_to']").val(data.month_to);
		box.find("input[name='year_to']").val(data.year_to);	
	}
    box.find("select[name='exp_type']").val(data.exp_type);
    box.find('#description').val(unescapeHTML(data.exp_description));
	$("#expBulletPointsRows").html('');
	$("#exp_count_bullet").val(0);

	////////////////////// BEGIN NEW BULLET POINTS CODE \\\\\\\\\\\\\\\\\\\\
	//get bullet_points data from edit button data attribute
	var bullets = data.bullet_points;
	if(typeof bullets === 'object'){
		//Remove existing bullet forms from modal
		$('.bullet-parent').remove();
		rebuildBulletIndex('#ExperienceInfoForm');

		//Add bullet forms
		for(i = 0; i < bullets.length; i++){
			var bullet_value = bullets[i].value;
			addBullet(bullet_value, false);
		}
		rebuildBulletIndex('#ExperienceInfoForm');
	}
	//\\\\\\\\\\\\\\\\\\\\ END NEW BULLET POINTS CODE ////////////////////
    box.foundation('reveal', 'open');
    addRemoveButton('experience');
}

function addRemoveButton( accomplishment_section ){

    var currentSection_removeFunctionCall = '';

    switch( accomplishment_section ){
        case 'experience': 
            currentSection_removeFunctionCall = 'removeExperience(this)';
        break;
        case 'clubOrg': 
            currentSection_removeFunctionCall = 'removeClubOrgInfo()';
        break;
        case 'honorAwards':
            currentSection_removeFunctionCall = 'removeHonorsAwardsInfo()';
        break;
        case 'certification':
            currentSection_removeFunctionCall = 'removeCertificationInfo()';
        break;
        case 'patents':
            currentSection_removeFunctionCall = 'removePatentsInfo()';
        break;
        case 'publications':
            currentSection_removeFunctionCall = 'removePublicationsInfo()';
        break;
    }

    var removeButtonEmbed = '<div class="small-4 medium-3 large-4 column" onclick="'+currentSection_removeFunctionCall+';">';
    removeButtonEmbed += '<div class="button btn-removeInfo">Remove</div>';
    removeButtonEmbed += '</div>';

    var cancel_btn = $('.button.btn-cancel').parent();
    var save_btn = $('.button.btn-Save').parent();
    var appendAfterCancelButton = $('.saveRemoveCancel_row .btn-cancel').parent();

    //insert remove button after the cancel button
    $(removeButtonEmbed).insertAfter(appendAfterCancelButton);
    //remove classes that accomodate two buttons, add column classes that accomodate 3 buttons
    $(cancel_btn).removeClass('small-6 medium-6 large-6');
    $(save_btn).removeClass('small-6 medium-6 large-6');
    $(cancel_btn).addClass('small-4 medium-3 large-4');
    $(save_btn).addClass('small-4 medium-6 large-4');
}

function hideRemoveButton(){

    var removeButtonEmbed = '<div class="small-4 medium-3 large-4 column" onclick="removeExperience(this);">';
    removeButtonEmbed += '<div class="button btn-removeInfo">Remove</div>';0

    var appendAfterCancelButton = $('.button.btn-removeInfo').parent();
    var cancel_btn = $('.button.btn-cancel').parent();
    var save_btn = $('.button.btn-Save').parent();

    $(appendAfterCancelButton).remove();

    $(cancel_btn).removeClass('small-4 medium-3 large-4');
    $(save_btn).removeClass('small-4 medium-6 large-4');
    $(cancel_btn).addClass('small-6 medium-6 large-6');
    $(save_btn).addClass('small-6 medium-6 large-6');
}

function removeExperience( exp ){

    var areYouSure = confirm('Are you sure you want to delete this Work Experience information?');

    if(areYouSure){

        //finding the input named postType and giving it a value of deleteExperience
        $('#ExperienceInfoForm').find("input[name='postType']").val('deleteExperience');
        var input = $('#ExperienceInfoForm').serialize();
        $.ajax({
            url: '/ajax/profile/removeExperience/' + Plex.ajaxtoken,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: input,
            type: 'POST'
        }).done(function(data, textStatus, xhr) {

            $('#ExperienceInfoForm').foundation('reveal', 'close');
            loadProfileInfo("experience");
            topAlert({
                img: '/images/topAlert/checkmark.png',
                backGroundColor: '#a0db39',
                textColor: '#fff',
                type: 'soft',
                dur: '3500',
                msg: data
            });//end of top alert

        });//end of ajax post

    }else{
        //do nothing
    }

}

function removeClubOrgInfo(){

    var areYouSure = confirm('Are you sure you want to delete this Club/Organization information?');

    if(areYouSure){
        //finding the input named postType and giving it a value of deleteExperience
        $('#ClubOrgInfoForm').find("input[name='postType']").val('removeClubOrg');
        var input = $('#ClubOrgInfoForm').serialize();

        $.ajax({
            url: '/ajax/profile/removeClubOrgInfo/' + Plex.ajaxtoken,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: input,
            type: 'POST'
        }).done(function(data, textStatus, xhr) {

            $('#ClubOrgInfoForm').foundation('reveal', 'close');
            loadProfileInfo("clubOrgs");
            topAlert({
                img: '/images/topAlert/checkmark.png',
                backGroundColor: '#a0db39',
                textColor: '#fff',
                type: 'soft',
                dur: '3500',
                msg: data
            });//end of top alert

        });//end of ajax post

    }else{
        //do nothing
    }

}

function removeCertificationInfo(){

    var areYouSure = confirm('Are you sure you want to delete this Certification information?');

    if(areYouSure){
        //finding the input named postType and giving it a value of deleteExperience
        $('#CertificatesInfoForm').find("input[name='postType']").val('removeCertification');
        var input = $('#CertificatesInfoForm').serialize();

        $.ajax({
            url: '/ajax/profile/removeCertificationInfo/' + Plex.ajaxtoken,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: input,
            type: 'POST'
        }).done(function(data, textStatus, xhr) {
            $('#CertificatesInfoForm').foundation('reveal', 'close');
            loadProfileInfo("certifications");
            topAlert({
                img: '/images/topAlert/checkmark.png',
                backGroundColor: '#a0db39',
                textColor: '#fff',
                type: 'soft',
                dur: '3500',
                msg: data
            });//end of top alert
        });//end of ajax post
    }
}

function removePatentsInfo(){

    var areYouSure = confirm('Are you sure you want to delete this Patent information?');

    if(areYouSure){
        //finding the input named postType and giving it a value of deleteExperience
        $('#PatentsInfoForm').find("input[name='postType']").val('removePatents');
        var input = $('#PatentsInfoForm').serialize();

        $.ajax({
            url: '/ajax/profile/removePatentsInfo/' + Plex.ajaxtoken,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: input,
            type: 'POST'
        }).done(function(data, textStatus, xhr) {
            $('#PatentsInfoForm').foundation('reveal', 'close');
            loadProfileInfo("patents");
            topAlert({
                img: '/images/topAlert/checkmark.png',
                backGroundColor: '#a0db39',
                textColor: '#fff',
                type: 'soft',
                dur: '3500',
                msg: data
            });//end of top alert
        });//end of ajax post

    }//end if statement
}

function removePublicationsInfo(){

    var areYouSure = confirm('Are you sure you want to delete this Publication information?');

    if(areYouSure){
        //finding the input named postType and giving it a value of deleteExperience
        $('#PublicationsInfoForm').find("input[name='postType']").val('removePublications');
        var input = $('#PublicationsInfoForm').serialize();

        $.ajax({
            url: '/ajax/profile/removePublicationsInfo/' + Plex.ajaxtoken,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: input,
            type: 'POST'
        }).done(function(data, textStatus, xhr) {

            $('#PublicationsInfoForm').foundation('reveal', 'close');
            loadProfileInfo("publications");
            topAlert({
                img: '/images/topAlert/checkmark.png',
                backGroundColor: '#a0db39',
                textColor: '#fff',
                type: 'soft',
                dur: '3500',
                msg: data
            });//end of top alert

        });

    }//end if statement
}

function removeHonorsAwardsInfo(){

    var areYouSure = confirm('Are you sure you want to delete this Honors/Awards information?');

    if(areYouSure){
        //finding the input named postType and giving it a value of deleteExperience
        $('#HonorAwardInfoForm').find("input[name='postType']").val('removeHonorsAwards');
        var input = $('#HonorAwardInfoForm').serialize();

        $.ajax({
            url: '/ajax/profile/removeHonorsAwards/' + Plex.ajaxtoken,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: input,
            type: 'POST'
        }).done(function(data, textStatus, xhr) {
            $('#HonorAwardInfoForm').foundation('reveal', 'close');
            loadProfileInfo("honorsAwards");
            topAlert({
                img: '/images/topAlert/checkmark.png',
                backGroundColor: '#a0db39',
                textColor: '#fff',
                type: 'soft',
                dur: '3500',
                msg: data
            });//end of top alert
        });//end of ajax post

    }//end if statement
}

function PostClubOrgInfo()
{
	$("#ClubOrgInfoForm").on("valid",function()
	{
		var input = $('#ClubOrgInfoForm').serialize();

        $.ajax({
            url: '/ajax/profile/clubOrgs/' + Plex.ajaxtoken,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: input,
            type: 'POST'
        }).done(function(data, textStatus, xhr) {
            $('#AddEditClubOrgForm').foundation('reveal', 'close');
            loadProfileInfo("clubOrgs");
            getNotifications();
        });
	});
	return false;
}

function EditClubOrg(e) {
    var data = $(e).data('club-info');
    var box = $('#AddEditClubOrgForm');

    box.find("#clubId").val(data.id);
    box.find("select[name='whocansee']").val(data.whocanseethis);
    box.find('#club_name').val(data.club_name);
    box.find("#position").val(data.position);
    box.find('#location').val(data.location);
    box.find("select[name='month_from']").val(data.month_from);
    box.find("input[name='year_from']").val(data.year_from);    
    if (data.currentlyworkhere == 1)
	{
        box.find('#icurrentlyworkhere_club').attr('checked', true);
		toggle_current_workplace( $('#icurrentlyworkhere_club'), $('#month_to_club'), $('#year_to_club'), 'YearCheckClub', $('.end_date_club'));
    }
	else
	{
	box.find("select[name='month_to']").val(data.month_to);
    box.find("input[name='year_to']").val(data.year_to);
	}
    box.find('#description').val(unescapeHTML(data.club_description));
	////////////////////// BEGIN NEW BULLET POINTS CODE \\\\\\\\\\\\\\\\\\\\
	//get bullet_points data from edit button data attribute
	var bullets = data.bullet_points;
	if(typeof bullets === 'object'){
		//Remove existing bullet forms from modal
		$('.bullet-parent').remove();
		rebuildBulletIndex('#ClubOrgInfoForm');

		//Add bullet forms
		for(i = 0; i < bullets.length; i++){
			var bullet_value = bullets[i].value;
			addBullet(bullet_value, false);
		}
		rebuildBulletIndex('#ClubOrgInfoForm');
	}
	//\\\\\\\\\\\\\\\\\\\\ END NEW BULLET POINTS CODE ////////////////////
    box.foundation('reveal', 'open');
    addRemoveButton('clubOrg');
}

function PostHonorAwardInfo()
{
	$("#HonorAwardInfoForm").on("valid",function()
	{
		var input = $('#HonorAwardInfoForm').serialize();
		
        $.ajax({
            url: '/ajax/profile/honorsAwards/' + Plex.ajaxtoken,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: input,
            type: 'POST'
        }).done(function(data, textStatus, xhr) {
            $('#AddEditHonorAwardForm').foundation('reveal', 'close');
            loadProfileInfo("honorsAwards");
            getNotifications();
        });
	});
	return false;
}

function EditHonorAward(e) {
    var data = $(e).data('honor-info');
    var box = $('#AddEditHonorAwardForm');

    box.find("#honorId").val(data.id);
    box.find("select[name='whocansee']").val(data.whocanseethis);
    box.find('#title').val(data.title);
    box.find("#issuer").val(data.issuer);
    box.find("select[name='month_received']").val(data.month_received);
    box.find("input[name='year_received']").val(data.year_received);
    box.find('#description').val(unescapeHTML(data.honor_description));
	////////////////////// BEGIN NEW BULLET POINTS CODE \\\\\\\\\\\\\\\\\\\\
	//get bullet_points data from edit button data attribute
	var bullets = data.bullet_points;
	if(typeof bullets === 'object'){
		//Remove existing bullet forms from modal
		$('.bullet-parent').remove();
		rebuildBulletIndex('#HonorAwardInfoForm');

		//Add bullet forms
		for(i = 0; i < bullets.length; i++){
			var bullet_value = bullets[i].value;
			addBullet(bullet_value, false);
		}
		rebuildBulletIndex('#HonorAwardInfoForm');
	}
	//\\\\\\\\\\\\\\\\\\\\ END NEW BULLET POINTS CODE ////////////////////
    box.foundation('reveal', 'open');
    addRemoveButton('honorAwards');
}

function PostCertificationInfo()
{
	$("#CertificatesInfoForm").on("valid",function()
	{
		var input = $('#CertificatesInfoForm').serialize();
		$.ajax({
            url: '/ajax/profile/certifications/' + Plex.ajaxtoken,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: input,
            type: 'POST'
        }).done(function(data, textStatus, xhr) {
            $('#addNewCertificates').foundation('reveal', 'close');
            loadProfileInfo("certifications");
        });
	});
	return false;
}

function EditCertification(e) {
    var data = $(e).data('certi-info');
    var box = $('#addNewCertificates');

    box.find("#certiId").val(data.id);
    box.find("select[name='whocansee']").val(data.whocanseethis);
    box.find('#certi_name').val(data.certi_name);
    box.find("#certi_auth").val(data.certi_auth);
    box.find('#certi_license').val(data.certi_license);
    box.find('#certi_url').val(data.certi_url);
    box.find("select[name='month_received']").val(data.month_received);
    box.find("input[name='year_received']").val(data.year_received);
    box.find("select[name='month_expire']").val(data.month_expire);
    box.find("input[name='year_expire']").val(data.year_expire);
    if (data.notexpire == 1)
	{
        box.find('#notexpire').attr('checked', true);
		toggle_current_workplace( $('#notexpire'), $('#month_expire_certi'), $('#year_expire_certi'), 'YearCheckCerti', $('.end_date_certi'));
    }
	else
	{
	box.find("select[name='month_expire']").val(data.month_expire);
    box.find("input[name='year_expire']").val(data.year_expire);	
	}
    box.find('#description').val(unescapeHTML(data.certi_description));
	////////////////////// BEGIN NEW BULLET POINTS CODE \\\\\\\\\\\\\\\\\\\\
	//get bullet_points data from edit button data attribute
	var bullets = data.bullet_points;
	if(typeof bullets === 'object'){
		//Remove existing bullet forms from modal
		$('.bullet-parent').remove();
		rebuildBulletIndex('#CertificatesInfoForm');

		//Add bullet forms
		for(i = 0; i < bullets.length; i++){
			var bullet_value = bullets[i].value;
			addBullet(bullet_value, false);
		}
		rebuildBulletIndex('#CertificatesInfoForm');
	}
	//\\\\\\\\\\\\\\\\\\\\ END NEW BULLET POINTS CODE ////////////////////
    box.foundation('reveal', 'open');
    addRemoveButton('certification');
}

// Removes all bullet points but the first and empties the value
function removeBullets(){
	$('.bullet-parent:not(:first)').remove();
	$('.bullet-input').val('');
}

// Adds validation to all but the first bullet point
function doBulletValidation(){
	$('.bullet-input').attr('required', 'required');
	$('.bullet-input').first().removeAttr('required');
}

function PostPatentsInfo()
{
	$("#PatentsInfoForm").on("valid",function()
	{
		var input = $('#PatentsInfoForm').serialize();
		$.ajax({
            url: '/ajax/profile/patents/' + Plex.ajaxtoken,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: input,
            type: 'POST'
        }).done(function(data, textStatus, xhr) {
            $('#addNewPatents').foundation('reveal', 'close');
            loadProfileInfo("patents");
        });
	});
	return false;
}

function EditPatents(e) {
    var data = $(e).data('patent-info');
    var box = $('#addNewPatents');

    box.find("#patentId").val(data.id);
    box.find("select[name='whocansee']").val(data.whocanseethis);
    box.find("select[name='patent_office']").val(data.patent_office);
    box.find('#patent_app_number').val(data.patent_app_number);
    box.find('#patent_title').val(data.patent_title);
    box.find("select[name='issue_month']").val(data.issue_month);
    box.find("#issue_day").val(data.issue_day);
    box.find("#issue_year").val(data.issue_year);
    box.find("#patent_url").val(data.patent_url);
    box.find('#description').val(unescapeHTML(data.patent_description));

    if (data.patent_authority == 1) {
        box.find('#patent_authority_1').attr('checked', true);
    } else {
        box.find('#patent_authority_2').attr('checked', true);
    }
	////////////////////// BEGIN NEW BULLET POINTS CODE \\\\\\\\\\\\\\\\\\\\
	//get bullet_points data from edit button data attribute
	var bullets = data.bullet_points;
	if(typeof bullets === 'object'){
		//Remove existing bullet forms from modal
		$('.bullet-parent').remove();
		rebuildBulletIndex('#PatentsInfoForm');

		//Add bullet forms
		for(i = 0; i < bullets.length; i++){
			var bullet_value = bullets[i].value;
			addBullet(bullet_value, false);
		}
		rebuildBulletIndex('#PatentsInfoForm');
	}
	//\\\\\\\\\\\\\\\\\\\\ END NEW BULLET POINTS CODE ////////////////////
    box.foundation('reveal', 'open');
    addRemoveButton('patents');
}

function PostPublicationsInfo()
{
	$("#PublicationsInfoForm").on("valid",function()
	{
		var input = $('#PublicationsInfoForm').serialize();
		$.ajax({
            url: '/ajax/profile/publications/' + Plex.ajaxtoken,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: input,
            type: 'POST'
        }).done(function(data, textStatus, xhr) {
            $('#addNewPublications').foundation('reveal', 'close');
            loadProfileInfo("publications");
        });
	});
	return false;
}

function EditPublications(e) {
    var data = $(e).data('pub-info');
    var box = $('#addNewPublications');

    box.find("#publicationId").val(data.id);
    box.find("select[name='whocansee']").val(data.whocanseethis);
    box.find('#title').val(data.title);
    box.find('#publication').val(data.publication);
    box.find("#publication_url").val(data.publication_url);
    box.find("select[name='pub_month']").val(data.pub_month);
    box.find("#pub_day").val(data.pub_day);
    box.find("#pub_year").val(data.pub_year);

    box.find('#description').val(unescapeHTML(data.pub_description));
	
	////////////////////// BEGIN NEW BULLET POINTS CODE \\\\\\\\\\\\\\\\\\\\
	//get bullet_points data from edit button data attribute
	var bullets = data.bullet_points;
	if(typeof bullets === 'object'){
		//Remove existing bullet forms from modal
		$('.bullet-parent').remove();
		rebuildBulletIndex('#PublicationsInfoForm');

		//Add bullet forms
		for(i = 0; i < bullets.length; i++){
			var bullet_value = bullets[i].value;
			addBullet(bullet_value, false);
		}
		rebuildBulletIndex('#PublicationsInfoForm');
	}
	//\\\\\\\\\\\\\\\\\\\\ END NEW BULLET POINTS CODE ////////////////////
    box.foundation('reveal', 'open');
    addRemoveButton('publications');
}

function SaveHighSchoolScores(){
  
	$("#ScoreInfoForm").on("valid",function(){
        cleanUpEmptyOtherScores();

		var input = $('#ScoreInfoForm').serializeArray();

        var input = $.param(input);
        $.ajax({
            url: '/ajax/profile/scores/' + Plex.ajaxtoken,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: input,
            type: 'POST'
        }).done(function(data, textStatus, xhr) {
            $('#addEditHsScore').foundation('reveal', 'close');
            loadProfileInfo("scores");
            getNotifications();
        });
	});
	return false;
}

function SaveCollegeScores() {
  
	$("#CollegeScoreForm").on("valid",function()
	{
		var input = $('#CollegeScoreForm').serialize();
		$.ajax({
            url: '/ajax/profile/scores/' + Plex.ajaxtoken,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: input,
            type: 'POST'
        }).done(function(data, textStatus, xhr) {
            $('#AddEditCollegeScore').foundation('reveal', 'close');
            loadProfileInfo("scores");
            getNotifications();
        });
	});
	return false;
}

function cleanUpEmptyOtherScores(){
    var scoreRows = $('.otherScoreRow');
    $.each(scoreRows, function(index, val) {
       if( $(val).find('.scoreClassName input').val() == ''){
            $(val).find('.scoreClassName input').attr('name','');
            $(val).find('.scoreClassScore input').attr('name','');
        }
    });
}

/* Function for adding HS Score */
function addEditHsScore(e) {

    var data = $(e).data('scoreInfo');
    var box = $('#addEditHsScore');
    if (data) {
        box.find("select[name='whocansee']").val(data.whocansee);
        box.find('#hsGpa').val(data.hsGpa);
        box.find('#weightedGpa').val(data.weightedGpa);
        box.find('#actScore').val(data.actScore);
        box.find('#psat_critical_reading').val(data.psat_critical_reading);
        box.find('#psat_math').val(data.psat_math);
        box.find('#psat_writing').val(data.psat_writing);
        box.find('#psat_total').val(data.psat_total);
        box.find('#sat_critical_reading').val(data.sat_critical_reading);
        box.find('#sat_math').val(data.sat_math);
        box.find('#sat_writing').val(data.sat_writing);
        box.find('#sat_total').val(data.sat_total);
    }
    box.foundation('reveal', 'open');
}

/* AJAX to remember user wants the progress alert closed...
 * until their percentage changes
 */
function ToggleNotificationAlert() {
	// Hides box
	$('#NotificationAlertBox').slideUp(250, 'easeInOutExpo', function(){
		// Turn off notification box until profile percentage changes again
		$.ajax({
			url: '/ajax/profile/suppress-progress-alert',
			data: { suppress: 1 },
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		});
	});
}

/* Receives a jquery object ( $(this) ) and decides whether or not
 * to prepend http depending on if it is already in the string
 * @param		field		$ object		the field to be operated on
 */
function http_helper( field ){
	var value = field.val();
	var has_http = value.indexOf('http') == -1 ? false : true;
	if( !has_http ){
		field.val('http://' + value);
    }
}

function resetForm() {
    $("#user_type").val("");
    $(".ft_zipcode").val("");
    $(".ft_hide_school_name").prop("checked", false);
    $(".ft_school_type").val("").attr('disabled', 'disabled');
    $(".ft_school_name").val("").attr('disabled', 'disabled');
    $('.ft_grad_year').val('');
    $("#schoolinterested1").val("");
    $("#schoolinterested2").val("");
    $("#schoolinterested3").val("");
    $("#autocomplete2").val("");
    $("#autocomplete3").val("");
    $("#autocomplete4").val("");

}

function openTranscriptPreview(element){

    //transcript name link to activate preview
    var previewLink = $('#transcript_preview_link');
    var previewModal = $('#transcript-preview-modal');

    //data variable that is storing the current anchor elements transcript name
    var previewLinkName = $(element).data('transcript-name');

    //grabs the file type of the uploaded file
    var filetype = previewLinkName.split(".").pop();

    //the row where the transcript image needs to be injected into in the reveal modal
    var preImgRow = $('.transcript_preview_img');

    //this will store the html that needs to be embedded into the preview modal
    var showPreview = '';

    if( filetype == 'pdf'){
        //iframe embed for pdf uploads
        var pdfPreview = '<iframe src="http://docs.google.com/gview?url=https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/transcripts/';
        pdfPreview += previewLinkName;
        pdfPreview += '&embedded=true" style="width:100%; height:500px;" frameborder="0"></iframe>';
        showPreview = pdfPreview;
    }else{
        //string to contruct image link
        var previewImg = '';
        previewImg += '<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/transcripts/';
        previewImg += previewLinkName;
        previewImg += '" alt="Uploaded Transcript" />';
        showPreview = previewImg;
    }

    //inject preview image into reveal modal
    preImgRow.html(showPreview);

    //open reveal modal on click
    previewModal.foundation('reveal', 'open');
}


// ***************************** Change country for international students profile - start 

//change country and change profile layout based on if student is internation or national
$('.save-country-change-btn').on('click', function(){
    var _this = this;
    var user_ID = $('.prof-change-country-form').data('user-id');
    var country_ID = $('.prof-change-country-form option:selected').val();
    var saved_country_msg = '<div class="column small-12 small-text-left medium-text-right">Click "What\'s Next" anytime to see what to fill out next. &nbsp;&nbsp;&nbsp;';
    saved_country_msg +=        '<span><img class="show-for-medium-up" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/point-to-whatsNext.jpg" alt="Pointing to Whats Next" /></span>'
    saved_country_msg +=        '<span class="remove-country-change-banner-btn"> X </span>';
    saved_country_msg +=    '</div>';

    var temp_financialinfo_tab = '';
    temp_financialinfo_tab += '<li class="menubutton financial-info-profile-tab tmp-financial-tab" onclick="loadProfileInfo("financialinfo");">';
    temp_financialinfo_tab +=   '<div class="financialinfo-icon"></div><span class="financialinfo">Financial Info </span>';
    temp_financialinfo_tab += '</li>';
                

    $.ajax({
      url: 'profile/setUserCountry',
      type: 'POST',
      data: {user_id: user_ID, country_id: country_ID},
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(ret) {
        $('.for-international-inner-row').html(saved_country_msg);
        $('.side_nav > li.objective-profile-tab').after(temp_financialinfo_tab);
        if( $('.main-uploadcenter-container').is(':visible') ){
            loadProfileInfo('uploadcenter');
        }
        topAlert({
            img: '/images/topAlert/checkmark.png',
            backGroundColor: '#a0db39',
            textColor: '#fff',
            type: 'soft',
            dur: '3500',
            msg: 'Your country has been successfully changed!'
        });//end of top alert 
    });
});

// Toggling pre-exams while editing exam scores
$(document).on('click', '#addEditHsScore .row > .toggle-checkbox > .toggle-pre-exams', function(event) {
    var type = $(this).data('type'),
        parent = $('.edit-scores-' + type),
        isChecked = $(this).is(':checked');

    if (isChecked) {

        Plex.profile.togglePreExams(parent, '.post-exam', '.pre-exam');

    } else {

        Plex.profile.togglePreExams(parent, '.pre-exam', '.post-exam');
    }

});

Plex.profile.togglePreExams = function(parent, old_exam, new_exam) {
    // Duplicate names will be disabled on hide
    var duplicateNames = ['psat_math', 'psat_total', 'sat_math', 'sat_total'];

    var oldParent = parent.find(old_exam);
    var newParent = parent.find(new_exam);

    oldParent.addClass('hide');
    duplicateNames.forEach(function(item) {
        oldParent.find('input[name="' + item + '"]').attr('disabled', true);
    });
          
    newParent.removeClass('hide');
    duplicateNames.forEach(function(item) {
        newParent.find('input[name="' + item + '"]').attr('disabled', false);
    });
}

//on click for financial temp financial tab
$(document).on('click', '.financial-info-profile-tab.tmp-financial-tab', function(){
    loadProfileInfo('financialinfo');
});


//remove country change banner on x click
$(document).on('click', '.remove-country-change-banner-btn', function(){
    var _this = this;
    $.ajax({
      url: 'profile/setProfileIntlCountryChange',
      type: 'POST',
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function() {
        closeChangeInternationBanner(_this);
    });
});


//intercept submission of transcript upload
$(document).on('submit', '#upload_docs_form, #upload_financial_docs_form', function(e){
    e.preventDefault();
    addThisTranscript(this);
});

//when upload btn is clicked, update modal's form docType hidden input value before opening the modal
$(document).on('click', '.upload-docs-btn', function(e){
    e.preventDefault();
    var docType = $(this).data('doc-type'),
        for_financial = $(this).hasClass('for-financial');

    if( docType === '' ){
        docType = 'UnknownDocType';
    }


    if( for_financial ){
        $('#upload_financial_docs_modal').foundation('reveal', 'open');
        $('#upload_financial_docs_form input.doctype').val(docType);
    }
    else{
        $('#upload_docs_modal').foundation('reveal', 'open');
        $('#upload_docs_form input.doctype').val(docType);
    }
});

//In military if it is yes, show military affiliation else hide military affiliation dropdown
$(document).on('change', '#infoInMilitary', function(e){
    e.preventDefault();
    var infoInMilitary = $('#infoInMilitary').val();
    var militaryAffiliation = $('.military_affiliation');
    if (infoInMilitary == 1) {
        militaryAffiliation.animate({
            opacity: "toggle"
          }, 500, function() {
            militaryAffiliation.removeClass('hide');
        });
        
    }else if(infoInMilitary == 0){
        militaryAffiliation.animate({
            opacity: "toggle"
          }, 1000, function() {
            militaryAffiliation.addClass('hide');
            $('#infoMilitaryAffiliation').val('');
        });
        
    }
});


//saving living expense amount
$(document).on('click', '.save-living-exp-btn', function(){
    var finance_amt_val = $('#amt-able-to-pay-form').val();
    // console.log(finance_amt_val);

    if( finance_amt_val ){

        $.ajax({
            url: 'ajax/profile/saveFinancialInfo',
            type: 'POST',
            data: {amt_able_to_pay: finance_amt_val},
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).done(function(ret){
            if( ret === 'success' ){
                topAlert({
                    img: '/images/topAlert/checkmark.png',
                    backGroundColor: '#a0db39',
                    textColor: '#fff',
                    type: 'soft',
                    dur: '3500',
                    msg: 'Living expense amount has been successfully saved!'
                });//end of top alert
            }else{
                topAlert({
                    img: '/images/topAlert/checkmark.png',
                    backGroundColor: '#ddd',
                    textColor: '#fff',
                    type: 'soft',
                    dur: '3500',
                    msg: 'Oops, something went wrong. Please try again.'
                });//end of top alert 
            }
        });
    }
});


//remove transcript
var removeThisTranscript = function(elem, post_type, tscript_id){

    var transcript_row = $(elem).closest('.transcript-details-row');

    // console.log('elem: ', elem);
    // console.log('post_type: ', post_type);
    // console.log('elem: ', tscript_id);

    $.ajax({
      url: '/ajax/profile/uploadcenter/' + Plex.ajaxtoken,
      type: 'POST',
      data: {postType: post_type, TransId: tscript_id},
      headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(ret) {
        // console.log('ret', ret);
        transcript_row.slideUp(500, 'easeInOutExpo').remove();
        topAlert({
            img: '/images/topAlert/checkmark.png',
            backGroundColor: '#a0db39',
            textColor: '#fff',
            type: 'soft',
            dur: '3500',
            msg: 'Your file has been successfully removed!'
        });//end of top alert
    });

}

//add transcript
var addThisTranscript = function(elem){

    var post_data = new FormData(elem),
        is_financial_form = $(elem).hasClass('is-financial-form');

    $.ajax({
        url: '/ajax/profile/uploadcenter/' + Plex.ajaxtoken,
        type: 'POST',
        data: post_data,
        contentType: false,
        processData: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(ret) {

        if( is_financial_form ){
            $('#upload_financial_docs_modal').foundation('reveal', 'close');
            loadProfileInfo('financialinfo');
        }else{
            $('#upload_docs_modal').foundation('reveal', 'close');
            loadProfileInfo('uploadcenter');
        }

        topAlert({
            img: '/images/topAlert/checkmark.png',
            backGroundColor: '#a0db39',
            textColor: '#fff',
            type: 'soft',
            dur: '3500',
            msg: 'Your file was successfully uploaded!'
        });//end of top alert
        getNotifications();
    });
}

var closeChangeInternationBanner = function(_this){
    $(_this).closest('.for-international-students-row').slideUp(500, 'easeInOutExpo');
}

var scrollToFinancialDocs = function(){
    $('html, body').animate({
        scrollTop: $('#financial-docs-scrollTo').offset().top
    }, 2000);
}
// **************************** Change country for international students profile - end 