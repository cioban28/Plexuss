<meta charset="utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0"/>
<meta name="p:domain_verify" content="8edfc77043263d3582f53df83941d89f"/>
<link href="https://diegoddox.github.io/react-redux-toastr/4.0/react-redux-toastr.min.css" rel="stylesheet" type="text/css">
@if ($currentPage == 'college' && isset($school_logo) && isset($college_data))
<meta property="og:image" content="{{$school_logo}}{{$college_data->logo_url}}" />
@elseif($currentPage == 'news' && isset($school_logo) && isset($news_details->img_sm))
<meta property="og:image" content="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$news_details->img_sm}}" />
@else
<meta property="og:image" content="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/images/fb_share_images/plexuss-app-icon-200.png" />
@endif
<meta property="og:image" content="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/images/fb_share_images/Page+1.png"/>
<meta property="og:image" content="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/images/fb_share_images/Page+2.png"/>
<meta property="og:image" content="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/images/fb_share_images/Page+3.png"/>
<meta property="og:image:type" content="image/png" />
<link rel="stylesheet" type="text/css" href="/daterangepicker/daterangepicker-bs2.css" media="all"/>
<link href="/css/jqvmap.min.css" media="screen" rel="stylesheet" type="text/css"/>
<link rel="stylesheet" href="/css/remodal.css">
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.8.1/css/all.css" integrity="sha384-50oBUHEmvpQ+1lW4y57PTFmhCaXp0ML5d60M1M7uH2+nqUivzIebhndOJK28anvf" crossorigin="anonymous">
@if (isset($signed_in) && $signed_in == 1)
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
    <script src="/js/tinycon.min.js"></script>
    <script src="/js/desktopNotifications.js?7"></script>
    <script src="/js/notification.js?v=1.00"></script>
@endif
<script src="https://js.stripe.com/v3/"></script>
<script src="https://cdn.onesignal.com/sdks/OneSignalSDK.js" async=""></script>
<script>
    var OneSignal = window.OneSignal || [];
    OneSignal.push(function() {
        OneSignal.init({
            appId: "74c58f77-8cd7-47ae-ba9c-bf79dd86b3bc",
        });
    });
