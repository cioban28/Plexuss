<!doctype html>
<html class="no-js" lang="en">
	<head>
		@include('private.headers.header')
	</head>
	<body id="{{$currentPage}}">
		@include('private.includes.topnav')
		<div class="row collapse profile-container clearfix">
			<div class='profile-left-site-bar-mobile'>  <!--  medium-4 large-2 column' -->
				@section('sidebar')
            		This is the master sidebar.
       			@show
			</div>
			<div id="profileRightSide" class='profile-content-container'>  <!-- medium-8 large-9 end column '> -->
				@yield('content')

			</div>


			<!-- sic -->
			@include('includes.smartInteractiveColumn')

		</div>
		@include('private.includes.backToTop')
		@include('private.footers.footer')
	</body>
</html>
