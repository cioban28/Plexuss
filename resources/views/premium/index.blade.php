@extends('admin.master')
@section('content')

	<div id="_StudentApp_Component" data-premium="{{$premium_user_type or ''}}">
		<!-- component rendered here -->
	</div>

	<script type="text/javascript" src="/js/bundles/StudentApp/StudentApp_bundle.js?v=1.043" async></script>
	
@stop