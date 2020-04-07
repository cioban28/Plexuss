@extends('advancedStudentSearch.master')
@section('content')
<?php 
// dd($data);
?>
	@if( (isset($webinar_is_live) && $webinar_is_live == true) || (isset($is_any_school_live) && $is_any_school_live == true) )
	<div class="row student-search-container chat-now-bar-isVisible" data-admin-type="{{$adminType}}">
	@else
	<div class="row student-search-container" data-admin-type="{{$adminType}}">
	@endif
		<div class="column small-12 medium-3 large-2 search-sidebar-container">

			<div class="back-to-dash small-text-left large-text-center">
				<a href="/{{$adminType}}/dashboard"> &lt; Back to Dashboard</a>
			</div>

			<div class="mobile-search-filter-nav-toggler clearfix show-for-small-only">
				<div class="left">Filter Nav </div><div class="left arrow">&nbsp;</div>
			</div>

			{{Form::select('savedFilters', $savedFilters, null, array('class' => 'savedFilters-class', 'data-type' => 'select', 'data-comp'=>'savedFilters'))}}
			<div class="delete-filter"><a href="@">Delete Filter</a></div>

			<div class="mobile-search-filter-nav-container isOpen">
			{{Form::open(array('id' => 'advanced-search-form', 'url' => '', 'data-abide' => 'ajax'))}}
			{{ Form::hidden('save_template_name_val', '', array('id' => 'save_template_name_val')) }}

			<ul class="side-nav search-sidenav" data-locked="@if(isset($show_upgrade_button) && $show_upgrade_button == 1) 1 @else 0 @endif">
				<li data-search-tab="date">
					<div class="s-label">Choose a date:</div>
					{{ Form::text('date',"", array('id' => 'dtrange','class'=>'dash-cal','placeholder'=>"&nbsp;&nbsp;Date(s)")) }}
				</li>

				<li data-search-tab="name">
					{{ Form::hidden('name_ie', 'include') }}
					{{ Form::text('name', null, array('id' => 'studentNameSearch', 'class'=>'', 'placeholder'=>'Search name or email')) }}
				</li>

				<li data-search-tab="location" class="search-tab">
					<div class="change-icon hide">&#x02713;</div>
					<a href="">Location</a>
					<div class="search-filter-form track">

						<div class="component clearfix has-tags country" data-dependency="state">
							<div class="s-label">Country:</div>
							<div class="left">
								{{Form::radio('country_ie', 'all', true, array('id' => 'country_all', 'class' => 'form-field', 'data-type' => 'radio'))}}
								{{Form::label('country_all', 'All')}}
							</div>
							<div class="left">
								{{Form::radio('country_ie', 'include', false, array('id' => 'country_include', 'class' => 'form-field', 'data-type' => 'radio'))}}
								{{Form::label('country_include', 'Include')}}
							</div>
							<div class="left">
								{{Form::radio('country_ie', 'exclude', false, array('id' => 'country_exclude', 'class' => 'form-field', 'data-type' => 'radio'))}}
								{{Form::label('country_exclude', 'Exclude')}}
							</div>
							<div class="select-container hidden">
								{{Form::select('country', $countries, null, array('id' => 's_countries', 'class' => 'form-field', 'data-type' => 'select', 'data-comp'=>'country'))}}
								<div class="tag-list clearfix">
									<!-- tags injected here -->	
								</div>
							</div>
						</div>
						<div class="component clearfix has-tags state hidden" data-dependency="city">
							<div class="s-label">State:</div>
							<div class="left">
								{{Form::radio('state_ie', 'all', true, array('id' => 'state_all', 'class' => 'form-field', 'data-type' => 'radio'))}}
								{{Form::label('state_all', 'All')}}
							</div>
							<div class="left">
								{{Form::radio('state_ie', 'include', false, array('id' => 'state_include', 'class' => 'form-field', 'data-type' => 'radio'))}}
								{{Form::label('state_include', 'Include')}}
							</div>
							<div class="left">
								{{Form::radio('state_ie', 'exclude', false, array('id' => 'state_exclude', 'class' => 'form-field', 'data-type' => 'radio'))}}
								{{Form::label('state_exclude', 'Exclude')}}
							</div>
							<div class="select-container hidden">
								{{Form::select('state', $states, null, array('id' => 's_states', 'class' => 'form-field', 'data-type' => 'select'))}}
								<div class="tag-list clearfix">
									<!-- tags injected here -->	
								</div>	
							</div>
						</div>
						<div class="component clearfix has-tags city hidden">
							<div class="s-label">City:</div>
							<div class="left">
								{{Form::radio('city_ie', 'all', true, array('id' => 'city_all', 'class' => 'form-field', 'data-type' => 'radio'))}}
								{{Form::label('city_all', 'All')}}
							</div>
							<div class="left">
								{{Form::radio('city_ie', 'include', false, array('id' => 'city_include', 'class' => 'form-field', 'data-type' => 'radio'))}}
								{{Form::label('city_include', 'Include')}}
							</div>
							<div class="left">
								{{Form::radio('city_ie', 'exclude', false, array('id' => 'city_exclude', 'class' => 'form-field', 'data-type' => 'radio'))}}
								{{Form::label('city_exclude', 'Exclude')}}
							</div>
							<div class="select-container hidden">
								{{Form::select('city', $cities, null, array('id' => 's_cities', 'class' => 'form-field', 'data-type' => 'select'))}}
								<div class="tag-list clearfix">
									<!-- tags injected here -->	
								</div>
							</div>
						</div>
						
					</div>
				</li>

				<li data-search-tab="startDateTerm" class="search-tab">
					<div class="change-icon hide">&#x02713;</div>
					<a href="">Start Date</a>
					<div class="search-filter-form">
						<div class="component">
							<select name="startDateTerm" id="startDateTerm_filter" class="form-field" data-type="select">
	                            @foreach( $dates as $key => $date )
	    	                        <option value="{{$date}}" @if( $key == '' ) disabled="disabled" selected="selected" @endif>{{$date}}</option>
	                            @endforeach
	                        </select>	
	                        <div class="tag-list clearfix">
								<!-- tags injected here -->	
							</div>
						</div>
					</div>
				</li>

				<li data-search-tab="financial" class="search-tab">
					<div class="change-icon hide">&#x02713;</div>
					<a href="">Financial</a>
					<div class="search-filter-form">
						<div class="component">
							<select name="financial" id="financial_filter" class="form-field" data-type="select">
	                            @foreach( $financial_options as $key => $amount )
	    	                        <option value="{{$key}}" @if( $key == '' ) selected="selected" @endif>{{$amount}}</option>
	                            @endforeach
	                        </select>	
	                        <div class="tag-list clearfix">
								<!-- tags injected here -->	
							</div>
						</div>
					</div>
				</li>

				<li data-search-tab="schooltype" class="search-tab">
					<div class="change-icon hide">&#x02713;</div>
					<a href="">Type of School</a>
					<div class="search-filter-form">
						<div class="component">
							{{Form::radio('schooltype', 'both', true, array('id' => 'both_typeofschool', 'class' => 'form-field', 'data-type' => 'radio'))}}
							{{Form::label('both_typeofschool', 'Both')}}
							<br />

							{{Form::radio('schooltype', 'online_only', false, array('id' => 'online_only_typeofschool', 'class' => 'form-field', 'data-type' => 'radio'))}}
							{{Form::label('online_only_typeofschool', 'Online Only')}}
							<br />

							{{Form::radio('schooltype', 'campus_only', false, array('id' => 'campus_only_typeofschool', 'class' => 'form-field', 'data-type' => 'radio'))}}
							{{Form::label('campus_only_typeofschool', 'Campus Only')}}	
						</div>
					</div>
				</li>

				<li data-search-tab="major" class="search-tab">
					<div class="change-icon hide">&#x02713;</div>
					<a href="">Major</a>
					<div class="search-filter-form track">
							
						<div class="component clearfix has-tags department" data-dependency="major">
							<div class="s-label">Department:</div>
							<div class="left">
								{{Form::radio('department_ie', 'all', true, array('id' => 'department_all', 'class' => 'form-field', 'data-type' => 'radio'))}}
								{{Form::label('department_all', 'All')}}
							</div>
							<div class="left">
								{{Form::radio('department_ie', 'include', false, array('id' => 'department_include', 'class' => 'form-field', 'data-type' => 'radio'))}}
								{{Form::label('department_include', 'Include')}}
							</div>
							<div class="left">
								{{Form::radio('department_ie', 'exclude', false, array('id' => 'department_exclude', 'class' => 'form-field', 'data-type' => 'radio'))}}
								{{Form::label('department_exclude', 'Exclude')}}
							</div>
							<div class="select-container hidden">
								{{Form::select('department', $departments, null, array('id' => 's_depts', 'class' => 'form-field', 'data-type' => 'select'))}}
								<div class="tag-list clearfix">
									<!-- tags injected here -->	
								</div>
							</div>
						</div>
						<div class="component clearfix has-tags major hidden">
							<div class="s-label">Major:</div>
							<div class="left">
								{{Form::radio('major_ie', 'all', true, array('id' => 'major_all', 'class' => 'form-field', 'data-type' => 'radio'))}}
								{{Form::label('major_all', 'All')}}
							</div>
							<div class="left">
								{{Form::radio('major_ie', 'include', false, array('id' => 'major_include', 'class' => 'form-field', 'data-type' => 'radio'))}}
								{{Form::label('major_include', 'Include')}}
							</div>
							<div class="left">
								{{Form::radio('major_ie', 'exclude', false, array('id' => 'major_exclude', 'class' => 'form-field', 'data-type' => 'radio'))}}
								{{Form::label('major_exclude', 'Exclude')}}
							</div>
							<div class="select-container hidden">
								{{Form::select('major', $majors, null, array('id' => 's_majors', 'class' => 'form-field', 'data-type' => 'select'))}}
								<div class="tag-list clearfix">
									<!-- tags injected here -->	
								</div>
							</div>
						</div>
						
					</div>
				</li>

				<li data-search-tab="scores" class="search-tab">

					@if(isset($show_upgrade_button) && $show_upgrade_button == 1)
					<span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span>
					@endif

					<div class="change-icon hide">&#x02713;</div>

					<a href="">Scores</a>
					<div class="search-filter-form">
						
						<div class="error-msg hidden"><i><b>Invalid</b>: Min value must not be greater than or equal the Max value</i></div>
						<div class="component scores">
							<div class="row">
								<div class="column small-12 s-label">HS GPA:</div>
								<div class="column small-6">
									{{Form::number('gpa_scores[]', null, array('class' => 'form-field', 'data-type' => 'text', 'placeholder' => 'Min', 'pattern'=>'gpa', 'min' => '0.1', 'max' => '4', 'step' => '0.1'))}}
									<small class="error">Invalid: must be in between 0.1-4.0</small>
								</div>
								<div class="column small-6">
									{{Form::number('gpa_scores[]', null, array('class' => 'form-field', 'data-type' => 'text', 'placeholder' => 'Max', 'pattern'=>'gpa', 'min' => '0.1', 'max' => '4', 'step' => '0.1'))}}
									<small class="error">Invalid: must be in between 0.1-4.0</small>
								</div>
							</div>

							<div class="row">
								<div class="column small-12 s-label">HS WEIGHTED GPA:</div>
								<div class="column small-6">
									{{Form::number('hsWeightedGPA[]', null, array('class' => 'form-field', 'data-type' => 'text', 'placeholder' => 'Min', 'pattern'=>'gpa', 'min' => '0.1', 'max' => '4', 'step' => '0.1'))}}
									<small class="error">Invalid: must be in between 0.1-4.0</small>
								</div>
								<div class="column small-6">
									{{Form::number('hsWeightedGPA[]', null, array('class' => 'form-field', 'data-type' => 'text', 'placeholder' => 'Max', 'pattern'=>'gpa', 'min' => '0.1', 'max' => '4', 'step' => '0.1'))}}
									<small class="error">Invalid: must be in between 0.1-4.0</small>
								</div>
							</div>

							<div class="row">
								<div class="column small-12 s-label">COLLEGE GPA:</div>
								<div class="column small-6">
									{{Form::number('collegeGPA[]', null, array('class' => 'form-field', 'data-type' => 'text', 'placeholder' => 'Min', 'pattern'=>'gpa', 'min' => '0.1', 'max' => '4', 'step' => '0.1'))}}
									<small class="error">Invalid: must be in between 0.1-4.0</small>
								</div>
								<div class="column small-6">
									{{Form::number('collegeGPA[]', null, array('class' => 'form-field', 'data-type' => 'text', 'placeholder' => 'Max', 'pattern'=>'gpa', 'min' => '0.1', 'max' => '4', 'step' => '0.1'))}}
									<small class="error">Invalid: must be in between 0.1-4.0</small>
								</div>
							</div>


							<div class="row">
								<div class="column small-12 s-label">SAT:</div>
								<div class="column small-6">
									{{Form::number('sat_scores[]', null, array('class' => 'form-field', 'data-type' => 'text', 'placeholder' => 'Min', 'pattern'=>'sat', 'min' => '600', 'max' => '2400'))}}
									<small class="error">Invalid: must be in between 600-2400</small>
								</div>
								<div class="column small-6">
									{{Form::number('sat_scores[]', null, array('class' => 'form-field', 'data-type' => 'text', 'placeholder' => 'Max', 'pattern'=>'sat', 'min' => '600', 'max' => '2400'))}}
									<small class="error">Invalid: must be in between 600-2400</small>
								</div>
							</div>
							<div class="row">
								<div class="column small-12 s-label">ACT:</div>
								<div class="column small-6">
									{{Form::number('act_scores[]', null, array('class' => 'form-field', 'data-type' => 'text', 'placeholder' => 'Min', 'pattern'=>'act', 'min' => '0', 'max' => '36'))}}
									<small class="error">Invalid: must be in between 0-36</small>
								</div>
								<div class="column small-6">
									{{Form::number('act_scores[]', null, array('class' => 'form-field', 'data-type' => 'text', 'placeholder' => 'Max', 'pattern'=>'act', 'min' => '0', 'max' => '36'))}}
									<small class="error">Invalid: must be in between 0-36</small>
								</div>
							</div>
							<div class="row">
								<div class="column small-12 s-label">TOEFL:</div>
								<div class="column small-6">
									{{Form::number('toefl_scores[]', null, array('class' => 'form-field', 'data-type' => 'text', 'placeholder' => 'Min', 'pattern'=>'toefl', 'min' => '0', 'max' => '120'))}}
									<small class="error">Invalid: must be in between 0-120</small>
								</div>
								<div class="column small-6">
									{{Form::number('toefl_scores[]', null, array('class' => 'form-field', 'data-type' => 'text', 'placeholder' => 'Max', 'pattern'=>'toefl', 'min' => '0', 'max' => '120'))}}
									<small class="error">Invalid: must be in between 0-120</small>
								</div>
							</div>
							<div class="row">
								<div class="column small-12 s-label">IELTS:</div>
								<div class="column small-6">
									{{Form::number('ielts_scores[]', null, array('class' => 'form-field', 'data-type' => 'text', 'placeholder' => 'Min', 'pattern'=>'ielts', 'min' => '0', 'max' => '9'))}}
									<small class="error">Invalid: must be in between 0-9</small>
								</div>
								<div class="column small-6">
									{{Form::number('ielts_scores[]', null, array('class' => 'form-field', 'data-type' => 'text', 'placeholder' => 'Max', 'pattern'=>'ielts', 'min' => '0', 'max' => '9'))}}
									<small class="error">Invalid: must be in between 0-9</small>
								</div>
							</div>
						</div>
						
					</div>
				</li>

				<li data-search-tab="uploads" class="search-tab">

					@if(isset($show_upgrade_button) && $show_upgrade_button == 1)
					<span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span>
					@endif
					<div class="change-icon hide">&#x02713;</div>

					<a href="">Uploads</a>
					<div class="search-filter-form">
							
						<div class="error-msg hidden"><i><b>Invalid</b>: At least one option must be checked</i></div>
						<div class="component uploads">
							<div class="uploads-filter-select-all-btn deselect-all">
								All
							</div>
							<div>
								{{Form::checkbox('uploads[]', 'transcript', true, array('id' => 's_transcript', 'class' => 'form-field', 'data-type' => 'checkbox'))}}
								{{Form::label('s_transcript', 'Transcript')}}
							</div>
							<div>
								{{Form::checkbox('uploads[]', 'financialInfo', true, array('id' => 's_financialinfo', 'class' => 'form-field', 'data-type' => 'checkbox'))}}
								{{Form::label('s_financialinfo', 'Financial Info')}}
							</div>
								{{Form::checkbox('uploads[]', 'ielts', true, array('id' => 's_ielts', 'class' => 'form-field', 'data-type' => 'checkbox'))}}
								{{Form::label('s_ielts', 'IELTS')}}
							<div>
								{{Form::checkbox('uploads[]', 'toefl', true, array('id' => 's_toefl', 'class' => 'form-field', 'data-type' => 'checkbox'))}}
								{{Form::label('s_toefl', 'TOEFL')}}
							</div>
							<div>
								{{Form::checkbox('uploads[]', 'resume', true, array('id' => 's_resume', 'class' => 'form-field', 'data-type' => 'checkbox'))}}
								{{Form::label('s_resume', 'Resume')}}
							</div>
							<div>
	                            {{Form::checkbox('uploads[]', 'passport', true, array('id' => 's_passport', 'class' => 'form-field', 'data-type' => 'checkbox'))}}
	                            {{Form::label('s_passport', 'Passport')}}
	                        </div>
	                        <div>
	                            {{Form::checkbox('uploads[]', 'essay', true, array('id' => 's_essay', 'class' => 'form-field', 'data-type' => 'checkbox'))}}
	                            {{Form::label('s_essay', 'Essay')}}
	                        </div>
	                        <div>
	                            {{Form::checkbox('uploads[]', 'prescreen_interview', true, array('id' => 's_prescreen_interview', 'class' => 'form-field', 'data-type' => 'checkbox'))}}
	                            {{Form::label('s_prescreen_interview', 'Plexuss Interview')}}
	                        </div>
	                        <div>
	                            {{Form::checkbox('uploads[]', 'other', true, array('id' => 's_other', 'class' => 'form-field', 'data-type' => 'checkbox'))}}
	                            {{Form::label('s_other', 'Other')}}
	                        </div>
						</div>
						
					</div>
				</li>

				<li data-search-tab="demographic" class="search-tab">

					@if(isset($show_upgrade_button) && $show_upgrade_button == 1)
					<span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span>
					@endif
					<div class="change-icon hide">&#x02713;</div>

					<a href="">Demographic</a>
					<div class="search-filter-form">
						
						<div class="error-msg hidden"><i><b>Invalid</b>: Min age must not be greater than or equal the Max age</i></div>
						<div class="row component age">
							<div class="column small-12 s-label">Age:</div>
							<div class="column small-6">
								{{Form::number('age[]', null, array('class' => 'form-field', 'data-type' => 'text', 'placeholder' => 'Min', 'pattern' => 'age', 'min' => '1', 'max' => '100'))}}
								<small class="error">Invalid: must be between 1-100</small>
							</div>
							<div class="column small-6">
								{{Form::number('age[]', null, array('class' => 'form-field', 'data-type' => 'text', 'placeholder' => 'Max', 'pattern' => 'age', 'min' => '1', 'max' => '100'))}}
								<small class="error">Invalid: must be between 1-100</small>
							</div>
						</div>
						<div class="component gender">
							<div class="s-label">Gender:</div>
							{{Form::select('gender', array('all'=>'All', 'male'=>'Male', 'female'=>'Female'), 'all', array('id'=>'s_gender', 'class'=>'form-field', 'data-type'=>'select'))}}
						</div>

						<div class="component religion clearfix has-tags">
							<div class="s-label">Religion:</div>
							<div class="left">
								{{Form::radio('religion_ie', 'all', true, array('id' => 'religion_all', 'class' => 'form-field', 'data-type' => 'radio'))}}
								{{Form::label('religion_all', 'All')}}
							</div>
							<div class="left">
								{{Form::radio('religion_ie', 'include', false, array('id' => 'religion_include', 'class' => 'form-field', 'data-type' => 'radio'))}}
								{{Form::label('religion_include', 'Include')}}
							</div>
							<div class="left">
								{{Form::radio('religion_ie', 'exclude', false, array('id' => 'religion_exclude', 'class' => 'form-field', 'data-type' => 'radio'))}}
								{{Form::label('religion_exclude', 'Exclude')}}
							</div>
							<div class="select-container hidden">
								{{Form::select('religion', $religions, null, array('id' => 's_religions', 'class' => 'form-field', 'data-type' => 'select', 'data-comp'=>'religion'))}}
								<div class="tag-list clearfix">
									<!-- tags injected here -->	
								</div>
							</div>
						</div>
						<br />
						
						<div class="component clearfix has-tags ethnic">
							<div class="s-label">Ethnicity:</div>
							<div class="left">
								{{Form::radio('ethnic_ie', 'all', true, array('id' => 'ethnic_all', 'class' => '', 'data-type' => 'radio'))}}
								{{Form::label('ethnic_all', 'All')}}
							</div>
							<div class="left">
								{{Form::radio('ethnic_ie', 'include', false, array('id' => 'ethnic_include', 'class' => '', 'data-type' => 'radio'))}}
								{{Form::label('ethnic_include', 'Include')}}
							</div>
							<div class="left">
								{{Form::radio('ethnic_ie', 'exclude', false, array('id' => 'ethnic_exclude', 'class' => '', 'data-type' => 'radio'))}}
								{{Form::label('ethnic_exclude', 'Exclude')}}
							</div>
							<div class="select-container hidden">
								{{Form::select('ethnic', $ethnicities, null, array('id'=>'s_ethnic', 'class'=>'form-field', 'data-type'=>'select'))}}
								<div class="tag-list clearfix">
									<!-- tags injected here -->	
								</div>
							</div>
						</div>

					</div>
				</li>

				<li data-search-tab="educationLevel" class="search-tab moreTab hide">

					@if(isset($show_upgrade_button) && $show_upgrade_button == 1)
					<span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span>
					@endif
					<div class="change-icon hide">&#x02713;</div>

					<a href="">Education Level</a>
					<div class="search-filter-form">
						
						<div class="error-msg hidden"><i><b>Invalid</b>: At least one option must be checked</i></div>
						<div class="component clearfix educationLevel">
							<div class="left">
								{{Form::checkbox('education[]', 'highschool', true, array('id' => 'education_hs', 'class' => '', 'data-type' => 'checkbox'))}}
								{{Form::label('education_hs', 'High School')}}
							</div>
							<div class="left">
								{{Form::checkbox('education[]', 'college', true, array('id' => 'education_college', 'class' => '', 'data-type' => 'checkbox'))}}
								{{Form::label('education_college', 'College')}}
							</div>
						</div>

					</div>
				</li>

				<li data-search-tab="desiredDegree" class="search-tab moreTab hide">

					@if(isset($show_upgrade_button) && $show_upgrade_button == 1)
					<span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span>
					@endif
					<div class="change-icon hide">&#x02713;</div>

					<a href="">Desired Degree</a>
					<div class="search-filter-form">
						
						<div class="error-msg hidden"><i><b>Invalid</b>: At least one option must be checked</i></div>
						<div class="component clearfix desiredDegree">
							<div class="left">
								{{Form::checkbox('degree[]', 'Certificate Programs', true, array('id' => 'degree_certificate', 'class' => '', 'data-type' => 'checkbox'))}}
								{{Form::label('degree_certificate', 'Certificate Programs')}}
							</div>
							<div class="left">
								{{Form::checkbox('degree[]', "Associate's Degree", true, array('id' => 'degree_associate', 'class' => '', 'data-type' => 'checkbox'))}}
								{{Form::label('degree_associate', 'Associate\'s Degree')}}
							</div>
							<div class="left">
								{{Form::checkbox('degree[]', "Bachelor's Degree", true, array('id' => 'degree_bachelors', 'class' => '', 'data-type' => 'checkbox'))}}
								{{Form::label('degree_bachelors', 'Bachelor\'s Degree')}}
							</div>
							<div class="left">
								{{Form::checkbox('degree[]', "Master's Degree", true, array('id' => 'degree_masters', 'class' => '', 'data-type' => 'checkbox'))}}
								{{Form::label('degree_masters', 'Master\'s Degree')}}
							</div>
							<div class="left">
								{{Form::checkbox('degree[]', "PHD / Doctorate", true, array('id' => 'degree_phd', 'class' => '', 'data-type' => 'checkbox'))}}
								{{Form::label('degree_phd', 'PHD / Doctorate')}}
							</div>
							<div class="left">
								{{Form::checkbox('degree[]', "Undecided", true, array('id' => 'degree_undecided', 'class' => '', 'data-type' => 'checkbox'))}}
								{{Form::label('degree_undecided', 'Undecided')}}
							</div>
							<div class="left">
								{{Form::checkbox('degree[]', 'Diploma', true, array('id' => 'degree_diploma', 'class' => '', 'data-type' => 'checkbox'))}}
								{{Form::label('degree_diploma', 'Diploma')}}
							</div>
							<div class="left">
								{{Form::checkbox('degree[]', 'other', true, array('id' => 'degree_other', 'class' => '', 'data-type' => 'checkbox'))}}
								{{Form::label('degree_other', 'Other')}}
							</div>
							<div class="left">
								{{Form::checkbox('degree[]', 'Juris Doctor', true, array('id' => 'degree_juris', 'class' => '', 'data-type' => 'checkbox'))}}
								{{Form::label('degree_juris', 'Juris Doctor')}}
							</div>
						</div>

					</div>
				</li>

				<li data-search-tab="militaryAffiliation" class="search-tab moreTab hide">

					@if(isset($show_upgrade_button) && $show_upgrade_button == 1)
					<span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span>
					@endif
					<div class="change-icon hide">&#x02713;</div>

					<a href="">Military Affiliation</a>
					<div class="search-filter-form">
						
						<div class="error-msg hidden"><i><b>Invalid</b>: At least one option must be checked</i></div>
						<div class="component inMilitary">
							<div class="s-label">In Military?</div>
							{{Form::select('inMilitary', array(''=>'Select...','0'=>'No', '1'=>'Yes'), 'all', array('id'=>'s_inMilitary', 'class'=>'form-field', 'data-type'=>'select'))}}
						</div>
						<div class="component militaryAffiliation hidden">
							<div class="s-label">Military Affiliation</div>
							{{Form::select('militaryAffiliation', $military_affiliation_arr, 'all', array('id'=>'s_militaryAffiliation', 'class'=>'form-field', 'data-type'=>'select'))}}
						</div>

					</div>
				</li>

				<li data-search-tab="profileCompletion" class="search-tab moreTab hide">

					@if(isset($show_upgrade_button) && $show_upgrade_button == 1)
					<span class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access these filters."></span>
					@endif
					<div class="change-icon hide">&#x02713;</div>

					<a href="">Profile Completion</a>
					<div class="search-filter-form">
						<div class="error-msg hidden"><i><b>Invalid</b>: At least one option must be checked</i></div>

						<div class="component clearfix profileLevel">
							<div class="s-label">Profile Completion</div>

							{{ Form::select('profileCompletion', array('' => 'Select...', '10' => '10%', '20' => '20%', '30' => '30%', '40' => '40%', '50' => '50%', '60' => '60%', '70' => '70%', '80' => '80%', '90' => '90%', '100' => '100%'), null, array('id' => 'profile_percent', 'class' =>'form-field', 'data-type' => 'select')) }}
						</div>
					</div>
				</li>

			</ul>
			<div>
				<div class="clearfix">
					<div class="more-option right">+ More Filter Options</div>
				</div>
			</div>
			<div>
				<div class="clearfix">
					<div class="cleared-msg">Filter Cleared!</div>
					<div type='button' class="button filt-btn update-search-filter-btn left">Filter</div>
					<div type='button' class="button filt-btn save-search-btn left">Save</div>
				</div>
				<div type='button' class="button filt-btn clear-search-filter-btn clearfix">Reset Filter</div>
			</div>
			
			{{Form::close()}}
			</div>
			
		</div>

		<div class="column small-12 medium-9 large-10 search-results-container">
			
			<!-- Required div for Sound Manager flash component -->
			<div id="sm2-container hidden"></div>

			<div class="row directions">
				<div class="column small-12 medium-6 large-8">
					<h4>Viewing <span class="total_viewing_count">{{$total_viewing_count or 0}}</span> out of <span class="total_results_count" data-totalresultscount ="{{$total_results_count or 0}}">{{$total_results_count or 0}}</span> results</h4>
				</div>
				<div class="column small-12 medium-6 large-4">
					<p>Students that you select to recruit from this list will be added to your pending folder. When a student says yes to your request, they will be available in your handshakes folder.</p>
				</div>
			</div>

			@if(isset($show_upgrade_button) && $show_upgrade_button == 1)
				<div class="upgrade-to-premier-msg">
					You've hit your limit of 100 students per month, please <span>Upgrade to Premier</span> for unlimited search results.
				</div>
			@endif

			<div class="filter-crumbs-container">
				<ul class="inline-list filter-crumb-list">
					<!-- crumb tags get injected here -->
				</ul>
			</div>

			<div class="row results-per-page-container">
				<div class="column small-12 large-3 end right">
					<label>
						Results per page:  
						{{Form::select('display_option', array('15' => 15 , '30' => 30, '50' => 50, '100' => 100, '200' => 200), $display_option, ['id' => 'displayOption'])}}
					</label>
				</div>
			</div>

			<div class="row results-header">
				<!--<div class="column small-1 select-col">
					{{Form::checkbox('select-all', 'select-all', false, array('class'=>'select-all-students-chbox'))}}
				</div>-->
				<div class="column small-1 arrow-col">Detail</div>
				<div class="column small-6 medium-3 large-2 name">Name</div>
				<div class="column small-4 medium-1 gpa">GPA</div>
				<div class="column medium-1 sat show-for-large-up">SAT</div>
				<div class="column medium-1 act show-for-large-up">ACT</div>
				<div class="column medium-4 large-3 degree show-for-medium-up">Interested in</div>
				<div class="column medium-1 country hide-for-small-only">Country</div>
				<div class="column medium-1 docs end show-for-large-up">Uploads</div>
			</div>

			<div class="list-of-results-container inquirieWrapper each-inquirie-container" data-page-type="{{$currentPage}}" data-has-results-onload="{{$has_searchResults}}">
				@include('advancedStudentSearch.ajax.searchResults')
			</div>

            @include('admin.contactPane.transferCallModal')

            @include('admin.contactPane.postingStudentModal')

			<!-- ajax loader -->
            <div class="text-center ss-ajax-loader">
                <svg width="70" height="20">
                    <rect width="20" height="20" x="0" y="0" rx="3" ry="3">
                        <animate attributeName="width" values="0;20;20;20;0" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="height" values="0;20;20;20;0" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="x" values="10;0;0;0;10" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="y" values="10;0;0;0;10" dur="1000ms" repeatCount="indefinite"/>
                    </rect>
                    <rect width="20" height="20" x="25" y="0" rx="3" ry="3">
                        <animate attributeName="width" values="0;20;20;20;0" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="height" values="0;20;20;20;0" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="x" values="35;25;25;25;35" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="y" values="10;0;0;0;10" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
                    </rect>
                    <rect width="20" height="20" x="50" y="0" rx="3" ry="3">
                        <animate attributeName="width" values="0;20;20;20;0" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="height" values="0;20;20;20;0" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="x" values="60;50;50;50;60" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="y" values="10;0;0;0;10" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
                    </rect>
                </svg>
            </div>
            <!-- ajax include -->
			@include('private.includes.ajax_loader')
			<!-- ajax include -->
            <!-- end of ajax loader -->

            <div class="row load-more-container text-center">
				<div class="column small-4 hidden-on-mobile">
					<h5>Viewing <span class="total_viewing_count">{{$total_viewing_count or 0}}</span> out of <span class="total_results_count" data-totalresultscount ="{{$total_results_count or 0}}">{{$total_results_count or 0}}</span> results</h5>
				</div>
				<div class="column small-8">
				  <div class="row">
				    <div class="load-more-wrapper column small-3">
				      <a class="load-more-results-btn tiny">show more results</a>
				    </div>
				    <div class="load-more-wrapper hidden">
				    	<div class="no-results">No more results</div>
				    </div>
				  </div>
				</div>
			</div>

		</div>
	</div>

	<!-- upgrade to recruit student modal -->
	<div id="upgradeToRecruit_modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">

		<div class="row">
			<div class="column small-12 text-right">
				<a class="close-reveal-modal help_helpful_videos_close_icon">&#215;</a>
			</div>
		</div>

		<div class="row">
			<div class="column small-12 upgrade-text text-center">
				Upgrade your account to start recruiting students	
			</div>
		</div>

		<div class="row upgrade-or-no-row">
			<div class="column small-12 medium-6 large-5 large-offset-1">
				<div class="upgrade text-center">
					I'd like to upgrade my account
				</div>
			</div>
			<div class="column small-12 medium-6 large-5 end">
				<div class="think-about-it text-center">
					I'll think about it
				</div>
			</div>
		</div>

	</div>

	<!-- transcript preview modal -->
	<div id="transcript-preview-modal" class="reveal-modal" data-reveal>
		<div class="row">
			<div class="column small-12 small-text-right">
				<a class="close-reveal-modal help_helpful_videos_close_icon">&#215;</a>
			</div>
		</div>
		<div class="row">
			<div class="column small-12 small-text-center transcript_preview_img">
			</div>
		</div>
	</div>

	<!-- sign up to be able to recruit more modal -->
	<div id="recruit_more_modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
		<div class="row">
			<div class="column small-12 small-text-right">
				<a class="close-reveal-modal help_helpful_videos_close_icon">&#215;</a>
			</div>
		</div>
		<div class="row">
			<div class="column small-12 small-text-center mailto-wrapper">
				<div class="msg">
					Would you like to recruit more users?
				</div>
				<div class="contact">
					Contact <a href="mailto:collegeservices@plexuss.com">collegeservices@plexuss.com</a>
				</div>
			</div>
		</div>
		<div class="row upgrade-or-naw-btn-row">
			<div class="column small-12 large-6 large-centered medium-centered">
				<a href="" data-reveal-id="thankyou-for-upgrading-modal" onClick="Plex.studentSearch.requestToBecomeMember();" class="radius button">I'm interested</a>
			</div>
		</div>
	</div>

	<!-- thank you for upgrading your account modal -->
	<div id="thankyou-for-upgrading-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
		<div class="row close-modal-x">
			<div class="column small-12 text-right">
				<a class="close-reveal-modal help_helpful_videos_close_icon" aria-label="Close">&#215;</a>
			</div>
		</div>
		<div class="row">
			<div class="column small-12 text-center thankyou-msg-col">
				<div>Thank you!</div>
				<div>Someone will contact you very soon to get you set up with your new account.</div>
			</div>
		</div>
		<div class="row">
			<div class="column medium-8 large-6 medium-centered text-center">
				<a href="" class="radius button secondary close-reveal-modal" aria-label="Close">Looking forward to it ;)</a>
			</div>
		</div>
	</div>

	<!-- save filter template modal -->
	<div id="save-filter-template-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
		<div class="clearfix">
			<div class="right">
				<a class="close-reveal-modal help_helpful_videos_close_icon" aria-label="Close">&#215;</a>
			</div>
		</div>

		<br />

		<div>
			<label for="save_template_name">Filter name: </label>
			<input id="save_template_name" type="text" name="name" placeholder="Enter the filter name" required="required">
		</div>
		
		<br />

		<div>
			<div class="save-filter-template-btn text-center">Save</div>
		</div>
	</div>
@stop