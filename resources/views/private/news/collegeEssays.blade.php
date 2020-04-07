<?php 
// dd($data);
?>

<div class="mt20"></div>

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
        <div class='news-article-box row collapse admitsee-box' data-articlePin="{{$catName or 'college-essays'}}">



           

            <div class='small-12 medium-12 medium-reset-order column admitsee-inner-cont'>
                 

                <div class="row">   
                    <div class="column small-10">
                        <!-- <div class="admitsee-title3">{{$news->external_author or ' '}}</div> -->
                        <!-- <div class="admitsee-print">Harvard '17  &nbsp; | &nbsp; La Blah, CA, USA</div> -->
                        <div class="admitsee-title3">{{$news->title or ' '}}</div>
                        <div class="admitsee-print">by {{$news->external_author or ' '}}</div>
                        

                    </div>

                     <div class=" column small-2 viewed-container">
                        @if(isset($news->hasViewed) && !empty($news->hasViewed))
                        <div class="viewed-icon">&#10003;</div>
                        <div class="viewed-txt">Viewed</div>
                       
                        @endif
                    </div>
                </div>
                
                    
                <div class="text-center mt15">
                    <div class="round-portrait">
                        @if($news->authors_img!='')
                        <a href='/news/essay/{{$news->slug}}/essay'>
                            <img class='news-img-title' src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$news->authors_img}}' title='{{$news->title}}' alt='{{$news->title}}' />
                        </a>
                    @else               
                        <a href='/news/essay/{{$news->slug}}/essay'>
                            <img class='news-img-title' src='/images/no_photo.jpg' title='Image' alt='Image' />
                        </a>
                    @endif  
                    </div>
                </div>

                <div class="admitsee-print bottom-fade essay-excerpt-box">
                       {!!  $excerpt !!}
                </div>
                
                <div class="btn-container">
                    <div class="text-center"><a href='/news/essay/{{$news->slug}}/essay' class="view-essay-btn">View Essay</a></div>
                </div>
                  
            </div>

                   
            
        </div>
    </div>
    @endforeach
    @endif

    <noscript>
    <div id="PaginatorNews" class="small-12 columns" text-align="center" style="display:block;">{{$newsdata->links()}}</div>
    </noscript>
    
   
</div>


<div id="loadmoreajaxloader" style="display:none;">
    <center><img src="/images/colleges/loading.gif" alt=""/></center>
</div>
