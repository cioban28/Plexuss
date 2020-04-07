



<!-- Fullscreen Video player -->
@if ( $currentPage == 'college-home')
	<div id="college-home-ranking-video" class="plexuss-video-popup reveal-modal medium college-home-modal-vid" data-reveal>
		<div class="row">
			<div class="column small-12 small-text-right">
				<a class="close-reveal-modal help_helpful_videos_close_icon">&#215;</a>
			</div>
		</div>
		<div class="flex-video">
			<iframe style='border:none;' width="446" height="251" src="//www.youtube-nocookie.com/embed/1QlApSS_4ZQ?version=3&amp;showinfo=0&amp;controls=1&amp;rel=0" allowfullscreen></iframe>
		</div>
	</div>
@endif

@if ( $currentPage == 'college' || $currentPage == 'ranking' )
	<div id="college-ranking-video" class="plexuss-video-popup reveal-modal medium college-home-modal-vid" data-reveal>
		<div class="row">
			<div class="column small-12 small-text-right">
				<a class="close-reveal-modal help_helpful_videos_close_icon">&#215;</a>
			</div>
		</div>
		<div class="flex-video">
			<iframe width="446" height="251" src="//www.youtube.com/embed/O73eOnoTtPE?version=3&amp;showinfo=0&amp;controls=1&amp;rel=0" frameborder="0" allowfullscreen></iframe>
		</div>
	</div>
	<div id="college-home-ranking-video" class="plexuss-video-popup reveal-modal medium" data-reveal>
		<div class="row">
			<div class="column small-12 small-text-right">
				<a class="close-reveal-modal help_helpful_videos_close_icon">&#215;</a>
			</div>
		</div>
		<div class="flex-video">
			<iframe style='border:none;' width="446" height="251" src="//www.youtube-nocookie.com/embed/1QlApSS_4ZQ?version=3&amp;showinfo=0&amp;controls=1&amp;rel=0" allowfullscreen></iframe>
		</div>
	</div>
@endif


<div id="recruitmeModal" data-options="" class="reveal-modal medium get-recruited-modal-sm" data-reveal style="top: 22px !important;"></div>

<!-- End of Fullscreen Video Player -->

@if ($currentPage == 'home' && isset($showFirstTimeHomepageModal))
	@include('private.includes.firstTimeHomepageModal')
	<!-- Google Code for Plexuss Conversion Page -->
	<script type="text/javascript">
		/* <![CDATA[ */
		var google_conversion_id = 962038753;
		var google_conversion_language = "en";
		var google_conversion_format = "2";
		var google_conversion_color = "ffffff";
		var google_conversion_label = "CPtiCJLdnFcQ4ZfeygM";
		var google_remarketing_only = false;
		/* ]]> */
	</script>
	<!-- End Google Code for Plexuss Conversion Page -->
	<script type="text/javascript" src="//www.googleadservices.com/pagead/conversion.js"></script>
	<noscript>
		<div style="display:inline;">
			<img height="1" width="1" style="border-style:none;" alt="" src="//www.googleadservices.com/pagead/conversion/962038753/?label=CPtiCJLdnFcQ4ZfeygM&amp;guid=ON&amp;script=0"/>
		</div>
	</noscript>
@endif
<?php
// @if ($currentPage == 'profile' && isset($profile_page_lock_modal))
// 	@include('private.includes.profileUnlockModal')
// @endif
?>
<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>

<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
<script src="/js/jquery.ui.touch-punch.min.js?7"></script>
<script src="/js/jquery.knob.js?7"></script>
<script src="/js/prod_ready/foundation/foundation.min.js"></script>
<script src="/js/prod_ready/foundation/foundation.abide.min.js"></script>
<script src="/js/prod_ready/foundation/foundation.reveal.min.js"></script>
<script src="/js/prod_ready/foundation/foundation.topbar.min.js"></script>
<script src="/js/prod_ready/foundation/foundation.tab.min.js"></script>
<script src="/js/prod_ready/foundation/foundation.tooltip.min.js"></script>
<script src="/js/prod_ready/foundation/foundation.equalizer.min.js"></script>
<script src="/js/prod_ready/foundation/foundation.interchange.min.js"></script>
<script src="/js/prod_ready/foundation/foundation.offcanvas.min.js"></script>
<script src="/js/lodash/lodash.js"></script>

<!-- when app is submitted include includes.smart_app_banner blade here -->
	@include('includes.smart_app_banner')
<!-- Lodash -->
<!-- <script src="https://cdn.jsdelivr.net/lodash/4.17.4/lodash.core.js"></script> -->

@if($currentPage == "college")
	<script src="/js/prod_ready/foundation/foundation.equalizer.min.js"></script>
@endif
<script src="/js/topnavsearch.js?7.01"></script>
<script src="/js/masonry/masonry-pkgd.js?7"></script>
<script src="/js/topNavigationScripts.js?8.01"></script>
<script src="/js/fastclick.js?7"></script>
<script src="/js/backToTop.js?7"></script>
<script src="/js/imagesloaded.pkgd.min.js?7"></script>

@if (isset($signed_in) && $signed_in == 1)
	<!--<script src="/js/notification.js?7"></script>-->
@endif

<script src="/js/selectivity-full.min.js?8"></script>

@if ($currentPage == 'home' && isset($showPrepSchoolModal))
	@include('private.includes.prepSchoolModal');
@endif
<!--//////////////////// SOCIAL API \\\\\\\\\\\\\\\\\\\\-->
<!--///// facebook \\\\\-->
<script>
	window.fbAsyncInit = function() {
		FB.init({
			{{-- Automatically use testing appId when on local --}}
			{{ "appId: 858655780878212," /* FB Test App */}}
			xfbml      : true,
			version    : 'v2.1'
		});
	};

	(function(d, s, id){
	 var js, fjs = d.getElementsByTagName(s)[0];
	 if (d.getElementById(id)) {return;}
	 js = d.createElement(s); js.id = id;
	 js.src = "//connect.facebook.net/en_US/sdk.js";
	 fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));
