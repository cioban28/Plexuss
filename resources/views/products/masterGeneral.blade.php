<!doctype html>
<html class="no-js" lang="en">

  <head>
    @include('private.headers.header')
    @include('includes.hotjar_for_plexuss_domestic')
    <link rel="stylesheet" type="text/css" href="/css/premiumGeneral/app.css">
    <link href="https://fonts.googleapis.com/css?family=Open+Sans" rel="stylesheet">
    <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.5.0/css/all.css" integrity="sha384-B4dIYHKNBt8Bc12p+WXckhzcICo0wtJAoU8YZTY5qE0Id1GSseTk6S+L3BlXeVIU" crossorigin="anonymous">


  </head>
  <body id="{{$currentPage}}" data-admintype="{{$adminType or 'admin'}}">

    <!-- top nav based on type of user -->
    @if( isset($is_agency) && $is_agency == 1 )
      @include('private.includes.agencyTopNav')
    @else
      @include('private.includes.topnav')
    @endif

    <!-- main content -->
    @yield('content')

    @include('private.footers.footer')
     <script>
            jQuery(document).ready(function($) {
              $('.owl-carousel').owlCarousel({
                items: 1,
                lazyLoad: true,
                loop: true,
                margin: 10,
                autoHeight: true,
                navigation: true,
                navigationText : ["<div class='nav-btn prev-slide'><i class='fa fa-angle-left'></i></div>","<div class='nav-btn next-slide'><i class='fa fa-angle-right'></i></div>"]
              });
            });
          </script>
        </div>
      </div>
    </section>
  </body>

</html>
