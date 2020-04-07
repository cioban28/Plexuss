@extends('admin.master')
@section('content')
<?php
	$inquiries = $inquiry_list;
	// dd( get_defined_vars() );
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
				@if((($currentPage == 'admin-pending') || ($currentPage == 'admin-approved') || ($currentPage == 'admin-converted')) && isset($is_aor) && ($is_aor == 0))
				<div class="right ms-btns group-msg-btn"> CREATE CAMPAIGN (<span class="chosen-count-display">0</span>)</div>
				@endif
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
                    <li class="admin-mobile-nav-item">
                        <a class="@if($currentPage == 'admin-converted') active @endif" href="/admin/inquiries/converted">Converted</a>
                        <span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Place a pixel on your registration or application page to track conversion.">?</span>
                    </li>
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
    						<span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students you have requested to recruit from your recommended list.">?</span>
    					</li>
    					<li class="admin-mobile-nav-item">
    						<a class="@if($currentPage == 'admin-approved') active @endif" href="/admin/inquiries/approved">Handshakes</a>
    						<span class="admin-side-menu-cnt">{{$num_of_approved or 0}}</span>
    						<span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students you've chosen to recruit. You are able to message these students.">?</span>
    					</li>
    					<li class="admin-mobile-nav-item">
    						<a class="@if($currentPage == 'admin-verifiedHs') active @endif" href="/admin/inquiries/verifiedHs">Verified Handshake</a>
    						<span class="admin-side-menu-cnt ">{{$num_of_verified_hs or 0}}</span>
    						<span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Verified handshake students.">?</span>
    					</li>
    					<li class="admin-mobile-nav-item">
    						<a class="@if($currentPage == 'admin-prescreened') active @endif" href="/admin/inquiries/prescreened" style="color: #24b26b">Prescreened</a>
    						<span class="admin-side-menu-cnt ">{{$num_of_prescreened or 0}}</span>
    						<span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students you've prescreened from your inquiry list.">?</span>
    					</li>
    					<li class="admin-mobile-nav-item">
    						<a class="@if($currentPage == 'admin-verifiedApp') active @endif" href="/admin/inquiries/verifiedApp">Verified Application</a>
    						<span class="admin-side-menu-cnt ">{{$num_of_verified_app or 0}}</span>
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
						@if( $currentPage == 'admin-recommended' )
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
    								<span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students you have requested to recruit from your recommended list.">?</span>
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
    								<span class="admin-side-menu-tooltip-icon has-tip tip-right" data-tooltip aria-haspopup="true" title="Students you've chosen to recruit.">?</span>
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
					</div>
					<!-- \\\\\\\\\\\\\\\\\\\\\ medium up side bar menu - end /////////////////// -->

					<!-- \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ main content - start ///////////////////////////////////// -->
					<div class="column small-12 medium-9 large-10 main-manage-students-content" data-equalizer-watch>

						<!-- from recommendations to pending message -->
						@if( $currentPage == 'admin-recommendations' )
						<div class="row pending-msg-and-pagination-row">
							<div class="column small-12">
								&#42;These recommendations expire daily
							</div>
						</div>
						@elseif( $currentPage == 'admin-approved' || $currentPage == 'admin-verifiedApp' || $currentPage == 'admin-verifiedHs' || $currentPage == 'admin-converted')
						<div class="row appr-btn-list show-for-medium-up clearfix">
							<div class="column medium-6 large-6 left-btns-app-pend">
								<div class="row left-side">
									@if(isset($is_aor) && $is_aor == 0)
									<a class="ms-btns group-msg-btn">
										<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/message.png">&nbsp;CREATE CAMPAIGN (<span class="chosen-count-display">0</span>)
									</a>
									
									@if(isset($is_aor) && $is_aor == 0)
									<a class="ms-btns text-msg-btn">
										<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/text_messages.png">&nbsp;TEXT STUDENTS (<span class="chosen-count-display">0</span>)
									</a>
									@endif

									@endif
									@if( $currentPage == 'admin-approved' || $currentPage == 'admin-converted' )
									<a class="ms-btns" data-reveal-id="exp-student-modal">
										<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/export.png">&nbsp;EXPORT STUDENTS
									</a>
									@endif
								</div>
							</div>
							<div class="column medium-6 large-6">
                                @if( $currentPage !== 'admin-removed')
                                    <div class="left">
                                        <div class='auto-dialer-button'>
                                            <div>Auto Dial</div>
                                            <div class='auto-dialer-icon'></div>
                                        </div>
                                    </div>
                                @endif

								<div class="float-right filterResult-cont">
									
									<!--div class="column large-4 large-offset-2"-->
                                        <a class="ms-btns filter-audience filter-audience-s">
                                            <img class="filter-audience-img" src="/images/setting/filter-white.png">
                                            <div class="filter-text">&nbsp;FILTER AUDIENCE</div>
                                        </a>
                                    <!--/div-->

									<!--div class="column large-6"-->
                                        <!-- <div class="resultsPerPage-container">
                                        	<label class="dropdown">
                                        		Results per page: 
                                      		</label>
                                         	

                                         	{{Form::select('display_option', array('15' => 15 , '30' => 30, '50' => 50, '100' => 100, '200' => 200),(isset($display_option) && $display_option != 15)? (string)$display_option : '15', array('id' => 'displayOption', 'class' => 'display-option'))}}
                                    	</div> -->
                                    	<!-- results per page -->
									<label class="results-label">
										<span class="results-title">Results per page:</span>  
										{{Form::select('display_option', array('15' => 15 , '30' => 30, '50' => 50, '100' => 100, '200' => 200),(isset($display_option) && $display_option != 15)? (string)$display_option : '15', array('id' => 'displayOption', 'class' => 'display-option results-select'))}}
									</label>
                                    <!--/div-->

								</div>
	            			</div>
						</div>

                        @include('admin.filterOptions')

						@elseif($currentPage == 'admin-pending')
						<div class="row appr-btn-list show-for-medium-up clearfix">
							<div class="column medium-6 large-6 left-btns-app-pend">
                                <div class="row left-side">
                                    @if(isset($is_aor) && $is_aor == 0)
                                    <a class="ms-btns group-msg-btn"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/message.png" />&nbsp;CREATE CAMPAIGN (<span class="chosen-count-display">0</span>)
                                    </a>
                                    @endif

                                    @if(Session::has('sales_super_power'))
									<a class="ms-btns text-msg-btn">
										<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/text_messages.png">&nbsp;TEXT STUDENTS (<span class="chosen-count-display">0</span>)
									</a>
									@endif
									
                                    <a class="ms-btns" data-reveal-id="exp-student-modal">
                                        <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/export.png">&nbsp;EXPORT STUDENTS
                                    </a>
                                </div>
							</div>

							<div class="column medium-6 large-6">
                                @if( $currentPage !== 'admin-removed')
                                    <div class="left">
                                        <div class='auto-dialer-button'>
                                            <div>Auto Dial</div>
                                            <div class='auto-dialer-icon'></div>
                                        </div>
                                    </div>
                                @endif

								<div class="float-right filterResult-cont">
									
									<!--div class="column large-4 large-offset-2"-->
                                        <a class="ms-btns filter-audience filter-audience-s">
                                            <img class="filter-audience-img" src="/images/setting/filter-white.png">
                                            <div class="filter-text">&nbsp;FILTER AUDIENCE</div>
                                        </a>
                                    <!--/div-->

									<!--div class="column large-6"-->
                                        <!-- <div class="resultsPerPage-container">
                                        	<label class="dropdown">
                                        		Results per page: 
                                      		</label>
                                         	{{Form::select('display_option', array('15' => 15 , '30' => 30, '50' => 50, '100' => 100, '200' => 200),(isset($display_option) && $display_option != 15)? (string)$display_option : '15', array('id' => 'displayOption', 'class' => 'display-option'))}}
                                    	</div> -->
                                    	<!-- results per page -->
									<label class="results-label">
										<span class="results-title">Results per page:</span>  
										{{Form::select('display_option', array('15' => 15 , '30' => 30, '50' => 50, '100' => 100, '200' => 200),(isset($display_option) && $display_option != 15)? (string)$display_option : '15', array('id' => 'displayOption', 'class' => 'display-option results-select'))}}
									</label>
                                    <!--/div-->

								</div>
	            			</div>
						</div>

						@include('admin.filterOptions')
						
						@elseif($currentPage == 'admin-removed' || $currentPage == 'admin-verifiedHs'|| 
								$currentPage == 'admin-verifiedApp')
						<div class="row appr-btn-list show-for-medium-up clearfix">

							<div class="right filterResult-cont">

								<!-- filter audience btn -->
								<a class="ms-btns filter-audience filter-audience-s">
	                                <img class="filter-audience-img" src="/images/setting/filter-white.png">
	                                <div class="filter-text">&nbsp;FILTER AUDIENCE</div>
	                            </a>

								<label class="results-label">
									<span class="results-title">Results per page:</span>  
									{{Form::select('display_option', array('15' => 15 , '30' => 30, '50' => 50, '100' => 100, '200' => 200), (isset($display_option) && $display_option != 15)? (string)$display_option : '15', array('id' => 'displayOption', 'class' => 'display-option results-select'))}}
								</label>
							</div>
						</div>

						@include('admin.filterOptions')

						@endif

                        @include('admin.contactPane.autoDialerRow')

                        @include('admin.contactPane.incomingCallModal')

                        @include('admin.contactPane.transferCallModal')

                        @include('admin.contactPane.postingStudentModal')

						{{-- Column headers --}}
						<div class="row inquirieHeader @if($currentPage == 'admin-approved' || $currentPage == 'admin-pending' || $currentPage == 'admin-converted') appr @endif">

							<div class="column small-1 chkbox-header-col text-right">{{Form::checkbox('name', 'all', false, array('class' => 'student-row-chkbx-all'))}}</div>

							<div class="column small-3 medium-2 large-2 name-col messageName"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"name","sortBy":"@if(isset($column_orders) && $column_orders["orderBy"] == "name"){{$column_orders["sort0By"] or "ASC"}}@else ASC @endif"}'> Name</div>

							<div class="custom-width column small-2 medium-2 text-center hide-for-large-up"><span data-tooltip aria-haspopup="true" class="has-tip sm" title="Use the applied column to indicate which students have applied. Plexuss uses this data to improve targeting and recommendation.">Applied</span></div>

							<div class="custom-width column small-2 medium-2 text-center hide-for-large-up"><span data-tooltip aria-haspopup="true" class="has-tip sm" title="Use the enrolled column to indicate which students have enrolled. Plexuss uses this data to improve targeting and recommendation.">Enrolled</span></div>

							@if( $currentPage != 'admin-verifiedApp' )
								<div class="column text-center large-1 @if( $currentPage == 'admin-inquiries' || $currentPage == 'admin-approved' || $currentPage == 'admin-converted' ) show-for-large-up @else small-2 medium-1 @endif">GPA</div>
							@endif

							<div class="custom-width column text-center medium-3 large-1 show-for-medium-up"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"major","sortBy":"@if(isset($column_orders) && $column_orders["orderBy"] == "major"){{$column_orders["sortBy"] or "ASC"}}@else ASC @endif"}'>Degree Interested In</div>

							<div class="column text-center large-1 show-for-large-up countryCol"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"country","sortBy":"@if(isset($column_orders) && $column_orders["orderBy"] == "country"){{$column_orders["sortBy"] or "ASC"}}@else ASC @endif"}'>Country</div>

							<div class="column text-center large-1 show-for-large-up"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"date","sortBy":"@if(isset($column_orders) && $column_orders["orderBy"] == "date"){{$column_orders["sortBy"] or "ASC"}}@else ASC @endif"}'>Date</div>

							{{-- @if( $currentPage != 'admin-verifiedApp' ) --}}
								<div class="column text-center large-1 show-for-large-up"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"uploads","sortBy":"@if(isset($column_orders) && $column_orders["orderBy"] == "uploads"){{$column_orders["sortBy"] or "ASC"}}@else ASC @endif"}'>Uploads</div>
							{{-- @endif --}}

							@if( $currentPage == 'admin-inquiries' || $currentPage == 'admin-approved' )
								<div class="column text-center small-2 medium-1 large-1">Message</div>
							@endif

							@if( $currentPage != 'admin-verifiedApp' )
								<div class="custom-width column large-1 text-center show-for-large-up"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"applied","sortBy":"@if(isset($column_orders) && $column_orders["orderBy"] == "applied"){{$column_orders["sortBy"] or "DESC"}}@else DESC @endif"}'>Applied <span data-tooltip data-width="150" aria-haspopup="true" class="has-tip tip-right lg" title="Use the applied column to indicate which students have applied. Plexuss uses this data to improve targeting and recommendation.">?</span></div>

								<div class="custom-width column large-1 text-center show-for-large-up"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"enrolled","sortBy":"@if(isset($column_orders) && $column_orders["orderBy"] == "enrolled"){{$column_orders["sortBy"] or "DESC"}}@else DESC @endif"}'>Enrolled <span data-tooltip data-width="150" aria-haspopup="true" class="has-tip tip-left lg" title="Use the enrolled column to indicate which students have enrolled. Plexuss uses this data to improve targeting and recommendation.">?</span></div>
							@endif


							@if( $currentPage == 'admin-verifiedApp' )
								<div class="column large-1 text-center show-for-large-up">
									<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"applied","sortBy":"@if(isset($column_orders) && $column_orders["orderBy"] == "applied"){{$column_orders["sortBy"] or "DESC"}}@else DESC @endif"}'>
									Status
								</div>

								<div class="column large-1 text-center show-for-large-up">&nbsp;</div>
								
								@if ( (isset($is_plexuss) && $is_plexuss) || (isset($is_organization) && $is_organization) )
									<div class="accepted-column column large-1 text-center show-for-large-up">
										<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/sort-icon.png" class="sort-col" data-order='{"orderBy":"applied","sortBy":"@if(isset($column_orders) && $column_orders["orderBy"] == "applied"){{$column_orders["sortBy"] or "DESC"}}@else DESC @endif"}'>
										Accepted
									</div>
								@endif
							@endif

							<div class="column text-center small-2 medium-1 large-1 non-last-col show-for-large-up">

								@if($currentPage == 'admin-removed')
									Restore
								@elseif($currentPage != "admin-verifiedHs" && $currentPage != "admin-verifiedApp")
									Remove
								@endif
							</div>
						</div>

						{{-- this is where a loop starts to fill out all the user info --}}
						<!-- each-inquirie-container -->
						<div class="each-inquirie-container" data-page-type="{{$currentPage}}" data-has-results="{{$has_searchResults or ''}}" data-current-viewing="{{$current_viewing}}" data-total-results="{{$total_results}}">
						@include('admin.searchResultApproved')

							<div class="row showResultsBar">
								<div class="column small-12 medium-4 large-4 showResults">
								</div>
								<div class="column small-12 medium-8 large-8 load-more text-center">
								</div>
							</div>
						</div>
						
						<!-- end ofeach-inquirie-container -->

						@include('private.includes.editMessageTemplate')
						
						<!-- File attachment modal - file attachments are organized by user -->
						@include('includes.fileAttachmentModal')

						<!-- ajax loader -->
						@include('private.includes.ajax_loader')
						<!-- ajax loader -->
						
						<!-- from recommended to pending msg - start -->
						@if( $currentPage == 'admin-recommendations' )
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
					<div class="column small-2 medium-2 large-2"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/applied.jpg"></div>
					<div class="column small-10 medium-10 large-10">
						<p>Use the applied column to indicate which students have applied.</p>
					</div>
					<div class="column small-2 medium-2 large-2"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/greenstar.jpg"></div>
					<div class="column small-10 medium-10 large-10">
						<p>Use the enrolled column to indicated which students have enrolled.</p>
					</div>
					<div class="column small-2 medium-2 large-2"></div>
					<div class="column small-10 medium-10 large-10">
						<p>Plexuss uses this data to improve targeting and recommendation.</p>
					</div>
				</div>
				<div class="column small-6 medium-5 large-4 text-center">
					<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/applied-enrolled.jpg" alt="Applied thumbnail">
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
					Whoops! None of the students you selected have opted-in to receive text messages. Please try other students!
				</div>
			</div>
		</div>

	</div><!-- end of off canvas wrap -->

@stop
