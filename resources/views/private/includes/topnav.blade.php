<?php

// 	echo "<pre>";
// print_r($currentPage);
// dd($data);
// dd(get_defined_vars());
    $is_international = !isset($country_based_on_ip) || $country_based_on_ip !== 'US'; // Case for checking just IP

    $user_country_id = isset($country_id) ? $country_id : null;

    $from_united_states = $user_country_id === 1 || !$is_international; // Case for checking users table or IP

    $me_tab_application_route = $from_united_states ? '/get_started' : 'college-application';
?>
<script>
    (function() {
        var _fbq = window._fbq || (window._fbq = []);
        if (!_fbq.loaded) {
            var fbds = document.createElement('script');
            fbds.async = true;
            fbds.src = '//connect.facebook.net/en_US/fbds.js';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(fbds, s);
            _fbq.loaded = true;
        }
        _fbq.push(['addPixelId', '1428934937356789']);
    })();

    window._fbq = window._fbq || [];
    window._fbq.push(['track', 'PixelInitialized', {}]);
</script><noscript>
    <img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?id=1428934937356789&amp;ev=PixelInitialized" />
</noscript>


@if( isset($premium_user_plan) || isset($premium_user_type))
	<div class="upgrade_-Tag"></div>
@endif


<!-- \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ inner non-frontpage top nav - start //////////////////////////////////////// -->

<!-- Daily chat bar! - start  -->
@if(isset($webinar_is_live) && $webinar_is_live == true)
	<div class="daily-chat-bar-container webinar-live" id="_webinar_bar">
	    <div class="blip"></div>
	    <div class="chat-label"><b><a href="/">Live Webinar going on right now! Join now!</a></b></div>
	</div>

@elseif(isset($is_any_school_live) && $is_any_school_live == true && (isset($is_mobile) && $is_mobile == false))
	<div class="daily-chat-bar-container" id="_chat_bar">
	    <div class="blip"></div>
	    <div class="chat-label"><strong>College daily chat is happening now.</strong> <a href="/chat"><i>See which colleges are online to talk with you</i></a></div>
	</div>
	<script>
		/*
			Purpose:
			- we don't want to show daily chat bar on /admin ONLY
			- want to show on rest of admin pages
			- can't use $currentPage == 'admin' b/c $currentPage is still admin even on /admin/profile, /admin/users, etc.
		*/
		var chat_bar = document.getElementById('_chat_bar'),
			path = window.location.pathname;

		if( chat_bar && path === '/admin' ) chat_bar.style.display = 'none';
	</script>

@elseif( (!isset($country_based_on_ip) || $country_based_on_ip !== 'US') && !isset($is_organization) && $currentPage !== 'international-students-page' )
	<!-- only international students should see this -->
	<!--<div class="daily-chat-bar-container intl-stu multi-alert" id="_intl_bar">
	    <div class="blip"></div>
	    <div class="chat-label"><b>International Students! Compare the costs of attending colleges in the US <u><a href="/international-students">here</a></u>!</b></div>
	</div>-->
@endif

<script>
	const chat = document.getElementById('_chat_bar');
	const webinar = document.getElementById('_webinar_bar');
	const intl = document.getElementById('_init_bar');
	const page = window.location.pathname;

	// hide everything if on news page
	if( page.includes('/news') ){
		if( chat ) chat.style.display = 'none';
		if( intl ) intl.style.display = 'none';
		if( webinar ) webinar.style.display = 'none';
	}
</script>
<!-- Daily chat bar! - end -->
@if(!isset($adminscholarshipPage) || $adminscholarshipPage=="admincms")
	@include('public.includes.topNavigationwLogo')
@endif

<!-- \\\\\\\\\\\\\\\\\\\\\\\\\\\ top nav - start ////////////////////////////////// -->
<div class="contain-to-grid BgTop1" id="react-hide-for-admin-2">
<script>
	//this is for the new admin - don't want to show topnav on portal login page
	var lowernav = document.getElementById('react-hide-for-admin-2'),
		path = window.location.pathname;

	if( lowernav && path === '/admin' ) lowernav.style.display = 'none';
</script>

