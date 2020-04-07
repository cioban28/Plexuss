<!doctype html>
<html class="no-js" lang="en">
	<head>
		@include('private.headers.header')
	</head>
	<body id="{{$currentPage}}">
		<!--ssss -->
		@include('private.includes.topnav')



        <!-- news section -->

        <div class="content-wrapper">
            <div id='newshomecontent' class="row collapse ranking-c-wrapper" style="height: 100%; max-width: 100%;">
            <div class="news-cont-left-container">
            @if(isset($PageLayout) && $PageLayout=="right")

                <!-- Right Side Part -->
                <div class="column small-12 medium-4 large-3">
                @section('sidebar')
                <!-- This is the master sidebar. -->
                @show
                </div>

                <!-- Left Side Part -->
                <div class="column small-12 large-9 medium-8" id="RankingSearchResultData">
                @yield('content')
                </div>
            @else

                <!-- Left Side Part -->
                <div class="column small-12 large-12">
                @yield('content')
                </div>


                <!-- Right Side Part -->
                <div class="column small-3 show-for-large-up">
                @section('sidebar')
                <!-- This is the master sidebar. -->
                @show
                </div>


            @endif
            </div>
    		</div>

           <!--  @yield('sic') -->



        </div>

		<!-- end news section -->
		@include('private.includes.backToTop')
		@include('private.footers.footer')
		<script language="javascript">
			$(document).foundation();
		</script>
	</body>
</html>
