@extends('private.home.master')
@section('content')
<div class="content-wrapper">

	<div class="row left-section collapse fullWidth pl20 pr20">
		<!--<div id="tmp-toastr">
			<div class="icon">&#9888;</div>
			<div>Have thoughts on how to improve Plexuss? <a href="/portal/messages/7916/college">Let us know!</a></div>
			<div class="close">&#10006;</div>
		</div>-->


		<div class="column medium-12 home-content-wrapper" >
			<!-- INDICATORS HEADING -->	

			<div class="row">
				<div class='column small-12'>
					<div class="black-feed-block">
						<div class="feed-block-inner text-left">MY INDICATORS</div>
					</div>
				</div>
			</div>
			<div class="row indicator_box collapse">
				<div class='column small-12'>
					<div class='row' data-equalizer>
						<div class='column small-12 medium-4 large-4'>
							<div class='row bluebox infoboxes'>
								<div class='column small-10 text-center small-centered mainmessage' data-equalizer-watch>
									# OF COLLEGES THAT WANT TO RECRUIT YOU:
								</div>
								<div class='row'>
									<div class="column small-6 small-text-center ">
										<img src="/images/large-home-recruit-you-icon.png" alt="recruit icon" class='recruiticon' />
									</div>
									<div class="column small-6 count small-text-center">
										<a href="/portal/collegesrecruityou" >{{$num_of_recruit or 0}}</a>
									</div>
									@if (isset($num_of_recruit ))
										@if ($num_of_recruit  == 0)
											<div class="column small-12 small-text-center whyzero">
												<span data-tooltip aria-haspopup="true" class="has-tip whyzero tip-top radius" title="In order for the recruitment process to begin, you need to create a profile. Watch your profile status meter to see when your profile is complete enough.">Why is this zero?</span>
											</div>
										@else
											<div class="column small-12 small-text-center whyzero">
												<span>&nbsp;</span>
											</div>

										@endif

									@endif
								</div>
							</div>
						</div>




						<div class='column small-12 medium-4 large-4 ko'>
							<div class='row redbox infoboxes'>
								<div class='column small-10 text-center small-centered mainmessage' data-equalizer-watch>
									# OF COLLEGES THAT VIEWED YOUR PROFILE:
								</div>
								<div class='row'>
									<div class="column small-6 small-text-center ">
										<img src="/images/large-home-viewing-icon.png" alt="recruit icon" class='recruiticon' />
									</div>
									<div class="column small-6 count small-text-center">
										<a href="/portal/collegesviewedprofile" >{{$num_of_colleges_viewed_you or 0}}</a>
									</div>
									@if (isset($num_of_colleges_viewed_you ))
										@if ($num_of_colleges_viewed_you  == 0)
											<div class="column small-12 small-text-center whyzero">
												<span data-tooltip aria-haspopup="true" class="has-tip whyzero tip-top radius" title="In order for colleges to view your profile, you need to complete at least 30% of your profile. Watch your profile status meter to see when your profile is complete enough.">Why is this zero?</span>
											</div>
										@else
											<div class="column small-12 small-text-center whyzero">
												<span>&nbsp;</span>
											</div>

										@endif

									@endif
								</div>
							</div>
						</div>

						<div class='column small-12 medium-4 large-4 '>
							<div class='row greenbox infoboxes'>
								<div class='column small-10 text-center small-centered mainmessage' data-equalizer-watch>
									<a href="/portal/recommendationlist">
										# OF COLLEGES PLEXUSS RECOMMENDS FOR YOU:
									</a>
								</div>
								<div class='row'>
									<div class="column small-6 small-text-center ">
										<a href="/portal/recommendationlist">
											<img src="/images/large-home-recruit-icon.png" alt="recruit icon" class='recruiticon' />
										</a>
									</div>
									<div class="column small-6 count small-text-center">
										<a href="/portal/recommendationlist" >{{$recommendCount or 0}}</a>
									</div>

									@if (isset($recommendCount ))
										@if ($recommendCount  == 0)
											<div class="column small-12 small-text-center whyzero">
												<span data-tooltip aria-haspopup="true" class="has-tip whyzero tip-top radius" title="In order for Plexuss to make recommendations, please fill out your profile. Adding several schools you are interested in will also give you relevant recommendations.">Why is this zero?</span>
											</div>
										@else
											<div class="column small-12 small-text-center whyzero">
												<span>&nbsp;</span>
											</div>

										@endif

									@endif

								</div>
							</div>
						</div>







					</div>
				</div>
			</div>

			<!-- WEBINAR BANNER -->
			<!--
			<div class='row' id="webinar-reg">
				<div class='column small-12'>
					<div class="black-feed-block" style="padding: 0">
						<a href="/webinar"><img src="images/webinar-banner-3.png" alt="Webinar Banner" /></a>
					</div>
				</div>
			</div>
			-->

			<!-- FEED HEADING -->
			<div class='row'>
				<div class='column small-12'>
					<div class="black-feed-block">
						<div class="feed-block-inner text-left">MY FEED</div>
					</div>
				</div>
			</div>
				
			<!-- MASONRY AFFECTED PINS -->
			<div id="container-box" class="row">
				<!--//////////////////// ADD HELP PINS MASONRY HERE \\\\\\\\\\\\\\\\\\\\-->
				@include('public.includes.gettingStartedPins')
				<!--\\\\\\\\\\\\\\\\\\\\ END HELP PINS MASONRY HERE ////////////////////-->
				@if($newsdata!='')
				<!--//////////////////// NEWS PINS MASONRY HERE \\\\\\\\\\\\\\\\\\\\-->
				@foreach($newsdata as $news)
			
				<div class='column small-12 medium-6 large-4'>
					<div class="news-article-box row collapse">
					<!-- 	<span class="gs-close gs-close-x gs-close-x-dark mr10">
								&#10006;
						</span> -->
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
				<!--\\\\\\\\\\\\\\\\\\\\  END NEWS PIN MASONRY ////////////////////-->
				@endif
			</div>
			<!-- END MASONRY PINS -->
	        <div id="loadmoreajaxloader" style="display:none;"><center><img src="/images/colleges/loading.gif" alt=""/></center></div>
		</div>

		<!--//////////////////// RIGHT SIDE ITEMS HERE \\\\\\\\\\\\\\\\\\\\-->
		<?php
			//other comment forms still partially parse	
		  	//@include('private.home.homeRightSide')
		?>
		<!--\\\\\\\\\\\\\\\\\\\\ RIGHT SIDE ITEMS END  ////////////////////-->
	
	</div>
