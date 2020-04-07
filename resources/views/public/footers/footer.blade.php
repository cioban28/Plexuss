<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>

<script src="/js/lodash/lodash.js"></script>

<!-- when app is submitted include includes.smart_app_banner blade here -->
@include('includes.smart_app_banner')

@if( isset($currentPage) && $currentPage == 'frontPage' )
	<!-- look at gulp file, task 'concat-js-fp', for all the scripts included in fp_foundation_all.min.js-->
	<script src="/js/prod_ready/foundation/fp_foundation_all.min.js?v=1.00" defer></script>
    <script src="/js/plexussMobileAd.js"></script>

    @if( !isset($is_webinar) )
		<!-- look at gulp file, task 'fp-all-js', for all the scripts included in fp_absolutely_all.min.js -->
		<script src="/js/prod_ready/frontpage/fp_absolutely_all.min.js?v=1.08" defer></script>

	@else
		<script src='/js/underscoreJS/underscore_prod.js'></script>
		<script src='/js/masonry/masonry-pkgd.js'></script>
		<script src='/js/pages.js'></script>
		<script src='/js/backToTop.js'></script>
		<script src='/js/topnavsearch.min.js?v=1.02'></script>
		<script src='/js/owl.carousel.min.js'></script>
		<script src='/js/share.min.js'></script>
		<script src='/js/prod_ready/frontpage/frontpage_section_loader.min.js'></script>
		<script src="/js/homepage.js"></script>
	@endif



@elseif( isset($currentPage)  && $currentPage == 'b2b')

		<script src="/js/prod_ready/foundation/foundation.min.js"></script>
		<script src="/js/prod_ready/foundation/foundation.abide.min.js"></script>
		<script src="/js/foundation/foundation.reveal.js"></script>

		<script src="https://unpkg.com/imagesloaded@4.1/imagesloaded.pkgd.min.js"></script>
		<script src="/js/masonry/masonry-pkgd.js?7"></script>
		<script src="/js/masonry/jquery.infinitescroll.min.js?8"></script>

		<script src='/js/owl.carousel.js'></script>
		<script src="/js/share.js?8"></script>
		<!-- <script src="/js/b2b.js?v=1.00"></script>
		@if(isset($resources_subpage) && $resources_subpage == '_PlexussOnboarding')
			<script src="/js/vendor/modernizr.min.js"></script>
			<script type="text/javascript" src="/daterangepicker/moment.min.js"></script>
			<script src="/js/prod_ready/foundation/foundation.tooltip.min.js"></script>
			<script src="/js/plexussOnboarding.js"></script>
		@endif -->

@elseif( isset($currentPage)  && $currentPage == 'admin-signup')
    <script src="/js/vendor/modernizr.min.js"></script>
    <script type="text/javascript" src="/daterangepicker/moment.min.js"></script>
    <script src="/js/prod_ready/foundation/foundation.min.js"></script>
    <script src="/js/prod_ready/foundation/foundation.abide.min.js"></script>
    <script src="/js/prod_ready/foundation/foundation.tooltip.min.js"></script>
    <script src="/js/foundation/foundation.reveal.js"></script>
    <script src="/js/admin/adminSignUp.js"></script>

@elseif( isset($currentPage)  && $currentPage == 'agency-signup')
    <script src="/js/vendor/modernizr.min.js"></script>
	<script type="text/javascript" src="/daterangepicker/moment.min.js"></script>
	<script src="/js/prod_ready/foundation/foundation.min.js"></script>
	<script src="/js/prod_ready/foundation/foundation.abide.min.js"></script>
    <script src="/js/prod_ready/foundation/foundation.tooltip.min.js"></script>
	<script src="/js/agency/agencySignUp.js"></script>
@else
	<script src="/js/prod_ready/foundation/foundation.min.js"></script>
@endif

@if (isset($signed_in) && $signed_in == 1)
	<script src="/js/tinycon.min.js"></script>
	<script src="/js/notification.js?v=1.0" defer></script>
	<script src="/js/desktopNotifications.js" defer></script>
	<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
	<script>
        var OneSignal = window.OneSignal || [];
        OneSignal.push(function() {
            OneSignal.init({
                appId: "74c58f77-8cd7-47ae-ba9c-bf79dd86b3bc",
            });
        });
	</script>
@endif

@if(isset($currentPage)  && $currentPage != 'b2b' && $currentPage != 'agency-signup' && $currentPage != 'admin-signup')
	<!-- needed for the search in the top navigation -->
	<script src='/js/topnavsearch.min.js?v=1.02'></script>
@endif


<script type="text/javascript">
	$(document).ready(function() {
		$(document).foundation();
	});
</script>

<div id="recruitmeModal" data-options="" class="reveal-modal medium get-recruited-modal-sm" data-reveal></div>

<!--//////////////////// SOCIAL API \\\\\\\\\\\\\\\\\\\\-->
<!--///// facebook \\\\\-->
<script>
	window.fbAsyncInit = function() {
	FB.init({
	  appId      : '858655780878212',
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

<!--\\\\\\\\\\\\\\\\\\\\ SOCIAL API ////////////////////-->

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

<script>
	(function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
	(i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
	m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
	})(window,document,'script','//www.google-analytics.com/analytics.js','ga');
	ga('create', 'UA-26730803-6', 'auto');
	ga('require', 'displayfeatures');
	ga('send', 'pageview');
</script>

<script type="text/javascript">
	setTimeout(function(){var a=document.createElement("script");
	var b=document.getElementsByTagName("script")[0];
	a.src=document.location.protocol+"//dnn506yrbagrg.cloudfront.net/pages/scripts/0026/9382.js?"+Math.floor(new Date().getTime()/3600000);
	a.async=true;a.type="text/javascript";b.parentNode.insertBefore(a,b)}, 1);
</script>

<script type="text/javascript">
	//Before recruit me submit, user should save infomation
	function openRegularRecruitmeModal(elem) {
		var fields = elem.closest('.userInfoNotify').find('input'), incomplete = false;
		var form = elem.closest('form');
		var formData = form.serialize();
		// console.log(formData);

		$.each(fields, function(){
			if ( !$(this).val() && $(this).required ){
				incomplete = true;
				return false;
			}
		});

		if( !incomplete ){
			$.ajax({
                url: '/ajax/recruitmeinfo',
                headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
                data: formData,
                type: 'POST'
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

	function submitRecruitmeModal(schoolId){
		// var input = $('#recruitMeModal').serialize();
		// $.post('/ajax/recruiteme/' + schoolId , input, function(data, textStatus, xhr) {
		// 	// if not on get_started, just redirect to portal
  //           console.log('am on get started: ', window.location.pathname.indexOf('get_started') > -1);
  //           console.log('path: ', window.location.pathname );
  //           if( window.location.pathname.indexOf('get_started') > -1 ) justInquired(data.inquired_list);
  //           else window.location.href = '/portal';

  //           $('#recruitmeModal').foundation('reveal', 'close');
		// });
	};

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



@if(isset($currentPage) && $currentPage == 'unsubscribeThisEmail')
	<script src="/js/unsubscribeEmail.js"></script>
@endif
