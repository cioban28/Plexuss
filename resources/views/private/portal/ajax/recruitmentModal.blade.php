<?php 

    // dd($data);
?>
@if ( $modaltype == "hotModal" )
    <!-- Profile Hot Modal  -->
    <div class="pos-rel model-inner-div">
        <a class="close-reveal-modal closeX">&#215;</a>
        <div class="row">
            <div class="small-12 column">
                {{ Form::open(array( 'id' => 'hotModal', 'name' => 'interested_reason','class' =>'no-padding', 'url' => '/ajax/recruiteme/'.$schoolId, 'data-abide' => 'ajax')) }}
                {{ Form::hidden( 'type', 'hotModal')}}
                {{ Form::hidden( 'on-page', '', array('id' => 'page_identifier') ) }}

                <div class='row'>
                    <div class='small-12 large-8 column'>
                        <h2 style="">Finish profile and increase your Chances of Recruitment.</h2>
                    </div>
                    <div class='small-12 large-4 column large-text-right' style=''>*Required Fields</div>
                </div>



                <div class='row'>
                    <div class='small-12 column header'>Personal Info</div>
                </div>
                <br/>
                <div class='row'>
                    <div class='small-12 large-2 column'>
                        <label class="inline" for='fnameinput'>First Name</label>
                    </div>
                    <div class='small-12 large-6 end column'>
                        {{ Form::text('fname', $fname, array( 'id' => 'fnameinput', 'placeholder' =>'First Name','required', 'pattern' => 'alpha_space'))}}
                        <small class='error'>Please enter a valid first name</small>
                    </div>
                </div>
                <div class='row'>
                    <div class='small-12 large-2 column'>
                        <label class="inline" for="lnameinput">Last Name</label>
                    </div>
                    <div class='small-12 large-6 end column'>
                        {{ Form::text('lname', $lname, array( 'id' => 'lnameinput' ,'placeholder' =>'last Name','required', 'pattern' => 'alpha_space'))}}
                        <small class='error'>Please enter a valid last name</small>
                    </div>
                </div>
                <div class='row'>
                    <div class='small-12 large-2 column'>
                        <label class="inline" for="addressinput">Address</label>
                    </div>
                    <div class='small-12 large-6 end column'>
                        {{ Form::text('address', $address, array( 'id' => 'addressinput' ,'placeholder' =>'Address','required', 'pattern' => 'address'))}}
                        <small class='error'>Please enter a valid address</small>
                    </div>
                </div>
                <div class='row'>
                    <div class='small-12 large-2 column'>
                        <label class="inline" for="addressinput">City</label>
                    </div>
                    <div class='small-12 large-6 end column'>
                        {{ Form::text('city', $city, array( 'id' => 'cityinput' ,'placeholder' =>'City','required', 'pattern' => 'city'))}}
                        <small class='error'>Please enter a valid city</small>
                    </div>
                </div>




                <div class='row'>
                    <div class='small-12 large-2 column'>
                        <label class="inline" for="stateinput">State/Province</label>
                    </div>
                    @if( isset($is_intl_student) && (int)$is_intl_student == 1 )
                    <div class='small-12 large-2 column'>
                        {{ Form::text('state', $state, array( 'id' => 'stateinput' ,'placeholder' =>'state','pattern'=>'state', 'maxlength' => '2' ) )}}
                    </div>
                    @else
                    <div class='small-12 large-2 column'>
                        {{ Form::text('state', $state, array( 'id' => 'stateinput' ,'placeholder' =>'state','pattern'=>'state', 'required', 'maxlength' => '2' ) )}}
                        <small class='error'>Please enter a valid state/province</small>
                    </div>
                    @endif
                    <div class='small-12 large-2 column'>
                        <label class="inline" for="zipinput">Zip code</label>
                    </div>
                    <div class='small-12 large-2 end column'>
                        {{ Form::text('zip', $zip, array( 'id' => 'zipinput' ,'placeholder' =>'Zipcode','pattern'=>'zip', 'maxlength' => '10'))}}
                        <small class='error'>Please enter a valid zip</small>
                    </div>
                </div>



                <div class='row'>
                    <div class='small-12 large-2 column'>
                        <label class="inline" for="emailinput">Email</label>
                    </div>
                    <div class='small-12 large-6 end column'>
                        {{ Form::text('email', $email, array( 'id' => 'emailinput' ,'placeholder' =>'Email Address','required', 'pattern' => 'email'))}}
                        <small class='error'>Please enter a valid email</small>
                    </div>
                </div>
                <div class='row'>
                    <div class='small-12 large-2 column'>
                        <label class="inline" for="phoneinput-with-code">Phone Number</label>
                    </div>
                    <div class='small-12 large-6 end column'>
                        {{ Form::text('phone', $phone, array( 'id' => 'phoneinput-with-code' ,'placeholder' =>'(000)000-0000','required', 'pattern' => 'phoneinput'))}}
                        <small class='error'>Please enter a valid phone</small>
                    </div>
                </div>

                

                <!-- First drop down area -->
                <div class='row'>
                    <div class='small-12 large-6 end column'>
                        <!-- birthdate area -->
                        <div class='row'>
                            <div class="small-12 large-4 column">
                                <label class='inline' for="monthinput">Birth Date (optional)</label>
                            </div>
                            <div class="small-12 large-8 column">
                                <div class="row">
                                    <div class="small-4 column">
                                        {{ Form::text('birthMonth', $birthMonth, array( 'id' => 'monthinput' ,'placeholder'=>'mm','pattern'=>'month'))}}
                                        <small class='error'>Please enter a valid month</small>
                                    </div>
                                    <div class="small-4 column">
                                        {{ Form::text('birthDay', $birthDay, array('placeholder'=>'dd','pattern'=>'number'))}}
                                        <small class='error'>Please enter a valid day</small>
                                    </div>
                                    <div class="small-4 column">
                                        {{ Form::text('birthYear', $birthYear, array('placeholder'=>'yyyy','pattern'=>'number'))}}
                                        <small class='error'>Please enter a valid year</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>



                <div class='row'>
                    <div class='small-12 large-6 end column'>
                        <div class='row'>
                            <div class='small-12 large-4 column'>
                                <label class='inline' for="genderinput">Gender (optional)</label>
                            </div>
                            <div class='small-12 large-8 end column'>{{ Form::select( 'gender', array('' => 'Select...', 'm' => 'Male', 'f' => 'Female'), $selected['gender'], array('id' => 'genderinput') )}}</div>
                        </div>
                    </div>
                </div>


                <!-- Objective Area -->
                <!-- Not Ready yet -->
                <div class='row'>
                    <div class='small-12 column header'>Objective</div>
                </div>
                <br/>


                <div class='row objectiveArea collapse'>
                    <div class='row'>
                        <div class='column small-12 large-6'>
                            “I would like to get a/an
                        </div>
                    </div>
                    <div class='row'>
                        <div class='column small-12 large-6'>
                            {{ Form::select( 'degree', $degree, $selected['degree'], array('class'=>'text_inset', 'id' => 'DegreesDropDown', 'required', 'style' => 'width:100%; margin-top:10px; margin-bottom:5px;'))}}
                            <small class='error'>Please enter a degree</small>
                        </div>
                    </div>
                    <div class='row'>
                        <div class='column small-12 large-6'>
                            in 
                        </div>
                    </div>
                    <div class='row'>     
                        <div class='column small-12 large-6'>
                            {{ Form::text('major',$major,array('id' => 'objMajor', 'style' => ' margin-top:10px;', 'class' => 'objAutocomplete','placeholder' => 'Enter a major...','required'))}}
                            <small class='error'>Please enter a major</small>
                        </div>
                    </div>
                    
                    <div class='row'>
                        <div class='column small-12 large-6'>
                            My dream would be to one day work as a (n) 
                        </div>
                    </div>

                    <div class='row'>
                        <div class='column small-12 large-6'>
                            {{Form::text('profession',$profession,array('id' => 'objProfession','class' => 'objAutocomplete' , 'style' => ' margin-top:10px;' ,'placeholder' => 'Enter a profession...','required'))}}
                            <small class='error'>Please enter a profession.</small>
                        </div>
                    </div>
                </div>
                <br/>



                <!-- HighSchool  GPA -->
                <div class='row'>
                    <div class='small-12 column header'>High School GPA</div>
                </div>
                <br/>
                <div class='row'>
                    <div class='small-12 large-4 column'>
                        <label class='inline' for="hs_gpa">Unweighted  GPA (max 4.0)</label>
                    </div>
                    <div class='small-12 large-2 end column'>
                        {{ Form::text('hs_gpa', $hs_gpa , array( 'placeholder' =>'score', 'required' , 'id' => 'hs_gpa', 'class'=>'small_input_text small-text-left large-text-center','pattern'=>'gpa', 'maxlength' => '4' ))}}
                        <small class='error'>Please enter a valid score</small>
                    </div>
                </div>
                <div class='row'>
                    <div class='small-12 large-4 column'>
                        <label class='inline' for="weighted_gpa">Weighted GPA (optional)</label>
                    </div>
                    <div class='small-12 large-2 end column'>
                        {{ Form::text('weighted_gpa', $weighted_gpa, array( 'placeholder' =>'score', 'id' => 'weighted_gpa', 'class'=>'small_input_text small-text-left large-text-center','pattern'=>'max_weighted_gpa', 'maxlength' => '8' ))}}
                        <small class='error'>Please enter a valid score</small>
                    </div>
                </div>
                <div class='row'>
                    <div class='small-12 large-4  column'>
                        <label class='inline' for="max_weighted_gpa">Maximum possible weighted GPA at your school: (optional)</label>
                    </div>
                    <div class='small-12 large-2 end column'>
                        {{ Form::text('max_weighted_gpa', $max_weighted_gpa, array( 'placeholder' =>'score', 'id' => 'max_weighted_gpa', 'class'=>'small_input_text small-text-left large-text-center','pattern'=>'max_weighted_gpa', 'maxlength' => '8' ))}}
                        <small class='error'>Please enter a valid score</small>
                    </div>
                </div>


                <!-- College  GPA -->
                <div class='row'>
                    <div class='small-12 large column header'>College GPA</div>
                </div>
                <br/>
                <div class='row'>
                    <div class='small-12 large-4 column'>
                        <label class='inline' for="overall_gpa">Overall GPA (optional)</label>
                        </div>
                    <div class='small-12 large-2 end column'>
                        {{ Form::text('overall_gpa', $overall_gpa, array( 'placeholder' =>'score', 'id' => 'overall_gpa', 'class'=>'small_input_text small-text-left large-text-center','pattern'=>'gpa', 'maxlength' => '4'))}}
                        <small class='error'>Please enter a valid score</small>
                    </div> <!-- Dont know where this is -->
                </div>



                <!-- SAT/ACT  SCORES -->
                <div class='row'>
                    <div class='small-12 large column header'>SAT / ACT Scores</div>
                </div>
                <br/>
                <div class='row'>
                    <div class='small-12 column'>ACT Scores (optional)</div>
                </div>
                <br/>
                <div class='row'>
                    <div class='small-12 large-2 column'>
                        <label class='inline' for="act_composite">Composite</label>
                    </div>
                    <div class='small-12 large-2 end column'>
                        {{ Form::text('act_composite', $act_composite, array( 'placeholder' =>'score', 'id' => 'act_composite', 'class'=>'small_input_text small-text-left large-text-center','pattern'=>'act', 'maxlength' => '4'))}}
                        <small class='error'>Please enter a valid score</small>
                    </div>
                </div>

                <br/>
                <div class='row'>
                    <div class='small-12 column'>SAT Scores (optional)</div>
                </div>
                <br/>

                <div class='row'>
                    <div class='small-12 large-2 column'>
                        <label class='inline' for="psat_total">Total:</label>
                    </div>
                    <div class='small-12 large-2 end column'>
                        {{ Form::text('sat_total', $sat_total, array( 'placeholder' =>'score', 'id' => 'sat_total', 'class'=>'small_input_text small-text-left large-text-center','pattern'=>'sat_total', 'maxlength' => '4'))}}
                        <small class='error'>Please enter a valid score</small>
                    </div>
                </div>
                <br/>

                <div class='row'>
                    <div class='small-12 column' style="font-weight:normal; font-size:14px;">After clicking save, we will notify the school you chose to be recruited by add it to your Portal</div>
                </div>

                <br/>

                <div class='row'>
                    <div class="small-6 large-2 large-offset-7 column text-right close-reveal-modal"><div class='whitebutton button '>cancel</div></div>
                    <div id="save-button-recruitment-modal" class="small-6 large-2 end column text-center">{{ Form::submit("Save", array('class' => 'button orangebutton' ))}}</div>
                </div>

                {{ Form::close()}}
            </div>
        </div>
    </div>


