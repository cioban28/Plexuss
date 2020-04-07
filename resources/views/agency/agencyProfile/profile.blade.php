<?php 
	$profile = $dt;
?>

@extends('agency.agencyProfile.master')
@section('content')
	<div class='agency-profile-container'>
		<div class='left-side-profile-container'>
			<div class='back-to-search-btn'>Back to College Expert Search</div>
			<div class='basic-profile-container'>
				<div class='profile-image'>
					<img src="{{ $profile['logo_url'] }}">
				</div>
				<div class='profile-info' data-agency_id='{{ $profile['agency_id'] }}'>
					<div class="share-buttons">
						<span class="stl">SHARE:</span>
						<a class="social_share share_facebook 
						" data-params="{&quot;platform&quot;:&quot;facebook&quot;,&quot;name&quot;:&quot;Agency Name&quot;,&quot;href&quot;:&quot;http:\/\/www.plexuss.com\/agency-profile\/1&quot;}"></a>
						<a class="social_share share_twitter 
						" data-params="{&quot;platform&quot;:&quot;twitter&quot;,&quot;text&quot;:&quot;Agency Name&quot;,&quot;href&quot;:&quot;http:\/\/www.plexuss.com\/agency-profile\/1&quot;}"></a>
						<a class="social_share share_pinterest
						" data-params="{&quot;platform&quot;:&quot;pinterest&quot;,&quot;description&quot;:&quot;Agency Name&quot;,&quot;href&quot;:&quot;http:\/\/www.plexuss.com\/agency-profile\/1&quot;}" data-pin-do="buttonPin" data-pin-config="above"></a>
						<a class="social_share share_linkedin
						" data-params="{&quot;platform&quot;:&quot;linkedin&quot;,&quot;title&quot;:&quot;Agency Name&quot;,&quot;href&quot;:&quot;http:\/\/www.plexuss.com\/agency-profile\/1&quot;,&quot;picture&quot;:&quot;https:\/\/s3-us-west-2.amazonaws.com\/asset.plexuss.com\/users\/images\/default.png&quot;}"></a>
					</div>
					<div class='row profile-header'>
						<h4>{{ $profile['agent_name'] }}</h4>
						<div class='star-rating'>
							@for ($i = 1; $i <= 5; $i++)
								@if (isset($profile['review_avg']) && ceil($profile['review_avg']) >= $i)
									<div class='star-icon active'></div>
								@else
									<div class='star-icon'></div>
								@endif
							@endfor
						</div>
						<small class='review-tab-span-btn'><span class='num-of-reviews'>{{ $profile['review_cnt'] or 0 }}</span> Reviews</small>
					</div>
					<div class='row location'>
						{{ isset($profile['country']) ? $profile['country'] : '' }}
					</div>
				</div>
			</div>
			<div class='detailed-profile-container mt10'>
				<div class='tab-btns-container'>
					<div class='tab-btn about-tab-btn active' data-tab='about'>About</div>
					<div class='tab-btn reviews-tab-btn' data-tab='reviews'>Reviews</div>
				</div>

				<div id='about-tab' class='tab mt20'>
					<div class='about-text'>
						{{ $profile['description'] or 'This agent has not provided a description' }}
					</div>

					<div class='write-review-container mt20'>
					@if (isset($signed_in) && $signed_in == 1)
						<div><b>Write a review</b></div>
						<div class='star-review-container' data-current-rating='0'>
							<div class='star-icon' data-rating='1'></div>
							<div class='star-icon' data-rating='2'></div>
							<div class='star-icon' data-rating='3'></div>
							<div class='star-icon' data-rating='4'></div>
							<div class='star-icon' data-rating='5'></div>
						</div>

						<div class='review-form'>
							<textarea rows="10" cols="80"></textarea>
							<div class='submit-review-btn'>Submit Review</div>
						</div>
					@else
						<a href='/signin?redirect={{ isset($profile_slug) ? $profile_slug : '' }}'>Sign in to submit a review</a>
					@endif
					</div>
				</div>

				<div id='reviews-tab' class='tab hidden mt20'>
					<div class='student-reviews-container'>
						{{-- Inject reviews here BELOW is a commented out example --}}
{{-- 					<div class='student-review'>
							<div class='profile-picture'>
								<img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png'>
							</div>
							<div class='review-content'>
								<div class='basic-info'>
									<div class='name'>
										Harold P.
									</div>
									<div class='rating-and-date'>
										<div class='rating'>
											<div class='star-icon active'></div>
											<div class='star-icon active'></div>
											<div class='star-icon active'></div>
											<div class='star-icon active'></div>
											<div class='star-icon'></div>
										</div>
										<div class='date'>July 27</div>
									</div>
								</div>
								<div class='location'>Pleasant Hill, CA</div>
								<div class='review-text-content'>
									I spent two summers at Stanford University with Education Unlimited. I was at their debate camps,
									and I always left prepared and energized for the upcoming school year.
									The instruction was intensive and in depth, and the instructors were all well connected at
									Universities I was interested in attending. There is no doubt that my college prospects were
									increased by my attendance at camp.
								</div>
							</div>							
						</div> --}}
					</div>
					<div class='write-review-container mt20'>
					@if (isset($signed_in) && $signed_in == 1)
						<div><b>Write a review</b></div>
						<div class='star-review-container' data-current-rating='0'>
							<div class='star-icon' data-rating='1'></div>
							<div class='star-icon' data-rating='2'></div>
							<div class='star-icon' data-rating='3'></div>
							<div class='star-icon' data-rating='4'></div>
							<div class='star-icon' data-rating='5'></div>
						</div>

						<div class='review-form'>
							<textarea rows="10" cols="80"></textarea>
							<div class='submit-review-btn'>Submit Review</div>
						</div>
					@else
						<a href='/signin?redirect={{ isset($profile_slug) ? $profile_slug : '' }}'>Sign in to submit a review</a>
					@endif
					</div>
				</div>
			</div>
		</div>

		<div class='right-side-profile-container'>
			<div class='engage-container'>
				<div class='header-text'>Engage</div>
				<div class='message-btn' data-slug={{ isset($profile['message_slug']) ? $profile['message_slug'] : NULL }}>Message</div>
			</div>

			<div class='services-container mt10'>
				<div class='header-text'>Services Offered</div>
				<ul class='services mt10'>
					@foreach ($profile['services_offered'] as $key => $value)
						<li class='service'>{{ $value }}</li>
					@endforeach

					@if (empty($profile['services_offered']))
						<li class='service'>No services found</li>
					@endif
				</ul>
			</div>

			<div class='hours-container mt10'>
				<div class='header-text'>Hours</div>
				<div class='hours-list mt10'>
					@if (isset($profile['days_of_operation']) && !empty($profile['days_of_operation']))
						@foreach ($profile['days_of_operation'] as $day => $hours)
							<?php $closed = !intval($hours['start']) || !intval($hours['end']); ?>
							<div class='hours'>
								<div class='day'>{{ ucfirst(substr($day, 0, 3)) }}</div>
								@if ($closed) 
									<div class='time'>Closed</div>
								@else
									<div class='time'>{{ strtolower($hours['start']) . ' - ' . strtolower($hours['end']) }}</div>
								@endif
							</div>
						@endforeach
					@else
						<div class='hours'>
							<div class='day'>Mon</div>
							<div class='time'>Not Listed</div>
						</div>

						<div class='hours'>
							<div class='day'>Tues</div>
							<div class='time'>Not Listed</div>
						</div>

						<div class='hours'>
							<div class='day'>Wed</div>
							<div class='time'>Not Listed</div>
						</div>

						<div class='hours'>
							<div class='day'>Thur</div>
							<div class='time'>Not Listed</div>
						</div>

						<div class='hours'>
							<div class='day'>Fri</div>
							<div class='time'>Not Listed</div>
						</div>
						<div class='hours'>
							<div class='day'>Sat</div>
							<div class='time'>Not Listed</div>
						</div>

						<div class='hours'>
							<div class='day'>Sun</div>
							<div class='time'>Not Listed</div>
						</div>
					@endif
				</div>
			</div>
		</div>

	</div>
@stop