@extends('betapublic.homepage.master')
@section('content')

<div class="sections">
	<div class="slides mobilefullscreen firstslide">
		
		<div class="CRN_logo_txt">College Recruiting Network</div>
		<div class="CRN_row row">
			<div class="CRN_logo_img large-12 columns">
				<img src="images/college_recruiting_network_2.png"/>
			</div>
		</div>
		<div class="row signupbuttonwrapper">
			<div class="small-push-2 small-8 medium-push-3 medium-6 large-push-4 large-4 column">
				<div class="signupbutton">Sign up for beta!</div>

			</div>
		</div>
		<div class="downarrowarea row">
			<div class="small-push-2 small-8 medium-push-4 medium-4 column">
				<img src="images/longdownarrow.png"/>
			</div>
		</div>
	</div>

	<div class='slides secondslide'>
		<h1>How Does It Work?</h1>
		<div class="row stepswrapper">
			<div class="small-12 medium-4 large-4 columns">
				<div class="stepsbox">
					<div class="stepsicon">1</div>
					<h2>SIGN UP</h2>
					<img src="/images/step1icon.png"/>
					<p>Create your academic profile.  This will enable universities to find you based on your academic achievements, extracurricular activities, location and much more.  On average, Plexuss users are recruited by 10 colleges.</p>
				</div>
			</div>

			<div class="small-12 medium-4 large-4 columns">
				<div class="stepsbox">
					<div class="stepsicon">2</div>
					<h2>PICK YOUR COLLEGES</h2>
					<img src="/images/step2icon.png"/>
					<p>Select all colleges you are interested in attending.  Shortly after, you will be contacted by the college representative on our network.</p>
				</div>
			</div>


			<div class="small-12 medium-4 large-4 columns">
				<div class="stepsbox">
					<div class="stepsicon">3</div>
					<h2>GET RECRUITED</h2>
					<img src="/images/step3icon.png"/>
					<p>Use our powerful search and recommendation engine to interact with a variety of colleges. Regardless of high school level, it is never too early or too late to begin using Plexuss. The future is in your hands!</p>
				</div>
			</div>

		</div>
		<div class="stepsbottomarrowbox row">
			<div class="moretoolstxt large-centered large-12 columns">For more information on our tools, see below</div>
		<div class="whitedownarrow"><img src="/images/whitedownarrow.png"></div>
		</div>
		


	</div>
	<div class="slides thirdslide">
		<div class="row" style="height:100%;">
			<div class="formbox medium-push-6 medium-5 large-push-6 large-6 columns">
				<div class="row message">
					<div class="large-12 columns">
						<div class="homeformcenterspacer"></div>

						<h2 class="betaForm">Notify me when it’s ready!</h2>
						<a name="form"></a>
						{{ Form::open(array('url' => 'submitBetaForm')) }}
						<div class="row">
							@if($errors->any())
                				<div data-alert class="alert-box">
                    			{{ implode('', $errors->all('<li class="error">:message</li>')) }}
                				</div>
            				@endif
							<div class="large-12 columns">
								{{Form::text('name', null, $attributes = array('placeholder'=>'Name', 'id'=>'namebox'))}}
								</div>
							</div>
							<div class="row">
								<div class="large-12 columns">
									{{Form::text('email', null, $attributes = array('placeholder'=>'Email Address', 'id'=>'emailbox'))}}
								</div>
							</div>
							<div class="row">
								<div class="large-3 columns">
									{{Form::label('iam', 'I am a')}}
								</div>
								<div class="large-9 columns">
									{{Form::select('type', array('student' => 'Student', 'parent' => 'Parent', 'counselor' => 'Counselor'), 'student', array('class' => 'type-dropdown', 'id'=>'usertypebox'))}}
								</div>
							</div>
							<div class="row">
								<div class="large-12 columns ui-front">
									{{ Form::text('school', null, $attributes = array('placeholder'=>'High School Attending', 'class'=> 'schooltxtbox', 'id'=>'auto') )}}
									{{ Form::text('response', '', array('id' =>'response', 'style'=>'display:none;')) }}
								</div>
							</div>
							<div class="row phone">
								<div class="large-12 columns">
									{{Form::text('phone', null ,$attributes = array('placeholder'=>'Your phone number ###-###-####', 'class'=> 'phonenumber', 'style'=> 'display:none;', 'id'=>'phonebox'))}}
								</div>
							</div>
							<div class="row">
								<div class="large-12 columns">
									{{ Form::submit('Get Notified', array('class' => 'button radius submitbutton'))}}
								</div>
							</div>
							{{ Form::close() }}
							<div class="row">
								<div class="large-12 columns">
									<p class="tos1">
									By pressing the "Get Notified" button, I understand that I may receive an email about educational services from Plexuss.com.
									</p>
									<p class="tos2" style="display:none;">
									By pressing the "Get Notified" button, I understand that I may receive a call and/or email  about educational services at the telephone number(s) previously provided, including a wireless number, using an automated technology.
									</p>
								</div>
							</div>
						</div>
					</div>

				</div>
			</div>
		</div>
	</div>
	<!-- End Top Nav Section -->
	@stop