<?php 
    
    // dd($featured);
?>

 <!-- feature box desktop only -->
 @if(isset($featured) && !empty($featured))
    <div class='row show-for-medium-up destop-only-featured-box collapse'>
       
                
        <div class='column small-12'>
            <div class="new-slider-block row" data-equalizer>
                <!-- left column  -->
                <div class="leftfeaturecol column small-9" data-equalizer-watch>
                    <div class=" slider-image-left featured-box" data-id="{{$featured[0]->id or ''}}">
                        @if( isset($featured) && isset($featured[0]->has_video) && $featured[0]->has_video == 1 )
                            <div class="layer-container" data-id='{{$featured[0]->id}}'>
                                <img src="{{ $featured[0]->img_sm }}" alt="{{$featured[0]->title}}" class="hide-for-small-only lg" />
                                <div class="layer">
                                    <div class="playbtn text-center">
                                        <div class="play-arrow"></div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="seemore-btn">
                                @if(isset($featured[0]->img_lg) && $featured[0]->img_lg != '')
                                    <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$featured[0]->img_lg}}" class="hide-for-small-only" alt="{{@$news->title}}"/>
                                @else
                                     <img src="/images/no_photo.jpg" class="hide-for-small-only no-photo-large" alt="{{@$news->title}}"/>
                                @endif
                            </div>
                        @endif
                        <div class="image-overlay hide-for-small-only">
                            <div class="overlay-inner">
                                <div class="news-overlay-heading">
                                    {{  isset($featured[0]->title) ? $featured[0]->title : ''}}<br/>
									
									<br>
									<span class="blog-overlay-link">
                                        @if( isset($featured[0]->has_video) && $featured[0]->has_video == 1 )
									        <a href="/news/article/{{$featured[0]->slug or ''}}">Watch video</a>
                                        @else
                                            <div class="seemore-btn text-left mt5">See full article</div>
                                        @endif
									</span>
									<!--/////////////// SOCIAL MEDIA BUTTONS \\\\\\\\\\\\\\\-->
                                    @if( isset( $share_buttons))
    									<div class="share-buttons-black">
    										@include('public.includes.shareButtons')
    									</div>
                                    @endif
									<!--\\\\\\\\\\\\\\\ SOCIAL MEDIA BUTTONS ///////////////-->
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!--  right column  -->
                <div class="rightfeaturecol column small-3" data-equalizer-watch>
                    <div class="slider-right slider-right-1 featured-box" data-id="{{$featured[1]->id or ''}}">
                        @if( isset($featured[1]->has_video) && $featured[1]->has_video == 1 )
                            <div class="layer-container" data-id='{{$featured[1]->id}}'>

                                @if(isset($featured[1]->img_sm) && $featured[1]->img_sm != '')
                                    <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$featured[1]->img_sm }}" alt="{{$featured[1]->title}}" class="hide-for-small-only sm" />
                                @else
                                    <img src="/images/no_photo.jpg" alt="{{$featured[1]->title}}" class="hide-for-small-only sm" />
                                @endif
                                 
                                <div class="layer">
                                    <div class="playbtn text-center">
                                        <div class="play-arrow"></div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="seemore-btn">
                                 @if(isset($featured[1]->img_sm) && $featured[1]->img_sm != '')
                                    <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$featured[1]->img_sm }}" alt="{{$featured[1]->title or ''}}" class="hide-for-small-only sm" />
                                @else
                                    <img src="/images/no_photo.jpg" alt="{{$featured[1]->title or ''}}" class="hide-for-small-only sm" />
                                @endif
                            </div>
                        @endif
                        <div class="image-overlay">
                            @if( isset($featured[1]->has_video) && $featured[1]->has_video == 1 )
                                <p>{{ $featured[1]->title or ''}} <br /> <span>
                                <a href="/news/article/{{$featured[1]->slug or ''}}">Watch video</a></span></p>
                            @else
                                <p>{{ $featured[1]->title or ''}} <br /> <span>
                                <div class="seemore-btn text-left ml5 mt5">See full article</div></span></p>
                            @endif
                        </div>
                    </div>
                    
                    <div class="slider-right slider-right-2 featured-box"  data-id="{{$featured[2]->id or ''}}">
                        @if( isset($featured[2]->has_video) && $featured[2]->has_video == 1 )
                            <div class="layer-container" data-id='{{$featured[2]->id}}'>
                                 @if(isset($featured[2]->img_sm) && $featured[2]->img_sm != '')
                                    <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$featured[2]->img_sm }}" alt="{{$featured[2]->title}}" class="hide-for-small-only sm" />
                                @else
                                    <img src="/images/no_photo.jpg" alt="{{$featured[2]->title}}" class="hide-for-small-only sm" />
                                @endif
                                <div class="layer">
                                    <div class="playbtn text-center">
                                        <div class="play-arrow"></div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="seemore-btn">
                                 @if(isset($featured[2]->img_sm) && $featured[2]->img_sm != '')
                                    <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$featured[2]->img_sm }}" alt="{{$featured[2]->title or ''}}" class="hide-for-small-only sm" />
                                @else
                                    <img src="/images/no_photo.jpg" alt="{{$featured[2]->title or ''}}" class="hide-for-small-only sm" />
                                @endif
                            </div>
                        @endif
                        <div class="image-overlay">
                            @if( isset($featured[2]->has_video) && $featured[2]->has_video == 1 )
                                <p>{{$featured[2]->title or ''}} <br /> <span><a href="/news/article/{{$featured[2]->slug or ''}}">Watch video</a></span></p>
                            @else
                                <p>{{$featured[2]->title or ''}} <br /> <span>
                                <div class="seemore-btn text-left ml5 mt5">See full article</div></span></p>
                            @endif
                        </div>
                    </div>

                
                </div>
            </div>
    	</div>
    </div>

@endif