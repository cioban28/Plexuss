// groupMessaging.js

var Plex = Plex || {};

$(function() {
 	$('.date-cal').daterangepicker({
    	singleDatePicker: true,
    	showDropdowns: true,
    	opens: 'left',
    	drops: 'up',
    	startDate: moment(),
    	minDate: moment(),
    }, function(start, end, label){
    	$('.date-cal').trigger('change');
    });
});

Plex.gM = (function($){
	'use strict';

	var start_campaign_elem = $('.campaign-start'), choose_campaign_elem = $('.campaign-choose'), edit_campaign_elem = $('.campaign-edit'), date_time_container_elem = $('.date-time-select-container'), current_campaign_id = $('#current-campaign-id'),//variable definitions 
		preview_iphone = $('.iphone'), preview_macbook = $('.macbook-pro'), preview_btn = $('.preview-btn'), preview_container = $('.preview-container') , iphone = $('.iphone .screen'), macbook = $('.macbook-pro .monitor'), pending_on_off = $('#pending-on-off'),//variable definitions
		name_elem = $('.c-name'), subj_elem = $('.c-subj'), body_elem = $('.c-body'), schedule_chkbx_elem = $('#schedule_later'), date_elem = $('.date-cal'), //variable definitions
		hr_elem = $('select[name="hours"]'), min_elem = $('select[name="minutes"]'), period_elem = $('select[name="period"]'), rec_cnt_elem = $('.rec-count'), camp_textarea_cnt = $('.content-options-row .textCnt'),//variable definitions
		rec_ul_elem = $('.recipient-ul'), results_elem = $('.results'), searched_camp_elem = $('.searched-campaigns'), prev_camp_container_elem = $('.previous-campaign-container'), text_SMS_elem = $('input[name="text_option"][value="SMS"]'), text_MMS_elem = $('input[name="text_option"][value="MMS"]'),//variable definitions
		save_route = '/saveCampaign', admin_type = 'admin', temp_arr = [], duplicate_found = false, sched_camp_container_elem = $('.scheduled-campaigns-container'), c_list_elem = $('.c-list'), campaign_list_shown = $('.campaign-list'), create_campaign_btn = $('.create-campaign-btn.button'),//variable definitions
		edit_list_btn_elem = $('.edit-std-list-btn'), message_template_dropdown = $('#message_template_dropdown'), insert_message_template = $('#insert_message_template'), rec_profile_elem = $('.r-profile-view'),
		camp_textarea = $('.camp-textarea'), analytics_container = $('.analytics-container'), previous_campaigns_container = $('.previous-campaign-container'), camps_set = new Set(), send_campagin_msg_modal = $('#send-campaign-msg-modal'), sending_msg_content = $('#send-campaign-msg-modal .msg'), send_away = $('#send-campaign-msg-modal .send-away-btn'),
		automatic_camp_btn = $('a.action-bar-btn'), automatic_camp_elem = $('.auto-camp-list'), cost_preview_container = $('.cost-preview-container'), cost_detail_table = $('#cost-details'), free_trial = $('.free-trial'), is_toll_free = $('input[name="isTollFree"]'), send_textmsg_notify = $('#send-campaign-msg-notify-modal'), send_success_modal = $('#send-success-modal'),
		input_area_code = $('.input-area-code'), area_code = $('.free-trial input[name="area-code"]'), states = $('.free-trial select[name="states"]'), phone_list_view = $('.phone-list-view'), number_details = $('.number-details'), que_ans = $('.qa'),
		search_number = $('.search-number'), phone_list_available = $('.phone-list-available'), show_more_phonelist = $('.show-more-phonelist'), lg_preview_btn = $('.lg-preview-btn'), setup_phone_number = $('.setup-phone-number'), change_plan = $('.pricing .change-plan'),
		show_more_phone_number, search_number_reset, current_campaign, campaign_list = [], oppositeDirection, template, html, student_list, data, new_student_props, temp, tab, //variable declarations
		toggle_campaign_views, toggle_campaign_views_step1_forward, toggle_campaign_views_step1_backward ,hide_loader, show_loader, hide_elem, show_elem, remove_hide_class_once, toggle_preview_view, toggle_preview_container, get_preview_template, set_campaign, savedMsg, failedMsg,//function variable declarations
		create_new_campaign, update_campaign, toggle_date_display, save_campaign, set_admin_type, render_preview, render_view, render_recipient_display, render_analytics, campaigns_existed_collection, render_campaign_choose,//function variable declarations
		get_campaign_info, msg_modal_open, msg_modal_close, msg_notify_open, msg_notify_close, sending_msg, scheduled_msg, build_msg_modal, validate_campaign, set_student_list, get_recipient_list_template, render_text_message_summary, send_campagin,//function variable declarations
		remove_student_from_list, search_students, update_search_results, no_results_template, results_template, toggle_results_view, get_search_data, searchData, toggle_exclude_stu_messaged, //function variable declarations
		search_campaigns, search_camp_template, toggle_searched_camp_view, toggle_lists, get_camp_recipients, remove_camp_recipients , add_from_prev_camps, remove_camp_recipients, remove_from_prev_camps, render_profile,
		open_save_message_templates, save_message_templates, load_message_templates, add_file, img_template, form, form_data, regex, delete_campaign, hidden_campaigns, setAutomaticCampaign, toggleAutomaticCamp, order_summary_modal_open, order_summary_modal_close,
		search_number_views, confirm_number_chosen, back_to_search_number, forward_to_textmsg, hide_toll_free_number_search, show_toll_free_number_search, upload_csv, phone_format_normalize, active_continue_btn, activate_send_away, deactivate_send_away,
		init = {
			done: 0
		};

	//Campaign constructor
	var Campaign = function Campaign(name, subj, body, schedule_date){
		this.id = null;
		this.c_name = name || '';
		this.c_subject = subj || '';
		this.c_body = body || '';
		this.save = true;
		this.send = false;
		this.schedule_later = false;
		this.date = schedule_date || null;
		this.last_sent_on = null;
		this.is_mms = 0;
		this.hr = '6';
		this.min = '00';
		this.period = 'am';
		this.is_default = true;
		this.isValid = false;
		this.recipients = 0;
		this.students = new StudentList();
		this.files = [];
		this.total_num_campaign_messages = null;
		this.total_num_campaign_read_messages = null;
		this.total_num_campaign_replied_messages = null;
		this.read_response_rate = null ;
		this.replied_response_rate = null ;
	};

	Campaign.prototype.setId = function(val){
		this.id = val;
	}

	Campaign.prototype.setName = function(val){
		this.c_name = val;
	};

	Campaign.prototype.setSubject = function(val){
		this.c_subject = val;
	};

	Campaign.prototype.setBody = function(val){
		this.c_body = val;
	};

	Campaign.prototype.setSave = function(val){
		this.save = val;
	};

	Campaign.prototype.setSelectedCnt = function(val) {
        this.recipients = val;
    }

	Campaign.prototype.setSend = function(val){
		this.send = val;
	};

	Campaign.prototype.setIsMMS = function(val) {
		this.is_mms = val;
	}

	Campaign.prototype.scheduleForLater = function(val){
		this.schedule_later = val;
	};

	Campaign.prototype.setDate = function(val){
		this.date = val;
	};

	Campaign.prototype.setHr = function(val){
		this.hr = val;
	};

	Campaign.prototype.setMin = function(val){
		this.min = val;
	};

	Campaign.prototype.setPeriod = function(val){
		this.period = val;
	};

	Campaign.prototype.formatDate = function(){
		return moment(this.date).format('dddd, MMM Do YYYY');
	};

	Campaign.prototype.formatTime = function(){
		return this.hr + ':' + this.min + ' ' + this.period;
	};

	Campaign.prototype.isNoLongerDefault = function(){
		this.is_default = false;
	};

	Campaign.prototype.setValidity = function(val){
		this.isValid = val;
	};

	Campaign.prototype.addStudents = function(val){
		this.students.add(val);
	};

	Campaign.prototype.removeStudentAt = function(val){
		this.students.removeWithId(val);
	};

	Campaign.prototype.studentCount = function(val){
		return this.students.list.length;
	};

	Campaign.prototype.findDuplicate = function(rec){
		return this.students.findDup(rec);	
	};

	Campaign.prototype.addFile = function(file){
		this.files.push(file);
	}

	Campaign.prototype.getAnalytics = function() {
		return {'total_num_campaign_messages' : this.total_num_campaign_messages ,
				'total_num_campaign_read_messages' : this.total_num_campaign_read_messages ,
				'total_num_campaign_replied_messages' : this.total_num_campaign_replied_messages ,
				'read_response_rate' : this.read_response_rate ,
				'replied_response_rate' : this.replied_response_rate
		};
	}

	//Student constructor
	var Student = function Student(init){
		this.props = init || {};
	};

	//StudentList constructor
	var StudentList = function StudentList(){
		this.list = [];
	};

	StudentList.prototype.add = function(std){
		this.list.push(std);
	};

	StudentList.prototype.removeWithId = function(uid){
		for( var i = 0; i < this.list.length; i++ ){
			// console.log(">>>>>" + this.list[i].props.user_id);
			// console.log("<<<<<" + uid);
			// console.log(this.list[i].props.user_id == uid);
			if( this.list[i].props.user_id == uid ) {
				console.log('test inside the removeWithId');
				this.list.splice(i, 1);
			}
		}
	};

	// -- private functions

	//show ajax loader
	show_loader = function(){
		$('.manage-students-ajax-loader').show();
	};

	//hide ajax loader
	hide_loader = function(){
		$('.manage-students-ajax-loader').hide();
	};

	//hide slide effect
	hide_elem = function( elem_to_hide, elem_to_show, dir, speed ){
	    oppositeDirection = 'left';

	    if( dir === 'left' )
	        oppositeDirection = 'right';

	    $(elem_to_hide).hide('slide', {direction: dir}, speed, function(){
	        show_elem( elem_to_show, oppositeDirection, speed );
	    });
	};

	//show slide effect
	show_elem = function( elem, dir, speed ){
		if( !init.done )
			remove_hide_class_once();

	    $(elem).show('slide', {direction: dir}, speed);
	};

	remove_hide_class_once = function(){
		if( start_campaign_elem.hasClass('hide') )
			start_campaign_elem.removeClass('hide');
		else
			edit_campaign_elem.removeClass('hide');

		init.done = 1
	};

	// -- init functions 
	search_number_views = function(elem) {
		$('.buy-option').remove();
		// here insert ajax call to post area-code or state infomation
		// ... area_code.val()
		// ... states.val()
		var form = elem.closest('form');
		var formdata = new FormData(form[0]);

		show_loader();

		$.ajax({
			url: '/' + admin_type + '/textmsg/searchForPhoneNumbers',
			type: 'POST',
			data: formdata,
			contentType: false,
            processData: false,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		})
		.done(function(data) {
			data = JSON.parse(data);
			// console.log(data);

			phone_list_view.html('');
			// results injected phone_list_view into a template
			for(var idx in data) {
				if(idx) {
					// modify data[idx]
					var phone_converted = phone_format_normalize(data[idx]); //(123) 456-7890
					var template = '<div class="column small-12 medium-12 large-6 end">';
					template += '<input id="phone-number-'+idx+'" name="phone-number-available" type="radio" value="'+ phone_converted +'">';
			 		template += '<label for="phone-number-'+idx+'"> ' + phone_converted + '</label></div>';
					phone_list_view.append(template);
				}
			}

		})
		.then(function(data) {
			hide_loader();

			data = JSON.parse(data);
			console.log(data);
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
				phone_list_available.append(content_1);
			} else {
				// no phone number available
				search_number.removeClass('hide');
				var content_2 = '<div class="column small-12 medium-12 large-12 buy-option errorinfo">';
				content_2 += '<span>*No phone number available please set another option.</span></div>';
				phone_list_available.append(content_2);
			}
			
		}, function() {
			hide_loader();
			show_more_phonelist.html('');
		});

		if(!search_number.hasClass('hide')){
			search_number.addClass('hide');			
		}
		if(phone_list_available.hasClass('hide')) {
			phone_list_available.removeClass('hide');
		}
	}

	confirm_number_chosen = function() {
		if(!phone_list_available.hasClass('hide')) {
			phone_list_available.addClass('hide');
		}
		if(!free_trial.hasClass('hide')) {
			free_trial.addClass('hide');
		}
		if(setup_phone_number.hasClass('hide')) {
			setup_phone_number.removeClass('hide');
		}

		// inject number chosen to next steps:
		var toll_number_chosen = phone_list_view.find('input[type="radio"]:checked');
		number_details.html(toll_number_chosen.val());
	}

	phone_format_normalize = function(phone) {
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

	// -- public functions
	toggle_campaign_views = function(){
		if( start_campaign_elem.is(':visible') ){
			// show choose campaign
			hide_elem(start_campaign_elem, choose_campaign_elem, 'left', 20);
			// hide camp_textarea
			if(!camp_textarea.hasClass('hide-textarea'))
				camp_textarea.addClass('hide-textarea');
			// hide analytics
			if(!analytics_container.hasClass('hide'))
				analytics_container.addClass('hide');

			// hide iphone/mac preview
			if(!preview_container.hasClass('hide'))
				preview_container.addClass('hide');

			if(!lg_preview_btn.hasClass('hide'))
				lg_preview_btn.addClass('hide');

			if(cost_preview_container.hasClass('hide'))
				cost_preview_container.removeClass('hide');

		} else if(edit_campaign_elem.is(':visible')){
			//show choose campaign
			hide_elem(edit_campaign_elem, choose_campaign_elem, 'left', 20);
			if(!camp_textarea.hasClass('hide-textarea'))
				camp_textarea.addClass('hide-textarea');
			if(!analytics_container.hasClass('hide')) 
				analytics_container.addClass('hide');
			if(!preview_container.hasClass('hide'))
				preview_container.addClass('hide');

			if(cost_preview_container.hasClass('hide'))
				cost_preview_container.removeClass('hide');

		} else {

		}
	};

	toggle_campaign_views_step1_forward = function() {
		// show step 2 : edit_campaign_elem
		if(choose_campaign_elem.is(':visible')) {
			// console.log('it is wierd');
			hide_elem(choose_campaign_elem, edit_campaign_elem, 'left', 20);
			// show camp_textarea
			if(camp_textarea.hasClass('hide-textarea'))
				camp_textarea.removeClass('hide-textarea');
			// show tinymce
			tinymce.get('textarea-editor').focus();
			tinymce.focusedEditor.setContent( current_campaign.c_body );
			tinymce.activeEditor.setContent( current_campaign.c_body );

			var _c_content = '';
			var _c_body = $(current_campaign.c_body);

			$.each(_c_body, function(i, elem){ 
				// console.log(elem.innerHTML);
				if(elem != "" && elem != undefined && elem.length != 0 && elem.innerText != undefined)
					_c_content += elem.innerText; 
			});
			
			// console.log(_c_content.length);
			camp_textarea_cnt.html(160 - _c_content.length);

			// hide analytics_container
			if(!analytics_container.hasClass('hide'))
				analytics_container.addClass('hide');
			// show preview_container
			if(preview_container.hasClass('hide'))
				preview_container.removeClass('hide');

			if(!cost_preview_container.hasClass('hide')) 
				cost_preview_container.addClass('hide');

			// inject selected camps id and camps recipients number to step 2
			var selected_stu_number = 0;
			campaign_list_shown.find('ul li input[id^="campaign_shown_"]:checked').each(function() {
				selected_stu_number += parseInt($(this).data('recipients'));
			});

			// console.log(selected_stu_number);
			edit_campaign_elem.find('.inject-selected-students').html(selected_stu_number);
		}
	};	

	toggle_campaign_views_step1_backward = function() {
		// back to start_campaign_elem
		if(choose_campaign_elem.is(':visible')) {
			hide_elem(choose_campaign_elem, start_campaign_elem, 'left', 20);
			// hide camp_textarea
			if(!camp_textarea.hasClass('hide-textarea'))
				camp_textarea.addClass('hide-textarea');
			// hide both analytics_container and preview_container
			if(!analytics_container.hasClass('hide'))
				analytics_container.addClass('hide');
			if(!preview_container.hasClass('hide'))
				preview_container.addClass('hide');

			if(!cost_preview_container.hasClass('hide'))
				cost_preview_container.addClass('hide');
		}
	};

	toggle_date_display = function(){
		if( current_campaign.schedule_later )
			date_time_container_elem.slideDown(250);
		else
			date_time_container_elem.slideUp(250);
	};

	toggle_exclude_stu_messaged = function(elem) {
		if(elem.is(":checked"))
			elem.siblings('.excludes-stu-messaged').removeClass('hide');
		else 
			elem.siblings('.excludes-stu-messaged').addClass('hide');
	};

	create_new_campaign = function(){
		//if current campaign is not set (undefined) or current campaign has already been modified so it's not the default one anymore, go ahead and create new campaign
		//else don't do anything because that means the current campaign is already storing a newly created/default campaign
		// if( !current_campaign || !current_campaign.is_default ){
		// 	current_campaign = new Campaign();
		// 	campaign_list.push(current_campaign);
		// }
		current_campaign = new Campaign();

		var pricing = $('.pricing');
		pricing.find('.total-stu-number').html('0');
		pricing.find('.cur-text-msg').html('0');

		$.ajax({
			url:  '/' + admin_type + '/ajax/createNewCampaign',
			type: 'POST',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		})
		.done(function() {
			current_campaign_id.val('');
		});

		// if( arguments.length === 0 ){
		// 	render_view();
		// 	render_preview();
		// 	render_recipient_display();
		// }
		
	};

	//update campaign - called whenever a campaign field is modified out
	update_campaign = function(val, meth){
		//if coming from approved page - create new campaign
		if( !current_campaign )
			create_new_campaign();

		//if it's a new campaign it still has default props, but making changes makes it no longer default campaign
		if( current_campaign.is_default )
			current_campaign.isNoLongerDefault();

		current_campaign[meth](val);
		render_preview();
	};

	set_admin_type = function(val){
		admin_type = val;
	};

	savedMsg = function(){
		temp = '';
		temp = current_campaign.sent ? 'sent' : 'saved';
		return {
			textColor: '#fff',
	        backGroundColor : 'green',
	        msg: 'Your campaign was successfully '+temp+'!',
	        type: 'soft',
	        dur : 5000
		};
	};

	failedMsg = function() {
		return {
			textColor: '#fff',
			bkg : '#FF0000',
			msg: "Your current plan do not support multimedia content, please try again!",
			type: 'soft',
			dur: 5000
		};
	}

	//send campaign only for notify modal or msg modal open 
	send_campagin = function() {
		validate_campaign();

		var form = $('.main-content-container form:visible');
		var formdata = new FormData(form[0]);
	    for(var prop in current_campaign) {
			if(current_campaign[prop] != null && typeof current_campaign[prop] != 'object' && typeof current_campaign[prop] != 'function') {
				formdata.append(prop, current_campaign[prop]);
			}
		}

		var curPage = $('.main-content-container').data('currentpage');
		formdata.append('currentPage', curPage);

		if(curPage == 'admin-textmsg') {
    		show_loader();

	    	$.ajax({
				url: '/' + admin_type + '/textmsg/getNumOfEligbleTextUsers',
				type: 'POST',
				data: formdata,
				contentType: false,
		        processData: false,
		        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			})
			.done(function(data) {
				hide_loader();
				data = JSON.parse(data);
				console.log(data);
				var save_campaign_btn = $('.save-campaign-btn');

				if(data['textmsg_tier'] == 'free') {
					save_campaign_btn.attr('data-isfreetrial', '1');
				} else {
					save_campaign_btn.attr('data-isfreetrial', '0');
				}

				// if current plan is free trial and eligble_users_cnt less than planed_cnt
				// send-campaign-msg-notify-modal will open 
				if(data['can_send'] == 0 || data['can_send'] == '0') {
					save_campaign_btn.attr('data-needchangeplan', '1');

					send_textmsg_notify.find('.total-eligble-users').html(data['total_eligble_users_cnt']);
					send_textmsg_notify.find('.total-users').html(data['total_users_cnt']);
					if(data['textmsg_tier'] == 'free') {
						send_textmsg_notify.find('.total-eligble-textmsg').html(data['num_of_free_texts']);
					} else {
						send_textmsg_notify.find('.total-eligble-textmsg').html(data['num_of_eligble_texts']);
					}
				
					// set data-needchangeplan when they open notify-modal then click 'continue' or 'change plan'
					msg_notify_open();
				} else {
					// send-campaign-msg-modal will open otherwise, no need to change plan
					save_campaign_btn.attr('data-needchangeplan', '0');

					var txt_phone_plan_desc    = $('.item-descrip.txt-phone-plan');
			        var txt_phone_plan_package = $('.item-package.txt-phone-plan');
			        var txt_msg_plan_desc      = $('.item-descrip.txt-msg-plan');
			        var txt_msg_plan_package   = $('.item-package.txt-msg-plan');
			        var summary_total = $('.summary-total');
			        var brief_summary = $('.brief-summary');

					// if they already set up their payment info
					if(data['textmsg_tier'] == 'pay_as_you_go') {

						// pending ...
						$.ajax({
							url: '/admin/textmsg/getTextmsgOrder',
							type: 'POST',
							data: {
								'textmsg_tier' : data['textmsg_tier'],
								'textmsg_plan' : data['flat_fee_sub_tier'],
								'textmsg_phone': data['textmsg_phone'],
							},
							headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
						}).done(function(resp) {
							console.log(resp);

							// check if the phone is ready to charge or not
					        if(resp['show_purchased_phone']) {
					            txt_phone_plan_package.find('.left').html(resp['textmsg_phone']);
					            txt_phone_plan_package.find('.right').html('<b>$60.00</b>');
					        } else {
					            txt_phone_plan_desc.find('.left, .right').html('');
					            txt_phone_plan_package.find('.left, .right').html('');
					        }

					        txt_msg_plan_desc.find('.left').html('<b>'+resp['textmsg_tier']+'</b>');        
					        txt_msg_plan_desc.find('.right').html('Monthly fee');

					        txt_msg_plan_package.find('.left').html(resp['plan']['num_of_eligble_text']+ ' texts');
					        txt_msg_plan_package.find('.right').html('<b>$'+resp['plan']['price']+'.00</b>');

					        summary_total.find('.right').html('<b>$'+ resp['total_cost'] + '.00</b>');

					        brief_summary.find('.brief-review.text-left').html(data['textmsg_tier']);
			   				brief_summary.find('.cur-text-msg').html(data['total_users_cnt']);

			   				if(data['textmsg_plan'] != 'plan-4') {
				   				brief_summary.find('.cur-text-msg-left').html(data['num_of_eligble_texts'] - data['total_users_cnt']);
				   				brief_summary.find('.meter').css('width', data['total_users_cnt']*100 / data['num_of_eligble_texts'] + '%');
				        	} else {
				        		brief_summary.find('.cur-text-msg-left').html('Unlimited');
				        		brief_summary.find('.meter').css('width',  '0%');
				        	}

							order_summary_modal_open();
						});
						
					} else if (data['textmsg_tier'] == 'flat_fee') {
			        	msg_modal_open(data['total_eligble_users_cnt'], data['num_of_eligble_texts']);
					} else {
						// data['textmsg_tier'] == 'free'
						msg_modal_open(data['total_eligble_users_cnt'], data['num_of_free_texts']);
					}
				}
			});

		} else {
			//curPage == 'admin-groupmsg'
			activate_send_away();
			msg_modal_open(current_campaign['recipients']);
		}

	}
	//save_campaign 
	save_campaign = function(elem, isSend){
		isSend = (typeof isSend != 'undefined')? isSend : false;
		//validate current campaign before sending/saving
		validate_campaign();
		// console.log(current_campaign);
		// console.log(JSON.stringify(current_campaign)); return a string
		var form = $('.main-content-container').find('form');
		var formdata = new FormData(form[0]);
		for(var prop in current_campaign) {
			if(current_campaign[prop] != null && typeof current_campaign[prop] != 'object' && typeof current_campaign[prop] != 'function') {
				formdata.append(prop, current_campaign[prop]);
			}
		}
		var curPage = $('.main-content-container').data('currentpage');
		formdata.append('currentPage', curPage);

		if(elem.data('isfreetrial') != null && elem.data('needchangeplan') != null) {
			formdata.append('isfreetrial', elem.data('isfreetrial'));
			formdata.append('needchangeplan', elem.data('needchangeplan'));
		}

		if( !current_campaign.is_default && current_campaign.isValid) {
			show_loader();
			
			$.ajax({
				url: '/' + admin_type + save_route,
				type: 'POST',
				data: formdata,
				contentType: false,
	    		processData: false,
	    		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	    		success: function(data) {
					if(data == 'success') {
						if(curPage == 'admin-textmsg' && isSend) {
							send_success_modal.foundation('reveal', 'open');
						} else {
							topAlert( savedMsg() );
							window.location.reload();
						}
					} else if(data == 'failed'){
						topAlert( failedMsg() );
					} else {
						alert('Oops! Something went wrong. Refresh the page and try again.');
					}
	    		},
	    		error: function(data) {
					alert('Oops! Something went wrong. Refresh the page and try again.');
	    		}
			}).done(function(data) {
				hide_loader();
				if(elem.data('needchangeplan') == '1') {
					window.location.href = '/settings/billing?plans=1';
				} else {
					// window.location.reload();
					console.log(data);
				}
			});
		} 
		else {
			alert('Cannot save or send an empty campaign.');
		}
	};

	// toggle preview container
	toggle_preview_container = function() {
		if(preview_container.hasClass('hide')) {
			preview_container.removeClass('hide');
		} else {
			preview_container.addClass('hide');
		}
	};

	// toggle previews - iphone and macbook
	toggle_preview_view = function(elem){
		if($('body').attr('id') == 'admin-textmsg') {
			return ;
		}
		preview_btn.removeClass('active');
		elem.addClass('active');

		if( preview_iphone.is(':visible') ){
			preview_iphone.hide(10);
			preview_macbook.delay(10).fadeIn(750);
		}else{
			preview_macbook.hide(10);
			preview_iphone.delay(10).fadeIn(750);
		}
	};

	// rendering campaign preview functions
	get_preview_template = function(){
		html = '';
		img_template = '';
		temp = '';

		html += '<div class="prev-subj"><b>Subject: </b>' + current_campaign.c_subject + '</div><br />';
		html += '<div>' + current_campaign.c_body + '</div>';
		
		return html;
	};

	render_preview = function(){
		template = get_preview_template();
		iphone.html(template);
		macbook.html(template);
	};

	get_campaign_info = function(cid){
		show_loader();

		var current_c_list = $('.c-list:visible');
		var currentpage = $('.main-content-container').data('currentpage');

		// var excludes_students = $('.excludes-stu-messaged').find('input:checked').length == 0 ? 0 : 1;

		$.ajax({
            url: '/' + admin_type + '/viewCampaign',
            data: {id: cid, currentPage: currentpage},
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'POST'
        }).done(function(data){
			lg_preview_btn.removeClass('hide');

			current_campaign = new Campaign();
			set_campaign($.parseJSON(data), cid);
			// console.log(current_campaign.getAnalytics());
			current_campaign_id.val(cid);

			render_preview();
			render_campaign_choose(data);
			// render_recipient_display();
			render_view();
			if(current_c_list.hasClass('previous-campaign-container')) {
				render_analytics();
			} else {
				if(!analytics_container.hasClass('hide'))
					analytics_container.addClass('hide');
			}
			hide_loader();
		});
	};

	render_campaign_choose = function(data) {

		var data_info = $.parseJSON(data); 
		campaign_list_shown.find('ul li input[name^="campaign_shown_"]').each(function() { 
			var _this = $(this); 
			if(_this.prop('name').substring(15) == data_info['c_name']) {
				_this.trigger('click');
				// _this.prop('checked', 'true');
			} else {
				_this.removeAttr('checked');
			}
		});

	};

	set_campaign = function(data, cid){
		// console.log(data);
		current_campaign.id = cid;

		for( var prop in data ){
			if( data.hasOwnProperty(prop) ){
				current_campaign[prop] = data[prop];
			}
			// if( data.hasOwnProperty(prop) && prop != 'recipients' ){
			// 	current_campaign[prop] = data[prop];
			// }else if( data.hasOwnProperty(prop) && prop === 'recipients' ){
			// 	for( var a = 0; a < data[prop].length; a++ ){
			// 		current_campaign.addStudents( new Student(data[prop][a]) );
			// 	}
			// }
		}	

		current_campaign.is_default = false;
	};

	render_view = function(data){
		name_elem.val( current_campaign.c_name );
		subj_elem.val( current_campaign.c_subject );

		tinymce.activeEditor.setContent( current_campaign.c_body );
		// check if there is textCnt class
		if (camp_textarea_cnt.length > 0 ) {
			var _c_content = '';
			var _c_body = $(current_campaign.c_body);

			$.each(_c_body, function(i, elem){ 
				// console.log(elem.innerText);
				if(elem != "" && elem != undefined && elem.length != 0 && elem.innerText != undefined)
					_c_content += elem.innerText; 
			});
			var curContentLeft = 160 - _c_content.length;

			camp_textarea_cnt.html(curContentLeft);

			var send_camp_btn = $('.send-campaign-btn');

			var curPage = $('.main-content-container').data('currentpage');

			if(curContentLeft < 0 && curPage == 'admin-textmsg') {
				send_camp_btn.css({
					'pointer-events': 'none',
                    'background-color': '#FF8D67'
				});
			} else {
				send_camp_btn.css({
					'pointer-events': 'all',
                    'background-color': '#FF5C26'
				});
			}
		}
		if( current_campaign.date ){
			schedule_chkbx_elem.prop('checked', true).trigger('change');
		}else{
			schedule_chkbx_elem.prop('checked', false).trigger('change');
		}

		date_elem.val( current_campaign.date );
		hr_elem.val( current_campaign.hr );
		min_elem.val( current_campaign.min );
		period_elem.val( current_campaign.period );
	};

	render_analytics = function() { 

		analytics_container.find('.selected-camp-name').html(current_campaign.c_name);
		analytics_container.find('.selected-camp-recipients').html('(' + current_campaign.recipients + ' recipients)');
		// check date is null or not, maybe it is a last sent date 
		var inject_schedule_date = analytics_container.find('.selected-camp-scheduled-date');
		if(current_campaign.last_sent_on == null){
			if(current_campaign.date != null && current_campaign.date != "")
				inject_schedule_date.html('Last sent on ' + current_campaign.date);
		} else {
			inject_schedule_date.html('Last sent on ' + current_campaign.last_sent_on);
		}
			

		analytics_container.find('.selected-camp table td[class^="inject-"]').each(function() {
			var _this = $(this);
			var slug = _this.attr('class').split('-')[1];
			
			if(slug != "" || slug != null || typeof slug != 'undefined') {
				if(slug == 'read_response_rate' || slug == 'replied_response_rate') {
					_this.html(current_campaign[slug] + '%');
				} else {
					_this.html(current_campaign[slug]);
				}
			}

		});
		analytics_container.removeClass('hide');
	};

	render_text_message_summary = function() {
		show_loader();

		var campaign_chosen = [];
		var campaign_excludes_stu = [];
		var selected_students_ids = null;
		var selected_students_ids_arr = [];

		var temp_campaign = campaign_list_shown.find('ul li input[id^="campaign_shown_"]:checked');
		var temp_campaign_excludes_stu = campaign_list_shown.find('ul li input[id^="excludes_check_"]:checked');

		var pricing = $('.pricing');
		var table_content = $('div#cost-details .cost-details-summary');
		var total_students = $('.total-stu-number');
		var current_text_msg = $('.cur-text-msg');
		var current_text_msg_left = $('.cur-text-msg-left');

		for(var i = 0; i < temp_campaign.length; i++) {
			campaign_chosen.push(temp_campaign[i].value);
		}

		for(var j = 0; j < temp_campaign_excludes_stu.length; j++) {
			campaign_excludes_stu.push(temp_campaign_excludes_stu[j].value);
		}

		// 'selected_from_manage_students' in campaign_chosen
		if(campaign_chosen.indexOf('selected_from_manage_students') != -1) {
			selected_students_ids = $('input[name="selected_students_id"]').val();	
			// selected_students_ids_arr = selected_students_id.split(',');
		}

		if(campaign_chosen.length == 0 && campaign_excludes_stu.length == 0 && !selected_students_ids) {
			table_content.html('');
			total_students.html('0');
			current_text_msg.html('0');

			pricing.find('.progress span').css('width', '0%');
			pricing.find('.progress').removeClass('alert').addClass('success');

			create_campaign_btn.css({
				'pointer-events': 'all', 'background-color': '#FF5C26', 'cursor': 'pointer',
			});

			change_plan.removeClass('text-center').addClass('text-right');
			change_plan.html('<a href="#" target="_blank">Change Plan</a>');

			hide_loader();
			return ;
		}

		$.ajax({
			url: '/' + admin_type + '/textmsg/textmsgSummary',
			type: 'POST',
			dataType: 'JSON',
			data: {campaign_chosen: campaign_chosen, campaign_excludes_stu: campaign_excludes_stu, selected_students_ids : selected_students_ids},
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		})
		.done(function(data) {
			console.log(data);
			//reset before append
			table_content.html('');
			var total_stu_number = 0;
			var num_of_eligble_texts = parseInt(current_text_msg_left.html());
			var perc = 0;

			for (var i = 0; i < data.length; i++) {
				if(data[i]['country_id']) {
					var template = '<li><div><div class="flag flag-'+ data[i]['country_code'].toLowerCase();
					template += '"></div><span>'+ data[i]['country_name'] +'</span></div><div>' + data[i]['user_count'] + ' students';
					template += '</div></li>';
					table_content.append(template);
					total_stu_number += parseInt(data[i]['user_count']);
				}
			};

			total_students.html(total_stu_number);
			current_text_msg.html(total_stu_number);

			if(num_of_eligble_texts != 0) {
				perc = total_stu_number * 100 / num_of_eligble_texts;				
			}

			// exceed limitation
			if(perc > 100 || num_of_eligble_texts == 0) {
				//disable next button
				perc = 100;

    			create_campaign_btn.css({
    				'pointer-events': 'none', 'background-color': '#F9B6A0', 'cursor': 'default',
    			});

				pricing.find('.progress').removeClass('success').addClass('alert');
				change_plan.removeClass('text-right').addClass('text-center');

				var content = "<span>You don't have sufficient credit. In order to continue you need to upgrade to either a flat fee plan or a pay as you go plan.</span>";
				content += '<a href="/settings/billing" class="button upgrade-btn">upgrade</a>';

				change_plan.html(content);
			} else {
				create_campaign_btn.css({
    				'pointer-events': 'all', 'background-color': '#FF5C26', 'cursor': 'pointer',
    			});

				pricing.find('.progress').removeClass('alert').addClass('success');
				change_plan.removeClass('text-center').addClass('text-right');
				change_plan.html('<a href="#" target="_blank">Change Plan</a>');
			}

			pricing.find('.progress span.meter').css('width', '' + perc + '%');

			hide_loader();

		});

	}

	// create a set stores all existed campaign ids
	campaigns_existed_collection = function() {
		c_list_elem.find('ul li input').each(function(){ 
			camps_set.add($(this).val()); 
		});
		return camps_set;
	};

	// message modal
	sending_msg = function(curNum, leftNum){
		var curPage = $('.main-content-container').data('currentpage');

		$('.curTextMsgCount').html('Total: <span>' + curNum + '</span> texts');
		
		if(curPage == 'admin-groupmsg') {
			return 'You are about to message ' + current_campaign.recipients + ' students. <br /> Are you sure you want to do this?';
		} else {
			return 'After sending this message you will have ' + (leftNum - curNum) + ' free texts left.';
		}
	};

	scheduled_msg = function(val){
		return 'You have scheduled your message to be sent to ' + val + ' students on ' + current_campaign.date + ' at ' + current_campaign.hr + ':' + current_campaign.min + current_campaign.period + '. <br /> Are you sure you want to do this?';
	};

	build_msg_modal = function(curNum, leftNum){
		if(typeof curNum !== 'number') {
			curNum = parseInt(curNum);
		}
		if(typeof leftNum !== 'number') {
			leftNum = parseInt(leftNum);
		}
		if(leftNum != 0) {
			var validcnt = curNum*100/leftNum;
			validcnt = validcnt > 100? 100 : validcnt;
			send_campagin_msg_modal.find('.meter').css('width', '' + validcnt + '%');
		} else {
			send_campagin_msg_modal.find('.meter').css('width', '' + 100 + '%');
		}
		send_campagin_msg_modal.find('.cur-text-msg').html(curNum);
		send_campagin_msg_modal.find('.cur-text-msg-left').html(leftNum);

		if( current_campaign.schedule_later ){
			sending_msg_content.html( scheduled_msg(curNum) );
			send_away.html('Yes, Schedule!');
		}else{
			sending_msg_content.html( sending_msg(curNum, leftNum) );
			send_away.html('Yes, send away!');
		}
	};

	activate_send_away = function() {
		if(send_away.hasClass('disabled'))
			send_away.removeClass('disabled');
	};

	deactivate_send_away = function() {
		if(!send_away.hasClass('disabled'))
			send_away.addClass('disabled');
	};

	msg_modal_open = function(curNum, leftNum){
		build_msg_modal(curNum, leftNum);
		$('#send-campaign-msg-modal').foundation('reveal', 'open');
	};

	msg_modal_close = function(){
		$('#send-campaign-msg-modal').foundation('reveal', 'close');
	};

	msg_notify_open = function() {
		$('#send-campaign-msg-notify-modal').foundation('reveal', 'open');
	};

	msg_notify_close = function() {
		$('#send-campaign-msg-notify-modal').foundation('reveal', 'close');
	};

	order_summary_modal_open = function() {
		$('#order-summary-modal').foundation('reveal', 'open');
	}

	order_summary_modal_close = function() {
		$('#order-summary-modal').foundation('reveal', 'close');
	}

	validate_campaign = function(){
		if( current_campaign.c_name != '' && current_campaign.c_subject != '' && current_campaign.c_body != '' ){
			update_campaign(true, 'setValidity');
		}
	};

	set_student_list = function(recipients){
		if( recipients && recipients.length > 0 ){
			if( recipients instanceof jQuery ){
				$.each(recipients, function(){
					data = $(this).data('info');
					current_campaign.addStudents( new Student(data) );
				});
			}else{
				for( var i = 0; i < recipients.length; i++ ){
					data = JSON.parse(recipients[i].json);
					current_campaign.addStudents( new Student(data) );
				}
			}
			render_recipient_display();
		}
	};

	// get_recipient_list_template = function(){
	// 	html = '';

	// 	$.each(current_campaign.students.list, function(){
	// 		html += '<li class="student clearfix" data-info="'+this.props.user_id+'">';
	// 		html += 	'<div class="left r-name">' + this.props.fname + ' ' + this.props.lname + '</div>';
	// 		html += 	'<div class="left r-profile"> i </div>';
	// 		html += 	'<div class="left remove-std-btn"> x </div>';
	// 		html += 	'<div class="r-profile-view">';
	// 		html += 		'<div class="clearfix">';			
	// 		html += 			'<div class="left image"><img src="'+this.props.profile_img_loc+'" alt="" /></div>';			
	// 		html += 			'<div class="left info">';			
	// 		html += 				'<div class="">'+this.props.fname+' '+this.props.lname+'</div>';			
	// 		html += 				'<div class="">'+this.props.current_school+'</div>';			
	// 		html += 				'<div class="flag '+this.props.country_code+'">'+this.props.country_name+'</div>';			
	// 		html += 			'</div>';			
	// 		html += 		'</div>';			
	// 		html += 		'<div class="">';			
	// 		html += 			'<div class="objec">'+this.props.objective+'</div>';	
	// 		html += 			'<table>';	
	// 		html += 				'<tbody>';	
	// 		html += 					'<tr>';	
	// 		html += 						'<td>GPA</td>';	
	// 		html += 						'<td>'+this.props.gpa+'</td>';	
	// 		html += 					'</tr>';	
	// 		html += 					'<tr>';	
	// 		html += 						'<td>SAT</td>';	
	// 		html += 						'<td>'+this.props.sat_score+'</td>';	
	// 		html += 					'</tr>';	
	// 		html += 					'<tr>';	
	// 		html += 						'<td>ACT</td>';	
	// 		html += 						'<td>'+this.props.act_composite+'</td>';	
	// 		html += 					'</tr>';	
	// 		html += 					'<tr>';	
	// 		html += 						'<td>TOEFL</td>';	
	// 		html += 						'<td>'+this.props.toefl_total+'</td>';	
	// 		html += 					'</tr>';	
	// 		html += 					'<tr>';	
	// 		html += 						'<td>IELTS</td>';	
	// 		html += 						'<td>'+this.props.ielts_total+'</td>';	
	// 		html += 					'</tr>';	
	// 		html += 				'<tbody>';	
	// 		html += 			'</table>';	
	// 		html += 			'<div><b>Financials for first year:</b> '+this.props.financial_firstyr_affordibility+'</div>';	
	// 		html += 			'<div><b>UPLOADS:</b>';	
	// 		if( !!parseInt(this.props.resume) ){
	// 			html += 				' resume';	
	// 		}
	// 		if( !!parseInt(this.props.transcript) ){
	// 			html += 				' transcript';	
	// 		}
	// 		if( !!parseInt(this.props.financial) ){
	// 			html += 				' financial';	
	// 		}
	// 		if( !!parseInt(this.props.toefl) ){
	// 			html += 				' toefl';	
	// 		}
	// 		if( !!parseInt(this.props.ielts) ){
	// 			html += 				' ielts';	
	// 		}
	// 		html += 			'</div>';	
	// 		html += 		'</div>';
	// 		html += 	'</div>';
	// 		html += '</li>';
	// 	});	

	// 	return html;
	// };

	// render_recipient_display = function(){
	// 	template = get_recipient_list_template();
	// 	rec_ul_elem.html(template);
	// 	rec_cnt_elem.html(current_campaign.studentCount());

	// 	if( current_campaign.studentCount() > 0 ){
	// 		if( edit_list_btn_elem.hasClass('add') )
	// 			edit_list_btn_elem.removeClass('add').addClass('edit').html('Edit list');
	// 	}else{
	// 		if( edit_list_btn_elem.hasClass('edit') )
	// 			edit_list_btn_elem.removeClass('edit').addClass('add').html('Choose Audience');
	// 	}
	// };

	// remove_student_from_list = function(uid){
	// 	$.post('/' + admin_type + '/removeStudentFromList', {id: uid}, function(){
	// 		current_campaign.removeStudentAt(uid);
	// 		render_recipient_display();
	// 	});
	// };

	//search students
	get_search_data = function(val){
		temp = {};
		temp_arr = [];

		for( var k = 0; k < current_campaign.studentCount(); k++ ){
			temp_arr.push(current_campaign.students.list[k].props.user_id);
		}

		temp.term = val;
		temp.user_ids = temp_arr;

		return temp;
	};

	search_students = function(val){
		searchData = get_search_data(val);
		$.ajax({
            url: '/' + admin_type + '/approvedStudentSearch',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: searchData,
            type: 'POST'
        }).done(function(data){
			update_search_results(data);
		});
	};

	results_template = function(recipients){
		html = '';

		for( var i = 0; i < recipients.length; i++ ){
			temp = JSON.parse(recipients[i].json);
			html += "<li class='result-item' data-info='"+recipients[i].json+"'>"+temp.fname +" "+temp.lname+" <span class='s-info'>?</span> <span class='s-applied'> @ </span></li>";
		}

		return html;
	};

	no_results_template = function(){
		html = '<li>Results not found or results are already in your list.</li>';
		return html;
	};

	toggle_results_view = function(manual){
		if( manual ){
			if( manual === 'open' )
				results_elem.slideDown(250);
			else
				results_elem.hide();
		}else{
			if( results_elem.is(':visible') ){
				results_elem.hide();
			}else{
				results_elem.slideDown(250);
			}
		}
		
	};

	update_search_results = function(data){
		if( data && typeof data === 'object' ){
			template = results_template(data);
		}else{
			template = no_results_template();
		}

		results_elem.find('ul').html(template);
		toggle_results_view('open');
	};

	search_camp_template = function(elem){
		html = '<li>';

		html += 	'<input type="radio" value="'+elem.find('input[type="radio"]').val()+'" id="'+elem.find('input[type="radio"]').val()+'" />';

		if( elem.find('label').hasClass('prev') ){
			html += 	'<label for="'+elem.find('input[type="radio"]').val()+'" class="camp-label prev">'+elem.find('label').text()+'</label>';
			html += 	'<div class="time-display">'+elem.find('.time-display').text()+'</div>';
		}else{
			html += 	'<label for="'+elem.find('input[type="radio"]').val()+'" class="camp-label sched">'+elem.find('label').text()+'</label>';
			html += 	'<div class="time-display clearfix">'
			html += 		'<div class="css-clock left">';
			html += 			'<div></div>';
			html += 			'<div></div>';
			html += 		'</div>';
			html += 		'<div class="left sch-time">'+elem.find('.sch-time').text()+'</div>';
			html +=		'</div>';
		}

		html += '</li>';

		return html;
	};

	search_campaigns = function(results){
		template = '';

		$.each(results, function(){
			template += search_camp_template($(this).parent());
		});		

		toggle_searched_camp_view(template, 'open');
	};

	toggle_searched_camp_view = function(template, toggle){
		temp = $('.t-tabs > div.active').data('tab');

		if( toggle === 'open' ){
			c_list_elem.hide();
			searched_camp_elem.find('ul').html(template);
			searched_camp_elem.show();
		}else{
			searched_camp_elem.hide();
			$( '.'+temp ).show();
		}
	};

	hidden_campaigns = function(elem) {

		var current_ul = elem.find('ul');
		var current_ul_height = current_ul.height();
		var hidden_elem = 0;
		current_ul.find('li').each(function() {
			if($(this).position().top - 150 > current_ul_height)
            	hidden_elem++;
		});
		// console.log(hidden_elem);

		if(hidden_elem > 0) {
			$('.campaign-start .show-more').removeClass('hide');
		} else {
			$('.campaign-start .show-more').addClass('hide');
		}

	};

	toggle_lists = function(elem){
		tab = elem.data('tab');

		if( tab ){
			$('.t-tabs > div.left.active').removeClass('active');
			$('.c-list').hide();
			$(elem).addClass('active');
			$('.'+tab).show();
			hidden_campaigns($('.'+tab));
		}
	};

	save_message_templates = function(elem){

		var template_name = $('#template_name').val();
		var c_body = current_campaign.c_body;

		$.ajax({
            url: '/ajax/saveMessageTemplates',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {name: template_name, content: c_body},
            type: 'POST'
        }).done(function(data, textStatus, xhr) {
			var txt = '<option value="'+data.id+'">'+data.name+'</option>';
			var check = false;
			var _data = data;
			$("#message_template_dropdown > option").each(function() {
			    if (this.text == _data.name) {
			    	check = true;
			    }
			});

			if (check === false) {
				message_template_dropdown.append(txt);
			}

			$('#insert_message_template').prop('checked', false);
			$('#save-template-modal').foundation('reveal', 'close');
			
		});

	};

	open_save_message_templates = function(elem){

		$('#save-template-modal').foundation('reveal', 'open');

	};

	load_message_templates = function(elem){

		if (message_template_dropdown.val() != '') {
			$.ajax({
	            url: '/ajax/loadMessageTemplates',
	            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	            data: {id: message_template_dropdown.val()},
	            type: 'POST'
	        }).done(function(data, textStatus, xhr) {
				update_campaign(data.content, 'setBody');
				tinymce.activeEditor.setContent( current_campaign.c_body );
				tinymce.get('textarea-editor').focus();
				// tinymce.focusedEditor.setContent( current_campaign.c_body );
				render_view();
			});
		}
	};

	get_camp_recipients = function(cid){
		show_loader();
		
		var excludes_students = $('.excludes-stu-messaged').find('input:checked').length == 0 ? 0 : 1;
		// console.log(excludes_students);
		$.ajax({
            url: '/' + admin_type + '/viewCampaign',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {id: cid, checked: excludes_students},
            type: 'POST'
        }).done(function(data){
			//update list views
			lg_preview_btn.removeClass('hide');
			add_from_prev_camps( $.parseJSON(data) );
			hide_loader();
		});
	};

	add_from_prev_camps = function(camps){

		var userNameArr = [];
		for( var k = 0; k < current_campaign.studentCount(); k++ ){
			userNameArr.push(current_campaign.students.list[k].props.fname+ ' '+current_campaign.students.list[k].props.lname);
		}

		for( var x = 0; x < camps.recipients.length; x++ ){
			if (userNameArr.indexOf(camps.recipients[x].fname+ ' '+camps.recipients[x].lname) == -1) {
				current_campaign.addStudents( new Student( camps.recipients[x] ) );
			}
			
		}
		render_recipient_display();
	};

	remove_camp_recipients = function(cid){
		show_loader();
		
		var excludes_students = $('.excludes-stu-messaged').find('input:checked').length == 0 ? 0 : 1;
		
		$.ajax({
            url: '/' + admin_type + '/viewCampaign',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {id: cid, checked: excludes_students},
            type: 'POST'
        }).done(function(data) {
			// console.log($.parseJSON(data));
			// update list views
			lg_preview_btn.removeClass('hide');
			remove_from_prev_camps( $.parseJSON(data) );
			$('.add-from-prev-camps select').val('');
			hide_loader();
		});
	}

	remove_from_prev_camps = function(camps) {

		for(var idx = 0; idx < camps.recipients.length; idx++) {
			// console.log(camps.recipients[idx]['fname'] +" " + camps.recipients[idx]['lname']);

			for(var i = 0; i < current_campaign.students.list.length; i++) {
				if(current_campaign.students.list[i].props.fname == camps.recipients[idx]['fname'] && 
				   current_campaign.students.list[i].props.lname == camps.recipients[idx]['lname'])
					current_campaign.students.list.splice(i, 1);
			}
		}
		// console.log(current_campaign);
		render_recipient_display();
	}

	setAutomaticCampaign = function(type, elem){
		$.ajax({
            url: '/'+ admin_type + '/setAutomaticCampaign/' + type,
            data: null,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'POST'
        }).done(function(data, textStatus, xhr) {
			/*optional stuff to do after success */
		});
	}

	add_file = function(elem, type){
		form = $('.campaign-form')[0];
		form_data = new FormData(form);

		$.ajax({
			url: '/'+admin_type+'/uploadAttachment/'+type,
			type: 'POST',
			data: form_data,
			contentType: false,
            processData: false,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		}).done(function(data){
			var data_return = JSON.parse(data);

			if (elem[0].files && elem[0].files[0]) {
	            var reader = new FileReader(), 
	            	img = $('img'), iterator = 0, file_name;

	            file_name = elem[0].files[0].name;
	            iterator = current_campaign.files.length;

	            reader.onload = function (e) {
	                temp = '';
	                // temp = typeof current_campaign.c_body == 'string' ? current_campaign.c_body + '\nImage-'+iterator : '\nImage-'+iterator;
	                if( data_return.is_image ){
		                current_campaign.c_body += '<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/campaign/uploads/'+data_return.name+'" alt="Campaign image" style="width: 100px;" />';
	                }else{
		                current_campaign.c_body += '<a href="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/campaign/uploads/'+data_return.name+'" target="_blank" style="width: 100px;">'+data_return.name+'</a>';
	                }
					// update_campaign( temp, 'setBody' );                
	                update_campaign( {img: e.target.result, name: file_name, aws: data}, 'addFile');
	                render_view();
	            };

	            reader.readAsDataURL(elem[0].files[0]);
	        }
		});
	};

	delete_campaign = function(cid){
		if( cid ){
			$.ajax({
	            url: '/'+admin_type+'/removeCampaign',
	            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	            data: {id: cid},
	            type: 'POST'
	        }).done(function(data){
				if( data === 'success' ){
					window.location.reload();
				}
			});
		}
		
	};

	toggleAutomaticCamp = function(){
		automatic_camp_elem.slideToggle(400);
	}

	hide_toll_free_number_search = function() {
		if(!phone_list_available.hasClass('hide')) {
			phone_list_available.addClass('hide');
		}
		if(search_number.hasClass('hide')) {
			search_number.removeClass('hide');
		}
		input_area_code.hide();
		que_ans.hide();
	}

	show_toll_free_number_search = function() {
		if(!phone_list_available.hasClass('hide')) {
			phone_list_available.addClass('hide');
		}
		if(search_number.hasClass('hide')) {
			search_number.removeClass('hide');
		}
		input_area_code.show();
		que_ans.show();
	}

	show_more_phone_number = function() {
		var preMaxHeight = phone_list_view.css('max-height');
		var currentHeight = phone_list_view.outerHeight();
		currentHeight += 130;
		phone_list_view.css('max-height', currentHeight + 'px');

		// console.log(preMaxHeight);
		// console.log(currentHeight);
		
		if(parseInt(currentHeight) < parseInt(preMaxHeight) + 130) {
			show_more_phonelist.hide();
		}
	};

	back_to_search_number = function() {
		free_trial.removeClass('hide');
		phone_list_available.removeClass('hide');
		setup_phone_number.addClass('hide');
	}

	search_number_reset = function() {
		input_area_code.find('input').prop('value', '');
		input_area_code.find('select').prop('value', '');
		show_more_phonelist.html('');
		show_more_phonelist.show();
		phone_list_view.html('');
		phone_list_view.css('max-height', '130px');
		$('.buy-option').remove();
		phone_list_available.addClass('hide');
		search_number.removeClass('hide');
	}

	forward_to_textmsg = function() {
		// call back function perchase 
		// and update database set 'txt_first_time' = false
		// reload the page
		var phone_selected = number_details.html();

		show_loader();

		$.ajax({
			url: '/' + admin_type + '/textmsg/purchasePhone',
			type: 'POST',
			data: {'phone': phone_selected},
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success: function() {
				window.location.reload();
			},
			error: function(resp) {
				console.log(resp);
			}
		}).done(function() {
			hide_loader();
		});
		 
	}

	upload_csv = function(chosen_file) {

		var form = new FormData();

    	form.append('file', chosen_file);

    	var set_text_msg_route = '/admin/textmsg';

		$.ajax({
			url: '/' + admin_type + '/textmsg/uploadCsv', 
			type: 'POST',
			enctype: 'multipart/form-data',
			contentType: false,
	        processData: false,
			data: form,
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		})
		.done(function() {
			window.location.href = set_text_msg_route;
			console.log("success");
		});
	}

	active_continue_btn = function() {
		var continue_btn = $('.buy-option a.button.success');
		continue_btn.removeClass('disabled');
		continue_btn.css('pointer-events','all');
	}

	return {
		showLoader : show_loader,
		hideLoader : hide_loader,
		hideShowMore : hidden_campaigns,
		toggleView: toggle_campaign_views,
		toggleViewStep1Forward : toggle_campaign_views_step1_forward,
		toggleViewStep1Backward : toggle_campaign_views_step1_backward,
		createCampaign: create_new_campaign,
		update: update_campaign,
		toggleDisplay: toggle_date_display,
		togglePreview: toggle_preview_view,
		togglePreviewContainer: toggle_preview_container,
		getExistedCampaignIds: campaigns_existed_collection, 
		toggleViewExcludeStuMessaged: toggle_exclude_stu_messaged,
		setAdminType: set_admin_type,
		save: save_campaign,
		sendCampagin: send_campagin,
		deleteCamp: delete_campaign,
		modify: get_campaign_info,
		openMsgModal: msg_modal_open,
		closeMsgModal: msg_modal_close,
		openMsgNotifyModal: msg_notify_open,
		closeMsgNotifyModal: msg_notify_close,
		setRecipients: set_student_list,
		removeStudent: remove_student_from_list,
		searchFor: search_students,
		resultsView: toggle_results_view,
		searchCampaigns: search_campaigns,
		toggleCampSearchView: toggle_searched_camp_view,
		toggleLists: toggle_lists,
		saveMessageTemplates: save_message_templates,
		loadMessageTemplates: load_message_templates, 
		openSaveMessageTemplates: open_save_message_templates,
		addRecipientsFromPrevCamps: get_camp_recipients,
		removeRecipientsFromPrevCamps: remove_camp_recipients,
		addFile: add_file,
		textMSGSummary: render_text_message_summary,
		setAutomaticCampaign: setAutomaticCampaign,
		toggleAutomaticCamp: toggleAutomaticCamp,
		searchPhoneNumbers: search_number_views,
		confirmPhoneNumber: confirm_number_chosen,
		showMorePhoneList : show_more_phone_number, 
		backToFreeTrialSearch: back_to_search_number,
		searchAgain: search_number_reset,
		forwardTextMsg: forward_to_textmsg,
		hideTollFreeNumSearch: hide_toll_free_number_search,
		showTollFreeNumSearch: show_toll_free_number_search,
		uploadCsv: upload_csv,
		activeContinue: active_continue_btn,
		activateSendAway: activate_send_away,
		deactivateSendAway: deactivate_send_away,
		openOrderSumModal: order_summary_modal_open,
		closeOrderSumModal: order_summary_modal_close,
	};

})($);

