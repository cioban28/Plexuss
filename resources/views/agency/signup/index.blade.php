@extends('agency.signup.master')
@section('content')
	@if (!isset($agency_application_completed))

{{-- 		<div class='agency-step-signup step-0'>
			@include('agency.signup.step_0')
		</div>
 --}}
		<div class='agency-step-signup step-1 @if (isset($signed_in) && $signed_in == 1) hidden @endif'>
			@include('agency.signup.step_1')
		</div>

		<div class='agency-step-signup step-2 @if (!isset($signed_in) || $signed_in == 0) hidden  @endif'>
			@include('agency.signup.step_2')
		</div>

		<div class='agency-step-signup step-3 hidden'>
			@include('agency.signup.step_3')
		</div>

		<div class='agency-steps-complete hidden'>
			@include('agency.signup.steps_complete')
		</div>

	@elseif (isset($agency_application_completed) && $agency_application_completed == 1)
	
		<div class='agency-steps-complete'>
			@include('agency.signup.steps_complete')
		</div>

	@endif
@stop