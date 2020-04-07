<meta name="google-site-verification" content="Qglree3wKpI0g2xgBLXP_I6kUPsXwCV0sif__XgHZ2s" />
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0,maximum-scale=1.0">
<meta name="p:domain_verify" content="8edfc77043263d3582f53df83941d89f"/>
<meta property="og:image" content="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/images/fb_share_images/plexuss-app-icon-200.png" />
<meta property="og:image" content="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/images/fb_share_images/Page+1.png"/>
<meta property="og:image" content="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/images/fb_share_images/Page+2.png"/>
<meta property="og:image" content="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/images/fb_share_images/Page+3.png"/>
<meta property="og:image:type" content="image/png" />
<meta name="csrf-token" content="{{ csrf_token() }}">

@if( isset($currentPage) && $currentPage == 'frontPage' )
    <link rel="stylesheet" href="/css/prod_ready/frontpage_all.min.css?v=2" />
    <link rel="stylesheet" href="/css/upgradeModal.css" />
    <!-- <link rel="stylesheet" href="/css/homepage.css" />  -->
@endif
@if ($currentPage== 'b2b')
    <title class="b2b-meta-title">{{ $meta_title or 'Plexuss | College Partnerships' }} </title>
    <meta name="description" content="Your Partner for International Recruiting. Create awareness and interest in your institution. Qualify candidates based on your academic standards. Qualify candidates for language proficiency and ability to pay. Communicate the value and specific benefits of your institution. Facilitate the complete application process.  Learn more at Plexuss.com">
    <meta name="keywords" content="college matchmaker international recruiting">
    <link rel="stylesheet" href="/css/normalize.min.css?8"/>
    <link rel="stylesheet" href="/css/foundation.min.css?8" />
    @if(isset($resources_subpage) && $resources_subpage == '_PlexussOnboarding')
        <link rel="stylesheet" href="/css/plexussOnboarding.css?8" />
    @endif
    <script>
            window.fbAsyncInit = function() {
            FB.init({
                {{-- Automatically use testing appId when on local --}}
                @if( App::isLocal() )
                    {!! "appId: '825814780812004'," /* FB Test App */!!}
                @else
                    {!! "appId: '663647367028747'," /* FB LIVE App */!!}
                @endif
                xfbml      : true,
                version    : 'v2.1'
            });
        };
    </script>

    <link rel="stylesheet" href="/css/base.css?8" />
    <link rel="stylesheet" href="/css/default.css?v=2.10" />
    <link rel="stylesheet" href="/css/news.css" />

    <link rel="stylesheet" href="/css/homepage.css?8.01" />
    <link rel="stylesheet" href="/css/prod_ready/owl.carousel.min.css">
    <link rel="stylesheet" href="/css/prod_ready/owl.theme.min.css">
    <link rel="stylesheet" href="/css/b2b.css?v=1.00" />
@elseif ($currentPage== 'college-submission')
    <title>College Matchmaker Recruiting Network | Join as a College | Plexuss.com</title>
    <meta name="description" content="Join Plexuss -- a college matchmaker service. Services to colleges include: chatting with students, managing your college page, reporting & analytics, recruiting students. Learn more at Plexuss.com">
    <meta name="keywords" content="college matchmaker">
    <link rel="stylesheet" href="/css/normalize.min.css?8"/>
    <link rel="stylesheet" href="/css/foundation.min.css?8" />
    <link rel="stylesheet" href="/css/base.css?8" />
    <link rel="stylesheet" href="/css/default.css?v=2.10" />
@elseif ($currentPage== 'college-prep')
    <title>College Matchmaker Website Network | Join as College Prep | Plexuss.com</title>
    <meta name="description" content="Join Plexuss -- a college matchmaker website. Services to colleges include: chatting with students, managing your college page, reporting & analytics, recruiting students. Learn more at Plexuss.com">
    <meta name="keywords" content="college matchmaker website">
    <link rel="stylesheet" href="/css/normalize.min.css?8"/>
    <link rel="stylesheet" href="/css/foundation.min.css?8" />
    <link rel="stylesheet" href="/css/base.css?8" />
    <link rel="stylesheet" href="/css/default.css?v=2.10" />
