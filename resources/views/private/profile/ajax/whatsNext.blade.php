<?php $step = '1'; ?>
@if( $step == '1' )
	{{Form::open()}}	
@elseif ($step == 'signup')
	<div  id = "wnTitle" class = "row">
		<div class="column small-12">
			<span class="page_head_black">
				Sign Up!
			</span>
		</div>
	</div>
	<div id = "wnForm" class = "row">
		<div class="column small-12">
				<div class='row'>
					<div class='large-12 column text-left no-padding'>
						<span><a href="/signup?utm_source=SEO&utm_medium={{$currentPage or ''}}" class="wnLink wnLinkPara">Sign up</a> for Plexuss!</span>
					</div>
				</div>
		</div>
	</div>
	<div class="row">
		<div class="small-12 column">
			&nbsp
		</div>
	</div>
@elseif ($step == 'none')
	<div  id = "wnTitle" class = "row">
		<div class="column small-12">
			<span class="page_head_black">
				Next Step: Explore our site!
			</span>
		</div>
	</div>
	<div id = "wnForm" class = "row">
		<div class="column small-12">
				<div class='row'>
					<div class='large-12 column text-left no-padding'>
						<span> Now that you've completed your profile, you're ready to be recruited! Check out our <a href="/college" class="wnLink wnLinkPara">College Pages</a> to find schools that are right for you; and see your favorite schools' stats! Compare schools against one another with our <a href="/comparison" class="wnLink wnLinkPara">Battle Page</a>! Or visit our <a href="/news" class="wnLink wnLinkPara">News Pages</a> to find interesting news about colleges you're interested in!</span>
					</div>
				</div>
		</div>
	</div>
	<div class="row">
		<div class="small-12 column">
			&nbsp
		</div>
	</div>
@elseif ($step == 'zip')
<script type="text/javascript">
$(document).foundation({
	abide:{
		patterns: {
			zip: /^\d{5}(-\d{4})?$/
		}
	}
});
</script>
	{{ Form::open(array('url' => 'ajax/whatsNext/', 'method' => 'POST', 'data-abide' => 'ajax')) }}
	<div  id = "wnTitle" class = "row">
		<div class="column small-12">
			<span>
				Next Step: Add your Zip Code<br>
			</span>
		</div>
	</div>

	<div id = "wnForm" class = "row">
		<div class="column small-12">
				<div class='row'>
					<div class='large-3 column text-left no-padding'>
						{{ Form::label('zip', 'Zip code', array('class'=> 'inline bold-font')) }}
					</div>
					<div class='large-4 column end'>
						{{ Form::text('zip', null, array('id' => 'zip', 'placeholder' => 'Zip', 'pattern' => 'zip', 'maxlength' => '10')) }}
						<small class="error">Enter a valid zip</small>
					</div>
				</div>
		</div>
	</div>
@elseif ($step == 'user_type')
<script type="text/javascript">
$(document).foundation();
</script>
	{{ Form::open(array('url' => 'ajax/whatsNext/', 'method' => 'POST', 'data-abide' => 'ajax')) }}
	<div  id = "wnTitle" class = "row">
		<div class="column small-12">
			<span>
				Next Step: What type of user are you?<br>
			</span>
		</div>
	</div>

	<div id = "wnForm" class = "row">
		<div class="column small-12">
				<div class='row'>
					<div class='medium-3 column text-left'>
						{{ Form::label('user_type', 'I am a(n)', array('class'=> 'inline bold-font')) }}
					</div>
					<div class='medium-9 column'>
						{{ Form::select('user_type', array('' => 'Select a user type', 'student' => 'Student', 'alumni' => 'Alumni', 'parent' => 'Parent', 'counselor' => 'Counselor'), null, array('id' => 'user_type', 'required pattern' => 'alpha')) }}
						<small class="error">Select a user type</small>
					</div>
				</div>
		</div>
	</div>
@elseif ($step == 'country')
<script type="text/javascript">
	$(document).foundation();
</script>
	{{ Form::open(array('url' => 'ajax/whatsNext/', 'method' => 'POST', 'data-abide' => 'ajax')) }}
	<div  id = "wnTitle" class = "row">
		<div class="column small-12">
			<span>
				Next Step: What country do you live in?<br>
			</span>
		</div>
	</div>

	<div id = "wnForm" class = "row">
		<div class="column small-12">
				<div class='row'>
					<div class='large-3 column text-left no-padding'>
						{{ Form::label('country', 'I live in', array('class'=> 'inline bold-font')) }}
					</div>
					<div class='large-9 column end'>
						{{ Form::select('country', $countries, null, array('id' => 'country', 'required pattern' => 'integer')) }}
						<small class="error">Select a user type</small>
					</div>
				</div>
		</div>
	</div>
@elseif ($step == 'education')
<script type='text/javascript'>
$('#education').change(function(){
	var input = $(this).val();
	var zip = '';
	if(zip == ''){
		getZip(input, addAutoComplete);
	}

	/* Disable/enable the following fields:
	 * - homeschooled, school name, grad year
	 */
	var default_disabled = $( '.wn_disabled' );
	if( input != '' ){
		default_disabled.prop( 'disabled', false );
	}
	else{
		default_disabled.prop( 'disabled', true );
	}

	// Hide/unhide homeschooled checkbox
	var homeschooled_box = $('#homeschooled_container');
	var homeschooled = $('#homeschooled');
	if( input == 'college' ){
		homeschooled_box.slideUp( 250, 'easeInOutExpo' );
	}
	else{
		homeschooled_box.slideDown( 250, 'easeInOutExpo' );
	}
	// Re-init foundation
	init_fndtn_wn_ed();

	function getZip(input, callback){
		$.ajax({
			url: "/ajax/getUserZip",
			type: 'POST',
			dataType: 'JSON',
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
			success: function(zip){
				if(callback != null){
					callback(zip, input);
				}
			}
		});
	}

	function addAutoComplete(zip, input){
		$('#school_name').autocomplete("option", "source", "/getAutoCompleteData?zipcode=" + zip + "&type=" + input);
	}
});

