<!--///////////////// start of top ranking colleges carousel \\\\\\\\\\\\\\\\\\-->
<div class="row college-carousel-header-row">
	<div class="column small-7 medium-4 large-2 college-carousel-label text-center">
		<div>Top Ranking Colleges</div>
	</div>
	<div class="column small-5 medium-8 large-10 college-carousel-label-seeAll">
		<a href="/ranking">See all</a>
	</div>
</div>

<div class="row collapse">
	<div class="column small-12 frontpage-carousel-container top-ranking-carousel-container-unique">
		
		<div class="prev-col-pin short">&#9668;</div>
		
		<!--remove empty id-->
		<div class="owl-carousel owl-theme all-frontpage-carousels top-ranking-carousel-container-unique-ajax" data-colleges-near-you="">
			
			<!-- creating college pins - used tympanus hover effect -->
			@for( $i = 0; $i < count($ranked_colleges); $i++ )

				<div class="item effect-sadie text-center">
					<div class="background-college-logo" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$ranked_colleges[$i]['logo_url']}}, (default)], [https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$ranked_colleges[$i]['logo_url']}}, (large)]"></div>
					<div class="college-pin-school-name">{{$ranked_colleges[$i]['school_name']}}</div>
					<figure>
						<img class="pin-page-turn-img" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/page-corner-curl_40x40.png" alt="">
						<figcaption>
							<div class="pin-back pin-back-img" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/{{$ranked_colleges[$i]['img_url']}}, (default)], [https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/{{$ranked_colleges[$i]['img_url']}}, (large)]">
								<a href="/college/{{$ranked_colleges[$i]['slug']}}" class="college-pin-link">
									<div class="row college-pin-footer-container">
										<div class="column small-4 text-left">
											<div class="top-rank-pin-rank-icon text-center"><strong>#{{$ranked_colleges[$i]['rank']}}</strong></div>
										</div>
										
										<div class="column small-8 pin-item-footer">
											<div>{{$ranked_colleges[$i]['distance']}} miles away</div>
											<div>SEE COLLEGE</div>
										</div>
									</div>
								</a>
							</div>
						</figcaption>
					</figure>
				</div>

		  	@endfor

		</div>

		<div class="next-col-pin short">&#9658;</div>

	</div>
</div>

<script>
	var owl = $(".top-ranking-carousel-container-unique-ajax"),
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
<!--///////////////// end of top ranking colleges carousel \\\\\\\\\\\\\\\\\\-->