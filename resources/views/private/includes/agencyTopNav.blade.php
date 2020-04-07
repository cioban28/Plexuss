<?php
/*
	echo "<pre>";
	dd( $data );
 */
 	// dd($data);
?>
@if ($currentPage == 'home' && isset($showFirstTimeHomepageModal))
    <!-- Facebook Conversion Code for fb -->
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
        })();

        window._fbq = window._fbq || [];
        window._fbq.push(['track', '6006470317719', {'value':'0.01','currency':'USD'}]);
    </script>

    <noscript>
        <img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?ev=6006470317719&amp;cd[value]=0.01&amp;cd[currency]=USD&amp;noscript=1" />
    </noscript>
@endif

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
</script>
<noscript>
    <img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?id=1428934937356789&amp;ev=PixelInitialized" />
</noscript>




<!-- \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ inner non-frontpage top nav - start //////////////////////////////////////// -->

<!-- Daily chat bar! - start  -->
@if($is_any_school_live == true && (isset($is_mobile) && $is_mobile == false))
<div class="row daily-chat-bar-container" id="_chat_bar">
    <div class="column small-12">
        <div class="blip"></div>
        <div class="chat-label"><strong>College daily chat is happening now.</strong> <a href="/chat"><i>See which colleges are online to talk with you</i></a></div>
    </div>
    <script>
		var chat_bar = document.getElementById('_chat_bar'),
			path = window.location.pathname;

		if( chat_bar && path === '/agency/settings' ) chat_bar.style.display = 'none';
	</script>
</div>
@endif
<!-- Daily chat bar! - end -->


