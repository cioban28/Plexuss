// adminAdvFilter.js
Plex.adminAdvFilter = {
	currentTab: '',
	prevTab: '',
	currentTab_text: '',
	filter_section_route: '/scholarshipadmin/filter/',
	save_filter_route: '/scholarshipadmin/ajax/setRecommendationFilter/',
	reset_filter_route: '/scholarshipadmin/ajax/resetRecommendationFilter/',
	getRecommendationPercent_route: '/scholarshipadmin/ajax/getNumberOfUsersForFilter',
	save_reset_row_visible: false,
	filter_tags_array: [],
	filter_section_state: [],
	change_has_been_made: false,
	currentFormFields: null,
	sections: [],
	id_start: 0,
	isInit: false,
	defaultState: null,
	majorsInitData: [],
	ranges: ['0.00','0 - 5,000','5,000 - 10,000', '10,000 - 20,000', '20,000 - 30,000', '30,000 - 50,000', '50,000'],
	ranges_formatted:  ['$0','$0 - $5,000','$5,000 - $10,000', '$10,000 - $20,000', '$20,000 - $30,000', '$30,000 - $50,000', '$50,000+']
}


$(document).ready(function(){
	Plex.adminAdvFilter.initSections();
	if( Plex.adminAdvFilter.isFilterLocked() ) Plex.adminAdvFilter.shutdownFilters();
	Plex.adminAdvFilter.showAllDoneBtn();
});

/***************************************************************
* all done button should only show if coming from admin setup (step3.js) 
* redirect from process sends fromAdmin=1
****************************************************************/
Plex.adminAdvFilter.showAllDoneBtn = function(){
	
	var url = window.location.href.split('?').splice(1,1); //array container only queries
	var tmp = [];  	//temp array to hold each query [0] query id [1] query value

	for(var i in url){

		tmp = url[i].split('=');

		if(tmp[0] === 'fromAdmin'){
			if(tmp[1] === '1')
				$('a.targeting-done-btn').css('display', 'inline-block');
		}
	}
};



// -- section objects start
Plex.adminAdvFilter.isFilterLocked = function(){
	return !!parseInt($('ul.adv-filtering-menu').first().data('locked'));
}

Plex.adminAdvFilter.shutdownFilters = function(){
	_.each(Plex.adminAdvFilter.sections.list, function(obj, index, arr){
		if( obj.name !== 'location' && obj.name !== 'major' && obj.name !== 'startDateTerm' && obj.name !== 'financial' && obj.name !== 'typeofschool' ){
			$('li[data-search-tab="'+obj.name+'"]').addClass('dekcol');
			obj.lock();
		}
	});
}

Plex.adminAdvFilter.initSections = function(){
	var sections = $('.adv-filtering-menu-container li[data-filter-tab]'), tab = null;

	Plex.adminAdvFilter.sections = new SectionList();

	$.each(sections, function(val, index){
		tab = $(this).data('filter-tab');
		Plex.adminAdvFilter.sections.addSection( new Section(tab) );
	});
}

//section obj and related functions
var Section = function Section(name){
	this.name = name;
	this.changed = !1;
	this.locked = !1;
	this.crumbs = [];
	this.alreadyInit = !1;
};

//change already init to true
Section.prototype.initStatus = function(status){
	this.alreadyInit = status;
};

//makes changed true and updates view to show checkmark
Section.prototype.hasChanged = function(){
	this.changed = !0;
	Plex.adminAdvFilter.sections.updateView();
};

//changes locked to true
Section.prototype.lock = function(){
	this.locked = !0;
};

//resets changed to false
Section.prototype.reset = function(){
	this.changed = !1;
	Plex.adminAdvFilter.sections.updateView();
};

//adds single crumb
Section.prototype.addCrumb = function(crumb){
	this.crumbs.push(crumb);
};

//removes single crumb
Section.prototype.removeCrumb = function(crumb){
	this.crumbs = _.reject(this.crumbs, crumb);
};

//removes single crumb by value
Section.prototype.removeCrumbByValue = function(crumb){
	this.crumbs = _.reject(this.crumbs, {value: crumb});
};

//removes crumbs by component 
Section.prototype.removeCrumbByComponent = function(comp){
	this.crumbs = _.reject(this.crumbs, {component: comp});
};

//removes crumbs by component 
Section.prototype.removeCrumbBySection = function(sect){
	this.crumbs = _.reject(this.crumbs, {section: sect});
};

//returns crumb based on component and/or val
Section.prototype.getCrumb = function(comp, val){
	if( comp && val ) return _.where(this.crumbs, {component: comp, value: val});
	else if( comp ) return _.where(this.crumbs, {component: comp});
	else return _.where(this.crumbs, {value: val});
};

//clear crumbs
Section.prototype.clearCrumbs = function(){
	this.crumbs.length = 0;
}

//remove all crumbs in current sections
Section.prototype.removeDiscardedCrumbs = function(){
	this.crumbs = _.reject(this.crumbs, {isDefault: false});
	Plex.adminAdvFilter.sections.renderCrumbs();
}

//find non default crumbs
Section.prototype.findNonDefaultCrumbs = function(){
	return _.findWhere(this.crumbs, {isDefault: false});
}

Section.prototype.hideDepartmentDegreeOptions = function(elem){
	$(elem).closest('.dept-item').find('input[data-department-degreeof]').addClass('hide');
};

Section.prototype.showDepartmentDegreeOptions = function(elem){
	$(elem).closest('.dept-item').find('input[data-department-degreeof]').removeClass('hide');
};

Section.prototype.buildMajorUI = function(id, val){
	var ui = this.buildItemUI('major', val);
	if( ui ) $('.major-list.'+id).prepend(ui).find('.dept-item').first().slideDown(250);
};

Section.prototype.buildDeptUI = function(id){
	var ui = this.buildItemUI('department'), 
		header = this.getDeptHeaderUI(),
		header_elem = $('.dept-list-row');	

	if( ui ){

		//if header does not exist yet, add it
		if( header_elem.length === 0 ){
			ui = header + ui;
			$('.dept-list').html(ui).find('.dept-item[data-dept]').first().slideDown(250);	
		}else{
			header_elem.after(ui).parent().find('.dept-item[data-dept]').first().slideDown(250);
		}

		//if id is valid, make call to get all majors of this dept
		if( id ){
			dept_obj = _.filter(this.crumbs, {component: 'department'}).pop();
			Plex.adminAdvFilter.populateMajorBasedOnDepartment( id, dept_obj.value );
		}
	}
};

Section.prototype.buildItemUI = function(type, filterby){
	var ui = '', list = null, trimmed_dept = '', dept_elem = null;

	list = _.filter(this.crumbs, {component: type});

	if( type === 'department' ){
		list = [list.pop()];
		//if this dept ui already exists, don't create it again
		if( $('.dept-item[data-dept="'+list[0].value+'"]').length > 0 ) return false;
	}else if( type === 'major' && filterby ){
		list = _.filter(list, {childOf: filterby});
		list = [list.pop()];
		if( $('.major-list .dept-item[data-dept="'+list[0].value+'"]').length > 0 ) return false;
	}

	_.each(list, function(obj, i){
		if( obj.value ) trimmed_dept = obj.value.replace(/\/| /g, '');
		ui += '<div class="dept-item" data-dept="'+obj.value+'">';
		ui += 	'<div class="subject">';
		ui += 		'<div class="dept-action">';
		ui += 			'<div class="remove-dept">';
		ui += 				'<div>&#10006;</div>';
		ui += 			'</div>';
		ui += 			'<div class="dept-name">';
		ui += 				obj.value;
		ui += 			'</div>';

		if( type === 'department' ){
			ui += 			'<div class="show-major">';
			ui += 				'<div class="arrow"></div>';
			ui += 			'</div>';
		}
		
		ui += 			'<input type="hidden" name="is_'+obj.component+'" value="'+obj.value+'" data-childOf="'+obj.childOf+'" data-id="'+obj.elem_id+'" />';
		ui += 		'</div>';
		ui += 	'</div>';
		ui += 	'<div class="subject">';
		ui += 		'<input type="checkbox" value="1" name="certificateProgram" checked="checked" data-'+obj.component+'-degreeof="'+obj.value+'" class="deg-option" />';
		ui += 	'</div>';
		ui += 	'<div class="subject">';
		ui += 		'<input type="checkbox" value="2" name="associates" checked="checked" data-'+obj.component+'-degreeof="'+obj.value+'" class="deg-option" />';
		ui += 	'</div>';
		ui += 	'<div class="subject">';
		ui += 		'<input type="checkbox" value="3" name="bachelors" checked="checked" data-'+obj.component+'-degreeof="'+obj.value+'" class="deg-option" />';
		ui += 	'</div>';
		ui += 	'<div class="subject">';
		ui += 		'<input type="checkbox" value="4" name="masters" checked="checked" data-'+obj.component+'-degreeof="'+obj.value+'" class="deg-option" />';
		ui += 	'</div>';
		ui += 	'<div class="subject">';
		ui += 		'<input type="checkbox" value="5" name="doctorate" checked="checked" data-'+obj.component+'-degreeof="'+obj.value+'" class="deg-option" />';
		ui += 	'</div>';

		if( type === 'department' ){
			ui += 	'<div class="major-pane component" data-component="major">';
			ui += 		'<div class="ml15"><b>Major:</b></div>';
			ui += 		'<div class="ml15">Choose one option</div>';
			ui += 		'<input type="radio" value="all" name="'+trimmed_dept+'_majors" checked="checked" id="'+trimmed_dept+'_all_major" class="radio-filter filter-all filter-this ml15" />';
			ui += 		'<label for="'+trimmed_dept+'_all_major">All</label>';
			ui += 		'<input type="radio" value="include" name="'+trimmed_dept+'_majors" id="'+trimmed_dept+'_include_major" class="radio-filter filter-all filter-this ml15" />';
			ui += 		'<label for="'+trimmed_dept+'_include_major">Include</label>';
			ui += 		'<input type="radio" value="exclude" name="'+trimmed_dept+'_majors" id="'+trimmed_dept+'_exclude_major" class="radio-filter filter-all filter-this ml15" />';
			ui += 		'<label for="'+trimmed_dept+'_exclude_major">Exclude</label>';
			ui += 		'<div class="hideOnLoad">';
			ui += 			'<div class="ml15">You can select multiple options, just click to add.</div>';
			ui += 				'<select name="major" id="'+trimmed_dept+'_selector" class="major-pane-selector select-filter filter-this ml15"></select>';
			ui += 			'<div class="major-list '+trimmed_dept+'_selector"></div>';
			ui += 		'</div>';
			ui += 	'</div>';
		}
		
		ui += '</div>';
	});

	return ui;
};

Section.prototype.destroyDeptUI = function(dept){
	var all_depts = $('.dept-item');

	//if more than one dept, just remove that one dept ui
	//else only one dept left, so remove header as well
	if( all_depts.length > 1 ){
		$('.dept-item[data-dept="'+dept+'"]').remove();
		this.removeCrumbByValue(dept);
	}else{
		$('.dept-list').html('');
	}

};

