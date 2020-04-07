<!doctype html>
<html class="no-js" lang="en">

	<head>
		@include('private.headers.header')
		@include('includes.facebook_event_tracking')
		<script type="text/javascript">
		fbq('track', 'Settings-page-{{$active_tab or ""}}');
		</script>
	</head>

	<body id="{{$currentPage}}">

		@include('private.includes.topnav')

		<div class="row fixed-width">

			<!-- settings menu side bar -->
			<div class='medium-3 column hide-for-small-only'>
				@section('sidebar')
	        		This is the master sidebar.
	   			@show
			</div>

			<!-- settings main content -->
			<div class='medium-9 column'>  
				@yield('content')
			</div>

		</div>

		@include('private.includes.ajax_loader')

		@include('private.footers.footer')

	</body>
</html>
