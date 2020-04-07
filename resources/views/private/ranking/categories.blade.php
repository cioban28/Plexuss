@extends('private.ranking.master')


<?php
// @section('sidebar')
//     <div class='row'>
//         <div class='column small-12 hide-for-small-only'>
//                 <!--
//                 <div class="large-12 columns side-bar-news page-right-side-bar side-bar-1 radius-4"></div>
//                 -->

//                 <!-- if user's profile percentage is greater than 0, hide the get started side bar 
//                 @if ($profile_perc == 0)
//                 <div class="row">
//                     <div class="large-12 columns page-right-side-bar side-bar-2 radius-4">
//                         <div class="text-center">
//                             <p class="step-number">1</p>
//                         </div>
//                         <p class="right-bar-heading white">Get Started</p>
//                         <p class="right-bar-para white">Wondering why your indicators are at zero?</p>
//                         <p class="right-bar-para white">You need a profile for the recruitment process to begin.</p>
//                         <div class="large-12 text-center">
//                             <a href="/profile" class="button get-started-button">Start your Profile</a>
//                         </div>
//                     </div>
//                 </div>
//                 @endif-->

//                <!-- <div class="large-12 columns side-bar-news page-right-side-bar side-bar-1 radius-4"></div>-->
//                 @if( isset($signed_in) && $signed_in == 0 )
//                     @include('private.includes.right_side_createAcct_ranking')
//                 @endif
//         </div>
        

//         <div class='column small-12 hide-for-small-only'>    
//                 @if( isset($signed_in) && $signed_in == 1 )
//                     @include('private.includes.invite_friends_right_side')
//                 @endif

//                 <!-- adsense area -->
//                 @include('private.includes.adsense-307x280')

//                 @include('private.includes.right_side_get_started')
// 				@include('private.includes.right_side_footer')
// 				<!--
//                 <div class="large-12 columns right-side-footer">
//                 <ul class="inline" style="line-height:0.5">
//                     	<li><img src="/images/p-logo.png" alt="Logo" /></li>
//                         <li style="padding-top: 10px;padding-left: 3px;"><a href="/about">About</a> •</li>
//                         <li style="padding-top: 10px;padding-left: 3px;"><a href="/contact">Contact</a> •</li>
//                         <li style="padding-top: 10px;padding-left: 3px;"><span id="RightMore" onclick="ShowHideFooterLinks(1);" style="cursor:pointer;">More&nbsp;<img src="/images/arrow_down.png" /></span><span id="RightMoreClose" style="display:none;cursor:pointer;" onclick="ShowHideFooterLinks(2);">Close&nbsp;<img src="/images/arrow_up.png" /></span></li>
//                     </ul>
//                     <div id="RightMoreDiv" style="display:none;padding-top:15px;">
//                     <ul class="inline" style="line-height:0.5;">
//                         <li><a href="/advertising">Advertising</a> •</li>
//                         <li><a href="/college-submission">College Submission</a> •</li>
//                         <li><a href="/scholarship-submission">Scholarship Submission</a> • </li>
//                         <li><a href="/careers-internships">Careers</a> • </li>
//                         <li><a href="/terms-of-service">Terms of Service</a> • </li>
//                         <li><a href="/privacy-policy">Privacy Policy</a></li>
//                     </ul>
//                     <div class="fs10 clr-fff" style="padding-top:10px;">Plexuss © 2015 <span class="pl40"><a  target='_blank' href="http://www.linkedin.com/company/plexuss-com"><img src="/images/social/linkedin_white.png" title="" alt=""></a><a  target='_blank' href="http://www.twitter.com/plexussupdates"><img src="/images/social/twitter_white.png" title="" alt="" class="pl15"></a></span>
//                     </div>
//                     </div>
//                 </div>
// 				-->
             
//         </div>
//     </div>
// @stop
?>

