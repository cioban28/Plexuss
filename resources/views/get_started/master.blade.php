<!DOCTYPE html>
<html lang="en">
<head>
	<!-- only make google fonts call for the steps that require it -->
	@if( isset($currentStep) && ($currentStep == '1' || $currentStep == '2' || $currentStep == '5') )
		<link href='https://fonts.googleapis.com/css?family=Architects+Daughter' rel='stylesheet' type='text/css'>
	@endif
	<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700' rel='stylesheet' type='text/css'>

	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0"/>
	<meta name="p:domain_verify" content="8edfc77043263d3582f53df83941d89f"/>
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<meta name="robots" content="noarchive, nofollow, noimageindex, noindex, noodp, nosnippet, notranslate, noydir">
	<title>{{$title or 'Get Started'}}</title>

	@include('includes.quora')
	<!-- only step 6 requires owl 2 css -->

	@if( isset($currentStep) && $currentStep == '7' )
		<link rel="stylesheet" href="/css/owl2/owl_all.min.css" />
	@endif

	@if( isset($currentStep) && $currentStep == '6' )
		@include('includes.twitter_conversion_tracking')
	@endif

	@if(isset($currentStep) && $currentStep == '1')
		@include('includes.facebook_conversion_script')
		@include('includes.twitter_conversion_tracking')
	@endif

	@include('private.headers.header')

	@include('includes.facebook_event_tracking')
	<script type="text/javascript">
	fbq('track', 'getStarted_step{{$currentStep or -1}}');
	</script>

    <script type="text/javascript" src="/js/amplitude.js?v=1.00"></script>
	<!-- concatenated file of foundation.min.css and getStarted.min.css -->
	<link rel="stylesheet" href="/css/prod_ready/foundation_getstarted.min.css?v=1.07">
	@if(isset($currentStep) && ($currentStep == '1' || $currentStep == '7' || $currentStep == 1 || $currentStep == 7))
	<!-- pinterest script begins -->
	<script type="text/javascript">
	!function(e){if(!window.pintrk){window.pintrk=function(){window.pintrk.queue.push(Array.prototype.slice.call(arguments))};var n=window.pintrk;n.queue=[],n.version="3.0";var t=document.createElement("script");t.async=!0,t.src=e;var r=document.getElementsByTagName("script")[0];r.parentNode.insertBefore(t,r)}}("https://s.pinimg.com/ct/core.js");

	pintrk('load','2614567057154');
	pintrk('page');
	</script>
	<noscript>
	<img height="1" width="1" style="display:none;" alt=""
	src="https://ct.pinterest.com/v3/?tid=2614567057154&noscript=1" />
	</noscript>
	<!-- pinterest script ends -->

	@include('includes.snapchat')
	@include('includes.yahoo')
	@endif
	@include('includes.hotjar_for_plexuss_domestic')

	<!-- Global site tag (gtag.js) - Google AdWords: 820637639 -->
	<script async src="https://www.googletagmanager.com/gtag/js?id=AW-820637639"></script>
	<script>
	  window.dataLayer = window.dataLayer || [];
	  function gtag(){dataLayer.push(arguments);}
	  gtag('js', new Date());

	  gtag('config', 'AW-820637639');
	</script>
	@if(isset($currentStep) &&  ($currentStep == '9'))
		<link rel="stylesheet" href="/css/get_started/build/css/intlTelInput.css">
	  <link rel="stylesheet" href="/css/get_started/build/css/demo.css">
	  <link rel="stylesheet" type="text/css" href="/css/get_started/get_started_step9.css">
	@endif
	</head>
	@if(isset($currentStep) && ($currentStep == '1' ))
	<script>qp('track', 'GenerateLead');</script>
	<!-- Event snippet for Sign Up Basic conversion page -->
	<script>
	  gtag('event', 'conversion', {'send_to': 'AW-820637639/dGKpCNuhunsQx9-nhwM'});
	  pintrk('track', 'signup');
	</script>
	@endif
	@if(isset($currentStep) && ($currentStep == '7' ))
	<script>qp('track', 'CompleteRegistration');</script>
	<!-- Event snippet for Sign Up Complete Profile conversion page -->
	<script>
	  gtag('event', 'conversion', {'send_to': 'AW-820637639/c8xPCNXcpnsQx9-nhwM'});
	  pintrk('track', 'profilecomplete');
	</script>
	@endif
<body class="step-{{$currentStep or 0}}">
	<div id="_GetStarted_Component">
		<!-- React component rendered here -->
		<div id="_StudentApp_Component"></div>
	</div>
	@if (isset($signed_in) && $signed_in == 1)
	    @if( isset($fb_id) )
	        <div id="_fb-id"></div>
	    @endif
	@endif

	<!-- used by each step -->
	<script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script>
	<script type="text/javascript" src="/js/bundles/GetStarted/GetStarted_bundle.js?v=0.02" async></script>
	
	<!-- contents injected here -->
	<!-- <div id="recruitmeModal" data-options="" class="reveal-modal medium get-recruited-modal-sm" data-reveal>
	</div> -->
	
	<!-- only step 6 needs owl -->
	@if( isset($currentStep) && $currentStep == 7 )
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/jquery-ui.min.js"></script>
		<script src="/js/owl2/owl.carousel.min.js"></script>
	@endif
	
	<!-- only step 1/2 needs moment.js -->
	@if( isset($currentStep) && ($currentStep == '1' || $currentStep == '2' ) )
		<script src="/js/moment.min.js"></script>
	@endif

	<!-- a concatenated file containing main foundation.js and foundation.reveal.js -->
	<script src="/js/prod_ready/foundation/main_plus_reveal.min.js"></script>

	@if(isset($for_higher_ed) && $for_higher_ed == true)
			<input id="leadid_token" name="universal_leadid" type="hidden" value=""/>
	@endif

	@if(isset($for_higher_ed) && $for_higher_ed ==  true)
		@include('includes.leadid')
	@endif

	@if( isset($currentStep) && $currentStep == 6 )
		<script src="/js/prod_ready/foundation/foundation.abide.min.js"></script>
	@endif

	<!-- concatenated script with underscore.js, react.js, and react-dom.js -->
	<script src="/js/reactJS/jsx/underscore_react_reactdom.min.js"></script>

	<!-- page analytics -->
	@include('includes.analytics')

	@if(isset($currentStep) && $currentStep == '1')
		<img height="1" width="1" style="display:none;" alt="" src="https://ct.pinterest.com/?tid=K3S3RE5NQvy&value=0.00&quantity=1"/>
	@endif
	
	@if(isset($for_higher_ed) && $for_higher_ed ==  true)
		@include('includes.leadid')
	@endif

</body>
</html>