@extends('b2b.resources.master')
@section('content')
    @if(isset($onboardingInfo) && count($onboardingInfo) > 0)
        @if($onboardingInfo['signup_complete'] == 0)
            <div class='admin-step-signup step-1 @if (isset($signed_in) && $signed_in == 1) hidden @endif'>
                @include('b2b.resources.step_1')
            </div>

            <div class='admin-step-signup step-3 @if (!isset($signed_in) || $signed_in == 0) hidden  @endif'>
                @include('b2b.resources.step_2')
            </div>

            <div class='admin-step-signup step-4 hidden'>
                @include('b2b.resources.step_3')
            </div>

            <div class='admin-steps-complete hidden'>
                @include('b2b.resources.steps_complete')
            </div>
        @elseif ($onboardingInfo['signup_complete'] == 1)
           <div class='admin-steps-complete'>
                @include('b2b.resources.steps_complete')
            </div>
        @endif
    @else
        <div class='admin-step-signup step-1 @if (isset($signed_in) && $signed_in == 1) hidden @endif'>
            @include('b2b.resources.step_1')
        </div>

        <div class='admin-step-signup step-3 @if (!isset($signed_in) || $signed_in == 0) hidden  @endif'>
            @include('b2b.resources.step_2')
        </div>

        <div class='admin-step-signup step-4 hidden'>
            @include('b2b.resources.step_3')
        </div>

        <div class='admin-steps-complete hidden'>
            @include('b2b.resources.steps_complete')
        </div>
    @endif
@stop