@if ( isset($signed_in) && $signed_in == 1) 
<div class='BgTop'>
@else
<div class='BgTop BgTop-notSignedIn hide-for-small-only'>
@endif
	<div class='row' id="innerpages-topnav">

		<!-- logo -->
		<div class='column small-12 medium-3 show-for-medium-up'>
			<ul class="title-area ">
				<li class="name">
					<a href="/"><img class="plex_logo_resize" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/premium_page/plexuss-white.png" alt='logo'/></a>

                    <span class='plexuss-student-network-text'>The Global Student Network</span>
				</li>
			</ul>
		</div>


		<!-- medium up search bar - DO NOT REMOVE -->
		<div class='column small-12 medium-offset-1 medium-6 large-offet-2'>
			<div class='mobilesearch' id="SearchMobileDiv" >
				<ul class='searchBar-padding-fix'>
					<li id="topnavsearch" class="row search-container collapse">
						<div class='column small-10 wrapper'>
							<div class="search-default-txt" onclick="setSearch();"></div>
							<input type="text" placeholder="Search Plexuss.." style="border: solid 1px black;" id="top_search_txt" class="top_search_txt" data-input>
							<input type="hidden" class="top_search_txt_val" value="" />
							
							<div class="top_search_filter d-none closedivset">
								<ul>									
									<li id="default"><img alt="image" src="/images/select-logo.png" /> <span>Search All of Plexuss..</span></li>
									<li id="college"><img alt="image" src="/images/select-collge-logo.png" /> <span>Search Colleges..</span></li>
									<li id="news"><img alt="image" src="/images/select-news-logo.png" /> <span>Search News..</span></li>
								</ul>
							</div>
						</div>
						<div class='column small-2'>
							<input type="hidden" class="top_search_type" value="" />
							<button class="go-btn" onclick="redirectSearch();"></button>
						</div>
						<div class="searchautocomplete"></div>
					</li>
				</ul>
			</div>	
		</div>


		<!-- sign out button area -->
		<div class='column small-12 medium-4 large-2 mobile-top-nav-notification-pane-styling'>
			<div class="topnav_buttons text-center">
				<!-- Messages menu toggle -->
				@if ( isset($signed_in) && $signed_in == 1) 
				<div class="row show-for-medium-up">

					<!-- messages notifications -->
					<div id='notify_msg' class="topnav_button notify_button medium-2 column notify_msg_unique">
						<img class='topnav_notify_image' src="/images/nav-icons/messages-white.png" />
						@if(isset($topnav_messages['unread_cnt']) && $topnav_messages['unread_cnt'] != 0)
							<div class="unread_count active">{{$topnav_messages['unread_cnt'] or ''}}</div>
						@else
							<div class="unread_count"></div>
						@endif
						<div class="open-notification-pane-arrow">
                            <div class="top-nav-noti-pan-arrow top-nav-msg-noti-arrow"></div>
                        </div>
					</div>

					<!-- all notifications -->
					<div id='notify_all' class="topnav_button notify_button medium-2 column notify_all_unique">
						<img class='topnav_notify_image' src="/images/nav-icons/notifications-white.png" />
						@if(isset($topnav_notifications['unread_cnt']) && $topnav_notifications['unread_cnt'] != 0)
							<div class="unread_count active">{{$topnav_notifications['unread_cnt'] or ''}}</div>
						@else
							<div class="unread_count"></div>
						@endif
						<div class="open-notification-pane-arrow">
                            <div class="top-nav-noti-pan-arrow top-nav-all-noti-arrow"></div>
                        </div>
					</div>
					
					<!-- profile image -->
					<div class="topnav_button medium-2 column settings-menu-icon-section end">
						<img alt='' src={{ $profile_img_loc or "/images/profile/default.png" }} class="user-profile-image" />
						<div class="open-notification-pane-arrow">
                            <div class="top-nav-settings-arrow"></div>
                        </div>
					</div>

					<!-- sign out -->
	{{-- 				<div class="topnav_button medium-4 column">
						<a class="logout-link" href="/signout">Sign Out</a>
					</div> --}}
				</div>
				@else
				<div class="row collapse signedOut_signupLogin_row topnav_button show-for-medium-up">
					<div class="column small-offset-3 small-4 large-offset-5 large-3 loginBtnHomepage">
						<a href="/signin">Login</a>
					</div>
					<div class="column small-5 large-4 signupBtnHomepage">
						<a href="/signup?utm_source=SEO&utm_medium={{$currentPage or ''}}">Sign up</a>
					</div>
				</div>
				@endif
				<!-- MESSAGE NOTIFICATIONS PANE -->
				<div class='row'>
					<div id='notify_msg_pane' class='small-12 column notify_pane'>
						<div class='row'>
							<div class='small-12 column notify_pane_container'>
								<!-- new notifications go here!!! -->
								<!-- NOTI 1 -->

								@if(isset($topnav_messages))
									@foreach ($topnav_messages['data'] as $note)
										<div class='row notify_item' onClick="notificationItemOnClick('{{$note['link'] or '/'}}');">
											<div class='small-2 column notify_image'>
												<img src='{{$note['img']  or "/images/profile/default.png" }}'/>
											</div>
											<div class='small-8 column'>
												<div class='row'>
													<div class='small-12 column notify_title'>
														<span>
															{{$note['Name'] or ''}}
														</span>
													</div>
												</div>
												<div class='row'>
													<div class='small-12 column notify_snippet'>
														<span>
															{{$note['msg'] or ''}}
														</span>
													</div>
												</div>
											</div>
											<div class='small-2 column notify_date'>
												<span>
													{{$note['date'] or ''}}
												</span>
											</div>
										</div>
									@endforeach
								@endif
								
								<!-- end notifications section -->
							</div>
						</div>
						<div class='row view_all_wrapper'>
							<a href='/portal/messages'>
								<div class='small-12 column view_all_button text-center'>
									View all messages
								</div>
							</a>
						</div>
					</div>
				</div>
				<!-- END MESSAGE NOTIFICATIONS PANE -->
				<!-- NOTIFICATIONS PANE -->
				<div class='row'>
					<div id='notify_all_pane' class='small-12 column notify_pane'>
						<div class='row'>
							<div class='small-12 column notify_pane_container'>
								<!-- new notifications go here!!! -->
								@if(isset($topnav_notifications))
									@foreach ($topnav_notifications['data'] as $note)

										<div class="row notify_page_notify_item" onclick="notificationItemOnClick('{{$note['link'] or '/'}}');">
											<div class='small-2 medium-1 columns {{$note['img'] or ''}}'></div>

											<div class='small-8 column notify_message'>
												
												<span class='notify_title'>{{$note['name'] or ''}}</span>
												<span class='notify_snippet'>{{$note['msg'] or ''}}</span>
											</div>
											<div class='small-2 medium-3 column text-right'>
												<span class='notify_time'>
													{{$note['date'] or ''}}
												</span>
											</div>
										</div>

									@endforeach
									<!-- end notifications section -->
								@else
									<div class='row notify_page_notify_item'>
										<div class='small-12 column notify_message'>
											No Notification available
										</div>
									</div>

								@endif
							</div>
						</div>
						<div class='row view_all_wrapper'>
							<a href='/notifications'>
								<div class='small-12 column view_all_button text-center'>
									View all notifications
								</div>
							</a>
						</div>
					</div>
				</div>
				<!-- end notifications pane -->

				<!-- settings dropdown menu pane - start -->
				<div class="row settings-topnav-menu-pane agency-profile-settings">
					<div class="column small-12">
						<div class='agency-settings settings-top-container'>
							<div>
								<img alt='' src={{ $profile_img_loc or "/images/profile/default.png" }} class="user-profile-image" />
							</div>
							<div>
								<div class='agent-name'>{{ $fname . ' ' . $lname }}</div>
{{-- 								<a href="/settings">
									<div class='edit-profile-btn'><u>Edit Profile</u></div>
								</a> --}}
							</div>
						</div>
						
						<a href="/agency/settings">
							<div class="row collapse settings-topnav-menu-item">
								<div class="column small-12 text-center">
									Settings
								</div>
							</div>
						</a>
						<a href="/signout">
							<div class="row collapse settings-topnav-menu-item">
								<div class="column small-12 text-center">
									Signout
								</div>
							</div>
						</a>

					</div>
				</div>
				<!-- settings dropdown menu pane - end -->
			</div>
		</div>


	</div>
