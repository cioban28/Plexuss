<!doctype html>
<html class="no-js" lang="en">
	<head>
		@include('private.headers.header')
        @include('includes.facebook_event_tracking')
	</head>
	<!-- Includes needed for topNav -->
	<body id="{{$currentPage}}">
		@include('private.includes.topnav')
		<div id="NCSA_Component">
			<div id="_StudentApp_Component">
				<!-- React Component -->
			</div>
		</div>

		<script type="text/javascript" src="/js/bundles/StudentApp/StudentApp_bundle.js" async></script>
		@include('private.footers.footer')
	</body>
</html>
	