Section.prototype.getDeptHeaderUI = function(){
	var header = '';

	header += '<div class="dept-list-row">';
	header += 	'<div class="dept-head">';
	header += 		'Select degree level for all departments';	
	header += 	'</div>';

	header += 	'<div class="opts-row">';
	header += 		'<div class="dept-degree-opts">';
	header += 			'<input type="checkbox" checked="checked" class="toggle-controller" name="certificateProgram">';
	header += 			'<input type="hidden" name="dept_certificateProgram" />';
	header += 		'</div>';
	header += 		'<div class="dept-degree-opts">';
	header += 			'<input type="checkbox" checked="checked" class="toggle-controller" name="associates">';
	header += 			'<input type="hidden" name="dept_associates" />';
	header += 		'</div>';
	header += 		'<div class="dept-degree-opts">';
	header += 			'<input type="checkbox" checked="checked" class="toggle-controller" name="bachelors">';
	header += 			'<input type="hidden" name="dept_bachelors" />';
	header += 		'</div>';
	header += 		'<div class="dept-degree-opts">';
	header += 			'<input type="checkbox" checked="checked" class="toggle-controller" name="masters">';
	header += 			'<input type="hidden" name="dept_masters" />';
	header += 		'</div>';
	header += 		'<div class="end dept-degree-opts">';
	header += 			'<input type="checkbox" checked="checked" class="toggle-controller" name="doctorate">';
	header += 			'<input type="hidden" name="dept_doctorate" />';
	header += 		'</div>';
	header += 	'</div>';

	header += 	'<div class="opts-label-row">';
	header += 		'<div class="opts-label">';
	header += 			'<label>Filter is <span>including</span> students interested in:</label>';
	header += 		'</div>';
	header += 		'<div class="opts-label">';
	header += 			'<label>Certificate Program</label>';
	header += 		'</div>';
	header += 		'<div class="opts-label">';
	header += 			'<label>Associate\'s</label>';
	header += 		'</div>';
	header += 		'<div class="opts-label">';
	header += 			'<label>Bachelor\'s</label>';
	header += 		'</div>';
	header += 		'<div class="opts-label">';
	header += 			'<label>Master\'s</label>';
	header += 		'</div>';
	header += 		'<div class="end opts-label">';
	header += 			'<label>Doctorate</label>';
	header += 		'</div>';
	header += 	'</div>';

	header += '</div>';

	return header;
};

Section.prototype.buildDeptCheckboxes = function(){
	var box = '';	


};

//crumb obj
var Crumb = function Crumb(sect, comp, opt, val, minVal, maxVal, elemId, child){
	this.section = sect || null,
	this.component = comp || null;
	this.option = opt || null;
	this.value = val || null;
	this.min = minVal || null;
	this.max = maxVal || null;
	this.elem_id = elemId || null; 
	this.isDefault = !0;
	this.childOf = child || null;
};

//edit based on prop and val passed
Crumb.prototype.edit = function(prop, val) {
	this[prop] = val;
};

//sectionList object and related functions
var SectionList = function SectionList(){
	this.list = [];
};

//adds section to SectionList
SectionList.prototype.addSection = function(section){
	this.list.push(section);
	this.initialMajorData = null;
};

//if section is changed, will add checkmark, otherwise will remove it
SectionList.prototype.updateView = function(){
	var elem = null;
	_.each(this.list, function(obj, index, arr){
		elem = $('li[data-filter-tab="'+obj.name+'"] .change-icon');
		if( obj.changed ) elem.removeClass('hide');
		else if( !elem.hasClass('hide') ) elem.addClass('hide');
	});
};

//resets all sections changed prop to false
SectionList.prototype.resetAll = function(){
	_.each(this.list, function(obj){
		if( obj.changed ) obj.reset();
	});
};

//return the section asked for
SectionList.prototype.getSection = function(section_name){
	return _.findWhere(this.list, {name: section_name});
};

//returns all sections that have changed true
SectionList.prototype.getChangedSections = function(bool){
	return _.where(this.list, {changed: bool});
};

//prepare html and inject to list elem on view
SectionList.prototype.renderCrumbs = function(){
	var html_crumbs = '', uiFunction = null, self = this, alteredName = null;

	//loop through each section
	_.each(this.list, function(obj){
		//if this section's crumb arr has a least one crumb, then start building ui
		if( obj.crumbs.length > 0 ){
			uiFunction = obj.name + 'CrumbUI';
			if( obj.name === 'typeofschool' ) alteredName = 'Type of School';
			html_crumbs += '<li>';
			html_crumbs += 	'<div class="clearfix">';
			html_crumbs += 		'<div class="left section">'+(alteredName || obj.name)+': </div>';
			html_crumbs += 		self[uiFunction](obj.crumbs, obj.name);
			html_crumbs += 	'</div>';
			html_crumbs += '</li>';
		}
		
	});

	//render to view
	$('.filter-crumb-list').html( html_crumbs );
}

//building location/major crumb ui using same function for both
Plex.adminAdvFilter.locationMajorBuild = function(crumbs){
	var html = '', option_icon = '';

	_.each(crumbs, function(obj){
		option_icon = obj.option === 'include' ? '+' : '-';
		html += '<div class="left tag" data-tag-belongsto="'+obj.section+'" data-tag-val="'+obj.value+'" data-tag-component="'+obj.component+'" data-elem="'+obj.elem_id+'">';
		html +=		'<span class="'+obj.option+'">'+option_icon+'</span>'+obj.value+'<span class="remove">x</span>';
		html += '</div>';
	});

	return html;
}

Plex.adminAdvFilter.selectFieldOnly = function(crumbs){
	var html = '';

	_.each(crumbs, function(obj){
		html += '<div class="left tag" data-tag-belongsto="'+obj.section+'" data-tag-val="'+obj.value+'" data-tag-component="'+obj.component+'" data-elem="'+obj.elem_id+'">';
		html +=		'<span class=""></span>'+(obj.option || obj.value)+'<span class="remove">x</span>';
		html += '</div>';
	});

	return html;
};

SectionList.prototype.locationCrumbUI = Plex.adminAdvFilter.locationMajorBuild;
SectionList.prototype.majorCrumbUI = Plex.adminAdvFilter.locationMajorBuild;
SectionList.prototype.militaryAffiliationCrumbUI = Plex.adminAdvFilter.selectFieldOnly;
SectionList.prototype.startDateTermCrumbUI = Plex.adminAdvFilter.selectFieldOnly;
//SectionList.prototype.financialCrumbUI = Plex.adminAdvFilter.selectFieldOnly;
SectionList.prototype.typeofschoolCrumbUI = Plex.adminAdvFilter.selectFieldOnly;

SectionList.prototype.financialCrumbUI = function(crumbs){
	var html = '';

	_.each(crumbs, function(obj){
		html += '<div class="left tag" data-tag-belongsto="'+obj.section+'" data-tag-val="'+obj.value+'" data-tag-component="'+obj.component+'" data-elem="'+obj.elem_id+'">';
		
		if(obj.value === "Interested in aid"){
			html +=	'<span class=""></span>'+(obj.option || obj.value)+'<span class="remove">x</span>';
		}else{
			html +=	'<span class=""></span>'+(obj.option || obj.value);
		}

		html += '</div>';
	});

	return html;
};




//building profile completion crumb ui
SectionList.prototype.profileCompletionCrumbUI = function(crumbs){
	var html = '';

	_.each(crumbs, function(obj){
		html += '<div class="left tag" data-tag-belongsto="'+obj.section+'" data-tag-val="'+obj.value+'" data-tag-component="'+obj.component+'" data-elem="'+obj.elem_id+'">';
		html +=		obj.value+'%<span class="remove">x</span>';
		html += '</div>';
	});

	return html;
};

//building uploads/educationLevel/desiredDegree ui and using same function for all three sections
Plex.adminAdvFilter.uploadsEduDegreeBuild = function(crumbs){
	var html = '';

	_.each(crumbs, function(obj){
		html += '<div class="left tag" data-tag-val="'+obj.value+'" data-tag-belongsto="'+obj.section+'" data-tag-component="'+obj.component+'" data-elem="'+obj.elem_id+'">';
		html += 	'<span class=""></span>'+obj.value+'<span class="remove">x</span>';
		html += '</div>';
	});

	return html;
}
SectionList.prototype.uploadsCrumbUI = Plex.adminAdvFilter.uploadsEduDegreeBuild;
SectionList.prototype.educationLevelCrumbUI = Plex.adminAdvFilter.uploadsEduDegreeBuild;
SectionList.prototype.desiredDegreeCrumbUI = Plex.adminAdvFilter.uploadsEduDegreeBuild;

//building scores crumb ui
SectionList.prototype.scoresCrumbUI = function(crumbs){
	var min_val = '', no_min = '-', max_val = '', no_max = '+', component = '',
		val = '', tags_arr = [], last_index = -1, html = '';

	_.each(crumbs, function(obj){
		if( obj.min && obj.max ) val = obj.min + ' - ' + obj.max;
		else if( obj.min ) val = obj.min + ' +';
		else val = '- ' + obj.max;

		html += '<div class="left tag" data-tag-belongsto="'+obj.section+'" data-tag-val="'+obj.value+'" data-tag-component="'+obj.component+'" data-elem="'+obj.elem_id+'">';
		html += 	'<span class=""></span>'+obj.component+': '+val+'<span class="remove">x</span>';
		html += '</div>';
	});

	return html;
}

//building demographic crumb ui
SectionList.prototype.demographicCrumbUI = function(crumbs){
	var html = '';

	_.each(crumbs, function(obj){
		if( obj.component.toLowerCase() === 'age' ){
			if( obj.min && obj.max ) val = obj.min + ' - ' + obj.max;
			else if( obj.min ) val = obj.min + ' +';
			else val = '- ' + obj.max;

			html += '<div class="left tag" data-tag-belongsto="'+obj.section+'" data-tag-val="'+obj.value+'" data-tag-component="'+obj.component+'" data-elem="'+obj.elem_id+'">';
			html += 	'<span class=""></span>'+obj.component+': '+val+'<span class="remove">x</span>';
			html += '</div>';
		}else if( obj.component.toLowerCase() === 'gender' ){
			html += '<div class="left tag" data-tag-val="'+obj.value+'" data-tag-belongsto="'+obj.section+'" data-tag-component="'+obj.component+'" data-elem="'+obj.elem_id+'">';
			html += 	'<span class=""></span>'+obj.value+'<span class="remove">x</span>';
			html += '</div>';
		}else{
			option_icon = obj.option === 'include' ? '+' : '-';
			html += '<div class="left tag" data-tag-belongsto="'+obj.section+'" data-tag-val="'+obj.value+'" data-tag-component="'+obj.component+'" data-elem="'+obj.elem_id+'">';
			html +=		'<span class="'+obj.option+'">'+option_icon+'</span>'+obj.value+'<span class="remove">x</span>';
			html += '</div>';
		}
	});

	return html;
};



//init crumbs after ajax call
SectionList.prototype.initCrumbs = function(section_name){
	var section = this.getSection(section_name),
		pageInputs = $('.filter-page-section .filter-this'),  //hidden inputs with crumb filters loaded from server
		initFunction = null, valued_elems = null, nonDefaultCrumbsFound = false;

	//if prevTab is set, then get prev section and check if it has non-default crumbs
	if( Plex.adminAdvFilter.prevTab ){
		prevSection = this.getSection(Plex.adminAdvFilter.prevTab);
		//if user discarded changes, remove those crumbs before adding more default ones
		if( prevSection ){
			prevSection.removeCrumbBySection(Plex.adminAdvFilter.prevTab);
			Plex.adminAdvFilter.sections.renderCrumbs();
		}
	}

	section.clearCrumbs();

	//crumb init start
	Plex.adminAdvFilter.isInit = true;

	//get function name, then run that init function
	initFunction = 'init'+section_name;
	this[initFunction](pageInputs, section_name);
	section.initStatus(!0);

	//crumb init end
	Plex.adminAdvFilter.isInit = false;
}

