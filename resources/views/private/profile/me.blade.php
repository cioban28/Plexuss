<!doctype html>
<html class="no-js" lang="en">
	<head>
		@include('private.headers.header')
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
		<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	</head>
	<body id="{{$currentPage}}">
		@include('private.includes.topnav')
		<div class=" me-container clearfix abs-wrapper h100">
			
			<div id="_StudentApp_Component" class="h101 pos-abs w100" data-premium="{{$premium_user_type or ''}}">
			<!-- component rendered here -->
			</div>


			
			@include('includes.smartInteractiveColumn')
		</div>
		<!-- sic -->
		
		<script type="text/javascript" src="/js/bundles/StudentApp/StudentApp_bundle.js?v=1.065" async></script>
		@include('private.includes.backToTop')
		@include('private.footers.footer')
	</body>
</html>