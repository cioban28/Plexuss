<!doctype html>
<html class="no-js" lang="en">

	<head>
		@include('private.headers.header')
	</head>
	<body id="{{$currentPage}}">
		<!-- topnav -->
		@include('private.includes.topnav')
		<!-- main content -->
		@yield('content')
		<!-- footer -->
		@include('private.footers.footer')
	</body>
</html>