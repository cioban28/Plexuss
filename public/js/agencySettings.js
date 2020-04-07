// agencySettings.js

Plex.agencySettings = {
	currentTab: '',
	getSection_route: '/agency/ajax/getSettingsSection/',
	saved_fields: [],
	current_fields: [],
	fields_obj_array: [],
	successfullySavedMsg: {
		type: 'soft',
		backGroundColor: '#a0db39',
		textColor: '#fff',
		img: '/images/topAlert/checkmark.png',
		dur: '3000',
		msg: 'Your profile has been saved successfully!'
	},
	successfullyRemovedSpecializedSchool: {
		type: 'soft',
		backGroundColor: '#a0db39',
		textColor: '#fff',
		img: '/images/topAlert/checkmark.png',
		dur: '3000',
		msg: 'You have successfully removed this school from the list.'
	},
	successfullyRemovedCustomService: {
		type: 'soft',
		backGroundColor: '#a0db39',
		textColor: '#fff',
		img: '/images/topAlert/checkmark.png',
		dur: '3000',
		msg: 'You have successfully removed this custom service from the list.'
	},
}

//show ajax loader
Plex.agencySettings.showLoader = function(){
	$('.manage-students-ajax-loader').show();
}

//hide ajax loader
Plex.agencySettings.hideLoader = function(){
	$('.manage-students-ajax-loader').hide();
}

//  Agency services
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

