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
						<a class="@if($currentPage == 'agency-leads') active @endif" href="/agency/inquiries">Leads</a>
						<span class="agency-side-menu-cnt">{{$num_of_leads or 0}}</span>
						<span class="agency-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students that have the most complete applications from the same country as the agent">?</span>
					</li>
					<li class="agency-mobile-nav-item">
						<a class="@if($currentPage == 'agency-opportunities') active @endif" href="/agency/inquiries/opportunities">Opportunities</a>
						<span class="agency-side-menu-cnt">{{$num_of_opportunities or 0}}</span>
						<span class="agency-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students the agent has promoted from leads">?</span>
					</li>
					<li class="agency-mobile-nav-item">
						<a class="@if($currentPage == 'agency-applications') active @endif" href="/agency/inquiries/applications">Applications</a>
						<span class="agency-side-menu-cnt">{{$num_of_applications or 0}}</span>
						<span class="agency-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students that have fully completed an application and have submitted them to the college">?</span>
					</li>
					<li class="agency-mobile-nav-item">
						<a class="@if($currentPage == 'agency-removed') active @endif" href="/agency/inquiries/removed">Removed</a>
						<span class="agency-side-menu-cnt">{{$num_of_removed or 0}}</span>
						<span class="agency-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students you've removed">?</span>
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
						@if( $currentPage == 'agency-leads' )
						<div class="row manage-students-tab active">
						@else
						<div class="row manage-students-tab">
						@endif
							<div class="column small-7 small-offset-1 manage-tab-text">
								<a href="/agency/inquiries">Leads</a>
							</div>
							<div id='num_of_leads' class="column small-2 text-right cnt">
								{{$num_of_leads or 0}}
							</div>
							<div class="column small-2 text-center">
								<span class="agency-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Requests from students who want to be recruited by your school.">?</span>
							</div>
						</div>

						<!-- Opportunities tab -->
						@if( $currentPage == 'agency-opportunities' )
						<div class="row manage-students-tab active">
						@else
						<div class="row manage-students-tab">
						@endif
							<div class="column small-7 small-offset-1 manage-tab-text">
								<a href="/agency/inquiries/opportunities">Opportunities</a>
							</div>
							<div id='num_of_opportunities' class="column small-2 text-right cnt">
								{{$num_of_opportunities or 0}}
							</div>
							<div class="column small-2 text-center">
								<span class="agency-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students you've chosen to recruit. You are able to message these students.">?</span>
							</div>
						</div>

						<!-- Applications tab -->
						@if( $currentPage == 'agency-applications' )
						<div class="row manage-students-tab active">
						@else
						<div class="row manage-students-tab">
						@endif
							<div class="column small-7 small-offset-1 manage-tab-text">
								<a href="/agency/inquiries/applications">Completed Applications</a>
							</div>
							<div id='num_of_applications' class="column small-2 text-right cnt">
								{{$num_of_applications or 0}}
							</div>
							<div class="column small-2 text-center">
								<span class="agency-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students you've chosen to recruit. You are able to message these students.">?</span>
							</div>
						</div>

						<!-- Removed Students tab -->
						@if( $currentPage == 'agency-removed' )
						<div class="row manage-students-tab removed active">
						@else
						<div class="row manage-students-tab removed">
						@endif
							<div class="column small-7 small-offset-1 manage-tab-text">
								<a href="/agency/inquiries/removed">Removed</a>
							</div>
							<div id='num_of_removed' class="column small-2 text-right cnt">
								{{$num_of_removed or 0}}
							</div>
							<div class="column small-2 text-center">
								<span class="agency-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students you've removed from your approved list.">?</span>
							</div>
						</div>
						
					</div>
					<!-- \\\\\\\\\\\\\\\\\\\\\ medium up side bar menu - end /////////////////// -->

					<!-- \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ main content - start ///////////////////////////////////// -->
					<div class="column small-12 medium-9 large-10 main-manage-students-content" data-equalizer-watch>
						<div class='agency-inquiry-options'>
							@if ($currentPage == 'agency-leads')
								<div class='request-leads-btn'>Request Leads</div>
							@else
								<div class='invisible-div'></div>
							@endif
							<div class="right filterResult-cont">
                   				<!-- filter audience btn -->
								<a class="ms-btns filter-audience filter-audience-s">
                                    <img class="filter-audience-img" src="/images/setting/filter-white.png">
                                    <div class="filter-text">&nbsp;FILTER AUDIENCE</div>
                                </a>

                                <!-- results per page -->
								<label class="results-label">
									<span class="results-title">Results per page:</span>  
									{{Form::select('display_option', array('15' => 15 , '30' => 30, '50' => 50, '100' => 100, '200' => 200),(isset($display_option) && $display_option != 15)? (string)$display_option : '15', array('id' => 'displayOption', 'class' => 'display-option results-select'))}}
								</label>

							</div>
						</div>
						@include('agency.agencyFilterOptions')	
						
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
						{{-- <div class='inquirie-header-container'>
							<div class='header-top-section'>
								<div class='request-leads-btn'>
									Request Leads
								</div>

								<div class='advanced-filter'>
	                                <a class="ms-btns filter-audience filter-audience-s">
	                                    <img class="filter-audience-img" src="/images/setting/filter-white.png">
	                                    <div class="filter-text">&nbsp;FILTER AUDIENCE</div>
	                                </a>
									<label class="results-label">
										<span class="results-title">Results per page:</span>  
										<select id="displayOption" class="display-option results-select" name="display_option"><option value="15" selected="selected">15</option><option value="30">30</option><option value="50">50</option><option value="100">100</option><option value="200">200</option></select>
									</label>
								</div>
							</div>
						</div> --}}
						<div class="row inquirieHeader @if($currentPage == 'agency-recommendations') recomm @endif">
							<div class='column person-icon sprite small-2 medium-1'></div>
							<div class="column small-6 medium-4 large-2 name-col"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"name", "sortBy": "ASC"}'>Name</div>
							<div class="column text-center large-1 gpa-col @if( $currentPage == 'agency-inquiries' || $currentPage == 'agency-approved' ) show-for-large-up @else small-2 medium-1 @endif"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"gpa", "sortBy": "ASC"}'>GPA</div>
							<!--
							<div class="column text-center large-1 show-for-large-up">SAT</div>
							<div class="column text-center large-1 show-for-large-up">ACT</div>
							-->
							<div class="column text-center medium-4 large-2 show-for-medium-up major-col"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"major", "sortBy": "ASC"}'>Interested In</div>
							<div class="column text-center large-1 show-for-large-up country-col"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"country", "sortBy": "ASC"}'>Country</div>
							<div class="column text-center large-1 show-for-large-up date-col"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"date", "sortBy": "ASC"}'>Date</div>

							@if( $currentPage == 'agency-opportunities' || $currentPage == 'agency-removed' )
							<div class="column text-center large-1 show-for-large-up message-col">Message</div>
							@endif

							<div class="column text-center large-1 show-for-large-up @if ( $currentPage == 'agency-removed' ) end @endif"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"uploads", "sortBy": "ASC"}'>Uploads</div>

							@if( $currentPage == 'agency-applications' )
							<div class="column text-center large-1 show-for-large-up status-col end">Status</div>
							@endif

							@if( $currentPage == 'agency-opportunities' )
							<div class="column text-center large-1 show-for-large-up time-elapsed-col end"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"date", "sortBy": "ASC"}'>Time Elapsed</div>
							@endif

							@if( $currentPage == 'agency-leads' )
							<div class="column text-center small-3 medium-2 large-2 recruit-col end">
								Recruit
								<span class="agency-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Selecting <b style='font-size:14px'>Yes</b> will move the student to Opportunities. Selecting <b style='font-size:14px'>No</b> will move the student to Removed.">?</span>
							</div>
							@endif
						</div>
						{{-- this is where a loop starts to fill out all the user info --}}
						<!-- each-inquirie-container -->
						<div class="each-inquirie-container" data-page-type="{{$currentPage}}" data-has-results="{{$has_searchResults or ''}}">
						@include('agency.bucketResults')
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

		<!-- Send to Removed bucket modal, only required if no note is left -->
		<div id="student-removal-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
			<a class="close-reveal-modal" aria-label="Close">&#215;</a>
			<h5>Please enter a reason for removal</h5>
			<form>
				<textarea rows='5' cols='10'></textarea>
				<button class='confirm-student-removal' type='submit'>Remove</button>
			</form>
		</div>

		<!-- Send to Removed bucket modal, only required if no note is left -->
		<div id="request-leads-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
			<a class="close-reveal-modal" aria-label="Close">&#215;</a>
			<div id='leads-request-successful'>
				<h5><b>You have successfully acquired the following</b></h5>
				<table>
					<tr>
						<td><b># Leads</b></td>
						<td class='new-leads-number'>10</td>
					</tr>
					<tr>
						<td><b># Completed Apps</b></td>
						<td class='new-applications-number'>20</td>
					</tr>
				</table>
				<div class='reload-btn'>View New Leads</div>
			</div>
			<div id='leads-request-failure'>
				<h5 class='error-msg'></h5>
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
