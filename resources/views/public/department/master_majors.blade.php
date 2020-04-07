<!doctype html>
<html class="no-js" lang="en">
<head>
    @include('private.headers.header')
	 <link rel="stylesheet" href="/css/college.css?8.07"/> 
</head>

<body id="{{$currentPage}}">
       @include('private.includes.topnav')
        <div class="row collapse mt10">

        	<div class="small-12 medium-12 large-12 columns">
                
                <div class="row">
                    <div class="column small-12">
                        @yield('content')
                    </div>
                </div>

            </div>

        </div>

        @include('private.footers.footer')

    </body>
</html>
