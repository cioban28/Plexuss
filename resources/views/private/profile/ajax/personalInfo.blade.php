<script src="../dropzone/dropzone.js?7"></script>
<script type="text/javascript" src="../js/jquery.form.min.js?7"></script>
<div class='viewmode' style='display:block;'>
<?php 
 // echo "<pre>";
 // print_r($data);
 // echo "</pre>";
 // exit();
?>	
<div class="row">
<div class="large-9 columns paddingleft0">
	<span><div class="personalicon"></div></span>
    <span class="page_head_black edit-img">Personal Info +25% to your profile status</span>
</div>
<div class="large-3 columns paddingleft0">	
    <span class="edit-normal"><a class="add-edit-link"  onclick='personalInfoEdit()'><strong>edit</strong><span class="edit-icon"><img src="../images/edit_icon.png" alt=""/></span></a></span>
    
</div>
</div>
    
	<hr/>
	<div class="mob-row-title">Visible to public</div>
    <br />
	<div class="row">
    	<div class="large-3 small-12 columns show-for-large-up">
        	<img id="profile_picture" src="{{ $user['profile_img_loc']}}" onclick="changeProfilePic(this)" title="Click to Edit Photo" alt="{{ $user['fname']}} {{ $user['lname']}}" />
        </div>
        <div class="large-9 small-12 columns padding0">
        	<div class="row padder0">
                <div class="large-5 small-6 columns the-profile-form bold-font">Name</div>
                <div class="large-6 small-6 columns the-profile-form @if(!isset($user['fname'])) missing @endif">{{ $user['fname']}} {{ $user['lname']}}</div>
            </div>
        	<div class="row padder0">
                <div class="large-5 small-6 columns the-profile-form bold-font">Country</div>
                <div class="large-6 small-6 columns the-profile-form @if(!isset($user['country'])) missing @endif">{{ $user['country'] or 'Missing Info' }}</div>
            </div>
            <div class="row padder0">
                <div class="large-5 small-5 columns the-profile-form bold-font">City &amp; State</div>
                <div class="large-6 small-6 columns the-profile-form @if($user['city']=="" && $user['state']=="") missing @endif">                             
                    @if($user['city']=="" && $user['state']=="")
                   		Missing Info
                    @else
                        @if($user['city']!="")
                            {{$user['city']}},&nbsp;
                        @endif
                            {{$user['state']}}
                    @endif
                </div>
            </div>
            <div class="row padder0">
                <div class="large-5 small-5 columns the-profile-form bold-font">Education Level</div>
				<div class="large-6 small-6 columns the-profile-form @if(!isset($user['edu_level_text'])) missing @endif">
					{{{ $user['edu_level_text'] or 'Missing Info' }}}
				</div>
            </div>
            <div class="row padder0">
                <div class="large-5 small-5 columns the-profile-form bold-font">School Name</div>
                <div class="large-6 small-6 columns the-profile-form @if(!isset($user['currentSchoolVal'])) missing @endif">{{{ $user['currentSchoolVal'] or 'Missing Info' }}}</div>
            </div>
            <div class="row padder0">
                <div class="large-5 small-5 columns the-profile-form bold-font">Graduation Year</div>
                <div class="large-6 small-6 columns the-profile-form @if(!isset($user['grad_Year'])) missing @endif">{{{ $user['grad_Year'] or 'Missing Info' }}}</div>
            </div>
            <div class="row padder0">
                <div class="large-5 small-5 columns the-profile-form bold-font">When do you plan to start college?</div>
                @if( isset($user['planned_start_term']) && !empty($user['planned_start_term']) )
                <div class="large-6 small-6 columns the-profile-form capitalize">{{{ $user['planned_start_term'].', '.$user['planned_start_yr'] }}}</div>
                @else
                <div class="large-6 small-6 columns the-profile-form missing">{{{ 'Missing Info' }}}</div>
                @endif
            </div>
        </div>
	</div>
    
    <hr class="mob-hr-styled" />
	<div class="mob-row-title">Visible only to Plexuss</div><br/>
	<div class="row padder0">
		<div class="large-3 small-6 columns the-profile-form bold-font">Email</div>
		<div class="large-9 small-6 columns the-profile-form @if(!isset($user['email'])) missing @endif">{{$user['email']}}</div>
	</div>
    <div class="row padder0">
        <div class="large-3 small-6 columns the-profile-form bold-font">Skype</div>
        <div class="large-9 small-6 columns the-profile-form @if(!isset($user['skype'])) missing @endif">
            <a href="skype:{{$user['skype']}}?call">
                <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/skype_icon.png" alt=""/>
            </a>
            {{$user['skype'] or 'Missing Info'}}
        </div>
    </div>
	<div class="row padder0">
		<div class="large-3 small-6 columns the-profile-form bold-font">Phone Number</div>
		<div class="large-9 small-6 columns the-profile-form @if(!isset($user['phone'])) missing @endif">{{{ $user['phone'] or 'Missing Info' }}}</div>
	</div>
	<div class="row padder0">
		<div class="large-3 small-6 columns the-profile-form bold-font">Address</div>
		<div class="large-9 small-6 columns the-profile-form @if(!isset($user['address'])) missing @endif">{{{ $user['address'] or 'Missing Info' }}}</div>
	</div>
	<br/>

	<hr class="mob-hr-styled" />
	<div class="mob-row-title">Visible to Recruiters and Scholarships</div><br/>

	<div class="row padder0">
    	<div class="large-3 small-12 medium-6 columns the-profile-form bold-font">Birth Date</div>
        <div class="large-3 small-12 medium-6 columns the-profile-form @if($user['birth_date']=="" || $user['birth_date']=='0000-00-00') missing @endif">
        @if($user['birth_date']!="" && $user['birth_date']!="0000-00-00")
    	    {{$user['birthdayM']}}-{{$user['birthdayD']}}-{{$user['birthdayY']}}
        @else
	        Missing Info
        @endif
        </div>
        <div class="large-3 small-12 medium-6 columns the-profile-form bold-font">Religion</div>
        <div class="large-3 small-12 medium-6 columns the-profile-form @if(!isset($user['religionVal'])) missing @endif">{{{ $user['religionVal'] or 'Missing Info' }}}</div>
	</div>
    
    <div class="row padder0">
    	<div class="large-3 small-12 medium-6 columns the-profile-form bold-font">Gender</div>
        <div class="large-3 small-12 medium-6 columns the-profile-form @if(trim($user['gender'])=="") missing @endif">
        @if(trim($user['gender'])=="")
        Missing Info
        @else
        {{$user['gender']}}
        @endif
        </div>
        <div class="large-3 small-12 medium-6 columns the-profile-form bold-font">Marital Status</div>
        <div class="large-3 small-12 medium-6 columns the-profile-form @if(!isset($user['maritalStatus'])) missing @endif">
            {{{ $user['maritalStatus'] or 'Missing Info' }}}
        </div>
	</div>
    
    <div class="row padder0">
    	<div class="large-3 small-12 medium-6 columns the-profile-form bold-font">Ethnicity</div>
        <div class="large-3 small-12 medium-6 columns the-profile-form @if(!isset($user['ethnicityVal'])) missing @endif">
           {{{ $user['ethnicityVal'] or 'Missing Info' }}}
        </div>
        <div class="large-3 small-12 medium-6 columns the-profile-form bold-font">Do you have children?</div>
        <div class="large-3 small-12 medium-6 columns the-profile-form @if(!isset($user['children'])) missing @endif">
             {{{ $user['children'] or 'Missing Info' }}}
         </div>
	</div>

    <div class="row padder0">
        <div class="large-3 small-12 medium-6 columns the-profile-form bold-font">In Military?</div>
        <div class="large-3 end small-12 medium-6 columns the-profile-form @if(!isset($user['military_affiliation_name'])) missing @endif">
            {{{ $user['military_affiliation_name'] or 'Missing Info' }}}
        </div>
    </div>

    <div class="row padder0 text-center show-for-small-only">
        <div class='column'>
            <button class="mob-btn-save-profile" onclick="personalInfoEdit(this);">Edit <span class="edit-icon"><img src="../images/edit_icon.png"/></span></button>
        </div>
    </div>
