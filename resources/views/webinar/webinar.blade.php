@extends('webinar.master')

@section('sidebar')

<!-- I am a sidebar -->
I am a sidebar

@stop
@section('content')


<!-- I am the content -->
	<div class='row collapse' style=" margin-top: 5px; border-radius: 5px; background-color: white;">
		<div class='column small-12 small-centered'>
			<!-- top half of webinar banner -->
			<div class='row' style="margin-top: 20px; margin-bottom: 25px;">
				<div class='column small-0 medium-10' id="bannerHead">
					<div style="color: #000000;font-weight:bold;font-size:20px;padding-top:10px;">
						<span style="color:#EE4035;font-size:bold;font-size:20px;">Free </span>College Webinar Invitation
					</div>
					<h2 style="margin-top:-10px;font-size:33px; font-weight:lighter;">
						The Surprising Benefits of a Faith-Based Education
					</h2>

					<div class="webinar-subtxt1">Narrowing down your college choices is much easier if you know the type of school you want to attend. You may already know of the Ivy-Leagues, 2-year colleges, and technical colleges - but one type of school that you may not have considered, but could be the best choice for you, is a faith-based college.</div>
				</div>

				<div class="column small-12 text-center medium-2">
					<img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/march1banner.png' alt='Date Ribbon' style="padding-top: 10px" />
				</div>
				
			</div>
			<!-- BOTTOM half of webinar banner -->
			<div class="row">
				<div class="small-12" style="padding-left: 1.2em;">
					<div class='row' style='font-size:16px;'>
						<!-- left bottom of banner -->
						<div class='column small-12 medium-6' id="bottomLeftOfBanner">
							<div class='row' style="font-size:14px;font-weight:normal;">
								<div class="column small-12 end">
									<b>Join us for our first webinar of 2017 and discover 6 surprising benefits of a faith-based education!</b><br/><br/>
								</div>
								<div class="column small-12">
									In this session, you will learn what sets faith-based institutions apart from other colleges and universities, and discover how a faith-based education can prepare you even more for your chosen profession and life after graduation!
									<br/><br/>
								</div>
								<div class="column small-12 end">
									<b>Schedule:</b>
									<br />
								</div>
								<ul class='row webinar-listitem'>
									<li class='column small-12'>10 minute Overview of Plexuss Features</li>
									<li class='column small-12'>30 minute presentation by Dr. William Wegert, Dean of International Student Programs at Liberty University, the world’s largest faith-based university</li>
									<li class='column small-12'>15-20 minutes Q&amp;A Session</li>
								</ul>
								<br/>
								<div class="row">
									<div class="column small-12">
										After reserving your spot, you will receive an email confirmation.  On March 1, 2017 you will receive an email invitation to join the webinar.<br/><br/>
									</div>
								</div>
								<div class="row">
									<div class="column small-12">
										Hope you can make it!<br/><br/>
									</div>
								</div>
							</div>
							<div style="font-weight:bold;font-size:14px;margin-bottom:20px;">- The Plexuss Team</div>
						</div>
						<!-- right bottom of banner -->
						<div class='column small-12 medium-6' id="rightForm">
							<div class="row">
								<div class='column small-12'>
									<div class="row">
										<div class="column small-12">
											This free webinar will go for one hour starting
										</div>
									</div>
									
									<div class="row">
										<div class="column small-12 timeTitle" style='font-weight:bold;'>
											@ 4PM (UTC) 
										</div>
									</div>

									<div class="row">
										<div class="column small-12">
											<div id="notRegistered" class="{{$webinar['formNotSubmitted'] or ''}}" >
												<br />
												<br />
												<div class="text-left"><strong>Name:</strong> {{$webinar['fname']}} {{$webinar['lname']}}</div>
												<div class="text-left"><strong>Email:</strong> {{$webinar['email']}}</div><br>
													<div class="button expand radius" onClick='webinarSubmit();' style="background-color:#FF5C26;font-weight:bold;font-size:26px;width: 90%;">
														Reserve Your Spot!
													</div>
												<br />
												<small><a href="/">Back to Home</a></small>
											</div>
											<div id="registered" class="{{$webinar['formSubmitted']  or ''}}">
												<div>Thank you for Registering</div>
												<div>Check your email for your confirmation</div>
												<br/>
												<a href="/">
													<div class="button radius" style="background-color: #FF5C26">Take me home</div>
												</a>
											</div>
										
											
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
			
		</div>
		<div class='column small-12 small-centered presentersLogo'>
			<div class="row">
				<div class="column small-12 text-center medium-6 medium-offset-3 end">
					Presented by Plexuss & Liberty University
				</div>
			</div>
			<div class="row" style="margin-top: 1em;">
				<div class="columns small-12 text-center">
					<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/plex_full_logo.png" alt="logo">
					<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/liberty-logo.jpg" class="webinar-uni-logo" alt="Liberty University logo">
				</div>
			</div>
		</div>
	</div>
@stop