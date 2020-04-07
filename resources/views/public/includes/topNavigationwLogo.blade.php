<?php
	$isSchool = 'false';
	if(isset($is_organization) && $is_organization == 1){
		$isSchool = 'true';
	}
?>
{{-- Front page only college chat is online --}}
@if (isset($college_chatting) && $college_chatting === true && (isset($is_mobile) && $is_mobile == false))
<div class="daily-chat-bar-container" id="_chat_bar">
    <div class="blip"></div>
    <div class="chat-label"><strong>College daily chat is happening now.</strong> <a href="/chat"><i>See which colleges are online to talk with you</i></a></div>
</div>
@endif

@if (isset($is_gdpr) && $is_gdpr == true)
<div class='eu-gdpr-notification'>
    <div class='eu-gdpr-left-side'>
        <div class='eu-gdpr-icon'></div>
        <div class='eu-gdpr-notification-text'>To our EU users: We are getting everything setup for GDPR so account login and signup has been disabled. You are free to browse the site in the meantime.</div>
        <div class='eu-gdpr-ok-button'>OK</div>
    </div>
    <div class='eu-gdpr-right-side'>
        <div class='eu-gdpr-close-button'>&times;</div>
    </div>
</div>
@endif

@if (Cookie::get('plexuss-gdpr-cookies-agree') == null)
<div class='gdpr-cookies-notification'>
        <div class='gdpr-cookies-icon'></div>
        <div class='gdpr-notification-text'>
            <div>We use cookies to personalize content and ads, to provide social media features, and to analyze our traffic. We also share information about your use of our site with colleges and partners. By continuing to browse the site you are agreeing to our use of cookies.</div>
            <div class='mt10'>Plexuss is updating its Terms of Use and Privacy Policy on May 25, 2018. See the updated Terms of Use <a class='gdpr-linkout' href='/terms-of-service' target='_blank'>here</a> and the updated Privacy Policy <a class='gdpr-linkout' href='/privacy-policy' target='_blank'>here</a>.</div>
        </div>
        <div class='gdpr-cookies-agree-button'>Yes, I agree</div>
</div>
@endif

<!-- user signed in start -->
@if ( isset($signed_in) && $signed_in == 1)
<!-- Mobile menu for logged in user -->
<div class='show-for-small-only sticky' id="sticky-search-bar1">

@if(empty($onlycollgepage))

@if(isset($currentUrlForSmartBanner) && $currentUrlForSmartBanner == '/checkout/premium')
  <nav class="top-bar no-search-bar" data-topbar>
@else
  <nav class="top-bar" data-topbar>
@endif
		<ul class="title-area">
			@if ($currentPage == 'portal')
				<li class="toggle-topbar menu-icon" onclick="$('#setting-mobile-menu').hide(); $('#collegeNav-mobile-menu').hide(); $('#college-mobile-menu').slideToggle(); $('#portalNav-mobile-menu').slideToggle(); switchIcon(this);">
        <a href="#"><span></span></a>
      </li>
			@else
	      <li class="toggle-topbar menu-icon" onclick="$('#setting-mobile-menu').hide(); $('#collegeNav-mobile-menu').hide();$('#portalNav-mobile-menu').hide(); $('#college-mobile-menu').slideToggle(); switchIcon(this);">
	        <a href="#"><span></span></a>
	      </li>
      @endif
			<li class="top-white-logo">
        @if( isset($is_organization) && $is_organization == 1 )
					<a href="/admin">
        @elseif (!isset($country_based_on_ip) || $country_based_on_ip !== 'US')
        	<a href="/">
        @else
          <a href="/">
        @endif
        		<img class='mobilelogo plex_logo_resize_homepage' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/premium_page/plexuss-white.png" alt=""/>
        	</a>
			</li>
      <li class="login_menu_icon">
				<!-- all notifications -->
				<a href="/notifications"><div id='notify_all' class="topnav_button notify_button">
					@if(isset($is_aor) && $is_aor == 0)
						<div class="nav-icon nav-utils notifications"></div>
						@if(isset($topnav_notifications['unread_cnt']) && $topnav_notifications['unread_cnt'] != 0)
							<div class="unread_count active">{{$topnav_notifications['unread_cnt'] or ''}}</div>
						@else
							<div class="unread_count"></div>
						@endif
						<div class="open-notification-pane-arrow">
								<div class="top-nav-noti-pan-arrow top-nav-all-noti-arrow"></div>
						</div>
						@else
						&nbsp;
						@endif
				</div></a>
				<!-- messages notifications -->
				@if(!isset($is_organization) || $is_organization == 0)
					<a href='/portal/messages'>
				@else
					<a href='/admin/messages'>
				@endif
				<div id='notify_msg' class="topnav_button notify_button">
					@if(isset($is_aor) && $is_aor == 0)
						<div class="nav-icon nav-utils msgs"></div>
						@if(isset($topnav_messages['unread_cnt']) && $topnav_messages['unread_cnt'] != 0)
							<div class="unread_count active">{{$topnav_messages['unread_cnt'] or ''}}</div>
						@else
							<div class="unread_count"></div>
						@endif
						<div class="open-notification-pane-arrow">
								<div class="top-nav-noti-pan-arrow top-nav-msg-noti-arrow"></div>
						</div>
						@else
						&nbsp;
						@endif
				</div></a>
      </li>
		</ul>
	@if(isset($currentUrlForSmartBanner) && $currentUrlForSmartBanner == '/checkout/premium')
	@else
    <div class="searchBar-container">
        <input type="text" placeholder="Search Plexuss" class="top_search_txt_val top_search_txt frontpage_mobile_main_search" data-input>
        <input type="hidden" class="top_search_txt_val" value="" />
        <input type="hidden" class="top_search_type" value="" />
        <div class="submit_advSearch_searchBar_btn" onclick="redirectSearch();"></div>
    </div>
    @endif
		<div class="top-bar-section"> <!-- Right Nav Section -->
      <ul class="topnav-college-drop" id="college-mobile-menu">
				<!--<ul class="show-for-small-only MobileNav topLevelListMenu">-->
					@if( isset($signed_in) && $signed_in == 1 )
						@if(! isset( $is_organization ) || (isset( $is_organization ) && stripos( $currentPage, 'setting' ) === false && stripos( $currentPage, 'admin' ) === false))
							<!-- Home tab -->
							<li class="backFillFix">
								<a href="/home" class="mobile_nav_tab_home">
										<div class="mobile-menu-img">
											<img class="mobile-nav-icon-img-new" alt='navimage' src="/images/white-menu/home.png" />
										</div><span>Home</span>
								</a>
							</li>
							<!-- Me tab -->
							<li class="backFillFix">
								<a class=" me @if($currentPage=='profile') active  @endif " href="/profile"  >
										<div class="mobile-menu-img">
											<img class="mobile-nav-icon-img-new" alt='navimage' src="/images/white-menu/profile.png" />
										</div><span>Me</span>
								</a>
							</li>
						@endif
					@endif
					{{-- IF ON ADMIN PAGE, SHOW ADMIN LINKS --}}
					@if( stripos( $currentPage, 'admin' ) !== false || (isset( $is_organization)  && stripos( $currentPage, 'setting' ) !== false))
						<!-- Dashboard link -->
						<li class="backFillFix">
							<a href="/admin/dashboard" class="mobile_nav_tab_admin">
									<div class="mobile-menu-img">
										<img class="mobile-nav-icon-img-new" alt='none' src="/images/white-menu/portal.png" />
									</div><span>Dashboard</span>
							</a>
						</li>
						<!-- Inquiries link -->
						<li class="backFillFix">
							<a href="/admin/inquiries" class="mobile_nav_tab_portal">
									<div class="mobile-menu-img">
										<img class="mobile-nav-icon-img-new" alt='none' src="/images/mobile_nav_icons/inquiries.png" />
									</div><span>Inquiries</span>
							</a>
						</li>
						<!-- chat link -->
						<li class="backFillFix">
							<a href="/admin/chat" class="mobile_nav_tab_portal">
									<div class="mobile-menu-img">
										<img class="mobile-nav-icon-img-new" alt='none' src="/images/mobile_nav_icons/chat.png" />
									</div><span>Chat</span>
							</a>
						</li>
						<!-- messages link -->
						<li class="backFillFix">
							<a href="/admin/messages" class="mobile_nav_tab_portal">
									<div class="mobile-menu-img">
										<img class="mobile-nav-icon-img-new" alt='none' src="/images/mobile_nav_icons/messages.png" />
									</div><span>Messages</span>
							</a>
						</li>
						<!-- campaign link -->
						<li class="backFillFix">
							<a href="/admin/groupmsg" class="mobile_nav_tab_portal">
									<div class="mobile-menu-img">
										<img class="mobile-nav-icon-img-new" alt='none' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/mass-message-icon-23X23.png" />
									</div><span>Campaign</span>
							</a>
						</li>
						<!-- Products and Services link -->
						<li class="backFillFix">
							<a href="/admin/products" class="mobile_nav_tab_portal">
									<div class="mobile-menu-img">
										<img class="mobile-nav-icon-img-new" alt='none' src="/images/mobile_nav_icons/products.png" />
									</div><span>Product and Services</span>
							</a>
						</li>

					{{-- end: admin specific mobile nav links --}}

					@else
						<!-- Portal tab -->
						@if( isset($signed_in) && $signed_in == 1 )
						<li class="backFillFix">
							<a href="JavaScript:Void(0);" class="mobile_nav_tab_portal" onclick="$('#portalNav-mobile-menu').slideToggle();">
									<div class="mobile-menu-img">
										<img class="mobile-nav-icon-img-new" alt='none' src="/images/white-menu/portal.png" />
									</div><span>Portal <img class="show-for-small-only mobile-menu-arrow img-right" src="/images/mobile_menu_arrow.png" alt="Nav Arrow"></span>
							</a>
						</li>
						@endif
						<!-- Colleges tab -->
						<li class="backFillFix">
							<a href="JavaScript:Void(0);" class="mobile_nav_tab_college" onclick="$('#collegeNav-mobile-menu').slideToggle();">
									<div class="mobile-menu-img">
										<img class="mobile-nav-icon-img-new" alt='none' src="/images/white-menu/college.png" />
									</div><span>Colleges <img class="show-for-small-only mobile-menu-arrow img-right" src="/images/mobile_menu_arrow.png" alt="Nav Arrow"></span>
							</a>
						</li>

						<!-- The Quad tab -->
						<li class="backFillFix">
							<a href="JavaScript:Void(0);" onclick="$('#quad-mobile-menu').slideToggle();" class="mobile_nav_tab_news">
									<div class="mobile-menu-img">
										<img class="mobile-nav-icon-img-new" alt=none src="/images/white-menu/news.png" />
									</div><span>The Quad <img class="show-for-small-only mobile-menu-arrow img-right" src="/images/mobile_menu_arrow.png" alt="Nav Arrow"></span>
							</a>
						</li>
						<li class="backFillFix">
		          <a href="JavaScript:Void(0);" class="mobile_nav_tab_college" onclick="$('#intl-mobile-menu').slideToggle();">
		            <div class="topnav-icon-sprite intl-students"></div>
		            <span>International Students <img class="show-for-small-only mobile-menu-arrow img-right" src="/images/mobile_menu_arrow.png" alt="Nav Arrow"></span>
		          </a>
		        </li>


					@if( isset($signed_in) && $signed_in==1 )
					<!-- Settings tab -->
						<li class="backFillFix has-dropdown" id="settingsMobileMenuTab">
							<a href="JavaScript:Void(0);" class="mobile_nav_tab_settings" onclick="$('#setting-mobile-menu').slideToggle();">
									<div class="mobile-menu-img">
										<img class="mobile-nav-icon-img-new" src="/images/white-menu/settings.png" alt="Settings Icon">
									</div><span>Settings <img class="show-for-small-only mobile-menu-arrow img-right" src="/images/mobile_menu_arrow.png" alt="Nav Arrow"></span>
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
							</ul>
						</li>
					@endif



					 <!-- premium  -->
					@if(  isset($signed_in) && $signed_in != 0  && ! isset( $is_organization ) && stripos( $currentPage, 'admin' ) === false)
						<li class="backFillFix">
							<a href="#" class="mobile_nav_tab_news">
									<div class="mobile-menu-img">
										@if( isset($premium_user_plan))
											<img class="mobile-nav-icon-img-new" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/premium_plus_icon.png" alt="Premium Icon">
										@else
											<img class="mobile-nav-icon-img-new" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/premium_icon.png" alt="Premium Icon">
										@endif
									</div><span>Premium</span>
							</a>
						</li>

					@endif

				@endif {{-- end: hide these links if on admin page --}}



					@if ( isset($signed_in) && $signed_in==1 )
					<!-- Sign Out tab -->
						<li class="backFillFix mobileMenu_signInOutMenuItem">
							<a href="/signout">
									<div class="mobile-menu-img">
										<img class="mobile-nav-icon-img-new" alt=none src="/images/white-menu/signout.png" data-src="/images/mobile_nav_icons/signout.png" />
									</div><span>Sign Out</span>
							</a>
						</li>
					@else
					<!-- Sign In tab -->
						<li class="backFillFix mobileMenu_signInOutMenuItem">
							<a href="/signin">
									<div class="mobile-menu-img">
										<img class="mobile-nav-icon-img-new" alt=none src="/images/white-menu/signout.png" data-src="/images/mobile_nav_icons/signout.png" />
									</div><span>sign in</span>
							</a>
						</li>
					@endif
					<li class='text-center backFillFix pt30 pb30 mobileMenu_companyInfoMenuItem'>
						<div class="row">
							<div class="column small-12">
								<a class='text-center mobile_menu_no_btm_border' href="/about">

								</a>
							</div>
							<div class="column small-12 pt15 pb15">
								<div class="normal-white"><span><a class="mobile_menu_no_btm_border" href="/about">Company</a></span> | <span><a class="mobile_menu_no_btm_border" href="/about">Information</a></span></div>
							</div>
						</div>
					</li>
      </ul>
		</div>
			<div class="top-bar-section"> <!-- Right Nav Section -->
      <ul class="topnav-college-drop" id="setting-mobile-menu">
				<!--<ul class="show-for-small-only MobileNav topLevelListMenu">-->
					@if( isset($signed_in) && $signed_in==1 )
					<!-- Settings tab -->
								<li class="mt10 mb10"><a href="Javascript:Void(0);" class="mobile_nav_back" onClick="$('#setting-mobile-menu').hide();"> <span class="majors-back-arrow-mobile">‹</span> Back </a><span class="inner-mobile-menu-title">Settings</span></li>
								<li class="backFillFix menu_li">
									<a href="/settings">
										<div class="mobile-menu-img">
											<img class="mobile-nav-icon-img-new_password" alt=none src="/images/Password@3x.png" />
										</div><span>Change Password</span>
									</a>
								</li>
								<li class="backFillFix menu_li">
									<a href="/notifications">
										<div class="mobile-menu-img">
											<img class="mobile-nav-icon-img-new_email" alt=none src="/images/email@3x.png" />
										</div><span>Email Notifications</span>
									</a>
								</li>
								<li class="backFillFix menu_li">
									<a href="/admin/messages">
										<div class="mobile-menu-img">
											<img class="mobile-nav-icon-img-new_text" alt=none src="/images/Text@3x.png" />
										</div><span>Text Notifications</span>
									</a>
								</li>
								@if( !isset($is_organization) || $is_organization == 0 || !isset($super_admin) || $super_admin == 0 )
								<li class="backFillFix menu_li">
									<a href="/settings/invite">
										<div class="mobile-menu-img">
											<img class="mobile-nav-icon-img-new_invite" alt=none src="/images/Invite@3x.png" />
										</div><span>Invite Friends</span>
									</a>
								</li>
								@endif
