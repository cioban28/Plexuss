<?php
    $is_international = !isset($country_based_on_ip) || $country_based_on_ip !== 'US'; // Case for checking just IP

    $user_country_id = isset($country_id) ? $country_id : null;

    $from_united_states = $user_country_id === 1 || !$is_international; // Case for checking users table or IP

    $me_tab_application_route = $from_united_states ? '/get_started' : 'college-application';
?>
 @if( !isset($signed_in) || $signed_in != 1 )
<!-- Top Nav Section -->
<!--<div id='loadingscreen'></div>-->
<div class='show-for-small-only sticky'>
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
      <ul class="topnav-college-drop" id="college-mobile-menu">
        <li>
          <a href="/college">
            <!-- <img id='public_college_search_icon' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon.png" data-src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
            <div class="topnav-icon-sprite find-colleges"></div>
            <span>Find Colleges</span>
          </a>
        </li>


        <li>
          <a href="/college-fairs-events">
            <!-- <img id='public_college_search_icon' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon.png" data-src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
            <div class="topnav-icon-sprite events"></div>
            <span>College Fair</span>
          </a>
        </li>

        <li>
          <a href="/college-majors">
            <div class="topnav-icon-sprite colleges-majors-icon"></div>

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
        <li style="max-height: inherit;">
          <div class="signup-mobile"><a href="/signup?utm_source=SEO&utm_medium=frontPage_center"><div class='homepage-signup-button'>Sign up</div></a></div>
          <div class='fb-login-container hide-for-large-up signup-mobile'>
            <div class='large-12 rela fb-login-btn'>
              <a href="/facebook?utm_source=SEO&utm_medium=signupPage" class='signupFB'>Sign up with Facebook</a>

            </div>
          </div>
        </li>
        <li class="backFillFix mobileMenu_signInOutMenuItem">
          <a href="/signin">
              <div class="column small-10 center">Sign in</div>
            </div>
          </a>
        </li>
      </ul>
		</div>
	</nav>
</div>
@endif

<!-- Daily chat bar! - start -->
@if(isset($webinar_is_live) && $webinar_is_live == true)
<div class="daily-chat-bar-container webinar-live">
    <div class="blip"></div>
    <div class="chat-label"><b><a href="/">Live Webinar going on right now! Join now!</a></b></div>
</div>
@endif
<!-- Daily chat bar! - end -->


