/***********************************************************************
 *===================== NAMESPACED VARIABLES ===========================
 ***********************************************************************
 * Holds namespaced variables for colleges
 */
Plex.college = {
	isAjaxRunning: false,
    stopAjaxing: false,
    skipAmount: 6,
    ajaxHold: 0,
};

$(document).foundation();


/* Re-arranges college page menu bar items and makes an AJAX call to fetch
 * different college page sections. After AJAX, hides the page section, injects
 * and replaces the hidden page section with the requested content, then shows
 * it again.
 */
function loadCollegeInfo( id, college_id, elem ) {

	//save the element clicked and then grab the info inside its Anchor tag to palce in the mobile menu dropdown header.
	var elem = $(elem);
	var clickhtml = elem.find('a').html();
	// $('#college-middle-menu').find('.mobileMenuTitle').html(clickhtml);
	// console.log(clickhtml);
	collegeMiddleMobileMenuClicked();

	//Kill the chat heartbeat checker.
	Plex.chat.stopChatReadyChecker();

	//grabbing youtube channel and tour id from data in collegeSingleView
	var ytChannel = $('.yt-channel').data('ytchannel');
	var tourId = $('.yt-channel').data('virtualtour');

	if(id!="") {
		window.history.pushState('Plexuss College Detail',"asdas", Plex.PushStart + id);
	} else {
		id='stats';
	}

	if(id == 'undergrad'  || id == 'grad' || id == 'epp'){
			setTopLinkGradUndergrad(id);
			
	}

	$.ajax({
		type: "GET",
		dataType: "html",
		url: '/ajax/college/' + id ,
		data: ({college_id:college_id}),
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		success: function(data) {
			
			$('#collegeInfoArea').slideUp( 400, 'easeInOutExpo', function(){

				if( (ytChannel != '' && ytChannel != undefined) && id == "overview" ){
					loadYoutubeCarousel();
				}else if( (tourId != '' && tourId != undefined) && id == "overview" ){
					loadYoutubeCarousel();
				}

				$('#collegeInfoArea').html(data).data('boxMode', 'ready').slideDown( 400, 'easeInOutExpo', function() {
					Plex.college.stopAjaxing = false;
                    Plex.college.skipAmount = 6;
                    Plex.college.ajaxHold = 0;

					if (id == "overview") loadOverview();
					else if(id == "stats") loadStats();

				/*	// Get page title from div of holding, strip whitespace from JSON first
					var share_params = $('#share_div_of_holding').data('share_params');
					var param_string = JSON.stringify( share_params );
					var reparsed_params = JSON.parse( param_string );
					var page_title = reparsed_params.page_title;

					// Set page title
					document.title = page_title != 'undefined' ? page_title : document.title;

					if (id == "overview") {
						loadOverview();
					} else if(id == "stats"){
						loadStats();
					};
					
					if (id == "chat") {
						//if the page called in is chat call the chat hearbeat
						console.log('Starting chat from menu
						// this is needed to re-cache chat elements that only exi');
						Plex.chat.startChatReadyChecker();st on chat ajax

						Plex.chat.initialize();
					};*/
					//if grad or undergrad 
					//-- must set link in topnav
					//-- must initiate owl carousel
					if(id == 'undergrad'  || id == 'grad' || id == 'epp'){
		 				initOwlInternationalAlumCarousel();
		 				initOwlVideoCarousel();
					}

				});
			});
			
		}
	});


	//passing the collage tab id that was click
	activeCollegeTab(id);

	//ArrangeMenuTabs(id); //looks like this resets the menu
}


function setTopLinkGradUndergrad(id){
	
	if(id == 'undergrad'){
		$('#undergrad-grad-text').text('Undergraduate Requirements');
	}
	if(id == 'grad'){
		$('#undergrad-grad-text').text('Graduate Requirements');
	}
	if(id == 'epp'){
		var aor_id = $('#college_aor_id').val();
		if ( aor_id && aor_id == 5){
			$('#undergrad-grad-text').text('ELS English Program');
		}else{
			$('#undergrad-grad-text').text('English Program');
		}
	}
}



