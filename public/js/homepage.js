/*** start of phase 2 of new front page js ***/
var Plex = Plex || {};
Plex.homepg = {
    skip: 10,
    limit: 10,
    member_scroll_pos: 0,
    in_progress: false,
    more_members: true
};

$(document).ready(function() {

    var signed_in = $('#get_started').data('signed_in');

    if (window.localStorage && window.localStorage.getItem('plexuss-gdpr-cookies-agree') != '1') {
        $('.gdpr-cookies-notification').css({display: 'flex'});
    }

    amplitude && amplitude.getInstance().logEvent('view front page', { is_logged_in: signed_in });

    $(".mobile-app-sms-phone").intlTelInput({
        utilsScript: "/js/phoneUtils.js"
    });

    $(document).on('change input countrychange', '#send-mobile-app-sms-modal .mobile-app-sms-phone', function(e) {
        var phone = $(this),
            parent = $(this).closest('#send-mobile-app-sms-modal'),
            isValid = $(this).intlTelInput('isValidNumber'),
            saveButton = parent.find('.send-mobile-app-sms-button');

        if (isValid) {
            saveButton.addClass('enabled');
            saveButton.html('Send an SMS');
        } else {
            saveButton.removeClass('enabled');
        }
    });

    $(document).on('click', '#send-mobile-app-sms-modal .send-mobile-app-sms-button', function(e) {
        var parent = $(this).closest('#send-mobile-app-sms-modal'),
            phoneNumber = parent.find('.mobile-app-sms-phone').intlTelInput('getNumber'),
            that = $(this);

        if ($(this).hasClass('enabled')) {

            // Disable button to avoid double sending
            $(this).removeClass('enabled');

            $(this).html('Sending...');

            $.ajax({
                url: '/phone/plexussAppSendInvitation',
                type: 'POST',
                data: {
                    phone: phoneNumber
                },
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                },
            }).done(function(response) {
                setTimeout(function() { that.html('SMS has been sent!') }, 2000);
            });
        }
    });
 
    var owl = $(".all-frontpage-carousels");
    var collegeNearYouData = $('#colleges-near-you-carousel').data('colleges-near-you');


    var sidebar_section_to_show = '';
    var all_sidebar_sections = $('.frontpage-side-bar-sections');

    var aws_url = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/';

    var all_carousel_headers = $('.college-carousel-label').toArray();
    

    //variables to do the number 
    var owlItemsAmount  = 0;

    // variable to determine if the user is hitting on prev button
    var isPrevious  = false;

    // start of custom owl carousel
    owl.owlCarousel({
        items : 6, //10 items above 1000px browser width
        itemsDesktop : [1300,4], //5 items between 1200px and 801px
        itemsDesktopSmall : [800,3], // betweem 900px and 601px
        itemsTablet: [600,3], //2 items between 600 and 0
        itemsMobile : [600, 1], // itemsMobile disabled - inherit from itemsTablet option
        pagination: false,
        loop: false,
       // slideSpeed : 500,
/**        startDragging: function(elem){
                console.log('drag that bitch'); 
                var _this = this;
                console.log(_this);
                var _thisOwl = _this.owl;

                owlItemsAmount = this.itemsAmount;
                console.log('item #s '+owlItemsAmount);

                var _carousel_name = elem.parent().attr('class').substr(elem.parent().attr('class').lastIndexOf(' ') + 1);

                ajaxCarouselItems(_carousel_name, owlItemsAmount);
            },
*/
        beforeMove: function(elem){
             var visibleItemsArr = this.visibleItems;

             var owlItemsAmount = this.itemsAmount;
             //console.log(visibleItemsArr);
             //console.log(owlItemsAmount -1);
             //console.log (visibleItemsArr.indexOf(owlItemsAmount - 1));

             if(visibleItemsArr.indexOf(owlItemsAmount - 1) != -1){

                var _carousel_name = elem.parent().attr('class').substr(elem.parent().attr('class').lastIndexOf(' ') + 1);

                ajaxCarouselItems(_carousel_name, owlItemsAmount, isPrevious);
             }

            //console.log('before move');
        }
    });
    // end of custom owl carousel



    //next button click for 'this' carousel
    $(document).on('click', '.next-col-pin', function(){
        var current_carousel = $(this).parent().find('.all-frontpage-carousels');
        isPrevious  = false;
        current_carousel.trigger('owl.next');
    });

    //prev button click for 'this' carousel
    $(document).on('click', '.prev-col-pin', function(){
        var current_carousel = $(this).parent().find('.all-frontpage-carousels');
        isPrevious  = true;
        current_carousel.trigger('owl.prev');
    });



    /*******************************************************
    *   handler for webinar form
    *
    ********************************************************/
    $(document).on('click', '#webinar-join-btn', function(e){

        //should validate form before sending
        var mdata = $('.home-webinar-form').serializeArray();

        //mdata[0] contains name
        //mdata[1] should contain email
        var name_re = /^[a-z]+\s*[a-z]*$/i;
        var email_re = /^[a-z0-9._%+-]+@[a-z0-9.-]+.[a-z]{2,4}$/i; 

        if( mdata[0].value.match(name_re) && mdata[1].value.match(email_re)){
           
            $.ajax({
                type: 'POST',
                url: '/webinar/saveWebinarLiveSignups',
                data: $('.home-webinar-form').serialize(),
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                success: function(response){

                    var res = JSON.parse(response);
                    if(res.status == 'ok'){
                        $('.home-webinar-background-image').html('<div class="centered-container">');
                        $('.centered-container').html(res.video);
                    }
                }

            });
        }
        else{
            $('.webinar-error-section').text('invalid name or email.');
           
        }
         e.preventDefault();

    });

    ////// may clean up later
    //webinar form -- clear first name when input focused on
    $('.home-webinar-form input[value = "Name"]').focus(function(){

        
        if($(this).val() == 'Name')
            $(this).val('');

    });
    //reset field to default if empty
    $('.home-webinar-form input[value = "Name"]').blur(function(){
        
        if($(this).val() == ''){
            $(this).attr("value", "Name");
            $(this).val("Name");
        }

    });
    //webinar form -- clear email when input focused on
    $('.home-webinar-form input[value = "Email"]').focus(function(){

        if($(this).val() == 'Email')
            $(this).val('');
    });
    //reset field to default if empty
    $('.home-webinar-form input[value = "Email"]').blur(function(){
        
        if($(this).val() == ''){
            $(this).attr("value", "Email");
            $(this).val("Email");
        }

    });


    /* frontpage sidebar nav logic to show/hide clicked side bar sections - start */
    $(document).on('click', '.frontpage-custom-icon-bar .item', function(e){
        e.preventDefault();
        sidebar_section_to_show = $(this).attr('href');

        //remove all green icons first
        $(this).parent().find('a.item .sidebar-icon-active').hide();
        $(this).parent().find('a.item .sidebar-icon-inactive').show();

        //remove any active class from all tabs first
        $(this).parent().find('a.item').removeClass('active-custom-side-bar');
        $(this).parent().find('a.item label').removeClass('active-custom-side-bar-label');

        //then add active class to 'this' tab
        $(this).addClass('active-custom-side-bar');
        $(this).find('label').addClass('active-custom-side-bar-label');

        //show green active icon
        $(this).find('img.sidebar-icon-inactive').hide();
        $(this).find('img.sidebar-icon-active').show();

        //show 'this' section
        $(all_sidebar_sections).hide();
        $(sidebar_section_to_show).show();

        //show back button
        $('.mobile-frontpage-back-btn').show();
    });
    /* frontpage sidebar nav logic to show/hide clicked side bar sections - end */

    /* close sidebar section on mobile - start */
    $(document).on('click', '.frontpage-back-btn, .mobile-frontpage-back-btn', function(){
        collapseSideBarSection();//hide/collapse all sections and remove any active classes to sidebar icon and labels
        $('.mobile-frontpage-back-btn').hide();//then hide the back btn of course
    });
    /* close sidebar section on mobile - end */



    /* equal heights logic on page load and window resize - start */
    // $(window).resize(function(){

    //     if( $(window).width() > 642 ){

    //         //match sidebar section height to sidebar menu
    //         var sidebarHeight = $(this).parent().height();
    //         var sectionHeight = $(this).height();
    //         var sideBarSection = $(this).attr('href');

    //         $(sideBarSection).height(sidebarHeight);
    //     }

    // });

    // $(document).on('click', '.frontpage-custom-icon-bar a', function(){

    //     if( $(window).width() > 642 ){

    //         //match sidebar section height to sidebar menu
    //         var sidebarHeight = $(this).parent().height();
    //         var sideBarSection = $(this).attr('href');
    //         var sectionHeight = $(sideBarSection).height();

    //         $(sideBarSection).height(sidebarHeight);
    //     }

    //     //check if on mobile, if so, scroll window to top on menu click (if you're not already at the top of page)
    //     if( $(window).width() < 642 ){
    //         var mobileSection = $(this).attr('href');
    //         $('body').animate({
    //             scrollTop: 0
    //         }, 1000);
    //     }
        
    // });
    /* equal heights logic on page load and window resize - end */


    $(document).on('change', '.frontpage-adv-search-form .frontpage-state',function(){

        var _this = $(this);

        if(_this.val() !== ''){
            populateCityBasedOnState(_this.val());
        }
    });
	
	/* carousel headers active in viewport - start */
    $(window).scroll(function(){
        var all_carousel_headers = $('.college-carousel-label');

        for (var i = 0; i < all_carousel_headers.length; i++) {
            if( $(all_carousel_headers[i]).offset().top - $(document).scrollTop() < 400 || 
                $(all_carousel_headers[i]).offset().top - $(document).scrollTop() < 100 ){
                $(all_carousel_headers[i]).addClass('college-carousel-label-active', 1000);
            }else{
                $(all_carousel_headers[i]).removeClass('college-carousel-label-active', 1000);
            }
        }
    });
    /* carousel headers active in viewport - end */ 



    /********* submit battle schools/submit find a college adv search on enter key press - start *********/
    $('.battleschools_search_input').keypress(function(e){
        if( e.which == 13 ){
            e.preventDefault();
            submitBattleSearch('battle_search_1', 'battle_search_2', 'battle_search_3');
        }
    });

    $('.frontpage-adv-search-form input, .frontpage-adv-search-form select, .frontpage-adv-search-form checkbox').keypress(function(e){

        if( e.which == 13 ){
            e.preventDefault();
            submitFrontpageAdvSearch();            
        }
    });
    /********* submit battle schools/submit find a college adv search on enter key press - end *********/



    // plex steps mobile click event to show step description
    $(document).on('click', '.mobile-steps-card', function(){
        $('.mobile-steps-front', this).toggle('drop', {}, 200);
    });



    /**** hiding sidebar on mobile when input field has focus - start ****/
    $('#frontpage_opening_side_bar_section input[type="text"], #find_a_college_side_bar_section select, #find_a_college_side_bar_section input[type="text"], #compare_side_bar_section input[type="text"]').focus(function(){
        if( $(window).width() < 640 ){
            $('.frontpage-custom-icon-bar.icon-bar').hide();    
        }
    }).focusout(function(){
        if( $(window).width() < 640 ){
            $('.frontpage-custom-icon-bar.icon-bar').show();    
        }
    });
    /**** hiding sidebar on mobile when input field has focus - end ****/ 

    isChatEnabled();

    $(document).on('click', '.eu-gdpr-notification .eu-gdpr-ok-button, .eu-gdpr-notification .eu-gdpr-close-button', function(event) { 
        $(this).closest('.eu-gdpr-notification').hide();
    });

    $(document).on('click', '.gdpr-cookies-notification .gdpr-cookies-agree-button', function(event) {
        $(this).closest('.gdpr-cookies-notification').hide();
        window.localStorage.setItem('plexuss-gdpr-cookies-agree', 1);

        $.ajax({
            method: 'POST',
            url: '/ajax/plexussCookieAgree',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        });
    });

    /********** department and major selection box events/handlers from 'search for colleges' on homepage ***/
    $(document).on('change', '.adv-c-s-majors-select', function(e){
        var that = $(this);
        var val = that.val();
        var dfrag = document.createDocumentFragment();

        // if(MajorsSearch.query.type == 'majors'){
        //     MajorsSearch.query.term = val;
        // }

        $('.sm-grey-loader').removeClass('hide');
        $('.majors-select-container').removeClass('hide');
        //display majors select after getting majors
        $.ajax({
            url: '/ajax/getMajorsFromCat?cat='+ val,
            type: 'GET',

        }).done(function(res){
            console.log(res);
            
            $('.sm-grey-loader').addClass('hide');

            //append default option
            var op = document.createElement('option');
            op.setAttribute('value', null);
            op.innerHTML = 'Select Major...';
            dfrag.appendChild(op);

            for(var i in res){
                var op = document.createElement('option');
                op.setAttribute('value', res[i].id);
                op.innerHTML = res[i].name;
                dfrag.appendChild(op);
            }           

            var select = document.getElementsByClassName('majors-selection-box')[0];
            
            select.innerHTML = '';
            select.appendChild(dfrag);
        })
    });

    $(document).on('mouseenter', '.plexuss-mobile-ad-frontpage-right-side .frontpage-circle-img', function(e) {
        var side = $(this).prop('class').replace(/\s+|frontpage-circle-img|-circle|active/g, '');

        var signed_in = $('#get_started').data('signed_in');

        switch (side) {
            case 'left':
                amplitude && amplitude.getInstance().logEvent('hover circle a', { 
                    is_logged_in: signed_in, 
                    Image : 'diverse students',
                    Text: 'Connect with other students & alumni',
                });

                break;

            case 'right':
                amplitude && amplitude.getInstance().logEvent('hover circle b', { 
                    is_logged_in: signed_in, 
                    Image : 'asian female grad',
                    Text: 'Find and apply to universities',
                });

                break;

            default: // Nothing
        }

        $('.' + side + '-circle-overlay').addClass('active');

        $(this).addClass('active');

    });

    $(document).on('mouseleave', '.plexuss-mobile-ad-frontpage-right-side .frontpage-circle-img', function(e) {
        var side = $(this).prop('class').replace(/\s+|frontpage-circle-img|-circle|active/g, '');
        var that = $(this);
        
        $('.' + side + '-circle-overlay').removeClass('active');

        setTimeout(function() {
            that.removeClass('active');
        }, 300);
    });

    $(document).on('click', '.frontpage-bottom-bar-container .send-app-store-sms-button', function(e) {
        $('#send-mobile-app-sms-modal').foundation('reveal', 'open');
    });

});//end of document ready

