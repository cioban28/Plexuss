<?php
	$collegeData = $college_data;
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

<!-- image carousel -->
<div class="row img-vid-college-container">
	@if (isset($college_data->college_media) && !empty($college_data->college_media) )
		@if( isset($college_data->youtube_videos) && !empty($college_data->youtube_videos) )
		<div class="pic-video-container text-center" id="pic-video-toggle">
			<span id="college-pic" class="college-pic-btn active-tab">PICS </span>
			|<span id="college-yt-vid" class="college-ytvid-btn"> VIDEO</span>
			@if( isset($college_data->virtual_tours) && !empty($college_data->virtual_tours) )
			| <span id="virtual-tour-tab" class="college-pic-btn"> TOUR</span>
			@endif
		</div>
		@elseif( (isset($college_data->youtube_videos) && count($college_data->youtube_videos) < 1) && (isset($college_data->virtual_tours) && !empty($college_data->virtual_tours) ) )
		<div class="pic-video-container text-center" id="pic-video-toggle">
			<span id="college-pic" class="college-pic-btn active-tab">PICS </span>
			| <span id="virtual-tour-tab" class="college-pic-btn"> TOUR</span>
		</div>
		@endif
		<div id="college-carousel" class="column small-12">
			<div id="leftCarouselNextArrow" class="leftArrow arrow prev"></div>
			<div id="rightCarouselNextArrow" class="rightArrow arrow next"></div>
			<div id="owl-example" class="owl-carousel">
				@foreach ($college_data->college_media as $images)
					@if($is_mobile == 1)
						<div class="item"><img class="media_images_resize" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/{{ $images['url'] }}" alt="{{ $images['title'] or "College Image" }}"></div>
					@else
						<div class="item"><img class="media_images_resize" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/{{ $images['url'] }}" alt="{{ $images['title'] or "College Image" }}"></div>
					@endif
				@endforeach
			</div>
		</div>
	@else
		@if( isset($college_data->virtual_tours) && !empty($college_data->virtual_tours) )
		<div class="pic-video-container text-center" id="pic-video-toggle">
			<span id="college-pic" class="college-pic-btn active-tab">PICS </span>
			| <span id="virtual-tour-tab" class="college-pic-btn"> TOUR</span>
		</div>
		@elseif( isset($college_data->youtube_videos) && !empty($college_data->youtube_videos) )
		<div class="pic-video-container text-center" id="pic-video-toggle">
			<span id="college-pic" class="college-pic-btn active-tab">PICS </span>
			| <span id="college-yt-vid" class="college-ytvid-btn"> VIDEO</span>
		</div>
		@endif
		<div id="college-carousel" class="column small-12 default-overview-img" data-imagecount="{{ count($college_data->college_media) }}" data-tourcount="{{ count($college_data->virtual_tours) }}">
			<img src="/images/colleges/default-college-page-photo_overview.jpg" alt="Default College image">
		</div>
	@endif
</div>


<!-- start of youtube carousel -->
@if( isset($college_data->youtube_videos) && !empty($college_data->youtube_videos) )
<div class="row">
	<div class="column small-12" id="youtube-vid-carousel">
		@if( count($college_data->youtube_videos) > 1 )
			<div class="leftArrow arrow  prev-btn"></div>
			<div class="rightArrow arrow  next-btn"></div>
		@endif
		<div id="owl-youtube" class="owl-carousel owl-theme">
			@foreach( $college_data->youtube_videos as $video )
				<!-- if a youtube video -->
				@if( isset($video['is_youtube']) && $video['is_youtube'] == 1 )
					<div class="item">
						<iframe type="text/html" width="100%" height="533" src="https://www.youtube.com/embed/{{ $video['video_id'] }}" style="border:none;" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>	
					</div>
				@elseif( isset($video['is_youtube']) && $video['is_youtube'] == 2 )
					<div class="item is-youniversity" data-id="{{$video['id']}}">
						<iframe src="" frameborder="0" style="width: 100%; height: 553px;"></iframe>
						<div class="loader">
							<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/loading.gif" alt="loading gif">
						</div>
					</div>
				@else
				<!-- else a vimeo video -->
					<div class="item">
						<iframe src="https://player.vimeo.com/video/{{$video['video_id']}}" width="100%" height="533" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
					</div>
				@endif
			@endforeach
		</div>
	</div>
</div>
@endif

<!-- YouVisit virtual tour -->
@if( isset($college_data->virtual_tours) && !empty($college_data->virtual_tours) )
@foreach( $college_data->virtual_tours as $tours )
<div class="row">
	<div id="virtual-tour" class="column small-12 tour-section small-text-center" data-universityid="{{ $tours['tour_id'] }}" >
		<!-- virtual tour gets injected here from youtubeCarousel.js on 'tour' click -->
		<!-- Don't alter anything other than the values for data-inst, data-image-width, and data-image-height -->
		<!--<a href="http://www.youvisit.com" class="virtualtour_embed" title="Virtual Tour" data-inst="{{ $tours['tour_id'] }}" data-link-type="image" data-image-width="98%" data-image-height="337" data-platform="plexuss">Virtual Tour</a>
		<script async="async" src='https://www.youvisit.com/tour/Embed/js2'></script>-->
	</div>
