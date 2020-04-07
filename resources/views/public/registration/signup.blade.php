
<?php 

	// dd($data);
?>


@extends('public.registration.masterTemplate')

@section('content')

	<div class='row'>
		<div class='large-12 column'>
			<h1>Sign Up for Free & Get Started</h1>
		</div>
	</div>
	<!--form id="form" data-abide-->
	{{ Form::open(array('url' => 'signup?utm_source=SEO&utm_medium=signupPage', 'data-abide', 'id'=>'form')) }} 
	{{Form::hidden('from_intl_students', '', array('id' => 'from_intl_students'))}}	
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

				<div class='column small-3 text-rigth'>{{ Form::text('month', null, array('id' => 'bday_month', 'placeholder' => 'M', 'maxlength' => '2', 'data-abide-validator'=>'monthChecker', 'required') ) }}</div>
				<div class='column small-1' style='font-size: 20px;'>/</div>
				<div class='column small-3'>{{ Form::text('day', null, array('id' => 'bday_day', 'placeholder' => 'D' , 'maxlength' => '2', 'data-abide-validator'=>'dayChecker', 'required')) }}</div>
				<div class='column small-1' style='font-size: 20px;'>/</div>
				<div class='column small-3 text-left end'>{{ Form::text('year', null, array('id' => 'bday_yr', 'placeholder' => 'Year', 'maxlength' => '4' ,'data-abide-validator'=>'yearChecker', 'required')) }}</div>



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

	<div class='row'>
		@if(isset($is_gdpr) && $is_gdpr == true)
			<div class='large-12 column optinmessage'>
				{{Form::checkbox('optin', 'checked' , false, array('class'=>'optinbox', 'id' => 'optin', 'required'))}}
				I agree to the Plexuss <a href='/terms-of-service' target='_blank'>terms of service</a> &amp; <a target='_blank' href='/privacy-policy'>privacy policy</a><br /><br />

				{{Form::checkbox('optin2', 'checked' , false, array('class'=>'optinbox', 'id' => 'optin2', 'required'))}}
				
				I understand that I will receive communication from Plexuss and its partner institutions and I will receive such communication until I modify my preferences.

				<small class="error">*You need to agree to both terms to join.</small>
			</div>
		@else
			<div class='large-12 column optinmessage'>
				{{Form::checkbox('optin', 'checked' , true, array('class'=>'optinbox','required'))}}
				I agree to the Plexuss <a href='/terms-of-service' target='_blank'>terms of service</a> &amp; <a target='_blank' href='/privacy-policy'>privacy policy</a>
				<small class="error">*You need to agree to the terms to join.</small>
			</div>
		@endif
		
	</div>
	<div class='row'>
		<div class='large-12 column'>
			<button class='signupButton'>
				<div id="loading"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></div>
				Sign up
			</button>
		</div>
	</div>
	<div class='row text-center'>
		<div class='large-5 column'><div class='orLine'></div></div>
		<div class='large-2 column ortxt'>Or Sign up with</div>
		<div class='large-5 column'><div class='orLine'></div></div>
	</div>
	<div class='row'>
		<div class='large-12 column rela'>
			<a href="/facebook" class='signupFB'>
				<img src='/images/social/facebook_white.png' class="facebook-white"/>
				Facebook
			</a>
			<a href="/googleSignin" class='signupGoogle'>
				<img src='/images/social/google-logo.svg' class="google-white"/>
				Google
			</a>
			{{-- <a href="/linkedinSignin" class='signupLinkedIn'>
				<img src='/images/social/LinkedIn-in.svg' class="linkedin-white"/>
				LinkedIn
			</a> --}}
		</div>
	</div>
	<br />
	<div class='row'>
		<div class='large-12 column text-center'>
			@if(isset($redirect))
			<a id="already_have_an_account" class="haveAcct" href="/signin?redirect={{$redirect}}">Already have an account?</a>
			@else
			<a id="already_have_an_account" class="haveAcct" href="/signin">Already have an account?</a>
			@endif
		</div>
	</div>

	<script type="text/javascript">
		var path = window.location.href;

		//if coming from intl page -- user is trying to signup for premium..? (I believe, not my code)
		if( path.indexOf('fromintl=true') > -1 ){
			// document.getElementById('from_intl_students').value = '/premium-plans';
			document.getElementById('from_intl_students').value = '/checkout/premium';

			document.getElementById('already_have_an_account').setAttribute('href', '/signin?fromintl=true');
		}



	</script>
	<!--/form-->
	{{Form::close() }}

	<!-- JQuery, without including whole header -->
	<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

	<!-- Amplitude Analytics snippet -->
	<script src="/js/amplitude.js"></script>
	<!-- end Amplitude -->

	<script type="text/javascript">
		
		//setup before functions
		var typingTimer;                //timer identifier
		var doneTypingInterval = 800;  //time in ms (0.6 second)
		var currentBox = undefined;
		var lastBoxRan = undefined;

		document.getElementById('loading').style.display = "none";
		$('.signupButton').remove('disabled');
		//on keyup, start the countdown
		$('#fname, #lname, #email, #password, #secondpassword, #bday_month, #bday_day, #bday_yr').keyup(function(){
		    clearTimeout(typingTimer);
		    if ($('#fname').val()){
		        currentBox  = $(this).attr('id');
		        typingTimer = setTimeout(doneTyping, doneTypingInterval);
		    }else if ($('#lname').val()){
		        currentBox  = $(this).attr('id');
		        typingTimer = setTimeout(doneTyping, doneTypingInterval);
		    }else if ($('#email').val()){
		        currentBox  = $(this).attr('id');
		        typingTimer = setTimeout(doneTyping, doneTypingInterval);
		    }else if ($('#password').val()){
		        currentBox  = $(this).attr('id');
		        typingTimer = setTimeout(doneTyping, doneTypingInterval);
		    }else if ($('#secondpassword').val()){
		        currentBox  = $(this).attr('id');
		        typingTimer = setTimeout(doneTyping, doneTypingInterval);
		    }else if ($('#bday_month').val()){
		        currentBox  = $(this).attr('id');
		        typingTimer = setTimeout(doneTyping, doneTypingInterval);
		    }else if ($('#bday_day').val()){
		        currentBox  = $(this).attr('id');
		        typingTimer = setTimeout(doneTyping, doneTypingInterval);
		    }else if ($('#bday_yr').val()){
		        currentBox  = $(this).attr('id');
		        typingTimer = setTimeout(doneTyping, doneTypingInterval);
		    }
		});

		//user is "finished typing," do something
		function doneTyping () {
			lastBoxRan = currentBox;
			runAmplitudeForSpecificPages();
		}

		// user clicked on tab.
		$('#fname, #lname, #email, #password, #secondpassword, #bday_month, #bday_day, #bday_yr').keydown(function(evt) {
		    if(evt.key === "Tab") {
		        if (lastBoxRan !== $(this).attr('id')) {
		        	currentBox  = $(this).attr('id');
		        	lastBoxRan = currentBox;
		        	runAmplitudeForSpecificPages();
		        }
		    }
		});

		function runAmplitudeForSpecificPages(){
			amplitude.getInstance().logEvent('field_step0_'+currentBox, {'method':'Profile Step: Fields'});
		}

		var form = $('#form')
		$( '.signupButton' ).on( "click", function() {
			if ($('#fname').val() != '' && $('#lname').val() != '' && $('#email').val() != '' && 
				$('#password').val() != '' && $('#secondpassword').val() != '' && $('#bday_month').val() != '' &&
				$('#bday_day').val() != '' && $('#bday_yr').val() != '' && $('#password').val() == $('#secondpassword').val()) {
				amplitude.getInstance().logEvent('step0_completed', {'method':'Sign Up'});
				document.getElementById('loading').style.display = "block";
				$(this).prop('disabled', true);
				form.submit()
			}
		});
		
		$( '.signupFB' ).on( "click", function() {
			amplitude.getInstance().logEvent('step0_completed', {'method':'Sign Up FB'});
		});
		// 	// var data = $('#form').serialize();
		// }

		// 	$.ajax({
		// 		url: '/signup',
		// 		type: 'POST',
		// 		data: data,
		// 	}).done(function(res){
		// 		// console.log(res);
		// 	});

		// });
	</script>
@stop

