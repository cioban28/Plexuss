<?php
	$inquiries = $inquiry_list;
	//
	// dd($inquiries);
	// echo "<pre>";
	// print_r($inquiries);
	// echo "</pre>";
	// exit();

	// dd( get_defined_vars() );
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

	<div class="row item inquirie_row @if( isset($key['is_notified']) && $key['is_notified'] == false ) unread @else read @endif @if($key['applied'] == 1) has-applied @else not-applied @endif @if($key['enrolled'] == 1) has-enrolled @else not-enrolled @endif"
	data-uid="{{$key['student_user_id']}}" data-hashedid='{{$key["hashed_id"] or ""}}'
	data-college="{{$user_id or 'null'}}" @if (isset($key['recruitment_id']))data-recruitment_id={{$key['recruitment_id']}} @endif>

		<div class="column small-1 student-chkbx-col text-right">
			@if(isset($key['is_handshake_paid']))
				<span class="handshake-dollar-sign">$</span>
			@endif
			{{Form::checkbox('name', 'student', false, array('class' => 'student-row-chkbx'))}}
		</div>

		<!-- student name -->

		<div class="column small-2 medium-1 large-1 text-left messageName" data-hashedid="{{$key['hashed_id']}}" OnClick='inquiriesToggleProfile(this);'>
			<span class='arrow'></span>
			<span class="inquiry-name">{{$key['name'] or ''}} </span>

			<!--<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/hasNotes.jpg" alt="Plexuss User Has Notes">-->
		</div>

		<!-- applied column -->
		<div class="column text-center small-2 medium-2 hide-for-large-up applied-star">
			<div class="stars applied-star @if(isset($key['applied']) && $key['applied'] == 1) applied @elseif(isset($key['user_applied']) && $key['user_applied'] == 1) user-applied @else no @endif">&#9733;</div>
		</div>

		<!-- enrolled column -->
		<div class="column text-center small-2 medium-2 hide-for-large-up enrolled-star">
			<div class="stars applied-star @if($key['enrolled'] == 1) enrolled @else no @endif">&#9733;</div>
		</div>

		<!-- student gpa -->
		@if( $currentPage != 'admin-verifiedApp' )
		<div class="text-center column medium-1 @if( $currentPage == 'admin-inquiries' || $currentPage == 'admin-approved' ) show-for-large-up @else small-2 @endif messageGPA">
			{{$key['gpa']  or 'N/A'}}
		</div>
		@endif

		<!-- student Programs Interested in -->
		<div class="custom-width text-center column small-12 medium-3 large-1 show-for-medium-up programsIntrest">
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
		<div class="column small-2 medium-1 large-1 show-for-large-up countryCol">
			@if (isset($key['country_code']) && $key['country_code'] != 'N/A')
				<span class="has-tip tip-top" data-tooltip aria-haspopup="true" title="{{$key['country_name']}}">
					<div class="flag flag-{{ strtolower($key['country_code']) }}"> </div>
				</span>
				<span class="countryName"> {{ $key['country_name'] or ''}} </span>
			@else
				N/A
			@endif
		</div>

		<!-- student date -->
		<div class="text-center column small-12 medium-1 show-for-large-up date">
			{{$key['date'] or ''}}
		</div>

		<!-- uploads doc -->
		{{-- @if( $currentPage != 'admin-verifiedApp' ) --}}
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
			  	  $key['prescreen_interview'] == false )

			  	  <li>&nbsp;</li>
			  @endif


			</ul>
		</div>
		{{-- @endif --}}

		<!-- message student icon -->
		@if( $currentPage == 'admin-inquiries' || $currentPage == 'admin-approved' )
		<div class="messageIconArea text-center column small-2 medium-1 large-1 @if ($key['hand_shake'] == 1) selected @endif">
			<div class='showMessageIcon ' style="display: inline-block;">
				<a href="/admin/messages/{{$key['student_user_id']}}/inquiry-msg">
					@if( isset($key['haveMessaged']) && $key['haveMessaged'] )
						<div class='message showMessageIcon haveMessaged'>
							<span data-tooltip aria-haspopup="true" class="has-tip" title="Have messaged before.">&nbsp;</span>
						</div>
					@else
						<div class='message showMessageIcon'>
							<span data-tooltip aria-haspopup="true" class="has-tip" title="Have NOT messaged yet.">&nbsp;</span>
						</div>
					@endif
				</a>
			</div>

			@if( isset($key['userTxt_opt_in']) && $key['userTxt_opt_in'] == 1 )
			<div class="textIcon">
				<a href="/admin/messages/{{$key['student_user_id']}}/inquiry-txt">
					@if( isset($key['haveTexted']) && $key['haveTexted'] )
						<div class='texted yes'>
							<span data-tooltip aria-haspopup="true" class="has-tip" style="border: none;" title="Have texted before.">&nbsp;</span>
						</div>
					@else
						<div class='texted no'>
							<span data-tooltip aria-haspopup="true" class="has-tip" style="border: none;" title="Have NOT texted yet.">&nbsp;</span>
						</div>
					@endif
				</a>
			</div>
			@else
			<div class='textIcon not-opted-in'></div>
			@endif

			<div class='ShowNA '>
				N/A
			</div>
		</div>
		@endif

		<!-- applied column -->
		@if( $currentPage != 'admin-verifiedApp' )
		<div class="custom-width column large-1 text-center show-for-large-up applied-star lg">
			<div class="stars applied-star @if(isset($key['applied']) && $key['applied'] == 1) applied @elseif(isset($key['user_applied']) && $key['user_applied'] == 1) user-applied @else no @endif">&#9733;</div>
		</div>
		@endif

		<!-- enrolled column -->
		@if( $currentPage != 'admin-verifiedApp' )
		<div class="custom-width column text-center small-2 medium-2 large-1 show-for-large-up enrolled-star lg @if($currentPage == "admin-verifiedHs" || $currentPage == "admin-verifiedApp") end @endif">
			<div class="stars applied-star @if($key['enrolled'] == 1) enrolled @else no @endif">&#9733;</div>
		</div>
		@endif

		<!-- application status -->
		@if( $currentPage == 'admin-verifiedApp' )
		<div class="column large-1 text-center admin-app-status">
			<span class="@if($key['profile_percent'] == 100) is_complete @endif">{{$key['profile_percent'] or 0}}%</span>
		</div>

		<div class="column small-2">
			<div class="view-app-btn" data-studentid="{{$key['hashed_id']}}">View Application</div>
		</div>
			<!-- college application accepted status -->
			@if( isset($is_organization) && $is_organization )
			<div class="colleges-app-status column small-2 end" data-college-id='{{ isset($org_school_id) ? $org_school_id : null }}'>
				<div {{ isset($key['colleges_accepted_status']) && $key['colleges_accepted_status'] == 'accepted' ? 'data-tooltip' : '' }}  
					class="app-status-btn {{ $key['accepted_status'] == 'accepted' ? 'selected' : '' }} {{ isset($key['colleges_accepted_status']) && $key['colleges_accepted_status'] == 'accepted' ? 'colleges-selected' : '' }}" 
					data-status='accepted' 
					aria-haspopup="true" 
					title="{{ isset($key['colleges_accepted_status']) && $key['colleges_accepted_status'] == 'accepted' ? 'A college has accepted this student.' : '' }}">
						Yes
				</div>

				<div {{ isset($key['colleges_accepted_status']) && $key['colleges_accepted_status'] == 'rejected' ? 'data-tooltip' : '' }} 
					class="app-status-btn {{ $key['accepted_status'] == 'rejected' ? 'selected' : '' }} {{ isset($key['colleges_accepted_status']) && $key['colleges_accepted_status'] == 'rejected' ? 'colleges-selected' : '' }}" 
					data-status='rejected'
					aria-haspopup="true" 
					title="{{ isset($key['colleges_accepted_status']) && $key['colleges_accepted_status'] == 'rejected' ? 'A college has rejected this student.' : '' }}">
						No
				</div>

				<div class="app-status-btn {{ $key['accepted_status'] == 'pending' ? 'selected' : '' }}" 
					data-status='pending'>
						Pending
				</div>
			</div>
			@endif
		@endif

		@if($currentPage == 'admin-removed')
		<!-- restore from list button -->
		<div class="text-center column small-2 medium-1 large-1 end text-center restore-student-col" data-studentid="{{$key['student_user_id']}}">&nbsp;
			<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/restore-button.png" alt=""/>		
		</div>
		@else
		<!-- remove from list button -->
		
		@if($currentPage != "admin-verifiedHs" && $currentPage != "admin-verifiedApp")
		<div class="text-center column small-2 medium-1 large-1 end text-center remove-student-col" data-studentid="{{$key['student_user_id']}}" data-in-pending="@if($currentPage == 'admin-pending') true @else false @endif">
			X
		</div>
		@endif
		@endif


		<!--//////////////////// include normal profile for regualr users , show actionbar profile for Plexuss /////-->
	
			<?php 
				//@include('admin.salesProfilePane')			
				//@include('admin.salesProfilePaneEdit')
				//@include('admin.regStudentProfilePane')  is the student profile pane before NB-112 edits
			?>

	</div><!-- end of inquirie row -->
@endforeach