<!--
								@if( !isset($is_organization) || $is_organization == 0 )
								<li class="backFillFix">
									<a href="/settings/billing" class="mobile_nav_tab_portal">
										Billing
									</a>
								</li>
								@endif

								@if (isset($is_gdpr) && $is_gdpr == true)
								<li class="backFillFix">
									<a href="/settings/data_preferences" class="mobile_nav_tab_portal">
										Data Preferences
									</a>
								</li>
								@endif
-->

					@endif


      </ul>
		</div>
			<div class="top-bar-section">
				<!-- Right Nav Section -->
				<!-- Portal Down bar-->
				<ul class="topnav-college-drop" id="portalNav-mobile-menu">
					<li class="mt10 mb10"><a href="Javascript:Void(0);" class="mobile_nav_back" onClick="$('#portalNav-mobile-menu').hide();">
						<span class="majors-back-arrow-mobile">‹</span> Back </a><span class="inner-mobile-menu-title">Portal</span>
					</li>

					<li class=menu_li">
						<a href="/portal/messages">
							<div class="topnav-icon-sprite messages"></div>
							<span>Messages</span>
						</a>
					</li>

					<li class="menu_li">
						<a href="/portal/scholarships">
							<div class="topnav-icon-sprite scholarships1"></div>
							<span>Scholarships</span>
						</a>
					</li>

					<li class="menu_li">
						<a href="/portal/applications">
							<div class="topnav-icon-sprite application"></div>
							<span>Applications</span>
						</a>
					</li>

					<li class="menu_li">
						<a href="/portal">
							<div class="topnav-icon-sprite list"></div>
							<span>Your list</span>
						</a>
					</li>

					<li class="menu_li">
						<a href="/portal/recommendationlist">
							<div class="topnav-icon-sprite recomended"></div>
							<span>Recommended</span>
						</a>
					</li>

					<li class="menu_li">
						<a href="/portal/collegesrecruityou">
							<div class="topnav-icon-sprite seeking"></div>
							<span>Schools seeking you</span>
						</a>
					</li>

					<li class="menu_li">
						<a href="/portal/collegesviewedprofile">
							<div class="topnav-icon-sprite viewing"></div>
							<span>Schools viewing you</span>
						</a>
					</li>

					<li class="menu_li">
						<a href="/portal/getTrashSchoolList">
							<div class="topnav-icon-sprite trash"></div>
							<span>Trash</span>
						</a>
					</li>
				</ul>
				<!-- End of Portal bar-->

      <ul class="topnav-college-drop" id="collegeNav-mobile-menu">
				<!--<ul class="show-for-small-only MobileNav topLevelListMenu">-->
					@if( isset($signed_in) && $signed_in==1 )
					<!-- Settings tab -->
							  @if (!isset($country_based_on_ip) || $country_based_on_ip == 'US')
						<li class="mt10 mb10"><a href="Javascript:Void(0);" class="mobile_nav_back" onClick="$('#collegeNav-mobile-menu').hide();"> <span class="majors-back-arrow-mobile">‹</span> Back </a><span class="inner-mobile-menu-title">Colleges</span></li>
						<li class="menu_li">
						  <a href="/college">
							<!-- <img id='public_college_search_icon' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon.png" data-src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
							<div class="topnav-icon-sprite find-colleges"></div>
							<span>Find Colleges</span>
						  </a>
						</li>

						<li class="menu_li">
						  <a href="/college-fairs-events">
							<!-- <img id='public_college_search_icon' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon.png" data-src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
							<div class="topnav-icon-sprite events"></div>
							<span>College Fair</span>
						  </a>
						</li>

						@endif
						@if (!isset($country_based_on_ip) || $country_based_on_ip !== 'US')
						<li class="menu_li">
						  <a href="/international-students">
							<div class="topnav-icon-sprite intl-students"></div>

							<!-- <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/intl_students_nav_icon.png" data-src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/intl_students_nav_icon_hover.png" alt=""/>&nbsp;&nbsp; -->International Students
						  </a>
						</li>
						@endif

						<li class="menu_li">
						  <a href="/college-majors">
							<div class="colleges-majors-icon"></div>

							<!-- <img src="/images/ranking/ranking_icon.png" data-src="/images/ranking/ranking_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp; --><span>Majors</span>
						  </a>
						</li>
						<li class="menu_li">
						  <a href="/scholarships">
							<div class="topnav-icon-sprite scholarships"></div>
							<span>Scholarships</span>
						  </a>
						</li>
						<li  class="menu_li">
						  <a href="/ranking">
							<div class="topnav-icon-sprite colleges-ranking"></div>

							<!-- <img src="/images/ranking/ranking_icon.png" data-src="/images/ranking/ranking_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp; --><span>Ranking</span>
						  </a>
						</li>
						<div class="clearfix" style="border-top: 1px #ff0000;"></div>
						<li class="menu_li">
						  <a href="/comparison">
							<div class="topnav-icon-sprite compare-colleges"></div>

							<!-- <img src="/images/ranking/compare_icon.png" data-src="/images/ranking/compare_icon_hover.png" alt=""/>&nbsp;&nbsp; --><span>Compare Colleges</span>
						  </a>
						</li>
						<li class="menu_li">
						  <a href="/news/catalog/college-essays">
							<!-- <img id='public_college_search_icon' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon.png" data-src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
							<div class="topnav-icon-sprite me-docs"></div>
							<span>College Essays</span>
						  </a>
						</li>
					@endif


      </ul>

			<!-- Quad -->
			<ul class="topnav-college-drop" id="quad-mobile-menu">
				<li class="mt10 mb10">
					<a href="Javascript:Void(0);" class="mobile_nav_back" onClick="$('#quad-mobile-menu').hide();"> <span class="majors-back-arrow-mobile">‹</span> Back </a><span class="inner-mobile-menu-title">The Quad</span>
				</li>
				<li class="menu_li">
						<a href="/news">
							<div class="topnav-icon-sprite me-news"></div>
							<span>News</span>
						</a>
					</li>
					<li class="menu_li">
							<a href="/news/catalog/college-essays">
									<div class="topnav-icon-sprite me-docs"></div>
									<span>College Essays</span>
							</a>
					</li>
      </ul>


				<!-- International Student -->
				<ul class="topnav-college-drop" id="intl-mobile-menu">
					<li class="mt10 mb10">
						<a href="Javascript:Void(0);" class="mobile_nav_back" onClick="$('#intl-mobile-menu').hide();"> <span class="majors-back-arrow-mobile">‹</span> Back </a><span class="inner-mobile-menu-title">International Students</span>
					</li>
					<li class="menu_li">
							<a href="/international-students">
								<div class="topnav-icon-sprite apply-university"></div>
								Apply to Universities
							</a>
						</li>
						<li class="menu_li">
								<a href="/international-resources/main">
										<div class="topnav-icon-sprite resource-center"></div>
										Resource Center
								</a>
						</li>
						<li class="menu_li">
								<a href="/agency-search">
										<div class="topnav-icon-sprite local-support"></div>
										Find Local Support
								</a>
						</li>
						<li class="menu_li">
								<a href="/premium-plans-info">
										<div class="topnav-icon-sprite plexuss-premium"></div>
										Plexuss Premium
								</a>
						</li>
	      </ul>
		</div>


	</nav>

