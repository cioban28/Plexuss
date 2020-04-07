<!doctype html>
<html class="no-js" lang="en">
	<head>
		<!-- header files -->
		@include('private.headers.header')
	</head>
	<body id="error_404">

		<!-- top nav -->
		@include('private.includes.topnav')

		<!-- main content -->
		@yield('content')

		<!-- footer nav -->
		@include('public.includes.footer')
		
		<!-- footer -->
		@include('private.footers.footer')

	</body>
</html>
