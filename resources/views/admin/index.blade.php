@extends('admin.master')
@section('content')
<?php 
    // dd(get_defined_vars());
?>

	<div id="AdminDashboard_Component" 
		data-setupcompleted="{{$completed_signup}}" 
		data-bg="{{$bing_bkground_img or ''}}"
		data-super="{{$super_admin}}"
        data-is_admin_premium="{{$is_admin_premium}}"
		data-orgs-first-user="{{$orgs_first_user or 0}}"
        data-user_id="{{$user_id or ''}}"
		data-logo="{{$school_logo}}"></div>
		
	<script type="text/javascript" src="/js/bundles/AdminDashboard/AdminDashboard_bundle.js?v=1.01" async></script>

@stop