<!----mobile view of collge page---->
@else

<nav class="top-bar onfixvisible" data-topbar>
		<ul class="title-area">
      <li class="toggle-topbar menu-icon" onclick="$('#setting-mobile-menu').hide(); $('#collegeNav-mobile-menu').hide();$('#portalNav-mobile-menu').hide(); $('#college-mobile-menu').slideToggle(); switchIcon(this);">
        <a href="#"><span></span></a>
      </li>
			<li class="top-white-logo">
        @if( isset($is_organization) && $is_organization == 1 )
					<a href="/admin">
        @elseif (!isset($country_based_on_ip) || $country_based_on_ip !== 'US')
        	<a href="/">
        @else
          <a href="/">
        @endif
        		<img class='mobilelogo plex_logo_resize_homepage' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/premium_page/plexuss-white.png" alt=""/>
        	</a>
			</li>
      <li class="login_menu_icon">
				<!-- all notifications -->
				<div id='notify_all' class="topnav_button notify_button notify_all_unique">
					@if(isset($is_aor) && $is_aor == 0)
						<div class="nav-icon nav-utils notifications"></div>
						@if(isset($topnav_notifications['unread_cnt']) && $topnav_notifications['unread_cnt'] != 0)
							<div class="unread_count active">{{$topnav_notifications['unread_cnt'] or ''}}</div>
						@else
							<div class="unread_count"></div>
						@endif
						<div class="open-notification-pane-arrow">
								<div class="top-nav-noti-pan-arrow top-nav-all-noti-arrow"></div>
						</div>
						@else
						&nbsp;
						@endif
				</div>
				<!-- messages notifications -->
				<div id='notify_msg' class="topnav_button notify_button notify_msg_unique">
					@if(isset($is_aor) && $is_aor == 0)
						<div class="nav-icon nav-utils msgs"></div>
						@if(isset($topnav_messages['unread_cnt']) && $topnav_messages['unread_cnt'] != 0)
							<div class="unread_count active">{{$topnav_messages['unread_cnt'] or ''}}</div>
						@else
							<div class="unread_count"></div>
						@endif
						<div class="open-notification-pane-arrow">
								<div class="top-nav-noti-pan-arrow top-nav-msg-noti-arrow"></div>
						</div>
						@else
						&nbsp;
						@endif
				</div>
      </li>
		</ul>
    <div class="searchBar-container">
        <input type="text" placeholder="Search Plexuss" class="top_search_txt_val top_search_txt frontpage_mobile_main_search" data-input>
        <input type="hidden" class="top_search_txt_val" value="" />
        <input type="hidden" class="top_search_type" value="" />
        <div class="submit_advSearch_searchBar_btn" onclick="redirectSearch();"></div>
    </div>
		<div class="top-bar-section"> <!-- Right Nav Section -->
      <ul class="topnav-college-drop" id="college-mobile-menu">
				<!--<ul class="show-for-small-only MobileNav topLevelListMenu">-->
					@if( isset($signed_in) && $signed_in == 1 )
						@if(! isset( $is_organization ) || (isset( $is_organization ) && stripos( $currentPage, 'setting' ) === false && stripos( $currentPage, 'admin' ) === false))
							<!-- Home tab -->
							<li class="backFillFix">
								<a href="/home" class="mobile_nav_tab_home">
										<div class="mobile-menu-img">
											<img class="mobile-nav-icon-img-new" alt='navimage' src="/images/white-menu/home.png" />
										</div><span>Home</span>
								</a>
							</li>
							<!-- Me tab -->
							<li class="backFillFix">
								<a class=" me @if($currentPage=='profile') active  @endif " href="/profile"  >
										<div class="mobile-menu-img">
											<img class="mobile-nav-icon-img-new" alt='navimage' src="/images/white-menu/profile.png" />
										</div><span>Me</span>
								</a>
							</li>
						@endif
					@endif
					{{-- IF ON ADMIN PAGE, SHOW ADMIN LINKS --}}
					@if( stripos( $currentPage, 'admin' ) !== false || (isset( $is_organization)  && stripos( $currentPage, 'setting' ) !== false))
						<!-- Dashboard link -->
						<li class="backFillFix">
							<a href="/admin/dashboard" class="mobile_nav_tab_admin">
									<div class="mobile-menu-img">
										<img class="mobile-nav-icon-img-new" alt='none' src="/images/white-menu/portal.png" />
									</div><span>Dashboard</span>
							</a>
						</li>
						<!-- Inquiries link -->
						<li class="backFillFix">
							<a href="/admin/inquiries" class="mobile_nav_tab_portal">
									<div class="mobile-menu-img">
										<img class="mobile-nav-icon-img-new" alt='none' src="/images/mobile_nav_icons/inquiries.png" />
									</div><span>Inquiries</span>
							</a>
						</li>
						<!-- chat link -->
						<li class="backFillFix">
							<a href="/admin/chat" class="mobile_nav_tab_portal">
									<div class="mobile-menu-img">
										<img class="mobile-nav-icon-img-new" alt='none' src="/images/mobile_nav_icons/chat.png" />
									</div><span>Chat</span>
							</a>
						</li>
						<!-- messages link -->
						<li class="backFillFix">
							<a href="/admin/messages" class="mobile_nav_tab_portal">
									<div class="mobile-menu-img">
										<img class="mobile-nav-icon-img-new" alt='none' src="/images/mobile_nav_icons/messages.png" />
									</div><span>Messages</span>
							</a>
						</li>
						<!-- campaign link -->
						<li class="backFillFix">
							<a href="/admin/groupmsg" class="mobile_nav_tab_portal">
									<div class="mobile-menu-img">
										<img class="mobile-nav-icon-img-new" alt='none' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/mass-message-icon-23X23.png" />
									</div><span>Campaign</span>
							</a>
						</li>
						<!-- Products and Services link -->
						<li class="backFillFix">
							<a href="/admin/products" class="mobile_nav_tab_portal">
									<div class="mobile-menu-img">
										<img class="mobile-nav-icon-img-new" alt='none' src="/images/mobile_nav_icons/products.png" />
									</div><span>Product and Services</span>
							</a>
						</li>

					{{-- end: admin specific mobile nav links --}}

					@else
						<!-- Portal tab -->
						@if( isset($signed_in) && $signed_in == 1 )
						<li class="backFillFix">
							<a href="JavaScript:Void(0);" class="mobile_nav_tab_portal" onclick="$('#portalNav-mobile-menu').slideToggle();">
									<div class="mobile-menu-img">
										<img class="mobile-nav-icon-img-new" alt='none' src="/images/white-menu/portal.png" />
									</div><span>Portal <img class="show-for-small-only mobile-menu-arrow img-right" src="/images/mobile_menu_arrow.png" alt="Nav Arrow"></span>
							</a>
						</li>
						@endif
						<!-- Colleges tab -->
						<li class="backFillFix">
							<a href="JavaScript:Void(0);" class="mobile_nav_tab_college" onclick="$('#collegeNav-mobile-menu').slideToggle();">
									<div class="mobile-menu-img">
										<img class="mobile-nav-icon-img-new" alt='none' src="/images/white-menu/college.png" />
									</div><span>Colleges <img class="show-for-small-only mobile-menu-arrow img-right" src="/images/mobile_menu_arrow.png" alt="Nav Arrow"></span>
							</a>
						</li>

						<!-- The Quad tab -->
						<li class="backFillFix">
							<a href="/news" class="mobile_nav_tab_news">
									<div class="mobile-menu-img">
										<img class="mobile-nav-icon-img-new" alt=none src="/images/white-menu/news.png" />
									</div><span>The Quad</span>
							</a>
						</li>


					@if( isset($signed_in) && $signed_in==1 )
					<!-- Settings tab -->
						<li class="backFillFix has-dropdown" id="settingsMobileMenuTab">
							<a href="JavaScript:Void(0);" class="mobile_nav_tab_settings" onclick="$('#setting-mobile-menu').slideToggle();">
									<div class="mobile-menu-img">
										<img class="mobile-nav-icon-img-new" src="/images/white-menu/settings.png" alt="Settings Icon">
									</div><span>Settings <img class="show-for-small-only mobile-menu-arrow img-right" src="/images/mobile_menu_arrow.png" alt="Nav Arrow"></span>
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
							</ul>
						</li>
					@endif



					 <!-- premium  -->
					@if(  isset($signed_in) && $signed_in != 0  && ! isset( $is_organization ) && stripos( $currentPage, 'admin' ) === false)
						<li class="backFillFix">
							<a href="#" class="mobile_nav_tab_news">
									<div class="mobile-menu-img">
										@if( isset($premium_user_plan))
											<img class="mobile-nav-icon-img-new" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/premium_plus_icon.png" alt="Premium Icon">
										@else
											<img class="mobile-nav-icon-img-new" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/premium_icon.png" alt="Premium Icon">
										@endif
									</div><span>Premium</span>
							</a>
						</li>

					@endif

				@endif {{-- end: hide these links if on admin page --}}



					@if ( isset($signed_in) && $signed_in==1 )
					<!-- Sign Out tab -->
						<li class="backFillFix mobileMenu_signInOutMenuItem">
							<a href="/signout">
									<div class="mobile-menu-img">
										<img class="mobile-nav-icon-img-new" alt=none src="/images/white-menu/signout.png" data-src="/images/mobile_nav_icons/signout.png" />
									</div><span>Sign Out</span>
							</a>
						</li>
					@else
					<!-- Sign In tab -->
						<li class="backFillFix mobileMenu_signInOutMenuItem">
							<a href="/signin">
									<div class="mobile-menu-img">
										<img class="mobile-nav-icon-img-new" alt=none src="/images/white-menu/signout.png" data-src="/images/mobile_nav_icons/signout.png" />
									</div><span>sign in</span>
							</a>
						</li>
					@endif
					<li class='text-center backFillFix pt30 pb30 mobileMenu_companyInfoMenuItem'>
						<div class="row">
							<div class="column small-12">
								<a class='text-center mobile_menu_no_btm_border' href="/about">

								</a>
							</div>
							<div class="column small-12 pt15 pb15">
								<div class="normal-white"><span><a class="mobile_menu_no_btm_border" href="/about">Company</a></span> | <span><a class="mobile_menu_no_btm_border" href="/about">Information</a></span></div>
							</div>
						</div>
					</li>
      </ul>
		</div>
			<div class="top-bar-section"> <!-- Right Nav Section -->
      <ul class="topnav-college-drop" id="setting-mobile-menu">
				<!--<ul class="show-for-small-only MobileNav topLevelListMenu">-->
					@if( isset($signed_in) && $signed_in==1 )
					<!-- Settings tab -->
								<li class="mt10 mb10"><a href="Javascript:Void(0);" class="mobile_nav_back" onClick="$('#setting-mobile-menu').hide();"> <span class="majors-back-arrow-mobile">‹</span> Back </a><span class="inner-mobile-menu-title">Settings</span></li>
								<li class="backFillFix menu_li">
									<a href="/settings">
										<div class="mobile-menu-img">
											<img class="mobile-nav-icon-img-new_password" alt=none src="/images/Password@3x.png" />
										</div><span>Change Password</span>
									</a>
								</li>
								<li class="backFillFix menu_li">
									<a href="/notifications">
										<div class="mobile-menu-img">
											<img class="mobile-nav-icon-img-new_email" alt=none src="/images/email@3x.png" />
										</div><span>Email Notifications</span>
									</a>
								</li>
								<li class="backFillFix menu_li">
									<a href="/admin/messages">
										<div class="mobile-menu-img">
											<img class="mobile-nav-icon-img-new_text" alt=none src="/images/Text@3x.png" />
										</div><span>Text Notifications</span>
									</a>
								</li>
								@if( !isset($is_organization) || $is_organization == 0 || !isset($super_admin) || $super_admin == 0 )
								<li class="backFillFix menu_li">
									<a href="/settings/invite">
										<div class="mobile-menu-img">
											<img class="mobile-nav-icon-img-new_invite" alt=none src="/images/Invite@3x.png" />
										</div><span>Invite Friends</span>
									</a>
								</li>
								@endif