</script>
<meta name="csrf-token" content="{{ csrf_token() }}">
    @if ($currentPage == 'admin-Products')
        <title> Products | Plexuss.com</title>
        <link rel="stylesheet" href="/css/products.css"/>
        <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400" rel="stylesheet">
        <!--link href="https://fonts.googleapis.com/css?family=Roboto:300,400" rel="stylesheet"-->
    @endif
    @if ($currentPage == 'college')
        @if(isset($noindex) && $noindex == 1)
        <meta name="robots" content="noindex">
        @endif
        <title>{{ $college_data->page_title or ''}}</title>
        <meta name="description" content="{{$college_data->meta_description or ''}}">
        <meta name="keywords" content="{{$college_data->meta_keywords or ''}}">
        <link rel="stylesheet" href="/css/prod_ready/frontpage_all.min.css?v=2" />

        @if (isset($Section) && $Section == 'overview')
            <link rel="canonical" href="https://plexuss.com/college/{{$college_data->slug}}">
        @endif
    @endif

     @if ($currentPage == 'scholarships')
        <title>Find Scholarships | College Recruiting Network | Plexuss.com</title>
        <meta name="description" content="Search for scholarships offered">
        <meta name="keywords" content="scholarships, college scholarships">

        <link rel="stylesheet" href="/css/scholarships.css?v=1.01" />
    @endif

     @if ($currentPage == 'department')
        <title>{{ $metainfo->meta_title or ''}}</title>
        <meta name="description" content="{{ $metainfo->meta_description or ''}}">
        <meta name="keywords" content="department">
    @endif

    @if ($currentPage == 'home')
        <title>College Recruiting Academic Network | Plexuss.com</title>
        <meta name="description" content="The Plexuss College Recruiting Academic Network specializes in high school student recruitng by America's colleges and universities. Join our recruiting network to connect with all colleges and universities in the US. ">
        <meta name="keywords" content="college search">

        <link rel="stylesheet" href="/css/home.css?8"/>
        <link rel="stylesheet" href="/css/gettingStartedPins.css?8"/>
        <link rel="stylesheet" href="/css/smartInteractiveColumn.css?8" />
    @endif

    @if ($currentPage == 'profile')
        <title>College Planning Profile | College Recruiting Network | Plexuss.com</title>
        <meta name="description" content="Welcome to your college planning profile - Complete 30 percent of your profile and get discovered and recruited by colleges. Include your accomplishments, awards and extracurricular activities. Only on Plexuss.com">
        <meta name="keywords" content="College planning profile">
        <link href="../dropzone/css/dropzone.css?8" rel="stylesheet"/>
        <link rel="stylesheet" href="/css/profile.css?8"/>
        <link rel="stylesheet" href="/css/me.css?8"/>
        <link href="/css/profile_score.css?8" rel="stylesheet">
        <link rel="stylesheet" href="/css/smartInteractiveColumn.css"/>
    @endif

    @if ($currentPage == 'college' || $currentPage == 'battle')
        <link rel="stylesheet" href="/css/search.css?8"/>
        <link rel="stylesheet" href="/css/college.css?8.07"/>
        <link rel="stylesheet" href="/css/base.css?8.01" />
        <link rel="stylesheet" href="/css/comments.css"/>
        <link rel="stylesheet" href="/css/comments.css"/>
    @endif

    @if ($currentPage == 'newschlorshippage')
        <link rel="stylesheet" href="/css/search.css?8"/>
        <link rel="stylesheet" href="/css/newschlorship.css"/>
        <link rel="stylesheet" href="/css/base.css?8.01" />
  @endif


    @if ($currentPage == 'comparison')
        <title>Compare College | College Recruiting Academic Network | Plexuss.com</title>
        <meta name="description" content="Compare colleges and academic stats of more than 4,000 colleges and universities. Find and compare schools by ranking, learn more about admissions, SAT, tuition, acceptance rate and cost.">
        <meta name="keywords" content="Compare colleges">
        <link rel="stylesheet" href="/css/comparison.css?8"/>
        <link rel="stylesheet" href="/css/smartInteractiveColumn.css"/>
    @endif

    @if ($currentPage == 'college-home')
        <title>College Search Engine | College Recruiting Network | Plexuss.com</title>
        <meta name="description" content="How do you find the right college for you? Use our free college search engine to find and compare colleges and universities on Plexuss.com.">
        <meta name="keywords" content="college search engine">
        <link rel="stylesheet" href="/css/search.css?8"/>
        <link rel="stylesheet" href="/css/college.css?8.07"/>
        <link rel="stylesheet" href="/css/base.css?8.01" />
        <link rel="stylesheet" href="/css/smartInteractiveColumn.css"/>
    @endif
    @if ($currentPage == 'majors')
        <title>College Search Engine | College Recruiting Network | Plexuss.com</title>
        <meta name="description" content="How do you find the right college for you? Search for colleges by major on Plexuss.com.">
        <meta name="keywords" content="college search engine">
        <link rel="stylesheet" href="/css/search.css?8"/>
        <link rel="stylesheet" href="/css/college.css?8.07"/>
        <link rel="stylesheet" href="/css/base.css?8.01" />
        <link rel="stylesheet" href="/css/smartInteractiveColumn.css"/>
    @endif

    @if( $currentPage == 'carepackage' )
        <title>College Care Package | College Recruiting Network | Plexuss.com</title>
        <link rel="stylesheet" href="/css/carepackage.css?6"/>
    @endif

    @if ($currentPage == 'privacy-policy')
        <title>{{$title or ''}}</title>
    @endif

    @if ($currentPage == 'setting')
        <script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
        <script type="text/javascript" src="/js/jquery.tooltipster.min.js"></script>
        <script type="text/javascript" src="https://js.stripe.com/v2/"></script>
        <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/jquery.dataTables.min.css?8">
        <link rel="stylesheet" href="/css/setting.css?8"/>
        <link rel="stylesheet" href="/css/tooltipster.css"/>
        <script type="text/javascript">
            jQuery(document).ready(function($) {

                var title =  '<table><thead><th colspan="3">User Roles</th></thead><tbody><tr><td></td><td>College Admin</td><td>College User</td></tr>';
                title += '<tr><td>Add/remove users</td><td><img src="/images/admin/check-green.png"/></td><td></td></tr>';
                title += '<tr><td>Manage user permissions</td><td><img src="/images/admin/check-green.png"/></td><td></td></tr>';
                title += '<tr><td>Manage all portals</td><td><img src="/images/admin/check-green.png"/></td><td></td></tr>';
                title += '<tr><td>Manage targeting</td><td><img src="/images/admin/check-green.png"/></td><td><img src="/images/admin/check-green.png"/></td></tr>';
                title += '<tr><td>Manage own portal</td><td><img src="/images/admin/check-green.png"/></td><td><img src="/images/admin/check-green.png"/></td></tr>';
                title += '</tbody></table>';

                $('.mytooltip').tooltipster({
                    content: $(title),
                    interactive: true
                });

                $('.user-access-tooltip').tooltipster({
                    theme: 'tooltipster-noir',
                    interactive: true,
                    contentAsHTML: true
                });
            });
        </script>
        @if(env('ENVIRONMENT') == 'LIVE')
        <script type="text/javascript">
            Stripe.setPublishableKey('pk_live_8To1rx24ZEQgXTUQEUMeRO6S');
        </script>
        @else
        <script type="text/javascript">
            Stripe.setPublishableKey('pk_test_18eNpd13Ckvy0UyZSgVWgp1E');
        </script>
        @endif
    @endif

    @if ($currentPage == 'portal')
        <title>College List | College Recruiting Academic Network | Plexuss.com</title>
        <meta name="description" content="Looking for a comlete college list? Plexuss College Portal empowers students to manage communication with colleges.  Select colleges that you want to engage with, view recommendations and see which colleges have viewed your profile.">
        <meta name="keywords" content="College list">
        <!--<link rel="stylesheet" href="/js/fullcalender/fullcalendar.css?8"/>
        <link rel="stylesheet" href="/js/zurb_datatable/dataTables.foundation.css?8"/>-->
        <link rel="stylesheet" href="/css/enjoyhint/enjoyhint.css"/>
        <link rel="stylesheet" href="/css/portal.css?8"/>
        <link rel="stylesheet" href="/css/scholarships.css?v=1.00"/>
        <link rel="stylesheet" href="/css/messagesChat.css"/>
        <link rel="stylesheet" href="/css/userPortal_main.css"/>
        <link rel="stylesheet" href="/css/smartInteractiveColumn.css?"/>
        <!-- return to minified version when going live -->
        <!--<link rel="stylesheet" href="/css/prod_ready/portal_all.min.css"/>-->
        <!--<link rel="stylesheet" href="/css/prod_ready/userPortal_main.min.css"/>-->
       <!-- <link rel="stylesheet" href="/js/zurb_datatable/jquery.dataTables.min.css?8"/>-->
       <!-- for messaging on portal - remove once this page is done in react/redux -->
       <script type="text/javascript">
            var socket_script = document.createElement('script'),
                path = window.location.origin+':3001';
            // console.log('here: ', path);
            socket_script.setAttribute('src', path+'/socket.io/socket.io.js');
            // console.log('and here: ', socket_script);
            document.head.appendChild(socket_script);
       </script>
    @endif

    @if( $currentPage == 'notifications' )
        <link rel='stylesheet' href='/css/notifications.css'>
    @endif

    @if($currentPage == 'college-essays')
         <title>College Essays | Plexuss.com</title>
                <meta name="description" content="College Essays">
                <meta name="keywords" content="admitsee college essays essay">
                <link rel="stylesheet" href="/css/admitsee.css?8" />
                <link rel="stylesheet" href="/css/news.css?8.0"/>
                <link rel="stylesheet" href="/css/comments.css"/>

                <link rel="stylesheet" href="/css/smartInteractiveColumn.css" />
                <!-- for the signup modal -->
                <link rel="stylesheet" type="text/css" href="/css/prod_ready/signupin_all.min.css" />
    @endif


    @if($currentPage == 'quad-testimonials')
         <title>Student testimonials | Plexuss.com</title>
                <meta name="description" content="Student Testimonials">
                <meta name="keywords" content="student testimonials">

                <link rel="stylesheet" href="/css/news.css?8.0"/>
                <link rel="stylesheet" href="/css/comments.css"/>

                <link rel="stylesheet" href="/css/smartInteractiveColumn.css" />
    @endif


    @if ($currentPage == 'news')
        @if(isset($catName))
            @if ($catName == 'celebrity-alma-mater')
                <title>Celebrities College Degrees | College Recruiting Academic Network | Plexuss.com</title>
                <meta name="description" content="Learn more about your favorite celebrities with college degrees and their Alma Mater. See the universities these famous people attended at Plexuss.com.">
                <meta name="keywords" content="celebrities with college degrees">
                <link rel="stylesheet" href="/css/news.css?8.0"/>
            @elseif  ($catName == 'college-news')
                <title>College News | College Recruiting Academic Network | Plexuss.com</title>
                <meta name="description" content="Get college news straight from colleges and universities including ranking, financial aid, admissions, college life and much more.">
                <meta name="keywords" content="college news">
                <link rel="stylesheet" href="/css/news.css?8.0"/>
            @elseif ($catName == 'college-sports')
                <title>College Basketball News | College Recruiting Academic Network | Plexuss.com</title>
                <meta name="description" content="Find the latest college sporting news, including college baseball, college football, college basketball and other college sports at Plexuss.com">
                <meta name="keywords" content="college basketball news">
                <link rel="stylesheet" href="/css/news.css?8.0"/>
            @elseif ($catName == 'campus-life')
                <title>Campus Life | College Recruiting Academic Network | Plexuss.com</title>
                <meta name="description" content="Guide to Campus Life! Find campus life news on Plexuss.com: getting involved in college, housing, and health.">
                <meta name="keywords" content="campus life">
                <link rel="stylesheet" href="/css/news.css?8.0"/>
            @elseif ($catName == 'paying-for-college')
                <title>Paying for College | College Recruiting Network | Plexuss.com</title>
                <meta name="description" content="Not sure how you will pay for college? Get information on paying for college news including financial aid. Up-to-date information straight from every university in the US available on Plexuss.com.">
                <meta name="keywords" content="paying for college">
                <link rel="stylesheet" href="/css/news.css?8.0"/>
            @elseif ($catName == 'financial-aid')
                <title>College Financial Aid News | College Recruiting Network | Plexuss.com</title>
                <meta name="description" content="Get information on college financial aid and learn the ways to pay for college on the Plexuss paying for college page.">
                <meta name="keywords" content="college financial aid">
                <link rel="stylesheet" href="/css/news.css?8.0"/>
            @elseif ($catName == 'life-after-college')
                <title>Life After College | College Recruiting Academic Network | Plexuss.com</title>
                <meta name="description" content="The Plexuss life after college news page features practical, actionable news that helps you -- the student -- focus on what to do after college based on the college fo your choice.">
                <meta name="keywords" content="life after college ">
                <link rel="stylesheet" href="/css/news.css?8.0"/>
            @elseif ($catName == 'getting-into-college')
                <title>How to Get into College | College Recruiting Network | Plexuss.com</title>
                <meta name="description" content="Learn how to get into college on Plexuss with actionable lists that help you -- the student -- focus on how to get into college and enjoy your college education.  Get recruited on Plexuss.com today!">
                <meta name="keywords" content="how to get into college">
                <link rel="stylesheet" href="/css/news.css?8.0"/>

             @elseif ($catName == 'careers')
                <title>Career Search News | College Recruiting Academic Network | Plexuss.com</title>
                <meta name="description" content="Career search news and practical, actionable information and updates that help you -- the student -- focus on college education and which university to choose. ">
                <meta name="keywords" content="career search">
                <link rel="stylesheet" href="/css/news.css?8.0"/>

             @elseif ($catName == 'ranking')
                <title>College Ranking News | College Recruiting Academic Network | Plexuss.com</title>
                <meta name="description" content="Find college ranking news on Plexuss.com. Discover blogs, news, and community conversations about different college rankings from around the US.">
                <meta name="keywords" content="college ranking">
                 <link rel="stylesheet" href="/css/news.css?8.0"/>
            @else
                <title>State College News | College Recruiting Academic Network | Plexuss.com</title>
                <meta name="description" content="Get out-of-state and in state college news straight from colleges and universities including ranking, financial aid, admissions, college life and much more on Plexuss.com.">
                <meta name="keywords" content="State college news">
                <link rel="stylesheet" href="/css/news.css?8.0"/>
            @endif
        @else
            <title>{{$news_details['attributes']['meta_title'] or 'Plexuss News Detail'}}</title>
            <meta name="title" content="{{$news_details['attributes']['meta_title'] or 'Plexuss News Detail'}}">
            <meta name="keywords" content="{{$news_details['attributes']['meta_keywords'] or ''}}">
            <meta name="description" content="{{$news_details['attributes']['meta_descrip'] or ''}}">
            <link rel="stylesheet" href="/css/news.css?8.0"/>
            <link rel="stylesheet" href="/css/comments.css"/>
        @endif

        <link rel="stylesheet" href="/css/smartInteractiveColumn.css" />
    @endif

    @if ($currentPage == 'ranking')
        @if (isset($meta_keyword))
            <title>{{$title or ''}}</title>
            <meta name="description" content="{{$meta_desc or ''}}">
            <meta name="keywords" content="{{$meta_keyword or ''}}">
        @else
            <title>College Ranking News | College Recruiting Academic Network | Plexuss.com</title>
            <meta name="description" content="Find college ranking news on Plexuss.com. Discover blogs, news, and community conversations about different college rankings from around the US.">
            <meta name="keywords" content="college ranking">
        @endif
        <link rel="stylesheet" href="/css/ranking.css?8"/>
        <link rel="stylesheet" href="/css/smartInteractiveColumn.css"/>
    @endif

    @if ($currentPage == 'careers')
        <title>Career Search News | College Recruiting Academic Network | Plexuss.com</title>
        <meta name="description" content="Career search news and practical, actionable information and updates that help you -- the student -- focus on college education and which university to choose. ">
        <meta name="keywords" content="career search">
        <link rel="stylesheet" href="/css/news.css?8.0"/>
    @endif

    @if ($currentPage == 'search')
        <title>College Search Engine | College Recruiting Network | Plexuss.com</title>
        <meta name="description" content="How do you find the right college for you? Use our free college search engine to find and compare colleges and universities on Plexuss.com.">
        <meta name="keywords" content="College search engine">
        <link rel="stylesheet" href="/css/search.css?8"/>
    @endif

    @if ($currentPage == 'colleges-by-state')
        <title>{{$title}}</title>
        <meta name="description" content="{{$meta_desc}}">
        <meta name="keywords" content="{{$meta_keyword}}">
        <link rel="stylesheet" href="/css/search.css?8"/>
        <link rel="stylesheet" href="/css/collegesByState.css?8"/>

    @endif

    @if ($currentPage == 'college-submission')
        <title>College Matchmaker Recruiting Network | Join as a College | Plexuss.com</title>
        <meta name="description" content="Join Plexuss -- a college matchmaker service. Services to colleges include: chatting with students, managing your college page, reporting & analytics, recruiting students. Learn more at Plexuss.com">
        <meta name="keywords" content="college matchmaker">
    @elseif ($currentPage == 'college-prep')
        <title>College Matchmaker Website Network | Join as College Prep | Plexuss.com</title>
        <meta name="description" content="Join Plexuss -- a college matchmaker website. Services to colleges include: chatting with students, managing your college page, reporting & analytics, recruiting students. Learn more at Plexuss.com">
        <meta name="keywords" content="college matchmaker website">
    @elseif ($currentPage == 'scholarship-submission')
        <title>Submit Scholarships for College Students | Recruiting Network | Plexuss.com</title>
        <meta name="description" content="Offering scholarships for college students? Please fill out the scholarship submission form. Plexuss will help you find students to meet your scholarship requirements.">
        <meta name="keywords" content="scholarships for college students">
    @elseif ($currentPage == 'about')
        <title>College 411 | About | College Recruiting Academic Network | Plexuss.com</title>
        <meta name="description" content="Get the college 411 at Plexuss -- a college recruiting network that has the complete guide to 4,000 American colleges and Universities. Find out information such as school history, campus setting, and academic calendar at college recruiting network. Learn more about us on Plexuss.com.">
        <meta name="keywords" content="college 411">
     @elseif ($currentPage == 'contact')
        <title>College 411 | About | College Recruiting Academic Network | Plexuss.com</title>
        <meta name="description" content="Get the college 411 at Plexuss -- a college recruiting network that has the complete guide to 4,000 American colleges and Universities. Find out information such as school history, campus setting, and academic calendar at college recruiting network. Learn more about us on Plexuss.com.">
        <meta name="keywords" content="college 411">
    @elseif ($currentPage == 'team')
        <title>Plexuss Team | College Recruiting Academic Network | Plexuss.com</title>
        <meta name="description" content="Plexuss is a college recruiting network that has complete guide to over 4,000 colleges and Universities. Meet the team behind the scene and learn more about getting recruited for colleges at Plexuss.com. ">
        <meta name="keywords" content="Plexuss">
    @elseif ($currentPage == 'careers-internships')
        <title>Career & Internship Search | College Recruiting Academic Network | Plexuss.com</title>
        <meta name="description" content="Career and internship search and opportunities at Plexuss.com, a college recruiting network that has the complete guide to over 4,000 American colleges and Universities. Find information such as school history and campus setting at college recruiting network.">
        <meta name="keywords" content="internship search">
    @elseif ($currentPage == 'help')
        <title>College Recruiting Academic Network | Help FAQ | Plexuss.com</title>
        <meta name="description" content="Plexuss.com is a college recruiting network that has the complete guide to over 4,000 colleges and Universities. Find out more about school history, campus setting academic calendar of colleges at college recruiting network. Learn more on Help FAQ page.">
        <meta name="keywords" content="college recruiting">
    @endif
    @if ($currentPage == 'admin')
        <title>College Admin Dashboard | Student Recruitment | Plexuss.com</title>
        <!--<link rel="stylesheet" type="text/css" href="/css/dataTables.foundation.css?8">
        <link rel="stylesheet" type="text/css" href="/css/adminDataTables.css?8">
        <link rel="stylesheet" href="/css/jquery.nouislider.min.css?8">
        <link rel="stylesheet" type="text/css" href="/css/wan-spinner.css?8">-->
        <link rel="stylesheet" type="text/css" href="/css/adminDash.css?8">
        <link rel="stylesheet" type="text/css" href="/css/adminContentManagement.css?8">
        <link href="https://diegoddox.github.io/react-redux-toastr/4.0/react-redux-toastr.min.css" rel="stylesheet" type="text/css">
        <script src="https://cdn.tinymce.com/4/tinymce.min.js"></script>
    @endif
    @if ($currentPage == 'admin-messages')
        <title>Student Messages | College Recruitment | Plexuss.com</title>
        <link rel="stylesheet" type="text/css" href="/css/dataTables.foundation.css?8">
        <link rel="stylesheet" type="text/css" href="/css/adminDataTables.css?8">
        <link rel="stylesheet" type="text/css" href="/css/admin.css?8">
        <link rel="stylesheet" type="text/css" href="/css/editMessageTemplate.css?8">
        <link rel="stylesheet" type="text/css" href="/css/collegeMessages.css"/>
        <script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
        <script>
            tinymce.init({
                selector:'#editMsgTemplate-editor',
                imagetools_cors_hosts: ['www.tinymce.com', 'codepen.io'],
                plugins:  ['link code'],
                setup: function(editor){
                    editor.on('change', function(e){
                        Plex.msgTemplates.selectedTemplate.edit('content', editor.getContent());
                    });
                    editor.on('keyup', function(){
                        Plex.msgTemplates.selectedTemplate.edit('content', editor.getContent());
                    });
                }
            });
        </script>
    @endif
    @if ($currentPage == 'admin-chat')
        <title>Chat with Students | College Recruitment | Plexuss.com</title>
        <link rel="stylesheet" type="text/css" href="/css/admin.css?8">
        <link rel="stylesheet" type="text/css" href="/css/editMessageTemplate.css?8">
        <link rel="stylesheet" type="text/css" href="/css/collegeMessages.css"/>
        <script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
        <script>
            tinymce.init({
                selector:'#editMsgTemplate-editor',
                imagetools_cors_hosts: ['www.tinymce.com', 'codepen.io'],
                plugins:  ['link code'],
                setup: function(editor){
                    editor.on('change', function(e){
                        Plex.msgTemplates.selectedTemplate.edit('content', editor.getContent());
                    });
                    editor.on('keyup', function(){
                        Plex.msgTemplates.selectedTemplate.edit('content', editor.getContent());
                    });
                }
            });
        </script>
    @endif
    @if ($currentPage == 'admin-inquiries' || $currentPage == 'admin-pending' || $currentPage == 'admin-approved' || $currentPage == 'admin-recommendations' || $currentPage == 'admin-removed' || $currentPage == 'admin-rejected' || $currentPage == 'admin-prescreened'  || $currentPage == 'admin-verifiedHs'  || $currentPage == 'admin-verifiedApp' || $currentPage == 'admin-converted')
        <title>{{$title or ''}} | College Recruitment | Plexuss.com</title>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        {{-- <link rel="stylesheet" type="text/css"  href="/css/360player/360player.css"> --}}
        {{-- <link rel="stylesheet" type="text/css"  href="/css/360player/360player-visualization.css"> --}}
        <link rel="stylesheet" type="text/css"  href="/css/mp3-player-button.css">
        <link rel="stylesheet" type="text/css" href="/css/editMessageTemplate.css?8">
        <!-- <link rel="stylesheet" type="text/css"  href="/css/flashblock.css"> -->
        {{-- <link rel="stylesheet" type="text/css"  href="/css/flashblock.css"> --}}
        <link rel="stylesheet" type="text/css"  href="/css/audioBar/bar-ui.css">
        <link rel="stylesheet" type="text/css" href="/css/admin.css?8">
        <link rel="stylesheet" type="text/css" href="/css/adminManageStudents.css?8">
        <link rel="stylesheet" type="text/css" href="/css/contact.css?8.01">
        <link rel="stylesheet" type="text/css" href="/css/intlTelInput.css?8">
        <link rel="stylesheet" type="text/css" href="/css/editMessageTemplate.css?8">
        <link rel="stylesheet" href="/css/jquery.nouislider.min.css?8">
        <link rel="stylesheet" type="text/css"  href="/css/motion-ui.css">
        <script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.0.4/socket.io.slim.js"></script>
        <script>
            tinymce.init({
                selector:'#editMsgTemplate-editor',
                imagetools_cors_hosts: ['www.tinymce.com', 'codepen.io'],
                plugins:  ['link code'],
                setup: function (editor) {
                    editor.on('change', function(e){
                        Plex.msgTemplates.selectedTemplate.edit('content', editor.getContent());
                    });
                    editor.on('keyup', function(){
                        Plex.msgTemplates.selectedTemplate.edit('content', editor.getContent());
                    });
                    editor.on('click', function(){
                        console.log('clicked!');
                    });
                }
            });
        </script>
        <script type="text/javascript" src="//media.twiliocdn.com/sdk/js/client/v1.3/twilio.min.js"></script>
    @endif
    @if( $currentPage == 'admin-adv-filtering' || $currentPage == 'admin-cms-filtering')
        <title>Recommendation Filters | Student Recruitment | Plexuss.com</title>
        <link rel="stylesheet" type="text/css" href="/css/adminAdvFiltering.css?8">
    @endif
    @if( $currentPage == 'agency-search' )
        <title>Agency Search | College Recruiting Academic Network | Plexuss.com</title>
        <link rel="stylesheet" type="text/css" href="/css/agencySearch.css?8">
    @endif
    @if( $currentPage == 'agency-profile' )
        <title>Agency Profile | College Recruiting Academic Network | Plexuss.com</title>
        <link rel="stylesheet" type="text/css" href="/css/agencyProfile.css?8">
    @endif
    @if( $currentPage == 'agency-adv-filtering' )
        <title>College Recruiting Academic Network | {{$title or 'Filter'}} | Plexuss.com</title>
        <link rel="stylesheet" type="text/css" href="/css/agencyAdvFiltering.css?8">
    @endif
    @if ($currentPage == 'agency')
        <title>Agency Dashboard | College Recruiting Academic Network | Plexuss.com</title>
        <link rel="stylesheet" type="text/css" href="/css/dataTables.foundation.css?8">
        <link rel="stylesheet" type="text/css" href="/css/adminDataTables.css?8">
        <link rel="stylesheet" type="text/css" href="/css/agencyDash.css?8">
    @endif
    @if ($currentPage == 'agency-reporting')
        <title>Agency Reporting | College Recruiting Academic Network | Plexuss.com</title>
        <link rel="stylesheet" type="text/css" href="/css/agencyReporting.css?8">
    @endif
    @if ($currentPage == 'agency-video-tutorial')
        <title>Agency Video Tutorial | College Recruiting Academic Network | Plexuss.com</title>
        <link rel="stylesheet" type="text/css" href="/css/agencyVideoTutorial.css?8">
    @endif
    @if ($currentPage == 'agency-messages')
        <link rel="stylesheet" type="text/css" href="/css/dataTables.foundation.css?8">
        <link rel="stylesheet" type="text/css" href="/css/adminDataTables.css?8">
        <link rel="stylesheet" type="text/css" href="/css/admin.css?8">
    @endif
    @if( $currentPage == 'agency-settings' )
        <link rel="stylesheet" type="text/css" href="/css/agencySettings.css?8">
    @endif
    @if( $currentPage == 'admin-content-management' )
        <title>College Recruiting Academic Network | {{$title or 'Content Management'}} | Plexuss.com</title>
        <link rel="stylesheet" type="text/css" href="/css/adminContentManagement.css?8">
    @endif
    @if($currentPage == 'agency-inquiries' || $currentPage == 'agency-recommendations' || $currentPage == 'agency-pending' || $currentPage == 'agency-approved' || $currentPage == 'agency-removed' || $currentPage == 'agency-rejected' || $currentPage == 'agency-leads' || $currentPage == 'agency-opportunities' || $currentPage == 'agency-applications' )
        <title>{{ucfirst(explode('-', $currentPage)[1])}} | Internatonal Agents Inquiries | Plexuss.com</title>

        <link rel="stylesheet" type="text/css" href="/css/agency.css?8">
        <link rel="stylesheet" type="text/css" href="/css/agencyManageStudents.css?8">
        <link rel="stylesheet" type="text/css" href="/css/agencyStudentProfile.css?8">
        <link rel="stylesheet" type="text/css" href="/css/agencyInquiries.css?8">

        <link rel="stylesheet" type="text/css" href="/css/contact.css?8.01">

        <link rel="stylesheet" type="text/css"  href="/css/mp3-player-button.css?8">
        <link rel="stylesheet" type="text/css"  href="/css/audioBar/bar-ui.css?8">
    @endif
    @if( $currentPage == 'agency-approval' )
        <link rel="stylesheet" type="text/css" href="/css/agencyApproval.css?8">
    @endif
    @if( $currentPage == 'admin-student-search' || $currentPage == 'agency-student-search' )
        <title>Advanced Search | Student Recruitment | Plexuss.com</title>
        <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
        {{-- <link rel="stylesheet" type="text/css"  href="/css/360player/360player.css"> --}}
        {{-- <link rel="stylesheet" type="text/css"  href="/css/360player/360player-visualization.css"> --}}
        <link rel="stylesheet" type="text/css"  href="/css/mp3-player-button.css">
        {{-- <link rel="stylesheet" type="text/css"  href="/css/flashblock.css"> --}}
        <link rel="stylesheet" type="text/css"  href="/css/audioBar/bar-ui.css">
        <link rel="stylesheet" type="text/css" href="/css/advancedStudentSearch.css?8">
        <link rel="stylesheet" type="text/css" href="/css/advancedStudentSearchProfile.css?8">
        <link rel="stylesheet" type="text/css" href="/css/contact.css?8.01">
        <script type="text/javascript" src="//media.twiliocdn.com/sdk/js/client/v1.3/twilio.min.js"></script>
    @endif
    @if( $currentPage == 'agency-groupmsg' || $currentPage == 'admin-groupmsg' || $currentPage == 'admin-textmsg')
        <title>Student Mass Messaging | College Recruitment | Plexuss.com</title>
        <link rel="stylesheet" type="text/css" href="/css/groupMessaging.css?8">
        <link rel="stylesheet" type="text/css" href="/css/editMessageTemplate.css?8">
        <link rel="stylesheet" type="text/css" href="/css/adminManageStudents.css?8">
        <script src="//cdn.tinymce.com/4/tinymce.min.js"></script>
        <script>
            tinymce.init({
                selector:'#textarea-editor',
                imagetools_cors_hosts: ['www.tinymce.com', 'codepen.io'],
                plugins:  ['link code'],
                setup: function(editor){
                    editor.on('change', function(e){
                        Plex.gM.update(editor.getContent(), 'setBody');
                    });
                    editor.on('keyup', function(){
                        Plex.gM.update(editor.getContent(), 'setBody');
                        var curChar = editor.getContent({format : 'text'}).length;
                        var curCharLeft = 161 - curChar;
                        var camp_btn = $('.camp-textarea div.send-campaign-btn.camp-btn');
                        $('.content-options-row .textCnt').html(curCharLeft);
                        var curPage = $('.main-content-container').data('currentpage');
                        if(curCharLeft < 0 && curPage == 'admin-textmsg') {
                            camp_btn.css({
                                'pointer-events': 'none',
                                'background-color': '#FF8D67'
                            });
                        } else {
                            camp_btn.css({
                                'pointer-events': 'all',
                                'background-color': '#FF5C26'
                            });
                        }
                    });
                }
            });
        </script>
        <script>
            tinymce.init({
                selector:'#editMsgTemplate-editor',
                imagetools_cors_hosts: ['www.tinymce.com', 'codepen.io'],
                plugins:  ['link code'],
                setup: function(editor){
                    editor.on('change', function(e){
                        Plex.msgTemplates.selectedTemplate.edit('content', editor.getContent());
                    });
                    editor.on('keyup', function(){
                        Plex.msgTemplates.selectedTemplate.edit('content', editor.getContent());
                    });
                }
            });
        </script>
    @endif
    @if ($currentPage == 'webinar')
        <link rel="stylesheet" type="text/css" href="/css/webinar.css?8">
    @endif
    @if( $currentPage == 'sales' || $currentPage == 'sales-messages' || $currentPage == 'sales-billing')
        <title>Plexuss Sales Central Control | College Recruiting Network | Plexuss.com</title>
        <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/dataTables.colReorder.min.css?8">
        <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/dataTables.colvis.jqueryui.css?8">
        <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/dataTables.colVis.min.css?8">
        <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/dataTables.fixedHeader.min.css?8">
        <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/dataTables.fixedColumns.min.css?8">
        <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/dataTables.keyTable.min.css?8">
        <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/dataTables.responsive.css?8">
        <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/dataTables.scroller.min.css?8">
        <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/jquery.dataTables.min.css?8">
        <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/jquery.dataTables_themeroller.css?8">
        <link rel="stylesheet" type="text/css" href="/css/salesCentralControl.css?8">
    @elseif( $currentPage == 'sales-pick-a-college' )
        <link rel="stylesheet" type="text/css" href="/css/salesCentralControl.css?8">
    @elseif($currentPage =='sales-tracking')
        <title>Plexuss Sales Central Control | College Recruiting Network | Plexuss.com</title>
       <link rel="stylesheet" type="text/css" href="/css/salesHeader.css?8">
       <script>
           let active_page_sales = "Tracking";
           let sub_page = 'Tracking';
       </script>
    @elseif($currentPage == 'sales-site-performance' || $currentPage == 'sales-clientReporting' || $currentPage == 'sales-email-reporting' || $currentPage == 'sales-device-os-reporting')
        <title>Plexuss Sales Central Control | College Recruiting Network | Plexuss.com</title>
        <!-- Compressed CSS -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/foundation-sites@6.5.0-rc.3/dist/css/foundation.min.css" integrity="sha256-b2khkeAav/7kTh0Bs5h1Xw1kqGL56SziJ5zk6bEvnAw= sha384-7nP0F9FVCI9Qg1SfsjHWQd+4ksCAxlF5pibRyPGxwn7NJpu1XuSaOoMh8JHIDSdk sha512-Rcgo7Zj9clxZoGtt4CBj1aEtCL9gBd64nYl3hkKEuWDwtK7hKY6c4D5vL4njDseuz31u1WWSM42SbvYe/3CZYQ==" crossorigin="anonymous">