$(document).ready(function(){
	var adminType = $('body').data('admintype'),
		stds = $('.student'), all_camps, camp_textarea = $('.camp-textarea'),
		fromSelectedCnt = $('.main-content-container').data('selectedcnt'), 
		currentpage = $('.main-content-container').data('currentpage');

	camp_textarea.addClass('hide-textarea');
	Plex.gM.setAdminType(adminType);

	if(parseInt(fromSelectedCnt) > 0 ) {
		$('.campaign-start .create-campaign-btn').trigger('click');
		$('#campaign_shown_manage_stu').prop('checked', 'true');
		if(currentpage != 'admin-groupmsg') {
			Plex.gM.textMSGSummary();
		}
	} else {
		$('#campaign_shown_manage_stu').parent('li').hide();
	}
	
	Plex.gM.hideShowMore($('.c-list:visible'));
	// Plex.gM.setRecipients(stds);
});

// from campaign create view go forward to step1
$(document).on('click', '.campaign-start .create-campaign-btn', function(){
	Plex.gM.createCampaign();
	Plex.gM.toggleView();
});

// from campaign-edit go back to step1
$(document).on('click', '.campaign-edit .back-btn', function(){
	Plex.gM.toggleView();
});

// from campaign-choose to campaign-edit
$(document).on('click', '.step1-forward .button', function() {
	Plex.gM.toggleViewStep1Forward();
	Plex.gM.update(parseInt($('.inject-selected-students').html()), 'setSelectedCnt');
});