<!--
								@if( !isset($is_organization) || $is_organization == 0 )
								<li class="backFillFix">
									<a href="/settings/billing" class="mobile_nav_tab_portal">
										Billing
									</a>
								</li>
								@endif

								@if (isset($is_gdpr) && $is_gdpr == true)
								<li class="backFillFix">
									<a href="/settings/data_preferences" class="mobile_nav_tab_portal">
										Data Preferences
									</a>
								</li>
								@endif
-->

					@endif


      </ul>
		</div>
			<div class="top-bar-section"> <!-- Right Nav Section -->


				<!-- Portal Down bar-->
				<ul class="topnav-college-drop" id="portalNav-mobile-menu">
					<li class="mt10 mb10"><a href="Javascript:Void(0);" class="mobile_nav_back" onClick="$('#portalNav-mobile-menu').hide();">
						<span class="majors-back-arrow-mobile">‹</span> Back </a><span class="inner-mobile-menu-title">Portal</span>
					</li>

					<li class=menu_li">
						<a href="">
							<div class="topnav-icon-sprite messages"></div>
							<span>Messages</span>
						</a>
					</li>

					<li class="menu_li">
						<a href="">
							<div class="topnav-icon-sprite scholarships1"></div>
							<span>Scholarships</span>
						</a>
					</li>

					<li class="menu_li">
						<a href="">
							<div class="topnav-icon-sprite application"></div>
							<span>Applications</span>
						</a>
					</li>

					<li class="menu_li">
						<a href="">
							<div class="topnav-icon-sprite list"></div>
							<span>Your list</span>
						</a>
					</li>

					<li class="menu_li">
						<a href="">
							<div class="topnav-icon-sprite recomended"></div>
							<span>Recommended</span>
						</a>
					</li>

					<li class="menu_li">
						<a href="">
							<div class="topnav-icon-sprite seeking"></div>
							<span>Schools seeking you</span>
						</a>
					</li>

					<li class="menu_li">
						<a href="">
							<div class="topnav-icon-sprite viewing"></div>
							<span>Schools viewing you</span>
						</a>
					</li>

					<li class="menu_li">
						<a href="">
							<div class="topnav-icon-sprite trash"></div>
							<span>Trash</span>
						</a>
					</li>
				</ul>
				<!-- End of Portal bar-->
      <ul class="topnav-college-drop" id="collegeNav-mobile-menu">
				<!--<ul class="show-for-small-only MobileNav topLevelListMenu">-->
					@if( isset($signed_in) && $signed_in==1 )
					<!-- Settings tab -->
							  @if (!isset($country_based_on_ip) || $country_based_on_ip == 'US')
						<li class="mt10 mb10"><a href="Javascript:Void(0);" class="mobile_nav_back" onClick="$('#collegeNav-mobile-menu').hide();"> <span class="majors-back-arrow-mobile">‹</span> Back </a><span class="inner-mobile-menu-title">Colleges</span></li>
						<li class="menu_li">
						  <a href="/college">
							<!-- <img id='public_college_search_icon' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon.png" data-src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
							<div class="topnav-icon-sprite find-colleges"></div>
							<span>Find Colleges</span>
						  </a>
						</li>

						<li class="menu_li">
						  <a href="/college-fairs-events">
							<!-- <img id='public_college_search_icon' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon.png" data-src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
							<div class="topnav-icon-sprite events"></div>
							<span>College Fair</span>
						  </a>
						</li>

						@endif
						@if (!isset($country_based_on_ip) || $country_based_on_ip !== 'US')
						<li class="menu_li">
						  <a href="/international-students">
							<div class="topnav-icon-sprite intl-students"></div>

							<!-- <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/intl_students_nav_icon.png" data-src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/intl_students_nav_icon_hover.png" alt=""/>&nbsp;&nbsp; -->International Students
						  </a>
						</li>
						@endif

						<li class="menu_li">
						  <a href="/college-majors">
							<div class="colleges-majors-icon"></div>

							<!-- <img src="/images/ranking/ranking_icon.png" data-src="/images/ranking/ranking_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp; --><span>Majors</span>
						  </a>
						</li>
						<li class="menu_li">
						  <a href="/scholarships">
							<div class="topnav-icon-sprite scholarships"></div>
							<span>Scholarships</span>
						  </a>
						</li>
						<li  class="menu_li">
						  <a href="/ranking">
							<div class="topnav-icon-sprite colleges-ranking"></div>

							<!-- <img src="/images/ranking/ranking_icon.png" data-src="/images/ranking/ranking_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp; --><span>Ranking</span>
						  </a>
						</li>
						<div class="clearfix" style="border-top: 1px #ff0000;"></div>
						<li class="menu_li">
						  <a href="/comparison">
							<div class="topnav-icon-sprite compare-colleges"></div>

							<!-- <img src="/images/ranking/compare_icon.png" data-src="/images/ranking/compare_icon_hover.png" alt=""/>&nbsp;&nbsp; --><span>Compare Colleges</span>
						  </a>
						</li>
						<li class="menu_li">
						  <a href="/news/catalog/college-essays">
							<!-- <img id='public_college_search_icon' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon.png" data-src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
							<div class="topnav-icon-sprite me-docs"></div>
							<span>College Essays</span>
						  </a>
						</li>
					@endif


      </ul>
		</div>


	</nav>




