<!doctype html>
<html class="no-js" lang="en">
	<head>
		@include('private.headers.header')
	</head>
	<body id="{{$currentPage}}">
        @if( isset($signed_in) && $signed_in == 0 && ($is_mobile != true))
    		<div class="daily-chat-bar-container webinar-live" id="_banner_ad_bar">
    		    <a id="ad_bar_img" href="/signup?utm_source=SEO&utm_medium=news&utm_content=free_universities&utm_campaign=news_top_banner&utm_term=">
    				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/banner/school-logos.png" alt="Sign up!">		    	
    		    </a>
    		    <a id="ad_bar_text" href="/signup?utm_source=SEO&utm_medium=news&utm_content=free_universities_other&utm_campaign=news_top_banner&utm_term=">
    				<div class="text">Register on Plexuss.  Apply to 10 Universities for Free!</div>
    		    </a>
    		    <a id="ad_bar_btn" href="/signup?utm_source=SEO&utm_medium=news&utm_content=free_universities_logo&utm_campaign=news_top_banner&utm_term=">
    				<div class="signupbtn">Sign up!</div>
    		    </a>
    		</div>
        @endif

		@include('private.includes.topnav')
		<!-- news section -->
        <div class="row show-for-small-only news_filter_sort_row">
        <div class="small-3 columns no-padding text-center news_filter" onClick="$('#NewsFilterDiv').toggle();">FILTER</div>
    	<div id="NewsFilterDiv">
            <ul class="filter_nav">
                <li class="active"><a href="/news/" class="clr-fff">All</a></li>
                <li style="border-bottom:#A0DB39 solid 2px;" class="college-cat"  onclick="openMobileNewsNav('college news');">
                	<!-- Removed id="college-cat" to remove hover effect on mobile -->
                    <a>COLLEGE NEWS <div class="arrow-right pr5 fr" style="margin-top:7px;">&nbsp;</div></a>
                    <div class="sub-menu" id="college-cat-div">
                    <ul>
                        @if($news_scat_data!='')
                        @foreach($news_scat_data as $news_scat)
                        <li><a href="/news/subcategory/{{$news_scat['slug']}}/">{{ $news_scat['name']}}</a></li>
                        @endforeach
                        @endif
                    </ul>
                    </div>
                    <div class="clearfix"></div>
                </li>
                <li style="border-bottom:#FF6666 solid 2px;" class="college-cat"  onclick="openMobileNewsNav('paying for college');">
                	<!-- Removed id="college-pay-cat" to remove hover effect on mobile -->
                    <a>PAYING FOR COLLEGE <div class="arrow-right pr5 fr" style="margin-top:7px;">&nbsp;</div></a>
                    <div class="sub-menu" id="college-pay-cat-div">
                    <ul>
                        @if($college_scat_data!='')
                        @foreach($college_scat_data as $college_subcat)
                        <li><a href="/news/subcategory/{{$college_subcat['slug']}}/">{{ $college_subcat['name']}}</a></li>
                        @endforeach
                        @endif
                    </ul>
                    </div>
                </li>
                <li style="border-bottom:#05CED3 solid 2px;" class="college-cat" onclick="openMobileNewsNav('life after college');">
                	<!-- Removed id="college-after-cat" to remove hover effect on mobile -->
                    <a>LIFE AFTER COLLEGE <div class="arrow-right pr5 fr" style="margin-top:7px;">&nbsp;</div></a>
                    <div class="sub-menu" id="college-after-cat-div">
                    <ul>
                        @if($college_after_data!='')
                        @foreach($college_after_data as $collegeafter_subcat)
                        <li><a href="/news/subcategory/{{$collegeafter_subcat['slug']}}/">{{ $collegeafter_subcat['name']}}</a></li>
                        @endforeach
                        @endif
                    </ul>
                    </div>
                </li>
            </ul>
        </div>
        
        <div class="small-6 columns no-padding text-center" style="font-size:20px;color:#F0F0F0;">The Quad</div>
        <div class="small-3 columns no-padding text-center news_sort_by" onClick="$('#NewsSortingDiv').toggle();">SORT BY
        	<div id="NewsSortingDiv">
                <ul class="filter_nav">
                    <li class="active"><a href="{{$OrderUrl1}}" class="clr-fff">NEWEST</a></li>
                    <li class="active"><a href="{{$OrderUrl2}}" class="clr-fff">OLDEST</a></li>
                </ul>
            </div>
        </div>
        </div>
		<div class="content-wrapper">
			<div id='newshomecontent' class="row collapse" style="height: 100%; max-width: 100%;">
				<!-- Left Side Part -->
				<div class="news-cont-left-container">
					@yield('content')
					@if( isset( $NewsId ) )
						<div class="hide">
							@include( 'public.includes.comments' )
						</div>
						<div class="row collapse hide-for-large-up">
							<div class="column small-12">
								@if( isset($signed_in) && $signed_in == 0 )
				                    @include('private.includes.right_side_createAcct_news')
				                @endif
							</div>
						</div>
					@endif
				</div>
				<!-- Right Side Part -->
				<?php
				// <div class="column small-3 show-for-large-up" style="height: 100%;">
				// 	@if( isset($premium_user_level_1) && $premium_user_level_1'] == 1 )
				// 		@include('includes.smartInteractiveColumn')
				// 	@else
				// 		@section('sidebar')
				// 		This is the master sidebar.
				// 		@show
				// 	@endif
				// </div>
				?>
			</div>
			<!-- SIC -->
			@if( isset($signed_in) && $signed_in == 0 )
				@include('includes.banner_ad')
			@else
				@include('includes.smartInteractiveColumn')
			@endif
		</div>
    <!-- end news section -->
		@include('private.includes.backToTop')
        @include('private.footers.footer')
		<script type="text/javascript">
        /*masonry function*/
            function show_profile_block(){
				$('.mobile-profile-row').toggle();
			}
            function toggle_menu_button(){
				$('#menu-toggler').trigger('click');
			}
			function menudisplay(li_id,div_id)
			{
				$("#"+li_id).mouseenter(function(e) {
					$("#"+div_id).css("display","block");
				});
				
				$("#"+div_id).mouseenter(function(e) {
					$("#"+div_id).css("display","block");
				});
				
				$("#"+div_id).mouseleave(function(e) {
					$("#"+div_id).css("display","none");
				});
				
				$("#"+li_id).mouseleave(function(e) {
					$("#"+div_id).css("display","none");
				});
			}
			$(document).ready(function(e) {
				menudisplay('collge-list-li','collge-list-div');
				menudisplay('mylist-list-li','mylist-list-div');
				menudisplay('college-cat','college-cat-div');
				menudisplay('college-pay-cat','college-pay-cat-div');
				menudisplay('aftercoll-cat-li','after-college-div')
				menudisplay('college-after-cat','college-after-cat-div');
				$("#owl-news").owlCarousel({
					navigation : true,
  				slideSpeed : 300,
  				paginationSpeed : 400,
  				singleItem : true,
  				pagination:false,
  				navigationText : false
				})
				/*
				$('.back-to-top').click(function(){
					$("html, body").animate({ scrollTop: 0 },400);
					return false;
				});
				 */
			});
			function openMobileNewsNav(navTab){
				switch(navTab){
					case 'college news':
						$('#college-cat-div').toggle();
						$('#college-pay-cat-div').hide();
						$('#college-after-cat-div').hide();
					break;
					case 'paying for college':
						$('#college-pay-cat-div').toggle();
						$('#college-cat-div').hide();
						$('#college-after-cat-div').hide();
					break;
					case 'life after college':
						$('#college-after-cat-div').toggle();
						$('#college-cat-div').hide();
						$('#college-pay-cat-div').hide();
					break;
				}			
			}
			function openfilter()
			{
				$('#news-filter-menu').css('display','block');
				$('#news-filter-menu').css('visibility','visible');
			}
			function closefilter()
			{
				$('#news-filter-menu').css('display','none');
				$('#news-filter-menu').css('visibility','hidden');
			}			
			function openSort(){
				$('#sort-filter-menu').css('display','block');
				$('#sort-filter-menu').css('visibility','visible');
			}
			function closeSort(){
				$('#sort-filter-menu').css('display','none');
				$('#sort-filter-menu').css('visibility','hidden');
			}
			$(document).foundation();

            $(window).load(function() {
               $('#container-box').masonry('reloadItems').masonry();
            });
		</script>
		<!-- \\\\\ DISQUS - adding comment count links - start ///// -->
		<script type="text/javascript">
		/* * * CONFIGURATION VARIABLES: EDIT BEFORE PASTING INTO YOUR WEBPAGE * * */
		var disqus_shortname = 'plexuss'; // required: replace example with your forum shortname
		/* * * DON'T EDIT BELOW THIS LINE * * */
		(function () {
		var s = document.createElement('script'); s.async = true;
		s.type = 'text/javascript';
		s.src = '//' + disqus_shortname + '.disqus.com/count.js';
		(document.getElementsByTagName('HEAD')[0] || document.getElementsByTagName('BODY')[0]).appendChild(s);
		}());
		</script>
		<!-- \\\\\ DISQUS - adding comment count links - end ///// -->
	</body>
</html>
