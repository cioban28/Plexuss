<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0">
	<title>Plexuss</title>
	<link href="/favicon.png" type="image/png"  rel="icon">
	<link rel="stylesheet" href="/css/normalize.min.css?6"/>
	<link rel="stylesheet" href="/css/foundation.min.css?6" />
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
	<script src="//code.jquery.com/ui/1.10.2/jquery-ui.min.js"></script>
	<link rel="stylesheet" href="//code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.min.css" />
	<!-- <link rel="stylesheet" href="/css/homepage.css" /> -->
	@if(isset($signed_in) && $signed_in == 1)
		@include('private.headers.header')
	@endif
		@include('public.headers.header')
	
</head>

<body class="{{ $currentPage }}">
	<!-- Top Nav Section -->
	@if(isset($signed_in) && $signed_in == 1)
		@include('private.includes.topnav')
	@else

	<div class='show-for-small-only'>
		<nav class="top-bar" data-topbar> 
			<ul class="title-area"> 
				<li class="name"> 
					<h1>
						<a href="/"><img src="/images/plexuss_logo.png" alt='logo'/></a>
					</h1> 
				</li> <!-- Remove the class "menu-icon" to get rid of menu icon. Take out "Menu" to just have icon alone --> 
					<li class="toggle-topbar menu-icon">
					<a href="#"><span>Menu</span></a>
				</li>
			</ul> 
			<section class="top-bar-section"> <!-- Right Nav Section --> 
				<ul class="right"> 
					<li class="has-dropdown"> 
						<a href="#">Company</a> 
						<ul class="dropdown"> 
							<li class='<?php if(Request::path() == 'about'){echo 'active';} ?>' >
								<a href="/about" >About</a>
							</li>
							<li class='<?php if(Request::path() == 'team'){echo 'active';} ?>'>
								<a href="/team">Meet the team</a>
							</li>
							<li class='<?php if(Request::path() == 'contact'){echo 'active';} ?>'>
								<a href="/contact">Contact</a>
							</li> 
						</ul> 
					</li>
					<li class="has-dropdown"> 
						<a href="#">Information</a> 
						<ul class="dropdown"> 
							<li class='<?php if(Request::path() == 'advertising'){echo 'active';} ?>'>
								<a href="/advertising">Advertising</a>
							</li>
							<li class='<?php if(Request::path() == 'college-submission'){echo 'active';} ?>'>
								<a href="/college-submission">College Submission</a>
							</li>
							<li class='<?php if(Request::path() == 'scholarship-submission'){echo 'active';} ?>'>
								<a href="/scholarship-submission">Scholarship Submission</a>
							</li> 
						</ul> 
					</li>
				</ul> <!-- Left Nav Section --> 

			</section> 
		</nav>
	</div>
	<div class="topnav  show-for-medium-up">
		<div class='first'>
			<div class="row ">
				<div class="columns">
					<div class="logo">
						<a href="/"><img src="/images/plexuss_logo.png" alt='logo'></a>
					</div>
				</div>
			</div>
		</div>

		<div class="second">

		</div>
	</div>
	@endif
	<!-- TOP BANNER AND FOOTER-PAGE-NAV MENU -->
	@include('public.footerpages.footerPageNaveMenu')

   @if(isset($thank_you))
		<div class='row'>
			<div class='column small-12'>
				<div class='row'>
					<div class='text-center large-12 column'>
						<h1 class='header1'>We'll get back to you soon.</h1>
					</div>
				</div>
				<div class='row'>
					<div class='column text-center'>
						<img src="/images/ThankYou.jpg" alt='Thank You!'/>
					</div>
				</div>
				<div class='row'>
					<div class='small-12 text-center column'>
						<h2 class='thankyoutext'>Thank you for contacting us.<br/>Someone from Plexuss will contact you shortly</h2>
					</div>
				</div>
			</div>
		</div>
	@else
		<!-- EVERYTHING BETWEEN HEADER AND FOOTER GOES HERE -->
		<div class='row'>
			<div class='small-12 column'>
				@if(isset($help_page) && $help_page == 1)
				<!-- HEADING, GREETING SEARCH BAR -->
				<div class='row'>
					<div class='small-12 column'>
						<div class="help-faq text-center">
							<span>
								Help & FAQ
							</span>
						</div>

						<div id='help-faq-greeting' class='text-center'>
							Hello <span class="txt-cap">{{$username or 'Guest'}}!</span> How can we help you today?
						</div>
						<div class="small-12 collapse pt20" style="margin-left:-10px;">
						  <!--
						  SEARCH INPUT BOX
						  <div class="small-10 medium-9 medium-offset-1 column">
							<input type="text" name="askquestion"  placeholder="Ask a question..." class="radius1 hgt32">
						  </div>				
						  <div class="small-2 medium-2 column cursor no-padding text-left">
								<div class="search-icon "></div>
						  </div> 
						  <div class="clearfix"></div>			  
						  -->
						</div>
					</div>
				</div>
				<!-- FAQ QUICK LINKS GRID -->
				<div class='row'>
					<div class='small-12 column faq-grid'>
						 <!-- Help-Grid heading -->
						 <div class='row'>
							 <div class='small-12 column text-center' id='faq-grid-heading'>
								Maybe these topics will help:
							</div>	
						 </div>
							<!-- Top Help-Grid row -->
						<div class='row'>
							<div class="small-12 medium-12 column">
								<div class='row'>
									<div class="small-12 medium-3 column">
											<div>
												<a href='/help/faq/general'>
													<div class='bck-button button expand'>
														General FAQ
													</div>
												</a>
											</div>
									</div>
									<div class="small-12 medium-3 column">
										<div>
											<a href='/help/faq/scholarship'>
												<div class="bck-button button expand">
													Scholarships FAQ
												</div>
											</a>
										</div>
									</div>
									<div class="small-12 medium-3 column">
										<div>
											<a href='/help/faq/job'>
												<div class="bck-button button expand">
													Jobs FAQ
												</div>
											</a>
										</div>
									</div>
									<div class="small-12 medium-3 column">
										<div>
											<a href='/help/faq/internship'>
												<div class="bck-button button expand">
													Internship FAQ
												</div>
											</a>
										</div>
									</div>
								</div>
							   <div class="clearfix"></div>	 
							</div>	
						</div>
						<!-- Bottom Help-Grid Row -->
						 <div class='row'>
							 <div class="small-12 medium-12 column">
								<div class='row'>
									<!--Offset to center -->
									<div class="small-12 medium-3 medium-offset-3 column">
										<div>
											<a href='/help'>
												<div class='bck-button button expand'>
													Getting Started
												</div>
											</a>
										</div>
									</div>
									<div class="small-12 medium-3 column end">
										<div>
											<a href='/help/helpful_videos'>
												<div class="bck-button button expand">
													Helpful Videos
												</div>
											</a>
										</div>
									</div>
								<!-- Buttons hidden for now
								<div class="small-12 medium-3 column">
									<div>
										<a href='#'>
											<div class="bck-button button expand" disabled>
												Downloads & Docs
											</div>
										</a>
									</div>
								</div>
								<div class="small-12 medium-3 column">
									<div>
										<a href='#'>
											<div class="bck-button button expand" disabled>
												Advertise on Plexuss
											</div>
										</a>
									</div>
								</div>
								-->
								</div>
							</div>  
						</div>
					</div>
				</div>
				@endif
				<!-- CONTENT -->
				<div class='row'>
					<div class='small-12 column text-center' id='help-heading'>
						@yield('help_heading')
					</div>
				</div>
				<div class='row'>
						@yield('content')
				</div>
			</div>
		</div>
	@endif




	@if(isset($signed_in) && $signed_in == 1)
		@include('private.footers.footer')
	@endif
		@include('public.includes.footer')

	<script src="/js/foundation/foundation.js?7"></script>
	<script src="/js/vendor/modernizr.js?7"></script>
	<script src="/js/foundation/foundation.topbar.js?7"></script>
	<script src="/js/foundation/foundation.equalizer.js?7"></script>
	<script src="/js/foundation/foundation.reveal.js?7"></script>
	<script src="/js/help.js?7"></script>
	<script>
	$(document).ready(function() {
		$(document).foundation();
		$(document).foundation('equalizer');
		$(document).foundation({
			reveal : {
				animation_speed: 400,
				close_on_background_click: false
			}
		});
	});
		
	</script>     
</body>
</html>