<nav class="top-bar onscrollvisible" data-topbar>
 <?php
  $CollegeData = $college_data;
 ?>
    <div class="row hide-for-large-up">

        <!-- This is the Recruit Me / Compare buttons on tablet and mobile view -->
        @if($signed_in == 1)
            @if ($isInUserList == 0)
                @if( isset($profile_perc) && $profile_perc < 30 && $completed_signup == 0 )
                    <div class="small-12 medium-6 columns is-redirect getrecurit" data-cid="{{$CollegeData->id}}">
                @else
                    <div class="small-12 medium-6 columns getrecurit" data-reveal-id="recruitmeModal" data-reveal-ajax="/ajax/recruiteme/{{$CollegeData->id}}">
                @endif
                        <div class='mob-recruit-btn text-left orange-btn'>
                            <img src="/images/colleges/recruit-me-white.png" alt=""/>Get Recruited!
                        </div>
                    </div>
            @else
                <div class="small-12 medium-6 columns alreadyonlist">
                    <div class='mob-recruit-btn-pending text-left'>
                        <img class="mob-already-added-icon" src="/images/colleges/recruitment-btn.png" alt="">&nbsp;&nbsp;Already on my list!
                    </div>
                </div>
            @endif
        @else
            <a href="/signup?requestType=recruitme&collegeId={{$CollegeData->id}}&utm_source=SEO&utm_medium={{$currentPage or ''}}&utm_content={{$CollegeData->id}}&utm_campaign=recruitme">
                <div class="small-12 medium-6 columns ">
                    <div class='mob-recruit-btn text-left orange-btn'>
                        <img src="/images/colleges/recruit-me-white.png" alt=""/>Get Recruited!
                    </div>
                </div>
            </a>
        @endif

        <div class="small-12 medium-6 text-left columns alreadyonlist">
            <a href="{{$CollegeData->paid_app_url or '/college-application'}}">
                <div class='mob-apply-btn'>
                    <img src="/images/colleges/apply-btn.png" alt=""/>Apply Now
                </div>
            </a>
        </div>

            <div class="small-12 medium-6 text-left columns onsticky">
           <a href="https://plexuss.com/adRedirect?company=edx&utm_source={{$college_slug}}&cid=2&uid={{$user_id}}">
                <div class='mob-vs-btn edx_img'>
                    <img src="/images/colleges/edx.png" alt=""/> Take a free course !
                </div>
            </a>
        </div>

    </div>
</div>


</nav>
<!---end -->
@endif

</div>



<div class='BgTop BgTop-cont' id="react-hide-for-admin"  data-isSchool="{{$isSchool}}">
<script>
	//this is for the new admin - don't want to show topnav on portal login page
	var uppernav = document.getElementById('react-hide-for-admin'),
		path = window.location.pathname;

	if( uppernav && path === '/admin' ) uppernav.style.display = 'none';
</script>
@elseif( (!isset($signed_in) || $signed_in != 1) &&  $currentPage != 'frontPage' && $currentPage != 'admin')
<!-- Top Nav Section -->
<!--<div id='loadingscreen'></div>-->
<div class='show-for-small-only sticky' id="sticky-search-bar2">
@if(empty($onlycollgepage))

	<nav class="top-bar" data-topbar>
		<ul class="title-area">
      <li class="toggle-topbar menu-icon" onclick="$('#college-mobile-menu').slideToggle(); switchIcon(this)">
        <a href="#"><span></span></a>
      </li>
			<li class="top-white-logo">
                @if( isset($is_organization) && $is_organization == 1 )
				<a href="/admin">
                @elseif (!isset($country_based_on_ip) || $country_based_on_ip !== 'US')
                <a href="/">
                @else
                <a href="/">
                @endif
                    <img class='mobilelogo plex_logo_resize_homepage' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/premium_page/plexuss-white.png" alt=""/>
                </a>
			</li>
        <li class="toggle-topbar-want top-want-menu" data-reveal-id="i-want-to-modal">
          <a href="#"><img src="/images/i-want-menu-icon.png" /></a>
        </li>
		</ul>
    <div class="searchBar-container">
        <input type="text" placeholder="Search Plexuss" class="top_search_txt_val top_search_txt frontpage_mobile_main_search" data-input>
        <input type="hidden" class="top_search_txt_val" value="" />

        <input type="hidden" class="top_search_type" value="" />
        <div class="submit_advSearch_searchBar_btn" onclick="redirectSearch();"></div>
    </div>
		<div class="top-bar-section"> <!-- Right Nav Section -->

			<!-- Quad -->
			<ul class="topnav-college-drop" id="quad-mobile-menu">
				<li class="mt10 mb10">
					<a href="Javascript:Void(0);" class="mobile_nav_back" onClick="$('#quad-mobile-menu').hide();"> <span class="majors-back-arrow-mobile">‹</span> Back </a><span class="inner-mobile-menu-title">The Quad</span>
				</li>
				<li class="menu_li">
					<a href="/news">
						<div class="topnav-icon-sprite me-news"></div>
						<span>News</span>
					</a>
				</li>
				<li class="menu_li">
					<a href="/news/catalog/college-essays">
						<div class="topnav-icon-sprite me-docs"></div>
						<span>College Essays</span>
					</a>
				</li>
			</ul>

			<!-- International Student -->
			<ul class="topnav-college-drop" id="intl-mobile-menu">
				<li class="mt10 mb10">
					<a href="Javascript:Void(0);" class="mobile_nav_back" onClick="$('#intl-mobile-menu').hide();"> <span class="majors-back-arrow-mobile">‹</span> Back </a><span class="inner-mobile-menu-title">International Students</span>
				</li>
				<li class="menu_li">
					<a href="/international-students">
						<div class="topnav-icon-sprite apply-university"></div>
						Apply to Universities
					</a>
				</li>
				<li class="menu_li">
					<a href="/international-resources/main">
						<div class="topnav-icon-sprite resource-center"></div>
						Resource Center
					</a>
				</li>
				<li class="menu_li">
					<a href="/agency-search">
						<div class="topnav-icon-sprite local-support"></div>
						Find Local Support
					</a>
				</li>
				<li class="menu_li">
					<a href="/premium-plans-info">
						<div class="topnav-icon-sprite plexuss-premium"></div>
						Plexuss Premium
					</a>
				</li>
			</ul>
			<!-- --------------------- -->

      <ul class="topnav-college-drop" id="college-mobile-menu">
        @if (!isset($country_based_on_ip) || $country_based_on_ip == 'US')
        <li>
          <a href="/college">
            <!-- <img id='public_college_search_icon' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon.png" data-src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
            <div class="topnav-icon-sprite find-colleges"></div>
            <span>Find Colleges</span>
          </a>
        </li>
        @endif
        @if (!isset($country_based_on_ip) || $country_based_on_ip !== 'US')
        <li>
          <a href="/international-students">
            <div class="topnav-icon-sprite intl-students"></div>

            <!-- <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/intl_students_nav_icon.png" data-src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/intl_students_nav_icon_hover.png" alt=""/>&nbsp;&nbsp; -->International Students
          </a>
        </li>
        @endif

        <li>
          <a href="/college-fairs-events">
            <!-- <img id='public_college_search_icon' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon.png" data-src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
            <div class="topnav-icon-sprite events"></div>
            <span>College Fairs</span>
          </a>
        </li>


        <li>
          <a href="/college-majors">
            <div class="colleges-majors-icon"></div>

            <!-- <img src="/images/ranking/ranking_icon.png" data-src="/images/ranking/ranking_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp; --><span>Majors</span>
          </a>
        </li>
        <li>
          <a href="/scholarships">
            <div class="topnav-icon-sprite scholarships"></div>
            <span>Scholarships</span>
          </a>
        </li>
        <li>
          <a href="/ranking">
            <div class="topnav-icon-sprite colleges-ranking"></div>

            <!-- <img src="/images/ranking/ranking_icon.png" data-src="/images/ranking/ranking_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp; --><span>Ranking</span>
          </a>
        </li>
        <div class="clearfix" style="border-top: 1px #ff0000;"></div>
        <li>
          <a href="/comparison">
            <div class="topnav-icon-sprite compare-colleges"></div>

            <!-- <img src="/images/ranking/compare_icon.png" data-src="/images/ranking/compare_icon_hover.png" alt=""/>&nbsp;&nbsp; --><span>Compare Colleges</span>
          </a>
        </li>



        <!-- Add Internation and Quad tabs -->

        <li <?php if($currentPage=='news') { ?> class="active" <?php } ?>>
          <a href="JavaScript:Void(0);" onclick="$('#quad-mobile-menu').slideToggle();" class="mobile_nav_tab_news">
              <div class="topnav-icon-sprite me-news"></div>
              <span>The Quad <img class="show-for-small-only mobile-menu-arrow img-right" src="/images/mobile_menu_arrow.png" alt="Nav Arrow"></span>
          </a>
        </li>
        <li class="backFillFix">
          <a href="JavaScript:Void(0);" class="mobile_nav_tab_college" onclick="$('#intl-mobile-menu').slideToggle();">
            <div class="topnav-icon-sprite intl-students"></div>
            <span>International Students <img class="show-for-small-only mobile-menu-arrow img-right" src="/images/mobile_menu_arrow.png" alt="Nav Arrow"></span>
          </a>
        </li>
        <!-- ---------- -->
        <li style="max-height: inherit;">
          <div class="signup-mobile"><a href="/signup?utm_source=SEO&utm_medium=frontPage_center"><div class='homepage-signup-button'>Sign up</div></a></div>
          <div class='fb-login-container hide-for-large-up signup-mobile'>
            <div class='large-12 rela fb-login-btn'>
              <a href="/facebook?utm_source=SEO&utm_medium=signupPage" class='signupFB'></i>Sign up with Facebook</a>

            </div>
          </div>
        </li>
        <li class="backFillFix mobileMenu_signInOutMenuItem">
          <a href="/signin">
              <div class="column small-10 center">Login</div>
            </div>
          </a>
        </li>
      </ul>
		</div>
	</nav>
