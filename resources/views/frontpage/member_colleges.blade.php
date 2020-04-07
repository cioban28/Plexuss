@if( isset($colleges_in_our_network) )
	@for( $i = 0; $i < count($colleges_in_our_network); $i++ )
		<div class="row college-available-to-chat-row">
			<div class="column large-1 show-for-large-up text-center">
				<img class="chat-college-img" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/{{$colleges_in_our_network[$i]['logo_url']}}" alt="{{$colleges_in_our_network[$i]['school_name']}} Logo">
			</div>
			
			<div class="column small-8 large-8">
				<div class="row">
					<div class="column small-12 large-9">
						@if($colleges_in_our_network[$i]['country_code'] == 'US')
						<div class="chat-college-name"><a href="/college/{{$colleges_in_our_network[$i]['slug']}}"><strong>{{$colleges_in_our_network[$i]['school_name']}}</strong></a>
						</div>
						@else
						<div class="chat-college-name"><a href="/college/{{$colleges_in_our_network[$i]['slug']}}"><strong>{{$colleges_in_our_network[$i]['school_name']}}</strong></a>&nbsp;&nbsp;&nbsp;
						<div class="flag flag-{{ strtolower($colleges_in_our_network[$i]['country_code']) }}"> </div>
						</div>
						@endif
						<div class="chat-college-location show-for-large-up">{{$colleges_in_our_network[$i]['city'].", ".$colleges_in_our_network[$i]['state'] }}</div>
					</div>
					<div class="column small-12 large-3 chat-college-time-available">
						@if($colleges_in_our_network[$i]['is_live'] == 0)
							<span style="color:red;"></span>
						@else
							<span style="color:green;">Online</span>
						@endif
					</div>
				</div>
			</div>

			<div class="column small-4 large-3 chat-college-chat-btn-column">
				@if ($colleges_in_our_network[$i]['is_live'] == 0)
					@if($signed_in == 1)
				        @if (!$colleges_in_our_network[$i]['isInUserList'])
							<a href="javascript:void(0)" data-reveal-id="recruitmeModal" data-reveal-ajax="/ajax/recruiteme/{{$colleges_in_our_network[$i]['college_id']}}">
								<div class="chat-college-chat-btn text-center"><b>GET RECRUITED</b></div>
							</a>
						@else
							<div class="chat-college-chat-btn already-requested text-center"><b>ALREADY REQUESTED</b></div>
						@endif
					@else
						<a href="/signup?requestType=recruitme&collegeId={{$colleges_in_our_network[$i]['college_id']}}">
							<div class="chat-college-chat-btn text-center"><b>GET RECRUITED</b></div>
						</a>
					@endif
				@else 
					<a href="{{$colleges_in_our_network[$i]['redirect_url']}}">
						<div class="chat-college-chat-btn text-center">
							<strong>CHAT</strong>
						</div>
					</a>
				@endif
			</div>
			
		</div>
	@endfor
@endif