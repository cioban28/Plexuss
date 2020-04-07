// userFeeback.js

var feedbackModal = {
	id: '#userFeedbackModal',
	open: function(){
		$(this.id).foundation('reveal', 'open');
	},
	hasApplied: function(rec_id, pr_id, applied){
		var obj = {};

		obj.applied = applied;
		if( rec_id ) obj.rec_id = rec_id;
		if( pr_id ) obj.pr_id = pr_id;

		$.ajax({
			url: '/ajax/saveAppliedToSchools',
			type: 'POST',
			data: obj,
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success: function(data){
				appliedToOthersModal.open();
			},
			error: function(err){
				console.log('err');
			}
		});
	},
	render: function(){
		var modal = $(this.id),
			name = modal.data('school-name');

		modal.find('.question span').html(name);
		this.open();
	},
};

var appliedToOthersModal = {
	id: '#appliedToOtherSchoolsModal',
	search_id: '#applied_search_results',
	applied_list: [],
	open: function(){
		$(this.id).foundation('reveal', 'open');
	},
	close: function(){
		$(this.id).foundation('reveal', 'close');
	},
	openSearch: function(){
		$(this.search_id).slideDown(150);
	},
	closeSearch: function(){
		$(this.search_id).slideUp(150);
	},
	searchSchool: function(val){
		var _this = this;

		$.ajax({
			url: '/ajax/searchForCollegesForThisUser',
			type: 'POST',
			data: {input: val},
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success: function(data){
				_this.updateSearchResults(data);
			},
			error: function(err){
				console.log('err: ', err);	
			}
		});
	},
	updateSearchResults: function(list){
		var results = '';

		$.each(list, function(){
			results += '<div class="result" data-school-id="'+this.id+'" data-pr="'+this.pr_id+'" data-rec="'+this.rec_id+'">'+this.school_name+'</div>';
		});

		$('#applied_search_results').html(results);
	},
	updateAppliedList: function(){
		var applied = '';

		$.each(this.applied_list, function(){
			applied += '<div class="applied-to">';
				applied += '<div>'+this.school_name+'</div>';
				applied += '<div class="remove-applied" data-school-id="'+this.id+'">x</div>';
			applied += '</div>';
		});

		$('#_applied_results').html(applied);
	},
	submitAppliedList: function(){
		var list = this.applied_list,
			_this = this;

		if( list.length > 0 ){
			$.each(list, function(){
				var obj = {};
				obj.applied = 'yes';
				if( this.id ) obj.school_id = this.id;
				if( this.pr_id ) obj.pr_id = this.pr_id;
				if( this.rec_id ) obj.rec_id = this.rec_id;

				_this.postApplied(obj);
			});	

			_this.close();
		}
	},
	postApplied: function(_data){
		$.ajax({
			url: '/ajax/saveAppliedToSchools',
			type: 'POST',
			data: _data,
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success: function(data){
				console.log(data);
			},
			error: function(err){
				console.log(err);
			}
		});
	},
	addSchool: function(school){
		var already_exists = false,
			btn = $(this.id+' .submit');

		$.each(this.applied_list, function(){
			if( this.id === school.id ) already_exists = true;
		});

		// prevent duplicate from being added to list
		if( !already_exists ){
			this.applied_list.push(school);
			this.updateAppliedList();
		}

		// if submit btn is disabled, enable it
		if( btn.is('[disabled=disabled]') ) btn.prop('disabled', false);

		// close search either way
		this.closeSearch();
	},
	removeSchool: function(school){
		// filter out school selected for removal
		this.applied_list = this.applied_list.filter(function(elem){
			return elem.id !== school.id;
		});

		// if list is empty, disable submit btn
		if( this.applied_list.length === 0 ){
			$(this.id+' .submit').prop('disabled', true);
		}

		// update list view
		this.updateAppliedList();
	},
}

// tmp - should have some trigger to show or not
$(document).ready(function(){
	feedbackModal.render();
});

// on feedback modal yes click, post yes for applied, then show appliedToOtherschoolsModal
$(document).on('click', feedbackModal.id + ' .yes', function(){
	var _this = $(this),
		rec_id = _this.data('rec'),
		pr_id = _this.data('pr');

	feedbackModal.hasApplied(rec_id, pr_id, 'yes');
});

// on feedback modal no click, show next modal (appliedToOtherSchoolsModal)
$(document).on('click', feedbackModal.id + ' .no', function(){
	var _this = $(this),
		rec_id = _this.data('rec'),
		pr_id = _this.data('pr');

	feedbackModal.hasApplied(rec_id, pr_id, 'no');
});

// on "haven't applied to any schools" click, close appliedToOtherSchoolsModal
$(document).on('click', appliedToOthersModal.id+' .havent-applied', function(){
	appliedToOthersModal.close();
});

// on search input focus, open search
$(document).on('focus', '#_search_applied', function(){
	appliedToOthersModal.openSearch();
});

// search for schools based on what user has entered
$(document).on('keyup', '#_search_applied', function(){
	var val = $(this).val();
	appliedToOthersModal.searchSchool(val);
});

// close search results container if click isn't in the search results container or the search input
$(document).on('click', function(e){
	var elem = $(e.target);

	if( !elem.hasClass('result') && !elem.hasClass('search') && elem.attr('id') !== '_search_applied' ){
		appliedToOthersModal.closeSearch();
	}
});

// capture selected schools name and id and save it to applied_list
$(document).on('click', '#appliedToOtherSchoolsModal .result', function(){
	var _this = $(this),
		school_id = _this.data('school-id'),
		rec_id = _this.data('rec'),
		pr_id = _this.data('pr'),
		name = _this.html().trim();

	appliedToOthersModal.addSchool({
		id: school_id, 
		school_name: name,
		pr_id: pr_id,
		rec_id: rec_id,
	});	
});

// submit applied list on submit btn click
$(document).on('click', '#appliedToOtherSchoolsModal button.submit', function(){
	appliedToOthersModal.submitAppliedList();	
});

// x btn click to remove selected school from the applied list
$(document).on('click', '#appliedToOtherSchoolsModal .remove-applied', function(){
	var school_id = $(this).data('school-id');
	appliedToOthersModal.removeSchool({id: school_id});	
});

$(document).on('click', '#collegeapplynow', function(){
	$.ajax({
        url: '/applyNowClicked',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        data: {slug: $(this).data('slug'), source: 'user_home', org_app_url: $(this).data('url')},
        type: 'POST'
    }).done(function(data){
		// console.log(data);
	});
});