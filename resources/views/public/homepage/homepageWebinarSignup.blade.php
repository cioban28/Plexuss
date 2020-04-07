@extends('public.homepage.master')
@section('content')

<!-- when daily chat bar is up, add this class: with-daily-chat-bar, to phase_two_frontpage element -->
@if( isset($webinar_is_live) && $webinar_is_live == true )
<div class="row phase_two_frontpage collapse with-daily-chat-bar">
@else
<div class="row phase_two_frontpage collapse">
@endif
	<div class="column small-12">
		
		<!-- \\\\\\\\\\\\\\\\\\\\\\\\\start of front page content banner/////////////////////// -->
		<div class="webinar-live-frontpage-top-content-banner-back">
		
			<div class="row collapse frontpage-content-banner-row">

				
				<div class="column medium-2 large-1 side-bar-nav">

					<!-- side bar nav -->
					<div class="icon-bar medium-vertical four-up frontpage-custom-icon-bar" data-chat-enabled="{{$enable_chat or 0}}">

						<!-- if signed in, add class, otherwise, don't -->
						@if( isset($signed_in) && $signed_in == 1 )
						<a class="item make-room-for-signedin-topbar" href="#get_started" data-section="get_started_section">
						@else
						<a class="item" href="#get_started" data-section="get_started_section">
						@endif
							<div class="text-center fp-icon"><div class="fp-sprite get-start"></div></div>
							<label class="hide-for-small-only">Get <br>Started</label>
						</a>
						<a class="item" href="#find_a_college_side_bar_section" data-section="find_a_college_section">
							<div class="text-center fp-icon"><div class="fp-sprite find-col"></div></div>
							<label class="hide-for-small-only">Find a <br>College</label>
						</a>
						<a class="item @if($enable_chat && !$webinar_is_live) active-custom-side-bar @endif" href="#chat_side_bar_section" data-section="member_colleges_section">
							<div class="text-center fp-icon"><div class="fp-sprite chat"></div></div>
							<label class="hide-for-small-only @if($enable_chat && !$webinar_is_live) active-custom-side-bar-label @endif">Member<br />Colleges</label>
						</a>
						<a class="item" href="#compare_side_bar_section" data-section="compare_colleges_section">
							<div class="text-center fp-icon"><div class="fp-sprite compare"></div></div>
							<label class="hide-for-small-only">Compare <br>Colleges</label>
						</a>
					</div>

					</div>

					<!--//////////////banner when webinar is live ////////////-->
					<!-- <div class='column medium-10 large-11 '>
					<div class="home-webinar-background-image">
					
					@if(isset($webinar_live_already_signup) && $webinar_live_already_signup == true)
						
						<div class="centered-container">
					 		{{$webinar_embeded_video or ''}}
					 	</div>

					@else

					<div class='mailer-form-cont'>
		
						<div class="webinar-live-msg">
							<div class="webinar-live-icon"></div>
							<div class="web-title-grey">LIVE Webinar:</div>
						</div>

						<div class="web-bold-title-grey">
							 The Surprising Benefits of a<br>
							 Faith-Based Education
							
						</div>
		
						<div class="web-sub-header-grey">
							Presented by William Wegert<br> 
						</div>
						<div class="webinar-error-section"></div>
						<form class="home-webinar-form">
							<input type="text" name="name" value="Name">
							<input type="text" name="email" value="Email">
							<button  id="webinar-join-btn">Join now</button>
						</form>

						<div class="bottom-university-container">
							<div class="uni-pic"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/liberty-logo.jpg" alt=""/></div>
							<!-- <div class="uni-text">Liberty University</div> -->
						<!-- </div>

					</div>
					@endif
					</div>
					</div> -->
					<!---////////////////-->

				

				<!-- back/close button to close side bar sections when open - end -->
				<div class="row mobile-frontpage-back-btn" style="@if($enable_chat && !$webinar_is_live) display: block; @endif">
					<div class="column small-12 medium-10">
						<div class="mobile-back-btn"><span>&#8249;</span> <span>Back</span></div>
					</div>
				</div>

				<!-- opening college recruitment welcome section -->
				<div id="frontpage_opening_side_bar_section" class="column medium-10 large-11 frontpage-side-bar-sections" style="@if($enable_chat && !$webinar_is_live) display:none; @endif">
					
					

					@if( isset($is_mobile) && $is_mobile )
					<!-- disable for webinar -->
					
					<div class="row frontpage-college-recruitment-welcome">
						<div class="column small-12 small-text-center">
							<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/frontpage-plex-logo-tagline.jpg" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/frontpage-plex-logo-tagline.jpg, (small)], [https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/college-academic-recruiting-network.png, (medium)]" alt="">
						</div>
					</div>
					
					@endif
					<!-- disable for webinar -->
					
					<div class="row">
						<div class="column small-11 small-centered show-for-small-only">
							<div class="searchBar-container">
			                    <input type="text" placeholder="Search Plexuss.." class="top_search_txt_val top_search_txt frontpage_mobile_main_search" data-input>
			                    <input type="hidden" class="top_search_txt_val" value="" />

			                    <input type="hidden" class="top_search_type" value="" />
			                    <div class="submit_advSearch_searchBar_btn" onclick="redirectSearch();"></div>
			                </div>			  
						</div>
					</div>
					

					


				</div>

				<!-- opening section's background image college link -->
				@if( isset($frontpage_bg_info) )
				<div class="row opening-background-college-caption">
					<div class="column small-11 small-centered text-right show-for-medium-up">
						@if(isset($frontpage_bg_info['show_chat_btn']) && $frontpage_bg_info['show_chat_btn'] == 2)
							<div class="fb-likes-container clearfix">
								<iframe src="https://www.facebook.com/plugins/like.php?href=https%3A%2F%2Fwww.facebook.com%2FPlexusscom-465631496904278%2F&width=88&layout=button_count&action=like&show_faces=false&share=false&height=21&appId=663647367028747" width="100" height="25" style="border:none;overflow:hidden" scrolling="no" frameborder="0" allowTransparency="true"></iframe>
								<!--<a href="https://www.facebook.com/Plexusscom-465631496904278" target="_blank">
									<section id="fb-likes-flipper" class="left">
										<p class="text-center">Like us!</p>
										<div id="facebook-cover">
											<div class="logo"><div class="recto"></div></div>
											<div class="top"></div>
											<div class="logo verso"></div>
										</div>
										<div id="shadow"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/fb-shadow.png" alt="shadow"></div>
									</section>
								</a>-->
							</div>
						@elseif( isset($frontpage_bg_info['show_chat_btn']) && $frontpage_bg_info['show_chat_btn'] != 1 )
							<a href="{{$frontpage_bg_info['slug'] or ''}}"><span>{{$frontpage_bg_info['school'] or ''}}</span></a>
						@else
							<a class="find-col-in-network-btn">Find colleges in our network</a>
						@endif
					</div>
				</div>
				@endif

				<!-- get started section - start -->
				@if( isset($webinar_is_live) && $webinar_is_live == true )
				<div id="get_started" class="column medium-10 large-11 frontpage-side-bar-sections" data-bg="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/get_started_bg.jpg" data-is-section="get_started_section" data-webinar="1">
				@else
				<div id="get_started" class="column medium-10 large-11 frontpage-side-bar-sections" data-bg="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/get_started_bg.jpg" data-is-section="get_started_section">
				@endif
					<!-- content ajaxed in on click -->
				</div>
				<!-- get started section - end -->

				<!-- find a college section - start -->
				<div id="find_a_college_side_bar_section" class="column medium-10 large-11 frontpage-side-bar-sections" style="@if($enable_chat && !$webinar_is_live) display: none; @endif" data-is-section="find_a_college_section">
					<!-- content ajaxed in on click -->
				</div>
				<!-- find a college section - end -->

				<!-- chat section - start -->
				<div id="chat_side_bar_section" class="column medium-10 large-11 frontpage-side-bar-sections" style="@if($enable_chat && $signed_in == 1 && !$webinar_is_live) display:block; height:598px; @elseif ($enable_chat && !$webinar_is_live) display:block; height:568px; @endif" data-is-section="member_colleges_section">
					<!-- content ajaxed in on click -->					
				</div>
				<!-- chat section - end -->

				<!-- compare colleges section - start -->
				<div id="compare_side_bar_section" class="column medium-10 large-11 frontpage-side-bar-sections" style="@if($enable_chat && !$webinar_is_live) display: none; @endif" data-is-section="compare_colleges_section">
					<!-- content ajaxed in on click -->
				</div>
				<!-- compare colleges section - end -->

			</div><!-- end of content banner row -->
		</div>
		<!-- /////////////////end of front page main content background image slider div\\\\\\\\\\\\\\\\ -->

		<!-- \\\\\\\\\\\\\\\\\\\start of list of carousels////////////////// -->
		<div id="fp-carousels-container" class="row collapse college-carousels-list">
			<div class="column small-12">
				<!-- carousels injected here -->
			</div>
		</div>
		<!-- ///////////////////end of list of carousels\\\\\\\\\\\\\\\\\\\ -->

	</div>
</div> 

<div class="section-loader text-center">
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

@stop
