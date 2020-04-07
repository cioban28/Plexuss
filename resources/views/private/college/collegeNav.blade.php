 <!-- college pages navigation -->
<div class="college-pages-navbar">
    <a href="/college" class="@if($currentPage == 'college-home') active @endif">
        Colleges
    </a>
    <a href="/college-majors"  class="@if($currentPage == 'department') active @endif">
        Majors
    </a>
    <a href="/scholarships"  class="@if($currentPage == 'scholarships') active @endif">
        Scholarships
    </a>
    <a href="/ranking"  class="@if($currentPage == 'ranking') active @endif">
        Ranking
    </a>
    <a href="/comparison"  class="@if($currentPage == 'comparison') active @endif">
        Compare Colleges
    </a>
    <!-- <a href="/news/catalog/college-essays"  class="@if($currentPage == 'essays') active @endif">
        College Essays
    </a> -->


</div>



<?php
    $page = '';
    switch($currentPage){
        case 'college-home':
            $page = 'Colleges';
            break;
        case 'department':
        case 'majors':
            $page = 'Majors';
            break;
        case 'ranking':
            $page = 'Ranking';
            break;
        case 'comparison':
            $page = 'Compare Colleges';
            break;
        case 'scholarships':
            $page = 'Scholarships';
            break;
        // case 'college-essays':
        //     $page = 'College Essays';
        //     break;
        default:
            break;
    }

?>
<div class="college-pages-navbar-mobile">
    <div class="page-listed">
        {{$page}}
        <div class="college-nav-arrow"></div>
    </div>

    <div class="college-nav-options">
        <a href="/college" class="@if($currentPage == 'college-home') active @endif">
            Colleges
        </a>


        <a href="/college-majors"  class="@if($currentPage == 'department') active @endif">
            Majors
        </a>

        <a href="/scholarships"  class="@if($currentPage == 'scholarships') active @endif">
            Scholarships
        </a>

        <a href="/ranking"  class="@if($currentPage == 'ranking') active @endif">
            Ranking
        </a>


        <a href="/comparison"  class="@if($currentPage == 'comparison') active @endif">
            Compare Colleges
        </a>


        <!-- <a href="/news/catalog/college-essays"  class="@if($currentPage == 'essays') active @endif">
            College Essays
        </a> -->
    </div>

</div>
