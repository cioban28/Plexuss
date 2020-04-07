<?php
// dd($data);
    if (isset($college_data)) {
        $collegeData = $college_data;
    }
?>

<!--///// social buttons div of holding \\\\\-->
<div id="share_div_of_holding"
	data-share_params='{
		"page_title":"{{ $collegeData->page_title }}",
		"image_prefix":"{{ $collegeData->share_image_path }}",
		"image_name":"{{ $collegeData->share_image }}"
	}'
></div>
<!--\\\\\ social buttons div of holding /////-->

<div class='row' style="border: solid 0px #ff0000;">
    <div class='column small-12'>
        <div style='display:block;'>
        	<div class="ranking-first-content column small-12">
            	<div class="row">
                	<div class="large-6 small-12 columns college-rank-divide no-padding">
                    	<div class="row">
        					<div class='small-12 column'>
        						<div class=' avg-rank-button'>
        							<div class="avg-rank-sub">PLEXUSS AVG. RANKING</div>
        							<div class="avg-rank-number">#{{$collegeData->plexuss or "N/A"}}</div>
        						</div>
        					</div>
        					<!--
        					<div class='small-2 column'>
        						&nbsp
        					</div>
        					-->
                        </div>
        				<div class='row'>
        					<div class='small-12 column text-center'>
                                <!--<span data-tooltip aria-haspopup="true" class="has-tip tip-right ranking-tooltip-box radius" title="Plexuss ranking takes the adjusted average of some of the most reputable and popular college ranking systems out there. It is based on the aggregation of other college rankings, so the weight of college characteristics it uses highly depends on the criteria utilized by the college rankings that we chose. This allows Plexuss ranking to cover nearly all of the college characteristics students care about.">
                                    <span class="ranking-tooltip-text">What is Plexuss Ranking?</span>
                                </span>-->
                                <a href="/ranking" class="ranking-tooltip-text">What is Plexuss Ranking?</a>
        					</div>
        				</div>
                        <div class="national-rank-panel">UNDERGRAD NATIONAL RANKING</div>
                        <br />

                        <div class="large-12 small-12 columns no-padding marg-10bot">
                            <div class="large-2 small-2 columns"><a href="https://www.usnews.com/best-colleges/rankings/national-universities" target="_blank"><img class="rank-logo-resize" src="/images/ranking/new_ranking_logos/usnews.png" alt="ranking-logo" /></a></div>
                            <div class="large-4 small-4 columns review-icons-text">US NEWS</div>
                            <div class="large-5 small-5 columns review-icons-rank">
                            #{{$collegeData->us_news or "N/A"}}
                            </div>
                        </div>
                        
                        <div class="large-12 columns no-padding marg-10bot">
                            <div class="large-2 small-2 columns"><a href="https://www.timeshighereducation.com/world-university-rankings/2017/world-ranking#!/page/0/length/25/sort_by/rank/sort_order/asc/cols/stats" target="_blank"><img class="rank-logo-resize" src="/images/ranking/new_ranking_logos/reuters.png" alt="ranking-logo" /></a></div>
                            <div class="large-4 small-4 columns review-icons-text">REUTERS</div>
                            <div class="large-5 small-5 columns review-icons-rank">
                            #{{$collegeData->reuters or "N/A"}}
                            </div>
                        </div>
                        
                        <div class="large-12 columns no-padding marg-10bot">
                            <div class="large-2 small-2 columns"><a href="https://www.forbes.com/top-colleges/list/#tab:rank" target="_blank"><img class="rank-logo-resize" src="/images/ranking/new_ranking_logos/forbes.png" alt="ranking-logo" /></a></div>
                            <div class="large-4 small-4 columns review-icons-text">FORBES</div>
                            <div class="large-5 small-5 columns review-icons-rank">
                            #{{$collegeData->forbes or "N/A"}}
                            </div>
                        </div>
                        
                        <div class="large-12 columns no-padding marg-10bot">
                            <div class="large-2 small-2 columns"><a href="https://www.topuniversities.com/university-rankings/world-university-rankings/2016" target="_blank"><img class="rank-logo-resize" src="/images/ranking/new_ranking_logos/qs-world.png" alt="ranking-logo" /></a></div>
                            <div class="large-4 small-4 columns review-icons-text">QS</div>
                            <div class="large-5 small-5 columns review-icons-rank">
                            	#{{$collegeData->qs or "N/A"}}
                            </div>
                        </div>
                        
                        <div class="large-12 columns no-padding marg-10bot">
                            <div class="large-2 small-2 columns"><a href="http://www.shanghairanking.com/ARWU2016.html" target="_blank"><img class="rank-logo-resize" src="/images/ranking/new_ranking_logos/shanghai.png" alt="ranking-logo" /></a></div>
                            <div class="large-4 small-4 columns review-icons-text">SHANGHAI</div>
                            <div class="large-5 small-5 columns review-icons-rank">
                                #{{$collegeData->shanghai_academic or "N/A"}}
                            </div>
                        </div>
                    </div>
                    
                    <div class="large-6 columns">
                    	<div class="rank-title-oneplace mt10">COLLEGE RANKING IN ONE PLACE</div>
                        <div class="rank-video-img">
                            <div class='row'>
                                <div class='column text-center'>
                                    <img style='cursor:pointer;' data-reveal-id="college-ranking-video" src="/images/video-images/rankings-video.png" alt="Plexuss Ranking Video">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <?php 
                /*?><div id="container-box" class="js-masonry">
                <div class="box-div" id="rank-total-box-panel1"><!--================--></div>
                <div class="box-div" id="rank-total-box-panel2"><!--================--></div>
                <div class="box-div" id="rank-total-box-panel3"><!--================--></div>
                <div class="box-div" id="rank-total-box-panel4"><!--================--></div>
                <div class="box-div" id="rank-total-box-panel5"><!--================--></div>
                <div class="box-div" id="rank-total-box-panel6"><!--================--></div>
                <div class="box-div" id="rank-total-box-panel7"><!--================--></div>
                <div class="box-div" id="rank-total-box-panel8"><!--================--></div>
                <div class="box-div" id="rank-total-box-panel9"><!--================--></div>
            </div><?php */?>
        </div>
    </div>
