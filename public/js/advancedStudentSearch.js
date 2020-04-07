// advancedStudentSearch.js

//global obj
Plex.studentSearch = {
	activeTab: null,
	adminType: null,
	tags: [],
	owl: null,
	unique: 1,
	ajaxFlagProfile: 0, //flag if ajax running do not make another call
	addedFromAdvancedSearchToPending : {
    	textColor: '#fff',
        backGroundColor : 'green',
        msg: 'Student has been added to your Pending list',
        type: 'soft',
        dur : 5000
    },
    failedFromAdvancedSearch : {
    	textColor: '#fff',
        backGroundColor : 'red',
        msg: 'Something went wrong, please try again later',
        type: 'soft',
        dur : 5000
    },
    addStudentManuallyMsg : {
    	textColor: '#fff',
        backGroundColor : 'green',
        msg: 'Student has been added to {{school_name}} {{type}} List',
        type: 'soft',
        dur : 8000
    },
    saveNotesInterval : '',
    _this: null,
    initData: null,
    cityInitDone: false,
    majorInitDone: false,
    sections: null,
    ranges: ['0.00','0 - 5,000','5,000 - 10,000', '10,000 - 20,000', '20,000 - 30,000', '30,000 - 50,000', '50,000'],
	ranges_formatted:  ['$0','$0 - $5,000','$5,000 - $10,000', '$10,000 - $20,000', '$20,000 - $30,000', '$30,000 - $50,000', '$50,000+'],

	addCollegeMatch: function(){
		
	}


};

//abide validation pattern definition
$(document)
  .foundation({
    abide : {
      patterns: {
        age: /^([1-9]?\d|100)$/,
        gpa: /^(?:[0-3]?\.{1}\d{1,2}|[0-4]{1}\.{1}[0]{1,2}|[0-4]{1}){1}$/,  // /^(?!0$)(([0-3]){1}\.([0-9]){1,2}|4\.(0){1,2}|([0-4]){1})$/,
		toefl: /^([0-9]?[0-9]|[1][0-1][0-9]|12[0])$/,
		ielts: /^[0-9]{1}$/,
		sat: /^([6-9][0-9][0]|[1][0-9][0-9][0]|[2][0-3][0-9][0]|[2][4][0][0])$/,
		act: /^([0-9]|[1-2][0-9]|[3][0-6])$/,
      }
    }
  });

//doc ready
$(document).ready(function(){

	Plex.studentSearch.adminType = $('.student-search-container').data('admin-type');

	var resultsOnload = $('.list-of-results-container').data('has-results-onload');
	Plex.studentSearch.hasResultsCheck(resultsOnload);

	var total_results_count = $(".total_results_count"),
		cnt = total_results_count.data("totalresultscount"),
		comma_separator_number_step = $.animateNumber.numberStepFactories.separator(',');

	total_results_count.animateNumber({ number: cnt,
    									numberStep: comma_separator_number_step });

	//init section objs
	Plex.studentSearch.initSections();

	//check if filter lock data is true, if so, shutdown filters
	var filterLocked = Plex.studentSearch.isFilterLocked();
	if( filterLocked ) Plex.studentSearch.shutdownFilters(); 

	/////////////////////////////////////////////////////
	// opens modal for prescreen upload
	//when upload btn is clicked, update modal's form docType hidden input value before opening the modal
	$(document).on('submit', '#upload_prescreen_form', function(e){
		e.preventDefault();
		var form = new FormData(e.target);

		$.ajax({
			url: '/admin/studentsearch/uploadPreScreenFile',
			type: 'POST',
			data: form,
			enctype: 'multipart/form-data',
			contentType: false,
        	processData: false, 
        	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success: function(data){
		        $('#upload_prescreen_modal').foundation('reveal', 'close');
			},
			error: function(err){
				console.log('err: ', err);	
			}
		});
	});

	$(document).on('click', '#upload_prescreen_btn', function(e){
	    e.preventDefault();
	    var docType = $(this).data('doc-type');

	    if( docType === '' ){
	        docType = 'UnknownDocType';
	    }


	    if( docType === 'prescreen' ){
	        $('#upload_prescreen_modal').foundation('reveal', 'open');
	        $('#upload_prescreen_form input.doctype').val(docType);
	    }
	});


	// ////////////////////////////////////////////////////////////////////
	// $(document).on('focusin', '.collegeSearch', function(){
	// 	$(this).val(' ');

	// 	$(this).one('focusout', function(){
	// 		$(this).val('search for a college...');
	// 	});
	// });

	
	// ////////////////////////////////////////////////////////////////////
	// //+ADD btn
	// $(document).on('click', '.add-college-matches-btn', function(e){
	
	// 	//show modal --parent class very generic ... parent().parent()..
	// 	$(this).parent().parent().parent().find('.add_college_modal').toggle();
	// 	$(this).parent().parent().parent().find('.matched_colleges_container').toggle();
		
	// 	//change add btn to reflect opened/closed state
	// 	if($(this).hasClass('closed-color')){
	// 		$(this).removeClass('closed-color');
	// 		$(this).addClass('opened-color');
	// 	}
	// 	else{
	// 		$(this).addClass('closed-color');
	// 		$( this).removeClass('opened-color');	
	// 	}

	// 	$(this).find('.college-add-btn').toggle();
	// 	$(this).find('.college-close-btn').toggle();

	// });



	// /////////////////////////////////////////////////////////////////////
	// //get results as typing in search field
	// $(document).on('keyup', '.collegeSearch', function(event){

	// 	var el = $(this).closest('.add_college_modal').find('.collegeResults');
	// 	el.html('Loading...');	//clear search box to repopulate with new results
		
	// 	var data = {
	// 		keyword: $(event.target).val()
	// 	};

	// 	$.ajax({
	// 		url: '/admin/studentsearch/searchForCollegesByKeyword',
	// 		type: 'POST',
	// 		data: data
	// 	}).done(function(data){
			
	// 		el.html('');	//clear search box to repopulate with new results
			
	// 		if(data.length === 0 )
	// 			el.text('No results found.');

	// 		for(var i in data){
	// 			//create listing for college and insert into #collegeResults
	// 			var div = $("<div>", {"class": "row college-listing-cont green-h college-listing-with-handler"});
	// 			var picdiv = $("<div>", {"class": "column large-2  medium-2 small-2 college-icon-cont"});
	// 			var pic = $("<img>", {"src": 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/' + data[i].logo_url});
	// 			var descContain = $("<div>", {"class": "column large-10 medium-10 small-10 college-desc-cont"});
	// 			var collegeName = $("<div>", {"class" : "match-college-name"});
	// 			var collegeLoc = $("<div>", {"class" : "match-college-place"});


	// 			//attatch college_id to div?
	// 			div.data('info', data[i].college_id);

	// 			collegeName.html(data[i].school_name);
	// 			collegeLoc.html(data[i].city +  ', ' + data[i].state);

	// 			picdiv.append(pic);
	// 			descContain.append(collegeName);
	// 			descContain.append(collegeLoc);
				
	// 			div.append(picdiv);
	// 			div.append(descContain);
				
	// 			el.append(div);
	// 		}
	// 	});
	// });

	// //////////////////////////////////////////////////////////////////
	// //selecting college
	// $(document).on('click', '.college-listing-with-handler', function(e){
		
	// 	var el = e.target;
	// 	var element = el.closest('.college-listing-cont');

	// 	var pCont = $(this).parent().parent().parent(); 	//parent container

	// 	//get image, college name, college location from first screen
	// 	//populate them in next screen
	// 	var name = $(element).find('.match-college-name').text();
	// 	var place = $(element).find('.match-college-place').text();
	// 	var picsrc = $(element).find('.college-icon-cont img').attr('src');

	// 	var portalCont = pCont.find('.portals-radio-container');  //portals container


	// 	//create listing for college and insert into .college-head
	// 	var div = $("<div>", {"class": "row college-listing-cont"});
	// 	var picdiv = $("<div>", {"class": "column large-2 medium-2 small-2 college-icon-cont"});
	// 	var pic = $("<img>", {"src": picsrc});
	// 	var descContain = $("<div>", {"class": "column large-10 medium-10 small-10 college-desc-cont"});
	// 	var collegeName = $("<div>", {"class" : "match-college-name"});
	// 	var collegeLoc = $("<div>", {"class" : "match-college-place"});

	// 	collegeName.html(name);
	// 	collegeLoc.html(place);

	// 	picdiv.append(pic);
	// 	descContain.append(collegeName);
	// 	descContain.append(collegeLoc);
		
	// 	div.append(picdiv);
	// 	div.append(descContain);

	// 	pCont.find('.college-head').html(div);



	// 	//grab college_id
	// 	var collegeId = $(element).data('info');  //grab college_id
	// 	div.data('info', collegeId); //attatch college_id data to this element too

	// 	var data = {
	// 		college_id: collegeId
	// 	};

	// 	//show loading before ajax completed
	// 	portalCont.text('Loading...');
		
	// 	//get portals
	// 	$.ajax({
	// 		url: '/admin/studentsearch/getOrganizationPortalsForThisCollege',
	// 		type: 'POST',
	// 		data: data 

	// 	}).done(function(data){

	// 		portalCont.html('');

	// 		//get portals and insert into .portals-radio-container
	// 		//General portal always exists
	// 		 var gradio = $('<input>' , {'type': "radio", 'name':"portal", 'id': 'generalPortal'});
	// 		 portalCont.append(gradio);

	// 		 if(data.general.org_portal_name)
	// 		 	portalCont.append( $('<label>', {'for': 'generalPortal' }).text(data.general.org_portal_name) );
	// 		 else
	// 			portalCont.append( $('<label>', {'for': 'generalPortal' }).text(data.general.aor_portal_name) );

	// 		 portalCont.append('<br />');
	// 		 gradio.data('aorId', data.general.aor_id);
	// 		 gradio.data('orgId', data.general.org_portal_id);
	// 		 gradio.data('aorPortalId', data.general.aor_portal_id);
			
	// 		//for each portal, add a radio button
	// 		var radio, label;
	// 		for(var i in data.portals){
				
	// 			radio = $('<input>' , {'type': "radio", 'name':"portal", 'id': 'portal'+i});
				

	// 			portalCont.append(radio);

	// 			label = $('<label>', {'for': 'portal'+i });

	// 			//should always be that there is an org portal if no aor, if aor no org portal
	// 			if(data.portals[i].org_portal_name != null){
	// 				radio.val(data.portals[i].org_portal_name);
	// 				label.text(data.portals[i].org_portal_name);
	// 			}
	// 			else{
	// 				radio.val(data.portals[i].aor_portal_name);
	// 				label.text(data.portals[i].aor_portal_name);
	// 			}
	// 			portalCont.append(label);

	// 			//set org and aor ids
	// 			//if not org then aor
	// 			if(!data.portals[i].org_portal_id){
	// 				radio.data('aorId', data.portals[i].aor_id);
	// 				radio.data('aorPortalId', data.portals[i].aor_portal_id);
	// 				radio.data('orgId', -1);
	// 			}else{
	// 				radio.data('aorId', -1);
	// 				radio.data('aorPortalId', -1);
	// 				radio.data('orgId', data.portals[i].org_portal_id);
	// 			}
				
	// 			portalCont.append('<br />');
	// 		}
	// 	});		

	// 	//show next step
	// 	pCont.find('.s1').hide();
	// 	pCont.find('.s2').show();

	// });


	// /////////////////////////////////////////////////////
	// //back button - on s2
	// $(document).on('click', '.add-back-btn', function(){

	// 	var pCont = $(this).closest('.add_college_modal'); //parent container

	// 	pCont.find('.s1').show();
	// 	pCont.find('.s2').hide();
	// });


	// ////////////////////////////////////////////////////////////
	// //Add School button
	// $(document).on('click', '.add-college-btn', function(e){

	// 	e.preventDefault();

		
	// 	var pCont = $(this).closest('.add_college_modal');
		
	// 	//get checked portal
	// 	var portal = pCont.find('.portals-radio-container input[type="radio"]:checked');
		
	// 	//get college info from last step( step 2) -- not same place as step 1
	// 	var el = $('.add_college_modal .college-head');
	// 	var name =  el.find('.match-college-name').text();
	// 	var place = el.find('.match-college-place').text();
	// 	var picsrc = el.find('.college-icon-cont img').attr('src');
		
	// 	var ele = el.find('.college-listing-cont');

	// 	//grab college_id
	// 	var collegeId = ele.data('info');

	// 	//get portal ids (set with Jquery .data())
	// 	var userId = $('#student_info').val();
	// 	var aorId = portal.data('aorId');
	// 	var orgId = portal.data('orgId');
	// 	var aorPortalId = portal.data('aorPortalId');
	// 	//console.log(aorId + ' ' + orgId + ' ' + aorPortalId);
	// 	Plex.studentSearch.addStudentManual('PreScreened', userId , collegeId , aorId, orgId, aorPortalId, name, function(){
			
	// 		//close this modal once done
	// 		pCont.hide();
	// 		var parent = pCont.parent();
	// 		parent.find('.matched_colleges_container').show();
	// 		//swtich back to step 1
	// 		pCont.find('.s1').show();
	// 		pCont.find('.s2').hide();


	// 		//change ADD btn to reflect opened/closed state
	// 		if(parent.find('.add-college-matches-btn').hasClass('opened-color')){
	// 			parent.find('.add-college-matches-btn').addClass('closed-color');
	// 			parent.find( '.add-college-matches-btn').removeClass('opened-color');	
	// 			parent.find('.college-add-btn').toggle();
	// 			parent.find('.college-close-btn').toggle();
	// 		}
	// 	});
				
	// });


}); //end document.ready