// from campaign choose to start campaign
$(document).on('click', '.campaign-choose .back-btn', function() {
	Plex.gM.toggleViewStep1Backward();
});

// toggle excludes students messaged
$(document).on('change', '.campaign-list ul li input' , function() {
	// before show .excludes-stu-messaged check if current campaign id is in camps_set
	var ExistedCampIds = Plex.gM.getExistedCampaignIds();
	if(ExistedCampIds.has($('#current-campaign-id').val())) {
		Plex.gM.toggleViewExcludeStuMessaged( $(this) );
	}
	var currentpage = $('.main-content-container').data('currentpage');
	if(currentpage != 'admin-groupmsg') {
		Plex.gM.textMSGSummary();
	}
});

// set campaign name
$(document).on('change', '.c-name', function(){
	Plex.gM.update($(this).val(), 'setName');
});

// set subject of campaign
$(document).on('change', '.c-subj', function(){
	Plex.gM.update($(this).val(), 'setSubject');
});

// set body of campaign
$(document).on('change', '.c-body', function(){
	Plex.gM.update($(this).val(), 'setBody');
});

// set SMS/MMS
$(document).on('change', 'input[name="text_option"]', function(){
	if($('input#textMMS').is(':checked')){
		Plex.gM.update(1 , 'setIsMMS');		
	} else {
		Plex.gM.update(0 , 'setIsMMS');
	}
});