function isChatEnabled(){
    var path = window.location.pathname,
        webinar_live = $('#get_started').data('webinar');
        
    if( path === '/chat' && !webinar_live ) triggerchat();
}

function triggerchat(){
    $('.frontpage-custom-icon-bar .item[href="#chat_side_bar_section"]').trigger('click');
}

//trigger 'chat with colleges' click when 'colleges in our network' button is clicked
$(document).on('click', '.find-col-in-network-btn', function(e){
    e.preventDefault();
    $('.frontpage-custom-icon-bar .item[href="#chat_side_bar_section"]').trigger('click');
});

//handle the redirect of virtual tour click
function openVirtualTourOnRedirect( elem ){

    //adding param to college page url
    var redirect_to_virtual_tour = $(elem).attr('href');
    redirect_to_virtual_tour += '?showtour=true';

    //redirect to college page with param
    window.location = redirect_to_virtual_tour;
}

//collapse/close side bar sections except the opening section
function collapseSideBarSection(){

    //remove active classes
    $('.frontpage-custom-icon-bar a').removeClass('active-custom-side-bar');
    $('.frontpage-custom-icon-bar a label').removeClass('active-custom-side-bar-label');

    //remove all green icons
    $('.frontpage-custom-icon-bar a.item .sidebar-icon-active').hide();
    $('.frontpage-custom-icon-bar a.item .sidebar-icon-inactive').show();

    //hide all sections and show opening section
    $('.frontpage-side-bar-sections').hide();
    $('#frontpage_opening_side_bar_section').show();
}


