<?php
    // dd(get_defined_vars());
?>
<div id="filter-options" class="row student-search-container" data-admin-type="{{$adminType or ''}}">
    <div class="column small-12 medium-12 large-12 search-sidebar-container">
        <div class="mobile-search-filter-nav-container isOpen">
        {{Form::open(array('url' => '', 'data-abide' => 'ajax'))}}

        <ul class="side-nav search-sidenav" data-locked="@if(isset($show_upgrade_button) && $show_upgrade_button == 1) 1 @else 0 @endif">
        
        <li data-search-tab="appStat" class="search-tab">
            <div class="change-icon hide">&#x02713;</div>
            <a href="">Application Status</a>
            <div class="search-filter-form">
               
                <div class="component appStat">
                    <div class="row">
                        <div class="column small-12 mt20 mb20">
                            @foreach($application_states as $k => $v)
                            <input name="application[]" id="app-pre-{{$k or ''}}" class="application-stat" value="{{$k or ''}}" type="radio"/>
                            <label for="app-pre-{{$k or ''}}"> 
                                {{$v or ''}}
                            </label><br/>
                            @endforeach
                        </div>
                    </div>
                </div>

            </div>
        </li>
        
            <li data-search-tab="name" class="search-tab">
                <div class="change-icon hide">&#x02713;</div>
                <a href="">Name or Email</a>
                <div class="search-filter-form">
                    <div class="error-msg hidden"><i><b>Invalid</b>: Input contains invalid characters</i></div>
                    <div class="component name">
                        <div class="row">
                            <div class="column small-12">
                                <div class="s-label">Name or Email:</div>
                                <small class="error">Invalid: Invalid Name or Email format</small>

                                <div class="left">
                                    {{Form::radio('name_ie', 'all', true, array('id' => 'name_all', 'class' => 'form-field', 'data-type' => 'radio'))}}
                                    {{Form::label('name_all', 'All')}}
                                </div>

                                <div class="left">
                                    {{ Form::radio('name_ie', 'include', false, array('id' => 'name_include', 'class' => 'form-field', 'data-type' => 'radio'))}}
                                    {{ Form::label('name_include', 'Include')}}
                                </div>
                                <div class="left">
                                    {{ Form::radio('name_ie', 'exclude', false, array('id' => 'name_exclude', 'class' => 'form-field', 'data-type' => 'radio'))}}
                                    {{ Form::label('name_exclude', 'Exclude')}}
                                </div>
                                <div class="select-container hidden">
                                    <div class="row">
                                        <div class="column small-9">
                                            {{ Form::text('name', null, array('class' => 'form-field', 'data-type' => 'text', 'pattern'=>'nameemail', 'placeholder'=>'Please input name or email', 'style' => 'margin: 0 auto;')) }}
                                        </div>
                                        {{ Form::submit('+', array('class' => 'add-name'))}}
                                    </div>

                                    <div class="tag-list clearfix">
                                        <!-- tags injected here -->   
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
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
            
            <li data-search-tab="startdate" class="search-tab">
                <div class="change-icon hide">&#x02713;</div>
                <a href="">Start Date</a>
                <div class="search-filter-form track">

                    <div class="component clearfix has-tags startyr" data-dependency="startterm">
                        <div class="s-label">Year:</div>
                        <div class="select-container">
                            <?php
                                $now = date('Y');
                                $month = date('n', strtotime('first day of this month'));
                            ?>
                            <select name="startDateTerm" id="startDateTerm_filter" class="form-field" data-type="select">
                                <option value="">Select...</option>
                                @for( $i = 0; $i < 8; $i++ )
                                    @if( $i == 0 ) 
                                        @if( (int)$month < 7) <option value="Fall {{$now}}">Fall {{$now}}</option> @endif
                                    @else
                                        <option value="Fall {{$now}}">Fall {{$now}}</option>
                                    @endif
                                    <option value="Spring {{$now}}">Spring {{$now}}</option>
                                    <?php $now++; ?>
                                @endfor
                            </select>

                            <div class="tag-list clearfix">
                                <!-- tags injected here -->
                            </div>
                        </div>
                    </div>
                    
                </div>
            </li>
            
            <li data-search-tab="financial" class="search-tab">
                <div class="change-icon hide">&#x02713;</div>
                <a href="">Financial Availability</a>
                <div class="search-filter-form">
                    <div class="component clearfix has-tags financial">
                        <div class="s-label">Financial Availability:</div>
                        <div class="select-container">
                            {{ Form::select('financial', array('' => 'Select...', '0.00' => '$0', '0 - 5,000' => '$0 - $5,000', '5,000 - 10,000' => '$5,000 - $10,000', '10,000 - 20,000' => '$10,000 - $20,000', '20,000 - 30,000' => '$20,000 - $30,000', '30,000 - 50,000' => '$30,000 - $50,000', '50,000' => '$50,000+'), '', array('id' => 'financial','class' => 'form-field', 'data-type' => 'select', 'data-comp' => 'financial'))}}
                            <div class="tag-list clearfix">
                                <!-- tags injected here -->
                            </div>
                        </div>
                    </div>
                </div>
            </li>
            
            <li data-search-tab="schooltype" class="search-tab">
                <div class="change-icon hide">&#x02713;</div>
                <a href="">School Type</a>
                <div class="search-filter-form">
                    <div class="component clearfix has-tags schooltype">
                        <div class="s-label">School Type:</div>
                        <div class="left">
                            {{Form::radio('schooltype', '2', true, array('id' => 'both_typeofschool', 'class' => 'form-field', 'data-type' => 'radio'))}}
                            {{Form::label('both_typeofschool', 'Both')}}
                        </div>
                        <div class="left">
                            {{Form::radio('schooltype', '1', false, array('id' => 'online_only_typeofschool', 'class' => 'form-field', 'data-type' => 'radio'))}}
                            {{Form::label('online_only_typeofschool', 'Online Only')}}
                        </div>
                        <div class="left">
                            {{Form::radio('schooltype', '0', false, array('id' => 'campus_only_typeofschool', 'class' => 'form-field', 'data-type' => 'radio'))}}
                            {{Form::label('campus_only_typeofschool', 'Campus Only')}}
                        </div>
                        <div class="clearfix"></div>

                        <div class="select-container hidden">
                            <div class="tag-list clearfix">
                            </div>
                        </div>

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

            <li data-search-tab="desiredDegree" class="search-tab">

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
            
            <li data-search-tab="scores" class="search-tab moreTab hide">

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

            <li data-search-tab="demographic" class="search-tab moreTab hide">
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

            <li data-search-tab="uploads" class="search-tab moreTab hide">
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

                        {{ Form::select('profileCompletion', array('0' => 'Select...', '10' => '10%', '20' => '20%', '30' => '30%', '40' => '40%', '50' => '50%', '60' => '60%', '70' => '70%', '80' => '80%', '90' => '90%', '100' => '100%'), null, array('id' => 'profile_percent', 'class' =>'form-field', 'data-type' => 'select')) }}
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
                <div class="button filt-btn update-search-btn left">Filter</div>
                <!--<div class="button filt-btn save-search-btn left">Save</div>-->
                <div class="button filt-btn clear-search-btn left">Reset Filter</div>
            </div>
        </div>
        
        {{Form::close()}}
        </div>

    </div>

</div>