</div>

<!-- ranking pins row - start -->
<div class="row generated-ranking-pins-container" data-equalizer>

        <div class="column small-12 medium-6 large-4">
            @foreach( $collegeData->ranking_pins_col_one as $key => $pin )
            <div class="row">
                <div class="column small-12">
                    
                    <!-- pin title -->
                    <div class="pin-inner-container">
                        <div class="row pin-title">
                            <div class="column small-12 text-center" data-equalizer-watch>
                                {{$pin->title}}
                            </div>
                        </div>

                        <div class="row pin-content">
                            <div class="column small-6 pin-rank small-text-center large-text-left">
                                <div>RANKED</div>
                                <div>#{{$pin->rank_num or 'N/A'}}</div>
                            </div>
                            <div class="column small-6 small-text-left large-text-center pin-img">
                                @if( !empty($pin->image) )
                                <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/{{$pin->image or ''}}" alt="Ranking Pin Image">
                                @endif
                            </div>
                            <div class="column small-12 pin-descript">
                                @if( !empty($pin->rank_descript) && $pin->rank_descript != 'null' )
                                    <div class="descript-container">
                                        {{ strlen($pin->rank_descript) > 80 ? substr($pin->rank_descript, 0, 80) . '...' : $pin->rank_descript }}
                                    </div>

                                    @if( strlen($pin->rank_descript) > 80 )
                                        <div class="column small-12 text-center">
                                            <a href="" class="see-more-pin-descript-btn" data-half-descript="{{substr($pin->rank_descript, 0, 80) . '...'}}" data-full-descript="{{$pin->rank_descript}}" data-is-open="false">Show more</a>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <div class="column small-12 see-full-rank-article">
                                @if( $pin->source != '' || $pin->source != null )
                                    <a href="{{$pin->source}}" target="_blank" class="see-full-rank-article-btn">See full article</a>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            @endforeach
        </div>

       <div class="column small-12 medium-6 large-4">
            @foreach( $collegeData->ranking_pins_col_two as $key => $pin )
            <div class="row">
                <div class="column small-12">
                    
                    <!-- pin title -->
                    <div class="pin-inner-container">
                        <div class="row pin-title">
                            <div class="column small-12 text-center" data-equalizer-watch>
                                {{$pin->title}}
                            </div>
                        </div>

                        <div class="row pin-content">
                            <div class="column small-6 pin-rank small-text-center large-text-left">
                                <div>RANKED</div>
                                <div>#{{$pin->rank_num or 'N/A'}}</div>
                            </div>
                            <div class="column small-6 small-text-left large-text-center pin-img">
                                @if( !empty($pin->image) )
                                <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/{{$pin->image or ''}}" alt="Ranking Pin Image">
                                @endif
                            </div>
                            <div class="column small-12 pin-descript">
                                @if( !empty($pin->rank_descript) && $pin->rank_descript != 'null' )
                                    <div class="descript-container">
                                        {{ strlen($pin->rank_descript) > 80 ? substr($pin->rank_descript, 0, 80) . '...' : $pin->rank_descript }}
                                    </div>

                                    @if( strlen($pin->rank_descript) > 80 )
                                        <div class="column small-12 text-center">
                                            <a href="" class="see-more-pin-descript-btn" data-half-descript="{{substr($pin->rank_descript, 0, 80) . '...'}}" data-full-descript="{{$pin->rank_descript}}" data-is-open="false">Show more</a>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <div class="column small-12 see-full-rank-article">
                                @if( $pin->source != '' || $pin->source != null )
                                    <a href="{{$pin->source}}" target="_blank" class="see-full-rank-article-btn">See full article</a>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            @endforeach
        </div>

        <div class="column small-12 medium-6 large-4">
            @foreach( $collegeData->ranking_pins_col_three as $key => $pin )
            <div class="row">
                <div class="column small-12">
                    
                    <!-- pin title -->
                    <div class="pin-inner-container">
                        <div class="row pin-title">
                            <div class="column small-12 text-center" data-equalizer-watch>
                                {{$pin->title}}
                            </div>
                        </div>

                        <div class="row pin-content">
                            <div class="column small-6 pin-rank small-text-center large-text-left">
                                <div>RANKED</div>
                                <div>#{{$pin->rank_num or 'N/A'}}</div>
                            </div>
                            <div class="column small-6 small-text-left large-text-center pin-img">
                                @if( !empty($pin->image) )
                                <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/{{$pin->image or ''}}" alt="Ranking Pin Image">
                                @endif
                            </div>
                            <div class="column small-12 pin-descript">
                                @if( !empty($pin->rank_descript) && $pin->rank_descript != 'null' )
                                    <div class="descript-container">
                                        {{ strlen($pin->rank_descript) > 80 ? substr($pin->rank_descript, 0, 80) . '...' : $pin->rank_descript }}
                                    </div>

                                    @if( strlen($pin->rank_descript) > 80 )
                                        <div class="column small-12 text-center">
                                            <a href="" class="see-more-pin-descript-btn" data-half-descript="{{substr($pin->rank_descript, 0, 80) . '...'}}" data-full-descript="{{$pin->rank_descript}}" data-is-open="false">Show more</a>
                                        </div>
                                    @endif
                                @endif
                            </div>

                            <div class="column small-12 see-full-rank-article">
                                @if( $pin->source != '' || $pin->source != null )
                                    <a href="{{$pin->source}}" target="_blank" class="see-full-rank-article-btn">See full article</a>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            @endforeach
        </div>
