<div class='row'>
	<div class='large-12 column'>
		<h3>Sign up & Get Started</h3>
	</div>
</div>
{{ Form::open(array('data-abide')) }}
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
		{{ Form::text('fname', null, array('id' => 'fname', 'placeholder'=>'First', 'required',  'pattern' => 'name')) }}
		<small class="error">*Please input your first name.</small>
	</div>
	<div class='large-6 column'>
		{{ Form::text('lname', null, array('id' => 'lname', 'required', 'placeholder'=>'Last', 'pattern' => 'name')) }}
		<small class="error">*Please input your last name.</small>
	</div>
</div>
<div class='row'>
	<div class='large-12 column'>
		{{ Form::text('email', null, array('id' => 'email', 'placeholder'=>'Email@address.com', 'required', 'pattern'=>'email')) }}
		<small class="error">*Please enter an Email Address.</small>
	</div>
</div>
<div class="row">
	<div class='large-6 column'>
		{{ Form::password('password', array('id' => 'password',  'placeholder' => 'Password' , 'required', 'pattern' => 'passwordpattern')) }}
		<small class="error passError">
			*Please enter a valid password with these requirements:<br/>
			8-13 letters and numbers<br/>
			Starts with a letter<br/>
			Contains at least one number
		</small>
	</div>
	<div class='large-6 column'>
		{{ Form::password(null, array( 'id' => 'secondpassword', 'placeholder' => 'Confirm Password' , 'required', 'data-equalto'=>'password')) }}
		<small class="error passConError">*Passwords do not match</small>
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
<div class='row'>
	<div class='agency-agreement-checkbox large-12 column'>
		<input id='agency-agreement-check-step-1' class='agreement-checkbox' type='checkbox' required />
		<label for='agency-agreement-check-step-1'>I agree to the Plexuss <a target='_blank' href='/terms-of-service'><u>terms of service</u></a> & <a target='_blank' href='/privacy-policy'><u>privacy policy</u></a></label>
	</div>
</div>
<div class='row'>
	<div class='large-12 column'>
		<button class='agency-signup-button step-1'>Sign up</button>
	</div>
</div>

<div class='row' style='text-align: center'>
    <div class='login-button' onclick='window.location = "/signin?redirect=agency-signup"'>Already have an account? Log in</div>
</div>
{{ Form::close() }}