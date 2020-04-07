@extends('public.homepage.master')
@section('content')
<div class="row unsubscribe-email">
    <div class="column large-12 company-name">
    	Plexuss
    </div>
    <div class="column large-offset-1 large-6 end">
    	<div class="row unsubsrcibe-begin">
			<div class="column large-12 text-center unsubscribe-title">Unsubscribe</div>
			<div class="column large-12 unsubscribe-notify">
				<span>Please input your email address: </span>
			</div>
			<div class="column large-12">
				{{Form::text('user_email_addr', '', array('class' => 'unsubscribe-user-email'))}}
				<small class="error"></small>
			</div>
			<div class="column large-12 unsubscribe-notify">
				<span>Clicking the confirmation button below will remove your email address from our mailings within the next 48 hours.</span>
			</div>
			<div class="column large-12 text-center">
				<a href="#" class="button unsubscribe-confirm">Unsubscribe Me</a>
			</div>
		</div>
	</div>
</div>
@stop