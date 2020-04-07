<!-- ////////////////// message a college section start \\\\\\\\\\\\\\\\\-->
<div class="row college-carousel-header-row">
	<div class="column small-7 medium-4 large-2 college-carousel-label text-center">
		<div>Message A College</div>
	</div>
</div>

<div class="row collapse">
	<div class="column small-12 frontpage-carousel-container message-a-college-container-unique">

		<!-- previous btn -->
		<div class="prev-col-pin">&#9668;</div>

		<!--remove empty id-->
		<div class="owl-carousel owl-theme all-frontpage-carousels message-a-college-container-unique-ajax" data-colleges-near-you="">
			
			<!-- creating college pins - used tympanus hover effect -->
			@if(isset($msg_a_college))
				@foreach( $msg_a_college as $rep )
					<div class="item effect-sadie text-center" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/{{$rep->school_bk_img or 'no-image-default.png'}}, (default)], [https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/{{$rep->school_bk_img or 'no-image-default.png'}}, (large)]">
						<div class="rep">
							<div class="name text-center"><b>{{$rep->fname or ''}} {{$rep->lname or ''}}</b></div>	
							<div class="title text-center">{{$rep->title or 'College Representative'}}</div>
							<div class="yr text-center @if(!isset($rep->member_since)) hidden @endif">{{ isset($rep->member_since) ? 'Since '.explode('-',$rep->member_since)[0] : 'N/A' }}</div>
							@if( isset($rep->profile_img_loc) && !empty($rep->profile_img_loc) )
								<div class="pic text-center" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/{{$rep->profile_img_loc or ''}}, (default)], [https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/{{$rep->profile_img_loc or ''}}, (large)]"></div>
							@else
								<div class="pic text-center has-default" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/default_avatar.png, (default)]"></div>
							@endif
							
							@if(isset($rep->school_name) && strlen($rep->school_name) > 45 )
								<div class="school text-center"><b>{{ substr(strip_tags($rep->school_name),0,45).'...' }}</b></div>
							@else
								<div class="school text-center"><b>{{$rep->school_name or ''}}</b></div>
							@endif
						</div>	
						
						<figure>
							<figcaption>
								<div class="pin-back pin-back-img msg-col">
									<div class="background-college-logo" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$rep->logo_url or ''}}, (default)], [https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$rep->logo_url or ''}}, (large)]"></div>
									<div class="name text-center"><b>{{$rep->fname or ''}} {{$rep->lname or ''}}</b></div>
									<div class="title text-center">{{$rep->title or 'College Representative'}}</div>
									<div class="yr text-center @if(!isset($rep->member_since)) hidden @endif">{{ isset($rep->member_since) ? 'Since '.explode('-',$rep->member_since)[0] : 'N/A' }}</div>
									<div class="descr">{{$rep->description or ''}}</div>
								</div>
								<a href="/portal/messages/{{$rep->college_id or ''}}/college" class="college-pin-link">
									<div class="send-msg-btn">
											SEND MESSAGE
									</div>
								</a>
							</figcaption>
						</figure>
					</div>
			  	@endforeach
		  	@endif

		</div>

		<!-- next btn -->
		<div class="next-col-pin">&#9658;</div>

	</div>
</div>

<script>
	var message_owl = $(".message-a-college-container-unique-ajax"),
    	collegeNearYouData = $('#colleges-near-you-carousel').data('colleges-near-you'),
    	sidebar_section_to_show = '',
    	all_sidebar_sections = $('.frontpage-side-bar-sections'),
    	aws_url = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/',
    	all_carousel_headers = $('.college-carousel-label').toArray(),
    	owlItemsAmount  = 0,
    	isPrevious  = false;

	message_owl.owlCarousel({
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
<!-- ////////////////// message a college section end \\\\\\\\\\\\\\\\\-->