</script>
<!--\\\\\ facebook /////-->
<script src="/js/share.js?8"></script>
<!--\\\\\\\\\\\\\\\\\\\\ SOCIAL API ////////////////////-->
<!-- Set the ajaxtoken -->
<script type="text/javascript">
	if (typeof Plex === 'undefined') var Plex = {};

	var obj = {
		@if ( isset($ajaxtoken) )
			'ajaxtoken': '{{$ajaxtoken}}',
		@endif

		@if ( isset($showFirstTimeHomepageModal) )
			'showFirstTimeHomepageModal' : {{{$showFirstTimeHomepageModal}}},
		@endif

		@if ( isset($showPrepSchoolModal) )
			'showPrepSchoolModal' : {{{$showPrepSchoolModal}}},
		@endif

		@if ( isset($profile_page_lock_modal) )
			'profile_page_lock_modal' : {{{$profile_page_lock_modal}}},
		@endif

		@if ( isset($RecruitCollegeId) )
			'reqruitUrl' : "/ajax/recruiteme/{{{$RecruitCollegeId}}}",
		@endif

		@if ( isset($redirect) )
			'redirect' : "/{{{$redirect}}}",
		@endif

		'modalAvailable': true,
	};

	// Merge Plex and obj
	$.extend(Plex, obj);
</script>

<!-- sign up modal needs this footer -- signup modal is used on college essays pages -->
@if(isset($currentPage) && $currentPage == 'college-essays')
	<script src="/js/admitsee.js?8"></script>
	<script src='/js/newsInfiniteScroll.js'></script>
	<script src="/js/news.js"></script>
@endif

@if(isset($currentPage) && $currentPage == 'scholarships')
	<script src="/js/prod_ready/accounting.min.js"></script>
	<script src="/js/scholarships.js?v=1"></script>
@endif

@if(isset($currentPage) && $currentPage == 'quad-testimonials')
	<script src="/js/news.js"></script>
	<script src='/js/newsInfiniteScroll.js'></script>
@endif

@if ($currentPage == 'home')
	<script src="/js/home.js?8"></script>
	<script src="/js/help.js?8"></script>
	<script src="/js/homeInfiniteScroll.js?8"></script>
	<script src="/js/reactJS/jsx/underscore_react_reactdom.min.js"></script>
	<script src="/js/prod_ready/profile/whatsNextComponents.min.js" defer></script>
@endif

@if($currentPage == 'admin-content-management')
	<script type="text/javascript">
		mixpanel.identify("{{$user_id or -1}}");
		mixpanel.track("{{$currentPage or 'unknown'}}");

		mixpanel.people.set({
		    "$first_name": "{{$fname or 'unknown'}}",
		    "$last_name": "{{$lname or 'unknown'}}",
		    "$email": "{{$email or 'unknown'}}",
		    "$gender": "{{$gender or 'unknown'}}",
		    "$is_org": "{{$is_organization or 0}}",
		    "$org_school_id": "{{$org_school_id or -1}}",
		    "$school_name": "{{$school_name or 'unknown'}}",
		    "$agency_id": "{{$agency_collection->agency_id or -1}}",
		    "$is_agency": "{{$is_agency or 0}}",
		    "$agency_name": "{{$agency_collection->name or 'unknown'}}",
		    "$org_plan_status": "{{$org_plan_status or 'unknown'}}",
		});
	</script>
@endif

@if ($currentPage == 'admin-messages' || $currentPage == 'admin-chat')
	<script src="/js/underscoreJS/underscore_prod.js"></script>
	<script src="/js/messages.js?8"></script>
	<script src="/js/adminChat.js?8"></script>
	<script src="/js/jquery.idle.js?8"></script>
	<script src="/js/commonChatMessage.js?8"></script>
	<script src="/js/editMessageTemplate.js?8"></script>
	<script src="/js/jquery.timeago.js?8"></script>
	<script type="text/javascript">
		mixpanel.identify("{{$user_id or -1}}");
		mixpanel.track("{{$currentPage or 'unknown'}}");
		mixpanel.people.set({
		    "$first_name": "{{$fname or 'unknown'}}",
		    "$last_name": "{{$lname or 'unknown'}}",
		    "$email": "{{$email or 'unknown'}}",
		    "$gender": "{{$gender or 'unknown'}}",
		    "$is_org": "{{$is_organization or 0}}",
		    "$org_school_id": "{{$org_school_id or -1}}",
		    "$school_name": "{{$school_name or 'unknown'}}",
		    "$agency_id": "{{$agency_collection->agency_id or -1}}",
		    "$is_agency": "{{$is_agency or 0}}",
		    "$agency_name": "{{$agency_collection->name or 'unknown'}}",
		    "$org_plan_status": "{{$org_plan_status or 'unknown'}}",
		});
	</script>
	<script src="/js/reactJS/jsx/react.js"></script>
	<script src="/js/reactJS/jsx/react-dom.js"></script>
@endif

@if ($currentPage == 'agency-messages')
	<script src="/js/messages.js?8"></script>
	<script src="/js/agencyChat.js?8"></script>
	<script src="/js/jquery.idle.js?8"></script>
	<script src="/js/commonChatMessage.js?8"></script>
	<script src="/js/jquery.timeago.js?8"></script>
	<script type="text/javascript">
		mixpanel.identify("{{$user_id or -1}}");
		mixpanel.track("{{$currentPage or 'unknown'}}");
		mixpanel.people.set({
		    "$first_name": "{{$fname or 'unknown'}}",
		    "$last_name": "{{$lname or 'unknown'}}",
		    "$email": "{{$email or 'unknown'}}",
		    "$gender": "{{$gender or 'unknown'}}",
		    "$is_org": "{{$is_organization or 0}}",
		    "$org_school_id": "{{$org_school_id or -1}}",
		    "$school_name": "{{$school_name or 'unknown'}}",
		    "$agency_id": "{{$agency_collection->agency_id or -1}}",
		    "$is_agency": "{{$is_agency or 0}}",
		    "$agency_name": "{{$agency_collection->name or 'unknown'}}",
		    "$org_plan_status": "{{$org_plan_status or 'unknown'}}",
		});
	</script>
@endif