<!-- What's next on Plexuss Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
      	<a type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </a>
        <h5 class="modal-title">
        	<div class="next-steps">
        		<img src="/images/next-steps.png" />        		
        	</div>
        	<p>
          	What would you like to do next on Plexuss?        		
        	</p>
        </h5>
      </div>
      <div class="modal-body" id="modal-content">
          <div class="row">
					  <div class="columns medium-6 small-12">
					  	<a href="/college"><button class="next-buttons">Search For Colleges</button></a>
					  </div>
					  <div class="columns medium-6 small-12">
				  		<a href="/college-majors"><button class="next-buttons">Find Majors</button></a>
					  </div>
					  <div class="columns medium-6 small-12">
				  		<a href="/scholarships"><button class="next-buttons">Find Scholarships</button></a>
					  </div>
					  <div class="columns medium-6 small-12">
				  		<a href="/ranking"><button class="next-buttons">View college Rankings</button></a>
					  </div>
					  <div class="columns medium-6 small-12">
				  		<a href="/international-students"><button class="next-buttons">Apply to Colleges</button></a>
					  </div>
					  <div class="columns medium-6 small-12">
				  		<a href="/news"><button class="next-buttons">Read Articles</button></a>
					  </div>
					  <div class="columns medium-6 small-12">
				  		<a href="/news/catalog/college-essays"><button class="next-buttons">Read Admission Essays</button></a>
					  </div>
					  <div class="columns medium-6 small-12">
				  		<a href="/checkout/premium"><button class="next-buttons-og">Upgrade to Premium</button></a>
					  </div>
					</div>
      </div>
      <div class="modal-footer">
      	<a href="#" class="dont-show">Don't show this again.</a>
      </div>
    </div>
  </div>
</div>
<script type="text/javascript">
    // Get the modal
    $(document).ready(function(e){
      var modal = document.getElementById('myModal');
        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];
        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
            amplitude.getInstance().logEvent("close_recommended_action_nonprofile", {content: "recommended action", Location1: '/home'});
        }
        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
      	}
        if({{$show_modal}}){
	      modal.style.display = "block";
	      amplitude.getInstance().logEvent("show_recommended_action_nonprofile", {content: "recommended action", Location1: '/home'});
	    }	
      
      
    });

    $('.dont-show').on('click', function(){
        var modal = document.getElementById('myModal');
        modal.style.display = "none";
        amplitude.getInstance().logEvent("dontshow_recommended_action_nonprofile", {content: "recommended action", Location1: '/home'});
        
        $.ajax({
            type: "POST",
            url: '/home/dont_show_modal',
            data:{
            	user_id:"{{$user_id}}"
            },
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(data){
                if(data == 'success'){
                    console.log('Won\'t show this pop up again ');
                }
            }
        });

        $.ajax({
            type: "POST",
            url: '/tracking/modals/home_page/dont_show_modal',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(data){
            }
        })      
    });

    $('.next-buttons, .next-buttons-og').on('click', function(e){
    	
    	amplitude.getInstance().logEvent("click_recommended_action_nonprofile", {content: "recommended action", section: $(this).text(), Location1: '/home'});

    	$.ajax({
            type: "POST",
            url: '/tracking/modals/home_page/'+$(this).text(),
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            success: function(data){
            }
        })
    });

</script>
		<!-- SIC -->
	<!-- <div class="sic-wrapper"> -->
	@include('includes.smartInteractiveColumn')
	<!-- </div> -->
</div><!-- end content-wrapper ->
@stop