// toggle schedule later form
$(document).on('change', '#schedule_later', function(){
	Plex.gM.update($(this).is(':checked'), 'scheduleForLater');
	Plex.gM.toggleDisplay();
});

// set calendar date
$(document).on('change', '.date-cal', function(){
	Plex.gM.update($(this).val(), 'setDate');
	$('.select-time').trigger('change');	
});

// set hour
$(document).on('change', 'select[name="hours"]', function(){
	Plex.gM.update($(this).val(), 'setHr');
});

// set minutes
$(document).on('change', 'select[name="minutes"]', function(){
	Plex.gM.update($(this).val(), 'setMin');
});

// set time period (am/pm)
$(document).on('change', 'select[name="period"]', function(){
	Plex.gM.update($(this).val(), 'setPeriod');
});

// send campaign
$(document).on('click', '.send-campaign-btn', function(){
	Plex.gM.sendCampagin();

});

$(document).on('click', '.pricing .change-plan a', function(e) {
	e.preventDefault();
	window.location.href = "/settings/billing?plans=1";
});

// upgrade-and-send is only shown for free trial users
// if they choose this they have to change plan then pay for it
$(document).on('click', 'a.button.upgrade-and-send-campaign', function(e) {
	e.preventDefault();
	Plex.gM.closeMsgNotifyModal();

	var save_campaign_btn = $('.save-campaign-btn');
	save_campaign_btn.attr({'data-isfreetrial' : '0', 'data-needchangeplan' : '1'});
	Plex.gM.save(save_campaign_btn, false);
	
});