function ArrangeMenuTabs(Current) {
	var Arr=Array('stats','ranking','financial-aid','admissions','tuition','enrollment', 'students');
	for(var i=0;i<Arr.length;i++) {
		Elem=$("[data-link='"+Arr[i]+"']");
		Elem.attr('class','college-'+Arr[i]+'-icon');
	}
	var Elem=$("[data-link='"+Current+"']");
	Elem.attr('class','college-'+Current+'-icon-active');
}

function redirectBattle(a,b,c){
	var val1 =$('#'+a).val();
	var val2 =$('#'+b).val();
	var val3 =$('#'+c).val();	
	
	val1 = val1!=''?val1+',':'';
	val2 = val2!=''?val2+',':'';
	val3 = val3!=''?val3+',':'';
	
	
	if(val1!=''){ 
		window.location='/comparison/?UrlSlugs='+val1+val2+val3;
	} else {
		alert('Please select school');
	}
}

$(function() {
	//This is for the desktop drop down menu to auto hide if mouse leaves it.
	$('.largeMoreDropDown').mouseleave(function() {
    	closeAllCollegeMenuDropDowns();
	});
    $('.largeStatsDropDown').mouseleave(function() {
        closeStatsDropDowns();
    });
    $('.largeTuitionDropDown').mouseleave(function() {
        closeTuitionDropDowns();
    });

    $('.largeCurrentStudentDropDown').mouseleave(function() {
        closeCurrentStudentDropDowns();
    });

    $('.paddingtb-university-name-panel').mouseenter(function(event) {
        // Mouse went over tabs, close all dropdowns.
        closeAllCollegeMenuDropDowns();
        closeStatsDropDowns();
        closeTuitionDropDowns();
        closeCurrentStudentDropDowns();
    })
});

//controls middle menu on the MOBILE single college page.
function collegeMiddleMobileMenuClicked(){
	/* This is only used for Medium and smaller devices. So only allow the
	 * below code to run when window width is medium or smaller (< 64.063em)
	 */
	var width = $(window).width() / parseFloat($("body").css("font-size"));
	if( width < 64.063 ){
		var elem = $('.dropDownCollegeMenu');
		if (elem.hasClass('active')) {
			elem.slideUp( 250,'easeInOutExpo', function (){
				$(this).css('display', '').removeClass('active');
			});
		} else {
			elem.slideDown(250, 'easeInOutExpo', function() {
				$(this).addClass('active');
			});
		};
	}
}

//Shows the desktop dropdown menu on single college view.
function collegeMiddleDesktopMoreClicked () {
	var elem = $('.largeMoreDropDown');
    closeStatsDropDowns();
    closeTuitionDropDowns();
    closeCurrentStudentDropDowns();
    elem.slideDown(250,'easeInOutExpo').addClass('active');
}

function collegeMiddleDesktopStatsOver () {
    var elem = $('.largeStatsDropDown');
    closeAllCollegeMenuDropDowns();
    closeTuitionDropDowns();
    closeCurrentStudentDropDowns();
    elem.slideDown(250,'easeInOutExpo').addClass('active');
}

function collegeMiddleDesktopCurrentStudentOver() {
    var elem = $('.largeCurrentStudentDropDown');
    closeAllCollegeMenuDropDowns();
    closeStatsDropDowns();
    closeTuitionDropDowns();
    elem.slideDown(250,'easeInOutExpo').addClass('active');
}

function collegeMiddleDesktopTuitionOver() {
    var elem = $('.largeTuitionDropDown');
    closeAllCollegeMenuDropDowns();
    closeStatsDropDowns();
    closeCurrentStudentDropDowns();
    elem.slideDown(250,'easeInOutExpo').addClass('active');
}

//dropdown for smaller screens
function topnavInternational(){
	var elem = $('#int_topNav_container');
	if (elem.hasClass('active')) {
			elem.slideUp( 250,'easeInOutExpo', function (){
				$(this).css('display', '').removeClass('active');
			});
		} else {
			elem.slideDown(250, 'easeInOutExpo', function() {
				$(this).addClass('active');
			});
		};
}


