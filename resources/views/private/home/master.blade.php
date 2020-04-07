<!doctype html>
<html class="no-js" lang="en">
	<head>
		@include('private.headers.header')
		@include('includes.facebook_event_tracking')
	</head>
	
	<body id="{{$currentPage}}">
		@include('private.includes.topnav')

		@yield('content')

		@include('private.includes.backToTop')
		@include('private.footers.footer')

		<!-- user feedback modal-->
		<!--  if( isset($user_feedback_college) && !isset($user_feedback_college->hideModal) )
			include('private.includes.userFeedbackModal')
		endif -->

	</body>
</html>