// save campaign
$(document).on('click', '.save-campaign-btn', function(){
	Plex.gM.save($(this), false);
});

// campaign preview open
$(document).on('click', '.lg-preview-btn', function(){
	var _this = $(this);
	var button = _this.find('.button');
	if(button.html() == "Preview") {
		button.html('Hide');
	} else {
		button.html('Preview');
	}
	Plex.gM.togglePreviewContainer();
});
// preview campaign
$(document).on('click', '.preview-btn', function(){
	Plex.gM.togglePreview($(this));
});

// remove student from campaign_list
$(document).on('click', '.remove-std-btn', function(){
	var rid = $(this).parent().data('info');
	Plex.gM.removeStudent(rid);
});

//modify campaign
$(document).on('click', '.c-view, .c-modify', function(){
	if( $('.campaign-start input[type="radio"]:checked').length > 0 ){
		Plex.gM.toggleView();
	}
});

//render preview based on what previous campaign is currently selected
$(document).on('change', 'input[name="previous_campaigns"]', function(){
	var camp = $(this).val();
	Plex.gM.modify(camp);
});

//yes send campaign
$(document).on('click', '.send-away-btn', function(){
	Plex.gM.update(true, 'setSend');
	Plex.gM.closeMsgModal();
	Plex.gM.save($(this), true);
});

