<!--///////////////// start of the quad carousel \\\\\\\\\\\\\\\\\\-->
<div class="row college-carousel-header-row">
	<div class="column small-7 medium-4 large-2 college-carousel-label text-center">
		<div>The Quad</div>
	</div>
	<div class="column small-5 medium-8 large-10 college-carousel-label-seeAll">
		<a href="/news">See all</a>
	</div>
</div>

<div class="row collapse">
	<div class="column small-12 frontpage-carousel-container quad-article-carousel-container-unique">
		
		<div class="prev-col-pin"><span class="leftarrw">&#9668;</span></div>
		
		<!--remove empty id-->
		<div class="owl-carousel owl-theme all-frontpage-carousels quad-article-carousel-container-unique-ajax the-quad-carousel">
			
			<!-- creating college pins - used tympanus hover effect -->
			@for( $i = 0; $i < count($articles); $i++ )

				@if( isset($articles[$i]['video']) )
					<div class="item effect-sadie text-center news-items" data-interchange="[{{ $articles[$i]['img_url'] }}, (default)]">
				@else
					<div class="item effect-sadie text-center news-items" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$articles[$i]['img_url']}}, (default)]">
				@endif
						<div class="filler"></div>
						<div class="college-pin-news-desc text-left">{{$articles[$i]['title']}}</div>
						<figure>
							<figcaption>
								
								<div class="row college-pin-footer-container college-news-pin-container">
									<div class="column small-4 news-pin-inner-container">
										<div class="text-center news-pin-hover-icon-img">
											@if( isset($articles[$i]['is_essay']) && $articles[$i]['is_essay'] == 0 )	
												<a href="/news/article/{{$articles[$i]['slug']}}" class="college-pin-link">
													<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/news-icon-for-hover.png" alt="">
												</a>
											@else
												<a href="/news/essay/{{$articles[$i]['slug']}}/1" class="college-pin-link">
													<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/news-icon-for-hover.png" alt="">
												</a>
											@endif
										</div>
										<div class="text-center news-pin-hover-desc">
											@if( isset($articles[$i]['video']) )
												<a href="/news/article/{{$articles[$i]['slug']}}" class="college-pin-link">WATCH VIDEO</a>
											@else
												@if( isset($articles[$i]['is_essay']) && $articles[$i]['is_essay'] == 0 )	
													<a href="/news/article/{{$articles[$i]['slug']}}" class="college-pin-link">SEE FULL ARTICLE</a>
												@else
													<a href="/news/essay/{{$articles[$i]['slug']}}/1" class="college-pin-link">SEE FULL ARTICLE</a>
												@endif
											@endif
										</div>
									</div>
								</div>
							</figcaption>
						</figure>
				</div>

		  	@endfor

		</div>

		<div class="next-col-pin"><span class="rightarrw">&#9658;</span></div>

	</div>
</div>

<script>
	var owl = $(".quad-article-carousel-container-unique-ajax"),
    	collegeNearYouData = $('#colleges-near-you-carousel').data('colleges-near-you'),
    	sidebar_section_to_show = '',
    	all_sidebar_sections = $('.frontpage-side-bar-sections'),
    	aws_url = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/',
    	all_carousel_headers = $('.college-carousel-label').toArray(),
    	owlItemsAmount  = 0,
    	isPrevious  = false;

	owl.owlCarousel({
        items : 6, //10 items above 1000px browser width
        itemsDesktop : [1300,4], //5 items between 1200px and 801px
        itemsDesktopSmall : [800,3], // betweem 900px and 601px
        itemsTablet: [600,3], //2 items between 600 and 0
        itemsMobile : [600, 1], // itemsMobile disabled - inherit from itemsTablet option
        pagination: false,
        loop: false,
        beforeMove: function(elem){
         	var visibleItemsArr = this.visibleItems,
         		owlItemsAmount = this.itemsAmount,
         		_carousel_name;

             if(visibleItemsArr.indexOf(owlItemsAmount - 1) != -1){
                _carousel_name = elem.parent().attr('class').substr(elem.parent().attr('class').lastIndexOf(' ') + 1);
                ajaxCarouselItems(_carousel_name, owlItemsAmount, isPrevious);
             }
        }
    });

    $(document).foundation('interchange', 'reflow');
</script>
<!--///////////////// end of the quad carousel \\\\\\\\\\\\\\\\\\-->