<!-- when daily chat bar is up, add this class: with-daily-chat-bar, to new-phaseTwo-topnav element and removed .fixed !!!!!!!!!!!!!! -->
@if(isset($currentPage) && $currentPage != 'unsubscribeThisEmail' || !isset($currentPage))



    @include('public.includes.topNavigationwLogo')

    <!-- bottom top nav for new homepage - start -->
    @if( isset($signed_in) && $signed_in == 1 )
        <div class="bottom-phaseTwo-top-nav show-for-medium-up">
            <nav class="top-bar frontpage-phaseTwo-topbar" data-topbar role="navigation">

                <section class="top-bar-section frontpage-bottom-top-bar-section">

                    <!-- left side nav -->
                    <ul class="left">
                        @if( isset($signed_in) && $signed_in == 1 )
                        <li><a href="/home">Home</a></li>
                        <!-- <li><a href="/profile">Me</a></li> -->
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
                        <li><a href="/portal">Portal</a></li>
                        @endif

                        <!-- colleges -->
                       <!--  <li <?php if($currentPage=='college-home' || $currentPage=='ranking' || $currentPage=='comparison') { ?> class="active" <?php } ?> style="position:relative;" onmouseover="$('#college-sub-menu').show();" onmouseout="$('#college-sub-menu').hide();">
                        <a class="mainTopNav_collegeTab" href="/college">
                            Colleges <span class="navigation-arrow-down1">&nbsp;</span>
                        </a>
                        <ul style="position:absolute;width:257px;display:none;z-index: 1;" id="college-sub-menu">
                            @if (!isset($country_based_on_ip) || $country_based_on_ip == 'US')
                            <li>
                                <a href="/college">
                                    <img id='public_college_search_icon' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon.png" data-src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Find Colleges
                                </a>
                            </li>
                            @endif
                            @if (!isset($country_based_on_ip) || $country_based_on_ip !== 'US')
                            <li>
                                <a href="/international-students">
                                    <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/intl_students_nav_icon.png" data-src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/intl_students_nav_icon_hover.png" alt=""/>&nbsp;&nbsp;International Students
                                </a>
                            </li>
                            @endif
                            <li>
                                <a href="/ranking">
                                    <img src="/images/ranking/ranking_icon.png" data-src="/images/ranking/ranking_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp;Ranking
                                </a>
                            </li>
                            <div class="clearfix" style="border-top: 1px #ff0000;"></div>
                            <li>
                                <a href="/comparison">
                                    <img src="/images/ranking/compare_icon.png" data-src="/images/ranking/compare_icon_hover.png" alt=""/>&nbsp;&nbsp;Compare Colleges
                                </a>
                            </li>
                        </ul> -->
                        <!-- </li> -->
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
                                    <!-- <img id='public_college_search_icon' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon.png" data-src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
                                    <div class="topnav-icon-sprite events"></div>
                                    College Fairs
                                </a>
                            </li>


                            <li>
                                <a href="/college-majors">
                                    <div class="colleges-majors-icon mt8"></div>

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
                    </li>
                        <!-- colleges -->
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
                          <ul class="topnav-college-drop" style="position:absolute;width:257px;display:none;z-index: 1;" id="int-sub-menu">
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
                              <li class="menu_li">
                                  <a href="/premium-plans-info">
									<div class="topnav-icon-sprite plexuss-premium"></div>
                                    Plexuss Premium
                                </a>
                              </li>

                            </ul>
                        </li>
                        @if( isset( $is_organization ) && $is_organization == 1 )
                            <li><a href="/admin">Admin</a></li>
                        @endif
                        @if(isset($is_sales))
                            <li>
                                <a href="/sales">Sales</a>
                            </li>
                            <li>
                                <a href="/publisher">Publisher</a>
                            </li>
                        @endif
                        @if($is_agency == 1)
                            <li>
                                <a href="/agency">Agency</a>
                            </li>
                        @endif


                    @if(!isset($is_sales))
                    @if (!isset($is_organization) || $is_organization == 0)

                        <!-- center nav -->
                        <li style="position:relative;" onmouseover="$('#int-sub-menu1').show();" onmouseout="$('#int-sub-menu1').hide();" class="i-want-to-tab-signedin front-page show-for-medium-up">
                          <a href="#">I want to<span class="navigation-arrow-down1">&nbsp;</span></a>
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
                    @endif
                    @endif
                    </ul>
                    <!-- right side nav -->
                    <ul class="right" style="display: flex; align-items: center;">
                        <!-- Remove upgrade button for now 1/17-->
                        @if( isset($premium_user_level_1) && $premium_user_level_1 == 0 && (!isset($is_organization) || $is_organization == 0 ) )
                            <!--
                            <li>
                                <div class="upgrade-to-premium-container">
                                    <div class="upgrade-to-premium-btn">Upgrade</div>
                                    <div class="upgrade-tooltip" style='z-index: 1;'>
                                        <div>Stand out to colleges as an elite member. Upgrade to premium!</div>
                                        <div class="upgrade-tooltip-arrow"></div>
                                    </div>
                                </div>
                            </li>
                            -->
                        @endif
                        <li><a class="business-services" href="/solutions">Business Services</a></li> 
                    </ul>
                </section>
            </nav>
        </div>
    @else

        <!-- top nav for homepage when not signed in -->
        <div class="bottom-phaseTwo-top-nav">
        <nav class="top-bar frontpage-phaseTwo-topbar hide-for-small-only" data-topbar role="navigation">

            <section class="top-bar-section frontpage-bottom-top-bar-section">

                <!-- left side nav -->
                <ul class="left">
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
                                    <!-- <img id='public_college_search_icon' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon.png" data-src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/intl_students/us_students_nav_icon_hover.png">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; -->
                                    <div class="topnav-icon-sprite events"></div>
                                    College Fairs
                                </a>
                            </li>
                            <li>
                                <a href="/college-majors">
                                    <div class="colleges-majors-icon mt8"></div>

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
                    </li>
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
                      <ul class="topnav-college-drop" style="position:absolute;width:257px;display:none;z-index: 1;" id="int-sub-menu">
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
                          <li class="menu_li">
                              <a href="/premium-plans-info">
                                <div class="topnav-icon-sprite plexuss-premium"></div>
                                Plexuss Premium
                              </a>
                          </li>
                        </ul>
                    </li>
                </ul>

                <!-- center nav -->
                @if (!isset($is_sales) && (!isset($is_organization) || $is_organization == 0))
                    <ul class="i-want-to-tab front-page show-for-medium-up @if($is_international) i-want-to-tab-intl @endif">
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
                @endif

                <!-- right side nav -->
                <ul class="right">
                    <li><a class="business-services" href="/solutions">Business Services</a></li>
                </ul>
            </section>
        </nav>
    </div>


    @endif
    <!-- bottom top nav for new homepage - end -->


</div><!-- end of new-phaseTwo-topnav -->


<script>
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
</script>
<!-- upgrade to premium modal -->
@if (isset($signed_in) && $signed_in === 1)
    @include('private.includes.upgradeToPremiumModalNew')
@endif

@endif