@elseif ($currentPage== 'scholarship-submission')
    <title>Submit Scholarships for College Students | Recruiting Network | Plexuss.com</title>
    <meta name="description" content="Offering scholarships for college students? Please fill out the scholarship submission form. Plexuss will help you find students to meet your scholarship requirements.">
    <meta name="keywords" content="scholarships for college students">
    <link rel="stylesheet" href="/css/normalize.min.css?8"/>
    <link rel="stylesheet" href="/css/foundation.min.css?8" />
    <link rel="stylesheet" href="/css/base.css?8" />
    <link rel="stylesheet" href="/css/default.css?v=1.11" />
@elseif ($currentPage== 'about')
    <title>College 411 | About | College Recruiting Academic Network | Plexuss.com</title>
    <meta name="description" content="Get the college 411 at Plexuss -- a college recruiting network that has the complete guide to 4,000 American colleges and Universities. Find out information such as school history, campus setting, and academic calendar at college recruiting network. Learn more about us on Plexuss.com.">
    <meta name="keywords" content="college 411">
    <link rel="stylesheet" href="/css/normalize.min.css?8"/>
    <link rel="stylesheet" href="/css/foundation.min.css?8" />
    <link rel="stylesheet" href="/css/base.css?8" />
    <link rel="stylesheet" href="/css/default.css?v=2.10" />
 @elseif ($currentPage== 'contact')
    <title>College 411 | About | College Recruiting Academic Network | Plexuss.com</title>
    <meta name="description" content="Get the college 411 at Plexuss -- a college recruiting network that has the complete guide to 4,000 American colleges and Universities. Find out information such as school history, campus setting, and academic calendar at college recruiting network. Learn more about us on Plexuss.com.">
    <meta name="keywords" content="college 411">
    <link rel="stylesheet" href="/css/normalize.min.css?8"/>
    <link rel="stylesheet" href="/css/foundation.min.css?8" />
    <link rel="stylesheet" href="/css/base.css?8" />
    <link rel="stylesheet" href="/css/default.css?v=2.10" />
@elseif ($currentPage== 'team')
    <title>Plexuss Team | College Recruiting Academic Network | Plexuss.com</title>
    <meta name="description" content="Plexuss is a college recruiting network that has complete guide to over 4,000 colleges and Universities. Meet the team behind the scene and learn more about getting recruited for colleges at Plexuss.com. ">
    <meta name="keywords" content="Plexuss">
    <link rel="stylesheet" href="/css/normalize.min.css?8"/>
    <link rel="stylesheet" href="/css/foundation.min.css?8" />
    <link rel="stylesheet" href="/css/base.css?8" />
    <link rel="stylesheet" href="/css/default.css?v=2.10" />
@elseif ($currentPage== 'careers-internships')
    <title>Career & Internship Search | College Recruiting Academic Network | Plexuss.com</title>
    <meta name="description" content="Career and internship search and opportunities at Plexuss.com, a college recruiting network that has the complete guide to over 4,000 American colleges and Universities. Find information such as school history and campus setting at college recruiting network.">
    <meta name="keywords" content="internship search">
    <link rel="stylesheet" href="/css/normalize.min.css?8"/>
    <link rel="stylesheet" href="/css/foundation.min.css?8" />
    <link rel="stylesheet" href="/css/base.css?8" />
    <link rel="stylesheet" href="/css/default.min.css?v=1.07" />
@elseif ($currentPage== 'help' || $currentPage== 'faqs')
    <title>College Recruiting Academic Network | Help FAQ | Plexuss.com</title>
    <meta name="description" content="Plexuss.com is a college recruiting network that has the complete guide to over 4,000 colleges and Universities. Find out more about school history, campus setting academic calendar of colleges at college recruiting network. Learn more on Help FAQ page.">
    <meta name="keywords" content="college recruiting">
    <link rel="stylesheet" href="/css/normalize.min.css?8"/>
    <link rel="stylesheet" href="/css/foundation.min.css?8" />
    <link rel="stylesheet" href="/css/base.css?8" />
    <link rel="stylesheet" href="/css/default.css?v=2.10" />
