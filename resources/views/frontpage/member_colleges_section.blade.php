@if( isset($signed_in) && $signed_in == 1 )
<div class="row hide-for-small-only chat-colleges-online-title make-room-for-signedin-topbar">
@else
<div class="row hide-for-small-only chat-colleges-online-title">
@endif
	<div class="column medium-9 colleges-online-head">
		Chat Directly with College Admission Advisors
	</div>

	<div class="column medium-2 chat-with-plex-btn">
		<a href="/portal/messages/7916/college" data-tooltip aria-haspopup="true" class="has-tip" title="Chat with Plexuss!">
			<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/chat-with-plexuss.png" width="57" height="39" alt="Chat with Plexuss">
		</a>
	</div>

	<!-- back/close button to close side bar sections when open - start -->
	<div class="column medium-1 medium-text-right frontpage-back-btn hide-for-small-only">
		<img class="tablet-up-back-btn" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/gray-x.png" alt="">
	</div>
</div>

<!-- list of colleges online now row -->
<div class="member-container">
	<div class="chat-colleges-listed-container">
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
	</div>
	<div class="member-loader text-center">
	    <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/loading.gif" width="32" height="32" alt="Loading gif">
	</div>
</div>

<script>
	$('.chat-colleges-listed-container').scroll(function(e){
	    var home = Plex.homepg;
	    if( home.member_scroll_pos < $(this).scrollTop() ) getMembersInOurNetwork();//if scrolling down, then get more members
	    home.member_scroll_pos = $(this).scrollTop();//set scroll pos new scroll position
	});

	function getMembersInOurNetwork(){
	    var home = Plex.homepg, loader = $('.member-loader');

	    //if ajax is not in progress (getting member colleges), then run ajax, otherwise don't
	    if( !home.in_progress && home.more_members ){
	        loader.show();//show loader
	        home.in_progress = !0;//change to true right before ajax call and make false once done allowing next ajax call to go

	         $.ajax({
	        	url: 'ajax/homepage/getMembersInOurNetwork/'+home.skip+'/'+home.limit,
	        	type: 'GET',
	        	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	        })
	        .done(function(data) {
	            loader.hide();//hide loader
	            if( data === 'done' ){
	                home.no_more_members = !1;
	            }else if( data !== 'fail' ){
	                if( home.skip === 0 ) loader.removeClass('init');
	                home.skip += 10;//increase skip for query to get the next 10 members
	                home.in_progress = !1;//ajax done so next ajax call can go
	                $('.chat-colleges-listed-container').append(data);//append results
	            }
	        });
	    }
	}
</script>