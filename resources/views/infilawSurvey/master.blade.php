<!doctype html>
<html class="no-js" lang="en">

	<head>
		@include('private.headers.header')
	</head>

	@if(isset($session_arr['school_name']) && $session_arr['school_name'] == 'Florida Coastal School of Law')
	<body id="{{$currentPage}}" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/infilawSurvey/fcsl-background.jpg, (default)]">
	@elseif(isset($session_arr['school_name']) && $session_arr['school_name'] == 'Charlotte School of Law')
	<body id="{{$currentPage}}" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/infilawSurvey/csl-background.jpg, (default)]">
	@else
	<body id="{{$currentPage}}">
	@endif

		<!-- main content -->
		@yield('content')

		<!-- footer -->
		@if( $currentPage == 'infilawSurvey' )
			<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
			<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
			<script src="/js/foundation/foundation.js"></script>
			<script src="/js/foundation/foundation.interchange.js"></script>
			<script src="/js/foundation/foundation.abide.js?8"></script>
			<script type="text/javascript">
				$(document).foundation({
					abide : {
					  patterns: {
					    age: /^([1-9]?\d|100)$/,
					    phone: /^([0-9\-\+\(\) ])+$/,
						name: /^([a-zA-Z\-\.' ])+$/,
						address: /^[a-zA-Z0-9\.,#\- ]+$/,
					  }
					}
				});
			</script>
			<script src="/js/infilawSurvey.js?8"></script>
		@endif

	</body>
</html>
