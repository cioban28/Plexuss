<!doctype html>
<html class="no-js" lang="en">
    <head>
        <meta name="csrf-token" content="{{ csrf_token() }}">
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
        <!-- <link rel="stylesheet" href="/css/news.css?8.0"> -->
        {{-- <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests">  --}}
    </head>

    <body id="{{$currentPage}}" class="body">
        <div class=" me-container clearfix abs-wrapper h100">

            <div id="_SocialApp_Component" data-premium="{{$premium_user_type or ''}}">
            <!-- component rendered here -->
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
        <script src="/js/amplitude.js" defer></script>
    </body>
</html>