//opens undergrad and graduate sub menu on international pages
function undergradGradDropdown(){
	var elem = $('#underGrad-dropdown');
	

	//if screen sizes smaller than 1231px --
	//if not opened
	//must move down the content below to make space for items
	
	

	if($(window).width() <= 1231 && elem.css('display') == 'none'){

		//get height of submenu
		var subHeight = $('#underGrad-dropdown').height();
		
		var newLi = $('<li>').css('height', subHeight);
		newLi.addClass('veryNewLi');
		//insert li, after Undergraduate..., with height equal to subHeight
		$('ul.int-topnav > li:nth-child(2)').after(newLi);

	}
	if($(window).width() <= 1231 && elem.css('display') == 'block'){
		$('.veryNewLi').remove();
	}

	//elem.toggle();
	if (elem.hasClass('active')) {
			elem.slideUp( 250,'easeInOutExpo', function (){
				$(this).css('display', '').removeClass('active');
			});
		} else {
			elem.slideDown(250, 'easeInOutExpo', function() {
				$(this).addClass('active');
			});
		};
}

//Closes any open middle drop down menus and resets them
function closeAllCollegeMenuDropDowns(){
	var elem = $('.largeMoreDropDown');
    elem.slideUp( 250,'easeInOutExpo').removeClass('active');
}

function closeStatsDropDowns(){
    var elem = $('.largeStatsDropDown');
    elem.slideUp( 250,'easeInOutExpo').removeClass('active');
}

function closeTuitionDropDowns(){
    var elem = $('.largeTuitionDropDown');
    elem.slideUp( 250,'easeInOutExpo').removeClass('active');
}

function closeCurrentStudentDropDowns(){
    var elem = $('.largeCurrentStudentDropDown');
    elem.slideUp( 250,'easeInOutExpo').removeClass('active');
}

function loadOverview(){
	var owl = $("#owl-example");
	var youtube = $('#owl-youtube');

	owl.owlCarousel({
		navigation : false, // Show next and prev buttons
		slideSpeed : 800,
		paginationSpeed : 400,
		singleItem:true,
		pagination:false,
		afterAction: setCurrentOwlItem
	});

	/* Button binding for carousel and college overview
	 */
	$(".next").click(function(){
		owl.trigger('owl.next');
	});
	$(".prev").click(function(){
		youtube.trigger('owl.next');
	});
	$(".prev").click(function(){
		owl.trigger('owl.prev');
	});

	$(".next-btn").click(function(){
		youtube.trigger('owl.next');
	});
		$(".prev-btn").click(function(){
		youtube.trigger('owl.prev');
	});
}

// Sets overview image position variable
// not sure this is the best way to do this...
function setCurrentOwlItem(){
	var carousel_index = this.owl.currentItem;
	var new_image_element = $('#college-carousel .owl-item img:eq(' + carousel_index + ')');
	var new_image_src = new_image_element.attr('src');

	Plex.current_overview_image = new_image_src.split("/").pop();
}

function loadStats(){
	graddonutbox();
	satdonutbox();
	actdonutbox();
	setResizeBox();
};

function loadRanking(){
	//Nothing here yet!
};


function loadAdmissions(){
	//Nothing here yet!
};

function loadYoutubeCarousel(){

	$.ajax({
		type: 'GET',
		url: '/js/youtubeCarousel.js',
		dataType: "script",
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		success: function(data){}
	});

};


function initOwlVideoCarousel(){
	var owl = $('.video_owl_carousel');

	owl.owlCarousel({

	  autoPlay: false,  //set autoplay every 3 secs

	  navigation: true,
	  navigationText: ['','' ],
	  pagination: false,

      items : 1, //1 items above 1000px browser width
      itemsDesktop : false, 
      itemsDesktopSmall : false, 
      itemsTablet: false,
      itemsMobile : false // itemsMobile disabled - inherit from itemsTablet option
  });

}



function initOwlInternationalAlumCarousel(){
	var owl = $('.owl-int-alumni-carousel');

	owl.owlCarousel({

	  autoPlay: 3000,  //set autoplay every 3 secs

	  navigation: true,
	  navigationText: ["&lsaquo;","&rsaquo;"],

      items : 3, //3 items above 1000px browser width
      itemsDesktop : [1000,3], //3 items between 1000px and 901px
      itemsDesktopSmall : [900,2], // betweem 900px and 601px
      itemsTablet: [600,1], //1 items between 600 and 0
      itemsMobile : false // itemsMobile disabled - inherit from itemsTablet option
  });

}