<!--
    <div class="row padder10 show-for-large-up">
    	<div class='panel'>Public profile link (how other Plexuss users will see you):<br/> <br /><span class="plex-pr-link">www.plexuss.com/profile/coming-soon/</span></div>
    </div>	
-->
</div>


<!-- Edit Form in Modal for Editing Personal Information -->
<div class='reveal-modal large remove_before_ajax' id="personalInfoEdit" data-reveal>
{{ Form::open(array('url' => "ajax/profile/personalInfo/" , 'method' => 'POST', 'id' => 'personalInfoForm','name'=>'personalInfoForm','data-abide'=>'ajax')) }}
{{ csrf_field() }}
{{ Form::hidden('SchoolId', $user['currentSchool'], array('id'=>'SchoolId')) }}

<div class="row">
<div class="large-4 columns paddingleft0">
	<span><div class="personalicon"></div></span>
    <span class="page_head_black" style="padding-left:5px;vertical-align:middle;padding-top: 3px;">Personal Info</span>
</div>
<div class="large-8 columns paddingleft0">&nbsp;</div>
</div>
	<!-- Missing Form -->
    <br />
	<div class="show-for-large-only">Information marked with an <img src="../images/profile/eyeicon.png" alt=""/> are visible to the public</div>
    <br />
    <br />    
	
    <div class="row">
		<div class="large-3 small-12 columns personalInfoform bold-font"> {{ Form::label('infoFName', 'First Name' )}} </div>
		<div class="large-7 small-12 medium-10 columns">
			{{ Form::text('infoFName',$user['fname'], array('required', 'pattern' => 'name'))}}
			<small class="error">Please enter your first name</small>
		</div>
        <div class="large-1 small-2 show-for-medium-only columns top-8marginer"><img src="../images/profile/eyeicon.png" alt=""/></div>
        <div class="large-2 show-for-large-only columns show-for-large-only"></div>
	</div>
    
	<div class="row">
		<div class="large-3 small-12 columns personalInfoform bold-font"> {{ Form::label('infoLName', 'Last Name' )}} </div>
		<div class="large-7 small-12 medium-10 columns">
			{{ Form::text('infoLName',$user['lname'], array('required', 'pattern' => 'name'))}}
			<small class="error">Please enter your last name</small>
		</div>
        <div class="large-1 small-2 show-for-medium-only columns top-8marginer"><img src="../images/profile/eyeicon.png" alt=""/></div>
        <div class="large-2 show-for-large-only columns show-for-large-only"></div>
	</div>

	<!-- COUNTRY -->
	<div class="row">
		<div class="large-3 small-12 columns personalInfoform bold-font"> {{ Form::label('infoCountry', 'Country' )}} </div>
		<div class="large-7 small-12 medium-10 columns">
			{{ Form::select('infoCountry', $countries, $user['country_id'], array('required', 'pattern' => 'integer'))}}
			<small class="error">Please select your country of residence</small>
		</div>
        <div class="large-1 small-2 show-for-medium-only columns top-8marginer"><img src="../images/profile/eyeicon.png" alt=""/></div>
        <div class="large-2 show-for-large-only columns show-for-large-only"></div>
	</div>
	<div class="row">
		<div class="large-3 small-12 columns personalInfoform bold-font">
            {{ Form::label('infoAddress', 'Address' )}} 
        </div>
		<div class="large-7 small-12 medium-10 columns">
            {{ Form::text('infoAddress',$user['address'],array('required', 'pattern' => 'address'))}}
            <small class="error">Please enter your address</small>
        </div>
        <div class="large-1 small-2 show-for-medium-only columns top-8marginer">
            <img src="../images/profile/question-icon.png" />
        </div>
        <div class="large-2 show-for-large-only columns show-for-large-only"></div>
	</div>
    
    <div class="row">
		<div class="large-3 small-12 columns personalInfoform bold-font"> 
            {{ Form::label('infoCity', 'City' )}} 
        </div>
		<div class="large-7 small-12 medium-10 columns">
            {{ Form::text('infoCity',$user['city'],array('id'=>'infoCity','pattern'=>'city', 'required'))}}
			<small class="error">Please enter a valid city name</small>
        </div>
        <div class="large-1 small-2 show-for-medium-only columns top-8marginer">
            <img src="../images/profile/lockicon.png" alt=""/>
        </div>
        <div class="large-2 show-for-large-only columns show-for-large-only"></div>
	</div>
    
	<div class="row">
		<div class="large-3 small-12 columns personalInfoform bold-font"> 
            {{ Form::label('infoState', 'State' )}} 
        </div>
		<div class="large-2 small-12 medium-10 columns">
            {{ Form::text('infoState',$user['state'],array('pattern'=>'alpha', 'required', 'maxlength' => '2'))}}
			<small class="error">Please enter a valid state abbreviation</small>
        </div>
        <div class="large-1 small-2 show-for-medium-only columns top-8marginer">
            <img src="../images/profile/eyeicon.png" alt=""/>
        </div>
        <div class="large-1 small-12 columns personalInfoform bold-font"> 
            {{ Form::label('infoZip', 'Zip' )}} 
        </div>
		<div class="large-2 small-12 medium-10 columns">
            {{ Form::text('infoZip',$user['zip'],array('pattern'=>'zip', 'required', 'maxlength' => '10'))}}
			<small class="error">Please enter a valid Zip Code</small>
        </div>
        <div class="large-1 small-2 show-for-medium-only columns top-8marginer">
            <img src="../images/profile/lockicon.png" alt=""/>
        </div>
        <div class="large-2 show-for-large-only columns"></div>
	</div>
    
	<div class="row">
		<div class="large-3 small-12 columns personalInfoform bold-font"> 
            {{ Form::label('infoEmail', 'Email' )}}
        </div>
		<div class="large-7 small-12 medium-10 columns">
            {{ Form::text('infoEmail',$user['email'], array('required', 'pattern' => 'email'))}}
			<small class="error">Please enter a valid email address</small>
        </div>
        <div class="large-1 small-2 show-for-medium-only columns top-8marginer">
            <img src="../images/profile/lockicon.png" alt=""/>
        </div>
        <div class="large-1 show-for-large-only columns"></div>
	</div>

    <div class="row">
        <div class="large-3 small-12 columns personalInfoform bold-font"> 
            {{ Form::label('infoSkype', 'Skype' )}}
        </div>
        <div class="large-7 small-12 medium-10 columns">
            {{ Form::text('infoSkype', $user['skype'], array('pattern' => 'skype'))}}
            <small class="error">Please enter a valid skype id</small>
        </div>
        <div class="large-1 small-2 show-for-medium-only columns top-8marginer">
            <img src="../images/profile/lockicon.png" alt=""/>
        </div>
        <div class="large-1 show-for-large-only columns"></div>
    </div>
	
    <div class="row">
		<div class="large-3 small-12 columns personalInfoform bold-font"> 
            {{ Form::label('infoPhoneNumber', 'Phone Number' )}} 
        </div>
		<div class="large-7 small-12 medium-10 columns">
            {{ Form::text('infoPhoneNumber',$user['phone'],array('required', 'pattern' => 'phone'))}}
            <small class="error">Please enter a valid phone number</small>
        </div>
        <div class="large-1 small-2 show-for-medium-only columns top-8marginer"><img src="../images/profile/lockicon.png" alt=""/></div>
        <div class="large-1 show-for-large-only columns"></div>
	</div>
    
	<!--//////////////////// Education Level \\\\\\\\\\\\\\\\\\\\-->
    <div class="row">
		<div class="large-3 small-12 columns personalInfoform bold-font"> 
            {{ Form::label('edu_level', 'Education Level' )}} 
        </div>
		<div class="large-7 small-12 columns">
            {{ Form::select('edu_level', array(''=>'Choose','college'=>'College','high_school'=>'High School'),$user['edu_level'],array('required','id'=>'edu_level'))}}
            <small class="error">Please select your education level</small>
        </div>
        <div class="large-1 show-for-large-only columns top-8marginer">&nbsp;</div>
        <div class="large-1 show-for-large-only columns"></div>
	</div>
	<!--\\\\\\\\\\\\\\\\\\\\ Education Level ////////////////////-->

	<!--//////////////////// HS Attended \\\\\\\\\\\\\\\\\\\\-->
	<div id='hs_attended_row' class="row" {{ $user['in_college'] ? 'style="display: none;"' : '' }}>
		<div class='large-3 small-12 column personalInfoform bold-font'>
			{{ Form::label('hs_attended', 'School Name') }}
		</div>
		<div class='large-7 small-12 column end'>
			{{ Form::select('hs_attended', $user['schools_attended']['high_schools'], $user['in_college'] ? '' : $user['currentSchool'], array('class' => 'school_group')) }}
			<small class="error">Select your high school, or select 'find another'...</small>
		</div>
	</div>
	<!--\\\\\\\\\\\\\\\\\\\\ HS Attended  ////////////////////-->

	<!--//////////////////// Colleges Attended \\\\\\\\\\\\\\\\\\\\-->
	<div id='colleges_attended_row' class="row" {{ $user['in_college'] ? '' : 'style="display: none;"' }}>
		<div class='large-3 small-12 column personalInfoform bold-font'>
			{{ Form::label('colleges_attended', 'School Name') }}
		</div>
		<div class='large-7 small-12 column end'>
			{{ Form::select('colleges_attended', $user['schools_attended']['colleges'], $user['in_college'] ? $user['currentSchool'] : '', array('class' => 'school_group')) }}
			<small class="error">Select your college, or select 'find another'...</small>
		</div>
	</div>
	<!--\\\\\\\\\\\\\\\\\\\\ Colleges Attended  ////////////////////-->

	<!--//////////////////// School Name \\\\\\\\\\\\\\\\\\\\-->
    <div id='new_school_row' class="row" style='display: none;'>
		<div class="large-3 small-12 columns personalInfoform bold-font"> 
            {{ Form::label('new_school', 'Add a school' )}} 
        </div>
		<div id="new_school_container" class="large-7 small-12 columns end ui-front">
			{{ Form::text('new_school', '', array( 'placeholder' =>'Find your school', 'class' => 'school_group', 'pattern' => 'school_name'))}}
			<small class="error">Enter your school name</small>
        </div>
	</div>
	<!--\\\\\\\\\\\\\\\\\\\\ School Name ////////////////////-->
    

    <!--\\\\\\\\\ graduation year //////////////////////////-->
    <div class="row">
		<div class="large-3 small-12 columns personalInfoform bold-font"> 
            {{ Form::label('infoGradYear', 'Graduation Year' )}} 
        </div>
		<div class="large-7 small-12 columns">
		<?php 
        $today = date("Y");
        $selected = $today;
        $startYear = $today + 6;
        $endYear = $today - 53;
        ?>
        <select id='infoGradYear' name="infoGradYear" required >
        <option value="">Select a year</option>
        @for ($i = $startYear; $i > $endYear; $i--)
        @if($user['grad_Year']==$i)
             <option value="{{$i}}" selected="selected">{{$i}}</option>
        @else
	        <option value="{{$i}}">{{$i}}</option>
        @endif
        @endfor
        </select>
         <small class="error">Please select your graduation year</small>
        </div>
        <div class="large-1 show-for-large-only columns top-8marginer">&nbsp;</div>
        <div class="large-1 show-for-large-only columns"></div> 
        <div class="large-1 show-for-large-only columns top-8marginer">&nbsp;</div>
        <div class="large-1 show-for-large-only columns"></div>
 
	</div>

      <!--\\\\\\\\\\\\\\\\\ intended start year ///////////////////-->
        <div class="row">
        <div class="large-3 small-12 columns personalInfoform bold-font"> 
            {{ Form::label('infoStartTerm', 'Intended Start Term' )}} 
        </div>
        <div class="large-7 small-12 columns">
        
        <?php 
        
        $TERMS = 
        array('Spring','Spring','Spring','Summer','Summer','Summer','Fall','Fall','Fall','Winter', 'Winter','Winter');
        
        $TERM_CYCLE = array('Spring','Summer','Fall','Winter');

        $today = date("Y");
        $term = $TERMS[abs(date("n") - 2 )];
        $onCycle = array_search($term, $TERM_CYCLE);
        $selected = $user['planned_start_term'].' '.$user['planned_start_yr'];
        $startYear = $today + 1; //start loop with next year because may be part way through current
        $endYear = $today + 10;
        ?>

        <select id='infoStartTerm' name="infoStartTerm" required >
            @if( !isset($user['planned_start_term']) && empty($user['planned_start_term']))
                <option value="">Select a year</option>
            @endif
            
            <!-- for remainder of this year -->
            @for($k = $onCycle; $k < count($TERM_CYCLE); $k++)
                @if($user['planned_start_term']==$TERM_CYCLE[$k] && $user['planned_start_yr']==$today)
                    <option value="{{$TERM_CYCLE[$k].' '.$today}}" selected='selected'>{{$TERM_CYCLE[$k]. ' '.$today}}</option>
                @else
                    <option value="{{$TERM_CYCLE[$k].' '.$today}}">{{$TERM_CYCLE[$k]. ' '.$today}}</option>
                 @endif
            @endfor
            
            <!--terms for years following this year -->
            @for($i = $startYear; $i < $endYear; $i++)
                @foreach($TERM_CYCLE as $termc)
                    @if($user['planned_start_term'] == $termc && $user['planned_start_yr'] == $i)
                        <option value="{{$termc.' '.$i}}" selected='selected'>{{$termc.' '.$i}}</option>
                    @else
                        <option value="{{$termc.' '.$i}}">{{$termc.' '.$i}}</option>
                    @endif
                @endforeach
            @endfor
        </select>

        <small class="error">Please select your intended start term</small>
        </div>
        <div class="large-1 show-for-large-only columns top-8marginer">&nbsp;</div>
        <div class="large-1 show-for-large-only columns"></div> 
        <div class="large-1 show-for-large-only columns top-8marginer">&nbsp;</div>
        <div class="large-1 show-for-large-only columns"></div>
 
    </div>


	<br/><br/>


    <!--\\\\\\\\\\\\\\\\\ visible to recruiters and scholarships section ///////////-->	
    <div class="row">
    	<div class="large-12 column">Visible to Recruiters and Scholarships</div>
    </div>
    <br/>
	
    <div class="row">
    	<div class="large-2 small-12 columns the-profile-form bold-font">
            {{ Form::label('infoBirthDate', 'Birth Date' )}}
        </div>
        <div class="large-1 small-4 columns">
            {{ Form::text('infoBirthDateM',$user['birthdayM'],array('placeholder'=>'mm','pattern'=>'month'))}}
        </div>
		<div class="large-1 small-4 columns">
            {{ Form::text('infoBirthDateD',$user['birthdayD'],array('placeholder'=>'dd','pattern'=>'number'))}}
        </div>
		<div class="large-1 small-4 columns">
            {{ Form::text('infoBirthDateY',$user['birthdayY'],array('placeholder'=>'yyyy','pattern'=>'number'))}}
        </div>
        <div class="large-1 columns the-profile-form show-for-large-up">&nbsp;</div>
        <div class="large-3 small-12 columns the-profile-form bold-font">
            {{ Form::label('infoReligion', 'Religion' )}}
        </div>
		<div class="large-3 small-12 columns" id="ReligionsDropDown">
            {{ Form::select('infoReligion', $religions, $user['religion' ] != 0 ? $user['religion' ] : '', array( 'pattern' => 'integer' ))}}
		</div>
	</div>
    <div class="row">
    	<div class="large-2 small-12 columns the-profile-form bold-font">
            {{ Form::label('infoGender', 'Gender' )}}
        </div>
        <div class="large-3 small-12 columns">
            {{ Form::select('infoGender', array(''=>'Choose', 'm'=>'Male', 'f'=>'Female' ),$user['gender'] )}}
        </div>
        <div class="large-1 columns the-profile-form show-for-large-up">&nbsp;</div>
        <div class="large-3 small-12 columns the-profile-form bold-font">
            {{ Form::label('infoMaritalStatus', 'Marital Status' )}}</div>
        <div class="large-3 small-12 columns">
            {{ Form::select('infoMaritalStatus', array('0'=>'Choose','Single'=>'Single','Married'=>'Married'),$user['maritalStatus'])}}
        </div>
	</div>
    
    <div class="row">
    	<div class="large-2 small-12 columns the-profile-form bold-font">
            {{ Form::label('infoEthnicity', 'Ethnicity' )}}</div>
		<div class="large-3 small-12 columns" id="EthnicitiesDropDown">
            {{ Form::select('infoEthnicity', $ethnicities, $user['ethnicity' ] != 0 ? $user['ethnicity' ] : '', array( 'pattern' => 'integer' ))}}
		</div>
        
        <div class="large-1 columns the-profile-form show-for-large-up">&nbsp;</div>
        <div class="large-3 small-12 columns the-profile-form bold-font">
            {{ Form::label('infoChildren', 'Do you have children?' )}}
        </div>
        <div class="large-3 small-12 columns">
            {{ Form::select('infoChildren', array(''=>'Choose','Yes'=>'Yes','No'=>'No'),$user['children'] )}}
        </div>
	</div>

    <div class="row">
        <div class="large-2 small-12 columns the-profile-form bold-font">
            {{ Form::label('infoInMilitary', 'In Military?' )}}
        </div>
        <div class="large-3 end small-12 columns">
            {{ Form::select('infoInMilitary', array(''=>'Choose','1'=>'Yes','0'=>'No'),$user['is_military_id'], array('required') )}}
            <small class="error">Please choose if you are in military or not</small>
        </div>    

        <div class="large-1 columns the-profile-form show-for-large-up">&nbsp;</div>
        <div class="large-6 small-12 columns bold-font">
            <div class="row military_affiliation @if(!isset($user['military_affiliation_id'])) hide @endif">
                <div class="large-6 small-12 columns the-profile-form bold-font">
                    {{ Form::label('infoMilitaryAffiliation', 'Military Affiliation' )}}
                </div>
                <div class="large-6 small-12 columns">
                    {{ Form::select('infoMilitaryAffiliation', $military_affiliation_arr, $user['military_affiliation_id'] )}}
                    <small class="error">Please choose your military affiliation</small>
                </div>
            </div>
        </div>
        
    </div>

    <br />
    <div class="row">
		<div class="small-6 column close-reveal-modal">
			<div class='button btn-cancel'>
				Cancel
			</div>
		</div>
        <div class="small-6 column">{{ Form::submit('Save', array('class'=>'button btn-Save', 'id' => 'personal_info_save_button'))}}</div>
        <!-- <div class="large-3 small-12 column btn-save-continue">Save &amp; Continue</div> -->
    </div>
{{ Form::close() }}
</div>

