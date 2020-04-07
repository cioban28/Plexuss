<!doctype html>
<html class="no-js" lang="en">

	<head>
		@include('private.headers.header')
        @include('includes.facebook_event_tracking')
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>
	</head>

	<body id="{{$currentPage or ''}}" class="stylish-scrollbar-mini">
		@include('private.includes.topnav')
		
		@yield('content')

		@include('private.footers.footer')
	    <script type="text/javascript">
	      if ($(window).width() < 1300) {
	          $("#sticky-search-bar1").removeClass('sticky');
	          $("#sticky-search-bar2").removeClass('sticky');
	    	}
	    </script>

	</body>
</html>