@if ($currentPage == 'agency-settings')
	<script src="/js/agencySettings.js?8"></script>
	<script type="text/javascript">
		mixpanel.identify("{{$user_id or -1}}");
		mixpanel.track("{{$currentPage or 'unknown'}}");
		mixpanel.people.set({
		    "$first_name": "{{$fname or 'unknown'}}",
		    "$last_name": "{{$lname or 'unknown'}}",
		    "$email": "{{$email or 'unknown'}}",
		    "$gender": "{{$gender or 'unknown'}}",
		    "$is_org": "{{$is_organization or 0}}",
		    "$org_school_id": "{{$org_school_id or -1}}",
		    "$school_name": "{{$school_name or 'unknown'}}",
		    "$agency_id": "{{$agency_collection->agency_id or -1}}",
		    "$is_agency": "{{$is_agency or 0}}",
		    "$agency_name": "{{$agency_collection->name or 'unknown'}}",
		    "$org_plan_status": "{{$org_plan_status or 'unknown'}}",
		});
	</script>
@endif

@if ($currentPage == 'agency-approval')
	<script src="/js/agencyApproval.js?8"></script>
	<script type="text/javascript">
		mixpanel.identify("{{$user_id or -1}}");
		mixpanel.track("{{$currentPage or 'unknown'}}");
		mixpanel.people.set({
		    "$first_name": "{{$fname or 'unknown'}}",
		    "$last_name": "{{$lname or 'unknown'}}",
		    "$email": "{{$email or 'unknown'}}",
		    "$gender": "{{$gender or 'unknown'}}",
		    "$is_org": "{{$is_organization or 0}}",
		    "$org_school_id": "{{$org_school_id or -1}}",
		    "$school_name": "{{$school_name or 'unknown'}}",
		    "$agency_id": "{{$agency_collection->agency_id or -1}}",
		    "$is_agency": "{{$is_agency or 0}}",
		    "$agency_name": "{{$agency_collection->name or 'unknown'}}",
		    "$org_plan_status": "{{$org_plan_status or 'unknown'}}",
		});
	</script>
@endif

@if ($currentPage == 'agency')
	<script src="/js/agencyApproval.js?8"></script>
	<script type="text/javascript">
		mixpanel.identify("{{$user_id or -1}}");
		mixpanel.track("agency-dashboard");
		mixpanel.people.set({
		    "$first_name": "{{$fname or 'unknown'}}",
		    "$last_name": "{{$lname or 'unknown'}}",
		    "$email": "{{$email or 'unknown'}}",
		    "$gender": "{{$gender or 'unknown'}}",
		    "$is_org": "{{$is_organization or 0}}",
		    "$org_school_id": "{{$org_school_id or -1}}",
		    "$school_name": "{{$school_name or 'unknown'}}",
		    "$agency_id": "{{$agency_collection->agency_id or -1}}",
		    "$is_agency": "{{$is_agency or 0}}",
		    "$agency_name": "{{$agency_collection->name or 'unknown'}}",
		    "$org_plan_status": "{{$org_plan_status or 'unknown'}}",
		});
	</script>
@endif

@if ($currentPage == 'admin-student-search' || $currentPage == 'agency-student-search')
	<script src="/js/underscoreJS/underscore_prod.js"></script>
	<script src="/js/jquery.animateNumber.min.js"></script>
	<script src="/js/advancedStudentSearch.js?8"></script>
	<script type="text/javascript">
		mixpanel.identify("{{$user_id or -1}}");
		mixpanel.track("{{$currentPage or 'unknown'}}");
		mixpanel.people.set({
		    "$first_name": "{{$fname or 'unknown'}}",
		    "$last_name": "{{$lname or 'unknown'}}",
		    "$email": "{{$email or 'unknown'}}",
		    "$gender": "{{$gender or 'unknown'}}",
		    "$is_org": "{{$is_organization or 0}}",
		    "$org_school_id": "{{$org_school_id or -1}}",
		    "$school_name": "{{$school_name or 'unknown'}}",
		    "$agency_id": "{{$agency_collection->agency_id or -1}}",
		    "$is_agency": "{{$is_agency or 0}}",
		    "$agency_name": "{{$agency_collection->name or 'unknown'}}",
		    "$org_plan_status": "{{$org_plan_status or 'unknown'}}",
		});
	</script>
@endif

@if ($currentPage == 'profile')
	<script src="../dropzone/dropzone.js?8"></script>
	<script src="/js/profile.js?8"></script>
	<script src="/js/reactJS/jsx/underscore_react_reactdom.min.js"></script>
	<script src="/js/prod_ready/profile/whatsNextComponents.min.js" async></script>
@endif

@if ($currentPage == 'admin-Products')
	<script src="/js/products.js"></script>
@endif

@if($currentPage=='ranking')
	<script src="/js/listing.js?8" type="text/javascript" charset="utf-8"></script>
	<script src="/js/reactJS/jsx/underscore_react_reactdom.min.js"></script>
	<script src="/js/prod_ready/profile/whatsNextComponents.min.js" async></script>
@endif

@if ($currentPage == 'department')
 		<script src="/js/college.js?8.05"></script>
@endif
@if ($currentPage == 'college')
    <script src="/js/infoboxes.js?8"></script>
    <script src="/js/college.js?8.05"></script>
    <script src="/js/chat.js?8"></script>
	<script src="/js/jquery.idle.js?8"></script>
	<script src="/js/commonChatMessage.js?8"></script>
    <link rel="stylesheet" href="/css/messagesChat.css?6"/>

    <script type='text/javascript'>
		$(document).ready(function(e) {
			$(".more-btn-menu").click(function(){
				$(".dropdown-list").slideToggle("slow");
			})
			$(".dropdown-list li").click(function(){
				$(".dropdown-list").hide();
			})
		});
</script>

	<!-- for each type of page we call in the only functions that are needed. -->
	@if ($pageViewType == 'overview')
		<script type="text/javascript">
			$(function() {
				loadOverview();
			});
		</script>
		<script src="/js/youtubeCarousel.js"></script>
	@endif

	@if ($pageViewType == 'stats')
		<script type="text/javascript">
			$(function() {
				loadStats();
			});
		</script>
	@endif

	@if ($pageViewType == 'ranking')
		<script type="text/javascript">
			$(function() {
				loadRanking();
			});
		</script>
	@endif

	@if ($pageViewType == 'admissions')
		<script type="text/javascript">
			$(function() {
				loadAdmissions();
			});
		</script>
	@endif

	<script src="/js/reactJS/jsx/underscore_react_reactdom.min.js"></script>
	<script src="/js/prod_ready/profile/whatsNextComponents.min.js" async></script>
@endif

