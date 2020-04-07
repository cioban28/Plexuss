@extends('admin.master')

@section('content')
	<div id="main-content-management-container" class="@if($is_any_school_live == true) with-daily-chat-bar @endif" data-school-slug="{{$school_slug}}" data-super-admin="{{$super_admin}}">
		<!-- components injected here from contentManagementComponents.js -->
	</div>
@stop