@section('content')
    <div class="row show-for-medium-up">
    <div class="small-12 columns" style="background-color:#ffffff;border-radius:5px;padding:0px;">
        <div class="row">
        <div class="small-12 columns"><span style="font-size:24px;color:#000000;padding-left:3px;line-height: 47px;">College Ranking</span>&nbsp;&nbsp;<span style="font-size:12px;color:#8A8A79;">All Ranking Categories</span></div>
        </div>
        <div class="row" style="background-color:#000000;">
        <div class="small-12 columns" style="line-height:30px;">&nbsp;</div>
        </div>
    </div>
    </div>
    <div class="row show-for-small-only">
    <div class="small-12 columns" style="text-align:center;font-size:24px;color:#ffffff;padding:12px 0;">College Ranking</div>
    <div class="small-12 columns" style="text-align:center;font-size:12px;color:#ffffff;">All Ranking Categories</div>
    </div>
    <br />
    <div class="row">
    <div id="container-box" class="js-masonry row">
    <?php $i=1; ?>
    @foreach($list_array as $key=>$listCollege)
    <?php $keyId=str_replace(" ","_",$key); ?>
    	<div class="large-4 medium-6 columns ranking-listing-category-box">
        	<div class="row">
            	<div class="small-12 columns" style="padding:0px;">
                    <div class="ranking-category-box">
                	<div class="row">
                    	<div class="small-12 columns ranking-category-box-head">{{$key}}</div>
                    </div>
                    <div class="row">
                        <div class="small-12 columns ranking-box-min-height">
                            @foreach($listCollege as $key1=>$listVal)


                                @if($key1==0)
                                    <div class="row">
                                    	<div class="small-12 columns ranking-category-box-subhead">{{{$listVal->source or 'Out Side Source'}}}</div>
                                    </div>
                                    <div class="row collapse ranking-cat-layout-disp" id="cat_collest_list_{{$keyId}}">
                                    <div class="small-12 columns">
                                    <div class="row">
                                    	<div class="small-12 columns ranking-category-image">
                                            <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/lists/images/{{{$listVal->image or 'best-food_icon.png'}}}" alt=""/>
                                        </div>
                                    </div>
                                @endif
                                @if($key1==3)
                                    <div class="row"><div class="small-12 columns" style="display:none;padding:0px;" id="expand_category_{{$keyId}}">
                                @endif
                                
                                <div class="row @if($key1%2==0) ranking-list-row-odd @else ranking-list-row-even @endif">
                                	<div class="small-2 columns ranking-college-left"><div class="circle-ranking"  style="">{{$listVal->order or 'NA'}}</div></div>
                                    <div class="small-10 columns ranking-college-right"><strong><a href="/college/{{$listVal->slug}}">{{$listVal->school_name or 'NA'}}</a></strong><br />{{$listVal->city or 'NA'}}, {{$listVal->state or 'NA'}}</div>
                                </div>                    
                            @endforeach

                            @if($key1>=3)
                                </div></div>
                            @endif
                                    </div>
                                    </div>                                    
                        </div>
                    </div>
                    <div class="row">
                        <div class="small-12 columns ranking-cat-layout-disp hide-for-small-only" id="cat_show_more_{{$keyId}}" style="text-align:center;padding:5px;"><a onclick="ExpandCategoriesRanking('{{$keyId}}')" id="expand_category_a_{{$keyId}}" class="ranking-show-more">Show more</a></div>
                        <div class="small-12 columns show-for-small-only" style="text-align:center;padding:5px;cursor:pointer;"><img src="/images/ranking/down-arrow.png" onclick="ToggleCatBoxView('{{$keyId}}');" id="cat_show_more_mobile_{{$keyId}}" alt=""/></div>
                    </div>
                    </div>
                </div>
            </div>
        </div>	
    @endforeach    	
    </div>
    </div>

    <!-- create acct engagement -->
    <div class="row small-collapse medium-uncollapse hide-for-large-up">
        <div class="column small-12 medium-10 medium-offset-1 end">
            @if( isset($signed_in) && $signed_in == 0 )
                @include('private.includes.right_side_createAcct_ranking')
            @endif
        </div>
    </div>

    <br /><br />
	<script type='text/javascript'>
		function ExpandCategoriesRanking(id) {
			$("#expand_category_"+id).slideToggle(500,function(){
				setResizeBox();
				if($("#expand_category_"+id).css('display')=="block")
				{
					$("#expand_category_a_"+id).html('Show less');
				}
				else
				{
					$("#expand_category_a_"+id).html('Show more');
				}
			});
		}
		function setResizeBox() {
			$('#container-box').masonry({
				itemSelector: '.box-div'
			});
		};
		function ToggleCatBoxView(Id)
		{
			$("#cat_collest_list_"+Id).toggleClass('ranking-cat-layout-disp');
			$("#cat_show_more_"+Id).toggleClass('ranking-cat-layout-disp');
			ExpandCategoriesRanking(Id);
			if($("#cat_collest_list_"+Id).css('display')=="block")
			{		
				$("#cat_show_more_mobile_"+Id).attr('src','/images/ranking/up-arrow.png')
			}
			else
			{
				$("#cat_show_more_mobile_"+Id).attr('src','/images/ranking/down-arrow.png')
			}
			setResizeBox();
		}
	</script>
@stop


@section('sic')
    @include('includes.smartInteractiveColumn')
@stop