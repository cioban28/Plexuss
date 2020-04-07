@extends('public.footerpages.masterTemplate')
@section('content')
<div class='formbox row collapse'>
	<div class="small-12 medium-6 medium-centered column">
    	<div class='row'>
      		<div class="large-12 column">
        		<div class='row'>
          <div class='large-12 column'>
            <h1>Sign up & Get Started</h1>
          </div>
        </div>
        		{{ Form::open(array('action' => 'AuthController@postSignup', 'data-abide' , 'id'=>'form')) }}
        		{{ csrf_field() }}
        		{{Form::hidden('footer_step', '1', array('id' => 'footer_step1'))}} 
        			<div class='row'>
          <div class='large-12 column'> @if($errors->any())
            <div class="alert alert-danger"> {!! implode('', $errors->all('
              <li class="error">:message</li>
              ')) !!} </div>
            @endif </div>
        </div>
        			<div class='row'>
          <div class='large-6 column'> {{ Form::text('fname', null, array('id' => 'fname', 'placeholder'=>'First Name', 'required',  'pattern' => 'name')) }} <small class="error">*Please enter First Name.</small> </div>
          <div class='large-6 column'> {{ Form::text('lname', null, array('id' => 'lname', 'required', 'placeholder'=>'Last Name', 'pattern' => 'name')) }} <small class="error">*Please enter Last Name.</small> </div>
        </div>
        			<div class='row'>
          <div class='large-12 column'> {{ Form::text('email', null, array('id' => 'email', 'placeholder'=>'Email Address', 'required', 'pattern'=>'email')) }} <small class="error">*Please enter an Email Address.</small> </div>
        </div>
        			<div class='row'>
          <div class='large-6 column'> {{ Form::password('password', array('placeholder' => 'Password' , 'required', 'pattern' => 'passwordpattern')) }} </div>
          <div class='large-6 column'> {{ Form::password('cpassword', array('placeholder' => 'Confirm Password' , 'required', 'pattern' => 'passwordpattern')) }} </div>
        </div>
        			<div class="row">
          <div class='large-12 column'> <small class="error passError">*Please enter a valid password with these requirements:<br/>
            8-13 letters and numbers<br/>
            Starts with a letter<br/>
            Contains at least one number<br/>
            </small> </div>
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
            <small class="error datedMonthError">*Please enter a valid Month.</small> <small class="error datedDayError">*Please enter a valid Day.</small> <small class="error datedYearError">*Please enter a valid Year.</small> <small class="error datedUnderAge">Sorry, You must be 13 years or older to sign up.</small> <small class="error">You must be 13 years or older to sign up.</small> </div>
          <div class='column small-12 medium-12 large-6 agenotice'> You must be 13 years or older to sign up. </div>
        </div>
					<div class="clearfix">&nbsp;</div>
        			<div class='row'>
          <div class='large-12 column'> {{ Form::checkbox('terms', 'agreed') }} I agree to the Plexuss <a href="#">terms of service</a> & <a href="#">privacy policy</a> <small class="error">*Please enter an Email Address.</small> </div>
        </div>
        			<div class='row'>
          <div class='large-12 column'> {{ Form::submit('Sign up', array('class'=>'signupButton'))}} </div>
        </div>
					<script type="text/javascript">
					var path = window.location.href;

					if( path.indexOf('fromintl=true') > -1 ){
						document.getElementById('from_intl_students').value = '/checkout/premium';
					}
				</script>
        		{{ Form::close() }} 
			</div>
    	</div>
  	</div>
</div>
@stop 