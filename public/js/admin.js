// admin.js
Plex.adminDash = {
	// list_of_new_features: [],
	// start_index: 0,
	// next_index: 1,
	// list_length: 0,
	// after_doc_ready: false,
	// features_inteval: null,
	goalSetSuccess: {
        textColor: '#fff',
        bkg : '#26B26B',
        msg: 'You have successfully set your goals!',
        type: 'soft',
        dur : 5000
    },
}

$(document).ready(function(){

	var remind = parseInt($('#exist-member-reminder-modal').data('remind')) || 0;
	// var textmsg_remind = parseInt($('#textmsg-expire-reminder-modal').data('remind')) || 0;

	if( remind ){
		$('#exist-member-reminder-modal').foundation('reveal', 'open');
	} 
	// else if( textmsg_remind ) {
	// 	$('#textmsg-expire-reminder-modal').foundation('reveal', 'open');
	// }

	var processing_modal = $('#export-processing-modal');
	
    if( processing_modal.length === 1 && processing_modal.data('processing-export') ){
        processing_modal.foundation('reveal', 'open');
    }

}); 
// end of ready function
/*
$(document).on('keydown', "#spinner-applied , #spinner-enrolled", function (e) {
    // Allow: backspace, delete, tab, escape, enter 
    if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110]) !== -1 ||
         // Allow: Ctrl+A, Command+A
        (e.keyCode == 65 && ( e.ctrlKey === true || e.metaKey === true ) ) || 
         // Allow: home, end, left, right, down, up
        (e.keyCode >= 35 && e.keyCode <= 40)) {
             // let it happen, don't do anything
             return;
    }
    // Ensure that it is a number and stop the keypress
    if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
        e.preventDefault();
    }
});*/
/*
$(document).on('click', "#set-goal-btn", function() {
	var spinner_applied = $("#spinner-applied");
	var spinner_enrolled = $('#spinner-enrolled');
	var goals = Plex.adminDash.getGoals(spinner_applied.val(), spinner_enrolled.val());

	$(this).addClass('disabled');

	if( (spinner_applied.val() > 0 && spinner_applied.val() <= 500) && (spinner_enrolled.val() > 0 && spinner_enrolled.val() <= 500) ) {
		$('.err-msg').slideUp(250);
		$.ajax({
			url: '/admin/setgoals',
			type: 'POST',
			data: goals
		}).done(function(data){
			topAlert(Plex.adminDash.goalSetSuccess);
			window.location.reload();
		});
	}else {
		$('.err-msg').slideDown(250);
	}
});*/

// $(document).on('click', '#goal-setting #start-here', function() {
// 	$('#goal-setting').hide();
// 	$('#new-member-reminder-modal').show();
// });

/* $(document).on('click', "#goal-setting #appointment-confirm", function() {
	$(this).addClass('disabled');
	
	$.ajax({
	  url: '/admin/appointmentWasSet',
	  type: 'POST',
	  data: {'appointment_set': 1},
	}).done(function(data){
		window.location.reload();
	});
}); 

Plex.adminDash.getGoals = function(){
	var start = null, end = null, parsedDate = null,
		args = Array.prototype.slice.call(arguments);

	if( $('#prem_start_end_date').val() ){
		parsedDate = Plex.adminDash.splitDate($('#prem_start_end_date').val());
		start = parsedDate[0];
		end = parsedDate[2];
	}

	return {
		num_of_applications: args[0],
		num_of_enrollments: args[1],
		premier_start_date: start,
		premier_end_date: end
	};
};

Plex.adminDash.splitDate = function(date){
	return date.split(' ');
};


$(document).on('click', '.mod-goals-btn', function(){
	Plex.adminDash.toggleGoalSettingDisplay();
});

Plex.adminDash.toggleGoalSettingDisplay = function(){
	var goal_meters = $('.goal-meters-container');
	var set_goals = $('#goal-setting-context');

	if( goal_meters.hasClass('hide') ){
		set_goals.addClass('hide');
		goal_meters.removeClass('hide');
	}else{
		goal_meters.addClass('hide');
		set_goals.removeClass('hide');
	}
}*/
/*
$(document).on('click', '.meter-type', function(){
	var _this = $(this);
	var annual_meters = $('.annual-meter');
	var meter = _this.data('meter');
	var approvedNumber = _this.data('num');

	var approvedGoal = $(".approvedGoal");

	if (meter == 'annually') {
		approvedGoal.html("<h3>"+(approvedNumber*12)+"</h3>");

	}else if(meter == 'quarterly'){
		approvedGoal.html("<h3>"+(approvedNumber*3)+"</h3>");

	}else if(meter == 'monthly'){
		approvedGoal.html("<h3>"+(approvedNumber*1)+"</h3>");
	}

	//toggle active class
	$('.meter-type').removeClass('active');
	_this.addClass('active');

	$('.appr-meter').hide(100);
	$('.appr-meter.'+meter).fadeIn(500);

	//toggle meter displays
	if( _this.hasClass('annual') && !annual_meters.is(':visible') ){
		annual_meters.fadeIn(500);
	}else if( annual_meters.is(':visible') ){
		annual_meters.hide(100);
	}
});*/

// $(document).on('click', '.manage-student-features-container a', function(e){
// 	if( $('.manage-student-features-container').hasClass('inactive') ){
// 		e.preventDefault();
// 	}
// });

