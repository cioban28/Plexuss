<div class="get-sign-up">
    <div class='row'>
        <div class='large-12 column'>
            <h3>Sign up & Get Started</h3>
        </div>
    </div>
    {{ Form::open(array('data-abide')) }}
    <input type="hidden" value="{{csrf_token()}}" name="_token">
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
                {{ Form::text('fname', null, array('id' => 'fname', 'placeholder'=>'First', 'required',  'pattern' => 'name', 'autocomplete' => 'off')) }}
                <small class="error">*Please input your first name.</small>
            </div>
            <div class='large-6 column'>
                {{ Form::text('lname', null, array('id' => 'lname', 'required', 'placeholder'=>'Last', 'pattern' => 'name', 'autocomplete' => 'off')) }}
                <small class="error">*Please input your last name.</small>
            </div>
        </div>
        <div class='row'>
            <div class='large-12 column'>
                {{ Form::text('email', null, array('id' => 'email', 'placeholder'=>'john.doe@university.edu', 'required', 'pattern'=>'email', 'autocomplete' => 'off')) }}
                <small class="error">*Please enter an Email Address.</small>
            </div>
        </div>
        <div class="row">
            <div class='large-6 column'>
                {{ Form::password('password', array('id' => 'password',  'placeholder' => 'Password' , 'required', 'pattern' => 'passwordpattern', 'autocomplete' => 'off')) }}
                <small class="error passError">
                    *Please enter a valid password with these requirements:<br/>
                    8-13 letters and numbers<br/>
                    Starts with a letter<br/>
                    Contains at least one number
                </small>
            </div>
            <div class='large-6 column'>
                {{ Form::password(null, array( 'id' => 'secondpassword', 'placeholder' => 'Confirm Password' , 'required', 'data-equalto'=>'password', 'autocomplete' => 'off')) }}
                <small class="error passConError">*Passwords do not match</small>
            </div>
        </div>

        <div class='row'>
            <div class='column small-12 medium-12 large-6'>

                <div class='formDateWrapper row collapse text-center'>

                    <div class='column small-3 text-rigth'>{{ Form::text('month', null, array('id'=> 'm','placeholder' => 'M', 'maxlength' => '2', 'data-abide-validator'=>'monthChecker', 'required') ) }}</div>
                    <div class='column small-1' style='font-size: 20px;'>/</div>
                    <div class='column small-3'>{{ Form::text('day', null, array('id'=> 'd','placeholder' => 'D' , 'maxlength' => '2', 'data-abide-validator'=>'dayChecker', 'required')) }}</div>
                    <div class='column small-1' style='font-size: 20px;'>/</div>
                    <div class='column small-3 text-left end'>{{ Form::text('year', null, array('id'=> 'y','placeholder' => 'Year', 'maxlength' => '4' ,'data-abide-validator'=>'yearChecker', 'required')) }}</div>



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
            <div class='admin-agreement-checkbox large-12 column'>
                <input id='admin-agreement-check-step-1' class='agreement-checkbox' type='checkbox' required checked />
                <label for='admin-agreement-check-step-1'>I agree to the Plexuss <a href='javascript:void(0);' class="term-service"><u>terms of service</u></a> & <a href='javascript:void(0);' class="term-service"><u>privacy policy</u></a></label>
            </div>
        </div>
        <div class='row'>
            <div class='large-12 column'>
                <button class='admin-signup-button step-1'>Sign up</button>
            </div>
        </div>
    {{ Form::close() }}
</div>
<div class='admin-signup-terms hidden-div'>
    <div class="dwnl-cont">
        <h3 style="color: #000;">Terms of Service</h3>
        <p>Welcome, and thanks for using Plexuss and/or other Plexuss services and apps! When you use our
            products and services, you’re agreeing to our terms of service, so please take a few minutes to read
            over the User Agreement below. Please note that by using our service, you are entering into a legally binding agreement.
        </p>
        <p>1. Introduction </p>
        <p>1.1 Purpose </p>
        <p>Plexuss‘ mission is to empower students to find and choose the very best college for them. To achieve
            our mission, we make services available through our websites, mobile applications, and developer
            platforms.
        </p>
        <p>1.2 Scope and Intent </p>
        <p>You agree that by registering on Plexuss or by using our websites, including our mobile applications,
            developer platforms, or any content or information provided as part of the Plexuss services, you are
            entering into a legally binding agreement with Plexuss, 231 market place #241, San ramon, CA 94583
            based on the terms of this Plexuss Terms of Service and the Plexuss Privacy Policy, which is here
        </p>
        {{ Form::open(array('data-abide')) }}
        <div class='row'>
            <input type="hidden" name="fname" id="f_name" required>
            <input type="hidden" name="lname" id="l_name" required>
            <input type="hidden" name="email" id="user_email" required>
            <input type="hidden" name="password" id="pwd" required>
            <input type="hidden" name="month" id="month" required>
            <input type="hidden" name="day" id="day" required>
            <input type="hidden" name="year" id="year" required>
            <div class='admin-agreement-checkbox large-12 column'>
                <input id='admin-agreement-check-step-2' class='agreement-checkbox' type='checkbox' required />
                <label for='admin-agreement-check-step-2'><b>I agree to the Plexuss <u>terms of service</u> & <u>privacy policy</u></b></label>
            </div>
        </div>
        <div class='row'>
            <div class='large-12 column'>
                <button class='admin-signup-button step-2'>Next</button>
                <span class='bck step-2'> < back</span>
            </div>
        </div>
        {{ Form::close() }}
    </div>
</div>

