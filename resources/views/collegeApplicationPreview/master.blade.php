<!DOCTYPE html>
<html class="no-js" lang="en">

	<head>
        @include('includes.facebook_event_tracking')
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>

		@include('private.headers.header')
	</head>

	<body id="{{$currentPage or ''}}" class="stylish-scrollbar-mini">

		<div id="_SocialApp_Component" data-premium="{{$premium_user_type or ''}}">
			<!-- component rendered here -->
		</div>

		<script type="text/javascript" src="/js/bundles/SocialApp/SocialApp_bundle.js?v=0.02" async></script>

	</body>
</html>
