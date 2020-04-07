<?php /*
@extends('get_started.master')

@section('content')

	<div id="get_started_step6_new" class="gs_step" data-step="{{$currentStep}}"></div>
	<script src="/js/prod_ready/getstarted/BreadCrumb_Step6_newComponents.min.js?v=1.05" defer></script>

@stop
 */
?>
 <?php 
    $major_details = isset($major_details) ? json_encode($major_details) : json_encode([]);
    // dd(get_defined_vars());
?>
@extends('get_started.master')

@section('content')
	
	<script type="text/javascript" src="//agrservice.educationdynamics.com/Scripts/Bundles/EddyAggregator" async></script>
	<div id="get_started_step7" class="gs_step" data-step="{{$currentStep}}" data-major_details="{{$major_details}}"></div>
	<script src="/js/prod_ready/getstarted/BreadCrumb_Step7_1_Components.min.js?v=1.11" defer></script>
	<!--<script src="/js/prod_ready/getstarted/BreadCrumb_Step7_Components.min.js" defer></script>-->

@stop