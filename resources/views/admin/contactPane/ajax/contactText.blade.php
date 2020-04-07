<?php 

	//print_r($data);
	//print_r($called_from);
	//dd($msg[0]['show_previous_msg);
	$lastdate = '';
	$printdate = true;

	$len = count($msg);
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


			<!-- for each date -->
			@if($printdate)
				<div class="date-divide">
					<div class="contact-date"> {{$date or 'unknown date'}}</div>
				</div>
			@endif


				<!-- for each message -->
				<div class="contact-msg-display-wrap clearfix" data-msgid="{{ $msg['msg_id'] or ''}}">

					<!-- if from student -->
					@if(isset($msg['is_org']) && $msg['is_org'] == 0)
						<div class="clearfix">

							<!-- name of who sent -->
							<div class="contact-msg-name fl">
								<div class="chat-portrait">
								
									@if(!empty($msg['img']))
										<img src="{{$msg['img']}}" alt="" class="portrait-other" />
									@else
										<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png" alt="">
									@endif
								</div>
								<div class="contact-poster-name">{{$msg['full_name'] or ''}}</div>
								
							</div>
							<div class="contact-msg-msg clearfix txt-bubble-s">
								<div class="txt-bubble-s-arrow"></div>	
								<!--  message  -->
								<div class="contact-msg-msg-col">
											{{ $msg['msg'] or '' }}
								</div>
								<!-- time sent -->
								<div class="contact-time">{{ $msg['time'] or '' }}</div>
							
							</div>

						</div>

					<!-- else, from Plexuss or College -->
					@else
						<div class="clearfix">


							<!-- name of who sent -->
							<div class="contact-msg-name fr ">
								<div class="chat-portrait fr">

									@if(!empty($msg['img']))
										<img src="{{$msg['img']}}" alt="" class="portrait-other" />
									@else
										<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/default.png" alt="">
									@endif
								</div>
								<div class="contact-poster-name txt-right pr23">{{$msg['full_name'] or ''}}</div>
								
							</div>
							
							<div class="contact-msg-msg clearfix txt-bubble-u">

								<!--  message  -->
								<div class="contact-msg-msg-col-text">
											{{ $msg['msg'] or '' }}
								</div>
								

								<!-- time sent -->
								<div class="contact-time-text">{{ $msg['time'] or ''}}</div>

								<div class="txt-bubble-u-arrow"></div>
							</div>

							

						</div>
					@endif

				</div>
				<!-- end for each date -->
	@endforeach

@else
	@if( isset($called_from)  && $called_from == 'getHistoryMsg')
		<div class="contact-no-more-prev">no more messages.</div>
	@else
		<!-- used to signify that nothing has been returned -->
		<div class="contact-no-more"></div>
	@endif

@endif
</div>