//search term on submit click
$(document).on('click', '.search-btn', function(){
	Plex.gM.searchFor($('.search-input').val());
});


// show more 
$(document).on('click', '.campaign-start .show-more', function() {
	var current_campaign_list = $(this).parents('.campaign-start').find('.c-list:visible');
	var current_ul_height = current_campaign_list.find('ul');

	// console.log(current_ul_height.height());

	var hidden_elem = 0;
	current_campaign_list.find('ul li').each(function() { 
		if($(this).position().top - 150 > current_ul_height.height()) 
			hidden_elem++;
	});

	// console.log(hidden_elem);
	var newheight = (current_ul_height.height() + 190).toString();

	if(hidden_elem > 0) {
		current_ul_height.css('max-height', newheight + 'px');
	} else if(hidden_elem == 0) {
		$('.campaign-start .show-more').addClass('hide');
	}
});

//or search on enter press
$('.search-input').keypress(function(e){

	if( e.which === 13 )
		Plex.gM.searchFor($(this).val());

});

$(document).on('click', '.exit-off-canvas', function(){
	Plex.gM.resultsView('close');
	$('.group-msg-container').addClass('autoscroll');	
});

$(document).on('click', '.edit-std-list-btn', function(){
	$('.group-msg-container').removeClass('autoscroll');	
});