<div class="row collapse" id="innerpages-topnav-second">
	<div class="small-12 columns top-bar-container hide-for-small">
  @if(isset($currentUrlForSmartBanner) && $currentUrlForSmartBanner == '/checkout/premium')
  @else
		<nav class="top-bar clearfix" data-topbar data-options="is_hover: false" role="navigation">

			<!-- <div class="mobile-menu-header left"><a class="custome-mobile-button" onclick="toggle_menu_button()" href="#">&nbsp;</a></div>
			<ul  class="title-area">
				<li class="toggle-topbar menu-icon left custome-mobile-button" style="border: thin solid red">
					<a id="menu-toggler" href="#"></a>
				</li>-->
				<!--<li class="name"> Do not remove this name box</li> -->

				<!-- mobile notification buttons
				<li class='right show-for-small-only' style='margin-top:7px; margin-right: 7px; border: thin solid red'>

					@if ( isset($signed_in) && $signed_in!=0 )
					<span style="font-size:11px;cursor:pointer;" onclick="HideNotiBox1();">STATUS&nbsp;</span>
					<span style="vertical-align:middle;padding-top:10px;cursor:pointer;" onclick="HideNotiBox1();">
						<input class="indicator_noti-small" id="indicator_noti-small" style="display:none;" value="25">
					</span>
					<span style="font-size:11px;margin-right:15px;cursor:pointer;" id="MobilePercentValue" onclick="HideNotiBox1();">&nbsp;0%</span>
					<span class="noti-icon-wrap">
						<img class="nav-noti-icon-img" alt="image" src="/images/nav-icons/topnav_notification_icon.jpg" />
					</span>
					@endif

					<span onclick="ShowSearchMobile();" style="cursor:pointer;"><img class="nav-search-icon-img" alt="image" src="/images/nav-icons/topnav_search_icon.jpg" /></span>
				</li>
			</ul>-->

			@if(!isset($adminscholarshipPage) || $adminscholarshipPage=="admincms")
			<!-- ////////// new mobile top nav with notifications icons - start \\\\\\\\\ -->
			<div class="row collapse title-area show-for-small-only restructured-mobile-top-nav">

				<!-- mobile menu toggler hamburger icon -->
				@if ( isset($signed_in) && $signed_in != 0 )
				<div class="column small-2 mobile-hamburger-menu-toggler-container toggle-topbar">
					<a id="menu-toggler" class="mobile-hamburger-menu-toggler-icon" href="#"></a>
				</div>
				@endif
				<!-- mobile profile status icon -->
					@if ( isset($signed_in) && $signed_in != 0 )
				<div class="column small-5 text-center">
					<div class="row collapse mobile-profile-status-row">
						<div class="column small-5 text-right">
							<span style="font-size:11px;cursor:pointer;" onclick="HideNotiBox1();">STATUS&nbsp;</span>
						</div>
						<div class="column small-2 text-center mobile-profile-status-meter">
							<span style="padding-top:10px;cursor:pointer;" onclick="HideNotiBox1();">
								<input class="indicator_noti-small" id="indicator_noti-small" style="display:none;" value="25">
							</span>
						</div>
						<div class="column small-5 text-left prof-status-percent">
							<span style="font-size:11px;cursor:pointer; margin-left:5px;" id="MobilePercentValue" onclick="HideNotiBox1();">&nbsp;0%</span>
						</div>
					</div>
				</div>
        @endif
				<div class="column small-5">
					<div class="row collapse">

						<!-- mobile messages notification icon -->
						<div class="column small-4 notify_msg_unique text-center topnav_button notify_button">
							@if( isset($signed_in) && $signed_in != 0 )
								<img class="nav-noti-icon-img" alt="image" src="/images/nav-icons/mobile-messages-icon.jpg" />
								@if(isset($topnav_messages['unread_cnt']) && $topnav_messages['unread_cnt'] != 0)
		                            <div class="unread_count active">{{$topnav_messages['unread_cnt'] or ''}}</div>
		                        @else
		                            <div class="unread_count"></div>
		                        @endif
								<div class="open-notification-pane-arrow">
		                            <div class="top-nav-noti-pan-arrow top-nav-msg-noti-arrow"></div>
		                        </div>
							@endif
						</div>

						<!-- mobile all notification icon -->
						<div class="column small-4 notify_all_unique text-center topnav_button notify_button">
							@if( isset($signed_in) && $signed_in != 0 )
								<img class="nav-noti-icon-img" alt="image" src="/images/nav-icons/topnav_notification_icon.jpg" />
								@if(isset($topnav_notifications['unread_cnt']) && $topnav_notifications['unread_cnt'] != 0)
		                            <div class="unread_count active">{{$topnav_notifications['unread_cnt'] or ''}}</div>
		                        @else
		                            <div class="unread_count"></div>
		                        @endif
								<div class="open-notification-pane-arrow">
		                            <div class="top-nav-noti-pan-arrow top-nav-all-noti-arrow"></div>
		                        </div>
							@endif
						</div>

						<!-- mobile search icon -->
{{-- 						<div class="column small-4 text-center" onclick="ShowSearchMobile();">
							<img class="nav-search-icon-img" alt="image" src="/images/nav-icons/topnav_search_icon.jpg" />
						</div> --}}

                       <!--  <div class="column small-4 text-center show-for-small-only" style="@if(isset($signed_in) && $signed_in === 0 && isset($currentPage) && $currentPage != 'department') float:right @endif" data-reveal-id="i-want-to-modal">
                            <img class="nav-search-icon-img" alt="image" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/search-icon-update.png" />
                        </div> -->

                        @include('includes.iWantToMobileModal')

					</div>
				</div>
        @if(isset($signed_in) && $signed_in === 0 && isset($currentPage) && $currentPage == 'department')
        <div class="column small-2">
          <a class='text-center mobile_menu_no_btm_border' href="/about">
            <img alt='' style="width: 40%;" src="/images/plexussLogoLetterBlack.png" />
          </a>
        </div>
        <div class="column small-5">
          <div class="text-center show-for-small-only">
            <span class="toggle-topbar menu-icon" style="float:right; margin:10px 0;">
      				<a href="#" style="color:#000;"><span>Menu</span></a>
      			</span>
          </div>
        </div>
        @endif

			</div>
			<!-- \\\\\\\\\\\\ new mobile top nav with notifications icons - end ///////////// -->
			@endif

			@if(!isset($adminscholarshipPage) || $adminscholarshipPage=="admincms")
				{{-- MOBILE NAV MENU --}}
				<!-- Left Nav Section -->
				<section  class="top-bar-section">
						<!-- Current Page Indicator -->

						<div class="row show-for-small-only">
							<div class="column small-12 mobile-menu-current_page_indicator">
								Home
							</div>
						</div>
						<ul class="show-for-small-only MobileNav topLevelListMenu">
							@if( isset($signed_in) && $signed_in == 1 )
								@if(! isset( $is_organization ) || (isset( $is_organization ) && stripos( $currentPage, 'setting' ) === false && stripos( $currentPage, 'admin' ) === false))
									<!-- Home tab -->
									<li class="backFillFix">
										<a href="/home" class="mobile_nav_tab_home">
											<div class="row">
												<div class="column small-2">
													<img class="mobile-nav-icon-img-new" alt='navimage' src="/images/white-menu/home.png" />
												</div>
												<div class="column small-10">Home</div>
											</div>
										</a>
									</li>
									<!-- Me tab -->
									<li class="backFillFix">
										<a class=" me @if($currentPage=='profile') active  @endif " href="/profile"  >
											<div class="row">
												<div class="column small-2">
													<img class="mobile-nav-icon-img-new" alt='navimage' src="/images/white-menu/profile.png" />
												</div>
												<div class="column small-9">Me</div>
												<div class="column small-1 nestedListArrowColumn">
													<img class="show-for-small-only mobile-menu-arrow" src="/images/mobile_menu_arrow.png" alt="Nav Arrow">
												</div>
											</div>
										</a>
									</li>
									<!-- Profile tab -->
									<li class="has-dropdown backFillFix" id="profile-list">
										<a href="" onclick="currentPageIndicator(this, 'Profile');" class="mobile_nav_tab_profile">
											<div class="row">
												<div class="column small-2">
													<img class="mobile-nav-icon-img-new" alt='nav-image' src="/images/mobile_nav_icons/profile.png" />
												</div>
												<div class="column small-9 mobile-menu-txt ">Application</div>
												<div class="column small-1 nestedListArrowColumn">
													<img class="show-for-small-only mobile-menu-arrow" src="/images/mobile_menu_arrow.png" alt="Nav Arrow">
												</div>
											</div>
										</a>
										<!-- personal info tab -->
										<ul class="dropdown ul-sub-nav MobileNav">
											<li class="backFillFix">
												<a href="/profile?section=personalInfo" class="nestedBackColor mobile_nav_tab_personalInfo">
													<div class="row">
														<div class="column small-2">
															<img alt='nav-image' src="/images/nav-icons/nav-personalinfo.png" />
														</div>
														<div class="column small-10">Personal Info</div>
													</div>
												</a>
											</li>
											<!-- Objectives tab -->
											<li class="backFillFix">
												<a href="/profile?section=objective" class="nestedBackColor mobile_nav_tab_objective">
													<div class="row">
														<div class="column small-2">
															<img alt='nav-image' src="/images/nav-icons/nav-objective.png" />
														</div>
														<div class="column small-10">Objective</div>
													</div>
												</a>
											</li>

											@if( isset($user_country_id) && $user_country_id != 1 )
											<!-- Financial Info tab -->
											<li class="backFillFix">
												<a href="/profile?section=financialinfo" class="nestedBackColor mobile_nav_tab_financialinfo">
													<div class="row">
														<div class="column small-2">
															<img alt='nav-image' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/financial-icon-mobile-profile_gray.png" />
														</div>
														<div class="column small-10">Financial Info</div>
													</div>
												</a>
											</li>
											@endif

											<!-- Scores tab -->
											<li class="GradeMenusMobile backFillFix">
												<a href="/profile?section=scores" class="nestedBackColor mobile_nav_tab_scores">
													<div class="row">
														<div class="column small-2">
															<img alt='nav-image' src="/images/nav-icons/nav-scores.png" />
														</div>
														<div class="column small-10">Scores</div>
													</div>
												</a>
											</li>

											<!-- Upload Center tab -->
											<li class="GradeMenusMobile backFillFix">
												<a href="/profile?section=uploadcenter" class="nestedBackColor mobile_nav_tab_uploadcenter">
													<div class="row">
														<div class="column small-2">
															<img alt='nav-image' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/upload-icon-mobile-mobile-profile_gray.png" />
														</div>
														<div class="column small-10">Upload Center</div>
													</div>
												</a>
											</li>

											<!-- High School Info tab -->
											<li class="GradeMenusMobile backFillFix">
												<a href="/profile?section=highschoolInfo" class="nestedBackColor mobile_nav_tab_highschoolInfo">
													<div class="row">
														<div class="column small-2">
															<img alt='nav-image' src="/images/nav-icons/nav-highschool.png" />
														</div>
														<div class="column small-10">High School Info</div>
													</div>
												</a>
											</li>
											<!-- College Info tab -->
											<li class="GradeMenusMobile backFillFix">
												<a href="/profile?section=collegeInfo" class="nestedBackColor mobile_nav_tab_collegeInfo">
													<div class="row">
														<div class="column small-2">
															<img alt='nav-image' src="/images/nav-icons/nav-collegeinfo.png" />
														</div>
														<div class="column small-10">College Info</div>
													</div>
												</a>
											</li>
											<!-- Accomplishments tab -->
											<li class="has-dropdown backFillFix" id="AccomplishmentMenuMobile" >
												<a href="" onclick="currentPageIndicator(this, 'Accomplishments');" class="nestedBackColor mobile_nav_tab_accomplishments">
													<div class="row">
														<div class="column small-2">
															<img alt='nav-image' src="/images/nav-icons/nav-accomplishment.png" />
														</div>
														<div class="column small-9">Accomplishments</div>
														<div class="column small-1 nestedListArrowColumn">
															<img class="show-for-small-only mobile-menu-arrow" src="/images/mobile_menu_arrow.png" alt="Nav Arrow">
														</div>
													</div>
												</a>
												<ul class="dropdown ul-sub-nav MobileNav">
													<!-- Experience tab -->
													<li class="backFillFix">
														<a href="/profile?section=experience" class="nestedBackColor mobile_nav_tab_experience">
															<div class="row">
																<div class="column small-2">
																	<img alt='nav-image' src="/images/nav-icons/nav-exp.png" />
																</div>
																<div class="column small-10">Experience</div>
															</div>
														</a>
													</li>
													<!-- Skills tab -->
													<li class="backFillFix">
														<a href="/profile?section=skills" class="nestedBackColor mobile_nav_tab_skills">
															<div class="row">
																<div class="column small-2">
																	<img alt='nav-image' src="/images/nav-icons/nav-skills.png" />
																</div>
																<div class="column small-10">Skills</div>
															</div>
														</a>
													</li>
													<!-- Interests tab -->
													<li class="backFillFix">
														<a href="/profile?section=interests" class="nestedBackColor mobile_nav_tab_interests">
															 <div class="row">
																<div class="column small-2">
																	<img alt='nav-image' src="/images/nav-icons/nav-interest.png" />
																</div>
																<div class="column small-10">Interests</div>
															</div>
														</a>
													</li>
													<!-- Club/Org tab -->
													<li class="backFillFix">
														<a href="/profile?section=clubOrgs" class="nestedBackColor mobile_nav_tab_clubOrgs">
															 <div class="row">
																<div class="column small-2">
																	<img alt='nav-image' src="/images/nav-icons/nav-club.png" />
																</div>
																<div class="column small-10">Clubs &amp; Orgs</div>
															</div>
														</a>
													</li>
													<!-- Honor/Awards tab -->
													<li class="backFillFix">
														<a href="/profile?section=honorsAwards" class="nestedBackColor mobile_nav_tab_honorsAwards">
															<div class="row">
																<div class="column small-2">
																	<img  alt='nav-image' src="/images/nav-icons/nav-honor.png" />
																</div>
																<div class="column small-10">Honors &amp; Awards</div>
															</div>
														</a>
													</li>
													<!-- Languages tab -->
													<li class="backFillFix">
														<a href="/profile?section=languages" class="nestedBackColor mobile_nav_tab_languages">
															<div class="row">
																<div class="column small-2">
																	<img  alt='nav-image' src="/images/nav-icons/nav-lang.png" />
																</div>
																<div class="column small-10">Languages</div>
															</div>
														</a>
													</li>
													<!-- Certifications tab -->
													<li class="backFillFix">
														<a href="/profile?section=certifications" class="nestedBackColor mobile_nav_tab_certifications">
															<div class="row">
																<div class="column small-2">
																	<img alt='nav-image' src="/images/nav-icons/nav-certifications.png" />
																</div>
																<div class="column small-10">Certifications</div>
															</div>
														</a>
													</li>
													<!-- Patents tab -->
													<li class="backFillFix">
														<a href="/profile?section=patents" class="nestedBackColor mobile_nav_tab_patents">
															<div class="row">
																<div class="column small-2">
																	<img alt='nav-image' src="/images/nav-icons/nav-patent.png" />
																</div>
																<div class="column small-10">Patents</div>
															</div>
														</a>
													</li>
													<!-- Publications tab -->
													<li class="backFillFix">
													<a href="/profile?section=publications" class="nestedBackColor mobile_nav_tab_publications">
														<div class="row">
																<div class="column small-2">
																	<img alt='nav-image' src="/images/nav-icons/nav-publications.png" />
																</div>
																<div class="column small-10">Publications</div>
															</div>
														</a>
													</li>
												</ul>
											</li>
										</ul>
									</li>
								@endif
							@endif



							{{-- IF ON ADMIN PAGE, SHOW ADMIN LINKS --}}
							@if( stripos( $currentPage, 'admin' ) !== false || (isset( $is_organization)  && stripos( $currentPage, 'setting' ) !== false))
								<!-- Dashboard link -->
								<li class="backFillFix">
									<a href="/admin/dashboard" class="mobile_nav_tab_admin">
										<div class="row">
											<div class="column small-2">
												<img class="mobile-nav-icon-img-new" alt='none' src="/images/mobile_nav_icons/portal.png" />
											</div>
											<div class="column small-10">Dashboard</div>
										</div>
									</a>
								</li>
								<!-- Inquiries link -->
								<li class="backFillFix">
									<a href="/admin/inquiries" class="mobile_nav_tab_portal">
										<div class="row">
											<div class="column small-2">
												<img class="mobile-nav-icon-img-new" alt='none' src="/images/mobile_nav_icons/inquiries.png" />
											</div>
											<div class="column small-10">Inquiries</div>
										</div>
									</a>
								</li>
								<!-- chat link -->
								<li class="backFillFix">
									<a href="/admin/chat" class="mobile_nav_tab_portal">
										<div class="row">
											<div class="column small-2">
												<img class="mobile-nav-icon-img-new" alt='none' src="/images/mobile_nav_icons/chat.png" />
											</div>
											<div class="column small-10">Chat</div>
										</div>
									</a>
								</li>
								<!-- messages link -->
								<li class="backFillFix">
									<a href="/admin/messages" class="mobile_nav_tab_portal">
										<div class="row">
											<div class="column small-2">
												<img class="mobile-nav-icon-img-new" alt='none' src="/images/mobile_nav_icons/messages.png" />
											</div>
											<div class="column small-10">Messages</div>
										</div>
									</a>
								</li>
								<!-- campaign link -->
								<li class="backFillFix">
									<a href="/admin/groupmsg" class="mobile_nav_tab_portal">
										<div class="row">
											<div class="column small-2">
												<img class="mobile-nav-icon-img-new" alt='none' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/mass-message-icon-23X23.png" />
											</div>
											<div class="column small-10">Campaign</div>
										</div>
									</a>
								</li>
								<!-- Products and Services link -->
								<li class="backFillFix">
									<a href="/admin/products" class="mobile_nav_tab_portal">
										<div class="row">
											<div class="column small-2">
												<img class="mobile-nav-icon-img-new" alt='none' src="/images/mobile_nav_icons/products.png" />
											</div>
											<div class="column small-10">Products and Services</div>
										</div>
									</a>
								</li>

							{{-- end: admin specific mobile nav links --}}

							@else
								<!-- Portal tab -->
								@if( isset($signed_in) && $signed_in == 1 )
								<li class="backFillFix">
									<a href="/portal" class="mobile_nav_tab_portal">
										<div class="row">
											<div class="column small-2">
												<img class="mobile-nav-icon-img-new" alt='none' src="/images/mobile_nav_icons/portal.png" />
											</div>
											<div class="column small-10 mobile-menu-txt ">Portal</div>
										</div>
									</a>
								</li>
								@endif
								<!-- Colleges tab -->
								<li class="backFillFix">
									<a href="/college" class="mobile_nav_tab_college">
										<div class="row">
											<div class="column small-2">
												<img class="mobile-nav-icon-img-new" alt='none' src="/images/mobile_nav_icons/college.png" />
											</div>
											<div class="column small-10 mobile-menu-txt ">Colleges</div>
										</div>
									</a>
								</li>
								<!-- international students tab -->
								<li class="backFillFix">
									<a href="/international-students" class="mobile_nav_tab_international_students">
										<div class="row">
											<div class="column small-2">
												<img class="mobile-nav-icon-img-new" alt='none' src="/images/mobile_nav_icons/college.png" />
											</div>
											<div class="column small-10 mobile-menu-txt ">International Students</div>
										</div>
									</a>
								</li>
								<!-- Ranking tab -->
								<li class="backFillFix">
									<a href="/ranking" class="mobile_nav_tab_ranking">
										<div class="row">
											<div class="column small-2">
												<img class="mobile-nav-icon-img-new" alt='none' src="/images/mobile_nav_icons/ranking.png" />
											</div>
											<div class="column small-10 mobile-menu-txt ">Ranking</div>
										</div>
									</a>
								</li>
								<!-- Comparison tab -->
								<li class="backFillFix">
									<a href="/comparison" class="mobile_nav_tab_comparison">
										<div class="row">
											<div class="column small-2">
												<img  alt='none' src="/images/ranking/compare_icon.png" />
											</div>
											<div class="column small-10 mobile-menu-txt ">Compare Colleges</div>
										</div>
									</a>
								</li>
								<!-- The Quad tab -->
								<li class="backFillFix">
									<a href="/news" class="mobile_nav_tab_news">
										<div class="row">
											<div class="column small-2">
												<img class="mobile-nav-icon-img-new" alt=none src="/images/mobile_nav_icons/news.png" />
											</div>
											<div class="column small-10 mobile-menu-txt ">The Quad</div>
										</div>
									</a>
								</li>

								@if (!isset($country_based_on_ip) || $country_based_on_ip !== 'US')
								<!-- International Students tab -->
								<li class="backFillFix">
									<a href="/international-resources" class="mobile_nav_tab_news">
										<div class="row">
											<div class="column small-2">
												<img class="mobile-nav-icon-img-new" alt=none src="/images/mobile_nav_icons/college.png" />
											</div>
											<div class="column small-10 mobile-menu-txt">International Resources</div>
										</div>
									</a>
								</li>
								@endif

								 <!-- premium  -->
								@if(  isset($signed_in) && $signed_in != 0  && ! isset( $is_organization ) && stripos( $currentPage, 'admin' ) === false)
									<li  class="backFillFix mobilePremium-btn mobilePrem @if($premium_user_type == 'onetime') prem-one-style @elseif($premium_user_type == 'onetime_plus') prem-plus-style @else prem-one-style @endif">

									<a>
									<div class="row">
											<div class="column small-2">
												@if( isset($premium_user_plan))
													<img class="mobile-nav-icon-img-new" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/premium_plus_icon.png" alt="Premium Icon">
												@else
													<img class="mobile-nav-icon-img-new" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/premium_icon.png" alt="Premium Icon">
												@endif
											</div>
											<div class="column small-10 mobile-menu-txt ">Premium</div>
										</div>
										</a>
									</li>

								@endif

								@if(  isset($signed_in) && $signed_in != 0  && ! isset( $is_organization ) && stripos( $currentPage, 'admin' ) === false)
									<li  id="upgradePremiun_mobilebtn" class="backFillFix prem-plus-style">

										<a>
											<div class="row">
												<div class="column small-2">
														<img class="mobile-nav-icon-img-new" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/star1.png" alt="upgrade Icon">
												</div>
												<div class="column small-10 mobile-menu-txt ">Upgrade to Premium</div>
											</div>
										</a>
									</li>

								@endif

								<!-- College Care Package tab -->
								<!--
								<li class="backFillFix">
									<a href="/carepackage" class="mobile_nav_tab_news">
										<div class="row">
											<div class="column small-2">
												<img class="mobile-nav-icon-img-new" alt=none src="/images/mobile_nav_icons/carepackage.jpg" />
											</div>
											<div class="column small-10">College Care Package</div>
										</div>
									</a>
								</li>
								-->
							@endif {{-- end: hide these links if on admin page --}}


							{{-- SHOW ADMIN LINK IF NOT ON ADMIN PAGE --}}
							@if( isset( $signed_in ) && $signed_in==1 && isset( $is_organization ) && stripos( $currentPage, 'admin' ) === false && stripos($currentPage,'setting') === false)
								<!-- admin dashboard -->
								<li class="backFillFix">
									<a href="/admin/dashboard" class="mobile_nav_tab_admin">
										<div class="row">
											<div class="column small-2">
												<img class="mobile-nav-icon-img-new" src="/images/mobile_nav_icons/admin.png" alt="Admin Icon">
											</div>
											<div class="column small-10">Admin</div>
										</div>
									</a>
								</li>
							@endif
							{{-- END SHOW ADMIN LINK --}}

							@if(isset($is_sales))
								<li <?php if($currentPage=='sales') { ?> class="active" <?php } ?>
									<a href="/sales">Sales</a>
								</li>
							@endif



							@if( isset($signed_in) && $signed_in==1 )


							<!-- Settings tab -->
								<li class="backFillFix has-dropdown" id="settingsMobileMenuTab">
									<a href="" class="mobile_nav_tab_settings" onclick="currentPageIndicator(this, 'Settings');">
										<div class="row">
											<div class="column small-2">
												<img class="mobile-nav-icon-img-new" src="/images/mobile_nav_icons/settings.png" alt="Settings Icon">
											</div>
											<div class="column small-9 mobile-menu-txt ">Settings</div>
											<div class="column small-1 nestedListArrowColumn">
												<img class="show-for-small-only mobile-menu-arrow" src="/images/mobile_menu_arrow.png" alt="Nav Arrow">
											</div>
										</div>
									</a>
									<ul class="dropdown ul-sub-nav MobileNav">
										<li class="backFillFix">
											<a href="/settings" class="nestedBackColor">
												Change Password
											</a>
										</li>
										@if( !isset($is_organization) || $is_organization == 0 || !isset($super_admin) || $super_admin == 0 )
										<li class="backFillFix">
											<a href="/settings/invite" class="nestedBackColor">
												Invite Friends
											</a>
										</li>
										@endif
										@if( !isset($is_organization) || $is_organization == 0 )
										<li class="backFillFix">
											<a href="/settings/billing" class="nestedBackColor">
												Billing
											</a>
										</li>
										@endif

										@if (isset($is_gdpr) && $is_gdpr == true)
										<li class="backFillFix">
											<a href="/settings/data_preferences" class="nestedBackColor">
												Data Preferences
											</a>
										</li>
										@endif
										<!--<li class="backFillFix">
											<a href="/settings/manageusers" class="nestedBackColor">
												Manage Users
											</a>
										</li>-->
									</ul>
								</li>
							@endif


							@if ( isset($signed_in) && $signed_in==1 )
							<!-- Sign Out tab -->
								<li class="backFillFix mobileMenu_signInOutMenuItem">
									<a href="/signout">
										<div class="row">
											<div class="column small-2">
												<img class="mobile-nav-icon-img-new" alt=none src="/images/mobile_nav_icons/signout.png" data-src="/images/mobile_nav_icons/signout.png" />
											</div>
											<div class="column small-10">sign out</div>
										</div>
									</a>
								</li>
							@else
							<!-- Sign In tab -->
								<li class="backFillFix mobileMenu_signInOutMenuItem">
									<a href="/signin">
										<div class="row">
											<div class="column small-2">
												<img class="mobile-nav-icon-img-new" alt=none src="/images/mobile_nav_icons/signout.png" data-src="/images/mobile_nav_icons/signout.png" />
											</div>
											<div class="column small-10">sign in</div>
										</div>
									</a>
								</li>
							@endif

							<!--<li><a href="/ranking">
							<span class="icon-span-width">
							<img alt='portal image' src="/images/nav-icons/nav-portal.png" data-src="/images/nav-icons/nav-portal-hover.png" />
							</span>
							<span >Ranking</span>
							</a></li>

							<li><a href="/settings">
							<span class="icon-span-width">
							<img src="/images/nav-icons/nav-settings.png" data-src="/images/nav-icons/nav-settings-hover.png" />
							</span>
							<span >Settings</span>
							</a></li>-->
							<li class='text-center backFillFix pt30 pb30 mobileMenu_companyInfoMenuItem'>
								<div class="row">
									<div class="column small-12">
										<a class='text-center mobile_menu_no_btm_border' href="/about">
											<img alt='none' style='width:20%' src="/images/nav-plexuss-logo.png" />
										</a>
									</div>
									<div class="column small-12 pt15 pb15">
										<div><span><a class="mobile_menu_no_btm_border" href="/about">Company</a></span> | <span><a class="mobile_menu_no_btm_border" href="/about">Information</a></span></div>
									</div>
								</div>
							</li>
						</ul><!-- end of initial menu list -->
					<!-- end of MOBILE NAV -->



					<!-- not mobile -->
					<ul class="left show-for-medium-up innerpages-lower-nav-ul-leftside topnav-cont"  id="react-hide-for-admin-3">

						@if ( isset($signed_in) && $signed_in == 1 )
							{{-- show only home link if page is an admin page --}}

							@if( stripos( $currentPage, 'admin' ) === false)
								@if(! isset( $is_organization ) || (isset( $is_organization ) && stripos( $currentPage, 'setting' ) === false))
									<!-- <li <?php if($currentPage=='home') { ?> class="active" <?php } ?>>
										<a href="/home" id="topNavHomeNoLeft">Home</a>
									</li>
									<li <?php if($currentPage=='profile') { ?> class="active" <?php } ?>>
										<a href="/profile">Profile</a>
									</li> -->
									<li <?php if($currentPage=='home') { ?> class="active" <?php } ?>>
										<a href="/home" id="topNavHomeNoLeft">Home</a>
									</li>

									<li id="me_tab_on_topnav" class="abs-wrapper">
										<a class=" me @if($currentPage=='profile') active  @endif " href="/profile">
											Me <span class="navigation-arrow-down1">&nbsp;</span>
										</a>
										<div class="me-drop">
											<a href="/profile/edit_public"><div class="topnav-icon-sprite public-profile"></div> Public Profile</a>
											<a href="/college-application"><div class="topnav-icon-sprite college-app"></div> College Application</a>
											<a href="/profile/documents"><div class="topnav-icon-sprite me-docs"></div> Your Documents</a>

										</div>
									</li>
								@endif
							@endif
						@endif


						<!-- ADMIN pages TOPNAV if AOR -->

						@if(  (stripos( $currentPage, 'admin' ) !== false || ( isset( $is_organization ) && stripos( $currentPage, 'setting' ) !== false)) && $is_aor == 1)
							<li id="react_route_to_dashboard" {{ $currentPage == 'admin' ? 'class="active"' : '' }}>
								@if( $currentPage == 'admin' )
								<a class="admin-topnav-items">
									Dashboard
								</a>
								@else
								<a href="/admin/dashboard" class="admin-topnav-items">
									Dashboard
								</a>
								@endif
							</li>

							<li {{ $currentPage == 'admin-recruitment' ? 'class="active"' : '' }}>
								<a href="/admin/manageCollege" class="admin-topnav-items recruitment-dropdown">
									Manage Colleges
								</a>
							</li>

							<li {{ $currentPage == 'admin-communication' ? 'class="active"' : '' }}>
								<a href="/admin/textmsg" class="admin-topnav-items communication-drop">
									Text Messages
								</a>
							</li>
							</ul>


						<!-- ADMIN PAGES TOPNAV LINKS -->
						@elseif( (stripos( $currentPage, 'admin' ) !== false || ( isset( $is_organization ) && stripos( $currentPage, 'setting' ) !== false)) && $is_aor == 0)
							<!-- Dashboard link -->
							<li id="react_route_to_dashboard" {{ $currentPage == 'admin' ? 'class="active"' : '' }}>
								@if( $currentPage == 'admin' )
								<a href="/admin/dashboard"  class="admin-topnav-items">
									Dashboard
								</a>
								@else
								<a href="/admin/dashboard" class="admin-topnav-items">
									Dashboard
								</a>
								@endif
							</li>

							<li {{ $currentPage == 'admin-recruitment' ? 'class="active"' : '' }}>
								<a class="admin-topnav-items recruitment-dropdown">
									Manage Students
								</a>
								<ul id="recruit-dropdown" class="admin-topnav-dropdown">
									<li {{ $currentPage == 'admin-inquiries' ? 'class="active"' : '' }}>
										<a href="/admin/inquiries"  class="admin-topnav-items">
											Inquiries
										</a>
									</li>

									<li {{ $currentPage == 'admin-converted' ? 'class="active"' : '' }}>
										<a href="/admin/inquiries/converted"  class="admin-topnav-items">
											Converted
										</a>
									</li>

									<li {{ $currentPage == 'admin-recommend' ? 'class="active"' : '' }}>
										<a href="/admin/inquiries/recommendations"  class="admin-topnav-items">
											Recommended
											@if (isset($is_admin_premium) && !$is_admin_premium)
												<div class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access this feature."></div>
											@endif
										</a>
									</li>
									<li {{ $currentPage == 'admin-pending' ? 'class="active"' : '' }}>
										<a href="/admin/inquiries/pending"  class="admin-topnav-items">
											Pending
											@if (isset($is_admin_premium) && !$is_admin_premium)
												<div class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access this feature."></div>
											@endif
										</a>
									</li>
									<li {{ $currentPage == 'admin-approved' ? 'class="active"' : '' }}>
										<a href="/admin/inquiries/approved"  class="admin-topnav-items">
											Handshakes
											@if (isset($is_admin_premium) && !$is_admin_premium)
												<div class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access this feature."></div>
											@endif
										</a>
									</li>
									<li {{ $currentPage == 'admin-verifiedHs' ? 'class="active"' : '' }}>
										<a href="/admin/inquiries/verifiedHs"  class="admin-topnav-items">
											Verified Handshakes
											@if (isset($is_admin_premium) && !$is_admin_premium)
												<div class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access this feature."></div>
											@endif
										</a>
									</li>

									@if( isset($num_of_prescreened) && $num_of_prescreened > 0 )
									<li {{ $currentPage == 'admin-prescreened' ? 'class="active"' : '' }}>
										<a href="/admin/inquiries/prescreened"  class="admin-topnav-items">
											Prescreened
											@if (isset($is_admin_premium) && !$is_admin_premium)
												<div class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access this feature."></div>
											@endif
										</a>
									</li>
									@endif

									<li {{ $currentPage == 'admin-verifiedApp' ? 'class="active"' : '' }}>
										<a href="/admin/inquiries/verifiedApp"  class="admin-topnav-items">
											Verified Application
											@if (isset($is_admin_premium) && !$is_admin_premium)
												<div class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access this feature."></div>
											@endif
										</a>
									</li>

									<!--
									<li {{ $currentPage == 'admin-prescreen' ? 'class="active"' : '' }}>
										<a href="/admin/inquiries/prescreen"  class="admin-topnav-items">
											PreScreen
										</a>
									</li>
									-->
								</ul>

							</li>

							<li {{ $currentPage == 'admin-communication' ? 'class="active"' : '' }}>
								<a class="admin-topnav-items communication-drop">
									Communication
								</a>

								<ul id="comm-dropdown" class="admin-topnav-dropdown">
									<!-- Messages link -->
									<li {{ $currentPage == 'admin-messages' ? 'class="active"' : '' }}>
									<a href='/admin/messages' class="admin-topnav-items">
										Messages
									</a>
									</li>
									<!-- Text Messages link -->
									<li {{ $currentPage == 'admin-text-messages' ? 'class="active"' : '' }}>
									<a href='/admin/textmsg' class="admin-topnav-items">
										Text Messages
										@if (isset($is_admin_premium) && !$is_admin_premium)
											<div class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access this feature."></div>
										@endif
									</a>
									</li>
									<!-- Campaigns link -->
									<li {{ $currentPage == 'admin-groupmsg' ? 'class="active"' : '' }}>
									<a href='/admin/groupmsg' class="admin-topnav-items">
										Campaigns
										@if (isset($is_admin_premium) && !$is_admin_premium)
											<div class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access this feature."></div>
										@endif
									</a>
									</li>
									<!-- Chat link -->
									<li {{ $currentPage == 'admin-chat' ? 'class="active"' : '' }}>
									<a href='/admin/chat' class="admin-topnav-items">
										Live Chat
										@if (isset($is_admin_premium) && !$is_admin_premium)
											<div class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access this feature."></div>
										@endif
									</a>
									</li>

								</ul>


							</li>
							<li {{ $currentPage == 'admin-tools' ? 'class="active"' : '' }}>
								<a class="admin-topnav-items" id="tools_topnav">
									Tools
								</a>

								<ul id="tools_dropdown" class="admin-topnav-dropdown">
									<li>
										@if( $currentPage == 'admin' )
										<a href="/admin/tools" id="react_route_to_tools" class="admin-topnav-items">
											Content Management
										</a>
										@else
										<a href="/admin/tools" class="admin-topnav-items">
											Content Management
										</a>
										@endif
									</li>
									<li>
										<a href="/admin/tools/scholarshipcms" class="admin-topnav-items">
											Manage Scholarships
										</a>
									</li>
									<li>
										<a href="/admin/studentsearch" class="admin-topnav-items">
											Advanced Search
											@if (isset($is_admin_premium) && !$is_admin_premium)
												<div class="lock has-tip" data-tooltip aria-haspopup="true" title="Please upgrade to Premier to access this feature."></div>
											@endif
										</a>
									</li>
								</ul>

							</li>


							<li {{ $currentPage == 'admin-Products' ? 'class="active"' : '' }}>
								<a href='/admin/products' class="admin-topnav-items">
									Services
								</a>
							</li>

						<!-- END ADMIN PAGES TOPNAV LINKS -->


						@else

							@if( isset($signed_in) && $signed_in == 1 )
							<li <?php if($currentPage=='portal') { ?> class="active" <?php } ?>>
								<a href="/portal">Portal</a>
							</li>
							@endif
							<li  class="@if($currentPage=='college-home' || $currentPage=='ranking' || $currentPage=='comparison') active @endif"
								 style="position:relative;" onmouseover="$('#college-sub-menu').show();" onmouseout="$('#college-sub-menu').hide();">
								<a class="mainTopNav_collegeTab" href="/college">
									Colleges <span class="navigation-arrow-down1">&nbsp;</span>
								</a>
								<ul class="topnav-college-drop" style="position:absolute;width:257px;display:none;z-index: 1;" id="college-sub-menu">

									<li>
										<a href="/college">
											<!-- <img id='public_college_search_icon' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon.png" data-src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
											<div class="topnav-icon-sprite find-colleges"></div>
											Find Colleges
										</a>
									</li>

									<li>
										<a href="/college-fairs-events">
											<div class="topnav-icon-sprite events"></div>

											<!-- <img src="/images/ranking/ranking_icon.png" data-src="/images/ranking/ranking_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp; -->College Fairs
										</a>
									</li>

									<li>
										<a href="/college-majors">
											<div class="colleges-majors-icon"></div>

											<!-- <img src="/images/ranking/ranking_icon.png" data-src="/images/ranking/ranking_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp; -->Majors
										</a>
									</li>
									<li>
										<a href="/scholarships">
											<div class="topnav-icon-sprite scholarships"></div>
											Scholarships
										</a>
									</li>
									<li>
										<a href="/ranking">
											<div class="topnav-icon-sprite colleges-ranking"></div>

											<!-- <img src="/images/ranking/ranking_icon.png" data-src="/images/ranking/ranking_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp; -->Ranking
										</a>
									</li>
									<div class="clearfix" style="border-top: 1px #ff0000;"></div>
									<li>
										<a href="/comparison">
											<div class="topnav-icon-sprite compare-colleges"></div>

											<!-- <img src="/images/ranking/compare_icon.png" data-src="/images/ranking/compare_icon_hover.png" alt=""/>&nbsp;&nbsp; -->Compare Colleges
										</a>
									</li>
								</ul>
                <li style="position:relative;" onmouseover="$('#quad-sub-menu').show();" onmouseout="$('#quad-sub-menu').hide();" <?php if($currentPage=='news') { ?> class="active" <?php } ?>>
                    <a href="/news">The Quad <span class="navigation-arrow-down1">&nbsp;</span></a>
                    <ul class="topnav-college-drop" style="position:absolute;width:257px;display:none;z-index: 1;" id="quad-sub-menu">
                    <li>
                        <a href="/news">
                          <div class="topnav-icon-sprite me-news"></div>
                          News
                        </a>
                      </li>
                      <li>
                          <a href="/news/catalog/college-essays">
                              <div class="topnav-icon-sprite me-docs"></div>
                              College Essays
                          </a>
                      </li>
                    </ul>
                </li>
                <li style="position:relative;" onmouseover="$('#int-sub-menu').show();" onmouseout="$('#int-sub-menu').hide();" <?php if($currentPage=='international-students' || $currentPage=='international-resources' || $currentPage=='agency-search') { ?> class="active" <?php } ?>>
                  <a href="/international-students">International Students <span class="navigation-arrow-down1">&nbsp;</span></a>
                  <ul class="topnav-college-drop" style="position:absolute;width:257px;display:none;z-index: 1;padding-bottom: 5px;" id="int-sub-menu">
                    <li>
                        <a href="/international-students">
                          <div class="topnav-icon-sprite apply-university"></div>
                          Apply to Universities
                        </a>
                      </li>
                      <li>
                          <a href="/international-resources/main">
                              <div class="topnav-icon-sprite resource-center"></div>
                              Resource Center
                          </a>
                      </li>
                      <li>
                          <a href="/agency-search">
                              <div class="topnav-icon-sprite local-support"></div>
                              Find Local Support
                          </a>
					  </li>
					  <li>
                          <a href="/premium-plans-info">
                              <div class="topnav-icon-sprite plexuss-premium"></div>
                              Plexuss Premium
                          </a>
					  </li>

                    </ul>
                </li>

							<!-- premium  -->
							@if( $currentPage == 'home' ||  $currentPage == 'college-home' ||
							$currentPage == 'portal' || $currentPage == 'ranking' || $currentPage == 'comparison' || $currentPage == 'news' ||
							$currentPage == 'college-essays' || $currentPage == 'profile' || $currentPage == 'international-resources-page'  ||
							(!empty($premium_user_plan) && $currentPage == 'international-students-page') || $currentPage == 'quad-testimonials')
								<li>
									<span class="topnav-btn menuPremium-btn">Premium</span>
								</li>
							@endif


							@if(isset($signed_in) && isset($is_organization))
								<li <?php if($currentPage=='admin') { ?> class="active" <?php } ?>>
									<a href="/admin">Admin</a>
								</li>
							@endif
						@endif

						<!--
						@if( stripos( $currentPage, 'admin' ) !== false )
							<li {{ $currentPage == 'admin-textmsg' ? 'class="active"' : ''}}>
								<a href="/admin/textmsg" class="admin-topnav-items">
									Text Messages
								</a>
							</li>
						@endif
						-->
						@if(isset($is_sales) && isset($is_aor) && $is_aor == 0)

							<li <?php if($currentPage=='sales') { ?> class="active" <?php } ?>>
								<a href="/sales">Sales</a>
							</li>
							<li <?php if($currentPage=='plex-publisher') { ?> class="active" <?php } ?>>
								<a href="/publisher">Publisher</a>
							</li>
						@endif
					<!--
						@if($is_agency == 1)
							<li <?php if($currentPage=='agency') { ?> class="active" <?php } ?>>
								<a href="/agency">Agency</a>
							</li>
						@endif
					-->
						@if(isset($signed_in) && isset($is_organization) && $is_organization == 1)
						<li <?php if($currentPage=='admin-reporting') { ?> class="active" <?php } ?>>
							@if ($currentPage == 'admin')
								<a href="reporting" id='react_route_to_reporting'>Reporting</a>
							@else
								<a href="/admin/reporting">Reporting</a>
							@endif
						</li>
						@endif


					<!-- center nav -->
					@if (!isset($is_sales) && (!isset($is_organization) || $is_organization == 0) && (isset($signed_in) && $signed_in == 1))
						<!-- center nav -->
                        <li style="position:relative;" onmouseover="$('#int-sub-menu1').show();" onmouseout="$('#int-sub-menu1').hide();" class="i-want-to-tab-signedin front-page show-for-medium-up">
                          <a href="#">I want to <span class="navigation-arrow-down1">&nbsp;</span></a>
                          <ul class="topnav-college-drop" style="position:absolute;width:257px;display:none;z-index: 1;" id="int-sub-menu1">
                             <li>
                                  <div class="i-want-to-dropdown" style="display: block !important;">
                                      <span class="dropdown-arrow @if(isset($country_based_on_ip) && $country_based_on_ip === 'US') domestic-arrow @endif">&#9650;</span>
                                      <div class="dropdown-link" data-href="/college">
                                          <div class="dropdown-image research-universities"></div>
                                          <div class="content-label">Research Universities</div>
                                      </div>

                                      <div class="dropdown-link" data-href="/scholarships">
                                          <div class="dropdown-image find-scholarships"></div>
                                          <div class="content-label">Find Scholarships</div>
                                      </div>

                                      <div class="dropdown-link" data-href="/college-application">
                                          <div class="dropdown-image apply-universities"></div>
                                          <div class="content-label">Apply to Universities</div>
                                      </div>

                                      @if (!isset($country_based_on_ip) || $country_based_on_ip !== 'US')
                                          <div class="dropdown-link" data-href="/agency-search">
                                              <div class="dropdown-image talk-to-advisor"></div>
                                              <div class="content-label">Talk to an Advisor</div>
                                          </div>
                                      @endif

                                  </div>
                              </li>
                            </ul>
                        </li>
					</ul>

					@elseif(!isset($is_sales) && (!isset($is_organization) || $is_organization == 0) && (isset($signed_in) && $signed_in == 0))
					</ul> <!-- very important -->
					<ul class="i-want-to-tab front-page show-for-medium-up">
                        <li>
                            <a class="" href="#">I WANT TO <span class="navigation-arrow-down1">&nbsp;</span></a>
                            <div class="i-want-to-dropdown">
                                <span class="dropdown-arrow @if(isset($country_based_on_ip) && $country_based_on_ip === 'US') domestic-arrow @endif">&#9650;</span>
                                <div class="dropdown-link" data-href="/college">
                                    <div class="dropdown-image research-universities"></div>
                                    <div class="content-label">Research Universities</div>
                                </div>

                                <div class="dropdown-link" data-href="/scholarships">
                                    <div class="dropdown-image find-scholarships"></div>
                                    <div class="content-label">Find Scholarships</div>
                                </div>

                                <div class="dropdown-link" data-href="/college-application">
                                    <div class="dropdown-image apply-universities"></div>
                                    <div class="content-label">Apply to Universities</div>
                                </div>

                                @if (!isset($country_based_on_ip) || $country_based_on_ip !== 'US')
                                    <div class="dropdown-link" data-href="/agency-search">
                                        <div class="dropdown-image talk-to-advisor"></div>
                                        <div class="content-label">Talk to an Advisor</div>
                                    </div>
                                @endif

                            </div>
                        </li>
                    </ul>
                    @else
                		</ul> <!-- very important -->
                	@endif

					<!-- Right Nav Section -->
					<ul class="right show-for-medium-up innerpages-lower-nav-ul-rightside">
						{{-- ADMIN PAGES TOPNAV RIGHT SIDE --}}
						@if( stripos( $currentPage, 'admin' ) !== false || isset( $is_organization ) && stripos( $currentPage, 'setting' ) !== false)
							@if(isset($premier_trial_end_date) && isset($is_aor) && $is_aor == 0 && $contract != 2)
							<li class="admin-topnav-premier">
								Days left on Premier <span>{{$premier_trial_end_date}} </span>
							</li>
							@elseif( isset($is_aor) && $is_aor == 1 )
							<li>
								<a href="#" class="admin-topnav-items">{{$org_name}}</a>
							</li>
							@endif
							<!-- Upgrade -->


							{{-- @if(isset($show_upgrade_button) && $show_upgrade_button == 1)
							<li class="admin-topnav-right">
								<a class="button upgrade alert" data-tooltip aria-haspopup="true" class="has-tip" title="@if(isset($requested_upgrade) && $requested_upgrade == 0) Upgrade for Guaranteed Application & Enrollment @else A Plexuss Premier Support Representative will be in contact with you shortly @endif" href="#">

								@if(isset($requested_upgrade) && $requested_upgrade == 0)Upgrade

								@else<span class="upgrade-in-progress">Upgrade in progress</span>@endif</a>
							</li>
							@endif --}}


							@if($contract == 2)
							<li class="admin-balance">
								<div><span class="acct-credit">ACCOUNT CREDIT:&nbsp;&nbsp; <b>${{$balance or 0.00}}</b></span></div>
							</li>
							@elseif(isset($requested_upgrade) && $requested_upgrade == 0)
							<div class="upgradeNotify hide">
								<span>I am ready to Upgrade </span>
								<br/>

								<span>Notify Premier Support Representative </span>
								<br/>
								<a href="#" class="button sendReq">Send</a>
							</div>
							@endif

							<div class="upgradeInProcess hide">
								<span>Upgrade in progress</span>
								<br />
								<span>A Plexuss Premier Support Representative will be in contact with you shortly.</span>
								<br/>
								<a href="#" class="button alreadyReq">OK</a>
							</div>
							<!-- <li class="admin-topnav-right"> -->
							<li class='topnav_admin_logo'
								@if( isset( $school_logo ) )
									style='background-image:url( "{{ $school_logo }}" )'
								@endif
								>

								<!-- is shown when hovering over the school logo on admin pages -->
								@if( isset($school_name))
								<div class="school-info-hover">
									{{ $school_name }}
								</div>
								@endif
							</li>

							<li style="padding: 10px; cursor: pointer">
								@if( $currentPage == 'admin' )
									<a style="padding: 0; margin: 0; line-height: 1;">
										<div id="react-route-to-portal-login-2" style="max-width: 150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden">
											<?php $id = -1; ?>
											@if(isset($default_organization_portal))
												{{$default_organization_portal->name}}
											@elseif($super_admin == 1)
												General
											@elseif(isset($organization_portals))
												<?php $id = $organization_portals[0]->id; ?>
												{{$organization_portals[0]->name}}
											@endif
										</div>
									</a>

								@else
									<a href="/admin" style="padding: 0; margin: 0; line-height: 1;">
										<div style="max-width: 150px; text-overflow: ellipsis; white-space: nowrap; overflow: hidden">
											<?php $id = -1; ?>
											@if(isset($default_organization_portal))
												{{$default_organization_portal->name}}
											@elseif($super_admin == 1)
												General
											@elseif(isset($organization_portals))
												<?php $id = $organization_portals[0]->id; ?>
												{{$organization_portals[0]->name}}
											@endif
										</div>
									</a>
								@endif
							</li>

							@if(false)
							@if( isset($organization_portals) && count($organization_portals) > 0 && $is_aor == 0)
							<li class="BgTop1 hide-for-small portal-nav-item" style="position:relative;">
								<div class="BgTop1 school-list" data-userid="{{Crypt::encrypt($user_id)}}"style="display:table">
									<?php $id = -1; ?>
									@if(isset($default_organization_portal))
										<span>{{$default_organization_portal->name}}</span>
									@elseif($super_admin == 1)
										<span>General--1</span>
									@elseif(isset($organization_portals))
										<?php $id = $organization_portals[0]->id; ?>
										<span>{{$organization_portals[0]->name}}</span>
									@endif
									<span><img src="/images/arrow_down.png"></span>
									<span title="You can switch portals by clicking the drop down arrow" data-tooltip aria-describedby="" data-width="200" class="has-tip tip-left q-mark" aria-haspopup="true">?
									</span>
									<ul id="portal-list">
										@if(isset($organization_portals))
										@foreach($organization_portals as $k)
											@if($id != -1 && $k->id == $id)
												<?php continue; ?>
											@endif
											@if($k->is_default != 1)
											<li><a href="#" class="portal-link" data-userid="{{Crypt::encrypt($k->id)}}">{{$k->name}}</a></li>
											@endif
										@endforeach
										@if($is_aor == 0)
										<li>
											<a href="/settings/manageusers?portals=1">
												<img class="settings-menu-icon activated" src="/images/setting/manager-users-white.png"> &nbsp;Manage Portals
											</a>
										</li>
										@endif
										@endif
									</ul>
								</div>
							</li>
							@endif
							@endif
								<!--
								<span>
									{{ $school_name or '' }}
								</span>
								<ul class="admin-portal">
									<li>
										<a href="#"> Test</a>
									</li>
								</ul>
								<!-- Enable this triangle when dropdown list is built!
								<span class='triangle'>
								</span>

							</li> -->
						{{-- END ADMIN PAGES TOPNAV RIGHT SIDE --}}
						@else

							@if( isset($signed_in) && $signed_in!=0 )
								<li class="hide-for-small">
									<div class='profile_stats_wrapper'>
										<?php
											$className = 'premium-user-icon';
											if(isset($premium_user_plan) && $premium_user_plan == "premium unlimited"){
												if( isset($premium_user_type) && $premium_user_type === 'onetime_plus' ){
													$className = 'premium-user-icon onetime_plus';
												}else{
													$className = 'premium-user-icon onetime';
												}
											}
										?>

										<div id="_premiumUserBadge" class="{{$className}}"></div>


											<div id="_premiumUserBadge" class="{{$className}}"></div>
											<!-- only show upgrade button for intl users-->
											@if( (!isset($premium_user_plan) ) && (!isset($is_organization) || $is_organization == 0 ) && $country_id != 1)

												<div class="upgrade-to-premium-container">
													<div class="upgrade-to-premium-btn">Upgrade</div>
													<div class="upgrade-tooltip">
														<div>Stand out to colleges as an elite member. Upgrade to premium!</div>
														<div class="upgrade-tooltip-arrow"></div>
													</div>
												</div>

											@endif
										<div class="profile-status-meter" onclick="HideNotiBox();"
											onmouseenter="$('.profile-tooltip').show(); return false;"
											onmouseleave=" $('.profile-tooltip').hide(); return false;">

											<!-- tooltip -->
											<?php

												$p = 'partially';

												if(isset($profile_percent) && $profile_percent != ''){
													$p = $profile_percent.'%';
												}


											?>


											<!-- <span class="profile-meter-title">Profile Status: </span> -->
											<span id="ProfileScore" onclick="HideNotiBox();">0</span>
											<span class="profscore-details">% &nbsp;&mdash;</span>
											<div style="transform: rotateZ(-95deg); display: inline-block;">
												<input class="indicator_noti" id="indicator_noti" value="25"  style="display:none;">
											</div>
											<div class="profile-tooltip">
												<div class="prof-tool-arrow"></div>
												Your profile is {{$p}} complete. To edit your profile click on the <a href="/profile">Me</a> tab
											</div>
										</div>

										<?php /*?><img src="/images/top-noti-icon-0.png" id="IndicatorIcon" style="margin-top:-3px;" /><?php */?>
									</div>

									<div id="NotificationAreaTop" style="display:none;"></div>
									<?php /*?><div class="noti-main-div noti-main-div-red">
										<span class="arrow-top"></span>
										<div class="noti-inner-div1">
											<div class="noti-title"><div class="title-icon"><img src="/images/noti-red.png"></div>You are not ready to be recruited.</div>
											<div class="noti-subtitle">FILL OUT YOUR PROFILE</div>
											<div class="noti-desc">You need to fill out your Profile  to unlock your Grades section so you can become ready for recruitment.</div>
										</div>
										<div class="noti-inner-div2">
											YOU’RE AT 0%
										</div>
									</div><?php */?>
								</li>
							@endif

							@if (isset($signed_in) && $signed_in === 1)
								<li class="BgTop1 show-for-medium-up"> <span class="topnav-divide">|</span> <a id="what-is-next" class="BgTop1" onclick="whatsNext()">  &nbsp;  What's next?</a> <!-- $('#WhatsNext').toggle(); -->
									<div id="whatsNext" >
										<div id="WhatsNextComponents">
											You are now recruitment ready!
											<!-- react whats next component rendered here -->
										</div>
										<!--<div style="width: 50px; height: 50px; position: relative; margin: 0 auto;">
											<img src="/images/ajax_loader.gif"/>
										</div>-->
									</div>
								</li>
							@endif
						@endif
					</ul>
		</section >
			@endif