SectionList.prototype.initstartDateTerm = function(){
	var hidden_elems = $('.component input[type="hidden"]');

	$.each(hidden_elems, function(){
		$('#startDateTerm_filter').val( $(this).val() ).trigger('change');
	});
};

SectionList.prototype.initfinancial = function(){
	var financial_elem = $('#financial_filter'), 
		val = '',
		interested_elem = $('#interested_in_aid'), 
		fields = $('.component input[type="hidden"]');

	
	//must get mininum value to get crumbs -- not all values
	//will actually traverse array to look for minimum value in case values are not in order
	var minNum = 50001;			//current max value is 50000 
	var num = null, min = null;
	var tmp = null;
	var arr = null;
	$.each(fields, function(){
		val = $(this).val();
		if( val === 'interested_in_aid' ) interested_elem.prop('checked', true).trigger('change');
		else {
			
			//get value
			tmp = val.replace(/\$|[-]/g, ' ');
			tmp = tmp.replace(/[,]|[.]/g, '');
			arr = tmp.split(' ');
			num = parseInt(arr[arr.length-1], 10);

			if(minNum){ //if min set
				if(minNum > num){ //check to see if it is greater than num
					minNum = num;	// used in comparison
					min = val;		// how actually represented 
				}
			}
		}
	
	});
	
	financial_elem.val( min ).trigger('change');

};

SectionList.prototype.inittypeofschool = function(){
	var hidden_elems = $('.component input[type="hidden"]'), val = '', _this = null;

	if( hidden_elems.length > 0 ){
		$.each(hidden_elems, function(){
			_this = $(this);
			if( _this.val() === '0' ) val = 'campus_only';
			else if( _this.val() === '1' ) val = 'online_only';
			else val = 'both'; //else hidden elem val should = 2

			$('input[name="typeofschool"][value="'+val+'"]').prop('checked', true).trigger('change');
		});
	}else{
		$('input[name="typeofschool"][value="both"]').prop('checked', true).trigger('change');
	}
};

Plex.adminAdvFilter.initCheckboxes = function(elem_arr){
	var checked_elems = [], elem = null;

	//if checked, then trigger change
	$.each(elem_arr, function(){
		if( $(this).is(':checked') && $(this).hasClass('checkbox-filter') ){
			$(this).trigger('change');
		}else if( $(this).hasClass('text-filter') && $(this).val() ){
			$(this).trigger('change');
		}
	});
}
SectionList.prototype.inituploads = Plex.adminAdvFilter.initCheckboxes;
SectionList.prototype.initeducationLevel = Plex.adminAdvFilter.initCheckboxes;
SectionList.prototype.initdesiredDegree = Plex.adminAdvFilter.initCheckboxes;
SectionList.prototype.initscores = Plex.adminAdvFilter.initCheckboxes;
Plex.adminAdvFilter.initLocMajorEthRel = function(elem_arr, section_name){
	var elem = null, tag = null, tags = null, crumb = null, min = null, max = null,
		val = null, section_obj = null, option = null, elem_id = null;

	section_obj = this.getSection(section_name);

	$.each(elem_arr, function(){
		elem = $(this);
		if( elem.hasClass('radio-filter') && elem.is(':checked') )
			option = elem.val();

		if( elem.hasClass('select-filter') ){
			tags = $('span[data-type-id="'+$(elem).attr('name')+'"]');
			$.each(tags, function(){
				tag = $(this);
				val = tag.data('tag-id');
				component = tag.data('type-id');
				crumb = new Crumb(section_name, component, option, val, min, max, elem_id);
				section_obj.addCrumb(crumb);
			});
		}
	});

	this.renderCrumbs();
}

SectionList.prototype.initlocation = Plex.adminAdvFilter.initLocMajorEthRel;

SectionList.prototype.initmajor = function(elem_arr, section_name){
	var hidden_elems = $('.filter-by-major-container input[type="hidden"]:not([name="_token"])'),
		_this = null, dept_id = null, dept_temp = null, dept_val = null, 
		major_id = null, major_temp = null, major_val = null,
		degree_id = null, degree_temp = null, degree_val = null,
		major_selector = null, in_ex = null, dept_arr = [], obj = {},
		dept_selector = $('#specificDepartment_filter');

	function getIdFor(who){
		if( who === 'dept' ) return _this.val().split(',')[0];
		else if( who === 'major' ) return _this.val().split(',')[1];
		return _this.val().split(',')[2];
	};

	//loop through each hidden elem
	$.each(hidden_elems, function(i){
		_this = $(this);
		
		//if elem is the include/exclude value
		if( _this.attr('name') === 'dept_in_ex' ){
			in_ex = _this.val();
			$('.filter-by-major-container input[name="department"][type="radio"][value="'+in_ex+'"]').prop('checked', true).trigger('change');

		}else if( _this.attr('name') === 'mdd' ){
			dept_id = getIdFor('dept');
			major_id = getIdFor('major');
			degree_id = getIdFor('degree');
			obj = {};
			is_new = false;

			dept_val = dept_selector.find('option[data-department-id="'+dept_id+'"]').val();

			obj = _.findWhere(dept_arr, {dept: dept_val});

			if( !obj ){
				obj = {dept: dept_val, majors: []};
				obj.in_ex = in_ex;
				is_new = !is_new;
				obj.major_id = major_id;
				obj['degree_'+degree_id] = degree_id;
			}else{
				obj['degree_'+degree_id] = degree_id;
			}

			mjr = _.findWhere(obj.majors, {major: major_id});

			if( !mjr ){
				tmp = {};
				tmp.major = major_id;
				tmp['degree_'+degree_id] = degree_id;
				obj.majors.push( tmp );
			}else{
				mjr['degree_'+degree_id] = degree_id;
			}

			if( is_new ) dept_arr.push(obj);

			//only trigger once per different dept ids
			if( dept_id && dept_temp !== dept_id ){
				dept_temp = dept_id;
				dept_val = dept_selector.find('option[data-department-id="'+dept_id+'"]').val();
				dept_selector.val(dept_val).trigger('change');

				if( major_id ){
					$('input[data-department-degreeof="'+dept_val+'"]').prop('checked', false);
					$('.dept-item[data-dept="'+dept_val+'"]').find('.show-major').addClass('needs-init');
				}
			}
		}

	});

	this.initialMajorData = dept_arr;
	Plex.adminAdvFilter.majorsInitData = dept_arr;
	this.initDeptDegreeBuild();
};

SectionList.prototype.initDeptDegreeBuild = function(){
	if( this.initialMajorData.length > 0 ){
		_.each(this.initialMajorData, function(obj){
			if( !obj.major_id ){
				//make all degrees for this major false first
				$('input[data-department-degreeof="'+obj.dept+'"]').prop('checked', false);
				for(var prop in obj){
					if( obj.hasOwnProperty(prop) && prop.indexOf('degree') > -1 ){
						$('input[data-department-degreeof="'+obj.dept+'"][value="'+obj[prop]+'"]').prop('checked', true);
					}
				}
			}
		});
	}
};

SectionList.prototype.initMajorBuild = function(dept){
	var dept_item = _.findWhere(this.initialMajorData, {dept: dept}),
		elem = $('.dept-item[data-dept="'+dept+'"]'), major = null;

	elem.find('input[type="radio"][value="'+dept_item.in_ex+'"]').prop('checked', true).trigger('change');

	_.each(dept_item.majors, function(obj, i){
		major = elem.find('select option[data-major-id="'+obj.major+'"]').val();
		elem.find('select').val(major).trigger('change');
		elem.find('.major-list .dept-item[data-dept="'+major+'"] input').prop('checked', false);

		for(var deg in obj){
			if( obj.hasOwnProperty(deg) && deg !== 'major' ){
				elem.find('input[data-major-degreeof="'+major+'"][value="'+obj[deg]+'"]').prop('checked', true);
			}
		}
	});
};

SectionList.prototype.initdemographic = function(elem_arr, section_name){
	var elem = null, _this = this, ageGender_arr = [], ethRel_arr = [];

	$.each(elem_arr, function(){
		elem = $(this);
		if( elem.hasClass('age') || elem.hasClass('gender') ){
			ageGender_arr.push(elem);
		}else{
			ethRel_arr.push(elem)
		}
	});

	_this.initscores(ageGender_arr);
	_this.initlocation(ethRel_arr, section_name);
}
SectionList.prototype.initmilitaryAffiliation = function(elem_arr, section_name){
	var inMili_val = null, _this = null, savedMili = null,
		inMili = $('#inMilitary_filter'), miliAffili = $('#militaryAffiliation_filter');

	if( inMili.val() === '1' ){
		inMili.trigger('change');
		miliAffili.slideDown(250);
		savedMili = $('.military-values');

		if( savedMili.length > 0 ){
			$.each(savedMili, function(){
				_this = $(this);
				miliAffili.find('option[value="'+_this.data('val')+'"]').prop('selected', true).parent().trigger('change');
			});

			// miliAffili.find('option[value=""]').prop('selected', true).parent().trigger('change');
		}
	}else if( inMili.val() === '0' ){
		inMili.trigger('change');
	}
};

SectionList.prototype.initprofileCompletion = function(elem_arr, section_name){
	$.each(elem_arr, function(){
		if( $(this).val().toString() ) $(this).trigger('change');
	});
};

Plex.adminAdvFilter.resetChanges = function(tab){
	var section = null;
	section = Plex.adminAdvFilter.sections.getSection(tab);
	if(section) section.reset();
};
// -- section objects end


// --------------------------- get filter section - start
$(document).on('click', '.adv-filtering-menu li', function(e){
	e.preventDefault();
	var section = null;

	Plex.adminAdvFilter.prevTab = Plex.adminAdvFilter.currentTab;
	Plex.adminAdvFilter.currentTab = $(this).data('filter-tab');

	section = Plex.adminAdvFilter.sections.getSection(Plex.adminAdvFilter.currentTab);
	if( section && section.locked ) return false;

	$('.adv-filtering-menu li').removeClass('active');

	if( Plex.adminAdvFilter.change_has_been_made ){
		Plex.adminAdvFilter.openDialogModal();
	}else{
		//no change has been made, so go ahead and just get select section	
		Plex.adminAdvFilter.getSelectedFilterSection();
	}
	
});



