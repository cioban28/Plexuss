<!-- Fist Time Home Modal -->
<div id="firstTimeMessagemodal" class="firstTimeMessageModal reveal-modal column" data-reveal>
	<div class='row'>
		<div class='large-centered small-12 column'>
			<h3 class='subheader'>Tell us a little about yourself...</h3>
			<p class="lead">There are a few questions we need in order to get you recruited by the colleges you want.  You can answer them now or later.</p>
		</div>
	</div>
	<div class="row">
		{{Form::open(array('url' => '/', 'method' => 'POST', 'data-abide' => 'ajax', 'id' => 'modalSchoolInfoForm'))}}
		{{Form::hidden('school_id', null , array('id'=>'school_id', 'class' => 'ft_reset'))}}
		<div class='large-centered large-12 column'>
			<!-- 
				THIS USER TYPE MODAL IS EXPANDABLE! Add a new div containing the requisite form elements
				to the bottom. The id name should follow the same ft_modal_[select value] or 
				[select_value]_rest_of_class_or_id_name naming convention.
			-->


			<!-- this will only show up if user signs up with Facebook, but an email isn't returned from Facebook (user didn't confirm their fb acct) - start -->
			@if( isset($email) && $email == 'none' )
				<div class="row collapse">
					<div class="column small-12 large-4">
						{{Form::label('no_email', 'Email', array())}}
					</div>
					<div class="column small-12 large-7 end">
						{{Form::email('no_email', null, array('placeholder'=>'Enter email...', 'pattern'=>'email', 'required'))}}
						<small class="error">Invalid email address entered</small>
					</div>
				</div>
			@endif
			<!-- this will only show up if user signs up with Facebook, but an email isn't returned from Facebook (user didn't confirm their fb acct) - start -->


			<!-- STUDENT TYPE -->
			<div id="#user_type_container" class='row collapse'>
				<div class='large-4 column'>
					{{ Form::label('user_type', 'I am a(n)', array('class'=> 'inline')) }}
				</div>
				<div class='large-7 column end'>
					{{ Form::select('user_type', array("" => 'Please select one...', 'student' => 'Student', 'alumni' => 'Alumni', 'parent' => 'Parent or Guardian', 'counselor' => 'Counselor or Teacher', 'university_rep' => 'University Rep'), null, array('id' => 'user_type', 'class' => 'no_reset', 'required')) }}
					<small class="error">You need to select a user type</small>
				</div>
			</div>

			<!-- BEGIN REGULAR STUDENT MODAL FORM -->
			<div id='ft_modal_student' class='row ft_modal_container'>
				<div class='small-12 column'>
					<!-- COUNTRY -->
					<div id="student_country_container" class="row collapse ft_country_container ftm_student">
						<div class='large-4 column'>
							{{Form::label('student_country', 'Your Country', array('class'=> 'inline')) }}
						</div>
						<div class='large-7 column end'>
							{{Form::select('student_country', $countries, '1', array( 'id' => 'student_country','class' => 'inline ft_country ft_required', 'required')) }}
							<small class="error">Add school attended</small>
						</div>
					</div>
					<!-- ZIPCODE -->
					<div id='student_zipcode_container' class='row collapse ftm_student'>
						<div class='large-4 column'>
							{{ Form::label('student_zipcode', 'Your Zip code', array('class'=> 'inline')) }}
						</div>
						<div class='large-7 column end'>
							{{ Form::text('student_zipcode', null, array('id' => 'student_zipcode', 'class' => 'ft_zipcode ft_reset', 'placeholder' => 'Zipcode', 'pattern' => 'zip')) }}
							<small class="error">You need to add your zipcode to help locate school near you.</small>
						</div>
					</div>
					<!-- LEVEL OF EDUCATION -->
					<div id="student_school_type_container" class="row collapse ftm_student">
						<div class='large-4 column'>
							{{Form::label('student_school_type', 'Current level of education', array('class'=> 'inline'))}}
						</div>
						<div class='large-7 column end'>
							{{Form::select('student_school_type', array('' => 'Select education level','highschool' => 'High School','college' => 'College'), null, array('id' => 'student_school_type', 'class' => 'ft_school_type ft_reset ft_disabled ft_required', 'required')) }}
							<small class="error">Select level of education</small>
						</div>
					</div>
					<!-- HOME SCHOOLED SCHOOL CHECKBOX -->
					<div class="row collapse ftm_student">
						<div id='student_homeschooled_container' class='large-offset-4 large-8 column'>
							{{Form::checkbox('student_homeschooled', '1', null, array('id' => 'student_homeschooled', 'class' => 'ft_hide_school_name ft_reset ft_disabled'))}}
							{{Form::label('student_homeschooled', 'Home schooled')}}
						</div>
					</div>
					<!-- SCHOOL NAME CONTAINER -->
					<div id="student_school_name_container" class="row collapse ft_school_name_container ftm_student">
						<div class='large-4 column'>
							{{Form::label('student_school_name', 'Name of school', array('class'=> 'inline')) }}
						</div>
						<div class='large-7 column end ui-front'>
							{{Form::text('student_school_name', null, array( 'id' => 'student_school_name','class' => 'inline ft_school_name ft_reset ft_disabled ft_required', 'required', 'pattern' => 'school_name')) }}
							<small class="error">Add school attended</small>
						</div>
					</div>
					<div class="row collapse ftm_student">
						<div class='large-4 column'>
							{{ Form::label('student_grad_year', 'Year of graduation', array('class'=> 'inline')) }}
						</div>
						<div class='large-7 column end'>
							<?php 
							$today = date("Y");
							$selected = $today;
							$startYear = $today + 6;
							$endYear = $today - 53;
							?>
							<select id='student_grad_year' class="field ft_grad_year ft_reset ft_disabled ft_required" name="student_grad_year" required >
								<option value="">Select a year</option>
								@for ($i = $startYear; $i > $endYear; $i--)
									<option value="{{$i}}">{{$i}}</option>
								@endfor
								<option value="ged">GED</option>
								<option value="ng">Never Graduated</option>
							</select>
							<small class="error">Select a Year</small>
						</div>
					</div>
				</div>
			</div>
			<!-- END STUDENT MODAL FORM -->
			<!-- BEGIN ALUMNI MODAL FORM -->
			<div id='ft_modal_alumni' class='row ft_modal_container'>
				<div class='small-12 column'>
					<!-- COUNTRY -->
					<div id="alumni_country_container" class="row collapse ft_country_container ftm_alumni">
						<div class='large-4 column'>
							{{Form::label('alumni_country', 'Your Country', array('class'=> 'inline')) }}
						</div>
						<div class='large-7 column end'>
							{{Form::select('alumni_country', $countries, '1', array( 'id' => 'alumni_country','class' => 'inline ft_country ft_required', 'required')) }}
							<small class="error">Add school attended</small>
						</div>
					</div>
					<!-- ZIPCODE -->
					<div id='alumni_zipcode_container' class='row collapse ftm_alumni'>
						<div class='large-4 column'>
							{{ Form::label('alumni_zipcode', 'Your Zip code', array('class'=> 'inline')) }}
						</div>
						<div class='large-7 column end'>
							{{ Form::text('alumni_zipcode', null, array('id' => 'alumni_zipcode', 'class' => 'ft_zipcode ft_reset', 'placeholder' => 'Zipcode', 'pattern' => 'zip')) }}
							<small class="error">You need to add your zipcode to help locate school near you.</small>
						</div>
					</div>
					<!-- LEVEL OF EDUCATION -->
					<div id="alumni_school_type_container" class="row collapse ftm_alumni">
						<div class='large-4 column'>
							{{Form::label('alumni_school_type', 'Highest level of education', array('class'=> 'inline'))}}
						</div>
						<div class='large-7 column end'>
							{{Form::select('alumni_school_type', array('' => 'Select education level','highschool' => 'High School','college' => 'College'), null, array('id' => 'alumni_school_type', 'class' => 'ft_school_type ft_reset ft_disabled ft_required', 'required')) }}
							<small class="error">Select level of education</small>
						</div>
					</div>
					<!-- HOME SCHOOLED/INTERNATIONAL SCHOOL CHECKBOX -->
					<div class="row collapse ftm_alumni">
						<div id='alumni_homeschooled_container' class='large-offset-4 large-8 column'>
							{{Form::checkbox('alumni_homeschooled', '1', null, array('id' => 'alumni_homeschooled', 'class' => 'ft_hide_school_name ft_reset ft_disabled'))}}
							{{Form::label('alumni_homeschooled', 'Home schooled')}}
						</div>
					</div>
					<!-- SCHOOL NAME CONTAINER -->
					<div id="alumni_school_name_container" class="row collapse ft_school_name_container ftm_alumni">
						<div class='large-4 column'>
							{{Form::label('alumni_school_name', 'Name of school', array('class'=> 'inline')) }}
						</div>
						<div class='large-7 column end ui-front'>
							{{Form::text('alumni_school_name', null, array( 'class' => 'inline', 'id' => 'alumni_school_name', 'class' => 'ft_school_name ft_reset ft_required ft_disabled', 'required', 'pattern' => 'school_name')) }}
							<small class="error">Add school attended</small>
						</div>
					</div>
					<div class="row collapse ftm_alumni">
						<div class='large-4 column'>
							{{ Form::label('alumni_grad_year', 'Year of graduation', array('class'=> 'inline')) }}
						</div>
						<div class='large-7 column end'>
							<?php 
							$today = date("Y");
							$selected = $today;
							$startYear = $today + 6;
							$endYear = $today - 53;
							?>
							<select id='alumni_grad_year' class="field ft_grad_year ft_reset ft_required ft_disabled" name="alumni_grad_year" required >
								<option value="">Select a year</option>
								@for ($i = $startYear; $i > $endYear; $i--)
									<option value="{{$i}}">{{$i}}</option>
								@endfor
								<option value="ged">GED</option>
								<option value="ng">Never Graduated</option>
							</select>
							<small class="error">Select a Year</small>
						</div>
					</div>
				</div>
			</div>
			<!-- END ALUMNI MODAL FORM -->
			<!-- BEGIN PARENT/GUARDIAN MODAL FORM -->
			<div id='ft_modal_parent' class='row ft_modal_container'>
				<div class='small-12 column'>
					<!-- COUNTRY -->
					<div id="parent_country_container" class="row collapse ft_country_container ftm_parent">
						<div class='large-4 column'>
							{{Form::label('parent_country', 'Your Country', array('class'=> 'inline')) }}
						</div>
						<div class='large-7 column end'>
							{{Form::select('parent_country', $countries, '1', array( 'id' => 'parent_country', 'class' => 'inline ft_country ft_required', 'required')) }}
							<small class="error">Add school attended</small>
						</div>
					</div>
					<!-- ZIPCODE -->
					<div id='parent_zipcode_container' class='row collapse ftm_parent'>
						<div class='large-4 column'>
							{{ Form::label('parent_zipcode', 'Your Zip code', array('class'=> 'inline')) }}
						</div>
						<div class='large-7 column end'>
							{{ Form::text('parent_zipcode', null, array('id' => 'parent_zipcode', 'class' => 'ft_zipcode ft_reset', 'placeholder' => 'Zipcode', 'pattern' => 'zip')) }}
							<small class="error">You need to add your zipcode to help locate school near you.</small>
						</div>
					</div>
					<!-- LEVEL OF EDUCATION -->
					<div id="parent_school_type_container" class="row collapse ftm_parent">
						<div class='large-4 column'>
							{{Form::label('parent_school_type', 'Your level of education', array('class'=> 'inline'))}}
						</div>
						<div class='large-7 column end'>
							{{Form::select('parent_school_type', array('' => 'Select education level','highschool' => 'High School','college' => 'College'), null, array('id' => 'parent_school_type', 'class' => 'ft_school_type ft_reset ft_disabled ft_required', 'required')) }}
							<small class="error">Select level of education</small>
						</div>
					</div>
					<!-- HOME SCHOOLED/INTERNATIONAL SCHOOL CHECKBOX -->
					<div class="row collapse ftm_parent">
						<div id='parent_homeschooled_container' class='large-offset-4 large-8 column'>
							{{Form::checkbox('parent_homeschooled', '1', null, array('id' => 'parent_homeschooled', 'class' => 'ft_hide_school_name ft_reset ft_disabled'))}}
							{{Form::label('parent_homeschooled', 'Home schooled')}}
						</div>
					</div>
					<!-- SCHOOL NAME CONTAINER -->
					<div id="parent_school_name_container" class="row collapse ft_school_name_container ftm_parent">
						<div class='large-4 column'>
							{{Form::label('parent_school_name', 'Name of your school', array('class'=> 'inline')) }}
						</div>
						<div class='large-7 column end ui-front'>
							{{Form::text('parent_school_name', null, array('class' => 'inline', 'id' => 'parent_school_name', 'class' => 'ft_school_name ft_reset ft_required ft_disabled', 'required', 'pattern' => 'school_name')) }}
							<small class="error">Add school attended</small>
						</div>
					</div>
					<div class="row collapse ftm_parent">
						<div class='large-4 column'>
							{{ Form::label('parent_grad_year', 'Year of graduation', array('class'=> 'inline')) }}
						</div>
						<div class='large-7 column end'>
							<?php 
							$today = date("Y");
							$selected = $today;
							$startYear = $today + 6;
							$endYear = $today - 53;
							?>
							<select id='parent_grad_year' class="field ft_grad_year ft_reset ft_required ft_disabled" name="parent_grad_year" required >
								<option value="">Select a year</option>
								@for ($i = $startYear; $i > $endYear; $i--)
									<option value="{{$i}}">{{$i}}</option>
								@endfor
								<option value="ged">GED</option>
								<option value="ng">Never Graduated</option>
							</select>
							<small class="error">Select a Year</small>
						</div>
					</div>
				</div>
			</div>
			<!-- END PARENT/GUARDIAN MODAL FORM -->
			<!-- BEGIN COUNSELOR/TEACHER MODAL FORM -->
			<div id='ft_modal_counselor' class='row ft_modal_container'>
				<div class='small-12 column'>
					<!-- COUNTRY -->
					<div id="counselor_country_container" class="row collapse ft_country_container ftm_counselor">
						<div class='large-4 column'>
							{{Form::label('counselor_country', 'Your Country', array('class'=> 'inline')) }}
						</div>
						<div class='large-7 column end'>
							{{Form::select('counselor_country', $countries, '1', array( 'class' => 'inline', 'id' => 'counselor_country','class' => 'ft_country', 'required')) }}
							<small class="error">Add school attended</small>
						</div>
					</div>
					<!-- ZIPCODE -->
					<div id='counselor_zipcode_container' class='row collapse ftm_counselor'>
						<div class='large-4 column'>
							{{ Form::label('counselor_zipcode', 'Your Zip code', array('class'=> 'inline')) }}
						</div>
						<div class='large-7 column end'>
							{{ Form::text('counselor_zipcode', null, array('id' => 'counselor_zipcode', 'class' => 'ft_zipcode ft_reset', 'placeholder' => 'Zipcode', 'pattern' => 'zip')) }}
							<small class="error">You need to add your zipcode to help locate school near you.</small>
						</div>
					</div>
					<!-- LEVEL OF EDUCATION -->
					<div id="counselor_school_type_container" class="row collapse ftm_counselor">
						<div class='large-4 column'>
							{{Form::label('counselor_school_type', 'Highest level of education', array('class'=> 'inline'))}}
						</div>
						<div class='large-7 column end'>
							{{Form::select('counselor_school_type', array('' => 'Select education level','highschool' => 'High School','college' => 'College'), null, array('id' => 'counselor_school_type', 'class' => 'ft_school_type ft_reset ft_disabled ft_required', 'required')) }}
							<small class="error">Select level of education</small>
						</div>
					</div>
					<!-- HOME SCHOOLED/INTERNATIONAL SCHOOL CHECKBOX -->
					<div class="row collapse ftm_counselor">
						<div id='counselor_homeschooled_container' class='large-offset-4 large-8 column'>
							{{Form::checkbox('counselor_homeschooled', '1', null, array('id' => 'counselor_homeschooled', 'class' => 'ft_hide_school_name ft_reset ft_disabled'))}}
							{{Form::label('counselor_homeschooled', 'Home schooled')}}
						</div>
					</div>
					<!-- SCHOOL NAME CONTAINER -->
					<div id="counselor_school_name_container" class="row collapse ft_school_name_container ftm_counselor">
						<div class='large-4 column'>
							{{Form::label('counselor_school_name', 'Name of school', array('class'=> 'inline')) }}
						</div>
						<div class='large-7 column end ui-front'>
							{{Form::text('counselor_school_name', null, array( 'class' => 'inline', 'id' => 'counselor_school_name', 'class' => 'ft_school_name ft_reset ft_required ft_disabled', 'required', 'pattern' => 'school_name')) }}
							<small class="error">Add school attended</small>
						</div>
					</div>
					<div class="row collapse ftm_counselor">
						<div class='large-4 column'>
							{{ Form::label('counselor_grad_year', 'Year of graduation', array('class'=> 'inline')) }}
						</div>
						<div class='large-7 column end'>
							<?php 
							$today = date("Y");
							$selected = $today;
							$startYear = $today + 6;
							$endYear = $today - 53;
							?>
							<select id='counselor_grad_year' class="field ft_grad_year ft_reset ft_required ft_disabled" name="counselor_grad_year" required >
								<option value="">Select a year</option>
								@for ($i = $startYear; $i > $endYear; $i--)
									<option value="{{$i}}">{{$i}}</option>
								@endfor
								<option value="ged">GED</option>
								<option value="ng">Never Graduated</option>
							</select>
							<small class="error">Select a Year</small>
						</div>
					</div>
				</div>
			</div>
			<!-- END COUNSELOR/TEACHER MODAL FORM -->

			<!-- begin University Rep modal form -->
			<div id='ft_modal_university_rep' class='row ft_modal_container'>
				<div class='small-12 column'>
					<!-- COUNTRY -->
					<div id="university_rep_country_container" class="row collapse ft_country_container ftm_university_rep">
						<div class='large-4 column'>
							{{Form::label('university_rep_country', 'Your Country', array('class'=> 'inline')) }}
						</div>
						<div class='large-7 column end'>
							{{Form::select('university_rep_country', $countries, '1', array( 'class' => 'inline', 'id' => 'university_rep_country','class' => 'ft_country', 'required')) }}
							<small class="error">Add school attended</small>
						</div>
					</div>
					<!-- ZIPCODE -->
					<div id='university_rep_zipcode_container' class='row collapse ftm_university_rep'>
						<div class='large-4 column'>
							{{ Form::label('university_rep_zipcode', 'Your Zip code', array('class'=> 'inline')) }}
						</div>
						<div class='large-7 column end'>
							{{ Form::text('university_rep_zipcode', null, array('id' => 'university_rep_zipcode', 'class' => 'ft_zipcode ft_reset', 'placeholder' => 'Zipcode', 'pattern' => 'zip')) }}
							<small class="error">You need to add your zipcode to help locate school near you.</small>
						</div>
					</div>
					<!-- LEVEL OF EDUCATION -->
					<div id="university_rep_school_type_container" class="row collapse ftm_university_rep">
						<div class='large-4 column'>
							{{Form::label('university_rep_school_type', 'Highest level of education', array('class'=> 'inline'))}}
						</div>
						<div class='large-7 column end'>
							{{Form::select('university_rep_school_type', array('' => 'Select education level','highschool' => 'High School','college' => 'College'), null, array('id' => 'university_rep_school_type', 'class' => 'ft_school_type ft_reset ft_disabled ft_required', 'required')) }}
							<small class="error">Select level of education</small>
						</div>
					</div>
					<!-- HOME SCHOOLED/INTERNATIONAL SCHOOL CHECKBOX -->
					<div class="row collapse ftm_university_rep">
						<div id='university_rep_homeschooled_container' class='large-offset-4 large-8 column'>
							{{Form::checkbox('university_rep_homeschooled', '1', null, array('id' => 'university_rep_homeschooled', 'class' => 'ft_hide_school_name ft_reset ft_disabled'))}}
							{{Form::label('university_rep_homeschooled', 'Home schooled')}}
						</div>
					</div>
					<!-- SCHOOL NAME CONTAINER -->
					<div id="university_rep_school_name_container" class="row collapse ft_school_name_container ftm_university_rep">
						<div class='large-4 column'>
							{{Form::label('university_rep_school_name', 'Name of school', array('class'=> 'inline')) }}
						</div>
						<div class='large-7 column end ui-front'>
							{{Form::text('university_rep_school_name', null, array( 'class' => 'inline', 'id' => 'university_rep_school_name', 'class' => 'ft_school_name ft_reset ft_required ft_disabled', 'required', 'pattern' => 'school_name')) }}
							<small class="error">Add school attended</small>
						</div>
					</div>
					<div class="row collapse ftm_university_rep">
						<div class='large-4 column'>
							{{ Form::label('university_rep_grad_year', 'Year of graduation', array('class'=> 'inline')) }}
						</div>
						<div class='large-7 column end'>
							<?php 
							$today = date("Y");
							$selected = $today;
							$startYear = $today + 6;
							$endYear = $today - 53;
							?>
							<select id='university_rep_grad_year' class="field ft_grad_year ft_reset ft_required ft_disabled" name="university_rep_grad_year" required >
								<option value="">Select a year</option>
								@for ($i = $startYear; $i > $endYear; $i--)
									<option value="{{$i}}">{{$i}}</option>
								@endfor
								<option value="ged">GED</option>
								<option value="ng">Never Graduated</option>
							</select>
							<small class="error">Select a Year</small>
						</div>
					</div>
				</div>
			</div>
			<!-- end University Rep modal form -->
			<div class='row '>
				<div class="column small-12 large-push-4 large-7 firstTime-skip-start-row">
					<div class="row">
						<div class="column small-12 large-6">
							@if( isset($email) && $email != 'none' )
								@if ( !isset($RecruitCollegeId) )
									<div class="ft_btn_skip firstTime_skipBtn">
										<a onclick="Plex.inviteContacts.openInviteModal();skipHomepageSchoolInfo();">skip this question and answer later</a>
										<!--<a onclick="skipHomepageSchoolInfo();">skip this question and answer later</a>-->
									</div>
								@endif
							@endif
						</div>
						<div class="column small-12 large-6">
							{{ Form::submit('Next', array('class'=>'button ft_btn_next'))}}
						</div>
					</div>
				</div>
			</div>
		</div>


		{{Form::close() }}
	</div>
</div>
