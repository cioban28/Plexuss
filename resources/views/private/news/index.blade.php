<?php 
    // dd($data);
?>


@extends('private.news.master')

@section('sidebar')
    <div class='row'>
        <div class='column small-12 hide-for-small-only'>
                <!--
                <div class="large-12 columns side-bar-news page-right-side-bar side-bar-1 radius-4"></div>
                -->
                
                <!-- this condition checks to see if the user's profile status is at zero, if so, display the 'get started' right hand bar, otherwise, display nothing
                @if ($profile_perc == 0)
                <div class="row">
                    <div class="large-12 columns page-right-side-bar side-bar-2 radius-4">
                        <div class="text-center">
                            <p class="step-number">1</p>
                        </div>
                        <p class="right-bar-heading white">Get Started</p>
                        <p class="right-bar-para white">Wondering why your indicators are at zero?</p>
                        <p class="right-bar-para white">You need a profile for the recruitment process to begin.</p>
                        <div class="large-12 text-center">
                            <a href="/profile" class="button get-started-button">Start your Profile</a>
                        </div>
                    </div>
                </div>
                @endif-->

                
               <!-- <div class="large-12 columns side-bar-news page-right-side-bar side-bar-1 radius-4"></div>-->
                
				<!-- -->
                @if( isset($signed_in) && $signed_in == 0 )
                    @include('private.includes.right_side_createAcct_news')
                @endif
        </div>
            

            <div class='column small-12 hide-for-small-only'>              

                @if( isset($signed_in) && $signed_in == 1 )
                    @include('private.includes.invite_friends_right_side')
                @endif

                <!-- adsense area -->
                @include('private.includes.adsense-307x280')


                @include('private.includes.right_side_get_started')
				@include('private.includes.right_side_footer')
				
            </div>
    </div>
@stop

