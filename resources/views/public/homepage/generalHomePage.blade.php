@extends('public.homepage.master')
@section('content')
<!-- when daily chat bar is up, add this class: with-daily-chat-bar, to phase_two_frontpage element -->
@if( isset($webinar_is_live) && $webinar_is_live == true )
<div class="row phase_two_frontpage collapse with-daily-chat-bar">
@else

  @if( isset($signed_in) && $signed_in == 1 )
    <div class="row phase_two_frontpage collapse">
  @else
    <div class="row extra-margin-notsigned phase_two_frontpage collapse">
  @endif
@endif
  <div class="column small-12">

    <!-- \\\\\\\\\\\\\\\\\\\\\\\\\start of front page content banner/////////////////////// -->
    @if( isset($frontpage_bg_info) && $frontpage_bg_info['is_video'] == 1 )
    <div class="frontpage-top-content-banner-back">
    @else
{{--
Old Front Page Background

    <div class="frontpage-top-content-banner-back" data-bg="" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/{{$frontpage_bg_info['image'] or 'courtyard-convo.jpg'}}, (medium)]">

End Old Front Page Background
--}}
        {{-- Plexuss Mobile Ad --}}
        <div class="owl-carousel owl-theme"><div id="slide1" class="frontpage-top-content-banner-back custSlider item">
    @endif
      <div class="row collapse frontpage-content-banner-row">

        <!-- background video - start -->
        @if( isset($frontpage_bg_info) && $frontpage_bg_info['is_video'] == 1 )
        <div class="background-video-container">
          <div class="hide-for-large-up video-replacement-for-sm-med" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/{{$frontpage_bg_info['poster']}}, (default)]"></div>

          <video class="show-for-large-up @if(isset($frontpage_bg_info['custom_class'])) {{$frontpage_bg_info['custom_class']}} @endif" autoplay muted loop poster="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/{{$frontpage_bg_info['poster']}}" id="background-vid">
            <source src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/{{$frontpage_bg_info['image']}}" type="video/mp4">
          </video>
        </div>
        @endif

        <!-- background video - end -->

        <!-- back/close button to close side bar sections when open - end -->
        <div class="row mobile-frontpage-back-btn" style="@if($enable_chat && !$webinar_is_live) display: block; @endif">
          <div class="column small-12 medium-10">
            <div class="mobile-back-btn"><span>&#8249;</span> <span>Back</span></div>
          </div>
        </div>
                @include('includes.iWantToMobileModal')
        <!-- opening college recruitment welcome section -->
        <div id="frontpage_opening_side_bar_section" class="column large-12 frontpage-side-bar-sections indian-bg" style="">

                    <div class="plexuss-mobile-ad-frontpage-left-side small-12">
                        <div class="homepage-plexuss-quote homepage-plexuss-quote-general">Guarantee Your University Acceptance With Plexuss Premium</div>
                        <a href="/premium-plans-info/general"><div class='homepage-signup-button homepage-learn-button-indian'>Learn more</div></a>
                    </div>

                    <div class="plexuss-mobile-ad-frontpage-right-side small-12">
                        <div class="hover-circles">
                          <div class='start-here-indicator hide-for-small'><img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/start-here.png'/></div>

                          <div><img class="frontpage-circle-img left-circle" src="/images/indianPremium/college-students-general.png"></div>
                          <div><img class="frontpage-circle-img right-circle" src="/images/indianPremium/premium-graduate-general.png"></div>
                        </div>
            
            @if (!(isset($is_mobile) && $is_mobile == true)) 
            <div id="chrome-extension-container">
                            <a class='chrome-extension-button' href="https://chrome.google.com/webstore/detail/plexuss-extension/eglbdkobllcefihgfgkipponopipnkma?hl=en" target="_blank">
                                <div class='close-chrome-extension-button'>&times;</div>
                                <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/download_chrome_extension.png" />
                            </a>
                        </div>
                        @endif
                    </div>

                    <div style="display: none" id="handshake-ticker-component">
                    </div>

                    <div class='circle-clicked-overlay left-circle-overlay'>
                        <div class='overlay-detail-text'>Connect with over 5 million students & alumni</div>
                    </div>

                    <div class='circle-clicked-overlay right-circle-overlay'>
                        <div class='overlay-detail-text'>Find and apply to universities & scholarships</div>
                    </div>
        </div>

        @if (!(isset($is_mobile) && $is_mobile == true))
        <!-- opening section's background image college link -->
        <div class="row opening-background-college-caption hidden" >
          <div class="column small-11 small-centered text-right show-for-medium-up">
            <div class="fb-likes-container clearfix">
              <iframe src="https://www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2FPlexusscom-465631496904278%2F&width=88&layout=button_count&action=like&show_faces=false&share=false&height=21&appId=663647367028747" width="100" height="25" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe>
            </div>
          </div>
        </div>
        @endif

        <!-- get started section - start -->
                <div id="get_started" class="column large-12 frontpage-side-bar-sections" data-bg="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/get_started_bg.jpg" data-is-section="get_started_section" style="padding-bottom: 2em;" data-signed_in="0">
                  <!-- content ajaxed in on click -->
        </div>
        <!-- get started section - end -->

        <!-- find a college section - start -->
        <div id="find_a_college_side_bar_section" class="column large-12 frontpage-side-bar-sections" style=" padding-bottom: 2em;" data-is-section="find_a_college_section">
          <!-- content ajaxed in on click -->
        </div>
        <!-- find a college section - end -->

        <!-- chat section - start -->
        <div id="chat_side_bar_section" class="column large-12 frontpage-side-bar-sections" style="" data-is-section="member_colleges_section">
          <!-- content ajaxed in on click -->
        </div>
        <!-- chat section - end -->

        <!-- compare colleges section - start -->
        <div id="compare_side_bar_section" class="column large-12 frontpage-side-bar-sections" style="" data-is-section="compare_colleges_section">
          <!-- content ajaxed in on click -->
        </div>
        <!-- compare colleges section - end -->

      </div><!-- end of content banner row -->
    </div>

    <div id="slide2" class="custSlider item hide-for-large-up">
        <p style="text-align:center"><img src="/images/plexuss-logo-p.png" /></p>
        <h1>What is Plexuss?</h1>
        <p>
          Plexuss enables students to find and apply to universities around the world.
        </p><p>
          For example if you’re a student outside of the united states, you can research and apply to universities directly from plexuss apps.
        </p><p class="slide2-lastpara">
          If you’re a student in the US you can research and apply to universities abroad.
        </p>
        @if( !isset($signed_in) || $signed_in != 1 )
        <a href="/signup?utm_source=SEO&utm_medium=frontPage_center"><div class='homepage-signup-button'>Sign up</div></a>
        @endif
    </div>

    <div id="slide3" class="custSlider item hide-for-large-up">
        <h1>Communicate & Connect</h1>
        <p class="para1">
          <input type="text" style="margin:0;" readonly="true" disabled="true" placeholder="Can you show me colleges?" />
        </p><p class="para_button">
          <button class="btn btn-primary button1" disabled="true">Sure thing! Please select an option below to narrow your search.</button>
        </p><p class="para2">
          <input type="text" readonly="true" placeholder="Colleges Around Me" disabled="true" />
        </p><p class="para2">
          <input type="text" readonly="true" placeholder="Colleges By Conference" disabled="true" />
        </p><p class="para2">
          <input type="text" readonly="true" placeholder="Top 10 Colleges" disabled="true" />
        </p>
        <p class="subtitle">More options</p>
        <p>
          Plexuss provides a variety of ways to communicate with colleges, current students and alumni including live chat, email and text.
        </p>

        @if( !isset($signed_in) || $signed_in != 1 )
        <a href="/signup?utm_source=SEO&utm_medium=frontPage_center"><div class='homepage-signup-button'>Sign up</div></a>
        @endif
    </div>

    <div id="slide4" class="custSlider item hide-for-large-up">
        <h1>Manage</h1>
        <p style="text-align:center"><img src="/images/manage.png" /></p>
        <p style="text-align:center">
          Use your portal to manage applications, scholarships, favorite colleges, receive recommendations and see which colleges have viewed your profile.
        </p>

        @if( !isset($signed_in) || $signed_in != 1 )
        <a href="/signup?utm_source=SEO&utm_medium=frontPage_center"><div class='homepage-signup-button'>Sign up</div></a>
        @endif
    </div>

    <div id="slide5" class="custSlider item hide-for-large-up">
        <h1>Apply</h1>
        <p style="text-align:center"><img src="/images/apply.png" /></p>
        <p style="text-align:center" class="para1">
          Use Plexuss free college application (OneApp) to apply to universities around the world and receive scholarships.
        </p><p style="text-align:center">
          It takes one app to start your college journey on Plexuss.
        </p>

        @if( !isset($signed_in) || $signed_in != 1 )
        <a href="/signup?utm_source=SEO&utm_medium=frontPage_center"><div class='homepage-signup-button'>Sign up</div></a>
        @endif
    </div>

  </div>

        <!-- bottom bar nav -->
        <div class="frontpage-bottom-bar-container">
            <div class="icon-bar medium-vertical four-up frontpage-custom-icon-bar" data-chat-enabled="{{$enable_chat or 0}}">
                <!-- if signed in, add class, otherwise, don't -->
                @if( isset($signed_in) && $signed_in == 1 )
                <!-- Removed this make-room-for-signedin-topbar -->
                <a class="item bottom-nav-item" href="#get_started" data-section="get_started_section">
                @else
                <a class="item bottom-nav-item" href="#get_started" data-section="get_started_section">
                @endif
                    <div class="text-center fp-icon"><div class="fp-sprite get-start"></div></div>
                    <label class="hide-for-small-only">Get Started</label>
                </a>
                <a class="item bottom-nav-item" href="#find_a_college_side_bar_section" data-section="find_a_college_section">
                    <div class="text-center fp-icon"><div class="fp-sprite find-col"></div></div>
                    <label class="hide-for-small-only">Find a College</label>
                </a>
                <a class="item bottom-nav-item @if($enable_chat && !$webinar_is_live) active-custom-side-bar @endif" href="#chat_side_bar_section" data-section="member_colleges_section">
                    <div class="text-center fp-icon"><div class="fp-sprite chat"></div></div>
                    <label class="hide-for-small-only @if($enable_chat && !$webinar_is_live) active-custom-side-bar-label @endif">Member Colleges</label>
                </a>
                <a class="item bottom-nav-item" href="#compare_side_bar_section" data-section="compare_colleges_section">
                    <div class="text-center fp-icon"><div class="fp-sprite compare"></div></div>
                    <label class="hide-for-small-only">Compare Colleges</label>
                </a>
            </div>
            <div class="bottom-bar-app-store-links">
                <a href="http://apple.co/2x0hv8I" target="_blank" style="margin-right:1em;">
                    <img class="download-appstore-icon bottom-bar-download-app-icon" src="/images/plexuss-mobile-ads/download-appstore.png">
                </a>

                <a href="http://bit.ly/2MSG5U7" target="_blank" style="margin-right:1em;">
                    <img class="download-googleplay-icon bottom-bar-download-app-icon" src="/images/plexuss-mobile-ads/google-play.png">
                </a>
                <div class="send-app-store-sms-button">Send an SMS link</div>

                @include('frontpage.send_mobile_app_sms_modal')
            </div>
        </div>
    <!-- /////////////////end of front page main content background image slider div\\\\\\\\\\\\\\\\ -->

    <!-- \\\\\\\\\\\\\\\\\\\start of list of carousels////////////////// -->
    <div id="fp-carousels-container" class="row collapse college-carousels-list hide-for-small">
      <div class="column small-12">
        <!-- carousels injected here -->
      </div>
    </div>
    <!-- ///////////////////end of list of carousels\\\\\\\\\\\\\\\\\\\ -->

  </div>
</div>

<div class="section-loader text-center hide-for-small">
    <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/loading.gif" alt="Loading gif">
</div>

@include('private.includes.plex_lightbox')

<div itemscope itemtype="http://schema.org/WebSite" style="display:none;">
  <meta itemprop="url" content="http://plexuss.com/"/>
  <form itemprop="potentialAction" itemscope itemtype="http://schema.org/SearchAction">
    <meta itemprop="target" content="https://plexuss.com/search?type=college&term={search_term_string}"/>
    <input itemprop="query-input" type="text" name="search_term_string" required/>
    <input type="submit"/>
  </form>
</div>

<script src="/js/vendor/jquery.js"></script>
<script src="/js/owl.carousel.min.js"></script>
@if(isset($is_mobile) && $is_mobile == true)
<script>
    $('.owl-carousel').owlCarousel({
      items: 1,
      startPosition: 1,
      singleItem: true,
      itemsScaleUp : false,
      slideSpeed: 500,
    });
</script>

@else
<script>
  $('.owl-carousel').owlCarousel({
      items: 1,
      startPosition: 1,
      singleItem: true,
      itemsScaleUp : false,
      slideSpeed: 500,
      mouseDrag:false,
    });
</script>
@endif

@stop