<!--//////////////////// PROFILE PICTURE MODAL \\\\\\\\\\\\\\\\\\\\-->
<div class='reveal-modal small' id="changeProfilePic" data-reveal>
	<div class='row'>
		<div class='small-12 column close_x'>
			<img src="/images/close-x.png" class="close-reveal-modal" style="float: right;" alt=""></img>
		</div>
	</div>
	{{ Form::open(array('url' => "/ajax/profile/personalInfoPhoto/" , 'method' => 'POST', 'id' => 'uploadProfilePictureForm', 'enctype' => 'multipart/form-data', 'name' => 'uploadPhotoForm', 'data-abide' => 'ajax' )) }}
	{{ csrf_field() }}
	<div class="row">
    	<div class="large-2 column">
			<div class='row collapse'>
				<div class='small-12 column'>
					<img id='modal_profile_picture' src="{{ $user['profile_img_loc']}}" alt="{{ $user['fname']}} {{ $user['lname']}}" />
				</div>
			</div>
			@if( strpos( $user['profile_img_loc'], '/images/profile/default.png' ) === false )
				<div class='row collapse'>
					<div id='remove-picture' class='button btn-text'>
						Delete
					</div>
				</div>
			@endif
        </div>
		<div class="large-10 column">
			<!--////////// Profile picture form \\\\\\\\\\-->
			<div class='row'>
				<div class='small-12 column'>
					{{ Form::label( 'profile_picture', 'Upload a Profile Picture', array( 'class' => 'upload-title' ) ) }}
				</div>
			</div>
			<div class='row'>
				<div class='small-12 column'>
					{{ Form::file('profile_picture', array( 'required', 'pattern' => 'file_types' )) }}
					<small class='error'>Accepted formats: .jpg, png or gif</small>
				</div>
			</div>
			<!--\\\\\\\\\\ Profile picture form //////////-->
		</div>
    </div>
    <div class="row" id="profile_image_row">
		<div class="small-6 column close-reveal-modal">
			<div class='button btn-cancel'>
				Cancel
			</div>
		</div>
		<div class="small-6 column">
			{{ Form::submit('Save', array('class' => 'button btn-Save')) }}
		</div>
    </div>
{{ Form::close() }}
</div>
<!--\\\\\\\\\\\\\\\\\\\\ PROFILE PICTURE MODAL ////////////////////-->

