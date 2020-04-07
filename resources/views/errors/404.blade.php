@extends('errors.master')

@section('content')

	<?php
		$yearbook_photos = ['yearbook_1.jpg','yearbook_2.jpg','yearbook_3.jpg','yearbook_4.jpg','yearbook_5.jpg','yearbook_6.jpg','yearbook_7.jpg','yearbook_8.jpg','yearbook_9.jpg','yearbook_10.jpg','yearbook_11.jpg','yearbook_12.jpg','yearbook_13.jpg','yearbook_14.jpg','yearbook_15.jpg','yearbook_16.jpg','yearbook_17.jpg','yearbook_18.jpg'];
	?>

	<div class="row main-404-container">
		<div class="column small-12">

			<div class="row">

				<!-- left side content/info/links/yada yada - start -->
				<div class="column small-12 large-6 left-side-column-container">
					
					<div class="row">
						<div class="column small-12 error_num small-only-text-center">
							{{$error_num or 404}}...
						</div>
					</div>

					<div class="row">
						<div class="column small-12 something-went-wrong-msg small-only-text-center">
							@if($error_num == 404)
								The requested URL was not found on this server. That’s all we know, but at least this isn't as bad as yearbook photos.
							@else
								Something went wrong, but at least this isn't as bad as yearbook photos
							@endif
						</div>
					</div>

					<div class="row">
						<div class="column small-12">
							<div class="get-back-on-track-btn text-center">Get back on track<span>!</span></div>
						</div>
					</div>

					<div class="row other-pages-interested-in-list-row">
						<div class="column small-12 small-only-text-center">Other pages you may be interested in:</div>
						<div class="column small-12 medium-6 small-only-text-center">
							<div><a href="/comparison">Compare colleges</a></div>
							<div><a href="/college">Find colleges</a></div>
							<div><a href="/ranking">Plexuss college ranking</a></div>
							<div><a href="/news">The quad</a></div>
						</div>
						<div class="column small-12 medium-6 small-only-text-center">
							<div><a href="/college-submission">Join as a College</a></div>
							<div><a href="/college-prep">Join as a college prep</a></div>
							<div><a href="/scholarship-submission">Submit a scholarship</a></div>
							<div><a href="/carepackage#sponsor">Become a care package partner</a></div>
						</div>
					</div>

					<div class="row">
						<div class="column small-12">
							<div><small>Feel free to contact us at support&commat;plexuss.com if you need additional help navigating our site.</small></div>
							<div><small>Image credits: Angela Rossi | <a href="http://www.beatupcreations.com" target="_blank">www.beatupcreations.com</a></small></div>
						</div>
					</div>

				</div>
				<!-- left side content/info/links/yada yada - end -->




				<!-- right side embarrassing yearbook pics - start -->
				<div class="column small-12 large-6 right-side-column-container">
					<div class="row">
						@foreach( $yearbook_photos as $photos )
						<div class="column small-4 medium-2 large-4 photo-container text-center">
							<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/errors/404/{{$photos}}" alt="">
						</div>
						@endforeach
					</div>
				</div>
				<!-- right side embarrassing yearbook pics - end -->
			</div>

		</div>
	</div>

@stop