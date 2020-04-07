<h3>Profile Info</h3>
<div class='row'>
	{{ Form::open(array('data-abide')) }}
	<div class='large-6 column'>
		<div class='required-info-notice'>*Required information</div>

		<div class='mt10'>
			<div><b>First name*</b></div>
			{{ Form::text('fname', $fname, array('id' => 'rep_fname', 'required', 'placeholder'=>'First name')) }}
		</div>

		<div>
			<div><b>Last name*</b></div>
			{{ Form::text('lname', $lname, array('id' => 'rep_lname', 'required', 'placeholder'=>'Last name')) }}
		</div>

        <div>
            <div><b>Institution*</b></div>
            {{ Form::text('rep_college_search', null, array('id' => 'rep_college_search', 'placeholder'=>'Search Institutions')) }}
            
            <div id='add-new-college-container' class='hidden'>
                <div class='top-container'>
                    <div class='add-college-back-button'>< Back</div>
                    <div class='add-college-header'>Add your Institution</div>
                    <div style='width: 10%;'></div>
                </div>

                <div>
                    <div><b>Institution*</b></div>
                    {{ Form::text('new_college_name', null, array('placeholder' => 'Name')) }}
                </div>
                
                <div>
                    <div><b>Country *</b></div>
                    @if (isset($country_list) && $country_list != null)
                        {{ Form::select('new_college_country', $country_list, null, array('id' => 'new_college_country')) }}
                    @else
                        {{ Form::text('new_college_country', null, array('placeholder' => 'Country')) }}
                    @endif
                </div>

                <div>
                    <div><b>City *</b></div>
                    {{ Form::text('new_college_city', null, array('id' => 'new_college_city', 'placeholder '=> 'City')) }}
                </div>

                <div>
                    <div><b>State / Province *</b></div>
                    {{ Form::text('new_college_state', null, array('id' => 'new_college_state', 'placeholder '=> 'State')) }}
                </div>

                <div class='submit-new-college-button'>Add</div>
            </div>

            <div id='rep_selected_college' class='hidden'>
                <div class='college-container'>
                    <div class='college-image'>{{-- Insert img tag here --}}</div>
                    <div class='college-label'>
                        <h5></h5>
                        <p></p>
                    </div>
                </div>
                <div class='change-college-button'>
                    Change
                </div>
            </div>
        </div>

        <div>
            <div><b>Title*</b></div>
            {{ Form::text('title', null, array('id' => 'rep_title', 'required', 'placeholder'=>'Name')) }}
        </div>

        <div>
            <div><b>Working since*</b></div>
            {{ Form::text('working_since_date', null, array('class' => 'datepicker', 'required', 'placeholder'=>'Select a date', 'pattern' => 'date')) }}
            <small class="error">*Please enter a valid date format. MM/DD/YYYY.</small>
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
            <div class='skype-label'>
                <img style='transform: scale(0.8)' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/skype_icon.png" alt="">
                <div class='skype-label-text'><b>Skype</b></div>
            </div>
            <div class='skype-input-container'>
                {{ Form::text('skype_id', null, array('id' => 'skype-id', 'placeholder'=>'Skype ID')) }}
            </div>
        </div>

        <div class='mt20'>
            <div><b>Blurb (Keep it clear and simple)</b></div>
            {{ Form::textarea('blurb', null, array('id' => 'about-company-blurb', 'maxlength' => 150, 'rows' => '5', 'placeholder'=>'')) }}
            <div class='blub-characters-container'>Characters left: <span class='blurb-characters-left'>150</span></div>
        </div>
	</div>

	<div class='large-6 column'>
		<div class='right-side-header'><b>Preview of your rep profile (Students will see)</b></div>
        <div class='dynamic-profile-preview-container'>
            <div class='preview-side-container'>
                <div class='dynamic-profile-preview-side front'>
                    <div class='user-image-icon'></div>
                    <div class='change-user-image-upload-container'>
                        <label for='admin-profile-photo'>
                            <div>Upload Photo</div>
                            <input id='admin-profile-photo' type='file' name='admin-profile-photo' accept='image/x-png, image/gif, image/jpeg'>
                        </label>
                    </div>
                    <div class='change-user-image-upload-text'>Only .jpg .png .gif .bmp allowed</div>
                </div>
                <div class='dynamic-profile-side-label'>Front</div>
            </div>
            <div class='preview-side-container'>
                <div class='dynamic-profile-preview-side back'>
                    <div class='college-image-icon'></div>
                    <div class='user-details-container'>
                        <div class='user-name'>{{ isset($fname) && isset($lname) ? ($fname . ' ' . $lname) : ''}}</div>
                        <div class='user-title'>[Title]</div>
                        <div>Since <span class='working-since-year'>[Year]</span></div>
                    </div>
                    <div class='preview-blurb-text'>[Small blurb about yourself]</div>
                </div>
                <div class='dynamic-profile-side-label'>Back</div>
            </div>
        </div>
        <div class='view-sample-image-button'>
            View a sample of what your pin could look like
        </div>
		<div class='show-profile-container'>
            <div>Show profile on these pages</div>
            <div class='show-toggle-checkboxes-container'>
                <div>
                    <label><input type="checkbox" name="show_on_frontpage_check" value="1"> Front page</label>
                    <div class='view-preview-button front'>View</div>
                </div>

                <div>
                    <label><input type="checkbox" name="show_on_collegepage_check" value="1"> College page</label>
                    <div class='view-preview-button college'>View</div>
                </div>

            </div>
            			
		</div>
	</div>
    <div class='large-12 column submit-container step-3'>
        <button class='admin-signup-button step-3'>Submit</button>
    </div>
	{{ Form::close() }}
    <div id="preview-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
        <a class="close-reveal-modal" aria-label="Close">&#215;</a>
        <div class='toggle-image-preview-container'>
            <div class='image-preview-text front active'>Front</div>
            <div class='image-preview-text college'>College</div>
        </div>
        <div class='preview-image-container'>
            <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/profile_on_frontpage_preview.jpg' />
        </div>
    </div>
</div>