@extends('private.ranking.master')


<!-- side bar before sic -->
<?php
    // @section('sidebar')
    //     <div class='row'>
    //         <div class='column small-12 hide-for-small-only'>
    //                 <!--
    //                 <div class="large-12 columns side-bar-news page-right-side-bar side-bar-1 radius-4"></div>
    //                 -->

    //                 <!-- if user's profile percentage is 0, then display 'get started' side bar, otherwise hide it
    //                 @if ($profile_perc'] == 0)
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
                    
    // 				<!-- RIGHT SIDE FOOTER (HELP, CONTACT, ABOUT, ETC.) HERE! -->
    //                 @if( isset($signed_in) && $signed_in == 0 )
    //                     @include('private.includes.right_side_createAcct_ranking')
    //                 @endif
    //         </div>
                    
    //         <div class='column small-12 hide-for-small-only'>
                
        //             @if( isset($signed_in) && $signed_in == 1 )
        //                 @include('private.includes.invite_friends_right_side')
        //             @endif
                    
        //             <!-- adsense area -->
        //             @include('private.includes.adsense-307x280')

        //             @include('private.includes.right_side_get_started')
    				// @include('private.includes.right_side_footer')
                
    //         </div>
    //     </div>
    // @stop
?>

@section('content')
    <!--
    <div class="row" style="border: thin solid blue">
    	<div class="medium-12 columns ">
            <div class='row col-ranking-box-top'>

            	<div class="medium-6 small-12 columns ranking-one-place-box">
                	<div class="ranking-right-head ranking-one-place-head small-only-text-center">College Rankings in One Place</div>
                    <div style="height:1px;margin:0 -7px;border-bottom: 1px solid #737373;" class="show-for-small-only"></div>
                    <div style="font-size:13px;line-height:20px" class="ranking-right-head1">
                    	We use the most reputable college ranking sources to bring you our Plexuss College Rankings into one place.
                    </div>
                    <div style="margin:0 -7px;border-bottom: 2px solid #c1c1c1;" class="show-for-small-only"></div>
                    <div style="margin-top:20px;" class="ranking-img-rows small-only-text-center">
                    	<div><img src="../images/ranking/best_colleges_logo.png" /><br /><br /><strong>US NEWS</strong><br />RANKING</div>
                        <div><img src="../images/ranking/plus_logo.png" /></div>
                       <div><img src="../images/ranking/forbes_logo.png" /><br /><br /><strong>FORBES</strong><br />RANKING</div>
                        <div><img src="../images/ranking/plus_logo.png" /></div>
                        <div><img src="../images/ranking/qs_logo.png" /><br /><br /><strong>QS WORLD</strong><br />RANKING</div>
                        <div><img src="../images/ranking/plus_logo.png" /></div>
                        <div><img src="../images/ranking/reuters_logo.png" /><br /><br /><strong>REUTERS</strong><br />RANKING</div>
                        <div><img src="../images/ranking/plus_logo.png" /></div>
                        <div><img src="../images/ranking/shanghai_logo.png" /><br /><br /><strong>SHANGHAI</strong><br />RANKING</div>
                    </div>
                    <div style="margin:0 -7px;border-bottom: 2px solid #c1c1c1;" class="show-for-small-only"></div>
                    <br />
                    <div class="grey-seperator show-for-medium-up"></div>                
                    <div class="show-for-medium-up plex-college-ranking-box text-center">
                        <div class="ranking-number-green" style="display: inline-block;">#1</div>
                        <div class="plex-rank-title-bottom">Plexuss College Rankings</div>
                        <div style="display: inline-block; margin-left: 12px;"><img src="../images/ranking/q-mark.png" /></div>
                    </div>
                </div>
                <div class="medium-6 columns text-center show-for-medium-up" style="padding-top:15px;">
                    <img style='cursor: pointer; padding-top: 10px;' data-reveal-id="college-ranking-video" src="/images/pages/ranking-video.png" alt="Plexuss College Video">
                </div>

            </div>
        </div>
    </div>
