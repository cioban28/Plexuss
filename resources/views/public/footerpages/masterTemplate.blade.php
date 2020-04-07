<!doctype html>
<html class="no-js" lang="en">
	<head>
		@include('public.headers.blackHeader')

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
				<div class="small-12 medium-5 column">
					<a href="/"><img class='plex_logo_resize_signup' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/premium_page/plexuss-white.png" alt='Plexuss Logo'/></a>
				</div>
				<div class="small-12 medium-6 medium-centered column">
					<span class="footer_top_head">
					
					@if(isset($fstep) && $fstep== 1)
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/portal/list.png" alt="Step One" />Step 1
					@else
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/portal/list_checked.png" alt="Step One" />Step 1
					@endif
					</span>
					
					<span class="footer_top_head">
					@if(isset($fstep) && $fstep == 2)
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/portal/hands.png" alt="Step Two" />Step 2
					@elseif(isset($fstep) && $fstep > 2)
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/portal/hands_checked.png" alt="Step Two" />Step 2
					@else
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/portal/hands_gray.png" alt="Step Two" /><label>Step 2</label>
					@endif	
					</span>
					<span class="footer_top_head">
					@if(isset($fstep) && $fstep == 3)
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/portal/user.png" alt="Step Three"/>Step 3
					@elseif(isset($fstep) && $fstep > 3)
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/portal/user_checked.png" alt="Step Three"/>Step 3
					@else
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/portal/user_gray.png" alt="Step Three"/><label>Step 3</label>
					@endif	
					</span>
					<!--<span class="footer_top_head">
					@if(isset($fstep) && $fstep== 4)
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/portal/thankyou.png" alt="Step Four" />Step 4
					@elseif(isset($fstep) && $fstep > 4)
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/portal/thankyou_checked.png" alt="Step Four"/>Step 4
					@else
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/portal/thankyou_gray.png" alt="Step Four"/><label>Step 4</label>
					@endif	
					</span>-->
					
				</div>
			</div>
		</div>
		@yield('message')
		<div class='status'>

		</div>

						@yield('content')

		@include('public.footers.regFooter')
	</body>
</html>