//get selected filter section; checking if user has made changes and prompting them to save before moving on
Plex.adminAdvFilter.getSelectedFilterSection = function(){
	var this_sections_fields = null, route = '';

	Plex.adminAdvFilter.showLoader();

	if( Plex.adminAdvFilter.currentTab === 'major' ) route = Plex.adminAdvFilter.filter_section_route + 'majorDeptDegree';
	else route = Plex.adminAdvFilter.filter_section_route + Plex.adminAdvFilter.currentTab;

	$.ajax({
		url: route,
		type: 'GET',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	})
	.done(function(data){
		//populate adv filter section container with html

		$('.targeting-video-ajaxed').remove();
		$('.filter-crumbs-container').removeClass('hide');
		$('.recomm-meter-container').removeClass('hide');
		$('.adv-filtering-section-container').removeClass('hide');
		$('.reset-save-filters-col').removeClass('hide');
		$('.adv-filtering-section-container').html(data);	
		$('.adv-filtering-menu li[data-filter-tab="'+Plex.adminAdvFilter.currentTab+'"]').addClass('active');
		$('.filtering-menu-sm-container').slideUp(250);

		//for mobile, update current section indicator
		Plex.adminAdvFilter.currentTab_text = $('.adv-filtering-menu li[data-filter-tab="'+Plex.adminAdvFilter.currentTab+'"]').first().text();
		$('.filter-page-indicator').html(Plex.adminAdvFilter.currentTab_text);

		//show only when a tab is clicked and once its open, no need to keep calling .show()
		if( !Plex.adminAdvFilter.save_reset_row_visible ){
			Plex.adminAdvFilter.save_reset_row_visible = true;
			$('.reset-save-filters-col').removeClass('hidden');
			$('.recomm-meter-container').removeClass('hidden');
		}

		//reset filter tags array - if there are tags on load, then save those to tags array
		Plex.adminAdvFilter.filter_tags_array = [];
		Plex.adminAdvFilter.checkIfTagsExistOnLoad();

		//save current state of this section's form fields
		this_sections_fields = $('.filter-by-'+Plex.adminAdvFilter.currentTab+'-container').find('.filter-this');

		//reset change_has_been_made to false when opening new filter section
		Plex.adminAdvFilter.change_has_been_made = false;

		//save current forms fields
		Plex.adminAdvFilter.currentFormFields = this_sections_fields;

		//only for location filter - hide select content if filter by ALL is checked on load
		Plex.adminAdvFilter.hideRadioBtnContentOnload();

		//if country section loads with US selected then show state/city, otherwise hide
		if( Plex.adminAdvFilter.currentTab === 'location' ){
			Plex.adminAdvFilter.countryOnLoad();
		}

		Plex.adminAdvFilter.sections.initCrumbs(Plex.adminAdvFilter.currentTab);

		Plex.adminAdvFilter.hideLoader();
	});
}

Plex.adminAdvFilter.getVideoSection = function(){
	var this_sections_fields = null, route = '';

	Plex.adminAdvFilter.showLoader();
	
	route = Plex.adminAdvFilter.filter_section_route + Plex.adminAdvFilter.currentTab;

	$.ajax({
		url: route,
		type: 'GET',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	})
	.done(function(data){
		//populate adv filter section container with html
		$('.filter-crumbs-container').addClass('hide');
		$('.recomm-meter-container').addClass('hide');
		$('.adv-filtering-section-container').addClass('hide');
		$('.reset-save-filters-col').addClass('hide');
		$('.video-container').html(data);	

		Plex.adminAdvFilter.hideLoader();
	});
}
// --------------------------- get filter section - end



//for location filter, if ALL checkboxes are selected on load, then hide its related select fields
Plex.adminAdvFilter.hideRadioBtnContentOnload = function(){
	if( Plex.adminAdvFilter.currentTab === 'location' || Plex.adminAdvFilter.currentTab === 'major' ){

		$.each(Plex.adminAdvFilter.currentFormFields, function(){
			var _this = $(this);
			if( _this.val() === 'all' && _this.is(':checked') ){
				_this.closest('.contains-tags-row').find('.selection-row').slideUp(10);
			}
		});
	}
}



//medium and small filter menu drop down
$(document).on('click', '.select-filter-btn-sm', function(){
	$('.filtering-menu-sm-container').slideToggle(250);
});

$(document).on('click', '.targeting-video', function(){
	Plex.adminAdvFilter.currentTab = 'video';

	Plex.adminAdvFilter.getVideoSection();

	Plex.adminAdvFilter.hideLoader();
});

$(document).on('click', '.close-targeting-video', function(){
	$('.targeting-video-ajaxed').remove();
	$('.adv-filtering-section-container').removeClass('hide');
});

//if usa students checkbox is checked, then hide rest of form
$(document).on('change', '#us_filter', function(){
	if( $(this).is(':checked') ){
		$('.for-usa-students-only-container').slideDown(250);
	}else{
		$('.for-usa-students-only-container').slideUp(250);
	}
});


//if a change has been made on this form, and is trying to change sections and user hasn't clicked save after, then alert them 
//their changes will not be saved if they do not click save
$(document).on('change', '.filter-page-section .filter-this', function(){
	var _this = $(this), section = null, component = _this.closest('.component').data('component'),
		filterby = _this.closest('.dept-item').data('dept');

	section = Plex.adminAdvFilter.sections.getSection(Plex.adminAdvFilter.currentTab);

	if( !Plex.adminAdvFilter.isInit ){
		Plex.adminAdvFilter.change_has_been_made = true;
		section.hasChanged();
	}

	//get this components crumb, if we have it already
	component = component ? component : _this.val();
	alreadyHaveCrumb = section.getCrumb(component);
	
	
	//if a crumb of this component type is already created, edit it, otherwise create it
	if( alreadyHaveCrumb.length > 0 ){
		Plex.adminAdvFilter.editCrumb(_this, alreadyHaveCrumb);
	}else{
		Plex.adminAdvFilter.createCrumb(_this);	
	}
	

	if( _this.hasClass('major-pane-selector') ){
		section.buildMajorUI( _this.attr('id'), filterby );
		section.hideDepartmentDegreeOptions(_this);
	}
});


Plex.adminAdvFilter.createCrumb = function(elem){
	var section_name = elem.closest('.filter-page-section').data('section'),
		component = elem.closest('.component').data('component'),
		elem_id = elem.attr('id'), childOf = elem.closest('.dept-item').data('dept'),
		option = null, val = elem.val(), section_obj = null, old_crumb = null,
		crumb = null, skip = false, gender = null, min = null, max = null;

	section_obj = Plex.adminAdvFilter.sections.getSection(section_name);

	switch(section_name){
		case 'location':
		case 'major':
		case 'startDateTerm':
			if( !elem.hasClass('radio-filter') ){
				option = elem.closest('.component').find('input[type="radio"]:checked').val();
				if( component === 'startDateTerm' && !val ) skip = true;
				if( component === 'major' || component === 'department' ){
					elem_id = $('#'+elem_id).find('option[value="'+val+'"]').data(component+'-id');
				}
			}else{
				if( elem.val() !== 'all' ){
					component_crumbs = _.where(section_obj.crumbs, {component: component});
					_.each(component_crumbs, function(obj){
						obj.option = elem.val();
					});

					skip = !skip;
				}
			}
			
			break;
		case 'scores':
			option = elem.data('scores');
			if(option === 'min') min = val;
			else max = val;
			break;
		case 'demographic':
			if( component.toLowerCase() === 'age' ){
				option = elem.data('scores');
				if(option === 'min') min = val;
				else max = val;
			}else if( component.toLowerCase() === 'gender' ){
				option = true;
				val = elem.parent().find('label[for="'+elem.attr('id')+'"]').text();
			}else{
				if( !elem.hasClass('radio-filter') ){
					option = elem.closest('.component').find('input[type="radio"]:checked').val();
				}else{
					if( elem.val() !== 'all' ){
						component_crumbs = _.where(section_obj.crumbs, {component: component.toLowerCase()});
						_.each(component_crumbs, function(obj){
							obj.option = elem.val();
						});

						Plex.adminAdvFilter.sections.renderCrumbs();
					}
					skip = !skip;
				}
			}
			break;
		case 'uploads':
		case 'educationLevel':
		case 'desiredDegree':
			if( elem.is(':checked') ){
				option = elem.is(':checked');
				component = val;
			}else{
				skip = !skip;
			}
			break;
		case 'militaryAffiliation':
		case 'typeofschool':
			if( component === 'inMilitary' ){
				if( val ) val = $('#inMilitary_filter option[value="'+val+'"]').text();
			}else if( component === 'typeofschool' ){
				val = $('input[name="typeofschool"]:checked + label').text();
			}else{
				if( val ) val = $('#militaryAffiliation_filter option[value="'+val+'"]').text();
			}
			break;
		case 'financial':
			if( val && val != null && val != ''){

				if( elem_id !== 'interested_in_aid' ){
					skip = false;
					option  = elem.find('option[value="'+val+'"]').text();
					old_crumb = _.findWhere(section_obj.crumbs, {value: val});
					if( old_crumb ) skip = true;
				}else{
					option = val.split('_').join(' ').capitalize();
					old_crumb = _.findWhere(section_obj.crumbs, {value: elem_id});
					//if interested in aid checkbox is not checked or we already have one interested in aid crumb, then skip -> do not add crumb
					if( !$('#'+elem_id).is(':checked') ){
						skip = true;
						if( old_crumb ){  //if old crumb exists -- remove it
							section_obj.removeCrumb(old_crumb);
							Plex.adminAdvFilter.sections.renderCrumbs();
						}
					}
					if( $('#'+elem_id).is(':checked') && old_crumb){
							skip = true;
							section_obj.removeCrumb(old_crumb);
							Plex.adminAdvFilter.sections.renderCrumbs();
					}
					//if checked -- go on to add crumb
				}
			}
			
			break;
	}

	if( !skip ){

		//with financial ranges, we want to add a list of crumbs versus just the value selected
		if(section_name === 'financial' && option != 'Interested in aid'){
						
			Plex.adminAdvFilter.setFinancialCrumbList(component, option, section_obj, val, min, max);
		}
		else{ //everything else - just add one crumb
			crumb = new Crumb(section_name, component, option, val, min, max, elem_id, childOf);


			if( !Plex.adminAdvFilter.isInit ) crumb.edit('isDefault', false);
				section_obj.crumbs.push(crumb);

		}
	
		Plex.adminAdvFilter.sections.renderCrumbs();
	}

};


