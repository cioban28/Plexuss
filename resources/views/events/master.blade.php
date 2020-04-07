<!doctype html>
<html class="no-js" lang="en">

	<head>
		@include('private.headers.header')		
	</head>
	<body id="{{$currentPage}}">
		@include('private.includes.topnav')

		<!-- events section -->
		        <div class="content-wrapper">
            <div id='eventcontent' class="row collapse ranking-c-wrapper">

                <!-- Left Side Part -->
                <div class="column small-12 large-12" id="eventcontent_left">
                @yield('content')
                </div>
			</div>
		</div>

		<!-- footer -->
		@if( $currentPage == 'plex-events' )

<!--
			<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
			<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
			<script src="/js/moment.min.js?8"></script>
			<script src="/js/underscoreJS/underscore_prod.js?8"></script>
-->
			<script src="/js/reactJS/react_v0.13.3_prod.js"></script>
			<script src="/js/reactJS/react_with_addons_v0.13.3_prod.js"></script>
			<script src="/js/reactJS/JSXTransformer_v0.13.3.js"></script>
<!--
			<script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
-->


			<!-- external react script -->
			<script type="text/jsx" src="/js/events.js?8"></script>
		@endif

		@include('private.includes.backToTop')
		@include('private.footers.footer')
		<script language="javascript">
			//console.log('foundation');
			$(document).foundation();
		</script>
	</body>
</html>