</div>
<!-- \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ inner non-frontpage top nav - end //////////////////////////////////////// -->






























<!-- \\\\\\\\\\\\\\\\\\\\\\\\\\\ inner mobile top nav - start ////////////////////////////////// -->
<div class="contain-to-grid BgTop1 clearfix">
<div class="row collapse" id="innerpages-topnav-second">
	<div class="small-12 columns">

		<nav class="top-bar" data-topbar data-options="is_hover: true" role="navigation">

			<!-- <div class="mobile-menu-header left"><a class="custome-mobile-button" onclick="toggle_menu_button()" href="#">&nbsp;</a></div>
			<ul  class="title-area">
				<li class="toggle-topbar menu-icon left custome-mobile-button" style="border: thin solid red">
					<a id="menu-toggler" href="#"></a>
				</li>-->
				<!--<li class="name"> Do not remove this name box</li> -->
				
				<!-- mobile notification buttons 
				<li class='right show-for-small-only' style='margin-top:7px; margin-right: 7px; border: thin solid red'>

					@if ( isset($signed_in) && $signed_in !=0 )
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



			<!-- ////////// new mobile top nav with notifications icons - start \\\\\\\\\ -->
			<div class="row collapse title-area show-for-small-only restructured-mobile-top-nav">

				<!-- mobile menu toggler hamburger icon -->
				<div class="column small-2 mobile-hamburger-menu-toggler-container toggle-topbar">
					<a id="menu-toggler" class="mobile-hamburger-menu-toggler-icon" href="#"></a>
				</div>

				<!-- mobile profile status icon -->
				<div class="column small-5 text-center">
					@if ( isset($signed_in) && $signed_in != 0 )
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
							<span style="font-size:11px;cursor:pointer;" id="MobilePercentValue" onclick="HideNotiBox1();">&nbsp;0%</span>
						</div>
					</div>
					@endif
				</div>

				<div class="column small-5">
					<div class="row collapse">
						
						<!-- mobile messages notification icon -->
						<div class="column small-4 notify_msg_unique text-center topnav_button notify_button">
							@if( isset($signed_in) && $signed_in != 0 )
								<img class="nav-noti-icon-img" alt="image" src="/images/nav-icons/mobile-messages-icon.jpg" />
								@if(isset($topnav_messages['unread_cnt']) && $topnav_messages['unread_cnt'] != 0)
		                            <div class="messages unread_count active">{{$topnav_messages['unread_cnt'] or ''}}</div>
		                        @else
		                            <div class="messages unread_count"></div>
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
						<div class="column small-4 text-center" onclick="ShowSearchMobile();">
							<img class="nav-search-icon-img" alt="image" src="/images/nav-icons/topnav_search_icon.jpg" />
						</div>

					</div>
				</div>

				
			</div>
			<!-- \\\\\\\\\\\\ new mobile top nav with notifications icons - end ///////////// -->

			


			{{-- MOBILE NAV MENU --}}
			<!-- Left Nav Section -->
			<section  class="top-bar-section">
				<!-- Current Page Indicator -->

				<!-- <div class="row show-for-small-only">
					<div class="column small-12 small-text-right mobile-menu-current_page_indicator">
						Home
					</div>
				</div> -->
				<ul class="show-for-small-only MobileNav topLevelListMenu">
					<!-- Home tab -->
					<?php
					// @if( isset($signed_in) && $signed_in == 1 )
					// <li class="backFillFix">
					// 	<a href="/home" class="mobile_nav_tab_home">
     //                    	<div class="row">
     //                    		<div class="column small-2">
     //                    			<img class="mobile-nav-icon-img-new" alt='navimage' src="/images/mobile_nav_icons/home.png" />
     //                    		</div>
     //                    		<div class="column small-10">Home</div>
     //                    	</div>
     //                    </a>
     //               	</li>
     //               	@endif
                   	?>
                   	@if(isset($signed_in))
						@if( stripos( $currentPage, 'admin' ) === false )
						<!-- Profile tab -->
							@if( isset($signed_in) && $signed_in == 1 )
							<li class="has-dropdown backFillFix" id="profile-list">
		                        <a href="" onclick="currentPageIndicator(this, 'Profile');" class="mobile_nav_tab_profile">
		                        	<div class="row">
		                        		<div class="column small-2">
		                        			<img class="mobile-nav-icon-img-new" alt='nav-image' src="/images/mobile_nav_icons/profile.png" />
		                        		</div>
		                        		<div class="column small-9">Profile</div>
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

						@if( stripos( $currentPage, 'agency' ) === false )
						<!-- Profile tab -->
							@if( isset($signed_in) && $signed_in == 1 )
							<li class="has-dropdown backFillFix" id="profile-list">
		                        <a href="" onclick="currentPageIndicator(this, 'Profile');" class="mobile_nav_tab_profile">
		                        	<div class="row">
		                        		<div class="column small-2">
		                        			<img class="mobile-nav-icon-img-new" alt='nav-image' src="/images/mobile_nav_icons/profile.png" />
		                        		</div>
		                        		<div class="column small-9">Profile</div>
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
					@endif
					{{-- IF ON ADMIN PAGE, SHOW ADMIN LINKS --}}
					@if( stripos( $currentPage, 'admin' ) !== false )
						<!-- Dashboard link -->
						<li class="backFillFix">
							<a href="/admin" class="mobile_nav_tab_admin">
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
					{{-- end: admin specific mobile nav links --}}

					{{-- IF ON agency PAGE, SHOW agency LINKS --}}
					@elseif( stripos( $currentPage, 'agency' ) !== false )
						<!-- Dashboard link -->
						<li class="backFillFix">
							<a href="/agency" class="mobile_nav_tab_admin">
								<div class="row">
									<div class="column small-2">
										<img class="mobile-nav-icon-img-new" alt='none' src="/images/mobile_nav_icons/portal.png" />
									</div>
									<div class="column small-10">Dashboard</div>
								</div>
							</a>
						</li>
						<!-- manage students link -->
						<li class="backFillFix">
							<a href="/agency/recommendations" class="mobile_nav_tab_portal">
								<div class="row">
									<div class="column small-2">
										<img class="mobile-nav-icon-img-new" alt='none' src="/images/mobile_nav_icons/inquiries.png" />
									</div>
									<div class="column small-10">Manage Students</div>
								</div>
							</a>
						</li>
						<!-- Campaigns link -->
						<li class="backFillFix">
							<a href="/agency/groupmsg" class="mobile_nav_tab_portal">
								<div class="row">
									<div class="column small-2">
										<img class="mobile-nav-icon-img-new" alt='none' src="/images/mobile_nav_icons/inquiries.png" />
									</div>
									<div class="column small-10">Campaigns</div>
								</div>
							</a>
						</li>
						<!-- messages link -->
						<li class="backFillFix">
							<a href="/agency/messages" class="mobile_nav_tab_portal">
								<div class="row">
									<div class="column small-2">
										<img class="mobile-nav-icon-img-new" alt='none' src="/images/mobile_nav_icons/messages.png" />
									</div>
									<div class="column small-10">Messages</div>
								</div>
							</a>
						</li>
					{{-- end: agency specific mobile nav links --}}
					@else
						<!-- Portal tab -->
						@if( isset($signed_in) && $signed_in == 1 )
						<li class="backFillFix">
							<a href="/portal" class="mobile_nav_tab_portal">
								<div class="row">
									<div class="column small-2">
										<img class="mobile-nav-icon-img-new" alt='none' src="/images/mobile_nav_icons/portal.png" />
									</div>
									<div class="column small-10">Portal</div>
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
									<div class="column small-10">Colleges</div>
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
									<div class="column small-10">Ranking</div>
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
									<div class="column small-10">Compare Colleges</div>
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
									<div class="column small-10">The Quad</div>
								</div>
							</a>
						</li>
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
					@if( isset( $signed_in ) && isset( $is_organization ) && stripos( $currentPage, 'admin' ) === false )
						<!-- admin dashboard -->
						<li class="backFillFix">
							<a href="/admin" class="mobile_nav_tab_admin">
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
						<li <?php if($currentPage=='sales') { ?> class="active" <?php } ?>>
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
		                			<div class="column small-9">Settings</div>
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
	                			<li class="backFillFix">
	                				<a href="/settings/invite" class="nestedBackColor">
	                					Invite Friends
	                				</a>
	                			</li>
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
		                			<div class="column small-10">Sign Out</div>
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
		                			<div class="column small-10">Sign In</div>
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
							<div class="column small-12 pt15">
								<div><span><a class="mobile_menu_no_btm_border" href="/about">Company</a></span> | <span><a class="mobile_menu_no_btm_border" href="/about">Information</a></span></div>
							</div>
						</div>
					</li>
				</ul><!-- end of initial menu list -->

			<ul class="left show-for-medium-up innerpages-lower-nav-ul-leftside">
				<!-- @if ( isset($signed_in) && $signed_in == 1 ) -->
					<!-- <li class=" @if($currentPage=='home') active @endif">
						<a href="/home" id="topNavHomeNoLeft">Home</a>
					</li> -->
					
				<!-- @endif -->

				{{-- ADMIN PAGES TOPNAV LINKS --}}
				@if( stripos( $currentPage, 'admin' ) !== false && ( isset($is_agency) && $is_agency == 0 ) )
					<!-- Dashboard link -->
					<li {{ $currentPage == 'admin' ? 'class="active"' : '' }}>
						<a href='/admin' class="admin-topnav-items">
							Dashboard
						</a>
					</li>

					<!-- Chat link -->
					<li {{ $currentPage == 'admin-chat' ? 'class="active"' : '' }}>
						<a href='/admin/chat' class="admin-topnav-items">
							Chat
						</a>
					</li>
					
					<!-- Messages link -->
					<li {{ $currentPage == 'admin-messages' ? 'class="active"' : '' }}>
						<a href='/admin/messages' class="admin-topnav-items">
							Messages
						</a>
					</li>

					<!-- Manage Students link -->
					@if ($currentPage == 'admin-inquiries' || $currentPage == 'admin-pending' || $currentPage == 'admin-approved' || $currentPage == 'admin-recommendations' || $currentPage == 'admin-removed' || $currentPage == 'admin-rejected' )
						<li {{ 'class="active"'}}>
					@else
						<li {{''}}>
					@endif
						<a href='/admin/inquiries' class="admin-topnav-items">
							Manage Students
						</a>
					</li>


				{{-- END ADMIN PAGES TOPNAV LINKS --}}
				
				{{-- agency PAGES TOPNAV LINKS --}}
				@elseif( stripos( $currentPage, 'agency' ) !== false || ($currentPage == 'admin-adv-filtering' && $is_agency == 1) )
					<!-- Dashboard link -->
					<li class={{ $currentPage == 'agency' ? 'active' : '' }}>
						<a href='/agency' class="admin-topnav-items">
							Dashboard
						</a>
					</li>

					<!-- Manage Students link -->
					@if ($currentPage == 'agency-inquiries' || $currentPage == 'agency-pending' || $currentPage == 'agency-approved' || $currentPage == 'agency-recommendations' || $currentPage == 'agency-removed' || $currentPage == 'agency-rejected' ||
						$currentPage == 'agency-leads' || $currentPage == 'agency-opportunities' || $currentPage == 'agency-applications' )
						<li class={{'active'}}>
					@else
						<li {{''}}>
					@endif
						<a href='/agency/inquiries' class="admin-topnav-items">
							Recruitment
						</a>
					</li>

					<!-- Messages link -->
					<li class={{ $currentPage == 'agency-messaging' ? 'active' : '' }}>
						<a href='/agency/messages' class="admin-topnav-items">
							Messaging
						</a>
					</li>

					{{-- Reporting Link --}}
					<li class={{ $currentPage == 'agency-reporting' ? 'active' : '' }}>
						<a href='/agency/reporting' class="admin-topnav-items">
							Reporting
						</a>
					</li>

                    {{-- Reporting Link --}}
                    <li class={{ $currentPage == 'agency-video-tutorial' ? 'active' : '' }}>
                        <a href='/agency/video-tutorial' class="admin-topnav-items">
                            Video Tutorial
                        </a>
                    </li>

