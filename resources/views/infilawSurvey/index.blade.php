@extends('infilawSurvey.master')

@section('content')
<?php
	// dd($data);
?>

	<div class="row collapse survey-container" data-uid="{{$hashed_infilaw_user_id}}" data-step="{{$current_step or 0}}">
		<div class="column small-12 medium-10 large-9 small-centered">

			<div class="school-type-header clearfix">

				@if(isset($session_arr['school_name']) && $session_arr['school_name'] == 'Florida Coastal School of Law')
				<div class="left"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/infilawSurvey/fcsl-logo-transparent.png" alt="Florida Coastal School of Law"></div>
				@elseif(isset($session_arr['school_name']) && $session_arr['school_name'] == 'Charlotte School of Law')
				<div class="left"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/infilawSurvey/csl-logo-transparent.png" alt="Charlotte School of Law"></div>
				@else
				<div class="left">
					<img class="landing" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/infilawSurvey/fcsl-logo-transparent.png" alt="Florida Coastal School of Law">
					<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/infilawSurvey/csl-logo-transparent.png" alt="Charlotte School of Law">
				</div>
				@endif

				@if( isset($current_step) )
					@if( $current_step == 9 || $current_step == 13 )
						<div class="right">Final Step!</div>
					@elseif( $current_step != 10 && $current_step != 13 )
						@if( $is_qualified )
							<div class="right">Question {{$current_step - 10}}/2</div>
						@else
							<div class="right">Question {{$current_step}}/8</div>
						@endif
					@endif
				@endif
			</div>

			{{Form::open(array('url' => '', 'method' => 'PUT', 'data-abide'=>'ajax', 'id'=>'survey-form'));}}

			<!-- landing page -->
			@if( !isset($current_step) )
			<div class="survey-page">
				<div class="head clearfix">
					<div class="left"><h3>Thank you for participating in this survey.</h3></div>
					<div class="left"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/infilawSurvey/amazon-card.jpg" alt="Amazon gift card image"></div>
				</div>

				<br />

				<div class="paras">
					<p>We regularly seek feedback from our constituencies to help enhance programs and academic services.  The experiences and thoughts of our alumni are integral to this process and help us ensure that we are providing a relevant, high quality education for our students.</p>
					<p>It will take less than 5 minutes to complete and you will receive an email with a $50 Amazon gift card code at the conclusion. Here is the first question:</p>
				</div>

				<br />
				<br />
				<br />

				<div class="text-center q1">
					<h3>Which Law School did you graduate from?</h3>
					<br />
					<div class="row">
						<div class="column small-12 medium-8 large-6 small-centered">
							{{Form::select('school_name', array('default'=>'Select one...', 'Charlotte School of Law'=>'Charlotte School of Law', 'Florida Coastal School of Law'=>'Florida Coastal School of Law'), 'default', array('class'=>'survey-field landing goToNext', 'data-formtype'=>'select'))}}
							<small class="error">Cannot have any empty fields</small>
						</div>	
					</div>
				</div>
			</div>			
			@endif

			<!-- q1/6 and final step -->
			@if( isset($current_step) && ($current_step == 1 || $current_step == 9) )
			<div class="survey-page">
				<div class="row">
					<div class="column small-12 medium-8 large-6 small-centered">
						<div>
							{{Form::label('namel','Name:');}}
							{{Form::text('name', (isset($session_arr['name']) ? $session_arr['name'] : null), array('placeholder'=>'First Name and Last Name', 'class'=>'survey-field', 'required', 'pattern'=>'name', 'data-formtype'=>'text'));}}
							<small class="error">Cannot be empty and must have valid characters</small>
						</div>

						<div>
							{{Form::label('emaill','Email:');}}
							{{Form::email('email', (isset($session_arr['email']) ? $session_arr['email'] : null), array('placeholder'=>'email@address.com', 'class'=>'survey-field', 'required', 'pattern'=>'email', 'data-formtype'=>'text'));}}
							<small class="error">Invalid email format</small>
						</div>

						<div>
							{{Form::label('phonel','Phone:');}}
							{{Form::text('phone', (isset($session_arr['phone']) ? $session_arr['phone'] : null), array('placeholder'=>'555-555-555', 'class'=>'survey-field', 'required', 'pattern'=>'phone', 'data-formtype'=>'text'));}}
							<small class="error">Invalid phone number format</small>
						</div>

						<!-- if final step page -->
						@if( $current_step == 9 )
						<div>
							{{Form::label('addressl','Address:');}}
							{{Form::text('address', null, array('placeholder'=>'123 Main St., CA 12345', 'class'=>'survey-field', 'required', 'pattern'=>'address', 'data-formtype'=>'text'));}}
							<small class="error">Invalid address format</small>
						</div>		
						@endif

						<div class="errorMsg">Cannot move on until all fields have been filled out.</div>
					</div>
				</div>
			</div>
			@endif

			<!-- q2/6 -->
			@if( isset($current_step) && $current_step == 2 )
			<div class="survey-page">
				<div class="text-center">
					<h3>How satisfied are you with your experience at the law school?</h3>
				</div>

				<br />

				<div class="row">
					<div class="column small-12 medium-8 large-6 small-centered">

						<ul class="small-block-grid-5 text-center">
							<li>
								{{Form::label('namel','1');}}
								{{Form::radio('experience_satisfy', '1', false, array('class'=>'survey-field goToNext', 'data-formtype'=>'radio'));}}
							</li>
							<li>
								{{Form::label('namel','2');}}
								{{Form::radio('experience_satisfy', '2', false, array('class'=>'survey-field goToNext', 'data-formtype'=>'radio'));}}
							</li>
							<li>
								{{Form::label('namel','3');}}
								{{Form::radio('experience_satisfy', '3', false, array('class'=>'survey-field goToNext', 'data-formtype'=>'radio'));}}
							</li>
							<li>
								{{Form::label('namel','4');}}
								{{Form::radio('experience_satisfy', '4', false, array('class'=>'survey-field goToNext', 'data-formtype'=>'radio'));}}
							</li>
							<li>
								{{Form::label('namel','5');}}
								{{Form::radio('experience_satisfy', '5', false, array('class'=>'survey-field goToNext', 'data-formtype'=>'radio'));}}
							</li>
						</ul>	

					</div>

					<div class="column small-12 medium-8 large-6 small-centered">
						<center>1=Not satisfied; 5=Extremely satisfied</center> 
					</div>
				</div>
			</div>
			@endif

			<!-- q3/6 -->
			@if( isset($current_step) && $current_step == 3 )
			<div class="survey-page">
				<div class="text-center">
					<h3>How satisfied are you with the career placement assistance?</h3>
				</div>

				<br />

				<div class="row">
					<div class="column small-12 medium-8 large-6 small-centered">

						<ul class="small-block-grid-5 text-center">
							<li>
								{{Form::label('namel','1');}}
								{{Form::radio('career_satisfy', '1', false, array('class'=>'survey-field goToNext', 'data-formtype'=>'radio'));}}
							</li>
							<li>
								{{Form::label('namel','2');}}
								{{Form::radio('career_satisfy', '2', false, array('class'=>'survey-field goToNext', 'data-formtype'=>'radio'));}}
							</li>
							<li>
								{{Form::label('namel','3');}}
								{{Form::radio('career_satisfy', '3', false, array('class'=>'survey-field goToNext', 'data-formtype'=>'radio'));}}
							</li>
							<li>
								{{Form::label('namel','4');}}
								{{Form::radio('career_satisfy', '4', false, array('class'=>'survey-field goToNext', 'data-formtype'=>'radio'));}}
							</li>
							<li>
								{{Form::label('namel','5');}}
								{{Form::radio('career_satisfy', '5', false, array('class'=>'survey-field goToNext', 'data-formtype'=>'radio'));}}
							</li>
						</ul>	

					</div>

					<div class="column small-12 medium-8 large-6 small-centered">
						<center>1=Not satisfied; 5=Extremely satisfied</center> 
					</div>
				</div>
			</div>
			@endif

			<!-- q4/8 -->
			@if( isset($current_step) && $current_step == 4 )
			<div class="survey-page">
				<div class="row">
					<div class="column small-12 medium-6">
						<h3>Did you enter the legal field after graduation?</h3>
					</div>
					<div class="column small-12 medium-6">
						{{Form::select('in_legal', array('default'=>'Select one...', 'Yes'=>'Yes', 'No'=>'No'), 'default', array('class'=>'survey-field goToNext', 'data-formtype'=>'select'))}}
					</div>
				</div>
				<div class="errorMsg">Cannot move on until a selection has been made.</div>
			</div>
			@endif

			<!-- q5/8 -->
			@if( isset($current_step) && $current_step == 5 )
			<div class="survey-page">
				<div class="row">
					<div class="column small-11 medium-6 small-centered">
						{{Form::label('namel','Did you pass the bar?');}}
						{{Form::select('pass_bar', array('default'=>'Select one...', 'Yes'=>'Yes', 'No'=>'No'), 'default', array('class'=>'survey-field noGoToNext', 'data-formtype'=>'select'))}}
						<small class="error">Cannot have any empty fields</small>
						<br />
						{{Form::label('namel','What state?');}}
						{{Form::select('bar_state', $session_arr['states'], 'default', array('class'=>'survey-field goToNext', 'data-formtype'=>'select'))}}
						<small class="error">Cannot have any empty fields</small>
					</div>
				</div>
			</div>
			@endif

			<!-- q6/8 -->
			@if( isset($current_step) && $current_step == 6 )
			<div class="survey-page">
				<div class="row">
					<div class="column small-11 medium-6 small-centered">
						{{Form::label('namel','Are you a practicing Attorney?');}}
						{{Form::select('practicing_attorney', array('default'=>'Select one...', 'Yes'=>'Yes', 'No'=>'No'), 'default', array('class'=>'survey-field noGoToNext', 'data-formtype'=>'select'))}}
						<small class="error">Cannot have any empty fields</small>
					</div>
				</div>

				<div class="clearfix">
					<div>
						If so, in what practices do you have experience?
					</div>
					<div class="left">
						@for ($i = 0; $i < ceil(count($ipae) * 1/2); $i++)
						    {{Form::checkbox('ipae', $ipae[$i]['id'], false, array( 'id' => 'ipae_'.$ipae[$i]['id'], 'class' => 'survey-field', 'data-formtype'=>'checkbox' ));}}
						    {{Form::label('ipae_'.$ipae[$i]['id'], $ipae[$i]['name'])}}
						    <br />
						@endfor
					</div>
					<div class="left">
						@for ($i = ceil(count($ipae) * 1/2); $i < count($ipae); $i++)
						    {{Form::checkbox('ipae', $ipae[$i]['id'], false, array( 'id' => 'ipae_'.$ipae[$i]['id'], 'class' => 'survey-field', 'data-formtype'=>'checkbox' ));}}
						    {{Form::label('ipae_'.$ipae[$i]['id'], $ipae[$i]['name'])}}
						    <br />
						@endfor
					</div>
				</div>
			</div>
			@endif

			<!-- q7/8 -->
			@if( isset($current_step) && $current_step == 7 )
			<div class="survey-page">
				<div class="row">
					<div class="column small-12 medium-6">
						<h3>What is your total 2015 income?</h3>
						<div><small>(Please include 2015 total earnings only)</small></div>
					</div>
					<div class="column small-12 medium-6">
						{{Form::select('income', $income_arr, 'default', array('class'=>'survey-field goToNext', 'data-formtype'=>'select'))}}
					</div>
				</div>
				<div class="errorMsg">Please fill out all of the fields.</div>
			</div>
			@endif

			<!-- q8/8 -->
			@if( isset($current_step) && $current_step == 8 )
			<div class="survey-page">
				<div class="row">
					<div class="column small-12 medium-6">
						<h3>Are you interested in networking/reunions with other alums?</h3>
					</div>

					<div class="column small-12 medium-6">
						{{Form::select('networking_alum', array('default'=>'Select one...', 'Yes'=>'Yes', 'No'=>'No'), 'default', array('class'=>'survey-field goToNext', 'data-formtype'=>'select'))}}
					</div>
				</div>
				<div class="errorMsg">Cannot move on until a selection has been made.</div>
			</div>
			@endif

			<!-- amazon code page before and after $1000 survey step 8 or 12 -->
			@if( isset($current_step) && $current_step == 10 )
				@if( isset($is_qualified) && $is_qualified )
					<div class="survey-page" data-qualified="true">
						<div>
							<h3>
								You qualify for $1,000 additional stipend.
							</h3>
						</div>

						<div>
							<p>
								Thank you for completing the survey.  We value your feedback.  You may also qualify to receive an additional $1,000 dollars or more for participating in a program.  Please proceed with completing the following few questions.  The questions take no longer than 5 minutes to complete.
							</p>

							<p>
								What will I have to do? In order to continue to improve the experience of current and future students, we are setting up focus groups to receive the feedback of our alumni.  The focus groups can be completed in-person or virtually.
							</p>

							<p>
								Upon completion, you will receive a call from a representative to provide you with more information.
							</p>
						</div>

						<div>
							<div class="text-center"><b>Are you interested?</b></div>
							<div class="row">
								<div class="column small-11 medium-6 small-centered">
									{{Form::select('interested_in_stipend', array('default'=>'Select one...', 'Yes'=>'Yes', 'No'=>'No'), 'default', array('class'=>'survey-field goToNext', 'data-formtype'=>'select'))}}
								</div>
							</div>
						</div>
					</div>
				@else
					<div class="survey-page completed">
						<div class="text-center">
							<h3>
								Thank you for your participation in this survey! Your feedback is an invaluable part of our commitment to improving the way we serve our students and community.
							</h3>
						</div>

						<br />

						<div class="text-center">
							<h4><b>Your $50 Amazon code will be emailed to you at</b></h4>
							<h4>{{$session_arr['email'] or 'Whoops! please fill out the form again'}}</h4>
						</div>

						<div class="text-center">
							<div></div>
						</div>
					</div>
				@endif
			@endif

			<!-- q2/4 11 
			@if( isset($current_step) && $current_step == 10 )
			<div class="survey-page">
				<div class="row">
					<div class="column small-11 medium-6 small-centered">
						{{Form::label('namel','Are you a licenced Attorney?');}}
						{{Form::select('licensed_attorney', array('default'=>'Select one...', 'Yes'=>'Yes', 'No'=>'No'), 'default', array('class'=>'survey-field'))}}
						<small class="error">Cannot have any empty fields</small>
						<br />
						{{Form::label('namel','What Jurisdiction?');}}
						{{Form::select('jurisdiction_state', $session_arr['states'], 'default', array('class'=>'survey-field goToNext'))}}
						<small class="error">Cannot have any empty fields</small>
					</div>
				</div>
			</div>
			@endif-->

			<!-- q4/4 11 -->
			@if( isset($current_step) && $current_step == 11 )
			<div class="survey-page">
				<div class="row">
					<div class="column small-11 medium-6 small-centered">
						<div>
							{{Form::label('namel','Current employer name?');}}
							{{Form::text('current_employer', null, array('placeholder'=>'Current employer name', 'class'=>'survey-field', 'required', 'pattern'=>'name', 'data-formtype'=>'text'));}}
							<small class="error">Cannot be empty and must have valid characters</small>
						</div>

						<br />

						<div>
							{{Form::label('namel','What is your current title?');}}
							{{Form::text('title', null, array('placeholder'=>'Current job title?', 'class'=>'survey-field', 'required', 'pattern'=>'name', 'data-formtype'=>'text'));}}
							<small class="error">Cannot be empty and must have valid characters - letters and numbers only.</small>
						</div>

						<div class="errorMsg">Please fill out all of the fields.</div>
					</div>
				</div>
			</div>
			@endif

			<!-- q4/4 12 -->
			@if( isset($current_step) && $current_step == 12 )
			<div class="survey-page">
				<div class="row">
					<div class="column small-11 medium-6 small-centered">
						<div>
							{{Form::label('namel','When was your current employment start date?');}}
							{{Form::text('start_date', null, array('placeholder'=>'MM/DD/YYYY', 'class'=>'survey-field', 'required', 'pattern'=>'month_day_year', 'data-formtype'=>'text'));}}
							<small class="error">Cannot be empty and must follow format of MM/DD/YYYY</small>
						</div>

						<br />

						<div>
							{{Form::label('namel','What is your exact 2015 income?');}}
							{{Form::text('exact_income', null, array('placeholder'=>'$XX,XXX', 'class'=>'survey-field', 'required', 'pattern'=>'number', 'data-formtype'=>'text'));}}
							<small class="error">Cannot be empty - use numbers only; no commas, periods, or $ sign needed</small>
						</div>

						<div class="errorMsg">Please fill out all of the fields.</div>
					</div>
				</div>
			</div>
			@endif

			<!-- amazon code page before and after $1000 survey step 10 or 13 -->
			@if( isset($current_step) && $current_step == 13 )
			<div class="survey-page completed">
				<div class="text-center">
					<h3>
						Thank you for your participation in this survey! Your feedback is an invaluable part of our commitment to improving the way we serve our students and community.
					</h3>
				</div>

				@if( (isset($is_qualified) && isset($is_interested)) && ($is_qualified && $is_interested) )
				<br />

				<div class="text-center">
					<h4>
						You will also receive a call from a representative to provide you with more information regarding the program and  the stipend.
					</h4>
				</div>
				@endif

				<br />

				<div class="text-center">
					<h4><b>Your $50 Amazon code will be emailed to you at</b></h4>
					<h4>{{$session_arr['email'] or 'Whoops! please fill out the form again'}}</h4>
				</div>

				<div class="text-center">
					<div></div>
				</div>
			</div>
			@endif

			{{Form::close();}}

			<br />
			<br />

			@if( isset($current_step) && ($current_step != 10 && $current_step != 13) )
			<div class="previous-next-btns-row clearfix">

				@if( isset($current_step) && $current_step > 1 )
				<div class="left">
					@if( isset($prev_step) )
						<a href="/infilaw/survey/step/{{$prev_step}}" class="prevstep">
							<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/infilawSurvey/mobile_menu_arrow.png" alt="arrow" class="arr left-a"> Previous Question
						</a>
					@endif
				</div>	
				@endif

				@if( isset($current_step) && $current_step < 13 )
				<div class="right">
					@if( isset($next_step) )
					<a href="/infilaw/survey/step/{{$next_step}}" class="nextstep">
					@else
					<a href="/infilaw/survey/step/1" class="nextstep">
					@endif
						@if( $current_step == 12 )
							Finish! <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/infilawSurvey/mobile_menu_arrow.png" alt="arrow" class="arr">
						@else
							Next Question <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/infilawSurvey/mobile_menu_arrow.png" alt="arrow" class="arr">
						@endif
					</a>
				</div>	
				@endif
			</div>
			@endif

			<div class="any-questions-call text-center">
				<div>If you have any questions, please give us a call at</div>
				<div><b><a href="tel:1-877-746-3228">1-877-746-3228</a></b></div>
			</div>

			<!--<div class="isurvey-ajax-loader text-center">
				<svg width="70" height="20">
                    <rect width="20" height="20" x="0" y="0" rx="3" ry="3">
                        <animate attributeName="width" values="0;20;20;20;0" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="height" values="0;20;20;20;0" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="x" values="10;0;0;0;10" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="y" values="10;0;0;0;10" dur="1000ms" repeatCount="indefinite"/>
                    </rect>
                    <rect width="20" height="20" x="25" y="0" rx="3" ry="3">
                        <animate attributeName="width" values="0;20;20;20;0" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="height" values="0;20;20;20;0" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="x" values="35;25;25;25;35" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="y" values="10;0;0;0;10" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
                    </rect>
                    <rect width="20" height="20" x="50" y="0" rx="3" ry="3">
                        <animate attributeName="width" values="0;20;20;20;0" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="height" values="0;20;20;20;0" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="x" values="60;50;50;50;60" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
                        <animate attributeName="y" values="10;0;0;0;10" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
                    </rect>
                </svg>
			</div>-->

		</div>
	</div>

	

@stop