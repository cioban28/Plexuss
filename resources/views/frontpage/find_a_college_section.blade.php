@if( isset($signed_in) && $signed_in == 1 )
<div class="row hide-for-small-only close-icon-and-adv-search-msg-row make-room-for-signedin-topbar">
@else
<div class="row hide-for-small-only close-icon-and-adv-search-msg-row">
@endif
	<div class="column small-12 medium-5 searchup-or-use-advSearch-message">
		<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/findACollegeOptionMessage.jpg" width="466" height="66" alt="">
	</div>

	<!-- back/close button to close side bar sections when open - start -->
	<div class="column medium-4 medium-text-right frontpage-back-btn">
		<img class="tablet-up-back-btn" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/gray-x.png" width="64" height="64" alt="">
	</div>
</div>

<div class="row frontpage-adv-search-form">
	{{Form::open(array('url' =>''))}}
	<!-- left side advanced search form -->
	<div class="column small-12 medium-6 advSearch_leftside">
		<div class="row show-for-small-only">
			<div class="column small-12">
				<div class="searchBar-container">
                    <input type="text" placeholder="Search Plexuss.." class="top_search_txt_val top_search_txt" data-input>
                    <input type="hidden" class="top_search_txt_val" value="" />
                    <!--remove duplicate id="top_search_txt_val"-->

                    <input type="hidden" class="top_search_type" value="" />
                    <!--remove duplicate id="top_search_type"-->
                    <div class="submit_advSearch_searchBar_btn" onclick="redirectSearch();"></div>
                </div>
			</div>
		</div>

		<div class="row">
			<div class="column small-3 medium-4 large-3">
				{{Form::label('advSearch_country', 'Country')}}
				{{ Form::select('country', $country, null, array('class' => 'frontpage-country')) }}
			</div>
			<div class="column small-3 medium-4 large-3" id="state_div">
				{{Form::label('advSearch_state', 'State')}}
				{{ Form::select('state', $states, null, array('class' => 'frontpage-state')) }}
			</div>
			<div class="column small-3 medium-4 large-6" id="city_div">
				{{Form::label('advSearch_city', 'City')}}
				{{ Form::select('city', $cities, null, array('class' => 'frontpage-city')) }}
			</div>
		</div>
		<div class="row">
			<div class="column small-6 large-4">
				{{Form::label('advSearch_zip', 'Zip Code')}}
				{{Form::text('advSearch_zip', null, array('pattern'=>'name', 'class'=>'frontpage-zip'))}}
			</div>
			<div class="column small-8 large-6">
				{{Form::label('advSearch_campusSetting', 'Campus Setting')}}
				{{Form::select('campus_setting', array(''=>'No Preference', 'City: Large'=>'City: Large', 'City: Midsize'=>'City: Midsize', 'City: Small'=>'City: Small', 'Rural: Distant'=>'Rural: Distant', 'Rural: Fringe'=>'Rural: Fringe', 'Rural: Remote'=>'Rural: Remote', 'Suburb: Large'=>'Suburb: Large', 'Suburb: Midsize'=>'Suburb: Midsize', 'Suburb: Small'=>'Suburb: Small', 'Town: Distant'=>'Town: Distant', 'Town: Fringe'=>'Town: Fringe', 'Town: Remote'=>'Town: Remote'), '', array('class'=>'frontpage-campussetting') )}}
			</div>
			<div class="column small-4 large-2 text-center">
				{{Form::label('advSearch_housing', 'Housing', array('class'=>''))}}
				{{Form::checkbox('housing', '1', false, array('id'=>'advSearch_housing', 'class'=>'frontpage-housing') )}}
			</div>
		</div>
		<div class="row">
			<div class="column small-12">
				{{Form::label('advSearch_degree', 'Degree Type')}}
				{{Form::select('degree_type', array(''=>'No Preference', 'bachelors_degree'=>'Bachelors Degree', 'masters_degree'=>'Masters Degree', 'post_masters_degree'=>'Post Masters Degree', 'doctors_degree_research'=>'Doctors Degree Research', 'doctors_degree_professional'=>'Doctors Degree Professional'), '', array('class'=>'frontpage-degree') )}}
			</div>
		</div>
		<!--
		<div class="row">
			<div class="column small-12">
				{{Form::label('majors', 'Programs/Majors (Physics, Engineering...)')}}
				{{Form::select('major', array(''=>'No Preference'), '', array('class'=>'frontpage-major') )}}
			</div>
		</div>
		-->
		<!-- <div class="row">
			<div class="column small-12">
				{{Form::label('religion', 'Religious Preference')}}
				{{Form::select('religion', $religion, null, array('class'=>'frontpage-religion') )}}
			</div>
		</div> -->

		@if(isset($depts_cat))
		<div class="row">
			<div class="column small-12">
				<label for="department">Department</label>
				<select class="department-select adv-c-s-majors-select" name="department">
					<option value={{null}} >Select a Department...</option>
					@foreach($depts_cat as $d)
						<option value={{$d->url_slug or ''}}>{{$d->name or ''}}</option>
					@endforeach
				</select>
			</div>
		</div>
		@endif

		<div class="row majors-select-container hide">
			<div class="column small-12">
				<label for="imajor">Major Offered <span><div class="sm-grey-loader hide"></div></span></label>
				<select class="majors-selection-box" name="imajor">
					<option value={{null}} >Select Major...</option>
				</select>
			</div>
		</div>		
		


	</div>

	<!-- right side advanced search form -->
	<div class="column small-12 medium-6 advSearch_rightside">
		
		<!-- max tuition -->
		<div class="row advSearch-slider-row-slider-values">
			<div class="column small-12">

				<div class="row collapse">
					<div class="column small-12">
						{{Form::label('tuition', 'Max Tuition & Fees', array('class'=>'advSearch-slider-row-headers') )}}
					</div>
				</div>

				<div class="row">
					<div id="slider-tuition" class="column large-8"></div>

					<div class="column small-6 large-2 small-text-left large-text-center advSearch-slider-value">
						$<span id="slider-tuition-snap-value-lower"></span>
					</div>

					<div class="column small-6 large-2 small-text-right large-text-center advSearch-slider-value">
						$<span id="slider-tuition-snap-value-upper"></span>
					</div>
				</div>

			</div>
		</div>

		<!-- undergrad enrollment -->
		<div class="row advSearch-slider-row-slider-values">
			<div class="column small-12">

				<div class="row collapse">
					<div class="column small-12">
						{{Form::label('enrollment', 'Undergraduate Enrollment', array('class'=>'advSearch-slider-row-headers') )}}
					</div>
				</div>

				<div class="row">
					<div id="slider-enrollment" class="column large-8"></div>

					<div class="column small-6 large-2 small-text-left large-text-center advSearch-slider-value">
						<span id="slider-enrollment-snap-value-lower"></span>
					</div>

					<div class="column small-6 large-2 small-text-right large-text-center advSearch-slider-value">
						<span id="slider-enrollment-snap-value-upper"></span>
					</div>
				</div>

			</div>
		</div>

		<!-- acceptance rate -->
		<div class="row advSearch-slider-row-slider-values">
			<div class="column small-12">

				<div class="row collapse">
					<div class="column small-12">
						{{Form::label('acceptance', 'Acceptance Rate', array('class'=>'advSearch-slider-row-headers') )}}
					</div>
				</div>

				<div class="row">
					<div id="slider-acceptancerate" class="column large-8"></div>

					<div class="column small-6 large-2 small-text-left large-text-center advSearch-slider-value">
						<span id="slider-acceptancerate-snap-value-lower"></span>%
					</div>

					<div class="column small-6 large-2 small-text-right large-text-center advSearch-slider-value">
						<span id="slider-acceptancerate-snap-value-upper"></span>%
					</div>	
				</div>

			</div>
		</div>

		<!-- test scores -->
		<div class="row ">
			<div class="column small-12 advSearch-slider-row-headers">
				{{Form::label('testscores', 'Test Scores 25% Percentile')}}
			</div>

			<!-- SAT Critical Reading score -->
			<div class="column small-12 large-4">
				<div class="row">
					<div class="column small-12">
						{{Form::label('acceptance', 'SAT Critical Reading')}}
					</div>
					<div class="column small-6">
						{{Form::text('advSearch_criticalReading', null, array('pattern'=>'name', 'placeholder'=>'Min', 'class'=>'frontpage-reading-min'))}}
					</div>
					<div class="column small-6">
						{{Form::text('advSearch_SAT_reading', null, array('pattern'=>'name', 'placeholder'=>'Max', 'class'=>'frontpage-reading-max'))}}
					</div>
				</div>
			</div>
			
			<!-- SAT Math score -->
			<div class="column small-12 large-4">
				<div class="row">
					<div class="column small-12">
						{{Form::label('acceptance', 'SAT Math')}}
					</div>
					<div class="column small-6">
						{{Form::text('advSearch_SAT_math', null, array('pattern'=>'name', 'placeholder'=>'Min', 'class'=>'frontpage-math-min'))}}
					</div>
					<div class="column small-6">
						{{Form::text('advSearch_SAT_math', null, array('pattern'=>'name', 'placeholder'=>'Max', 'class'=>'frontpage-math-max'))}}
					</div>
				</div>
			</div>

			<!-- ACT score -->
			<div class="column small-12 large-4">
				<div class="row">
					<div class="column small-12">
						{{Form::label('acceptance', 'ACT Composite')}}
					</div>
					<div class="column small-6">
						{{Form::text('advSearch_ACT_composite', null, array('pattern'=>'name', 'placeholder'=>'Min', 'class'=>'frontpage-composite-min'))}}
					</div>
					<div class="column small-6">
						{{Form::text('advSearch_ACT_composite', null, array('pattern'=>'name', 'placeholder'=>'Max', 'class'=>'frontpage-composite-max'))}}
					</div>
				</div>
			</div>
		</div>

		<!-- form submission -->
		<div class="row">
			<div class="column small-12">
				<div class="row">
					<div class="column small-6 large-push-7 large-2 large-text-right">
						{{Form::reset('Clear', array('class' => 'clear-advSearch-btn'))}}
					</div>
					<div class="column small-6 large-3">
						<div class="submit-advSearch-btn text-center" onClick="submitFrontpageAdvSearch();">Submit</div>
					</div>
				</div>
			</div>
		</div>

		<!-- error message to display if all form fields are empty; at least one must be filled -->
		<div class="row advSearch-error-row">
			<div class="column small-12 small-centered large-push-6 large-6 large-uncentered frontpage-advSearch-error-text">
				<i>Must fill out at least one of the form fields to see results!</i>
			</div>
		</div>

	</div>
	{{Form::close()}}