//build serialized url of search results, also validate to see if at least one form field was entered, if not, don't redirect
function submitFrontpageAdvSearch(){
	var advSearch_country = $('.frontpage-country').val() || '';
    var advSearch_state = $('.frontpage-state').val() || '';
    var advSearch_city = $('.frontpage-city').val() || '';
    var advSearch_zip = $('.frontpage-zip').val();
    var advSearch_campussetting = $('.frontpage-campussetting').val();
    var advSearch_housing = $('.frontpage-housing').val();
    var advSearch_degree = $('.frontpage-degree').val();
    var advSearch_major = $('.frontpage-major').val();
    var advSearch_religion = $('.frontpage-religion').val();
    var advSearch_reading_min = $('.frontpage-reading-min').val();
    var advSearch_reading_max = $('.frontpage-reading-max').val();
    var advSearch_math_min = $('.frontpage-math-min').val();
    var advSearch_math_max = $('.frontpage-math-max').val();
    var advSearch_composite_min = $('.frontpage-composite-min').val();
    var advSearch_composite_max = $('.frontpage-composite-max').val();
    var advSearch_tuition = $('#slider-tuition').val();
    var advSearch_enrollment = $('#slider-enrollment').val();
    var advSearch_acceptancerate = $('#slider-acceptancerate').val();
    var department = $('.adv-c-s-majors-select').val();
    var major = $('.majors-selection-box').val();

    var advSearch_serialization = '';
    var validate_advSearch_form = false;

    advSearch_serialization += 'search?country=' + advSearch_country;
	advSearch_serialization += '&state=' + advSearch_state;
    advSearch_serialization += '&city=' + advSearch_city;
    advSearch_serialization += '&zipcode=' + advSearch_zip;
    advSearch_serialization += '&degree=' + advSearch_degree;

    if( $('.frontpage-housing').is(':checked') ){

        //if housing checkbox is checked, then add campus housing to serialized url, otherwise don't
        advSearch_serialization += '&campus_housing=' + advSearch_housing;
    }
    
    advSearch_serialization += '&locale=' + advSearch_campussetting;
    advSearch_serialization += '&tuition_max_val=' + parseInt(advSearch_tuition[1]);
    advSearch_serialization += '&enrollment_min_val=' + advSearch_enrollment[0];
    advSearch_serialization += '&enrollment_max_val=' + advSearch_enrollment[1];
    advSearch_serialization += '&applicants_min_val=' + advSearch_acceptancerate[0];
    advSearch_serialization += '&applicants_max_val=' + advSearch_acceptancerate[1];
    advSearch_serialization += '&min_reading=' + advSearch_reading_min;
    advSearch_serialization += '&max_reading=' + advSearch_reading_max;
    advSearch_serialization += '&min_sat_math=' + advSearch_math_min;
    advSearch_serialization += '&max_sat_math=' + advSearch_math_max;
    advSearch_serialization += '&min_act_composite=' + advSearch_composite_min;
    advSearch_serialization += '&max_act_composite=' + advSearch_composite_max;
    // advSearch_serialization += '&religious_affiliation=' + advSearch_religion;
    advSearch_serialization += '&type=college';
    advSearch_serialization += '&term=';
    advSearch_serialization += '&department=' + department;
    advSearch_serialization += '&imajor=' + (major ? major : '');

    //check if at least one form field was entered/changed
    $('.frontpage-adv-search-form input[type="text"], .frontpage-adv-search-form select').each(function(){

        if( $(this).val() !== '' ){
            validate_advSearch_form = true;
        }
    });

    //if none of the text/select input fields were filled out, then check if the range sliders have moved
    if( !validate_advSearch_form ){

        //check if the max value of any of the range sliders is greater than one, to see if it was moved by user
        $('.frontpage-adv-search-form #slider-tuition, .frontpage-adv-search-form #slider-enrollment, .frontpage-adv-search-form #slider-acceptancerate').each(function(){
            
            if( $(this).val()[1] > 0 ){
                validate_advSearch_form = true;
            }
        });

        //if form is still not filled at this point, check if the checkbox was checked
        if( !validate_advSearch_form ){

            if( $('.frontpage-housing').is(':checked') ){
                validate_advSearch_form = true;
            }
        }

    }

    //if at least one form field was entered/checked/edited, then build serialization and redirect with search results
    if( validate_advSearch_form ){
        window.location.href = advSearch_serialization;
    }else{
        $('.frontpage-adv-search-form .advSearch-error-row').fadeIn(300);
        $('#find_a_college_side_bar_section').animate({
            scrollTop: $('#find_a_college_side_bar_section').height()
        }, 1000);
    }
}


