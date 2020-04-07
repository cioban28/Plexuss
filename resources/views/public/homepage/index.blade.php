<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>College Network | College Recruiting Academic Network I Plexuss.com</title>
        <script src="/js/vendor/modernizr.js?8"></script>
        <link rel="stylesheet" href="/css/foundation.css">
        <link rel="stylesheet" href="/css/normalize.min.css?6">
        <link rel="stylesheet" href="/css/college.css">

        <meta content="width=device-width, initial-scale=1" name="viewport" />
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css">
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
        <link rel="stylesheet" type="text/css" charset="UTF-8" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.css" />
        <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick-theme.min.css" />
        <link rel="stylesheet" href="/css/prod_ready/owl.carousel.min.css"/>
        <link rel="stylesheet" href="/css/prod_ready/owl.theme.min.css">
        <link href="https://cdnjs.cloudflare.com/ajax/libs/flag-icon-css/3.3.0/css/flag-icon.min.css" rel="stylesheet"/>
        <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,300,300i,400i' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="/css/userMissingFields.css">
        <!-- for the signup modal -->
        {{-- <link rel="stylesheet" type="text/css" href="/css/prod_ready/signupin_all.min.css" /> --}}
        <style>
            body {
                font-family: "Open Sans" !important;
                background-color: #f2f4f6;
            }
        </style>
    </head>

    <body id="{{$currentPage}}" class="body">
        <!-- Start SmartBanner configuration -->
        <meta name="smartbanner:title" content="Plexuss College Application">
        <meta name="smartbanner:author" content="Plexuss">
        <meta name="smartbanner:price" content="FREE">
        <meta name="smartbanner:price-suffix-apple" content=" - On the App Store">
        <meta name="smartbanner:price-suffix-google" content=" - In Google Play">
        <meta name="smartbanner:icon-apple" content="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/plexuss-app-icon-black-green.png">
        <meta name="smartbanner:icon-google" content="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/plexuss-app-icon-black-green.png">
        <meta name="smartbanner:button" content="VIEW">
        <meta name="smartbanner:button-url-apple" content="http://apple.co/2x0hv8I">
        <meta name="smartbanner:button-url-google" content="http://bit.ly/2MSG5U7">
        <meta name="smartbanner:enabled-platforms" content="android,ios">
        <!-- End SmartBanner configuration -->
        <link rel="stylesheet" href="/css/smartbanner.min.css">
        <div class=" me-container clearfix abs-wrapper h100">

            <div id="_FrontPage_Component" data-premium="{{$premium_user_type or ''}}">
            <!-- component rendered here -->
                <div id="_SocialApp_Component" data-premium="{{$premium_user_type or ''}}">
                </div>
            </div>

        </div>
        <!-- page analytics -->
	    @include('includes.analytics')
        <script type="text/javascript" src="/js/bundles/SocialApp/SocialApp_bundle.js?v=0.02" async></script>
            @if (isset($signed_in) && $signed_in == 1)
                <script>
                    var AmplitudeData =  <?php echo json_encode(get_defined_vars()); ?>;
                </script>
            @endif
        <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.0/jquery.min.js"></script>
		<script src="//code.jquery.com/ui/1.10.2/jquery-ui.min.js"></script>
        <script src="/js/amplitude.js" defer></script>
        <script src="/js/smartbanner.min.js"></script>
    </body>
</html>


