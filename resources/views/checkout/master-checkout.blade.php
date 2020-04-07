<!doctype html>
<html class="no-js" lang="en">
  <head>
    @include('private.headers.header')
    @include('includes.facebook_event_tracking')
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://js.stripe.com/v3/"></script>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    <link rel="stylesheet" type="text/css" href="/css/checkout/checkout.css">
  </head>

  <body id="{{$currentPage}}" class="body-set">
    @include('private.includes.topnav')

    @yield('content')

    @include('private.includes.backToTop')
    @include('private.footers.footer')

    <script type="text/javascript" src="/js/checkout.js"></script>
  </body>
</html>
