Plex.agencyInquiries = {
	remove_this : 0,
    unique: 1,
    uniqueMatched: 1,
    activeTab: null,
    tags: [],
	removeFromApproveSuccessMsg : {
        textColor: '#fff',
        backGroundColor : 'green',
        msg: 'Student has been successfully removed from your Handshakes list',
        type: 'soft',
        dur : 5000
    },
    removeFromPendingSuccessMsg : {
        textColor: '#fff',
        backGroundColor : 'green',
        msg: 'Student has been successfully removed from your Pending list',
        type: 'soft',
        dur : 5000
    },
    addedFromInquiriesToApproved : {
        textColor: '#fff',
        backGroundColor : 'green',
        msg: 'Student has been added to your Handshakes list',
        type: 'soft',
        dur : 5000
    },
    addedFromRecommendedToPending : {
    	textColor: '#fff',
        backGroundColor : 'green',
        msg: 'Student has been added to your Pending list',
        type: 'soft',
        dur : 5000
    },
    fileRemoveSuccessMsg: {
        textColor: '#fff',
        backGroundColor : 'green',
        msg: 'File has successfully been removed',
        type: 'soft',
        dur : 6000
    },
    restoreFromRemovedMsg : {
        textColor: '#fff',
        backGroundColor : 'green',
        msg: 'Student has been successfully restored from your removed list, and is in your Handshakes list',
        type: 'soft',
        dur : 5000
    },
    fileUploadSuccessMsg: {
        textColor: '#fff',
        backGroundColor : 'green',
        msg: 'File has successfully been uploaded',
        type: 'soft',
        dur : 6000
    },
    bucketChangeSuccess: {
        textColor: '#fff',
        backGroundColor : 'green',
        msg: 'Student has successfully been moved to {{bucket}}',
        type: 'soft',
        dur : 6000
    },
    undoFailed: {
        textColor: '#fff',
        bkg: '#DD1144',
        msg: 'Unable to undo this student. Try again later or move the student first.',
        type: 'soft',
        dur: 6000
    },
    error: {
        textColor: '#fff',
        bkg: '#DD1144',
        msg: 'An error occured, try again later',
        type: 'soft',
        dur: 7000
    },
    saveNotesInterval : '',
    sections: null,
    ranges: ['0.00','0 - 5,000','5,000 - 10,000', '10,000 - 20,000', '20,000 - 30,000', '30,000 - 50,000', '50,000'],
    ranges_formatted:  ['$0','$0 - $5,000','$5,000 - $10,000', '$10,000 - $20,000', '$20,000 - $30,000', '$30,000 - $50,000', '$50,000+'],
}


$(document).ready(function() {
 	var student_owl = $('.student-profile-also-interested-in');
	student_owl.owlCarousel({
		autoPlay: false,
		items : 4,
		itemsDesktop : [1199,3],
		itemsDesktopSmall : [979,3],
		itemsTablet : [768, 3],
		itemsMobile : [479, 1],
		pagination : false,
		beforeMove : function(elem){
			var itemsVisible = this.visibleItems;
		    var totalItems = this.itemsAmount;
		    var itemsAllowed = this.options.items;

		    Plex.agencyInquiries.hideCompetitionCarouselArrows(totalItems, itemsAllowed);
		}
	});

    Plex.agencyInquiries.majorsList = new MajorCrumbList;

    Plex.agencyInquiries.initSections();

    Plex.agencyInquiries.closeAllSearchFilterBoxes();

	//click event for competition carousel arrow clicks
	$('.competitor-carousel-arrow').click(function(){
		Plex.agencyInquiries.moveCompetitionCarousel(student_owl, this);
	});

	var hasResultsOnLoad = $('.hasResults').data('last-results');

	Plex.agencyInquiries.injectLoadMoreButton();
	Plex.agencyInquiries.hasResultsCheck(hasResultsOnLoad);

    $('#current-bucket-results').html($('.hasResults').last().data('currently-viewing'))
    $('#total-bucket-results').html($('.hasResults').last().data('total-results'));

});//end of doc ready

///////// show or hide address in dropdown profile
$(document).on('click', '.add-show-more', function(){
    var that = $(this);
    var text = that.text();

    that.parent().find('.address-more-info').toggle();
    if(text === 'Show')
        that.text('Hide');
    else
        that.text('Show');
});

// -- load more functions
$(document).on('click', '.each-inquirie-container .load-more-btn.active', function(e){
	e.preventDefault();
	var _this = $(this);
	var container = _this.closest('.each-inquirie-container');
	var page_type = container.data('page-type').split('-');
	var admin = page_type[0];
	var page = page_type[1];

	Plex.agencyInquiries.loadMoreResults(page, admin, container);
});

$(document).on('click', '.agency-inquiry-options .request-leads-btn', function(event){
    event.preventDefault();
    var modal = $('#request-leads-modal'),
        new_leads_number = modal.find('.new-leads-number'),
        new_applications_number = modal.find('.new-applications-number'),
        error_msg = modal.find('.error-msg'),
        request_failure_div = $('#leads-request-failure'),
        request_success_div = $('#leads-request-successful');

    Plex.agencyInquiries.showLoader();

    $.ajax({
        url: '/agency/generateAgencyLeads',
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(data) {
        data = JSON.parse(data);
        
        if (data.status == 'success') {
            request_failure_div.hide();
            new_leads_number.html(data.num_of_leads);
            new_applications_number.html(data.num_of_applications);
            request_success_div.fadeIn(200);
            modal.foundation('reveal', 'open');

        } else if (data.status == 'failed') {
            request_success_div.hide();
            request_failure_div.find('.error-msg').html(data.error_msg);
            request_failure_div.fadeIn(200);
            modal.foundation('reveal', 'open');

        }
        Plex.agencyInquiries.hideLoader();

        // Do nothing on invalid response
    });
});

$(document).on('click', '#leads-request-successful .reload-btn', function(event){
    Plex.agencyInquiries.showLoader();
    $('#request-leads-modal').foundation('reveal', 'close');
    window.location.reload();
});

//ajax call to retrieve more results, if any
Plex.agencyInquiries.loadMoreResults = function(page, admin){
	Plex.agencyInquiries.displayMoreResults($('#displayOption').val());
}

//init section obj start
Plex.agencyInquiries.initSections = function(){
    var sections = $('li.search-tab'), tab = null;

    Plex.agencyInquiries.sections = new SearchSectionList();

    $.each(sections, function(val, index){
        tab = $(this).data('search-tab');
        Plex.agencyInquiries.sections.addSection( new SearchSection(tab) );
    });
}

var SearchSection = function SearchSection(name){
    this.name = name;
    this.changed = !1;
    // this.locked = !1;
}

SearchSection.prototype.hasChanged = function(){
    this.changed = !0;
    Plex.agencyInquiries.sections.updateView();
}

SearchSection.prototype.reset = function(){
    this.changed = !1;
    Plex.agencyInquiries.sections.updateView();
}

var SearchSectionList = function SearchSectionList(){
    this.list = [];
}

SearchSectionList.prototype.addSection = function(section){
    this.list.push(section);
}

SearchSectionList.prototype.updateView = function(){
    var elem = null;
    _.each(this.list, function(obj, index, arr){
        elem = $('li.search-tab[data-search-tab="'+obj.name+'"] .change-icon');
        if( obj.changed ) elem.removeClass('hide');
        else if( !obj.changed ){
            if( elem.is(':visible') ) elem.addClass('hide');
        }
    });
}

SearchSectionList.prototype.resetAll = function(){
    _.each(this.list, function(obj){
        if( obj.changed ) obj.reset();
    });
}

SearchSectionList.prototype.getSection = function(section_name){
    return _.findWhere(this.list, {name: section_name});
}

SearchSectionList.prototype.getChangedSections = function(bool){
    return _.where(this.list, {changed: bool});
}
// -- init section obj end

// -- update search results
$(document).on('click', '.update-search-btn', function(e){
    e.preventDefault();
    Plex.agencyInquiries.updateSearchResults($(this));
});

// -- clear filter
$(document).on('click', '.clear-search-btn', function(e){
    e.preventDefault();
    Plex.agencyInquiries.clearFilter();
});

$(document).on('click', '.uploads-filter-select-all-btn', function(event){
    Plex.agencyInquiries.toggleUploadsFilterSelectAll(event);
});

$(document).on('change', '#displayOption', function(event){
    Plex.agencyInquiries.displayMoreResults($(this).val());
});

Plex.agencyInquiries.displayMoreResults = function(num_of_results) {
    var page = $('body').attr('id').split('-')[1];
    
    Plex.agencyInquiries.showLoader();

    $.ajax({
        url: '/agency/inquiries/' + page + '/true?display=' + num_of_results + '&init=0',
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(data) {
        $('.each-inquirie-container').append(data);
        Plex.agencyInquiries.hideLoader();

        // Remove and append new loadmorebutton
        $('.each-inquirie-container .load-more-container').remove();

        Plex.agencyInquiries.injectLoadMoreButton();

        Plex.agencyInquiries.hasResultsCheck($('.hasResults').last().data('last-results'));

        $('#current-bucket-results').html($('.hasResults').last().data('currently-viewing'))
        $('#total-bucket-results').html($('.hasResults').last().data('total-results'));

    });
}

// -- run filter audience
Plex.agencyInquiries.updateSearchResults = function(elem) {
    var current_page = $('.update-search-btn').closest('body').attr('id');
    var route = '/agency/inquiries/updateSearchResults';
    var form = elem.closest('form');
    var formdata = new FormData(form[0]);

    // this is an ajax call, but it will reset sorting and display
    formdata.append('init', '1');

    var formIs = '';
    var hasResults = 1;

    formIs = Plex.agencyInquiries.validateEntireForm();

    if( formIs != 'clean' ) {
        if( Plex.agencyInquiries.activeTab !== formIs )
            $('li[data-search-tab="'+formIs+'"] > a').trigger('click');

    } else {
        //start ajax loader
        Plex.agencyInquiries.showLoader();

        // close filter options
        $('.filter-result a.ms-btns').trigger('click');

        $.ajax({
            url: route + '/' + current_page,
            type: 'POST',
            data: formdata,
            contentType: false,
            processData: false,
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).done(function(data) {
            var current_viewing = null;
            var total_results = null;

            //hide loader
            Plex.agencyInquiries.hideLoader();
            
            // is_reset is already be 1
            $('.each-inquirie-container').html('');
            $('.each-inquirie-container').html(data);
            // $('.each-inquirie-container').append('<div class="row showResultsBar"><div class="column small-12 medium-4 large-4 showResults"></div><div class="column small-12 medium-8 large-8 load-more text-center"></div></div>');
            Plex.agencyInquiries.injectLoadMoreButton();

            current_viewing = $('.hasResults').last().data('current-viewing');
            total_results = $('.hasResults').last().data('total-results');
            //check if these are the last results
            hasResults = $('.hasResults').last().data('last-results');

            Plex.agencyInquiries.hasResultsCheck(hasResults);

            $('#current-bucket-results').html($('.hasResults').last().data('currently-viewing'))
            $('#total-bucket-results').html($('.hasResults').last().data('total-results'));

            // $('.each-inquirie-container .showResults').html(Plex.agencyInquiries.injectResults(current_viewing, total_results));
        
            $(elem).closest('#filter-options').hide();
            Plex.agencyInquiries.switchIcon($('a.ms-btns.filter-audience'));

        });
    }
}

Plex.agencyInquiries.clearFilter = function() {
    //reset Start Date
    $('select#startterm').prop('value', '');
    $('select#startyr').prop('value', '');
    //reset financial
    $('select#financial').prop('value', '');
    //reset school type
    $('select#schooltype').prop('value', '');
    //reset location, majors, demo ethnicity, and demo religion
    $('.select-container .tag-list').html('');
    $('input#country_all, input#department_all, input#ethnic_all, input#religion_all, input#schooltype_all, input#financial_all, input#startyr_all, input#startterm_both, input#name_all').trigger('click');
    //reset scores and demographic age text fields
    $('input[data-type="text"]').val('').trigger('blur');
    //reset demographic gender to all
    $('select#s_gender').val('all').trigger('change');
    //reset inMilitary select field to default
    $('select#s_inMilitary, select#s_militaryAffiliation').val('').trigger('change');
    //reset Uploads, Education level, Desired Degree - all section with checkboxes only - reset to checked
    $('input[data-type="checkbox"]').prop('checked', true);
    //reset Profile Completion
    $('select#profile_percent').prop('value', '0');
    //reset applied and enrolled filter
    $('input#applied-filter').prop('checked', false);
    $('input#enrolled-filter').prop('checked', false);
    //close all filter boxes
    $('li.search-tab a.active').trigger('click');

    Plex.agencyInquiries.tags.length = 0;
    // Plex.agencyInquiries.updateFilterCrumbView();
    Plex.agencyInquiries.sections.resetAll();

    $('.cleared-msg').slideDown(250);
    setInterval(function(){
        $('.cleared-msg').slideUp(250);
    }, 3000);

    var currentPage = $('.each-inquirie-container').data('page-type');
    if(currentPage) {
        currentPage = currentPage.split('-');
    }
    var adminType = currentPage[0];
    var page = currentPage[1];

    window.location.href = '/'+ adminType + '/inquiries/' + page;
}

Plex.agencyInquiries.toggleUploadsFilterSelectAll = function(event) {
    event.preventDefault();

    var button = $(event.target),
        radios = button.parents('.component.uploads').find('.form-field'),
        belongsTo = $(button).closest('li.search-tab').data('search-tab'),
        section_obj = Plex.agencyInquiries.sections.getSection(belongsTo),
        currentPage = $('.each-inquirie-container').data('page-type');

    radios.each(function(index, radio) {
        if (button.hasClass('select-all'))
            $(this).prop('checked', true);
        else
            $(this).prop('checked', false);
        
        $(this).trigger('change');
    });

    if (button.hasClass('select-all')) {
        button.removeClass('select-all');
        button.addClass('deselect-all');
    } else {
        button.removeClass('deselect-all');
        button.addClass('select-all');
    }

    if (section_obj && !section_obj.changed) 
        section_obj.hasChanged();
}

Plex.agencyInquiries.initTags = function(data){
    var name = null, prev_key, filter_obj = {}, tabName = '';

    //clear filter before adding new filter
    Plex.agencyInquiries.clearFilter();
    Plex.agencyInquiries.tags.length = 0;

    //save data globally
    Plex.agencyInquiries.initData = data;

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

        switch( name[0] ){
            case 'startdate':
            case 'schooltype':
            case 'financial':
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
                        Plex.agencyInquiries.initLocation(filter_obj);

                    prev_key = null;
                }
                break;
            case 'gpa':
            case 'scores':
            case 'age':
                Plex.agencyInquiries.initScores(key, value);
                break;
            case 'uploads':
            case 'education':
            case 'degree':
                tabName = Plex.agencyInquiries.getTabName(name[0]);
                //first, uncheck all checkboxes for each component, then add ones we have values for
                $('.component.'+tabName).find('input[type="checkbox"]').prop('checked', false);
                if( value.length > 0 )
                    Plex.agencyInquiries.initUploadsEducationDegree(value);
                break;
            case 'date':
                //if date isn't empty, init date
                if( value )
                    Plex.agencyInquiries.initDate(key, value);
                break;
            default:
                //if value isn't all, init Gender/militaryAffiliation
                if( value !== 'all' )
                    Plex.agencyInquiries.initGenderMilitary(key, value);
            break;
        }
    });

}