@if ($currentPage == 'college-home')
    <script src="/js/infoboxes.js?8"></script>
    <script src="/js/college.js?8.05"></script>
    <script src="/js/search.js?8"></script>
	<script src="/js/kayaksearch.js?v=1.00"></script>
    <script type='text/javascript'>
		$(document).ready(function(e) {
			$(".more-btn-menu").click(function(){
				$(".dropdown-list").slideToggle("slow");
			})
			$(".dropdown-list li").click(function(){
				$(".dropdown-list").hide();
			})
		});

		// added as a final refresh of masonry for safari.
		$(window).load(function() {
			setResizeBox();
		});
	</script>

	<script src="/js/reactJS/jsx/underscore_react_reactdom.min.js"></script>
	<script src="/js/prod_ready/profile/whatsNextComponents.min.js" async></script>
@endif

@if ($currentPage == 'battle')
	<script src="/js/battle.js?8"></script>
	<script src="/js/reactJS/jsx/underscore_react_reactdom.min.js"></script>
	<script src="/js/prod_ready/profile/whatsNextComponents.min.js" async></script>
@endif

@if ($currentPage == 'comparison')
	<script src="/js/comparison.js?8"></script>
	<script src="/js/reactJS/jsx/underscore_react_reactdom.min.js"></script>
	<script src="/js/prod_ready/profile/whatsNextComponents.min.js" async></script>
@endif

@if ($currentPage == 'setting')
	<script type="text/javascript">
		Plex.import_option_was_visited = true;
	</script>
	<script src="/js/foundation/foundation.js"></script>
    <script src="/js/foundation/foundation.interchange.js"></script>
    <script src="/js/foundation/foundation.reveal.js"></script>
    <script src="/js/jqueryDataTablesPlugin/jquery.dataTables.min.js?8"></script>
	<script src="/js/setting.js?8"></script>
	<script src="/js/reactJS/jsx/underscore_react_reactdom.min.js"></script>
	<script src="/js/prod_ready/profile/whatsNextComponents.min.js" async></script>
@endif

@if ($currentPage == 'portal')
	<script src="/js/fullcalender/lib/moment.min.js?8"></script>
	<script src="/js/portal.js?8"></script>
	<script src="/js/zurb_datatable/jquery.dataTables.js?8"></script>
	<script src="/js/zurb_datatable/dataTables.foundation.js?8"></script>
	<!--<script src="/js/messages.js?8"></script>-->
	<script src="/js/jquery.idle.js?8"></script>
	<script src="/js/commonChatMessage.js?8"></script>
	<script src="/js/jquery.timeago.js?8"></script>
	<script src="/js/enjoyhint/enjoyhint.min.js"></script>
	<script src="/js/fileAttachment.js"></script>
	<script src="/js/scholarships.js?v=1"></script>
	<!-- will want to modularize things ans seperate later, for now... -->
	<script src="/js/contact/contact.js?8.0"></script>
	<script src="/js/contact/phonecall.js?8.10"></script>
	<script src="/js/editMessageTemplate.js?8"></script>
	<script src="/js/reactJS/jsx/underscore_react_reactdom.min.js"></script>
	<script src="/js/prod_ready/portal/PortalSideNav_Component.min.js" async></script>
	<script src="/js/prod_ready/profile/whatsNextComponents.min.js" async></script>
	<script src="/js/portal_messages.js" async></script>
@endif

@if ($currentPage == 'admin')
	<!--<script src="/js/inquiries.js?7"></script>
	<script src="/js/jquery.touchSwipe.min.js?8"></script>-->
	<script src="/js/admin.js"></script>
	<script type="text/javascript">
		mixpanel.identify("{{$user_id or -1}}");
		mixpanel.track("admin-dashboard");
		mixpanel.people.set({
		    "$first_name": "{{$fname or 'unknown'}}",
		    "$last_name": "{{$lname or 'unknown'}}",
		    "$email": "{{$email or 'unknown'}}",
		    "$gender": "{{$gender or 'unknown'}}",
		    "$is_org": "{{$is_organization or 0}}",
		    "$org_school_id": "{{$org_school_id or -1}}",
		    "$school_name": "{{$school_name or 'unknown'}}",
		    "$agency_id": "{{$agency_collection->agency_id or -1}}",
		    "$is_agency": "{{$is_agency or 0}}",
		    "$agency_name": "{{$agency_collection->name or 'unknown'}}",
		    "$org_plan_status": "{{$org_plan_status or 'unknown'}}",
		});
	</script>
	<!--<script src="/js/jquery.nouislider.all.min.js?8"></script>-->
@endif

@if ($currentPage == 'agency')
	<script src="/js/agencyDashboard.js"></script>
	<script src="/js/clipboard.min.js"></script>
@endif

@if ($currentPage == 'admin-inquiries' || $currentPage == 'admin-pending' || $currentPage == 'admin-approved' || $currentPage == 'admin-recommendations' || $currentPage == 'admin-removed' || $currentPage == 'admin-rejected' || $currentPage == 'admin-prescreened' ||
	$currentPage == 'admin-verifiedHs' || $currentPage == 'admin-verifiedApp' || $currentPage == 'admin-student-search' || $currentPage == 'agency-student-search' || $currentPage == 'admin-converted')
	<script src="/js/underscoreJS/underscore_prod.js"></script>
	<script src="https://unpkg.com/rxjs@5.2.0/bundles/Rx.min.js"></script>
	<script src="/js/berniecode-animator.js?8"></script>
	<script src="/js/360player/excanvas.js?8"></script>
	<script src="/js/soundmanager/scripts/soundmanager2-nodebug-jsmin.js?8"></script>
	{{-- <script src="/js/360player/360player.js?8"></script> --}}
	<script src="/js/mp3-player-button.js?8"></script>
	{{-- <script src="/js/soundmanager/scripts/initSoundManager.js?8"></script> --}}
	{{-- <script src="/js/flashblock.js?8"></script> --}}
	<script src="/js/audioBar/bar-ui.js?8"></script>
	<script src="/js/moveStudent.js?8"></script>
	<script src="/js/inquiries.js?9.11"></script>
	<script src="/js/majorCrumbs.js"> </script>
	<script src="/js/contact/contact.js?8.0"></script>
	<script src="/js/fileAttachment.js"></script>
	<script src="/js/contact/phonecall.js?8.10"></script>
    <script src="/js/editMessageTemplate.js?8"></script>
    <script src="/js/intlTelInput.js?8"></script>
	<script type="text/javascript">
		mixpanel.identify("{{$user_id or -1}}");
		mixpanel.track("{{$currentPage or 'unknown'}}");
		mixpanel.people.set({
		    "$first_name": "{{$fname or 'unknown'}}",
		    "$last_name": "{{$lname or 'unknown'}}",
		    "$email": "{{$email or 'unknown'}}",
		    "$gender": "{{$gender or 'unknown'}}",
		    "$is_org": "{{$is_organization or 0}}",
		    "$org_school_id": "{{$org_school_id or -1}}",
		    "$school_name": "{{$school_name or 'unknown'}}",
		    "$agency_id": "{{$agency_collection->agency_id or -1}}",
		    "$is_agency": "{{$is_agency or 0}}",
		    "$agency_name": "{{$agency_collection->name or 'unknown'}}",
		    "$org_plan_status": "{{$org_plan_status or 'unknown'}}",
		});
	</script>
	<script src="/js/jquery.nouislider.all.min.js?8"></script>
