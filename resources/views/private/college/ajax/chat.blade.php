<?php
    if (isset($college_data)) {
        $collegeData = $college_data;
    }
?>


<!--///// social buttons div of holding \\\\\-->
<div id="share_div_of_holding"
    data-share_params='{
        "page_title":"{{-- $collegeData->page_title --}}",
        "image_prefix":"{{-- $collegeData->share_image_path --}}",
        "image_name":"{{-- $collegeData->share_image --}}"
    }'
></div>
<!--\\\\\ social buttons div of holding /////-->
<!--///// chat div of holding \\\\\-->
<div id="chat_div_of_holding"
	data-college_id="{{ $CollegeId }}"
></div>
<!--\\\\\ chat div of holding /////-->


{{-- If chat is not declaried we may run into issue here. AO --}}
<div class='chatWrapper @if ( $chat['isLive'] && $signed_in ) {{' active'}} @endif'
	data-signed_in='{{ $signed_in }}'>
	<div class="row chatOnlineBox"><!-- Chat Area -->
		<!-- idle overlay -->
		<div id='chat_idle_overlay_wrapper' class='small-12 column idle_overlay_wrapper'>
			<div class='row'>
				<div id='chat_idle_overlay' class='small-12 column idle_overlay'>
					<div class='row'>
						<div id='chat_idle_title' class='small-12 column text-center idle_title'>
							<span>Idle Mode</span>
						</div>
					</div>
					<div class='row'>
						<div id='chat_idle_description' class='small-12 column text-center idle_description'>
							<span>You are now in idle mode. Our servers appreciate the break! Your chat will updated once you type or move your mouse.</span>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- end idle overlay -->
		<div class="column small-12">
			<div class="row collapse">
				<div class="column small-12 medium-3 leftChatColumn">
					<div class="row">


						<div class="column small-12 chatLeftHeader">
							Chat
							<span>(Click to Chat)</span>
						</div>

						<div class='chatUserLists'>
							
							@if (isset($chat) && isset($chat['topicUsr']))
								@foreach ($chat['topicUsr'] as $key => $usr)
									<div class="column small-12 chatUser @if ($key == 0 ) {{' selected'}} @endif" data-topicid='{{ $usr['thread_id'] }}' data-threadtypeid='{{ $usr['thread_type_id'] }}' data-threadtype='{{ $usr['thread_type'] }}' onclick="Plex.chat.changeTopic(this);">
										{{$usr['Name']}}
									</div>
								@endforeach
							@endif
				  
						</div>
					</div>
				</div>
				<div class="column small-12 medium-9 rightChatColumn">
					<div class="row show-for-small-only back-to-admin-chatters-btn">
						<div class="column small-12">
							< Back
						</div>
					</div>
					<div class="row">
						<div class="column smll-12 messageScrollArea">
							<!-- CHAT MESSAGES GO HERE -->
						</div>
					</div>
					<!-- BEGIN TEXTAREA -->
					<div class='row'>
						<div class='column small-12 chatTxtbox'>
							<div class="row">
								<div class="column small-10">
									{{ Form::textarea ('username' ,'', array('class'=>'chattext') ) }}
								</div>
								<div class="column small-2">
									<div class='button sendbutton' onclick="Plex.chat.sendChat();">Send</div>
								</div>
							</div>
						</div>
					</div>
					<!-- END TEXTAREA -->
				</div>
			</div>
		</div>
	</div><!-- End Chat Area -->

	<!--
		Note!!! The two .chatOfflineWrapper elements  below are tied to chat.js!
		They, as well as the .chatOnlineBox class use the .active class to switch
		their visibility. Check chat.js before big modifications! -AW
	-->
	<!--==================== CHAT ONLINE, !SIGNED IN ====================-->
	<div id='notSignedInMessage' class="row chatOfflineWrapper @if( $chat['isLive'] && !$signed_in ) {{ ' active' }} @endif">
		<div class="column small-12 ">
			<div class="row chatOffLineBox">
				<div class="column small-12 text-center chat_status">
					<span class='chat_online'>
						Chat is online!
					</span>
				</div>
				<div class="column small-12 text-center chat_intent">
				</div>
				<div class="column small-12 text-center chat_action">
						<div class='button'>
							<a class='chat_online' href='/signin'>Sign in to get started</a>
						</div>
				</div>
			</div>
		</div>
	</div>
	<!--==================== END CHAT ONLINE, !SIGNED IN ====================-->

	<!--==================== CHAT OFFLINE ====================-->
	<div id='chatOfflineMessage' class="row chatOfflineWrapper @if( !$chat['isLive'] ) {{ ' active' }} @endif" >
		<div class="column small-12 ">
			<div class="row chatOffLineBox">
				@if( $signed_in && $chat['in_our_network'] )

					<div class="column small-12 text-center chat_status">
						<span class='chat_offline'>
							Chat is offline.
						</span>
					</div>
					<div class="column small-12 text-center chat_intent">
						but we would still like to hear from you :)
					</div>
					<div class="column small-12 text-center chat_action">
							<div class='button'>
								<a href='/portal/messages/{{ $CollegeId }}/college/{{$collegeData->thread_id or ''}}'>Send a message instead</a>
							</div>
					</div>
				@elseif( $signed_in && !$chat['in_our_network'] )
				
					<div class="column small-12 text-center chat_status">
						<div class="row collapse">
							<div class="column small-12 small-centered rep_offline">
								<span class='chat_offline'>
									College representatives are offline.
								</span>
							</div>
							<div class="row">
								<div class="column small-12 small-centered">
									<span class="plex_msg">
									if you have questions, send Plexuss a message and we'll respond soon
									</span>
								</div>
							</div>
							<div class="column small-12 text-center chat_action">
								<div class='button'>
									<a class='chat_offline' href='/portal/messages/7916/college'>Send a message</a>
								</div>
							</div>
						</div>      
					</div>
				
				@else
					<div class="column small-12 text-center chat_status">
						<span class='chat_offline'>
							Chat is offline.
							</span>
					</div>
					<div class="column small-12 text-center chat_intent">
						but we would still like to hear from you :)
					</div>
					<div class="column small-12 text-center chat_action">
						<div class='button'>
							<a class='chat_offline' href='/signin'>Sign in to send a message</a>
						</div>
					</div>
				@endif
			</div>
		</div>
	</div>
	<!--==================== END CHAT OFFLINE ====================-->
</div>
