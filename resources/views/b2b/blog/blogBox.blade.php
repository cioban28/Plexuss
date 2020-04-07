<?php 
	// dd($articles);

?>
@if(isset($articles)  && !empty($articles))
@foreach($articles as $news)              
	<div class='column small-12 medium-6 large-3 end newsitem' data-slug="{{$news->slug or ''}}" data-id="{{$news->id or ''}}">
		<div class='blog-article-box row collapse' data-articlePin="{{$news->subcat or ''}}">
			<div class='small-8 small-push-4 medium-12 medium-reset-order column '>
				<div class='row collapse'>
					<div class='column newsBoxTitle'>
						<div class='bc-heading blog-title-link seemore-btn'>{{ $news->title or ' '}}</div>
					</div>
					<div class='hide-for-small-only column newboxauthor'>
						{{ $news->external_author or 'Unknown Author' }} &nbsp; | &nbsp;  {{ $news->timeAgo or ''}}
					</div>
				</div>
			</div>
			<div class='small-4 small-pull-8 column medium-12 medium-reset-order'>
				<div class='bc-image' style=''>
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
                    	<div class="seemore-btn">
    						@if(isset($news->img_sm) && $news->img_sm!='')
    							
    								<img class='news-img-title' src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$news->img_lg}}' title='{{$news->title}}' alt='{{$news->title}}' />
    							
    						@else				
    							
    								<img class='news-img-title box-no-photo' src='/images/no_photo.jpg' title='Image' alt='Image' />
                                
    						@endif	
						</div>						
                    @endif
			
				</div>
			</div>
			<div class='hide-for-small-only  small-8 medium-12 column'>
				<div class='row collapse'>
					<div class='column blogBoxdescription seemore-btn'>
						
							{{ substr(strip_tags($news->content),0,100) }} 
							
					</div>
					<div class='column hide-for-small-only blogboxseemore seemore-btn'>
                        @if( isset($news->has_video) && $news->has_video == 1 )
							Watch video
                        @else
                           See full article
                        @endif
					</div>
				</div>
			</div>
		</div>
	</div>
@endforeach
@endif