</nav>
@endif
</div>
</div>
</div>
<!-- \\\\\\\\\\\\\\\\\\\\\\\\\\\ top nav - end ////////////////////////////////// -->

@if(isset($currentPage) && $currentPage != 'terms-of-service')
@include('public.registration.premiumSignupModal')
@endif

<!-- upgrade to premium modal -->
@if (isset($signed_in) && $signed_in === 1)
    @include('private.includes.upgradeToPremiumModalNew')
@endif



<!-- Common notification popup call in js for alert message -->
<div id="Notification-Popup" class="reveal-modal tiny radius" data-reveal>
    <div class="alert-msg-div" align="center">

    </div>
<a class="close-reveal-modal c-black">&#215;</a>
</div>
<!-- Common notification popup call in js for alert message -->

<script type="application/javascript">
function show_profile_block(){
	if(document.getElementById('ProfileMenu').style.visibility=="visible"){
	document.getElementById('ProfileMenu').style.visibility="hidden";
	}
	else
	{
	document.getElementById('ProfileMenu').style.visibility="visible";
	}
}
function show_news_sortbymenu(){
	if(document.getElementById('news-sort-by-menu').style.visibility=="visible"){
	document.getElementById('news-sort-by-menu').style.visibility="hidden";
	}
	else
	{
	document.getElementById('news-sort-by-menu').style.visibility="visible";
	}
}
function show_news_filters(){
	if(document.getElementById('news-filter-menu').style.visibility=="visible"){
	document.getElementById('news-filter-menu').style.visibility="hidden";
	}
	else
	{
	document.getElementById('news-filter-menu').style.visibility="visible";
	}
}