</div>

<script src="/js/jquery.nouislider.all.min.js?8"></script>
<script>
	/************* noiuslider initialization - start *************/
    // tuition slider
    $('#slider-tuition').noUiSlider({
        start: [ 0, 0 ],
        step: 1000,
        behaviour: 'drag-tap',
        connect: true,
        range: { 'min': 0 , 'max': 90000 },
        format: {
            to: function( val ){
                return parseInt(val);
            },
            from: function(val){
                return val;
            }
        }
    });

    $('#slider-tuition').Link('lower').to($('#slider-tuition-snap-value-lower'));
    $('#slider-tuition').Link('upper').to($('#slider-tuition-snap-value-upper'));

    // enrollment slider
    $('#slider-enrollment').noUiSlider({
        start: [ 0, 0 ],
        step: 1000,
        behaviour: 'drag-tap',
        connect: true,
        range: { 'min': 0 , 'max': 250000 },
        format: {
            to: function( val ){
                return parseInt(val);
            },
            from: function(val){
                return val;
            }
        }
    });

    $('#slider-enrollment').Link('lower').to($('#slider-enrollment-snap-value-lower'));
    $('#slider-enrollment').Link('upper').to($('#slider-enrollment-snap-value-upper'));

    // acceptance rate
    $('#slider-acceptancerate').noUiSlider({
        start: [ 0, 0 ],
        step: 5,
        behaviour: 'drag-tap',
        connect: true,
        range: { 'min': 0 , 'max': 100 },
        format: {
            to: function( val ){
                return parseInt(val);
            },
            from: function(val){
                return val;
            }
        }
    });

    $('#slider-acceptancerate').Link('lower').to($('#slider-acceptancerate-snap-value-lower'));
    $('#slider-acceptancerate').Link('upper').to($('#slider-acceptancerate-snap-value-upper'));
    /**************** noiuslider initialization - end ******************/
	
	var values = [];
	$('.frontpage-state option').each(function() { 
		values.push( $(this).attr('value') );
	});
	var vall = values;
	$( ".frontpage-country" ).change(function() 
	{
		country_name = $(this).val();
		if(country_name !="US"){
			$("#state_div").html('<label for="advSearch_state">State</label><input name="state" type="text" value="" placeholder="State Name" id="state-select-box" />');
			$("#city_div").html('<label for="advSearch_city">City</label><input name="city" type="text" value="" placeholder="City Name" id="city-select-box" />');
		}else{
			$("#state_div").html('<label for="advSearch_state">Select State</label><select class="frontpage-state" id="state-select-box" name="state"></select>');
			$.each(vall, function(key, value) {
				$('.frontpage-state').append('<option value="' + value + '">' + (value == '' ? 'Select...' : value) + '</option>');
			});
			$("#city_div").html('<label for="advSearch_city">Select City</label><select class="frontpage-city" id="city-select-box" name="city"><option value="">Select state first</option></select>');
		}
	});
</script>