{{-- 					<li class="has-dropdown agency-settings-dropdown">
						<a href="/agency/settings">Settings</a>
						<ul class="agency-settings-drop dropdown">
							<li><a class="agency-setting-option" href="/agency/settings/profileInfo">Profile Info</a></li>
							<li><a class="agency-setting-option" href="/agency/settings/paymentInfo">Payment Info</a></li>
							<li><a class="agency-setting-option" href="/agency/filter">Recommendation Filter</a></li>
						</ul>	
					</li> --}}

					<!-- <li 
					{{-- {{ $currentPage == 'agency-groupmsg' ? 'class="active"' : '' }}> --}}
						<a href="/agency/groupmsg">Campaigns</a>
					</li> -->


				{{-- END AGENCY PAGES TOPNAV LINKS --}}
				@else
					@if( isset($signed_in) && $signed_in == 1 )
					<li <?php if($currentPage=='portal') { ?> class="active" <?php } ?>>
						<a href="/portal">Portal</a>
					</li>
					@endif
					<li <?php if($currentPage=='college-home' || $currentPage=='ranking' || $currentPage=='comparison') { ?> class="active" <?php } ?> style="position:relative;" onmouseover="$('#college-sub-menu').show();" onmouseout="$('#college-sub-menu').hide();">
						<a class="mainTopNav_collegeTab" href="/college">
							Colleges <span class="navigation-arrow-down1">&nbsp;</span>
						</a>
						<ul style="position:absolute;width:257px;display:none;" id="college-sub-menu">
							<li>
								<a href="/college">
									<img id='public_college_search_icon' src="/images/search-gray.png" data-src="/images/search1.png">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Find Colleges
								</a>
							</li>
							<li>
								<a href="/ranking">
									<img src="/images/ranking/ranking_icon.png" data-src="/images/ranking/ranking_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp;Ranking
								</a>
							</li>
							<div class="clearfix" style="border-top: 1px #ff0000;"></div>
							<li>
								<a href="/comparison">
									<img src="/images/ranking/compare_icon.png" data-src="/images/ranking/compare_icon_hover.png" />&nbsp;&nbsp;Compare Colleges
								</a>
							</li>
						</ul>
					</li>
					<li <?php if($currentPage=='news') { ?> class="active" <?php } ?>>
						<a href="/news">The Quad</a>
					</li>
					@if(isset($signed_in) && isset($is_organization))
						<li <?php if($currentPage=='admin') { ?> class="active" <?php } ?>>
							<a href="/admin">Admin</a>
						</li>
					@endif
				@endif

				@if(isset($is_sales))
					<li <?php if($currentPage=='sales') { ?> class="active" <?php } ?>>
						<a href="/sales">Sales</a>
					</li>
				@endif
			</ul>
			
			<!-- Right Nav Section -->
			
			<ul class="right show-for-medium-up innerpages-lower-nav-ul-rightside">
				{{-- ADMIN PAGES TOPNAV RIGHT SIDE --}}
				@if( substr($currentPage, 0, 5) == 'admin')
					<li class="admin-topnav-right">
						<div class='topnav_admin_logo'
						@if( isset( $school_logo ) )
							style='background-image:url( "{{ $school_logo }}" )'
						@endif
						></div>
						<span>
							{{ $school_name or '' }}
						</span>
						<!-- Enable this triangle when dropdown list is built!
						<span class='triangle'>
						</span>
						-->
					</li>
				{{-- END ADMIN PAGES TOPNAV RIGHT SIDE --}}
				@elseif( stripos( $currentPage, 'agency' ) !== false )
						<li class="admin-topnav-right">
							<div class='topnav_admin_logo'
							@if( isset( $school_logo ) )
								style='background-image:url( "{{ $school_logo }}" )'
							@endif
							></div>
							<span data-tooltip aria-haspopup="true" title='{{ $agency_name or '' }}'>
								{{-- General --}}
							</span>

							<!-- Enable this triangle when dropdown list is built!
							<span class='triangle'>
							</span>
							-->
						</li>
{{-- 					<li class="agency-balance">
						@if($agency_collection->is_trial_period == 0)

							<div><span class="acct-credit">ACCOUNT CREDIT:&nbsp;&nbsp; <b>${{$balance or 0}}</b></span> 
							<span class="agency-topnav-sep">&nbsp;&nbsp;|&nbsp;&nbsp;</span>
							<span><a href="/agency/settings/paymentInfo"><u>Add credit</u></a></span></div>

						@else
							<div><span class="acct-credit"><b><u>{{$remaining_trial or 0}}</u> DAYS LEFT OF TRIAL PERIOD</b></span> 
							<span class="agency-topnav-sep">&nbsp;&nbsp;|&nbsp;&nbsp;</span> 
							<span><a href="/agency/settings/paymentInfo"><u>Add payment info</u></a></span></div>
						@endif
					</li> --}}

				@else
					@if( isset($signed_in) && $signed_in!=0 )
						<li class="hide-for-small" style="border-right:solid 1px #fff;">
							<div class='profile_stats_wrapper' onclick="HideNotiBox();">
								<span class="hide-status-text-on-tablet">Profile Status:&nbsp;&nbsp;</span>
								<div class="profile-status-meter">
								<input class="indicator_noti" id="indicator_noti" value="25"  style="display:none;"></div>
								<span id="ProfileScore">0</span>%
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

				<li class="BgTop1 show-for-medium-up clearfix"> <a class="BgTop1 nav-small-text " onclick="whatsNext()">What's Next</a> <!-- $('#WhatsNext').toggle(); -->
					<div id="whatsNext" >
						<div style="width: 50px; height: 50px; position: relative; margin: 0 auto;">
							<img src="/images/ajax_loader.gif"/>
						</div>
					</div>
				</li>
				@endif
			</ul>
