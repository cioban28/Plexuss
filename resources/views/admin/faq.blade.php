@extends('admin.master')
@section('content')

	<div id="HelpPage_Component" 
		data-setupcompleted="{{$completed_signup}}" 
		data-bg="{{$bing_bkground_img or ''}}"
		data-super="{{$super_admin}}"
		data-orgs-first-user="{{$orgs_first_user or 0}}"
		data-logo="{{$school_logo}}"></div>
		
	<script type="text/javascript" src="/js/bundles/AdminDashboard/AdminDashboard_bundle.js" async></script>

@stop