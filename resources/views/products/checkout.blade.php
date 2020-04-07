<!doctype html>
<html class="no-js" lang="en">

	<head>
		@include('private.headers.header')
        @include('includes.facebook_event_tracking')
        @include('includes.hotjar_for_plexuss_domestic')
		<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>
	</head>

	<body id="{{$currentPage or ''}}" class="stylish-scrollbar-mini">
		@include('private.includes.topnav')

		<div id="_StudentApp_Component" data-premium="{{$premium_user_type or ''}}">
			<!-- component rendered here -->
		</div>

		<script type="text/javascript" src="/js/bundles/StudentApp/StudentApp_bundle.js?v=1.043" async></script>
	    
	    <!-- WhatsHelp.io widget -->
		<script type="text/javascript">
		(function () {
		var options = {
		whatsapp: "+1 310 2373494", // WhatsApp number
		call_to_action: "Message us", // Call to action
		position: "right", // Position may be 'right' or 'left'
		};
		var proto = document.location.protocol, host = "whatshelp.io", url = proto + "//static." + host;
		var s = document.createElement('script'); s.type = 'text/javascript'; s.async = true; s.src = url + '/widget-send-button/js/init.js';
		s.onload = function () { WhWidgetSendButton.init(host, proto, options); };
		var x = document.getElementsByTagName('script')[0]; x.parentNode.insertBefore(s, x);
		})();
		</script>
		<!-- /WhatsHelp.io widget --> 
	    
	    @include('private.footers.footer')
	    <script type="text/javascript">
	      if ($(window).width() < 1300) {
	          $("#sticky-search-bar1").removeClass('sticky');
	          $("#sticky-search-bar2").removeClass('sticky');
	    	}
	    </script>

	</body>
</html>