@endif

@if( $currentPage == 'agency-adv-filtering' )
	<script src="/js/underscoreJS/underscore_prod.js"></script>
	<script src="/js/agencyAdvFilter.js?8"></script>
	<script type="text/javascript">
		mixpanel.identify("{{$user_id or -1}}");
		mixpanel.track("{{$currentPage or 'unknown'}}");
		mixpanel.people.set({
		    "$first_name": "{{$fname or 'unknown'}}",
		    "$last_name": "{{$lname or 'unknown'}}",
		    "$email": "{{$email or 'unknown'}}",
		    "$gender": "{{$gender or 'unknown'}}",
		    "$is_org": "{{$is_organization or 0}}",
		    "$org_school_id": "{{$org_school_id or -1}}",
		    "$school_name": "{{$school_name or 'unknown'}}",
		    "$agency_id": "{{$agency_collection->agency_id or -1}}",
		    "$is_agency": "{{$is_agency or 0}}",
		    "$agency_name": "{{$agency_collection->name or 'unknown'}}",
		    "$org_plan_status": "{{$org_plan_status or 'unknown'}}",
		});
	</script>
@endif

@if( $currentPage == 'admin-adv-filtering'|| $currentPage == 'admin-cms-filtering')
	<script src="/js/underscoreJS/underscore_prod.js"></script>
	@if(isset($adminscholarshipPage) && ($adminscholarshipPage == 'filtering' || $adminscholarshipPage == 'admincms'))
		<script src="/js/scholarshipadminAdvFilter.js?8"></script>
	@else
		<script src="/js/adminAdvFilter.js?8"></script>
	@endif
	<script type="text/javascript">
		mixpanel.identify("{{$user_id or -1}}");
		mixpanel.track("{{$currentPage or 'unknown'}}");
		mixpanel.people.set({
		    "$first_name": "{{$fname or 'unknown'}}",
		    "$last_name": "{{$lname or 'unknown'}}",
		    "$email": "{{$email or 'unknown'}}",
		    "$gender": "{{$gender or 'unknown'}}",
		    "$is_org": "{{$is_organization or 0}}",
		    "$org_school_id": "{{$org_school_id or -1}}",
		    "$school_name": "{{$school_name or 'unknown'}}",
		    "$agency_id": "{{$agency_collection->agency_id or -1}}",
		    "$is_agency": "{{$is_agency or 0}}",
		    "$agency_name": "{{$agency_collection->name or 'unknown'}}",
		    "$org_plan_status": "{{$org_plan_status or 'unknown'}}",
		});
	</script>
@endif

@if ( $currentPage == 'agency-profile')
	<script src="/js/agency/agencyProfile.js"></script>
@endif

@if ( $currentPage == 'agency-search')
	<script src="/js/agency/agencySearch.js"></script>
@endif

@if( $currentPage == 'agency-recommendations' || $currentPage == 'agency-pending'
	|| $currentPage == 'agency-approved' || $currentPage == 'agency-inquiries' || $currentPage == 'agency-removed' || $currentPage == 'agency-rejected' || $currentPage == 'agency-leads' || $currentPage == 'agency-opportunities' || $currentPage == 'agency-applications' )
	<script src="/js/majorCrumbs.js"> </script>
	<script src="/js/underscoreJS/underscore_prod.js"></script>
	<script src="/js/soundmanager/scripts/soundmanager2-nodebug-jsmin.js?8"></script>
	<script src="https://unpkg.com/rxjs@5.2.0/bundles/Rx.min.js"></script>
	<script src="/js/audioBar/bar-ui.js?8"></script>
	<script src="/js/mp3-player-button.js?8"></script>
	<script src="/js/agencyStudentCarousel.js?8"></script>
	<script src="/js/agencyInquiries.js?8"></script>
	<script type="text/javascript">
		mixpanel.identify("{{$user_id or -1}}");
		mixpanel.track("{{$currentPage or 'unknown'}}");
		mixpanel.people.set({
		    "$first_name": "{{$fname or 'unknown'}}",
		    "$last_name": "{{$lname or 'unknown'}}",
		    "$email": "{{$email or 'unknown'}}",
		    "$gender": "{{$gender or 'unknown'}}",
		    "$is_org": "{{$is_organization or 0}}",
		    "$org_school_id": "{{$org_school_id or -1}}",
		    "$school_name": "{{$school_name or 'unknown'}}",
		    "$agency_id": "{{$agency_collection->agency_id or -1}}",
		    "$is_agency": "{{$is_agency or 0}}",
		    "$agency_name": "{{$agency_collection->name or 'unknown'}}",
		    "$org_plan_status": "{{$org_plan_status or 'unknown'}}",
		});
	</script>
@endif

@if($currentPage == 'notifications')
	<script type="text/javascript">
		mixpanel.identify("{{$user_id or -1}}");
		mixpanel.track("View_Notifications");
		mixpanel.people.set({
		    "$first_name": "{{$fname or 'unknown'}}",
		    "$last_name": "{{$lname or 'unknown'}}",
		    "$email": "{{$email or 'unknown'}}",
		    "$gender": "{{$gender or 'unknown'}}",
		    "$is_org": "{{$is_organization or 0}}",
		    "$org_school_id": "{{$org_school_id or -1}}",
		    "$school_name": "{{$school_name or 'unknown'}}",
		    "$agency_id": "{{$agency_collection->agency_id or -1}}",
		    "$is_agency": "{{$is_agency or 0}}",
		    "$agency_name": "{{$agency_collection->name or 'unknown'}}",
		    "$org_plan_status": "{{$org_plan_status or 'unknown'}}",
		});
	</script>
