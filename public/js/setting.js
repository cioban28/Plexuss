/***********************************************************************
 *===================== NAMESPACED VARIABLES ===========================
 ***********************************************************************
 * Holds namespaced variables for settings page
 */
Plex.settings = {
    slideEffectSpeed: 500,
    num_of_contacts: 0,
    currently_checked_count: 0,
    contact_array : [],
    contactObject : {
        contact_name: '',
        contact_email: '',
    },
    inviteSuccessMsg : {
        textColor: '#fff',
        backGroundColor : 'green',
        msg: 'Invitation(s) have been sent!',
        type: 'soft',
        dur : 5000
    },
    inviteErrorMsg : {
        textColor: '#fff',
        bkg : '#ee0909',
        msg: 'Oops. Looks like something went wrong. Please try again.',
        type: 'soft',
        dur : 5000
    },
    portalDeactivatedMsg : {
        textColor: '#fff',
        backGroundColor : 'green',
        msg: '',
        type: 'soft',
        dur : 5000
    },
    managePortalErrorMsg : {
        textColor: '#fff',
        bkg : '#ee0909',
        msg: 'Oops. Looks like portal is not valid. Please try again',
        type: 'soft',
        dur : 5000
    },
    savedSettingInfoMsg : {
        textColor: '#fff',
        backGroundColor : 'green',
        msg: 'User information has been saved!',
        type: 'soft',
        dur : 5000
    },
    deleteUserMsg : {
        textColor: '#fff',
        backGroundColor : 'green',
        msg: 'User has been removed!',
        type: 'soft',
        dur : 5000
    },
    portalCreatedMsg : {
        textColor: '#fff',
        backGroundColor : 'green',
        msg: 'Portal has been created!',
        type: 'soft',
        dur : 5000
    },
    portalRenamedMsg : {
        textColor: '#fff',
        backGroundColor : 'green',
        msg: 'Portal has been renamed!',
        type: 'soft',
        dur : 5000
    },
    userAddedMsg : {
        textColor: '#fff',
        backGroundColor : 'green',
        msg: 'User has been added!',
        type: 'soft',
        dur : 5000
    },
    userDeletedMsg : {
        textColor: '#fff',
        backGroundColor : 'green',
        msg: 'User has been deleted!',
        type: 'soft',
        dur : 5000   
    },
    emailInvalidMsg : {
        textColor: '#fff',
        bkg : '#ee0909',
        msg: 'Email is not valid, please try again!',
        type : 'soft',
        dur  : 5000 
    },
    usersAccessInvalidMsg : {
        textColor: '#fff',
        bkg : '#ee0909',
        msg: 'At least one access should be chosen!',
        type : 'soft',
        dur : 5000
    },
    saveNotifications : {
        textColor: '#fff',
        bkg : '#24b26b',
        msg: 'Notifications successfully updated!',
        type : 'soft',
        dur : 5000
    },
    removedPhone : {
        textColor: '#fff',
        bkg : '#24b26b',
        msg: 'Successfully removed phone number!',
        type : 'soft',
        dur : 5000
    },
    is_student: false,
    payment_plan: '',
    payment_price: '',
    edit_payment_clicked: false,
    textmsg_phone: '',
    textmsg_tier: '',
    textmsg_plan: '',
    textmsg_price: '',
    textmsg_ready_to_send: 0,
    phone_list_available: $('.phone-list-available'),
    search_number: $('.search-number'),
    input_area_code: $('.input-area-code'),
    que_ans: $('.qa'),
    have_validated_phone_already: false,
};

$(document).foundation('tooltip', 'reflow');

$(document).tooltip({
    content: function () {
        return $(this).prop('title');
    }
});

// open  reveal Model
$(document).foundation({
    reveal: {
        animation: 'fadeAndPop',
        delay: 100,
        animation_speed: 300,
        close_on_background_click: true,
        close_on_esc: true,
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

$(document).foundation({
    abide : {
        patterns: {
            passwordpattern: /^(?=[^\d_].*?\d)\w(\w|[!@#$%*]){7,20}/
        }
    }
});


/* ///////////////////// new settings js - start of invite friends - start \\\\\\\\\\\\\\\\\\\\\\ */
$(document).ready(function(){

    Plex.settings.checkParam();
    Plex.settings.hideElem($('.plex-billing-price-section'));

    //in case user get redirected to contact list page, on load hide the manage imported contacts link
    if( $('#invite-from-contact-list').hasClass('hide-sub-section') ){
        $('.manage-imported-contacts-link').show();
    }else{
        $('.manage-imported-contacts-link').hide();
    }

    //on load, deselect all checkboxes
    Plex.settings.deselectCheckboxes();
    Plex.settings.updateContactCount();

    var elems = document.getElementsByClassName('users-bg');
    for(i = 0; i < elems.length; i++){
       elems[i].style.backgroundColor = bgColorGen();
    }

    init_pi_fndtn();

    // bind_profile_picture_submit("{{ $data['ajaxtoken'] }}");
    // $('.users-access-list').styleddropdown();

    Plex.settings.getUsersAutocomplete();

    var dataSet = [
        [ "5421", "2011/04/25", "System Architect", "$320,800", "paid"],
        [ "5422", "2011/04/26", "Software Engineer", "$321,800", "paid"],
        [ "5423", "2011/04/27", "information server", "$322,800", "paid"],

    ];

    $('#invoice-history').DataTable( {
        data: dataSet,
        columns: [
            { title: "Order No." },
            { title: "Billing Data" },
            { title: "Description" },
            { title: "Amount" },
            { title: "Status" }
        ],
        "paging":   false,
        "ordering": true,
        "bFilter":    false, 
        "bInfo":      false,
        "columnDefs": [
            {"className": "dt-center", "targets": "_all"},
            {"orderable": false, targets: [2,3,4]},
        ],

    } );

    Plex.settings.setUserType();
    
});//end of document ready

$(document).on('click', '.current-phone-view .edit', function(){
    $('#back-to-text-notifications').show();
    $('#has-phone-to-setup-notifications').hide();
    $('#no-phone-get-phone-info').show().find('.enter-confirmation-code').hide().parent().find('.enter-phone-form').show();
});

$(document).on('click', '.current-phone-view .remove', function(){
    $('#phone-removal-modal').foundation('reveal', 'open');
});

$(document).on('click', '#yes-delete-my-phone-btn', function(){
    Plex.settings.deletePhone();
});

Plex.settings.deletePhone = function(){
    $.ajax({
        url: '/settings/save/deleteUsersPhoneNumber',
        type: 'POST',
        data: {remove: 1},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data){
            topAlert(Plex.settings.removedPhone);
            $('#phone-removal-modal').foundation('reveal', 'close');
            window.location.href = '/settings/text';
        },
        error: function(err){
            console.log('err: ', err);
        }
    });
};

$(document).on('click', '#back-to-text-notifications', function(){
    $('#back-to-text-notifications').hide();
    $('#no-phone-get-phone-info').hide();
    $('#has-phone-to-setup-notifications').show();
});

$(document).on('click', '#dialing-codes-toggler, #edited-dialing-codes-toggler', function(){
    if( $(this).hasClass('edited') ) $('#edited-dialing-codes-dropdown').slideToggle(200);
    else $('#dialing-codes-dropdown').slideToggle(200);
});

$(document).on('click', '#dialing-codes-dropdown .codes, #edited-dialing-codes-dropdown .codes', function(){
    var edited = $(this).parent().hasClass('edited'), 
        id = edited ? 'edited-phone-field' : 'phone-field',
        code = $(this).data('code');

    if( edited ) $('#edited-dialing-codes-toggler .selected-code > span').html(code);
    else $('#dialing-codes-toggler .selected-code > span').html(code);

    Plex.settings.validatePhoneWithTwilio(id);

    if( edited ) $('#edited-dialing-codes-dropdown').slideUp(200);
    else $('#dialing-codes-dropdown').slideUp(200);
});

$(document).on('click', '.notif-details', function(){
    var id = $(this).attr('id');
    $('#'+id+'-dropdown').slideToggle();
});

$(document).on('change', '.notif-row input[type="checkbox"]', function(){
    var _this = $(this), switches = null, all_on = false;

    if( _this.hasClass('toggle-all') ){
        _this.closest('.notif-row').find('.notif-dropdown input').prop('checked', _this.is(':checked'));
    }else{
        if( !_this.is(':checked') ){
            _this.closest('.notif-row').find('.toggle-all').prop('checked', false);
        }else{
            switches = _this.closest('.notif-dropdown').find('input');
            all_on = false;

            $.each(switches, function(){
                if( $(this).is(':checked') ) all_on = true;
                else{
                    all_on = false;
                    return false;
                }
            });

            if( all_on ) _this.closest('.notif-row').find('.toggle-all').prop('checked', true);
        }
    }
});

$(document).on('click', '.update-phone', function(){
    var edit_field = $('.edit-phone-wrapper .edit-phone'),
        phone_num = $('.edit-phone-wrapper .number');

    if( phone_num.is(':visible') ){
        phone_num.hide();
        edit_field.show();
    }else{
        phone_num.show();
        edit_field.hide();
    }
});

$(document).on('click', '.back-to-phone-enter-btn', function(e){
    e.preventDefault();
    Plex.settings.backToPhoneInfoSection();
});

$(document).on('click', '.resend-code-btn', function(e){
    e.preventDefault();
    Plex.settings.sendCode('resend');
});

$(document).on('keyup', '#phone-field, #edited-phone-field', function(){
    var id = $(this).attr('id');
    Plex.settings.validatePhoneWithTwilio(id);
});

Plex.settings.openConfirmationSection = function(){
    $('.enter-phone-form').hide();
    $('.enter-confirmation-code').show();
};

Plex.settings.backToPhoneInfoSection = function(){
    $('.enter-phone-form').show();
    $('.enter-confirmation-code').hide();
};

Plex.settings.openTextNotificationsSection = function(){
    $('#no-phone-get-phone-info').hide();
    $('#has-phone-to-setup-notifications').show();
};

Plex.settings.validatePhoneWithTwilio = function(id){
    var phone = id === 'phone-field' ? $('#phone-field').val() : $('#edited-phone-field').val(),
        code = id === 'phone-field' ? $('#calling_code').html() : $('#edited_calling_code').html(),
        full_phone = code.trim()+phone.trim();

    $.ajax({
        url: '/phone/validatePhoneNumber',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: {phone: full_phone},
        type: 'POST'
    }).done(function(data){
        if( data && !data.error ){
            if( id === 'phone-field' ) $('.phone-err').hide();
            else $('.edit-phone-err').hide();

            var split = data.phoneNumber.substr(data.country_phone_code.length, data.phoneNumber.length);
            var formatted = data.country_phone_code + ' ' + split;

            $('#entered_phone_num').html(formatted);
            $('#formatted_hidden_phone, #edited_formatted_hidden_phone').val(formatted);
            $('#edited-phone-field').val(split);
            $('#edited-calling_code').val(data.country_phone_code); 

            //only get in here if user already has a phone number, but is not opted in for texting
            //and the user clicked next to opt in, but we haven't validated their number yet, so if we 
            //are here, then that means, their phone number is valid and we can save their validated
            //phone number and opt them into texting.
            if( !Plex.settings.have_validated_phone_already ){
                Plex.settings.savePhoneNumberAndOptInTexting($('.to-confirmation-section-btn'));
            }
        }else{
            if( id === 'phone-field' ) $('.phone-err').show();
            else $('.edit-phone-err').show();
        }
        
        Plex.settings.have_validated_phone_already = true;
    });


};

//send code to phone number provided
Plex.settings.sendCode = function(who){
    var num = !who ? $('#phone-field').val() : $('#edited-phone-field').val(),
        code = !who ? $('#calling_code').html() : $('#edited_calling_code').html();
    $.ajax({
        url: '/get_started/sendPhoneConfirmation',
        type: 'POST',
        data: {phone: num.trim(), dialing_code: code.trim()},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data){
            try {
                data = JSON.parse(data);
            } catch (e) {
                // For some reason the response has a <pre></pre> appended to it.
                // This will catch and find the object within the string response.
                // This can be removed later if we find why the <pre> is being prepended.
                var stringify = data.match(/{.*}/) && data.match(/{.*}/).join('');

                data = JSON.parse(stringify);
            }

            if( data && data.response === 'success' ){
                // console.log('success on sending');
            }else{
                console.log(data.error_message);
                // console.log('error on sending');
                Plex.settings.confirmCodeError(who, data.error_message);
            }
        },
        error: function(err){
            console.log(err);
        }
    });
};

Plex.settings.confirmCodeError = function(who, error_msg){
    var resend = $('.resend-err'),
        code = $('.code-err'),
        reached_limit = $('.reached-limit-err'),
        err = resend;
    
    if( who === 'code' ) err = code;

    if( error_msg && error_msg.includes('reached the maximum') ) {
        reached_limit.html(error_msg);
        err = reached_limit;
    }

    err.show();
    setTimeout(function(){
        err.hide();
    }, 12000);
};

//post code and confirm validity
$(document).on('click', '.confirm-code-btn', function(e){
    e.preventDefault();
    
    var form = $(this).closest('form'),
        formdata = new FormData( form[0] );

    $.ajax({
        url: '/get_started/checkPhoneConfirmation',
        type: 'POST',
        data: formdata,
        enctype: 'multipart/form-data',
        contentType: false,
        processData: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data){
            var data = JSON.parse(data);
            if( data && data.response === 'success' ){
                var phone = $('#edited-phone-field').val(),
                    code = $('#edited_calling_code').html();

                Plex.settings.optInUser();
                $('#back-to-text-notifications').hide();
                $('.current-phone-view .phone').html('+'+code+' '+phone);
                Plex.settings.openTextNotificationsSection();
            }
            else Plex.settings.confirmCodeError('code');
        },
        error: function(err){
            console.log(err);
        }
    });
});

Plex.settings.optInUser = function(){
   $.ajax({
        url: '/settings/save/optInUserForText',
        type: 'POST',
        data: {opt_in: 1},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data){
            // console.log(data);
        },
        error: function(err){
            console.log(err);
        }
    }); 
};

//post phone num and address details
$(document).on('click', '.to-confirmation-section-btn', function(e){
    e.preventDefault(); 

    if( Plex.settings.have_validated_phone_already ){
        Plex.settings.savePhoneNumberAndOptInTexting($(this));
    }else{
        Plex.settings.validatePhoneWithTwilio('phone-field');
    }
});

Plex.settings.savePhoneNumberAndOptInTexting = function(elem){
    var form = $(elem).closest('form'),
        formdata = new FormData( form[0] ),
        phone = $('#phone-field'),
        phone_err = $('.phone-err'),
        terms = $('#terms-agree');

    if( phone.val() && !phone_err.is(':visible') && terms.is(':checked') ){
        $('.phone-form-err').hide();
        $.ajax({
            url: '/settings/save/savePhoneInfo',
            type: 'POST',
            data: formdata,
            enctype: 'multipart/form-data',
            contentType: false,
            processData: false,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(data){
                Plex.settings.sendCode();
                Plex.settings.openConfirmationSection();
            },
            error: function(err){
                console.log(err);
            }
        });
    }else{
        $('.phone-form-err').show();
    }
}

$(document).on('click', '.save-edited-phone-btn', function(e){
    e.preventDefault();

    var new_phone = $('#edited-phone-field').val(),
        new_code = $('#edited_calling_code').html(),
        edit_error = $('.edit-phone-err');

    if( !$('.edit-phone-err').is(':visible') ){
        edit_error.hide();
        $.ajax({
            url: '/settings/save/saveEditedPhoneNumber',
            type: 'POST',
            data: {phone: new_phone.trim(), dialing_code: new_code.trim()},
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(data){
                $('.edit-phone-wrapper .edit-phone').hide();
                $('.edit-phone-wrapper .number').show();
            },
            error: function(err){
                console.log(err);
            }
        });
    }else{
        edit_error.show();
    }
        
});

$(document).on('click', '.save-notifications-btn.button', function(e){
    e.preventDefault();

    var form = $(this).closest('form');
    var notification_data = Plex.settings.getNotifcationData(form);
    $.ajax({
        url: '/settings/save/saveEmailNotifications',
        type: 'POST',
        data: notification_data,
        // enctype: 'multipart/form-data',
        // contentType: false,
        // processData: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data){
            topAlert(Plex.settings.saveNotifications);
        },
        error: function(err){
            console.log(err);
        }
    });
});