<link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/dataTables.colReorder.min.css?8">
    <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/dataTables.colvis.jqueryui.css?8">
    <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/dataTables.colVis.min.css?8">
    <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/dataTables.fixedHeader.min.css?8">
    <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/dataTables.fixedColumns.min.css?8">
    <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/dataTables.keyTable.min.css?8">
    <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/dataTables.responsive.css?8">
    <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/dataTables.scroller.min.css?8">
    <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/jquery.dataTables.min.css?8">
    <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/jquery.dataTables_themeroller.css?8">
    <link rel="stylesheet" type="text/css" href="/css/salesCentralControl.css?8">

        <link rel="stylesheet" type="text/css" href="/css/dataTables.foundation.css?8">
       <link rel="stylesheet" type="text/css" href="/css/salesHeader.css?8">
       <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.7.2/css/all.css" integrity="sha384-fnmOCqbTlWIlj8LyTjo7mOUStjsKC4pOpQbqyi7RrhN7udi9RwhKkMHpvLbHG9Sr" crossorigin="anonymous">
       <script src="{{asset('js/emailReporting/sorttable.js')}}"></script>
       @if($currentPage =='sales-site-performance')
            <script>
                let active_page_sales = 'Reporting';
                let sub_page = 'Site Performance';
           </script>
       @endif
       @if($currentPage =='sales-device-os-reporting')
            <script>
                let active_page_sales = 'Reporting';
                let sub_page = "Device & OS Reporting";
           </script>
       @endif
        @if($currentPage == 'sales-clientReporting')
            <script>
                let active_page_sales = 'Reporting';
                let sub_page = 'Client Reporting'
            </script>
        @endif
        @if($currentPage == 'sales-email-reporting')
            <script>
                let active_page_sales = 'Reporting';
                let sub_page = 'Email Reporting'
            </script>
        @endif
    @elseif($currentPage =='sales-social-newsfeed')
        <title>Plexuss Sales Central Control | College Recruiting Network | Plexuss.com</title>
        <!-- Compressed CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/foundation-sites@6.5.0-rc.3/dist/css/foundation.min.css" integrity="sha256-b2khkeAav/7kTh0Bs5h1Xw1kqGL56SziJ5zk6bEvnAw= sha384-7nP0F9FVCI9Qg1SfsjHWQd+4ksCAxlF5pibRyPGxwn7NJpu1XuSaOoMh8JHIDSdk sha512-Rcgo7Zj9clxZoGtt4CBj1aEtCL9gBd64nYl3hkKEuWDwtK7hKY6c4D5vL4njDseuz31u1WWSM42SbvYe/3CZYQ==" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" charset="UTF-8" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick.min.css" />
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.6.0/slick-theme.min.css" />
        <link rel="stylesheet" type="text/css" href="/css/dataTables.foundation.css?8">
       <link rel="stylesheet" type="text/css" href="/css/salesHeader.css?8">
       <script src="{{asset('js/emailReporting/sorttable.js')}}"></script>
        <script>
           let active_page_sales = "Social Newsfeed";
        </script>

        @if($newsfeed_sub_page == 'All Posts')
            <script>
                let sub_page = 'All Posts'
            </script>
        @endif
        @if($newsfeed_sub_page == 'Plexuss Only')
            <script>
                let sub_page = 'Plexuss Only'
            </script>
        @endif

    @elseif($currentPage =='sales-student-tracking')
        <title>Plexuss Sales Central Control | College Recruiting Network | Plexuss.com</title>
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/foundation-sites@6.5.0-rc.3/dist/css/foundation.min.css" integrity="sha256-b2khkeAav/7kTh0Bs5h1Xw1kqGL56SziJ5zk6bEvnAw= sha384-7nP0F9FVCI9Qg1SfsjHWQd+4ksCAxlF5pibRyPGxwn7NJpu1XuSaOoMh8JHIDSdk sha512-Rcgo7Zj9clxZoGtt4CBj1aEtCL9gBd64nYl3hkKEuWDwtK7hKY6c4D5vL4njDseuz31u1WWSM42SbvYe/3CZYQ==" crossorigin="anonymous">
         <link rel="stylesheet" type="text/css" href="/css/dataTables.foundation.css?8">
       <link rel="stylesheet" type="text/css" href="/css/salesHeader.css?8">
       <script src="{{asset('js/emailReporting/sorttable.js')}}"></script>
        <script>
            let active_page_sales = "Tracking"
            let sub_page = "Overview Tracking";
       </script>
    @endif

    @if($currentPage == 'sales-scholarships')
        <title>Plexuss Sales Central Control | Scholarships</title>
        <link rel="stylesheet" type="text/css" href="/css/salesCentralControl.css?8">
    @endif

    @if( $currentPage == 'sales-agency-reporting' || $currentPage == 'sales-pixel-tracking-test')
        <title>Plexuss Sales Central Control | College Recruiting Network | Plexuss.com</title>
        <link rel="stylesheet" type="text/css" href="/css/salesCentralControl.css?8">
    @endif

    @if($currentPage == 'sales-email-reporting')
    <title>Plexuss Sales Central Control | College Recruiting Network | Plexuss.com</title>
    <link rel="stylesheet" type="text/css" href="/css/salesCentralControl.css?8">
    <link rel="stylesheet" type="text/css" href="/css/salesCentralControl.css?8">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="{{asset('css/emailReporting/emailReporting.css')}}">
    @endif

    @if( $currentPage == 'manageColleges' || $currentPage == 'manageCollegesReporting' )
        <title>Plexuss Agency of Record Central Control | College Recruiting Network | Plexuss.com</title>
        <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/dataTables.colReorder.min.css?8">
        <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/dataTables.colvis.jqueryui.css?8">
        <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/dataTables.colVis.min.css?8">
        <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/dataTables.fixedHeader.min.css?8">
        <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/dataTables.fixedColumns.min.css?8">
        <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/dataTables.keyTable.min.css?8">
        <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/dataTables.responsive.css?8">
        <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/dataTables.scroller.min.css?8">
        <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/jquery.dataTables.min.css?8">
        <link rel="stylesheet" type="text/css" href="/css/jqueryDataTablesPlugin/jquery.dataTables_themeroller.css?8">
        <link rel="stylesheet" type="text/css" href="/css/aorCentralControl.css">
    @endif

    @if( $currentPage == 'specialevent-happyBirdthdayToYou' )
        <link rel="stylesheet" type="text/css" href="/css/specialEvents/happyBirthday.css?8">
    @endif

    @if( $currentPage == 'infilawSurvey' )
        <title>{{$title or 'Infilaw Survey'}}</title>
        <link rel="stylesheet" type="text/css" href="/css/infilawSurvey.css?8">
    @endif

    @if( $currentPage == 'plexuss-conferences' )
        <title>{{$title or 'Plexuss Conferences'}}</title>
        <link rel="stylesheet" type="text/css" href="/css/conference.css?8">
    @endif

    <link href="/favicon.png" type="image/png"  rel="icon"/>
    <link rel="stylesheet" href="/css/normalize.min.css?8"/>
    <link rel="stylesheet" href="/css/foundation.min.css?8" />
    <link rel="stylesheet" href="/css/switch.css?8"/>
    <link rel="stylesheet" href="//ajax.googleapis.com/ajax/libs/jqueryui/1.11.1/themes/smoothness/jquery-ui.css" />
    <link rel="stylesheet" href="/css/default.css?9.0" />
    <link rel="stylesheet" href="/css/upgradeModal.css" />
    <!--<link rel="stylesheet" href="/css/default.min.css" />-->
    <link rel="stylesheet" href="/css/base.css?8.01" />
    <!-- Open Sans Google webfont -->
    <link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>

    <!--Not Use Jinita-->
   <!-- <link rel="stylesheet" href="/js/toggle/bootstrap-toggle-buttons.css?8"/> -->
    <!--<link rel="stylesheet" href="/css/owl.carousel.css?8"/>
    <link rel="stylesheet" href="/css/owl.theme.css?8">-->
    <link rel="stylesheet" href="/css/prod_ready/owl.carousel.min.css"/>
    <link rel="stylesheet" href="/css/prod_ready/owl.theme.min.css">
    <script src="/js/vendor/modernizr.js?8"></script>
    <link rel="stylesheet" href="/css/selectivity-full.min.css?8">
    @if( $currentPage == 'plex-publisher' )
       <title>Publish an Article | Academic Recruiting Network | Plexuss.com</title>
       <link rel="stylesheet" type="text/css" href="/css/publisher.css?8">
    @endif

    @if( $currentPage == 'premium')
        <title>Premium Plans Information | Plexuss.com</title>
        <link rel="stylesheet" type="text/css" href="/css/premium.css?8">

        <!-- Global site tag (gtag.js) - Google Ads: 820637639 -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=AW-820637639"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', 'AW-820637639');
        </script>

        <!-- Event snippet for Premium - View Info Page conversion page -->
        <script>
          gtag('event', 'conversion', {'send_to': 'AW-820637639/VCSqCMDImpEBEMffp4cD'});
        </script>
    @endif

    @if($currentPage ==  "checkout-premium")
        <title>Checkout Premium | Plexuss.com</title>

        <!-- Global site tag (gtag.js) - Google Ads: 820637639 -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=AW-820637639"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', 'AW-820637639');
        </script>
        <!-- Event snippet for Premium - View Checkout conversion page -->
        <script>
          gtag('event', 'conversion', {'send_to': 'AW-820637639/WjITCN-CopEBEMffp4cD'});
        </script>
    @endif

    @if($currentPage == 'congratulation-premium')
        <title>Congratulations!!! | Plexuss.com</title>
        <!-- Global site tag (gtag.js) - Google Ads: 820637639 -->
        <script async src="https://www.googletagmanager.com/gtag/js?id=AW-820637639"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag('js', new Date());

          gtag('config', 'AW-820637639');
        </script>

        <!-- Event snippet for Premium - Puchase conversion page -->
        <script>
          gtag('event', 'conversion', {
              'send_to': 'AW-820637639/jltgCN_xjpEBEMffp4cD',
              'transaction_id': ''
          });
        </script>
    @endif

    @if( $currentPage == 'plex-events' )
       <title>College Fairs | University Events | Plexuss</title>
       <meta name="description" content="Looking to attend a college fair or a university admissions events? Visit Plexuss to discover and RSVP to a college event near you.">
       <link rel="stylesheet" type="text/css" href="/css/events.css?8">
    @endif

    <!-- error page stylesheets -->
    @if( $currentPage == 'error_404' )
        <title>Error {{$error_num}} | Plexuss</title>
        <link rel="stylesheet" type="text/css" href="/css/404.css?8">
    @endif
    @if( $currentPage == 'international-students-page' )
        <script type="text/javascript" src="https://js.stripe.com/v2/"></script>

        @if(env('ENVIRONMENT') == 'LIVE')
            <script type="text/javascript">
                Stripe.setPublishableKey('pk_live_8To1rx24ZEQgXTUQEUMeRO6S');
            </script>
        @else
            <script type="text/javascript">
                Stripe.setPublishableKey('pk_test_18eNpd13Ckvy0UyZSgVWgp1E');
            </script>
        @endif
    @endif
    <!-- For Chrome for Android: -->
    <link rel="icon" sizes="192x192" href="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/general/apple-touch-icon-192x192-precomposed.png">
    <!-- For iPhone 6 Plus with @3× display: -->
    <link rel="apple-touch-icon-precomposed" sizes="180x180" href="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/general/apple-touch-icon-180x180-precomposed.png">
    <!-- For iPad with @2× display running iOS ≥ 7: -->
    <link rel="apple-touch-icon-precomposed" sizes="152x152" href="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/general/apple-touch-icon-152x152-precomposed.png">
    <!-- For iPad with @2× display running iOS ≤ 6: -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/general/apple-touch-icon-144x144-precomposed.png">
    <!-- For iPhone with @2× display running iOS ≥ 7: -->
    <link rel="apple-touch-icon-precomposed" sizes="120x120" href="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/general/apple-touch-icon-120x120-precomposed.png">
    <!-- For iPhone with @2× display running iOS ≤ 6: -->
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/general/apple-touch-icon-114x114-precomposed.png">
    <!-- For the iPad mini and the first- and second-generation iPad (@1× display) on iOS ≥ 7: -->
    <link rel="apple-touch-icon-precomposed" sizes="76x76" href="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/general/apple-touch-icon-76x76-precomposed.png">
    <!-- For the iPad mini and the first- and second-generation iPad (@1× display) on iOS ≤ 6: -->
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/general/apple-touch-icon-72x72-precomposed.png">
    <!-- For non-Retina iPhone, iPod Touch, and Android 2.1+ devices: -->
    <link rel="apple-touch-icon-precomposed" href="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/general/apple-touch-icon-57x57-precomposed.png"><!-- 57×57px -->