//edit crumbs that we already have
Plex.adminAdvFilter.editCrumb = function(elem, old_crumbs){
	var section_name = elem.closest('.filter-page-section').data('section'),
		component = elem.closest('.component').data('component'),
		option = null, val = elem.val(), section_obj = null, minOrMax = '',
		crumb = null, skip = false, gender = null, min = null, max = null,
		elem_id = elem.attr('id'), component_crumbs = null, temp = '',
		section_obj = null, isDuplicate = false, childOf = elem.closest('.dept-item').data('dept');

	switch(section_name){
		case 'location':
		case 'major':
		case 'startDateTerm':
			section_obj = Plex.adminAdvFilter.sections.getSection(section_name);
			if( !elem.hasClass('radio-filter') ){
				//if duplicate is found, duplicate object is returned
				if( val ){
					isDuplicate = _.find(old_crumbs, function(obj){
						return obj.value === val;
					});

					//if duplicate is not found, create new crumb
					if( !isDuplicate && val ){
						option = elem.closest('.component').find('input[type="radio"]:checked').val();
						if( component === 'major' || component === 'department' ){
							elem_id = $('#'+elem_id).find('option[value="'+val+'"]').data(component+'-id');
						}

						crumb = new Crumb(section_name, component, option, val, min, max, elem_id, childOf);

						if( !Plex.adminAdvFilter.isInit ) crumb.edit('isDefault', false);
						section_obj.crumbs.push(crumb);
					}
				}
				
			}else{
				if( elem.val() !== 'all' ){
					component_crumbs = _.where(section_obj.crumbs, {component: component});
					_.each(component_crumbs, function(obj){
						obj.option = elem.val();
					});
					if( section_name === 'major' && (val === 'include' || val === 'exclude') ){
						temp = val === 'include' ? 'including' : 'excluding';
						$('.opts-label').find('span').html(temp);
					} 
				}
			}
			break;
		case 'scores':
			option = elem.data('scores')
			_.each(old_crumbs, function(obj){
				if( option === 'min' ) obj.min = val;
				else obj.max = val;
			});
			break;
		case 'demographic':
			section_obj = Plex.adminAdvFilter.sections.getSection(section_name);
			if( component.toLowerCase() === 'age' ){
				option = elem.data('scores')
				_.each(old_crumbs, function(obj){
					if( option === 'min' ) obj.min = val;
					else obj.max = val;
				});
			}else if( component.toLowerCase() === 'gender' ){
				val = elem.parent().find('label[for="'+elem.attr('id')+'"]').text();
				old_crumbs[0].value = val;
			}else{
				if( !elem.hasClass('radio-filter') ){
					//if duplicate is found, duplicate object is returned
					isDuplicate = _.find(old_crumbs, function(obj){
						return obj.value === val;
					});

					//if duplicate is not found, create new crumb
					if( !isDuplicate ){
						option = elem.closest('.component').find('input[type="radio"]:checked').val();
						crumb = new Crumb(section_name, component, option, val, min, max);

						if( !Plex.adminAdvFilter.isInit )
							crumb.edit('isDefault', false);

						section_obj.crumbs.push(crumb);
					}
				}else{
					if( elem.val() !== 'all' ){
						component_crumbs = _.where(section_obj.crumbs, {component: component.toLowerCase()});
						_.each(component_crumbs, function(obj){
							obj.option = elem.val();
						});

						Plex.adminAdvFilter.sections.renderCrumbs();
					}
				}
			}
			break;
		case 'uploads':
		case 'educationLevel':
		case 'desiredDegree':
			section_obj = Plex.adminAdvFilter.sections.getSection(section_name);
			if( !elem.is(':checked') )
				section_obj.removeCrumb(old_crumbs[0]);
			break;
		case 'militaryAffiliation':
		case 'typeofschool':
			section_obj = Plex.adminAdvFilter.sections.getSection(section_name);
			if( component === 'inMilitary' ){
				val = $('#inMilitary_filter option[value="'+val+'"]').text();
				if( val === 'No' ){
					old_crumbs[0].value = val;
					section_obj.removeCrumbByComponent('militaryAffiliation');
				}else if( val !== 'Select...' ){
					old_crumbs[0].value = val;
				}
			}else if( component === 'typeofschool' ){
				old_crumbs[0].elem_id = elem_id;
				old_crumbs[0].value = $('input[name="typeofschool"]:checked + label').text();
			}else{
				val = $('#militaryAffiliation_filter option[value="'+val+'"]').text();

				isDuplicate = _.find(old_crumbs, function(obj){
					return obj.value === val;
				});

				if( !isDuplicate ){
					crumb = new Crumb(section_name, component, option, val, min, max);

					if( !Plex.adminAdvFilter.isInit )
						crumb.edit('isDefault', false);

					if( val !== 'Select...'  )
						section_obj.crumbs.push(crumb);
				}
			}
			break;
		case 'profileCompletion':
			section_obj = Plex.adminAdvFilter.sections.getSection(section_name);
			old_crumbs[0].value = val;
			isDuplicate = !1;
			break;
		case 'financial':
			section_obj = Plex.adminAdvFilter.sections.getSection(section_name);

			//if option is 'Select...' val = ''  
			//setFinancialCrumbList should clear ranges and keep 'interested in aid' if any
			if(val === ''){
				Plex.adminAdvFilter.setFinancialCrumbList(component, option, section_obj, val, min, max);
			}

			if( val && val != null && val != ''){
				
				if( val !== 'interested_in_aid'){
				
						option  = elem.find('option[value="'+val+'"]').text();		
						Plex.adminAdvFilter.setFinancialCrumbList(component, option, section_obj, val, min, max);
				

				}else{ //interested in aid
					old_crumb = _.findWhere(section_obj.crumbs, {value: 'interested_in_aid'});
					
					option = val.split('_').join(' ');

					//if interested in aid checkbox is not checked and we already have one interested in aid crumb, then remove that crumb
					//else create new interested in aid crumb
					if( !$('#'+elem_id).is(':checked') && old_crumb ){
						section_obj.removeCrumb(old_crumb);

						Plex.adminAdvFilter.sections.renderCrumbs();
					}
					//if checked and no crumb, add crumb
					if($('#'+elem_id).is(':checked') && !old_crumb ){

						crumb = new Crumb(section_name, component, option, val, min, max);
						section_obj.crumbs.push(crumb);
						
					}
					//if checked and there is an old crumb-- do nothing
					//if not checked and there is no old crumb -- do nothing
				}
			break;
		}
	}

	if( !isDuplicate) Plex.adminAdvFilter.sections.renderCrumbs();
}



Plex.adminAdvFilter.removeCrumb = function(elem){
	var section_name = elem.data('tag-belongsto'),
		component = elem.data('tag-component'),
		val = elem.data('tag-val'), section_obj = null,
		crumb = null, section_obj = null, this_crumb = null;

	//get current section's obj
	section_obj = Plex.adminAdvFilter.sections.getSection(section_name);
	section_obj.hasChanged();
	Plex.adminAdvFilter.change_has_been_made = true;
	//get crumb from this section
	crumb = section_obj.getCrumb(component);

	switch(section_name){
		case 'location':
			this_crumb = _.findWhere(crumb, {value: val});
			$('span[data-tag-id="'+val+'"]').remove();
			Plex.adminAdvFilter.removeTag(val);
			if( crumb.length === 1 ){
				$('.component[data-component="'+component+'"]').find('input[value="all"]').trigger('click');
			}
			break;
		case 'major':
			this_crumb = _.findWhere(crumb, {value: val});
			section_obj.destroyDeptUI(this_crumb.value);
			$('span[data-tag-id="'+val+'"]').remove();
			Plex.adminAdvFilter.removeTag(val);
			if( crumb.length === 1 ){
				$('.component[data-component="'+component+'"]').find('input[value="all"]').trigger('click');
			}
			break;
		case 'scores':
			$('input#'+crumb[0].elem_id).closest('.component').find('.filter-this').val('');
			this_crumb = crumb[0];
			break;
		case 'demographic':
			if( component.toLowerCase() === 'age' ){
				//regardless of if min is empty or max is or both have values, make both values emtpy
				$('input#'+crumb[0].elem_id).closest('.component').find('.filter-this').val('');
				this_crumb = crumb[0];
			}else if( component.toLowerCase() === 'gender' ){
				//regardless, on remove, make 'All' checked
				$('input#all_gender_filter').prop('checked', true);
				this_crumb = crumb[0];
			}else{
				$('span[data-tag-id="'+val+'"]').remove();
				this_crumb = _.findWhere(crumb, {value: val});
				if( crumb.length === 1 )
					$('.component[data-component="'+component+'"]').find('input[value="all"]').trigger('click');
			}
			break;
		case 'uploads':
		case 'educationLevel':
		case 'desiredDegree':
			//make elem unchecked
			$('input#'+crumb[0].elem_id).prop('checked', false);
			this_crumb = crumb[0];
			break;
		case 'militaryAffiliation':
		case 'startDateTerm':
			if( component === 'inMilitary' ){
				this_crumb = crumb[0];
				$('#inMilitary_filter').val('');
			}else if( component === 'startDateTerm' ){
				this_crumb = _.findWhere(crumb, {value: val});
			}else{
				this_crumb = _.findWhere(crumb, {value: val});
				if( crumb.length === 1 )
					$('#militaryAffiliation_filter').val('');
			}
			break;
		case 'profileCompletion':
			$('#'+component+'_filter').val('');
			break;
		case 'financial':
			if( component === 'interested_in_aid' ){
				this_crumb = _.findWhere(crumb, {value: val});
				$('#interested_in_aid').prop('checked', false);
			}

			if( component === 'financial' ) this_crumb = _.findWhere(crumb, {value: val});
			break;
	}

	//remove crumb from array
	if( crumb ) section_obj.removeCrumb(this_crumb);
	//re-render to update view
	Plex.adminAdvFilter.sections.renderCrumbs();
}


//remove crumb event
$(document).on('click', '.tag .remove', function(){
	var _this = $(this).parent();

	Plex.adminAdvFilter.removeCrumb(_this);
});


//select all degree checkboxes
$(document).on('change', '#select_all_degrees', function(){
	if( $(this).is(':checked') ){
		$(this).closest('form').find('.filter-this').prop('checked', true);
	}else{
		$(this).closest('form').find('.filter-this').prop('checked', false);
	}
});


//hide show militaryAffiliation
$(document).on('change', '#inMilitary_filter', function(){
	if( $(this).val() === '1' )
		$('.miliAffili').removeClass('hide');
	else
		$('.miliAffili').addClass('hide');
});




/********************************************************************
	function creates the financial crumb list
	when not Interested in aid
	
*********************************************************************/
Plex.adminAdvFilter.setFinancialCrumbList = function(component, option, section_obj, val, min ,max){
						crumb = new Crumb('financial', component, option, val, min, max);

						var interest = _.findWhere(section_obj.crumbs, {value: 'interested_in_aid'});
				
						if(interest != undefined){
							section_obj.crumbs.length = 0;
							section_obj.crumbs.push(interest);
						}
						else{
							section_obj.crumbs.length = 0;
						}

						//if value is ''  option chosen was 'Select...'
						//just return with list cleared
						if(val === '')
							return;
						
						section_obj.crumbs.push(crumb);

						for(var i = $.inArray(val.trim(), Plex.adminAdvFilter.ranges) + 1; i < Plex.adminAdvFilter.ranges.length; i++){
							
							var ncrumb = new Crumb('financial', component, Plex.adminAdvFilter.ranges_formatted[i], Plex.adminAdvFilter.ranges[i], min, max);
							section_obj.crumbs.push(ncrumb);	
						}

};




// --------------------- filter dialog box functions - start
//open dialog modal
Plex.adminAdvFilter.openDialogModal = function(){
	$('#filter-dialog-modal').foundation('reveal', 'open');
}

//closes dialog modal
Plex.adminAdvFilter.closeDialogModal = function(){
	$('#filter-dialog-modal').foundation('reveal', 'close');
}

//saves filter, closes modal, and changes filter section
$(document).on('click', '#filter-dialog-modal .save', function(){
	var filterData = null, clicked_section = $('.adv-filtering-menu li[data-filter-tab="'+Plex.adminAdvFilter.currentTab+'"]').first();

	Plex.adminAdvFilter.currentTab = Plex.adminAdvFilter.prevTab;
	filterData = Plex.adminAdvFilter.buildJSONDataOfValuesToPost(Plex.adminAdvFilter.currentFormFields);	
	Plex.adminAdvFilter.closeDialogModal();
	Plex.adminAdvFilter.saveFilter(filterData, clicked_section);	
});

//closes modal, does NOT save, and changes filter section
$(document).on('click', '#filter-dialog-modal .discard', function(){
	Plex.adminAdvFilter.resetChanges(Plex.adminAdvFilter.prevTab);
	Plex.adminAdvFilter.getSelectedFilterSection();
	Plex.adminAdvFilter.closeDialogModal();
});

//closes modal and stays on current section
$(document).on('click', '#filter-dialog-modal .cancel', function(){
	Plex.adminAdvFilter.closeDialogModal();
});
// --------------------- filter dialog box functions - end