function toggle_menu_button(){
	$('#menu-toggler').trigger('click');
	//$('.top-bar').css({'height':'auto !important'});
}
function HideNotiBox(){

	if($('#NotificationAreaTop').css("display")=='none')
	{
		$('#NotificationAreaTop').fadeIn(1000).mouseenter(function(){
			$('#NotificationAreaTop').show();
		});
		$('#NotificationAreaTop').mouseleave(function(){
			setTimeout(function() {
				$('#NotificationAreaTop').fadeOut(2000);
			}, 1000);
		});
	}
	else
	{
		$('#NotificationAreaTop').hide();
	}
}

function HideNotiBox1(){

	if($('#NotificationAreaTopMobile').css("visibility")=='hidden')
	{
		$('#NotificationAreaTopMobile').css("visibility", "visible");
	}
	else
	{
		$('#NotificationAreaTopMobile').css("visibility", "hidden");
	}
}


//ShowSearchMobile();

function whatsNext(action, skip){

	// Don't toggle if user clicks 'Save & Continue'
	// Don't toggle if user clicks 'skip'
	if(action != 'saveCont' && (!skip || skip == '')){
		$('#whatsNext').slideToggle('250', 'easeInOutExpo');
		//don't run anything if user clicks to hide
		if(!$('#whatsNext').is(':visible')){
			return;
		}
	}

	if(!action){
		var action = "init";
	}

	// $.ajax({
	// 	url:"/ajax/whatsNext",
	// 	type: "GET",
	// 	data: {'action':action, 'skip':skip},
	// 	success: function(wnDataReturn){
	// 		$('#whatsNext').html(wnDataReturn);
	// 		// Evaluates JS returned by AJAX
	// 		// $("#whatsNext").find("script").last().each(function(i) {
	// 		// 	eval($(this).text());
	// 	 //   });
	// 	}
	// });
}