</div>
<!-- ranking pins row - end -->

<script type="text/javascript">
//$(document).ready(function(e) {
	/* Get ranking box */
	/*getTotalRankingBoxes('#004358','MOST DEBT 2014','#23','<img src="/images/colleges/daily-beast-logo.png">','The Daily Beast','http://google.com','ranking-box','rank-total-box-panel1');
	
	getTotalRankingBoxes('#04A6AE','BEST LIBRARY','#10','<img src="/images/colleges/best-col-logo-big.png">','US NEWS','http://google.com','ranking-box','rank-total-box-panel2');
	
	getTotalRankingBoxes('#004358','TOP ROI','#18','<img src="/images/colleges/forbes-logo-big.png">','FORBES','http://google.com','ranking-box','rank-total-box-panel3');
	
	getTotalRankingBoxes('#004358','HEALTHIEST CAMPUS CAFÉS','#6','<img src="/images/colleges/busi-week-logo-big.png">','BUSINESS WEEK','http://google.com','ranking-box','rank-total-box-panel4');
	
	getTotalRankingBoxes('#004358','TOP COOKING SCHOOLS','#36','<img src="/images/colleges/princeton-logo-big.png">','THE PRINCETON REVIEW','http://google.com','ranking-box','rank-total-box-panel5');
	
	getTotalRankingBoxes('#004358','MOST DEBT 2014','23','<img src="/images/colleges/daily-beast-logo.png">','The Daily Beast','http://google.com','ranking-box','rank-total-box-panel6');
	
	getTotalRankingBoxes('#004358','MOST DEBT 2014','23','<img src="/images/colleges/daily-beast-logo.png">','The Daily Beast','http://google.com','ranking-box','rank-total-box-panel7');
	
	getTotalRankingBoxes('#004358','MOST DEBT 2014','23','<img src="/images/colleges/daily-beast-logo.png">','The Daily Beast','http://google.com','ranking-box','rank-total-box-panel8');
	
	getTotalRankingBoxes('#004358','MOST DEBT 2014','23','<img src="/images/colleges/daily-beast-logo.png">','The Daily Beast','http://google.com','ranking-box','rank-total-box-panel9');*/
	
	 //setResizeBox();
//});
</script>