// Hide/unhide school name box when homeschooled is checked
$( '#homeschooled' ).change(function(){
	var sn_box = $( '#school_name_container' );
	var sn_field = $( '#school_name' );
	if( $( this ).is( ':checked' ) ){
		sn_box.slideUp( 250, 'easeInOutExpo' );
		sn_field.removeAttr( 'required' );
	}
	else{
		sn_box.slideDown( 250, 'easeInOutExpo' );
		sn_field.attr( 'required', 'required' );
	}
	// Re-init fndtn
	init_fndtn_wn_ed();
});

$('#school_name').autocomplete({
	source: 'getAutoCompleteData',
	change: function(event, ui){
		var input = $('#school_name');
		var autocomp_list = $('#school_name_container .ui-front .ui-autocomplete > li');
		var match = false;
		// Set default val for input's data val if it is not found/set in the DOM
		var data_val = typeof input.data( 'school' ) == 'undefined' ? '' : input.data( 'school' ).toLowerCase();
		var user_val = input.val().toLowerCase();

		// Loop through the autocomplete list to find matches
		autocomp_list.each(function(){
			var val = $(this).html();
			var li_val = val.toLowerCase();
			var indexOf = li_val.indexOf(user_val);
			/* If a match is found in the autocomplete list but
			 * the values don't match, clear the field
			 * For example, when a user types something quickly
			 * but does not let autocomplete load results, or if
			 * a user is not specific enough: eg. there are 3
			 * piedmont high schools
			 */
			if( indexOf > -1 && data_val != user_val){
				input.val('');
				input.data('school', '');
				$('#wn_school_id').val('');
				match = true;
				return false;
			}
			/* If there's a match between user input and item
			 * selected from the autocomplete list, close the 
			 * country box
			 */
			else if( indexOf > -1 ){
				/* Hide Country Box since we already know the country of the
				 * school that's in our DB, duh!
				 */
				match = true;
			}
		});
		// END .each() LOOP

		/* If the user's input is a school that is not found in autocomplete, (we don't have it)
		 * then clear the #school_id value, and input's data field
		 */
		if( match == false && data_val != user_val ){
			input.data('school', '');
			$('#wn_school_id').val('');
		}
	},
	minLength: 1,
	select: function(event, ui) {
		var school_field = $(this);
		school_field.data( 'school', ui.item.value );
		//window.school_id = ui.item.value;
		$('#wn_school_id').val(ui.item.id);
	}
});