$(document).on('click', '.save-data_preferences-btn.button', function(e){
    e.preventDefault();

    var form = $(this).closest('form');
    var notification_data = Plex.settings.getDataPreferences(form);

    $.ajax({
        url: '/settings/save/saveDataPreferences',
        type: 'POST',
        data: notification_data,
        // enctype: 'multipart/form-data',
        // contentType: false,
        // processData: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data){
            topAlert(Plex.settings.saveNotifications);
        },
        error: function(err){
            console.log(err);
        }
    });
});

Plex.settings.getNotifcationData = function(form){
    var fields = $(form).find('input[type="checkbox"]'), 
        type = $(form).find('input[type="hidden"]'),
        _this = null,
        data = {};

    data[type.attr('name')] = type.val();

    $.each(fields, function(){
        _this = $(this);
        if( !_this.hasClass('toggle-all') ) data[_this.attr('name')] = _this.is(':checked');
    });

    return data;
};

Plex.settings.getDataPreferences = function(form){
    var fields = $(form).find('input[type="checkbox"]'), 
        type = $(form).find('input[type="hidden"]'),
        _this = null,
        data = {};

    data[type.attr('name')] = type.val();

    $.each(fields, function(){
        _this = $(this);
        data[_this.attr('name')] = _this.is(':checked');
    });

    return data;
};

Plex.settings.setUserType = function(){
    Plex.settings.is_student = $('#plex-student-billing').is(':visible');
};

Plex.settings.checkParam = function(){
    var url = window.location.href, param = null, parsed = null;

    if( url.indexOf('?') !== -1 ){
        param = url.substr( url.indexOf('?') );
        parsed = param.slice(1);
        parsed = parsed.split('=');

        switch( parsed[0] ){
            case 'portals':
                if( parseInt(parsed[1]) === 1 ) $('.manage-portals-link').trigger('click');
                break;
            case 'upgrade':
                if( parseInt(parsed[1]) === 1 ) $('.choose-plan-btn[data-plan="onetime"]').trigger('click');
                else if( parseInt(parsed[1]) === 2 ) $('.choose-plan-btn[data-plan="monthly"]').trigger('click');
                else Plex.settings.showPlansIfNotPremium();
                break;
            case 'plans' :
                if( parseInt(parsed[1]) === 1 ) $('.change-plan-btn').trigger('click');
                break;
            case 'invoices' :
                if( parseInt(parsed[1]) === 1 ) $('.billing-tab.invoices').trigger('click');
                break;
            case 'pricing' :
                if( parseInt(parsed[1]) === 1 ) $('.billing-tab.pricing').trigger('click');
                break;
            case 'sendmsg' :
                if( parseInt(parsed[1]) != 0) {
                    $('.change-plan-btn').trigger('click');
                    Plex.settings.textmsg_ready_to_send = parseInt(parsed[1]);
                }
                break;
            default:
                break;
        }
    }else{
        Plex.settings.showPlansIfNotPremium();
    }
}


//triggers when user clicks anywhere on the DOM
$(document).on('click', function(e){
    Plex.settings.outsideOfDivClickToClose(e, '.invite-preview-modal', '.preview-invite-hover-arrow');
});


//triggers when individual invite friend checkbox is checked or unchecked
$(document).on('change', '.invite-friend-option-checkbox', function(){
    $('.send-invites-btn').removeAttr('disabled');
    $('.send-invites-error-msg').slideUp(250);
    Plex.settings.makeCheckboxActive(this);
    Plex.settings.updateSelectedContactsCount();
})


//triggers when 'select all' checkbox check/not checked is changed
$(document).on('change', '.select_all_invites_checkbox', function(){
    var _this = $(this);
    Plex.settings.selectAllInviteChoices(_this);
    Plex.settings.updateSelectedContactsCount();
})


//submit invidivual email invite validation - remove disabled
$('.individual-email-value').focus(function(){
    $('.invite-submit-email-btn').removeAttr('disabled');   
});


/***** submit individual email button click - start *****/
$(document).on('click', '.invite-submit-email-btn', function(){
    Plex.settings.submitIndividualEmailInvite();
});
/***** submit individual email button click - end *****/


/***** send invites action - start *****/
$(document).on('click', '.send-invites-btn', function(){
    Plex.settings.sendInvites();
});
/***** send invites action - end *****/


/***** preview invite link click toggle - start *****/
$(document).on('click', '.plex-invite-friends-section .preview-invite', function(e){

    if( $('.invite-preview-modal').css('display') === 'none' ){
        $('.invite-preview-modal').fadeIn(250);
        $('.preview-invite-hover-arrow').css('visibility', 'visible');
    }else{
        $('.invite-preview-modal').fadeOut(250);
        $('.preview-invite-hover-arrow').css('visibility', 'hidden');
    }
});
/***** preview invite link click toggle - end *****/


 /***** settings section toggler - start *****/
$(document).on('click', '.settings-menu-item', function(e){

    e.preventDefault();

    var all_menu_items = $('.settings-menu-item').parent();
    var all_sections = $('.settings-section');
    var clicked_item = $(this).attr('href');

    // Facebook event tracking
    fbq('track', 'Settings-page-'+ clicked_item);

    var contact_list_subsection = $('.invite-sub-section#invite-from-contact-list');
    var invite_opening_subsection = $('.invite-friends-opening');

    //remove active class from menu item, add to clicked menu item
    $(all_menu_items).removeClass('active');
    $(this).parent().addClass('active');

    //if user clicks invite friends section tab and currently on contact list subsection, show invite friends opening subsection
    if( clicked_item === '#plex-invitefriends' ){
        if( $('#invite-from-contact-list').is(':visible') ){
            $('.back-to-previous-section').trigger('click');
        }
    }

    //hide all sections, show clicked section
    $(all_sections).slideUp(500).delay(500);
    $(clicked_item).slideDown(500, function(){
        Plex.settings.setUserType();
        if( clicked_item === '#plex-student-billing' ) Plex.settings.showPlansIfNotPremium();
    });

});
/***** settings section toggler - end *****/

Plex.settings.isPremium = function(){
    return $('.plex-billing-default-section').data('is-premium');
};

Plex.settings.showPlansIfNotPremium = function(){
    var package_options = $('.billing-package-options');

    //if premium already, show manage subscription page
    //else show premium plans page
    if( Plex.settings.isPremium() ){
        package_options.hide().closest('.plex-billing-default-section').removeClass('billing-bg');
        $('.manage-subscription-container').show();
    }else{
        package_options.show().closest('.plex-billing-default-section').addClass('billing-bg');
    }
};


/***** invite friends sub section toggler/manage imported contacts buttons - start *****/
$(document).on('click', '.manage-imported-contacts-link', function(e){

    var close_this = $('.invite-friends-opening');
    var open_this = $('#invite-from-contact-list');

    Plex.settings.slidingHideEffect( close_this, open_this, 'left', Plex.settings.slideEffectSpeed);

    //hide this button when clicked
    $(this).fadeOut(500);

    //check if any of the import options have been clicked, if so make ajax call and reset it to false
    if( Plex.import_option_was_visited ){
        Plex.settings.getAlreadyStoredContacts();
        Plex.import_option_was_visited = false;
    }

});
/***** invite friends sub section toggler/manage imported contacts buttons - end *****/


/***** back to previous section toggler - start *****/
$(document).on('click', '.back-to-previous-section', function(e){

    e.preventDefault();

    var this_subSection = $(this).closest('div.invite-sub-section');
    var inviteHome = $('.invite-friends-opening');

    Plex.settings.slidingHideEffect( this_subSection, inviteHome, 'right', Plex.settings.slideEffectSpeed);

    //show manage imported contacts buttons when back to previous button is clicked
    $('.manage-imported-contacts-link').fadeIn(250);
});
/***** back to previous section toggler - end *****/


//hide and slide out of view effect for invite subsections
Plex.settings.slidingHideEffect = function( hide_elem, show_elem, dir, speed ){
    
    var oppositeDirection = 'left';

    if( dir === 'left' ){
        oppositeDirection = 'right';
    }

    $(hide_elem).hide('slide', {direction: dir}, speed, function(){
        Plex.settings.slidingShowEffect( show_elem, oppositeDirection, speed );
    });
}

//show and slide into view for invite subsections
Plex.settings.slidingShowEffect = function( elem, dir, speed ){
    $(elem).show('slide', {direction: dir}, speed);
}

//if select all checkbox is checked, select all invite friends options, else deselect all
Plex.settings.selectAllInviteChoices = function( elem ){

    var invite_list_all_friends = elem.closest('div.invite-sub-section').find('div.invite-friends-list-row input[type="checkbox"]');

    if( elem.is(':checked') ){
        $(invite_list_all_friends).prop('checked', true);
    }else{
        Plex.settings.deselectCheckboxes();
    }

    Plex.settings.makeCheckboxActive(invite_list_all_friends);
}

