

		@if(isset($articles) &&  !empty($articles))
		@foreach($articles as $a)

<?php 
	
	$MONTHS = ['JAN', "FEB", "MARCH", "APRIL", "MAY", "JUNE", "JULY", "AUG", "SEPT", "OCT", "NOV", "DEC"];

// dd($a);
	$toks = explode(',', $a->article_date);

	$day = strtoupper($toks[0]);
	$mandd = $toks[1];
	$yr = explode(' ', $toks[2])[1];

?>
			<div class="newf-article-wrapper clearfix">

				<div class="newf-lcol">
					<div class="date-box">
						<div class="newf-day">{{$day or ''}}</div>
						<div class="newf-date">{{ $mandd or ' '}} , {{$yr or ''}}</div>
					</div>
				</div>

				<div class="newf-rcol">
					
					<div class="newf-art-wrapper">
						<!--/////////////// SOCIAL MEDIA BUTTONS \\\\\\\\\\\\\\\-->
						<div class="share-cont">
					
							@include('public.includes.shareButtons')
						
						</div>
						<!--\\\\\\\\\\\\\\\ SOCIAL MEDIA BUTTONS ///////////////-->



						<!-- image -->
						@if( isset($a->img_lg) && $a->img_lg != '' && $a->img_lg != null)		
								<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/{{$a->img_lg}}" />
						@endif


						<!-- title -->
						<div class="newf-title">{{  $a->title  or ''}}</div>

						<!-- text -->
						{!! $a->content or '' !!}

						<!-- divider -->
						<div class="newf-dotted-line">
							<div class="lil-logo"></div>
						</div>

					
					</div>
				</div>
			</div>
		@endforeach
	
		@endif