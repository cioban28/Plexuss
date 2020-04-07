@extends('public.homepage.master')
@section('content')
<?php 
// dd($data); 
?>
{{Form::hidden('email', $email)}}
<div class="row unsubscribe-email">
	<div class="column large-offset-3 large-6">
		<div><img class="plexuss-icon" src="/images/plexuss-p-circle.png"/></div>
		<!-- <span>Plexuss</span> -->
	</div>
	<div class="column medium-8 medium-offset-2 large-6 large-offset-3 end survey-content">
		<div class="row">
			<div class="column large-12 result">
				<div><img class="unsub-icon" src="/images/unsub-icon.png"/></div>
				<span>Unsubscribe</span>
			</div>
			<div class="column large-12 survey-reason">
				<span>If you have a moment, please let us know why you are unsubscribing: </span>
			</div>
			<div class="column large-12">
				<ul>
					<li>
						{{Form::radio('unsubscribe-option', 'I no longer want to receive these emails', null, array('id' => 'unsubscribe-option-1'))}}
						{{Form::label('unsubscribe-option-1', 'I no longer want to receive these emails')}}
					</li>
					<li>
						{{Form::radio('unsubscribe-option', 'I never signed up for this mailing list', null, array('id' => 'unsubscribe-option-2'))}}
						{{Form::label('unsubscribe-option-2', 'I never signed up for this mailing list')}}
					</li>
					<li>
						{{Form::radio('unsubscribe-option', 'The emails are inappropriate', null, array('id' => 'unsubscribe-option-3'))}}
						{{Form::label('unsubscribe-option-3', 'The emails are inappropriate')}}
					</li>
					<li>
						{{Form::radio('unsubscribe-option', 'The emails are spam', null, array('id' => 'unsubscribe-option-4'))}}
						{{Form::label('unsubscribe-option-4', 'The emails are spam')}}
					</li>
					<li>
						{{Form::radio('unsubscribe-option', '', null, array('id' => 'unsubscribe-option-5'))}}
						{{Form::label('unsubscribe-option-5', 'Other (Fill in reason below)')}}
					</li>
				</ul>
			</div>
			<div class="column large-12 unsubscribe-reason">
				{{Form::textarea('reason', null, ['size' => '30x5'])}}
			</div>
			<div class="column large-12 survey-title">
				<span class="small">Are you sure you wish to unsubscribe from all Plexuss emails?</span>
			</div>
			<div class="column small-4 small-offset-2 medium-3 medium-offset-3">
				<input class="button" type="submit" value="Yes" style="background-color: #959595;">
			</div>
			<div class="column small-4 medium-3">
				<input class="button" type="submit" value="No">
			</div>
			<div class="column large-12"> 
				<a href="/" class="return-home">&lt;&lt;&nbsp;return to our website</a>
			</div>
		</div>
	</div>
</div>

<!-- ajax loader -->
<div class="text-center unsubscribe-email-ajax-loader">
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
</div>
<!-- end of ajax loader -->

@stop