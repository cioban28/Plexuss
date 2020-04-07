<?php
	// dd($newsdata);
?>
@foreach($newsdata as $news)
<div class='column small-12 medium-6 large-4'>
	<div class="news-article-box row collapse">
		<!-- Title -->
		<div class="small-8 small-push-4 medium-12 medium-reset-order column ">
			<div class='row collapse'>		
				<div class='column newsBoxTitle'>
					@if($news->cat == 'college essays')
					<a class="bc-heading news-title-link" href="/news/essay/{{ $news->slug }}/essay">{{ $news->title}}</a>
					@else
					<a class="bc-heading news-title-link" href="/news/article/{{ $news->slug }}">{{ $news->title}}</a>
					@endif
				</div>
				<div class='hide-for-small-only column newboxauthor'>
					{{ $news->visible_author }} | {{ $news->created_at}}
				</div>
			</div>
		</div>
		<!-- Image box -->
		<div class='small-4 small-pull-8 column medium-12 medium-reset-order'>
			<div class="bc-image home-news-img" style="text-align: center;">
				@if( isset($news->has_video) && $news->has_video == 1 )
					<a href="/news/article/{{ $news->slug }}">
						<div class="layer-container">
                            <img src="{{ $news->img_sm }}" alt="{{$news->title}}" class="hide-for-small-only sm" />
                            <div class="layer">
                                <div class="playbtn text-center">
                                    <div class="play-arrow"></div>
                                </div>
                            </div>
                        </div>
					</a>
				@else
					@if($news->news_subcategory_id == 9 || $news->news_subcategory_id == 10)
						@if(isset($news->authors_img) && $news->authors_img!='')
							@if($news->cat == 'college essays')
							<a href="/news/essay/{{ $news->slug }}/essay">
							@else
							<a href="/news/article/{{ $news->slug }}">
							@endif
								<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$news->img_sm}}" title="{{$news->title}}" alt="{{$news->title}}" />
							</a>
						@else
							<a href="/news/article/{{ $news->slug }}">
								<img class="home-news-nopic" src="images/no_photo.jpg" title="Image" alt="Image" />
							</a>
						@endif
					@else
						@if($news->img_sm!='')
							<a href="/news/article/{{ $news->slug }}">
								<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$news->img_sm}}" title="{{$news->title}}" alt="{{$news->title}}" />
							</a>
						@else
							<a href="/news/article/{{ $news->slug }}">
								<img src="images/no_photo.jpg" title="Image" alt="Image" />
							</a>
						@endif
					@endif
				@endif
				<a href="/news/article/{{ $news->slug }}">
				<div class="category-badge">
					{{ $news->subcat}}
				</div>
				</a>
			</div>
		</div>
		<!-- content & read more -->
		<div class="hide-for-small-only  small-8 medium-12 column">
			<div class="row collapse">
				<div class='column newsBoxdescription'>
					@if($news->cat == 'college essays')
					<a href="/news/essay/{{ $news->slug }}/essay">
					@else
					<a href="/news/article/{{ $news->slug }}">
					@endif
						{{ substr(strip_tags($news->content),0,100)}}..
					</a>
				</div>
				<div class='column hide-for-small-only newboxseemore'>
					@if( isset($news->has_video) && $news->has_video == 1 )
						<a href="/news/article/{{ $news->slug }}">Watch video</a>
					@elseif ($news->cat == 'college essays')
						<a href="/news/essay/{{ $news->slug }}/essay">See full essay</a>
					@else
						<a href="/news/article/{{ $news->slug }}">See full article</a>
					@endif
				</div>
			</div>
		</div>
	</div>
</div>
@endforeach