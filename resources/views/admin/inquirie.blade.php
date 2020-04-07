@extends('admin.master')
@section('content')
<?php
	$inquiries = $inquiry_list;
	// dd($currentPage);
?>

	<div class="off-canvas-wrap main-managestudents-container" data-offcanvas>
		@if( (isset($webinar_is_live) && $webinar_is_live == true) || (isset($is_any_school_live) && $is_any_school_live == true) )
		<div class="inner-wrap chat-now-bar-isVisible">
		@else
		<div class="inner-wrap">
		@endif
	
			<!-- button to open off canvas menu -->
			<div class="mobile-appr-btn-list hide-for-medium-up clearfix hide-for-medium-up">
				<div class="left">
					<a class="left-off-canvas-toggle hide-for-medium-up mobile-admin-nav-btn"> <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/admin_mobile_nav_icon.jpg" alt=""> {{$title}}</a>
				</div>
				<!-- <div class="right ms-btns group-msg-btn"> MESSAGE STUDENTS (<span class="chosen-count-display">0</spen>)</div> -->
				
			</div>
			
			<!-- mobile off canvas menu -->
			<aside class="left-off-canvas-menu hide-for-medium-up">
				<ul class="off-canvas-list">
					<li><label for="manageStu">Manage Students</label></li>
					<li class="admin-mobile-nav-item">
						<a class="@if($currentPage == 'admin-inquiries') active @endif" href="/admin/inquiries">Inquiries</a>
						<span class="admin-side-menu-cnt">{{$num_of_inquiry or 0}}</span>
						<span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Requests from students who want to be recruited by your school.">?</span>
					</li>

                    @if (isset($default_organization_portal->ro_id))
                        <li class="admin-mobile-nav-item">
                            <a class="@if($currentPage == 'admin-converted') active @endif" href="/admin/inquiries/converted">Converted</a>
                            <span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Place a pixel on your registration or application page to track conversion.">?</span>
                        </li>
                    @endif

                    @if (isset($is_admin_premium) && $is_admin_premium) 
    					<li class="admin-mobile-nav-item">
    						<a class="@if($currentPage == 'admin-recommendations') active @endif" href="/admin/inquiries/recommendations">
    							Recommended
    							<div><small class="recommendation-expiration">Expires in {{$expiresIn or '24 hrs'}}</small></div>
    						</a>
    						<span class="admin-side-menu-cnt">{{$num_of_recommended or 0}}</span>
    						<span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students recommended by Plexuss based on your admission criteria. These recommendations expire after 24 hours.">?</span>
    						
    					</li>
    					<li class="admin-mobile-nav-item">
    						<a class="@if($currentPage == 'admin-pending') active @endif" href="/admin/inquiries/pending">Pending</a>
    						<span class="admin-side-menu-cnt">{{$num_of_pending or 0}}</span>
    						<span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students you have requested to recruit from you recommended list.">?</span>
    					</li>
    					<li class="admin-mobile-nav-item">
    						<a class="@if($currentPage == 'admin-approved') active @endif" href="/admin/inquiries/approved">Handshakes</a>
    						<span class="admin-side-menu-cnt">{{$num_of_approved or 0}}</span>
    						<span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students you've chosen to recruit. You are able to message these students.">?</span>
    					</li>
    					<li class="admin-mobile-nav-item">
    						<a class="@if($currentPage == 'admin-verifiedHs') active @endif" href="/admin/inquiries/verifiedHs">Verified Handshake</a>
    						<span class="admin-side-menu-cnt">{{$num_of_verified_hs or 0}}</span>
    						<span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Verified handshake students.">?</span>
    					</li>
    					<li class="admin-mobile-nav-item">
    						<a class="@if($currentPage == 'admin-prescreened') active @endif" href="/admin/inquiries/prescreened" style="color: #24b26b">Prescreened</a>
    						<span class="admin-side-menu-cnt">{{$num_of_prescreened or 0}}</span>
    						<span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students you've prescreened from your inquiry list.">?</span>
    					</li>
    					<li class="admin-mobile-nav-item">
    						<a class="@if($currentPage == 'admin-verifiedApp') active @endif" href="/admin/inquiries/verifiedApp">Verified Application</a>
    						<span class="admin-side-menu-cnt">{{$num_of_verified_app or 0}}</span>
    						<span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Verified application students.">?</span>
    					</li>
                        <li class="admin-mobile-nav-item">
                            <a class="@if($currentPage == 'admin-rejected') active @endif" href="/admin/inquiries/rejected">Rejected</a>
                            <span class="admin-side-menu-cnt">{{$num_of_rejected or 0}}</span>
                            <span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students you've rejected from your inquiry list.">?</span>
                        </li>
                    @endif
					<li class="admin-mobile-nav-item">
						<a class="@if($currentPage == 'admin-removed') active @endif" href="/admin/inquiries/removed">Removed</a>
						<span class="admin-side-menu-cnt">{{$num_of_removed or 0}}</span>
						<span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students you've removed from your approved list.">?</span>
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
						@if( $currentPage == 'admin-inquiries' )
						<div class="row manage-students-tab active">
						@else
						<div class="row manage-students-tab">
						@endif
							<div class="column small-7 small-offset-1 manage-tab-text">
								<a href="/admin/inquiries">Inquiries</a>
							</div>
							<div class="column small-2 text-right cnt num_of_inquiry">
								{{$num_of_inquiry or 0}}
							</div>
							<div class="column small-2 text-center">
								<span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Requests from students who want to be recruited by your school.">?</span>
							</div>
						</div>

                        <!-- Converted tab -->
                        {{-- @if (isset($default_organization_portal->ro_type) && $default_organization_portal->ro_type == 'click') --}}
                            @if( $currentPage == 'admin-converted' )
                            <div class="row manage-students-tab active">
                            @else
                            <div class="row manage-students-tab">
                            @endif
                                <div class="column small-7 small-offset-1 manage-tab-text">
                                    <a href="/admin/inquiries/converted">Converted</a>
                                </div>
                                <div class="column small-2 text-right cnt num_of_converted">
                                    {{$num_of_converted or 0}}
                                </div>
                                <div class="column small-2 text-center">
                                    <span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Place a pixel on your registration or application page to track conversion.">?</span>
                                </div>
                            </div>
                        {{-- @endif --}}

                        @if (isset($is_admin_premium) && $is_admin_premium) 
    						<!-- Recommended tab -->
    						@if( $currentPage == 'admin-recommendations' )
    						<div class="row manage-students-tab active">
    						@else
    						<div class="row manage-students-tab">
    						@endif
    							<div class="column small-7 small-offset-1 manage-tab-text">
    								<a href="/admin/inquiries/recommendations">Recommended</a>
    							</div>
    							<div class="column small-2 text-right cnt">
    								{{$num_of_recommended or 0}}
    							</div>
    							<div class="column small-2 text-center">
    								<span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students recommended by Plexuss based on your admission criteria. These recommendations expire after 24 hours.">?</span>
    							</div>
    							<div class="column small-11 small-offset-1">
    								<small class="recommendation-expiration">Expires in {{$expiresIn or '24 hrs'}}</small>
    							</div>
    						</div>

    						<!-- Pending tab -->
    						@if( $currentPage == 'admin-pending' )
    						<div class="row manage-students-tab active">
    						@else
    						<div class="row manage-students-tab">
    						@endif
    							<div class="column small-6 small-offset-2 manage-tab-text">
    								<a href="/admin/inquiries/pending">- Pending</a>
    							</div>
    							<div class="column small-2 text-right cnt">
    								{{$num_of_pending or 0}}
    							</div>
    							<div class="column small-2 text-center">
    								<span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students you have requested to recruit from you recommended list.">?</span>
    							</div>
    						</div>

    						<!-- Approved Students tab -->
    						@if( $currentPage == 'admin-approved' )
    						<div class="row manage-students-tab active">
    						@else
    						<div class="row manage-students-tab">
    						@endif
    							<div class="column small-7 small-offset-1 manage-tab-text">
    								<a href="/admin/inquiries/approved">Handshakes</a>
    							</div>
    							<div class="column small-2 text-right cnt">
    								{{$num_of_approved or 0}}
    							</div>
    							<div class="column small-2 text-center">
    								<span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students you've chosen to recruit. You are able to message these students.">?</span>
    							</div>
    						</div>

    						<!-- Verified Handshake Students tab -->
    						@if( $currentPage == 'admin-verifiedHs' )
    						<div class="row manage-students-tab active">
    						@else
    						<div class="row manage-students-tab">
    						@endif
    							<div class="column small-7 small-offset-1 manage-tab-text">
    								<a href="/admin/inquiries/verifiedHs">Verified Handshake</a>
    							</div>
    							<div class="column small-2 text-right cnt verifiedHsCnt-total">
    								{{$num_of_verified_hs or 0}}
    							</div>
    							<div class="column small-2 text-center">
    								<span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Verified handshake students">?</span>
    							</div>
    						</div>

    						<!-- Prescreened Students tab -->
    						@if( isset($num_of_prescreened) && $num_of_prescreened > 0 )
    							@if( $currentPage == 'admin-prescreened' )
    							<div class="row manage-students-tab active" style="border-left: 5px solid #24b26b">
    							@else
    							<div class="row manage-students-tab">
    							@endif
    								<div class="column small-7 small-offset-1 manage-tab-text">
    									<a href="/admin/inquiries/prescreened" style="color: #24b26b">Prescreened</a>
    								</div>
    								<div class="column small-2 text-right cnt verifiedPCnt-total" style="color: #24b26b">
    									{{$num_of_prescreened or 0}}
    								</div>
    								<div class="column small-2 text-center">
    									<span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students that have been preselected for you through our screening process">?</span>
    								</div>
    							</div>
    						@endif

    						<!-- Verified Application Students tab -->
    						@if( $currentPage == 'admin-verifiedApp' )
    						<div class="row manage-students-tab active">
    						@else
    						<div class="row manage-students-tab">
    						@endif
    							<div class="column small-7 small-offset-1 manage-tab-text">
    								<a href="/admin/inquiries/verifiedApp">Verified Application</a>
    							</div>
    							<div class="column small-2 text-right cnt verifiedACnt-total">
    								{{$num_of_verified_app or 0}}
    							</div>
    							<div class="column small-2 text-center">
    								<span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Verified application students">?</span>
    							</div>
    						</div>
                            <!-- Rejected Students tab -->
                            @if( $currentPage == 'admin-rejected' )
                            <div class="row manage-students-tab active">
                            @else
                            <div class="row manage-students-tab">
                            @endif
                                <div class="column small-7 small-offset-1 manage-tab-text">
                                    <a href="/admin/inquiries/rejected">Rejected</a>
                                </div>
                                <div class="column small-2 text-right cnt">
                                    {{$num_of_rejected or 0}}
                                </div>
                                <div class="column small-2 text-center">
                                    <span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students you've rejected from your inquiry list.">?</span>
                                </div>
                            </div>
                        @endif
						<!-- Removed Students tab -->
						@if( $currentPage == 'admin-removed' )
						<div class="row manage-students-tab active">
						@else
						<div class="row manage-students-tab">
						@endif
							<div class="column small-7 small-offset-1 manage-tab-text">
								<a href="/admin/inquiries/removed">Removed</a>
							</div>
							<div class="column small-2 text-right cnt">
								{{$num_of_removed or 0}}
							</div>
							<div class="column small-2 text-center">
								<span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students you've removed from your approved list.">?</span>
							</div>
						</div>
{{-- 
                        <div class="row email-support-notice">
                            <div>Email <a href="mailto:collegeservices@plexuss.com">collegeservices@plexuss.com</a> with any questions or concerns</div>
                        </div> --}}
					</div>
					<!-- \\\\\\\\\\\\\\\\\\\\\ medium up side bar menu - end /////////////////// -->

					<!-- \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ main content - start ///////////////////////////////////// -->
					<div class="column small-12 medium-9 large-10 main-manage-students-content" data-equalizer-watch>

						<!-- from recommendations to pending message -->
						@if( $currentPage == 'admin-inquiries' || $currentPage == 'admin-rejected' || $currentPage == 'admin-prescreened')
							@if( $currentPage == 'admin-prescreened' )
							<div class="row appr-btn-list show-for-medium-up clearfix" > <!--style="margin: 15px 0"-->
								<div class="column medium-6 large-6 left-btns-app-pend">
									<div class="row left-side">							
										@if(isset($is_aor) && $is_aor == 0)
										<a class="ms-btns group-msg-btn">
											<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/message.png">&nbsp;CREATE CAMPAIGN (<span class="chosen-count-display">0</span>)
										</a>
										<a class="ms-btns text-msg-btn">
											<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/text_messages.png">&nbsp;TEXT STUDENTS (<span class="chosen-count-display">0</span>)
										</a>
										@endif
									</div>
								</div>
							@else
							<div class="row appr-btn-list show-for-medium-up clearfix">
							@endif
                                @if( $currentPage !== 'admin-rejected')
                                    <div class="left">
                                        <div class='auto-dialer-button'>
                                            <div>Auto Dial</div>
                                            <div class='auto-dialer-icon'></div>
                                        </div>
                                    </div>
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

							@include('admin.filterOptions')	

						@elseif( $currentPage == 'admin-recommendations' )
						<div class="row pending-msg-and-pagination-row">
							<div class="column small-12">

								<div class="recomm-top-row">
									@if(!$show_filter)
									<a href="#" data-reveal-id="upgrade-acct-modal" class="radius button action-bar-btn recommendation">
										<div class="action-bar-content"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/rfilter.png" alt=""></div>
										<div class="action-bar-content">TARGETING</div>
									</a>
									@else
									<a href="/admin/filter" class="radius button action-bar-btn recommendation">
										<div class="action-bar-content"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/rfilter.png" alt=""></div>
										<div class="action-bar-content">TARGETING</div>
									</a>
									@endif
								</div>
								
								@if( $show_filter )
								<div class="recomm-top-row recomm-color-key-container">
									<div class="key-row"><div class="color-key-box"></div><span class="color-key-text"><b>Results based on your <a href="/admin/studentsearch" target="_blank"><u>Advanced Filter</u></a></b></span></div>
									<div class="key-row"><div class="color-key-box"></div><span class="color-key-text"><b>Results based on Plexuss logic</b></span></div>
								</div>
								@endif

								@if(isset($show_upgrade_button) && $show_upgrade_button == 1)
								<div class="recomm-top-row">
									<span class="upgradeNotify">Upgrade</span>
									<span> to Premier to receive more than 5 recommendations a day</span>
								</div>
								@endif

								<div class="recomm-top-row show-for-medium-up right">
									<label>
										Results per page:  
										{{Form::select('display_option', array('15' => 15 , '30' => 30, '50' => 50, '100' => 100, '200' => 200),(isset($display_option) && $display_option != 15)? (string)$display_option : '15', array('id' => 'displayOption', 'class' => 'display-option'))}}
									</label>
								</div>
							
							</div>
						</div>
						@endif

                        @include('admin.contactPane.autoDialerRow')

                        @include('admin.contactPane.incomingCallModal')

                        @include('admin.contactPane.transferCallModal')

                        @include('admin.contactPane.postingStudentModal')

						{{-- Column headers --}}
						@if( $currentPage != 'admin-prescreened' && $currentPage != 'admin-inquiries' )
							<div class="row inquirieHeader @if($currentPage == 'admin-recommendations') recomm @endif">
								<div class="column small-5 medium-3 large-2 name-col messageName"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"name","sortBy":"@if(isset($column_orders) && $column_orders["orderBy"] == "name"){{$column_orders["sortBy"] or "DESC"}}@else DESC @endif"}'>Name</div>
								<div class="column text-center large-1 @if( $currentPage == 'admin-inquiries' || $currentPage == 'admin-approved' ) show-for-large-up @else small-2 medium-1 @endif">GPA</div>
								<!--
								<div class="column text-center large-1 show-for-large-up">SAT</div>
								<div class="column text-center large-1 show-for-large-up">ACT</div>
								-->
								<div class="column text-center medium-4 large-2 show-for-medium-up programsIntrest"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"major","sortBy":"@if(isset($column_orders) && $column_orders["orderBy"] == "major"){{$column_orders["sortBy"] or "DESC"}}@else DESC @endif"}'>Degree Interested In</div>
								<div class="column text-center large-1 show-for-large-up country-col countryCol"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"country","sortBy":"@if(isset($column_orders) && $column_orders["orderBy"] == "country"){{$column_orders["sortBy"] or "DESC"}}@else DESC @endif"}'>Country</div>
								<div class="column text-center large-1 show-for-large-up"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"date","sortBy":"@if(isset($column_orders) && $column_orders["orderBy"] == "date"){{$column_orders["sortBy"] or "DESC"}}@else DESC @endif"}'>Date</div>
								<div class="column text-center large-1 show-for-large-up"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"uploads","sortBy":"@if(isset($column_orders) && $column_orders["orderBy"] == "uploads"){{$column_orders["sortBy"] or "ASC"}}@else ASC @endif"}'>Uploads</div>
								@if( $currentPage == 'admin-inquiries' || $currentPage == 'admin-approved' )
								<div class="column text-center small-3 medium-2 large-1">Message</div>
								@endif
								<div class="column text-center small-4 medium-3 large-2 end">
									<!--<span class="admin-side-menu-tooltip-icon has-tip show-for-large-up" data-tooltip aria-haspopup="true" title="You can choose to recruit students from this column. <div style='border: thin solid white'>Yes</div> <br> The student will be notified that you would like to recruit them. <div>NO</div> <br> This student will be marked 'no' until they update their profile.">?</span>-->Recruit
								</div>
							</div>
						@endif

                        @if( $currentPage == 'admin-inquiries' )
                            <div class="row inquirieHeader @if($currentPage == 'admin-recommendations') recomm @endif">
                                <div class="column small-5 medium-3 large-2 name-col messageName"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"name","sortBy":"@if(isset($column_orders) && $column_orders["orderBy"] == "name"){{$column_orders["sortBy"] or "DESC"}}@else DESC @endif"}'>Name</div>
                                <div class="column text-center large-1 @if( $currentPage == 'admin-inquiries' || $currentPage == 'admin-approved' ) show-for-large-up @else small-2 medium-1 @endif">GPA</div>
                                <!--
                                <div class="column text-center large-1 show-for-large-up">SAT</div>
                                <div class="column text-center large-1 show-for-large-up">ACT</div>
                                -->
                                <div class="column text-center medium-4 large-2 show-for-medium-up programsIntrest" style="margin-top: -0.4em;"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"major","sortBy":"@if(isset($column_orders) && $column_orders["orderBy"] == "major"){{$column_orders["sortBy"] or "DESC"}}@else DESC @endif"}'>Degree Interested In</div>
                                <div class="column text-center large-1 show-for-large-up country-col countryCol header"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"country","sortBy":"@if(isset($column_orders) && $column_orders["orderBy"] == "country"){{$column_orders["sortBy"] or "DESC"}}@else DESC @endif"}'>Country</div>
                                <div class="column text-center large-1 show-for-large-up"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"date","sortBy":"@if(isset($column_orders) && $column_orders["orderBy"] == "date"){{$column_orders["sortBy"] or "DESC"}}@else DESC @endif"}'>Date</div>
                                <div class="column text-center large-1 show-for-large-up"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"uploads","sortBy":"@if(isset($column_orders) && $column_orders["orderBy"] == "uploads"){{$column_orders["sortBy"] or "ASC"}}@else ASC @endif"}'>Uploads</div>
                                @if( $currentPage == 'admin-inquiries' || $currentPage == 'admin-approved' )
                                <div class="column text-center small-3 medium-2 large-1">Message</div>
                                @endif
                                
                                <div class="column large-1 text-center show-for-large-up"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"applied","sortBy":"@if(isset($column_orders) && $column_orders["orderBy"] == "applied"){{$column_orders["sortBy"] or "DESC"}}@else DESC @endif"}'>Applied <span data-tooltip data-width="150" aria-haspopup="true" class="has-tip tip-right lg" title="Use the applied column to indicate which students have applied. Plexuss uses this data to improve targeting and recommendation.">?</span></div>

                                <div class="column large-1 text-center show-for-large-up"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"enrolled","sortBy":"@if(isset($column_orders) && $column_orders["orderBy"] == "enrolled"){{$column_orders["sortBy"] or "DESC"}}@else DESC @endif"}'>Enrolled <span data-tooltip data-width="150" aria-haspopup="true" class="has-tip tip-left lg" title="Use the enrolled column to indicate which students have enrolled. Plexuss uses this data to improve targeting and recommendation.">?</span></div>

                                <div class="column text-center small-4 medium-3 large-1 end">
                                    <!--<span class="admin-side-menu-tooltip-icon has-tip show-for-large-up" data-tooltip aria-haspopup="true" title="You can choose to recruit students from this column. <div style='border: thin solid white'>Yes</div> <br> The student will be notified that you would like to recruit them. <div>NO</div> <br> This student will be marked 'no' until they update their profile.">?</span>-->Remove
                                </div>
                            </div>
                        @endif

						@if( $currentPage == 'admin-prescreened' )
							<div class="row inquirieHeader @if($currentPage == 'admin-recommendations') recomm @endif">

								<div class="column small-1 chkbox-header-col text-right">{{Form::checkbox('name', 'all', false, array('class' => 'student-row-chkbx-all'))}}</div>

								<div class="column small-5 medium-3 large-2 name-col messageName text-left" style="width: 12.66667%;">
									<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"name","sortBy":"@if(isset($column_orders) && $column_orders["orderBy"] == "name"){{$column_orders["sortBy"] or "DESC"}}@else DESC @endif"}'>
									Name
								</div>

								<div class="column text-center large-1 @if( $currentPage == 'admin-inquiries' || $currentPage == 'admin-approved' ) show-for-large-up @else small-2 medium-1 @endif">GPA</div>

								<div class="column text-center medium-4 large-2 show-for-medium-up" style="width: 14.66667%">
									<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"major","sortBy":"@if(isset($column_orders) && $column_orders["orderBy"] == "major"){{$column_orders["sortBy"] or "DESC"}}@else DESC @endif"}'>
									Degree Interested In
								</div>

								<div class="column text-center large-1 show-for-large-up country-col countryCol">
									<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"country","sortBy":"@if(isset($column_orders) && $column_orders["orderBy"] == "country"){{$column_orders["sortBy"] or "DESC"}}@else DESC @endif"}'>
									Country
								</div>

								<div class="column text-center large-1 show-for-large-up">
									<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"date","sortBy":"@if(isset($column_orders) && $column_orders["orderBy"] == "date"){{$column_orders["sortBy"] or "DESC"}}@else DESC @endif"}'>
									Date
								</div>

								<div class="column text-center large-1 show-for-large-up">
									<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"uploads","sortBy":"@if(isset($column_orders) && $column_orders["orderBy"] == "uploads"){{$column_orders["sortBy"] or "ASC"}}@else ASC @endif"}' />
									Uploads
								</div>
								
								<div class="column text-center small-4 medium-2 large-1">
									Interview Status
								</div>

								<div class="column text-center small-4 medium-1">
									Applied
								</div>

								<div class="column text-center small-4 medium-1">
									Enrolled
								</div>

								<div class="column text-center show-for-medium-up medium-1">
									Remove
								</div>
							</div>
						@endif
						
						{{-- this is where a loop starts to fill out all the user info --}}
						<!-- each-inquirie-container -->
						<div class="each-inquirie-container" data-page-type="{{$currentPage}}" data-has-results="{{$has_searchResults or ''}}" data-current-viewing="{{$current_viewing}}" data-total-results="{{$total_results}}">
						@include('admin.searchResultInquiries')
							<div class="row showResultsBar">
								<div class="column small-12 medium-4 large-4 showResults">
								</div>
								<div class="column small-12 medium-8 large-8 load-more text-center">
								</div>
							</div>
						</div>
						<!-- end of each-inquirie-container -->

						<!-- ajax loader -->
						@include('private.includes.ajax_loader')
						<!-- ajax loader -->

						<!-- from recommended to pending msg - start -->
						@if( $currentPage == 'admin-recommendations' )
						<div class="row pending-msg-and-pagination-row pending-msg-on-recommendations">
							<div class="column small-12">
								&#42;Students recruited from this list need to approve your request and will be added to <a href="/admin/inquiries/pending"><span class="added-to-pending-msg">Pending</span></a>
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

	<!-- File attachment modal - file attachments are organized by user -->
	@include('includes.fileAttachmentModal')

	@include('private.includes.editMessageTemplate')

@stop
