<?php 
	// dd($services_offered);
    // dd(get_defined_vars());
?>

<div class="row agency-settings-section-profileInfo">

{{ Form::open(array('url' => '/agency/ajax/saveProfileInfo', 'method' => 'POST', 'id' => 'profileInfoForm', 'data-abide'=>'ajax')) }}

	<div class="column small-12 large-6">
		
		<div class="section-head">Profile Info</div>		

		<div>
			{{Form::label('id', 'Make sure your company name is correct*', array('class' => 'strong-labels'))}}
			{{Form::text('name', $agency_name, array('placeholder' => 'Company name', 'pattern' => 'name', 'required', 'class' => 'form-field'))}}
			 <small class="error">Company name is required and cannot be empty.</small>
		</div>

		<div class="row">
			<div class="column small-12 large-6">
				{{Form::label('id', 'First Name*', array('class' => 'strong-labels'))}}
				{{Form::text('fname', $fname, array('placeholder' => 'First Name', 'pattern' => 'name', 'required', 'class' => 'form-field'))}}
				 <small class="error">Your first name is required and cannot be empty.</small>
			</div>
			<div class="column small-12 large-6">
				{{Form::label('id', 'Last Name*', array('class' => 'strong-labels'))}}
				{{Form::text('lname', $lname, array('placeholder' => 'Last Name', 'pattern' => 'name', 'required', 'class' => 'form-field'))}}
				 <small class="error">Your last name is required and cannot be empty.</small>
			</div>
		</div>

		<div>
			{{Form::label('id', 'Upload a nice profile photo', array('class' => 'strong-labels'))}}
			@if( isset($profile_pic) && !empty($profile_pic) )
			<div class="agency-prof-pic-container">
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/agency/profilepics/{{$profile_pic or ''}}" alt="Agency Profile Picture">
			</div>
			@endif
			{{Form::file('agencyProfilePic', array('class' => 'form-field'))}}	
		</div>

		<div>
			{{Form::label('id', 'Company Website', array('class' => 'strong-labels'))}}
			{{Form::text('web_url', $web_url, array('placeholder' => 'https://plexuss.com', 'pattern' => 'url', 'class' => 'form-field'))}}
			<small class="error">Company website is required. Ex: https://plexuss.com</small>
		</div>

        <div>  
            <div class='skype-label'>
                <img style='transform: scale(0.8)' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/skype_icon.png" alt="">
                <div class='skype-label-text'>Skype</div>
            </div>
            <div class='skype-input-container'>
                {{ Form::text('skype_id', $skype_id, array('id' => 'skype-id', 'placeholder'=>'Skype ID')) }}
{{--                 <span class="agency-skype-tooltip-icon has-tip" data-tooltip aria-haspopup="true" title="Skype is the most efficient way to communicate with you. Please provide a Skype ID.">?</span> --}}
            </div>
        </div>

        <div>
            <div>WhatsApp</div>
            {{ Form::text('whatsapp_id', $whatsapp_id, array('id' => 'whatsapp-id', 'placeholder'=>'WhatsApp ID')) }}
        </div>

		<div>
			{{Form::label('id', 'Phone Number*', array('class' => 'strong-labels'))}}
			{{Form::text('phone', $company_phone, array('placeholder' => '555-555-5555', 'pattern' => 'phone_num', 'required', 'class' => 'form-field'))}}
			<small class="error">Phone number is required. Ex: 123-123-1234</small>
		</div>

		<div>
			{{Form::label('id', 'Country*', array('class' => 'strong-labels'))}}
			{{Form::select('country', $countries, $my_country , array('class' => 'form-field', 'data-type' => 'select'))}}
			<small class="error">Country is required and. Select the appropriate country from the drop down list.</small>
		</div>

		<div>
			{{Form::label('id', 'State', array('class' => 'strong-labels'))}}
			{{Form::select('state', $states, $my_state , array('class' => 'form-field', 'data-type' => 'select'))}}
			<small class="error">State is required and. Select the appropriate state from the drop down list.</small>
		</div>

		<div>
			{{Form::label('id', 'City', array('class' => 'strong-labels'))}}
			{{Form::text('city', $city, array('placeholder' => 'Los Angeles', 'class' => 'form-field'))}}
			<small class="error">City is required and must be a string of letters only.</small>
		</div>

		<div class='business-hours-container mt20'>
			<div>Hours of Operation</div>
			<div class='days-container mt10'>
				@if (isset($days_of_operation) && !empty($days_of_operation))
					@foreach ($days_of_operation as $day => $hours)
						<?php 
							$start = isset($hours['start']) ? $hours['start'] : 'choose';
							$end = isset($hours['end']) ? $hours['end'] : 'choose';

							$is_open = intval($start) && intval($end);
						?>
						<div class='day @if($day == 'monday') active @endif @if($is_open) open @else closed @endif' data-day='{{$day}}' data-hours='{"open": "{{ $start }}", "close": "{{ $end }}"}'>{{ strtoupper(substr($day, 0, 3)) }}</div>
					@endforeach
				@else
					<div class='day active' data-day='monday' data-hours='{"open": "choose", "close": "choose"}'>MON</div>
					<div class='day' data-day='tuesday' data-hours='{"open": "choose", "close": "choose"}'>TUES</div>
					<div class='day' data-day='wednesday' data-hours='{"open": "choose", "close": "choose"}'>WED</div>
					<div class='day' data-day='thursday' data-hours='{"open": "choose", "close": "choose"}'>THU</div>
					<div class='day' data-day='friday' data-hours='{"open": "choose", "close": "choose"}'>FRI</div>
					<div class='day' data-day='saturday' data-hours='{"open": "choose", "close": "choose"}'>SAT</div>
					<div class='day' data-day='sunday' data-hours='{"open": "choose", "close": "choose"}'>SUN</div>
				@endif
			</div>
			<div class='hours-container mt20'>
				<span>Open</span>
				<select class='hours-select open-hour'>
					<option value="choose" selected='selected' disabled>Choose</option>
					<option value="12:00 AM">12:00 AM</option>
					<option value="12:30 AM">12:30 AM</option>
					@for ($i = 1; $i <= 11; $i++)
						<option value="{{$i}}:00 AM">{{$i}}:00 AM</option>
						<option value="{{$i}}:30 AM">{{$i}}:30 AM</option>
					@endfor
					<option value="12:00 AM">12:00 PM</option>
					<option value="12:30 AM">12:30 PM</option>
					@for ($i = 1; $i <= 11; $i++)
						<option value="{{$i}}:00 PM">{{$i}}:00 PM</option>
						<option value="{{$i}}:30 PM">{{$i}}:30 PM</option>
					@endfor
					<option value="closed">Closed</option>
				</select>
				<span>Closed</span>
				<select class='hours-select close-hour'>
					<option value="choose" selected='selected' disabled>Choose</option>
					<option value="12:00 AM">12:00 AM</option>
					<option value="12:30 AM">12:30 AM</option>
					@for ($i = 1; $i <= 11; $i++)
						<option value="{{$i}}:00 AM">{{$i}}:00 AM</option>
						<option value="{{$i}}:30 AM">{{$i}}:30 AM</option>
					@endfor
					<option value="12:00 AM">12:00 PM</option>
					<option value="12:30 AM">12:30 PM</option>
					@for ($i = 1; $i <= 11; $i++)
						<option value="{{$i}}:00 PM">{{$i}}:00 PM</option>
						<option value="{{$i}}:30 PM">{{$i}}:30 PM</option>
					@endfor
					<option value="closed">Closed</option>
				</select>
			</div>
			<div class='checkbox-options-container'>
				<div class='checkbox large-12 column'>
					<input id='normal-business-hours-check' type='checkbox' />
					<label for='normal-business-hours-check'>Normal Business Hours</label>
				</div>
  				<div class='checkbox large-12 column end'>
					<input id='not-open-check' type='checkbox' />
					<label for='not-open-check'>Not Open</label>
				</div>
			</div>
		</div>

	</div>

	<div class="column small-12 large-6">

		<div class="services-offered-container">
				<div class='service-checkboxes'>
					{{Form::label('id', 'Services Offered*', array('class' => 'strong-labels'))}}
					<small>Select all that apply, at least one required.</small>

					@if (!isset($services_offered) || empty($services_offered)) 
						<div class='mt10 service'>
							<input class='services-offered-option' id='agency-service-college-counseling' type='checkbox'/>
							<label for='agency-service-college-counseling'>College Counseling</label>
						</div>
						<div class='mt10 service'>
							<input class='services-offered-option' id='agency-service-tutoring-center' type='checkbox' />
							<label for='agency-service-tutoring-center'>Tutoring Center</label>
						</div>
						<div class='mt10 service'>
							<input class='services-offered-option' id='agency-service-test-preparation' type='checkbox' />
							<label for='agency-service-test-preparation'>Test Preparation</label>
						</div>
						<div class='mt10 service'>
							<input class='services-offered-option' id='agency-service-student-assistance' type='checkbox' />
							<label for='agency-service-student-assistance'>International Student Assistance</label>
						</div>
					@else
						<div class='mt10 service'>
							<input class='services-offered-option' id='agency-service-college-counseling' type='checkbox' @if(in_array('College Counseling', $services_offered)){{'checked'}}@endif/>
							<label for='agency-service-college-counseling'>College Counseling</label>
						</div>
						<div class='mt10 service'>
							<input class='services-offered-option' id='agency-service-tutoring-center' type='checkbox' @if(in_array('Tutoring Center', $services_offered)){{'checked'}}@endif/>
							<label for='agency-service-tutoring-center'>Tutoring Center</label>
						</div>
						<div class='mt10 service'>
							<input class='services-offered-option' id='agency-service-test-preparation' type='checkbox' @if(in_array('Test Preparation', $services_offered)){{'checked'}}@endif/>
							<label for='agency-service-test-preparation'>Test Preparation</label>
						</div>
						<div class='mt10 service'>
							<input class='services-offered-option' id='agency-service-student-assistance' type='checkbox' @if(in_array('International Student Assistance', $services_offered)){{'checked'}}@endif/>
							<label for='agency-service-student-assistance'>International Student Assistance</label>
						</div>
						@foreach ($services_offered as $service)
							@if ($service != 'College Counseling' && $service != 'Tutoring Center' && $service != 'Test Preparation' && $service != 'International Student Assistance')
								<div class='mt10 service'>
									<input class='services-offered-option' id='{{join('-', preg_split('/\s+/', strtolower($service)))}}' type='checkbox' checked/>
									<label for='{{join('-', preg_split('/\s+/', strtolower($service)))}}'>{{$service}}</label>
								</div>
							@endif
						@endforeach
					@endif
				</div>
				<div class='mt10 add-agency-service-btn'>+ Add a service</div>
				<div>
					{{ Form::text('new-service-name', null, array('id' => 'new-service-name')) }}
				</div>

			<small class="error">At least one services offered must be checked or added.</small>
		</div>

		<div>
			{{Form::label('id', 'Tell students about your company', array('class' => 'strong-labels'))}}
			<textarea class="form-field" name="detail" rows="5" data-type="textarea">{{$detail or ''}}</textarea>
		</div>	

		<div>
			{{Form::label('id', 'Do you specialize in any schools?', array('class' => 'strong-labels'))}}
			<small class="info">If so, type in a school name and add it to the list below</small>
			{{Form::text('school_names', '', array('placeholder' => 'Start typing a schools name...', 'class' => 'specialized-school-input form-field', 'id' => 'specialized_school_search'))}}
			<div class="specialized-schools-container">
				<!-- autocomplete will inject school names here -->
				@if( isset($specialized_schools) && !empty($specialized_schools) )
					@foreach( $specialized_schools as $school )
						<div class="specialed-item clearfix">
							<div class="specialized-inner left">{{$school['school_name']}}</div>
							<div class="specialized-inner right remove-specialized-school"> X </div>
							{{Form::hidden('schools_specialized_in[]', $school['school_id'])}}
						</div>
					@endforeach
				@endif
			</div>
		</div>

	</div>

	<div class="column small-12">
		<div class="row">
			<div class="column small-12 large-6 save-row-inner">
				<div class="agency-settings-error-msg">
					<small>There is an error on the page. Make sure none of the required* fields are empty.</small>	
				</div>

				<div class="row">
					<div class="column small-6">
						<div class="btn-profile saveProfile-btn text-center">Save</div>
					</div>
					<div class="column small-6">
						<!--<div class="btn-profile previewProfile-btn text-center">Preview Public Profile</div>-->
					</div>
				</div>	
			</div>
		</div>
	</div>

{{Form::close()}}

</div>

<script>
	$(document).ready(function() {
		$('.day.active').click();
	});
</script>
{{-- 
<script type="text/javascript">
	$(document)
	.foundation({
		abide : {
		  patterns: {
		    age: /^([1-9]?\d|100)$/,
		    phone_num: /^([0-9\-\+\(\) ])+$/,
			name: /^([a-zA-Z\-\.' ])+$/,
		  }
		}
	});
</script> --}}