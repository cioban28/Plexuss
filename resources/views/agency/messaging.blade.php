@extends('agency.master')
@section('content')

	<!-- when daily chat bar is up, add this class: with-daily-chat-bar to messingingWrapper -->
	<div class="row messingingWrapper @if($is_any_school_live == true) with-daily-chat-bar @endif">
		<div class="column small-12">

	    
		    <div class='row portal_header_nav show-for-medium-up'><!-- top your list menu -->
		    	
		    	<!--
		        <ul>
		          	<li class="column small-6 text-left">INBOX | TRASH</li>
		   			<li class="column small-2">WRITE NEW MESSAGE</li>
		   			<li class="column small-2"><div class="actionsIcon"></div>Actions <div class='downArrow'></div></li>
		   			<li class="column small-2"><div class="settingsGear"></div>SETTINGS</li>
		        </ul>
		        -->
	        </div><!-- End top your list menu -->


				
			<div class="row @if ($currentPage == 'agency-chat' ) {{'active'}} @endif chatMainWindow "><!-- Chat Area -->
			<!-- idle overlay -->
			<div id='chat_idle_overlay_wrapper' class='small-12 column idle_overlay_wrapper'>
				<div class='row collapse'>
					<div id='chat_idle_overlay' class='small-12 column idle_overlay'>
						<div class='row'>
							<div id='chat_idle_title' class='small-12 column text-center idle_title'>
								<span>Idle Mode</span>
							</div>
						</div>
						<div class='row'>
							<div id='chat_idle_description' class='small-12 column text-center idle_description'>
								<span>You are now in idle mode. Our servers appreciate the break! Your chat will be updated once you type or move your mouse.</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- end idle overlay -->
			    <div class="column small-12">
			        <div class="row collapse">
			            <div class="column small-3 leftChatColumn" data-hasheduid="{{$hashed_uid}}">
			                <div class="row">
			                    <div class="column small-12 chatLeftHeader agency-chat-header">
			                        My Status
			                        {{ Form::select( 'chat_online_status', array( '0' => 'Offline', '1' => 'Online'), 'Offline', array( 'onchange' => 'Plex.chat.chatOnlineStatus(this);', 'id' => 'chat_online_status' ) ) }}
			                    </div>
			                    <div class="column small-12 chatLeftHeader agency-chat-header">
			                    	Students Online
			                    </div>
			                    <div class='chatUserLists'>
			         				<!-- Users will populate here -->
			                    </div>
			                </div>
			                <!--<div class="switchbuttons-wrapper">
			                	
			                	<div class="switchbuttons mainChatButton"  onclick="Plex.chat.toggleChatAndMessageWindowsViews('agency-chat');">
			                		Main Chat
									<span class='unread_count'></span>
			                	</div>
			                	<div class="switchbuttons privateChatButton" onclick="Plex.chat.toggleChatAndMessageWindowsViews('agency-messages');">
			                		Private Messages
									<span class='unread_count'></span>
			                	</div>
			                </div>-->
			            </div>
			            <div class="column small-9 rightChatColumn">
			                <div class="row">
			                    <div class='messageScrollArea'>
			                        <div class="column small-12">
			     						&nbsp;
			                        </div>
			                    </div>
			                </div>
							<!-- BEGIN TEXTBOX -->
							<div class='row'>
								<div class='column small-12 chatTxtbox'>
									<div class="row">
										<div class="column small-10">
											{{ Form::textarea ('username' ,'', array('class'=>'chattext') )}}
										</div>
										<div class="column small-2">
											<div class='button sendbutton' onclick="Plex.chat.sendChat();">Send</div>
										</div>
									</div>
								</div>
							</div>
							<!-- END TEXTBOX -->
			            </div>
			        </div>
			    </div>
			</div><!-- End Chat Area -->

			<div class="row @if ($currentPage == 'agency-messages' ) {{'active'}} @endif messageMainWindow"><!-- Start messaging section -->
				<?php 
				/*
				data-threadtypeid = the UserID or the CollegeID passing in with the post button. 
				data-threadtype = is going to be 'college' or 'user'. This lets php know what time of id we are passing in during a post.
				data-topic = is the topic thread used for getting new messages.
				data-stickyuserid = is the id of user or school we want the back end to leave stuck on top of the list. Needs this since there wont be a thread for it. we pass -1 as the post id along with this
									stickyuserid.
				*/ ?>
				<!-- idle overlay -->
				<div id='messages_idle_overlay_wrapper' class='small-12 column idle_overlay_wrapper'>
					<div class='row'>
						<div id='messages_idle_overlay' class='small-12 column idle_overlay'>
							<div class='row'>
								<div id='messages_idle_title' class='small-12 column text-center idle_title'>
									<span>Idle Mode</span>
								</div>
							</div>
							<div class='row'>
								<div id='messages_idle_description' class='small-12 column text-center idle_description'>
									<span>You are now in idle mode. Our servers appreciate the break! Your messages will be updated once you type or move your mouse.</span>
								</div>
							</div>
						</div>
					</div>
				</div>
				<!-- end idle overlay -->
				<!-- left side -->
				<div class="column small-12 medium-3  leftMessageColumn" data-stickyuserid="{{$stickyUsr}}">
					<div class='row'>
						<div class="small-12 column usersArea">



							@if ( isset($topicUsr) && count($topicUsr) > 0  ) {{-- Topic Array --}}

								@foreach ($topicUsr as $topicList) {{-- Loop the topics --}}

									@if (isset($topicList['thread_members']) && count($topicList['thread_members']) <= 1)
										{{-- For each topic print out each members- should only be one in this loop. --}}
										{{-- For right now there will ONLY be one to one views. Group chat will get revisted later. AO --}}
										<div class="row messageContacts"  data-topicid='{{ $topicList['thread_id'] }}' data-threadtypeid='{{ $topicList['thread_type_id'] }}' data-threadtype='{{ $topicList['thread_type'] }}' onclick="Plex.messages.changeTopic(this);">
											
											<div class="column small-2">
												<img src="{{$topicList['img']}}" alt="{{ $topicList['thread_name'] }}" title="{{ $topicList['thread_name'] }}">
											</div>

											<div class="column small-9">
												<div class="row">
													<div class="column small-10 text-left messageName">{{ $topicList['Name'] or ''}}</div>

													<div class="column small-12 text-left messageSample">{{ $topicList['msg'] or ''}}</div>
												</div>
											</div>

											<div class="column small-1">
												<div class="column small-2 messageDate text-center inline">
													@if ( isset($topicList['num_unread_msg']) )
														@if ( $topicList['num_unread_msg'] == 0 )
															&nbsp;
														@else
															{{ $topicList['num_unread_msg'] }}
														@endif
													@endif
												</div>
											</div>
										</div>
										{{-- END - for each topic print out each members --}}
									@else
										{{-- For GROUP topic print out each members- should be more than one. --}}
										<div class="row messageContacts"  data-topicid='{{ $topicList['thread_id'] }}' data-threadtypeid='{{ $topicList['thread_type_id'] }}' data-threadtype='{{ $topicList['thread_type'] }}'  onclick="Plex.messages.changeTopic(this);">
											<div class="column small-12">
												<div class="row">
			<?php
				$i = 0;
				$totalUsers = count($topicList['thread_members']);
			?>
	@foreach ($topicList['thread_members'] as $topic_user)
		<?php
			$i++;
			$isEnd = ( $i == $totalUsers) ? 'end' :'' ;	
		?>
		<div class="column small-2 {{$isEnd}}">
			<img src="{{$topic_user['img']}}" alt="{{ $topic_user['Name'] }}" title="{{ $topic_user['Name'] }}">
		</div>
	@endforeach
	</div>
	</div>
	<div class="column small-12">
	<div class="row">
	<!-- <div class="column small-10 text-left messageName">{{ $topicList['Name'] or ''}}</div> -->

	<div class="column small-10 text-left messageSample">{{ $topicList['msg'] or ''}}</div>
	<div class="column small-2 messageDate text-center inline">{{ $topicList['num_unread_msg'] or ''}}</div>
	</div>
	</div>
	</div>


				{{-- END - for each topic print out each members --}}
				@endif


				@endforeach {{-- END - Loop the topics --}}
				@endif {{-- END Topic Array --}}



				</div>
				</div>
					<!--<div class="switchbuttons-wrapper">
						
	                	<div class="switchbuttons mainChatButton" onclick="Plex.chat.toggleChatAndMessageWindowsViews('agency-chat');">
	                		Main Chat
							<span class='unread_count'></span>
	                	</div>
	                	<div class="switchbuttons privateChatButton" onclick="Plex.chat.toggleChatAndMessageWindowsViews('agency-messages');">
	                		Private Messages
							<span class='unread_count'></span>
	                	</div>
                	</div>-->
				</div>
				<!-- right side-->
				<div class="column small-12 medium-9 rightMessageColumn">
					<div class='row collapse'>
						<div class='msgScrollBox'></div>
					</div>
					<!-- BEGIN TEXTBOX -->
					<div class='row collapse'>
						<div class="column small-12 messageTxtbox">
							<div class="row">
								<div class="column small-10">
									{{ Form::textarea ('username' ,'', array('class'=>'msgtext') )}}
								</div>
								<div class="column small-2">
									<div class='button sendbutton' onclick="Plex.messages.sendMessage();">Send</div>
								</div>
							</div>
						</div>
					</div>
					<!-- END TEXTBOX -->
				</div>
			</div><!-- END messaging section -->
		</div>

	</div>

	<div class="messagingBottomMenu show-for-small-only">
		<div class="row">
			<div class="column small-6 text-center">inbox</div>
			<div class="column small-6 text-center">trash</div>
		</div>
	</div>
@stop