@else
<nav class="top-bar onfixvisible" data-topbar>
		<ul class="title-area">
      <li class="toggle-topbar menu-icon" onclick="$('#college-mobile-menu').slideToggle(); switchIcon(this)">
        <a href="#"><span></span></a>
      </li>
			<li class="top-white-logo">
                @if( isset($is_organization) && $is_organization == 1 )
				<a href="/admin">
                @elseif (!isset($country_based_on_ip) || $country_based_on_ip !== 'US')
                <a href="/">
                @else
                <a href="/">
                @endif
                    <img class='mobilelogo plex_logo_resize_homepage' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/premium_page/plexuss-white.png" alt=""/>
                </a>
			</li>
        <li class="toggle-topbar-want top-want-menu" data-reveal-id="i-want-to-modal">
          <a href="#"><img src="/images/i-want-menu-icon.png" /></a>
        </li>
		</ul>
    <div class="searchBar-container">
        <input type="text" placeholder="Search Plexuss" class="top_search_txt_val top_search_txt frontpage_mobile_main_search" data-input>
        <input type="hidden" class="top_search_txt_val" value="" />

        <input type="hidden" class="top_search_type" value="" />
        <div class="submit_advSearch_searchBar_btn" onclick="redirectSearch();"></div>
    </div>
		<div class="top-bar-section"> <!-- Right Nav Section -->
      <ul class="topnav-college-drop" id="college-mobile-menu">
        @if (!isset($country_based_on_ip) || $country_based_on_ip == 'US')
        <li>
          <a href="/college">
            <!-- <img id='public_college_search_icon' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon.png" data-src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
            <div class="topnav-icon-sprite find-colleges"></div>
            <span>Find Colleges</span>
          </a>
        </li>
        @endif
        @if (!isset($country_based_on_ip) || $country_based_on_ip !== 'US')
        <li>
          <a href="/international-students">
            <div class="topnav-icon-sprite intl-students"></div>

            <!-- <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/intl_students_nav_icon.png" data-src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/intl_students_nav_icon_hover.png" alt=""/>&nbsp;&nbsp; -->International Students
          </a>
        </li>
        @endif

        <li>
          <a href="/college-fairs-events">
            <!-- <img id='public_college_search_icon' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon.png" data-src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
            <div class="topnav-icon-sprite events"></div>
            <span>College Fairs</span>
          </a>
        </li>


        <li>
          <a href="/college-majors">
            <div class="colleges-majors-icon"></div>

            <!-- <img src="/images/ranking/ranking_icon.png" data-src="/images/ranking/ranking_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp; --><span>Majors</span>
          </a>
        </li>
        <li>
          <a href="/scholarships">
            <div class="topnav-icon-sprite scholarships"></div>
            <span>Scholarships</span>
          </a>
        </li>
        <li>
          <a href="/ranking">
            <div class="topnav-icon-sprite colleges-ranking"></div>

            <!-- <img src="/images/ranking/ranking_icon.png" data-src="/images/ranking/ranking_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp; --><span>Ranking</span>
          </a>
        </li>
        <div class="clearfix" style="border-top: 1px #ff0000;"></div>
        <li>
          <a href="/comparison">
            <div class="topnav-icon-sprite compare-colleges"></div>

            <!-- <img src="/images/ranking/compare_icon.png" data-src="/images/ranking/compare_icon_hover.png" alt=""/>&nbsp;&nbsp; --><span>Compare Colleges</span>
          </a>
        </li>
        <li>
          <a href="/news/catalog/college-essays">
            <!-- <img id='public_college_search_icon' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon.png" data-src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
            <div class="topnav-icon-sprite me-docs"></div>
            <span>College Essays</span>
          </a>
        </li>
        <li <?php if($currentPage=='news') { ?> class="active" <?php } ?>>
          <a href="/news">
            <div class="topnav-icon-sprite me-news"></div>
           <span> The Quad </span>
          </a>
        </li>
        <li style="max-height: inherit;">
          <div class="signup-mobile"><a href="/signup?utm_source=SEO&utm_medium=frontPage_center"><div class='homepage-signup-button'>Sign up</div></a></div>
          <div class='fb-login-container hide-for-large-up signup-mobile'>
            <div class='large-12 rela fb-login-btn'>
              <a href="/facebook?utm_source=SEO&utm_medium=signupPage" class='signupFB'></i>Sign up with Facebook</a>

            </div>
          </div>
        </li>
        <li class="backFillFix mobileMenu_signInOutMenuItem">
          <a href="/signin">
              <div class="column small-10 center">Login</div>

          </a>
        </li>
      </ul>
		</div>
	</nav>
 <nav class="top-bar onscrollvisible" data-topbar>
 <?php
  $CollegeData = $college_data;
 ?>
    <div class="row hide-for-large-up">

        <!-- This is the Recruit Me / Compare buttons on tablet and mobile view -->
        @if($signed_in == 1)
            @if ($isInUserList == 0)
                @if( isset($profile_perc) && $profile_perc < 30 && $completed_signup == 0 )
                    <div class="small-12 medium-6 columns is-redirect getrecurit" data-cid="{{$CollegeData->id}}">
                @else
                    <div class="small-12 medium-6 columns getrecurit" data-reveal-id="recruitmeModal" data-reveal-ajax="/ajax/recruiteme/{{$CollegeData->id}}">
                @endif
                        <div class='mob-recruit-btn text-left orange-btn'>
                            <img src="/images/colleges/recruit-me-white.png" alt=""/>Get Recruited!
                        </div>
                    </div>
            @else
                <div class="small-12 medium-6 columns alreadyonlist">
                    <div class='mob-recruit-btn-pending text-left'>
                        <img class="mob-already-added-icon" src="/images/colleges/recruitment-btn.png" alt="">&nbsp;&nbsp;Already on my list!
                    </div>
                </div>
            @endif
        @else
            <a href="/signup?requestType=recruitme&collegeId={{$CollegeData->id}}&utm_source=SEO&utm_medium={{$currentPage or ''}}&utm_content={{$CollegeData->id}}&utm_campaign=recruitme">
                <div class="small-12 medium-6 columns getrecurit">
                    <div class='mob-recruit-btn text-left orange-btn'>
                        <img src="/images/colleges/recruit-me-white.png" alt=""/>Get Recruited!
                    </div>
                </div>
            </a>
        @endif

        <div class="small-12 medium-6 text-left columns alreadyonlist">
            <a href="{{$CollegeData->paid_app_url or '/college-application'}}">
                <div class='mob-apply-btn'>
                    <img src="/images/colleges/apply-btn.png" alt=""/>Apply Now
                </div>
            </a>
        </div>

          <div class="small-12 medium-6 text-left columns onsticky">
           <a href="https://plexuss.com/adRedirect?company=edx&utm_source={{$college_slug}}&cid=2&uid={{$user_id}}">
                <div class='mob-vs-btn edx_img'>
                    <img src="/images/colleges/edx.png" alt=""/> Take a free course !
                </div>
            </a>
        </div>


    </div>
</div>


</nav>