Plex.agencyInquiries.initCity = function(){
    if( !Plex.agencyInquiries.initData || !Plex.agencyInquiries.initData.city ) return;

    _.each(Plex.agencyInquiries.initData.city, function(value){
        $('#s_cities').val(value).trigger('change');
    });

    Plex.agencyInquiries.cityInitDone = true;
}

Plex.agencyInquiries.initMajor = function(){ 
    if( !Plex.agencyInquiries.initData || !Plex.agencyInquiries.initData.major ) return;

    console.log('majors in initMajor: ', Plex.agencyInquiries.initData.major)
    _.each(Plex.agencyInquiries.initData.major, function(value){
        $('#s_majors').val(value).trigger('change');
    });

    Plex.agencyInquiries.majorInitDone = true;
}

Plex.agencyInquiries.getTabName = function(name){
    if( name === 'degree' )
        return 'desiredDegree';
    else if( name === 'education' )
        return name + 'Level';
    else
        return name;
}

Plex.agencyInquiries.initDate = function(key, value){
    var to = '', from = '', date_split = null;
    date_split = value.split(' ');

    $('input[name="'+key+'"]').val(value);
    $('input[name="daterangepicker_start"]').val(date_split[0]);
    $('input[name="daterangepicker_end"]').val(date_split[2]);
    $('.applyBtn').trigger('click');
}

Plex.agencyInquiries.initGenderMilitary = function(key, value){
    $('select[name="'+key+'"]').val(value).trigger('change');
}

Plex.agencyInquiries.initUploadsEducationDegree = function(arr){
    if( !$.isArray(arr) ) arr = [arr];
    if( arr.length > 0 ){
        _.each(arr, function(value, index, arr){
            //check all the boxes that we have values for
            $('input[type="checkbox"][value="'+value+'"]').prop('checked', true).trigger('change');
        });
    }
}

Plex.agencyInquiries.initScores = function(key, arr){
    $('input[name="'+key+'[]"][placeholder="Min"]').val(arr[0]).trigger('blur');
    $('input[name="'+key+'[]"][placeholder="Max"]').val(arr[1]).trigger('blur');;
}

Plex.agencyInquiries.initLocation = function(obj){
    var elem = $('input[name="'+obj.name+'"][value="'+obj.val+'"]'),
        select_field = elem.closest('.component').find('select'),
        section = elem.closest('li.search-tab').data('search-tab'),
        val = '';

    Plex.agencyInquiries.activeTab = section;
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

// -- validation by section
//validate entire form; return tab that has invalid input; else return 'clean'
Plex.agencyInquiries.validateEntireForm = function(){
    var invalid_page = 'clean';
    var valid = false;
    //var sections = ['scores', 'uploads', 'demographic', 'educationLevel', 'desiredDegree'];
    var sections = ['scores', 'demographic', 'educationLevel', 'desiredDegree'];
    var validator = null;
    var runFunction;

    for( var i = 0; i < sections.length; i++){
        validator = sections[i] + 'IsValid';
        runFunction = Plex.agencyInquiries[validator];
        valid = runFunction();

        if( !valid ){
            invalid_page = sections[i];
            break;
        }
    }

    return invalid_page;
};

//validate min scores is not greater than max scores
Plex.agencyInquiries.scoresIsValid = function(){
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
    Plex.agencyInquiries.toggleErrorMsg(valid, errorElem);

    return valid;
};

//validate uploads has at least one checked
Plex.agencyInquiries.uploadsIsValid = function(){
    var component = $('.component.uploads');
    var fields = component.find('input[type="checkbox"]');
    var errorElem = component.parent().find('.error-msg');
    var valid = false;

    valid = Plex.agencyInquiries.atLeastOneChecked(fields);
    //toggle error message
    Plex.agencyInquiries.toggleErrorMsg(valid, errorElem);

    return valid;
};

//validate demographic
Plex.agencyInquiries.demographicIsValid = function(){
    var component = $('.component.age');
    var fields = component.find('input[type="number"]');
    var errorElem = component.parent().find('.error-msg');
    var min = parseInt($(fields[0]).val());
    var max = parseInt($(fields[1]).val());
    var valid = true;

    if( min >= max )
        valid = false;

    //toggle error message
    Plex.agencyInquiries.toggleErrorMsg(valid, errorElem);

    return valid;
};

//validate that at least hs or college is checked
Plex.agencyInquiries.educationLevelIsValid = function(){
    var valid = false;
    var errorElem = $('.component.educationLevel').parent().find('.error-msg');

    if( $('#education_hs').is(':checked') || $('#education_college').is(':checked') )
        valid = true;
    else
        valid = false;

    //toggle error message
    Plex.agencyInquiries.toggleErrorMsg(valid, errorElem);

    return valid;
};

//validate that at least one degree is checked
Plex.agencyInquiries.desiredDegreeIsValid = function(){
    var component = $('.component.desiredDegree');
    var form_fields = component.find('input[type="checkbox"]');
    var errorElem = component.parent().find('.error-msg');
    var valid = false;

    //atLeastOneChecked will return true or false and then toggle error msg if there is an error or not
    valid = Plex.agencyInquiries.atLeastOneChecked(form_fields);
    Plex.agencyInquiries.toggleErrorMsg(valid, errorElem);

    return valid;
};

//returns true if at least one checkbox is checked, else returns false
Plex.agencyInquiries.atLeastOneChecked = function(fields){
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
Plex.agencyInquiries.toggleErrorMsg = function(valid, errorElem){
    if( !valid )
        errorElem.removeClass('hidden');
    else
        errorElem.addClass('hidden');
};
// -- validation by section

// -- tags
//save tags in global array
Plex.agencyInquiries.saveTag = function(belongs, val, type, text, option){
    var tag = {};
    tag.belongs_to = belongs;
    tag.val = val;
    tag.component = type;
    tag.text = text;
    tag.option = option || null;
    Plex.agencyInquiries.tags.push(tag);
}

//update tag based on prop
Plex.agencyInquiries.updateTag = function(tag, prop, val){
    var update_this_tag = null;

    _.each(tag, function(obj, index, arr){
        update_this_tag = _.findWhere(Plex.agencyInquiries.tags, obj);
        update_this_tag[prop] = val;
    });
}

//remove tags from global array
Plex.agencyInquiries.removeTag = function(tag_val){
    tag_val = ''+tag_val;
    Plex.agencyInquiries.tags = _.reject(Plex.agencyInquiries.tags, {val: tag_val});
}
Plex.agencyInquiries.removeTagAsNumber = function(tag_val){
    Plex.agencyInquiries.tags = _.reject(Plex.agencyInquiries.tags, {val: +tag_val});
}

//remove multiple tags by section type
Plex.agencyInquiries.removeTagBySection = function(tag_type){
    Plex.agencyInquiries.tags = _.reject(Plex.agencyInquiries.tags, {belongs_to: tag_type});
}

//remove multiple tags by component type
Plex.agencyInquiries.removeTagByComponent = function(tag_type){
   Plex.agencyInquiries.tags = _.reject(Plex.agencyInquiries.tags, {component: tag_type});
}

Plex.agencyInquiries.removeCrumb = function(section, comp, valu){
    Plex.agencyInquiries.tags = _.reject(Plex.agencyInquiries.tags, {val: valu});
}

//get tags by component
Plex.agencyInquiries.getTagsByComponent = function(type){
    return _.filter(Plex.agencyInquiries.tags, {component: type});
}

//get tags by certain prop
Plex.agencyInquiries.getTags = function(prop, val){
    var searchObj = {};
    searchObj[prop] = val;
    return _.where(Plex.agencyInquiries.tags, searchObj);
}

//no duplicate tags
Plex.agencyInquiries.findTag = function(obj_array, obj_val, obj_type){
    var result = null;
    result = _.findWhere(obj_array, {val: obj_val, component: obj_type});
    return result === undefined ? false : true;
}

//filter tags by type and inject html into appropriate tag list
Plex.agencyInquiries.updateTagListView = function(elem, type){
    var tmp_arr = _.filter(Plex.agencyInquiries.tags, {component: type});
    $(elem).parents('.select-container').find('.tag-list').html( Plex.agencyInquiries.buildFilterTags(tmp_arr) );
}

//update view
Plex.agencyInquiries.makeTags = function(elem, type){
    var tmp_arr = _.filter(Plex.agencyInquiries.tags, {component: type});
    $(elem).closest('.search-filter-form').find('.select-container .tag-list').html( Plex.agencyInquiries.buildFilterTags(tmp_arr, true) );
}

//build tag html to display on the page
Plex.agencyInquiries.buildFilterTags = function(tag_list, useText){
    var html_tags = '', txt = '';

    $(tag_list).each(function(tag){
        txt = useText ? this.text : this.val;
        html_tags += '<div class="left tag" data-tag-val="'+this.val+'" data-component="'+this.component+'" data-belongsto="'+this.belongs_to+'">';
        
        //if financial -- we want to remove the ability to remove tags from this list
        //and only allow users to change list through dropdown
        if(this.component === 'financial'){
            html_tags +=  this.text;
        }else if(this.component === 'hasApplied'){
            html_tags +=  this.text + '<span class="remove-tag"> &times; </span>';
        }else{
            html_tags +=  txt + '<span class="remove-tag"> &times; </span>';
        }
        html_tags +=     '<input type="hidden" name="'+this.component+'[]" value="'+txt+'" />'
        html_tags += '</div>';
    });

    return html_tags;
}

Plex.agencyInquiries.alreadyHaveTag = function(comp, txt){
    return _.where(Plex.agencyInquiries.tags, {component: comp, text: txt});
}

Plex.agencyInquiries.tagExists = function(comp){
    return _.findWhere(Plex.agencyInquiries.tags, {component: comp});
}

// -- end tags

// Sort columns
$(document).on('click', '.sort-col', function() {
    var order_data = $(this).data('order'),
        orderBy = order_data.orderBy,
        sortBy = order_data.sortBy,
        page = $('body').attr('id').split('-')[1];

    Plex.agencyInquiries.showLoader();

    $.ajax({
        url: '/agency/inquiries/' + page + '?orderBy=' + orderBy + '&sortBy=' + sortBy,
        type: 'GET',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},

    }).done(function(response) {
        var current_viewing = null;
        var total_results = null;

        //hide loader
        Plex.agencyInquiries.hideLoader();
        
        // is_reset is already be 1
        $('.each-inquirie-container').html('');
        $('.each-inquirie-container').html(response);
        // $('.each-inquirie-container').append('<div class="row showResultsBar"><div class="column small-12 medium-4 large-4 showResults"></div><div class="column small-12 medium-8 large-8 load-more text-center"></div></div>');
        Plex.agencyInquiries.injectLoadMoreButton();

        current_viewing = $('.hasResults').last().data('current-viewing');
        total_results = $('.hasResults').last().data('total-results');
        //check if these are the last results
        hasResults = $('.hasResults').last().data('last-results');

        Plex.agencyInquiries.hasResultsCheck(hasResults);

        $('#current-bucket-results').html($('.hasResults').last().data('currently-viewing'));
        $('#total-bucket-results').html($('.hasResults').last().data('total-results'));

    });

    sortBy = sortBy == 'ASC' ? 'DESC' : 'ASC';

    $(this).data('order', { "orderBy": orderBy, "sortBy": sortBy });
});

// -- radio btn form field event handler
$(document).on('change', 'input[type="radio"][data-type="radio"].form-field', function(){
    var _this = $(this), val = _this.val(),
        text = _this.next().text(), option = null,
        section = _this.closest('li.search-tab').data('search-tab'), already_have_tag = null,
        error_msg = _this.parent().find('.error'), section_obj = null, validator = false;

    if( section === 'schooltype' ){
        already_have_tag = Plex.agencyInquiries.tagExists(section);

        if( !already_have_tag ){
            Plex.agencyInquiries.saveTag(section, val, section, text, option);
        }else{
            Plex.agencyInquiries.updateTag(already_have_tag, 'val', val);
            Plex.agencyInquiries.updateTag(already_have_tag, 'text', text);
        }

        Plex.agencyInquiries.makeTags(_this, section);
    }
});
// -- radio btn form field event handler



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
    //         //check if tag is already created for this component
            already_have_tag = Plex.agencyInquiries.alreadyHaveTag(component, text);
            if( already_have_tag.length === 0 ){
                Plex.agencyInquiries.saveTag(section, _this.val(), component, text, option);
            }else{
                Plex.agencyInquiries.updateTag(already_have_tag, 'val', _this.val());
            }
        }else{
            already_have_tag = Plex.agencyInquiries.alreadyHaveTag(component, text);
            if( already_have_tag.length > 0 ){

                Plex.agencyInquiries.removeTag(already_have_tag[0].val);
            }
        }
    }

    $('.component.scores').find("input").each( function(){
        if($(this).val() != "")
            validator = true;
    });

    section_obj = Plex.agencyInquiries.sections.getSection(belongsTo);

    if(!validator) {
        if( section_obj ) section_obj.reset();
    }

    if( section_obj && !section_obj.changed ) section_obj.hasChanged();

    // Plex.agencyInquiries.updateFilterCrumbView();
});

