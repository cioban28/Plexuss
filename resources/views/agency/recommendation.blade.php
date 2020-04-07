@extends('agency.master')
@section('content')
<?php
	$inquiries = $inquiry_list;
?>

	<div class="off-canvas-wrap main-managestudents-container" data-offcanvas>
		<div class="inner-wrap @if($is_any_school_live) chat-now-bar-isVisible @endif">
	
			<!-- button to open off canvas menu -->
			<a class="left-off-canvas-toggle hide-for-medium-up mobile-agency-nav-btn"> <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/admin_mobile_nav_icon.jpg" alt=""> {{$title}}</a>
			
			<!-- mobile off canvas menu -->
			<aside class="left-off-canvas-menu hide-for-medium-up">
				<ul class="off-canvas-list">
					<li><label for="">Manage Students</label></li>
					<li class="agency-mobile-nav-item">
						<a class="@if($currentPage == 'agency-inquiries') active @endif" href="/agency/inquiries">Inquiries</a>
						<span class="agency-side-menu-cnt">{{$num_of_inquiry or 0}}</span>
						<span class="agency-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Requests from students who want to be recruited by your school.">?</span>
					</li>
					<li class="agency-mobile-nav-item">
						<a class="@if($currentPage == 'agency-recommendations') active @endif" href="/agency/recommendations">
							Recommended
							<div><small class="recommendation-expiration">Expires in {{$expiresIn or '24 hrs'}}</small></div>
						</a>
						<span class="agency-side-menu-cnt">{{$num_of_recommended or 0}}</span>
						<span class="agency-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students recommended by Plexuss based on your admission criteria. These recommendations expire after 24 hours.">?</span>
						
					</li>
					<li class="agency-mobile-nav-item">
						<a class="@if($currentPage == 'agency-pending') active @endif" href="/agency/pending">Pending</a>
						<span class="agency-side-menu-cnt">{{$num_of_pending or 0}}</span>
						<span class="agency-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students you have requested to recruit from you recommended list.">?</span>
					</li>
					<li class="agency-mobile-nav-item">
						<a class="@if($currentPage == 'agency-approved') active @endif" href="/agency/approved">Approved</a>
						<span class="agency-side-menu-cnt">{{$num_of_approved or 0}}</span>
						<span class="agency-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students you've chosen to recruit. You are able to message these students.">?</span>
					</li>
					<li class="agency-mobile-nav-item">
						<a class="@if($currentPage == 'agency-removed') active @endif" href="/agency/removed">Removed</a>
						<span class="agency-side-menu-cnt">{{$num_of_removed or 0}}</span>
						<span class="agency-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students you've removed from your approved list.">?</span>
					</li>
					<li class="agency-mobile-nav-item">
						<a class="@if($currentPage == 'agency-rejected') active @endif" href="/agency/rejected">Rejected</a>
						<span class="agency-side-menu-cnt">{{$num_of_rejected or 0}}</span>
						<span class="agency-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students you've rejected from your inquiry list.">?</span>
					</li>
				</ul>
			</aside>

			<!-- main content -->
			<section class="main-section">
				
				<!-- \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ full page - start ///////////////////////////////////// -->
				<div class="row inquirieWrapper collapse" data-equalizer data-equalizer-mq="medium-up">

					<!-- \\\\\\\\\\\\\\\\\\\\\ medium up side bar menu - start /////////////////// -->
					<div class="column medium-3 large-2 manage-student-sidebar-menu hide-for-small-only" data-equalizer-watch>
						<!-- Manage Students title -->
						<div class="row">
							<div class="column small-11 small-offset-1 sidebar-menu-header">
								Manage Students
							</div>
						</div>

						<!-- Inquiries tab -->
						@if( $currentPage == 'agency-inquiries' )
						<div class="row manage-students-tab active">
						@else
						<div class="row manage-students-tab">
						@endif
							<div class="column small-7 small-offset-1 manage-tab-text">
								<a href="/agency/inquiries">Inquiries</a>
							</div>
							<div class="column small-2 text-right cnt">
								{{$num_of_inquiry or 0}}
							</div>
							<div class="column small-2 text-center">
								<span class="agency-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Requests from students who want to be recruited by your school.">?</span>
							</div>
						</div>
						
						<!-- Recommended tab -->
						@if( $currentPage == 'agency-recommendations' )
						<div class="row manage-students-tab active">
						@else
						<div class="row manage-students-tab">
						@endif
							<div class="column small-7 small-offset-1 manage-tab-text">
								<a href="/agency/recommendations">Recommended</a>
							</div>
							<div class="column small-2 text-right cnt">
								{{$num_of_recommended or 0}}
							</div>
							<div class="column small-2 text-center">
								<span class="agency-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students recommended by Plexuss based on your admission criteria. These recommendations expire after 24 hours.">?</span>
							</div>
							<div class="column small-11 small-offset-1">
								<small class="recommendation-expiration">Expires in {{$expiresIn or '24 hrs'}}</small>
							</div>
						</div>

						<!-- Pending tab -->
						@if( $currentPage == 'agency-pending' )
						<div class="row manage-students-tab active">
						@else
						<div class="row manage-students-tab">
						@endif
							<div class="column small-6 small-offset-2 manage-tab-text">
								<a href="/agency/pending">- Pending</a>
							</div>
							<div class="column small-2 text-right cnt">
								{{$num_of_pending or 0}}
							</div>
							<div class="column small-2 text-center">
								<span class="agency-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students you have requested to recruit from you recommended list.">?</span>
							</div>
						</div>

						<!-- Approved Students tab -->
						@if( $currentPage == 'agency-approved' )
						<div class="row manage-students-tab active">
						@else
						<div class="row manage-students-tab">
						@endif
							<div class="column small-7 small-offset-1 manage-tab-text">
								<a href="/agency/approved">Approved</a>
							</div>
							<div class="column small-2 text-right cnt">
								{{$num_of_approved or 0}}
							</div>
							<div class="column small-2 text-center">
								<span class="agency-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students you've chosen to recruit. You are able to message these students.">?</span>
							</div>
						</div>

						<!-- Removed Students tab -->
						@if( $currentPage == 'agency-removed' )
						<div class="row manage-students-tab active">
						@else
						<div class="row manage-students-tab">
						@endif
							<div class="column small-7 small-offset-1 manage-tab-text">
								<a href="/agency/removed">Removed</a>
							</div>
							<div class="column small-2 text-right cnt">
								{{$num_of_removed or 0}}
							</div>
							<div class="column small-2 text-center">
								<span class="agency-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students you've removed from your approved list.">?</span>
							</div>
						</div>

						<!-- Rejected Students tab -->
						@if( $currentPage == 'agency-rejected' )
						<div class="row manage-students-tab active">
						@else
						<div class="row manage-students-tab">
						@endif
							<div class="column small-7 small-offset-1 manage-tab-text">
								<a href="/agency/rejected">Rejected</a>
							</div>
							<div class="column small-2 text-right cnt">
								{{$num_of_rejected or 0}}
							</div>
							<div class="column small-2 text-center">
								<span class="agency-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students you've rejected from your inquiry list.">?</span>
							</div>
						</div>
					</div>
					<!-- \\\\\\\\\\\\\\\\\\\\\ medium up side bar menu - end /////////////////// -->

					<!-- \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ main content - start ///////////////////////////////////// -->
					<div class="column small-12 medium-9 large-10 main-manage-students-content" data-equalizer-watch>

						<!-- from recommendations to pending message -->
						@if( $currentPage == 'agency-recommendations' )
						<div class="row pending-msg-and-pagination-row">
							<div class="column small-12">

								<div class="recomm-top-row">
									@if(!$show_filter)
									<a href="#" data-reveal-id="upgrade-acct-modal" class="radius button action-bar-btn recommendation">
										<div class="action-bar-content"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/rfilter.png"></div>
										<div class="action-bar-content">TARGETING</div>
									</a>
									@else
									<a href="/agency/filter" class="radius button action-bar-btn recommendation">
										<div class="action-bar-content"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/rfilter.png"></div>
										<div class="action-bar-content">TARGETING</div>
									</a>
									@endif
								</div>
								
								@if( $show_filter )
								<div class="recomm-top-row recomm-color-key-container">
									<div class="key-row"><div class="color-key-box"></div><span class="color-key-text"><b>Results based on your <u>Advanced Filter</u></b></span></div>
									<div class="key-row"><div class="color-key-box"></div><span class="color-key-text"><b>Results based on Plexuss logic</b></span></div>
								</div>
								@endif
							</div>
						</div>
						@endif

						{{-- Column headers --}}
						<div class="row inquirieHeader @if($currentPage == 'agency-recommendations') recomm @endif">
							<div class="column small-6 medium-4 large-2 name-col">Name</div>
							<div class="column text-center large-1 @if( $currentPage == 'agency-inquiries' || $currentPage == 'agency-approved' ) show-for-large-up @else small-2 medium-1 @endif">GPA</div>
							<!--
							<div class="column text-center large-1 show-for-large-up">SAT</div>
							<div class="column text-center large-1 show-for-large-up">ACT</div>
							-->
							<div class="column text-center medium-4 large-2 show-for-medium-up">Interested In</div>
							<div class="column text-center large-1 show-for-large-up">Country</div>
							<div class="column text-center large-1 show-for-large-up">Date</div>
							<div class="column text-center large-1 show-for-large-up">Uploads</div>
							@if( $currentPage == 'agency-inquiries' || $currentPage == 'agency-approved' )
							<div class="column text-center small-3 medium-2 large-1">Message</div>
							@endif
							<div class="column text-center small-3 medium-2 large-2 end">
								<!--<span class="agency-side-menu-tooltip-icon has-tip show-for-large-up" data-tooltip aria-haspopup="true" title="You can choose to recruit students from this column. <div style='border: thin solid white'>Yes</div> <br> The student will be notified that you would like to recruit them. <div>NO</div> <br> This student will be marked 'no' until they update their profile.">?</span>-->Recruit
							</div>
						</div>
						
						{{-- this is where a loop starts to fill out all the user info --}}
						<!-- each-inquirie-container -->
						<div class="each-inquirie-container" data-page-type="{{$currentPage}}" data-has-results="{{$has_searchResults or ''}}">
						@include('agency.searchResultRecommendation')
						</div>
						<!-- end of each-inquirie-container -->

						<!-- ajax loader -->
						@include('private.includes.ajax_loader')
						<!-- ajax loader -->

						<!-- from recommended to pending msg - start -->
						@if( $currentPage == 'agency-recommendations' )
						<div class="row pending-msg-and-pagination-row pending-msg-on-recommendations">
							<div class="column small-12">
								&#42;Students recruited from this list need to approve your request and will be added to <a href="/agency/pending"><span class="added-to-pending-msg">Pending</span></a>
							</div>
						</div>
						@endif
						<!-- from recommended to pending msg - end -->

					</div>
					<!-- \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ main content - end ///////////////////////////////////// -->
				</div>
				<!-- \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ full page - end ///////////////////////////////////// -->
			</section>

			<!-- click to close off canvas menu when open -->
			<a class="exit-off-canvas"></a>

		</div><!-- end of inner wrap -->

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

		<!-- upgrade acct modal -->
		<div id="upgrade-acct-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
			<div class="row">
				<div class="column small-12 text-right">
					<a class="close-reveal-modal help_helpful_videos_close_icon" aria-label="Close">&#215;</a>
				</div>
			</div>

			<div class="row upgrade-msg-row">
				<div class="column small-12 text-center">
					Upgrade your account to filter your daily student recommendations
				</div>
			</div>

			<div class="row filter-intro-container" data-equalizer>
				<div class="column small-12 medium-4">
					<div class="filter-intro-step" data-equalizer-watch>
						<div class="text-center">1</div>
						<div class="text-center">
							<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/step-1-filter.png" alt="Plexuss">
						</div>
						<div>
							You receive student recommendations daily, but you're looking for certain kinds of students
						</div>
					</div>	
				</div>
				<div class="column small-12 medium-4">
					<div class="filter-intro-step" data-equalizer-watch>
						<div class="text-center">2</div>
						<div class="text-center">
							<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/step-2-filter.png" alt="Plexuss">
						</div>
						<div>
							Choose what you'd like to filter by and save your changes (menu on the left)	
						</div>
					</div>	
				</div>
				<div class="column small-12 medium-4">
					<div class="filter-intro-step" data-equalizer-watch>
						<div class="text-center">3</div>
						<div class="text-center">
							<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/step-3-filter.png" alt="Plexuss">
						</div>
						<div>
							Based on your filters, you will receive recommendations that may be a better fit for your school	
						</div>
					</div>	
				</div>
			</div>

			<div class="row upgrade-or-naw-btn-row">
				<div class="column small-12 medium-6 large-5 large-offset-1 text-right">
					<a href="" data-reveal-id="thankyou-for-upgrading-modal" onClick="Plex.inquiries.requestToBecomeMember();" class="radius button">I'd like to upgrade my account</a>
				</div>
				<div class="column small-12 medium-6 large-5 end">
					<a href="" class="radius button secondary close-reveal-modal" aria-label="Close">I'll think about it</a>
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
					<div>Sina or Molly will contact you very soon to get you set up with your new account.</div>
					<div>(We're working on giving you a place to manage upgrading your account in the future, so thank you for your patience.)</div>
				</div>
			</div>
			<div class="row">
				<div class="column medium-8 large-6 medium-centered text-center">
					<a href="" class="radius button secondary close-reveal-modal" aria-label="Close">Looking forward to it ;)</a>
				</div>
			</div>
		</div>

	</div><!-- end of off canvas wrap -->

@stop
