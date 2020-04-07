@extends('private.profile.master')

@section('sidebar')
<script language="javascript">
var DefaultSection='{{$Section}}';
</script>

	<div class='row mobile-profile-row hide-for-small-only profile-sidebar-nav-container'>
		<div class='small-12 column'>
			<ul class="side_nav">
				<li><label>Profile</label></li>

				<li class='menubutton' onclick='loadProfileInfo("personalInfo");'>
					<div class='personalicon'></div><span class='personalInfo'>Personal Info <span class="prof_tab_perc">+25%</span></span>
				</li>

				<li class='menubutton objective-profile-tab' onclick='loadProfileInfo("objective");'>
					<div class='objectiveicon'></div><span class='objective'>Objective <span class="prof_tab_perc">+5%</span></span>
				</li>

				<li class='menubutton financial-info-profile-tab' onclick='loadProfileInfo("financialinfo");'>
					<div class='financialinfo-icon'></div><span class='financialinfo'>Financial Info <span class="prof_tab_perc">+5%</span></span>
				</li>

				<li class='menubutton' onclick='loadProfileInfo("uploadcenter");'>
					<div class='uploadcenter-icon'></div><span class='uploadcenter'>Upload Center<span class="prof_tab_perc">+5%</span></span>
				</li>

                <ul style="position:relative;">               
					<!--<div class="profile_section_locked" id="GradeLock" align="center">
						<div style="top:50%;">
							<img src="/images/lock-icon.png" alt="Locked" /><br />You can unlock and add your Grades after filling out your Personal Info
						</div>
					</div>-->
					<li class=''>
						<label>Grades</label>
					</li>
					<li class='menubutton' onclick='loadProfileInfo("scores");'>
						<div class='scoresicon'></div><span class='scores'>Scores <span class="prof_tab_perc">+5%</span></span>
					</li>
	                <li style="line-height:8px;">
	                	<img src="/images/blank.png" height="5" />
	                </li>
                </ul>

                <ul style="position:relative;">
                	<!--<div class="profile_section_locked" id="AccomplishmentsLock" align="center">
                		<div style="top:50%;margin-top:75%;">
                			<img src="/images/lock-icon.png" alt="Locked" /><br />You can unlock and add your Grades after filling out your Personal Info
                		</div>
                	</div>-->
					<li class='' onclick=''><label>Accomplishments</label>
		                <ul class="accomplishments-nav">
		                    <li class="experience"><a href="#" onclick='loadProfileInfo("experience");'><span class="acc-icon-image i-experience"></span>Experience <span class="prof_tab_perc">+1%</span></a></li>
		                    <li class="skills"><a href="#" onclick='loadProfileInfo("skills");'><span class="acc-icon-image i-skills"></span>Skills <span class="prof_tab_perc">+1%</span></a></li>
		                    <li class="interests"><a href="#" onclick='loadProfileInfo("interests");'><span class="acc-icon-image i-interests"></span>Interests <span class="prof_tab_perc"></span></a></li>
		                    <li class="clubOrgs"><a href="#" onclick='loadProfileInfo("clubOrgs");'><span class="acc-icon-image i-clubs"></span>Clubs &amp; Orgs <span class="prof_tab_perc">+1%</span></a></li>
		                    <li class="honorsAwards"><a href="#" onclick='loadProfileInfo("honorsAwards");'><span class="acc-icon-image i-honors"></span>Honors &amp; Awards <span class="prof_tab_perc">+1%</span></a></li>
		                    <li class="languages"><a href="#" onclick='loadProfileInfo("languages");'><span class="acc-icon-image i-languages"></span>Languages <span class="prof_tab_perc">+1%</span></a></li>
		                    <li class="certifications"><a href="#" onclick='loadProfileInfo("certifications");'><span class="acc-icon-image i-certifications"></span>Certifications </a></li>
		                    <li class="patents"><a href="#" onclick='loadProfileInfo("patents");'><span class="acc-icon-image i-patents"></span>Patents </a></li>
		                    <li class="publications"><a href="#" onclick='loadProfileInfo("publications");'><span class="acc-icon-image i-publications"></span>Publications </a></li>
		                </ul>
	                </li>
	                
                </ul>

                <ul class="extra-info-nav">
                	<li><label>Extra Info (optional)</label></li>
                	<li class='menubutton' onclick='loadProfileInfo("highschoolInfo");'><div class='highschoolicon'></div><span class='highschoolInfo'>High School Info</span></li>
					<li class='menubutton' onclick='loadProfileInfo("collegeInfo");'><div class='collegeicon'></div><span class='collegeInfo'>College Info</span></li>
                </ul>
			</ul>			
		</div>
	</div>
@stop

@section('content')

<!-- NOT USING ANYMORE
<div id="NotificationAlertBox">
</div>
-->
	
	<!-- Change country profile banner - start -->
	@if( isset($prof_intl_country_chng) && $prof_intl_country_chng == 0 )
	<div class="row for-international-students-row">
		<div class="column small-12">
			<div class="row for-international-inner-row">
				<div class="column small-12 large-8">International Students have a different profile. To switch, tell us which country you are from: </div>
				<div class="column small-12 large-4">
					<div class="row">
						<div class="column small-12 medium-6 country-change-form-container">
						{{Form::open()}}
							{{Form::select('change-country', $countries,'1', array('class'=>'prof-change-country-form', 'data-user-id'=>$user_id))}}
						{{Form::close()}}
						</div>
						<div class="column small-3 medium-2 large-3 text-center save-country-change-btn">Save</div>
						<div class="column small-9 medium-2 small-text-right large-text-center remove-country-change-banner-btn">X</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	@endif
	<!-- Change country profile banner - end -->

 <div class="clearfix"></div>
	<div class="row rightsidemenu">
		<div class='column'>
			<div id='personalInfo' class='profilePanel' data-type >
				Personal Info
			</div>
			<div id='objective' class='profilePanel' data-type>
				Objective Box
			</div>
			<div id="financialinfo" class="profilePanel" data-type>
				Financial Info
			</div>
			<div id='scores' class='profilePanel' data-type>
				Scores Box
			</div>
			<div id="uploadcenter" class="profilePanel" data-type>
				Upload Center Box
			</div>
			<div id='highschoolInfo' class='profilePanel' data-type>
				High School Info Box
			</div>
			<div id='collegeInfo' class='profilePanel' data-type>
				College Info Box
			</div>
			<div id='experience' class='profilePanel' data-type>
				College Info Box
			</div>
			<div id='skills' class='profilePanel' data-type>
				College Info Box
			</div>
			<div id='interests' class='profilePanel' data-type>
				College Info Box
			</div>
			<div id='clubOrgs' class='profilePanel' data-type>
				College Info Box
			</div>
			<div id='honorsAwards' class='profilePanel' data-type>
				College Info Box
			</div>
			<div id='languages' class='profilePanel' data-type>
				College Info Box
			</div>
			<div id='certifications' class='profilePanel' data-type>
				College Info Box
			</div>
			<div id='patents' class='profilePanel' data-type>
				College Info Box
			</div>
			<div id='publications' class='profilePanel' data-type>
				College Info Box
			</div>
		</div>
	</div>
@stop
