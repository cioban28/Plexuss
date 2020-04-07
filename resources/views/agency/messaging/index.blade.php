{{-- Reuse Admin Messaging Component for agency --}}
@extends('agency.master')
@section('content')
	<div id="AdminDashboard_Component"
		data-setupcompleted="{{$completed_signup or ''}}" 
		data-bg="{{$bing_bkground_img or ''}}"
		data-super="{{$super_admin or ''}}"
		data-orgs-first-user="{{$orgs_first_user or 0}}"
		data-logo="{{$school_logo or ''}}"></div>
		
	<script type="text/javascript" src="/js/bundles/AdminDashboard/AdminDashboard_bundle.js" async></script>
@stop