<!-- start Mixpanel --><script type="text/javascript">(function(e,b){if(!b.__SV){var a,f,i,g;window.mixpanel=b;b._i=[];b.init=function(a,e,d){function f(b,h){var a=h.split(".");2==a.length&&(b=b[a[0]],h=a[1]);b[h]=function(){b.push([h].concat(Array.prototype.slice.call(arguments,0)))}}var c=b;"undefined"!==typeof d?c=b[d]=[]:d="mixpanel";c.people=c.people||[];c.toString=function(b){var a="mixpanel";"mixpanel"!==d&&(a+="."+d);b||(a+=" (stub)");return a};c.people.toString=function(){return c.toString(1)+".people (stub)"};i="disable time_event track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config people.set people.set_once people.increment people.append people.union people.track_charge people.clear_charges people.delete_user".split(" ");
for(g=0;g<i.length;g++)f(c,i[g]);b._i.push([a,e,d])};b.__SV=1.2;a=e.createElement("script");a.type="text/javascript";a.async=!0;a.src="undefined"!==typeof MIXPANEL_CUSTOM_LIB_URL?MIXPANEL_CUSTOM_LIB_URL:"file:"===e.location.protocol&&"//cdn.mxpnl.com/libs/mixpanel-2-latest.min.js".match(/^\/\//)?"https://cdn.mxpnl.com/libs/mixpanel-2-latest.min.js":"//cdn.mxpnl.com/libs/mixpanel-2-latest.min.js";f=e.getElementsByTagName("script")[0];f.parentNode.insertBefore(a,f)}})(document,window.mixpanel||[]);
mixpanel.init("7518ec6fdff3ee07c3229d93fab45270");</script><!-- end Mixpanel -->
<!-- Amplitude Analytics snippet -->

<!--Global variable... -->
@if (isset($signed_in) && $signed_in == 1)
<script>
    var AmplitudeData =  <?php echo json_encode(get_defined_vars()); ?>;
</script>
@endif
<script src="/js/amplitude.js" defer></script>
<!-- end Amplitude -->
