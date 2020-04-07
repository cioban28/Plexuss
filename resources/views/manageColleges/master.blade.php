<!doctype html>
<html class="no-js" lang="en">

	<head>
		@include('private.headers.header')
	</head>
	<body id="{{$currentPage}}">

		<!-- main content -->
		@if( isset($currentPage) && $currentPage == 'manageColleges' )
		<div class="row collapse aor-central-control-container" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/sales/sales-dash-bg.jpg, (default)]">
		@else
		<div class="row collapse aor-central-control-container">
		@endif
			<div class="column small-12">
				
				<!-- aor top nav -->
				<div class="row collapse aor-topnav-container">
					<div class="column small-12">
						@include('private.includes.manageCollegesTopNav')
					</div>
				</div>

				<!-- main content -->
				<div class="row collapse aor-main-content-container">
					<div class="column small-12">
						@yield('content')
					</div>
				</div>

			</div>
		</div>
		
		<!-- footer -->
		@if( $currentPage == 'manageColleges' || $currentPage == 'manageCollegesReporting' )
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
			<!--<script src="/js/moment.min.js?8"></script>
			<script type="text/javascript" src="/daterangepicker/moment.min.js"></script>
			<script type="text/javascript" src="/daterangepicker/daterangepicker.js"></script>
			<script src="/js/commonfunction.js?8"></script>-->
			<script type="text/javascript" src="/js/AORControlCenter.js?"></script>

			<script>
				$(document).foundation();
			</script>
		@endif

	</body>
</html>
