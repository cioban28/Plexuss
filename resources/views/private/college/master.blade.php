<!doctype html>
<html class="no-js" lang="en">
	<head>
		@include('private.headers.header')
        @include('includes.facebook_event_tracking')
        @if(isset($currentPage) && $currentPage == "college")
            @include('includes.hotjar_for_plexuss_domestic')
        @endif
        
	</head>
	<body id="{{$currentPage}}">
        @if( isset($signed_in) && $signed_in == 0 && ($is_mobile != true) && !Session::has('closeSignupOffer'))
            <div class="_signupBanner" id="_banner_ad_bar">
                <!-- <a id="ad_bar_img" href="/signup?utm_source=SEO&utm_medium=college&utm_content=free_universities&utm_campaign=college_top_banner&utm_term=">
                    <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/banner/school-logos.png" alt="Sign up!">
                </a> -->
                <!-- <a id="ad_bar_text" href="/signup?utm_source=SEO&utm_medium=college&utm_content=free_universities_other&utm_campaign=college_top_banner&utm_term=">
                    <div class="text">Register on Plexuss.  Apply to 10 Universities for Free!</div>
                </a> -->
                <a id="ad_bar_text" href="/signup?utm_source=SEO&utm_medium=college&utm_content=free_universities_other&utm_campaign=college_top_banner&utm_term=">
                    <div class="text">Read up to 50 College Essays</div>
                </a>
                <a id="ad_bar_btn" href="/signup?utm_source=SEO&utm_medium=college&utm_content=free_universities_logo&utm_campaign=college_top_banner&utm_term=">
                    <div class="signupbtn">Sign up!</div>
                </a>
								<span class="close-icon" id="signup-offer-close" style="float: right;padding-right: 0.9em;padding-top: 0.5em;cursor: pointer;">x</span>
            </div>
        @endif

		@include('private.includes.topnav')

		<div class="row collapse college-page-toplevel-container" style="position:relative;">
		{{-- */$pagetype = isset($pagetype)?$pagetype:'stats'/* --}}

                <!-- Left Side Part -->
                <div class="column small-12 @if($currentPage == 'college') large-9 @else large-12 @endif">
                    <!--generate the right panel-->
                    @section('collegenav')
                    @show
                </div>
                <!-- End Left Side Part  -->

                <!-- Right Side Part -->
                <div class="column small-3 large-3 show-for-large-up">
                    @section('sidebar')
                        This is the master sidebar.
                    @show
                </div>
                <!-- End Right Side Part -->
		</div>

        <div class="row">
            <div class="column small-12">
                <?php
                    //unset($collegeImages);
                    //unset($college_data);
                    //unset($alerts);
                    /*
                    echo '<pre>';
                    print_r($data);
                    echo '</pre>';

                    */
                ?>
            </div>
        </div>
		@include('private.includes.backToTop')
        @include('private.footers.footer')
        <script language="javascript">
            DefaultSection='{{$Section}}';
            Plex.PushStart='{{$CollegName}}';

            //This will ONLY run if the page Section is chat. The ajax version will call this from the menu.
            @if ($Section == 'chat')
                Plex.chat.startChatReadyChecker();
            @endif

						$("#signup-offer-close").on('click', function(){$("#_banner_ad_bar").hide(); $("#college-mobile-menu").css("top", "80px")})
        </script>

        @if( isset($eddy_found) && $eddy_found )
            <!-- Education Dynamics ad scripts -->
            @include('includes.eddy_ads')
        @endif

<?php

if(!empty($_GET['applynowmodal'])):

   
?>

<?php
if($_GET['applynowmodal'] == 1):
?>
<script>
$(document).ready(function(){
$('#collegeapplynow').click();


});

</script>
<?php
endif;
endif;

?>
	</body>
</html>
