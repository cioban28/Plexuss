@extends('public.registration.masterTemplate')


@section('message')
<div class="alert-bar-orange text-center" style="background-color: #FF5C26;">
<div class="circle-white">&excl;</div> Invalid token session. Please try again
</div>
<br />
<br />
@stop


@section('content')

<div class='row'>
	<div class='large-12 column'>
		<h1>Password Reset</h1>
	</div>
</div>
<div class='row'>
	<div class='large-12 column'>
		<p>Enter the email address associated with your Plexuss account. We will send you an email with a link to reset your password.</p>
	</div>
</div>


{{ Form::open(array('action' => 'ResetPasswordController@postRemind', 'data-abide', 'id'=>'form')) }}

{{ csrf_field() }}
<div class='row'>
		<div class='large-12 column'>
			{{ Form::text('email', null, array('id' => 'email', 'placeholder'=>'Email Address', 'required', 'pattern'=>'email')) }}
			<small class="error">*Please enter an Email Address.</small>
		</div>
	</div>

<div class='large-12 column'>
		{{ Form::submit('Try Again', array('class'=>'signupButton'))}}
</div>

{{ Form::close() }}


<div class='row forgottxt'>
		<div class='large-12 column text-center'>
				<a href="/signup?utm_source=SEO&utm_medium={{$currentPage or ''}}">Don’t  have an account yet?</a>
		</div>
</div>

@stop