@elseif ($currentPage== 'privacy-policy' || $currentPage== 'terms-of-service')
    <title>{{$title or ''}}</title>

    <link rel="stylesheet" href="/css/normalize.min.css?8"/>
    <link rel="stylesheet" href="/css/foundation.min.css?8" />
    <link rel="stylesheet" href="/css/default.css?v=2.10" />
    <link rel="stylesheet" href="/css/base.css?8" />

    <script src="/js/vendor/modernizr.js?8"></script>

@elseif ($currentPage == 'admin-signup')
    <title>International College Administrator | Sign Up | Plexuss.com</title>
    <meta name="description" content="International College Administrator Sign Up">
    <link rel="stylesheet" href="/css/foundation.min.css?8" />
    <link rel="stylesheet" href="/css/base.css?8" />
    <link rel="stylesheet" href="/css/adminSignup.css?8" />
    <link rel='stylesheet' href='/css/countryFlags.css?8'>
    <link rel="stylesheet" href="/css/selectivity-full.min.css?8">


@elseif ($currentPage == 'agency-signup')
    <title>International Agency | Sign Up | Plexuss.com</title>
    <meta name="description" content="International Agency Sign Up">
    <link rel="stylesheet" href="/css/foundation.min.css?8" />
    <link rel="stylesheet" href="/css/base.css?8" />
    <link rel="stylesheet" href="/css/agency.css?8.01" />
    <link rel='stylesheet' href='/css/countryFlags.css?8'>
    <link rel="stylesheet" href="/css/selectivity-full.min.css?8">

@else
    <title>College Network | College Recruiting Academic Network I Plexuss.com</title>
    <meta name="description" content="Which college is the best for you? Our college network has a complete guide to all accredited colleges and universities in the United States. Find more about school history, campus setting, academic calendar, financial aid and much more on Plexuss.">
    <meta name="keywords" content="College network">
    <link rel="stylesheet" href="/css/normalize.min.css?8"/>
    <link rel="stylesheet" href="/css/foundation.min.css?8" />
    <link rel="stylesheet" href="/css/base.css?8" />
    <link rel="stylesheet" href="/css/default.css?v=2.10" />
@endif

<link rel="stylesheet" href="//code.jquery.com/ui/1.10.2/themes/smoothness/jquery-ui.min.css" />
<link href='https://fonts.googleapis.com/css?family=Open+Sans:400,700,300' rel='stylesheet' type='text/css'>
<link href="/favicon.png" type="image/png"  rel="icon">

<!-- so far, only the frontpage and help sections use public header -->

@if(isset($currentPage) && $currentPage == 'unsubscribeThisEmail')
    <link rel="stylesheet" href="/css/unsubscribeEmail.css" />
@endif


<script>
    (function() {
        var _fbq = window._fbq || (window._fbq = []);
        if (!_fbq.loaded) {
            var fbds = document.createElement('script');
            fbds.async = true;
            fbds.src = '//connect.facebook.net/en_US/fbds.js';
            var s = document.getElementsByTagName('script')[0];
            s.parentNode.insertBefore(fbds, s);
            _fbq.loaded = true;
        }
        _fbq.push(['addPixelId', '1428934937356789']);
    })();

    window._fbq = window._fbq || [];
    window._fbq.push(['track', 'PixelInitialized', {}]);
</script>
<noscript>
    <img height="1" width="1" alt="" style="display:none" src="https://www.facebook.com/tr?id=1428934937356789&amp;ev=PixelInitialized" />
</noscript>



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

<!--Global variable ... -->
@if (isset($signed_in) && $signed_in == 1)
<script>
    var AmplitudeData =  <?php echo json_encode(get_defined_vars()); ?>;
</script>
@endif

<script src="/js/amplitude.js?v=1.00"></script>
<!-- end Amplitude -->
