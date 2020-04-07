@extends('agency.master')
@section('content')
	<div class="row agency-settings-main-container">
		<div class="column small-12 medium-3 large-2 agency-settings-leftside-menu-container">

			<div class="settings-menu-header show-for-medium-up">Settings</div>
			<div class="settings-menu-header hide-for-medium-up mobile-agency-menu-btn">Settings Menu</div>
			<ul class="side-nav agency-settings-sidenav">
				<li data-agency-menu-tab="profileInfo" class="agency-menu-tab @if($agencySettingLoadThis == 'profileInfo') active @endif"><a href="/agency/settings/profileInfo">Profile Info</a></li>
				<!--<li data-agency-menu-tab="adminRoles" class="agency-menu-tab @if($agencySettingLoadThis == 'adminRoles') active @endif"><a href="">Admin Roles</a></li>
				<li data-agency-menu-tab="notifications" class="agency-menu-tab @if($agencySettingLoadThis == 'notifications') active @endif"><a href="">Notifications</a></li>-->
				{{-- <li data-agency-menu-tab="paymentInfo" class="agency-menu-tab @if($agencySettingLoadThis == 'paymentInfo') active @endif"><a href="/agency/settings/paymentInfo">Payment Info</a></li> --}}
			</ul>
			
		</div>

		<div class="column small-12 medium-9 large-10 agency-settings-rightside-content-container">
			@include('agency.ajax.'.$agencySettingLoadThis)
		</div>

		@include('private.includes.ajax_loader')
	</div>
@stop