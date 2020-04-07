<!doctype html>
<html class="no-js" lang="en">
<head>
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
    @include('private.headers.header')
    <link rel="stylesheet" type="text/css" href="/css/department/department.css">
</head>

<body id="{{$currentPage}}">
    @include('private.includes.topnav')
    <div class="row collapse mt10">

    	<!-- left Panel -->
        <div class="medium-4 large-3 columns side-bar-1 college-pages-navbar" style="margin: auto;" id="filter-search-div">
             <a href="{{{url()->previous()}}}" class="majors-back-btn">
                <span class="majors-back-arrow">&lsaquo;</span> Back
             </a>

             <div class="row" style="padding-right: 0.5em;">
               <div class="column small-12 side-bar-departments">
               <ul id="menu">
                    @foreach($all_departments_with_majors as $key => $value)
                    <li>
                    <div><a @if($selected==$value['slug']) class="active" @endif href="/college-majors/{{ $value['slug'] }}">{{ $key }}</a></div>
                       <?php $majors = $value['majors'] ?>
                       @if(isset($majors) && sizeof($majors) > 0)
                           <ul>
                            <li>
                                <div>
                                    <a href="/college-majors/{{ $value['slug'] }}">Overview</a>
                                </div>
                            </li>     
                           @foreach($majors as $major)
                               <li>
                                   <div>
                                       <a @if($selected_major==$major->slug) class="active" @endif href="/college-majors/{{$major->mdd_slug}}/{{$major->slug}}">{{$major->name}}</a>
                                   </div>
                               </li>
                           @endforeach
                           </ul>
                       @endif 
                   </li>
                   @endforeach
                </ul>
               </div>
             </div>
        </div>

        <!-- Right Side Part -->
        <div class="small-12 medium-8 large-9 columns">
            <div class="majors-tag">
                @include('private.college.collegeNav')
            </div>
            <div class="row">
                <div class="column small-12">
                    @yield('content')
                </div>
            </div>
        </div>
    </div>

    @include('private.footers.footer')
    <script>
        $( function() {
            $( "#menu" ).menu();
        } );
    </script>
    <style type="text/css">
        ul.ui-widget-content {
            width: unset;
            font-weight: unset;
        }
        .ui-widget-content {
            border: unset;
        }
        .ui-icon, .ui-widget-content .ui-icon {
            background-image: none;
        }
        .ui-menu .ui-menu {
            margin-left: 52px;
            padding: 18px;
            border-radius: 4px;
            left: 172px !important;
        }
        .ui-state-active, 
        .ui-widget-content .ui-state-active, 
        .ui-widget-header .ui-state-active{
            border: none;
            color: #40c553;
        }
        .department-content-div ul {
            padding-left: 10px;
        }
        .department-content-div ul li{
            list-style: disc !important;
            padding: 5px !important;
            margin: 5px !important;
            color: #79796A;
            font-family: "Century Gothic",CenturyGothic,AppleGothic,sans-serif;
        }
        .side-bar-departments a:hover,
        .side-bar-departments a:active,
        {
            color: #2ac56c;
        }
    </style>
    </body>
</html>
