// appliedStudents.js
// and Group Messaging Button click functions

var Plex = Plex || {};

//Revealing Module Pattern
Plex.appliedStudents = (function($){
	'use strict';

	// -- private vars 

	var has_applied, has_enrolled, get_user_id, build_data, build_data_applied, build_data_enrolled, toggle_enrolled_state, make_enrolled, send_data, starred, toggle_applied_state, remind_data, make_applied, //applied student function vars
		add_to_list, add_all_to_list, remove_from_list, student, get_user_name, save_elem, already_in_list, send_to_group_messaging_page, send_to_text_messaging_page, remove_all_from_list, update_count_indicator, //group msg function vars
		yes = 0, no = 1, u_id, u_applied, u_enrolled, current_elem, set_admin_type, remind_me_next_month, remind_me_later, open_applied_modal, close_applied_modal, open_textMsgError_modal, close_textMsgError_modal, //universal vars
		adminType = 'admin', make_applied_route = '/ajax/applied', applied_reminder_route = '/ajax/appliedReminder', //routes
		remind_me_later_route = '/ajax/appliedRemindMeLater', appliedModal = '#applied-student-reminder-modal', //routes
		textMsgErrorModal = '#text-message-error-modal',
		starred_src = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/applied.jpg', //img src
		starred_src_enrolled = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/greenstar.jpg', //img src
		notstarred_src = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/notapplied.jpg', //img src
		student_list = [], rows_selected_count = 0, counter = 0, new_student, set_group_msg_route,
		make_enrolled_route = '/ajax/enrolled'; //group messaging vars

	//Student constructor
	var Student = function Student(id, name){
		this.id = id;
		this.list_id = ++counter,
		this.name = name;
	};	

	// -- private functions

	//check if user is already an applied student or not
	has_applied = function(){
        return current_elem.find('.applied-star').hasClass('applied');
	};

	has_enrolled = function() {
        return current_elem.find('.applied-star').hasClass('enrolled');
	}

	//return current user's hashed id
	get_user_id = function(){
		return current_elem.closest('.inquirie_row').find('.messageName').data('hashedid');
	};

	get_user_name = function(){
		return current_elem.closest('.inquirie_row').find('.messageName .inquiry-name').text();
	};

	//return obj containing user id and if applied already or not
	build_data_applied = function(){
		u_id = get_user_id();
		u_applied = has_applied() ? yes : no;

		return {
			id: u_id,
			applied: u_applied,
			type: adminType,
		}
	};

	build_data_enrolled = function() {
		u_id = get_user_id();
		u_enrolled = has_enrolled() ? yes : no;
		return {
			id: u_id,
			enrolled: u_enrolled,
			type: adminType,
		}
	};


	//save the current/clicked element
	save_elem = function(elem){
		current_elem = elem;
	};

	//toggle star image src
	toggle_applied_state = function(){
		starred = has_applied();
		current_elem.toggleAppliedState(starred);	
	};

	toggle_enrolled_state = function() {
		starred = has_enrolled();
		current_elem.toggleEnrolledState(starred);
	}

	remind_data = function(remind){
		return {
			remind_next_month: yes,
			type: adminType,
			remind_me_later: remind || 0
		};
	};

	//jquery plugin - toggle applied state
	$.fn.toggleAppliedState = function( starred ){
		var star = this.find('.stars');

		// if already starred/applied
		// else not yet applied
		if( star.hasClass('applied') ){
			// if it was previously user-applied, add that back
			// else add applied
			if( star.hasClass('was-user-applied') ){
				star.removeClass('applied was-user-applied').addClass('user-applied');
			}else{
				star.removeClass('applied').addClass('no');
			}

		}else{
			//if star is user-applied, remove user-applied style and add no style and add tmp was-user-applied to find later
			//else remove applied style and add no style
			if( star.hasClass('user-applied') ){
				star.removeClass('user-applied').addClass('applied was-user-applied');
			}else{
				star.removeClass('no').addClass('applied');
			}
		}
		
		return this;
	};


	//jquery plugin - toggle enrollment state
	$.fn.toggleEnrolledState = function(starred) {
		var star = this.find('.stars');

		if( star.hasClass('enrolled') ) star.removeClass('enrolled').addClass('no');
		else star.removeClass('no').addClass('enrolled');

		return this;
	};


	// -- public functions 
	make_applied = function(elem){
		//save elem so other functions can use current elem
		save_elem(elem);

        var inqRow = elem.closest('.inquirie_row');

        var student_name = inqRow.find('.inquiry-name').html().trim();
        var student_user_id = inqRow.data('uid');
        var student_country = inqRow.find('.countryName').html().trim();

        var mixpanel_student = {
            'Student Name': student_name,
            'Student User ID': student_user_id,
            'Student Country': student_country,
        }

		//build ajax post data containing user id and 0 or 1 to make applied or not
		send_data = build_data_applied();

		//save to db
		$.ajax({
            url: '/' + adminType + make_applied_route, 
            data: send_data,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'POST'
        }).done(function(ret){
			toggle_applied_state();

            if (send_data.applied == 0) {
                mixpanel.track('admin-unmark-applied', mixpanel_student);

            } else {
                mixpanel.track('admin-mark-applied', mixpanel_student);
            }
		});
	};

	make_enrolled = function(elem){
		save_elem(elem);

        var inqRow = elem.closest('.inquirie_row');

        var student_name = inqRow.find('.inquiry-name').html().trim();
        var student_user_id = inqRow.data('uid');
        var student_country = inqRow.find('.countryName').html().trim();

        var mixpanel_student = {
            'Student Name': student_name,
            'Student User ID': student_user_id,
            'Student Country': student_country,
        }

		send_data = build_data_enrolled();

		$.ajax({
            url: '/' + adminType + make_enrolled_route, 
            data: send_data,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'POST'
        }).done(function(ret){
			toggle_enrolled_state();

            if (send_data.enrolled == 0) {
                mixpanel.track('admin-unmark-enrolled', mixpanel_student);

            } else {
                mixpanel.track('admin-mark-enrolled', mixpanel_student);
            }
		});
	}

	set_admin_type = function(type){
		adminType = type;
	};

	remind_me_next_month = function(){
		send_data = remind_data();
		
		$.ajax({
            url: '/' + adminType + applied_reminder_route, 
            data: send_data,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'POST'
        }).done(function(ret){
			close_applied_modal();
		});
	},

	remind_me_later = function(){
		send_data = remind_data(no);

		$.ajax({
            url: '/' + adminType + remind_me_later_route, 
            data: send_data,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'POST'
        }).done(function(){
			close_applied_modal();
		});
	},

	open_applied_modal = function(){
		$(appliedModal).foundation('reveal', 'open');
	};

	close_applied_modal = function(){
		$(appliedModal).foundation('reveal', 'close');
	};

	open_textMsgError_modal = function(){
		$(textMsgErrorModal).foundation('reveal', 'open');
	};

	close_textMsgError_modal = function(){
		$(textMsgErrorModal).foundation('reveal', 'close');
	};

	// -- group messaging functions
	already_in_list = function(std_id){
		return _.findWhere(student_list, {id: std_id});
	}

	add_to_list = function(elem){
		save_elem(elem);
		u_id = get_user_id();
		name = get_user_name();	
		// in_list = already_in_list(u_id);

		if( !already_in_list(u_id) ){
			new_student = new Student(u_id, name);
			student_list.push(new_student);
		}

		update_count_indicator();
	};

	add_all_to_list = function(elems){
		//loop through each element to save each to list
		elems.each(function(){
			var self = $(this);
			add_to_list(self);
		});
	};

	remove_from_list = function(elem){
		save_elem(elem);
		u_id = get_user_id();

		//remove Student from array
		student_list = _.reject(student_list, {id: u_id});

		//update count in view
		update_count_indicator();
	};

	remove_all_from_list = function(){
		//empties student list array
		student_list.length = 0;
		//update count in view
		update_count_indicator();
	};

	update_count_indicator = function(){
		$('.chosen-count-display').html(student_list.length);
	};

	send_to_group_messaging_page = function(){
		set_group_msg_route = '/' + adminType + '/groupmsg';

		//if student rows were selected then save to cache, done with laravel, otherwise just redirect to group message page
		if( student_list.length > 0 ){
			$.ajax({
				url: '/' + adminType + '/setGroupMsg',
				type: 'POST',
				data: JSON.stringify(student_list),
				dataType: 'json',
				contentType: 'application/json',
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				complete: function(data){
					window.location.href = set_group_msg_route;
				}
			});
		}else{
			window.location.href = set_group_msg_route;
		}
		
	};

	send_to_text_messaging_page = function() {
		var set_text_msg_route = '/' + adminType + '/textmsg';

		if( student_list.length > 0) {
			$.ajax({
				url: '/' + adminType + '/setGroupMsgForText',
				type: 'POST',
				dataType: 'json',
				contentType: 'application/json',
				data: JSON.stringify(student_list),
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				complete: function(data) {
					if (data.responseText == 'success')
						window.location.href = set_text_msg_route;
					else
						Plex.appliedStudents.openTxtMsgErrorModal();
				}
			});
		} else {
			window.location.href = set_text_msg_route;
		}
	}


	// -- return publicly accessible obj props
	return {
		applied: make_applied,
		enrolled : make_enrolled,
		setType: set_admin_type,
		openModal: open_applied_modal,
		closeModal: close_applied_modal,
		openTxtMsgErrorModal: open_textMsgError_modal,
		closeTxtMsgErrorModal: close_textMsgError_modal,
		remindMeNextMonth: remind_me_next_month,
		remindMeLater: remind_me_later,
		sendToGM: send_to_group_messaging_page,
		sendToTM: send_to_text_messaging_page,
		addStudent: add_to_list,
		addAllStudents: add_all_to_list,
		removeStudent: remove_from_list,
		removeAllStudents: remove_all_from_list
	};	

})($);