$(document).ready(function(){
	
	//Get the current url pathname and pass whatever is after the last occurance of '/', which should be the college menu tab name
	/*This is used so even if a user reloads the page on any college tab, the active class will still be applied to the 
	correct college tab*/
	var path = window.location.pathname;
	var tab = path.substring( path.lastIndexOf('/') + 1 );
	var accepted_tabs = ['overview','undergrad', 'grad', 'epp','stats','ranking','admissions','chat','financial-aid','enrollment','tuition','news'];
	var tab_found = false;

	//loop through the accepted tabs array and check if the parsed tab value matches any of the accepted tab values
	for (var i = 0; i < accepted_tabs.length; i++){
		if( accepted_tabs[i] == tab ){
			tab_found = true;
			break;
		}
	};

	//if a match is found, then pass the tab variable, if not, assume overview page
	if( tab_found ){
		activeCollegeTab(tab);
	}else{
		activeCollegeTab('overview');
		$('.icon-overview').addClass('.tab-icon-overview');
		$('.tab-icon-overview').removeClass('.icon-overview');
	}


	if(tab == 'grad'){
		$('#undergrad-grad-text').text('Graduate Requirements');
	}
	if(tab == 'undergrad'){
		$('#undergrad-grad-text').text('Undergraduate Requirements');
	}
	if(tab == 'epp'){
		var aor_id = $('#college_aor_id').val();
		if ( aor_id && aor_id == 5){
			$('#undergrad-grad-text').text('ELS English Program');
		}else{
			$('#undergrad-grad-text').text('English Program');
		}
		
	}

	initOwlInternationalAlumCarousel();
	initOwlVideoCarousel();





	//////////////////////
	$(document).on('click', function(e){
		
		if($(e.target).closest('#underGrad-dropdown').length === 0 && 
			$(e.target).closest('.undergradGradepp-tab').length === 0){
		
			$('#underGrad-dropdown').hide();
		}

	});



	// //////////////////////
	// // handler for clicking major department buttons on college pages
	$(document).on('click', '.majors-toggle-btn', function(e){
		var more = $('.majors-toggle-mobile');
		var moreToggle = $(this);

		if(more.is(':visible')){
			more.css('display','none');
			moreToggle.text('show more...');
		}else{
			more.css('display','inline-block');
			moreToggle.text('show less...');
		}	
	});


	// Close top nav alert for Read up to 50 college essays
	$(document).on('click', '#signup-offer-close', function(e){
		$.ajax({
			url: '/ajax/closeSignupOffer',
			type: 'POST',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		}).done(function() {
			// console.log("success");
		});
	});


}); //end .ready


/*************************************
* is it daylight saving time?
* //in current user system...
**************************************/
function isDST(){

	var d = new Date();
	var jan = new Date(d.getFullYear(), 0, 1);
	var jul = new Date(d.getFullYear(), 6, 1);
	var stdOffset = Math.max(jan.getTimezoneOffset(), jul.getTimezoneOffset());
	
	var cOffset = d.getTimezoneOffset();
	
	return stdOffset > cOffset;


}


/**************************************
* techRepublic... 
 * function to calculate local time
 * in a different city
 * given the city's UTC offset
 **************************************/
function calcTime(offset) {

    // create Date object for current location
    var d = new Date();

    // convert to msec
    // add local time zone offset
    // get UTC time in msec
    var utc = d.getTime() + (d.getTimezoneOffset() * 60000);

    // create new Date object for different city
    // using supplied offset
    var nd = new Date(utc + (3600000*offset));

    // return time as a string
    return nd.toLocaleString();
}