//search result click - add to recipient list if not already there
$(document).on('click', '.result-item', function(){
	var data = $(this).data('info');
	Plex.gM.setRecipients($(this));
	Plex.gM.resultsView('close');
	$('.search-input').val('');
});

//search campaigns
$('.s-campaign-input').keypress(function(e){
	if( e.which === 13 ){
		var camp_results = $( ".camp-label:contains('"+$(this).val()+"')" ).css( "text-decoration", "underline" );
		Plex.gM.searchCampaigns(camp_results);
	}
});

$(document).on('click', '.clear-s-btn', function(){
	$('.s-campaign-input').val('');
	Plex.gM.toggleCampSearchView('', 'close');
});

//toggle new campaign list structure
$(document).on('click', '.t-tabs > div.left', function(){
	var tab = $(this).data('tab');
	Plex.gM.toggleLists($(this));
});

// open the save message template modal
$(document).on('change', '#insert_message_template', function(){
	if( $(this).is(':checked') ){
		Plex.gM.openSaveMessageTemplates($(this));
	}
});

$(document).on('click', '#save-template-modal .close-reveal-modal', function () {
	$('#insert_message_template').prop('checked', false);
});

// save message template 
$(document).on('click', '.save-template-btn', function(){
	Plex.gM.saveMessageTemplates($(this));
});