//when user is this tab is not active (if user in on a different tab/window), then stop the interval
/*
$(window).blur(function(){
	clearInterval(Plex.adminDash.features_interval);
	Plex.adminDash.after_doc_ready = true;
});

//start interval again when user back - ran after the initial interval has been ran and cleared
$(window).focus(function(){
	if(Plex.adminDash.after_doc_ready){
		Plex.adminDash.features_interval = setInterval(function(){ 
			Plex.adminDash.newFeatureSlider();
		}, 7000);
	}
});

//logic for cycling through each new feature
Plex.adminDash.newFeatureSlider = function(){
	var start_over = 0;

	//perform slide effect
	Plex.adminDash.slideHide( Plex.adminDash.list_of_new_features[Plex.adminDash.start_index], Plex.adminDash.list_of_new_features[Plex.adminDash.next_index], 'left', 750 );

	//increment array index
	Plex.adminDash.start_index++;
	Plex.adminDash.next_index++;

	//check index have reached the end, if so, start counter over
	if( Plex.adminDash.next_index >= Plex.adminDash.list_length ){
		Plex.adminDash.next_index = start_over;
	}else if( Plex.adminDash.start_index >= Plex.adminDash.list_length ){
		Plex.adminDash.start_index = start_over;
	}
}

//slide hide
Plex.adminDash.slideHide = function( hide_elem, show_elem, dir, speed ){
    
    var oppositeDirection = 'left';

    if( dir === 'left' ){
        oppositeDirection = 'right';
    }

    $(hide_elem).hide('slide', {direction: dir}, speed, function(){
        Plex.adminDash.slideShow( show_elem, oppositeDirection, speed );
    });
}

//slide show
Plex.adminDash.slideShow = function( elem, dir, speed ){
    $(elem).show('slide', {direction: dir}, speed);
}
*/
/*
made by Shelley -- temparary modal for notice text msg expire within 15 days
Plex.adminDash.textmsg_expire_reminder_modal_open = function() {
	$("#textmsg-expire-reminder-modal").foundation("reveal", "open");
}

Plex.adminDash.textmsg_expire_reminder_modal_close = function() {
	$("#textmsg-expire-reminder-modal").foundation("reveal", "close");
}

$("#exist-member-reminder-modal").bind("closed", function() {
    Plex.adminDash.textmsg_expire_reminder_modal_open();
});

$("#textmsg-expire-reminder-modal").bind('closed', function(event) {
	var _this = $(this);
	// do ajax call like appliedRemindMeLater
	$.post('/admin/ajax/textmsgRemindMeLater', {textmsg_remind: 1, type: 'admin'}, function(){
		Plex.adminDash.textmsg_expire_reminder_modal_close();
	});

});

$(document).on('click', '.button.set-auto-renew-btn', function() {
	var auto_renew = $('input[name="set-auto-renew"]');
	var msg = 'We are no longer automatically renewing your plan and you will no longer on this plan at the end of the current billing cycle.';

    if(auto_renew) {
    	if( auto_renew.is(':checked') ) {
    		msg = "You are back on recurring payments and we will continue to automatically renew your plan.";
    	}

    	$.post('/setting/toggleAdminUserRecurring', {recurring: auto_renew.is(':checked')}, function() {
			Plex.adminDash.textmsg_expire_reminder_modal_close();

	        topAlert({
	            textColor: '#fff',
	            backgroundColor: 'green',
	            msg: msg,
	            type: 'soft',
	            dur: 15000
	        });
	    });
    }
	
});

*/
/* JS used for old admin News stuff. Andrew made
    $(document).foundation();
	$('#news_category').change(function(){
		var categoryId = this.value;
		$.ajax({
			url: '/getsubcategory',
			type: 'GET',
			data: {'categoryId':categoryId},
			dataType: 'json'
		}).done(function(id){
			$('#news_subcategory')
				.find('option')
				.remove()
				.end()
			;

			$.each(id, function(index, object){
				$('#news_subcategory').append('<option value=' + object['id'] + '>' + object['name'] + '</option>');
			});
		});
	});

	$('input[name=source]').change(function(){
		if($('input[name=source]:checked').val() == 'external'){
			formExternal.slideDown();
			$('.externalFields').attr('required', '');
			$(document).foundation();
		}else{
			formExternal.slideUp();
			$('.externalFields').removeAttr('required');
			$(document).foundation();
		}
		//formExternal.toggle();
	});
	$('#news_category').change(function(){
		if($('#news_category').val() != ''){
			subCatRow.slideDown();
			$('#news_category').attr('required', '');
			$(document).foundation();
		}else{
			subCatRow.slideUp();
			$('#news_category').removeAttr('required');
			$(document).foundation();
		}
	});

	$('document').ready(function(){
		formExternal = $('#formExternal');
		if($('input[name=source]:checked').val() == 'external'){
			formExternal.show();
			$('.externalFields').attr('required', '');
			$(document).foundation();
		}else{
			formExternal.hide();
			$('.externalFields').removeAttr('required');
			$(document).foundation();
		}
		subCatRow = $('#subCatRow');
		if($('#news_category').val() == ''){
			console.log('selected news cat correctly!');
			subCatRow.hide();
		}
	});
*/