function activeCollegeTab(page){

	//stores the current college tab and tab icon, so we know which tab to apply the active class to
	var current_college_tab = '.tab-' + page;
	var current_college_tab_icon = '.icon-' + page;

	//This is class that will turn the tab text orange
	var active_college_tab_icon = 'tab-' + page + '-icon';


	//if undergrad or grad -- set top navigation menu link to active
	// the active tab class does not change between undergrad, grad, and engP
	if(page == 'undergrad' || page == 'grad' || page == 'epp'){
		current_college_tab = '.tab-undergradengP';
		current_college_tab_icon = '.icon-undergrad-m';
		
		active_college_tab_icon = 'tab-undergrad-icon';

		//$('.tab-undergrad-menu .arrow').css('border-top', '7px solid #FF5C26');

	}
	
	
	//if(page != 'undergrad' || page != 'grad'){
		//$('.tab-undergrad-menu .arrow').css('border-top', '7px solid #ffffff');
	//}	
	// 	if(tab == 'grad'){
	// 	$('#undergrad-grad-text').text('Graduate Requirements');
	// }
	// if(tab == 'undergrad'){
	// 	$('#undergrad-grad-text').text('Undergraduate Requirements');
	// }
	// if(tab == 'epp'){
	// 	$('#undergrad-grad-text').text('English Pathway Programs');
	// }

	/* two arrays:
	-> active_icon_classes = the class names for an active tab icon (orange icon) 
	-> inactive_icon_tab = the class name of the inactive tab icon, where the active icon class should go, should the user
	be on that page.
	*/
	var active_icon_classes = ['tab-undergrad-icon', 'tab-undergrad-icon', 'tab-undergrad-icon', 'tab-undergrad-icon', 'tab-overview-icon', 'tab-stats-icon', 'tab-ranking-icon', 'tab-admissions-icon', 'tab-chat-icon', 'tab-financial-aid-icon', 'tab-enrollment-icon', 'tab-tuition-icon', 'tab-news-icon'];
	var inactive_icon_tab = ['.icon-undergrad-menu', '.icon-undergrad', '.icon-grad', '.icon-engP','.icon-overview', '.icon-stats', '.icon-ranking', '.icon-admissions', '.icon-chat','.icon-financial-aid','.icon-enrollment','.icon-tuition', '.icon-news'];

	//loop counter to track what iteration the loop is currently on
	var counter = 0;

	//remove the active class from all tabs
	$('ul.largeMoreDropDown, ul.university-attached-menu, .int-topnav').find('li a').removeClass('active-college-tab');

	/*loop through the array of inactive icon tab names and remove the active icon class that should be associated with that 
	inactive icon tab since the active_icon_array elements are purposely in line with the inactive_icon_tab in terms of tab name
	and index number, for example, 'tab-overview-icon' is the active icon class name for the inactive icon tab named
	'icon-overview' */
	$.each(inactive_icon_tab, function(key, value){

		$(inactive_icon_tab[counter]).removeClass(active_icon_classes[counter]);
		counter++;
	});


	//add active class to current tab and tab icon
	$(current_college_tab).addClass('active-college-tab');
	$(current_college_tab_icon).addClass(active_college_tab_icon);

	//if current tab is undergrad or grad -- also set the dropdown menu items active
	if(page == 'undergrad'){
		$('.tab-undergrad').addClass('active-college-tab');
		$('.icon-undergrad').addClass(active_college_tab_icon);
	}
	if(page == 'grad'){
		$('.tab-grad').addClass('active-college-tab');
		$('.icon-grad').addClass(active_college_tab_icon);
	}
	if(page == 'epp'){
		$('.tab-engP').addClass('active-college-tab');
		$('.icon-engP').addClass(active_college_tab_icon);
	}

}

Plex.triggerChatBtn = function(){
	$('.chat-with-btn').trigger('click');
}


//college ranking pins functions
$(document).on('click', '.see-more-pin-descript-btn', function(e){
	e.preventDefault();
	Plex.college.seeMorePinDescription(this);
});

Plex.college.seeMorePinDescription = function(elem){
	var _this = $(elem);
	var full_descript = _this.data('full-descript');
	var half_descript = _this.data('half-descript');
	var is_open = _this.data('is-open');

	//if open, collapse it, else open it
	if( is_open ){
		_this.closest('.pin-descript').find('div:first-child').html(half_descript).removeClass('expanded');
		_this.html('Show more');
	}else{
		_this.closest('.pin-descript').find('div:first-child').html(full_descript).addClass('expanded');
		_this.html('Show less');
	}

	//toggle is-open
	_this.data('is-open', !is_open);
}


//mobile chat
$(window).resize(function(){
	if( $(window).width() < 640 ){
		$('.rightChatColumn').hide();
	}else{
		$('.rightChatColumn').show();
		$('.leftChatColumn').show();
	}
});

$(document).on('click', '#agencyRepAd .agency-link', function(){
	var slug = $(this).data('slug');

	window.open(slug, '_blank');
});

$(document).on('click', '.chatOnlineBox .leftChatColumn .chatUser', function(){
	if( $(window).width() < 640 ){
	    Plex.common.slideHide( $(this).closest('.leftChatColumn'), $('.rightChatColumn'), 'left', 500);
	}
});

