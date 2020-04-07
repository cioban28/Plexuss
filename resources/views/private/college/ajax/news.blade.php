<?php
	$collegeData = $college_data;
	// dd($collegeData);
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
<!-- news article -->
<div class="row">
	@if (isset($collegeMappings) && count($collegeMappings) > 0)
		<div class="small-12 columns quad news">
			FROM THE QUAD
		</div>
		<div class="small-12 columns no-padding bg-ext-black radial-bdr">
			@foreach($collegeMappings as $mapping)
				<?php $news_article = \App\NewsArticle::where('id',$mapping->news_id)->first();?>
				<div class="row">
					<div class="small-2 columns">
						<div class="survival-guide-img news">
							@if ($news_article['img_sm'] != '')
								<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$news_article['img_sm']}}" alt="{{$college_name}} image" class="news-article-img">
							@else
								<img src="/images/colleges/default-college-page-photo_overview.jpg" alt="Default College image" class="news-article-img">
							@endif
						</div>
					</div>
					<div class="small-10 columns">
						<div class="suvival-guide-title news">
							<a href="/news/article/{{$news_article['slug']}}" target="_blank">{{$college_name}} - Survival Guide</a>
						</div>
						<div class="survival-guide-desc news">
							{{$news_article['meta_description']}}
						</div>
					</div>
				</div>
			@endforeach
		</div>
	@endif
</div>
<div class="clearfix-padding"></div>
<div class="row news-container">
	<div class="column small-12 overview-news-area radial-bdr">
		@if(isset($news) && !empty($news))

			@foreach($news as $newsItem)
			<div class="row news-item-container">
				<div class="column small-2 text-center">
					<a href="{{$newsItem['url'] or '#'}}" target="_blank" class="news-related" data-slug="{{$collegeData->slug or ''}}">
						<img src="{{$newsItem['image']['thumbnail']['contentUrl'] or 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/icons/default_news_icon.png'}}" width='100' height="100"/>
					</a>
				</div>
				<div class="column small-10 end">
					<div class="row">
						<a href="{{$newsItem['url'] or '#'}}" target="_blank" class="news-related" data-slug="{{$collegeData->slug or ''}}">
							{{$newsItem['name'] or ''}}
						</a>
					</div>
					<div class="row news-desc">
						{{$newsItem['description'] or ''}}
					</div>
					<div class="row text-right news-date">
						<span>{{$newsItem['provider'][0]['name'] or ''}}</span>&nbsp;&nbsp;-&nbsp;&nbsp; 
						<span>{{$newsItem['datePublished'] or ''}}</span>
					</div>
				</div>
			</div>
			@endforeach
		@else
			<div class="row news-item-container no-news">
				<div class="column small-12 text-center">
					Sorry, we couldn't find any news associated with this school.
				</div>
			</div>

		@endif
	</div>
</div>