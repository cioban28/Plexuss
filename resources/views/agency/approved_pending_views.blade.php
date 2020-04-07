@extends('agency.master')
@section('content')
<?php
	$inquiries = $inquiry_list;
	// dd($data);
?>

	<div class="off-canvas-wrap main-managestudents-container" data-offcanvas>
		<div class="inner-wrap @if($is_any_school_live) chat-now-bar-isVisible @endif">
	
			<!-- button to open off canvas menu -->
			<div class="mobile-appr-btn-list clearfix hide-for-medium-up">
				<div class="left">
					<a class="left-off-canvas-toggle hide-for-medium-up mobile-agency-nav-btn"> <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/admin_mobile_nav_icon.jpg" alt=""> {{$title}}</a>
				</div>
				<div class="right ms-btns group-msg-btn"><img src="" /> CREATE CAMPAIGN (<span class="chosen-count-display">0</span>)</div>
			</div>
			
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
						@if( $currentPage == 'agency-recommended' )
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
								&#42;These recommendations expire daily
							</div>
						</div>
						@elseif( $currentPage == 'agency-approved' )
						<div class="appr-btn-list clearfix show-for-medium-up">
							<div class="left ms-btns group-msg-btn"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/message.png" />&nbsp;CREATE CAMPAIGN (<span class="chosen-count-display">0</span>)</div>
							<div class="left ms-btns"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/export.png" />&nbsp;EXPORT STUDENTS</div>
						</div>
						@endif

						{{-- Column headers --}}
						<div class="row inquirieHeader @if($currentPage == 'agency-approved') appr @endif">
							<div class="column small-1 chkbox-header-col text-right">{{Form::checkbox('name', 'all', false, array('class' => 'student-row-chkbx-all'))}}</div>
							<div class="column small-5 medium-3 large-2 name-col">Name</div>
							<div class="column small-2 medium-2 text-center hide-for-large-up"><span data-tooltip aria-haspopup="true" class="has-tip sm" title="Use the applied column to indicate which students have applied. Plexuss uses this data to improve targeting and recommendation.">Applied</span></div>
							<div class="column text-center large-1 @if( $currentPage == 'agency-inquiries' || $currentPage == 'agency-approved' ) show-for-large-up @else small-2 medium-1 @endif">GPA</div>
							<div class="column text-center medium-2 large-1 show-for-medium-up">Interested In</div>
							<div class="column text-center large-1 show-for-large-up">Country</div>
							<div class="column text-center large-1 show-for-large-up">Date</div>
							<div class="column text-center large-1 show-for-large-up">Uploads</div>
							@if( $currentPage == 'agency-inquiries' || $currentPage == 'agency-approved' )
							<div class="column text-center small-2 medium-2 large-1">Message</div>
							@endif
							<div class="column large-1 text-center show-for-large-up">Applied <span data-tooltip aria-haspopup="true" class="has-tip lg" title="Use the applied column to indicate which students have applied. Plexuss uses this data to improve targeting and recommendation.">?</span></div>
							<div class="column text-center small-2 medium-2 large-1 end">
								@if($currentPage == 'agency-removed')
									Restore
								@else
									Remove
								@endif
							</div>
						</div>
						
						{{-- this is where a loop starts to fill out all the user info --}}
						<!-- each-inquirie-container -->
						<div class="each-inquirie-container" data-page-type="{{$currentPage}}" data-has-results="{{$has_searchResults or ''}}">
						@include('agency.searchResultApproved')
						</div>
						<!-- end of each-inquirie-container -->

						<!-- ajax loader -->
						@include('private.includes.ajax_loader')
						<!-- ajax loader -->
						
						<!-- from recommended to pending msg - start -->
						@if( $currentPage == 'agency-recommendations' )
						<div class="row pending-msg-and-pagination-row pending-msg-on-recommendations">
							<div class="column small-12">
								&#42;Students recruited from this list need to approve your request and will be added to <span class="added-to-pending-msg">Pending</span>
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

		<!-- applied student reminder -->
		<div id="applied-student-reminder-modal" class="reveal-modal" data-reveal data-options="close_on_background_click: false" data-remind="{{$applied_reminder or 0}}">
			<div class="clearfix">
				<div class="right">
					<a class="close-reveal-modal help_helpful_videos_close_icon">&#215;</a>
				</div>
			</div>

			<div class="row reminder">
				<div class="column small-6 medium-7 large-8">
					<h3>Have any new students applied?</h3>
					<br />
					<p>Use the applied column to indicate which students have applied.</p>
					<p>Plexuss uses this data to improve targeting and recommendation.</p>
				</div>
				<div class="column small-6 medium-5 large-4 text-center">
					<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/appliedThumnail.jpg" alt="Applied thumbnail">
				</div>
			</div>

			<br />

			<div class="row">
				<div class="column small-6 medium-5 medium-offset-1 large-offset-3 large-3">
					<div class="ok-btn text-center modal-btn">Ok</div>
				</div>
				<div class="column small-6 medium-5 large-3 end">
					<div class="remind-later-btn text-center modal-btn">Remind me later</div>
				</div>
			</div>
		</div>

		<!-- text message error modal -->
		<div id="text-message-error-modal" class="reveal-modal" data-reveal>
			<div class="row">
				<div class="column small-12 small-text-right">
					<a class="close-reveal-modal help_helpful_videos_close_icon">&#215;</a>
				</div>
			</div>
			<div class="row">
				<div class="column small-12 small-text-center">
					Sorry, none of the selected students have opt-in to receive text message. Please try another user(s).
				</div>
			</div>
		</div>

	</div><!-- end of off canvas wrap -->

@stop
