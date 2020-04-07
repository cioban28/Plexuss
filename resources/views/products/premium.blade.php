<?php 
	// dd($data);
?>

<!doctype html>
<html> 
	<head>
		@include('private.headers.header')
		<link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,300i" rel="stylesheet">
	</head>

	<body>

		<!-- ////////// logo bar ////////////// -->
		<div class="prem-top-nav">
			<a href="/" class="float-left prem-logo-cont">
				<img class="prem-plexlogo" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/premium_page/plexuss-white.png" alt="logo">
			</a>
			<a href="/signup?redirect=/checkout/premium" class="float-right pupgrade-btn large-only">{{$page_content['upgrade-now'] or 'Upgrade now'}}</a>
		</div>


		<!-- ////// banner /////// -->
		<div class="prem-banner-cont">
			<div class="prem-banner">
				 <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/premium_page/header-image-opt.jpg"/>
			</div>

			<div class="banner-text">
			<div class="banner-head">{{$page_content['banner-head'] or 'Apply to universities through Plexuss'}}</div><br/>
			<div class="banner-sub">{{$page_content['banner-sub'] or 'International Common Application'}}</div>
			<a href="/signup?redirect=/checkout/premium" class="pupgrade-btn mobile-only"> {{$page_content['upgrade-now'] or 'Upgrade now'}}</a>
			</div>
		</div>
		


		<!-- /////////////////// main content /////////////////// -->
		<div class="main-cont-container">
			<div class="prem-why-title">{{$page_content['prem-why-title'] or 'Why Plexuss Premium?'}}</div>
			

			<div class="prem-row free-app-cont">
				<div class="prem-col">
					<div class="prem-title2">{{$page_content['free-app-cont-prem-title2'] or 'Free Applications'}}</div>
					<div class="prem-desc">
						{{$page_content['free-app-cont-prem-desc'] or 'Most application fees range from $50-100 each, but with Plexuss Premium you are 
						given 5 applications to select universities in our network for free, saving you $125+'}}
					</div>
					<a href="/signup?redirect=/checkout/premium" class="float-left pupgrade-btn"> {{$page_content['upgrade-now'] or 'Upgrade now'}}</a>
				</div>
				<div class="prem-col">
					<div class="writing-img"></div>
				</div>
			</div>
			

			<div class="prem-row one-on-one-cont">
				<div class="prem-col"><div class="meeting-img"></div></div>
				<div class="prem-col">
					<div class="prem-title2">{{$page_content['one-on-one-cont-prem-title2'] or '1-on-1 Meeting'}}</div>
					<div class="prem-desc">
						{{$page_content['one-on-one-cont-prem-desc'] or 'One-on-One support with you over the next few months as you prepare for college, including help filling out your applications and understanding what documents you will need to include. These services are often provided by agents at a cost of $500+, but with Plexuss Premium you get the same support plus the free applications and essays for $99 total.'}}
					</div>
					<a href="/signup?redirect=/checkout/premium" class="float-left pupgrade-btn"> {{$page_content['upgrade-now'] or 'Upgrade now'}}</a>
				</div>
			</div>
			

			<div class="prem-row essay-cont">
				<div class="prem-col">
					<div class="prem-title2">{{$page_content['essay-cont-prem-title2'] or 'Review 20 Essays'}}</div>
					<div class="prem-desc">
						{{$page_content['essay-cont-prem-desc'] or '<p>Read college admissions essays from students who got accepted to universities like Harvard, Stanford, Princeton, Yale University, and more. Many high ranking universities require an admissions essay, which can be the determining factor for whether or not you are admitted to the university. </p>

						<p>These essays provide you with tips on essay theme, format, and vocabulary for when you write your own admissions essay.</p>'}}
						
					</div>
					<a href="/signup?redirect=/checkout/premium" class="float-left pupgrade-btn"> {{$page_content['upgrade-now'] or 'Upgrade now'}}</a>
				</div>
				<div class="prem-col"><div class="essay-img"></div></div>
			</div>
			



			<!--///// student testimonials /////-->
			<div class="prem-row testimonials">
				<div class="prem-why-title">{{$page_content['testimonials-prem-why-title'] or 'Don’t take our word for it...'}} </div>
				<div class="prem-why-sub">{{$page_content['testimonials-prem-why-sub'] or 'See what students have to say'}}</div>
				<div class="narrow-nested-row">
					
					
					<div class="prem-col">
						<div class="round-img-cont">
							<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/premium_page/alve.png" >
						</div>
						<div class="prem-name">{{$page_content['prem-col-prem-name1'] or 'Alve Chowdhury'}}</div>
						<div class="from-country">{{$page_content['prem-col-from-country1'] or 'from Bangladesh'}}</div>
						<div class="prem-desc">{{$page_content['prem-col-prem-desc1'] or '&quot;I found Plexuss while Googling... Once you
												are a member of Plexuss, they consider it
												their responsibility to help you... If you need 
												information about any school anywhere in
												the USA, Plexuss will definitely help you a lot.&quot;'}}
						</div>
						<div class="appliedto">
							{{$page_content['prem-col-appliedto1'] or 'Applied to Northeastern University<br/>
							Texas A&M University<br/>
							and Adelphi University '}}
							
						</div>
					</div>
					

					<div class="prem-col">
						<div class="round-img-cont">
								<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/premium_page/evan.png" >
							</div>
							<div class="prem-name">{{$page_content['prem-col-prem-name2'] or 'Evan Saber'}}</div>
							<div class="from-country">{{$page_content['prem-col-from-country2'] or 'from Kirkuk, Iraq'}}</div>
							<div class="prem-desc">
								{{$page_content['prem-col-prem-desc2'] or '&quot;Plexuss has been a motivational step to work harder to reach my goals. Every time I log into the website I start imagining and asking myself what it would be like to study abroad. It simply has inspired me to work harder, to follow my dreams, and gives me hope that anything is possible!&quot;'}}


							</div>
					</div>


				</div><!-- end nested row -->
			</div><!-- end student testimonials -->
		



			<!--///// college testimonials /////-->
			<div class="prem-row testimonials">
				<div class="prem-why-sub">{{$page_content['testimonials-rem-why-sub'] or 'See what colleges have to say'}}</div>
				<div class="narrow-nested-row">
					
					
					<div class="prem-col">
						<div class="prem-img-cont-college">
							<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/University_of_Illinois_at_Chicago.png" >
						</div>
						<div class="college-name">{{$page_content['testimonials-college-name1'] or 'The University of Illinois<br/> at Chicago'}}</div>
						<div class="prem-desc">
							{{$page_content['testimonials-prem-desc1'] or '&quot;No other student search service on the market offers such a wide diversity of quality prospective students and communication tools.  We rely on Plexuss with our international recruitment efforts.  Specially now that you can apply directly using Plexuss&rsquo; platform &quot;'}}

						</div>
						<div class="quote-name">{{$page_content['testimonials-quote-name1'] or '&ndash;Richard O&rsquo;Rourke'}}</div>
						<div class="college-rep">
							{{$page_content['testimonials-college-rep1'] or 'Associate Director Office of<br />
							Admissions Recruitment &amp; Outreach'}}
							
						</div>
					</div>
					

					<div class="prem-col">
						<div class="prem-img-cont-college">
								<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/Humboldt_State_University.png" >
							</div>
							<div class="college-name">{{$page_content['testimonials-college-name2'] or 'Humbolt State University'}}</div>
							<div class="prem-desc">
							{{$page_content['testimonials-prem-desc2'] or '&quot;I encourage every international student to use Plexuss to connect with Humboldt state University.  Plexuss is a the best resource I have seen in helping students find the right college.  I have great trust in students who are referred to us through Plexuss.  I look forward to see your online applications.&quot;'}}
							</div>
							<div class="quote-name">{{$page_content['testimonials-quote-name2'] or '&ndash;Emily Kirsch'}}</div>
							<div class="college-rep">
							{{$page_content['testimonials-college-rep2'] or 'International Marketing &amp; <br/ >
							Recruitment Coordinator'}}
						</div>
					</div>


				</div><!-- end nested row -->
			</div><!-- end college testimonials -->

			<div class="text-center">
			<a href="/signup?redirect=/checkout/premium" class="pupgrade-btn upgrade-large-bottom"> {{$page_content['upgrade-now'] or 'Upgrade now'}}</a>
			</div>
		</div>


		<!--///////////// footer ////////////////-->
		<div class="prem-footer">

			<div class="footer-center">
				<div class="prem-plex-icon"></div>
				<div>
					&copy;2017 Plexuss.com
				</div>
				<div>
					<a href="/terms-of-service" target="_blank">Terms of Service</a> | 
					<a href="/privacy-policy" target="_blank"> Privacy Policy </a>
				</div>
				<div class="green">
					Connect with us: 
					<a class="social-link prem-ln" href="https://www.linkedin.com/company/plexuss-com" target="_blank"></a>
					<a class="social-link prem-twitter" href="https://twitter.com/plexussupdates" target="_blank"></a>
					<a class="social-link prem-facebook" href="https://www.facebook.com/plexussupdates/" target="_blank"></a>
					<a class="social-link prem-youtube" href="https://www.youtube.com/channel/UCLBI8NqybOCZYmjxq8f6P1Q" target="_blank"></a>

				</div>
				<div>
					Any questions?  Email us at &nbsp;  <span class="green underline"> support@plexuss.com</span>
				</div>
			</div>
		</div>


			@include('private.footers.footer')

	</body>
</html>