$(document).on('change', 'input[data-type="checkbox"]', function(){
    var _this = $(this), section = Plex.agencyInquiries.getSectionName(_this), component = section, belongsTo = _this.closest('li.search-tab').data('search-tab'),
        val = _this.val(), option = _this.is(':checked') ? 'include' : 'exclude', text = _this.parent().find('label').text();

    //if checked, add tag, else remove it
    if( _this.is(':checked') ){
        Plex.agencyInquiries.saveTag(section, val, component, text, option);
    }else{
        Plex.agencyInquiries.removeCrumb(section, component, val);
    }

    section_obj = Plex.agencyInquiries.sections.getSection(belongsTo);
    if( section_obj && !section_obj.changed ) section_obj.hasChanged();
    // Plex.agencyInquiries.updateFilterCrumbView();
});

Plex.agencyInquiries.getSectionName = function(elem){
    return elem.closest('li.search-tab').data('search-tab');
}
// -- end text input filed event trigger

// -- component toggle
$(document).on('click', 'input.add-name', function() {
    var _this = $(this).prev('div').find('input'), val_text = _this.val(),
        belongsTo = _this.closest('li.search-tab').data('search-tab'),
        component_name = _this.attr('name'), duplicateFound = false, demo_tag = null,
        option = _this.closest('.component').find('input[type="radio"]:checked').val(),
        val = '', prop = '', section_obj = null;

    demo_tag = Plex.agencyInquiries.getTags('component', component_name);

    // if there is an input name text
    if(option == 'all') {
        Plex.agencyInquiries.removeTagByComponent(component_name);
    } else {
        // include or exclude
        if(val_text) {
            // find duplicate
            duplicateFound = Plex.agencyInquiries.findTag(Plex.agencyInquiries.tags, val_text, component_name);
            if(!duplicateFound) {
                Plex.agencyInquiries.saveTag(belongsTo, val_text, _this.attr('name'), val_text, option);
                Plex.agencyInquiries.updateTagListView(_this, component_name);
            }
        }
    }

    section_obj = Plex.agencyInquiries.sections.getSection(belongsTo);
    if( section_obj && !section_obj.changed ) section_obj.hasChanged();

    _this.prop('value', '');

});

$(document).on('change', 'select[data-type="select"]:not(.savedFilters-class)', function(){
    var _this = $(this),
        val_text = $('option[value="'+_this.val()+'"]', _this).text(),
        belongsTo = _this.closest('li.search-tab').data('search-tab'),
        component_name = _this.attr('name'), duplicateFound = false, demo_tag = null,
        option = _this.closest('.component').find('input[type="radio"]:checked').val(),
        val = '', prop = '', state = '', section_obj = null;

    if( component_name === 'city' && !Plex.agencyInquiries.cityInitDone ){
        Plex.agencyInquiries.populateCityBasedOnState($('#s_states').val(), true);
    }else if( component_name === 'major' && !Plex.agencyInquiries.majorInitDone ){
        Plex.agencyInquiries.populateMajorBasedOnDepartment($('#s_depts').val(), true);
    }

    if( Plex.agencyInquiries.activeTab === 'location' ||  Plex.agencyInquiries.activeTab === 'major' ||
        Plex.agencyInquiries.activeTab === 'startdate' ||  Plex.agencyInquiries.activeTab === 'schooltype' ||
        Plex.agencyInquiries.activeTab === 'financial' )
        Plex.agencyInquiries.toggleComponents(_this);

    if( component_name === 'gender' ){
        val = _this.val();
        prop = 'val';
    }else{
        val = val_text;
        prop = 'text';
    }


    //if value is not empty or equal to Select..., then proceed to check if this tag already exists, if not, save it
    if( _this.val() && val_text !== 'Select...' ){
        if( (Plex.agencyInquiries.activeTab === 'demographic' && component_name === 'gender') ||
            (Plex.agencyInquiries.activeTab === 'militaryAffiliation' && component_name === 'inMilitary') ||
            (Plex.agencyInquiries.activeTab === 'profileCompletion') ){

            demo_tag = Plex.agencyInquiries.getTags('component', component_name);

            if( val === 'all' ){
                Plex.agencyInquiries.removeTagByComponent(component_name);
            }else{
                if( demo_tag.length > 0 ) Plex.agencyInquiries.updateTag(demo_tag, prop, val);
                else Plex.agencyInquiries.saveTag( belongsTo, _this.val(), _this.attr('name'), val_text, option );
            }

        }
        else if(Plex.agencyInquiries.activeTab === 'financial'){
 
            //clear tag list to create new one
            //Plex.agencyInquiries.tags.length = 0;
            Plex.agencyInquiries.removeTagByComponent(component_name);

            //save tag for this value and all above it
            for(var i = $.inArray(val.trim(), Plex.agencyInquiries.ranges_formatted); i < Plex.agencyInquiries.ranges.length; i++){
                Plex.agencyInquiries.saveTag( belongsTo, Plex.agencyInquiries.ranges[i], _this.attr('name'), Plex.agencyInquiries.ranges_formatted[i], option );             
            }
    
        }
        else{
            // come back
            duplicateFound = Plex.agencyInquiries.findTag(Plex.agencyInquiries.tags, _this.val(), component_name);
            if( !duplicateFound && val !== 'all' && _this.val() ){
                Plex.agencyInquiries.saveTag( belongsTo, _this.val(), _this.attr('name'), val_text, option );
                Plex.agencyInquiries.updateTagListView(_this, component_name);
               
            }
        }
    }else{      
        if( Plex.agencyInquiries.activeTab === 'demographic' || 
            Plex.agencyInquiries.activeTab === 'militaryAffiliation' || 
            Plex.agencyInquiries.activeTab === 'profileCompletion' ||
            Plex.agencyInquiries.activeTab === 'financial'){
                Plex.agencyInquiries.removeTagByComponent(component_name);
        }
    }

    section_obj = Plex.agencyInquiries.sections.getSection(belongsTo);
    if( section_obj && !section_obj.changed ) section_obj.hasChanged();

    Plex.agencyInquiries.updateTagListView(_this, component_name);
});

// remove tag
$(document).on('click', '.tag-list .remove-tag', function(){
    var _this = $(this).parent(),
        val = _this.data('tag-val'),
        select_elem = _this.closest('.select-container').find('select');

    // update tags list
    if( _this.closest('.tag-list').hasClass('for-applied') ) Plex.agencyInquiries.removeTagAsNumber(val);
    else Plex.agencyInquiries.removeTag(val);

    //remove from view
    _this.remove();

    //check if any components need to be toggled depending on tag removed
    Plex.agencyInquiries.onTagRemove(_this, select_elem);
});

