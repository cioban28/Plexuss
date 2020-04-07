<?php 

	//dd($data);
	// dd( get_defined_vars() ;)
?>



@extends('private.news.master')

@section('sidebar')

	<!--
	<div class="hide-for-small-only">
    <div class="large-12 columns side-bar-news page-right-side-bar side-bar-1 radius-4"></div>
	<div class="large-12 columns  side-bar-news page-right-side-bar side-bar-2 radius-4"></div>
	<div class="large-12 columns side-bar-news page-right-side-bar side-bar-1 radius-4"></div>
	-->
    <div class='row'>
		<div class='column small-12'>

            <!-- hiding get started side bar if profile status is not zero 
            @if ($profile_perc == 0)
            <div class="row">
				<div class="large-12 columns page-right-side-bar side-bar-2 radius-4">
					<div class="text-center">
						<p class="step-number">1</p>
					</div>
					<p class="right-bar-heading white">Get Started</p>
					<p class="right-bar-para white">Wondering why your indicators are at zero?</p>
					<p class="right-bar-para white">You need a profile for the recruitment process to begin.</p>
					<div class="large-12 text-center">
						<a href="/profile" class="button get-started-button">Start your Profile</a>
					</div>
				</div>
			</div>
            @endif-->

			<!-- RIGHT SIDE FOOTER (HELP, CONTACT, ABOUT, ETC.) HERE! -->
			@if( isset($signed_in) && $signed_in == 0 )
                @include('private.includes.right_side_createAcct_news')
            @endif
        </div>

        

        <div class='column small-12'>
            @if( isset($signed_in) && $signed_in == 1 )
                @include('private.includes.invite_friends_right_side')
            @endif

             <!-- adsense area -->
             @include('private.includes.adsense-307x280')

			@include('private.includes.right_side_get_started')
			@include('private.includes.right_side_footer')
		</div>
    </div>

	
@stop


<?php 
	// dd($data);
