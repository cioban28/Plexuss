<!doctype html>
<html class="no-js" lang="en">

	<head>
		@include('private.headers.header')
	</head>
	<body id="{{$currentPage}}">

		<!-- main content -->
		@yield('content')

		<!-- footer -->
		<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
		<script src="/js/moment.min.js?8"></script>
		<script src="/js/underscoreJS/underscore_prod.js?8"></script>
		<script src="/js/reactJS/react_v0.13.3_dev.js"></script>
		<script src="/js/reactJS/react_with_addons_v0.13.3_dev.js"></script>
		<script src="/js/reactJS/JSXTransformer_v0.13.3.js"></script>

		<!-- external react script -->
		<script type="text/jsx" src="/js/leadility.js?8"></script>
	</body>
</html>