// load message template 
$(document).on('change', '#message_template_dropdown', function(){
	Plex.gM.loadMessageTemplates($(this));
});

// Automatic campaign pending switch
$(document).on('change', '#pending-on-off', function() {
	Plex.gM.setAutomaticCampaign('pending', $(this));
});

// Automatic campaign handshake switch
$(document).on('change', '#handshake-on-off', function() {
	Plex.gM.setAutomaticCampaign('handshake', $(this));
});



// old select or unselect from previous camps, add/remove selected from current camps  
// $(document).on('change', 'input[name="select_from_prev_camps"]', function(){
// 	var camp = $(this).val();
// 	if( $(this).is(':checked') ){
// 		//add recipients from this campaign to list
// 		Plex.gM.addRecipientsFromPrevCamps(camp);
// 	} else {
// 		Plex.gM.removeRecipientsFromPrevCamps(camp);
// 	}
// });

//append results inside div
// $(document).on('change', '.add-from-prev-camps select', function(){
// 	var _this = $(this);
// 	var camp = _this.find('option:selected');
// 	var camp_id = camp.val();
// 	var camp_name = camp.html().trim();
// 	if(camp_id != "") {
// 		var inject_label_Area = $(this).siblings('.inject-label-from-prev-camps').find('ul');
// 		var label_already_exist = inject_label_Area.find('label.camp-label.previ');

// 		//check if the label already exists
// 		if(label_already_exist.length != 0) {
// 			//loop to find the existed one
// 			for(var idx = 0; idx < label_already_exist.length; idx++) {
// 				if(label_already_exist[idx]['htmlFor'] == camp_id)
// 					return ;
// 			}
// 		}

// 		//inject label
// 		inject_label_Area.append('<label class="camp-label previ" for="' + camp_id + '"><span>' + camp_name + '&nbsp;</span><span>&nbsp;&times;</span></label>');
// 		//add recipients
// 		Plex.gM.addRecipientsFromPrevCamps(camp_id);
// 	}
// });

//remove results from prev camps
// $(document).on('click', '.add-from-prev-camps label.camp-label.previ span:last-child', function(){
// 	var _this = $(this);
// 	var label = _this.closest('label');
// 	var camp_id = label.attr('for');
// 	//delete the label
// 	if(label.length != 0)
// 		label.remove();
// 	if(camp_id != '') {
// 		Plex.gM.removeRecipientsFromPrevCamps(camp_id);
// 	}	
// });


$(document).on('click', '.r-profile', function(){
	var _this_view = $(this).closest('.student').find('.r-profile-view');


	if( _this_view.is(':visible') ){
		_this_view.hide();
	}else{
		$('.r-profile-view').hide();
		_this_view.slideDown(250);
	}
});

$(document).on('change', '#attachments', function(){
	Plex.gM.addFile($(this), 'attach');	
});

$(document).on('change', '#insert_pic_attachments', function(){
	Plex.gM.addFile($(this), 'img');	
});

$(document).on('click', '.c-delete', function(){
	var selected = $('input[name="previous_campaigns"]:checked');
	if( selected.length > 0 ){
		Plex.gM.deleteCamp(selected.val());
	}
});

$(document).on('click', 'a.action-bar-btn', function(){
	Plex.gM.toggleAutomaticCamp();
});

$(document).on('change', 'input[name="isTollFree"]', function() {
	Plex.gM.searchAgain();

	if($(this).val() == "yes") {
		Plex.gM.hideTollFreeNumSearch();		
	} else {
		Plex.gM.showTollFreeNumSearch();
	}
});

$(document).on('click', '.search-number a.button.success', function() {
	Plex.gM.searchPhoneNumbers($(this));
});

$(document).on('click', '.phone-list-available a.button.search-again', function() {
	Plex.gM.searchAgain();
});

$(document).on('click', '.phone-list-available a.button.success' , function() {
	Plex.gM.confirmPhoneNumber();
});

$(document).on('click', '.show-more-phonelist', function() {
	Plex.gM.showMorePhoneList();
});

$(document).on('click', '.back-to-search-number span', function() {
	Plex.gM.backToFreeTrialSearch();
});

$(document).on('click', '.forward-to-textmsg a.button.success', function(e) {
	e.preventDefault();
	Plex.gM.forwardTextMsg();
});

$(document).on('change', 'input#upload-csv', function(e) {
	var chosen_file = e.target.files[0];
	Plex.gM.uploadCsv(chosen_file);
});

$(document).on('change', 'input[name="phone-number-available"]', function() {
	Plex.gM.activeContinue();
});

$(document).on('click', 'input[name="term-condition"]', function() {
	if($(this).is(':checked')) {
		Plex.gM.activateSendAway();		
	} else {
		Plex.gM.deactivateSendAway();
	}
});