// ---------------------------- reset all filters click event of this form's fields to empty - start
Plex.adminAdvFilter.resetFields = function(){
	var section = null;

	switch(Plex.adminAdvFilter.currentTab){
		case 'location':
			$('#all_country_filter').prop('checked', true).trigger('change');
			section = Plex.adminAdvFilter.sections.getSection(Plex.adminAdvFilter.currentTab);
			section.removeCrumbBySection(Plex.adminAdvFilter.currentTab);
			Plex.adminAdvFilter.sections.renderCrumbs();
			break;
		case 'startDateTerm': 
			$('#startDateTerm_filter').val('').trigger('change');
			Plex.adminAdvFilter.eraseAndRenderCurrentSection();
			break;
		case 'financial':
			$('#financial_filter').val('').trigger('change');
			$('#interested_in_aid').prop('checked', false).trigger('change');
			Plex.adminAdvFilter.eraseAndRenderCurrentSection();			
			break;
		case 'typeofschool':
			$('input#both_typeofschool').prop('checked', true).trigger('change');
			break;
		case 'major':
			Plex.adminAdvFilter.eraseMajors();
			break;
		case 'scores':
			Plex.adminAdvFilter.eraseScores();
			break;
		case 'uploads':
			Plex.adminAdvFilter.eraseUploads();
			break;
		case 'demographic':
			Plex.adminAdvFilter.eraseDemo();
			break;
		case 'educationLevel':
			Plex.adminAdvFilter.eraseEdu();
			break;
		case 'desiredDegree':
			Plex.adminAdvFilter.eraseDegree();
			break;
		case 'militaryAffiliation':
			Plex.adminAdvFilter.eraseMilitary();
			break;
		case 'profileCompletion':
			Plex.adminAdvFilter.eraseProfile();
			break;
	}

	return Plex.adminAdvFilter.currentFormFields;
};

Plex.adminAdvFilter.eraseBySection = function(){
	var section = null;
	section = Plex.adminAdvFilter.sections.getSection(Plex.adminAdvFilter.currentTab);
	section.removeCrumbBySection(Plex.adminAdvFilter.currentTab);
	Plex.adminAdvFilter.sections.renderCrumbs();
};

Plex.adminAdvFilter.eraseMajors = function(){
	$('#all_department_filter').prop('checked', true).trigger('change');
	Plex.adminAdvFilter.eraseBySection();
};

Plex.adminAdvFilter.eraseProfile = function(){
	$('#profileCompletion_filter').val('');
	Plex.adminAdvFilter.eraseBySection();
};

Plex.adminAdvFilter.eraseMilitary = function(){
	$('#inMilitary_filter').val('').trigger('change');
	Plex.adminAdvFilter.eraseBySection();
};

Plex.adminAdvFilter.eraseDegree = function(){
	Plex.adminAdvFilter.eraseBySection();
	$('.checkbox-filter.filter-this').prop('checked', true).trigger('change');
};

Plex.adminAdvFilter.eraseEdu = function(){
	Plex.adminAdvFilter.eraseBySection();
	$('#hsUsers_filter, #collegeUsers_filter').prop('checked', true).trigger('change');
};

Plex.adminAdvFilter.eraseDemo = function(){
	var fields = $('input.checkbox-filter');

	$('input.text-filter').val('');
	$('#all_gender_filter, #all_eth_filter, #all_rgs_filter').prop('checked', true).trigger('change');
	Plex.adminAdvFilter.eraseBySection();
};

Plex.adminAdvFilter.eraseUploads = function(){
	var fields = $('input.checkbox-filter'), section = null;

	Plex.adminAdvFilter.eraseBySection();

	$.each(fields, function(){
		$(this).prop('checked', true).trigger('change');
	});
};

Plex.adminAdvFilter.eraseScores = function(){
	var fields = $('input.filter-this'), section = null;

	$.each(fields, function(){
		$(this).val('');
	});

	Plex.adminAdvFilter.eraseBySection();
};

Plex.adminAdvFilter.eraseAndRenderCurrentSection = function(){
	var section = null;

	section = Plex.adminAdvFilter.sections.getSection(Plex.adminAdvFilter.currentTab);
	section.removeCrumbByComponent(Plex.adminAdvFilter.currentTab);
	Plex.adminAdvFilter.sections.renderCrumbs();
};

$(document).on('click', '.reset-filters-btn', function(e){
	Plex.adminAdvFilter.resetFilter();
});

// ----------------- COME BACK TO RESET FILTER -----------------
Plex.adminAdvFilter.resetFilter = function(){
	var filterData = null, formFields = [], section = null;

	// if( confirm('Are you sure you want to reset ' + Plex.adminAdvFilter.currentTab.toUpperCase() + ' filter back to default? Click "OK" to confirm.') ){
	// 	formFields = Plex.adminAdvFilter.resetFields(),
	// 	Plex.adminAdvFilter.resetAll();
	// }

	formFields = Plex.adminAdvFilter.resetFields(),
	Plex.adminAdvFilter.resetAll();
}

Plex.adminAdvFilter.resetAll = function(){
	var route = '';

	Plex.adminAdvFilter.showLoader();

	if( Plex.adminAdvFilter.currentTab === 'major' ) route = Plex.adminAdvFilter.reset_filter_route + 'majorDeptDegree';
	else route = Plex.adminAdvFilter.reset_filter_route + Plex.adminAdvFilter.currentTab;

	$.ajax({
		url: route,
		type: 'POST',
		data: {reset: 1},
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		success: function(data){
			if( data === 'done' ){
				Plex.adminAdvFilter.successfullyResetFilterAlert();
				Plex.adminAdvFilter.getNumberOfUsersForFilter(); //adjust filter percentage
			}
			else Plex.adminAdvFilter.erroredResetFilterAlert();

			Plex.adminAdvFilter.change_has_been_made = false;
			Plex.adminAdvFilter.hideLoader();
		},
		error: function(err){
			Plex.adminAdvFilter.erroredResetFilterAlert();
		}
	});
};
// ---------------------------- reset all filters click event of this form's fields to empty - end



// ----------------------------- save this form's fields via ajax post - start

//when save filter btn is clicked, validate form fields first before posting data
$(document).on('click', '.save-filters-btn', function(e){
	var this_forms_fields = $(this).closest('.common-container-for-filter-sections').find('.filter-this');
	var filter_data = {};
	var passes_validation = false;

	passes_validation = Plex.adminAdvFilter.selectFieldValidation(this_forms_fields);

	if( passes_validation ){
		$(this).closest('.common-container-for-filter-sections').find('.select-option-error').slideUp(250);

		if( Plex.adminAdvFilter.currentTab === 'major' ) filter_data = Plex.adminAdvFilter.buildCustomMajorData();
		else filter_data = Plex.adminAdvFilter.buildJSONDataOfValuesToPost(this_forms_fields);

		Plex.adminAdvFilter.saveFilter(filter_data);


	}else{
		//error message
		$(this).closest('.common-container-for-filter-sections').find('.select-option-error').slideDown(250);
	}

	mixpanel.track("Set_Rec_Filter",
		{
			"location": document.body.id
		}
	);
});

//saving filter to db
Plex.adminAdvFilter.saveFilter = function(filter_data, fromDiaglog){
	var saveFilter_route = Plex.adminAdvFilter.save_filter_route + Plex.adminAdvFilter.currentTab,
		section_obj = Plex.adminAdvFilter.sections.getSection(Plex.adminAdvFilter.currentTab);

	Plex.adminAdvFilter.showLoader();

	if( Plex.adminAdvFilter.currentTab === 'major' ) saveFilter_route = Plex.adminAdvFilter.save_filter_route + 'majorDeptDegree';

	console.log('filter: ', filter_data);

	$.ajax({
		url: saveFilter_route,
		type: 'POST',
		data: filter_data,
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	}).done(function(ret){
		Plex.adminAdvFilter.successfullySavedFilterAlert();
		Plex.adminAdvFilter.change_has_been_made = false;
		Plex.adminAdvFilter.getNumberOfUsersForFilter();
		section_obj.initStatus(!1);

		if( fromDiaglog ) fromDiaglog.trigger('click');
		else Plex.adminAdvFilter.hideLoader();
	});
}

Plex.adminAdvFilter.buildCustomMajorData = function(){
	var fields = $('form').find(':input'),
		all_items = $('.dept-item').find(':input'), obj = {},
		dataArr = [], _this = null, dept = '', major = '', degree = '',
		dept_inEx_option = $('input[type="radio"][name="department"]:checked').val(),
		name = '', tmp_major = {}, depts, mjrs, deptsOnly, majorsOnly, majorDegrees, deptDegrees;

	//function to filter by departments only
	deptsOnly = function(i){
		return $(this).attr('name') === 'is_department';
	};

	//function to filter by this department's selected majors
	majorsOnly = function(i){
		return $(this).data('childof') === _this.val();
	};

	//function to filter by a specific department's selected degree
	deptDegrees = function(){
		return $(this).data('department-degreeof') === _this.val() && $(this).is(':checked');
	};

	//function to filter by a specific major's selected degree
	majorDegrees = function(){
		return $(this).data('major-degreeof') === self.val() && $(this).is(':checked');
	};

	//loops through each selected degree, add its id to a copy of the arg obj (tmp_obj) and 
	//adds the new obj to a temp arr which is concatenated with dataArr
	addSelectedDegrees = function(degrees, tmp_obj){
		var arr = [];

		$.each(degrees, function(){
			elem = $(this); //degree elem
			obj_copy = _.extend({}, tmp_obj); //make a copy oftmp_obj 

			obj_copy.degree_id = +(elem.val());
			arr.push(obj_copy);
		});

		return concatWith_dataArr(arr);
	};

	//returns a new copy of dataArr with param concatenated to it
	concatWith_dataArr = function(arr){
		return dataArr.concat(arr);
	};

	//filter by depts only
	depts = all_items.filter(deptsOnly);

	//loop through each department selected
	$.each(depts, function(){
		_this = $(this); //dept elem
		obj = {department_id: '', major_id: '', degree_id: '', in_ex: ''}; //each loop, init new obj

		obj.in_ex = dept_inEx_option; //init object with the selected include/exclude value
		obj.department_id = _this.data('id'); //init obj with this department id

		// //filter by dataArr of this department's majors
		mjrs = all_items.filter(majorsOnly);

		// //loop through this department's selected majors
		if( mjrs.length > 0 ){
			//update include/exclude prop with the majors include/exclude option value
			obj.in_ex = $('.dept-item[data-dept="'+_this.val()+'"]').find('input[type="radio"]:checked').val();

			$.each(mjrs, function(){
				self = $(this); //major elem
				obj_with_major = _.extend({}, obj); //make copy of obj
				obj_with_major.major_id = self.data('id'); //set new obj with this major id

				//filter by dataArr of this major's selected degree options
				degrees = all_items.filter(majorDegrees);

				//loop through this major's selected degree options
				if( degrees.length > 0 ) dataArr = addSelectedDegrees(degrees, obj_with_major);
				else dataArr.push(obj_with_major);

			});
		}else{
			//filter by dataArr of this department's degrees
			degrees = all_items.filter(deptDegrees);

			//addSelectedDegrees loops through each selected degree and adds to dataArr
			if( degrees.length > 0 ) dataArr = addSelectedDegrees(degrees, obj);
			else dataArr.push(obj);
		}

	});

	arr1 = _.filter(dataArr.slice(), {in_ex: 'include'});
	arr2 = _.filter(dataArr.slice(), {in_ex: 'exclude'});
	dataArr = arr1.concat(arr2);

	console.log('data: ', dataArr);
	return {data: dataArr};
};

Plex.adminAdvFilter.showLoader = function(){
	$('.targeting-ajax-loader').show();
};
Plex.adminAdvFilter.hideLoader = function(){
	$('.targeting-ajax-loader').hide();
};

//gets the percentage of user recommended based on filter changes
Plex.adminAdvFilter.getNumberOfUsersForFilter = function(){
	$.ajax({
		url: Plex.adminAdvFilter.getRecommendationPercent_route,
		type: 'GET',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	}).done(function(percent){
		$('.recomm-meter-container .meter').animate({width: percent + '%'});
	});
}