//on doc ready, check if any sections are locked
//make all sections, except location/major locked
Plex.studentSearch.isFilterLocked = function(){
	return !!parseInt($('ul.search-sidenav').data('locked'));
}

Plex.studentSearch.shutdownFilters = function(){
	_.each(Plex.studentSearch.sections.list, function(obj, index, arr){
		if( obj.name !== 'location' && obj.name !== 'major' && obj.name !== 'startDateTerm' && obj.name !== 'financial' && obj.name !== 'schooltype' ){
			$('li[data-search-tab="'+obj.name+'"]').addClass('dekcol');
			obj.lock();
		}
	});
}


//init section obj start
Plex.studentSearch.initSections = function(){
	var sections = $('li.search-tab'), tab = null;

	Plex.studentSearch.sections = new SectionList();

	$.each(sections, function(val, index){
		tab = $(this).data('search-tab');
		Plex.studentSearch.sections.addSection( new Section(tab) );
	});
}


var Section = function Section(name){
	this.name = name;
	this.changed = !1;
	this.locked = !1;
}

Section.prototype.hasChanged = function(){
	this.changed = !0;
	Plex.studentSearch.sections.updateView();
}

Section.prototype.lock = function(){
	this.locked = !0;
}

Section.prototype.reset = function(){
	this.changed = !1;
	Plex.studentSearch.sections.updateView();
}

var SectionList = function SectionList(){
	this.list = [];
}

SectionList.prototype.addSection = function(section){
	this.list.push(section);
}

SectionList.prototype.updateView = function(){
	var elem = null;
	_.each(this.list, function(obj, index, arr){
		elem = $('li.search-tab[data-search-tab="'+obj.name+'"] .change-icon');
		if( obj.changed ) elem.removeClass('hide');
		else if( !obj.changed ){
			if( elem.is(':visible') ) elem.addClass('hide');
		}
	});
}

SectionList.prototype.resetAll = function(){
	_.each(this.list, function(obj){
		if( obj.changed ) obj.reset();
	});
}

SectionList.prototype.getSection = function(section_name){
	return _.findWhere(this.list, {name: section_name});
}

SectionList.prototype.getChangedSections = function(bool){
	return _.where(this.list, {changed: bool});
}
// -- init section obj end

// -- owl carousel left/right slider events
$(document).on('click', '.requested-by .owl-arrow', function(){
	var this_carousel = $(this).parent().find('.student-search-owl');

	if( $(this).hasClass('owl-right') )
		Plex.studentSearch.owlRight(this_carousel);
	else
		Plex.studentSearch.owlLeft(this_carousel);
});

//initialize new owls - called everytime new results are loaded onto the page
Plex.studentSearch.initNewOwl = function(){
	//get all unset owls
	var unsetowls = $('.student-search-owl[data-isset="0"]');
	//if there are any new unset carousels, then set them, otherwise there are no new results so don't need to do anything
	if( unsetowls.length > 0 ){
		//add unique class name
		unsetowls.addClass('owl-' + Plex.studentSearch.unique);

		//get new owls
		var owl = $('.student-search-owl.owl-'+Plex.studentSearch.unique);

		//set owlcarousel to new owl
		owl.owlCarousel({
			pagination: false,
			beforeMove : function(elem){
				var itemsVisible = this.visibleItems;
			    var totalItems = this.itemsAmount;
			    var itemsAllowed = this.options.items;

			    Plex.studentSearch.hideOwlArrows(totalItems, itemsAllowed);
			}
		});

		//increment unique num
		Plex.studentSearch.unique++;
		//make unset owl set
		unsetowls.attr('data-isset', 1);
	}
}

//slide carousel right
Plex.studentSearch.owlRight = function(this_carousel){
	this_carousel.trigger('owl.next');
}

//slide carousel left
Plex.studentSearch.owlLeft = function(this_carousel){
	this_carousel.trigger('owl.prev');
}

//hide arrows if no room to slide
Plex.studentSearch.hideOwlArrows = function(items_total, items_allowed){
	if( items_total <= items_allowed ){
		$('.student-search-owl').parent().find('.owl-arrow').hide();
	}else{
		$('.student-search-owl').parent().find('.owl-arrow').show();
	}
}

$(document).on('click', '.student-search-owl', function(){
	Plex.studentSearch.owlLeft($(this));
});
// -- owl carousel left/right slider events

// save filters
$(document).on('click', '.save-search-btn', function(e){
	e.preventDefault();

	$('#save-filter-template-modal').foundation('reveal', 'open');
	Plex.studentSearch._this = $(this);
	//Plex.studentSearch.updateSearchResults($(this));
});

Plex.studentSearch.saveMessageTemplates = function(){

	var template_name = $('#save_template_name').val();
	if (template_name == '') {
		return;
	}
	$('#save_template_name_val').val(template_name);

	var elem = Plex.studentSearch._this;
	var form = elem.closest('form');
	var formdata = new FormData(form[0]);
	var tmp_arr  = {'name': template_name};

	var route = '/' + Plex.studentSearch.adminType + '/studentsearch/saveFilter';

	$.ajax({
		url: route,
		type: 'POST',
		data: formdata,
		contentType: false,
    	processData: false,
    	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	}).done(function(data){
		var txt = '<option value="'+data+'">'+template_name+'</option>';
		$('.savedFilters-class').append(txt);
		$('#save-filter-template-modal').foundation('reveal', 'close');
	});

};

// save message template 
$(document).on('click', '.save-filter-template-btn', function(){
	Plex.studentSearch.saveMessageTemplates();
});
// end save filter

// Load saved filter