</div>
@endforeach
@endif
<!-- survival guide -->
<div class="row">
	@if (isset($survivalGuide) && !empty($survivalGuide) )
		<div class="large-12 columns no-padding bg-ext-black radial-bdr">
			<div class="large-4 columns">
				<div class="survival-guide-img">
					@if ($survivalGuide['img_sm'] != '')
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$survivalGuide['img_sm']}}" alt="College image">
					@else
						<img src="/images/colleges/default-college-page-photo_overview.jpg" alt="Default College image">
					@endif
				</div>
			</div>
			<div class="large-8 columns">
				<div class="quad">
					FROM THE QUAD
				</div>
				<div class="suvival-guide-title">
					{{$college_data->school_name}} - Survival Guide
				</div>
				<div class="survival-guide-desc">
					{{$survivalGuide['meta_description']}}
				</div>
				<div class="see-full-article">
					<a href="/news/article/{{$survivalGuide['slug']}}" target="_blank" class="slug-article">See full article</a>
				</div>
			</div>
		</div>
	@endif
</div>
<div class="clearfix-padding"></div>
@if( isset($collegeData->overview_content) && $collegeData->overview_content != '' )
<!-- Hiding this for now! Back end will need to fix! --> 


<div class="row">
	<div class="column small-12">
		<div class="row overview-text-area">
			<div class="column">
				<h2>About</h2>
				<h3>{{ $collegeData->school_name or '' }}</h3>
				{!! $collegeData->overview_content !!}
				@if(isset($collegeData->overview_source) && $collegeData->overview_source !='')
				<div class="overviewSource">Source {{$collegeData->overview_source or ''}}</div>
				@endif
				
			</div>
		</div>
	</div>
</div>

@else
<!-- Hiding this for now! Back end will need to fix! -->
<!-- 
<div class="row">
	<div class="column small-12">
		<div class="row overview-text-area">
			<div class="column">
				<h2>About<span style="display:block;">University of California Los Angeles</span></h2>
				<p>UCLA is a public, comprehensive university. Founded as a Normal school in 1919, it later became the first branch of the University of California system. 
					Its 419-acre campus is located in Westwood Village, within the corporate limits of Los Angeles.We doubt the critics, reject the status quo and see opportunity in dissatisfaction. 
					Our campus, faculty and students are driven by optimism. It is not naïve; it is essential. And it has fueled every accomplishment, allowing us to redefine what’s possible, time after time.
					This can-do perspective has brought us 12 Nobel Prizes, 12 Rhodes Scholarships, more NCAA titles than any university and more Olympic medals than most nations. 
					Our faculty and alumni helped create the Internet and pioneered reverse osmosis. And more than 100 companies have been created based on technology developed at UCLA.
				</p>

				<h2>History of<span style="display:block;">University of California Los Angeles</span></h2>
				<p>The University of California, Los Angeles was founded in 1919 as a public, coeducational research university. 
					It is the only leading research institution in the world founded in the 20th century. UCLA sits on 419 acres at the base of the Santa Monica mountains, five miles from the Pacific Ocean. The Los Angeles County Museum of Art, the Music Center, Chinatown, Olvera Street, Little Tokyo and the Downtown business district are a few miles to the east. Mountains, beaches lakes and deserts are all within a short drive. One of the world’s most ethnically and culturally diverse communities, students come to UCLA from all 50 states and more than 100 foreign countries, though the majority of undergraduates are from California. UCLA is a public, comprehensive university. Founded as a Normal school in 1919, it later became the first branch of the University of California system. Its 419-acre campus is located in Westwood Village, within the corporate limits of Los Angeles. Highly ranked by the Association of Research Libraries, UCLA’s library system contains more than 11 million print and electronic volumes, and more than 800,000 electronic resources held in two general collections, ten special subject libraries and twelve other campus collections. The Fowler Museum of cultural history and the Hammer Museum of Art are also available resources to students. UCLA offers undergraduates a choice of more than 125 undergraduate degree programs, many of which rank among the top in the nation. Our undergraduates have the opportunity to be directly involved in research, and can get hands-on experience by pursuing internships. Opportunities to study, travel and work abroad are also available.
				</p>
			</div>
		</div>
	</div>
</div>
-->
@endif

<div class="clearfix-padding"></div>

