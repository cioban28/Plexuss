<?php 

	//dd($data);
	$lastdate = '';
	$printdate = true;
?>


<!-- for each date , we get messages ordered by created_at-->
<div class="contact-messages-wrapper"  data-threadid="{{  $thread_id or -1 }}">
@if(isset($msg) && !empty($msg))

	<div class="mb30 mt10 contact-load-more">Load previous post...</div>

	@foreach( $msg as $msg)
	<?php
		// dd($msg);
		$date = date('m / d / y', strtotime($msg['date']));
		// $time = date('g:ia', strtotime($msg['date']));

		if(strcmp($date, $lastdate) != 0)
			$printdate = true;
		else
			$printdate = false;

		$lastdate = $date;
	?>

			@if($printdate)
				<div class="date-divide">
					<div class="contact-date"> {{$date or 'unknown date'}}</div>
				</div>
			@endif


				<!-- for each message -->
				<div class="contact-msg-display-wrap clearfix" data-msgid="{{ $msg['msg_id'] or ''}}">

					<!-- name of who sent -->
					<div class="contact-msg-name">
						<div class="chat-portrait">
							<!-- if user has portrait -->
							@if(!empty($msg['img']))
								<img src="{{$msg['img']}}" alt="" class="portrait-other">
							@else
								<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png" alt="">
							@endif
						</div>
						<div class="contact-poster-name">{{$msg['full_name'] or ''}}</div>
					</div>

					
					<!-- time sent -->
					<div class="contact-time mt20">{{ $msg['time'] or ' '}}</div>


					<!--  message  -->
					<div class="contact-msg-msg-col mt20">
						<div class="contact-e-subject">
							Subject: &nbsp; 
							Interested in Attending UCLA
						</div>
						<div class="email-body-cont">
							{{ $msg['msg'] or ' ' }}
						</div>
					</div>
					

				</div>
			<!-- end for each date -->
	@endforeach

@else
	
	@if(isset($called_from) && $called_from == 'getHistoryMsg')
		<div class="contact-no-more-prev">no more messages.</div>
	@else
		<!-- used to signify that nothing has been returned -->
		<div class="contact-no-more"></div>
	@endif

@endif
</div>