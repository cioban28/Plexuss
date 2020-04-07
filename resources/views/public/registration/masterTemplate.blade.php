<!doctype html>
<html class="no-js" lang="en">
	<head>
		@include('public.headers.regHeader')
		@include('includes.facebook_event_tracking')
		@include('includes.quora')
		@if(isset($currentPage) && ($currentPage == "signup" || $currentPage == "signin"))
			@include('includes.hotjar_for_plexuss_domestic')
		@endif
		
		<script type="text/javascript">
			fbq('track', 'loaded-signup-page');
		</script>
		<!-- Global site tag (gtag.js) - Google AdWords: 820637639 -->
		<script async src="https://www.googletagmanager.com/gtag/js?id=AW-820637639"></script>
		<script>
		  window.dataLayer = window.dataLayer || [];
		  function gtag(){dataLayer.push(arguments);}
		  gtag('js', new Date());

		  gtag('config', 'AW-820637639');
		</script>
	</head>
	<body id="reg">
		<div id='topNav'>
			<div class='topBar'>
				@if (!isset($country_based_on_ip) || $country_based_on_ip !== 'US')
					<a href="/"><img class='plex_logo_resize_signup' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/plex_full_logo.png" alt='Plexuss Logo'/></a>
				@else
					<a href="/"><img class='plex_logo_resize_signup' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/plex_full_logo.png" alt='Plexuss Logo'/></a>
				@endif
			</div>
		</div>
		@yield('message')
		<div class='status'>

		</div>
		<div class='formbox row collapse'>
			<div class="small-12 medium-6 medium-centered column">
				<div class='row formheader'>
					<div class='large-12 column'>
						<div class='formshield'></div>
					</div>	
				</div>
				<div class='row formBody'>
					<div class="large-12 column">
						@yield('content')
					</div>
				</div>
			</div>
		</div>
			
		@include('public.footers.regFooter')
	</body>
</html>
