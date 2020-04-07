<!doctype html>
<html class="no-js" lang="en">
	<head>
		@include('private.headers.header')
		<!-- temporary -->
		 <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	</head>
	<body id="{{$data['currentPage']}}">
		@include('private.includes.topnav')
	
        <div class="row" style="position:relative;">
        
		<div class="custom-row">
			<div class="custom-12">
				@yield('content')
			</div>		<!-- End Left Side Part  -->	
			<!-- End Right Side Part -->
		</div>

        </div>
		@include('private.footers.footer')
		@include('private.admin.news.footers.footer')
	</body>
</html>
