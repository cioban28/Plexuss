@extends('public.registration.masterTemplate')

@section('content')
	<div class='row'>
		<div class='large-12 column'>
			<h1>Welcome Back!</h1>
		</div>
	</div>
	{{ Form::open(array('action' => 'AuthController@postSignin', 'data-abide' , 'id'=>'form')) }}
	{{ csrf_field() }}
	{{Form::hidden('from_intl_students', '', array('id' => 'from_intl_students'))}}	

	<script type="text/javascript">
		var path = window.location.href;

		if( path.indexOf('fromintl=true') > -1 ){
			document.getElementById('from_intl_students').value = '/checkout/premium';
		}
	</script>

	<div class='row'>
		<div class='large-12 column'>
			@if($errors->any())
				<div class="alert alert-danger">
				{!! implode('', $errors->all('<li class="error">:message</li>')) !!}
				</div>
			@endif
		</div>
	</div>
	<div class='row'>
		<div class='large-12 column'>
			{{ Form::text('email', null, array('id' => 'email', 'placeholder'=>'Email Address', 'required', 'pattern'=>'email')) }}
			<small class="error">*Please enter an Email Address.</small>
		</div>
	</div>
	<div class='row'>
		<div class='large-12 column'>
			{{ Form::password('password', array('id' => 'password', 'placeholder' => 'Password' , 'required', 'pattern' => 'passwordpattern')) }}
		</div>
	</div>
	<div class="row">
		<div class='large-12 column'>
			<small class="error passError">*Please enter a valid password with these requirements:<br/>
					8-13 letters and numbers<br/>
					Starts with a letter<br/>
					Contains at least one number<br/>
				
			</small>
		</div>
	</div>
	<div class='row'>
		<div class='large-12 column'>
			<button class='signupButton'>
				<div id="loading"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></div>
				Sign in
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

	<div class='row forgottxt'>
		<div class='large-5 column text-center'><a href="/forgotpassword">Forgot password?</a></div>
		<div class='large-7 column text-center'><a href="/signup?utm_source=SEO&utm_medium={{$currentPage or ''}}">Don’t  have an account yet?</a></div>
	</div>
{{ Form::close() }}
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script type="text/javascript">
		var form = $('#form')
		document.getElementById('loading').style.display = "none";
		$('.signupButton').remove('disabled');
		$( '.signupButton' ).on( "click", function() {
			if ($('#email').val() != '' &&  $('#password').val() != '') {
				document.getElementById('loading').style.display = "block";
				$(this).prop('disabled', true);
				form.submit()
			}
		});
</script>
@stop