@endif

@if( $currentPage == 'agency-reporting' )
	<script src="/js/agencyReporting.js?8"></script>
@endif

<!-- applied students script for both agency and admin approved pages -->
@if( $currentPage == 'agency-approved' || $currentPage == 'admin-inquiries' || $currentPage == 'admin-approved' || $currentPage == 'admin-pending' || $currentPage == 'agency-pending' ||  $currentPage == 'admin-verifiedApp' || $currentPage == 'admin-verifiedHs' || $currentPage == 'admin-prescreened' || $currentPage == 'admin-removed'  || $currentPage == 'admin-converted')
	<script src="/js/appliedStudents.js?8"></script>
	<script src="/js/underscoreJS/underscore_prod.js"></script>
@endif

@if( $currentPage == 'agency-groupmsg' || $currentPage == 'admin-groupmsg' || $currentPage == 'admin-textmsg')
	<script src="/js/underscoreJS/underscore_prod.js"></script>
	<script src="/js/groupMessaging.js?8"></script>
	<script src="/js/editMessageTemplate.js?8"></script>
@endif

@if ($currentPage == 'news')
	<script src="/js/reactJS/jsx/underscore_react_reactdom.min.js"></script>
	@if ( isset( $NewsId ) )
		<script src='/js/newsInfiniteScroll.js'></script>
	@else
		<script src='/js/newsInfiniteScroll.js'></script>
		<!--<script src="/js/underscoreJS/underscore_prod.js"></script>-->
	@endif
	<!-- Why do we need this migrate file? Our site should be running smooth on new jquery. -->
	<!-- <script src="//code.jquery.com/jquery-migrate-1.2.1.js"></script> -->
	<script src="/js/masonry/jquery.infinitescroll.min.js?8"></script>
	<script src="/js/prod_ready/profile/whatsNextComponents.min.js" async></script>
	<script src="/js/news.js"></script>
@endif
<script src="/js/masonry/jquery.infinitescroll.min.js?8"></script>
@if ($currentPage == 'search' || $currentPage == 'colleges-by-state')
	<script src="/js/search.js"></script>
	<script src="/js/kayaksearch.js?v=1.00"></script>
@endif

@if ($currentPage == 'colleges-by-state')
    <script src="/js/collegeByState.js?v=1.00"></script>
@endif

@if( $currentPage == 'carepackage' )
	<script src="/js/carepackage.js?7"></script>
	<script src="/js/reactJS/jsx/underscore_react_reactdom.min.js"></script>
	<script src="/js/prod_ready/profile/whatsNextComponents.min.js" async></script>
@endif

@if( $currentPage == 'error_404')
	<script src="/js/http_errors.js?8"></script>
	<script src="/js/reactJS/jsx/underscore_react_reactdom.min.js"></script>
	<script src="/js/prod_ready/profile/whatsNextComponents.min.js" async></script>
@endif

@if( $currentPage == 'specialevent-happyBirdthdayToYou' )
	<script src="/js/specialEvents/happyBirthday.js?8"></script>
@endif

<!-- GLOBAL JS FILES THAT NEED TO USE NAMESPACE HERE-->
<script type="text/javascript" src="/daterangepicker/moment.min.js"></script>
<script type="text/javascript" src="/daterangepicker/daterangepicker.js"></script>
<script src="/js/commonfunction.js?8"></script>
<script src="/js/owl.carousel.js?8"></script>
<script src="/js/topAlert.js?7"></script>
<!-- GLOBAL JS FILES THAT NEED TO USE NAMESPACE ENDS-->
<script type="text/javascript">
function ShowRankingFilters(){
	$("#RankingSearchPanel").removeClass('ranking_listing_search');
	$("#RankingSearchResultData").addClass('ranking_result_disp');
}

function HideRankingFilters(){
	$("#RankingSearchPanel").addClass('ranking_listing_search');
	$("#RankingSearchResultData").removeClass('ranking_result_disp');
}
var CurrentCounter=2;
var counterAction = 0;

function ShowOhterSources(){
	if(counterAction == 0){
		CurrentCounter++;
	}else{
		CurrentCounter--;
	}

	for(var i=1;i<=5;i++)
	{
	$("#RankingSource"+i).hide();
	$(".RankingSource"+i).hide();
	}
	var flag=0;
	for(var i=CurrentCounter;i>=1;i--)
	{
		if(flag<=2)
		{
			if(counterAction == 0){
			$("#RankingSource"+i).show('slide', {direction: 'right'}, 1000);
			$(".RankingSource"+i).show('slide', {direction: 'right'}, 1000);
			}
			else
			{
			$("#RankingSource"+i).show('slide', {direction: 'left'}, 1000);
			$(".RankingSource"+i).show('slide', {direction: 'left'}, 1000);
			}
		flag++;
		}else{
		break;
		}
	}
	if(CurrentCounter==5)
	{
		counterAction = 1;
		$('#showhiderightdata').attr('src','/images/ranking/arrow-left.png');

	}
	if(CurrentCounter==3)
	{
		counterAction = 0;
		$('#showhiderightdata').attr('src','/images/ranking/arrow-right.png');

	}
}

var mCurrentCounter=0;
var mcounterAction = 0;
function mShowOhterSources(){
	if(mcounterAction == 0){
		mCurrentCounter++;
	}else{
		mCurrentCounter--;
	}

	for(var i=1;i<=5;i++)
	{
	$("#mRankingSource"+i).hide();
	$(".mRankingSource"+i).hide();
	}
	var flag=0;
	for(var i=mCurrentCounter;i>=1;i--)
	{
		if(flag<=0)
		{

		$("#mRankingSource"+i).show();
		$(".mRankingSource"+i).show();

		flag++;
		}else{
		break;
		}
	}
	if(mCurrentCounter==5)
	{
		mcounterAction = 1;
		$('#mshowhiderightdata').attr('src','/images/ranking/arrow-left.png');

	}
	if(mCurrentCounter==1)
	{
		mcounterAction = 0;
		$('#mshowhiderightdata').attr('src','/images/ranking/arrow-right.png');

	}
}
//new block created to clean up the india mess