-->
    <div class="row">
        <div class='columns small-12'>
            <div class="center-college-nav mt30">
            @include('private.college.collegeNav')
            </div>
        </div>
    </div>
        
    <div class="row" data-equalizer>
    	<div class="medium-6 columns ranking-boxes" style="">
        	<div class="col-ranking-box row" data-equalizer-watch>
            	<div style="color:#F3F3F3;font-size:26px;background-color:#202020;text-align:center;padding: 12px 0;line-height:36px;border-radius:5px 5px 0 0;">Plexuss College Rank&nbsp;</div>
                @foreach($RankingData as $key=>$DataRanking)                
                    <div class="TopRankingData">
    					<div class="row" style="line-height:20px;padding:8px 0;">
                        <div class="small-2 columns" style="padding-top: 7px;"><div class="rank-bg-panel-small"><div class="rank-numb-small" style="font-size:10px;">#{{$DataRanking->plexuss}}</div></div></div>
                        <div class="small-10 columns" style="color:#000000;font-size:15px;"><strong><a href="/college/{{$DataRanking->slug}}" style="color:#000000;">{{$DataRanking->College}}</a></strong> <br />{{{$DataRanking->city or 'City'}}}, {{{$DataRanking->state or 'State'}}}</div>
                        </div>
                    </div>
                @endforeach
                <div style="text-align:center;" class='column small-10 small-centered'>
                    <a href="/ranking/listing" class="ranking-button">View full Plexuss ranking list</a>
                </div>
            </div>
        </div>
		<!-- Normal width is 6 when box above is not commented -->
        <div class="medium-6 columns ranking-boxes" style="padding-bottom:15px;">
        	<div class="col-ranking-box" data-equalizer-watch>
            	<div style="color:#F3F3F3;font-size:26px;background-color:#202020;text-align:center;padding: 12px 0;line-height:36px; border-radius:5px 5px 0 0;">
                    Other Ranking Lists&nbsp;
                </div>
                <div class="row">
                    
                @foreach($catData as $key=>$category)
                    <div class="small-4  columns text-center">
                        <div class='ranking-lists-titles' >
                            {{$category->title}}
                        </div>
                        <img class='ranking-lists-title-images' src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/lists/images/{{{$category->image or 'best-food_icon.png'}}}"  alt=""/>
                    </div>
                @endforeach

                </div>          
                <div class="row">
                <div class="small-10 columns small-centered" style="text-align:center;">
                    <a  href='/ranking/categories' class="ranking-button"> See all ranking lists </a>
                </div>
                </div>                
            </div>
        </div>
    </div>


    <!-- start of Gary's informative Ranking article - start -->

    <div class="row plex-ranking-meaning-container">
        <div class="column small-12">
            
            <div class="row plex-college-rank-header">
                <div class="column small-12 small-only-text-center">
                    What are Plexuss College Rankings?
                </div>
            </div>

            <div class="row" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/ranking/images/ranking-header.jpg, (default)], [https://s3-us-west-2.amazonaws.com/asset.plexuss.com/ranking/images/ranking-header.jpg, (large)]">
                <div class="column small-12">
                    <!-- make width 100% and height like 300px or so -->
                </div>
            </div>

            <!-- row 1 untitled section -->
            <div class="row">
                <div class="column small-12 medium-6">
                    <p>College rankings can be a useful tool for a student in their college search process. However, the amount of college rankings out there can make things confusing. Comparing the ranking of these different sources can be time consuming and students may also find it difficult to determine whether a particular source is reputable or not. To alleviate some of these problems, Plexuss.com has created Plexuss College Rankings.</p>
                </div>
                <div class="column small-12 medium-6 text-center thumbnail-container">
                    <img data-reveal-id="college-ranking-video" src="/images/pages/ranking-video.png" alt="Plexuss College Video">
                </div>
                <div class="column small-12">
                    <p>In addition to conveniently gathering rankings of several sources on our site, we've aggregated the college rankings of what we consider to be the most reputable ranking sources in order to create our own ranking system. Think of Plexuss College Rankings as a rank summary of sorts, a convenient and overall ranking system which easily tells you what college ranking systems agree on. Because our rankings are based on the aggregation of other college rankings, the weight of college characteristics it uses highly depends on the criteria utilized by the sources that we've chosen. The benefit in doing this is that with Plexuss College Rankings nearly all of the college characteristics that students and college experts care about are taken into account.</p>
                </div>
            </div>


            <!-- row 2 selection of college ranking sources section -->
            <div class="row">
                <div class="column small-12 medium-6">
                    <div>
                        <h4><strong>Selection of College Ranking Sources</strong></h4>
                    </div>
                    <div>
                        <p>We examined all of the college rankings that we could find and chose five which we believe to have the most methodologically sound criteria. Ultimately, we chose rankings by <a href="http://colleges.usnews.rankingsandreviews.com/best-colleges" target="_blank">U.S. News (National University Rankings)</a>, <a href="http://www.forbes.com/top-colleges/" target="_blank">Forbes' America's Top Colleges</a>, <a href="http://www.timeshighereducation.co.uk/world-university-rankings/" target="_blank">Times Higher Education World University Rankings (Reuters)</a>, <a href="http://www.topuniversities.com/qs-world-university-rankings" target="_blank">QS World University Rankings</a>, and <a href="http://www.shanghairanking.com/" target="_blank">The Academic Ranking of World Universities (Shanghai Rankings)</a>.</p>
                    </div>
                </div>
                <div class="column small-12 medium-6">
                    <img class="thumbnail-to-fullview" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/ranking/images/ranking-table-1_thumbnail.jpg" alt="plexuss ranking table" data-img-name="ranking-table-1.PNG">
                    <p><small>Ranking criteria categorized and grouped into percentages to reflect overall weight given to each category by each ranking source.</small></p>
                </div>
                <div class="column small-12">
                    <p>In an effort to make our rankings more impartial, we included rankings produced by non-US countries such as Times Higher Education, QS World University Rankings (United Kingdom) and Academic Ranking of World Universities (China). We also took into account a ranking's popularity, general reception, and availability of full methodology when deciding our sources. Though not as established as the other ranking systems we've selected, we decided to use Forbes' rankings as a reference to diversify the weights used by our aggregated ranking criteria as displayed in the table above.</p>
                </div>
            </div>


            <!-- row 3 plexuss college rankings calculation section -->
            <div class="row">
                <div class="column small-12">
                    <div>
                        <h4><strong>Plexuss College Rankings Calculation</strong></h4>
                    </div>
                    <div>
                        <p><i>Warning</i>: The following section goes into the exact details of how we came up with our rankings. We suggest skipping this section unless you are  truly interested in discovering how our rankings are calculated and are not bored by numbers.</p>
                    </div>
                    <div>
                        <p>To aggregate our ranking sources, we first standardized all rankings on a similar, compatible scale ranging between 0 and 100. We did this by converting college ranks to scores which utilize percentiles.</p>
                    </div>
                </div>

                <div class="column small-12 medium-6">
                    <p>For each ranking source, each college's rank is converted to a percentage which indicates the number of schools that are ranked higher or equal to when compared to that college. Assuming no ties for first place, a college which takes the #1 spot on a ranking system would receive 0% (see Princeton University in the table above, which displays a sample of college rankings from U.S. News). To make these scores more intuitive, each converted score is subtracted from 1, and then the resulting value is multiplied by 100. This now means that schools that are better ranked have higher adjusted scores, and that all schools will have an adjusted score between 0 and 100.</p>
                </div>

                <div class="column small-12 medium-6 thumbnail-container">
                    <img class="thumbnail-to-fullview" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/ranking/images/ranking-table-2_thumbnail.jpg" alt="plexuss ranking table" data-img-name="ranking-table-2.PNG">
                </div>

                <div class="column small-12">
                    <p>With 202 schools ranked in U.S. News' 2014-2015 National Universities Ranking, schools positioning around rank 100 would receive a converted score close to 50 (see Auburn University, University of Dayton, and Buffalo State SUNY). Meanwhile, colleges ranking near 150 would receive an adjusted score around 25 (see the University of Mississippi).</p>
                    <p>The same process is applied to other ranking sources. After conducting some more adjustments (explained more below), the average of the adjusted scores for each college are taken. Schools are then sorted based on these averaged scores; the college with the highest average adjusted score is given the Plexuss rank of "1", the college with the second highest average adjusted score is given the Plexuss rank of "2", and so on.</p>
                </div>

                <div class="column small-12 medium-6">
                    <p>We dealt with missing rankings by giving colleges with four or five rankings the highest ranks. Colleges with three rankings are ranked next, followed by colleges with only two rankings, and finally, colleges with only a single ranking. Colleges that are not ranked by any of the sources that we chose are not assigned a Plexuss College Ranking. Colleges with four or five rankings are ranked together (instead of separately) to give leeway to what some may consider as arbitrary reasoning for why some of our sources excluded ranking certain colleges. Take for example, the fact that Times Higher Education Rankings automatically excluded colleges which have produced fewer than 1000 research articles between the years 2007 and 2011.</p>
                </div>

                <div class="column small-12 medium-6 thumbnail-container">
                    <img class="thumbnail-to-fullview" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/ranking/images/ranking-table-3_thumbnail.jpg" alt="plexuss ranking table" data-img-name="ranking-table-3.PNG">
                </div>

                <div class="column small-12">
                    <p>
                        Colleges which were given a ranged ranking such as "200 - 225" are given a ranking equivalent to the average of the numbers in that range (i.e., the ranged ranking of 200-225 gives the average  212.5). This applies to Times Higher Education and Shanghai rankings. For sources which rank international colleges, rankings are adjusted as if the original source were only ranking colleges in the United States. For example, QS World University Rankings (2014 - 2015) ranked Harvard as 4th internationally, however, we are displaying an adjusted rank of 2 for Harvard because it is the second highest ranked university in the United States. 
                    </p>
                </div>
            </div>


            <!-- row 4 suggestion to students section -->
            <div class="row">
                <div class="column small-12">
                    <h4><strong>A suggestion to students</strong></h4>
                </div>
                <div class="column small-12">
                    <p><i>We do not recommend using Plexuss College Rankings as the sole criteria in your college selection process.</i></p>
                </div>
                <div class="column small-12">
                    <p>
                        Colleges that are on top of our rankings will not necessarily be the best colleges for all students. We recommend using these rankings in conjunction with other features on our site. <a href="/comparison" target="_blank">Compare colleges</a> based on the college characteristics that are important to you. Take into account your engagement and interaction with colleges through our <a href="/portal" target="_blank">recruitment portal</a> and <a href="/chat" target="_blank">chat</a>. Come up with your decision based on criteria that matters most to you.
                    </p>
                </div>
                <div class="column small-12">
                    <p>
                        Ultimately, we want you to decide which college is best for you, and <a href="/ranking/listing" target="_blank">Plexuss College Rankings</a> is just one of the many tools that we are providing to help get you there.
                    </p>
                </div>
                <div class="column small-12">
                    <p>
                        <strong>Should you have questions, criticisms or feedback on how we could improve Plexuss College Rankings please contact us at support&commat;plexuss.com .</strong>
                    </p>
                </div>
                <div class="column small-12">
                    <p><small>Ranking calculations used in this article are from Plexuss’ 2014-2015 College Rankings. Our current rankings may not reflect the numbers listed here.</small></p>
                </div>
            </div>

        </div>
    </div>

    <!-- start of Gary's informative Ranking article - end -->

    <!-- data table img full view - start -->
    <div id="ranking-data-fullview-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
        <!-- img will get injected here -->
        <img src="" alt="Plexuss College Rank">
    </div>    
    <!-- data table img full view - end -->


    <!-- create acct engagement -->
    <div class="row small-collapse medium-uncollapse hide-for-large-up">
        <div class="column small-12">
            @if( isset($signed_in) && $signed_in == 0 )
                @include('private.includes.right_side_createAcct_ranking')
            @endif
        </div>
    </div>



 

@stop

<?php
    // @section('sic')
    //    <!-- SIC -->
    //     @include('includes.smartInteractiveColumn')
    // @stop
?>