//successfully saved filter alert
Plex.adminAdvFilter.successfullySavedFilterAlert = function(){
	var alert_msg = {
        textColor: '#fff',
        backGroundColor : '#26b24b',
        msg: Plex.adminAdvFilter.currentTab_text + ' filter has been successfully saved! <br/> Note:  Your targeting changes may impact some students.  As a result you may not be able to find them on the current portal.  You can always find the student in the general portal.',
        type: 'soft',
        dur : 10000
    };	

    topAlert(alert_msg, true);
}

//successfully saved filter alert
Plex.adminAdvFilter.successfullyResetFilterAlert = function(){
	var alert_msg = {
        textColor: '#fff',
        backGroundColor : '#26b24b',
        msg: Plex.adminAdvFilter.currentTab_text + ' filter has been successfully reset!',
        type: 'soft',
        dur : 5000
    };

    topAlert(alert_msg);
}

//successfully saved filter alert
Plex.adminAdvFilter.erroredResetFilterAlert = function(){
	var alert_msg = {
        textColor: '#fff',
        backGroundColor : '#717171',
        msg: 'Hmm, there seems to be an issue resetting these filters. Please refresh the page and try again.',
        type: 'soft',
        dur : 5000
    };

    topAlert(alert_msg);
}


//prototype method to capitalize the first letter of the string it is chained to
String.prototype.capitalize = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
}


//organize form fields, setting up dynamic key names and values making it ajax ready
Plex.adminAdvFilter.buildJSONDataOfValuesToPost = function(form_fields){
	
	var values_to_send = {}, tmp_key = null, skills = [], languages = [], component = null,
		interests = [], is_skillInterest = false, section_obj = null, crumbs = null, arr = [];

	$.each(form_fields, function(){
		var _this = $(this);

		if( _this.hasClass('select-filter') ){
			//create array of cities selected and add 'include' or 'exclude'
			tmp_key = _this.attr('name');
			if( Plex.adminAdvFilter.currentTab === 'militaryAffiliation' ){
				crumbs = null;
				if( _this.hasClass('inMili') ){
					arr = Plex.adminAdvFilter.buildSelectFilterArrayWithCrumbs('inMilitary');
					arr = arr[0];
				}else{
					arr = Plex.adminAdvFilter.buildSelectFilterArrayWithCrumbs('militaryAffiliation');
				}
				values_to_send[tmp_key] = arr;
			}else if( Plex.adminAdvFilter.currentTab === 'profileCompletion' ){
				values_to_send[tmp_key] = _this.val();
			}else if( Plex.adminAdvFilter.currentTab === 'startDateTerm' || Plex.adminAdvFilter.currentTab === 'financial' ){
				values_to_send[tmp_key] = Plex.adminAdvFilter.buildSelectFilterArrayWithCrumbs(Plex.adminAdvFilter.currentTab);
			}else{
				values_to_send[tmp_key] = Plex.adminAdvFilter.buildSelectFilterArray(_this.attr('name'));
			}
		}else if( _this.hasClass('checkbox-filter') || _this.hasClass('radio-filter')  ){
			//everything else is either a checkbox, radio btn, or text field
			tmp_key = _this.attr('id');
			values_to_send[tmp_key] = _this.is(':checked');
		}else if( _this.hasClass('text-filter') ){
			if( _this.hasClass('skillsInterests') ){
				is_skillInterest = true;
				if( _this.hasClass('skill-filter') && _this.val() !== '' )
					skills.push(_this.val());
				else if( _this.hasClass('interest-filter') && _this.val() !== '' )
					interests.push(_this.val());
				else if( _this.hasClass('language-filter') && _this.val() !== '' )
					languages.push(_this.val());
			}else{
				tmp_key = _this.attr('id');
				values_to_send[tmp_key] = _this.val();
			}
		}
	});

	if( is_skillInterest ){
		values_to_send['skills_filter'] = skills;
		values_to_send['interests_filter'] = interests;
		values_to_send['language_filter'] = languages;
	}

	return values_to_send;
}

Plex.adminAdvFilter.buildSelectFilterArrayWithCrumbs = function(comp){
	var tmp_array = [], section_obj = null, crumbs = null;

	section_obj = Plex.adminAdvFilter.sections.getSection(Plex.adminAdvFilter.currentTab);
	crumbs = _.where(section_obj.crumbs, {component: comp});
	tmp_arr = _.pluck(crumbs, 'value');

	return tmp_arr;
}

//filter through the tags array and 'this type', put values in temp array and return that array
Plex.adminAdvFilter.buildSelectFilterArray = function(this_type){
	var tmp_array = [];

	tmp_array = _.chain(Plex.adminAdvFilter.filter_tags_array)
		.filter({type: this_type})
		.pluck('val')
		.value();

	return tmp_array;
}


// select field validation - if include/exclude is checked, select option must be chosen before being able to save
Plex.adminAdvFilter.selectFieldValidation = function(form_fields){
	var is_valid = true;

	$.each(form_fields, function(){
		var _this = $(this);	
		
		//radio button validation
		if( _this.hasClass('radio-filter') ){
			var _this_radio_val = _this.val();
			var _this_radio_type = _this.attr('name');

			if( _this.is(':checked') ){
				if( _this.val() !== 'all' && (_this.val() === 'include' || _this.val() === 'exclude' ) ){
					if( _.isEmpty(_.filter(Plex.adminAdvFilter.filter_tags_array, {type: _this.attr('name')})) ){
						is_valid = false;
						return false;
					}
				}
			}
		//text field validation
		}else if( _this.hasClass('text-filter') && (_this.hasClass('scores') || _this.hasClass('age')) ){
			if( _this.parent().hasClass('error') ){
				is_valid = false;
				return false;
			}
		}else if( _this.hasClass('skillsInterests') ){
			if( _this.val() === '' ){
				is_valid = false;
			}else{
				//as long as one is not empty, break out of loop and pass validation
				is_valid = true;
				return false;
			}
		}
	});

	//for Scores section ONLY! - check to make sure the MIN values aren't greater than the MAX values
	if( is_valid && (Plex.adminAdvFilter.currentTab === 'scores' || Plex.adminAdvFilter.currentTab === 'demographic') ){
		if( Plex.adminAdvFilter.minMaxComparison() ){
			//if in here, means that a min value was found to be greater than it's related max value
			is_valid = false;
		}
	}

	//for education level section only!
	if( is_valid && Plex.adminAdvFilter.currentTab == 'educationLevel' ){
		if(!Plex.adminAdvFilter.atleastOneCheckboxChecked(Plex.adminAdvFilter.currentFormFields)){
			is_valid = false;
		}
	}

	if( Plex.adminAdvFilter.currentTab === 'militaryAffiliation' ){
		if( $(form_fields[0]).val() === '' )
			is_valid = false;
	}

	//majors is a little different so for now, no validation
	if( Plex.adminAdvFilter.currentTab === 'major' ) is_valid = true;

	Plex.adminAdvFilter.showHideErrors(is_valid);	

	return is_valid;
}


//show hide error messages
Plex.adminAdvFilter.showHideErrors = function(is_valid){
	if( !is_valid ){
		$('.minMaxError').slideDown(250);
	}else{
		$('.minMaxError').slideUp(250);
	}
}


//check if at least one checkbox is checked
Plex.adminAdvFilter.atleastOneCheckboxChecked = function(checkboxes){
	var atleast_one_checked = false;

	$.each(checkboxes, function(){
		if( $(this).is(':checked') ){
			atleast_one_checked = true;
			return true; //break out once one passes
		}
	});

	return atleast_one_checked;
}


//take the organized object array and compare the min/max props of each obj
Plex.adminAdvFilter.minMaxComparison = function(){

	//calls a function that organizes input values into an array of objects
	var minMax_object_list = Plex.adminAdvFilter.organizedMinMaxValues();
	var min_isGreaterThan_max = false;

	//loop through each object to check if min is greater than or equal to max - if so, break out and return true
	var returned_obj = _.find(minMax_object_list, function(obj, iterater){
		if( obj.min >= obj.max ){
			min_isGreaterThan_max = true;
			return true;
		}
	});

	return min_isGreaterThan_max;
}


//organize scores form field values into separate objects; so for each type, put the min and max in their own obj and put that in a temp array
Plex.adminAdvFilter.organizedMinMaxValues = function(){
	var minMax_array = [];
	tmp_min = '';
	tmp_obj = null;

	$.each(Plex.adminAdvFilter.currentFormFields, function(){
		//check if field is of type text
		if( $(this).attr('type') == 'text' ){
			//check if it is the min field or max field
			if( $(this).data('scores') === 'min' ){
				tmp_min = parseFloat($(this).val());
			}else{
				//create a temp obj with max/min values used to compare in minMaxComparison function
				tmp_obj = {min: tmp_min, max: parseFloat($(this).val())};
				minMax_array.push(tmp_obj);
			}
		}
	});

	return minMax_array;
}
// ----------------------------- save this form's fields via ajax post - end




// -------------------------------- create filter tags for select drop down options - start
$(document).on('change', '.adv-filtering-section-container select', function(){
	var _this = $(this);
	var this_value = $(this).val();
	var id = $('option[value="'+this_value+'"]', this).data('department-id');
	var this_text = $('option[value="'+this_value+'"]', this).text();
	var this_type = $(this).attr('name');
	var duplicate_found = null, section = null;

	section = Plex.adminAdvFilter.sections.getSection(Plex.adminAdvFilter.currentTab);

	//don't save the tag if it's the default 'Select..' text
	if( this_text !== 'Select..' && this_value !== '' ){
		duplicate_found = _.findWhere(section.crumbs, {val: this_value});

		//if a certain tag was already found, dont save it again
		if( !duplicate_found ){
			Plex.adminAdvFilter.saveTag(this_text, this_value, this_type);
			if( this_type === 'department' ){
				section.buildDeptUI(id);
			}
		}

		Plex.adminAdvFilter.addTagToCorrectViewContainer($(this), this_type);
	}
});


//remove dept crumb and ui
$(document).on('click', '.remove-dept', function(){
	var _this = $(this), name = _this.closest('.dept-item').data('dept'),
		dept_item = $('.dept-item[data-dept]'),
		section = null, major_list = _this.closest('.major-list');

		//get current section
		section = Plex.adminAdvFilter.sections.getSection(Plex.adminAdvFilter.currentTab);

		//if section found, destroy dept ui and remove from crumb list
		if( section ){
			section.removeCrumbByValue(name);
			Plex.adminAdvFilter.sections.renderCrumbs();

			if( major_list.length > 0 ){
				if( major_list.find('.dept-item').length === 1 ){
					major_list.closest('.major-pane.component').find('input[value="all"]').prop('checked', true).trigger('change');
					section.showDepartmentDegreeOptions(major_list);
				}
			}

			section.destroyDeptUI(name);
			if( section.crumbs.length === 0 ) $('#all_department_filter').prop('checked', true).trigger('change');
		}
});


$(document).on('click', '.show-major', function(){
	var _this = $(this), pane = _this.closest('.dept-item').find('.major-pane'),
		dept = _this.closest('.dept-item').data('dept');

	if( _this.hasClass('needs-init') ){
		Plex.adminAdvFilter.sections.initMajorBuild(dept);
		_this.removeClass('needs-init');
	}

	_this.toggleClass('open');
	if( _this.hasClass('open') ) pane.slideDown(250);
	else pane.slideUp(250);
});


$(document).on('change', '.toggle-controller', function(){
	var _this = $(this), name = _this.attr('name');

	if( _this.is(':checked') ) $('input[type="checkbox"][name="'+name+'"]:not(.toggle-controller)').prop('checked', true);
	else $('input[type="checkbox"][name="'+name+'"]:not(.toggle-controller)').prop('checked', false);
});