//deselect specific checkboxes or all checkboxes
Plex.settings.deselectCheckboxes = function(){
        $('input[type="checkbox"].invite-chkbox').prop('checked', false);
}

//add 'chosen' class when invite friend checkbox is checked
Plex.settings.makeCheckboxActive = function( elem ){
    var _this_parent = $(elem).closest('div.invite-user-choice');

    if( $(elem).is(':checked') ){
        _this_parent.addClass('chosen');
    }else{
        _this_parent.removeClass('chosen');
    }
}

//check for param in url to display certain settings page based on a redirect link
Plex.settings.checkUrlForParam = function(){

    if(Plex.settings.getRedirectParam('invite') === 'true'){
        $('.settings-menu-item[href="#plex-invitefriends"]').trigger('click');
    }
}

//close the invite preview modal on DOM click
Plex.settings.outsideOfDivClickToClose = function(e, modal, arrow){

    if( $('.invite-preview-modal').css('display') !== 'none' && e.target.className !== 'preview-invite'){
        $(modal).fadeOut(250);
        $(arrow).css('visibility', 'hidden');
    }
}

//send individual email invite
Plex.settings.submitIndividualEmailInvite = function(){

    var individual_email = $('.individual-email-value').val();

    if( $('.individual-email-submission-row small.error').is(':visible') ){
        $('.invite-submit-email-btn').attr('disabled', 'disabled');
    }else{
        //show ajax loader
        $('#submit-individual-invite-ajax-loader').show();
        $.ajax({
            type: 'POST',
            url: '/ajax/sendSingleInvite',
            data: {invite_me_email: individual_email},
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(data){
                topAlert(Plex.settings.inviteSuccessMsg);
                //hide ajax loader
                $('#submit-individual-invite-ajax-loader').hide();
                //reset form field value
                $('.individual-email-value').val('');
            },
            error: function(data){
                Plex.settings.inviteErrorMsg.msg = data;
                topAlert(Plex.settings.inviteErrorMsg);
                //hide ajax loader
                $('#submit-individual-invite-ajax-loader').hide();
            }
        });//end of ajax
    }
}

//sends the selected contacts to controller to know what contacts to send the invite email to
Plex.settings.sendInvites = function(){
    //clear array every click
    Plex.settings.contact_array = [];

    var contacts_to_invite = $('.invite-friend-option-checkbox:checked');
    var invite_me_email = '';
    var invite_me_name = '';
    var send_these_contacts = '';



    //if no contact checkboxes were selected show error message, otherwise send contacts via ajax
    if( contacts_to_invite.length === 0 ){
        $('.send-invites-btn').attr('disabled', 'disabled');
        $('.send-invites-error-msg').slideDown(250);
    }else{

        //show ajax loader
        $('#manage-contacts-ajax-loader').show();

        //loop through each of the checked contacts, pass each of the contacts name/email to contactObject, and store in array
        contacts_to_invite.each(function(){
            invite_me_name = $(this).data('contacts-name');
            invite_me_email =  $(this).attr('id');
            Plex.settings.contact_array.push( Plex.settings.contactObject = {contact_name: invite_me_name, contact_email: invite_me_email} );
        });

        //convert array into JSON format
        send_these_contacts = JSON.stringify(Plex.settings.contact_array);

        //make ajax call to post contact info to controller, if successful show success alert
        $.ajax({
            type: 'POST',
            url: '/ajax/sendInvites',
            data: send_these_contacts,
            contentType: "application/json; charset=utf-8",
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(data){
                if( data == 'success' ){
                    topAlert(Plex.settings.inviteSuccessMsg);
                    //hide ajax loader
                    $('#manage-contacts-ajax-loader').hide();
                    Plex.settings.removeFromContactsList( contacts_to_invite, '.invite-user-choice-row' );
                    Plex.settings.updateContactCount();
                    Plex.settings.updateSelectedContactsCount();
                }else{
                    Plex.settings.sendInvitesErrorMsg();
                    //hide ajax loader
                    $('#manage-contacts-ajax-loader').hide();
                }
            }
        });//end of ajax
    }//end of else
}

//update the selected contacts count
Plex.settings.updateSelectedContactsCount = function(){
    Plex.settings.currently_checked_count = $('.invite-friend-option-checkbox:checked').length;

    //update count
    $('.selected-counter').html(Plex.settings.currently_checked_count).addClass('updated', 100);
    setTimeout(function(){
        $('.selected-counter').html(Plex.settings.currently_checked_count).removeClass('updated', 100)
    }, 1500);

    if( Plex.settings.currently_checked_count > 0 ){
        //if > 0 and not visible, then show it, otherwise safe to assume it is already visible
        if( $('.contacts-currently-selected').is(':hidden') ){
            $('.contacts-currently-selected').fadeIn(250);
        }
    }else{
        $('.contacts-currently-selected').fadeOut(250);
    }
}

//update the number of contacts in your list count
Plex.settings.updateContactCount = function(){
    Plex.settings.num_of_contacts = $('.invite-user-choice-row').length;
    $('.contact-list-count').html( '(' + Plex.settings.num_of_contacts + ' contacts)' );
}

//remove contact from contacts list after successfully sending them an invite to Plexuss
Plex.settings.removeFromContactsList = function( remove_me, my_parent ){
    $(remove_me).closest(my_parent).remove();
}

//send invites error message toggle
Plex.settings.sendInvitesErrorMsg = function(){
    $('.mailing-invites-error-msg').slideDown(250);
    setTimeout(function(){
        $('.mailing-invites-error-msg').slideUp(250);
    }, 5000);
}

//ajax call if user already has contacts imported
Plex.settings.getAlreadyStoredContacts = function(){

    //show ajax loader 
    $('#manage-contacts-ajax-loader').show();

    $.ajax({
        type: 'GET',
        url: '/ajax/getImportedStudents',
        dataType: 'text json',
        contentType: "application/json; charset=utf-8",
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data){

            //hide ajax loader
            $('#manage-contacts-ajax-loader').hide();

            //populate the contact list
            if( data.length === 0 ){
                //show 'no contacts yet' message
                $('.no-contacts-yet-msg').show();
                $('.send-invites-btn, .select-all-users-checkbox-row, .choose-contacts-description').hide();
            }else{
                //populate contacts container with contacts
                $('.invite-friends-list-container-col').html(Plex.settings.populateListOfContactsContainer(data));
                $('.send-invites-btn, .select-all-users-checkbox-row, .choose-contacts-description').show();
                Plex.settings.updateContactCount();
            }
        },
        error: function(data){
            //do nothing
            // console.log('error has occured');
        }
    });//end of ajax
}

//building html contact list for ajax call of already imported contacts
Plex.settings.populateListOfContactsContainer = function( contacts ){

    var inject_contacts = '';

                                        $(contacts).each(function(){

    inject_contacts +=                      '<div class="row invite-user-choice-row">';
    inject_contacts +=                          '<div class="column small-12 invite-user-choice">';

    inject_contacts +=                              '<div class="row">';
    inject_contacts +=                                  '<div class="column small-1">';
    inject_contacts +=                                      '<input type="checkbox" name="name" value="value" id="'+this.invite_email+'" class="invite-friend-option-checkbox invite-chkbox" data-contacts-name="'+this.invite_name+'">';
    inject_contacts +=                                  '</div>';
    inject_contacts +=                                  '<div class="column small-11">';
    inject_contacts +=                                      '<label for="'+this.invite_email+'">';

                                                                if( this.invite_name === '' ){
    inject_contacts +=                                          '<span class="invite-name">(No name)</span> ';
                                                                }else{
    inject_contacts +=                                          '<span class="invite-name">'+this.invite_name+'</span> '; 
                                                                }

    inject_contacts +=                                          '&nbsp;&nbsp;&nbsp;&nbsp;';

                                                                if( this.invite_email !== '' ){
    inject_contacts +=                                          '<span>'+this.invite_email+'</span> ';
                                                                }

    inject_contacts +=                                      '</label>';
    inject_contacts +=                                  '</div>';
    inject_contacts +=                              '</div>';

    inject_contacts +=                          '</div>';
    inject_contacts +=                      '</div>';   

                                        });
    
    return inject_contacts;
}

//function to parse param (if there is a param passed in url) - returns true or false
Plex.settings.getRedirectParam = function( arg ){

    arg = arg.replace(/[\[]/, "\\\[").replace(/[\]]/, "\\\]");

    var regex = new RegExp("[\\?&]" + arg + "=([^&#]*)"),
        results = regex.exec(location.search);
        
    return results == null ? "" : decodeURIComponent(results[1].replace(/\+/g, " "));
}
/* ///////////////////// new settings js - start of invite friends - end \\\\\\\\\\\\\\\\\\\\\\ */

/* \\\\\\\\\\\\\\\\\\\\\ start of manage users ////////////////////////// */
$(document).on('click', '.manage-portals-link', function(event) {
    event.preventDefault();

    $('.plex-manage-users-section').slideUp(250);
    $('.plex-manage-portals-section').slideDown(500).delay(500);
});

$(document).on('click', '.manage-users-link', function(event) {
    event.preventDefault();

    $('.plex-manage-portals-section').slideUp(250);
    $('.plex-manage-users-section').slideDown(500).delay(500);
});


function bgColorGen() {
    return '#'+'0123456789abcdef'.split('').map(function(v,i,a){
        return i>5 ? null : a[Math.floor(Math.random()*16)] 
    }).join('');
}

// rename portal -- just for view
$(document).on('click', '.portal-container .rename-portal, .deactived-portal-container .rename-portal', function(event){
    event.preventDefault();
    var _this = $(this);
    _this.css('font-weight', 600);

    var _this_orgin_portal = _this.siblings('.portal-name');
    var origin_portal = _this_orgin_portal.find('.portal-name-shown');

    var type_new_name = _this_orgin_portal.find('.type-new-name');
    type_new_name.find('input[type=text]').css('value', origin_portal.html());

    origin_portal.addClass('hide');
    type_new_name.removeClass('hide');
});

// toggle font-weight
$('.portal-container .rename-portal').bind('blur', function (event) {
    event.preventDefault();
    $(this).css('font-weight', 400);
}).bind('focus', function (event) {
    event.preventDefault();
    $(this).css('font-weight', 600);
});

$('.deactived-portal-container .rename-portal').bind('blur', function (event) {
    event.preventDefault();
    $(this).css('font-weight', 400);
}).bind('focus', function (event) {
    event.preventDefault();
    $(this).css('font-weight', 600);
});

// rename portal by click submit button
$(document).on('click', '.portal-container .portal-name input[type=submit], .deactived-portal-container .portal-name input[type=submit]', function() {

    Plex.settings.loadingStart();
    var _this = $(this);
    var hashedid = _this.parents('.portal-container').data('hashedid');
    if(typeof hashedid == 'undefined') 
        hashedid = _this.parents('.deactived-portal-container').data('hashedid');
    
    var new_portal = _this.parents('.portal-name').find('input[type=text]');
    var new_portal_name = new_portal.val();


    if(new_portal_name.length > 30) {
        _this.parents('.portal-name').find('.error').css('display', 'block');
        Plex.settings.loadingEnd();

    } else {
        _this.parents('.portal-name').find('.error').css('display', 'none');
        _this.parents('.portal-name').find('.portal-name-shown').removeClass('hide').html(new_portal_name);
        _this.parents('.portal-name').find('.type-new-name').addClass('hide');

        $.ajax({
            url: '/admin/ajax/createEditPortal',
            type: 'POST',
            dataType: 'JSON',
            data: {'type': 'rename', 'hashedid' : hashedid, 'name' : new_portal_name } ,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(data){
                topAlert(Plex.settings.portalRenamedMsg);
                Plex.settings.loadingEnd();
            } ,
            error: function(data){ 
                if(data['responseText'] == "success" ) {
                    topAlert(Plex.settings.portalRenamedMsg);                    
                }
                else {
                    Plex.settings.inviteErrorMsg.msg = data['responseText'];
                    topAlert(Plex.settings.inviteErrorMsg);                    
                }
                Plex.settings.loadingEnd();
            }
        });

        _this.parents('.portal-container').find('.rename-portal').trigger('blur');
        _this.parents('.deactived-portal-container').find('.rename-portal').trigger('blur');
    }
    
});