@section('content')    

    <!-- desktop menu -->
    @include('private.news.newsNavigation')
   


    <!-- mobile slider -->
    <div class='row collapse' style='display:none;'>
        <div class="column small-12">
            <div id='owl-news' class='owl-carousel owl-theme'>
                @if(isset($newsdata) && $newsdata!='')
                    @foreach($newsdata as $news)  
                        <div class="item">
                            <span class="category-tag">{{$news->cat}}</span>
                            <div class="mobile-news-heading-div">{{$news->title}}</div>
                            <a href="/news/article/{{$news->slug}}">
                                <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$news->img_sm}}" title="{{$news->title}}" alt="{{$news->title}}" />
                            </a>
                        </div>
                    @endforeach
                @endif 
            </div>
        </div>
    </div>
    <!-- end mobile slider -->



    <?php $cat_bck='#FF6666';?>

    <div class="news-content-container">

        <!-- feature box desktop only -->
        @include('private.news.featured')


        <!-- //////////////////// main content area ////////////// -->
        @if(isset($catName) && $catName == "college-essays")
            @include('private.news.collegeEssays')
          
        @elseif($currentPage == 'quad-testimonials')
            @include('private.news.testimonials')   
        @else     

            <div id="container-box" class="js-masonry row"  data-masonry-options='{ "itemSelector": ".newsitem" }'>

                    <!--<div class="item" id="load_data"></div>-->
                    @if(isset($newsdata) && $newsdata!='')
                    @foreach($newsdata as $news)  
        			<div class='column small-12 medium-6 large-4 newsitem'>
        				<div class='news-article-box row collapse' data-articlePin="{{$news->subcat}}">
        					<div class='small-8 small-push-4 medium-12 medium-reset-order column '>
        						<div class='row collapse'>
        							<div class='column newsBoxTitle'>
        								<a class='bc-heading news-title-link' href='/news/article/{{$news->slug}}'>{{$news->title or ''}}</a>
        							</div>
        							<div class='hide-for-small-only column newboxauthor'>
        								{{ $news->visible_author or 'Unknown'}} | {{$news->created_at}}
        							</div>
        						</div>
        					</div>
        					<div class='small-4 small-pull-8 column medium-12 medium-reset-order'>
        						<div class='bc-image text-center' style=''>
                                @if( isset($news->has_video) && $news->has_video == 1 )
                                    <div class="layer-container" data-id='{{$news->id}}'>
                                        <img src="{{$news->img_sm}}" alt="" />
                                        <div class="layer">
                                            <div class="playbtn text-center">
                                                <div class="play-arrow"></div>
                                            </div>
                                        </div>
                                    </div>
                                @else
            						@if($news->img_sm!='')
            							<a href='/news/article/{{$news->slug}}'>
            								<img class='news-img-title' src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$news->img_lg}}' title='{{$news->title}}' alt='{{$news->title}}' />
            							</a>
            						@else				
            							<a href='/news/article/{{$news->slug}}'>
            								<img class='news-img-title nophoto-width' src='/images/no_photo.jpg' title='Image' alt='Image' />
                                        </a>
            						@endif							
                                @endif
        						<div class='category-badge' data-categoryName="{{$news->subcat}}">{{$news->subcat}}</div>
        						</div>
        					</div>
        					<div class='hide-for-small-only  small-8 medium-12 column'>
        						<div class='row collapse'>
        							<div class='column newsBoxdescription'>
        								<a href='/news/article/{{$news->slug}}'>
        									 {{substr(strip_tags($news->content),0,100)}}..
        								</a>
        							</div>
        							<div class='column hide-for-small-only newboxseemore'>
                                        @if( isset($news->has_video) && $news->has_video == 1 )
            								<a href='/news/article/{{$news->slug}}'>Watch video</a>
                                        @else
                                            <a href='/news/article/{{$news->slug}}'>See full article</a>
                                        @endif
        							</div>
        						</div>
        					</div>
        				</div>
        			</div>
                    @endforeach
                @endif <!--  end if newsdata -->
                <?php 
                // <!-- <noscript>
                // <div id="PaginatorNews" class="small-12 columns" text-align="center" style="display:block;">{{$newsdata->links()}}</div>
                // </noscript> -->
                ?>
            
        </div>

        <div id="loadmoreajaxloader" style="display:none;"><center><img src="/images/colleges/loading.gif" alt=""/></center></div>
        @endif
        
        <!--<div class="clearfix">&nbsp;</div>-->
        <div>&nbsp;&nbsp;</div>
    </div>



    @if($currentPage != 'quad-testimonials')
    <!-- using foundation modal to create lightbox -->
    <div id="lightbox" class="reveal-modal text-center" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
        <!--video script inject here-->
        <div class="clearfix close-lightbox">
            <div class="right">
                <a class="close-reveal-modal">&#215;</a>
            </div>
        </div>

        <div class="iframe-container">
            <iframe src="" frameborder="0"></iframe>
            <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/loading.gif" alt="Loading gif">
        </div>

        <div class="news-owl-carousel owl-carousel owl-theme on-index">
            @if(isset($newsdata))
            @foreach($newsdata as $news)
                @if( $news->has_video == 1 )
                    <div class="item layer-container" data-id='{{$news->id}}'>
                        <img src="{{$news->img_sm}}" alt="" />
                        <div class="name-layer text-center">
                            <div>{{$news->title}}</div>
                        </div>
                    </div>
                @endif
            @endforeach
            @endif
        </div>
    </div>
    @endif
    
    <!-- Code to get Data From News Controller -->
    
    <script type="text/javascript">

	/* Infinite Scroll Functions */
	var PageNumber="{{$page}}";
	var AjaxHold=0;

	function scrollinfinite(lastDataId){
		PageNumber++;
		AjaxHold=1;
		$('div#loadmoreajaxloader').show();
		$.ajax({
		url: "{{URL::action('NewsController@newsAjaxData')}}",
		data: {page:PageNumber,order:'{{$order}}',cat_id:'{{$cat_id}}',sub_cat_id:'{{$sub_cat_id}}',flagCat:'{{$flagCat}}'},
		method: "POST",
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        },
		success: function(html){
			AjaxHold=0;
			//alert("ss"+html[0]+"ss");
			
			//alert('a');
			//$('#load_data').html(html);
			$("#container-box").append(html);

			imagesLoaded( '#container-box', function() {

				$('#container-box').masonry('reloadItems').masonry()

			});

			$('div#loadmoreajaxloader').hide();

			var order="";
			if('{{$order}}'=='asc'){
				order='?order=asc';	
			}						
		},
		error:function(){
			$('div#loadmoreajaxloader').html('');
		}
		});
	}
	</script>
@stop