function ajaxCarouselItems(carouselName, itemsAmount, isPrevious){
    var _content = '';
    var _thisOwl = $("."+carouselName+ "-ajax");
    var isObjEmpty = true;


    $.ajax({
        url: 'ajax/homepage/getCarouselItems/'+carouselName+'/'+itemsAmount,
        dataType: 'json',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    })
    .done(function(result) {
        
        $.each(result, function(index, el) {
            isObjEmpty = false;

            if( carouselName == 'message-a-college-container-unique' ){
                if( el.school_bk_img ) _content += '<div class="item effect-sadie text-center" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/'+el.school_bk_img+', (default)], [https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/'+el.school_bk_img+', (large)]">';
                else _content += '<div class="item effect-sadie text-center" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/no-image-default.png, (default)], [https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/no-image-default.png, (large)]">';

                _content +=     '<div class="rep">';
                _content +=         '<div class="name text-center"><b>'+el.fname + ' ' + el.lname+'</b></div>';

                if( el.title ) _content += '<div class="title text-center">'+el.title+'</div>';
                else _content +=         '<div class="title text-center">College Representative</div>';

                if( el.member_since ) _content +='<div class="yr text-center">Since '+el.member_since.split('-')[0]+'</div>';
                else _content +=    '<div class="yr text-center hidden">N/A</div>';

                if( el.profile_img_loc ) _content +='<div class="pic text-center" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'+el.profile_img_loc+', (default)], [https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'+el.profile_img_loc+', (large)]"></div>';
                else _content +=    '<div class="pic text-center has-default" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/default_avatar.png, (default)]"></div>';

                if( el.school_name.length > 45 ) _content += '<div class="school text-center"><b>'+el.school_name.substr(0,45)+'</b></div>';
                else _content +=    '<div class="school text-center"><b>'+el.school_name+'</b></div>';

                _content +=     '</div>';
                    
                _content +=     '<figure>';
                _content +=         '<figcaption>';
                _content +=             '<div class="pin-back pin-back-img msg-col">';
                _content +=                 '<div class="background-college-logo" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'+el.logo_url+', (default)], [https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'+el.logo_url+', (large)]"></div>';
                _content +=                 '<div class="name text-center"><b>'+el.fname+' '+el.lname+'</b></div>';
                if( el.title ) _content +=       '<div class="title text-center">'+el.title+'</div>';
                else _content +=                 '<div class="title text-center">College Representative</div>';

                if( el.member_since ) _content +='<div class="yr text-center">Since '+el.member_since.split('-')[0]+'</div>';
                else _content +=            '<div class="yr text-center hidden">N/A</div>';

                if( el.description ) _content += '<div class="descr">'+el.description+'</div>';
                else _content +=            '<div class="descr"></div>';
                _content +=             '</div>';

                _content +=             '<a href="/portal/messages/'+el.college_id+'/college" class="college-pin-link">';
                _content +=                 '<div class="send-msg-btn">SEND MESSAGE</div>';
                _content +=             '</a>';
                _content +=         '</figcaption>';
                _content +=     '</figure>';
                _content += '</div>';
            }else if (carouselName == 'near-you-carousel-container-unique'){
                _content +=    '<div class="item effect-sadie text-center">';
                _content +=         '<div class="background-college-logo" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'+el.logo_url+', (default)], [https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'+el.logo_url+', (large)]"></div>';
                _content +=         '<div class="college-pin-school-name">'+el.school_name+'</div>';
                _content +=          '<img class="pin-page-turn-img" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/page-corner-curl_40x40.png" alt="">';
                _content +=          '<figcaption>';
                _content +=                  '<div class="pin-back pin-back-img" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/'+el.img_url+', (default)], [https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/'+el.img_url+', (large)]">';
                _content +=                  '<!--<img class="pin-back" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/'+el.img_url+'" alt="">-->';
                _content +=                  '<a href="/college/'+el.slug+'" class="college-pin-link">';
                _content +=                      '<div class="row college-pin-footer-container">';
                _content +=                          '<div class="column small-4 text-left">';
                _content +=                              '<div class="top-rank-pin-rank-icon text-center"><strong>#'+el.rank+'</strong></div>';
                _content +=                         '</div>';
                _content +=                          '<div class="column small-8 pin-item-footer">';
                _content +=                              '<div>'+el.distance+' miles away</div>';
                _content +=                              '<div>SEE COLLEGE</div>';
                _content +=                          '</div>';
                _content +=                      '</div>';
                _content +=                  '</a>';
                _content +=                  '</div>';
                _content +=          '</figcaption>';
                _content +=      '</div>';                
            }else if(carouselName == 'top-ranking-carousel-container-unique'){
                _content +=       '<div class="item effect-sadie text-center">';
                _content +=         '<div class="background-college-logo" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'+el.logo_url+', (default)], [https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/'+el.logo_url+', (large)]"></div>';
                _content +=            '<div class="college-pin-school-name">'+el.school_name+'</div>';
                _content +=            '<img class="pin-page-turn-img" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/page-corner-curl_40x40.png" alt="">';
                _content +=            '<figcaption>';
                _content +=                '<div class="pin-back pin-back-img" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/'+el.img_url+', (default)], [https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/'+el.img_url+', (large)]">';
                _content +=                    '<!--<img class="pin-back" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/'+el.img_url+'" alt="">-->';
                _content +=                    '<a href="/college/'+el.slug+'" class="college-pin-link">';
                _content +=                        '<div class="row college-pin-footer-container">';
                _content +=                            '<div class="column small-4 text-left">';
                _content +=                                '<div class="top-rank-pin-rank-icon text-center"><strong>#'+el.rank+'</strong></div>';
                _content +=                             '</div>';                            
                _content +=                             '<div class="column small-8 pin-item-footer">';
                _content +=                                 '<div>'+el.distance+' miles away</div>';
                _content +=                                 '<div>SEE COLLEGE</div>';

                _content +=                             '</div>';
                _content +=                         '</div>';
                _content +=                     '</a>';
                _content +=                 '</div>';
                _content +=             '</figcaption>';
                _content +=         '</div>';

            }else if(carouselName == 'virtual-tours-carousel-container-unique'){
                _content +=            '<div class="item effect-sadie text-center pin-back-img" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/'+el.img_url+', (default)], [https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/'+el.img_url+', (large)]">';
                _content +=                '<!--<img class="college-pin-virtualtour-image" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/'+el.img_url+'" alt="">-->';
                _content +=                '<div class="college-pin-virtualtour-school-name">';
                _content +=                    '<div class="vt-school-name text-left">'+el.school_name+'</div>';
                _content +=                '</div>';
                                
                                
                _content +=                '<figcaption>';
                _content +=                        '<div class="row college-pin-footer-container college-news-pin-container">';
                _content +=                            '<div class="column small-4 news-pin-inner-container">';
                _content +=                                 '<div class="text-center news-pin-hover-icon-img">';
                _content +=                                     '<a href="/college/'+el.slug+'" onclick="openVirtualTourOnRedirect(this); return false;">';
                _content +=                                         '<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/tour-icon-for-hover.png" alt="">';
                _content +=                                     '</a>';
                _content +=                                 '</div>';
                _content +=                                '<div class="text-center news-pin-hover-desc">';
                _content +=                                    '<a href="/college/'+el.slug+'" class="college-pin-link" onclick="openVirtualTourOnRedirect(this); return false;"><div>VIEW TOUR</div></a>';
                _content +=                                '</div>';
                _content +=                            '</div>';
                _content +=                        '</div>';
                _content +=                '</figcaption>';
                _content +=            '</div>';

            }else if(carouselName == 'quad-article-carousel-container-unique'){
                if( el.video ){
                    _content +=            '<div class="item effect-sadie text-center news-items" data-interchange="['+el.img_url+', (default)]">';
                }else{
                    _content +=            '<div class="item effect-sadie text-center news-items" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/'+el.img_url+', (default)]">';
                }
                _content +=                '<div class="filler"></div>';
                _content +=                '<div class="college-pin-news-desc text-left">'+el.title+'</div>';
                _content +=                '<figcaption>';
                                        
                _content +=                        '<div class="row college-pin-footer-container college-news-pin-container">';
                _content +=                            '<div class="column small-4 news-pin-inner-container">';
                _content +=                                 '<div class="text-center news-pin-hover-icon-img">';
                if (el.is_essay == 0) {
                    _content +=                                     '<a href="/news/article/'+el.slug+'" class="college-pin-link">';
                }else{
                    _content +=                                     '<a href="/news/essay/'+el.slug+'/1" class="college-pin-link">';
                }
                _content +=                                         '<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/news-icon-for-hover.png" alt="">';
                _content +=                                     '</a>';
                _content +=                                 '</div>';
                _content +=                                '<div class="text-center news-pin-hover-desc">';
                if (el.is_essay == 0) {
                    _content +=                                    '<a href="/news/article/'+el.slug+'" class="college-pin-link">';
                }else{
                    _content +=                                    '<a href="/news/essay/'+el.slug+'/1" class="college-pin-link">';
                }
                if( el.video ){
                    _content +=                                        '<div>WATCH VIDEO</div>';
                }else{
                    _content +=                                        '<div>SEE FULL ARTICLE</div>';
                }
                _content +=                                    '</a>';
                _content +=                                '</div>';
                _content +=                            '</div>';
                _content +=                        '</div>';
                _content +=                '</figcaption>';
                _content +=            '</div>';

            }



        });

        _thisOwl.data('owlCarousel').addItem(_content);


        // Make sure the user is not going to the previous items, and there's more items that has been added.
        if(isObjEmpty && !isPrevious){
            _thisOwl.trigger('owl.jumpTo', 0);
        }else if(!isPrevious){
            _thisOwl.trigger('owl.jumpTo', itemsAmount -6);
        }
        
        $(document).foundation('interchange', 'reflow');

    })
    .fail(function() {
        // console.log("error on ");
    });
    
}