//when mobile chat back button is pressed to go back to list of college admins to chat with
$(document).on('click', '.chatOnlineBox .back-to-admin-chatters-btn', function(){
	if( $(window).width() < 640 ){
	    Plex.common.slideHide( $(this).closest('.rightChatColumn'), $('.leftChatColumn'), 'right', 500);
	}
});


$(document).on('click', '.news-container a.news-related', function() {
	var _this = $(this);
	var data = { 'news_link': _this.attr('href') , 'slug': _this.data('slug')};
	// console.log(data);
	var slug = _this.data('slug');

	mixpanel.track("college_news_click",{
            "news_link": _this.attr('href'), // Property
            "slug": slug
        });

	$.ajax({
		url: '/collegeNewsClicked',
		type: 'POST',
		data: data,
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	}).done(function() {
		// console.log("success");
	});
	
});

$(document).on('click', '#affiliateAds', function(){
	$.ajax({
        url: '/adClicked',
        data: {slug: $('#hidden-college-slug').data('slug'),
			   company: $('#hidden-affiliate-company').data('company'),
			   adCopyId: $('#hidden-adcopy-id').data('adcopyid')},
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(data) {
		// $('.start-plexuss-btn').trigger('click');//remove when returning invite feature
	});
});


$(document).on('click', '#plexussBannerAd', function(){
	$.ajax({
        url: '/adClicked',
        data: {slug: $('#hidden-college-slug').data('slug'),
			   company: $('#hidden-affiliate-company').data('company'),
			   adCopyId: $('#hidden-adcopy-id').data('adcopyid')},
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(data){
		// console.log(data);
	});
});

$(document).on('click', '#collegeapplynow, #collegeCommonApply', function(){
	var _this = $(this);

	$.ajax({
        url: '/applyNowClicked',
        data: {
			slug: _this.data('slug'), 
			source: _this.data('source'), 
			org_app_url: _this.data('url'),
		},
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(data){
		// console.log(data);
	});
});

$(document).on('change', '#revenue_programs_details .revenue_program_name_toggle', function(event) {
    var revenue_programs = $(this).closest('#revenue_programs_details').data('revenue_programs');

    var detailsContainer = $('#revenue_programs_details .revenue_programs_details_container');

    var find = _.find(revenue_programs, function(program) { return program.program_name === event.target.value });

    var selling_points = null;

    var html = '';

    if (!_.isEmpty(find.selling_points)) {
        selling_points = find.selling_points;
    }

    if (_.isArray(selling_points) && !_.isEmpty(selling_points)) {
        selling_points.forEach(function(point) {
            html += ('<p>' + point + '</p>');
        });
    }

    detailsContainer.html(html);
});

$(window).on('scroll', function () {
    if ($(window).scrollTop() + 2 >= $(document).height() - $(window).height()) {
        if (Plex.college.ajaxHold === 0) {
            Plex.college.ajaxHold = 1;

            if (window.location.pathname.indexOf('current-students') != -1 || window.location.pathname.indexOf('alumni') != -1)
                scrollInfinite();
        }
    }
});

function scrollInfinite() {
    var college_id = $('.paddingtb-university-name-panel').data('cid');

    if(Plex.college.stopAjaxing){
        $('div#loadmoreajaxloader').hide();
    } else {
        $('div#loadmoreajaxloader').show();

        var alumniAjaxRoute = "/college/alumniAjaxData";
        var currentStudentsAjaxRoute = "/college/currentStudentAjaxData";
         
        var route = (window.location.pathname.indexOf('current-students') != -1) 
            ? currentStudentsAjaxRoute 
            : alumniAjaxRoute;

        $.ajax({
            url: route,
            data: {skipAmount: Plex.college.skipAmount, collegeId: college_id},
            method: "POST",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
        })
        .done(function (html) {
            $('div#loadmoreajaxloader').hide();
            $("#container-box").append(html);

            // No more results
            if (_.isEmpty(html) || html.indexOf('student-name') == -1) {
                Plex.college.stopAjaxing = true;
            }

            Plex.college.ajaxHold = 0;
            Plex.college.skipAmount += 6;
        })
        .fail(function () {
            $('div#loadmoreajaxloader').html('');
        });
    }
}