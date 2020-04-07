@if(isset($catName) && $catName != "college-essays")
           
    <div class='row show-for-medium-up destop-only-featured-box'>
                   
                <div class='column small-12'>
                    
                    <div class="new-slider-block row" data-equalizer>
                        <!-- left column  -->
                        <div class="leftfeaturecol column small-9" data-equalizer-watch>
                            <div class=" slider-image-left">
                                @if( isset($featured_rand_news[0]->has_video) && $featured_rand_news[0]->has_video == 1 )
                                    <div class="layer-container" data-id='{{$featured_rand_news[0]->id}}'>
                                        <img src="{{ $featured_rand_news[0]->img_sm }}" alt="{{$featured_rand_news[0]->title}}" class="hide-for-small-only lg" />
                                        <div class="layer">
                                            <div class="playbtn text-center">
                                                <div class="play-arrow"></div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <a href="/news/article/{{$featured_rand_news[0]->slug}}">
                                        <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$featured_rand_news[0]->img_lg}}" class="hide-for-small-only" alt="{{@$news->title}}"/>
                                    </a>
                                @endif
                                <div class="image-overlay hide-for-small-only">
                                    <div class="overlay-inner">
                                        <div class="news-overlay-heading">
                                            {{$featured_rand_news[0]->title}}<br/>
                                            <span>
                                                {{ isset($featured_rand_news[0]->external_author) ? 'by ' . $featured_rand_news[0]->external_author : 'via ' . $featured_rand_news[0]->external_name }}
                                            </span>
                                            <br>
                                            <span class="news-overlay-link">
                                                @if( isset($featured_rand_news[0]->has_video) && $featured_rand_news[0]->has_video == 1 )
                                                    <a href="/news/article/{{$featured_rand_news[0]->slug}}">Watch video</a>
                                                @else
                                                    <a href="/news/article/{{$featured_rand_news[0]->slug}}">See full article</a>
                                                @endif
                                            </span>
                                            <!--/////////////// SOCIAL MEDIA BUTTONS \\\\\\\\\\\\\\\-->
                                            <div class="share-buttons-white">
                                                @include('public.includes.shareButtons')
                                            </div>
                                            <!--\\\\\\\\\\\\\\\ SOCIAL MEDIA BUTTONS ///////////////-->
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!--  right column  -->
                        <div class="rightfeaturecol column small-3" data-equalizer-watch>
                            <div class="slider-right slider-right-1">
                                @if( isset($featured_rand_news[1]->has_video) && $featured_rand_news[1]->has_video == 1 )
                                    <div class="layer-container" data-id='{{$featured_rand_news[1]->id}}'>
                                        <img src="{{ $featured_rand_news[1]->img_sm }}" alt="{{$featured_rand_news[1]->title}}" class="hide-for-small-only sm" />
                                        <div class="layer">
                                            <div class="playbtn text-center">
                                                <div class="play-arrow"></div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <a href="/news/article/{{$featured_rand_news[1]->slug}}">
                                        <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$featured_rand_news[1]->img_sm}}" class="hide-for-small-only" alt="{{@$news->title}}" />
                                    </a>
                                @endif
                                <div class="image-overlay">
                                    @if( isset($featured_rand_news[1]->has_video) && $featured_rand_news[1]->has_video == 1 )
                                        <p>{{$featured_rand_news[1]->title}} <br /> <span><a href="/news/article/{{$featured_rand_news[1]->slug}}">Watch video</a></span></p>
                                    @else
                                        <p>{{$featured_rand_news[1]->title}} <br /> <span><a href="/news/article/{{$featured_rand_news[1]->slug}}">See full article</a></span></p>
                                    @endif
                                </div>
                            </div>
                            
                            <div class="slider-right slider-right-2">
                                @if( isset($featured_rand_news[2]->has_video) && $featured_rand_news[2]->has_video == 1 )
                                    <div class="layer-container" data-id='{{$featured_rand_news[2]->id}}'>
                                        <img src="{{ $featured_rand_news[2]->img_sm }}" alt="{{$featured_rand_news[2]->title}}" class="hide-for-small-only sm" />
                                        <div class="layer">
                                            <div class="playbtn text-center">
                                                <div class="play-arrow"></div>
                                            </div>
                                        </div>
                                    </div>
                                @else
                                    <a href="/news/article/{{$featured_rand_news[2]->slug}}">
                                        <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$featured_rand_news[2]->img_sm}}" class="hide-for-small-only" alt="{{@$news->title}}"style="width: 100%" />
                                    </a>
                                @endif
                                <div class="image-overlay">
                                    @if( isset($featured_rand_news[2]->has_video) && $featured_rand_news[2]->has_video == 1 )
                                        <p>{{$featured_rand_news[2]->title}} <br /> <span><a href="/news/article/{{$featured_rand_news[2]->slug}}">Watch video</a></span></p>
                                    @else
                                        <p>{{$featured_rand_news[2]->title}} <br /> <span><a href="/news/article/{{$featured_rand_news[2]->slug}}">See full article</a></span></p>
                                    @endif
                                </div>
                            </div>


                        </div>
                    </div>
                </div>
    </div>
@endif








 