@extends('sales.master')

@section('content')
<?php 
	// dd($active_thread_msg);
	$no_chat_history = true;
?>
	<!-- \\\\\ top row filter container - start /////-->
	<div class="row sales-messages-filter-container">
		<div class="column small-12">
			
			<!-- new 'filter' row, just displaying school info now -->
			<div class="row">
				<div class="small-12 msg-header-row">
						<span>{{$msg_owner_school_name or 'No school'}} <span class="show-for-large-up">|</span> </span> <br class="hide-for-large-up"><span>International Dept. <span class="show-for-large-up">|</span></span> <br class="hide-for-large-up"><span>{{$msg_owner_name or 'NO NAME'}}</span>
				</div>
			</div>
			
		</div>
	</div>
	<!-- \\\\\ top row filter container - end /////-->



	<!-- \\\\\ main content message container - start /////-->
	<div class="row collapse sales-messages-content-container">
		<div class="column small-12">
			
			<!-- left side convo column -->
			<div class="row collapse left-side-convo">
				<div class="column small-12">
					
					<!-- choose convo title -->
					<div class="row choose-convo-title">
						<div class="column small-12 text-center">
							Conversations ({{$threads_cnt or 0}})
						</div>
					</div>

					@if( isset($thread_list) )

						@foreach( $thread_list as $thread )
							@if( $thread['is_chat'] == 1 )
							<div class="row chat-thread convo-thread @if($thread['active_thread'] ==true) active-thread @endif" data-thread-id="{{$thread['thread_id']}}">
								<div class="column small-12">
									General Chat
								</div>
							</div>

							<?php 
								$no_chat_history = false;
								break;
							?>
							@endif
						@endforeach

						@if( $no_chat_history )
						<div class="row no-chat-history">
							<div class="column small-12">
								No Chat History
							</div>
						</div>
						@endif

						<!-- list of threads the college has -->
						<div class="list-of-threads-container">
						@foreach( $thread_list as $threads )
							@if( $threads['is_chat'] != 1 )
							<div class="row list-of-threads convo-thread @if($threads['active_thread']) active-thread @endif @if($threads['idle'] == 'true') is-idle @endif" data-thread-id="{{$threads['thread_id']}}">
								<div class="column small-12">
									{{$threads['name']}}
								</div>

								<div class="redirect-to-reply-button text-center">
									<a href="{{$threads['msg_link']}}" target="_blank">&#8634;</a>
								</div>
							</div>
							@endif
						@endforeach
						</div>

					@endif

				</div>
			</div>

			<!-- right side message view and notes column -->
			<div class="row collapse right-side-msgView">
				<div class="column small-12">
					
					<div class="row collapse right-side-inner-msgview">
						<div class="column small-12">
							
							<!-- messages view section -->
							<div class="msg-view-section">

								<!-- date -->
								<?php 
									$current_date = '';
									if( isset($active_thread_msg) ){
										$active_thread_msg = json_decode($active_thread_msg->data);
									}
								?>
								@if(isset($active_thread_msg))
									@foreach($active_thread_msg as $msg)
										<?php 	
											$time = date('h:i',strtotime($msg->date));
											$tmp_date = date('l jS \of F Y',strtotime($msg->date));
										?>

										@if($current_date == '')
											<div class="msg-divider">
												<hr />	
											</div>

											<div class="row msg-date">
												<div class="column small-12">
													{{$tmp_date or ''}}
												</div>
											</div>
											<?php 
												$current_date = $tmp_date;
											?>
										@elseif($current_date != '' && $current_date != $tmp_date)
											<div class="msg-divider">
												<hr />	
											</div>

											<div class="row msg-date">
												<div class="column small-12">
													{{$tmp_date or ''}}
												</div>
											</div>
											<?php 
												$current_date = $tmp_date;
											?>

										@endif

										<div class="row msg-details-container @if($msg->is_org == 1) is-college @endif">
											<!-- time -->
											<div class="column small-12 medium-2 large-1 msg-time-sent">
												{{ $time or ''}}
											</div>

											<!-- name -->
											<div class="column small-12 medium-10 large-3 msg-name">
												{{$msg->full_name or ''}}
											</div>

											<!-- message -->
											<div class="column small-12 large-8 msg-content">
												{{$msg->msg or ''}}
											</div>
										</div>
									@endforeach
								@endif

							</div>

							<!-- ajax loader -->
		                    <div class="text-center sales-msg-ajax-loader">
	                            <svg width="70" height="20">
	                                <rect width="20" height="20" x="0" y="0" rx="3" ry="3">
	                                    <animate attributeName="width" values="0;20;20;20;0" dur="1000ms" repeatCount="indefinite"/>
	                                    <animate attributeName="height" values="0;20;20;20;0" dur="1000ms" repeatCount="indefinite"/>
	                                    <animate attributeName="x" values="10;0;0;0;10" dur="1000ms" repeatCount="indefinite"/>
	                                    <animate attributeName="y" values="10;0;0;0;10" dur="1000ms" repeatCount="indefinite"/>
	                                </rect>
	                                <rect width="20" height="20" x="25" y="0" rx="3" ry="3">
	                                    <animate attributeName="width" values="0;20;20;20;0" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
	                                    <animate attributeName="height" values="0;20;20;20;0" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
	                                    <animate attributeName="x" values="35;25;25;25;35" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
	                                    <animate attributeName="y" values="10;0;0;0;10" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
	                                </rect>
	                                <rect width="20" height="20" x="50" y="0" rx="3" ry="3">
	                                    <animate attributeName="width" values="0;20;20;20;0" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
	                                    <animate attributeName="height" values="0;20;20;20;0" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
	                                    <animate attributeName="x" values="60;50;50;50;60" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
	                                    <animate attributeName="y" values="10;0;0;0;10" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
	                                </rect>
	                            </svg>
		                    </div>
		                    <!-- end of ajax loader -->

							<!-- notes pane section -->
							<div class="notes-pane-section">
								<div class="note-taking-pane">
									<?php 
										if( isset($active_thread_msg) ){
											$notepad = json_decode($active_thread_msg->note_arr);
										}
									?>
									<textarea class="sales-messages-notes" name="sales-message-notes" placeholder="Write your notes here:" rows="18" data-thread-id-note="{{$thread_list[0]['thread_id'] or ''}}">{{$notepad->note or ''}}</textarea>
								</div>

								<div class="last-saved-note-section">
									Last saved: <span class="last-saved-updated-time">{{$notepad->note_date or '--'}}</span>
								</div>

								<!-- ajax loader -->
			                    <div class="text-center auto-save-ajax-loader">
		                            <svg width="70" height="20">
		                                <rect width="20" height="20" x="0" y="0" rx="3" ry="3">
		                                    <animate attributeName="width" values="0;20;20;20;0" dur="1000ms" repeatCount="indefinite"/>
		                                    <animate attributeName="height" values="0;20;20;20;0" dur="1000ms" repeatCount="indefinite"/>
		                                    <animate attributeName="x" values="10;0;0;0;10" dur="1000ms" repeatCount="indefinite"/>
		                                    <animate attributeName="y" values="10;0;0;0;10" dur="1000ms" repeatCount="indefinite"/>
		                                </rect>
		                                <rect width="20" height="20" x="25" y="0" rx="3" ry="3">
		                                    <animate attributeName="width" values="0;20;20;20;0" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
		                                    <animate attributeName="height" values="0;20;20;20;0" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
		                                    <animate attributeName="x" values="35;25;25;25;35" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
		                                    <animate attributeName="y" values="10;0;0;0;10" begin="200ms" dur="1000ms" repeatCount="indefinite"/>
		                                </rect>
		                                <rect width="20" height="20" x="50" y="0" rx="3" ry="3">
		                                    <animate attributeName="width" values="0;20;20;20;0" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
		                                    <animate attributeName="height" values="0;20;20;20;0" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
		                                    <animate attributeName="x" values="60;50;50;50;60" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
		                                    <animate attributeName="y" values="10;0;0;0;10" begin="400ms" dur="1000ms" repeatCount="indefinite"/>
		                                </rect>
		                            </svg>
			                    </div>
			                    <!-- end of ajax loader -->
							</div>

						</div>
					</div>

				</div>
			</div>

		</div>
	</div>
	<!-- \\\\\ main content message container - end /////-->

@stop