function currentPageIndicator( element, tab ){

	var menuListParent;
	var backBtn;
	var newBackButtonHTML = '';

	if( tab == 'Profile' ){
		menuListParent = $(element).parent('#profile-list');
		backBtn = $(menuListParent).find('li.back');
	}else if( tab == "Accomplishments"){
		menuListParent = $(element).parent('#AccomplishmentMenuMobile');
		backBtn = $(menuListParent).find('li.back');
	}else if( tab == 'Settings' ){
		menuListParent = $(element).parent('#settingsMobileMenuTab');
		backBtn = $(menuListParent).find('li.back');
	}

	newBackButtonHTML += '<h5><a href="javascript:void(0)" class="nestedBackColor">';
	newBackButtonHTML += '<div class="row backBtnRow">';
	newBackButtonHTML += '<div class="column small-1 backBtnColumn">';
	newBackButtonHTML += '<img class="show-for-small-only backBtnArrow mobile-menu-arrow" src="/images/mobile_menu_arrow.png" alt="Nav Arrow">';
	newBackButtonHTML += '</div>';
	newBackButtonHTML += '<div class="column small-3">';
	newBackButtonHTML += 'Back';
	newBackButtonHTML += '</div>';
	newBackButtonHTML += '<div class="column small-7 end small-text-right">';
	newBackButtonHTML += tab;
	newBackButtonHTML += '</div>';
	newBackButtonHTML += '</div>';
	newBackButtonHTML += '</a></h5>';

	//inject new back button row
	$(backBtn).html(newBackButtonHTML);
}





