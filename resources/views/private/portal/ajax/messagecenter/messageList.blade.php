<div class="row collapse msging">
	<div class="column small-12">

	    <div class="row pos-rel">
	        <div class="show-for-small-only"><!-- header menu in mobile view -->
	            <div class="row pt15">
	                <div class="small-12 column fs20 header-portal-messages chat-msg-title-back">MESSAGES</div>
	            </div>
	        </div><!-- End header menu in mobile view -->

	    </div>

	    <!-- show this if user has no messages -->
    	<div id="_noMsgsLayer" class="no-msgs-layer">
    		<div class="no-msgs-msg text-center">
    			You have not initiated any conversations with colleges. To do so, view some college pages and click Get Recruited or Send Message.
    			<br />
    			<div>
        			<a href="/college" class="view-coll-btn">
        				<div>View Colleges</div>
        			</a>
        		</div>
    		</div>
    	</div>

		<div class="row collapse messageMainWindow"><!-- Start messaging section -->
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
								<span>You are now in idle mode. Our servers appreciate the break! Your messages will updated once you type or move your mouse.</span>
							</div>
						</div>
					</div>
				</div>
			</div>
			<!-- end idle overlay -->

			<!-- left side -->
			<div class="column small-12 medium-4 large-3 leftMessageColumn is-msg-col" data-stickyuserid="{{isset($stickyUsr) ? $stickyUsr : ''}}">
				<div id="usersThreadlist" class='usersArea stylish-scrollbar'>
					@if ( isset($topicUsr) && count($topicUsr) > 0  ) <!-- Topic Array -->
						@foreach ($topicUsr as $topicList) <!-- Loop the topics -->
							@if ( count($topicList['thread_members']) <= 1)
								<!-- For each topic print out each members- should only be one in this loop. -->
								<!-- For right now there will ONLY be one to one views. Group chat will get revisted later. AO -->
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
											@if ($topicList['num_unread_msg'] == 0)
												&nbsp;
											@else
												{{ $topicList['num_unread_msg']}}
											@endif
										</div>
									</div>

								</div>
								<!-- END - for each topic print out each members -->
							@else
								<!-- For GROUP topic print out each members- should be more than one. -->
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
								<!-- END - for each topic print out each members -->
							@endif
						@endforeach <!-- END - Loop the topics -->
					@endif <!-- END Topic Array -->
				</div>
			</div>

			<!-- center -->
			<div class="column small-12 medium-8 large-9 rightMessageColumn is-msg-col">
				<!-- Message display area -->
				<div id="_convoContainer" class='msgScrollBox stylish-scrollbar'></div>
				<!-- End message display area -->
				<!-- Message input area -->
				<div class='row msg-text-response-box' style="position: relative;">
					<div id="ellipsis">
						<div class="ellipse">
							<div class="ellips-dot"></div>
							<div class="ellips-dot"></div>
							<div class="ellips-dot"></div>
						</div>
						<span id="ellipsis_name"></span>
					</div>
					<div class="column small-12 messageTxtbox">
						<div class="row">
							<div class="column small-8 medium-10">

								<div id="_msgSendInput" contenteditable="true" name="username" class="msgtext" ></div>
							</div>
							<div class="column small-4 medium-2">
								<div id="_msgSendBtn" class='button sendbutton'>Send</div>
							</div>
						</div>
					</div>

					<div class="column small-12 ">
						<div class="attch-file-open">
							<div class="attatch-icon"></div> <span class="att-files-txt">File Attachments</span>
						</div>
					</div>

				</div>
				<!-- End Message input area -->
			</div>

			<!-- right side rep info -->
			<div id="rep_youre_messaging" class="column small-12 large-3 is-msg-col show-for-large-up hardhide stylish-scrollbar">
				<!-- react component here -->
			</div>

		</div>
		</div><!-- END messaging section -->




</div>

<!-- get react component -->
<script src="/js/prod_ready/portal/Rep_Messaging_Component.min.js" defer></script>

<!-- footer menu in mobile view -->
<!-- footer menu in mobile view -->

@if( isset($stickyUsr) )
	<script type="text/javascript">
		/* if we're creating a new placeholder thread ( id == -1 ), we need
		 * to enable sticky until a message is sent. We also need to save
		 * the recipient type ( user/college ) and the thread type ( chat/inquiry )
		 */
		// console.log('Loaded Sticky!!');
		// Plex.messages.sticky_recipient_id = {{ $stickyUsr }};
		// Plex.messages.stickyEnabled = true;
		// Plex.messages.sticky_thread_type = "{{ $sticky_thread_type }}";

		// console.log(Plex.messages);
	</script>
@endif
	<script type='text/javascript'>
		// STICKY RECIPIENT TYPE ON PORTAL MESSAGES WILL ALWAYS BE COLLEGE
		// Plex.messages.sticky_recipient_type = 'college';
		// Plex.messages.getMessagesUrl = '/portal/ajax/messages/getNewMsgs/';
		// Plex.messages.getUserNewTopicsUrl = '/portal/ajax/messages/getUserNewTopics';
	 // 	//Plex.messages.sendMessageUrl = '/portal/ajax/messages/postMsg/';
	 // 	Plex.messages.topicReadUrl = '/portal/ajax/messages/setMsgRead/';
	</script>
