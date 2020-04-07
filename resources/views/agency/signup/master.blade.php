<!doctype html>
<html class="no-js" lang="en">
<head>
@include('public.headers.header')
</head>
<body class='agency-signup-body'>
	<div class="agency-signup-topnav-container clearfix">
		<!-- logo -->
		<div class="agency-signup-logo">
			<a href="#"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/premium_page/plexuss-white.png" alt="Plexuss"/></a>
			<span>Agents Only</span>
		</div>

		@if (!isset($agency_application_completed))
			<div class="agency-signup-steps-icon">
				<div class='agency-step-icon step-1'>
					<div class='sprite active'></div>
					<div class='step-checkmark @if (isset($signed_in) && $signed_in == 1) active  @endif'></div>
					<div class='step-text @if (!isset($signed_in) || $signed_in == 0) active @endif'>Step 1</div>
				</div>
				<div class='agency-step-icon step-2'>
					<div class='sprite @if (isset($signed_in) && $signed_in == 1) active  @endif'></div>
					<div class='step-checkmark'></div>
					<div class='step-text @if (isset($signed_in) && $signed_in == 1) active  @endif'>Step 2</div>
				</div>
				<div class='agency-step-icon step-3'>
					<div class='sprite'></div>
					<div class='step-checkmark'></div>
					<div class='step-text'>Step 3</div>
				</div>
			</div>
		@elseif (isset($agency_application_completed) && $agency_application_completed == 1)
			<div class="agency-signup-steps-icon">
				<div class='agency-step-icon step-1'>
					<div class='sprite active'></div>
					<div class='step-checkmark active'></div>
					<div class='step-text'>Step 1</div>
				</div>
				<div class='agency-step-icon step-2'>
					<div class='sprite active'></div>
					<div class='step-checkmark active'></div>
					<div class='step-text'>Step 2</div>
				</div>
				<div class='agency-step-icon step-3'>
					<div class='sprite active'></div>
					<div class='step-checkmark active'></div>
					<div class='step-text'>Step 3</div>
				</div>
			</div>
		@endif

		{{-- Invisible div to help flex justify space --}}
		<div class='invisible-div'></div>
	</div>

	@yield('content')

	@include('private.includes.ajax_loader')
	
	@include('public.footers.footer')
</body>
</html>
