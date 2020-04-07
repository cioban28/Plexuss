<h3>Profile Info</h3>
<div class='row'>
	{{ Form::open(array('data-abide')) }}
	<div class='large-6 column'>
		<div>*Required information</div>

		<div class='mt10'>
			<div><b>Company Name * (Will not be displayed)</b></div>
			{{ Form::text('company_name', null, array('id' => 'company-name', 'required', 'placeholder'=>'College Campus Counseling')) }}
		</div>

		<div>
			<div><b>Representative Name *</b></div>
			{{ Form::text('representative_name', null, array('id' => 'representative-name', 'required', 'placeholder'=>'Name')) }}
		</div>

        <div>  
            <div class='skype-label'>
                <img style='transform: scale(0.8)' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/skype_icon.png" alt="">
                <div class='skype-label-text'><b>Skype</b></div>
            </div>
            <div class='skype-input-container'>
                {{ Form::text('skype_id', null, array('id' => 'skype-id', 'placeholder'=>'Skype ID')) }}
{{--                 <span class="agency-skype-tooltip-icon has-tip" data-tooltip aria-haspopup="true" title="Skype is the most efficient way to communicate with you. Please provide a Skype ID.">?</span> --}}
            </div>
        </div>

        <div>
            <div><b>WhatsApp</b></div>
            {{ Form::text('whatsapp_id', null, array('id' => 'whatsapp-id', 'placeholder'=>'WhatsApp ID')) }}
        </div>

		<div class='image-upload-container'>
			<div><b>Upload a nice profile photo</b></div>
			<div class='image-upload'>
				<label for='agency-profile-photo'>
					<div class='upload-photo-icon'>
						<img src='/images/upload-photo.png'>
						<div id='agency-profile-photo-checkmark'></div>
					</div>
				</label>
				<input id='agency-profile-photo' type='file' name='agency-profile-photo' accept='image/x-png, image/gif, image/jpeg'>
				<div>
			    	<img id="img-preview" src="#" alt="" />
			    </div>
		    </div>
            {{-- <small class="profile-picture-upload-error">You must upload a profile picture</small> --}}
		</div>
		
		<div>
			<div><b>Company Website (Will not be displayed)</b></div>
			{{ Form::text('website_url', null, array('id' => 'company-website', 'placeholder'=>'https://www.college-campus-counseling.com')) }}
		</div>

		<div>
			<div><b>Phone *</b></div>
			<div class='phone-field'>
				<select name='country_code' id='country-code-select' required>
					@foreach ($country_code_list as $key => $country_name)
					<?php
						$regex = '/\((\+[^)]+)\)/';
						preg_match($regex, $country_name, $matched);
						$phone_code = $matched[1];
					?>
					<option value='{{$phone_code}}'>{{$country_name}}</option>
					@endforeach
				</select>

				{{ Form::text('phone_number', null, array('id' => 'phone-number', 'required', 'placeholder'=>'Enter phone number')) }}
				<small class="phone-error-msg error">*Not a valid phone number. Make sure your country code is correct.</small>
			</div>
		</div>

		<div>
			<div><b>Country *</b></div>
			@if (isset($country_list) && $country_list != null)
			{{ Form::select('country-name', $country_list, null, array('id' => 'country-name', 'required')) }}
			@else
			{{ Form::text('country-name', null, array('id' => 'representative-name', 'required', 'placeholder'=>'Country')) }}
			@endif
		</div>

		<div>
			<div><b>City</b></div>
			{{ Form::text('city-name', null, array('id' => 'city-name', 'placeholder'=>'City')) }}
		</div>
	</div>

	<div class='large-6 column'>
		<div><b>Services Offered*</b></div>
		<small>Select all that apply, at least one required.</small>
		<div class='service-checkboxes'>
			<div class='mt10 service'>
				<input id='agency-service-college-counseling' type='checkbox' />
				<label for='agency-service-college-counseling'>College Counseling</label>
			</div>
			<div class='mt10 service'>
				<input id='agency-service-tutoring-center' type='checkbox' />
				<label for='agency-service-tutoring-center'>Tutoring Center</label>
			</div>
			<div class='mt10 service'>
				<input id='agency-service-test-preparation' type='checkbox' />
				<label for='agency-service-test-preparation'>Test Preparation</label>
			</div>
			<div class='mt10 service'>
				<input id='agency-service-student-assistance' type='checkbox' />
				<label for='agency-service-student-assistance'>International Student Assistance</label>
			</div>
		</div>
		<div class='mt10 add-agency-service-btn'>+ Add a service</div>
		<div>
			{{ Form::text('new-service-name', null, array('id' => 'new-service-name')) }}
		</div>

		<div class='mt20'>
			<div><b>What can you do for a Plexuss Student?</b></div>
			{{ Form::textarea('about_company_text', null, array('id' => 'about-agent-company', 'placeholder'=>'')) }}
		</div>

		<div class='view-agency-profile-sample-btn'>
			<img class='profile-sample-img' src="/images/agency/agency_profile_example.png" />
			<u>View a sample of what your page could look like</u>
		</div>

		<div class='business-hours-container mt20'>
			<div><b>Hours of Operation</b></div>
			<div class='days-container mt10'>
				<div class='day active' data-day='monday' data-hours='{"open": "choose", "close": "choose"}'>MON</div>
				<div class='day' data-day='tuesday' data-hours='{"open": "choose", "close": "choose"}'>TUES</div>
				<div class='day' data-day='wednesday' data-hours='{"open": "choose", "close": "choose"}'>WED</div>
				<div class='day' data-day='thursday' data-hours='{"open": "choose", "close": "choose"}'>THU</div>
				<div class='day' data-day='friday' data-hours='{"open": "choose", "close": "choose"}'>FRI</div>
				<div class='day' data-day='saturday' data-hours='{"open": "choose", "close": "choose"}'>SAT</div>
				<div class='day' data-day='sunday' data-hours='{"open": "choose", "close": "choose"}'>SUN</div>
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

		<div class='row'>
			<div class='large-12 column'>
				<button class='agency-signup-button step-3'>Next</button>
			</div>
		</div>
	</div>
	{{ Form::close() }}
</div>