</script>




<!-- /////////////////// new main search for mobile - start \\\\\\\\\\\\\\\\\\\\\ -->
<div class="row hide-for-medium-up">
	<div class="small-12 new-mobile-search-plex-row">

		<div class="mobile-plex-search-container">
			<div class="plex-search-logo"></div>
			<input type="text" placeholder="Search Plexuss" id="top_search_txt" class="top_search_txt mobile-top-nav-search-new" data-input required>
			<input type="hidden" class="top_search_txt_val" value="" />
			<div class="mobile-plex-search-btn" onclick="redirectSearch();"></div>
			<input type="hidden" class="top_search_type" value="" />
		</div>

		<!--<div class="row collapse">
			<div class="column small-10">

			</div>

			<div class="column small-2 main-mobile-search-btn-col">
				<input type="hidden" id="top_search_type" class="top_search_type" value="" />
				<div class="go-btn button postfix" onclick="redirectSearch();"></div>
			</div>
		</div>-->

	</div>
	<div class="searchautocomplete"></div>
</div>
<!-- /////////////////// new main search for mobile - end \\\\\\\\\\\\\\\\\\\\\ -->




<div id="NotificationAreaTopMobile" class="show-for-small-only abs-wrapper" style="display:block;visibility:hidden;"></div>
<!-- Orange Alert Box - Used for messages -->

<!-- //////////////////// topAlert boxes \\\\\\\\\\\\\\\\\\\\ -->
<div id='topAlertContainer'>
	<div id='topAlert'>
		<div id='taSoftContainer'>
		</div>
		<div id='taHardContainer'>
		</div>
	</div>
</div>
<!-- \\\\\\\\\\\\\\\\\\\\ topAlert boxes  //////////////////// -->


<!-- js for react SIC -->
<script type="text/javascript" src="/js/reactSIChandlers.js" defer></script>