$(document).on('change', 'input[type="checkbox"].deg-option', function(){
	var _this = $(this), name = _this.attr('name'),
		toggler = $('input[type="checkbox"][name="'+name+'"].toggle-controller');

	if( !_this.is(':checked') ) toggler.prop('checked', false);
	else{
		//get all deg-option of this same name and filter by not checked
		all = $('input[type="checkbox"][name="'+name+'"].deg-option').filter(function(){
			return !$(this).is(':checked');
		});

		//if they are all checked, make the toggler true
		if( all.length === 0 ) toggler.prop('checked', true);
	}
});


//remove tag click event
$(document).on('click', '.remove-tag', function(){
	var tag_id = $(this).parent().data('tag-id'), related_crumb = null;

	Plex.adminAdvFilter.removeTag(tag_id);
	$(this).parent().remove();

	related_crumb = $('.filter-crumb-list .tag[data-tag-val="'+tag_id+'"]');
	Plex.adminAdvFilter.removeCrumb(related_crumb);

	//if tag is United States, then close States/City options
	if( tag_id == 'United States' ){
		Plex.adminAdvFilter.toggleCountrySection('close');
	}
});


$(document).on('change', '#state_filter', function(){
    var _this = $(this);

    if(_this.val() != ''){
       Plex.adminAdvFilter.populateCityBasedOnState(_this.val());
    }
});

//save tags in global array
Plex.adminAdvFilter.saveTag = function(text, val, type){
	var tag = {};
	tag.name = text;
	tag.val = val;
	tag.type = type;
	Plex.adminAdvFilter.filter_tags_array.push(tag);
}


//remove tags from global array
Plex.adminAdvFilter.removeTag = function(tag_id){
	Plex.adminAdvFilter.filter_tags_array = _.reject(Plex.adminAdvFilter.filter_tags_array, {val: tag_id});
}


//remove multiple tags by type
Plex.adminAdvFilter.removeMultipleTags = function(tag_type){
	Plex.adminAdvFilter.filter_tags_array = _.reject(Plex.adminAdvFilter.filter_tags_array, {type: tag_type});
}


//no duplicate tags
Plex.adminAdvFilter.noDuplicateObjects = function(obj_array, obj_val, obj_type){
	var result = null;
	result = _.findWhere(obj_array, {val: obj_val, type: obj_type});
	return result === undefined ? false : true;
}


//filter tags by type and inject html into appropriate tag list
Plex.adminAdvFilter.addTagToCorrectViewContainer = function(elem, this_type){
	var tmp_arr = _.filter(Plex.adminAdvFilter.filter_tags_array, {type: this_type});
	$(elem).closest('.contains-tags-row').find('.filter-tags-list').html( Plex.adminAdvFilter.buildFilterTags(tmp_arr) );	
}


//build tag html to display on the page
Plex.adminAdvFilter.buildFilterTags = function(tag_list){
	var html_tags = '';

	$(tag_list).each(function(tag){
		html_tags += '<span class="advFilter-tag" data-tag-id="'+this.val+'" data-type-id="'+this.type+'">';
		html_tags += 	this.name + '<span class="remove-tag"> x </span>';
		html_tags += '</span>';
	});	

	return html_tags;
}

//check if tags exist on section load
Plex.adminAdvFilter.checkIfTagsExistOnLoad = function(){
	var tags_lists_found = $('.filter-page-section .filter-tags-list');
	var tags_exist_onLoad = $('.filter-page-section .filter-tags-list').has('span');

	if( tags_lists_found.length > 0 ){
		if( tags_exist_onLoad.length > 0 ){
			//has at least one tag
			var tags = tags_exist_onLoad.find('span.advFilter-tag');
			var tag_name = '';
			var tag_type = '';

			//for each tag found, save them
			$.each(tags, function(){
				tag_name = $(this).data('tag-id');
				tag_type = $(this).data('type-id');
				//using tag_name for both tag name and tag id - see params of saveTag()
				Plex.adminAdvFilter.saveTag(tag_name, tag_name, tag_type);
			});
		}
	}
}
// -------------------------------- create filter tags for select drop down options - end




// -------------------------------- dynamically adding new text field on input change - start
$(document).on('keypress', '.achievements-col input', function(){
	var _this = $(this);
	var new_field = '';
	var has_created_new_field = _this.data('new-field-created');
	var field_name = _this.attr('name');

	if( _this.val() != '' && !has_created_new_field ){
		_this.data('new-field-created', true);
		new_field = Plex.adminAdvFilter.buildNewInputField(field_name);
		_this.closest('.achievements-col').append(new_field);
	}

});


//remove this achievement
$(document).on('click', '.remove-achievement-btn', function(){
	if( $(this).hasClass('orig') ){
		$(this).closest('.orig-field').find('input').val('');
	}else{
		$(this).closest('.new-achievement-field').remove();
	}
});


//building new input field for skills/interests section
Plex.adminAdvFilter.buildNewInputField = function(name){
	var input_field = '';

	input_field += '<div class="new-achievement-field">';
	input_field += 		'<input class="text-filter filter-this '+name+'-filter skillsInterests" name="'+name+'" type="text" placeholder="Enter '+name+'" data-new-field-created="false" />';
	input_field += 		'<div class="remove-achievement-btn"> X </div>'
	input_field += '<div>';

	return input_field;
}
// -------------------------------- dynamically adding new text field on input change - end



//toggle text when radio buttons are toggled
$(document).on('change', '.adv-filtering-section-container .radio-filter', function(){
	var txt = '', _this = $(this);

	if( _this.val() == 'exclude' ){
		txt = 'excluding';
	}else{
		txt = 'including';
	}

	if( _this.val() == 'all' ){
		//if all states are chosen, force all cities
		if( Plex.adminAdvFilter.currentTab === 'location' && _this.attr('id') === 'all_state_filter' ){
			$('#all_city_filter').trigger('click');
			_this.parent().find('select').val('').parent().find('.filter-tags-list').html('<small>No state selected yet.</small>');
			Plex.adminAdvFilter.removeMultipleTags('state');
			$('#all_city_filter').parent().find('select').val('').parent().find('.filter-tags-list').html('<small>No city selected yet.</small>');
			Plex.adminAdvFilter.removeMultipleTags('city');

		}else if( Plex.adminAdvFilter.currentTab === 'location' && _this.attr('id') === 'all_city_filter' ){
			_this.parent().find('select').val('').parent().find('.filter-tags-list').html('<small>No city selected yet.</small>');
			Plex.adminAdvFilter.removeMultipleTags('city');

		}else if( Plex.adminAdvFilter.currentTab === 'location' && _this.attr('id') === 'all_country_filter' ){
			//if all countries are chosen, hide state and city
			_this.parent().find('select').val('').parent().find('.filter-tags-list').html('<small>No country selected yet.</small>');
			Plex.adminAdvFilter.removeMultipleTags('country');
			Plex.adminAdvFilter.toggleUS();

		}else if( Plex.adminAdvFilter.currentTab === 'major' && _this.attr('id') === 'all_department_filter' ){
			section = Plex.adminAdvFilter.sections.getSection('major');
			section.removeCrumbByComponent('department');
			section.removeCrumbByComponent('major');

			Plex.adminAdvFilter.sections.renderCrumbs();
			$('.dept-list').html('');
			$('#specificDepartment_filter').val('');

		}else if( Plex.adminAdvFilter.currentTab === 'major' && _this.attr('id').indexOf('_major') > -1 ){
			//if radio btn = all and tab = major and id = anything appended with _major - remove just major crumbs
			section = Plex.adminAdvFilter.sections.getSection(Plex.adminAdvFilter.currentTab);
			section.removeCrumbByComponent('major');
			section.showDepartmentDegreeOptions(_this);

			Plex.adminAdvFilter.sections.renderCrumbs();
			_this.parent().find('select[name="major"]').val('').parent().find('.major-list').html('').parent().slideUp(250); //remove all listed major items
			
		}else if( Plex.adminAdvFilter.currentTab === 'demographic' && _this.attr('id') === 'all_eth_filter' ){
			_this.parent().find('select').val('').parent().find('.filter-tags-list').html('<small>No ethinicity selected yet.</small>');
			Plex.adminAdvFilter.removeMultipleTags('ethnicity');
		}

		_this.closest('.contains-tags-row').find('.selection-row').slideUp(250);

	}else if( $('#all_state_filter').is(':checked') && ( _this.attr('id') === 'include_city_filter' || _this.attr('id') === 'exclude_city_filter' ) ){
		//if State filter by ALL is checked, don't allow user to include/exclude city
		$('#all_city_filter').trigger('click');
		$('.filter-page-section .no-city-if-all-state-error').removeClass('hidden');
		setTimeout(function(){
			$('.no-city-if-all-state-error').addClass('hidden');
		}, 5000);

	}else if( !$('#all_department_filter').is(':checked') && _this.attr('id').indexOf('_major') > -1 ){
		_this.parent().find('.hideOnLoad').slideDown(250);
	}else{
		_this.closest('.contains-tags-row').find('.selection-row').slideDown(250);
	}

	_this.closest('.contains-tags-row').find('.include-exclude-txt').html(txt);
});


$(document).on('click', 'input.radio-filter[name="country"]', function(){
	var country_toggle = $(this).val() === 'all' ? 'close' : 'open';

	if( country_toggle !== 'open' ){
		Plex.adminAdvFilter.toggleUS();
	}
});


$(document).on('change', 'select[name="country"]', function(){
	Plex.adminAdvFilter.toggleUS();
});

Plex.adminAdvFilter.toggleUS = function(){
	if( Plex.adminAdvFilter.hasUS() )
		Plex.adminAdvFilter.toggleCountrySection('open');
	else
		Plex.adminAdvFilter.toggleCountrySection('close');
}

Plex.adminAdvFilter.hasUS = function(){
	var us_found = null;
	us_found = _.findWhere(Plex.adminAdvFilter.filter_tags_array, {type: 'country', val: 'United States'});
	return _.isObject(us_found);
}

Plex.adminAdvFilter.toggleCountrySection = function(toggle){
	if(toggle === 'open')
		$('#all_state_filter, #all_city_filter').closest('.contains-tags-row').slideDown(50);
	else
		$('#all_state_filter, #all_city_filter').trigger('click').closest('.contains-tags-row').slideUp(50);
}

Plex.adminAdvFilter.countryOnLoad = function(){
	if( $('#all_country_filter').is(':checked') || !Plex.adminAdvFilter.hasUS() ){
		Plex.adminAdvFilter.toggleCountrySection('close');
	}
}

Plex.adminAdvFilter.populateCityBasedOnState = function(stateAbbr){
    $.getJSON("/ajax/homepage/getCityByState/"+stateAbbr, function(result) {
        var options = $("#city_filter");
        options.find('option').remove();  
        options.append($("<option />").val('').text('Select..'));
        $.each(result, function(key, value) {
            options.append($("<option />").val(value).text(value));
        });
    });
}

Plex.adminAdvFilter.populateMajorBasedOnDepartment = function(departmentId, dept){
    $.getJSON("/ajax/getMajorByDepartmentWithIds/"+departmentId, function(result) {
        var options = $('.dept-item[data-dept="'+dept+'"]').find('.major-pane-selector');
        options.find('option').remove();  

        if( _.has(result, "") ){
        	result = _.omit(_.extend({}, result), "");
        	options.append( $('<option value="" selected="selected" disabled="disabled">Select...</option>') )
        }

        $.each(result, function(key, value) {
            options.append($('<option data-major-id="'+key+'" />').val(value).text(value));
        });
    });
}