function populateCityBasedOnState(stateAbbr){
    $.getJSON("ajax/homepage/getCityByState/"+stateAbbr, function(result) {
        var options = $(".frontpage-adv-search-form .frontpage-city");
        options.find('option').remove();  
        options.append($("<option />").val('').text('Select..'));
        $.each(result, function(key, value) {
            options.append($("<option />").val(value).text(value));
        });
    });
}

function comparisionAutocomplete(txtID,txthiddenId)
{
    var item_urlslug=$('#item_urlslug').val();
    $("#"+txtID).autocomplete();
    $(function() {  
    
        var  urlslug='';
        $("#owl-compare").find("[data-slugs]").each(function(index, element) {          
        var slug = $(element).data("slugs");
        if(slug !== '')
            { urlslug +=slug + ','; }
        });
    
        $("#"+txtID).autocomplete({
            source:"/getslugAutoCompleteData?type=colleges&urlslug="+urlslug,
            minLength: 1,
            select: function(event, ui) {
                $(this).data('hsname', ui.item.label);
                $('#'+txthiddenId).val(ui.item.slug);
            }
        });
        $("#"+txtID).change(function() {
            var _this = $(this);
            if (_this.val() !== _this.data('hsname')) {
                _this.val('');
            }
        });
    }); 
    
}

