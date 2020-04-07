<?php 
	// dd($data);
	$a = $news_details;
	// dd($a);
?>

<div id="blog-body-wrapper" class="blog-content-wrapper" data-idex="0" data-type="articles" data-id="{{$a['id']}}" data-title="{{$a['title']}}">


	<!-- ////////// subscribe section //////////// -->
	@include('b2b.blog.blogSubscribe')




	<div class="blog-content-cont clearfix">

		<!--/////////////// SOCIAL MEDIA BUTTONS \\\\\\\\\\\\\\\-->
		<div class="share-cont">
			@include('public.includes.shareButtons')
		</div>
		<!--\\\\\\\\\\\\\\\ SOCIAL MEDIA BUTTONS ///////////////-->



		<div class="blogview-img-container">
			@if(isset($a['img_lg']) && $a['img_lg'] != '')		
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$a['img_lg']}}" />
			@endif
		</div>



		<!-- /////////////// Blog content /////////////////-->
		<div class="blogview-title">{{  $a['title'] }}</div>

		<div class="author-cont-wrapper">


			<!-- ///////////// left column ////////// -->
			<div class="author-col">

				@if( isset( $a['authors_img'] )  && $a['authors_img'] != null)
				<div class="author-img-cont">
					<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{ $a['authors_img'] }} " />
				</div>
				@endif
				<div class="author-name">BY {{ strtoupper( $a['external_name'] ) }} </div>
				<div class="posted-time">{{ strtoupper($timeago) }} </div>
			</div>


			<!-- /////////////// right column ///////////////////-->
			<?php 

				//place highlighted content about halfway through
				// may choose to be more picky in future?  maybe after firt <p> found, if any else halfway?
				$start = 0;
				$end = null;
				if(strpos($a['content'], '</p>', 0)){
					$end =  strpos($a['content'], '</p>', 0) + 4;
				}else if (strpos($a['content'], '</div>', 0)){
					$end = strpos($a['content'], '</div>', 0) + 6;
				}
				//strlen($a['content'])/2;

				$paragraph1 = substr($a['content'], $start, $end-$start);
				$theRest = substr($a['content'], $end-$start);

				//get a highlighted section if none in DB
				$hs = 0;
				$hend = strpos($a['content'], '.', 0) ? strpos($a['content'], '.', 0) : strpos($a['content'], '!', 0);

				$highlighted = substr($a['content'], $hs, $hend);

			?>
			<div class="article-col">

				<div class="inner-text">
					{!! $paragraph1 or '' !!}
					
					<div class="highlighted-cont">
					@if(isset($a['highlighted']) && $a['highlighted'] != '')
						{!! $a['highlighted'] !!}
					@else
						{!! $highlighted or '' !!}
					@endif
					</div>

					{!! $theRest or '' !!}
				</div>



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




				<!-- \\\\\\\\\\\\\\ DISQUS on Plexuss.com /////////////// -->
				<div id="disqus_comment_section">
					<div id="disqus_thread"></div>
				</div>

				<script type="text/javascript">
				    /* * * CONFIGURATION VARIABLES * * */
				    var disqus_shortname = 'plexuss';
				    
				    /* * * DON'T EDIT BELOW THIS LINE * * */
				    (function() {
				        var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
				        dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
				        (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
				    })();
				</script>
				<noscript>Please enable JavaScript to view the <a href="https://disqus.com/?ref_noscript" rel="nofollow">comments powered by Disqus.</a></noscript>
				<!-- \\\\\\\\\\\\\\ DISQUS /////////////// -->
		


			</div>
		</div>






	</div>



</div>