<!doctype html>
<html class="no-js" lang="en">

	<head>
		@include('private.headers.header')
		@include('includes.facebook_event_tracking')
		@include('includes.hotjar_for_plexuss_domestic')
		<link rel="stylesheet" type="text/css" href="/css/PremiumIndia/app.css">
		<link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
	</head>
	<body id="{{$currentPage}}" data-admintype="{{$adminType or 'admin'}}">

		<!-- top nav based on type of user -->
		@if( isset($is_agency) && $is_agency == 1 )
			@include('private.includes.agencyTopNav')
		@else
			@include('private.includes.topnav')
		@endif

		<!-- main content -->
		@yield('content')

		@include('private.footers.footer')

	</body>
</html>
