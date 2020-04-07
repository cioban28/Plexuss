<!doctype html>
<html class="no-js" lang="en">

	<head>
		@include('private.headers.header')
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

		<!-- ajax loader -->
		@include('private.includes.ajax_loader')
		<!-- ajax loader -->
		<!-- footer -->

		@include('private.footers.footer')

	</body>
</html>