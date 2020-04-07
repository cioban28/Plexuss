<!doctype html>
<html class="no-js" lang="en">


	<head>
		@if( isset($currentPage) && ($currentPage != 'sales-pick-a-college' || $currentPage != 'sales-application-order') )
			@include('private.headers.header')
		@endif

		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>

		@if( isset($currentPage) && ($currentPage == 'sales-pick-a-college' || $currentPage == 'sales-application-order' ))
			<link rel="stylesheet" type="text/css" href="/css/salesCentralControl.css?8">
			<link rel="stylesheet" href="/css/default.css?8" />
			<link rel="stylesheet" href="/css/foundation.min.css?8" />
			<link href="https://diegoddox.github.io/react-redux-toastr/4.0/react-redux-toastr.min.css" rel="stylesheet" type="text/css">
		@endif

	</head>
	<body id="{{$currentPage}}">

		<!-- main content -->
		@if( isset($currentPage) && $currentPage == 'sales' )
		<div class="row collapse sales-central-control-container" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/sales/sales-dash-bg.jpg, (default)]">
		@else
		<div class="row collapse sales-central-control-container">
		@endif
			<div class="column small-12">

				<!-- sales top nav -->
				<div class="row collapse sales-topnav-container">

						@if( isset($currentPage) && ($currentPage == 'sales-tracking' || $currentPage == 'sales-site-performance' || $currentPage == 'sales-email-reporting' || $currentPage == 'sales-device-os-reporting' || $currentPage == 'sales-social-newsfeed') || $currentPage == 'sales-student-tracking')

							@include('private.includes.salesTopNavNew')
						@elseif( isset($currentPage))
							@include('private.includes.salesTopNav')
						@endif
					</div>
				</div>

				<!-- main content -->
				<div class="row collapse sales-main-content-container">
					<div class="column small-12">
						@yield('content')
					</div>
				</div>

			</div>
		</div>

		<!-- footer -->
		@if( $currentPage == 'sales' || $currentPage == 'sales-clientReporting'
				|| $currentPage == 'sales-messages' || $currentPage == 'sales-billing')
			<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
			<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
			<script src="/js/foundation/foundation.js"></script>
			<script src="/js/foundation/foundation.interchange.js"></script>
			<script src="/js/foundation/foundation.reveal.js"></script>
			<script src="/js/jqueryDataTablesPlugin/jquery.dataTables.min.js?8"></script>
			<script src="/js/jqueryDataTablesPlugin/dataTables.colReorder.min.js?8"></script>
			<script src="/js/jqueryDataTablesPlugin/dataTables.colVis.min.js?8"></script>
			<script src="/js/jqueryDataTablesPlugin/dataTables.fixedHeader.min.js?8"></script>
			<script src="/js/jqueryDataTablesPlugin/dataTables.fixedColumns.min.js?8"></script>
			<script src="/js/jqueryDataTablesPlugin/dataTables.keyTable.min.js?8"></script>
			<script src="/js/jqueryDataTablesPlugin/dataTables.responsive.min.js?8"></script>
			<script src="/js/jqueryDataTablesPlugin/dataTables.scroller.min.js?8"></script>
			<script src="/js/moment.min.js?8"></script>
			<script type="text/javascript" src="/daterangepicker/moment.min.js"></script>
			<script type="text/javascript" src="/daterangepicker/daterangepicker.js"></script>
			<!--<script src="/js/commonfunction.js?8"></script>-->
			<script src="/js/salesControlCenter.js?8"></script>

			<script>
				$(document).foundation();
			</script>
		@elseif( $currentPage == 'sales-tracking' )
			<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
			<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
			<script src="/js/moment.min.js?8"></script>
			<script src="/js/underscoreJS/underscore_prod.js?8"></script>
			<script src="/js/reactJS/react_v0.13.3_prod.js"></script>
			<script src="/js/reactJS/react_with_addons_v0.13.3_prod.js"></script>
			<script src="/js/reactJS/JSXTransformer_v0.13.3.js"></script>
			<script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
			<script src="/daterangepicker/moment.min.js"></script>
			<script src="/daterangepicker/daterangepicker.js"></script>

			<!-- external react script -->
			<script type="text/jsx" src="/js/salesHeader.js?8"></script>
			<!-- javascript/jquery script -->
			<script src="/js/sales.js?8"></script>
		@elseif($currentPage == 'sales-site-performance' || $currentPage == 'sales-student-tracking'
		|| $currentPage == 'sales-device-os-reporting')
			<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
			<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
			<script src="/js/moment.min.js?8"></script>
			<script src="/js/underscoreJS/underscore_prod.js?8"></script>
			<script src="/js/reactJS/react_v0.13.3_prod.js"></script>
			<script src="/js/reactJS/react_with_addons_v0.13.3_prod.js"></script>
			<script src="/js/reactJS/JSXTransformer_v0.13.3.js"></script>
			<script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
			<script src="/daterangepicker/moment.min.js"></script>
			<script src="/daterangepicker/daterangepicker.js"></script>

			<!-- external react script -->
			<script type="text/jsx" src="/js/salesHeader.js?8"></script>
			<!-- javascript/jquery script -->
			<script src="/js/sales.js?8"></script>
		@elseif($currentPage == 'sales-social-newsfeed')
			<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
			<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
			<script src="/js/moment.min.js?8"></script>
			<script src="/js/underscoreJS/underscore_prod.js?8"></script>
			<script src="/js/reactJS/react_v0.13.3_prod.js"></script>
			<script src="/js/reactJS/react_with_addons_v0.13.3_prod.js"></script>
			<script src="/js/reactJS/JSXTransformer_v0.13.3.js"></script>
			<script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
			<script src="/daterangepicker/moment.min.js"></script>
			<script src="/daterangepicker/daterangepicker.js"></script>

			<!-- external react script -->
			<script type="text/jsx" src="/js/salesHeader.js?8"></script>
			<!-- javascript/jquery script -->
			<script src="/js/sales.js?8"></script>
		@endif

		@if($currentPage == 'sales-email-reporting')

			<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
			<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
			<script src="/js/moment.min.js?8"></script>
			<script src="/js/underscoreJS/underscore_prod.js?8"></script>
			<script src="/js/reactJS/react_v0.13.3_prod.js"></script>
			<script src="/js/reactJS/react_with_addons_v0.13.3_prod.js"></script>
			<script src="/js/reactJS/JSXTransformer_v0.13.3.js"></script>
			<script src="http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
			<script src="/daterangepicker/moment.min.js"></script>
			<script src="/daterangepicker/daterangepicker.js"></script>

			<!-- external react script -->
			<script type="text/jsx" src="/js/salesComponents.js?8"></script>
			<!-- javascript/jquery script -->
			<script src="/js/sales.js?8"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
			<script src="{{asset('js/emailReporting/sorttable.js')}}"></script>
			<script src="{{asset('js/emailReporting/emailReporting.js')}}" ></script>
		@endif

	</body>
</html>