</section >
</nav>
</div>
</div>
</div>
<!-- \\\\\\\\\\\\\\\\\\\\\\\\\\\ inner mobile top nav - end ////////////////////////////////// -->



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

	$.ajax({
		url:"/ajax/whatsNext",
		type: "GET",
		data: {'action':action, 'skip':skip},
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}, 
		success: function(wnDataReturn){
			$('#whatsNext').html(wnDataReturn);
			// Evaluates JS returned by AJAX
			$("#whatsNext").find("script").each(function(i) {
				eval($(this).text());
		   });
		}
	});
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

function dynamicActivePage( currentPage, activeTab ){

	var activeMenuItem = '.mobile_nav_tab_' + activeTab;
	var profileTab = $('.mobile_nav_tab_profile');
	var accomplishmentsTab = $('.mobile_nav_tab_accomplishments');
	var accomplishmentsList = ['experience', 'skills', 'interests', 'clubOrgs', 'honorsAwards', 'languages', 'certifications', 'patents', 'publications'];
	//find all the list items that can be active and remove the active class
	var allMobileNavListItems = $('.topLevelListMenu').find('li a:not(.mobileMenu_companyInfoMenuItem a, .mobileMenu_signInOutMenuItem a, li.title.back.js-generated)').removeClass('active_mobile_nav_tab');

	//dynamically updating the mobile nav current page header
	$('.mobile-menu-current_page_indicator').html(currentPage);
	//add the active class to the correct page
	$(activeMenuItem).addClass('active_mobile_nav_tab');
	
	if( currentPage == 'profile'){
		$(profileTab).addClass('active_mobile_nav_tab');
		
		for (var i = 0; i <= accomplishmentsList.length; i++) {
			if( accomplishmentsList[i] == activeTab ){
				$(accomplishmentsTab).addClass('active_mobile_nav_tab');
			}
		};
	}
}



</script>




<!-- /////////////////// new main search for mobile - start \\\\\\\\\\\\\\\\\\\\\ -->
<div class="row hide-for-medium-up">
	<div class="small-12 new-mobile-search-plex-row">
		
		<div class="mobile-plex-search-container">
			<div class="plex-search-logo"></div>
			<input type="text" placeholder="Search Plexuss.." class="top_search_txt mobile-top-nav-search-new" data-input>
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




<div id="NotificationAreaTopMobile" class="show-for-small-only" style="display:block;visibility:hidden;"></div>
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