<div class="row" id="overview-bottom-3-boxes">
	<div class="column small-12 large-4 general-info-box">
		<div class="row">
			<div class="column small-12 text-left box-3-top-header">GENERAL INFORMATION</div>
		</div>
		<div class="row box-3-infobox">
			<div class="column small-12">Type:</div>
			<div class="column small-12 value">{{ $collegeData->school_sector }}</div>
			<div class="column small-12">Campus setting:</div>
			<div class="column small-12 value">{{$collegeData->institution_size}}</div>
			<div class="column small-12">Campus housing:</div>
			<div class="column small-12 value">{{$collegeData->campus_housing}}</div>
			<div class="column small-12">Religious Affiliation:</div>
			<div class="column small-12 value">{{$collegeData->religious_affiliation}}</div>
			<div class="column small-12">Academic Calendar:</div>
			<div class="column small-12 value">{{$collegeData->calendar_system}}</div>
		</div>
	</div>
	<div class="column small-12 large-4 general-links-box">
		<div class="row">
			<div class="column small-12 text-left box-3-top-header">GENERAL LINKS</div>
		</div>
		<div class="column small-12 box-3-infobox">
			<div class="row">
				@if(isset($collegeData->school_url) && strlen($collegeData->school_url) > 1 )
				<div class="column small-12">
					<a class="col-overview-link-hover" href="{{$collegeData->school_url}}" target="_blank"> > Website</a>
				</div>
				@endif
				<!--<div class="column small-12 value">
					<a target="_blank" href="{{$collegeData->school_url}}">
						{{$collegeData->school_url}}</a>
					</div>-->

				@if(isset($collegeData->admission_url) && strlen($collegeData->admission_url) > 1)
				<div class="column small-12">
					<a class="col-overview-link-hover" href="{{$collegeData->admission_url}}" target="_blank"> > Admissions</a>
				</div>
				@endif
				<!--<div class="column small-12 value">
					<a target="_blank" href="{{$collegeData->admission_url}}">
						{{$collegeData->admission_url}}
					</a>
				</div>-->
				@if(isset($collegeData->application_url) && strlen($collegeData->application_url) > 1)
				<div class="column small-12">
					<a id="collegeCommonApply" 
						data-source="college_common_apply"
                        data-url="{{$collegeData->application_url or ''}}"
                        data-slug="{{$collegeData->slug or ''}}"
						class="col-overview-link-hover" 
						href="{{$collegeData->application_url}}" 
						target="_blank"> > Apply Online</a>
				</div>
				@endif
				<!--<div class="column small-12">
					<a target="_blank" href="{{$collegeData->application_url}}">
						{{$collegeData->application_url}}
					</a>
				</div>-->
				@if(isset($collegeData->financial_aid_url) && strlen($collegeData->financial_aid_url) > 1)
				<div class="column small-12">
					<a class="col-overview-link-hover" href="{{$collegeData->financial_aid_url}}" target="_blank"> > Financial Aid</a>
				</div>
				@endif
				<!--<div class="column small-12 value">
					<a target="_blank" href="{{$collegeData->financial_aid_url}}">
						{{$collegeData->financial_aid_url}}
					</a>
				</div>-->
				@if(isset($collegeData->calculator_url) && strlen($collegeData->calculator_url) > 1)
				<div class="column small-12">
					<a class="col-overview-link-hover" href="{{$collegeData->calculator_url}}" target="_blank"> > Net Price Calculator</a>
				</div>
				@endif
				<!--<div class="column small-12 value">
					<a target="_blank" href="{{$collegeData->calculator_url}}">
						{{$collegeData->calculator_url}}
					</a>
				</div>-->
				@if(isset($collegeData->mission_url) && strlen($collegeData->mission_url) > 1)
				<div class="column small-12">
					<a class="col-overview-link-hover" href="{{$collegeData->mission_url}}" target="_blank"> > Mission Statement</a>
				</div>
				@endif
				<!--<div class="column small-12 value">
					<a target="_blank" href="{{$collegeData->mission_url}}">
						{{$collegeData->mission_url}}
					</a>
				</div>-->
			</div>
		</div>
	</div>
	<div class="column small-12 large-4 find-out-more">
		<div class="row">
			<div class="column small-12 text-left box-3-top-header">FIND OUT MORE</div>
		</div>
		<div class="column small-12 box-3-infobox">
			<div class="row">
				<div class="column small-12"><a class="col-overview-link-hover" href="/college/{{ $collegeData->slug }}/stats"> > Stats</a></div>
				<div class="column small-12"><a class="col-overview-link-hover" href="/college/{{ $collegeData->slug }}/ranking"> > Ranking</a></div>
				<div class="column small-12"><a class="col-overview-link-hover" href="/college/{{ $collegeData->slug }}/admissions"> > Admissions</a></div>
				<div class="column small-12"><a class="col-overview-link-hover" href="/college/{{ $collegeData->slug }}/financial-aid"> > Financial Aid</a></div>
				<div class="column small-12"><a class="col-overview-link-hover" href="/college/{{ $collegeData->slug }}/enrollment"> > Enrollment</a></div>
				<div class="column small-12"><a class="col-overview-link-hover" href="/college/{{ $collegeData->slug }}/tuition"> > Tuition</a></div>
			</div>
		</div>
	</div>
</div>
