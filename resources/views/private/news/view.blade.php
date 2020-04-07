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

@section('content')



	<!-- single view main nav row including breadcrumbs and menu - start -->
	<div class='row collapse show-for-medium-up fullWidth'>
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


			<div class="news-content-container">
				<div class='row show-for-medium-up destop-only-featured-box'>
			                <div class="search-articles-container column">

			                    <!-- search bar -->
			                    <div class="row collapse search-bar">
			                        <div class="small-10 column">
			                            {{Form::text('search_articles', null, array('class' => '', 'placeholder' => 'Search by University or keyword'))}}
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

			
		</div>
	</div>


	<!-- single view main nav row including breadcrumbs and menu - end -->
    
	<div itemscope itemtype="https://schema.org/Article" class='row fullWidth'>
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
				<div class="news-img-div" data-singleViewBanner="{{ $news_details->cat }}">
					<div class="featured singleViewBadge" data-singleViewBadge="{{ $news_details->cat }}">{{ $news_details->cat }}</div>
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

			<!-- article main content - start -->
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

						<p itemprop="articleBody" class="content-text ">{!! $news_details->content !!}</p>

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
