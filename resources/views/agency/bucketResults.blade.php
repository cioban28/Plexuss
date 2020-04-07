<?php
	// $inquiries = $inquiry_list;
	if (!isset($inquiries) && isset($inquiry_list)) {
		$inquiries = $inquiry_list;
	}
?>

<div class="hasResults hide" data-total-results="{{ $total_results }}" data-currently-viewing="{{ $current_viewing }}" data-last-results="{{ isset($has_more_results) && $has_more_results == true }}"></div>

@foreach ($inquiries as $key)

	<div class="row item inquirie_row @if( !isset($key['is_notified']) || $key['is_notified'] == false ) unread @else read @endif" data-uid='{{$key['user_id'] or ""}}' data-hashed_id='{{$key['hashed_id'] or ""}}'>
		<!-- student name -->
		{{-- @if( (isset($key['paid']) && $key['paid'] == 1) || ($currentPage == 'agency-pending') ) --}}
	
		<div class="column small-5 medium-3 large-2 messageName text-left clearfix" data-hashedid='{{$key['hashed_id'] or ""}}'>
			<span class='arrow'></span>
			<span class="inquiry-name make-inline">{{$key['name'] or ''}} </span>
		<!--<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/hasNotes.jpg" alt="Plexuss User Has Notes">-->
		</div>

		<!-- student gpa -->
		<div class="text-center column medium-1 messageGPA">
			{{$key['gpa']  or 'N/A'}}
		</div>

		<!-- student Programs Interested in -->
		@if (isset($key['degree']) && isset($key['major']))
		<div class="tip-top column show-for-medium-up programsIntrest text-center" data-tooltip aria-haspopup="true"  title='{{( $key['degree']['display_name'] . ", " . $key['major'] ) }}'>
			{{( $key['degree']['initials'] . ', ' . $key['major'] ) }}
		</div>
		@else
		<div class="tip-top text-center column small-12 medium-2 large-1 show-for-medium-up programsIntrest">
			N/A
		</div>
		@endif
		{{-- $key['degree']['display_name'] . ", " .  --}}
		
		<!-- student Country -->
		<div class="text-center column small-2 medium-1 large-1 show-for-large-up country">
			@if (isset($key['country_code']) && $key['country_code'] != 'N/A')
				<span class="has-tip tip-top country-name-container" data-tooltip aria-haspopup="true" title="{{$key['country_name']}}">
					<div class="flag flag-{{ strtolower($key['country_code']) }}"> </div>
					<div class="countryName">{{ $key['country_name'] }}</div>
				</span>
			@else
				N/A
			@endif
		</div>

		<!-- student date -->
		<div class="text-center column small-12 medium-1 show-for-large-up date">
			{{$key['date'] or ''}}
		</div>

		<!-- message student icon -->
		@if( $currentPage == 'agency-inquiries' || $currentPage == 'agency-approved' || $currentPage == 'agency-opportunities' || $currentPage =='agency-removed' )
		<div class="messageIconArea text-center column small-2 medium-2 large-1 selected">
			<div class='showMessageIcon '>
				<a target='_blank' href="/agency/messages/{{$key['user_id']}}/agency-msg">
					<div class='message showMessageIcon'>&nbsp;</div>
				</a>
			</div>

			<div class='ShowNA '>
				N/A
			</div>
		</div>
		@endif

		<!-- uploads doc -->
		<div class="text-center column small-12 medium-4 large-1 show-for-large-up uploadsDoc">
			
			<ul id="uploadDocs">
			  @if($key['transcript'] == true)
			  <li data-tooltip aria-haspopup="true" title='Transcript' class="uploadDocsSpriteSmall transcript">&nbsp;</li>
			  @endif
			  @if($key['toefl'] == true)
			  <li data-tooltip aria-haspopup="true" title='TOEFL' class="uploadDocsSpriteSmall toefl">&nbsp;</li>
			  @endif
			  @if($key['ielts'] == true)
			  <li data-tooltip aria-haspopup="true" title='IELTS' class="uploadDocsSpriteSmall ielts">&nbsp;</li>
			  @endif
			  @if($key['financial'] == true)
			  <li data-tooltip aria-haspopup="true" title='Financial' class="uploadDocsSpriteSmall financial">&nbsp;</li>
			  @endif
			  @if($key['resume'] == true)
			  <li data-tooltip aria-haspopup="true" title='Resume/CV' class="uploadDocsSpriteSmall resume">&nbsp;</li>
			  @endif
			  @if($key['transcript'] == false && 
			  	  $key['toefl'] == false && 
			  	  $key['ielts'] == false && 
			  	  $key['financial'] == false &&
			  	  $key['resume'] == false)

			  	  <li>&nbsp;</li>
			  @endif


			</ul>
		</div>

		@if( $currentPage == 'agency-applications' )
			<div class="text-center column small-12 medium-1 show-for-large-up status">
				{{$key['profile_percent'] or 0}}%
			</div>
			<div class="column small-2">
				<a class="view-app-btn" target='_blank' href='/view-student-application/{{$key['hashed_id']}}'>View Application</a>
			</div>
		@endif

		<!-- time elapsed -->
		@if( $currentPage == 'agency-opportunities' )
			<div class="text-center column small-12 medium-1 show-for-large-up date">
				{{$key['time_elapsed'] or ''}}
			</div>
		@endif

		@if( $currentPage == 'agency-leads')
		<div class='agency-recruit-status column small-2 end'>
			<div class='recruit-status' data-status='yes'>Yes</div>
			<div class='recruit-status' data-status='no'>No</div>
		</div>
		@endif

		<!-- \\\\\\\\\\\\\\ student profile pane - start /////////////// -->
		<div class='student-profile-data'>
			{{-- Inject student profile in here. --}}
		</div>
		<!-- \\\\\\\\\\\\\\ student profile pane - end /////////////// -->
	</div><!-- end of inquirie row -->
@endforeach