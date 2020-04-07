@extends('admin.signup.master')
@section('content')
	@if (!isset($admin_application_completed))

		<div class='admin-step-signup step-1 @if (isset($signed_in) && $signed_in == 1) hidden @endif'>
			@include('admin.signup.step_1')
		</div>

		<div class='admin-step-signup step-2 @if (!isset($signed_in) || $signed_in == 0) hidden  @endif'>
			@include('admin.signup.step_2')
		</div>

		<div class='admin-step-signup step-3 hidden'>
			@include('admin.signup.step_3')
		</div>

		<div class='admin-steps-complete hidden'>
			@include('admin.signup.steps_complete')
		</div>

	@elseif (isset($admin_application_completed) && $admin_application_completed == 1)
	
		<div class='admin-steps-complete'>
			@include('admin.signup.steps_complete')
		</div>

	@endif
@stop