function submitBattleSearch(a,b,c){
    var val1 = $('#'+a).val().substr(0, $('#'+a).val().lastIndexOf('-')).trim().replace(/\s+/g, '-');
    var val2 = $('#'+b).val().substr(0, $('#'+b).val().lastIndexOf('-')).trim().replace(/\s+/g, '-');
    var val3 = $('#'+c).val().substr(0, $('#'+c).val().lastIndexOf('-')).trim().replace(/\s+/g, '-');

    var slug = val1+val2+val3;
    
    val1 = val1!==''?val1+',':'';
    val2 = val2!==''?val2+',':'';
    val3 = val3!==''?val3+',':'';
    
    if( val1 === '' && val2 === '' && val3 === '' ){ 
        alert('Please select at least one school');
    }else{
        window.location='/comparison/?UrlSlugs='+val1+val2+val3;
    }


}

function closeChromeExtension() {
        document.getElementById('chrome-extension-container').classList.add('hidden');
        document.getElementsByClassName('fb-likes-container')[0].classList.add('no-extension');
} 

document.getElementsByClassName('close-chrome-extension-button')[0].addEventListener('click', function(event) {
        event.preventDefault();
        event.stopPropagation();

        closeChromeExtension();
});

window.onload = function() {
    var isChrome = /Chrome/.test(navigator.userAgent) && /Google Inc/.test(navigator.vendor);
    
    if (isChrome) {
        document.getElementById('chrome-extension-container').classList.remove('hidden');
        document.getElementsByClassName('fb-likes-container')[0].classList.remove('no-extension');
    }
}
/*** end of phase 2 of new front page js ***/