$( document ).ready(function() {
	//Function to get profile indicator data on every page
	getNotifications();

	$("#indicator_noti").knob({
		min : 0,
		max : 100,
		angleOffset : 0,
		angleArc : 360,
		stopper : true,
		readOnly : true,
		cursor : false,
		lineCap : 'butt',
		thickness : '0.35',
		width : 27,
		height : 27,
		displayInput : false,
		displayPrevious : true,
		fgColor : '#93D63B',
		inputColor : '#87CEEB',
		bgColor : '#DDDDDD'
	});

	$("#indicator_noti-small").knob({
		min : 0,
		max : 100,
		angleOffset : 0,
		angleArc : 360,
		stopper : true,
		readOnly : true,
		cursor : false,
		lineCap : 'butt',
		thickness : '0.35',
		width : 27,
		height : 27,
		displayInput : false,
		displayPrevious : true,
		fgColor : '#93D63B',
		inputColor : '#87CEEB',
		bgColor : '#DDDDDD'
	});

	@if ($currentPage == 'ranking' && @$PageAction=="listing")
		ShowOhterSources();
		mShowOhterSources();
		$(".listing-data-owl").owlCarousel({
			navigation : true, // Show next and prev buttons
			slideSpeed : 300,
			paginationSpeed : 400,
			items:2
			// "singleItem:true" is a shortcut for:
			// items : 1,
			// itemsDesktop : false,
			// itemsDesktopSmall : false,
			// itemsTablet: false,
			// itemsMobile : false
		});

		$( "#ranking-search-slider-range" ).slider({
			range: true,
			min: 0,
			max: 100,
			values: [{{$ranking_search_zip_min}},{{$ranking_search_zip_max}}],
			slide: function( event, ui ) {
				$( "#ranking-search-slider-range-label" ).html(ui.values[ 0 ] + " - " + ui.values[ 1 ] );
				$("#ranking_search_zip_min").val(ui.values[0]);$("#ranking_search_zip_max").val(ui.values[1]);
			}
		});

		$( "#ranking-search-slider-range-label" )
		.html($( "#ranking-search-slider-range" )
		.slider( "values", 0 ) + " - " + $( "#ranking-search-slider-range" )
		.slider( "values", 1 ) );

		$( "#ranking-search-tutitionfee-rangeslider" ).slider({
			range: true,
			min: {{$sliderData1->minTuition}},
			max: {{$sliderData1->maxTuition}},
			values: [{{$minTuition}},{{$maxTuition}}],
			slide: function( event, ui ) {
				$( "#ranking-search-tutitionfee-rangeslider-label" ).html( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
				$("#tuition_fee_min").val(ui.values[0]);$("#tuition_fee_max").val(ui.values[1]);
			}
		});

		$( "#ranking-search-tutitionfee-rangeslider-label" )
		.html( "$" + $( "#ranking-search-tutitionfee-rangeslider" )
		.slider( "values", 0 ) + " - $" + $( "#ranking-search-tutitionfee-rangeslider" )
		.slider( "values", 1 ) );

		$( "#ranking-search-undergraduate-rangeslider" ).slider({
			range: true,
			min: {{$sliderData1->minUndergrad}},
			max: {{$sliderData1->maxUndergrad}},
			values: [{{$minUndergrad}},{{$maxUndergrad}}],
			slide: function( event, ui ) {
				$( "#ranking-search-undergraduate-rangeslider-label" ).html( "$" + ui.values[ 0 ] + " - $" + ui.values[ 1 ] );
				$("#undergrade_min").val(ui.values[0]);$("#undergrade_max").val(ui.values[1]);
			}
		});

		$( "#ranking-search-undergraduate-rangeslider-label" )
		.html( "$" + $( "#ranking-search-undergraduate-rangeslider" )
		.slider( "values", 0 ) + " - $" + $( "#ranking-search-undergraduate-rangeslider" )
		.slider( "values", 1 ) );

		$( "#ranking-search-applicantadmitted-rangeslider" ).slider({
			range: true,
			min: {{$sliderData1->minAdmitted}},
			max: {{$sliderData1->maxAdmitted}},
			values: [{{$minAdmitted}},{{$maxAdmitted}}],
			slide: function( event, ui ) {
				$( "#ranking-search-applicantadmitted-rangeslider-label" ).html(ui.values[ 0 ] + "% - " + ui.values[ 1 ]+"%");
				$("#admitted_min").val(ui.values[0]);$("#admitted_max").val(ui.values[1]);
			}
		});

		$( "#ranking-search-applicantadmitted-rangeslider-label" )
		.html($( "#ranking-search-applicantadmitted-rangeslider" )
		.slider( "values", 0 ) + "% - " + $( "#ranking-search-applicantadmitted-rangeslider" )
		.slider( "values", 1 )+"%");

	@endif
});

function RunSlider(Num){
	var OwlSlider=$(".listing-data-owl").data('owlCarousel');
	//alert(OwlSlider);
	//return;
	if(Num==1)
	{
	OwlSlider.prev();
	}
	else
	{
	OwlSlider.next();
	}
}

function show_profile_block(){
	if(document.getElementById('ProfileMenu').style.visibility=="visible"){
		document.getElementById('ProfileMenu').style.visibility="hidden";
	}
	else
	{
		document.getElementById('ProfileMenu').style.visibility="visible";
	}
}

function show_news_sortbymenu() {
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
}

/* Doughnut Box JS snippet */
function avgdonutbox(){
	$(".avg-doughnut-box").knob({
		min : 0,
		max : 100,
		step : 5,
		angleOffset : 235,
		angleArc : 250,
		stopper : true,
		readOnly : true,
		cursor : false,
		lineCap : 'butt',
		thickness : '0.3',
		width : 175,
		displayInput : true,
		displayPrevious : true,
		fgColor : '#000000',
		inputColor : '#FFFFFF',
		font : 'Arial',
		fontWeight : 'bold',
		bgColor : '#FFFFFF',
		'draw' : function () {
			$(this.i).val(this.cv + '%')
		}
	});
}

function graddonutbox(){
	$(".doughnut_grad_box").knob({
		min : 0,
		max : 100,
		angleOffset : 0,
		angleArc : 360,
		stopper : true,
		readOnly : true,
		rotation: 'acw',
		cursor : false,
		lineCap : 'butt',
		thickness : '0.3',
		width : 175,
		displayInput : true,
		displayPrevious : true,
		fgColor : '#05ced3',
		inputColor : '#05ced3',
		font : 'Arial',
		fontWeight : 'bold',
		bgColor : '#b9babb',
		'draw' : function () {
			$(this.i).val(this.cv + '%')
		}
	});
}

function satdonutbox(){
	$(".sat-doughnut-box").knob({
		min : 600,
		max : 2400,
		angleOffset : 235,
		angleArc : 250,
		stopper : true,
		readOnly : true,
		cursor : false,
		lineCap : 'butt',
		thickness : '0.3',
		width : 175,
		displayInput : true,
		displayPrevious : true,
		fgColor : '#000000',
		inputColor : '#FFFFFF',
		font : 'Arial',
		fontWeight : 'bold',
		bgColor : '#FFFFFF',
	});
}

function actdonutbox(){
	$(".act-doughnut-box").knob({
		min : 1,
		max : 36,
		angleOffset : 235,
		angleArc : 250,
		stopper : true,
		readOnly : true,
		cursor : false,
		lineCap : 'butt',
		thickness : '0.3',
		width : 175,
		displayInput : true,
		displayPrevious : true,
		fgColor : '#000000',
		inputColor : '#FFFFFF',
		font : 'Arial',
		fontWeight : 'bold',
		bgColor : '#FFFFFF',
	});
}

function ShowHideFooterLinks(Param){
	if(Param==1){
		$('#right-footer-reveal').slideDown(250, 'easeInOutExpo', function(){
			$('#right-footer-more').fadeOut(250, function(){
				$('#right-footer-less').fadeIn(100);
			});
		});
	}
	else{
		$('#right-footer-reveal').slideUp(250, 'easeInOutExpo', function(){
			$('#right-footer-less').fadeOut(250, function(){
				$('#right-footer-more').fadeIn(100);
			});
		});
	}
}
</script>

<script type='text/javascript'>
	/***********************************************************************
	 *================= INITIALIZE TOPALERTS, IF PRESENT ===================
	 ***********************************************************************
	 * For topAlert feature
	 * This function catches all incoming topAlert objects from the topAlerts
	 * array, and initializes them.
	 */
	$(document).ready(function(){
		@if(isset($alerts) && count($alerts)>0)
			@foreach ($alerts as $alert)
			topAlert({
				@if(isset($alert['color']))
					color: "{{ $alert['color'] }}",
				@endif
				@if(isset($alert['text']))
					text: "{{ $alert['text'] }}",
				@endif
				@if(isset($alert['img']))
					img: "{{ $alert['img'] }}",
				@endif
				@if(isset($alert['bkg']))
					bkg: "{{ $alert['bkg'] }}",
				@endif
				type: "{{ $alert['type'] }}",
				dur: "{{ $alert['dur'] }}",
				msg: "{!! $alert['msg'] !!}"
			});
			@endforeach
			@endif
	});
</script>

<script type="text/javascript">
	//Before recruit me submit, user should save infomation
	function openRegularRecruitmeModal(elem) {
		var fields = elem.closest('.userInfoNotify').find('input:required'), incomplete = false;
		var form = elem.closest('form');
		var formData = form.serialize();
		// console.log(formData);

		$.each(fields, function(){
			if ( !$(this).val() ){
				incomplete = true;
				return false;
			}
		});

		if( !incomplete ){
			$.ajax({
				url: '/ajax/recruitmeinfo',
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
				data: formData,
				type: 'POST',
			}).done(function(data, textStatus, xhr) {

				if(data == 'success') {
					elem.parents('.userInfoNotify').hide("slide", { direction: "left" }, 400);
					$('.model-inner-div.regularRecruitme').delay(50).show('slide', { direction: "left" }, 400, function () {
						$(this).removeClass('hide');
					});
				}
			});
		}
	}

	function submithotModal(schoolId){
		var input = $('#hotModal').serialize();
		$.ajax({
			url: '/ajax/recruiteme/' + schoolId,
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			data: input,
			type: 'POST'
		}).done(function(data, textStatus, xhr) {

			$("html, body").animate({ scrollTop: 0 }, "slow");
			$('#recruitmeModal').html(data);
			$('#recruitmeModal').foundation('reveal', 'open');
		});

	};

	function skipHomepageSchoolInfo(){
		var token = Plex.ajaxtoken;

        console.log('skipped sign up');
		$.ajax({
			url: '/ajax/modalForm/schoolInfoSkip/' + token,
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			type: 'POST'
		}).done(function(data) {
			// $('.start-plexuss-btn').trigger('click');//remove when returning invite feature
		});

		if (Plex.redirect) {
			window.location.replace(Plex.redirect);
		};

	};
</script>
@include('includes.analytics')
{{-- If the $quizInfo is found we know we can load the quizes logic. --}}
@if (isset($quizInfo) && count($quizInfo) )
<script type="text/javascript">
//We need to add this to only show on pages needed.. Tho I think ALL pages will have a quiz.
    $(document).ready(function() {
    	var quizCarousel = $("#quizCarousel");
	    quizCarousel.owlCarousel({
		    navigation : false, // Show next and prev buttons
		    slideSpeed : 250,
		    paginationSpeed : 400,
		    singleItem:true,
		    autoPlay:true,
		    stopOnHover:true,
		    pagination:false
	    });
	    $("#quizControls .left").click(function(){
			 quizCarousel.trigger('owl.prev');
		})
	    $("#quizControls .right").click(function(){
			quizCarousel.trigger('owl.next');
		})
    });
</script>
@endif
<script type='text/javascript'>
	$(document).ready(function(){
		$(document).foundation({
			abide : {
				patterns: {
					passwordpattern: /^(?=[^\d_].*?\d)\w(\w|[!@#$%*]){7,20}/
				}
			}
		});
	});

	$('#form input[name="password"]').on('invalid', function () {
		$('.passError').show();
	}).on('valid', function () {
		$('.passError').hide();
	});
</script>
<script src="/js/remodal.min.js"></script>
