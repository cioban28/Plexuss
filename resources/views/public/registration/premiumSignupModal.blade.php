

<link rel="stylesheet" href="/css/signupModal.css" />



<div class="signupModal-back">
</div>

<div class="signupModal-wrapper">
	<div class="signupModal-cont">
		
		<div class='row'>
			<div class="formshield"></div>
			<div class='large-12 column'>
				<h1>Sign Up &amp; Get Started</h1>
			</div>
		</div>
		<!-- {{ Form::open(array('url' => 'signup?redirect=/checkout/premium', 'data-abide', 'id'=>'form')) }} -->
		
		<form id="form" action="/signup" method="POST" class="premiumSignupModalForm" data-abide>
 		{{Form::hidden('from_intl_students', '', array('id' => 'from_intl_students'))}}	
 		
 		<?php 
 			$current= 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
 		?>
 		{{Form::hidden("currentPage", $current, array('id' => 'currentPage'))}}	
 		{{Form::hidden("fromUpgradeModal", true , array('id' => 'fromUpgradeModal'))}}	

		<input type="hidden" value="{{$currentPage}}" name="fromPage"/>
		<div class='row'>
			<div class='large-12 column'>
				@if($errors->any())
					<div class="alert alert-danger">
					{!! implode('', $errors->all('<li class="error">:message</li>')) !!}
					</div>
				@endif
			</div>
		</div>
		<div class="row">
			<div class='large-6 column'>
				{{ Form::text('fname', null, array('id' => 'fname', 'placeholder'=>'First Name', 'required',  'pattern' => 'name')) }}
				<small class="error">*Please input your first name.</small>
			</div>
			<div class='large-6 column'>
				{{ Form::text('lname', null, array('id' => 'lname', 'required', 'placeholder'=>'Last Name', 'pattern' => 'name')) }}
				<small class="error">*Please input your last name.</small>
			</div>
		</div>
		<div class='row'>
			<div class='large-12 column'>
				{{ Form::text('email', null, array('id' => 'email', 'placeholder'=>'Email Address', 'required', 'pattern'=>'email')) }}
				<small class="error">*Please enter an Email Address.</small>
			</div>
		</div>
		<div class="row">
			<div class='large-6 column'>
				{{ Form::password('password', array('id' => 'password',  'placeholder' => 'Password' , 'required', 'pattern' => 'passwordpattern')) }}
			</div>
			<div class='large-6 column'>
				{{ Form::password(null, array( 'id' => 'secondpassword', 'placeholder' => 'Confirm Password' , 'required', 'data-equalto'=>'password')) }}
				<small class="error passConError">*Please re enter a password</small>
			</div>
		</div>
		<div class="row">
			<div class='large-12 column'>
				<small class="error passError">*Please enter a valid password with these requirements:<br/>
						8-13 letters and numbers<br/>
						Starts with a letter<br/>
						Contains at least one number
				
				</small>
			</div>
		</div>


		<div class='row'>
			<div class='column small-12 medium-12 large-6'>


				<div class='formDateWrapper row collapse text-center'>

					<div class='column small-3 text-rigth'>{{ Form::text('month', null, array('placeholder' => 'M', 'maxlength' => '2', 'data-abide-validator'=>'monthChecker', 'required') ) }}</div>
					<div class='column small-1' style='font-size: 20px;'>/</div>
					<div class='column small-3'>{{ Form::text('day', null, array('placeholder' => 'D' , 'maxlength' => '2', 'data-abide-validator'=>'dayChecker', 'required')) }}</div>
					<div class='column small-1' style='font-size: 20px;'>/</div>
					<div class='column small-3 text-left end'>{{ Form::text('year', null, array('placeholder' => 'Year', 'maxlength' => '4' ,'data-abide-validator'=>'yearChecker', 'required')) }}</div>



				</div>
				<small class="error datedMonthError">*Please enter a valid Month.</small>
				<small class="error datedDayError">*Please enter a valid Day.</small>
				<small class="error datedYearError">*Please enter a valid Year.</small>
				<small class="error datedUnderAge">Sorry, You must be 13 years or older to sign up.</small>


				<small class="error">You must be 13 years or older to sign up.</small>
			</div>
			<div class='column small-12 medium-12 large-6 agenotice'>
				You must be 13 years or older to sign up.
			</div>
		</div>


		<!-- phone number -->
		@if(isset($countriesAreaCode))
		<div class="row phone-container">
			<div class="column small-12">
				
				<div class="phone-wrapper">
					<input type="text" class="reg-phone" name="phone" placeholder="Enter phone number" required="required" />
					
					
					<div class="country-code-cont">
						
						<span class="current-code" name="countryCode">+ 1</span>
						<input type="hidden" class="hiddenCountryCode" value="+1" name="country_code" />
						<span class="phone-arrow"></span>
					</div>

					<div class="country-code-dropdown">
						@foreach($countriesAreaCode as $code)
							<div class="country-code">+ {{ $code['country_phone_code'] }}  ( {{$code['country_name']}} ) </div>
						@endforeach
					</div>

					<small class="error">Please enter a valid phone number </small>
					<!-- <small class="EEEerror">TWILIO The provided phone number is not valid.</small> -->
				</div>
			</div>
		</div>
		@endif
		<!-- end phone -->



		<div class='row'>
			<div class='large-12 column optinmessage'>
				{{Form::checkbox('optin', 'checked' , true, array('class'=>'optinbox','required'))}}
				I agree to the Plexuss <a href='/terms-of-service' target='_blank'>terms of service</a> &amp; <a target='_blank' href='/privacy-policy'>privacy policy</a>
				<small class="error">*You need to agree to the terms to join.</small>
			</div>
		</div>
		<div class='row'>
			<div class='large-12 column'>
				<!-- {{ Form::submit('Sign up', array('class'=>'signupButton'))}} -->
				<input type="submit" class="signupButton" value="Sign up" />
			</div>
		</div>
		<div class='row text-center'>
			<div class='show-for-large-up large-5 column'><div class='orLine'></div></div>
			<div class='large-2 column ortxt'>Or</div>
			<div class='show-for-large-up large-5 column'><div class='orLine'></div></div>
		</div>
		<div class='row'>
			<div class='large-12 column rela'>
				<a href="/facebook?utm_source=SEO&utm_medium=signupPage" class='signupFB'>Sign up with Facebook</a>
				<div class="fb-logo-container">
					<div id="facebook-logo" class="sm"></div>
				</div>
			</div>
		</div>
		<br />
		<div class='row'>
			<div class='large-12 column text-center'>
				<a id="already_have_an_account" class="haveAcct" href="/signin?redirect=scholarships">Already have an account?</a>
			</div>
		</div>

		<script type="text/javascript">
			var path = window.location.href;

			if( path.indexOf('fromintl=true') > -1 ){
				document.getElementById('from_intl_students').value = '/checkout/premium';
				document.getElementById('already_have_an_account').setAttribute('href', '/signin?fromintl=true');
			}
		</script>
		<!-- </form> -->
		{{ Form::close() }}

	</div><!-- end signupModal-cont -->
</div>



<script src="/js/signUpModal.js" defer></script>