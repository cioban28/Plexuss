<?php
	$inquiries = $inquiry_list;
?>

<script type="text/javascript" src="/js/jquery.tooltipster.min.js"></script>
<link rel="stylesheet" href="/css/tooltipster.css"/>

<script type="text/javascript">
    $(document).ready(function($) {
        $('.row-of-student-profile .admin-side-menu-tooltip-icon').tooltipster({
            theme: 'tooltipster-right',
            interactive: true,
            contentAsHTML: true
        });
    });
</script>

<div class="hasResults hide" data-last-results="{{$has_searchResults or 'null'}}" data-current-viewing="{{$current_viewing}}" data-total-results="{{$total_results}}" data-college="{{$user_id or 'null'}}" data-more-results="{{json_encode($inquiries)}}"></div>
<!-- remove applied colleges confirmation modal -->
<div style='display: none;' id='remove-applied-college-confirm' title='Remove College'>
  	<p>Are you sure you want to remove this student from this college?</p>
  	<p class='removed-college-name'></p>
  	<p class='removed-student-name'></p>
  	<p class='removed-student-status'></p>
</div>
@foreach ($inquiries as $key)

	<div class="row item inquirie_row @if( isset($key['is_notified']) && $key['is_notified'] == false ) unread @else read @endif  @if($currentPage == 'admin-recommendations')  @if( isset($key['recommendation_type']) && $key['recommendation_type'] != 'not_filtered') adv-filter-generated-recommendation @else plex-generated-recommendation @endif @endif" data-id={{$key['hashed_id']}} data-uid="{{$key['student_user_id']}}" data-hashedid='{{$key["hashed_id"] or ""}}' data-college="{{$user_id or 'null'}}" @if (isset($key['recruitment_id']))data-recruitment_id={{$key['recruitment_id']}} @endif>

		@if( $currentPage == 'admin-prescreened' )
		<!-- student check box -->
		<div class="column small-1 student-chkbx-col text-right">
			@if(isset($key['is_handshake_paid']))
				<span class="handshake-dollar-sign">$</span>
			@endif
			{{Form::checkbox('name', 'student', false, array('class' => 'student-row-chkbx'))}}
		</div>
		<!-- student name -->
		<div class="column small-5 medium-3 large-2 text-left messageName" data-hashedid='{{$key['hashed_id'] or ""}}'  data-uid="{{$key['student_user_id']}}" OnClick='inquiriesToggleProfile(this);' style="width: 12.66667%">
		@else
		<div class="column small-5 medium-3 large-2 text-left messageName" data-hashedid='{{$key['hashed_id'] or ""}}'  data-uid="{{$key['student_user_id']}}" OnClick='inquiriesToggleProfile(this);'>
		@endif
			<span class='arrow'></span><span class="inquiry-name" data-tooltip aria-haspopup="true" class="has-tip" title="{{$key['name'] or ''}}">{{$key['name'] or ''}}</span>
		</div>

		<!-- student gpa -->
		<div class="text-center column medium-1 @if( $currentPage == 'admin-inquiries' || $currentPage == 'admin-approved' ) show-for-large-up @else small-2 @endif messageGPA">
			{{$key['gpa']  or 'N/A'}}
		</div>

		<!-- student SAT -->
		<!--
		<div class="text-center column small-2 medium-1 show-for-large-up messageSAT">
			{{$key['sat_score']  or 'N/A'}}
		</div>
		-->
		<!-- student ACT -->
		<!--
		<div class="text-center column small-2 medium-1 show-for-large-up messageACT">
			{{$key['act_composite']  or 'N/A'}}
		</div>
		-->

		<!-- student Programs Interested in -->
		<div class="text-center column small-12 medium-4 large-2 show-for-medium-up programsIntrest" style="width: 14.66667%">
			<?php
				if(isset($key['degree_initials']) && isset($key['major'])){
				 	$degree = $key['degree_initials'].', '.$key['major']; 
				}elseif (isset($key['degree_initials'])) {
				 	$degree = $key['degree_initials'] or 'N/A'; 
				}else{
				 	$degree = $key['major'] or 'N/A'; 
				}
			?>
			<span data-tooltip aria-haspopup="true" class="has-tip" title="{{$key['degree_name'].': '.$key['major']}}">{{$degree or 'N/A'}}</span>
		</div>
		
		<!-- student Country -->
		<div class=" column small-2 medium-1 large-1 show-for-large-up countryCol">
			@if (isset($key['country_code']) && $key['country_code'] != 'N/A')
				<span class="has-tip tip-top" data-tooltip aria-haspopup="true" title="{{$key['country_name']}}">
					<div class="flag flag-{{ strtolower($key['country_code']) }}"></div>
				</span>
				<span class="countryName"> {{ $key['country_name'] or ''}}</span>
			@else
				N/A
			@endif
		</div>

		<!-- student date -->
		<div class="text-center column small-12 medium-1 show-for-large-up date">
			{{$key['date'] or ''}}
		</div>

		<!-- uploads doc -->
		<div class="text-center column small-12 medium-4 large-1 show-for-large-up uploadsDoc">
			
			<ul id="uploadDocs" class="uploaded-docs-list">
			   @if(isset($key['prescreen_interview']) && $key['prescreen_interview'] == true)
			  <li class="has-tip uploadDocsSpriteSmall prescreen_interview" data-tooltip aria-haspopup="true" title="Prescreen Interview" data-type="prescreen_interview">&nbsp;</li>
			  @endif
			  @if($key['transcript'] == true)
			  <li class="has-tip uploadDocsSpriteSmall transcript" data-tooltip aria-haspopup="true" title="Transcript"  data-type="transcript">&nbsp;</li>
			  @endif
			  @if($key['toefl'] == true)
			  <li class="has-tip uploadDocsSpriteSmall toefl" data-tooltip aria-haspopup="true" title="TOEFL"  data-type="toefl">&nbsp;</li>
			  @endif
			  @if($key['ielts'] == true)
			  <li class="has-tip uploadDocsSpriteSmall ielts" data-tooltip aria-haspopup="true" title="IELTS"  data-type="ielts">&nbsp;</li>
			  @endif
			  @if($key['financial'] == true)
			  <li class="has-tip uploadDocsSpriteSmall financial" data-tooltip aria-haspopup="true" title="Financial Document"  data-type="financial">&nbsp;</li>
			  @endif
			  @if($key['resume'] == true)
			  <li class="has-tip uploadDocsSpriteSmall resume" data-tooltip aria-haspopup="true" title="Resume / CV"  data-type="resume">&nbsp;</li>
			  @endif
			  @if($key['other'] == true)
			  <li class="has-tip uploadDocsSpriteSmall other" data-tooltip aria-haspopup="true" title="Other"  data-type="other">&nbsp;</li>
			  @endif
			  @if($key['essay'] == true)
			  <li class="has-tip uploadDocsSpriteSmall essay" data-tooltip aria-haspopup="true" title="Essay"  data-type="essay">&nbsp;</li>
			  @endif
			  @if($key['passport'] == true)
			  <li class="has-tip uploadDocsSpriteSmall passport" data-tooltip aria-haspopup="true" title="Passport"  data-type="passport">&nbsp;</li>
			  @endif
			  @if($key['transcript'] == false && 
			  	  $key['toefl'] == false && 
			  	  $key['ielts'] == false && 
			  	  $key['financial'] == false &&
			  	  $key['resume'] == false &&
			  	  $key['other'] == false &&
			  	  $key['essay'] == false &&
			  	  $key['passport'] == false &&
			  	  $key['prescreen_interview'] == false)

			  	  <li>&nbsp;</li>
			  @endif


			</ul>
		</div>

		<!-- message student icon -->
		@if( $currentPage == 'admin-inquiries' || $currentPage == 'admin-approved' )
		<div class="messageIconArea text-center column small-3 medium-2 large-1 @if ($key['hand_shake'] == 1) selected @endif">
            <div class ='inquiries-row-message-container'>
                <div class='message-button' onclick='window.open("/admin/messages/{{$key['student_user_id']}}/inquiry-msg");'></div>
            </div>
{{-- 			<div class='showMessageIcon '>
				<a href="/admin/messages/{{$key['student_user_id']}}/inquiry-msg">
					<div class='inquiries-message message showMessageIcon'></div>
				</a>
			</div>

			<div class='ShowNA '>
				N/A
			</div> --}}
		</div>
		@endif

        @if( $currentPage == 'admin-inquiries')
        <!-- applied column -->
        <div class="column large-1 text-center show-for-large-up applied-star lg">
            <div class="stars applied-star @if(isset($key['applied']) && $key['applied'] == 1) applied @elseif(isset($key['user_applied']) && $key['user_applied'] == 1) user-applied @else no @endif">&#9733;</div>
        </div>
        <!-- enrolled column -->
        <div class="column text-center small-2 medium-2 large-1 show-for-large-up enrolled-star lg @if($currentPage == "admin-verifiedHs" || $currentPage == "admin-verifiedApp" || $currentPage == "admin-converted") end @endif">
            <div class="stars applied-star @if($key['enrolled'] == 1) enrolled @else no @endif">&#9733;</div>
        </div>
        @endif

		<!-- yes/no recruit buttons -->
		@if( $currentPage == 'admin-prescreened' )
		<div class="text-center column small-4 medium-3 large-1 end text-center">
		@elseif( $currentPage == 'admin-inquiries' )
        <div class="column text-center small-2 medium-2 large-1 show-for-large-up" style='float: left;display: flex !important;justify-content: center;'>
        @else
		<div class="text-center column small-4 medium-3 large-2 end text-center">
		@endif
			<div class="row buttonColumn">
				@if( isset($title) && $title == 'Student Recommendations' )
					<div class="columns small-6 medium-6">
						<div class='button yesbutton yesnobuttons @if ($key['hand_shake'] == 1) selected @endif' onclick="SendRecruithandShakeStatus( {{$key['student_user_id']}}, 1, this, true);" >Yes</div>
					</div>
					<div class="columns  small-6 medium-6">
						<div class='button nobutton yesnobuttons @if ($key['hand_shake'] == -1) selected @endif' onclick="SendRecruithandShakeStatus({{$key['student_user_id']}}, -1, this, true);">No</div>
					</div>

				@elseif( $currentPage == 'admin-prescreened' )
					<div class="columns small-6 medium-6">
						<div class='button yesbutton yesnobuttons @if ($key['interview_status'] == 1) selected @endif' onclick="setInterviewStatus(this, 'yes', {{$key['student_user_id']}});" >Yes</div>
					</div>
					<div class="columns  small-6 medium-6">
						<div class='button nobutton yesnobuttons @if ($key['interview_status'] == -1) selected @endif' onclick="setInterviewStatus(this, 'no', {{$key['student_user_id']}});" >No</div>
					</div>
                @elseif ( $currentPage == 'admin-inquiries' )
                    <div class="columns small-6 medium-6">
                        <div class='inquiries-remove-button'></div>
                    </div>
				@elseif ( $currentPage != 'admin-inquiries' )
					<div class="columns small-6 medium-6">
						<div class='button yesbutton yesnobuttons @if ($key['hand_shake'] == 1) selected @endif' onclick="SendRecruithandShakeStatus( {{$key['student_user_id']}}, 1, this, false);">Yes</div>
					</div>
					<div class="columns  small-6 medium-6">
						<div class='button nobutton yesnobuttons @if ($key['hand_shake'] == -1) selected @endif' onclick="SendRecruithandShakeStatus({{$key['student_user_id']}}, -1, this, false);">No</div>
					</div>
                @endif
			</div>
		</div>

		@if( $currentPage == 'admin-prescreened' )
			<div class="prescreened column large-1 text-center show-for-large-up applied-star lg end">
				<div onClick="setAppliedEnrolledPrescreened(this, 'applied', {{$key['student_user_id']}})" class="stars applied-star @if(isset($key['applied']) && $key['applied'] == 1) applied @elseif(isset($key['user_applied']) && $key['user_applied'] == 1) user-applied @else no @endif">&#9733;</div>
			</div>

			<!-- enrolled column -->
			<div class="prescreened column text-center small-2 medium-2 large-1 show-for-large-up enrolled-star lg end">
				<div onClick="setAppliedEnrolledPrescreened(this, 'enrolled', {{$key['student_user_id']}})" class="stars applied-star @if($key['enrolled'] == 1) enrolled @else no @endif">&#9733;</div>
			</div>

			<!-- remove from list button -->
			<div class="text-center column small-2 medium-2 large-1 text-center remove-student-for-prescreened-col" data-rid="{{$key['rid']}}" >
				X
			</div>
		@endif

		
	</div><!-- end of inquirie row -->

@endforeach