<!--\\\\\\\\\\\\ recruit me modal ////////////// -->
@else
    
    <!-- contact info show only if contact info missing-->
    <!--////////////// Contact Info modal //////////////////-->
    <!-- if students contact info is not completed -->
    <div id="recruitme_contact_modal" class="pos-rel modal-inner-div userInfoNotify @if ( !isset($phone) || !isset($address) || !isset($city) || !isset($state) || !isset($zip))  @else hide @endif">
        {{ Form::open(array( 'id' => 'recruitMeModal', 'name' => 'contact_info', 'data-abide'=>'ajax' ,'class' =>'contact-form no-padding')) }}
        {{ Form::hidden( 'type', 'recruitMeModal')}}
        {{ Form::hidden( 'on-page', '', array('id' => 'page_identifier') ) }}

        @if(isset($aorSchool))
            {{ Form::hidden( 'aorSchool', $aorSchool)}}
        @endif

        <div class='row'>
            <div class="column small-11"></div>
            <div class="column small-1 txt-right"><a class="close-reveal-modal closeX">&#215;</a></div>
        </div>
        <div class="row">
            
            <div class="incomplete-contact-info-title column small-11 end text-left">
                You need to add your phone number and address information before you can Get Recruited! Fill in this information to continue
            </div>

            <!-- start of contact form fields -->
            <div class="column small-offset-1 small-10 end incomplete-contact-info">
                
                <!--// phone number -->
                <div class="row">
                    <div class="column small-3">
                        <label class="inline" for="phoneinput-with-code">Phone Number</label>
                    </div>
                    <div class="column small-9 end">
                        
                        {{ Form::text('phone', $phone, array( 'id' => 'phoneinput-with-code' ,'placeholder' =>'(000)000-0000','required'))}}
                        {{ Form::hidden('area_code', '', array('class' => 'area_code')) }}
                        
                        <!-- country code container on phone input -->
                        <div class="flag-code">
                            <?php 
                                $countryCode = 'us';
                                foreach($countriesAreaCode as $code){
                                    if($code['country_id'] == $country_id)
                                        $countryCode = strtolower($code['country_code']);
                                }
                            ?>

                            <!-- flag image -->
                            <div id="input_flag" class="flag flag-{{$countryCode}}"></div>
                           
                            <!-- dropdown arrow -->
                            &nbsp;&#9662; 

                            <!-- country code value (+1 for example) --> 
                            <div class="code-val">
                                @foreach( $countriesAreaCode as $code )
                                    @if( $code['country_id'] == $country_id )
                                        +{{trim($code['country_phone_code'])}}
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        <div class="twilio-err"><small>Please enter a valid phone</small></div>
                        
                        <!-- dorpdown list -->
                        <div id="phone-code-list">
                            <ul>
                                @foreach( $countriesAreaCode as $code )
                                    <li data-phone-code="{{$code['country_phone_code']}}">
                                        <div class="flag flag-{{strtolower($code['country_code'])}}"></div>
                                        <div class="country-name-code">{{$code['country_name']}} (+{{$code['country_phone_code']}})</div>
                                    </li>   
                                @endforeach
                            </ul>
                        </div>
                    </div>

                    <!--  consent checkbox -->
                    <div class="column small-3">&nbsp;</div>
                    <div class="column small-9 end" style="padding-bottom: 1.7em;">
                        {{Form::checkbox('txt_opt_in', 'acceptTextMsgFromShcool', (isset($txt_opt_in) && $txt_opt_in == -1)? false : true , array('id' => 'txt_opt_in'))}}
                        <label for="txt_opt_in" class="inline">I consent to receive text message from Plexuss and universities on the Plexuss network. <a href="/text-privacy-policy" target="_blank">Privacy Policy</a></label>
                    </div>
                </div>

                <!--// street -->
                <div class="row">
                    <div class="column small-3">
                        <label class="inline" for="addressinput">Address</label>
                    </div>
                    <div class="column small-9 end">
                        {{ Form::text('address', $address, array( 'id' => 'addressinput', 'placeholder' =>'Address', 'required', 'pattern' => 'address'))}}
                        <small class='error'>Please enter a valid address</small>
                    </div>
                </div>

                <!--// city -->
                <div class="row">
                    <div class="column small-3">
                        <label class="inline" for="addressinput">City</label>
                    </div>
                    <div class="column small-9 end">
                        {{ Form::text('city', $city, array( 'id' => 'cityinput' ,'placeholder' =>'City','required', 'pattern' => 'city'))}}
                        <small class='error'>Please enter a valid city</small>
                    </div>
                </div>

                <!-- state, zip-->
                <div class="row">
                    <div class='small-12 large-3 column'>
                        <label class="inline" for="stateinput">State/Province</label>
                    </div>
                    @if( (isset($is_intl_student) && (int)$is_intl_student == 1) || (isset($country_id) && $country_id != 1) )
                    <div class='small-12 large-4 column'>
                        {{ Form::text('state', $state, array( 'id' => 'stateinput' ,'placeholder' =>'state/province','pattern'=>'state' ) )}}
                        <small class='error'>Please enter a valid state/province</small>
                    </div>
                    @else
                    <div class='small-12 large-4 column text-center'>
                        @if(isset($zipRequired) && $zipRequired)
                        {{ Form::text('state', $state, array( 'id' => 'stateinput' ,'placeholder' =>'state','pattern'=>'state', 'required' ) )}}
                        @else
                        {{ Form::text('state', $state, array( 'id' => 'stateinput' ,'placeholder' =>'state','pattern'=>'state' ) )}}
                        @endif
                        <small class='error'>Please enter a valid state/province</small>
                    </div>
                    @endif
                    <div class='small-12 large-2 column'>
                        <label class="inline" for="zipinput">Zip code</label>
                    </div>
                    <div class='small-12 large-3 end column'>
                        @if( (isset($is_intl_student) && (int)$is_intl_student == 1) || (isset($country_id) && $country_id != 1) )
                        {{ Form::text('zip', $zip, array( 'id' => 'zipinput' ,'placeholder' =>'Zipcode','pattern'=>'zip', 'maxlength' => '10'))}}
                        @elseif(isset($zipRequired) && $zipRequired)
                        {{ Form::text('zip', $zip, array( 'id' => 'zipinput' ,'placeholder' =>'Zipcode','pattern'=>'zip', 'required','maxlength' => '10'))}}
                        <small class='error'>Please enter a valid zip</small>
                        @else
                        {{ Form::text('zip', $zip, array( 'id' => 'zipinput' ,'placeholder' =>'Zipcode','pattern'=>'zip', 'maxlength' => '10'))}}
                        @endif
                    </div>
                </div>


                <!-- submit button -->
                <div class="row">
                    <div class="column large-5 medium-6 small-10 small-centered text-center">
                        <input type="submit" id="contact_submit" class="button greenbutton" value="Next" onClick="submitRecruitmeModalContact(event, {{ $schoolId or 'error-988'}})" style="cursor:pointer;" />
                        <!--  onClick="submitRecruitmeModal({{ $schoolId or 'error-988'}});" -->
                    </div>
                </div>
            </div>
        </div>
        {{ Form::close()}}
    </div>

    <!-- hide if contact info is missing, else show -->    
    <div id="modal_step2" class=" @if ( !isset($phone) || !isset($address) || !isset($city) || !isset($state) || !isset($zip)) hide @else  @endif">
       
        <!--///// custom questions?  -- if none $custom_questions'] will not exist /////-->
        @if (!empty($custom_questions))
            <div class="pos-rel modal-inner-div userInfoNotify">
                {{ Form::open(array( 'id' => 'recruitMeModal', 'name' => 'custom_questions', 'data-abide'=>'ajax' ,'class' =>'custom-form no-padding')) }}
                {{ Form::hidden( 'type', 'recruitMeModal')}}
                {{ Form::hidden( 'on-page', '', array('id' => 'page_identifier') ) }}

                @if( isset($aorSchool) )
                    {{ Form::hidden( 'aorSchool', $aorSchool)}}
                @endif

                <!-- close button, the x -->
                <div class='row x-btn'>
                    <div class="column small-11"></div>
                    <div class="column small-1 txt-right"><a class="close-reveal-modal closeX">&#215;</a></div>
                </div>

                <!-- title -->
                <?php 
                    if(isset($school_name))
                        $school = $school_name;
                    else
                        $school =  'This University';
                ?>
                <div class="recruitTitle">
                    {{$custom_questions[0]['title'] or
                         $school.' needs a little more information before recruiting you' 

                    }}
                </div>
      

                <div class="row question-container">
                    <!-- for each custom question -->
                    @foreach($custom_questions as $question)

                        <!-- question can be predefined view, usually nested or complex -->
                        @if ($question['predefined'] === 'tofel-ielts')

                           @include('private.portal.ajax.includes.EnglishProficiency')

                        <!-- or not predefined, but be a predefined type -given by $type -->
                        @else
                            <div class="custom-question">{{$question['question'] or ' '}}</div>

                            @if($question['type'] === 'yes/no')
                                <input type="radio" name={{$question['field_name']}} id="yes-selection" value="yes" required="required"/>
                                    <label for="yes-selection" class="clearfix">
                                        <div class="radio-col"><span></span></div>
                                        <div class="radio-desc-col">
                                        Yes
                                        </div>
                                    </label>
                                <br/>
                                <input type="radio" name={{$question['field_name']}} id="no-selection" value="no" required="required"/>
                                    <label for="no-selection" class="clearfix">
                                        <div class="radio-col"><span></span></div>
                                        <div class="radio-desc-col">
                                        No
                                        </div>
                                    </label>
                                <br/>
                            @endif<!-- end if type yes/no-->

                        @endif<!-- end predefined-->

                    @endforeach 
                </div>

                <!-- submit button -->
                <div class="row">
                    <div class="column large-5 medium-6 small-10 small-centered text-center">
                        <input type="submit" id="custom_submit" class="button greenbutton" value="View my list" onClick="submitRecruitmeModalCollege(event, '.custom-form', {{ $schoolId or 'error-988'}});" />
                    </div>
                </div>

                @include('private.includes.ajax_loader')

                {{ Form::close() }}
            </div><!-- end modal container -->    


        <!--//// why interested ////-->
        @else

            <!--//////////////// Why interested modal //////////////////-->
            <!-- why is the student interested in this college  -->
            <div class="pos-rel model-inner-div regularRecruitme"> <!--@if(isset($showProfileInfo) && $showProfileInfo == 'showProfileModal') hide @endif"-->
                {{ Form::open(array( 'id' => 'recruitMeModal', 'name' => 'interested_reason', 'data-abide'=>'ajax' ,'class' =>'why-form no-padding')) }}
                {{ Form::hidden( 'type', 'recruitMeModal')}}
                {{ Form::hidden( 'on-page', '', array('id' => 'page_identifier') ) }}

                @if(isset($aorSchool))
                    {{ Form::hidden( 'aorSchool', $aorSchool)}}
                @endif

                <div class='row'>
                    <div class="column small-11"></div>
                    <div class="column small-1 txt-right"><a class="close-reveal-modal closer_sec">&#215;</a></div>
                </div>
                
                @if(!isset($gdpr_lang))
                <div class="recruitTitle column small-12 large-10 large-offset-1 text-center">
                    This school has been added to your list!
                </div>

                <div class="recruitSubTitle column small-12 text-center">
                    {{$school_name or ''}} wants to know why you’re interested
                </div>
              
                <div class="row"> 
                    <div class="column small-12  large-6 leftRecruitForm">
                        <div class="row">
                            <div class="applyTitle small-12 column">SELECT ALL THAT APPLY</div>
                        </div>
                        <div class="row">
                            <ul class="services-ul small-12 column">
                                <li>
                                     {{ Form::checkbox('reputation', 1 , null, array( 'id' => 'rmm_reputation' ))}} {{ Form::label('rmm_reputation', 'Academic Reputation') }}
                                </li>                        
                                <li>
                                     {{ Form::checkbox('location', 1 , null, array( 'id' => 'rmm_location' ))}} {{ Form::label('rmm_location', 'Location') }}
                                </li>   
                                <li>
                                    {{ Form::checkbox('tuition', 1 , null, array( 'id' => 'rmm_tuition' ))}} {{ Form::label('rmm_tuition', 'Cost of Tuition') }}
                                </li>   
                                <li>
                                    {{ Form::checkbox('program_offered', 1 , null, array( 'id' => 'rmm_program_offered' ))}} {{ Form::label('rmm_program_offered', 'Majors or Programs Offered') }}
                                </li>   
                                <li>
                                    {{ Form::checkbox('athletic', 1 , null, array( 'id' => 'rmm_athletic' ))}} {{ Form::label('rmm_athletic', 'Athletics') }}
                                </li>
                                 <li>
                                    {{ Form::checkbox('onlineCourse', 1 , null, array( 'id' => 'rmm_onlineCourse' ))}} {{ Form::label('rmm_onlineCourse', 'Online Courses') }}
                                </li>  
                                <li>
                                    {{ Form::checkbox('campus_life', 1 , null, array( 'id' => 'rmm_campus_life' ))}} {{ Form::label('rmm_campus_life', 'Campus Life') }}
                                 </li>
                                 <li>
                                    Other
                                 </li>
                                <li> 
                                    {{ Form::text('other', null, array('class'=>'otherReason')) }}  
                                </li>                                   
                            </ul>
                        </div>  
                    </div>
                    
                    <!-- This area here will change depending on the score values  -->
                    <!-- Compare scores area -->
                    <div class="small-12 medium-12 large-6  column rightRecruitCompare">
                        <div class="row">
                            <div class="compareTitle column small-12">COMPARE YOUR SCORES TO THEIRS</div>
                        </div>
                        <div class="row">
                            <div class="small-12 column compareMessage">
                                Colleges will review your request and are not required to contact you.  It is completely up to their discretion and enrollment requirements.
                            </div>
                        </div>
                        <div class="row">
                            <div class="column small-12 large-6">
                                <div class="row">
                                    <div class="avgScoreTitle column small-12 text-center">AVERAGE SCORES</div>
                                </div>
                                <div class="row">
                                    <div class="column small-6">
                                        <div class="circle avgGPA">GPA</div>
                                    </div>
                                    <div class="column small-6">
                                        <div class="circle gray">{{ $collegeScores['gpa'] }}</div>
                                    </div>
                                </div>
                                <div class="row pb5">
                                    <div class="column small-6">
                                        <div class="circle avgSAT">SAT</div>
                                    </div>
                                    <div class="column small-6">
                                        <div class="circle gray">{{ $collegeScores['sat'] }}</div>
                                    </div> 
                                </div>
                                <div class="row pb5">
                                    <div class="column small-6">
                                        <div class="circle avgACT">ACT</div>
                                    </div>
                                    <div class="column small-6">
                                        <div class="circle gray">{{ $collegeScores['act'] }}</div>
                                    </div>
                                </div>
                            </div>
                            <div class="column small-12 large-6">
                                <div class="row">
                                    <div class="scoreTitle column small-12 text-center">YOUR SCORES</div>
                                </div>
                                <div class="row">
                                    <div class="column small-6">
                                        <div class="circle yourGPA">GPA</div>
                                    </div>
                                    <div class="column small-6">
                                        <div class="circle gray">{{ $usrScores['gpa'] }}</div>
                                    </div>
                                </div>
                                <div class="row pb5">
                                    <div class="column small-6">
                                        <div class="circle yourSAT">SAT</div>
                                    </div>
                                    <div class="column small-6">
                                        <div class="circle gray">{{ $usrScores['sat'] }}</div>
                                    </div>
                                </div>
                                <div class="row pb5 ">
                                    <div class="column small-6">
                                        <div class="circle yourACT">ACT</div>
                                    </div>
                                    <div class="column small-6">
                                        <div class="circle gray">{{ $usrScores['act'] }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <!-- End Compare scores area -->
                </div>
                @endif
                
                <!-- End of in network checks. -->
                @if(isset($gdpr_lang) && !isset($gdpr_disclaimer))
                <?php
                    $has_gdpr_lang = true;
                ?>
                <div class='row text-center'>
                    <div class="columns small-1">
                        <input type="checkbox" id= "gdpr_lang" name="gdpr_lang" required="required">    
                    </div>
                    <div class="columns small-11" style="font-size: 1px;">
                        <label for='gdpr_lang' style="font-size: 11em;">{!! $gdpr_lang !!}</label>
                    </div>
                </div>
                @elseif(isset($gdpr_lang) && isset($gdpr_disclaimer))
                <?php
                    $has_gdpr_disclaimer = true;
                ?>
                <div class="row">
                    <div class="applyTitle small-12 column">How I would like <span style="color: red;">{{$school_name or ''}}</span> to contact me?</div>
                </div>
                <div class="row">
                    {!! $gdpr_top_msg !!}
                </div>
                <div class='row text-center'>
                    <div class="columns small-1">
                        <input type="checkbox" id= "gdpr_phone" name="gdpr_phone" required="required">    
                    </div>
                    <div class="columns small-1 end" style="font-size: 1px;">
                        <label for='gdpr_phone' style="font-size: 11em;padding-top: 0.5em;padding-right: 8em;">Phone</label>
                    </div>
                </div>
                <div class='row text-center'>
                    <div class="columns small-1">
                        <input type="checkbox" id= "gdpr_email" name="gdpr_email" required="required">    
                    </div>
                    <div class="columns small-1 end" style="font-size: 1px;">
                        <label for='gdpr_email' style="font-size: 11em;padding-top: 0.5em;padding-right: 8em;">Email</label>
                    </div>
                </div>
                @endif
                <div class="row">
                    <div class="column large-5 medium-6 small-10 small-centered text-center">                        
                        <input type="submit" id='add-to-list-submit-button' class="button greenbutton" style="cursor:pointer;margin-top: 20px;margin-bottom: 4px;" value="View my list" onClick="submitRecruitmeModalCollege(event, '.why-form', {{ $schoolId or 'error-988'}}, {{$has_gdpr_lang or 'undefined'}}, {{$has_gdpr_disclaimer or 'undefined'}});" />
                    </div>
                </div>

                @if(isset($gdpr_disclaimer))
                <div class="row">
                     <div class="columns small-12" style="font-size: 1px;">
                        <label for='gdpr_disclaimer' style="font-size: 11em;">{!! $gdpr_disclaimer !!}</label>
                    </div>
                </div>
                @endif

                <div class='row text-center'>
                    <input type='checkbox' id='dont-take-to-portal' name='dont-take-to-portal' />
                    <label for='dont-take-to-portal'>Don't take me to my portal after I select a school</label>
                </div>

                <!-- Here we need to check or display IF this school is in the plexuss network -->
                <div class="row">
                    @if($in_our_network == 0)
                        <div class="column small-12 notInNetwork">
                            This college is not part of our network, but we will be reaching out to them. We will let them know you are interested in their program.
                        </div>
                    @elseif (isset($aorSchool[0]) && $aorSchool[0] == 1)
                        <div class="column small-12 inNetwork">
                            This college is part of our network for its online programs. It is represented by a partner who is affiliated with the university.
                        </div>
                    @else
                        <div class="column small-12 inNetwork">
                        This college is part of our network. After you have finished your profile we will automatically let their admission office know you are interested so they can contact you.
                        </div>
                    @endif
                </div>

                @include('private.includes.ajax_loader')

                {{Form::close()}}
            </div>

        @endif<!-- end custom/why interested -->
    </div>
@endif<!-- end hotmodal -->


<script type="text/javascript">

        $('.manage-students-ajax-loader').hide();
        
        $(document).foundation({
            abide : {
                patterns : {
                    address: /^[a-zA-Z0-9\.,#\- ]+$/,
                    state: /^[a-zA-Z\.\- ]+$/,
                    city: /^[a-zA-Z\.\- ]+$/,
                    zip: /^[a-zA-Z0-9\.,\- ]+$/,
                    phoneinput : /^\s*(?:\+?(\d{1,3}))?[-. (]*(\d{3})[-. )]*(\d{3})[-. ]*(\d{4})(?: *x(\d+))?\s*$/,
                    alpha_space : /^[a-zA-Z ]*$/,
                    college_name: /^[\s]*(([a-zA-Z])+([\s]|[\-]|['])*)+[\s]*$/,
                    dashes_only: /^[0-9-]*$/,
                    number: /^[-+]?[0-9]\d*$/,
                    month : /^[-+]?[0-9]\d*$/,
                    gpa: /^(([0-3]){1}\.([0-9]){1,2}|4\.(0){1,2}|([0-4]){1})$/,
                    max_weighted_gpa: /^(([0-9])+|([0-9])+\.([0-9]){1,2})$/,
                    act: /^([1-9]|[1-2][0-9]|[3][0-6])$/,
                    sat: /^([2-7][0-9][0]|[8][0][0])$/,
                    sat_total:/^([6-9][0-9][0]|[1][0-9][0-9][0]|[2][0-3][0-9][0]|[2][4][0][0])$/,
                    toefl: /^[\s]*([0-9]{1,2}|[1][0-1][0-9]|[1][2][0])[\s]*$/,
                    ielts: /^[\s]*([0-8]{0,1}[\.][0-9]|[0-9]|9.0)[\s]*$/,
                    itep: /^[\s]*([0-5]{0,1}[\.][0-9]|[0-6]|6.0)[\s]*$/,
                    pte: /^[\s]*([1-8][0-9]|90)[\s]*$/
                   
                }
            },
            reveal :{
                close_on_background_click: !(window.location.pathname.indexOf('get_started') > -1) //prevent closing on get started page
            }

        });

        function validatePhoneWithTwilio(full_phone){
            $.ajax({
                url: '/phone/validatePhoneNumber',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: {phone: full_phone},
                type: 'POST'
            }).done(function(data){

                //if no error validating phone number, hide error msg
                //else show error message
                if( data && !data.error ){
                    $('.twilio-err').hide();
                    $('.check-info-btn').prop('disabled', false);
                    enableInput($('#contact_submit'), null,'btn-disabled');
                    
                }else{
                    $('.twilio-err').show();
                    $('.check-info-btn').prop('disabled', true);
                    disableInput($('#contact_submit'), null,'btn-disabled');
                }
            });
        };

        
        var toggleDropdown = function(){
            var dropdown = $('#phone-code-list');
            if( !dropdown.is(':visible') ) dropdown.slideDown(250);
            else dropdown.slideUp(250);
        };



        /*******************************************
        * toggles checkboxes for English Proficiency questions
        *
        *********************************************/
        var enableCheckboxes = function(toenable, cswitch){
            if(cswitch.is(':checked')){ /* if switch for checkbox group is checked */
                toenable.find('*').each(function(){
                    
                    //enable all except score text box -- which should be enabled only by checkbox
                    if(!$(this).is('.eng-score') && !$(this).is('.eng-inst')){
                        $(this).attr('disabled', false);
                        $(this).removeClass('eng-disabled');
                    }

                    //if any checkboxes already checked, enable those score text boxes
                    //again -- situation encountered when user selects another radio after filling out checkboxes
                    if($(this).is('input[type="checkbox"]')){
                        
                        var id = $(this).attr('id');         
                        if($(this).is(':checked')){
                            $('#'+id+'Score').attr('disabled',false);
                            $('#'+id+'Score').removeClass('eng-disabled');
                            $('#'+id+'Score').attr('required', 'required');
                        
                            //or it may be the English speaking institution text field
                            if($(this).is('#attended')){
                                $('.eng-inst').attr('disabled',false);
                                $('.eng-inst').removeClass('eng-disabled');
                                $('.eng-inst').attr('required', 'required');
                            }
                        }//end if checked
                    }//end if checkbox

                });//end .each()
            }else{ /* else switch is not checked */
                toenable.find('*').each(function(){
                    $(this).attr('disabled', true);
                    $(this).addClass('eng-disabled');
                });
            }
        };


        /***************************
        * check to see if English proficiency form is valid -- foundation's .valid not working for me
        * check for empty fields
        * check for checkbox group have at least one :checked
        * check if radio group has at least one checked
        * validate input
        * --score fields made required only when checkbox clicked
        ****************************/
        var validEngForm = function(form){
            var valid = true;

            //if no checkboxes checked -> invalid
            if(form.find('input[type="checkbox"]:checked').length === 0){
                return false;
            }


            //if checkbox checked -> check form fields
            form.find('*').each(function(){
 
                //if required and not disabled, and empty -> not valid
                if($(this).attr('required') && !$(this).is('disabled')){

                    //validate input too -- foundation's .valid is not working
                    // -- rolling own solution for now
                    if($(this).is('.eng-score') && $(this).val().trim() != '') {
                        
                        if($(this).attr('pattern') === 'toefl')
                            ivalid =validInput( '^[\\s]*([0-9]{1,2}|[1][0-1][0-9]|[1][2][0])[\\s]*$',$(this));
                        else if($(this).attr('pattern') === 'ielts')
                            ivalid =validInput( '^[\\s]*([0-8]{0,1}[\.][0-9]|[0-9]|9.0)[\\s]*$',$(this));
                        else if($(this).attr('pattern') === 'itep')
                            ivalid =validInput( '^[\\s]*([0-5]{0,1}[\.][0-6]|[0-9]|6.0)[\\s]*$',$(this));
                        else if($(this).attr('pattern') === 'pte')
                            ivalid =validInput( '^[\\s]*([1-8][0-9]|90)[\\s]*$',$(this));

                        //check valid input for this input and 
                        //make sure there are no check areas with empty fields, ect... so also call validForm
                        if(!ivalid)      
                            valid = false;
                    }//end eng-score check


                    //validate School names
                    if($(this).is('.eng-inst') && $(this).val().trim() != ''){
                        if(!validInput( '^[\\s]*(([a-zA-Z])+([\\s]|[\-]|[\'])*)+[\\s]*$', $(this)) ){
                            valid = false;
                        }
                    }//end school names check


                    //if empty fields-> not valid
                    if($(this).val().trim() === ''){
                        valid = false;
                    }

                }//end not disabled and required
            });
   
            return valid;
        };


        /********************************************************
        *   validates a general form WIP
        *   checks to see if all radio groups have at least one checked
        *   params:
        *       form: form to check -- or section
        *       takes an array of objects(inputs): [ {input selector,  regex} ] 
        */
        var validForm = function(form, inputs){
            var valid = true;

            //radio groups
            valid = valid && validRadios(form);


            //valid text fields
            for(var i in inputs){
                var input = $(inputs[i].selector);
                if(input.attr('required')){
                  
                    valid  = valid && validInput( inputs[i].regex , input);
                }
            }

            return valid;
        };

        /////////////////////
        // checks for valid input given a regex and an element to check
        var validInput = function(regex, el){
          
            var pat = new RegExp(regex);
            return pat.test(el.val());
        };

        /////////////////
        //checks to make sure at least one radio checked, per radio group
        var validRadios = function(form){
            
            var valid = true;
            
            $( form + " input:radio").each(function(){
                console.log('radio!');
                if($(this).attr('required')){
                    var name = $(this).attr("name");
                    if($("input:radio[name="+name+"]:checked").length === 0){
                        valid = false;
                    }
                }

            });

            return valid;
        };

        ///////////////
        //checks to make sure at least on checkbox checked, per checkbox group
         var validCheckboxes = function(form){
            
            var valid = true;
            
            $( form + " input:checkbox").each(function(){
                var name = $(this).attr("name");
                if($("input:checkbox[name="+name+"]:checked").length === 0){
                  valid = false;
                }

            });

            return valid;
        };


        /**************************************************
        * takes classes : disable and enable for IE < 9 support
        ***************************************************/
        var disableInput = function(input, enable, disable){
            
            if(enable && input.hasClass(enable) ){
                input.removeClass(enable);
            }
            if(disable && !input.hasClass(disable)){

                input.addClass(disable);
            }
            input.attr('disabled', 'disabled');
        };
        var enableInput = function(input, enable, disable){

            if(disable && input.hasClass(disable))
                input.removeClass(disable);
            if(enable && !input.hasClass(enable))
                input.addClass(enable);
            input.removeAttr('disabled');
        };

        var justInquired = function(data){
            var elem = null, checkmark = '<span class="check">&#x02713;</span>';

            _.each(data, function(school){
                elem = $('.recruit-me-pls[data-id="'+school+'"]');
                if( elem.length > 0 ) elem.parent().html(checkmark);
            });
        };


        /**********************************************
        *   Submit handler
        */
        var submitRecruitmeModalCollege = function(e, selector, collegeId, has_gdpr_lang, has_gdpr_disclaimer){
            e.preventDefault();
            var input = $(selector).serialize();
            
            var runAjax = false;

            if (has_gdpr_lang != undefined) {

                var gdpr  = $('#gdpr_lang');

                if(!gdpr.is(":checked")) {
                    alert("You must provide consent to be contacted before this school can be added to your list.");
                }else{
                    runAjax = true;
                }

            }else if (has_gdpr_disclaimer != undefined) {

                var gdpr_email  = $('#gdpr_email');
                var gdpr_phone  = $('#gdpr_phone');

                if(!gdpr_email.is(":checked") && !gdpr_email.is(":checked")) {
                    alert("You must provide consent to be contacted before this school can be added to your list.");
                }else{
                    runAjax = true;
                }

            }else{
                runAjax = true;
            }
            
            if (runAjax) {
                $('.manage-students-ajax-loader').show();
                $.ajax({
                    url: '/ajax/recruiteme/' + collegeId,
                    headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                    data: input,
                    type: 'POST'
                }).done(function(data, textStatus, xhr) {
                    $('.manage-students-ajax-loader').hide();

                    if( window.location.pathname.indexOf('get_started') > -1 ) {
                      justInquired(data.inquired_list);  
                    } else {
                        if (input.indexOf('dont-take-to-portal=on') == -1) {
                            window.location.href = '/portal/recommendationlist';
                        } else {
                            if ($('.college-engage-box-row.orange-btn').length > 0) {
                                $('.college-engage-box-row.orange-btn').replaceWith(
                                    '<div class="row recruitment-btn-pending college-engage-box-row">' +
                                        '<div class="small-3 column">' +
                                            '<img src="/images/colleges/recruit-me-white.png" alt="">' +
                                        '</div>' +
                                        '<div class="small-9 column text-left">Already on my list!</div>' +
                                    '</div>'
                                );
                            }
                        }
                    }

                    $('#recruitmeModal').foundation('reveal', 'close');
                });
            }
            
        };

        var submitRecruitmeModalContact = function(e, collegeId){

            e.preventDefault();
            $.ajax({

                url: '/ajax/recruitmeinfo', 
                type: 'POST',
                data: $('#recruitMeModal').serialize(),
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
 
            }).done(function(e){
                //hide contact modal and show modal_step2
                //do not want to touch the ajax function-- just in case -- so will change modals even if save failed?
              
                $('#modal_step2').removeClass('hide');
                $('#recruitme_contact_modal').slideUp(function(){
                    $('#modal_step2').delay(300).slideDown(500);    
                });     

            });
        };
       


        $(document).ready(function(){
            var page = window.location.pathname;
            var code = '';
            //var EngValid = false; //boolean flag for : if all English score values are valid 
                                  //(foundation's built in .valid  will not seem to work right now)

            // if current page is get started hide, close buttons and prevent modal closing
            if( window.location.pathname.indexOf('get_started') > -1 ){
                $('.closeX').hide();
            }

            if( page.indexOf('/') > -1 ) page = page.split('/')[1];

            $('#page_identifier').val(page);

            /////////////////////////
            //disable submit button -- enabled if form filled out
            disableInput($('#custom_submit'), null,'btn-disabled');
            disableInput($('#contact_submit'), null,'btn-disabled');

            ///////////////////////////
            //disable the score checkboxes
            enableCheckboxes($('.eng-checkbox-container'), $('#experience'));


            /////////////////////////////
            // on entire contact form change
            $('.contact-form').on('keyup', function(e){

                var inputs = [{selector: '#phoneinput-with-code', regex: '(.*?)'},
                              {selector: '#addressinput', regex: '^[a-zA-Z0-9\\.,#\\- ]+$'},
                              {selector: '#cityinput', regex: '^[a-zA-Z\\.\\- ]+$'},
                              {selector: '#stateinput', regex: '^[a-zA-Z\\.\\- ]+$'},
                              {selector: '#zipinput', regex: '^[a-zA-Z0-9\\.,\\- ]+$'}];  

                if(validForm('.contact-form', inputs) === true && !$('.twilio-err').is(':visible'))
                    enableInput($('#contact_submit'), null,'btn-disabled');
                else
                    disableInput($('#contact_submit'), null,'btn-disabled');

            });
            

            /////////////////////////////
            $('.custom-form').on('change', function(e){
                
                //only want to handle other parts of custom form 
                // -- not English Proficiency
                if( $('.eng-radio-container').has($(e.target)).length)
                    return;

                if(validForm('.custom-form') === true)
                    enableInput($('#custom_submit'), null,'btn-disabled');
                else
                    disableInput($('#custom_submit'), null,'btn-disabled');

            });
            


            //////////////////////////////////////////////////
            // when clicking radio
            //enable checkboxes on clicking some experience radio
            //also handles the submit button disable/enable
            $('input[name="englishKnowledge"]').on('change', function(e){
                e.stopPropagation();
                var target = $(e.target);

                //userAgents can change -- but using this to detect device right now...
                //currently, opera mini/ IE mobile do not support window.matchMedia -- using that in case userAgent not successgul
                
                if( /IEMobile|Opera Mini/i.test(navigator.userAgent) | window.matchMedia("only screen and (max-width: 480px)").matches) {
                    if($(this).is('#experience')){
                        $('.eng-checkbox-container').slideDown();
                    }
                    else{
                         $('.eng-checkbox-container').slideUp();
                    }
                }
                

                enableCheckboxes($('.eng-checkbox-container'), $('#experience'));
               
                //if not the some experience radio or a child of checkbox area
                if(!target.is('#experience') && target.is(':checked')
                    && !$('.eng-checkbox-container').has(target).length){
                    
                    enableInput($('#custom_submit'), null, 'btn-disabled');
              
                }// if a child of checkbox area or some experience radio
                 //disable submit till checkboxes filled out correctly
                else if(target.is('#experience')  || $('.eng-checkbox-container').has(target).length){
                  
                    //if coming back to it -- meaning already filled content correctly -> want enabled submit
                    if(validEngForm($('.eng-checkbox-container')) === true){
                        enableInput($('#custom_submit'), null, 'btn-disabled');
                    }
                    else{
                        disableInput($('#custom_submit'), null,'btn-disabled');
                        
                    }
                }                
            });


            ////////////////////////////////////////////
            //when checking checkbox for Eglich Proficiency tests
            $('.eng-check').on('click', function(e){
                e.stopPropagation();

                //get id of $this
                //should be textbox with id of  $this.id +'Score'
                var id = $(this).attr('id');
                
                //if 'triggering' checkbox :checked -> enable textfield
                if($(this).is(':checked')){
                    if($(this).is('.score-check')){
                        $('#'+id+'Score').attr('required', 'required');
                        enableInput($('#'+id+'Score'), null, 'eng-disabled');
                    }
                    //or it may be the English speaking institution text field
                    if($(this).is('#attended')){
                        enableInput($('.eng-inst'), null, 'eng-disabled');
                        $('.eng-inst').attr('required', 'required');
                    }
                //if not checked -> disable score textfield again
                }else{
                    if($(this).is('.score-check')){
                        disableInput($('#'+id+'Score'), null, 'eng-disabled');
                        $('#'+id+'Score').removeAttr('required');
                        $('#'+id+'Score').removeAttr('data-invalid');
                    }
                    if($(this).is('#attended')){
                        disableInput($('.eng-inst'), null, 'eng-disabled');
                        $('.eng-inst').removeAttr('required');
                        $('.eng-inst').removeAttr('data-invalid');
                    }
                }

                //check if form is still valid
                if(validEngForm($('.eng-checkbox-container')) === true ){
                    enableInput($('#custom_submit'), null, 'btn-disabled');
                }
                else{
                   disableInput($('#custom_submit'), null, 'btn-disabled');
                }    
            });

    

            //////////////////////////////////////////////
            // clears input field when clicked
            $('.eng-score').on('click', function(e){
                var value = $(this).val();
                var that = $(this);

                $(this).val('');

                $(document).one('click', function(e){
                    if( !$(e.target).is('.eng-score') && (that.val() === '' || that.val() === ' ')){
                        alert(that.val());
                        that.val(value);
                    }
                });
                

            });

             $('.eng-inst').on('click', function(e){
                var value = $(this).val();
                var that = $(this);

                $(this).val('');

                $(document).one('click', function(e){
                    if(!$(e.target).is('.eng-inst') && (that.val() === '' || that.val() === ' '))
                        that.val(value);
                });
                

            });


            //////////////////////////////////////
            // if filling score text box -- if data is valid, enable submit
            $('.eng-score').on('keyup', function(e){
            

                if(validEngForm($('.eng-checkbox-container')) === true){
                     enableInput($('#custom_submit'), null, 'btn-disabled');
                }else{
                     disableInput($('#custom_submit'), null, 'btn-disabled');
                }

            });
             //////////////////////////////////////
            // if filling institutions text box -- if data is valid, enable submit
            $('.eng-inst').on('keyup', function(e){
            
                if(validEngForm($('.eng-checkbox-container')) === true){
                     enableInput($('#custom_submit'), null, 'btn-disabled');
                }else{
                     disableInput($('#custom_submit'), null, 'btn-disabled');
                }

            });


            //////////////////////////////////
            $('#hotModal').on('valid', function() {
                submithotModal( {{ $schoolId or 'error-986'}} );
            });

            /////////////////////////////////
            $(document).on('keyup', '#phoneinput-with-code', function(){
                var val = $(this).val(),
                    code = $('.code-val').text(),
                    full_phone = code.trim()+val.trim();
                    
                    validatePhoneWithTwilio(full_phone);

            });
            
            //////////////////////////////////
            $(document).on('click', '.flag-code', function(e){
                e.stopPropagation();
                toggleDropdown(); 
            });


            ///////////////////////////////////
            $(document).on('click', '#phone-code-list li', function(e){
                code = $(this).data('phone-code');
                $('.code-val').html('+'+code);

                //very delicate, but trying to find way to change the flag based on country code here
                //cannot access php var used above that corresponds to country code 
                var flag = $(this).find('.flag');
                var classList = flag.attr('class').split('/\s+/');
                    //second class should be the one we need but just in case -- we loop through
                    for(var i in classList){
                        if(classList[i].includes('flag-'))
                            var flagclass = classList[i];
                    }
                $('#input_flag').removeClass();
                $('#input_flag').addClass(flagclass);    

                toggleDropdown();
                $('#phoneinput-with-code').trigger('keyup');
            });


            ////////////////////////////////
            $(document).on('change', '#phoneinput-with-code', function(){
                $('.area_code').val( $('.code-val').text().trim() );
            });


            /////////////////////////////////
            $(document).on('click', '.check-info-btn', function(){
                $('.area_code').val( $('.code-val').text().trim() );

                var info = {
                    address: $('#addressinput').val(),
                    city: $('#cityinput').val(),
                    state: $('#stateinput').val(),
                    phone: $('#phoneinput-with-code').val(),
                    area_code: $('.area_code').val()
                };

                if( $('#txt_opt_in').is(':checked') ) info.txt_opt_in = true; 

                if( $('input[data-invalid]').length === 0 && !$('.twilio-err').is(':visible') ){
                    $.ajax({
                        url: '/ajax/recruitmeinfo',
                        type: 'POST',
                        data: info,
                        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
                        success: function(data){
                            console.log(data);
                            openRegularRecruitmeModal($('.check-info-btn'));
                        },
                        error: function(err){
                            console.log(err);
                        }
                    });
                }
            });


            //Set autocomplete options for majors autocomplete field
            $('#objMajor').autocomplete({
                source: '/getObjectiveMajors',
                appendTo: '#hfMajorContainer',
                minLength: 3,
                select: function(event, ui){
                    $(this).data('selected', ui.item.value);
                }
            });

            // Set autocomplete options for professions autocomplete field
            $('#objProfession').autocomplete({
                source: '/getObjectiveProfessions',
                appendTo: '#hfProfessionContainer',
                minLength: 3,
                select: function(event, ui){
                    $(this).data('selected', ui.item.value);
                }
            });

            //Delete text if user does not select an autocomplete option
            $('#objMajor').change(function(){
                if($(this).val() !== $(this).data('selected')){
                    $(this).val('');
                }
            });

            $('#objProfession').change(function(){
                if($(this).val() !== $(this).data('selected')){
                    $(this).val('');
                }
            });

        });//**end document.ready

      

       
</script>