function init_fndtn_wn_ed(){
	$(document).foundation({
		abide: {
			patterns: {
				school_name: /^([0-9a-zA-Z\.\(\),\-'"!@#& ])+$/
			}
		}
	});
}

init_fndtn_wn_ed();
</script>

	{{ Form::open(array('url' => 'ajax/whatsNext/', 'method' => 'POST', 'data-abide' => 'ajax')) }}
	{{Form::hidden('wn_school_id', null , array('id'=>'wn_school_id'));}}
	<div  id = "wnTitle" class = "row">
		<div class="column small-12">
			<span>
				Next Step: Add your Current Education<br>
			</span>
		</div>
	</div>

	<div id = "wnForm" class = "row">
		<div class="column small-12">
				<div class='row'>
					<div class='large-4 column text-left no-padding'>
						{{ Form::label('education', 'Education level', array('class'=> 'inline bold-font')) }}
					</div>
					<div class='large-7 column end'>
						{{ Form::select('education', array('' => 'Current level of education', 'highschool' => 'High School', 'college' => 'College'), null, array('id' => 'education', 'required' => 'required')) }}
						<small class="error">Select an education level</small>
					</div>
				</div>
				<div id='homeschooled_container' class='row'>
					<div class='large-offset-4 large-8 column'>
							{{Form::checkbox('homeschooled', '1', null, array('id' => 'homeschooled', 'class' => 'wn_checkbox wn_disabled', 'disabled'))}}
							{{Form::label('homeschooled', 'Home schooled', array('class' => 'inline bold-font'))}}
					</div>
				</div>
				<div id='school_name_container' class='row'>
					<div class='large-4 column text-left no-padding'>
						{{ Form::label('school_name', 'School name', array('class'=> 'inline bold-font')) }}
					</div>
					<div class='large-8 column ui-front'>
						{{ Form::text('school_name', null, array('id' => 'school_name', 'placeholder' => 'Enter your school name', 'class' => 'wn_disabled', 'disabled', 'required', 'pattern' => 'school_name')) }}
						<small class="error">Enter a school</small>
					</div>
				</div>
				<div class='row'>
					<div class='large-4 column text-left no-padding'>
						{{ Form::label('grad_year', 'Graduation year', array('class'=> 'inline bold-font')) }}
					</div>
					<div class='large-4 column end'>
						<?php 
							$today = date("Y");
							$selected = $today;
							$startYear = $today + 6;
							$endYear = $today - 53;
						?>
						<select id='grad_year' class="field wn_disabled" name="grad_year" required disabled>
							<option value="">Select a year</option>
							@for ($i = $startYear; $i > $endYear; $i--)
	    						<option value="{{$i}}">{{$i}}</option>
							@endfor
							<option value="ged">GED</option>
							<option value="ng">Never Graduated</option>
						</select>
						<small class="error">Select a grad year</small>
					</div>
				</div>
		</div>
	</div>
@elseif ($step == 'address')
	<script type="text/javascript">
		$(document).foundation({
			abide: {
				patterns: {
					address: /^([0-9a-zA-Z\.,\- ])+$/,
					city: /^([a-zA-Z\.' ])+$/,
					state: /^([a-zA-Z]){2}$/
				}
			}
		});
	</script>
	{{ Form::open(array('url' => 'ajax/whatsNext/', 'method' => 'POST', 'data-abide' => 'ajax')) }}
	<div  id = "wnTitle" class = "row">
		<div class="column small-12">
			<div class="row">
				<div class="column small-12">
					Next Step: Add your Address
				</div>
			</div>
		</div>
	</div>

	<div id = "wnForm" class = "row">
		<div class="column small-12 address-section">
				<div class='row'>
					<div class='small-3 column text-left'>
						{{ Form::label('address', 'Address', array('class'=> 'inline bold-font')) }}
					</div>
					<div class='small-9 column'>
						{{ Form::text('address', null, array('id' => 'address', 'placeholder' => 'Enter your address', 'required pattern' => 'address', 'maxlength' => '50')) }}
						<small class="error">Enter a valid address</small>
					</div>
				</div>
				<div class='row'>
					<div class='small-3 column text-left'>
						{{ Form::label('city', 'City', array('class'=> 'inline bold-font')) }}
					</div>
					<div class='small-9 column'>
						{{ Form::text('city', null, array('id' => 'city', 'placeholder' => 'Enter your city', 'required pattern' => 'city', 'maxlength' => '50')) }}
						<small class="error">Enter a valid city</small>
					</div>
				</div>
				<div class='row'>
					<div class='small-3 column text-left'>
						{{ Form::label('state', 'State', array('class'=> 'inline bold-font')) }}
					</div>
					<div class='small-5 column end'>
						{{ Form::text('state', null, array('id' => 'state', 'placeholder' => 'State', 'required pattern' => 'state', 'maxlength' => '2')) }}
						<small class="error">Enter a valid state</small>
					</div>
				</div>
		</div>
	</div>
@elseif ($step == 'phone')
	<script type="text/javascript">
		$(document).foundation({
			abide: {
				patterns: {
					phone: /^1?\-?\(?([0-9]){3}\)?([\.\-])?([0-9]){3}([\.\-])?([0-9]){4}$/
				}
			}
		});
	</script>
	{{ Form::open(array('url' => 'ajax/whatsNext/', 'method' => 'POST', 'data-abide' => 'ajax')) }}
	<div  id = "wnTitle" class = "row">
		<div class="column small-12">
			<span>
				Next Step: Add your Phone Number<br>
			</span>
		</div>
	</div>

	<div id = "wnForm" class = "row">
		<div class="column small-12">
			<div class='row'>
				<div class='large-12 column text-left no-padding'>
					{{ Form::label('phone', 'Phone Number', array('class'=> 'inline bold-font')) }}
				</div>
			</div>
			<div class='row'>
				<div class='large-12 column text-left no-padding'>
					{{ Form::text('phone', null, array('id' => 'phone', 'placeholder' => 'Enter your phone number', 'required pattern' => 'phone', 'maxlength' => '15')) }}
					<small class="error">Enter a valid phone number</small>
				</div>
			</div>
		</div>
	</div>
@elseif ($step == 'birth_date')
	<script type="text/javascript">
		$(document).foundation({
			abide: {
				patterns: {
					month: /^([1-9]|[1][0-2])$/,
					day: /^([1-9]|[1-2][0-9]|[3][0-1])$/,
					year: /^([1][8][8-9][0-9]|[1][9][0-9][0-9]|[2][0][0-1][0-9])$/
				}
			}
		});
	</script>
	{{ Form::open(array('url' => 'ajax/whatsNext/', 'method' => 'POST', 'data-abide' => 'ajax')) }}
	{{ Form::hidden('birth_date', null) }}
	<div  id = "wnTitle" class = "row">
		<div class="column small-12">
			<span>
				Next Step: Add your Birth Date<br>
			</span>
		</div>
	</div>

	<div id = "wnForm" class = "row">
	<div class="column small-12">
			<div class='row'>
				<div class='large-12 column text-left no-padding'>
					{{ Form::label('month', 'Birth date', array('class'=> 'inline bold-font')) }}
				</div>
			</div>
			<div class='row' style="width: 350px;">
				<div class='large-3 column text-left no-padding'>
					{{ Form::text('month', null, array('id' => 'month', 'placeholder' => 'MM', 'required pattern' => 'month', 'maxlength' => '2')) }}
					<small class="error">Month (MM)</small>
				</div>
				<div class='large-3 column text-left no-padding'>
					{{ Form::text('day', null, array('id' => 'day', 'placeholder' => 'DD', 'required pattern' => 'day', 'maxlength' => '2')) }}
					<small class="error">Day (DD)</small>
				</div>
				<div class='large-4 column text-left no-padding end'>
					{{ Form::text('year', null, array('id' => 'year', 'placeholder' => 'YYYY', 'required pattern' => 'year', 'maxlength' => '4')) }}
					<small class="error">Year (YYYY)</small>
				</div>
			</div>
		</div>
	</div>
@elseif ($step == 'gender')
	<script type="text/javascript">
		$(document).foundation();
	</script>
	{{ Form::open(array('url' => 'ajax/whatsNext/', 'method' => 'POST', 'data-abide' => 'ajax')) }}
	<div  id = "wnTitle" class = "row">
		<div class="column small-12">
			<span>
				Next Step: Add your Gender<br>
			</span>
		</div>
	</div>
	<div id = "wnForm" class = "row">
		<div class="column small-12">
			<div class='row'>
				<div class='large-12 column text-left no-padding'>
					{{ Form::label('gender', 'Gender', array('class'=> 'inline bold-font')) }}
				</div>
			</div>
			<div class='row'>
				<div class='large-6 column text-left no-padding end'>
					{{ Form::select('gender', array('' => 'select', 'm' => 'Male', 'f' => 'Female'), null, array('id' => 'gender', 'required')) }}
					<small class="error">Select a gender</small>
				</div>
			</div>
		</div>
	</div>
@elseif ($step == 'ethnicity')
<script type='text/javascript'>
	function getEthnicities(IdSel){
		$.ajax({
			url: "/ajax/profile/DropDownData/",
			type: 'GET',
			data: { Type:'ethnicities',vSel:IdSel},
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		})
		.done(function(data) {
			$("#ethnicity").html(data);
		});	
	}
getEthnicities(0);
$(document).foundation();
</script>
	{{ Form::open(array('url' => 'ajax/whatsNext/', 'method' => 'POST', 'data-abide' => 'ajax')) }}
	<div  id = "wnTitle" class = "row">
		<div class="column small-12">
			<span>
				Next Step: Add your Ethnicity<br>
			</span>
		</div>
	</div>
	<div id = "wnForm" class = "row">
		<div class="column small-12">
			<div class='row'>
				<div class='large-12 column text-left no-padding'>
					{{ Form::label('ethnicity', 'Ethnicity', array('class'=> 'inline bold-font')) }}
				</div>
			</div>
			<div class='row'>
				<div class='large-6 column text-left no-padding end'>
					{{ Form::select('ethnicity', array('' => 'Choose'), null, array('id' => 'ethnicity', 'required')) }}
					<small class="error">Select an ethnicity</small>
				</div>
			</div>
		</div>
	</div>
@elseif ($step == 'religion')
<script type='text/javascript'>
	function getReligions(IdSel)
	{
		$.ajax({
			url: "/ajax/profile/DropDownData/",
			type: 'GET',
			data: { Type:'religions',vSel:IdSel},
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		})
		.done(function(data) {
			$("#religion").html(data);
		});
	}
	getReligions(0);
	$(document).foundation();
</script>
	{{ Form::open(array('url' => 'ajax/whatsNext/', 'method' => 'POST', 'data-abide' => 'ajax')) }}
	<div  id = "wnTitle" class = "row">
		<div class="column small-12">
			<span>
				Next Step: Add your Religion<br>
			</span>
		</div>
	</div>
	<div id = "wnForm" class = "row">
		<div class="column small-12">
			<div class='row'>
				<div class='large-12 column text-left no-padding'>
					{{ Form::label('religion', 'Religion', array('class'=> 'inline bold-font')) }}
				</div>
			</div>
			<div class='row'>
				<div class='large-6 column text-left no-padding end'>
					{{ Form::select('religion', array('' => 'Not Religious'), null, array('id' => 'religion')) }}
					<small class="error">Enter a religion</small>
				</div>
			</div>
		</div>
	</div>
@elseif ($step == 'married')
	<script type="text/javascript">
		$(document).foundation();
	</script>
	{{ Form::open(array('url' => 'ajax/whatsNext/', 'method' => 'POST', 'data-abide' => 'ajax')) }}
	<div  id = "wnTitle" class = "row">
		<div class="column small-12">
			<span>
				Next Step: Add your Marital Status<br>
			</span>
		</div>
	</div>
	<div id = "wnForm" class = "row">
		<div class="column small-12">
			<div class='row'>
				<div class='large-12 column text-left no-padding'>
					{{ Form::label('married', 'Marital status', array('class'=> 'inline bold-font')) }}
				</div>
			</div>
			<div class='row'>
				<div class='large-6 column text-left no-padding end'>
					{{ Form::select('married', array('not married', 'married'), null, array('id' => 'married', 'required')) }}
					<small class="error">Select an option</small>
				</div>
			</div>
		</div>
	</div>
@elseif ($step == 'children')
	<script type="text/javascript">
		$(document).foundation();
	</script>
	{{ Form::open(array('url' => 'ajax/whatsNext/', 'method' => 'POST', 'data-abide' => 'ajax')) }}
	<div  id = "wnTitle" class = "row">
		<div class="column small-12">
			<span>
				Next Step: Do you have children?<br>
			</span>
		</div>
	</div>
	<div id = "wnForm" class = "row">
		<div class="column small-12">
			<div class='row'>
				<div class='large-12 column text-left no-padding'>
					{{ Form::label('children', 'Do you have children?', array('class'=> 'inline bold-font')) }}
				</div>
			</div>
			<div class='row'>
				<div class='large-6 column text-left no-padding end'>
					{{ Form::select('children', array('no', 'yes'), array('id' => 'children', 'required')) }}
					<small class="error">Select an option</small>
				</div>
			</div>
		</div>
	</div>
@elseif ($step == 'objective')
<script type="text/javascript">
	$(document).ready(function(){
	//Set autocomplete options for majors autocomplete field
		$('#wnObjMajor').autocomplete({
			source: '/getObjectiveMajors',
			appendTo: '#wnObjMajorContainer',
			select: function(event, ui){
				$(this).data('selected', ui.item.value);
			}
		});

	// Set autocomplete options for professions autocomplete field
		$('#wnObjProfession').autocomplete({
			source: '/getObjectiveProfessions',
			appendTo: '#wnObjProfessionContainer',
			select: function(event, ui){
				$(this).data('selected', ui.item.value);
			}
		});

	//Delete text if user does not select an autocomplete option
		$('#wnObjMajor').change(function(){
			if($(this).val() !== $(this).data('selected')){
				$(this).val('');
			}
		});

		$('#wnObjProfession').change(function(){
			if($(this).val() !== $(this).data('selected')){
				$(this).val('');
			}
		});
	});
	function getDegrees(DegreeId){
		$.ajax({
			url: "/ajax/profile/DropDownData/",
			type: 'GET',
			data: { Type:'degree',DegreeId:DegreeId},
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		})
		.done(function(data) {
			$("#degree_type").html(data);
		});	
	}
	getDegrees(0);
	//getAOSoptions(0,0);
	$(document).foundation();
	/*
	function getAOSoptions(aosId,aocId)
	{
		$.get("/ajax/profile/DropDownData/", { Type:'aos',aosId:aosId})
		.done(function( data ) {
			$("#aos").html(data);
			getAOCoptions(aocId);
		});	
	}
	function getAOCoptions(aocId)
	{
		var aosId=0;
		if(document.getElementById('objAos'))
		{
			if(document.getElementById('objAos').value!=""){
			aosId=document.getElementById('objAos').value; }
		}
		$.get("/ajax/profile/DropDownData/", { Type:'aoc',aocId:aocId,aosId:aosId})
		.done(function( data ) {
			$("#aoc").html(data);
		});	
	}
	 */
</script>
	{{ Form::open(array('url' => 'ajax/whatsNext/', 'method' => 'POST', 'data-abide' => 'ajax')) }}
	{{ Form::hidden('objective', null) }}
	<div  id = "wnTitle" class = "row" style="width: 400px;">
		<div class="column small-12">
			<span>
				Next Step: Add your Objective<br>
			</span>
		</div>
	</div>
	<div id = "wnForm" class = "row">
		<div class="column small-12">
			<div class='row'>
				<div class='large-6 column text-left no-padding'>
					{{ Form::label('degree_type', 'I would like to get a/an', array('class'=> 'inline bold-font')) }}
				</div>
				<div id='wnObjMajorContainer' class='large-6 column text-left no-padding end'>
					{{ Form::select('degree_type', array('Degree type:'), null, array('id' => 'degree_type', 'required')) }}
					<small class="error">Select a degree type</small>
				</div>
			</div>
			<div class='row'>
				<div class='large-1 column text-left no-padding'>
					{{ Form::label('aoc', 'in', array('class' => 'inline bold-font')) }}
				</div>
				<div id='wnObjProfessionContainer' class='large-6 column text-left no-padding end'>
					{{
						Form::text(
							'wnObjMajor',
							isset($user['major']) ? $user['major'] : null,
							array(
								'id' => 'wnObjMajor',
								'class' => 'objAutocomplete',
								 'data-selected' => isset($user['major']) ? $user['major'] : null,
								 'placeholder' => 'Enter a major...',
								'required'
							)
						)
					}}
					<small class="error">Select a major</small>
				</div>
			</div>
			<div class='row'>
				<div class='large-12 column text-left no-padding'>
					{{ Form::label('aos', 'My dream would be to one day work as a/an', array('class' => 'inline bold-font')) }}
				</div>
			</div>
			<div class='row'>
				<div class='large-12 column text-left no-padding end'>
					{{
						Form::text(
							'wnObjProfession',
							isset($user['profession']) ? $user['profession'] : null,
							array(
								'id' => 'wnObjProfession',
								'class' => 'objAutocomplete',
								 'data-selected' => isset($user['profession']) ? $user['profession'] : null,
								 'placeholder' => 'Enter a profession...',
								'required'
							)
						)
					}}
					<small class="error">Select a profession</small>
				</div>
			</div>
		</div>
	</div>
@elseif ($step == 'hs_gpa')
	<script type="text/javascript">
		$(document).foundation({
			abide: {
				patterns: {
					gpa: /^(([0-3]){1}\.([0-9]){1,2}|4\.(0){1,2}|([0-4]){1})$/,
					max_weighted_gpa: /^(([0-9])+|([0-9])+\.([0-9]){1,2})$/
				}
			}
		});
	</script>
	{{ Form::open(array('url' => 'ajax/whatsNext/', 'method' => 'POST', 'data-abide' => 'ajax')) }}
	<div  id = "wnTitle" class = "row">
		<div class="column small-12">
			<span>
				Next Step: Add your High School GPA<br>
			</span>
		</div>
	</div>
	<div id = "wnForm" class = "row">
		<div class="column small-12">
			<div class='row'>
				<div class='large-4 column text-left no-padding'>
					{{ Form::label('hs_gpa', 'High school GPA', array('class'=> 'inline bold-font')) }}
				</div>
				<div class='large-4 column text-left no-padding end'>
					{{ Form::text('hs_gpa', null, array('id' => 'hs_gpa', 'placeholder' => 'GPA', 'required pattern' => 'gpa', 'maxlength' => '4')) }}
					<small class="error">Enter a valid GPA</small>
				</div>
			</div>
			<div class='row'>
				<div class='large-4 column text-left no-padding'>
					{{ Form::label('weighted_gpa', 'Weighted GPA', array('class'=> 'inline bold-font')) }}
				</div>
				<div class='large-4 column text-left no-padding end'>
					{{ Form::text('weighted_gpa', null, array('id' => 'weighted_gpa', 'placeholder' => 'GPA', 'pattern' => 'max_weighted_gpa')) }}
					<small class="error">Enter a valid GPA</small>
				</div>
			</div>
		</div>
	</div>
@elseif ($step == 'act')
	<script type="text/javascript">
		$(document).foundation({
			abide: {
				patterns: {
					act: /^([1-9]|[1-2][0-9]|[3][0-6])$/
				}
			}
		});
	</script>
	{{ Form::open(array('url' => 'ajax/whatsNext/', 'method' => 'POST', 'data-abide' => 'ajax')) }}
	{{ Form::hidden('act_scores', null) }}
	<div  id = "wnTitle" class = "row">
		<div class="column small-12">
			<span>
				Next Step: Add your ACT Scores<br>
			</span>
		</div>
	</div>
	<div id = "wnForm" class = "row">
		<div class="column small-12">
			<div class='row'>
				<div class='large-5 column text-left no-padding'>
					{{ Form::label('act_english', 'ACT English', array('class'=> 'inline bold-font')) }}
				</div>
				<div class='large-4 column text-left no-padding end'>
					{{ Form::text('act_english', isset($act_english) ? $act_english : null, array('id' => 'act_english', 'pattern' => 'act', 'maxlength' => '2', 'placeholder' => '1 - 36')) }}
					<small class="error">Enter a valid score</small>
				</div>
			</div>
			<div class='row'>
				<div class='large-5 column text-left no-padding'>
					{{ Form::label('act_math', 'ACT Math', array('class'=> 'inline bold-font')) }}
				</div>
				<div class='large-4 column text-left no-padding end'>
					{{ Form::text('act_math', isset($act_math) ? $act_math : null, array('id' => 'act_math', 'pattern' => 'act', 'maxlength' => '2', 'placeholder' => '1 - 36')) }}
					<small class="error">Enter a valid score</small>
				</div>
			</div>
			<div class='row'>
				<div class='large-5 column text-left no-padding'>
					{{ Form::label('act_composite', 'ACT Composite', array('class'=> 'inline bold-font')) }}
				</div>
				<div class='large-4 column text-left no-padding end'>
					{{ Form::text('act_composite', isset($act_composite) ? $act_composite : null, array('id' => 'act_composite', 'pattern' => 'act', 'maxlength' => '2', 'placeholder' => '1 - 36')) }}
					<small class="error">Enter a valid score</small>
				</div>
			</div>
		</div>
	</div>
@elseif ($step == 'psat_total')
	<script type="text/javascript">
		$(document).foundation({
			abide: {
				patterns: {
					psat: /^([2-7][0-9]|[8][0])$/,
					psat_total:/^([6-9][0-9]|[1][0-9][0-9]|[2][0-3][0-9]|[2][4][0])$/
				}
			}
		});
	</script>
	{{ Form::open(array('url' => 'ajax/whatsNext/', 'method' => 'POST', 'data-abide' => 'ajax')) }}
	{{ Form::hidden('psat_scores', null) }}
	<div  id = "wnTitle" class = "row">
		<div class="column small-12">
			<span>
				Next Step: Add your PSAT Scores<br>
			</span>
		</div>
	</div>
	<div id = "wnForm" class = "row">
		<div class="column small-12">
			<div class='row'>
				<div class='large-5 column text-left no-padding'>
					{{ Form::label('psat_reading', 'PSAT Reading', array('class'=> 'inline bold-font')) }}
				</div>
				<div class='large-4 column text-left no-padding end'>
					{{ Form::text('psat_reading', null, array('id' => 'psat_reading', 'pattern' => 'psat', 'maxlength' => '2', 'placeholder' => '0 - 80')) }}
					<small class="error">Enter a valid score</small>
				</div>
			</div>
			<div class='row'>
				<div class='large-5 column text-left no-padding'>
					{{ Form::label('psat_writing', 'PSAT Writing', array('class'=> 'inline bold-font')) }}
				</div>
				<div class='large-4 column text-left no-padding end'>
					{{ Form::text('psat_writing', null, array('id' => 'psat_writing', 'pattern' => 'psat', 'maxlength' => '2', 'placeholder' => '0 - 80')) }}
					<small class="error">Enter a valid score</small>
				</div>
			</div>
			<div class='row'>
				<div class='large-5 column text-left no-padding'>
					{{ Form::label('psat_math', 'PSAT Math', array('class'=> 'inline bold-font')) }}
				</div>
				<div class='large-4 column text-left no-padding end'>
					{{ Form::text('psat_math', null, array('id' => 'psat_math', 'pattern' => 'psat', 'maxlength' => '2', 'placeholder' => '0 - 80')) }}
					<small class="error">Enter a valid score</small>
				</div>
			</div>
			<div class='row'>
				<div class='large-5 column text-left no-padding'>
					{{ Form::label('psat_total', 'PSAT Total', array('class'=> 'inline bold-font')) }}
				</div>
				<div class='large-4 column text-left no-padding end'>
					{{ Form::text('psat_total', null, array('id' => 'psat_total', 'pattern' => 'psat_total', 'maxlength' => '3', 'placeholder' => '0 - 240')) }}
					<small class="error">Enter a valid score</small>
				</div>
			</div>
		</div>
	</div>
@elseif ($step == 'sat_total')
	<script type="text/javascript">
		$(document).foundation({
			abide: {
				patterns: {
					sat: /^([2-7][0-9][0]|[8][0][0])$/,
					total:/^([6-9][0-9][0]|[1][0-9][0-9][0]|[2][0-3][0-9][0]|[2][4][0][0])$/
				}
			}
		});
	</script>
	{{ Form::open(array('url' => 'ajax/whatsNext/', 'method' => 'POST', 'data-abide' => 'ajax')) }}
	{{ Form::hidden('sat_scores', null) }}
	<div  id = "wnTitle" class = "row">
		<div class="column small-12">
			<span>
				Next Step: Add your SAT Scores<br>
			</span>
		</div>
	</div>
	<div id = "wnForm" class = "row">
		<div class="column small-12">
			<div class='row'>
				<div class='large-5 column text-left no-padding'>
					{{ Form::label('sat_reading', 'SAT Reading', array('class'=> 'inline bold-font')) }}
				</div>
				<div class='large-4 column text-left no-padding end'>
					{{ Form::text('sat_reading', null, array('id' => 'sat_reading', 'pattern' => 'sat', 'maxlength' => '3', 'placeholder' => '200 - 800')) }}
					<small class="error">Enter a valid score</small>
				</div>
			</div>
			<div class='row'>
				<div class='large-5 column text-left no-padding'>
					{{ Form::label('sat_writing', 'SAT Writing', array('class'=> 'inline bold-font')) }}
				</div>
				<div class='large-4 column text-left no-padding end'>
					{{ Form::text('sat_writing', null, array('id' => 'sat_writing', 'pattern' => 'sat', 'maxlength' => '3', 'placeholder' => '200 - 800')) }}
					<small class="error">Enter a valid score</small>
				</div>
			</div>
			<div class='row'>
				<div class='large-5 column text-left no-padding'>
					{{ Form::label('sat_math', 'SAT Math', array('class'=> 'inline bold-font')) }}
				</div>
				<div class='large-4 column text-left no-padding end'>
					{{ Form::text('sat_math', null, array('id' => 'sat_math', 'pattern' => 'sat', 'maxlength' => '3', 'placeholder' => '200 - 800')) }}
					<small class="error">Enter a valid score</small>
				</div>
			</div>
			<div class='row'>
				<div class='large-5 column text-left no-padding'>
					{{ Form::label('sat_total', 'SAT Total', array('class'=> 'inline bold-font')) }}
				</div>
				<div class='large-4 column text-left no-padding end'>
					{{ Form::text('sat_total', null, array('id' => 'sat_total', 'pattern' => 'total', 'maxlength' => '4', 'placeholder' => '600 - 2400')) }}
					<small class="error">Enter a valid score</small>
				</div>
			</div>
		</div>
	</div>
@elseif ($step == 'in_hs')
	{{ Form::open(array('url' => 'ajax/whatsNext/', 'method' => 'POST')) }}
	<div  id = "wnTitle" class = "row">
		<div class="column small-12">
			<span>
				Next Step: Add your High School Courses<br>
			</span>
		</div>
	</div>
	<div id = "wnForm" class = "row">
		<div class="column small-12">
			<div class='row'>
				<div class='large-12 column text-left no-padding' style="width: 500px;">
					<span>Add to your unlocked accomplishments profile section so that Plexuss can find scholarships for you.</span>
				</div>
			</div>
			<div class='row'>
				<div class='large-12 column text-left no-padding'>
					<a href="/profile?section=highschoolInfo" class="no-padding wnLink">Take me to my High School Courses section!</a>
				</div>
			</div>
		</div>
	</div>
@elseif ($step == 'in_college')
	{{ Form::open(array('url' => 'ajax/whatsNext/', 'method' => 'POST')) }}
	<div  id = "wnTitle" class = "row">
		<div class="column small-12">
			<span>
				Next Step: Add your College Courses<br>
			</span>
		</div>
	</div>
	<div id = "wnForm" class = "row">
		<div class="column small-12">
			<div class='row'>
				<div class='large-12 column text-left no-padding' style="width: 500px;">
					<span>Add to your unlocked accomplishments profile section so that Plexuss can find scholarships for you.</span>
				</div>
			</div>
			<div class='row'>
				<div class='large-12 column text-left no-padding'>
					<a href="/profile?section=collegeInfo" id="wnLink" class="no-padding wnLink">Take me to my College Courses section!</a>
				</div>
			</div>
		</div>
	</div>
<!--
elseif ($step == 'experience')
	<script type="text/javascript">
		function init_foundation_custom(){
			$(document).foundation({
				abide: {
					patterns: {
						company: /^([0-9a-zA-Z\.,\- ])+$/,
						year: /^([1][8][8-9][0-9]|[1][9][0-9][0-9]|[2][0][0-1][0-9])$/
					},
					validators:{
						YearCheckCerti: function(el, required, parent) {
							
							var MonthTo=months[$("#month_to").val()];
							var MonthFrom=months[$("#month_from").val()];
							var FromYear=$("#year_from").val();
							var Val=el.value;
							var pat=/^\d+$/;

							if(pat.test(Val)==false){
								return false;
							}
							
							if(pat.test(Val)==false){
								return false;
							}

							if(el.value < FromYear){
								return false;

							}else if(el.value == FromYear){
								if(MonthTo < MonthFrom){
									return false;
								}
								else{
									return true;
								}
							}
							else{
								return true;
							}
						
						}
					}
				}
			});
		}
		init_foundation_custom();
		$("#current").click(function(){
			toggle_current_workplace_wn(this, $('#month_to'), $('#year_to'), 'YearCheckCerti');
		});
		function toggle_current_workplace_wn(checkbox, end_month, end_year, validatorName){
				end_month_error = end_month.next();
				end_year_error = end_year.next();
				if(checkbox.checked==true)
				{
					end_month.removeAttr("required data-invalid");
					end_year.removeAttr("required data-invalid data-abide-validator");
					end_month_error.hide(250, function(){ end_month_error.remove(); });
					end_year_error.hide(250, function(){ end_year_error.remove(); });
					init_foundation_custom();
				}
				else
				{
					end_month.attr("required","required");
					end_year.attr({
						'required': 'required',
						'data-abide-validator': validatorName
					});
					end_month.after("<small class='error'>Month</small>");
					end_year.after("<small class='error'>Year</small>");
					init_foundation_custom();
				}
		}
	</script>
	{{ Form::open(array('url' => 'ajax/whatsNext/', 'method' => 'POST', 'data-abide' => 'ajax')) }}
	{{ Form::hidden('experience', null) }}
	<div  id = "wnTitle" class = "row">
		<div class="column small-12">
			<span>
				Next Step: Add your Work Experience<br>
			</span>
		</div>
	</div>
	<div id = "wnForm" class = "row" style="width: 500px;">
		<div class="column small-12">
			<div class='row'>
				<div class='large-3 column text-left no-padding'>
					{{ Form::label('company', 'Company Name', array('class'=> 'inline bold-font')) }}
				</div>
				<div class='large-9 column text-left no-padding end'>
					{{ Form::text('company', null, array('id' => 'company', 'required pattern' => 'company', 'maxlength' => '35')) }}
					<small class="error">Enter a company name</small>
				</div>
			</div>
			<div class='row'>
				<div class='large-3 column text-left no-padding'>
					{{ Form::label('title', 'Title', array('class'=> 'inline bold-font')) }}
				</div>
				<div class='large-9 column text-left no-padding end'>
					{{ Form::text('title', null, array('id' => 'title', 'required pattern' => 'company', 'maxlength' => '35')) }}
					<small class="error">Enter a title</small>
				</div>
			</div>
			<div class='row'>
				<div class='large-3 column text-left no-padding'>
					{{ Form::label('location', 'Location', array('class'=> 'inline bold-font')) }}
				</div>
				<div class='large-9 column text-left no-padding end'>
					{{ Form::text('location', null, array('id' => 'location', 'required pattern' => 'company', 'maxlength' => '35')) }}
					<small class="error">Enter a location</small>
				</div>
			</div>
			<div class='row'>
				<div class='large-3 column text-left no-padding'>
					{{ Form::label('month_from', 'Time Period', array('class'=> 'inline bold-font')) }}
				</div>
				<div class='large-2 column text-left no-padding'>
					{{ Form::select('month_from', array(
						'' =>  'Month', 
						'Jan' => 'January',
						'Feb' => 'February',
						'Mar' => 'March',
						'Apr' => 'April',
						'May' => 'May',
						'Jun' => 'June',
						'Jul' => 'July',
						'Aug' => 'August',
						'Sep' => 'September',
						'Oct' => 'October',
						'Nov' => 'November',
						'Dec' => 'December'
						), null, array('id' => 'month_from', 'required')) }}
					<small class="error">Month</small>
				</div>
				<div class='large-2 column text-left no-padding end'>
					{{ Form::text('year_from', null, array('id' => 'year_from', 'placeholder' => 'YYYY', 'required pattern' => 'year', 'maxlength' => '4')) }}
					<small class="error">Year</small>
				</div>
				<div class='large-1 column text-left no-padding'>
					{{ Form::label('month_to', 'to', array('class'=> 'inline bold-font')) }}
				</div>
				<div class='large-2 column text-left no-padding'>
					{{ Form::select('month_to', array(
						'' =>  'Month', 
						'Jan' => 'January',
						'Feb' => 'February',
						'Mar' => 'March',
						'Apr' => 'April',
						'May' => 'May',
						'Jun' => 'June',
						'Jul' => 'July',
						'Aug' => 'August',
						'Sep' => 'September',
						'Oct' => 'October',
						'Nov' => 'November',
						'Dec' => 'December'
						), null, array('id' => 'month_to', 'required')) }}
					<small class="error">Month</small>
				</div>
				<div class='large-2 column text-left no-padding end'>
					{{ Form::text('year_to', null, array('id' => 'year_to', 'placeholder' => 'YYYY', 'required pattern' => 'year', 'maxlength' => '4')) }}
					<small class="error">Year</small>
				</div>
			</div>
			<div class="row">
				<div class="small-3 column">
					<span>&nbsp</span>
				</div>
				<div class="small-1 column">
					{{ Form::checkbox('currentlyworkhere', 1, 0, array('id' => 'current')) }}
				</div>
				<div class="small-5 column text-left no-padding end">
					{{ Form::label('currentlyworkhere', 'I currently work here', array('class' => 'inline bold-font')) }}
				</div>
			</div>
			<div class="row">
				<div class="small-3 column">
					{{ Form::label('experience_type', 'Experience Type', array('class' => 'inline bold-font')) }}
				</div>
				<div class="small-9 column">
					{{ Form::select('experience_type', array('' => 'Choose', 'Intern' => 'Intern', 'Volunteer' => 'Volunteer', 'Part-time' => 'Part-time', 'Full-time' => 'Full-time'), null, array('required')) }}
					<small class="error">Select an experience type</small>
				</div>
			</div>
			<div class="row">
				<div class="small-3 column">
					{{ Form::label('description', 'Description', array('class' => 'inline bold-font')) }}
				</div>
				<div class="small-9 column">
					{{ Form::textarea('description', null, array('id' => 'description', 'placeholder' => 'description', 'rows' => '3', 'maxlength' => '500')) }}
				</div>
			</div>
		</div>
	</div>
-->
@elseif ($step == 'accomplishments')
	{{ Form::open(array('url' => 'ajax/whatsNext/', 'method' => 'POST')) }}
	<div  id = "wnTitle" class = "row">
		<div class="column small-12">
			<span>
				Next Step: Add your Accomplishments<br>
			</span>
		</div>
	</div>
	<div id = "wnForm" class = "row">
		<div class="column small-12">
			<div class='row'>
				<div class='large-12 column text-left no-padding' style="width: 500px;">
					<span>Add to your unlocked accomplishments profile section so that Plexuss can find scholarships for you.</span>
				</div>
			</div>
			<div class='row'>
				<div class='large-12 column text-left no-padding'>
					<a href="/profile?section=experience" class="no-padding wnLink">Take me to my Accomplishments section!</a>
				</div>
			</div>
		</div>
	</div>
@elseif ($step != '')
	{{ Form::open(array('url' => 'ajax/whatsNext/', 'method' => 'POST')) }}
	<div  id = "wnTitle" class = "row">
		<div class="column small-12">
			<span>
				{{ "Data: " . $step }}
			</span>
		</div>
	</div>
@endif

@if ($step != 'none' && $step != 'signup')
	<div id = "wnButtons" class = "row">
		<div class="column small-12">
			<div class='row'>
				@if ($step == 'accomplishments'
					|| $step == 'in_college'
					|| $step == 'in_hs')
					<div class='small-2 small-offset-10 column text-right'>
						<span id="wnSkip" class='button'>Skip</span>
					</div>
				@else
					<div class="column small-8 small-push-4 text-right">
						<span id="wnSkip" class='button'>Skip</span>
						{{ Form::submit("Save", array('class' => 'button', 'id' => 'wnSave')) }}
						{{ Form::submit("Save & Continue", array('class' => 'button', 'id' => 'wnSaveCont')) }}
					</div>
				@endif
			</div>
		</div>
	</div>
@endif

{{ Form::close() }}

<script type="text/javascript">
	//Ajax for submission
	$('form').on('valid',function(e){
		e.preventDefault();
		$.ajax({
			type     : "POST",
			cache    : false,
			url      : $(this).attr('action'),
			data     : $(this).serialize(),
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
			success  : function(data) {
				whatsNext('saveCont', '');
				getNotifications();
				topAlert({
					type: 'soft',
					img: '/images/topAlert/checkmark.png',
					dur: '3000',
					msg: 'Profile updated!'
				});
			}
		});
		putLoader();
	});

	// Hides What's Next panel on click of 'save' but not 'save & cont'
	$('#wnSave').on('click',function(){
		$('form').on('valid', function(e){
			$('#whatsNext').slideToggle('250', 'easeInOutExpo');
		});
	});

	$('#wnSkip').on('click', function(){
		whatsNext('init', {{ '"' . $step . '"'}});
		putLoader();
	});
	function putLoader(){
		$('#whatsNext').html("<div style='width: 50px; height: 50px; position: relative; margin: 0 auto;'><img src='/images/ajax_loader.gif'/></div>");
	}
</script>