$(document).ready(function(){
	var type, parsedType, remind;

	//get and set admin type
	type = $('.each-inquirie-container').data('page-type');
	parsedType = type.substring(0, type.indexOf('-'));

	//get reminder value 
	remind = parseInt($('#applied-student-reminder-modal').data('remind'));

	Plex.appliedStudents.setType(parsedType);

	//0 = dont open reminder, 1 = open reminder
	if( remind )
		Plex.appliedStudents.openModal();
});

//trigger saving of applied or not on clicking of star
$(document).on('click', '.applied-star.lg:not(.prescreened)', function(){
	var _this = $(this);
	Plex.appliedStudents.applied(_this);
});

$(document).on('click', '.enrolled-star.lg:not(.prescreened)', function(){
	var _this = $(this);
	Plex.appliedStudents.enrolled(_this);
});

$(document).on('click', '.remind-later-btn', function(){
	Plex.appliedStudents.remindMeLater();
});

$(document).on('click', '#applied-student-reminder-modal .close-reveal-modal', function(){
	Plex.appliedStudents.remindMeLater();
});

$(document).on('click', '.ok-btn', function(){
	Plex.appliedStudents.remindMeNextMonth();
});

$(document).on('change', '.student-row-chkbx', function(){
	var _this = $(this), all_chkbx = $('.student-row-chkbx-all');
	if( _this.is(':checked') ){
		Plex.appliedStudents.addStudent(_this);
	}else{
		if( all_chkbx.is(':checked') )
			all_chkbx.prop('checked', false);

		Plex.appliedStudents.removeStudent(_this);
	}
});

$(document).on('change', '.student-row-chkbx-all', function(){
	var all_selected_rows;

	if( $(this).is(':checked') ){
		all_selected_rows = $('.student-row-chkbx').prop('checked', true);
		Plex.appliedStudents.addAllStudents(all_selected_rows);
	}else{
		all_selected_rows = $('.student-row-chkbx').prop('checked', false);
		Plex.appliedStudents.removeAllStudents();
	}

});

$(document).on('click', '.group-msg-btn', function(){
	Plex.appliedStudents.sendToGM();
});

$(document).on('click', '.text-msg-btn', function() {
	Plex.appliedStudents.sendToTM();
});