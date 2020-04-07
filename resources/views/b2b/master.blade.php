<?php
	//need to add a class if the top white strip banner is on
	//if banner is showing $banner_on is true otherwise , false
	$banner_on = false;
?>

<html>
<head>
	@include('public.headers.header')
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
	<link rel="stylesheet" href="/css/owl2/owl.carousel.min.css">
	<link rel="stylesheet" href="/css/prod_ready/owl.theme.min.css">
<!--   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" /> -->
  <link rel="stylesheet" href="/css/B2B/footer.css" />
  <link rel="stylesheet" href="/css/B2B/home.css" />
  <link rel="stylesheet" href="/css/B2B/about.css" />
  <link rel="stylesheet" href="/css/B2B/slick.css" />
  <link rel="stylesheet" href="/css/B2B/slick-theme.css" />
  <link rel="stylesheet" href="/css/B2B/testimonal.css" />
  <link rel="stylesheet" href="/css/B2B/news.css" />
  <link rel="stylesheet" href="/css/B2B/why-plexuss.css" />
  <link rel="stylesheet" href="/css/B2B/contact-us.css" />
  <link rel="stylesheet" href="/css/B2B/our-solutions.css" />
  <link rel="stylesheet" href="/css/B2B/recruit.css" />
  <link rel="stylesheet" href="/css/B2B/retain.css" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.12.6/css/selectize.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jsSocials/1.5.0/jssocials-theme-flat.min.css">

  <style type="text/css">
  	.row {
	    max-width: 73em;
    	padding-left: 15px;
    	padding-right: 15px;
  	}
  </style>
</head>

	<body id="{{$currentPage}}">

		<!--// top navigation and white strip banner -->
			<div data-subpage="<?php echo $b2b_subpage; ?>" class="top-section-cont @if(isset($banner_on) && $banner_on == true) banner-on @endif">
				<div class="b2b-topnav-container @if (isset($b2b_subpage) && $b2b_subpage != '_Home') sticky @endif">
					<div class="row flex">
						<div class="b2b-logo @if(isset($b2b_subpage) && $b2b_subpage == '_Home') visible @else hidden @endif"><a href="/solutions/home"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/premium_page/plexuss-white.png" alt="Plexuss"/></a></div>
						<div class="b2b-logo-P-black @if(isset($b2b_subpage) && $b2b_subpage != '_Home') visible @else hidden @endif"><a href="/solutions/home"><img src="../../../images/plexussLogoLetterBlack.png" alt="Plexuss"/></a></div>
						<ul class="b2b-topnav-menu">
							<li class='about-us-link'>
								<a class="linkname" href="/solutions/about-us">About Us</a>
							</li>

							<li class='why-plexuss-link'>
								<a class="linkname" href="/solutions/why-plexuss">Why Plexuss?</a>
							</li>

							<li class="our-solutions-link">
								<a class="linkname" href="/solutions/our-solutions">Our Solutions</a>
							</li>

							<li class="testimonials-link">
								<a class="linkname" href="/solutions/testimonials">Testimonials</a>
							</li>

							<li class="news-link">
								<a class="linkname" href="/solutions/news">News</a>
							</li>

							<li class="b2btop-link">
								<a class="linkname" href="/solutions/contact-us">Contact Us</a>
							</li>
							<li>
								<a href="/admin-signup"><img src="../../../images/b2b/Partner Login.svg" class="partner-login-icon">Partner Login</a>
							</li>
						</ul>
					</div>
					<span class="target"></span>
				</div>

				<!-- logo -->
		<!-- end top navigation -->

		<!-- mobile menu -->
		<div class="b2b-topnav-container-mobile">
			<div class="hamburger-menu-container">
				<div class="hamburger-menu flex-left">
					<a id="mobile-nav-toggle"><span></span></a>
				</div>

				<div class="b2b-logo-mobile flex-center">
					<a href="/solutions/home">
						<img style="width: 166px" src="../../../images/b2b/Plexuss Logo white.png" alt="Plexuss"/>
					</a>
				</div>
				<div class="flex-right"></div>
			</div>

			<div class="b2b-topnav-mobile-menu-container">
				<ul class="b2b-topnav-mobile-menu">
					<li class='b2btop-link about-us-link'>
						<a class="mobile-link" href="/solutions/about-us">About Us</a>
					</li>

					<li class='b2btop-link why-plexuss-link'>
						<a class="mobile-link" href="/solutions/why-plexuss">Why Plexuss?</a>
					</li>

					<li class="b2btop-link our-solutions-link">
						<a class="mobile-link" href="/solutions/our-solutions">Our Solutions</a>
					</li>

					<li class="b2btop-link testimonials-link">
						<a class="mobile-link" href="/solutions/testimonials">Testimonials</a>
					</li>

					<li class="b2btop-link news-link">
						<a class="mobile-link" href="/solutions/news">News</a>
					</li>

					<li class="b2btop-link">
						<a class="mobile-link" href="/solutions/contact-us">Contact Us</a>
					</li>
					<li class="b2btop-link">
						<a href="/admin-signup" class="linkname">Partner Login</a>
					</li>
				</ul>

				<div class="mobile-menu-footer">
					<div class="row">
						<span class="mobile-follow-us">Follow Us</span>
		      	<a href="#" class="mobile-social-icon"><img src="../../../images/b2b/LinkedIn.svg"></a>
		      	<a href="#" class="mobile-social-icon"><img src="../../../images/b2b/facebook-f.svg"></a>
		      	<a href="#" class="mobile-social-icon"><img src="../../../images/b2b/twitter.svg"></a>
					</div>
					<p class="mobile-copy-right">&copy; <?php echo Date('Y'); ?> PLEXUSS INC. ALL RIGHTS RESERVED</p>
				</div>
			</div>
		</div>
		<!-- end mobile menu -->

	</div>
	<!--// end  top section -->

	<div class="loader-overlay">
		<div class="loader"></div>
	</div>

	<!-- //ajax pages in Content area  -->
	<div class="_b2b-content-wrapper  @if(isset($banner_on) && $banner_on == true) banner-on @endif">
		@yield('b2b-content')
	</div>
	<!--// end content area -->

	</body>
</html>
