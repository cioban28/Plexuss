// infilawSurvey.js
var iSurvey = {

	user: {
		id: 0,
		setId: function(id){
			this.id = id;
		}
	},
	saveRoute: '/infilaw/survey/saveInfo',
	step: {
		num: 0,
		valid: false,
		setValidity: function(valid){
			this.valid = valid;
		},
		stipend_interest: false,
		setInterest: function(interest){
			this.stipend_interest = interest;
		},
		qualified: false,
		setQualified: function(qual){
			this.qualified = qual;
		},
		stepRoute: '/infilaw/survey/step/',
		setStepNum: function(step){
			this.num = step;
		},
		nextStepRoute: '',
		setNextRoute: function(href){
			this.nextStepRoute = href;
		},
		prevStepRoute: '',
		setPrevRoute: function(href){
			this.prevStepRoute = href;
		}
	},
	
};

//on ready, initialize
$(document).ready(function(){
	//get and set user id
	var getId = $('.survey-container').data('uid');
	iSurvey.user.setId(getId);

	//get and set step num
	var getStep = $('.survey-container').data('step');
	iSurvey.step.setStepNum(parseInt(getStep));

	if( iSurvey.step.num === 8 ){
		var getQualified = $('.survey-page').data('qualified');
		iSurvey.step.setQualified(getQualified);
	}
});


//on input change, do something
$(document).on('change','.survey-field', function(e){
	var _this = $(this);
	var formType = _this.data('formtype');
	var validateFuncName = formType + 'Validation';
	var is_valid = iSurvey[validateFuncName](_this);

	if( is_valid ){
		if( iSurvey.step.num === 10 && iSurvey.step.qualified ){
			//set stipend interest to true
			if( _this.val() === 'Yes' )
				iSurvey.step.setInterest( !!_this.val() );
		}
		var _this_e = e;
		iSurvey.prepDataForPosting(_this, _this_e);
	}
});


//on next step click, do something
$(document).on('click', '.nextstep', function(e){
	// var all_valid = true, all_fields;
	// e.preventDefault();

	// //get all fields on this page
	// all_fields = iSurvey.getCurrentPageFields();

	// //validate all fields on page
	// all_valid = iSurvey.fullPageValidate();

	// //if all fields are valid, then post, else nothing
	// if( all_valid ){
	// 	iSurvey.step.setValidity(all_valid);
	// 	iSurvey.prepDataForPosting(all_fields, e);
	// }

	// iSurvey.nextStepFunction(e);
	iSurvey.nextStepFunction(e, null);
});


iSurvey.getCurrentPageFields = function(){
	return $('.survey-field');
}


//creating FormData and appending user id, then saving
iSurvey.prepDataForPosting = function(field, _this_e){
	var data = new FormData();
	var name = '', val = '', goToNextArr = {bool:false, selectedNo: false};

	field.each(function(){
		name = $(this).attr('name');
		val = $(this).val();
		data.append(name, val);
	});

	//append current user hashed id
	data.append('hashed_infilaw_user_id', iSurvey.user.id);

	if(field.hasClass('goToNext')){
		goToNextArr.bool = true;
	}

	// if the question has the following class and has No value we want to move to next question
	if(field.hasClass('noGoToNext') && val == "No"){
		goToNextArr.bool = true;
		goToNextArr.selectedNo = true;
	}

	//save changes
	iSurvey.saveChanges(data, field, _this_e, goToNextArr);
}

//ajax call to save data
iSurvey.saveChanges = function(formData, field, _this_e, goToNextArr){
	var stepR = '';

	$.ajax({
		url: iSurvey.saveRoute,
		type: 'POST',
		data: formData,
		contentType: false,
	    processData: false,
	    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	}).done(function(data){
		console.log(data);

		// if(field.hasClass('goToNext')){
		// 	console.log('here');
		// 	iSurvey.nextStepFunction(_this_e);
		//close loader
		// iSurvey.endLoader();
		//redirect to next step in survey
		// if( iSurvey.step.num < 1 )
		// 	iSurvey.goToNextStep( iSurvey.step.stepRoute + '1' );
		// else if( iSurvey.step.num === 8 ){
		// 	//if interested, redirect to step 9, else redirect to last step 13
		// 	if( iSurvey.step.stipend_interest )
		// 		iSurvey.goToNextStep( iSurvey.step.stepRoute + '9' );
		// 	else
		// 		iSurvey.goToNextStep( iSurvey.step.stepRoute + '13' );
		// }

		//if(field.hasClass('goToNext')){
		if (goToNextArr.bool) {
			iSurvey.nextStepFunction(_this_e, goToNextArr);
		}
	});
}



// ----- validation for type of form field 
//validatation for text fields
iSurvey.textValidation = function(field){
	var err = field.parent().find('small.error:visible');

	if( field.val() !== '' && err.length === 0 )
		return true;

	return false;
}

//validatation for select fields
iSurvey.selectValidation = function(field){
	if( field.val() !== 'default' )
		return true;

	return false;
}

//validation for radio buttons
iSurvey.radioValidation = function(field){
	return true;
}

//validation for checkboxes buttons
iSurvey.checkboxValidation = function(field){
	return true;
}

//full page validate
iSurvey.fullPageValidate = function(){
	var fields = $('.survey-field');
	var error_fields = $('small.error');
	var is_valid = true;

	//if there are any error fields, check if any are showing/invalid
	if( error_fields.length > 0 ){
		error_fields.each(function(){
			if( $(this).is(':visible') ){
				is_valid = false;
				return false;
			}
		});
	}

	//only check the field values if is_valid is still true at this point
	if( fields.length > 0 && is_valid ){

		fields.each(function(){
			var _this = $(this);

			//if radio button, then assume false - only valid when at least one is checked
			if( _this.is(':radio') ){
				is_valid = false;
				if( _this.is(':checked') ){
					is_valid = true;
					return false;
				}
			}else{
				//else its either type text or select
				if( _this.val() === 'default' || _this.val() === '' ){
					is_valid = false;
					return false;
				}
			}
			
		});

	}
	iSurvey.toggleCustomErrorMsg(is_valid);
	return is_valid;
}

iSurvey.toggleCustomErrorMsg = function(valid){
	if(valid)
		$('.errorMsg').slideUp(250);
	else
		$('.errorMsg').slideDown(250);
}
// ----- validation for type of form field 




//redirect to first page of survey
iSurvey.goToNextStep = function(nextStep){
	window.location.href = nextStep;
}

iSurvey.nextStepFunction = function(e, goToNextArr){
	e.preventDefault();

	var is_valid = false;

	is_valid = iSurvey.fullPageValidate();

	// for some questions if they choose no we want the user to move to next step
	// even though there are more questions on the page
	if (goToNextArr !=null && goToNextArr.selectedNo == true) {
		is_valid = true;
	}
	
	if(is_valid){
		iSurvey.goToNextStep( iSurvey.step.stepRoute + (iSurvey.step.num +1) );
	}
}

iSurvey.startLoader = function(){
	$('.isurvey-ajax-loader').show();
}

iSurvey.endLoader = function(){
	$('.isurvey-ajax-loader').hide();	
}