$(document).on('blur', '.portal-container .portal-name', function(event){
    // event.preventDefault();

    // var _this = $(this);
    // var hashedid = _this.parent('.portal-container').data('hashedid');
    // var new_portal_name = _this.find('input[type=text]').val();
    // // console.log(new_portal_name);
    // _this.html(new_portal_name);

    // $.ajax({
    //     url: '/admin/ajax/createEditPortal',
    //     type: 'POST',
    //     dataType: 'JSON',
    //     data: {'type': 'rename', 'hashedid' : hashedid, 'name' : new_portal_name }
    // })
    // .done(function() {
    //     console.log("success");
    // });

});


//create new portal
$(document).on('click', '.new-portal-shown input[type=submit]', function() {

    Plex.settings.loadingStart();

    var _this = $('.new-portal-shown');
    var new_portal_name = _this.find('input[type=text]').val();
    _this.css('display', 'none');
    
    $.ajax({
        url: '/admin/ajax/createEditPortal',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: {'type': 'create', 'name': new_portal_name },
        type: 'POST'
    }).done(function(data, textStatus, xhr) {

        if(data == 'success') {
            topAlert(Plex.settings.portalCreatedMsg);
            Plex.settings.loadingEnd();

            if($('.portal-container:visible').length == 0) {
                // there is no portal anymore
                var portal_template = $('.portal-container');
                portal_template.removeClass('hide');
                portal_template.find('.portal-name-shown').html(new_portal_name);
                portal_template.find('.type-new-name input[type=text]').val(new_portal_name);
            }else {
                // insert in last elem portal-container (not last-child)
                var portal_template = $('.portal-container').last().clone();
                portal_template.find('.portal-name-shown').html(new_portal_name);
                portal_template.find('.type-new-name input[type=text]').val(new_portal_name);
                portal_template.insertAfter($('.portal-container').last());
            }

        } else {
            Plex.settings.usersAccessInvalidMsg.msg = data;
            Plex.settings.loadingEnd();
            topAlert(Plex.settings.usersAccessInvalidMsg);
        }
        
        window.location.href = '/settings/manageusers?portals=1';
    });
    
}); 


// remove users from a portal
$(document).on('click', '.plex-manage-portals-section .row.portal-container .emailList a.close', function() {

    Plex.settings.loadingStart();
    var email_addr = $(this).parent('.email-addr');
    email_addr.css('display', 'none');

    var hasheduserdid = email_addr.data('hasheduserid');

    var hashedid = $(this).parents('.portal-container').data('hashedid');

    $.ajax({
        url: '/admin/ajax/addRemoveUsers',
        type: 'POST',
        dataType: 'JSON',
        data: {'type': 'delete', 'hashedid' : hashedid, 'hasheduserid' : hasheduserdid} , 
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data){
            if (data['responseText'] == "Success") {
                topAlert(Plex.settings.userDeletedMsg);
            }else{
                Plex.settings.inviteErrorMsg.msg = data;
                topAlert(Plex.settings.inviteErrorMsg);
            }   
            Plex.settings.loadingEnd();     
        } ,
        error: function(data){   
            // console.log(data);  
            if(data['responseText'] == "Success" ) {
                topAlert(Plex.settings.userDeletedMsg); 
            } else {
                Plex.settings.inviteErrorMsg.msg = data;
                topAlert(Plex.settings.inviteErrorMsg);
            }
            Plex.settings.loadingEnd();     
        }
    });
    
});

//focus on and blur toggle input text
$('.plex-manage-portals-section .emailList .add-users input[type=text]').bind('focus', function(event) {
    var check_access = $(this).parents('.add-users').find('.check-access');

    var title =  '<table style="width: 4em;"><thead><th colspan="3">User Roles</th></thead><tbody><tr><td></td><td>College Admin</td><td>College User</td></tr>';
    title += '<tr><td>Add/remove users</td><td><img src="/images/admin/check-green.png"/></td><td></td></tr>';
    title += '<tr><td>Manage user permissions</td><td><img src="/images/admin/check-green.png"/></td><td></td></tr>';
    title += '<tr><td>Manage all portals</td><td><img src="/images/admin/check-green.png"/></td><td></td></tr>';
    title += '<tr><td>Manage targeting</td><td><img src="/images/admin/check-green.png"/></td><td><img src="/images/admin/check-green.png"/></td></tr>';
    title += '<tr><td>Manage own portal</td><td><img src="/images/admin/check-green.png"/></td><td><img src="/images/admin/check-green.png"/></td></tr>';
    title += '</tbody></table>';

    check_access.find('.has-tip').attr('title', title);
    
    check_access.css('display', 'block');
});

//test multiple email address
Plex.settings.testEmail = function (email) {
    var regex = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
    var result = email.replace(/\s/g, "").split(/,|;/);        
    for(var i = 0;i < result.length;i++) {
        if(!regex.test(result[i])) {
            return false;
        }
    }       
    return true;
}

// add new user for a portal
$(document).on('click', '.plex-manage-portals-section .row.portal-container .emailList input[type=submit]', function(){
    var _this = $(this);
    var email_addr_template = null;
    var hashedid = null;
    var textArea = _this.parents('.add-users').find('input[type=text]');
    var new_email_addr = textArea.val().trim();

    if(new_email_addr == null || new_email_addr.length == 0 || !Plex.settings.testEmail(new_email_addr) ) {
        textArea.val('');
        textArea.parents('.add-users').find('.check-access').css('display', 'none');
        topAlert(Plex.settings.emailInvalidMsg);
        return ;
    } else if( _this.parents('.add-users').find('input[type=radio]:checked').length == 0) {
        topAlert(Plex.settings.usersAccessInvalidMsg);
        return ;
    }
    else {
        Plex.settings.loadingStart();
        // remove space
        new_email_addr = new_email_addr.replace(/\s/g, '');

        var email_list= _this.parents('.emailList').first().find('.email-list');
        
        hashedid = _this.parents('.portal-container').data('hashedid'); 

        var users_access_checked = _this.parents('.add-users').find('input[name=users-access]:checked');


        $.ajax({
            url: '/admin/ajax/addRemoveUsers',
            type: 'POST',
            dataType: 'JSON',
            data: {'type': 'add', 'email' : new_email_addr, 'hashedid' : hashedid, 'users_access' : users_access_checked.val()} , 
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(data){
                // console.log(data);
                if (typeof data == "object" ) {
                    topAlert(Plex.settings.userAddedMsg);
                }else{
                    Plex.settings.inviteErrorMsg.msg = data;
                    topAlert(Plex.settings.inviteErrorMsg);
                }
            } ,
            error: function(data){  
                if(typeof data == "object") {
                    topAlert(Plex.settings.userAddedMsg); 
                } else {
                    Plex.settings.inviteErrorMsg.msg = data;
                    topAlert(Plex.settings.inviteErrorMsg);
                }
            }
        })
        .done(function(ret) {

            // console.log(ret);

            for (var index in ret){

                if(email_list.find('span:visible').length == 0) {
                    //inject new email
                    if(ret[index]['super_admin'] == 0) {
                        email_addr_template = email_list.find('span.user').first().clone();                
                    } else {
                        email_addr_template = email_list.find('span.superadmin').first().clone();          
                    } 
                }
                else {
                    email_addr_template = email_list.find('span:visible').last().clone();
                }

                email_addr_template.html(ret[index]['email'] + "<a href='#'' class='close'>&nbsp;&times;</a>");
                
                email_addr_template.attr('data-hasheduserid', ret[index]['hasheduserid']);
                email_addr_template.removeClass('hide');
                email_addr_template.removeClass('user');
                email_addr_template.removeClass('superadmin');

                email_list.append(email_addr_template);
            }

            Plex.settings.loadingEnd();
            window.location.href = '/settings/manageusers?portals=1';
        });
        
        textArea.val('');
        textArea.parents('.add-users').find('.check-access').css('display', 'none');

    }

});

$(document).on('click', '.save-settings', function(event) {
    var _this = $('.modify-users');
    _this.find('.portal-collections').html('');
    _this.css('display', 'none');
});

$(document).on('click', '.edit-add-portal', function(event) {
    event.preventDefault();
    var _this = $(this);

    var others = $('.edit-add-portal').not(_this);
    others.each(function() {
        var _self = $(this);
        var emailList = _self.siblings('.emailList');
        if(emailList.is(':visible')){
            emailList.slideUp(400);  
            _self.css('font-weight', 400);          
        } 
    });

    _this.siblings('.emailList').slideToggle(function () {    
        if($(this).is(':visible')) {
            _this.css('font-weight', 600);
        } else {
            _this.css('font-weight', 400);
        }
    });

});

$(document).on('click', '.plex-manage-portals-section .row.new-portal span', function(){
    $('.new-portal-shown').slideToggle(400);
});

$(document).on('click', '.users-acct:not(:last-child)', function(event){
    event.preventDefault();

    // get data 
    var user_info = $(this).data('user'); 

    var portal_info = [];
    var modify_users = $('.modify-users');
    
    if($('.modify-users').is(':visible')) {
        // console.log('the modify-users already exist');
        $('.modify-users').slideUp(250);

        if($('.user_information_user_id').prop('value') == user_info['user_id']) {
            return;
        }
    }     

    $('.user_information_user_id').val(user_info['user_id']);   

    $(this).find('.users-access > div').each(function(index, el) {
        // console.log($(this).data('portal-info'));
        if( $.inArray($(this).data('portal-info'), portal_info) == -1) {
            portal_info.push($(this).data('portal-info').toString().trim());
        }
    });

    modify_users.find('.user-img').html('<img src=" ' + user_info['profile_img_loc'] + '" style="width: 200px; padding: 20px 40px;"/>');

    modify_users.find('.lname').attr('value', user_info['lname']);
    modify_users.find('.fname').attr('value', user_info['fname']);

    modify_users.find('.portal-collections').html('');


    $('.portal-name-shown').each(function(index, el) {

        var _this_portal_name = $(this).html().trim();

        //if inside array
        if(_this_portal_name != "") {
            if( $.inArray(_this_portal_name, portal_info) == -1 ) { // not in array
                modify_users.find('.portal-collections')
                            .append('<div class="item"><input type="checkbox" id="portal-' 
                            + _this_portal_name + '" name="portal-' 
                            + _this_portal_name + '"> <label for="portal-'
                            + _this_portal_name + '"> ' + _this_portal_name +'</label></div>');
            } else {
                modify_users.find('.portal-collections')
                            .append('<div class="item"><input type="checkbox" id="portal-' 
                            + _this_portal_name + '" name="portal-' 
                            + _this_portal_name + '" checked="true"> <label for="portal-'
                            + _this_portal_name + '"> ' + _this_portal_name +'</label></div>');
            }
        }
    });

    if(user_info['super_admin'] == 0) {
        $('#updated-admin').prop('checked', false);
        $('#updated-user').prop('checked', true);
    } else {
        $('#updated-admin').prop('checked', true);
        $('#updated-user').prop('checked', false);
    }

    var add_user_steps = $('.add-users-step1, .add-users-step2, .add-users-finalstep');
    if(!add_user_steps.is(":visible")) {
        modify_users.slideToggle(400);
    } else {
        add_user_steps.slideUp(250);
        modify_users.slideToggle(400);
    }

    $('html,body').animate({ scrollTop: modify_users.offset().top} ,'slow');
});

$('.users-acct:not(:last-child)').hover(function() {
    /* Stuff to do when the mouse enters the element */
    $(this).find('img.right').removeClass('hide');
}, function() {
    /* Stuff to do when the mouse leaves the element */
    $(this).find('img.right').addClass('hide');
});


$(document).on('click', '.users-acct:last-child', function(event){
    event.preventDefault();

    var title =  '<table style="width: 4em;"><thead><th colspan="3">User Roles</th></thead><tbody><tr><td></td><td>College Admin</td><td>College User</td></tr>';
    title += '<tr><td>Add/remove users</td><td><img src="/images/admin/check-green.png"/></td><td></td></tr>';
    title += '<tr><td>Manage user permissions</td><td><img src="/images/admin/check-green.png"/></td><td></td></tr>';
    title += '<tr><td>Manage all portals</td><td><img src="/images/admin/check-green.png"/></td><td></td></tr>';
    title += '<tr><td>Manage targeting</td><td><img src="/images/admin/check-green.png"/></td><td><img src="/images/admin/check-green.png"/></td></tr>';
    title += '<tr><td>Manage own portal</td><td><img src="/images/admin/check-green.png"/></td><td><img src="/images/admin/check-green.png"/></td></tr>';
    title += '</tbody></table>';

    $('.add-users-step1 .has-tip').attr('title', title);

    if(!$('.modify-users, .add-users-step2, .add-users-finalstep').is(":visible") ) {
        $('.add-users-step1').slideToggle(400);
        $('html,body').animate({ scrollTop: $(".add-users-step1").offset().top} ,'slow');
    } else if ($('.modify-users').is(':visible') ) {
        $('.modify-users').slideUp(250);
        $('.add-users-step1').slideToggle(400).delay(250);
    }
});