$(document).on('click', '.new-service-remove-btn', function(event) {
	event.preventDefault();
	var parent = $(this).closest('.user-added-service');
	parent.remove();
});
// End agency services 

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
		if (Plex.agencySettings.validateHours($(this))) {
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

Plex.agencySettings.validateHours = function(select_input) {
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
/** End hours of operation **/

$(document).ready(function(){
	Plex.agencySettings.currentTab = $('.agency-settings-sidenav li.active').data('agency-menu-tab');
	Plex.agencySettings.toggleStates();
	// $('.agency-settings-sidenav li[data-agency-menu-tab="'+Plex.agencySettings.currentTab+'"]').addClass('active');
	
	$(document).foundation({
		abide : {
		  patterns: {
		    age: /^([1-9]?\d|100)$/,
		    phone_num: /^([0-9\-\+\(\) ])+$/,
			name: /^([a-zA-Z\-\.' ])+$/,
		  }
		}
	});
});

//----- get agency settings section - start
$(document).on('click', '.agency-menu-tab', function(e){
	e.preventDefault();
	var tab = $(this).data('agency-menu-tab');
	Plex.agencySettings.currentTab = tab;
	Plex.agencySettings.getSection(Plex.agencySettings.currentTab);
});

//makes ajax call to get corressponding section
Plex.agencySettings.getSection = function(tab){
	$('.agency-settings-sidenav li').removeClass('active');

	$.ajax({
		url: Plex.agencySettings.getSection_route + tab,
		type: 'GET',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	}).done(function(section){
		$('.agency-settings-sidenav li[data-agency-menu-tab="'+Plex.agencySettings.currentTab+'"]').addClass('active');
		$('.agency-settings-rightside-content-container').html(section);
	});
}
//----- get agency settings section - end

//----- Save profile information
$(document).on('click', '.saveProfile-btn', function(){
	//get current form fields

	//pass to validation function - if valid, returns true, else returns false
	if( Plex.agencySettings.fieldsAreValid() ){
		Plex.agencySettings.saveProfileInfo();
	}

});

Plex.agencySettings.reloadThisSection = function(){
	$('.agency-settings-sidenav > li[data-agency-menu-tab="'+Plex.agencySettings.currentTab+'"]').trigger('click');
}

Plex.agencySettings.saveProfileInfo = function(){
	var form_id = '#' + Plex.agencySettings.currentTab + 'Form',
		formData = new FormData( $(form_id)[0] ),
		days_of_operation = Plex.agencySettings.formatBusinessHours(),
		services = [];

	formData.append('days_of_operation', JSON.stringify(days_of_operation));

	// Grab all checked services
	$.each($('.service-checkboxes > .service'), function() {
		if ($(this).find(':checkbox').is(':checked')) {
			var label = $(this).find('label').text().trim();
			services.push(label);
		}
	});

	formData.append('services', JSON.stringify(services));

	Plex.agencySettings.showLoader();

	$.ajax({
		url: '/agency/ajax/saveProfileInfo',
		type: 'POST',
		data: formData,
		contentType: false,
    	processData: false,
    	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	}).done(function(data){
		Plex.agencySettings.hideLoader();
		Plex.agencySettings.reloadThisSection();
		topAlert(Plex.agencySettings.successfullySavedMsg);
	});
}

Plex.agencySettings.formatBusinessHours = function(){
	var days_of_operation = {};

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

	return days_of_operation;
}

//validate required fields before being able to save
Plex.agencySettings.fieldsAreValid = function(){
	var is_valid = true;
	var form_id = '#' + Plex.agencySettings.currentTab + 'Form';
	var all_fields = $(form_id + ' .form-field');
	var each_checkbox = 0;
	var at_least_one_checkbox = $('.service-checkboxes input').is(':checked');

	$.each(all_fields, function(){
		var _this = $(this);
		//check if any of the required fields are invalid or empty
		if( _this.attr('required') && _this.parent().find('small.error').is(':visible') ){
				is_valid = false;
				return false;
		}else if( _this.attr('required') && _this.data('type') === 'select' && _this.val() === '' ){
				console.log(_this);
			is_valid = false;
			return false;
		}
	});

	// Validate atleast one services offered option selected
	if (!at_least_one_checkbox) {
		is_valid = false;
	}

	Plex.agencySettings.showHideErrorMsg(is_valid);
	return is_valid;
}

Plex.agencySettings.showHideErrorMsg = function(valid){
	var form_id = '#' + Plex.agencySettings.currentTab + 'Form';

	if( valid ){
		$(form_id + ' .agency-settings-error-msg').slideUp(250);
	}else{
		$(form_id + ' .agency-settings-error-msg').slideDown(250);
	}
}

Plex.agencySettings.makeFieldsIntoObjArray = function(){
	var form_id = '#' + Plex.agencySettings.currentTab + 'Form';
	var all_fields = $(form_id)[0];
	var all_fields_array = [];
	var newFormData = new FormData(all_fields);

	$.each(all_fields, function(){
		var _this = $(this);
		var tmp = {};

		if( _this.attr('type') !== 'hidden' ){
			tmp.name = _this.attr('name');
			tmp.type = _this.attr('type') === undefined ? _this.data('type') : _this.attr('type');
			if( tmp.type === 'file' && _this.val() !== '' ){
				tmp.val = newFormData;
			}else{
				tmp.val = _this.val();
			}
		}

		if( !$.isEmptyObject(tmp) ){
			all_fields_array.push(tmp);	
		}
	});

	return all_fields_array;
}

$(document).on('keypress', '#specialized_school_search', function(){
	var _this = $(this);
	var this_val = _this.val();
	Plex.agencySettings.collegeAutocomplete(_this);
});

Plex.agencySettings.collegeAutocomplete = function(elem){
	$(elem).autocomplete({
		source: '/getslugAutoCompleteData?type=colleges&urlslug=',
		minLength: 1,
		create: function(){
			$(this).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
				var inner_html = '';
				inner_html +=  '<a><div class="list_item_container">';
				inner_html +=  		'<div class="title">' + item.label + '</div>';
				inner_html +=  		'<div class="description">' + item.state + ', ' + item.city + '</div>';
				inner_html +=  '</div></a>';
				
				return $( "<li></li>" )
					.data( "item.autocomplete", item )
					.append(inner_html)
					.appendTo( ul );
			};
		},
		select: function(e, ui){
			e.preventDefault();
			var school_added = '<div class="specialed-item clearfix">';
			school_added += 		'<div class="specialized-inner left">' + ui.item.label + '</div><div class="specialized-inner right remove-specialized-school"> X </div>';
			school_added += 		'<input type="hidden" value="' + ui.item.id + '" name="schools_specialized_in[]" />';
			school_added += 	'</div>';
			$('.specialized-schools-container').append(school_added);
			$(elem).val('');
		},
	});
}

//click event for removing a specialized school from the list
$(document).on('click', '.remove-specialized-school', function(){
	var self = $(this);
	if( confirm('Are you sure you want to permanentely remove this school from your specialized list? (You can always add this school back to your list later)') ){
		Plex.agencySettings.removeSchoolFromSpecializedList(self);
	}
});

//function to remove a row in the specialized schools list
Plex.agencySettings.removeSchoolFromSpecializedList = function(elem){
	var college_id = $(elem).parent().find('input').val();

	$.ajax({
		url: '/agency/ajax/removeAgentsSpecializedSchool/' + college_id,
		type: 'POST',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	}).done(function(ret){
		$(elem).parent().remove();
		topAlert(Plex.agencySettings.successfullyRemovedSpecializedSchool);		
		Plex.agencySettings.reloadThisSection();
	});
}

//on enter key press, add custom service to list
$(document).on('keypress', '#other-services-offered-input', function(e){
	var self = $(this);
	if( e.which === 13 ){
		Plex.agencySettings.addCustomService(self.val());
		self.val('');
	}	
});

//add custom service to list of services
Plex.agencySettings.addCustomService = function(service){
	var other_service_container = $('.other-services-offered-container');
	var ul_exists = $('.other-services-offered-container').find('ul');
	var newService = '<li class="remove-custom-service"><label class="soft-labels">'+service+' <span> X </span></label>';
	newService += 		'<input type="hidden" value="'+service+'" name="custom_services[]" />';
	newService += 	'</li>';
	var startNewUL = '<ul></ul>';

	if( ul_exists.length !== 0 ){
		ul_exists.append(newService);
	}else{
		other_service_container.html(startNewUL).children().append(newService);
	}
}

//remove custom service offered click event
$(document).on('click', '.remove-custom-service', function(){
	if( confirm('Are you sure you want to delete this custom service? (You can always re-add it later)') ){
		Plex.agencySettings.removeCustomService(this);
	}
});

//removes custom service
Plex.agencySettings.removeCustomService = function(elem){
	var self = $(elem);
	var ul_elem = self.parent();
	var service_value = self.find('input').val();

	//ajax call to remove custom service from db
	$.ajax({
		url: '/agency/ajax/removeCustomAgentService',
		type: 'POST',
		data: {service_val: service_value},
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	}).done(function(ret){
		topAlert(Plex.agencySettings.successfullyRemovedCustomService);
		//if there are more than one items in the list, then just remove it
		//else if at the last item, just remove the entire ul
		if( ul_elem.children().length > 1 ){
			self.remove();
		}else{
			ul_elem.remove();
		}
	});
}

$(document).on('change', 'select[name="country"]', function(){
	Plex.agencySettings.toggleStates();
});

//if country = United States then show states dropdown, otherwise hide it
Plex.agencySettings.toggleStates = function(){
	var country_val = $('select[name="country"]').val();

	if( country_val === 'United States' ){
		$('select[name="state"]').parent().slideDown(50);
	}else{
		$('select[name="state"]').parent().slideUp(50);
	}
}
//----- End profile information


//mobile menu open menu event
// $(document).on('click', '.mobile-agency-menu-btn', function(){
// 	$(this).parent().find('.agency-settings-sidenav').slideToggle(250);
// });