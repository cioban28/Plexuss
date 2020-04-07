<!-- //similar to news, but a different concept, so seperate blade in case diff -->



<div id="container-box" class="js-masonry row"  data-masonry-options='{ "itemSelector": ".newsitem" }'>
 <!--<div class="item" id="load_data"></div>-->
    @if(isset($newsdata) && $newsdata!='')
    @foreach($newsdata as $news)  
    <?php 
           // echo "<pre>";
           // print_r($news);
           // echo "</pre>";
           // exit();
           // dd($news);


            $excerpt = substr($news->basic_content, 0, 407);
            if($excerpt == false){ $excerpt = ' '; }
    ?>
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
                        @if($news->img_sm != '' && $news->img_lg)
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
    @endif

    
  <!--   <noscript>
    <div id="PaginatorNews" class="small-12 columns" text-align="center" style="display:block;">{{$newsdata->links()}}</div>
    </noscript>
     -->


</div>

<div id="loadmoreajaxloader" style="display:none;">
    <center><img src="/images/colleges/loading.gif" alt=""/></center>
</div>
