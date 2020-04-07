@extends('groupMessaging.master')

@section('content')

	@if( (isset($webinar_is_live) && $webinar_is_live == true) || (isset($is_any_school_live) && $is_any_school_live == true) )
	<div class="off-canvas-wrap group-msg-container autoscroll chat-now-bar-isVisible" data-offcanvas>
	@else
	<div class="off-canvas-wrap group-msg-container autoscroll" data-offcanvas>
	@endif
		<div class="inner-wrap">


			<!-- Off Canvas Menu 
			<aside class="left-off-canvas-menu">
			    <div class="row student-list-container">
			    	<div class="column small-12">
			    		<div class="rec-summary">
			    			<span class="rec-count">
			    				{{  isset($recipients) && count($recipients) > 0 ? count($recipients) : 0 }}
			    			</span> 
			    			<span class="rec-title">Students in this list</span>
			    			<span class="left-off-canvas-toggle">Hide</span> 
			    		</div><br/>

						<div class="search-students-form hide">
					    	<div class="row">
								<div class="large-12 column">
									<div class="row collapse">
										<div class="small-10 columns">
											<input type="text" placeholder="Add Students" class="search-input">
										</div>
										<div class="small-2 columns">
											<a href="#" class="search-btn button postfix"> + </a>
										</div>
									</div>
								</div>
							</div>
							<div class="results">
								<ul>
									<!-- search results injected here 
								</ul>
							</div>
					    </div>

					    <div class="add-from-prev-camps">
					    	<select>
					    		<option value="">
					    			select from campaigns: 
					    		</option>
					    		@if(isset($campaigns['all_approved']) && !empty($campaigns['all_approved']))
					    		<option value="{{$campaigns['all_approved']['campaign_id']}}">
					    			{{ $campaigns['all_approved']['name'].' ('.$campaigns['all_approved']['recipients'].')' }}
					    		</option>
					    		@endif
					    		@if(isset($campaigns['all_pending']) && !empty($campaigns['all_pending']))
					    		<option value="{{$campaigns['all_pending']['campaign_id']}}">
					    			{{ $campaigns['all_pending']['name'].' ('.$campaigns['all_pending']['recipients'].')' }}
					    		</option>
					    		@endif
					    		@if(isset($campaigns['previous']) && !empty($campaigns['previous']))
						    		@foreach($campaigns['previous'] as $previous)
						    		<option value="{{$previous['campaign_id']}}">
						    			{{ $previous['name'].' ('.$previous['recipients'].')' }}
						    		</option>
						    		@endforeach
						    	@endif
						    	@if(isset($campaigns['scheduled']) && !empty($campaigns['scheduled']))
						    		@foreach($campaigns['scheduled'] as $scheduled)
						    		<option value="{{$scheduled['campaign_id']}}">
						    			{{ $scheduled['name'].' ('.$scheduled['recipients'].')' }}
						    		</option>
						    		@endforeach
						    	@endif
						    	@if(isset($campaigns['draft']) && !empty($campaigns['draft']))
						    		@foreach($campaigns['draft'] as $draft)
						    		<option value="{{$draft['campaign_id']}}">
						    			{{ $draft['name'].' ('.$draft['recipients'].')' }}
						    		</option>
						    		@endforeach
						    	@endif

					    	</select>

				    		<div class="excludes-stu-messaged">
				    			{{ Form::checkbox('excludes-check', 'excludes students messaged', null, array('id' => 'excludes-check')) }}
								{{ Form::label('excludes-check', 'Exclude Students I\'ve already messaged', array('class'=>'check-title')) }}	  			
				    		</div>

					    	<!-- add label here 
					    	<div class="inject-label-from-prev-camps">
					    		<ul>
					    		</ul>
					    	</div>

					    	<ul class="recipient-ul">
                            @if( isset($recipients) && count($recipients) > 0 )
                                @foreach( $recipients as $rec )
                                    <li class="student clearfix" data-info='{{$rec['json']}}'>
                                        <div class="left r-name">{{$rec['fname'].' '.$rec['lname']}}</div>
                                        <div class="left r-profile"> i </div>
                                        <div class="left remove-std-btn"> x </div>
                                    </li>   
                                @endforeach   
                            @endif
                        	</ul>

					    </div>
			    	</div>
			    </div>

			    
			</aside> -->

			<!-- main content goes here -->
			<div class="main-content-container" data-selectedcnt="{{$student_count}}" data-currentpage="{{$currentPage}}">
				@if($currentPage == 'admin-textmsg' && isset($purchased_phone) && $textmsg_tier == 'flat_fee' && $current_time->gt($textmsg_expires_date) || $currentPage == 'admin-textmsg' && !isset($purchased_phone))
					
					@include('groupMessaging.searchTollFreePhoneNumber')
					
				@elseif($currentPage == 'admin-groupmsg' || $currentPage == 'admin-textmsg')
				<div class="row">
					<div class="column small-12 medium-6 large-5">
						<div class="campaign-start">
							@if( empty($campaigns['previous']) && empty($campaigns['scheduled']) && empty($campaigns['draft'])  )
								<div>You have 0 campaigns. Use the button below to get started.</div>
							@endif

							<div>
								@if($currentPage == 'agency-groupmsg' || $currentPage == 'admin-groupmsg')
								<div class="create-campaign-btn button">Create new campaign</div>
								@elseif($currentPage == 'admin-textmsg')
								<div class="create-campaign-btn button">Create new text campaign</div>
								@endif
							</div>

							@if( !empty($campaigns['previous']) || !empty($campaigns['scheduled']) || !empty($campaigns['draft']) )
								<p class="small-only-text-center">OR</p>

								<div class="clearfix t-tabs">
									<div class="left t-sched active" data-tab="scheduled-campaigns-container">Scheduled</div>
									<div class="left">&nbsp;|&nbsp;</div>
									<div class="left t-prev" data-tab="previous-campaign-container">Previous</div> 
									<div class="left">&nbsp;|&nbsp;</div>
									<div class="left t-draft" data-tab="draft-campaigns-container">Draft</div>
								</div>

								<div class="previous-campaign-container c-list">
									<!-- show 5 then show more button -->
									<ul>
										@if( isset($campaigns['previous']) && count($campaigns['previous']) > 0 )
											@foreach( $campaigns['previous'] as $previous )
												<li>
													{{Form::radio('previous_campaigns', $previous['campaign_id'], false, array('id'=>$previous['campaign_id']))}}
													{{Form::label($previous['campaign_id'], $previous['name'].' ('.$previous['recipients'].' recipients)', array('class'=>'camp-label previ'))}}
													@if(isset($previous['last_sent_on']) && $previous['last_sent_on'] == "Pending to send...")
														<div class="time-display">{{$previous['last_sent_on'] or ''}}</div>
													@else
														<div class="time-display">Last sent on: {{$previous['last_sent_on'] or ''}}</div>
													@endif

													
												</li>
											@endforeach
										@else
											<li><small>No campaigns in this list yet.</small></li>
										@endif
									</ul>
									<div class="btn-options-container clearfix">
										<div class="left c-view">View</div>
										<div class="left c-delete">Delete</div>
									</div>
								</div>

								<div class="scheduled-campaigns-container c-list">
									<!-- show 4 then show more button -->
									<ul>
										@if( isset($campaigns['scheduled']) && count($campaigns['scheduled']) > 0 )
											@foreach( $campaigns['scheduled'] as $scheduled )
												<li>
													{{Form::radio('previous_campaigns', $scheduled['campaign_id'], false, array('id'=>$scheduled['campaign_id']))}}
													{{Form::label($scheduled['campaign_id'], $scheduled['name'].' ('.$scheduled['recipients'].' recipients)', array('class'=>'camp-label sched'))}}
													<div class="time-display clearfix">
														<div class="css-clock left">
															<div></div>
															<div></div>
														</div>
														<div class="left sch-time">Scheduled for {{$scheduled['scheduled_at']}}</div>
													</div>
												</li>
											@endforeach
										@else
											<li><small>No campaigns in this list yet.</small></li>
										@endif
									</ul>
									@if( isset($campaigns['scheduled']) && count($campaigns['scheduled']) > 0 )
										<div class="btn-options-container clearfix">
											<div class="left c-modify">Modify</div>
											<div class="left c-delete">Cancel</div>
										</div>
									@endif
								</div>

								<div class="draft-campaigns-container c-list">
									<ul>
										@if( isset($campaigns['draft']) && count($campaigns['draft']) > 0 )
											@foreach( $campaigns['draft'] as $draft )
												<li>
													{{Form::radio('previous_campaigns', $draft['campaign_id'], false, array('id'=>$draft['campaign_id']))}}
													{{Form::label($draft['campaign_id'], $draft['name'].' ('.$draft['recipients'].' recipients)', array('class'=>'camp-label prev'))}}
													@if(isset($draft['last_sent_on']))
														<div class="time-display">Last sent on {{$draft['last_sent_on'] or ''}}</div>
													@endif
												</li>
											@endforeach
										@else
											<li><small>No campaigns in this list yet.</small></li>
										@endif
									</ul>
									<div class="btn-options-container clearfix">
										<div class="left c-view">View</div>
										<div class="left c-delete">Delete</div>
									</div>
								</div>

								<div class="searched-campaigns c-list">
									<ul>
										<!-- searches will be injected here -->
									</ul>
									<div class="btn-options-container clearfix">
										<div class="left c-view">View</div>
										<div class="left c-delete">Delete</div>
									</div>
								</div>
							@endif			
							
							<div class="row text-center">
								<a href="#" class="button secondary show-more">show more >></a>
							</div>

						</div>

						{{Form::open(array('class'=>'campaign-form', 'files' => true))}}
						{{Form::hidden('current_campaign_id', null, array('id' => 'current-campaign-id'))}}
						<div class="campaign-choose">
							<div class="back-btn">< Back to Campaigns</div>
							<!-- Step 1 -->
							<div class="step1-title">
								Step 1 - Choose Audience
							</div>

							<div class="campaign-list">
								@if($currentPage == 'admin-textmsg')
								<div class="row">
									<div class="columns small-5">
										{{Form::file('uploadFile', array('id' => 'upload-csv', 'accept' => '.csv, application/vnd.ms-excel, application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'))}}
										<label for="upload-csv" class="button upload-csv-btn">Upload List
											<span data-tooltip aria-haspopup="true" class="has-tip selected-students-icon" title="Acceptable formats are .csv, .xls">?
											</span>
										</label>
									</div>
									<div class="column small-7">
										<div class="row">
											<div class="columns small-12">
												<a href="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/upload.xlsx">Sample Upload</a>
											</div>
											<div class="columns small-12">
												<a href="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/list+of+countries.xlsx">List of Countries</a>
											</div>
										</div>
										
									</div>
								</div>
								@endif
								<ul>
									<!-- set the condition for selecting from manage students -->
									<li>
										{{Form::checkbox('campaigns_selected', 'selected_from_manage_students', null, array('id'=>'campaign_shown_manage_stu', 'data-recipients' => $student_count))}}
										{{Form::label('campaign_shown_manage_stu', 'Selected Students ('.$student_count.' recipients)' )}}
										<?php $students_ids = array(); ?>
										@foreach($student_list as $student_list)
											<?php array_push($students_ids, $student_list['id']);?>
										@endforeach
										{{Form::hidden('selected_students_id', implode(",",$students_ids) )}}
									</li>

									@if(isset($campaigns['all_approved']) && !empty($campaigns['all_approved']))
									<li >
										{{Form::checkbox('campaign_shown_'.$campaigns['all_approved']['name'], $campaigns['all_approved']['campaign_id'], null, array('id' => 'campaign_shown_'.$campaigns['all_approved']['campaign_id'] , 'data-recipients'=>$campaigns['all_approved']['recipients']))}}
										{{Form::label('campaign_shown_'.$campaigns['all_approved']['campaign_id'], $campaigns['all_approved']['name'].' ('.$campaigns['all_approved']['recipients'].' recipients)')}}
										<div class="excludes-stu-messaged hide">
                                			{{ Form::checkbox('excludes_check_'.$campaigns['all_approved']['name'], $campaigns['all_approved']['campaign_id'], null, array('id' => 'excludes_check_'.$campaigns['all_approved']['campaign_id'])) }}
                                			{{ Form::label('excludes_check_'.$campaigns['all_approved']['campaign_id'] , 'Exclude Students I\'ve already messaged', array('class'=>'check-title')) }}
                                			<span data-tooltip aria-haspopup="true" class="has-tip show-for-medium-up" title=" This will exclude students you have previously messaged."><img src="/images/setting/tooltip_icon.png"/></span>  
                                		</div>
									</li>
									@endif

									@if(isset($campaigns['all_pending']) && !empty($campaigns['all_pending']))
									<li>
										{{Form::checkbox('campaign_shown_'.$campaigns['all_pending']['name'] , $campaigns['all_pending']['campaign_id'], null, array('id' => 'campaign_shown_'.$campaigns['all_pending']['campaign_id'], 'data-recipients' => $campaigns['all_pending']['recipients']))}}
										{{Form::label('campaign_shown_'.$campaigns['all_pending']['campaign_id'], $campaigns['all_pending']['name'].' ('.$campaigns['all_pending']['recipients'].' recipients)')}}
										<div class="excludes-stu-messaged hide">
                                			{{ Form::checkbox('excludes_check_'.$campaigns['all_pending']['name'] , $campaigns['all_pending']['campaign_id'], null, array('id' => 'excludes_check_'.$campaigns['all_pending']['campaign_id'])) }}
                                			{{ Form::label('excludes_check_'.$campaigns['all_pending']['campaign_id'] , 'Exclude Students I\'ve already messaged', array('class'=>'check-title')) }}
                                			<span data-tooltip aria-haspopup="true" class="has-tip show-for-medium-up" title=" This will exclude students you have previously messaged."><img src="/images/setting/tooltip_icon.png"/></span>  
                                		</div>
									</li>
									@endif

									@if(isset($campaigns['scheduled']) && !empty($campaigns['scheduled']))
										@foreach($campaigns['scheduled'] as $scheduled)
										<li>
											{{Form::checkbox('campaign_shown_'.$scheduled['name'], $scheduled['campaign_id'], null, array('id' => 'campaign_shown_'.$scheduled['campaign_id'], 'data-recipients' => $scheduled['recipients']))}}
											{{Form::label('campaign_shown_'.$scheduled['campaign_id'], $scheduled['name'].' ('.$scheduled['recipients'].' recipients)')}}
											<div class="excludes-stu-messaged hide">
	                                			{{ Form::checkbox('excludes_check_'.$scheduled['name'], $scheduled['campaign_id'], null, array('id' => 'excludes_check_'.$scheduled['campaign_id'])) }}
	                                			{{ Form::label('excludes_check_'.$scheduled['campaign_id'] , 'Exclude Students I\'ve already messaged', array('class'=>'check-title')) }}
	                                			<span data-tooltip aria-haspopup="true" class="has-tip show-for-medium-up" title=" This will exclude students you have previously messaged."><img src="/images/setting/tooltip_icon.png"/></span>  
                                			</div>
										</li>
										@endforeach
									@endif

									@if(isset($campaigns['previous']) && !empty($campaigns['previous']))
										@foreach($campaigns['previous'] as $previous)
										<li>
											{{Form::checkbox('campaign_shown_'.$previous['name'], $previous['campaign_id'], null, array('id' => 'campaign_shown_'.$previous['campaign_id'], 'data-recipients' => $previous['recipients']))}}
											{{Form::label('campaign_shown_'.$previous['campaign_id'], $previous['name'].' ('.$previous['recipients'].' recipients)')}}
											<div class="excludes-stu-messaged hide">
	                                			{{ Form::checkbox('excludes_check_'.$previous['name'], $previous['campaign_id'], null, array('id' => 'excludes_check_'.$previous['campaign_id'])) }}
	                                			{{ Form::label('excludes_check_'.$previous['campaign_id'] , 'Exclude Students I\'ve already messaged', array('class'=>'check-title')) }}
	                                			<span data-tooltip aria-haspopup="true" class="has-tip show-for-medium-up" title=" This will exclude students you have previously messaged."><img src="/images/setting/tooltip_icon.png"/></span>  
                                			</div>
										</li>
										@endforeach
									@endif

									@if(isset($campaigns['draft']) && !empty($campaigns['draft']))
										@foreach($campaigns['draft'] as $draft)
										<li>
											{{Form::checkbox('campaign_shown_'.$draft['name'] , $draft['campaign_id'], null, array('id' => 'campaign_shown_'.$draft['campaign_id'], 'data-recipients' => $draft['recipients']))}}
											{{Form::label('campaign_shown_'.$draft['campaign_id'], $draft['name'].' ('.$draft['recipients'].' recipients)')}}
											<div class="excludes-stu-messaged hide">
	                                			{{ Form::checkbox('excludes_check_'.$draft['name'], $draft['campaign_id'], null, array('id' => 'excludes_check_'.$draft['campaign_id'])) }}
	                                			{{ Form::label('excludes_check_'.$draft['campaign_id'] , 'Exclude Students I\'ve already messaged', array('class'=>'check-title')) }}
	                                			<span data-tooltip aria-haspopup="true" class="has-tip show-for-medium-up" title=" This will exclude students you have previously messaged."><img src="/images/setting/tooltip_icon.png"/></span>  
                                			</div>
										</li>
										@endforeach
									@endif									
								</ul>
							</div>

							<div class="step1-forward">
								<a href="#" class="create-campaign-btn button">
									Next
								</a>
							</div>
						</div>

						<div class="campaign-edit">
							<div class="back-btn">< Back to Audience</div>
							<!-- Step 2-->
							<div class="step2-title">
								Step 2 - Setup Campaign
							</div>

							<div class="row step2-count">
								<span class="inject-selected-students"> </span> students will receive this message
								<span data-tooltip aria-haspopup="true" class="has-tip" data-width="300" title="Note for sending Text Messages <br><br> Depending if you are sending a message through Plexuss, or you are sending a Text Message this list will vary as not all students have put in their phone number."><img src="/images/setting/tooltip_icon.png"/></span>
							</div> 

							<div class="row">
								<div class="column small-12 medium-6">
									{{Form::label('name', 'Campaign')}}
									{{Form::text('name', null, array('class'=>'c-name text-field', 'placeholder'=>'Enter new campaign name'))}}
								</div>
							</div>

							<br class="show-for-small-only" />

							<div class="row">
								<div class="column small-12 medium-6">
									{{Form::label('subject', 'Subject line')}}
									{{Form::text('subject', null, array('class'=>'c-subj text-field', 'placeholder'=>'Enter new campaign name'))}}	
								</div>
							</div>

							<div class="row std-list-font hide">
								@if($student_count == 0)
								<div class="column medium-4">
									<a class="edit-std-list-btn left-off-canvas-toggle add hide" href="#">Choose Audience</a>
								</div>
								<div class="column medium-2">
									<a class="button hide" href="#">Upload List</a>
								</div>
								<div class="column medium-4">
									<span class="rec-count">0</span> students in this list 
								</div>
									
								@else
								<div class="column medium-4">
									<a class="edit-std-list-btn left-off-canvas-toggle edit hide" href="#">Edit List</a>
								</div>
								<div class="column medium-2">
									<a class="button hide" href="#">Upload List</a>
								</div>
								<div class="column medium-4">
									<span class="rec-count"></span> students in this list 
								</div>
								
								@endif
								<div class="column medium-2"></div>
							</div>

							<div class="clearfix content-options-row">
								<div class="left">
									{{Form::file('files2', array('id'=>'insert_pic_attachments'))}}
									<label for="insert_pic_attachments" class="insert_pic_attachments"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/insert_pic.png" alt="Insert Pic"></label>
								</div>
								@if(isset($currentPage) && $currentPage != 'admin-textmsg')
								<div class="left">
									{{Form::file('files', array('id'=>'attachments'))}}
									<label for="attachments" class="attachments"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/paperclip.jpg" alt="paperclip attach"></label>
								</div>
								@endif
								<div class="left">
									{{Form::select('message_template_dropdown', $message_template, null , array( 'id' => 'message_template_dropdown'))}}
								</div>
								<div class="left">
									{{Form::checkbox('insert_message_template', 'for_later', false, array('id' => 'insert_message_template', 'class' => '', 'data-type' => 'checkbox'))}}
									{{Form::label('insert_message_template', 'Save as template')}}	
									<div class="edit-msg-temp-link">edit templates</div>
								</div>
								@if($currentPage == 'admin-textmsg')
								<div class="right">
									<span class="textCnt"></span> characters remaining
								</div>
								@endif
							</div>
						</div>

						<div class="camp-textarea">
							{{Form::textarea('body', null, array('class'=>'c-body textarea-field', 'id'=>'textarea-editor'))}}

							<div class="row date-time-selection-row">
								<div class="column small-12 large-7">
									<div class="sched-later-container">
										{{Form::checkbox('schedule_later', 'for_later', false, array('id' => 'schedule_later', 'class' => '', 'data-type' => 'checkbox'))}}
										{{Form::label('schedule_later', 'Schedule for later')}}	
									</div>
									<div class="date-time-select-container">
										{{Form::text('date', null, array('class'=>'date-cal', 'placeholder'=>'Click to add date...'))}}
										{{Form::selectRange('hours', 1, 12, 6, array('class'=>'select-time'))}}
										<select name="minutes" class="select-time">
											@for( $i = 0; $i <= 60; $i++ )
												@if( $i < 10 )
													<option value="0{{$i}}">0{{$i}}</option>
												@else
													<option value="{{$i}}">{{$i}}</option>
												@endif
											@endfor
										</select>
										{{Form::select('period', array('am'=>'am', 'pm'=>'pm'), 'am', array('class'=>'select-time'))}} <span>Pacific Time</span>
									</div>	
								</div>
								<div class="column small-12 medium-6 large-2 text-center">
									<div class="save-campaign-btn camp-btn" @if($currentPage == 'admin-textmsg') data-validcnt="" data-isfreetrial="" data-needchangeplan="" @endif>Save</div>
								</div>
								<div class="column small-12 medium-6 large-3 end text-center">
									<div class="send-campaign-btn camp-btn">Send</div>
								</div>
							</div>
						</div>
						
						{{Form::close()}}

					</div>

					@if( $currentPage != 'admin-textmsg' )
					<div class="column small-12 medium-6 large-5 end large-offset-2 preview-side show-for-medium-up">
						<div class="row">
							<div class="column small-8 medium-12 large-6 right">
								<a class="radius button action-bar-btn">
								<div class="row">
									<div class="column small-2">
										<img alt="" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/automatic-campaigns.png">
									</div>
									<div class="column small-9">
										AUTOMATIC CAMPAIGNS
									</div>
									<div class="column small-1">
										<span data-tooltip aria-haspopup="true" class="has-tip tip-left selected-students-icon" title="Don't have time to send messages to students? We will automatically send messages on your behalf if you do not send any campaigns for one week. Please uncheck pending/handshakes if you want this turned off.">?</span>
									</div>
								</div>
								</a>
							</div>

						</div>

						<div class="row small-8 medium-12 large-offset-6 large-6 auto-camp-list">
							  {{Form::checkbox('Pending', 'Pending', $pending_auto_campaign , array( 'id' => 'pending-on-off' ))}}
						      {{Form::label('pending-on-off', 'Pending')}}
						</div>
						<div class="row small-8 medium-12 large-offset-6 large-6 auto-camp-list">
							  {{Form::checkbox('Handshake', 'Handshake', $handshake_auto_campaign , array( 'id' => 'handshake-on-off' ))}}
						      {{Form::label('handshake-on-off', 'Handshake')}}
						</div>
					</div>
					@endif

					<div class="column small-12 medium-6 large-5 end large-offset-1 preview-side show-for-medium-up">
						<!-- preview analytics -->
						@if($currentPage == 'admin-textmsg')
						<div class="row billing-nav">
							<div class='column show-for-large-only large-8 end'>
								<nav>
									<section class="top-bar-section">
										<ul class="right">
											<li>
												<a href="/settings/billing" class="inner-bar">Billing</a>
											</li>
											<li>
												<a href="/settings/billing?invoices=1" class="inner-bar">Invoices</a>
											</li>
											<li>
												<a href="/settings/billing" class="inner-bar">Pricing</a>
											</li>
											<li>
												<a href="/settings/billing?plans=1" class="inner-bar">Plans</a>
											</li>
										</ul>
									</section>
								</nav>
							</div>
							
						</div>
						@endif

						<div class="analytics-container hide">
							<div class="row analytics-title">Analytics <img src='/images/admin/analytics-icon.png' alt=""/></div>
							<div class="row selected-camp">
								<div class="column medium-12 large-12"><span class="selected-camp-name"></span>
									<span class="selected-camp-recipients"></span>
								</div>
								<div class="column medium-12 large-12 selected-camp-scheduled-date"></div>
								<table>
									<thead>
										<th></th>
										<th>Total Messages</th>
										<th>Response</th>
										<th>Response Rate</th>
									</thead>
									<tbody>
										<tr>
											<td>Read</td>
											<td class="inject-total_num_campaign_messages"></td>
											<td class="inject-total_num_campaign_read_messages"></td>
											<td class="inject-read_response_rate"></td>
										</tr>
										<tr>
											<td>Replied</td>
											<td class="inject-total_num_campaign_messages"></td>
											<td class="inject-total_num_campaign_replied_messages"></td>
											<td class="inject-replied_response_rate"></td>
										</tr>
									</tbody>
								</table>
							</div>
						</div>

						<!-- preview image -->
						<div class="lg-preview-btn hide">
							<a href="#" class="button">Preview</a>
						</div>
						@if($currentPage == 'admin-textmsg')

						<div class="cost-preview-container hide">

							<div class="row cost-details-preview">
								<div id="cost-details">
									<ul>
										<div>Country</div>
										<div># of Students</div>
									</ul>
									<!-- inject summary here -->
									<ul class="cost-details-summary">
									</ul>
								</div>
							</div>
							<div class="row pricing">
								<div class="column small-1">
								</div>
								<div class="column small-2 total-cost-preview">
									<span>SMS</span>
								</div>
								<div class="column small-5 end total-cost-preview">
									<span>Total: </span>
									<span class="total-stu-number"></span>
								</div>
								<div class="column small-12 notice">
									<span>*This does not include receiving messages. <br/>Additional charges may apply.</span>
								</div>
								<div class="column small-12">
									<div class="row ">
										<div class="column small-3 brief-review text-left">{{$textmsg_tier or ''}}</div>
										<div class="column small-9 brief-review text-right">
											Used <span class="cur-text-msg"></span> / 
											@if(isset($textmsg_tier) && $textmsg_tier == 'free')
											<span class="cur-text-msg-left">{{$num_of_free_texts or 0}}</span>
											@elseif(isset($textmsg_tier) && $textmsg_tier == 'flat_fee' && $flat_fee_sub_tier != 'plan-4')
											<span class="cur-text-msg-left">{{$num_of_eligble_texts or 0}}</span>
											@elseif(isset($textmsg_tier) && $textmsg_tier == 'flat_fee' && $flat_fee_sub_tier == 'plan-4')
											<span class="cur-text-msg-left">Unlimited</span>
											@endif
										</div>
									</div>
									<div class="progress success round">
  										<span class="meter" style="width: 1%"></span>
									</div>
								</div>
								<div class="column small-12 text-right change-plan">
									<a href="#">Change Plan</a>
								</div>
							</div>
						</div>
						@endif
						<div class="text-center preview-container show-for-medium-up hide">
							<div class="iphone">
								<div class="screen"></div>
								<div class="home"></div>
							</div>
							@if($currentPage == 'admin-groupmsg')
							<div class="macbook-pro">
								<div class="macbook">
									<div class="camera"></div>
									<div class="monitor"></div>	
								</div>
								<div class="keyboard">
									<div class="handle"></div>
								</div>	
							</div>
							@endif
							<div class="text-center preview-btn-container">
								@if($currentPage == 'admin-textmsg')
								<span class="preview-btn">Mobile</span>
								@else
								<span class="preview-btn">Desktop</span> | <span class="preview-btn active">Mobile</span>
								@endif
							</div>
						</div>
					</div>

				</div>
				
				
				@endif
				
			</div>	

			<!-- large preview modal
			<div id="lg-preview-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
				
				<div class="clearfix">
					<div class="right">
						<a class="close-reveal-modal help_helpful_videos_close_icon" aria-label="Close">&#215;</a>
					</div>
				</div>

				<div class="text-center">Preview</div>

				<div class="iphone lg">
					<div class="screen">
					</div>
					<div class="home"></div>
				</div>

				<div class="macbook-pro lg">
					<div class="macbook">
						<div class="camera"></div>
						<div class="monitor"></div>	
					</div>
					<div class="keyboard">
						<div class="handle"></div>
					</div>	
				</div>

				<div class="text-center preview-btn-container">
					<span class="preview-btn">Desktop</span> | <span class="preview-btn active">Mobile</span>
				</div>
			</div> -->

			<!-- msg sending modal -->
			<div id="send-campaign-msg-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
				<div class="clearfix">
					<div class="right">
						<a class="close-reveal-modal help_helpful_videos_close_icon" aria-label="Close">&#215;</a>
					</div>
				</div>
				@if($currentPage == 'admin-textmsg')
				<div class="curTextMsgCount text-center">
					<!-- msg number inject here-->
				</div>
				@endif
				<div class="msg text-center">
					<!-- msg inject here -->	
				</div>
				@if($currentPage == 'admin-textmsg')
				<div class="term-condition text-center">
					{{Form::checkbox('term-condition', 'term_condition', null, array('id' => 'term-condition-1'))}}
					<label for="term-condition-1" class="agreement-confirm">Yes, I agree to the above <a href="/terms-of-service" target="_blank">terms and conditions</a></label>
				</div>
				@endif
				<div class="text-center">
					<a class="send-away-btn button disabled"></a>
				</div>

				@if($currentPage == "admin-textmsg")
				<div class="row pricing">
					<div class="column small-12">
						<div class="row ">
							<div class="column small-3 brief-review">Free Texts</div>
							<div class="column small-9 brief-review text-right">
								Used <span class="cur-text-msg"></span> / <span class="cur-text-msg-left"></span>
							</div>
						</div>
						<div class="progress success round">
								<span class="meter" style="width: 1%"></span>
						</div>
					</div>
					<div class="column small-12 text-right change-plan">
						<a href="#">Change Plan</a>
					</div>
				</div>
				@endif
			</div>

			<!-- msg sending notify modal -->
			<div id="send-campaign-msg-notify-modal" class="reveal-modal" data-reveal aria-labelledby="" aria-hidden="true" role="dialog">
				<div class="clearfix">
                    <div class="right">
                        <a class="close-reveal-modal help_helpful_videos_close_icon" aria-label="Close">&#215;</a>
                    </div>
                </div>
                <div class="row text-center notify-title">
                	Notice
                </div>
                <div class="row text-center">
                	<div class="column text-left large-offset-2 large-10 brief-review">
                		Only <span class="total-eligble-users"></span> of the <span class="total-users"></span> recipients are eligible for free texting.
                	</div>
                	<div class="column text-left large-offset-2 large-10 brief-review">
                		Your current eligble text number is <span class="total-eligble-textmsg"></span>. <br/>Please upgrade and send to all.
                		
                	</div>
                </div>
                <div class="row actions">
                	<!--<div class="column text-center small-12 medium-12 large-6"><a class="button continue-send-campaign">Continue</a></div>-->
                	<div class="column text-center large-12 end"><a class="button alert upgrade-and-send-campaign">Upgrade and Send</a></div>
                </div>

			</div>

			<!-- order summary modal -->
			<div id="order-summary-modal" class="reveal-modal" data-reveal aria-labelledby="" aria-hidden="true" role="dialog">
				<div class="clearfix">
					<div class="right">
						<a class="close-reveal-modal help_helpful_videos_close_icon" aria-label="Close">&#215;</a>
					</div>
				</div>

				<div class="billing-checkout">
		            <div class="billing-header text-center">Order Summary</div>
		            <div class="summary-head clearfix">
		                <div class="left">Item</div> 
		                <div class="right"></div>
		            </div>
		            <hr />
		            <div class="summary-list">
		                <div class="summary-item">
		                    <div class="item-descrip txt-phone-plan clearfix">
		                        <div class="left"><b>Purchased Phone Number</b></div>
		                        <div class="right">One time fee</div>
		                    </div>
		                    <div class="item-package txt-phone-plan clearfix">
		                        <div class="left">{{$purchased_phone or 'N/A'}}</div>
		                        <div class="right"><b>$60.00</b></div>
		                    </div>
		                </div>
		            </div>
		            <div class="summary-list">
		                <div class="summary-item">
		                    <div class="item-descrip txt-msg-plan clearfix">
		                        <div class="left"><b>Premium</b></div>
		                        <div class="right">Monthly fee</div>
		                    </div>
		                    <div class="item-package txt-msg-plan clearfix">
		                        <div class="left">Plexuss Premium Membership</div>
		                        <div class="right"><b>$100.00</b></div>
		                    </div>
		                </div>
		            </div>
		            <hr />
		            <div class="summary-total clearfix">
		                <div class="left"><b>Grand total</b></div>
		                <div class="right"><b>$100.00</b></div>
		            </div>
		            <div class="row text-center">
		            	<a class="send-away-btn order-summary-preview button">Send</a>
		            </div>

		            @if($currentPage == 'admin-textmsg')
					<div class="term-condition text-center">
						{{Form::checkbox('term-condition', 'term_condition', null, array('id' => 'term-condition-2'))}}
						<label for="term-condition-2" class="agreement-confirm">Yes, I agree to the above <a href="/terms-of-service" target="_blank">terms and conditions</a></label>
					</div>

					<div class="column small-12 brief-summary">
						<div class="row">
							<div class="column small-3 brief-review text-left"></div>
							<div class="column small-9 brief-review text-right">
								Used <span class="cur-text-msg"></span> / <span class="cur-text-msg-left"></span>
							</div>
						</div>
						<div class="progress success round">
							<span class="meter" style="width: 1%"></span>
						</div>
					</div>
					@endif

        		</div>

			</div>

			<!-- send successful modal -->
			<div id="send-success-modal" class="reveal-modal" data-reveal aria-labelledby="" aria-hidden="true" role="dialog">
				<div class="clearfix">
					<div class="right">
						<a class="close-reveal-modal help_helpful_videos_close_icon" aria-label="Close">&#215;</a>
					</div>
				</div>
				<div class="row">
					<div class="column small-12 text-center sent-title">
						Your messages have been sent. 
					</div>
					<div class="column small-12 text-center brief-review">
						A confirmation/invoice has been sent to <span>{{$email or 'your email'}}</span> for your record.
					</div>
					<div class="column small-12 text-center back-to-textmsg">
						<a href="/admin/textmsg">Take me back to Text Messages</a>
					</div>
				</div>
			</div>
			<!-- msg save template modal -->
			<div id="save-template-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
				<div class="clearfix">
					<div class="right">
						<a class="close-reveal-modal help_helpful_videos_close_icon" aria-label="Close">&#215;</a>
					</div>
				</div>

				<br />

				<div>
					<label for="template_name">Template name: </label>
					<input id="template_name" type="text" name="name" placeholder="Enter the template name">
				</div>
				
				<br />

				<div class="text-center">
					<a class="save-template-btn button">Save</a>
				</div>
			</div>

			<!-- close the off-canvas menu -->
			<a class="exit-off-canvas"></a>

		</div>
	</div>

@stop