?>
@section('content')

	<!-- single view main nav row including breadcrumbs and menu - start -->
	<div id="newsContainer" class='row collapse fullWidth'   data-uid="{{$user_id or ''}}" data-nid="{{$hashed_news_id or ''}}">
		<div class="small-12 column news-header hidden-display">

			<!-- breadcrumbs -->
			<div class="new-header-top">
				<ul class="breadcrumbs">
					<li><a href="/news/">The Quad</a></li>
					<li><a href="/news/catalog/{{$bread_data->catSlug}}">{{$bread_data->cat}}</a></li>
					<li><a href="/news/subcategory/{{$bread_data->subcatSlug}}">{{$bread_data->subcat}}</a></li>
				</ul>
			</div>

			@include('private.news.newsNavigation')
			<!-- the quad menu - end -->

			<!-- search dropdown -->
			<div class="news-content-container">
				<div class='row show-for-medium-up destop-only-featured-box'>
			                <div class="search-articles-container column">

			                    <!-- search bar -->
			                    <div class="row collapse search-bar">
			                        <div class="small-10 column">
			                            {{Form::text('search_articles', null, array('id' => 'srch_articles','class' => '', 'placeholder' => 'Search by University or keyword'))}}
			                        </div>
			                        <div class="small-2 column submit-container" data-interchange="[https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/Search.png, (default)]">
			                            <a href="" class="submit-search postfix"></a>
			                        </div>
			                    </div>

			                    <div class="row collapse col-headers">
			                        <div class="column small-8"><h5><b>Articles & Videos</b></h5></div>
			                        <div class="column small-4"><h5 class="text-center"><b>My Schools</b></h5></div>
			                    </div>


			                    <!-- results -->
			                    <div class="result-and-school-container"> 
			                        <div class="row collapse">
			                            <!-- results -->
			                            <div class="column small-8 results-container"> 
			                                <div class="results">
			                                    <!-- results injected here -->
			                                    <div class="intro-msg">
			                                        <b>Search something to get started...</b>
			                                    </div>
			                                    <div>No results</div>
			                                </div>
			                                <div class="loader hide">
			                                    <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/loading.gif" alt="search loader">
			                                </div>
			                            </div>


			                            <!-- recruited schools -->
			                            <div class="column small-4 school-container"> 
			                                <ul>
			                                    @if( isset($signed_in) && $signed_in == 1 )
			                                        @if( isset($college_recruits) && !empty($college_recruits) )
			                                            @foreach($college_recruits as $college)
			                                                <li><a href="" class="college-recruit">{{$college['name']}}</a></li>
			                                            @endforeach
			                                        @else
			                                            <li class="text-center">No schools in your list</li>
			                                        @endif
			                                    @else
			                                        <li class="text-center">
			                                            <a href="/signin?redirect=news">Signin</a> to see schools
			                                        </li>
			                                    @endif
			                                </ul>
			                            </div>
			                        </div>
			                    </div>
			                </div>
			    </div>
			</div>
			<!-- end search dropdown -->

		</div>
	</div>
	<!-- single view main nav row including breadcrumbs and menu - end -->
    
	<div id="newsContentContainer" itemscope itemtype="https://schema.org/Article" class='row fullWidth'>
		<div class="small-12 column news-description">

			<!-- social row - start -->
			<div class='row collapse'>
				<!-- disqus comment count and comment link -->
				<div class='small-5 column comments-elevator'>
				</div>

				<!-- share buttons -->
				<div class= 'small-7 column'>
					<div class='share-buttons'>
						@include('public.includes.shareButtons')
					</div>
				</div>
			</div>
			<!-- social row - end -->


			<!-- articles large banner image -->
			<div>
				<span class="category-tag no-big-display">CAMPUS LIVING</span>
				<div class="news-img-div college-essay-img" data-singleViewBanner="{{ $news_details->cat }}">
					<div class="featured collegeEssays singleViewBadge" data-singleViewBadge="{{ $news_details->cat }}">{{ $news_details->cat }}</div>
					@if( isset($news_details->has_video) && $news_details->has_video == 1 )
						{{ $news_details->img_lg }}
						<div class="owl-container">
							<div class="news-owl-carousel owl-carousel owl-theme">
								@foreach( $video_articles as $article )
									<div class="item">
										<a href="/news/article/{{$article->slug}}">
											<img src="{{$article->img_sm}}" alt="{{$article->title}}">
											<div class="name-layer text-center">
												<div>{{$article->title}}</div>
											</div>
										</a>
									</div>
								@endforeach
							</div>
							<div class="owl-nav-btn nav-prev"><div class="arrow-back"><div class="arrow"></div></div></div>
							<div class="owl-nav-btn nav-next"><div class="arrow-back"><div class="arrow"></div></div></div>
						</div>
					@else
						@if($news_details->img_lg!='')					
							<img itemprop="image" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$news_details->img_lg}}" 
							title="{{$news_details->title}}" alt="{{$news_details->title}}" class="news-detail-image" />
						@else
							<div style="text-align:center"><img src="/images/no_photo.jpg" title="Image" alt="Image" /> </div>				   
						@endif	  
					@endif
				</div>
			</div>
		
			<!-- article title -->
			<h1 itemprop="headline" class="heading-h1">{{ $news_details->title }}</h1>




			<!--/////////// article main content - start ////////////-->
			<div class="row">
				<div class="medium-2 small-12 columns">
					@if(isset( $source['internal_img'] ))
						<img src="{{ $source['internal_img'] }}" style="margin-bottom:10px;" alt="">
						<div class="internalAuthor">
							<p class="text-right">By {{ strtoupper($source['internal_fname']) }}</p>
							<p class="text-right"> {{ strtoupper($source['internal_lname']) }}</p>
						</div>
						<p class="text-right">1 hour ago</p>
					@else
						<div class="internalAuthor">
							<p class="small-text-left medium-text-right hide-for-small-only">By 
								@if( isset($news_details['authors_profile_link']) && !empty($news_details['authors_profile_link']) )
								<a href="{{$news_details['authors_profile_link'] or ''}}" rel="author" target="_blank">{{ strtoupper($source['external_author']) }}</a>
								@else
								{{ strtoupper($source['external_author']) }}
								@endif
							</p>
							<p class="small-text-left medium-text-right hide-for-small-only">{{ strtoupper($source['external_name']) }}</p>
							@if( isset($news_details['authors_img']) && !empty($news_details['authors_img']) )
							<div class="authors_img hide-for-small-only"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$news_details['authors_img']}}" alt=""></div>
							<p class="hide-for-small-only">{{$news_details['authors_description'] or ''}}</p>
							@endif
						</div>
					@endif
				</div>
				<div class="medium-10 small-12 columns">
					<!-- //////////////////// CONTENT CONTAINER \\\\\\\\\\\\\\\\\\\\-->
					<div style="position: relative;">

						<div class="row author_row_mobile" data-equalizer>
							<div class="column small-3" data-equalizer-watch>
								<div class="authors_img hide-for-medium-up">
									@if( isset($news_details['authors_img']) && !empty($news_details['authors_img']) )
									<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$news_details['authors_img']}}" alt="">
									@endif
								</div>
							</div>
							<div class="column small-9" data-equalizer-watch>
								<p class="no-big-display author-name hide-for-medium-up">BY 
									@if( isset($news_details['authors_profile_link']) && !empty($news_details['authors_profile_link']) )
									<a href="{{ $news_details['authors_profile_link'] or ''}}" rel="author">{{ strtoupper($source['external_author']) }}</a>
									@else
									{{ strtoupper($source['external_author']) }}
									@endif
								</p>
								<p class="small-text-left medium-text-right hide-for-medium-up authors_source"> {{ strtoupper($source['external_name']) }}</p>
								<p class="hide-for-medium-up authors_description">{{$news_details['authors_description'] or ''}}</p>
							</div>
						</div>
						<p itemprop="datePublished" class="post-time small-only-text-right">{{ $news_details->created_at }} ago</p>

						


						<!-- article section container-->
						<div id="essayWrapper" itemprop="articleBody" class="content-text">

							

							<!-- if user unlocks, get content from server, place premium content in below-->	
							@if( isset($is_essay_purchased) && $is_essay_purchased == true)
								{!! $news_details->premium_content  or '' !!}
								
							@else

								<!--///// article body ////-->
								<div  class="essay-cont">{!! $news_details->basic_content !!}</div>

							
								<!-- unlock essays  -->
								<div class="unlock-cont">
									
									<!-- if not signed in -->
									@if( isset($signed_in) && $signed_in == 0 )
										<div class="unlock-txt">To unlock College Essays</div>
										<div class="unlock-btn signupModal-btn">Join Premium!</div>
									@else
										<!-- else signed in (premium and not)-->	
										<div class="unlock-btn unlock-essay">Unlock Essay?</div>
										<div class="unlock-txt-small">
											<span class="num-unlocked">{{ $num_of_eligible_premium_essays or 0 }}</span> Essay Views Remaining 
										</div>
									@endif

								</div>


								<!-- ajax loader -->
								<div id="unlockajaxloader" style="display:none;"><center><img src="/images/colleges/loading.gif" alt=""/></center></div>

								<!-- premium section -->
								<div class=" pre-blur">
									<!-- svg blur for cross browser support -->
									<svg version="1.1" xmlns="http://www.w3.org/2000/svg" height="0" width="0">
										<defs>
										    <filter id="blur" x="0" y="0">
										        <feGaussianBlur stdDeviation="5" />
										    </filter>
										</defs>
									</svg>  

									<!-- dummy text to show 'blurred' (do not want userrs to view source and see actual text)-->
									<p>Lorem ipsum dolor sit amet, tale debet inciderint an his, dicit mentitum offendit id nec, ius mnesarchum inciderint eu. Eos an invenire tractatos efficiendi. Ex timeam impetus sanctus usu, at est apeirian complectitur. Nec ad justo nominati, meliore graecis explicari id mei. Duo porro suscipit vulputate no, vel id tota summo officiis.</p>

									<p>Nominavi suavitate nec cu, magna intellegebat nam ei. At per nihil accusamus. Vix malis voluptatum ex, ut usu apeirian patrioque vituperatoribus, ex mel dico quodsi petentium. Nostrum indoctum iudicabit eu cum.</p>

									<p>Vitae omittam neglegentur ius ad, est no meliore adipisci disputando. Nec aperiri atomorum tractatos ut, fugit meliore eam ex. Eu cum ubique accusamus intellegam. Et has principes vulputate, ut duo solet decore accusata, et sea lobortis imperdiet. Qui ne populo liberavisse, id his aliquam denique epicuri, malis dicta invidunt mei ei.</p>

									<p>Mel te elitr veniam delicata. Te pro nostro fierent, ex mea accusam aliquando. Vis in option molestie, homero regione concludaturque ei cum. Quis saperet nominati eu vel.</p>

									<p>Lorem ipsum dolor sit amet, tale debet inciderint an his, dicit mentitum offendit id nec, ius mnesarchum inciderint eu. Eos an invenire tractatos efficiendi. Ex timeam impetus sanctus usu, at est apeirian complectitur. Nec ad justo nominati, meliore graecis explicari id mei. Duo porro suscipit vulputate no, vel id tota summo officiis.</p>

									<p>Nominavi suavitate nec cu, magna intellegebat nam ei. At per nihil accusamus. Vix malis voluptatum ex, ut usu apeirian patrioque vituperatoribus, ex mel dico quodsi petentium. Nostrum indoctum iudicabit eu cum.</p>

								</div>
							
							@endif


						</div>
						
					</div>
					<!--\\\\\\\\\\\\\\\\\\\\ CONTENT CONTAINER ////////////////////-->
				</div>
			</div>
			<!-- article main content - end -->

			<div class="clearfix"></div>

			<!-- social media buttons -->
			<div class="row">
				<p class="medium-12 columns news-source">
					<span style="float:right;">
						@if(isset($source['external_name']))
							<a href="{{ $source['external_url'] }}" rel="nofollow">SOURCE: {{ $source['external_name'] }}</a>
						@endif
					</span>
				</p>
				<div class="clearfix"></div>
				<!--/////////////// SOCIAL MEDIA BUTTONS \\\\\\\\\\\\\\\-->
				<div class="share-buttons-striped">
					@include('public.includes.shareButtons')
				</div>
				<!--\\\\\\\\\\\\\\\ SOCIAL MEDIA BUTTONS ///////////////-->
			</div>



			<!--  -->
			<div class="clearfix"></div>
			<!--  -->

		</div>



	</div>
@stop







<!-- /////////// SIGN UP MODAL for essay content ////////////// -->
		



<!-- message modal -->
<div id="admitseeMessageModal" 
	 class="reveal-modal news-msg-modal" 
	 data-reveal aria-labelledby="modalTitle" 
	 aria-hidden="true" 
	 role="dialog" 
	 style="display:none;">
	
					
	<div class="insufficientfunds-error">				
    
	
    	 <p class="modal-message">
		    Looks like you've used all your essay views, <br>
		    please sign up to view additional essays.</p>

    
    	<div class="_upgradetoPremium">Join Premium!</div>
    

    </div>

    <div class="invalid-error">
    	<p class="modal-message"></p>
    </div>

    <a class="close-reveal-modal closeModal-btn" aria-label="Close">&times;</a>

</div>

