@if(!empty($right_handside_carousel['data'][0]))
<div class='row collapse engagement-modulez-rightside-content'>
	<div class='small-12 column'>
		<div id="engagement-modulez-slider" class="owl-carousel owl-theme">
				
					
						<!-- $engagment_type == 'near you' -->
						@if( $right_handside_carousel['type'] == 'near you' )
							@foreach ($right_handside_carousel['data'] as $dt)

								<div class="item">
									<div class="row collapse modulez-near-you-row">
										<div class="column small-12">
											
											<div class="equal-heights-main">
											<div class="row equal-heights-top">
												<div class="column small-12 modulez-near-you-title text-center">
													universities in your area
												</div>
											</div>

											<div class="row edit-location-row equal-heights-upper-middle">
												<div class="column small-12 modulez-near-you-location text-center change-location-section">
													<div class="edit-near-you-location-btn ">
														{{$dt['userCityName'] or ''}}, {{$dt['userStateName'] or ''}}
														<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/pencil_edit.png" alt="edit city">
													</div>
												</div>

												<div class="column small-12 change-location-section location-input">
													{{Form::open(array('url'=>''))}}
													<div id="edit-colleges-near-you" class="row collapse edit-location-near-you-form">
														<div class="column small-9 large-10">
															{{Form::text('update_location', '', array('placeholder'=>'Change location...', 'id'=>'selectivity_form', 'class'=>'select_new_location'))}}
															<!-- hidden form that contains selected city value -->
															{{ Form::hidden('city_tag_list', '', array( 'id' => 'city_location_data')) }}
														</div>
														<div class="column small-3 large-2">
															<a href="" class="button postfix close-edit-location-form">X</a>
														</div>
													</div>
													{{Form::close()}}
												</div>
											</div>

											<div class="row collapse modulex-near-you-background-img-logo-name-container equal-heights-lower-middle">
												<div class="column small-12 modulez-near-you-school-img-col" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/{{$dt['img_url'] or ''}}, (default)], [https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/{{$dt['img_url'] or ''}}, (medium)]">
													<div class="near-you-college-logo" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$dt['logo_url'] or ''}}, (default)]"></div>
													<div class="near-you-college-rank text-center">#{{$dt['rank'] or ''}}</div>
													<div class="modulez-near-you-school-name text-center">{{$dt['school_name'] or ''}}</div>
												</div>
											</div>
											</div>

											<div class="row modulez-near-you-engagement-btns-row equal-heights-bottom">
												<div class="column small-4">
													<div class="modulez-near-you-engagement-btns text-center engagement-modulez-like-btn" onClick='Plex.setLikeTally("{{$dt['like_type'] or ''}}", "{{$dt['like_type_col'] or ''}}", "{{$dt['like_type_val'] or ''}}");'>Like</div>
												</div>
												<div class="column small-4">
													<a href="/college/{{$dt['slug'] or ''}}">
														<div class="modulez-near-you-engagement-btns text-center">
															View
														</div>
													</a>
												</div>
												<div class="column small-4">
													<div class="modulez-near-you-engagement-btns text-center engagement-modulez-skip-btn">Skip</div>
												</div>
											</div>

										</div>
									</div>
								</div>
							@endforeach

						<!-- $engagment_type == 'news' -->
						@elseif( $right_handside_carousel['type'] == 'news' )


						@foreach ($right_handside_carousel['data'] as $dt)
						<div class="item">
							<div class="row collapse modulez-news-row">
								<div class="column small-12">
									
									<div class="equal-heights-main">
									<div class="row equal-heights-top">
										<div class="column small-12 modulez-news-title text-center">
											latest news
										</div>
									</div>

									<div class="row equal-heights-upper-middle">
										<div class="column small-12 modulez-news-article-header">
											<div class="row collapse">
												<div class="column small-12 text-left modulez-news-articlename">
													{{$dt['title'] or ''}}
												</div>
											</div>
											<div class="row collapse modulez-news-article-details-row">
												<div class="column medium-12 large-6 text-left">
													by {{$dt['author'] or ''}}
												</div>
												<div class="column large-1 show-for-large-up"> | </div>
												<div class="column medium-12 large-5 text-left">
													{{$dt['date'] or ''}}
												</div>
											</div>
										</div>
									</div>

									<div class="row modulez-article-background-img-row equal-heights-lower-middle">
										@if( isset($dt['has_video']) && $dt['has_video'] == 1 )
											<div class="column small-12 modulez-news-school-img-col" data-interchange="[{{$dt['img'] or ''}}, (default)]">
										@else
											<div class="column small-12 modulez-news-school-img-col" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$dt['img'] or ''}}, (default)]">
										@endif
											<div class="modulez-news-article-category {{$dt['catSlug'] or ''}}-category-color">{{$dt['catName'] or ''}}</div>
										</div>
									</div>
									</div>

									<div class="row modulez-news-engagement-btns-row equal-heights-bottom">
										<div class="column small-4">
											<div class="modulez-news-engagement-btns text-center engagement-modulez-like-btn" onClick='Plex.setLikeTally("{{$dt['like_type'] or ''}}", "{{$dt['like_type_col'] or ''}}", "{{$dt['like_type_val'] or ''}}");'>Like</div>
										</div>
										<div class="column small-4">
											<a href="/news/article/{{$dt['slug'] or ''}}">
												<div class="modulez-news-engagement-btns text-center">View</div>
											</a>
										</div>
										<div class="column small-4">
											<div class="modulez-news-engagement-btns text-center engagement-modulez-skip-btn">Skip</div>
										</div>
									</div>

								</div>
							</div>
						</div><!-- end of owl item -->
						@endforeach
		
						<!-- $engagment_type == 'ranking' -->
						@elseif(!empty($right_handside_carousel['data']) && $right_handside_carousel['type'] == 'ranking' )

						@foreach($right_handside_carousel['data'] as $dt)
							<div class="item">
								<div class="row collapse modulez-ranking-row">
									<div class="column small-12">
										
										<div class="equal-heights-main">
										<div class="row equal-heights-top">
											<div class="column small-12">
												<div class="row">
													<div class="column small-12 modulez-ranking-title text-center">
														other rankings
													</div>
												</div>

												<div class="row">
													<div class="column small-12 modulez-ranking-type-name text-center">
														{{$dt[0]['title'] or ''}}
													</div>
												</div>
											</div>
										</div>
										

										@if(isset($dt[0]['listImage']) && $dt[0]['listImage'] !='')
										<div class="row equal-heights-upper-middle">
											<div class="column small-12 modulez-ranking-type-img text-center">
												<img src="{{$dt[0]['listImage'] or ''}}">
												
											</div>
										</div>
										@endif

										<div class="row collapse modulez-ranking-container equal-heights-lower-middle">
											<div class="column small-12">
												
												@for( $i = 0; $i < count($dt); $i++ )
												<div class="row modulez-ranking-university-row" data-equalizer>
													<div class="column medium-3 text-center modulez-rank-number-container" data-equalizer-watch>
														<div class="modulez-rank-number">#{{$dt[$i]['rank'] or ''}}</div>
													</div>
													<div class="column medium-9 modulez-university-name medium-text-center large-text-left" data-equalizer-watch>
														<a href="/college/{{$dt[$i]['slug'] or ''}}" class="university-name-link">
															{{$dt[$i]['school_name'] or ''}}
														</a>
													</div>
												</div>
												@endfor

											</div>
										</div>
										</div>
										

										<div class="row modulez-ranking-engagement-btns-row equal-heights-bottom">
											<div class="column medium-4">
												<div class="modulez-ranking-engagement-btns text-center engagement-modulez-like-btn" onClick='Plex.setLikeTally("{{$dt[0]['like_type'] or ''}}", "{{$dt[0]['like_type_col'] or ''}}", "{{$dt[0]['like_type_val'] or ''}}");'>Like</div>
											</div>

											<div class="column small-4">
												<a href="{{$right_handside_carousel['link'] or ''}}">
													<div class="modulez-ranking-engagement-btns text-center">
														View
													</div>
												</a>
											</div>
											<!--
											<div class="column medium-8">
												<div class="modulez-ranking-engagement-btns text-center" onClick='Plex.rightHandSideCarousel.viewCollegeComparison("{{$right_handside_carousel["comparisonSlugs"] or ''}}");'>Compare</div>
											</div>
											-->
											<div class="column medium-4">
												<div class="modulez-ranking-engagement-btns text-center engagement-modulez-skip-btn">Skip</div>
											</div>
										</div>

									</div>
								</div>
							</div><!-- end of owl item -->
						@endforeach
						<!-- $engagment_type == 'similar ranking' -->
						@elseif( $right_handside_carousel['type'] == 'similar ranking' )
						<div class="row collapse modulez-ranking-row">
							<div class="column small-12">
								
								<div class="row">
									<div class="column small-11 small-centered modulez-ranking-title text-center">
										similar ranked colleges with higher acceptance rate
									</div>
								</div>

								<div class="row collapse modulez-ranking-container">
									<div class="column small-12">
										
										@for( $i = 0; $i < 3; $i++ )
										<div class="row modulez-similar-ranking-university-row" data-equalizer>
											<div class="column medium-3 text-center modulez-rank-number-container" data-equalizer-watch>
												<div class="modulez-rank-number">#1</div>
											</div>
											<div class="column medium-9 modulez-university-name medium-text-center large-text-left" data-equalizer-watch>
												<a href="/college/harvard-university" class="university-name-link">
													Harvard University
												</a>
											</div>
										</div>
										@endfor

									</div>
								</div>

								<div class="row modulez-ranking-engagement-btns-row">
									<!--<div class="column medium-4">
										<div class="modulez-ranking-engagement-btns text-center engagement-modulez-like-btn">Like</div>
									</div>-->
									<div class="column medium-8">
										<div class="modulez-ranking-engagement-btns text-center" onClick="viewCollegeComparison('Stanford-University', 'Princeton-University', 'Harvard-University');">Compare</div>
									</div>
									<div class="column medium-4">
										<div class="modulez-ranking-engagement-btns text-center engagement-modulez-skip-btn">Skip</div>
									</div>
								</div>

							</div>
						</div>

						<!-- $engagment_type == 'comparisons' -->
						@elseif( $right_handside_carousel['type'] == 'comparisons' )
						<div class="row collapse modulez-comparison-row">
							<div class="column small-12">
								
								<div class="row">
									<div class="column small-10 small-centered modulez-comparison-title text-center">
										popular comparisons
									</div>
								</div>

								<div class="row modulez-comparison-of-schools-row">
									<div class="column small-6 text-right">
										<div class="row collapse">
											<div class="column small-12 text-center">
												<div class="modulez-compared-school" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/Stanford_University.png ,(default)]"></div>
											</div>
										</div>

										<div class="row">
											<div class="column small-11 small-centered modulez-compared-school-name text-center">
												Stanford University
											</div>
										</div>
									</div>

									<div class="column small-6">
										<div class="row collapse">
											<div class="column small-12 text-center">
												<div class="modulez-compared-school" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/Princeton_University.png ,(default)]"></div>
											</div>
										</div>

										<div class="row">
											<div class="column small-11 small-centered modulez-compared-school-name text-center">
												Princeton University
											</div>
										</div>
									</div>

									<div class="modulez-comparison-vs">vs</div>
								</div>

								<div class="row modulez-comparison-engagement-btns-row">
									<div class="column medium-4">
										<!--<div class="modulez-comparison-engagement-btns text-center engagement-modulez-like-btn">Like</div>-->
									</div>
									<div class="column medium-8">
										<div class="modulez-comparison-engagement-btns text-center" onclick="viewCollegeComparison('Stanford-University', 'Princeton-University', '');">Compare</div>
									</div>
									<div class="column medium-4">
										<div class="modulez-comparison-engagement-btns text-center engagement-modulez-skip-btn">Skip</div>
									</div>
								</div>

							</div>
						</div>
						@endif
					
			
		</div><!-- end of modulez-slider owl carousel -->

	</div>
	
	<!-- right side engagement ajax loader - appears whenever new slides get ajaxed in -->
	<div id="right-side-engagement-ajax-loader" data-interchange="[/images/rightside-engagement/on-to-the-next-one-hiker.png, (default)]]">
		<svg width="70" height="20">
			<rect width="20" height="20" x="0" y="0" rx="3" ry="3">
			    <animate attributeName="width" values="0;20;20;20;0" dur="1000ms" repeatCount="indefinite"/>
			    <animate attributeName="height" values="0;20;20;20;0" dur="1000ms" repeatCount="indefinite"/>
			    <animate attributeName="x" values="10;0;0;0;10" dur="1000ms" repeatCount="indefinite"/>
			    <animate attributeName="y" values="10;0;0;0;10" dur="1000ms" repeatCount="indefinite"/>
			</rect>
		  	<rect width="20" height="20" x="25" y="0" rx="3" ry="3">
			    <animate attributeName="width" values="0;20;20;20;0" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
			    <animate attributeName="height" values="0;20;20;20;0" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
			    <animate attributeName="x" values="35;25;25;25;35" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
			    <animate attributeName="y" values="10;0;0;0;10" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
		  	</rect>
		  	<rect width="20" height="20" x="50" y="0" rx="3" ry="3">
			    <animate attributeName="width" values="0;20;20;20;0" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
			    <animate attributeName="height" values="0;20;20;20;0" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
			    <animate attributeName="x" values="60;50;50;50;60" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
			    <animate attributeName="y" values="10;0;0;0;10" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
		  	</rect>
		</svg>
	</div>

	<!-- like animation - appears whenever user clicks like button -->
	<div class="like-animation" data-interchange="[/images/rightside-engagement/liked.png, (default)]]"></div>

</div><!-- end of engagement-modulez-rightside-content row -->
@endif