$(document).on('click', '.modify-users .updateTargetProfilePic ', function(){
    // console.log(target_user_id); // 286128
    $('#updateProfilePic').foundation('reveal', 'open');
});

$(document).on('click', '#updateProfilePic .btn-Save', function(){
    var form = $(this).closest('form');
    var formdata = new FormData(form[0]);
    $('#updateProfilePic').foundation('reveal', 'close');

    Plex.settings.loadingStart();
    
    $.ajax({
        url: '/ajax/profile/personalInfoPhoto',
        type: 'POST',
        data: formdata,
        contentType: false,
        processData: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(data){
        Plex.settings.loadingEnd();
        topAlert(Plex.settings.savedSettingInfoMsg);
        // console.log('success');
    });


});

$(document).on('click', '#moveToNextStep', function(){
    // check multiple email address valid
    var _this = $(this);
    var users_access = _this.closest('.users-access'); 

    if(Plex.settings.testEmail(users_access.siblings('.add-users-name').find('input').val())) {
        if(users_access.find('input[type=radio]:checked').length != 0){
            $('.add-users-step1').slideUp(400);
            $('.add-users-step2').slideDown(400);
        } else {
            topAlert(Plex.settings.usersAccessInvalidMsg);
        }  
    } else {
        topAlert(Plex.settings.emailInvalidMsg);
    }

});


$(document).on('click', '.add-users-finalstep .ok-btn', function() {
    $('.add-users-finalstep').slideUp(400);
    window.location.href = '/settings/manageusers';
});

$(document).on('click', '.target-icon', function() {
    $(this).siblings('.target-criteria').slideToggle(400);
});

$(document).on('click', '#deactivate-user .deactivate-btn', function() {
    var inject_section = $('#delete-user-acct').find('input[name="deactivate_suggestion"]');
    if(inject_section.length != 0) {
        inject_section.prop('value', $('.deactivate-suggestion-textarea').val());        
    }
});

/*
Bind profile picture upload 
On valid submit of profile image form via Ajax
*/

function init_pi_fndtn(){
    $(document).foundation({
        abide : {
            patterns : {
                // address: /^[a-zA-Z0-9\.,#\- ]+$/,
                // skype: /^[a-z][a-z0-9\.,\-_]{5,31}$/i,
                // phone: /^([0-9\-\+\(\) ])+$/,
                // city: /^[a-zA-Z\.\- ]+$/,
                // number: /^[-+]?[0-9]\d*$/,
                // month : /^[-+]?[0-9]\d*$/,
                // zip: /^[a-zA-Z0-9\.,\- ]+$/,
                // name: /^([a-zA-Z\-\.' ])+$/,
                // school_name: /^([\u0000-\uFFFF\ ])+$/,
                file_types:/^[0-9a-zA-Z\\:\-_ ]+.(jpg|png|gif|JPG|PNG|GIF)$/
            }
        }
    });
}


// function updateProfilePic(e) {
//     $('#updateProfilePic').foundation('reveal', 'open');
// }

// function bind_profile_picture_submit( token ){
//     $( '#uploadProfilePictureForm' ).on( 'valid.fndtn.abide', function( event ) {
//         $.ajax({
//             url: '/ajax/profile/personalInfoPhoto/' + token,
//             data: new FormData( this ),
//             method: 'POST',
//             cache: false,
//             contentType: false,
//             processData: false,
//             dataType: 'json',
//             success: function( data ){
//                 // close the modal
//                 $( '#updateProfilePic' ).foundation( 'reveal', 'close' );
//                 // Show top Alert
//                 topAlert({
//                     img: data.img,
//                     bkg: data.bkg,
//                     textColor: data.textColor,
//                     type: 'soft',
//                     dur: data.dur,
//                     msg: data.msg
//                 });
//             }
//         });
//     });

// }

/* \\\\\\\\\\\\\\\\\\\\ new settings js - start of manage users - end //////////////////////*/

/* //////////// old settings js that handles password change, not touching - start \\\\\\\\\\\\\\ */
$('#accountSettingsChangePass input[name="new_pass"]').on('invalid', function () {
    $('.passError').show();
}).on('valid', function () {
    $('.passError').hide();
});

$(function() {
    ChangeAccPassword();
});

//Load the Setting info of the Id supplied.
function loadSettingInfo(id) {
    Plex.settings.clearPasswordFields();
    $('.settingpanel').hide();
    
    $.ajax({
       // url: '/ajax/setting/' + id + '/' + Plex.ajaxtoken,
	    url: '/setting/' + id + '/' + Plex.ajaxtoken,
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).always(function(data) {
        $('#' + id).html(data).data('boxMode', 'ready').show();
        $('.' + id).addClass('selected');
    });
}

function ChangeAccPassword(){
	$("#accountSettingsChangePass").on("valid",function(){
		var input = $('#accountSettingsChangePass').serialize();
		$.ajax({
            url: '/setting/accountSetting/' + Plex.ajaxtoken,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: input,
            type: 'POST'
        }).done(function(data, textStatus, xhr){
			if(data == "New Password & Verify Password not matched"){
				var x = {
                    textColor: '#ffffff',
                    backGroundColor : 'red',
                    msg: 'New Password & Verify Password not matched.',
                    type: 'soft',
                    dur : 5000
                };
			}else if (data == 'Old Password not matched') {
                var x = {
                    textColor: '#ffffff',
                    backGroundColor : 'red',
                    msg: 'Your old password does not match.',
                    type: 'soft',
                    dur : 5000
                };
            } else if (data == 'Account Password Changed Successfully.'){

                var x = {
                    textColor: '#ffffff',
                    backGroundColor : 'green',
                    msg: 'Your password has been updated.',
                    type: 'soft',
                    dur : 5000
                };
            };

            topAlert(x);
            loadSettingInfo("accountSetting");
        });
	});
}


//clears password input fields
Plex.settings.clearPasswordFields = function(){
    $('.password-change-form input[type="password"]').val('');
}


$(document).on('click', '.deactive-portal', function() {
    var _this = $(this).parent();

    var hashedid = _this.data('hashedid');
    var portal_name = _this.data('portalname');
    
    var question_deactive  = $('.question-deactive');
    if(_this.hasClass('deactived-portal-container')) {
        question_deactive.html('Are you sure you want to activate '+ portal_name +' Portal?');
    } else {
        question_deactive.html('Are you sure you want to deactivate ' + portal_name + ' Portal?');
    }

    var cancel_portal = $('.cancel-portal');
    cancel_portal.attr('data-hashedid', hashedid);

});

Plex.settings.activateDeactivatePort = function(_this){
    var hashedid = _this.parent().attr('data-hashedid');

    $.ajax({
        url: '/admin/ajax/createEditPortal',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: {'type': 'deactivate', 'hashedid' : hashedid },
        type: 'POST'
    }).done(function(data, textStatus, xhr) {
        $('#deactive-portal-modal').foundation('reveal', 'close');
        if (data == "deactivate" || data == 'activate') {
            Plex.settings.portalDeactivatedMsg.msg = 'Portal has been ' + data + "d!";
            topAlert(Plex.settings.portalDeactivatedMsg);

            if(data == "deactivate") {
                // find portal-container which hashedid is equal
                var deactive_portal = $('.portal-container').filter(function(index) {
                
                    return $(this).data('hashedid') == hashedid; 
                });
                if(deactive_portal.length == 1) {
                    deactive_portal.removeClass('portal-container').addClass('deactived-portal-container');
                    deactive_portal.find('.deactive-portal a').html('Activate');
                }

                //check .deactived-portal-container exist
                if($('.deactived-portal-container').length == 0) 
                    deactive_portal.insertAfter($('.deactive-portal-begins'));
                else {  
                    deactive_portal.insertAfter($('.deactived-portal-container').last());
                }

            } else if( data == "activate" ) {
                var active_portal = $('.deactived-portal-container').filter(function(index) {                
                    return $(this).data('hashedid') == hashedid; 
                });

                if(active_portal.length == 1) {
                    active_portal.removeClass('deactived-portal-container').addClass('portal-container');
                    active_portal.find('.deactive-portal a').html('Deactivate');
                }

                //check .deactived-portal-container exist
                if($('.portal-container:visible').length == 0) 
                    active_portal.insertAfter($('.active-portal-begins'));
                else {  
                    active_portal.insertAfter($('.portal-container:visible').last());
                }
            }
        }else{
            Plex.settings.managePortalErrorMsg.msg = data;
            topAlert(Plex.settings.managePortalErrorMsg);
        }
    }).always(function(data_or_XHR, textStatus) {
        if (textStatus == 'success') {
            window.location.href = '/settings/manageusers?portals=1';
        }
    });
}

Plex.settings.cancerlSetting = function(elem) {
    var currentModifyTab = elem.closest('.modify-users');
    if(currentModifyTab.is(":visible")) {
        currentModifyTab.slideUp(500).delay(500); 
    }
}

Plex.settings.saveSetting = function(elem){
    
    var form = elem.closest('form');
    var formdata = new FormData(form[0]);

    Plex.settings.loadingStart();

    $.ajax({
        url: '/admin/ajax/saveSettingInfo',
        type: 'POST',
        data: formdata,
        contentType: false,
        processData: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(data){
        Plex.settings.loadingEnd();
        topAlert(Plex.settings.savedSettingInfoMsg);
        window.location.href = '/settings/manageusers';
    });

}

Plex.settings.userAddComfirm = function(_this) {
    var form = _this.closest('form');
    var formdata = new FormData(form[0]);

    $('.add-users-step2').slideUp(400);

    Plex.settings.loadingStart();

    $.ajax({
        url: '/admin/ajax/addUserFromManageUser',
        type: 'POST',
        data: formdata,
        contentType: false,
        processData: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(data){
        Plex.settings.loadingEnd();
        topAlert(Plex.settings.savedSettingInfoMsg);
        var _last_step = $('.add-users-finalstep');
        var users_list = _last_step.find('.users-list');
        users_list.html('');

        data = JSON.parse(data);
        for(var i = 0; i < data.length; i++){
            if(data[i].hasOwnProperty('email')){
                users_list.append('<div class="column large-12"><img src="/images/admin/check-green.png"><span>'+ data[i]['email'] + '</span></div>');
            }
        }
            
        _last_step.slideDown(400);
        $('html,body').animate({ scrollTop: _last_step.offset().top} ,'slow');


    });
}

Plex.settings.delUserFromOrg = function(_this) {
    var form = _this.closest('form');
    var formdata = new FormData(form[0]);
    $('#confirmDelUser').foundation('reveal', 'close');

    Plex.settings.loadingStart();

    $.ajax({
        url: '/admin/ajax/deleteUserFromOrganization',
        type: 'POST',
        data: formdata,
        contentType: false ,
        processData: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(data){
        Plex.settings.loadingEnd();
        topAlert(Plex.settings.deleteUserMsg);
        window.location.href = '/settings/manageusers';
    });
}

Plex.settings.loadingStart = function(){
    $('.manage-students-ajax-loader').show();
}

//hide ajax loader
Plex.settings.loadingEnd = function(){
    $('.manage-students-ajax-loader').hide();
}

Plex.settings.split = function(val){
    return val.split(/,\s*/);
}

Plex.settings.extractLast = function(term) {
    return Plex.settings.split(term).pop();
}

Plex.settings.getUsersAutocomplete = function() {
    $('.users-access-list .field').autocomplete({
        minLength : 1,
        source : function(req, resp) {
            var data = $.getJSON("/admin/getUsersAutocomplete?", {term: Plex.settings.extractLast(req.term)}, resp);
            resp($.ui.autocomplete.filter(data, Plex.settings.extractLast(req.term)));
        },
        focus: function(event, ui) {
            return false;
        },
        select : function(event, ui) {
            var inputfield = $('.users-access-list .field:visible').first();
            var terms = Plex.settings.split(inputfield.val());
            terms.pop();
            terms.push(ui.item.email);
            terms = terms.join(", ");
            inputfield.prop('value', terms);
            return false;
        }

    });

    $('.add-users-step1 .field').autocomplete({
        minLength : 1,
        source : function(req, resp) {
            var data = $.getJSON('/admin/getUsersAutocomplete?', {term: Plex.settings.extractLast(req.term)}, resp);
            resp($.ui.autocomplete.filter(data, Plex.settings.extractLast(req.term)));
        },
        focus: function(event, ui) {
            return false;
        },
        select: function(event, ui) {
            var inputfield = $('.add-users-step1 .field');
            var terms = Plex.settings.split(inputfield.val());
            terms.pop();
            terms.push(ui.item.email);
            terms = terms.join(', ');
            inputfield.prop('value', terms);
            return false;
        }
    });

}

/*
    begin with new payment method
 */
$('.card-cvc').on('keyup', function(e){
    if( $(this).val().length > 2 ) $(this).val( $(this).val().substr(0, 3) );
});

$('.zip.zip-code').on('keyup', function(e){
    if( $(this).val().length > 4 ) $(this).val( $(this).val().substr(0, 5) );
});

$(document).on('click', '.change-plan-btn', function(){
    var package_options = $('.billing-package-options'), payment_section = $('.billing-info');

    if( payment_section.is(':visible') ) payment_section.hide();

    Plex.settings.edit_payment_clicked = payment_section.is(':visible');
    Plex.settings.updatePaymentFormToReflectEditForm();//update payment form save button

    package_options.closest('.plex-billing-default-section').addClass('billing-bg');

    Plex.settings.slidingHideEffect( $('.manage-subscription-container'), package_options.closest('.plex-billing-default-section'), 'left', Plex.settings.slideEffectSpeed );
    package_options.show();
    
});

$(document).on('click', '.edit-payment-btn', function(){
    var bill_info = $('.billing-info');
    bill_info.slideToggle(250);

    Plex.settings.edit_payment_clicked = bill_info.is(':visible');
    Plex.settings.updatePaymentFormToReflectEditForm();
});

Plex.settings.updatePaymentFormToReflectEditForm = function(){
    var submit_btn = $('#save-creditcard-info'),
        cc_error = $('.billing-err > div:first-child > small'),
        cvc_error = $('.billing-err > div:nth-child(2)');

    if( Plex.settings.edit_payment_clicked ){
        $('.billing-info .plan-title').hide();
        submit_btn.val('Save');
        cc_error.html('Please re-enter you credit card number and cvc code in order to save updates.');
        cvc_error.hide();
    }else{
        $('.billing-info .plan-title').show();
        submit_btn.val('Next');
        cc_error.html('Make sure card number has the correct number of digits and no letters.');
        cvc_error.show();
    }
};

$(document).on('click', '.plex-manage-student-billing-section .back-to-plans-btn', function(){
    var billing_info = $('.billing-info'), checkout = $('.billing-checkout');

    $('.plex-billing-default-section').addClass('billing-bg');
    $(this).hide();

    if( billing_info.is(':visible') ) Plex.settings.slidingHideEffect( billing_info, $('.billing-package-options'), 'right', Plex.settings.slideEffectSpeed);
    else if( checkout.is(':visible') ) Plex.settings.slidingHideEffect( checkout, $('.billing-package-options'), 'left', Plex.settings.slideEffectSpeed);
});

$(document).on('click', '.choose-plan-btn:not(.disabled)', function(){
    var billing_info = $('.billing-info'),
        plan = $(this).data('plan');

    if( plan ){
        Plex.settings.payment_plan = plan;
    }

    $('.back-to-plans-btn').show();
    $('.plex-billing-default-section').removeClass('billing-bg');

    Plex.settings.slidingHideEffect( $('.billing-package-options'), billing_info, 'left', Plex.settings.slideEffectSpeed);

});

$(document).on('click', '#complete-purchase', function(){
    if( $('#plex-student-billing').length > 0 ) {
        Plex.settings.completePurchase();         
    } else {
        Plex.settings.completePurchaseAdmin();
    }
});

Plex.settings.validated = function(){
    var valid = false, card_num = null;

    if( $('.card-cvc').val().length === 3 ) valid = true;
    else valid = false;

    if( valid && $('.card-expiry-month').val() && $('.card-expiry-year').val() ) valid = true;
    else valid = false;

    if( valid ){
        card_num = $('.card-number').val();

        switch( card_num.substr(0, 1) ){
            case '4':
            case '5':
            case '6':
                //is visa, mastercard, or discover - all are 16 digit numbers
                if( card_num.length === 16 ) return true;
                break;
            case '3':
                //if starts w/37 and is 15 chars long its amex
                if( +(card_num.substr(1, 1)) === 7 && card_num.length === 15 ) return true; 
                //if starts w/35 and is 16 chars long its jcb 
                if( +(card_num.substr(1, 1)) === 5 && card_num.length === 16 ) return true; 
                //if starts with 3x(not 5 or 7) and is 11 chars long its diners club
                if( ( +(card_num.substr(1, 1)) !== 5 || +(card_num.substr(1, 1)) !== 7 ) && card_num.length === 14 ) return true; 
                break;
            default: return false;
        }
    }

    return false;
};

Plex.settings.completePurchase = function(){
    // Facebook event tracking
    var price = '', payment_data = {};
    payment_data.price = Plex.settings.determinePaymentPrice();
    payment_data.plan = Plex.settings.payment_plan;

    fbq('track', 'Settings-billing-completePurchase-Users', {value: payment_data.price, currency: 'USD'});

    $.ajax({
        url: '/chargeCustomer',
        type: 'POST',
        data: payment_data,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data){
            if( data === 'false' ){
                topAlert({
                    textColor: '#fff',
                    bkg : '#ee0909',
                    msg: 'Your payment could not be processed. Please check that you entered your information correctly.',
                    type: 'soft',
                    dur : 10000
                });
            }else{
                $('.billing-email').html(data);
                $('.back-to-plans-btn').hide();
                Plex.settings.slidingHideEffect( $('.billing-checkout'), $('.billing-done'), 'left', Plex.settings.slideEffectSpeed);
            }
        },
        error: function(err){
            topAlert({
                textColor: '#fff',
                bkg : '#ee0909',
                msg: 'Oops. Looks like something went wrong. Please refresh the page and try again.',
                type: 'soft',
                dur : 10000
            });
        }
    });
};

Plex.settings.completePurchaseAdmin = function() {
    var payment_data = {};
    payment_data.textmsg_price = Plex.settings.textmsg_price;
    payment_data.textmsg_phone = Plex.settings.textmsg_phone;
    payment_data.textmsg_tier = Plex.settings.textmsg_tier;
    payment_data.textmsg_plan = Plex.settings.textmsg_plan;

    fbq('track', 'Settings-billing-completePurchase-Users', {value: payment_data.textmsg_price, currency: 'USD'});
    $.ajax({
        url: '/chargeCustomer',
        type: 'POST',
        data: payment_data,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data){
            if( data === 'false' ){
                topAlert({
                    textColor: '#fff',
                    bkg : '#ee0909',
                    msg: 'Your payment could not be processed. Please check that you entered your information correctly.',
                    type: 'soft',
                    dur : 10000
                });
                window.location.href = "/settings/billing?plans=1";
            } else {
                $('.billing-email').html(data);
                $('.back-to-plans-btn').hide();
                Plex.settings.slidingHideEffect( $('.billing-checkout'), $('.billing-done'), 'left', Plex.settings.slideEffectSpeed);
            }
        },
        error: function(err){
            topAlert({
                textColor: '#fff',
                bkg : '#ee0909',
                msg: 'Oops. Looks like something went wrong. Please refresh the page and try again.',
                type: 'soft',
                dur : 10000
            });
        }
    }).done(function() {
        // console.log("success");
    });
    
}

$(document).on('click', '#save-creditcard-info', function(e) {

    if(!$('#agreement-check').is(':checked')) {
        $('.billing-err').slideDown(250);
        return;        
    } 

    if( Plex.settings.validated() ){
        // Facebook event tracking
        fbq('track', 'Settings-billing-addCard');

        if( $('.billing-err').is(':visible') ) $('.billing-err').slideUp(250);

        var creditcard_token = Stripe.card.createToken({
            name: $('.contact-name').val(),
            address_line1: $('.street-address').val(),
            address_line2: $('.apt').val(),
            address_city: $('.city').val(),
            address_state: $('.state').val(),
            address_zip: $('.zipcode').val(),
            address_country: $('.countries-list select option:selected').val(),
            number: $('.card-number').val(),
            exp_month: $('.card-expiry-month').val(),
            exp_year: $('.card-expiry-year').val(),
            cvc: $('.card-cvc').val()
        }, stripeResponseHandler);
    }else{
        $('.billing-err').slideDown(250);
    }
});

function stripeResponseHandler(status, response) {
  // Grab the form:
    var form = $('#payment-form'),
        callback_err = $('.callback-err');

    if (response.error) { // Problem!
        callback_err.show().find('small').html(response.error.message);

        // Show the errors on the form:
        form.find('.payment-errors').text(response.error.message);
        form.find('.submit').prop('disabled', false); // Re-enable submission

    } else { // Token was created!
        callback_err.hide();
        // payment_plan only for student users
        if(Plex.settings.payment_plan === 'onetime' || Plex.settings.payment_plan === "monthly") {
            Plex.settings.setPriceDisplay();
        }

        // Get the token ID:
        var token = response.id, exp_month = response.card.exp_month, 
            payment_data = {
                stripeToken: token, 
                business_name: $('.business-name').val(),
                name: response.card.name,
                address_line1: response.card.address_line1,
                apt: $('.apt').val() != ''? $('.apt').val(): 'N/A',
                address_city: response.card.address_city,
                address_state: response.card.address_state,
                address_zip: response.card.address_zip,
                address_country: response.card.address_country,
                last4: response.card.last4,
                exp_month : exp_month,
                exp_year : response.card.exp_year,
                type: response.card.brand,
                phone: $('.card-phone').val(),
                
            };
        // need to seperate the payment_data
        if($('#plex-billing').length > 0) {
            // in admin's account
            payment_data.textmsg_tier = Plex.settings.textmsg_tier;
            payment_data.textmsg_plan = Plex.settings.textmsg_plan;
            payment_data.textmsg_phone = Plex.settings.textmsg_phone;
            payment_data.textmsg_ready_to_send = Plex.settings.textmsg_ready_to_send;
        } else {
            // in user's account 
            payment_data.plan = Plex.settings.payment_plan;
            payment_data.price = Plex.settings.determinePaymentPrice();
        }

        if(exp_month < 10) {
            exp_month = '0' + exp_month;
        }

        // Insert the token ID into the form so it gets submitted to the server:
        form.append($('<input type="hidden" name="stripeToken">').val(token));

        // Submit the token
        $.ajax({
            url: '/settings/billing',
            type: 'POST',
            data: payment_data,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(data) {
                // inject updated info to record
                if( Plex.settings.is_student ) Plex.settings.studentCallback();
                else Plex.settings.adminCallback(data);
            },
            error: function(err) {
                console.log(err);
            }
        });
    
    }
};

$(document).on('focus', '.card-number', function(){
    $(this).val('');
});

Plex.settings.setPriceDisplay = function(){
    if( Plex.settings.payment_plan === 'onetime' ){
        $('.item-descrip .right').html('One time fee'); 
        $('.item-package .right, .summary-total .right').html('$100.00');
    }else {
        $('.item-descrip .right').html('Monthly fee'); 
        $('.item-package .right, .summary-total .right').html('$9.99');
    }
};

// only for students premiumn account toggle auto renew
$(document).on('change', '.plex-manage-student-billing-section #auto_renew', function(){
    var msg = 'We are no longer automatically renewing your plan and you will no longer be a Premium user at the end of the current billing cycle.';

    if( $(this).is(':checked') ) msg = 'You are back on recurring payments and we will continue to automatically renew your plan.';

    $.ajax({
        url: '/setting/togglePremiumUserRecurring',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: {recurring: $(this).is(':checked')},
        type: 'POST'
    }).done(function(){
        topAlert({
            textColor: '#fff',
            backGroundColor : 'green',
            msg: msg,
            type: 'soft',
            dur : 15000
        });
    });
});

// for admins user account toggle auto renew
$(document).on('change', '.plex-manage-billing-section #auto_renew', function() {
    var msg = 'We are no longer automatically renewing your plan and you will no longer on this plan at the end of the current billing cycle.';

    if( $(this).is(':checked') ) msg = "You are back on recurring payments and we will continue to automatically renew your plan.";

    $.ajax({
        url: '/setting/toggleAdminUserRecurring',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: {recurring: $(this).is(':checked')},
        type: 'POST'
    }).done(function() {
        topAlert({
            textColor: '#fff',
            backgroundColor: 'green',
            msg: msg,
            type: 'soft',
            dur: 15000
        });
    });
});

Plex.settings.determinePaymentPrice = function(){
    return Plex.settings.payment_plan === 'onetime' ? '100.00' : '9.99';
};

Plex.settings.determinePaymentPriceAdmin = function() {
    var payment_option = {}, price = null;
    payment_option['textmsg_phone'] = Plex.settings.textmsg_phone;
    payment_option['textmsg_tier'] = Plex.settings.textmsg_tier;
    payment_option['textmsg_plan'] = Plex.settings.textmsg_plan;

    $.ajax({
        url: '',
        type: 'GET',
        data: payment_option,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    })
    .done(function(data) {
        // console.log("success");
    });
    
    return price;
}

Plex.settings.studentCallback = function(){
    if( !Plex.settings.edit_payment_clicked ){
        Plex.settings.slidingHideEffect( $('.billing-info'), $('.billing-checkout'), 'left', Plex.settings.slideEffectSpeed);
    }else{
        Plex.settings.edit_payment_clicked = false;
        topAlert({
            textColor: '#fff',
            backGroundColor : 'green',
            msg: 'Your payment information has successfully been updated!',
            type: 'soft',
            dur : 15000
        });
    }
};

Plex.settings.adminCallback = function(data){

    if(typeof data == 'string') {
        topAlert({
            textColor: '#fff',
            bkg: 'red',
            msg : data,
            type: 'soft',
            dur : 15000 
        });

        return ;
    }

    Plex.settings.setPriceDisplayAdmin(data);

    if( !Plex.settings.edit_payment_clicked ) {
        Plex.settings.slidingHideEffect($('.billing-info'), $('.billing-checkout'), 'left', Plex.settings.slideEffectSpeed);
    } else {
        Plex.settings.edit_payment_clicked = false;
        topAlert({
            textColor: '#fff',
            backGroundColor : 'green',
            msg: "Your payment information has successfully been updated!",
            type: 'soft',
            dur: 15000
        });
    }

};

Plex.settings.setPriceDisplayAdmin = function(data) {

    if( data['textmsg_tier'] == 'pay_as_you_go') {
        // pending ....
        switch (Plex.settings.textmsg_plan){
            case 'plan-1':
                break;
            case 'plan-2':
                break;
            case 'plan-3':
                break;
            case 'plan-4':
                break;
            default:
                break;
        } 
    } else if( data['textmsg_tier'] == "flat_fee") {

        var txt_phone_plan_desc    = $('.item-descrip.txt-phone-plan');
        var txt_phone_plan_package = $('.item-package.txt-phone-plan');

        var txt_msg_plan_desc      = $('.item-descrip.txt-msg-plan');
        var txt_msg_plan_package   = $('.item-package.txt-msg-plan');

        // check if the phone is ready to charge or not
        if(data['show_purchased_phone']) {
            txt_phone_plan_package.find('.left').html(data['textmsg_phone']);
            txt_phone_plan_package.find('.right').html('<b>$60.00</b>');
        } else {
            txt_phone_plan_desc.find('.left, .right').html('');
            txt_phone_plan_package.find('.left, .right').html('');
        }

        txt_msg_plan_desc.find('.left').html('<b>'+data['textmsg_tier']+'</b>');        
        txt_msg_plan_desc.find('.right').html('Monthly fee');

        txt_msg_plan_package.find('.left').html(data['plan']['num_of_eligble_text']+ ' texts');
        txt_msg_plan_package.find('.right').html('<b>$'+data['plan']['price']+'.00</b>');

        $('.summary-total .right').html('<b>$'+ data['total_cost'] + '.00</b>');
    }
}

// toggle between billing default tab and invoice history
$(document).on('click', 'a[href="#plex-student-billing"],  a[href="/settings/billing"]', function(){
    var _this = $(this), clicked = _this.data('bill-tab');
    $('.bill-tab').removeClass('active');
    _this.addClass('active');
    Plex.settings.openBillingSection(clicked);
});

Plex.settings.openBillingSection = function(clicked){
    // var title = clicked === 'default' ? 'Complete Order' : 'Billing History',
    var title = 'Billing History';
        default_section = $('.plex-billing-default-section'), back_btn = $('.back-to-plans-btn'),
        package_options = $('.billing-package-options');

    //if( clicked === 'history' ){
        Plex.settings.getInvoiceHistory(title, clicked); 
         // $('.billing-header').html(title);
    // }else{
    //     $('.billing-section').slideUp(400, function(){
    //         $('.billing-header').html(title);
    //         $('.billing-section.'+clicked).slideDown(400);

    //         //if plan options view is visible, show bg and hide back btn
    //         //else hide bg and show back btn
    //         if( !Plex.settings.isPremium() ){
    //             if( package_options.is(':visible') ){
    //                 default_section.addClass('billing-bg');
    //                 back_btn.hide();
    //             }else{
    //                 default_section.removeClass('billing-bg');
    //                 back_btn.show();   
    //             }
    //         }else{
    //             if( default_section.hasClass('billing-bg') ) default_section.removeClass('billing-bg');
    //             package_options.hide();
    //             $('.manage-subscription-container').show();
    //         }
            
    //     });
    // }
};

Plex.settings.getInvoiceHistory = function(title, clicked){
    $.ajax({
        url: '/setting/getInvoiceForUsers',
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data){
            Plex.settings.buildInvoice(JSON.parse(data));
            $('.billing-section').slideUp(400, function(){
                $('.billing-header').html(title);
                $('.billing-section.'+clicked).slideDown(400);
                $('.back-to-plans-btn').hide();
                $('.plex-billing-default-section').removeClass('billing-bg');
            });
        },
        error: function(err){
            // console.log(err);
        }
    });
};

Plex.settings.getInvoiceHistoryAdmin = function() {
    $.ajax({
        url: '/setting/getInvoiceForAdmin',
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(data) {
            Plex.settings.buildInvoiceAdmin(JSON.parse(data));
        },
        error: function(err) {
            console.log(err);
        }
    });
    
}

Plex.settings.renderInvoiceHistory = function(html){
    $('.billing-history').html(html);
};

Plex.settings.buildInvoice = function(data){
    var html = '', header = '', ui = '', invoice = null;

    header += '<div class="invoice invoice-header clearfix">';
        header += '<div class="order-no left">Order No.</div>';
        header += '<div class="billing-date left">Billing Date</div>';
        header += '<div class="billing-descrip left">Description</div>';
        header += '<div class="billing-amount left">Amount</div>';
    header += '</div>';

    if( data.length > 0 ){
        html = '';
        for (var i = 0; i < data.length; i++) {
            invoice = data[i];
            html += '<div class="invoice invoice-item clearfix">';
                html += '<div class="order-no left">'+invoice.invoice_id+'</div>';
                html += '<div class="billing-date left">'+invoice.created_at+'</div>';
                html += '<div class="billing-descrip left">'+invoice.level+'</div>';
                html += '<div class="billing-amount left">$'+invoice.amount+'</div>';
                html += '<div class="billing-descrip-sm clearfix">'+invoice.level+'</div>';
            html += '</div>';
        }
 
    }else{
        html = '<div class="text-center no-history">No previous history</div>';
    }

    ui = header.concat(html);
    Plex.settings.renderInvoiceHistory(ui);
};

Plex.settings.buildInvoiceAdmin = function(data) {
    var html = '', header = '', ui = '', invoice = null;

    header += '<div class="invoice-title">Invoice History</div>';

    header += '<div class="invoice invoice-header clearfix">';
        header += '<div class="order-no left">Order No.</div>';
        header += '<div class="billing-date left">Billing Date</div>';
        header += '<div class="billing-descrip left">Description</div>';
        header += '<div class="billing-amount left">Amount</div>';
    header += '</div>';

    if( data.length > 0 ) {
        html = '';
        for(var i = 0 ; i < data.length; i++) {
            invoice = data[i];
            html += '<div class="invoice invoice-item clearfix">';
                html += '<div class="order-no left">'+invoice.invoice_id+'</div>';
                html += '<div class="billing-date left">'+invoice.created_at+'</div>';
                html += '<div class="billing-descrip left">'+invoice.type+'</div>';
                html += '<div class="billing-amount left">$'+invoice.amount+'</div>';
            html += '</div>';
        }

        ui = header.concat(html);
        Plex.settings.renderInvoiceHistory(ui);
    }
}

$(document).on('click', '.edit-card-btn', function(){
    $('.payment-info-record').slideUp(400);

    // inject from record to payment-info-container
    var user_info = $("div[class^='inject-'],div[class*=' inject-']");
    var regex = /inject-([^&]*)/;
    user_info.each(function(index, el) {
        var _this = $(this);
        var _this_info = _this.html().trim();

        var attributes = _this.attr('class');
        var inject_slug = attributes.match(regex);
        if(inject_slug) {
            inject_slug = inject_slug[1];
        }

        if(inject_slug == 'card-number' || inject_slug == 'card-cvc'){
            $('.' + inject_slug).prop('value', null);
        } else if(inject_slug == 'state') {
            $('select[name="state"] option').each(function(el) {
                if($(this).text() == _this_info){ 
                    $(this).prop('selected', true); 
                }
            });
        } else if(inject_slug == 'card-exp-date') {
            $('.card-expiry-month').prop('value', null);
            $('.card-expiry-year').prop('value', null);
        } else {
            $('.' + inject_slug).prop('value', _this_info);
        }
        
    });

    $('.payment-info-container').removeClass('hide');
    $('.payment-info-container').slideDown(400);
    $('.hide-payment-info').html('');
});

$(document).on('click', 'li.billing-tab.plans, .change-plan-btn', function(){
    Plex.settings.navtabswitching('plans');
        
    Plex.settings.hideElem($('.plex-billing-default-section'));
    Plex.settings.hideElem($('.plex-textmsg-plan'));
    Plex.settings.hideElem($('.plex-textmsg-search-phone'));
    Plex.settings.hideElem($('.billing-info'));
    Plex.settings.hideElem($('.billing-checkout'));
    Plex.settings.hideElem($('.billing-done'));
    Plex.settings.hideElem($('.billing-history'));
    Plex.settings.hideElem($('.plex-billing-price-section'));

    $('.plex-billing-plan-section').slideDown(400);

    Plex.settings.resetContinueBtn();
});

$(document).on('click', 'a.back-payment-default, li.billing-tab.billing', function(){
    Plex.settings.navtabswitching('billing');

    Plex.settings.hideElem($('.plex-billing-plan-section'));
    Plex.settings.hideElem($('.plex-textmsg-plan'));
    Plex.settings.hideElem($('.plex-textmsg-search-phone')); 
    Plex.settings.hideElem($('.billing-info'));   
    Plex.settings.hideElem($('.billing-checkout'));
    Plex.settings.hideElem($('.billing-done'));
    Plex.settings.hideElem($('.billing-history'));
    Plex.settings.hideElem($('.plex-billing-price-section'));

    $('.plex-billing-default-section').slideDown(400);

    Plex.settings.resetContinueBtn();
});

$(document).on('click', 'li.billing-tab.invoices', function() {
    Plex.settings.navtabswitching('invoices');

    Plex.settings.hideElem($('.plex-billing-default-section'));
    Plex.settings.hideElem($('.plex-billing-price-section'));
    Plex.settings.hideElem($('.plex-billing-plan-section'));
    Plex.settings.hideElem($('.plex-textmsg-plan'));
    Plex.settings.hideElem($('.plex-textmsg-search-phone'));
    Plex.settings.hideElem($('.billing-info'));
    Plex.settings.hideElem($('.billing-checkout'));
    Plex.settings.hideElem($('.billing-done'));

    Plex.settings.getInvoiceHistoryAdmin();

    $('.billing-history').slideDown(400);

    Plex.settings.resetContinueBtn();
});

$(document).on('click', 'li.billing-tab.pricing', function() {
    Plex.settings.navtabswitching('pricing');

    Plex.settings.hideElem($('.plex-billing-default-section'));
    Plex.settings.hideElem($('.plex-billing-plan-section'));
    Plex.settings.hideElem($('.plex-textmsg-plan'));
    Plex.settings.hideElem($('.plex-textmsg-search-phone'));
    Plex.settings.hideElem($('.billing-info'));
    Plex.settings.hideElem($('.billing-checkout'));
    Plex.settings.hideElem($('.billing-done'));
    Plex.settings.showElem($('.plex-billing-price-section'));

    Plex.settings.getInvoiceHistoryAdmin();

    $('.billing-history').slideDown(400);

    Plex.settings.resetContinueBtn();
});

Plex.settings.showElem = function(elem) {
        elem.show();
}

Plex.settings.hideElem = function(elem) {
    if(elem.is(':visible')) {
        elem.hide();
    }
}

Plex.settings.navtabswitching = function(option) {
    var billing_tab = $('.plex-manage-billing-section');
    var billing_tab_plans = billing_tab.find('li.billing-tab.plans');
    var billing_tab_billing = billing_tab.find('li.billing-tab.billing');
    var billing_tab_invoices = billing_tab.find('li.billing-tab.invoices');
    var billing_tab_pricing = billing_tab.find('li.billing-tab.pricing');


    if(option == 'billing') {
        billing_tab.find('li.billing-tab').css('color', '#36AED8');
        billing_tab_billing.css('text-decoration', 'underline');
        billing_tab_plans.css('text-decoration', 'none');
        billing_tab_invoices.css('text-decoration', 'none');
        billing_tab_pricing.css('text-decoration', 'none');
    } else if (option == 'plans') {
        billing_tab.find('li.billing-tab').css('color', '#FFF');
        billing_tab_billing.css('text-decoration', 'none');
        billing_tab_plans.css('text-decoration', 'underline');
        billing_tab_invoices.css('text-decoration', 'none');
        billing_tab_pricing.css('text-decoration', 'none');
    } else if (option == 'invoices') {
        billing_tab.find('li.billing-tab').css('color', '#36AED8');
        billing_tab_billing.css('text-decoration', 'none');
        billing_tab_plans.css('text-decoration', 'none');
        billing_tab_invoices.css('text-decoration', 'underline');
        billing_tab_pricing.css('text-decoration', 'none');
    }  else if (option == 'pricing') {
        billing_tab.find('li.billing-tab').css('color', '#36AED8');
        billing_tab_billing.css('text-decoration', 'none');
        billing_tab_plans.css('text-decoration', 'none');
        billing_tab_invoices.css('text-decoration', 'none');
        billing_tab_pricing.css('text-decoration', 'underline');
    }
}

Plex.settings.resetContinueBtn = function() {
    var continue_step2 = $('.plex-textmsg-forward-step2 a.button');
    if(!continue_step2.hasClass('disabled'))
        continue_step2.addClass('disabled');
}


$(document).on('click', '.select-plan-btn:not(.disabled)', function(){
    var _this = $(this);
    var plex_text_msg = $('.plex-textmsg-plan'),
        plex_textmsg_plan_container = $('.plex-textmsg-plan-container'),
        billing_textmsg_tier = _this.data('tier');

    if( billing_textmsg_tier ){
        Plex.settings.textmsg_tier = billing_textmsg_tier;
    }

    if(billing_textmsg_tier == 'free') {
        plex_textmsg_plan_container.html('<select><option>Select</option><option value="plan-0">0-500&nbsp;&nbsp;&nbsp;&nbsp;free trial</option></select>');
        // $('select[name="' + billing_textmsg_tier + '"]').removeClass('hide');
    } else if(billing_textmsg_tier == 'pay_as_you_go') {
        var content_1 = '<select>';                
        content_1 += '<option value="">Select</option>';
        content_1 += '<option value="plan-1">1 - 1,000&nbsp;&nbsp;5 cents/msg</option>';
        content_1 += '<option value="plan-2">1,001 - 10,000&nbsp;&nbsp;4 cents/msg</option>';
        content_1 += '<option value="plan-3">10,001 - 100,000&nbsp;&nbsp;&nbsp;3 cents/msg</option>';
        content_1 += '<option value="plan-4">Unlimited&nbsp;&nbsp;2 cents/msg</option></select>';

        plex_textmsg_plan_container.html(content_1);
    } else {
        var content_2 = '<select>';
        content_2 += '<option value="">Select</option>';
        content_2 += '<option value="plan-1">1 - 1,000&nbsp;&nbsp;$40.00/month</option>';
        content_2 += '<option value="plan-2">1,001 - 10,000&nbsp;&nbsp;$300.00/month</option>';
        content_2 += '<option value="plan-3">10,001 - 100,000&nbsp;&nbsp;$2,000.00/month</option>';
        content_2 += '<option value="plan-4">Unlimited&nbsp;&nbsp;$3,000.00/month</option></select>';

        plex_textmsg_plan_container.html(content_2);
    }

    Plex.settings.navtabswitching('billing');

    Plex.settings.slidingHideEffect( $('.plex-billing-plan-section'), plex_text_msg, 'left', Plex.settings.slideEffectSpeed);

    // $('.back-to-plans-btn').show();

    // Plex.settings.slidingHideEffect( $('.plex-billing-plan-section'), billing_info, 'left', Plex.settings.slideEffectSpeed);

});

$(document).on('click', '.plex-manage-billing-section .back-to-plans-btn', function(){
    var billing_info = $('.billing-info'), checkout = $('.billing-checkout');

    $('.plex-billing-plan-section').addClass('billing-bg');
    $(this).hide();

    if( billing_info.is(':visible') ) Plex.settings.slidingHideEffect( billing_info, $('.plex-billing-plan-section'), 'right', Plex.settings.slideEffectSpeed);
    else if( checkout.is(':visible') ) Plex.settings.slidingHideEffect( checkout, $('.plex-billing-plan-section'), 'left', Plex.settings.slideEffectSpeed);
});

$(document).on('change', '.plex-textmsg-plan-container select', function() {
    var continue_step2 = $('.plex-textmsg-forward-step2 a.button');

    if($(this).val() != '') {
        Plex.settings.textmsg_plan = $(this).val();

        if(continue_step2.hasClass('disabled'))
            continue_step2.removeClass('disabled');
    } else {
        if(!continue_step2.hasClass('disabled'))
            continue_step2.addClass('disabled');
    }
});

$(document).on('click', '.plex-textmsg-forward-step2 a.button', function() {
    // go to step 2 
    $('.back-to-dash').html('');
    Plex.settings.slidingHideEffect($('.plex-textmsg-plan'), $('.plex-textmsg-search-phone'), 'left', Plex.settings.slideEffectSpeed);
});

$(document).on('click', '.plex-textmsg-search-phone .search-number a.button', function() {
    Plex.settings.search_number_views($(this));
});

$(document).on('change', 'input[name="phone-number-available"]', function() {
    Plex.settings.active_continue_btn();
});

$(document).on('click', '.phone-list-available a.button.search-again', function() {
    Plex.settings.search_number_reset();
});

$(document).on('change', 'input[name="isTollFree"]', function() {
    Plex.settings.search_number_reset();

    if($(this).val() == "yes") {
        Plex.settings.hideTollFreeNumSearch();        
    } else {
        Plex.settings.showTollFreeNumSearch();
    }
});

$(document).on('click', '.phone-list-available a.button.success, .plex-textmsg-forward-step3 a.button', function() {
    // go to step 3 (check out)
    // set phone number to Plex.settings.textmsg_phone
    if($('.plex-textmsg-search-phone .notify strong').length != 0) {
        Plex.settings.textmsg_phone = $('.plex-textmsg-search-phone .notify strong').html();
    } else {
        Plex.settings.textmsg_phone = $('.phone-list-view input[type="radio"]:checked').val();
    }

    Plex.settings.textmsg_price = Plex.settings.determinePaymentPriceAdmin();

    Plex.settings.slidingHideEffect( $('.plex-textmsg-search-phone'), $('.billing-info') , 'left', Plex.settings.slideEffectSpeed);
});

Plex.settings.active_continue_btn = function() {
    var continue_btn = $('.buy-option a.button.success');
    continue_btn.removeClass('disabled');
    continue_btn.css('pointer-events','all');
}

Plex.settings.search_number_views = function(elem) {
    $('.buy-option').remove();

    var form = elem.closest('form');
    var formdata = new FormData(form[0]);
    var show_more_phonelist = $('.show-more-phonelist');
    // var search_number = $('.search-number');

    Plex.settings.loadingStart();

    $.ajax({
        url: '/admin/textmsg/searchForPhoneNumbers',
        type: 'POST',
        data: formdata,
        contentType: false,
        processData: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    })
    .done(function(data) {
        data = JSON.parse(data);
        // console.log(data);
        var phone_list_view = $('.phone-list-view');

        phone_list_view.html('');
        // results injected phone_list_view into a template
        for(var idx in data) {
            if(idx) {
                // modify data[idx]
                var phone_converted = Plex.settings.phone_format_normalize(data[idx]); //(123) 456-7890
                var template = '<div class="column small-12 medium-12 large-6 end">';
                template += '<input id="phone-number-'+idx+'" name="phone-number-available" type="radio" value="'+ phone_converted +'">';
                template += '<label for="phone-number-'+idx+'"> ' + phone_converted + '</label></div>';
                phone_list_view.append(template);
            }
        }

    })
    .then(function(data) {
        Plex.settings.loadingEnd();

        data = JSON.parse(data);
        var phone_list_available_num = data.length;

        // set a limitation to show more
        if( phone_list_available_num > 0) {
            if( phone_list_available_num > 8 ) {
                show_more_phonelist.html('show more >>');
            }
            // remove error info
            $('.buy-option.errorinfo').remove();

            var content_1 = '<div class="column small-12 medium-12 large-12 buy-option">';
            content_1 += '<a href="#" class="button success disabled" style="pointer-events: none;">Continue</a>';
            content_1 += '<a href="#" class="button secondary search-again">Reset</a></div>';
            Plex.settings.phone_list_available.append(content_1);
        } else {
            // no phone number available
            Plex.settings.search_number.removeClass('hide');
            var content_2 = '<div class="column small-12 medium-12 large-12 buy-option errorinfo">';
            content_2 += '<span>*No phone number available please set another option.</span></div>';
            Plex.settings.phone_list_available.append(content_2);
        }
        
    }, function() {
        Plex.settings.loadingEnd();
        show_more_phonelist.html('');
    });

    if(!Plex.settings.search_number.hasClass('hide')){
        Plex.settings.search_number.addClass('hide');         
    }
    if(Plex.settings.phone_list_available.hasClass('hide')) {
        Plex.settings.phone_list_available.removeClass('hide');
    }
}

Plex.settings.phone_format_normalize = function(phone) {
    //normalize string and remove all unnecessary characters
    phone = (""+phone).replace(/[^\d]/g, "");
    phone = phone.slice(1);

    //check if number length equals to 10
    if (phone.length == 10) {
        //reformat and return phone number
        return phone.replace(/(\d{3})(\d{3})(\d{4})/, "(<strong>$1</strong>) $2-$3");
    }

    return null;
}

Plex.settings.search_number_reset = function() {
    // var input_area_code = $('.input-area-code');
    var show_more_phonelist = $('.show-more-phonelist');
    var phone_list_view = $('.phone-list-view');

    Plex.settings.input_area_code.find('input').prop('value', '');
    Plex.settings.input_area_code.find('select').prop('value', '');
    show_more_phonelist.html('');
    show_more_phonelist.show();
    phone_list_view.html('');
    phone_list_view.css('max-height', '130px');
    $('.buy-option').remove();
    Plex.settings.phone_list_available.addClass('hide');
    Plex.settings.search_number.removeClass('hide');
}

Plex.settings.hideTollFreeNumSearch = function() {

    if(!Plex.settings.phone_list_available.hasClass('hide')) {
        Plex.settings.phone_list_available.addClass('hide');
    }
    if(Plex.settings.search_number.hasClass('hide')) {
        Plex.settings.search_number.removeClass('hide');
    }
    Plex.settings.input_area_code.hide();
    Plex.settings.que_ans.hide();
}

Plex.settings.showTollFreeNumSearch = function() {

    if(!Plex.settings.phone_list_available.hasClass('hide')) {
        Plex.settings.phone_list_available.addClass('hide');
    }
    if(Plex.settings.search_number.hasClass('hide')) {
        Plex.settings.search_number.removeClass('hide');
    }
    Plex.settings.input_area_code.show();
    Plex.settings.que_ans.show();
}

/* //////////// old settings js that handles password change, not touching - end \\\\\\\\\\\\\\ */

/*function clearSelectedFromleftnav() {
    $('.menubutton').removeClass('selected');
}

function addParentCounselor(e) 
{
    $('#addParentCounselor').foundation('reveal', 'open');
}

function closeReveal(id)
{
    $('#'+id).foundation('reveal', 'close');
}

function PostGrantInfo()
{
        var input = $('#grantForm').serialize();
        //console.log(input);
        $.post('/setting/grantSetting/' + Plex.ajaxtoken, input, function(data, textStatus, xhr) {
        //console.log(data);
        closeReveal('#addParentCounselor');       
        loadProfileInfo("grantSetting");
    });
}
function show_setting_block()
{
    $('#SettingMenu').toggle(); 
}
$(document).mouseup(function (e)
{
    $('.closedivset').hide();   

});*/