$(document).on('change', '.savedFilters-class', function(e){
	e.preventDefault();

	var selected_option = $('.savedFilters-class').val();
	if (selected_option == '') {
		return;
	}
	var route = '/' + Plex.studentSearch.adminType + '/studentsearch/getAdvancedSearchFilter';

	var hasResults = 1;
	var comma_separator_number_step = $.animateNumber.numberStepFactories.separator(',');
	var savedFilters = null;

	//start ajax loader
	Plex.studentSearch.loadingStart();

	$.ajax({
        url: route,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: {id: selected_option},
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(data, textStatus, xhr) {
		//inject new results
		$('.list-of-results-container').html(data);

		//get has_searchResults data to check if there are more results
		hasResults = $('.hasResults').last().data('last-results');
		Plex.studentSearch.hasResultsCheck(hasResults);

		//hide ajax loader
		Plex.studentSearch.loadingEnd();
		var results_count = $('.hasResults').data('total-results');
		var viewing_count = $('.hasResults').data('viewing-count');
		$('.total_results_count').animateNumber({ number: results_count,
												  numberStep: comma_separator_number_step });
		$('.total_viewing_count').html( $('.resulting-students').length );

		savedFilters = $('.savedFilters').data('saved-filters');
		Plex.studentSearch.initTags(savedFilters);
	});
});

// -- update search results
$(document).on('click', '.update-search-filter-btn', function(e){
	e.preventDefault();
	Plex.studentSearch.updateSearchResults($(this));
});

$(document).on('click', '.clear-search-filter-btn', function(e){
    e.preventDefault();
	Plex.studentSearch.clearFilter();
});

Plex.studentSearch.updateSearchResults = function(elem){
	var form = elem.closest('form');
	var formdata = new FormData(form[0]);
	var route = '/' + Plex.studentSearch.adminType + '/studentsearch/updateSearchResults';
	var formIs = '';
	var hasResults = 1;
	var comma_separator_number_step = $.animateNumber.numberStepFactories.separator(',');

	//validate entire form, if error somewhere, it will return which section error is, otherwise will return 'clean' which is good to run ajax
	formIs = Plex.studentSearch.validateEntireForm();

	//if form is not 'clean', open 'dirty' tab, else update student search results
	if( formIs !== 'clean' ){
		if( Plex.studentSearch.activeTab !== formIs )
			$('li[data-search-tab="'+formIs+'"] > a').trigger('click');
	}else{
		//start ajax loader
		Plex.studentSearch.loadingStart();

		$.ajax({
			url: route,
			type: 'POST',
			data: formdata,
			contentType: false,
	    	processData: false,
	    	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		}).done(function(data){
			//inject new results
			$('.list-of-results-container').html(data);

			//get has_searchResults data to check if there are more results
			hasResults = $('.hasResults').last().data('last-results');
			Plex.studentSearch.hasResultsCheck(hasResults);

			//hide ajax loader
			Plex.studentSearch.loadingEnd();
			var results_count = $('.hasResults').data('total-results');
			var viewing_count = $('.hasResults').data('viewing-count');
			$('.total_results_count').animateNumber({ number: results_count,
    												  numberStep: comma_separator_number_step });
			$('.total_viewing_count').html( $('.resulting-students').length );
		});
	}
}

//show ajax loader
Plex.studentSearch.loadingStart = function(){
	$('.ss-ajax-loader').show();
}

//hide ajax loader
Plex.studentSearch.loadingEnd = function(){
	$('.ss-ajax-loader').hide();
}

//remove 'load more' button and add no more results text when out of results
Plex.studentSearch.hasResultsCheck = function(hasResults){
	if( hasResults !== 1 ){
		$('.load-more-results-btn').parent().addClass('hidden');
		$('.no-results').parent().removeClass('hidden');
	}else{
		$('.load-more-results-btn').parent().removeClass('hidden');
		$('.no-results').parent().addClass('hidden');
	}
}

Plex.studentSearch.clearFilter = function(){
	//reset location, majors, demo ethnicity, and demo religion
	$('input#country_all, input#department_all, input#ethnic_all, input#religion_all').trigger('click');
	//reset scores and demographic age text fields
	$('input[data-type="text"]').val('').trigger('blur');
	//reset demographic gender to all
	$('select#s_gender').val('all').trigger('change');
	//reset inMilitary select field to default 
	$('select#s_inMilitary, select#s_militaryAffiliation').val('').trigger('change');
	//reset Uploads, Education level, Desired Degree - all section with checkboxes only - reset to checked
	$('input[data-type="checkbox"]').prop('checked', true);
	//close all filter boxes
	$('li.search-tab a.active').trigger('click');
	//clear student name search
	$('#studentNameSearch').val('');

	Plex.studentSearch.tags.length = 0;

	$('li[data-search-tab="startDateTerm"]').find('.tag.left').remove();
	$('li[data-search-tab="financial"]').find('.tag.left').remove();
	$('#both_typeofschool').prop('checked', true);

	Plex.studentSearch.updateFilterCrumbView();
	Plex.studentSearch.sections.resetAll();

	$('.cleared-msg').slideDown(250);
	setInterval(function(){
		$('.cleared-msg').slideUp(250);
	}, 3000);
}

Plex.studentSearch.initTags = function(data){
	var name = null, prev_key, filter_obj = {}, tabName = '', section = null;

	//clear filter before adding new filter
	Plex.studentSearch.clearFilter();
	Plex.studentSearch.tags.length = 0;

	//save data globally
	Plex.studentSearch.initData = data;

	$.each(data, function(key, value){
		name = key.split('_');

		for(var i = 0; i < name.length; i++ ){
			if( name[i].toLowerCase().indexOf('gpa') > -1 ){
				name[0] = 'gpa';
				break;
			}else if( name[i].toLowerCase().indexOf('scores') > -1 ){
				name[0] = 'scores';
				break;
			}else if( name[i].toLowerCase().indexOf('ethnic') > -1 ){
				name[0] = 'ethnic';
			}
		}

		section = Plex.studentSearch.sections.getSection(name[0]);

		switch( name[0] ){
			case 'country':
			case 'state':
			case 'city':
			case 'department':
			case 'major':
			case 'religion':
			case 'ethnic':
				if( !prev_key ){
					prev_key = name.join('_');
					filter_obj.name = prev_key;
					filter_obj.val = value;
				}else{
					//use the key to inject value
					filter_obj.tags = value;
					if( filter_obj.val !== 'all' )
						Plex.studentSearch.initLocation(filter_obj);

					prev_key = null;
				}
				break;
			case 'gpa':
			case 'scores':
			case 'age':
				Plex.studentSearch.initScores(key, value);
				break;
			case 'uploads':
			case 'education':
			case 'degree':
				tabName = Plex.studentSearch.getTabName(name[0]);			
				//first, uncheck all checkboxes for each component, then add ones we have values for
				$('.component.'+tabName).find('input[type="checkbox"]').prop('checked', false);
				if( value.length > 0 && section && !section.locked )
					Plex.studentSearch.initUploadsEducationDegree(value);
				break;
			case 'date':
				//if date isn't empty, init date
				if( value )
					Plex.studentSearch.initDate(key, value);
				break;
			case 'startDateTerm':
				Plex.studentSearch.initStartDateTerm(name[0], value);
				break;
			case 'financial':
				Plex.studentSearch.initFinancial(name[0], value);
				break;
			default:
				//if value isn't all, init Gender/militaryAffiliation
				if( value !== 'all' )
					Plex.studentSearch.initGenderMilitary(key, value);	
			break;
		}
	});
	
}

Plex.studentSearch.initCity = function(){
	if( !Plex.studentSearch.initData || !Plex.studentSearch.initData.city ) return;

	_.each(Plex.studentSearch.initData.city, function(value){
		$('#s_cities').val(value).trigger('change');
	});

	Plex.studentSearch.cityInitDone = true;
}

Plex.studentSearch.initMajor = function(){
	if( !Plex.studentSearch.initData || !Plex.studentSearch.initData.major ) return;

	_.each(Plex.studentSearch.initData.major, function(value){
		$('#s_majors').val(value).trigger('change');
	});

	Plex.studentSearch.majorInitDone = true;
}

Plex.studentSearch.getTabName = function(name){
	if( name === 'degree' )
		return 'desiredDegree';
	else if( name === 'education' )
		return name + 'Level';
	else
		return name;
}

Plex.studentSearch.initDate = function(key, value){
	var to = '', from = '', date_split = null;
	date_split = value.split(' ');	

	$('input[name="'+key+'"]').val(value);
	$('input[name="daterangepicker_start"]').val(date_split[0]);
	$('input[name="daterangepicker_end"]').val(date_split[2]);
	$('.applyBtn').trigger('click');
}

Plex.studentSearch.initGenderMilitary = function(key, value){
	$('select[name="'+key+'"]').val(value).trigger('change');
};

Plex.studentSearch.initDateAndFinancial = function(component, arr){
	if( _.isArray(arr) && arr.length > 0 ){
		_.each(arr, function(value, i){
			$('select[name="'+component+'"]').val(value).trigger('change');
		});
	}
};

Plex.studentSearch.initStartDateTerm = Plex.studentSearch.initDateAndFinancial;
Plex.studentSearch.initFinancial = Plex.studentSearch.initDateAndFinancial;

Plex.studentSearch.initUploadsEducationDegree = function(arr){
	if( !$.isArray(arr) ) arr = [arr];
	if( arr.length > 0 ){
		_.each(arr, function(value, index, arr){
			//check all the boxes that we have values for
			$('input[type="checkbox"][value="'+value+'"]').prop('checked', true).trigger('change');
		});
	}
}

Plex.studentSearch.initScores = function(key, arr){
	$('input[name="'+key+'[]"][placeholder="Min"]').val(arr[0]).trigger('blur');
	$('input[name="'+key+'[]"][placeholder="Max"]').val(arr[1]).trigger('blur');;
}

Plex.studentSearch.initLocation = function(obj){
	var elem = $('input[name="'+obj.name+'"][value="'+obj.val+'"]'),
		select_field = elem.closest('.component').find('select'),
		section = elem.closest('li.search-tab').data('search-tab'),
		val = '';

	Plex.studentSearch.activeTab = section;
	elem.prop('checked', true).trigger('change');

	if( obj.tags.length === 1 ){
		val = obj.tags[0] === 'Select...' ? '' : obj.tags[0];
		select_field.val(val).trigger('change');
	}else{
		_.each(obj.tags, function(value, index, arr){
			select_field.val(value).trigger('change');
		});
	}

}
// -- update search results



// -- validation by section
//validate entire form; return tab that has invalid input; else return 'clean'
Plex.studentSearch.validateEntireForm = function(){
	var invalid_page = 'clean';
	var valid = false;
	//var sections = ['scores', 'uploads', 'demographic', 'educationLevel', 'desiredDegree'];
	var sections = ['scores', 'demographic', 'educationLevel', 'desiredDegree'];
	var validator = null;
	var runFunction;

	for( var i = 0; i < sections.length; i++){
		validator = sections[i] + 'IsValid';
		runFunction = Plex.studentSearch[validator];
		valid = runFunction();

		if( !valid ){
			invalid_page = sections[i];
			break;
		}
	}

	return invalid_page;
};

//validate min scores is not greater than max scores
Plex.studentSearch.scoresIsValid = function(){
	var component = $('.component.scores');
	var fields = component.find('input[type="number"]');
	var errorElem = component.parent().find('.error-msg');
	var valid = true;
	var tempMin = 0;

	$.each(fields, function(i){
		var _this = $(this);
		var val = parseFloat(_this.val());

		if( i%2 === 0 ){
			tempMin = val;
		}else{
			if( (_.isNumber(val) && _.isNumber(tempMin) ) && tempMin >= val ){
				valid = false;
			}
		}

	});

	//toggle error message
	Plex.studentSearch.toggleErrorMsg(valid, errorElem);

	return valid;
};

//validate uploads has at least one checked
Plex.studentSearch.uploadsIsValid = function(){
	var component = $('.component.uploads');
	var fields = component.find('input[type="checkbox"]');
	var errorElem = component.parent().find('.error-msg');
	var valid = false;

	valid = Plex.studentSearch.atLeastOneChecked(fields);
	//toggle error message
	Plex.studentSearch.toggleErrorMsg(valid, errorElem);

	return valid;
};

//validate demographic
Plex.studentSearch.demographicIsValid = function(){
	var component = $('.component.age');
	var fields = component.find('input[type="number"]');
	var errorElem = component.parent().find('.error-msg');
	var min = parseInt($(fields[0]).val());
	var max = parseInt($(fields[1]).val());
	var valid = true;

	if( min >= max )
		valid = false;

	//toggle error message
	Plex.studentSearch.toggleErrorMsg(valid, errorElem);

	return valid;
};

//validate that at least hs or college is checked
Plex.studentSearch.educationLevelIsValid = function(){
	var valid = false;
	var errorElem = $('.component.educationLevel').parent().find('.error-msg');

	if( $('#education_hs').is(':checked') || $('#education_college').is(':checked') )
		valid = true;
	else
		valid = false;

	//toggle error message
	Plex.studentSearch.toggleErrorMsg(valid, errorElem);

	return valid;
};

//validate that at least one degree is checked
Plex.studentSearch.desiredDegreeIsValid = function(){
	var component = $('.component.desiredDegree');
	var form_fields = component.find('input[type="checkbox"]');
	var errorElem = component.parent().find('.error-msg');
	var valid = false;

	//atLeastOneChecked will return true or false and then toggle error msg if there is an error or not
	valid = Plex.studentSearch.atLeastOneChecked(form_fields);
	Plex.studentSearch.toggleErrorMsg(valid, errorElem);

	return valid;	
};

//returns true if at least one checkbox is checked, else returns false
Plex.studentSearch.atLeastOneChecked = function(fields){
	var oneChecked = false;

	$.each(fields, function(){
		if( $(this).is(':checked') ){
			oneChecked = true;
			return false;
		}	
	});

	return oneChecked;
};

//toggles error msg based on validity
Plex.studentSearch.toggleErrorMsg = function(valid, errorElem){
	if( !valid )
		errorElem.removeClass('hidden');
	else
		errorElem.addClass('hidden');
};

// -- validation by section



// -- tags
//save tags in global array
Plex.studentSearch.saveTag = function(belongs, val, type, text, option){
	var tag = {};
	tag.belongs_to = belongs;
	tag.val = val;
	tag.component = type;
	tag.text = text;
	tag.option = option || null;
	Plex.studentSearch.tags.push(tag);
}

//update tag based on prop
Plex.studentSearch.updateTag = function(tag, prop, val){
	var update_this_tag = null;

	_.each(tag, function(obj, index, arr){
		update_this_tag = _.findWhere(Plex.studentSearch.tags, obj);
		update_this_tag[prop] = val;
	});
}

//remove tags from global array
Plex.studentSearch.removeTag = function(tag_val){
	Plex.studentSearch.tags = _.reject(Plex.studentSearch.tags, {val: tag_val});
}

//remove multiple tags by section type
Plex.studentSearch.removeTagBySection = function(tag_type){
	Plex.studentSearch.tags = _.reject(Plex.studentSearch.tags, {belongs_to: tag_type});
}

//remove multiple tags by component type
Plex.studentSearch.removeTagByComponent = function(tag_type){
	Plex.studentSearch.tags = _.reject(Plex.studentSearch.tags, {component: tag_type});
}

Plex.studentSearch.removeCrumb = function(section, comp, valu){
	Plex.studentSearch.tags = _.reject(Plex.studentSearch.tags, {val: valu});
}

//get tags by component
Plex.studentSearch.getTagsByComponent = function(type){
	return _.filter(Plex.studentSearch.tags, {component: type});
}

//get tags by certain prop
Plex.studentSearch.getTags = function(prop, val){
	var searchObj = {};
	searchObj[prop] = val;
	return _.where(Plex.studentSearch.tags, searchObj);
}

//no duplicate tags
Plex.studentSearch.findTag = function(obj_array, obj_val, obj_type){
	var result = null;
	result = _.findWhere(obj_array, {val: obj_val, component: obj_type});
	return result === undefined ? false : true;
}

//filter tags by type and inject html into appropriate tag list
Plex.studentSearch.updateTagListView = function(elem, type){
	var tmp_arr = _.filter(Plex.studentSearch.tags, {component: type});
	$(elem).parent().find('.tag-list').html( Plex.studentSearch.buildFilterTags(tmp_arr) );	
}

//build tag html to display on the page
Plex.studentSearch.buildFilterTags = function(tag_list){
	var html_tags = '';

	$(tag_list).each(function(tag){
		html_tags += '<div class="left tag" data-tag-val="'+this.val+'" data-component="'+this.component+'" data-belongsto="'+this.belongs_to+'">';
		
		if(this.component === 'financial'){
			//text is different than val -- text contains '$'..
			//also, do not want the remkove function
			html_tags += this.text;
		}
		else{
			html_tags += this.val + '<span class="remove-tag"> &times; </span>';
		}
		html_tags += 	'<input type="hidden" name="'+this.component+'[]" value="'+this.val+'" />'
		html_tags += '</div>';
	});	

	return html_tags;
}

//add new tag to crumb list and rerender view with new crumbs
Plex.studentSearch.updateFilterCrumbView = function(section){
	var sorted_tags = _.sortBy(Plex.studentSearch.tags, 'belongs_to'),
		groupedBy_section = _.groupBy(Plex.studentSearch.tags, 'belongs_to');
	$('.filter-crumb-list').html( Plex.studentSearch.buildCrumbTags(groupedBy_section) );	
}

//build crumb view
Plex.studentSearch.buildCrumbTags = function(section_obj){
	var html_tags = '', min = '', max = '', tag = null;

	for(var section_prop in section_obj){
		if( section_obj.hasOwnProperty(section_prop) ){
			tag = section_obj[section_prop];
			html_tags += '<li>';
			html_tags += 	'<div class="clearfix">';
			html_tags += 		'<div class="left section">'+section_prop+':</div>';
			html_tags += 		Plex.studentSearch.prepCrumb(section_prop, tag);
			html_tags += 	'</div>';
			html_tags += '</li>';
		}
	}

	return html_tags;
}

Plex.studentSearch.prepCrumb = function(section, tags){
	var html = '', html_tags_arr = [], component = '', val = '', option_icon = null, last_index = -1,
		min_val = '', max_val = '', min_text = '', max_text = '', age_set = false, section_obj;

	_.each(tags, function(obj, index, arr){
		//if section obj return, make changed
		section_obj = Plex.studentSearch.sections.getSection(section);
		if( section_obj && !section_obj.changed ) section_obj.hasChanged();

		switch( section ){
			case 'location':
			case 'major':
				

				option_icon = Plex.studentSearch.getOptionVal(obj.option);
				val = obj.option.toLowerCase() === 'all' ? 'All' : obj.val;
				html += '<div class="left tag" data-tag-belongsto="'+obj.belongs_to+'" data-tag-val="'+obj.val+'"><span class="'+obj.option+'">'+option_icon+'</span>'+val+'<span class="remove">x</span></div>';
				break;
			case 'scores':
				//if new component, then reset variables and get value of min/max
				if( obj.component !== component ){
					component = obj.component;
					min_val = '';
					max_val = '';
					min_text = '';
					max_text = '';

					//formatting for min/max if min is without max or max is without min
					if( obj.text === 'min' ){
						min_val = obj.val ? obj.val : null;
						min_text = obj.val ? obj.val + ' +' : '';
					}else{
						max_val = obj.val ? obj.val : null;
						max_text = obj.val ?  '- ' + obj.val : '';
					}

					//format based on the min/max values that are defined
					if( min_val && max_val )
						val = min_val + ' - ' + max_val;
					else if( min_val )
						val = min_text;
					else
						val = max_text;

					html = '<div class="left tag" data-tag-belongsto="'+obj.belongs_to+'" data-tag-val="'+obj.val+'" data-tag-component="'+component+'" data-tag-text="'+obj.text+'"><span class=""></span>'+component+': '+val+'<span class="remove">x</span></div>';
					//add to html tag list
					html_tags_arr.push(html);
					//save last index of array b/c this is where we just save this newely created tag
					last_index = html_tags_arr.length - 1;
				}else{
					if( obj.text === 'min' ){
						min_val = obj.val ? obj.val : null;
						min_text = obj.val ? obj.val + ' +' : '';
					}else{
						max_val = obj.val ? obj.val : null;
						max_text = obj.val ? '- ' + obj.val : '';
					}

					if( min_val && max_val ){
						val = min_val + ' - ' + max_val;
					}
					else if( min_val ){
						val = min_text;
					}
					else{
						val = max_text;
					}

					//update the previous tag made
					html_tags_arr[last_index] = '<div class="left tag" data-tag-belongsto="'+obj.belongs_to+'" data-tag-val="'+obj.val+'" data-tag-component="'+component+'" data-tag-text="'+obj.text+'"><span class=""></span>'+component+': '+val+'<span class="remove">x</span></div>';
				}
				break;
			case 'demographic':
				component_capitalize = Plex.studentSearch.capitalize(obj.component);
				if( obj.component.toLowerCase() === 'age' ){
					if( obj.component !== component ){
						component = obj.component;
						min_val = '';
						max_val = '';
						min_text = '';
						max_text = '';

						//formatting for min/max if min is without max or max is without min
						if( obj.text === 'min' ){
							min_val = obj.val ? obj.val : null;
							min_text = obj.val ? obj.val + ' +' : '';
						}else{
							max_val = obj.val ? obj.val : null;
							max_text = obj.val ?  '- ' + obj.val : '';
						}

						//format based on the min/max values that are defined
						if( min_val && max_val )
							val = min_val + ' - ' + max_val;
						else if( min_val )
							val = min_text;
						else
							val = max_text;

						html = '<div class="left tag" data-tag-belongsto="'+obj.belongs_to+'" data-tag-val="'+obj.val+'" data-tag-component="'+component+'" data-tag-text="'+obj.text+'"><span class=""></span>'+component+': '+val+'<span class="remove">x</span></div>';
						//add to html tag list
						html_tags_arr.push(html);
						// save last index of array b/c this is where we just save this newely created tag
						last_index = html_tags_arr.length - 1;
					}else{
						if( obj.text === 'min' ){
							min_val = obj.val ? obj.val : null;
							min_text = obj.val ? obj.val + ' +' : '';
						}else{
							max_val = obj.val ? obj.val : null;
							max_text = obj.val ? '- ' + obj.val : '';
						}

						if( min_val && max_val ){
							val = min_val + ' - ' + max_val;
						}
						else if( min_val ){
							val = min_text;
						}
						else{
							val = max_text;
						}

						//update the previous tag made
						html_tags_arr[last_index] = '<div class="left tag" data-tag-belongsto="'+obj.belongs_to+'" data-tag-val="'+obj.val+'" data-tag-component="'+component+'" data-tag-text="'+obj.text+'"><span class=""></span>'+component+': '+val+'<span class="remove">x</span></div>';
					}
				}else if( obj.component === 'religion' || obj.component === 'ethnic' ){
					option_icon = Plex.studentSearch.getOptionVal(obj.option);
					val = obj.option.toLowerCase() === 'all' ? 'All' : obj.val;
					html = '<div class="left tag" data-tag-belongsto="'+obj.belongs_to+'" data-tag-val="'+obj.val+'" data-tag-component="'+obj.component+'"><span class="'+obj.option+'">'+option_icon+'</span>'+val+'<span class="remove">x</span></div>';
					html_tags_arr.push(html);
				}else{
					//gender
					html = '<div class="left tag" data-tag-belongsto="'+obj.belongs_to+'" data-tag-val="'+obj.val+'" data-tag-component="'+obj.component+'"><span class=""></span>'+obj.val+'<span class="remove">x</span></div>';
					html_tags_arr.push(html);
				}
				break;
			case 'uploads':
			case 'educationLevel':
			case 'desiredDegree':
			case 'startDateTerm':
				html += '<div class="left tag" data-tag-val="'+obj.val+'" data-tag-belongsto="'+obj.belongs_to+'"><span class=""></span>'+obj.val+'<span class="remove">x</span></div>';
				break;
			case 'militaryAffiliation':
			case 'financial':
				html += '<div class="left tag" data-tag-val="'+obj.val+'" data-tag-component="'+obj.component+'" data-tag-belongsto="'+obj.belongs_to+'"><span class=""></span>'+obj.text+'</div>';
				break;
			case 'schooltype':
				html += '<div class="left tag" data-tag-val="'+obj.val+'" data-tag-component="'+obj.component+'" data-tag-belongsto="'+obj.belongs_to+'"><span class=""></span>'+obj.text+'<span class="remove">x</span></div>';
				break;
			case 'profileCompletion':
				html += '<div class="left tag" data-tag-val="'+obj.val+'" data-tag-component="'+obj.component+'" data-tag-belongsto="'+obj.belongs_to+'"><span class=""></span>'+obj.text+'<span class="remove">x</span></div>';
			default:
				break;
		}

		if( section === 'scores' || section === 'demographic' ){
			html = html_tags_arr.join('');
		}
	});

	return html;
}

Plex.studentSearch.capitalize = function(str){
	var str_arr = str.split(''), copy = [];

	for(var i = 0; i < str_arr.length; i++){
		if( i === 0 )
			copy.push( str_arr[i].toUpperCase() );
		else
			copy.push( str_arr[i] );
	}
	
	return copy.join('');
}

Plex.studentSearch.getOptionVal = function(option){
	if( option === 'include' ) return '+';
	else if( option === 'exclude' ) return '-';
	return '';
}

Plex.studentSearch.alreadyHaveTag = function(comp, txt){
	return _.where(Plex.studentSearch.tags, {component: comp, text: txt});
}
// -- tags


// -- text input field event trigger
$(document).on('blur', 'input[type="number"][data-type="text"].form-field', function(){
	var _this = $(this), component = _this.closest('.row').find('.s-label').text().split(''), 
		component = _.without(component, component.pop()).join(''),
		belongsTo = _this.closest('li.search-tab').data('search-tab'),
		text = _this.attr('placeholder').toLowerCase(), option = _this.attr('pattern'),
		section = _this.closest('li.search-tab').data('search-tab'), already_have_tag = null,
		error_msg = _this.parent().find('.error'), section_obj = null, validator = false;

	//check if score is valid
	if( !error_msg.is(':visible') ){
		//if input has a value and no error msg visible, then save tag
		if( _this.val() ){
			//check if tag is already created for this component
			already_have_tag = Plex.studentSearch.alreadyHaveTag(component, text);
			if( already_have_tag.length === 0 ){
				Plex.studentSearch.saveTag(section, _this.val(), component, text, option);
			}else{
				Plex.studentSearch.updateTag(already_have_tag, 'val', _this.val());
			}
		}else{
			already_have_tag = Plex.studentSearch.alreadyHaveTag(component, text);
			if( already_have_tag.length > 0 ){
				Plex.studentSearch.removeTag(already_have_tag[0].val);
			}
		}
	}

	$('.component.scores').find("input").each( function(){ 
		if($(this).val() != "") 
			validator = true;
	});
	if(!validator) {
		section_obj = Plex.studentSearch.sections.getSection(belongsTo);
		if( section_obj ) section_obj.reset();	
	}

	Plex.studentSearch.updateFilterCrumbView();
});

$(document).on('change', 'input[data-type="checkbox"]', function(){
	var _this = $(this), section = Plex.studentSearch.getSectionName(_this), component = section,
		val = _this.val(), option = _this.is(':checked') ? 'include' : 'exclude', text = _this.parent().find('label').text();

	//if checked, add tag, else remove it
	if( _this.is(':checked') ){
		Plex.studentSearch.saveTag(section, val, component, text, option);
	}else{
		Plex.studentSearch.removeCrumb(section, component, val);
	}

	Plex.studentSearch.updateFilterCrumbView();
});

Plex.studentSearch.getSectionName = function(elem){
	return elem.closest('li.search-tab').data('search-tab');
}
// -- text input field event trigger


// -- component toggle
$(document).on('change', 'select[data-type="select"]:not(.savedFilters-class)', function(){
	var _this = $(this),
		val_text = $('option[value="'+_this.val()+'"]', _this).text(),
		belongsTo = _this.closest('li.search-tab').data('search-tab'),
		component_name = _this.attr('name'), duplicateFound = false, demo_tag = null,
		option = _this.closest('.component').find('input[type="radio"]:checked').val(),
		val = '', prop = '', state = '', section_obj = null;

	if( component_name === 'city' && !Plex.studentSearch.cityInitDone ){
		Plex.studentSearch.populateCityBasedOnState($('#s_states').val(), true);
	}else if( component_name === 'major' && !Plex.studentSearch.majorInitDone ){
		Plex.studentSearch.populateMajorBasedOnDepartment($('#s_depts').val(), true);
	}

	if( Plex.studentSearch.activeTab === 'location' ||  
		Plex.studentSearch.activeTab === 'major')
			Plex.studentSearch.toggleComponents(_this);

	if( component_name === 'gender' ){
		val = _this.val();
		prop = 'val';
	}else{
		val = val_text;
		prop = 'text';
	}

	//if value is not empty or equal to Select..., then proceed to check if this tag already exists, if not, save it
	if( _this.val() && val_text !== 'Select...' ){
		if( (Plex.studentSearch.activeTab === 'demographic' && component_name === 'gender') || 
			(Plex.studentSearch.activeTab === 'militaryAffiliation' && component_name === 'inMilitary') ||
			(Plex.studentSearch.activeTab === 'profileCompletion') ){

			//if user selects 'no' military, tags for affiliations should be removed
			if(Plex.studentSearch.activeTab === 'militaryAffiliation' && component_name === 'inMilitary' &&
				val ==='No'){

				//if there are tags exisiting in component militaryAffiliation, we want to remove them
				Plex.studentSearch.removeTagByComponent('militaryAffiliation');

				//also set select option for militarayAffiliation back to 'Select...'
				$('#s_militaryAffiliation').val('');
			}


			demo_tag = Plex.studentSearch.getTags('component', component_name);

			if( val === 'all' ){
				Plex.studentSearch.removeTagByComponent(component_name);
			}else{
				if( demo_tag.length > 0 ) Plex.studentSearch.updateTag(demo_tag, prop, val);
				else Plex.studentSearch.saveTag( belongsTo, _this.val(), _this.attr('name'), val_text, option );
			}


		}
		else if(Plex.studentSearch.activeTab === 'financial'){

			//want to add a list versus appending or replacing one tag
			//clear existing list with component
			Plex.studentSearch.removeTagByComponent(component_name);

			for(var i = $.inArray(val.trim(), Plex.studentSearch.ranges_formatted); i < Plex.studentSearch.ranges_formatted.length; i++){
							
				Plex.studentSearch.saveTag( belongsTo, Plex.studentSearch.ranges[i], _this.attr('name'), Plex.studentSearch.ranges_formatted[i], option );	
			}

			Plex.studentSearch.updateTagListView(_this, component_name);
			Plex.studentSearch.updateFilterCrumbView();
			

			//want the selected option to update the selected option
			val = val.replace(/[$]/g, '');	//because value is different that val

			if(val === '0'){
				val = val + ".00"; 
			}
			if(val === '50,000+'){
				val = val.replace(/[+]/, '');
			}
			_this.val(val);

		}
		else{
			duplicateFound = Plex.studentSearch.findTag(Plex.studentSearch.tags, _this.val(), component_name);
			if( !duplicateFound && val !== 'all' && _this.val() ){
				Plex.studentSearch.saveTag( belongsTo, _this.val(), _this.attr('name'), val_text, option );
				Plex.studentSearch.updateTagListView(_this, component_name);
				Plex.studentSearch.updateFilterCrumbView();

				if( Plex.studentSearch.activeTab === 'startDateTerm' ) _this.val('Select...');
				
			}
		}
	}else{

		if( Plex.studentSearch.activeTab === 'demographic' || 
			Plex.studentSearch.activeTab === 'militaryAffiliation' || 
			Plex.studentSearch.activeTab === 'profileCompletion' ||
			Plex.studentSearch.activeTab === 'financial'){
				Plex.studentSearch.removeTagByComponent(component_name);
				Plex.studentSearch.updateTagListView(_this, component_name);
		}
	}

	Plex.studentSearch.updateFilterCrumbView();
});

//remove crumb
$(document).on('click', '.filter-crumb-list .remove', function(){
	var _this = $(this).parent(), section = _this.data('tag-belongsto');
	Plex.studentSearch.removeCrumbBasedOnsection(section, _this);
});

Plex.studentSearch.removeCrumbBasedOnsection = function(section, crumb){
	var corresponding_elem = null, val = crumb.data('tag-val'), component = crumb.data('tag-component'), text = crumb.data('tag-text'),
		belongs_to = crumb.data('tag-belongsto');

	switch( section ){
		case 'location':
		case 'major':
			corresponding_elem = $('.tag-list .tag[data-tag-val="'+val+'"]').find('.remove-tag');
			corresponding_elem.trigger('click');
			if($('.search-filter-form .department').find("#department_all").is(":checked")){
				section_obj = Plex.studentSearch.sections.getSection(belongs_to);
				if( section_obj ) section_obj.reset();
			}
			Plex.studentSearch.updateFilterCrumbView();
			break;
		case 'scores':
			text = Plex.studentSearch.capitalize(text);

			if( text === 'Min' ){
				corresponding_elem = $('li[data-search-tab="scores"]').find('.s-label:contains('+component+')').parent().find('input[placeholder="'+text+'"]');
			}else{
				corresponding_elem = $('li[data-search-tab="scores"]').find('.s-label:contains('+component+')').parent().find('input');
			}
			corresponding_elem.val('').trigger('blur');
			break;
		case 'demographic':
			if( component === 'religion' || component === 'ethnic' ){
				corresponding_elem = $('.tag-list .tag[data-tag-val="'+val+'"]').find('.remove-tag');
				corresponding_elem.trigger('click');
			}else if( component === 'Age' ){
				text = Plex.studentSearch.capitalize(text);

				if( text === 'Min' ){
					corresponding_elem = $('li[data-search-tab="demographic"]').find('.s-label:contains('+component+')').parent().find('input[placeholder="'+text+'"]');
				}else{
					corresponding_elem = $('li[data-search-tab="demographic"]').find('.s-label:contains('+component+')').parent().find('input');
				}

				corresponding_elem.val('').trigger('blur');
			}else{
				//remove gender tag and reset to all and update crumb view
				$('#s_gender').val('all');
				Plex.studentSearch.removeTag(val);
				Plex.studentSearch.updateFilterCrumbView();
			}
			if( $('.component.age').find("input").first().val() == "" && $('.component.age').find("input").last().val() == "" 
				&& $('.component.gender').find("#s_gender").val() == "all" 
				&& $('.component.religion').find("#religion_all").is(":checked")
				&& $('.component.ethnic').find("#ethnic_all").is(':checked') ) {
				section_obj = Plex.studentSearch.sections.getSection(belongs_to);
				if( section_obj ) section_obj.reset();
			}
				
			break;
		case 'uploads':
		case 'educationLevel':
		case 'desiredDegree':
			corresponding_elem = $('li[data-search-tab="'+belongs_to+'"]').find('input[value="'+val+'"]');
			corresponding_elem.prop('checked', false).trigger('change');
			if($('.component.uploads').find("input:checked").length == 0
				|| $('.component.educationLevel').find("input:checked").length == 0 
				|| $('.component.desiredDegree').find("input:checked").length == 0 ) {
				section_obj = Plex.studentSearch.sections.getSection(belongs_to);
				if( section_obj ) section_obj.reset();
			}
			Plex.studentSearch.updateFilterCrumbView();
			break;
		case 'militaryAffiliation':
			if( component === 'inMilitary' ){
				$('#s_inMilitary').val('').trigger('change');
				$('#s_militaryAffiliation').val('').trigger('change');
				// Plex.studentSearch.removeTagByComponent(component);
			}else{
				$('#s_militaryAffiliation').val('').trigger('change');
				// Plex.studentSearch.removeTag(val);
			}
			section_obj = Plex.studentSearch.sections.getSection(belongs_to);
			if( section_obj ) section_obj.reset();
			Plex.studentSearch.updateFilterCrumbView();
			break;
		case 'profileCompletion':
			if( component === 'profileCompletion' ){
				$('#profile_percent').val('').trigger('change');
			}
			section_obj = Plex.studentSearch.sections.getSection(belongs_to);
			if( section_obj ) section_obj.reset();
			Plex.studentSearch.updateFilterCrumbView();
			break;
		case 'startDateTerm':
		case 'financial':
			section_obj = Plex.studentSearch.sections.getSection(belongs_to);
			Plex.studentSearch.removeTag(val);
			$('li[data-search-tab="'+belongs_to+'"]').find('.tag[data-tag-val="'+val+'"]').remove();
			if( section_obj ) section_obj.reset();
			Plex.studentSearch.updateFilterCrumbView();
			break;
		case 'schooltype':
			$('input#both_typeofschool').prop('checked', true);
			Plex.studentSearch.removeTag(val);
			Plex.studentSearch.updateFilterCrumbView();
			break;
	}
}

//remove tag
$(document).on('click', '.tag-list .remove-tag', function(){
	var _this = $(this).parent(),
		val = _this.data('tag-val'),
		select_elem = _this.closest('.select-container').find('select');

	//remove tag object from array and update view
	_this.remove();
	Plex.studentSearch.removeTag(val);

	//check if any components need to be toggled depending on tag removed
	Plex.studentSearch.onTagRemove(_this, select_elem);	
});

//toggle components if last tag removed or if US tag removed
Plex.studentSearch.onTagRemove = function(tag, container){
	var val = tag.data('tag-val'),
		current_component_tags = null,
		component_name = tag.data('component');

	//get the current components tags
	current_component_tags = Plex.studentSearch.getTagsByComponent(component_name);

	//if there are no more tags for this component, trigger click, otherwise do nothing
	if( _.isEmpty(current_component_tags) ){
		$('.component.'+component_name+' input[value="all"]').trigger('click');
	}else if( val === 'United States' ){
		container.val('');
		Plex.studentSearch.toggleComponents( container );
	}

	Plex.studentSearch.updateFilterCrumbView();
}

//toggle components based on select field value
Plex.studentSearch.toggleComponents = function(elem){
	var dependency = elem.closest('.component').data('dependency');
	var search_form = elem.closest('.search-filter-form');
	var name = elem.attr('name');

	if( typeof dependency !== 'undefined' ){
		switch( name ){
			case 'country':
				if( elem.val() === 'United States' || Plex.studentSearch.findTag(Plex.studentSearch.tags, 'United States', 'country') )
					search_form.find('.component.'+dependency).removeClass('hidden');
				else
					search_form.find('.component:not(.'+name+')').addClass('hidden');
				break;
			default:
				if( elem.val() !== '' )
					search_form.find('.component.'+dependency).removeClass('hidden');
				else
					search_form.find('.component.'+dependency).addClass('hidden');
				break;
		}	
	}
}
// -- component toggle



// -- all/include/exclude toggle
$(document).on('change', '.search-sidenav input[type="radio"]', function(){
	var component_name = null, tmp, _this = $(this);

	if( Plex.studentSearch.activeTab === 'location' ||  Plex.studentSearch.activeTab === 'major' ||  Plex.studentSearch.activeTab === 'demographic'){
		Plex.studentSearch.toggleSelectContainer( _this );	

		//get component name, then find tags by component name
		component_name = _this.attr('name').split('_')[0];
		tagsFound = Plex.studentSearch.getTags('component', component_name);

		//if any found, update them with new radio val
		if( tagsFound.length > 0 ){

			//update crumb tags data, then update crumb tag view 
			Plex.studentSearch.updateTag(tagsFound, 'option', $(this).val());
			Plex.studentSearch.updateFilterCrumbView();
		}
		
	}else if( Plex.studentSearch.activeTab === 'schooltype' ){
		//get component name, then find tags by component name
		component_name = _this.attr('name');
		tagsFound = Plex.studentSearch.getTags('belongs_to', component_name);

		//if any found, update them with new radio val
		if( tagsFound.length > 0 ){
			//update crumb tags data, then update crumb tag view 
			Plex.studentSearch.updateTag(tagsFound, 'text', _this.next().text());
			Plex.studentSearch.updateFilterCrumbView();
		}else{
			var belongs = _this.attr('name'), val = _this.val(), type = null, text = _this.next().text(), option = null;
			Plex.studentSearch.saveTag(belongs, val, type, text, option);
			Plex.studentSearch.updateFilterCrumbView();
		}
	}
});

//toggle select container and related component
Plex.studentSearch.toggleSelectContainer = function(elem){
	var container = elem.closest('.component').find('.select-container');
	var select_field = container.find('select');

	//hide or show containers
	if( elem.val() === 'all' ){
		Plex.studentSearch.resetComponent(elem);
		container.addClass('hidden');
		Plex.studentSearch.toggleComponents(select_field);
	}else{
		container.removeClass('hidden');
		container.find('.xclude-toggle').html(elem.val());
	}
};

//reset component back to default
Plex.studentSearch.resetComponent = function(elem){
	var comp = elem.attr('name');
	var component_name = comp.slice(0, comp.indexOf('_'));

	//remove tag html and empty select field
	if( component_name === 'country' || component_name === 'department' ){
		elem.closest('.search-filter-form').find('input[type="radio"][value="all"]').trigger('click').closest('.search-filter-form').clearSelectFieldAndTagList();
		component_name = elem.closest('li.search-tab').data('search-tab');

		//remove all tags by section from tags array
		Plex.studentSearch.removeTagBySection(component_name);
	}else{
		elem.closest('.component').clearSelectFieldAndTagList();

		//remove all tags by component
		Plex.studentSearch.removeTagByComponent(component_name);
	}

	Plex.studentSearch.updateFilterCrumbView();
};

$.fn.clearSelectFieldAndTagList = function(){
	this.find('select').val('').parent().find('.tag').remove();
	return this;
};
// -- all/include/exclude toggle



// Not needed - available in inquiries.js
// -- search tab functions
// $(document).on('click', '.search-tab a', function(e){
// 	e.preventDefault();
// 	var clicked_tab = $(this).parent().data('search-tab'), section = {};

// 	section = Plex.studentSearch.sections.getSection(clicked_tab);
// 	if( section && section.locked ) return false;

// 	$('li.search-tab a').removeClass('active');

// 	//if clicked tab is already active just close it, otherwise open it
// 	if( clicked_tab === Plex.studentSearch.activeTab ){
// 		Plex.studentSearch.activeTab = null;
// 		Plex.studentSearch.closeAllSearchFilterBoxes();
// 	}else{
// 		Plex.studentSearch.activeTab = clicked_tab;
// 		Plex.studentSearch.openSearchFilter();
// 		$(this).addClass('active');
// 	}
// });

// Plex.studentSearch.closeAllSearchFilterBoxes = function(){
// 	$('.search-filter-form').removeClass('open').slideUp(10);
// }

// Plex.studentSearch.openSearchFilter = function(){
// 	Plex.studentSearch.closeAllSearchFilterBoxes();
// 	$('li[data-search-tab="'+Plex.studentSearch.activeTab+'"]').find('.search-filter-form').addClass('open').slideDown(500);
// }
// -- search tab functions


// -- student results checkbox functions
$(document).on('change', 'input[type="checkbox"].student-result', function(){
	$(this).toggleSelectedStudent();
});

//tiggered when select all checkbox change
$(document).on('change', '.select-all-students-chbox', function(){
	$(this).toggleAllStudentResults();
});

//Mixpanel view profile on advanced search 
$(document).on('change', '.resulting-students', function(){
	mixpanel.track("View_Profile",
		{
			"location": "Advanced Search"
		}
	);
});

//jquery plugin to toggle selected student row
$.fn.toggleSelectedStudent = function(){
	if( this.is(':checked') ){
		this.closest('.resulting-students').addClass('selected');
	}else{
		this.closest('.resulting-students').removeClass('selected');
	}

	return this;
};

//jquery plugin to for select all students toggle
$.fn.toggleAllStudentResults = function(){
	if( this.is(':checked') ){
		$('input[type="checkbox"].student-result').prop('checked', true).closest('.resulting-students').addClass('selected');
	}else{
		$('input[type="checkbox"].student-result').prop('checked', false).closest('.resulting-students').removeClass('selected');
	}

	return this;
};
// -- student results checkbox functions


// -- populate select fields based on dependencies

//when state select field has changed values
$(document).on('change', '#s_states', function(){
    var _this = $(this);

    if(_this.val() != ''){
       Plex.studentSearch.populateCityBasedOnState(_this.val());
    }
});

//when department select field has changed values
$(document).on('change', '#s_depts', function(){
    var _this = $(this);
    
    if(_this.val() != ''){
       Plex.studentSearch.populateMajorBasedOnDepartment(_this.val());
    }
});

//get cities based on selected state
Plex.studentSearch.populateCityBasedOnState = function(stateAbbr, fromInit){
    $.getJSON("/ajax/homepage/getCityByState/"+stateAbbr, function(result) {
        var options = $("#s_cities");
    	Plex.studentSearch.populate(options, result);
    	if( fromInit ){
    		Plex.studentSearch.initCity();
    	}
    });
}

//get majors based on selected department
Plex.studentSearch.populateMajorBasedOnDepartment = function(departmentAbbr, fromInit){
    $.getJSON("/ajax/getMajorByDepartment/"+departmentAbbr.replace(/\//g, '&'), function(result) {
        var options = $("#s_majors");
    	Plex.studentSearch.populate(options, result);

    	if( fromInit ){
    		Plex.studentSearch.initMajor();
    	}
    });
}

Plex.studentSearch.populate = function(target, data){
	target.find('option').remove();  
	$.each(data, function(key, value) {
	    target.append($("<option />").val(value).text(value));
	});
}
// -- populate select fields based on dependencies



// -- student full profile container toggle
$(document).on('click', '.toggle-profile-btn, .resulting-students .name', function(){
	Plex.studentSearch.toggleFullProfileContainer($(this));
});

(function($) {
    $.fn.getAttributes = function() {
        var attributes = {}; 

        if( this.length ) {
            $.each( this[0].attributes, function( index, attr ) {
                attributes[ attr.name ] = attr.value;
            } ); 
        }
        return attributes;
    };
})(jQuery);




Plex.studentSearch.toggleFullProfileContainer = function(button){
// only toggle full profile container when selected	
	var inqRow = button.parents('.inquirie_row');
	var recruit = button.siblings('.recruit');
	var attributes = recruit.getAttributes();
	var hashed_user_id = recruit.attr('data-hashed-id');
	var full_profile_container = button.siblings('.full-profile-container');
	var full_profile_attributes = full_profile_container.getAttributes();

	Plex.contact.currentThread = -1;

	if(full_profile_container.hasClass('ajax-init') && Plex.studentSearch.ajaxFlagProfile === 0){
    	$('.manage-students-ajax-loader').show();
		//set ajax flag to 'on' -- we are running ajax call and do not want to repeat
		Plex.studentSearch.ajaxFlagProfile = 1;

		//the container does not use ajax before
		$.ajax({
			url: '/admin/studentsearch/loadProfileData',
			type: 'POST',
			data: {'hashed_user_id': hashed_user_id},
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		})
		.done(function(data) {


			full_profile_container.removeClass('ajax-init');
			full_profile_container.append(data);

			//ajax finished -- set flag back to 'off'
			Plex.studentSearch.ajaxFlagProfile = 0;
    		$('.manage-students-ajax-loader').hide();

    		// init owl here
    		// var save_btn = $('.save-edit-btn');
    		// console.log(button);
    		
    		requiredAfterAjax(button, inqRow);
    		
		}); 
	}


	if(full_profile_container.hasClass('hidden')){
		
		full_profile_container.removeClass('hidden');
		full_profile_container.slideDown(250, 'easeInOutExpo');

		// //hide all other profiles
	    $('.inquirie_row.viewing').each(function(){
	        $(this).removeClass('viewing');
	        $(this).find('.full-profile-container').slideUp( 250, 'easeInOutExpo' );
	        $(this).find('.full-profile-container').addClass('hidden');
	        $(this).find('.active').removeClass('active');  
	        $(this).find('.arrow').toggleClass('opened');      
	    });

	    inqRow.addClass('viewing');
	}
	else{
		full_profile_container.addClass('hidden');
		inqRow.removeClass('viewing');
	}
	
	$(inqRow).find('.arrow').toggleClass('opened');

	
}

// Plex.studentSearch.toggleFullProfileContainer = function(button){
// 	var prof_container = button.closest('.resulting-students').find('.full-profile-container');

// 	//toggle arrow
// 	button.closest('.resulting-students').find('.arrow').rotateArrow();

// 	//toggle container
// 	if(prof_container.hasClass('hidden')){
// 		prof_container.slideDown(250).removeClass('hidden');
// 		button.parent().addClass('selected');
// 	}else{
// 		prof_container.slideUp(250).addClass('hidden');
// 		button.parent().removeClass('selected');
// 	}

// }
// -- student full profile container toggle



// -- load more results
$(document).on('click', '.load-more-results-btn', function(e){
	e.preventDefault();
	var loadmore_option = $('#displayOption option:selected').val();
	Plex.studentSearch.loadMore(loadmore_option);
});

$(document).on('change', '#displayOption', function(e){
	e.preventDefault();
	var display_option = $('#displayOption option:selected').val();
	Plex.studentSearch.loadMore(display_option);
});

//load more search results
Plex.studentSearch.loadMore = function(loadmore_option){
	loadmore_option = typeof loadmore_option === 'undefined'? 15 : loadmore_option;

	var hasResults = '';

	//start ajax loader
	Plex.studentSearch.loadingStart();
	var total_results = $('.total_results_count');
	var total_viewing = $('.total_viewing_count');
	var total_results_count = parseInt(total_results.first().text());
	var total_viewing_count = parseInt(total_viewing.first().text()) + parseInt(loadmore_option);

	if(total_viewing_count > total_results_count ){
		total_viewing_count = total_results_count;
	}

	$.ajax({
        url: '/admin/studentsearch/loadmore',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: {'total_viewing_count': total_viewing_count, 'loadmore_option' : loadmore_option},
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(results){
		//append more results to existing results
		$('.list-of-results-container').append(results);

		//hide ajax loader
		Plex.studentSearch.loadingEnd();

		//get last student result, check data for false, if false hide 'load more' button
		hasResults = $('.hasResults').last().data('last-results');
		Plex.studentSearch.hasResultsCheck(hasResults);
		total_viewing.html( $('.resulting-students').length );
	});

}

// -- recruit student
$(document).on('click', '.recruit-me', function(e){
	e.preventDefault();
	
	Plex.studentSearch.recruitStudent($(this));

	mixpanel.track("Recruit_Yes",
		{
			"location": document.body.id
		}
	);
	
});

Plex.studentSearch.recruitStudent = function(elem){

	var userid = elem.parent().data('hashed-id');

	$.ajax({
		type: 'POST',
		url: '/admin/studentsearch/setRecruit',
		data: {userid:userid},
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	}).done(function(data){
		if (data == 'success') {
			elem.removeClass('recruit-me');
			elem.addClass('already-recruited');
			elem.html('Recruited!');
			topAlert(Plex.studentSearch.addedFromAdvancedSearchToPending);
		}else if(data == 'limit reached'){
			Plex.studentSearch.openModal( $('#recruit_more_modal') );
		}else if(data == 'failed'){
			topAlert(Plex.studentSearch.failedFromAdvancedSearch);
		}
	});

}

// Required by inquirie pages, now available in inquiries.js
// Plex.studentSearch.addStudentManual = function(type, user_id, college_id, aor_id, org_portal_id, aor_portal_id, school_name, callback){
// 	var at = Plex.studentSearch.addStudentManuallyMsg;
// 	var org = $.extend({}, Plex.studentSearch.addStudentManuallyMsg);

// 	//alert.msg = alert.msg.text.replace('{{type}}', type);
// 	var msg = at.msg;
// 	msg = msg.replace(/{{type}}/g, type);
// 	msg = msg.replace(/{{school_name}}/g, school_name);
// 	at.msg = msg;
	
// 	//console.log(aor_id, org_portal_id, aor_portal_id);
// 	$.ajax({
// 		type: 'POST',
// 		url: '/admin/studentsearch/addStudentManual',
// 		data: {type:type, user_id:user_id, college_id:college_id, school_name:school_name,
// 			   aor_id:aor_id, org_portal_id:org_portal_id, aor_portal_id:aor_portal_id},
// 	})
// 	.done(function() {
// 		topAlert(at);
// 		console.log("success");
// 		// callback();
// 	});
// 	Plex.studentSearch.addStudentManuallyMsg = org;
// }
// -- get recruited


// -- mobile search nav toggler
$(document).on('click', '.mobile-search-filter-nav-toggler', function(){
	var nav_container = $(this).parent().find('.mobile-search-filter-nav-container');
	$(this).find('.arrow').rotateArrow();
	Plex.studentSearch.toggleMobileSearchNav(nav_container);
});

//only if the mobile search filter nav is not visible, then on resize, if the nav container is closed then open it
$(window).resize(function(){
	if( !$('.mobile-search-filter-nav-toggler').is(':visible') ){
		var nav_container = $('.mobile-search-filter-nav-container');

		if( !nav_container.hasClass('isOpen') ){
			nav_container.addClass('isOpen').show();
		}
	}
});

//arrow toggler jquery plugin
$.fn.rotateArrow = function(){
	if( this.hasClass('rotate') )
		this.removeClass('rotate');
	else
		this.addClass('rotate');		

	return this;
}

Plex.studentSearch.toggleMobileSearchNav = function(container){
	if( container.hasClass('isOpen') ){
		//close 
		container.removeClass('isOpen').slideUp(250);
	}else{
		//open
		container.addClass('isOpen').slideDown(250);
	}
}
// -- mobile search nav toggler



// -- preview docs modal
$(document).on('click', '.view-doc', function(e){
	e.preventDefault();
	var doc_src = $(this).data('doc-src');
	var doc_alt = $(this).data('doc-type');

	if( doc_src !== '' ){
		//build and inject image onto modal
		Plex.studentSearch.buildPreviewImg(doc_src, doc_alt);
	}else{
		//src is empty so throw error because there's no image to show
		Plex.studentSearch.buildErrorMsg();
	}
	
	//open modal
	Plex.studentSearch.openModal( $('#preview_docs_modal') );
});

Plex.studentSearch.buildErrorMsg = function(){
	var error = '<div class="text-center">Image of document is unavailable at this time</div>';
	$('#preview_docs_modal .preview-doc-wrapper').html(error);
}

Plex.studentSearch.buildPreviewImg = function(src, alt){
	var img = '<img src="'+ src +'" alt="'+ alt +'" />';
	$('#preview_docs_modal .preview-doc-wrapper').html(img);
}

Plex.studentSearch.openModal = function(modalElem){
	modalElem.foundation('reveal', 'open');
}

Plex.studentSearch.closeModal = function(modalElem){
	modalElem.foundation('reveal', 'close');
}
// -- preview docs modal



// -- upgrade acct modal
$(document).on('click', '.upgrade-or-no-row .upgrade', function(){
	// console.log(this);
});


Plex.studentSearch.requestToBecomeMember = function(){

	$.ajax({
		url: '/admin/ajax/requestToBecomeMember',
		type: 'POST',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	})
	.done(function(){
		
	});
}

//In military if it is yes, show military affiliation else hide military affiliation dropdown
$(document).on('change', '#s_inMilitary', function(e){
    e.preventDefault();
    $(this).toggleMilitaryAffiliation();
});

$.fn.toggleMilitaryAffiliation = function(){
	var val = this.val(),
		militaryAffiliation = $('.militaryAffiliation');

	if( parseInt(val) ){
		militaryAffiliation.fadeIn(500);
	}else{
		militaryAffiliation.fadeOut(500);
        if( val === '' ) $('#s_inMilitary').val('');
	}

	return this;
}

// Not needed - Same function available in inquiries.js

// $(document).on('click', '.more-option', function(e){
// 	var moreOptions = $('.more-option');
// 	var moreTab = $('.moreTab');
// 	if (moreOptions.text() == '+ More Filter Options') {
// 		moreTab.removeClass('hide');
// 		moreOptions.text('- Less Filter Options');
// 	}else{
// 		moreTab.addClass('hide');
// 		moreOptions.text('+ More Filter Options');
// 	}
// });

Plex.studentSearch.deleteFilterModal = function(){
	var selected_filter =  $('.savedFilters-class');
	var val = selected_filter.val();

	if (val != '') {
		var txt = $('.savedFilters-class option[value="'+val+'"]').text();
		$('#delete-filter-template-modal .filterName').html(txt);
		$(".btnrow .delete-filter-template-btn").attr('data-fval', val);
		$(".btnrow .delete-filter-template-btn").attr('data-ftxt', txt);
		$('#delete-filter-template-modal').foundation('reveal', 'open');
	}
}

$(document).on('click', '.delete-filter', function(event) {
	event.preventDefault();
	/* Act on the event */
	Plex.studentSearch.deleteFilterModal();
	
});

Plex.studentSearch.deleteFilter = function(){
	var btn = $(".btnrow .delete-filter-template-btn");
	var val = btn.data('fval');
	var txt = btn.data('ftxt');

	$.ajax({
        url: '/admin/studentsearch/deleteFilter',
        data: {save_template_name_val: txt},
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(data, textStatus, xhr) {
		$(".savedFilters-class option[value='"+val+"']").remove();
		$('#delete-filter-template-modal').foundation('reveal', 'close');
	});
}

//start interval when textarea has focus
$(document).on('focus', '.notes-textarea', function(){
	var these_notes = this;
	var student_id = $(this).data('studentid');

	//loop - every 10 seconds, while textarea is in focus, save the note
	Plex.studentSearch.saveNotesInterval = setInterval(function(){
		Plex.studentSearch.autosaveNotes( student_id, these_notes );
	}, 10000);
});

//stop interval when textarea is out of focus
$(document).on('blur', '.notes-textarea', function() {
	var _this = this;
	var this_student_id = $(this).data('studentid');

	//once out of focus, save note
	clearInterval(Plex.studentSearch.saveNotesInterval);
	Plex.studentSearch.autosaveNotes( this_student_id, this );
});

//autosave notes
Plex.studentSearch.autosaveNotes = function( id, notes ){
	var note_data = $(notes).val();
	var last_saved = $(notes).parent().find('.last-saved-note-time');

	$('.save-note-ajax-loader').show();

	//post note and update 'last saved' time
	$.ajax({
		url: '/admin/studentsearch/setNote',
		type: 'POST',
		data: {user_id: id, note: note_data},
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	})
	.done(function(time){
        $('.save-note-ajax-loader').hide();
        if(time === '*Input is empty.') {
            $(last_saved).text(time);
        } else {
            $(last_saved).text('Last Saved: ' + time);
        }
	});
}