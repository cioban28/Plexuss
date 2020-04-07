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
	<body id="{{ $currentPage }}">

		@include('private.includes.agencyTopNav')
		
	
		@yield('content')

		@include('private.includes.backToTop')
		@include('private.footers.footer')


		@if ( $currentPage == 'agency-messages' || $currentPage == 'agency-chat'  )
			<script type="text/javascript">
				//This handles the switchcing of the chat to message window. I did not inlclude it in the main script so normal user dont see it.
				Plex.messages.getMessagesUrl = '/agency/ajax/messages/getNewMsgs/';
		 		Plex.messages.getUserNewTopicsUrl = '/agency/ajax/messages/getUserNewTopics';
		 		Plex.messages.topicReadUrl = '/agency/ajax/messages/setMsgRead/';
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
	</body>
</html>
