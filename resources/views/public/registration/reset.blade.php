@extends('public.registration.masterTemplate')

@section('content')
	{{ Form::open(array('action' => 'ResetPasswordController@postReset', 'data-abide', 'id'=>'form')) }}
	{{ csrf_field() }}
	<input type="hidden" name="token" value="{{ $token }}">
	<div class='row'>
		<div class='large-12 column'>
			<h1>Password Reset</h1>
		</div>
	</div>
	<div class='row'>
		<div class='large-12 column'>
			<p>We care about your safety, so your password should contain 8-13 letters and numbers, and must have at least one number.</p>
		</div>
	</div>
	<div class="row">
		<div class='large-12 column'>
			{{ Form::email('email', '', array('placeholder' => 'Email' , 'required', 'id' => 'email', 'pattern'=>'email')) }}
			<small class="error passConError">*Please enter a valid email</small>
		</div>
		<div class='large-6 column'>
			{{ Form::password('password', array('placeholder' => 'Password' , 'required', 'id' => 'password')) }}
		</div>
		<div class='large-6 column'>
			{{ Form::password('password_confirmation', array('placeholder' => 'Confirm Password' , 'required', 'data-equalto'=>'password',)) }}
			<small class="error passConError">*Please re enter a password</small>
		</div>
	</div>
	<div class="row">
		<div class='large-12 column'>
			<small class="error passError">*Please enter a valid password with these requirements:<br/>
				<ul>
					<li>8-13 letters and numbers </li>
					<li>Starts with a letter</li>
					<li>Contains at least one number</li>
				</ul>
			</small>
		</div>
	</div>
	<div class='row'>
		<div class='large-12 column'>
			{{ Form::submit('Reset Password', array('class'=>'signupButton'))}}
		</div>
	</div>
	<div class='row forgottxt'>
		<div class='large-12 column text-center'>
			<a href="/signup?utm_source=SEO&utm_medium={{$currentPage or ''}}">Don’t  have an account yet?</a>
		</div>
	</div>
	{{ Form::close() }}
@stop