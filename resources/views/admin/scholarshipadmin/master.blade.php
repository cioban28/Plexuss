<!doctype html>
<html class="no-js" lang="en">
	<head>
		@include('private.headers.header')

		<style>
			/* IE9, IE10 */
			@media screen and (min-width:0\0) {
			    html{
			    	position: relative !important; 
			    	z-index: -25 !important;
			   	}
			}

			/* IE10+ */
			@media screen and (-ms-high-contrast: active), (-ms-high-contrast: none) {
			   html{
			    	position: relative !important; 
			    	z-index: -25 !important;
			   	}
			}
		</style>

		 <script src="//ajax.googleapis.com/ajax/libs/jquery/1.11.1/jquery.min.js"></script> 
	</head>
	<body id="{{$currentPage}}">

		@include('private.includes.topnav')
		
		@if(isset($column_orders))
			{{ Form::hidden('orderBy', $column_orders['orderBy'], array('class'=>'hidden_orderBy')) }}
			{{ Form::hidden('sortBy', $column_orders['sortBy'], array('class'=>'hidden_sortBy')) }}
		@endif
		@yield('content')

		@if(isset($export_fields))
			<!-- Export Students modal -->
			<div id="exp-student-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog">
				<div class="row">
					<div class="column small-12 text-right">
						<a class="close-reveal-modal help_helpful_videos_close_icon" aria-label="Close">&#215;</a>
					</div>
				</div>

				{{ Form::open(array('url' => '/admin/exportApprovedStudentsFile/admin', 'method' => 'post')) }}
				{{ Form::hidden('currentPage', $currentPage) }}
				<div class="row">
					<div class="column small-3 end">
						Date Range:
						{{ Form::text('date', $export_fields_date , array('id' => 'dtrange','required','class'=>'dash-cal','placeholder'=>"&nbsp;&nbsp;Date(S)")) }}
					</div>
				</div>
				<div class="row">
					<div class="column small-12">
						&nbsp;
					</div>
				</div>
				
				<div class="row">
					<div class="column small-3">
						<!-- currently 30 checkboxes , four columns balances out fairly nice -->
						<?php //dd(count($export_fields)) 
							$perCol = ceil(count($export_fields) * 1/4);
						?>
						@for ($i = 0; $i < $perCol ; $i++)
						    {{Form::checkbox($export_fields[$i]['id'], $export_fields[$i]['id'], true, array( 'id' => 'export_'.$export_fields[$i]['id'] ))}}
						    {{Form::label('export_'.$export_fields[$i]['id'], $export_fields[$i]['name'])}}
						    <br />
						@endfor
					</div>

					<div class="column small-3">
						@for ($i = $perCol; $i < $perCol*2; $i++)
						    {{Form::checkbox($export_fields[$i]['id'], $export_fields[$i]['id'], true, array( 'id' => 'export_'.$export_fields[$i]['id'] ))}}
						    {{Form::label('export_'.$export_fields[$i]['id'], $export_fields[$i]['name'])}}
						    <br />
						@endfor
					</div>

					<div class="column small-3">
						@for ($i = $perCol*2; $i < $perCol*3; $i++)
						    {{Form::checkbox($export_fields[$i]['id'], $export_fields[$i]['id'], true, array( 'id' => 'export_'.$export_fields[$i]['id'] ))}}
						    {{Form::label('export_'.$export_fields[$i]['id'], $export_fields[$i]['name'])}}
						    <br />
						@endfor
					</div>

					<div class="column small-3">
						@for ($i = $perCol*3; $i < count($export_fields); $i++)
						    {{Form::checkbox($export_fields[$i]['id'], $export_fields[$i]['id'], true, array( 'id' => 'export_'.$export_fields[$i]['id'] ))}}
						    {{Form::label('export_'.$export_fields[$i]['id'], $export_fields[$i]['name'])}}
						    <br />
						@endfor
					</div>
				</div>

				<div class='row export_download_note'>
		            <div class='small-2 column dl_button'>
		                {{ Form::submit('Download', array('class'=>'radius button'))}}
		            </div>
		            <div class='small-10 column'>
		            	<b>Note:</b> Download available for contacts in the approved folder only. 
		            </div>
		        </div>
				{{ Form::close() }}
			</div>	
		@endif

		@if( isset($processing_export) && $processing_export )
			<div id="export-processing-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-processing-export="{{$processing_export}}">
				<h3>We are processing your request</h3>
				<br />
				<div>We will email a link to <b>{{$email}}</b> to download your files once your request has been processed.</div>
				<br />
				<div>This may take 5 to 10 minutes.</div>
				<br />
				<div>Thank you for your patience.</div>
				<br />
				<div>
					<div class="ok-btn close-reveal-modal">Ok</div>
				</div>
			</div>
		@endif

		
		<div id="why-removing-appr-student-modal" class="reveal-modal" data-reveal aria-labelledby="modalTitle" aria-hidden="true" role="dialog" data-options="close_on_background_click: false">
			<div class="text-right close-remove-modal">x</div>

			<div class="title">Please indicate why you are removing this student from your Handshakes</div>
			<div class="feedback">Your feedback helps us provide you with higher quality students!</div>

			<form>
			
				<ul>
					<li>
						<input type="checkbox" value="true" name="inaccurate_information" id="inaccurate-info" class="required-info-box" />
						<label for="inaccurate-info">Inaccurate Information</label>
						<ul>
							<li>
								<input type="checkbox" value="true" name="inaccurate_information_financial" id="inaccurate-financial" class="inaccuracy-field required-info-box" disabled />
								<label for="inaccurate-financial">Financial</label>
							</li>
							<li>
								<input type="checkbox" value="true" name="inaccurate_information_startdate" id="inaccurate-startdate" class="inaccuracy-field required-info-box" disabled />
								<label for="inaccurate-startdate">Start date</label>
							</li>
							<li>
								<input type="checkbox" value="true" name="inaccurate_information_other" id="other-innacuracies" class="inaccuracy-field required-info-box" disabled />
								<label for="other-innacuracies">Other inaccuracies</label>
							</li>
						</ul>
					</li>
					<li>
						<input type="checkbox" value="true" name="student_not_responsive" id="student-not-responsive" class="required-info-box" />
						<label for="student-not-responsive">Student is not responsive</label>
					</li>
					<li>
						<div>
							<input type="checkbox" value="true" name="other_reason" id="innacurate-other" class="required-info-box" />
							<label for="innacurate-other">Other</label>
							<input type="text" name="other_reason_response" class="other-txt required-info-box" id="other-innacuracies-input" disabled />	
						</div>
					</li>
				</ul>

				<div class="text-right">
					<div class="close-remove-modal cancel">Cancel</div>
					<button class="radius remove-std-btn" disabled>Remove Student</button>
				</div>
				
			</form>

		</div>

		@include('private.includes.backToTop')
		@include('private.footers.footer')

		@if ( $currentPage == 'admin-messages' || $currentPage == 'admin-chat'  )
			<script type="text/javascript">
				//This handles the switchcing of the chat to message window. I did not inlclude it in the main script so normal user dont see it.
				Plex.messages.getMessagesUrl = '/admin/ajax/messages/getNewMsgs/';
		 		Plex.messages.getUserNewTopicsUrl = '/admin/ajax/messages/getUserNewTopics';
		 		Plex.messages.topicReadUrl = '/admin/ajax/messages/setMsgRead/';
				Plex.messages.windowActiveNow = "{{ $currentPage }}";
				Plex.messages.college_id = "{{ $org_school_id }}";
				// RECIPIENT TYPE ON ADMIN PAGE WILL ALWAYS BE USER
				Plex.messages.sticky_recipient_type = 'user';

				Plex.chat.windowActiveNow = "{{ $currentPage }}";
				Plex.chat.mainCollegeChatId = "{{ $org_school_id }}";
				/* if we're creating a new placeholder thread ( id == -1 ), we need
				 * to enable sticky until a message is sent. We also need to save
				 * the recipient type ( user/college ) and the thread type ( chat/inquiry )
				 */
				@if ( $stickyUsr )
					Plex.messages.sticky_recipient_id = "{{ $stickyUsr }}";
					Plex.messages.stickyEnabled = true;
					Plex.messages.sticky_thread_type = "{{ $sticky_thread_type }}";
				@endif
			</script>
		@endif

		@if( $currentPage == 'admin-content-management' )
			<link rel="stylesheet" type="text/css" href="/css/croppie.css" />
			<script src="/js/moment.min.js?8"></script>
			<script src="/js/underscoreJS/underscore_prod.js?8"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/react/0.13.3/JSXTransformer.js"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/react/0.13.3/react-with-addons.js"></script>
			<script src="https://cdnjs.cloudflare.com/ajax/libs/react/0.13.3/react.js"></script>
			<script src="/js/croppie.js"></script>
			<script src="/js/exif.js"></script>
			<!--<script src="/js/reactJS/react_v0.13.3_prod.js"></script>
			<script src="/js/reactJS/react_with_addons_v0.13.3_prod.js"></script>
			<script src="/js/reactJS/JSXTransformer_v0.13.3.js"></script>-->
			<!-- external react script -->
			<script type="text/jsx" src="/js/contentManagementComponents.js?8"></script>
		@endif
	</body>
</html>
