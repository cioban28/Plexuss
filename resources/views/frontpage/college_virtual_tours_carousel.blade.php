<!--///////////////// start of college virtual tours carousel \\\\\\\\\\\\\\\\\\-->
<div class="row college-carousel-header-row">
	<div class="column small-7 medium-4 large-2 college-carousel-label text-center">
		<div>College Virtual Tours</div>
	</div>
</div>

<div class="row collapse">
	<div class="column small-12 frontpage-carousel-container virtual-tours-carousel-container-unique">
		
		<div class="prev-col-pin short">&#9668;</div>
		
		<!--remove empty id-->
		<div class="owl-carousel owl-theme all-frontpage-carousels virtual-tours-carousel-container-unique-ajax" data-colleges-near-you="">
			
			<!-- creating college pins - used tympanus hover effect -->
			@for( $i = 0; $i < count($virtual_tour_colleges); $i++ )

				<div class="item effect-sadie text-center pin-back-img" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/{{$virtual_tour_colleges[$i]['img_url']}}, (default)], [https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/{{$virtual_tour_colleges[$i]['img_url']}}, (large)]">
					<div class="college-pin-virtualtour-school-name">
						<div class="vt-school-name text-left">{{$virtual_tour_colleges[$i]['school_name']}}</div>
					</div>
					
					<figure>
						<figcaption>
							<div class="row college-pin-footer-container college-news-pin-container">
								<div class="column small-4 news-pin-inner-container">
									<div class="text-center news-pin-hover-icon-img">
										<a href="/college/{{$virtual_tour_colleges[$i]['slug']}}" onclick="openVirtualTourOnRedirect(this); return false;">
											<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/tour-icon-for-hover.png" alt="">
										</a>
									</div>
									<div class="text-center news-pin-hover-desc">
										<a href="/college/{{$virtual_tour_colleges[$i]['slug']}}" class="college-pin-link" onclick="openVirtualTourOnRedirect(this); return false;">VIEW TOUR</a>
									</div>
								</div>
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
	var owl = $(".virtual-tours-carousel-container-unique-ajax"),
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
<!--///////////////// end of college virtual tours carousel \\\\\\\\\\\\\\\\\\-->