Plex.agencyInquiries.onTagRemove = function(tag, container){
    var val = tag.data('tag-val'),
        current_component_tags = null,
        component_name = tag.data('component');

    //get the current components tags
    current_component_tags = Plex.agencyInquiries.getTagsByComponent(component_name);

    //if there are no more tags for this component, trigger click, otherwise do nothing
    if( _.isEmpty(current_component_tags) ){
        $('.component.'+component_name+' input[value="all"]').trigger('click');
    }else if( val === 'United States' ){
        container.val('');
        Plex.agencyInquiries.toggleComponents( container );
    }
}
//toggle components based on select field value
Plex.agencyInquiries.toggleComponents = function(elem){
    var dependency = elem.closest('.component').data('dependency');
    var search_form = elem.closest('.search-filter-form');
    var name = elem.attr('name');

    if( typeof dependency !== 'undefined' ){
        switch( name ){
            case 'country':
                if( elem.val() === 'United States' || Plex.agencyInquiries.findTag(Plex.agencyInquiries.tags, 'United States', 'country') )
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

    if( Plex.agencyInquiries.activeTab === 'location' ||  Plex.agencyInquiries.activeTab === 'major' ||
        Plex.agencyInquiries.activeTab === 'demographic' || Plex.agencyInquiries.activeTab === 'startdate' ||
        Plex.agencyInquiries.activeTab === 'name' || Plex.agencyInquiries.activeTab === 'schooltype' ||
        Plex.agencyInquiries.activeTab === 'financial' ){
        Plex.agencyInquiries.toggleSelectContainer( _this );

        //get component name, then find tags by component name
        component_name = _this.attr('name').split('_')[0];
        tagsFound = Plex.agencyInquiries.getTags('component', component_name);

        //if any found, update them with new radio val
        if( tagsFound.length > 0 ){

            //update crumb tags data, then update crumb tag view
            Plex.agencyInquiries.updateTag(tagsFound, 'option', $(this).val());
            // Plex.agencyInquiries.updateFilterCrumbView();
        }

    }
});

//toggle select container and related component
Plex.agencyInquiries.toggleSelectContainer = function(elem){
    var container = elem.closest('.component').find('.select-container');
    var select_field = container.find('select');
    var input_field = container.find('input[type="text"]');

    //hide or show containers
    if( elem.val() === 'all' ){
        Plex.agencyInquiries.resetComponent(elem);
        container.addClass('hidden');
        Plex.agencyInquiries.toggleComponents(select_field);
        Plex.agencyInquiries.toggleComponents(input_field);
    }else{
        container.removeClass('hidden');
        container.find('.xclude-toggle').html(elem.val());
    }
};

//reset component back to default
Plex.agencyInquiries.resetComponent = function(elem){
    var comp = elem.attr('name');
    var component_name = comp.slice(0, comp.indexOf('_'));

    //remove tag html and empty select field
    if( component_name === 'country' || component_name === 'department' ||
        component_name === 'startdate' || component_name === 'schooltype' ||
        component_name === 'financial' ){
        elem.closest('.search-filter-form').find('input[type="radio"][value="all"]').trigger('click').closest('.search-filter-form').clearSelectFieldAndTagList();
        component_name = elem.closest('li.search-tab').data('search-tab');

        //remove all tags by section from tags array
        Plex.agencyInquiries.removeTagBySection(component_name);
    }else{
        elem.closest('.component').clearSelectFieldAndTagList();

        //remove all tags by component
        Plex.agencyInquiries.removeTagByComponent(component_name);
    }

    // Plex.agencyInquiries.updateFilterCrumbView();
};

$.fn.clearSelectFieldAndTagList = function(){
    this.find('select').val('').parent().find('.tag').remove();
    return this;
};

// -- populate select fields based on dependencies

//when year select field has changed values
$(document).on('change', '#startyr', function() {
    var _this = $(this);

    if(_this.val() != '') {
        $('.component.startterm').removeClass('hidden');
    }
});

//when state select field has changed values
$(document).on('change', '#s_states', function(){
    var _this = $(this);

    if(_this.val() != ''){
       Plex.agencyInquiries.populateCityBasedOnState(_this.val());
    }
});

//when department select field has changed values
$(document).on('change', '#s_depts', function(){
    var _this = $(this);

    if(_this.val() != ''){
       Plex.agencyInquiries.populateMajorBasedOnDepartment(_this.val());
    }
});

//get cities based on selected state
Plex.agencyInquiries.populateCityBasedOnState = function(stateAbbr, fromInit){
    $.getJSON("/ajax/homepage/getCityByState/"+stateAbbr, function(result) {
        var options = $("#s_cities");
        Plex.agencyInquiries.populate(options, result);
        if( fromInit ){
            Plex.agencyInquiries.initCity();
        }
    });
}

//get majors based on selected department
Plex.agencyInquiries.populateMajorBasedOnDepartment = function(departmentAbbr, fromInit){
    $.getJSON("/ajax/getMajorByDepartment/"+departmentAbbr.replace(/\//g, '&'), function(result) {
        var options = $("#s_majors");
        Plex.agencyInquiries.populate(options, result);

        if( fromInit ){
            Plex.agencyInquiries.initMajor();
        }
    });
}

Plex.agencyInquiries.populate = function(target, data){
    target.find('option').remove();
    $.each(data, function(key, value) {
        target.append($("<option />").val(value).text(value));
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

$(document).on('click', '.more-option', function(e){
    var moreOptions = $('.more-option');
    var moreTab = $('.moreTab');
    if (moreOptions.text() == '+ More Filter Options') {
        moreTab.removeClass('hide');
        moreOptions.text('- Less Filter Options');
    }else{
        moreTab.addClass('hide');
        moreOptions.text('+ More Filter Options');
    }
});

// -- populate select fields based on dependencies


// open filter option
Plex.agencyInquiries.switchIcon = function(elem) {
    var _this = elem;
    var icon = _this.find('img');
    var text = _this.find('.filter-text');
    var filter_options = $('#filter-options');

    if(filter_options.is(':visible')) {
        _this.addClass('active');
        icon.attr('src', '/images/setting/filter-blue.png');
    } else {
        _this.removeClass('active');
        icon.attr('src', '/images/setting/filter-white.png');
    }
}

$(document).on('click', '.ms-btns.filter-audience', function(e) {
   // var target = $(e.target);
    var filter_options= $('#filter-options');

    //position element under filter audience button
    var newLeft = $('.ms-btns.filter-audience').offset().left - 
    ( $('.manage-student-sidebar-menu').width() + $('#filter-options').width() ) + 
    $('.ms-btns.filter-audience').width();

    filter_options.css('left', newLeft + 'px');

    if(!filter_options.is(":visible")) {
        filter_options.css('display', 'block');
        Plex.agencyInquiries.switchIcon($('a.ms-btns.filter-audience'));
    } else {
        filter_options.css('display', 'none');
        Plex.agencyInquiries.switchIcon($('a.ms-btns.filter-audience'));
    }

});

$(document).on('click', function(e){
       
    //if is filter audience button , just return as it has it's own handling
    if($(e.target).closest('a.ms-btns.filter-audience').length){
        // $('#filter-options').toggle();
     return;
    }

    //if not in filter options box 
    if($(e.target).closest('#filter-options').length === 0 ){
        if( !$(e.target).hasClass('remove-tag') ){
            $('#filter-options').hide();
            Plex.agencyInquiries.switchIcon($('a.ms-btns.filter-audience')); 
        }
    }
});

// -- search tab functions
$(document).on('click', '.search-tab a', function(e){
    e.preventDefault();
    var clicked_tab = $(this).parent().data('search-tab'), section = {};

    section = Plex.agencyInquiries.sections.getSection(clicked_tab);
    if( section && section.locked ) return false;

    $('li.search-tab a').removeClass('active');

    //if clicked tab is already active just close it, otherwise open it
    if( clicked_tab === Plex.agencyInquiries.activeTab ){
        Plex.agencyInquiries.activeTab = null;
        Plex.agencyInquiries.closeAllSearchFilterBoxes();
    }else{
        Plex.agencyInquiries.activeTab = clicked_tab;
        Plex.agencyInquiries.openSearchFilter();
        $(this).addClass('active');
    }
});

Plex.agencyInquiries.closeAllSearchFilterBoxes = function(){
    $('.search-filter-form').removeClass('open').slideUp(10);
}

Plex.agencyInquiries.openSearchFilter = function(){
    Plex.agencyInquiries.closeAllSearchFilterBoxes();
    $('li[data-search-tab="'+Plex.agencyInquiries.activeTab+'"]').find('.search-filter-form').addClass('open').slideDown(500);
}
// -- search tab functions

Plex.agencyInquiries.switchIcon = function(elem) {
    var _this = elem;
    var icon = _this.find('img');
    var text = _this.find('.filter-text');
    var filter_options = $('#filter-options');

    if(filter_options.is(':visible')) {
        _this.addClass('active');
        icon.attr('src', '/images/setting/filter-blue.png');
    } else {
        _this.removeClass('active');
        icon.attr('src', '/images/setting/filter-white.png');
    }
}

//show ajax loader
Plex.agencyInquiries.showLoader = function(){
	$('.manage-students-ajax-loader').show();
}

//hide ajax loader
Plex.agencyInquiries.hideLoader = function(){
	$('.manage-students-ajax-loader').hide();
}

//remove 'load more' button and add no more results text when out of results
Plex.agencyInquiries.hasResultsCheck = function(hasResults){
	if( hasResults !== 1 ){
		$('.show-more-text').addClass('hide');
		$('.no-results-txt').removeClass('hide');
        $('.load-more-btn').removeClass('active');
	}
}

//inject load more button onto page
Plex.agencyInquiries.injectLoadMoreButton = function(){
	$('.each-inquirie-container').append(Plex.agencyInquiries.loadMoreButton());
}

//build load more button
Plex.agencyInquiries.loadMoreButton = function(){
	var btn = [
        '<div class="load-more-container">',
            '<div>',
                '<div>Currently Viewing : <b><span id="current-bucket-results">5</span></b></div>',
                '<div>Total Results : <b><span id="total-bucket-results">5</span></b></div>',
            '</div>',

    		'<div class="load-more-btn load-more text-center active">',
        		'<div class="show-more-text">Show more results</div>',
        		'<div class="no-results-txt hide">No more results</div>',
    		'</div>',

            // Invisible div to help with flex display
            '<div style="width: 13em;"></div>',
        '</div>'
	].join("");

	return btn;
}
// -- load more functions



//start interval when textarea has focus

$(document).on('focus', '.notes-textarea', function(){
	var these_notes = this;
	var student_id = $(this).data('studentid');

	//loop - every 10 seconds, while textarea is in focus, save the note
	Plex.agencyInquiries.saveNotesInterval = setInterval(function(){
		Plex.agencyInquiries.autosaveNotes( student_id, these_notes );
	}, 10000);
})

//stop interval when textarea is out of focus
$(document).on('blur', '.notes-textarea', function() {
	var _this = this;
	var this_student_id = $(this).data('studentid');

	//once out of focus, save note
	clearInterval(Plex.agencyInquiries.saveNotesInterval);
	Plex.agencyInquiries.autosaveNotes( this_student_id, this );
});

//autosave notes
Plex.agencyInquiries.autosaveNotes = function( id, notes ){
	var note_data = $(notes).val();
	var last_saved = $(notes).parent().find('.last-saved-note-time');

	$('.save-note-ajax-loader').show();

	//post note and update 'last saved' time
	$.ajax({
		url: '/agency/setNote',
		type: 'POST',
		data: {user_id: id, note: note_data},
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	})
	.done(function(time){
		$('.save-note-ajax-loader').hide();
		$(last_saved).text('Last Saved: ' + time);
	});
}

//hide arrows if not enough competition
Plex.agencyInquiries.hideCompetitionCarouselArrows = function( items_total, items_allowed ){
	if( items_total <= items_allowed ){
		$('.competitor-carousel-arrow').hide();
	}else{
		$('.competitor-carousel-arrow').show();
	}
}

//arrow click for Competition carousel
Plex.agencyInquiries.moveCompetitionCarousel = function( owls, arrow ){
	if( $(arrow).hasClass('leftarrow') ){
		owls.trigger('owl.prev');
	}else{
		owls.trigger('owl.next');
	}
}


//remove student click event
$(document).on('click', '.remove-student-col', function(){
	var this_student = $(this).data('studentid');
	var in_pending = $(this).data('in-pending').trim();
	Plex.agencyInquiries.removeStudent(this, this_student, in_pending);

	mixpanel.track("Remove_Student",
		{
			"location": document.body.id
		}
	);
});

//remove student from list
Plex.agencyInquiries.removeStudent = function(student, studentID, from_pending){

	$.ajax({
		url: '/agency/setRecommendationRecruit/' + studentID + '/' + Plex.agencyInquiries.remove_this,
		type: 'POST',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	})
	.done(function() {
		//remove this student row from list after ajax call
		$(student).closest('.inquirie_row').remove();

		if( from_pending === 'true' ){
			topAlert(Plex.agencyInquiries.removeFromPendingSuccessMsg);
		}else{
			topAlert(Plex.agencyInquiries.removeFromApproveSuccessMsg);
		}
	});
}

//restore student click event
$(document).on('click', '.restore-student-col', function(){
	var this_student = $(this).data('studentid');
	Plex.agencyInquiries.restoreStudent(this, this_student);

	mixpanel.track("Restore_Student",
		{
			"location": document.body.id
		}
	);

});

$(document).on('click', '.actionbar-btn', function(e){
    //e.stopPropagation();
    if($(this).hasClass('status-identifier')){
        var menu = null;
        var that = $(this);
        var button = that.closest('.actionbar-btn');

        if($(this).find('.promote_menu').length)
            menu = $(this).find('.promote_menu')
        else if($(this).find('.state-menu').length)
            menu = $(this).find('.state-menu'); 
        else 
            menu = $(this).find('.status-menu');

        menu.toggle();

        //if menu is displayed
        if(menu.is(':visible')){  

            button.addClass('actionbar-btn-active');
            button.removeClass('actionbar-btn-notactive');
            if(!button.hasClass('actionbar-btn-updated')){
                button.find('.promote-arrow').addClass('promote-arrow-active');
                button.find('.promote-arrow').removeClass('promote-arrow-notactive');    
            }
           
            //close menus if clicking outside of it
            $(document).one('click', function(e){

                if(!$(e.target).hasClass('actionbar-btn') && $(e.target).parents('.actionbar-btn').length === 0){
                        menu.hide();
                        
                        if(button.hasClass('actionbar-btn-active')){
                            button.removeClass('actionbar-btn-active');
                            button.addClass('actionbar-btn-notactive');
                            button.find('.promote-arrow').addClass('promote-arrow-notactive');
                            button.find('.promote-arrow').removeClass('promote-arrow-active');
                        }
                }
            });

        //if menu is not displayed    
        }else{
       
            button.removeClass('actionbar-btn-active');
            button.addClass('actionbar-btn-notactive');

            if(!button.hasClass('actionbar-btn-updated')){
                button.find('.promote-arrow').addClass('promote-arrow-notactive');
                button.find('.promote-arrow').removeClass('promote-arrow-active');
            }

        }

       
    }
});

$(document).on('click', '.status-menu li:not([data-status="undo"]):not([data-status="removed"])', function(e){
    e.stopPropagation();

    Plex.agencyInquiries.showLoader();

    var inqRow = $(this).closest('.inquirie_row'),
        menu = $(this).closest('.status-menu'),
        new_bucket_name = $(this).data('status'),
        hashed_id = inqRow.data('hashed_id');

    Plex.agencyInquiries.changeStudentAgencyBucket(hashed_id, new_bucket_name);
});

$(document).on('click', '.status-menu li[data-status="undo"]', function(event) {
    event.stopPropagation();
    var inqRow = $(this).closest('.inquirie_row'),
        hashed_id = inqRow.data('hashed_id'),
        bucketChangeSuccessMsg = $.extend({}, Plex.agencyInquiries.bucketChangeSuccess),
        current_bucket_name = $('body').attr('id').split('-')[1],
        current_num_of_buckets = parseInt($('#num_of_' + current_bucket_name).text());

    Plex.agencyInquiries.showLoader();

    $.ajax({
        url: '/agency/inquiries/undoStudentAgencyBucketChange',
        type: 'POST',
        data: { hashed_id: hashed_id },
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    })
    .done(function(response) {
        Plex.agencyInquiries.hideLoader();
        
        response = JSON.parse(response);

        if (response.success && response.success == 1) {
            var new_num_of_buckets = parseInt($('#num_of_' + response.bucket_name).text());
            
            bucketChangeSuccessMsg.msg = bucketChangeSuccessMsg.msg.replace('{{bucket}}', response.display_name);
            
            inqRow.fadeOut(500);
            topAlert(bucketChangeSuccessMsg);
            current_num_of_buckets--;
            new_num_of_buckets++;
            $('#num_of_' + current_bucket_name).text(current_num_of_buckets);
            $('#num_of_' + response.bucket_name).text(new_num_of_buckets);

        } else {
            topAlert(Plex.agencyInquiries.undoFailed);
        }
    });
});

$(document).on('click', '.status-menu li[data-status="removed"], .agency-recruit-status .recruit-status[data-status="no"]', function(event) {
    // Validate notes exist.
    event.stopPropagation();
    var inqRow = $(this).closest('.inquirie_row'),
        new_bucket_name = 'removed',
        hashed_id = inqRow.data('hashed_id'),
        uid = inqRow.data('uid'),
        notes = inqRow.find('.notes-textarea'),
        modal = $('#student-removal-modal'),
        remove_button = modal.find('.confirm-student-removal');

    if (notes.val() == '' || !notes.val()) {
        remove_button.data('uid', uid);
        remove_button.data('hashed_id', hashed_id);
        $('#student-removal-modal').foundation('reveal', 'open');
    } else {
        Plex.agencyInquiries.changeStudentAgencyBucket(hashed_id, new_bucket_name);
    }
});

$(document).on('click', '.confirm-student-removal', function(event) {
    event.preventDefault();
    // back here
    var parent = $(this).closest('#student-removal-modal'),
        user_id = $(this).data('uid'),
        inqRow = $('.inquirie_row[data-uid=' + user_id + ']'),
        hashed_id = $(this).data('hashed_id'),
        notes = parent.find('textarea');

    if (notes.val() == '') { return; }

    parent.foundation('reveal', 'close');

    Plex.agencyInquiries.autosaveNotes(user_id, notes);

    Plex.agencyInquiries.showLoader();

    Plex.agencyInquiries.changeStudentAgencyBucket(hashed_id, 'removed');

});

$(document).on('click', '.agency-recruit-status .recruit-status[data-status="yes"]', function(event) {
    event.stopPropagation();
    var status = $(this).data('status'),
        inqRow = $(this).closest('.inquirie_row'),
        hashed_id = inqRow.data('hashed_id');

    Plex.agencyInquiries.changeStudentAgencyBucket(hashed_id, 'opportunities');
});

Plex.agencyInquiries.changeStudentAgencyBucket = function(hashed_id, new_bucket_name) {

    var inqRow = $('.inquirie_row[data-hashed_id="' + hashed_id + '"]'),
        bucketChangeSuccessMsg = $.extend({}, Plex.agencyInquiries.bucketChangeSuccess),
        current_bucket_name = $('body').attr('id').split('-')[1], // currentPage is stored as the id of the body.
        current_num_of_buckets = parseInt($('#num_of_' + current_bucket_name).text()),
        new_num_of_buckets = parseInt($('#num_of_' + new_bucket_name).text());

    Plex.agencyInquiries.showLoader();

    $.ajax({
        type: 'POST',
        url: '/agency/inquiries/changeStudentAgencyBucket',
        data: { hashed_id: hashed_id, bucket_name: new_bucket_name },
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(response) {
        Plex.agencyInquiries.hideLoader();
        response = JSON.parse(response);

        if (response.success && response.success == 1) {
            bucketChangeSuccessMsg.msg = bucketChangeSuccessMsg.msg.replace('{{bucket}}', response.display_name);
            inqRow.fadeOut(500);
            topAlert(bucketChangeSuccessMsg);
            current_num_of_buckets--;
            new_num_of_buckets++;
            $('#num_of_' + current_bucket_name).text(current_num_of_buckets);
            $('#num_of_' + new_bucket_name).text(new_num_of_buckets);

        } else {
            topAlert(Plex.agencyInquiries.error);
        }

    }).fail(function(response){
        Plex.agencyInquiries.hideLoader();
        
        topAlert(Plex.agencyInquiries.error);

    });
}

/********************** upload modal *********************************/
//////
$(document).on('opened.fndtn.reveal', '[data-reveal]', function () {
    var modal = $(this);
    if(modal.is('.upload_docs_modal'))
        $('.reveal-modal-bg').fadeIn();
});

///////
$(document).on('click', '.actionbar-uploadModal, .edit-new-upload-btn', function() {
    var hash_id = $(this).closest('.actionbar-container').data('uhid') || $(this).closest('.uploads-edit-container').data('uhid');
    $('.upload-files-modal[data-uhid="'+ hash_id +'"]').first().foundation('reveal','open');
});

////////
$(document).on('click', '.actionbar-upload-btn', function(event) {
    var hash_id = $(this).closest('.upload-files-modal').data('uhid');

    var docType = $(this).find('.actionbar-upload-txt').text().trim().toLowerCase();
    if(docType === 'interview')
        docType = 'prescreen_interview';
    if(docType === 'resume / portfolio')
        docType = 'resume';
    if(docType === 'financial document')
        docType = 'financial';
    var postType = docType + 'upload'; 

    docModal = $('.upload_docs_modal[data-uhid="'+ hash_id +'"]').first();

    docModal.find('input[name="docType"]').attr('value', docType);
    docModal.find('input[name="postType"]').attr('value', 'transcriptupload');

    $(this).closest('.upload-files-modal').foundation('reveal', 'close');
    docModal.foundation('reveal', 'open');
});

//////////
$(document).on('click', '.close-ufModal', function() {
    $(this).closest('.upload-files-modal').foundation('reveal', 'close');
    $('.reveal-modal-bg').fadeOut();
});

///////////
$(document).on('click', '.close-udModal', function() {
    $(this).closest('.upload_docs_modal').foundation('reveal', 'close');
    $('.reveal-modal-bg').fadeOut();
});

//////// verify a contact handler (sales)
//  green checkmark next to contact info
//
$(document).on('click', '.contact-verify', function(e){
    //send post ajax to set verified contact
    var me = $(this);
    var inqRow = me.closest('.inquirie_row');
    var uid = me.parents('.inquirie_row').data('uid');
    var type = me.parent().find('.c-info-label').text();

    var data = {user_id: uid};

    var verified = false;
    var url = '/agency/inquiries/savePlexussUserInfo';

    if(!me.hasClass('verified')){
        verified = true;
    }

    //changed to reflect backend
    if(type === 'Phone'){
        type = 'Phonecall';
    }
    if(type === 'SMS'){
        type= "Phone";
    }

    Plex.agencyInquiries.showLoader();

    data[type + '_verified'] = verified;

    $.ajax({
        url: url,
        type: 'POST',
        data: data,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(response){

        var enbled = inqRow.find('.text-enabled');
        var disbled = inqRow.find('.text-disabled'); 

        console.log(response);

        if(response === 'success'){
            if(me.hasClass('verified')){
                me.removeClass('verified');

            }
            else{
                me.addClass('verified');
            }


            if(type === 'Phonecall'){
                if(me.closest('.sales-student-edit-pane').length > 0)
                    $('.sales-student-pane #phonecallc').toggleClass('verified');
                else
                    $('.sales-student-edit-pane #phonecallc').toggleClass('verified');
            }
            if(type === 'Phone'){
                if(me.closest('.sales-student-edit-pane').length > 0)
                    $('.sales-student-pane #phonec').toggleClass('verified');
                else
                    $('.sales-student-edit-pane #phonec').toggleClass('verified');

            }



            //show text in contact options
            if(type === 'Phone' && $('.admin_TxtSetup').length > 0){
                if(me.hasClass('verified')){

                    enbled.removeClass('hide');
                    disbled.addClass('hide');
                }
                else{
                    enbled.addClass('hide');
                    disbled.removeClass('hide');
                }
                //also toggle the text panel if SMS not enabled (just in case user was on that panel)
                if(enbled.hasClass('hide'))
                    inqRow.find('.contact-btn-wrapper:first').click();
            }

        }
        else{
            topAlert(Plex.agencyInquiries.error);
        }
        
        Plex.agencyInquiries.hideLoader();
   });

});

//////////////////////////////////////////////////////////////
$(document).on('click', '.upload-menu-toggle', function(){

    $('.upload-type-menu').slideUp(300);

    slideMenu( $(this).find('.upload-type-menu'), 300);
});


//////////////////////////////////////////////////////////////
$(document).on('mouseenter', '.upload-icon-hover', function(e){
    e.stopPropagation();
    $(this).closest('.upload-menu-toggle').find('.upload-type-tooltip').fadeIn(100);
});

//////////////////////////////////////////////////////////////
$(document).on('mouseleave', '.upload-icon-hover', function(e){
    e.stopPropagation(); 
    $(this).closest('.upload-menu-toggle').find('.upload-type-tooltip').fadeOut(100);
});

//////////////////////////////////////////////////////////////
$(document).on('mouseenter', '.upload-change-type', function(e){
    e.stopPropagation();
    var type = $(this).data('type');
    var top = $(this).position().top + 56; //e.pageY - $(this).offset().top;
    var tooltip = $(this).closest('.upload-menu-container').find('.upload-option-tooltip.' + type);

    tooltip.css('top', top);

    tooltip.fadeIn(100);
});

 //////////////////////////////////////////////////////////////
$(document).on('mouseleave', '.upload-change-type', function(e){
    e.stopPropagation();
    var type = $(this).data('type');
    var tooltip = $(this).closest('.upload-menu-container').find('.upload-option-tooltip.' + type);

    tooltip.fadeOut(100)
});


/////////////////////////////////////////////////////////
$(document).on('click', function(e){

   $('.upload-type-tooltip').fadeOut(100);
   $('.upload-option-tooltip').fadeOut(100);


   if($(e.target).closest('.upload-menu-toggle').length === 0)
        $('.upload-type-menu').slideUp(300);            

});


///////////////////////////////////////////////////////////////
$(document).on('click','.upload-change-type', function(){

    var type = $(this).data('type'); //new type user intends to change to
    var id = $(this).closest('.edit-upload-item-container').find('.remove-upload-btn').data('transcript-id'); //transcript id
    var cont = $(this).closest('.edit-upload-item-container').find('.upload-menu-toggle');
    var menuToggleIcon = cont.find('.upload-icon-hover');
    var ctype = cont.data('ctype'); //current type

    menuToggleIcon.removeClass(ctype);
    menuToggleIcon.addClass(type);
    menuToggleIcon.attr('data-ctype', type);

    cont.attr('data-ctype', type);
    cont.data('ctype', type);
    cont.attr('data-transcript-id', id);
    cont.attr('data-changed', '1');
});

/********************** edit dropdown profile handlers ************************/

///// dirty flag for changed inputs
// do not forget -- //on submit must clear all flags
$(document).on('change', '.salesProfileEdit input', function(e){
    $(this).attr('data-changed', '1');
});

$(document).on('change', '.salesProfileEdit select', function(e){
    $(this).attr('data-changed', '1');
});

//////// edit student button handler ///////////
$(document).on('click','.edit-student', function(e){


    var el = $(e.target).closest('.regular-actions');
    var pPane =  el.closest('.inquirie_row').find('.sales-student-pane');
    var other = el.closest('.inquirie_row').find('.sales-student-edit-pane');
    var contactPane = el.closest('.inquirie_row').find('.contact-pane-wrapper');
    
    el.hide();
    pPane.hide();
    contactPane.hide();
    contactPane.removeClass('opened');

    other.show();
    other.find('.edit-actions').show();     

});

////////// save edit student dropdown profile (sales) ///
$(document).on('click', '.save-edit-btn', function(e){
    // e.preventDefault();
    var row = $(this).parents('.inquirie_row');
    var dataObj = {};
    var majors = [];
    var bday_str = [];
    var that = $(this);

    var currUploads = []; ///store current upload types
                          // to cahnge incons on summary row

    
    dataObj['uploadTypeChange'] = [];
    dataObj['transcript_labels'] = [];


    //will manually check form -- abide not quite working
    if( row.find('#salesProfileEdit input[data-invalid]').length){
        topAlert(Plex.agencyInquiries.editFormErrorInputs);
        return;
    }


    //only send fields that have been changed
    row.find('.salesProfileEdit  [data-changed="1"]').each(function(){
        
        // get all changed inputs as long as they are not empty
        //handle majors, scores, and gpa seperately
        if($(this).attr('name') != 'objMajor' && !$(this).hasClass('userScore') && $(this).attr('name') != 'editGPA' 
        && !$(this).hasClass('bday')
        && !$(this).hasClass('edit-uploads-input')  
        && $(this).val()){
            dataObj[$(this).attr('name')] = $(this).val();
        }
        
        //include scores even if empty
        if($(this).hasClass('userScore'))
            dataObj[$(this).attr('name')] = $(this).val();

        if($(this).attr('name') === 'editGPA'){
            dataObj[$(this).attr('name')] = $(this).val();
        }

        // transcript labels
        if($(this).is('.edit-uploads-input')) {
            var transcript_id = $(this).parents('.edit-upload-item-container').find('.remove-upload-btn').data('transcript-id');
            var new_label = $(this).val();

            dataObj['transcript_labels'].push({
                id: transcript_id,
                new_label: new_label
            });
        }

        //transcript type changes
        if($(this).is('.upload-menu-toggle')){
            var type = $(this).data('ctype');
            var id = $(this).data('transcript-id');

            dataObj['uploadTypeChange'].push({
                    id: id,
                    new_type: type
                });


            row.find('.upload-menu-toggle').each(function(){
                var type = $(this).data('ctype');
               
               currUploads.push(type); 

               currUploads = currUploads.filter(function( item, index, inputArray ) {
                       return inputArray.indexOf(item) == index;
                });
            });
            
        }


       
    });

   if(row.find('.salesProfileEdit  .bday[data-changed="1"]')){

        var y = row.find('.salesProfileEdit [name="birthYear"]').val();
        var m = row.find('.salesProfileEdit [name="birthMonth"]').val();
        var d = row.find('.salesProfileEdit [name="birthDay"]').val();
         

        //if any are empty  -- we do not want to change anything except what is not empty -- so set respective value to what it was on load 
        if(m.trim() === ''){
            m = row.find('.birthday-wrapper').data('m');
        }
        if(d.trim() === ''){
            d = row.find('.birthday-wrapper').data('d');   
        }
        if(y.trim() === ''){
            y = row.find('.birthday-wrapper').data('y');
        }


        //must be in two digit format
        if(m.length === 1){
            m = '0' + m;
        }
        if(d.length === 1){
            d = '0' + d;
        }


        bday_str[0]= y;
        bday_str[1]= m;
        bday_str[2]= d;

        dataObj['birthday'] = bday_str.join('-');
   }else{
        dataObj['birthday'] = null;
   }


    
    //plus majors crumbs
    row.find('.crumb-name').each(function(){
        majors.push($(this).text());
    });
    dataObj['majors'] = majors;
    
    dataObj['user_id'] = row.data('uid');
    


    Plex.agencyInquiries.showLoader();

    $.ajax({
        url: '/agency/inquiries/savePlexussUserInfo',
        type: 'POST',
        data: dataObj,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(response){
        if(response === 'failed')
            topAlert(Plex.agencyInquiries.saveProfileError);

        var el = $(e.target).closest('.edit-actions');
        var pPane =  el.closest('.sales-student-edit-pane');
        var other = pPane.parent().find('.sales-student-pane');
          

        //clear all 'dirty' flags -- for next use
        row.find('.salesProfileEdit  [data-changed="1"]').each(function(){

            $(this).removeAttr('data-changed');

        });

        //want to refresh only ajaxed part
        Plex.agencyInquiries.injectProfile(that);

        //and to change the upload icons in summary row
        var docsRow = row.find('ul.uploaded-docs-list');
        
        if(currUploads.length > 0){
             docsRow.empty();
            
            for(var i in currUploads){
                docsRow.append(Plex.agencyInquiries.makeUploadIcon(currUploads[i]));
            }
        }

    });//end ajax
    
   
});

Plex.agencyInquiries.makeUploadIcon = function(type){

   return '<li class="mr3 has-tip uploaded-docs-thumb uploadDocsSpriteSmall ' +
              type + 
          '" data-tooltip="" aria-haspopup="true" data-selector="tooltip-j4edcohow" aria-describedby="tooltip-j4edcohow" title="">&nbsp;</li>';
};

/////////////
$(document).on('click', '.cancel-edit-btn', function(e){

    var el = $(e.target).closest('.edit-actions');
    var pPane =  el.closest('.inquirie_row').find('.sales-student-pane');
    var other = el.closest('.inquirie_row').find('.sales-student-edit-pane');
    var contactPane = el.closest('.inquirie_row').find('.contact-pane-wrapper');

    el.hide();
    other.hide();

    pPane.show();
    pPane.find('.regular-actions').show();   


});

// Remove upload attachment button
$(document).on('click', '.remove-upload-btn', function(event) {
    var that = $(this),
        inqRow = $(this).parents('.inquirie_row');
        hashed_user_id = inqRow.data('hashed_id'),
        transcript_id = $(this).data('transcript-id'),
        doc_type = $(this).data('doc-type');

    Plex.agencyInquiries.showLoader();

    // console.log(hashed_user_id, transcript_id);
    $.ajax({
        url: '/agency/inquiries/removeTranscriptAttachment',
        type: 'POST',
        data: {
            hashed_user_id: hashed_user_id, 
            transcript_id: transcript_id
        },
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    })
    .done(function(result) {
        
        Plex.agencyInquiries.hideLoader();

        if (result === 'success') {
            topAlert(Plex.agencyInquiries.fileRemoveSuccessMsg);

            if (doc_type == 'prescreen_interview') {
                Plex.agencyInquiries.removePrescreenInterviewLink(inqRow);
            } else {
                inqRow.find('.uploadDoc-box[data-transcript-id="' + transcript_id + '"]').remove();
            }
            
            that.parents('.edit-upload-item-container').remove();

        } else {
            console.log("Failed");
        }
    });
});


Plex.agencyInquiries.removePrescreenInterviewLink = function(inqRow) {
    var interviewSectionTitle = inqRow.find('.prescreen-interview-title'),
        interviewPlayer = inqRow.find('.prescreen-interview-player');

    inqRow.find('.interview-links[data-transcript-id="' + transcript_id + '"]').remove();

    var interviewLinksCount = inqRow.find('.sm2-playlist-wrapper > ul > li').length;

    soundManager.pauseAll();
    
    if (interviewLinksCount === 0) {
        interviewSectionTitle.addClass('hidden');
        interviewPlayer.addClass('hidden');
    }

    Plex.agencyInquiries.resizeInterviewPlayList(inqRow);

}

/////////
$(document).on('click', '.upload_docs_form_p .upload-files-btn', function(e){
    e.preventDefault();
    var modal = $(this).parents('.upload_docs_modal');
    var msg = modal.find('.message-box');  
    var form = $(this).parents('.upload_docs_form_p')[0];
    var mdata = new FormData(form);
    var hid = modal.find('[name="_token"]').val();
    mdata.append('hashed_user_id', hid);
    mdata.append('_token', $('meta[name="csrf-token"]').attr('content'));
  
    msg.text('Sending...');

    $.ajax({
        url: '/ajax/profile/uploadcenter/' + $('.upload_docs_form_p').find('[name="_token"]').val(),
        type: 'POST',
        data: mdata,
        cache: false,
        contentType: false,
        processData: false,
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(response){
        var res = JSON.parse(response);
        var uid = res['user_id'];
        var rmsg = res['msg'];

        var row =  $('.inquirie_row[data-uid="' + uid + '"]');
        var rowT= row.find('.uploadsDoc ul');

        msg.text('Document uploaded successfully!');
        topAlert(Plex.agencyInquiries.fileUploadSuccessMsg);
        
        setTimeout(function(){
            modal.foundation('reveal','close'); 
            $('.reveal-modal-bg').fadeOut(); 
            msg.text("");
        }, 1000);
        // modal.foundation('reveal','close');
        // $('.reveal-modal-bg').fadeOut();

        //append uploads to view
        var displayName = ''; 
        var rowIcon = $('<li></li>');
        var inconT = '';

        switch(res['doc_type']){
            case 'prescreen_interview':
                displayName = 'Interview';
                //if tile on result bar exist -> nothing
                if(!rowT.find('.prescreen_interview').length)
                    rowIcon = $('<li class="uploadDocsSpriteSmall prescreen_interview">&nbsp;</li>');
                break;
            case 'transcript':
                displayName = "Transcript";
                //if tile on result bar exist -> nothing
                if(!rowT.find('.transcript').length)
                rowIcon = $('<li class="uploadDocsSpriteSmall transcript">&nbsp;</li>');
                break;
            case 'financial':
                displayName = "Financial Docs";
                //if tile on result bar exist -> nothing
                if(!rowT.find('.financial').length)
                rowIcon = $('<li class="uploadDocsSpriteSmall financial">&nbsp;</li>');
                break;
            case 'resume':
                displayName = "Resume/Porfolio";
                //if tile on result bar exist -> nothing
                if(!rowT.find('.resume').length)
                rowIcon = $('<li class="uploadDocsSpriteSmall resume">&nbsp;</li>');
                break;
            case 'passport':
                displayName = "Passport";
                //if tile on result bar exist -> nothing
                if(!rowT.find('.passport').length)
                rowIcon = $('<li class="uploadDocsSpriteSmall passport">&nbsp;</li>');
                break;
            case 'toefl':
                displayName = "TOEFL";
                //if tile on result bar exist -> nothing
                if(!rowT.find('.toefl').length)
                rowIcon = $('<li class="uploadDocsSpriteSmall toefl">&nbsp;</li>');
                break;
            case 'ielts':
                displayName = "IELTS";
                //if tile on result bar exist -> nothing
                if(!rowT.find('.ielts').length)
                rowIcon = $('<li class="uploadDocsSpriteSmall ielts">&nbsp;</li>');
                break;
            case 'essay':
                displayName = "Essay";
                //if tile on result bar exist -> nothing
                if(!rowT.find('.essay').length)
                rowIcon = $('<li class="uploadDocsSpriteSmall essay">&nbsp;</li>');
                break;
            default:
                displayName = 'Other';
                rowIcon = $('<li class="uploadDocsSpriteSmall other">&nbsp;</li>');
                break;
        }

        var toolTip = ""; // Only use if label is greater than 17 characters
        var transcriptLabel = ""; // Only use if label is not empty

        if (res['transcript_label'] && res['transcript_label'].toString().trim() !== "") {
            transcriptLabel = res['transcript_label'];

            if (transcriptLabel.length >= 17) {
                transcriptLabel = transcriptLabel.substr(0, 13) + "...";
                toolTip = res['transcript_label'];
            }
        }

        var interviewCount = 0;

        if (res['doc_type'] === 'prescreen_interview') {
            interviewCount = row.find('.interview-links').length + 1;

            // Decrement to replace removed interview. This case happens if users delete interviews and add new ones in unpredictable order.
            while (row.find('.edit-uploads-input[placeholder="Interview ' + interviewCount + '"]').length > 0) {
                interviewCount--;
            }

            var interviewPlayerLink = 
                                '<li class="interview-links" data-transcript-id="' + res['transcript_id'] + '">' +
                                    '<div class="sm2-row">' +
                                        '<div class="sm2-col sm2-wide">' +
                                            '<a type="' + res["mime_type"] + '" href="' + res["path"] + '">' + 
                                               '<b>' + (transcriptLabel || displayName + ' ' + interviewCount) + '</b>' +
                                            '</a>' +
                                        '</div>' +
                                        '<div class="sm2-col">' +
                                            '<a href="' + res["path"] + '" target="_blank" title="Download" class="sm2-icon sm2-music sm2-exclude">Download this track</a>' +
                                        '</div>' +
                                    '</div>' +
                                '</li>';

            row.find('.sm2-playlist-wrapper > ul').append(interviewPlayerLink);
            row.find('.prescreen-interview-title').removeClass('hidden');
            row.find('.prescreen-interview-player').removeClass('hidden');

            Plex.agencyInquiries.resizeInterviewPlayList(row);

        } else {
            var uploadTile = $('<div class="uploadDoc-box" data-transcript-id="' + res['transcript_id'] + '">' +
                                    '<div class="row">' +
                                        '<div class="column small-12">'+
                                            '<div class="uploadDocsSpriteLarge ' + res['doc_type'] + ' "></div>' +
                                            
                                            '<div class="row uploadDetail">' +
                                                '<div class="column small-12" title="' + toolTip + '"><b> ' + (transcriptLabel || displayName) + '</b></div> ' +
                                                '<div class="column small-3"><a href="#" onClick="openTranscriptPreview(this);" data-transcript-name="' + res['file_name'] + '">View</a></div>' +
                                                '<div class="column small-1">|</div>' +
                                                '<div class="column small-3 end"><a href="' + res['path'] + '"> Download</a></div> ' +   
                                                '</div></div></div></div>' );                                                         

            row.find('.uploads-display-container > .prescreen-interview-title').before(uploadTile);
        }

        var editUploadTile = '<div class="row collapse edit-upload-item-container mt14">';
        if ( res['doc_type'] == 'prescreen_interview' )
            editUploadTile += '<div class="column mt2 small-1 uploadDocsSpriteInterview interview"></div>';
        else 
            editUploadTile += '<div class="column mt2 small-1 uploadDocsSpriteLarger ' + res['doc_type'] + '"></div>';                 
        editUploadTile += '<div class="column small-9 validSelecto">';

        editUploadTile += '<input value="' + res['transcript_label'] + '" class="edit-uploads-input" placeholder="' + displayName + ((interviewCount > 0) ? " " + interviewCount : "") + '" name="edit-uploads-label" type="text">';
        editUploadTile +='</div><div class="column small-1 remove-upload-btn" data-transcript-id="' + res['transcript_id'] + '" data-doc-type="' + res['doc_type'] + '">Remove</div></div>'
        
        //if this is the firt upload -- remove 'No .. uploads' message
        var none = row.find('.uploads-none');
        if(none.length){
            none.remove();
        }

        row.find(".uploads-edit-container").append(editUploadTile);

        rowT.append(rowIcon);
        
    });

});
/******* end uploads *******************/

//restore student from list
Plex.agencyInquiries.restoreStudent = function(student, studentID){

	$.ajax({
		url: '/agency/inquiries/setRestore/' + studentID,
		type: 'POST',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	})
	.done(function() {
		//restore this student row from list after ajax call
		$(student).closest('.inquirie_row').remove();
		topAlert(Plex.agencyInquiries.restoreFromRemovedMsg);
	});
}

Plex.agencyInquiries.requestToBecomeMember = function(){

	$.ajax({
		url: '/admin/ajax/requestToBecomeMember',
		type: 'POST',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	})
	.done(function(){

	});
}


$(document).on('click', '.item.inquirie_row > .messageName', function(){
	// var is_paid = $(this).data('is-paid');
	// if( is_paid === 1 ){
		inquiriesToggleMenu(this);
	// }else{

	// }
});

function inquiriesToggleMenu(el){
	var _this = $(el);
	var inqRow = _this.closest('.inquirie_row');
	var hashed_id = inqRow.data('hashed_id');
	var hashedid = _this.data('hashedid');
	var inqdropdown = inqRow.find('.dropdownbox');
	var all_inquiries_container = $('.each-inquirie-container');
	var profile_pane = inqRow.find('.student-profile-pane');

	//scroll to bottom of container when multiple student profile panes are open
	// $(all_inquiries_container).stop().animate({
	//   scrollTop: inqRow.position().top - all_inquiries_container.position().top
	// }, 500);

	$(el).find('.arrow').toggleClass('opened');

	if (_this.hasClass('active')) {
		_this.removeClass('active');
		inqdropdown.fadeOut(300);
		inqdropdown.addClass('hidden');

	} else if (inqdropdown.length > 0 && !_this.hasClass('active')) {
		_this.addClass('active');
		inqdropdown.removeClass('hidden');
		inqdropdown.fadeIn(300);

	} else {
		Plex.agencyInquiries.showLoader();
		Plex.agencyInquiries.injectProfile(_this, function(){
			if( $(inqRow).hasClass('unread') ){
				$(inqRow).removeClass('unread').addClass('read');
			}

			// commented out set agency view
			$.ajax({
				url: '/agency/ajax/setAgencyViewingYourProfile',
				type: 'POST',
				data: {hashedid: hashedid},
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			})
			.done(function(data) {
				if( $(inqRow).hasClass('unread') ){
					$(inqRow).removeClass('unread').addClass('read');
				}
			});

                $(document).foundation('equalizer', 'reflow');
                $(document).foundation('interchange', 'reflow');
                Plex.agencyInquiries.initNewOwl();

                Plex.agencyInquiries.initMajors(inqRow.data('uid'));

			Plex.agencyInquiries.hideLoader();
		});
		
	}

	//re-initialize foundation equalizer
	// $(document).foundation('equalizer', 'reflow');

}

Plex.agencyInquiries.injectProfile = function($_element, callback) {
	var _this = $_element;
	var inqRow = _this.closest('.inquirie_row');
	var hashed_id = inqRow.data('hashed_id');
	var inqdropdown = inqRow.find('.profile.dropdownbox');

	var all_inquiries_container = $('.each-inquirie-container');
	var profile_pane = inqRow.find('.student-profile-pane');
	$.ajax({
		url: '/agency/inquiries/loadProfileData',
		type: 'POST',
		data: { hashed_id: hashed_id, currentPage: $('body').attr('id') },
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	})
	.done(function(response) {
		Plex.agencyInquiries.hideLoader();

		inqRow.find('.student-profile-data').html(response);
        inqRow.find('#salesProfileEdit').foundation('abide');

		inqdropdown = inqRow.find('.profile.dropdownbox');

		_this.addClass('active');
		inqdropdown.removeClass('hidden');
		inqdropdown.fadeIn(400);

        if (inqRow.find('.prescreen-interview-player').length > 0) {
	        Plex.agencyInquiries.restartSoundManager(inqRow);
            // soundManager.onready(basicMP3Player.init);
	    }

	    if (callback) {
	    	callback();
	    }

        Plex.agencyInquiries.initializeStudentCarousel(inqRow);
	});
}

Plex.agencyInquiries.initNewOwl = function(){
    //get all unset owls
    var unsetowls = $('.student-profile-also-interested-in[data-isset="0"]');

    //if there are any new unset carousels, then set them, otherwise there are no new results so don't need to do anything
    if( unsetowls.length > 0 ){
        //add unique class name
        unsetowls.addClass('owl-' + Plex.agencyInquiries.unique);

        //get new owls
        var owl = $('.student-profile-also-interested-in.owl-'+Plex.agencyInquiries.unique);

        //set owlcarousel to new owl
        owl.owlCarousel({
            autoPlay: false,
            items : 4,
            itemsDesktop : [1199,3],
            itemsDesktopSmall : [979,3],
            itemsTablet : [768, 3],
            itemsMobile : [479, 1],
            pagination : false,
            beforeMove : function(elem){
                var itemsVisible = this.visibleItems;
                var totalItems = this.itemsAmount;
                var itemsAllowed = this.options.items;

                Plex.agencyInquiries.hideCompetitionCarouselArrows(totalItems, itemsAllowed);
            }
        });

        //increment unique num
        Plex.agencyInquiries.unique++;
        //make unset owl set
        unsetowls.attr('data-isset', 1);

        //click event for competition carousel arrow clicks
        $('.competitor-carousel-arrow').click(function(){
            Plex.agencyInquiries.moveCompetitionCarousel(owl, this);
        });
    }
}

//hide arrows if not enough competition
Plex.agencyInquiries.hideCompetitionCarouselArrows = function( items_total, items_allowed ){
    if( items_total <= items_allowed ){
        $('.competitor-carousel-arrow').hide();
    }else{
        $('.competitor-carousel-arrow').show();
    }
}

Plex.agencyInquiries.moveCompetitionCarousel = function( owls, arrow ){
    if( $(arrow).hasClass('leftarrow') ){
        owls.trigger('owl.prev');
    }else{
        owls.trigger('owl.next');
    }
}


Plex.agencyInquiries.initMajors = function(uid){
    var txt = '';

    //empty majorsList
    Plex.agencyInquiries.majorsList.clearList();

    $(".inquirie_row[data-uid='" + uid + "'] .sales-student-edit-pane").find('.major-listing').each(function(){
        
        //if not empty
        if($(this).text().trim()){
           
            txt = $(this).text().trim();

            //build crumb list
            Plex.agencyInquiries.majorsList.addTextCrumb(txt);
        }
    });

    //append crumbs to view
    for(var i = 0; i < Plex.agencyInquiries.majorsList.length(); i++){     
        $("[data-uid='" + uid + "']").find('.majors_crumb_list').append(Plex.agencyInquiries.majorsList.crumbs[i].getCrumb());
    } 
}

Plex.agencyInquiries.restartSoundManager = function(inqRow){
    if (inqRow.find('.prescreen-interview-player').length > 0 || inqRow.find('.prev-call-container').length > 0) {
        soundManager.pauseAll();
        soundManager.soundIDs.forEach(function(id) {
            soundManager.destruct(id);
        });
        soundManager.reboot();
    }
}

Plex.agencyInquiries.resizeInterviewPlayList = function(inqRow) {
    var isOpen = inqRow.find('.prescreen-interview-player').hasClass('playlist-open'),

        playListUL = inqRow.find('.sm2-playlist-drawer')[0];

    playListUL.style.height = (isOpen ? playListUL.scrollHeight : 0) + 'px';
}

function SendRecruithandShakeStatus( userId , status, elem, recommended ){
	//get the inquire row.
	var dataRow = $(elem).parents('.inquirie_row');

	//find the hand shake indicators and turn them off.
	dataRow.find('.yesnobuttons').removeClass('selected');
	dataRow.find('.messageIconArea').removeClass('selected');

	var yesStatus = '';
	var noStatus = '';
	var	messageIconStatus = '';

	var set_route = '';

	//set route - different routes for recommended students and recruited students
	if( recommended === true ){
		set_route = '/agency/setRecommendationRecruit/';
	}else{
		set_route = '/agency/inquiries/setRecruit/';
	}

	//Set status variables
	if (status == 1) {
		yesStatus = 'selected';
		messageIconStatus = 'selected';
	} else {
		noStatus = 'selected';

	};

	$.ajax({
		url: set_route + userId+'/' + status,
		type: 'POST',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	})
	.done(function() {
		//set all the indicators status one call back.
		dataRow.find('.yesbutton').addClass(yesStatus);
		dataRow.find('.messageIconArea').addClass(messageIconStatus);
		dataRow.find('.nobutton').addClass(noStatus);

		if( status === 1 ){
			if( recommended === true ){
				topAlert(Plex.agencyInquiries.addedFromRecommendedToPending);
				//remove this student row from list after ajax call
				$(elem).closest('.inquirie_row').remove();
			}else{
				topAlert(Plex.agencyInquiries.addedFromInquiriesToApproved);
			}
		}
	});
}

Plex.agencyInquiries.initializeStudentCarousel = function(inqRow) {
    var uid = inqRow.data('uid');
    var fitBtn = inqRow.find('.fit-competitor-btns.fit'),
        competitorBtn = inqRow.find('.fit-competitor-btns.competitor'),
        fitCarousel = inqRow.find('.fit-carousel'),
        competitorCarousel = inqRow.find('.competitor-carousel'),
        appliedCarousel = inqRow.find('.applied-carousel')
        // statusBtn = inqRow.find('.fit-status');

    var student_pane = inqRow.find('.sales-student-pane');
    var shouldShowMatchedColleges = student_pane.data('show-matched');
    
    if( +shouldShowMatchedColleges ) {
        Plex.agencyInquiries.getMatchedCollegesForThisUser(uid, fitCarousel.find('.student-profile-matched'));
    }

    if( fitBtn.length > 0 || competitorBtn.length > 0) { 
        _Move.initRx(
            fitBtn, 
            competitorBtn, 
            fitCarousel, 
            competitorCarousel
        );
    }
}

Plex.agencyInquiries.getMatchedCollegesForThisUser = function(user_id, carousel){
    var loader = carousel.find('.matched-loading');

    loader.show();

    $.ajax({
        type: 'POST',
        url: '/agency/inquiries/getMatchedCollegesForThisUser',
        data: {user_id: user_id},
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        success: function(schools){
            loader.hide();
            var parsed_schools = JSON.parse(schools);

            if( parsed_schools.length ) Plex.agencyInquiries.buildMatchedSchools(parsed_schools, carousel);

            $(document).foundation('equalizer', 'reflow');
            $(document).foundation('interchange', 'reflow');
            Plex.agencyInquiries.initNewMatchedOwl();
        },       
    });
}

Plex.agencyInquiries.initNewMatchedOwl = function(){
    //get all unset owls
    var unsetowls = $('.student-profile-matched[data-isset="0"]');
    // console.log('unset: ', unsetowls);

    //if there are any new unset carousels, then set them, otherwise there are no new results so don't need to do anything
    if( unsetowls.length > 0 ){
        //add unique class name
        unsetowls.addClass('owl-' + Plex.agencyInquiries.uniqueMatched);

        //get new owls
        var owl = $('.student-profile-matched.owl-'+Plex.agencyInquiries.uniqueMatched);

        //set owlcarousel to new owl
        owl.owlCarousel({
            autoPlay: false,
            items : 4,
            itemsDesktop : [1199,3],
            itemsDesktopSmall : [979,3],
            itemsTablet : [768, 3],
            itemsMobile : [479, 1],
            pagination : false,
            beforeMove : function(elem){
                var itemsVisible = this.visibleItems;
                var totalItems = this.itemsAmount;
                var itemsAllowed = this.options.items;

                Plex.agencyInquiries.hideCompetitionCarouselArrows(totalItems, itemsAllowed);
            }
        });

        //increment uniqueMatched num
        Plex.agencyInquiries.uniqueMatched++;
        //make unset owl set
        unsetowls.attr('data-isset', 1);

        //click event for competition carousel arrow clicks
        $('.competitor-carousel-arrow').click(function(){
            Plex.agencyInquiries.moveCompetitionCarousel(owl, this);
        });
    }
};

Plex.agencyInquiries.buildMatchedSchools = function(schools, carousel){
    var html = '';

    $.each(schools, function(){
        html += '<div class="item text-center">';

        html +=     '<div class="row">';
        html +=         '<div class="column small-12">';
        html +=             '<div class="college-logos-background" data-interchange="['+this.logo_url+', (default)]"></div>';
        html +=         '</div>';
        html +=     '</div>';

        html +=     '<div class="row" data-equalizer-watch>';
        html +=         '<div class="column small-12 college-competitor-name competition-school-name-lg">';
        html +=             '<a target="_blank" href="/college/'+this.slug+'">'+this.school_name+'</a>';
        html +=         '</div>';
        html +=     '</div>';

        html +=     '<div class="row">';
        html +=         '<div class="column small-12 page-views">';

        if( this.hasApplied === 1 )      html += '<div class="fit-status good" data-id="'+this.college_id+'" data-school="'+this.school_name+'">&check;</div>';

        html +=         '</div>';
        html +=     '</div>';

        html += '</div>';
    });

    carousel.html(html);
    Plex.agencyInquiries.buildMoveMenus(schools);
}

Plex.agencyInquiries.buildMoveMenus = function(schools){
    var html = '',
        div = $('#all-move-menus'),
        student_user_id = div.data('school-id');

    $.each(schools, function(){
        html += '<div class="move-student-menu" data-school="'+this.school_name+'">';
            html += '<div class="school">';
                html += '<div class="logo" data-interchange="['+this.logo_url+', (default)]"></div>';
                html += '<div>'+this.school_name+'</div>';
                html += '<div>Cambridge, MA</div>';
            html += '</div>';
            html += '<div style="margin: 10px 0;"><b>Select Portal</b></div>';
            html += '<ul></ul>';
            html += '<div class="actions">';
                html += '<div><u class="move-back">Back</u></div>';
                html += '<div><div class="move-btn" data-school="'+this.college_id+'" data-user="'+student_user_id+'">Move</div></div>';
            html += '</div>';
        html += '</div>';
    }); 

    div.html(html);
}

/********** majors handlers ************************************************/
////////// majors for sales edit profile ////////////////
$(document).on('keyup', '#objMajor', function(e){

    showMajorsList(e);
    getMajors(e);
});

/////
$(document).on('click', '.major-listing-cont', function(e){

    addMajorsHandler(e, Plex.agencyInquiries.majorsList);

});

/////////// 
$(document).on('click', '.obj-close-btn', function(e){

     //remove from crumb list' -- should return new list or null if not able to remove/value not found
    if(Plex.agencyInquiries.majorsList.removeCrumb( e.target.parentNode.getElementsByClassName('crumb-name')[0].innerHTML ) != null){
        
        //if removed from list successfully, remove rendered crumb from view
        var crumb = e.target.parentNode;
        crumb.remove();

        //if list now less than max -- hide the 'reached max note'
        console.log(Plex.agencyInquiries.majorsList.length());
        if(Plex.agencyInquiries.majorsList.length() < 4 ){
            $(e.target).closest('.majors-container').find('#max-note').hide();
        }
    }
    
});
/************* end majors handlers **********************/

//abide validation pattern definition
$(document).foundation({
    abide : {
        patterns: {
            age: /^([1-9]?\d|100)$/, /*
            gpa: /^(?:[0-3]?\.{1}\d{1,2}|[0-4]{1}\.{1}[0]{1,2}|[0-4]{1}){1}$/,    // /^(?!0$)(([0-3]){1}\.([0-9]){1,2}|4\.(0){1,2}|([0-4]){1})$/,
            toefl: /^([0-9]?[0-9]|[1][0-1][0-9]|12[0])$/,
            ielts: /^[0-9]{1}$/,
            sat: /^([6-9][0-9][0]|[1][0-9][0-9][0]|[2][0-3][0-9][0]|[2][4][0][0])$/,
            act: /^([1-9]|[1-2][0-9]|[3][0-6])$/, */
            name: /^[a-zA-Z\,\'\-]*$/,
            nameemail: /^[a-zA-Z0-9,.-_'!#$%&*+/=?^`{|}~ ]$/,
            address: /^[a-zA-Z0-9\.,#\-\' ]+$/,
            state: /^[a-zA-Z\.\-\' ]+$/,
            city: /^[a-zA-Z\.\-\' ]+$/,
            zip: /^[a-zA-Z0-9\.,\- ]+$/,
            phoneinput : /^\s*(?:\+?(\d{1,3}))?[-. (]*(\d{3})[-. )]*(\d{3})[-. ]*(\d{4})(?: *x(\d+))?\s*$/,
            alpha_space : /^[a-zA-Z ]*$/,
            college_name: /^[\s]*(([a-zA-Z])+([\s]|[\-]|['])*)+[\s]*$/,
            dashes_only: /^[0-9-]*$/,
            number: /^[-+]?[0-9]\d*$/,
            month : /^[-+]?[0-9]\d*$/,
            gpa: /^(([0-3]){1}\.([0-9]){1,2}|4\.(0){1,2}|([0-4]){1})$/,
            max_weighted_gpa: /^(([0-9])+|([0-9])+\.([0-9]){1,2})$/,
            act: /^([0-9]|[1-2][0-9]|[3][0-6])$/,
            sat: /^([6-9][0-9][0]|[1][0-9][0-9][0]|[2][0-3][0-9][0]|[2][4][0][0])$/,
            sat_total:/^([6-9][0-9][0]|[1][0-9][0-9][0]|[2][0-3][0-9][0]|[2][4][0][0])$/,
            toefl: /^[\s]*([0-9]{1,2}|[1][0-1][0-9]|[1][2][0])[\s]*$/,
            ielts: /^[\s]*([0-8]{0,1}[\.][0-9]|[0-9]|9.0)[\s]*$/,
            itep: /^[\s]*([0-5]{0,1}[\.][0-9]|[0-6]|6.0)[\s]*$/,
            pte: /^[\s]*([1-8][0-9]|90)[\s]*$/
        },
        validators:{
            monthChecker: function(el, required, parent){
                var value = $(el).val();
                if ( ($.isNumeric(value) && value <= 12 && value > 0 )|| value.trim() === '' ) {
                    $('.datedMonthError').css('display', 'none');
                    return true;
                } else{
                    $('.datedMonthError').css({
                        'display': 'inline-block',
                        'margin-bottom': '2px'
                    });
                    return false;
                };
            },
            dayChecker: function(el, required, parent){
                var value = $(el).val();
                if ( ($.isNumeric(value) && value <= 31 && value >= 1)  || value.trim() === '' ) {
                    $('.datedDayError').css('display', 'none');
                    return true;
                } else{
                    $('.datedDayError').css({
                        'display': 'inline-block',
                        'margin-bottom': '2px'
                    });
                    return false;
                };
            },
            yearChecker: function(el, required, parent){
                var value = $(el).val();
                var currentDate = (new Date).getFullYear();
                var minAgeAllowed = currentDate - 13;

                if( value.trim() === ''){
                    $('.datedYearError').css('display', 'none');
                    $('.datedUnderAge').css('display', 'none');
                    return true;
                }
                else if( !$.isNumeric(value) || value > currentDate || value <=  currentDate - 100 ){
                    $('.datedYearError').css({
                        'display': 'inline-block',
                        'margin-bottom': '2px'
                    });
                    return false;
                } else if ( value > minAgeAllowed) {
                    $('.datedUnderAge').css({
                        'display': 'inline-block',
                        'margin-bottom': '2px'
                    });

                    $('.agenotice').css({
                        'color':'#FF7F00',
                        'font-weight':'bold'
                    });
                    return false;
                }else{
                    $('.datedYearError').css('display', 'none');
                    $('.datedUnderAge').css('display', 'none');
                    return true;
                };
            }
        }
    }

});