<script language="javascript">
    //reload zurb items.
	function init_pi_fndtn(){
		$(document).foundation({
			abide : {
				patterns : {
					address: /^[a-zA-Z0-9\.,#\- ]+$/,
                    skype: /^[a-z][a-z0-9\.,\-_]{5,31}$/i,
					phone: /^([0-9\-\+\(\) ])+$/,
					city: /^[a-zA-Z\.\- ]+$/,
					number: /^[-+]?[0-9]\d*$/,
					month : /^[-+]?[0-9]\d*$/,
					zip: /^[a-zA-Z0-9\.,\- ]+$/,
					name: /^([a-zA-Z\-\.' ])+$/,
					school_name: /^([\u0000-\uFFFF\ ])+$/,
					file_types:/^[0-9a-zA-Z\\:\-_ ]+.(jpg|png|gif|JPG|PNG|GIF)$/
				}
			}
		});
	}
	init_pi_fndtn();

	$(function() {
		PostPersonalInfo();
	});    

    $("#DropZoneId").dropzone({
        url: "ajax/profile/personalInfoPhoto/",
        maxFiles: 1,
    	autoProcessQueue: false,
        maxfilesexceeded: function() {
            this.removeAllFiles();
           // this.addFile(file);	 
        },
    	dictMaxFilesExceeded: function () {
    	alert("You can not upload any more files.");
    	},
    	maxfilesreached: function () {
    	alert("You can not upload any more files.");
    	},
    	paramName:'profilepic',
    	success: function (file,data)
    	{
    		//alert(file.name);
    		//alert(data);
    		document.getElementById('photoPath').value=data;
    		//window.location='http://localhost/profile';	
    	},
    	addRemoveLinks: true
    })
    //var myDropzone = new Dropzone("div#DropZoneId", { url: "ajax/profile/personalInfo/",maxFiles:'1'});
    $(document).ready(function(){
		/*
    	var options = { 
    			target:   '',   // target element(s) to be updated with server response 
    			beforeSubmit:  showLoading,  // pre-submit callback 
    			success:       successUpload,  // post-submit callback 
    			resetForm: true        // reset the form after successful submit 
    		}; 
    		
    	 $('#uploadPhotoForm').submit(function() { 
    			$(this).ajaxSubmit(options);
    			// always return false to prevent standard browser submit and page navigation 
    			return false;
    		}); 
		 */
		/* For PersonalInfo Area
		 * If a user selects a country that is not the US
		 * Then we remove the required attribute from US specific
		 * fields:
		 * - Address
		 * - City
		 * - State
		 * - Zip
		 */
		$('#infoCountry').change(function(){
			var address = $( '#infoAddress' );
			var city = $( '#infoCity' );
			var state = $( '#infoState' );
			var zip = $( '#infoZip' );
			var us_only = $( '.us_only' );
			var us_only_box = $( '.us_only_box' );
			// If selection is not the US, remove required fields
			if( $( this ).val() != 1 ){
				address.removeAttr( 'required' );
				city.removeAttr( 'required' );
				state.removeAttr( 'required' );
				zip.removeAttr( 'required' );

				us_only_box.slideUp( 250, 'easeInOutExpo' );
				us_only.val( '' );
			}
			// If selection IS the US, add required
			else{
				address.attr( 'required', 'required' );
				city.attr( 'required', 'required' );
				state.attr( 'required', 'required' );
				zip.attr( 'required', 'required' );

				us_only_box.slideDown( 250, 'easeInOutExpo' );
			}
			init_pi_fndtn();
		});
		/* For PersonalInfo Area
		 * If the user's pre-selected country is not the U.S.
		 * Then we remove the required attribute from US specific
		 * fields:
		 * - Address
		 * - City
		 * - State
		 * - Zip
		 */
		if( $('#infoCountry').val() != 1 ){
			var address = $( '#infoAddress' );
			var city = $( '#infoCity' );
			var state = $( '#infoState' );
			var zip = $( '#infoZip' );
			var us_only = $( '.us_only' );
			var us_only_box = $( '.us_only_box' );

			address.removeAttr( 'required' );
			city.removeAttr( 'required' );
			state.removeAttr( 'required' );
			zip.removeAttr( 'required' );
			init_pi_fndtn();

			us_only_box.hide();
			us_only.val( '' );
		}
    }); 
	// End document ready

	/*
    function successUpload() {
		$('#changeProfilePic').foundation('reveal', 'close');
		loadProfileInfo("personalInfo");
    }

    function showLoading() {
    	$('#UploadButton1').html('<span class="headColData">Upload in Progress, Please Wait...</span>');
    	$('#UploadButton2').html('<span class="headColData">Upload in Progress, Please Wait...</span>');
    }
	 */

	/* For high school/college 'schools attended' drop down
	 * switches to the correct (high school/college) dropdown based
	 * on the user's 'level of education' dropdown selection
	 */
	$('#edu_level').change(function(){
		edu_level = $(this).val();
		colleges_attended_row = $('#colleges_attended_row');
		hs_attended_row = $('#hs_attended_row');
		if( edu_level == 'college' ){
			// Hide/show rows
			hs_attended_row.slideUp( 250, 'easeInOutExpo', function(){
				colleges_attended_row.slideDown( 250, 'easeInOutExpo', function(){
					colleges_attended_val = $('#colleges_attended').val();
					new_school_row = $('#new_school_row');
					// check if 'add school' option is selected and slide autocomplete down if so
					if( colleges_attended_val == 'new' ){
						new_school_row.slideDown( 250, 'easeInOutExpo' );
					}
					else{
						new_school_row.slideUp( 250, 'easeInOutExpo' );
					}
				} );
			} );
		}
		else if( edu_level == 'high_school' ){
			// Hide/show rows
			colleges_attended_row.slideUp( 250, 'easeInOutExpo', function(){
				hs_attended_row.slideDown( 250, 'easeInOutExpo', function(){
					hs_attended_val = $('#hs_attended').val();
					new_school_row = $('#new_school_row');
					// Check if add school option is selected and slide autocomplete down if so
					if( hs_attended_val == 'new' ){
						new_school_row.slideDown( 250, 'easeInOutExpo' );
					}
					else{
						new_school_row.slideUp( 250, 'easeInOutExpo' );
					}
				} );
			} );
		}

		init_new_school_autocomp();
		school_group_fndtn();
	});
	
	/***********************************************************************
	 *===============SHOW/HIDE NEW_SCHOOL DROPDOWN SECTION==================
	 ***********************************************************************
	 * These blocks show/hide the new_school autocomplete when the user
	 * selects the 'search for another school' option
	 */
	 // College
	$('#colleges_attended').change(function(){
		selection = $(this).val();
		if( selection == 'new' ){
			$('#new_school_row').slideDown( 250, 'easeInOutExpo', school_group_fndtn );
		}
		else{
			$('#new_school_row').slideUp( 250, 'easeInOutExpo', school_group_fndtn );
		}
	});

	// High School
	$('#hs_attended').change(function(){
		selection = $(this).val();
		if( selection == 'new' ){
			$('#new_school_row').slideDown( 250, 'easeInOutExpo', school_group_fndtn );
		}
		else{
			$('#new_school_row').slideUp( 250, 'easeInOutExpo', school_group_fndtn );
		}
	});
	/***********************************************************************
	 *===============END SHOW/HIDE NEW_SCHOOL DROPDOWN SECTION==============
	 ***********************************************************************/

	// Add/remove required attributes depending on if
	// the element is visible or not
	function school_group_fndtn(){
		hidden = $('.school_group:hidden');
		visible = $('.school_group:visible');

		hidden.removeAttr('required');
		hidden.removeAttr('data-invalid');
		/* We can't do hidden.val('') because this resets default values
		 * as this function is called when the modal is shown
		 */
		$('#new_school').val('');

		visible.attr('required', 'required');

		init_pi_fndtn();
	}
	/* New School Autocomplete
	 * This autocomplete is wrapped in a function which can be called
	 * to re-initialize autocomplete with a different route. This allows
	 * us to change toggle the autocomplete results between high schools
	 * and colleges
	 */
	function init_new_school_autocomp(){
		var edu_level_val = $('#edu_level').val();
		if( edu_level_val != 'college' && edu_level_val != 'high_school' ){
			return false;
		}
		$("#new_school").autocomplete({
			source:"getAutoCompleteData?zipcode=" + '95376' + "&type=" + edu_level_val + "&unverified=1",
			minLength: 1,
			change: function(event, ui){
				var user_type = $('#user_type').val();
				var input = $('#new_school');
				var autocomp_list = $('#new_school_container .ui-autocomplete > li');

				var match = false;
				// Set default val for input's data val if it is not found/set in the DOM
				var data_val = typeof input.data( 'school' ) == 'undefined' ? '' : input.data( 'school' ).toLowerCase();
				var user_val = input.val().toLowerCase();


				// Loop through the autocomplete list to find matches
				autocomp_list.each(function(loop_count){
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
						$('#SchoolId').val('');
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
				 * then clear the #SchoolId value, and input's data field
				 */
				if( match == false && data_val != user_val ){
					input.data('school', '');
					$('#SchoolId').val('');
				}
			},
			select: function(event, ui) {
				var school_name_field = $('#new_school');
				var school_id_field = $('#SchoolId');

				// Set form field values on autocomplete select
				school_name_field.data('school', ui.item.value);
				school_id_field.val(ui.item.id);
			}
		});
	}
</script>