@endif
</div>
<div class='BgTop BgTop-notSignedIn hide-for-small-only'>
@else
<div class='BgTop BgTop-notSignedIn hide-for-small-only'>
@endif

	<div class='row collapse' id="innerpages-topnav">

		<!-- logo -->
		<div class='column small-3 show-for-medium-up' style='white-space: nowrap;'>
			<ul class="title-area ">
				<!--
				help pages NOT implemented yet
				<li><a href="/admin/help"><div class="help-faq"></div></a></li>
				-->
				<li class="name">
					@if( isset($is_organization) && $is_organization == 1 )
						@if( $currentPage == 'admin' )
						<span id="react_route_to_dashboard_2" style="cursor: pointer">
							<a href="/admin/dashboard">
								<img class="plex_logo_resize" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/premium_page/plexuss-white.png" alt='logo'/>
							</a>
						</span>
						@else
						<a href="/admin/dashboard">
							<img class="plex_logo_resize" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/premium_page/plexuss-white.png" alt='logo'/>
						</a>
						@endif
					@elseif (!isset($country_based_on_ip) || $country_based_on_ip !== 'US')
                		<a href="/">
                			<img class="plex_logo_resize" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/premium_page/plexuss-white.png" alt="logo">
                		</a>
					@else
						<a href="/">
							<img class="plex_logo_resize" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/premium_page/plexuss-white.png" alt='logo'/>
						</a>
					@endif
                    <span class='plexuss-student-network-text'>The Global Student Network</span>
				</li>


			</ul>
		</div>


		<!-- medium up search bar - DO NOT REMOVE -->
		<div class='column small-12 medium-5 large-6 clearfix'>
			<!-- (for colleges?) -->
			<?php
			// dd($data);
			?>
			@if(isset($is_organization) && $is_organization == 1 && (($currentPage !== 'admin' && $currentPage !== 'admin-inquiries' && $currentPage !== 'admin-removed' && $currentPage !== 'admin-converted' && $currentPage !== 'admin-Products') || (isset($is_admin_premium) && $is_admin_premium)))
					<div class='mobilesearch fl' id="SearchMobileDiv" >
					<ul class='searchBar-padding-fix row'>
						<li id="topnavsearch" class=" search-container" data-cid="{{$org_school_id or ''}}">
                            <div class='column small-11 wrapper no-padding'>
                                <div class="search-default-txt search-hamburger-img" onclick="setSearch();"></div>
                                <input type="text" placeholder="Search Plexuss" style="border: solid 1px black;" class="top_search_txt top_search_txt_val" id="mytopsearch" data-input onkeypress="javascript: if(event.keyCode == 13) topnavSearchStudent(event);
                                else{
						                    	AdminCollegeAutocomplete();
						                    }	">
                                <input type="hidden" class="top_search_txt_val" value="" />

                                <div class="top_search_filter d-none closedivset">
                                    <ul>
                                        <li id="default">
                                            <img alt="image" src="/images/select-logo.png" />
                                            <span>Search All of Plexuss..</span></li>
                                        <li id="college">
                                            <img alt="image" src="/images/select-collge-logo.png" />
                                            <span>Search Colleges..</span></li>
                                        <li id="news">
                                            <img alt="image" src="/images/select-news-logo.png" />
                                            <span>Search News..</span></li>

                                        <!-- if college -->
                                        <li id="students">
                                            <img alt="image" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/topsearch-student-icon.png" />
                                            <span>Search Students..</span>
                                        </li>

                                    </ul>
                                </div>
                            </div>
							<div class='column small-1'>
                                    <input type="hidden" class="top_search_type" value="" />
                                    <button class="go-btn" onclick="topnavSearchStudent(event);"></button>
                                </div>
							<div class="searchautocomplete"></div>
						</li>
					</ul>
				</div>

				<div class="adv-search-top-container show-for-large-up fr">
					<a class="adv-search-topnav show-for-medium-up" href="/admin/studentsearch">Advanced Search</a>
				</div>

			@elseif (isset($is_organization) && $is_organization == 1)
                <div class='admin-upgrade-button' onclick='location.href="/admin/premium-plan-request"'>
                    Upgrade to Premium
                </div>
			@else
				<div class="row txt-center">

						<div class="column show-for-medium-up medium-9 large-8 fn mauto">
			                <div class="main-plex-search-bar-container">
			                    <input id="top_search_txt" type="text" placeholder="Search Plexuss..." class="top_search_txt top_search_txt_val" data-input onkeypress="javascript: if(event.keyCode == 13) {topSearch(event);}
			                    else{
			                    	CollegeAutocomplete();
			                    }
			                     ">
			                    <input type="hidden" class="top_search_txt_val" value="" />
			                    <input type="hidden" class="top_search_type" value="" />
                                <!-- <div class="submit-plex-main-search-btn go-btn" onclick="redirectSearch();"></div> -->
			                    <div class="submit-plex-main-search-btn go-btn" onclick="topSearch(event);"></div>
			                </div>
			            </div>

				</div>
			@endif
		</div>

		<!-- sign out button area -->
		<div class='column small-12 medium-3 mobile-top-nav-notification-pane-styling'>
			<div class="topnav_buttons">
				<!-- Messages menu toggle -->
				@if ( isset($signed_in) && $signed_in == 1)
				<div class="show-for-medium-up text-right">

					<!-- messages notifications -->
					<div id='notify_msg' class="topnav_button notify_button notify_msg_unique">
						@if(isset($is_aor) && $is_aor == 0)
							<div class="nav-icon nav-utils msgs"></div>
							@if(isset($topnav_messages['unread_cnt']) && $topnav_messages['unread_cnt'] != 0)
								<div class="unread_count active">{{$topnav_messages['unread_cnt'] or ''}}</div>
							@else
								<div class="unread_count"></div>
							@endif
							<div class="open-notification-pane-arrow">
	                            <div class="top-nav-noti-pan-arrow top-nav-msg-noti-arrow"></div>
	                        </div>
	                    @else
							&nbsp;
                        @endif
					</div>

					<!-- all notifications -->
					<div id='notify_all' class="topnav_button notify_button notify_all_unique">
						@if(isset($is_aor) && $is_aor == 0)
							<div class="nav-icon nav-utils notifications"></div>
							@if(isset($topnav_notifications['unread_cnt']) && $topnav_notifications['unread_cnt'] != 0)
								<div class="unread_count active">{{$topnav_notifications['unread_cnt'] or ''}}</div>
							@else
								<div class="unread_count"></div>
							@endif
							<div class="open-notification-pane-arrow">
	                            <div class="top-nav-noti-pan-arrow top-nav-all-noti-arrow"></div>
	                        </div>
	                    @else
	                    	&nbsp;
                        @endif
					</div>
					<!-- settings -->
					<div class="topnav_button settings-menu-icon-section">
						<!--<img alt="settings" src="/images/nav-icons/settings_32.png" />-->
						<div class="nav-icon nav-utils settings"></div>
						<div class="open-notification-pane-arrow">
                            <div class="top-nav-settings-arrow"></div>
                        </div>
					</div>

					<!-- profile image -->
					<div class="topnav_button">
						<a href="/profile">
							<!--<img alt='' src={{ $profile_img_loc or "/images/profile/default.png" }} class="user-profile-image" />-->
							<div class="nav-profile-pic nav-utils" style="background-image: url({{ $profile_img_loc or '/images/profile/default.png' }})"></div>
						</a>
					</div>

					<!-- sign out -->
					<div class="topnav_button is-link">
						<a class="logout-link" href="/signout">sign out</a>
					</div>
				</div>
				@else
				<div class="row collapse signedOut_signupLogin_row topnav_button show-for-medium-up">
                    @if (!isset($is_gdpr) || $is_gdpr == false)
    					<div class="column small-offset-3 small-4 large-offset-5 large-3 loginBtnHomepage text-center">
    						<!-- <a href="/signin">Login</a> -->
    						<a href="javascript:void(0);" onclick="$('#absLoginForm').toggle();">
                                <div class="newLoginBtn">Login</div>
                            </a>
    					</div>
                    @endif


					<!-- login dropdown  -->
						<div id="absLoginForm">

                        	<div id="loginFormContainer" style="border: solid 0px #ff0000;padding:20px 20px;">
                                <div class='row'>
                                    <div class='large-12 columns'>
                                        <h1>Welcome Back!</h1>
                                    </div>
                                </div>



                                {{ Form::open(array('action' => 'AuthController@postSignin', 'data-abide' , 'id'=>'form')) }}
                                <div class='row'>
                                    <div class='large-12 columns'>
                                        @if(isset($errors) && $errors->any())
                                            <div class="alert alert-danger">
                                            {!! implode('', $errors->all('<li class="error">:message</li>')) !!}
                                            </div>
                                        @endif
                                    </div>
                                </div>



                                <div class='row'>
                                    <div class='large-12 columns'>
                                        {{ Form::text('email', null, array('id' => 'email', 'placeholder'=>'Email Address', 'required', 'pattern'=>'email')) }}
                                        <small class="error">*Please enter an Email Address.</small>
                                    </div>
                                </div>



                                <div class='row'>
                                    <div class='large-12 columns'>
                                        {{ Form::password('password', array('placeholder' => 'Password' , 'required', 'pattern' => 'passwordpattern')) }}
                                    </div>
                                </div>


                                <div class="row">
                                    <div class='large-12 columns text-left'>
                                        <small class="error passError ">*Please enter a valid password with these requirements:<br/>
                                            8-13 letters and numbers<br/>
                                            Starts with a letter<br/>
                                            Contains at least one number
                                        </small>
                                    </div>
                                </div>


                                <div class='row'>
                                    <div class='large-12 columns'>
                                        {{ Form::submit('Sign in', array('class'=>'signupButton'))}}
                                    </div>
                                </div>



                                <div class='row text-center'>
                                    <div class='show-for-large-up large-5 columns'>
                                        <div class='orLine'></div>
                                    </div>
                                    <div class='large-2 column ortxt'>OR</div>
                                    <div class='show-for-large-up large-5 columns'>
                                        <div class='orLine'></div>
                                    </div>
                                </div>


                                <div class='row'>
                                    <div class='large-12 columns rela'>
                                        <a href="/facebook" class='signupFB'>Log in with Facebook</a>
                                        <div class="fb-logo-container">
                                            <div id="facebook-logo" class="sm"></div>
                                        </div>
                                    </div>
                                </div>


                                <div class='row forgottxt'>
                                    <div class='large-7 columns text-center'>
                                        <a href="/signup?utm_source=SEO&utm_medium={{$currentPage or ''}}">Don’t  have an account yet?</a>
                                    </div>
                                    <div class='large-5 columns text-center'>
                                        <a href="/forgotpassword">Forgot password?</a>
                                    </div>
                                </div>

                                {{ Form::close() }}

                            </div>
						</div><!-- // end login dropdown -->

                    @if (!isset($is_gdpr) || $is_gdpr == false)
    					<div class="column small-5 large-4 signupBtnHomepage text-center">
    						@if(isset($url_params))
    							@if( $currentPage == 'international-students-page' )
    								<a href="/signup?utm_source=SEO&utm_term=topnav&utm_medium=international-students&redirect=international-students">Sign up</a>
    							@else
    								<a href="/signup?{{$url_params or ''}}">sign up</a>
    							@endif
    						@else
    							@if( $currentPage == 'international-students-page' )
    								<a href="/signup?utm_source=SEO&utm_term=topnav&utm_medium=international-students&redirect=international-students">Sign up</a>
    							@else
    								<a href="/signup?utm_source=SEO&utm_medium={{$currentPage or ''}}">Sign up</a>
    							@endif
    						@endif
    					</div>
                    @endif
				</div>
				@endif
				<!-- MESSAGE NOTIFICATIONS PANE -->
				<div class='row collapse'>
					<div id='notify_msg_pane' class='small-12 column notify_pane'>
						<div class='row collapse'>
							<div class='small-12 column notify_pane_container'>
								<!-- new notifications go here!!! -->
								<!-- NOTI 1 -->

								@if(isset($topnav_messages))
									@foreach ($topnav_messages['data'] as $note)
										<div class='row notify_item' onClick="notificationItemOnClick('{{$note['link'] or '/'}}');">
											<div class='small-2 column notify_image'>
												<img src='{{$note['img']  or "/images/profile/default.png" }}' alt=""/>
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
							@if(!isset($is_organization) || $is_organization == 0)
							<a href='/portal/messages'>
							@else
							<a href='/admin/messages'>
							@endif
								<div class='small-12 column view_all_button text-center'>
									View all messages
								</div>
							</a>
						</div>
					</div>
				</div>
				<!-- END MESSAGE NOTIFICATIONS PANE -->
				<!-- NOTIFICATIONS PANE -->
				<div class='row collapse'>
					<div id='notify_all_pane' class='small-12 column notify_pane'>
						<div class='row collapse'>
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
				<div class="row settings-topnav-menu-pane">
					<div class="column small-12">
						<a href="/settings">
							<div class="row collapse settings-topnav-menu-item">
								<div class="column small-12">
									Change Password
								</div>
							</div>
						</a>

						<!-- Advanced search log back in  -->
						@if(Session::has('sales_log_back_in_user_id'))
						<?php
						$logbackinURL = '/admin/ajax/logBackInAdvancedSearch/'.Session::get('sales_log_back_in_user_id');
						 ?>
						<a href="{{$logbackinURL}}">
							<div class="sales-logbackin settings-topnav-menu-item">
						 		Go back to Advanced Search
							</div>
						</a>
						@endif

						@if( !isset($is_organization) || $is_organization == 0 || !isset($super_admin) || $super_admin == 0 )
						<a href="/settings/email">
							<div class="row collapse settings-topnav-menu-item">
								<div class="column small-12">
									Email Notifications
								</div>
							</div>
						</a>

						<a href="/settings/text">
							<div class="row collapse settings-topnav-menu-item">
								<div class="column small-12">
									Text Notifications
								</div>
							</div>
						</a>

						<a href="/settings/invite">
							<div class="row collapse settings-topnav-menu-item">
								<div class="column small-12">
									Invite Friends to Plexuss
								</div>
							</div>
						</a>
						@endif

						@if(isset($super_admin) && $super_admin == 1 && $is_aor == 0)
							@if( $currentPage == 'admin' )
								<div id="react_route_to_manage_users" class="row collapse settings-topnav-menu-item">
									<div class="column small-12">
										Manage Users
									</div>
								</div>
							@else
								<a href="/admin/users">
									<div id="react_route_to_manage_users" class="row collapse settings-topnav-menu-item">
										<div class="column small-12">
											Manage Users
										</div>
									</div>
								</a>
							@endif
						@endif

						@if(isset($super_admin) && $super_admin == 1 && $is_aor == 0)
							@if( $currentPage == 'admin' )
								<div id="react_route_to_manage_portals" class="row collapse settings-topnav-menu-item">
									<div class="column small-12">
										Manage Portals
									</div>
								</div>
							@else
								<a href="/admin/portals">
									<div id="react_route_to_manage_portals" class="row collapse settings-topnav-menu-item">
										<div class="column small-12">
											Manage Portals
										</div>
									</div>
								</a>
							@endif
						@endif

						@if( isset($super_admin) && $super_admin == 1 && isset($is_aor) && $is_aor == 0 )
							@if( $currentPage == 'admin' )
								<div id="react_route_to_portal_login" class="row collapse settings-topnav-menu-item">
									<div class="column small-12">
										Switch Portals
									</div>
								</div>
							@else
								<a href="/admin">
									<div id="react_route_to_portal_login" class="row collapse settings-topnav-menu-item">
										<div class="column small-12">
											Switch Portals
										</div>
									</div>
								</a>
							@endif
						@endif

						@if( isset($super_admin) && $super_admin == 1 && isset($is_aor) && $is_aor == 0 )
							@if( $currentPage == 'admin' )
								<div id="react_route_to_profile" class="row collapse settings-topnav-menu-item">
									<div class="column small-12">
											Edit Profile
									</div>
								</div>
							@else
								<a href="/admin/profile">
									<div id="react_route_to_profile" class="row collapse settings-topnav-menu-item">
										<div class="column small-12">
												Edit Profile
										</div>
									</div>
								</a>
							@endif
						@endif

						@if(isset($is_aor) && $is_aor == 0)
						<a href="/settings/billing">
							<div class="row collapse settings-topnav-menu-item">
								<div class="column small-12">
									Billing
								</div>
							</div>
						</a>
						@endif

                        @if (isset($is_gdpr) && $is_gdpr == true)
                        <a href="/settings/data_preferences">
                            <div class="row collapse settings-topnav-menu-item">
                                <div class="column small-12">
                                    Data Preferences
                                </div>
                            </div>
                        </a>
                        @endif

						<!-- Sales log back in  -->
						@if(Session::has('sales_log_back_in_user_id'))
						<?php
						$logbackinURL = '/admin/ajax/logBackIn/'.Session::get('sales_log_back_in_user_id');
						 ?>
						<a href="{{$logbackinURL}}">
							<div class="sales-logbackin settings-topnav-menu-item">
						 		Go back to Sales
							</div>
						</a>
						@endif

						<!-- AOR log back in -->
						@if(Session::has('aor_log_back_in_user_id'))
						<?php
						$logbackinURL = '/admin/ajax/logBackIn/'.Session::get('aor_log_back_in_user_id');
						?>
						<a href="{{$logbackinURL}}">
							<div class="sales-logbackin settings-topnav-menu-item">
								Go Back to Manage Colleges
							</div>
						</a>
						@endif

					</div>
				</div>
				<!-- settings dropdown menu pane - end -->
			</div>
		</div>


	</div>
