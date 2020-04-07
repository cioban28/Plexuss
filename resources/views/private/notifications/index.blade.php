<!doctype html>
<html>
	<head>
		@include('private.headers.header')
	</head>
	<body id="{{$currentPage}}">
		@include('private.includes.topnav')
	
		<div class='row'>
			<!-- NOTIFICATIONS CONTAINER -->
			<div id='notify_page_container' class='small-12 column'>
				<!-- notify top row -->
				<div class='row'>
					<div id='notify_page_top_wrapper' class='small-12 column'>
						<div class='row'>
							<div class='small-12 column'>
								<span class="no-display" id="notify_page_title">All</span>
								<span id='notify_page_title' class="captalized center_title">
									Notifications
								</span>
							</div>

							<!--<div class='small-6 column text-right'>
								{{ Form::label( 'notify_page_filter', 'Sort by type: ', array( 'id' => 'notify_page_filter_label', 'class' => 'notify_page_filter' ) ) }}
								{{ Form::select( 'notify_page_filter', array( '0' => 'filter' ), 0, array( 'id' => 'notify_page_filter', 'class' => 'notify_page_filter' ) ) }}
							</div>-->
						</div>
					</div>
				</div>
				<!-- /notify top row -->
				<div class='row'>
					<div id='notify_page_notify_container' class='small-12 column'>
					<!-- //////////////////// NOTIFY CONTENTS\\\\\\\\\\\\\\\\\\\\ -->
						@if( sizeof($notifications['data']) >0)
							@foreach ($notifications['data'] as $note)

							<!-- NOTIFY ITEM -->
								@if($note['is_read'] == 1)
									<div class='column text-right message_time'>
										<span class='notify_time'>
											{{$note['date'] or ''}}
										</span>
									</div>
									<div class='row notify_page_notify_item' onClick="notificationItemOnClick('{{$note['link'] or '/'}}');">
										<div class='small-10 column notify_message'>
											<div class='notify_image columns {{$note['img'] or ''}}'></div>
											<div class="message-header">
												<span class='notify_title'>{{$note['name'] or ''}}</span>
												<span class='notify_snippet mobile-display'>{{$note['msg'] or ''}}</span>
											</div>
										</div>
										<div class='no-display small-2 column text-right'>
											<span class='notify_time'>
												{{$note['date'] or ''}}
											</span>
										</div>
									</div>
								@else
									<div class='unread_message column text-right message_time'>
										<span class='notify_time'>
											{{$note['date'] or ''}}
										</span>
									</div>
									<div class='unread_message row notify_page_notify_item' onClick="notificationItemOnClick('{{$note['link'] or '/'}}');">
										<div class='small-10 column notify_message'>
											<div class='notify_image columns {{$note['img'] or ''}}'></div>
											<div class="message-header">
												<span class='notify_title'>{{$note['name'] or ''}}</span>
												<span class='notify_snippet mobile-display'>{{$note['msg'] or ''}}</span>
											</div>
										</div>
										<div class='no-display small-2 column text-right'>
											<span class='notify_time'>
												{{$note['date'] or ''}}
											</span>
										</div>
									</div>
								@endif
							@endforeach
						@else
							<div class='row notify_page_notify_item'>
								<div class='small-12 column notify_message center_title'>
									No Record Found
								</div>
								
							</div>
						@endif
					<!-- \\\\\\\\\\\\\\\\\\\\ NOTIFY CONTENTS //////////////////// -->
					</div>
				</div>
				<!--<div class='row'>
					<div id='notify_page_show_more' class='small-12 column text-center'>
						<span>See more...
					</div>
				</div>-->
			</div>
			<!-- END NOTIFICATIONS CONTAINER -->
		</div>

		@include('private.includes.backToTop')
		@include('private.footers.footer')
	</body>
</html>