</div>
<script>
	function topSearch(event){
		var value=$('#top_search_txt').val();
		if(value!==''){
			var type=$('.top_search_type').val();
			var cid=$('#topnavsearch').data('cid');
			var mDefault='default';
			if(window.location.pathname.split('/')[0]==='admin')mDefault='students';
			if(type==''){
				window.location='/search?type='+mDefault+'&term='+value+'&cid='+cid;
			}else{
				window.location='/search?type='+type+'&term='+value;}
			}else{
				alert("Empty input field !!");
				return false;
			}
		}

	function topnavSearchStudent(event){
		var value=$('#mytopsearch').val();
		if(value!=''){
			var type=$('.top_search_type').val();
			var cid=$('#topnavsearch').data('cid');
			var mDefault='default';
			if(window.location.pathname.split('/')[0]==='admin')mDefault='students';
			if(type==''){
				window.location='/search?type='+mDefault+'&term='+value+'&cid='+cid;
			}
			else{
				window.location='/search?type='+type+'&term='+value;
			}
		}
		else{
			alert("Empty input field !!");
			return false;
		}
	}

	function switchIcon(e) {
		if($(e).html() == 'x') {
			$(e).html('<a href="#"><span></span></a>');
      $(e).removeClass("close-icon-pos");
		}
		else {
			$(e).html('x');
      $(e).addClass("close-icon-pos");
		}
	}

function CollegeAutocomplete(){
		var stype = 'default';
    var src = "/getTopSearchAutocomplete?type=" + stype;
    var cid = $('#topnavsearch').data('cid');
    if (cid)
        src += "&cid=" + cid;
    $("#top_search_txt").autocomplete({

        source: src,
            focus: function( event, ui ) {
            //$( "#search" ).val( ui.item.title ); // uncomment this line if you want to select value to search box
            return false;
        },
        select: function( event, ui ) {
            window.location.href = ui.item.url;
        }
	    }).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
	    var newsurl = '';
        if (item.type == 'news') { newsurl = 'article/'; }
        var inner_html = '';
        if (item.searchtype === 'students') { inner_html += '<a href="/admin/' + item.type + '">';
            inner_html += '<div class="list_item_container clearfix">';
            inner_html += '<div class="res-leftside">';
            inner_html += '<div class="list_item_image_container"><img class="image" src="' + item.image + '" alt=""/></div>';
            inner_html += '<div class="title">' + item.value + '</div>';
            inner_html += '<div class="res-email">' + (item.email || 'N/A') + '</div>';
            inner_html += '</div>';
            inner_html += '<div class="res-rightside"><div>' + item.tab + '</div> <div class="res-phone">' + (item.phone || 'N/A') + '</div></div>';
            inner_html += '</div></a>'; } else { inner_html += '<a href="/' + item.type + '/' + newsurl + item.slug + '">';
            inner_html += '<div class="list_item_container clearfix">';
            inner_html += '<div class="list_item_image_container"><img class="image" src="' + item.image + '" alt=""/></div>';
            inner_html += '<div style="padding-left: 64px;">';
            inner_html += '<div class="title">' + item.value + '</div>';
            inner_html += '<span class="description">' + item.desc + '</span>';
            inner_html += '</div>';
            inner_html += '</div>';
            inner_html += '</a>'; }
	        return $( "<li></li>" )
	                .data( "item.autocomplete", item )
	                .append(inner_html)
	                .appendTo( ul );
	    };
	}

function AdminCollegeAutocomplete(){
		var stype = 'default';
    var src = "/getTopSearchAutocomplete?type=" + stype;
    var cid = $('#topnavsearch').data('cid');
    if (cid)
        src += "&cid=" + cid;
    $("#mytopsearch").autocomplete({

        source: src,
            focus: function( event, ui ) {
            //$( "#search" ).val( ui.item.title ); // uncomment this line if you want to select value to search box
            return false;
        },
        select: function( event, ui ) {
            window.location.href = ui.item.url;
        }
	    }).data( "ui-autocomplete" )._renderItem = function( ul, item ) {
	    var newsurl = '';
        if (item.type == 'news') { newsurl = 'article/'; }
        var inner_html = '';
        if (item.searchtype === 'students') { inner_html += '<a href="/admin/' + item.type + '">';
            inner_html += '<div class="list_item_container clearfix">';
            inner_html += '<div class="res-leftside">';
            inner_html += '<div class="list_item_image_container"><img class="image" src="' + item.image + '" alt=""/></div>';
            inner_html += '<div class="title">' + item.value + '</div>';
            inner_html += '<div class="res-email">' + (item.email || 'N/A') + '</div>';
            inner_html += '</div>';
            inner_html += '<div class="res-rightside"><div>' + item.tab + '</div> <div class="res-phone">' + (item.phone || 'N/A') + '</div></div>';
            inner_html += '</div></a>'; } else { inner_html += '<a href="/' + item.type + '/' + newsurl + item.slug + '">';
            inner_html += '<div class="list_item_container clearfix">';
            inner_html += '<div class="list_item_image_container"><img class="image" src="' + item.image + '" alt=""/></div>';
            inner_html += '<div style="padding-left: 64px;">';
            inner_html += '<div class="title">' + item.value + '</div>';
            inner_html += '<span class="description">' + item.desc + '</span>';
            inner_html += '</div>';
            inner_html += '</div>';
            inner_html += '</a>'; }
	        return $( "<li></li>" )
	                .data( "item.autocomplete", item )
	                .append(inner_html)
	                .appendTo( ul );
	    };
	}
</script>
<!-- \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\ inner non-frontpage top nav - end //////////////////////////////////////// -->
