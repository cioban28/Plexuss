//Only Holds messages calls.
/***********************************************************************
 *===================== NAMESPACED VARIABLES ===========================
 ***********************************************************************
 * Holds namespaced variables for chat
 */
Plex.chat = {
	windowActiveNow : undefined,
	chatIsInit:false,  //are we chatting or messaging
	mainCollegeChatId : undefined,
	chatHeartbeatEnabled : undefined,
	currentTopicIdSelected : undefined,
	currentTopicNumUnread : undefined,
	recipient_id : undefined,
	sticky_recipient_type : undefined,
	lastMessageIdInTopic : undefined,
	lastMessageDateInTopic : undefined,
	lastMessageUserInTopic : undefined,
	lastMessageTimestampInTopic : undefined,
	//bottomMessageReadGap : 10,
	chatMessageHeartBeatTime : 5000,
	forceWindowScroll : true,
	//sticky_recipient_id : undefined,
	//stickyEnabled : false,
	getMessagesUrl : '/ajax/college/chat/getNewMsgs/',
	getUserNewTopicsUrl : '/ajax/college/chat/threadHeartBeat',
	sendMessageUrl : '/ajax/messaging/postMsg', // new single POST message route
	topicReadUrl : '/admin/ajax/messages/setMsgRead/',

	getMsgsHistoryUrl : '/ajax/messaging/getHistoryMsg/',
	noMoreHistoryMsg : false,
	
	hashedUserId : undefined,
	messageArea : undefined,
	sendButton : undefined,
	enterEnabled : true,
	idleChecker : undefined,
	sticky_thread_type : undefined,
	userTooltip_array: {},
	topicUserListGlobalString: 0,

	isAjaxRunning: false,
	isPrevMessageRunning: false,
	isAjaxRunningTopicList: false,
	isAjaxRunningGetMessages: false,
};
/************************************************************************/

/***********************************************************************
 *========================= RUN ON PAGELOAD ============================
 ***********************************************************************/
$( document ).ready( function(){
	Plex.chat.initialize();

	$(".rightChatColumn .messageScrollArea").scroll(function()
    {
        var div = $(this);
        if(div.scrollTop() == 0 && Plex.chat.noMoreHistoryMsg == false){
        	Plex.chat.getPreviousMessages();
        }
    });
} );



/***********************************************************************/

/***********************************************************************
 *===================== INITIALIZE CHAT ITEMS ==========================
 ***********************************************************************
 * initializes chat items
 */
Plex.chat.initialize = function(){
	this.cacheChatItems();
	this.bindTextareaEnter();
	this.setReadMessageScrollChecker();
	this.checkOnlineStatus();
	this.checkMessagesView(); // admin only. Checks if message view is
							  // currently visible. if so, start heartbeat
	this.setIdleChecker();
	
	// need a way to check if chatting or messaging -- program as it currently is... uses same js, same blade
	//and toggles functionality based on flag chatIsInit
	if(Plex.chat.checkMessagesView())
		this.chatIsInit = false; 
	else
		this.chatIsInit = true;
}
/***********************************************************************/

/***********************************************************************
 *===================== SET IDLE CHECKER/TIMER =========================
 ***********************************************************************
 * Sets an idle checker to switch the heartbeat on/off
 */
Plex.chat.setIdleChecker = function(){
	// don't allow idle re-init of checker
	if( this.idleChecker ){
		console.log( 'adminChat idle checker already exists!' );
		return;
	}
	var _this = this;
	Plex.chat.idleChecker = $( document ).idle( { 
		onIdle: function(){
			console.log( 'chat idle!' );
			// don't show idle message if chat is not online
			var online = parseInt( $( '#chat_online_status' ).val() );
			if( !online ) return; 
			//_this.stopChatReadyChecker();
			_this.stopChatMessageHeartBeatTimer();
			_this.stopAdminChatOnlineHeartbeat();
			$( '#chat_idle_overlay' ).show( 'slide', { direction: 'left' }, 250 );
		},
		onActive: function(){
			console.log( 'chat active!' );
			// don't activate chat unless user has explicitly set chat to active
			var online = parseInt( $( '#chat_online_status' ).val() );
			if( !online ) return;

			// stop heartbeat before re-starting
			_this.stopChatMessageHeartBeatTimer();
			// get topic list and messages, THEN setInterval for heartbeat
			_this.getTopicList( _this.startChatMessageHeartBeatTimer() );
			// stop init
			_this.stopAdminChatOnlineHeartbeat();
			_this.setAdminChatOnlineHeartbeat();
			$( '#chat_idle_overlay' ).hide( 'slide', { direction: 'left' }, 250 );
		},
		//idle: 600000 // idle after 10 minutes
		idle: 480000 // idle after 8 minutes
		//idle: 300000 // idle after 5 minutes
		//idle: 60000 // idle after 1 minute
		//idle: 5000 // idle after 5 seconds
	} );
}
/***********************************************************************/

/***********************************************************************
 *======================== CHECKS ONLINE STATUS ========================
 ***********************************************************************
 * Checks main chat's online status. Called on page load
 */
Plex.chat.checkOnlineStatus = function(){
	var elem = document.getElementById( 'chat_online_status' );
	this.chatOnlineStatus( elem );
}
/***********************************************************************/

/***********************************************************************
 *========================= CACHE CHAT ITEMS ===========================
 ***********************************************************************
 * Caches chat items on document ready
 */
Plex.chat.cacheChatItems = function(){
	this.hashedUserId = $( '.leftChatColumn' ).data( 'hasheduid' );
	this.messageArea = $( '.messageScrollArea' );
	this.sendButton = $( '.chatTxtbox' ).children( '.row' ).find( '.sendbutton' );
	// chat switch button's unread count element
	this.mainChatCount = $( '.switchbuttons-wrapper' )
		.children( '.switchbuttons.mainChatButton' )
		.children( '.unread_count' );
}
/***********************************************************************/

/***********************************************************************
 *================= GET DATA INFO FROM SELECTED ITEM ===================
 ***********************************************************************
 * Accepts a thread object and collects all needed information
 * @param		elem		object			Represents a topic/thread. Used
 * 											to switch between different threads
 */
Plex.chat.getDataInfoFromSelectedItem = function (elem){

	// Only set these items if they're undefined. Otherwise, they have
	// a current value, and hence, are selected/active
	if ( typeof this.currentTopicIdSelected == 'undefined' ) {
		this.currentTopicIdSelected = elem.data('topicid');
	};

	if( typeof this.currentTopicNumUnread == 'undefined' ){
		this.currentTopicNumUnread = elem.data( 'num_unread' );
	}

	if ( typeof this.recipient_id == 'undefined' ) {
		this.recipient_id = elem.data('recipient_id');
	};

	if ( typeof this.sticky_recipient_type == 'undefined' ) {
		this.sticky_recipient_type = elem.data('sticky_recipient_type');
	};
}
/************************************************************************/

/***********************************************************************
 *========================== CHANGE TOPIC ==============================
 ***********************************************************************
 * Triggered when user clicks a contact/thread/chat in the contact list.
 * Clears message area. Changes selected thread.
 */
Plex.chat.changeTopic = function (elem){
	var messageContacts = $('.leftChatColumn');
	var messageScrollArea = $('.rightChatColumn .messageScrollArea');
	messageScrollArea.html('');
	messageContacts.find('.chatUser').removeClass('selected');
	// resets values for getDataInfoFromSelectedItem
	this.currentTopicIdSelected = undefined;
	this.currentTopicNumUnread = undefined;
	this.recipient_id = undefined;
	this.sticky_recipient_type = undefined;

	this.firstMessageIdInTopic = undefined;
	this.noMoreHistoryMsg = false;

	// Switch selected item by setting this' variables
	var contact = $(elem);
	this.getDataInfoFromSelectedItem(contact);

	//set the variables needed
	//clear the last lastMessageIdInTopic so scipt gets a new fresh set.
	this.lastMessageIdInTopic = undefined;
	// clear the lastMessageDateInTopic so script can get a new one
	this.lastMessageDateInTopic = undefined;
	// clear last user to enter a message
	this.lastMessageUserInTopic = undefined;
	// clear last timestamp
	this.lastMessageTimestampInTopic = undefined;
	
	//set this new topic clicked to selected.
	contact.addClass('selected');
	this.forceWindowScroll = true;
	this.getMessages();
	this.setThreadRead();
}
/***********************************************************************/

/***********************************************************************
 *================ SWITCH TO PRIVATE MESSAGES WITH USER ================
 ***********************************************************************
 * Admin chat ONLY. Used to switch from the chat view to the private messages
 * view when the admin clicks on an online user. Checks if there is a currently
 * existing thread, and if not, enables sticky ( which creates a visual thread
 * placeholder ), unsets the last_message_id variable ( so getMessages fetches
 * a fresh set of last 20 messages ) and finally, switches to the messages
 * view.
 * @param		string		thread_type			The type of thread to create:
 * 												so far we have: inquiry-msg and
 * 												chat-msg
 * @param		string		thread_id			The thread id. This is needed
 * 												for the messages getTopicList()
 * 												method to pre-select the
 * 												correct thread
 * @param		string		recipient_id		The id of the recipient, be it
 * 												college or user.
 */
Plex.chat.switchToMessagesWithUser = function( thread_type, thread_id, recipient_id ){
	console.log( 'recipient_id: '+ recipient_id + ' thread_id: ' + thread_id );
	console.log( typeof thread_id );
	// set thread id for messages
	Plex.messages.currentTopicIdSelected = thread_id;
	// enable sticky only if there is no thread b/t selected user and current user
	if( thread_id == -1 ){
		
		Plex.messages.stickyEnabled = true;
		// RECIPIENT TYPE ON ADMIN PAGE WILL ALWAYS BE USER
		// set recipient id
		Plex.messages.sticky_recipient_id = recipient_id;
		// set new thread type
		Plex.messages.sticky_thread_type = typeof thread_type != 'undefined' ? thread_type : 'chat-msg';
		
	}
	/* unset last messages id for messages. Needed for because this is
	 * typically done with changeTopic ( which this method takes the
	 * place of).
	 */
	Plex.messages.lastMessageIdInTopic = undefined;
	// Switch to messages view
	Plex.chat.toggleChatAndMessageWindowsViews( 'admin-messages' );
}
/***********************************************************************/

/***********************************************************************
 *========================= GET TOPIC LIST =============================
 ***********************************************************************
 * Gets the 'topic list'/ all members of the contact list. This is fired with
 * the heartbeat only to refresh the contact list.
 * @param		function		callback		optional callback. Used with
 * 												startMessageHeartBeatTimer to
 * 												immediately call this method
 * 												and then, after it's done
 * 												send in an unnamed function with
 * 												setInterval as a callback. That
 * 												way we can get messages right after
 * 												a message has been sent, and restart
 * 												the heartbeat.
 */
Plex.chat.getTopicList = function ( callback ){
	//This updates the FULL topic list.
	var _this = this;
	var _url = '';

	if(Plex.chat.isAjaxRunningTopicList){
		return;
	}

	Plex.chat.isAjaxRunningTopicList = true;

	if ( typeof _this.currentTopicIdSelected == 'undefined' && typeof _this.mainCollegeChatId == 'undefined' ) {
		//right now Admin chat does not need
		// admin topics shows non-admins only
		_url = this.getUserNewTopicsUrl  +'/' + '-1' + '/college';
	} else if( _this.windowActiveNow == 'admin-messages' || _this.windowActiveNow == 'admin-chat' ){
		_url = this.getUserNewTopicsUrl  +'/' + _this.mainCollegeChatId + '/college';
	} else{
		// user topics show admins only (???)
		_url = this.getUserNewTopicsUrl  +'/' + _this.mainCollegeChatId + '/users';
	};

	// make AJAX call
	$.ajax({
		url: _url,
		type: 'GET',
		dataType: 'json',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	})
	.done(function( data ) {
		// console.log(data.topicUsr);
		// console.log();
		// $.each(data.topicUsr, function(){
		// 	console.log(JSON.stringify(this));
		// });
		// console.log('-------------------- start --------------------');
		// if( data.topicUsr.length > Plex.chat.topicUserListGlobalString ){
		// 	Plex.chat.topicUserListGlobalString = data.topicUsr.length;
		// 	_this.addTopicList(data);
		// }
		// console.log('-------------------- end --------------------');

		Plex.chat.isAjaxRunningTopicList = false;
		_this.addTopicList(data);
		
		
	});

	// call callback if set
	if( typeof callback != 'undefined' ){
		callback();
	}
}
/***********************************************************************/

/***********************************************************************
 *========================= ADD TOPIC LIST =============================
 ***********************************************************************
 * Called after getTopicList(). Adds the retrieved topics and replaces those
 * that are in the current set with .html().
 * @CALLS getMessages when done
 * @param		data		object			contains all variables needed to
 * 											add the topic html
 */
Plex.chat.addTopicList = function (data){
	var html = '';
	var contents = '';

	var leftMessageColumn = $('.leftChatColumn .chatUserLists');

	// $('#admin-chat > span.tooltip').remove();

	$.each(data.topicUsr, function(index, val) {
		if ( index == 0 &&  typeof Plex.chat.currentTopicIdSelected == 'undefined'   ) {
			Plex.chat.currentTopicIdSelected = val.thread_id;
		};

		// hides main chat from topic list
		var hide_main_chat = val.is_main_chat ? ' style="display:none;"' : '';

		var tooltip_content = '';
		if(Plex.chat.userTooltip_array['key'+val.thread_type_id] != undefined){  
		   tooltip_content = Plex.chat.userTooltip_array['key'+val.thread_type_id];
		}


		html += '<div class="column small-12 chatUser"'
			+ ' data-topicid="' + val.thread_id + '"'
			+ ' data-num_unread="' + val.num_unread_msg + '"'
			+ ' data-recipient_id="' + val.thread_type_id + '"'
			+ ' data-sticky_recipient_type="' + val.thread_type + '"'
			// + ' onclick="Plex.chat.switchToMessagesWithUser(' 
			// 	+ val.sticky_thread_type // the type of thread to create
			// 	+ ', '
			// 	+ val.thread_id  // thread id
			// 	+ ', '
			// 	+ val.thread_type_id // recipient id ( user/college )
			// + ' );"'
			+ ' onclick="Plex.chat.switchToMessages(' + val.thread_type_id + ', \'inquiry-msg\')" ' +
			+ hide_main_chat
			+ '>';
        html +=  	val.Name + '<span title="'+tooltip_content+'" class="has-tip tip-right prof-tooltip" aria-haspopup="true" data-tooltip data-uid="'+val.thread_type_id+'"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/profile_quickview.jpg" alt="plexuss" /></span>';
		html +=		'<span class="unread_count">';
		html += 		val.num_unread_msg;
		html += 	'</span>';
        html += '</div>';
	});

	// find element to modify	

	var updateLeftColumnBool = false;

	if (Plex.chat.topicUserListGlobalString == '') {

		updateLeftColumnBool = true;

	}else if(Plex.chat.topicUserListGlobalString != html){
		updateLeftColumnBool = true;
	}

	if (updateLeftColumnBool) {
		Plex.chat.topicUserListGlobalString = html;
		leftMessageColumn.html(html);
		$(document).foundation('tooltip', 'reflow');
	}	

	// default action: set first/top thread in list as active if no thread selected
	if ( typeof Plex.chat.recipient_id == 'undefined' ){
		var firstcontact = leftMessageColumn.find('.chatUser:first-child');
		Plex.chat.getDataInfoFromSelectedItem(firstcontact);
		leftMessageColumn.find('[data-recipient_id='+ Plex.chat.recipient_id +']').addClass('selected');
	}

	// set selected visually
	var selected_topic = leftMessageColumn.find('[data-recipient_id='+ Plex.chat.recipient_id +']');
	selected_topic.addClass('selected');
	// set thread as read
	this.currentTopicNumUnread = selected_topic.data( 'num_unread' );
	this.setThreadRead();

	// update unread count
	this.mainChatCount.html( data.main_chat_unread_msg );
	if( typeof Plex.messages != 'undefined' ){
		Plex.messages.mainMessagesCount.html( data.private_msg_unread_msg );
	}

	this.getMessages();
}

Plex.chat.switchToMessages = function(user_id, type) {
	// Main chat does not redirect
	if (user_id == -1) {
		return;
	}

	window.open('/admin/messages/' + user_id + '/' + type, '_blank')
}


/***********************************************************************
 *============================ GET MESSAGES ============================
 ***********************************************************************
 * Fetches All messages and NEW ones for user id supplied.
 */
Plex.chat.getMessages = function (){
	//turn off submit button
	//this.toggleSendButton();
	var _this = this;
	var url ='';
	var isappend = false;
	var isprepend = false;

	// if(Plex.chat.isAjaxRunningGetMessages){
	// 	return;
	// }

	Plex.chat.isAjaxRunningGetMessages = true;


	if ( typeof this.lastMessageIdInTopic != 'undefined' ) {
		// if we have a last message id, send
  		url = this.getMessagesUrl + this.currentTopicIdSelected + '/'+ this.lastMessageIdInTopic;
  		isappend = true;
	} else {
		// if not, we'll fetch the last n messages
		url = this.getMessagesUrl + this.currentTopicIdSelected ;
	}

	// do da AJAX mon
	$.ajax({
		url: url,
		type: 'GET',
		dataType: 'json',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	})
	.done(function(data) {
		data = JSON.parse(data.msg);
		Plex.chat.isAjaxRunningGetMessages = false;

		if (data.length != 0) {
			_this.addmessages( data, isappend, isprepend);
		}

	});
}
/************************************************************************/

/***********************************************************************
 *============================ GET PREVIOUS MESSAGES ============================
 ***********************************************************************
 * Fetches All previous messages and prepend to the top of messages
 */
Plex.chat.getPreviousMessages = function (){

	var _this = this;
	var url ='';
	var isappend = false;
	var isprepend = true;

	if(Plex.chat.isPrevMessageRunning){
		return;
	}

	Plex.chat.isPrevMessageRunning = true;

	url = this.getMsgsHistoryUrl + this.currentTopicIdSelected + '/-1/'+ Plex.chat.firstMessageIdInTopic;

	$.ajax({
		url: url,
		type: 'GET',
		dataType: 'json',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	})
	.done(function(data) {
		data = JSON.parse(data.msg);
		Plex.chat.isPrevMessageRunning = false;
		
		if (data.length == 0) {
			Plex.messages.noMoreHistoryMsg = true;
			$('.rightChatColumn .messageScrollArea .msg_history_load_more').remove();
		}else{
			_this.addmessages(data,isappend, isprepend);
		}
	});
}

/***********************************************************************
 *====================== ADD/INJECT MESSAGES ===========================
 ***********************************************************************
 * Inject the messages received from getMessages
 */
Plex.chat.addmessages = function(data, isappend, isprepend){

	//Build the messages 
	var html = '';
	var isFirstMessage = false;
	var msgScrollBox   = $('.rightChatColumn .messageScrollArea');
	var cnt = 0;
	var show_previous_msg = 0;

	$.each( data, function( index, message ) {
		// check and build date if necessary
		var last_message_date = Plex.chat.lastMessageDateInTopic;
		var message_date = Plex.common.make_date( message.date );
		message_date = message_date.toLocaleDateString();

		show_previous_msg = message.show_previous_msg;
		
		if (!isFirstMessage && !isappend) {
			isFirstMessage = true;
			//This saves the first message id so we can access it later during the NEW message load.
			Plex.chat.firstMessageIdInTopic = message.msg_id;
		}

		if( last_message_date != message_date ){
			Plex.chat.lastMessageDateInTopic = message_date;
			// get separator string
			var separator = Plex.common.get_date_separator( message.date );
			// build html
			html += "<div class='row dateSeparator text-center'>";
			html += 	"<div class='separatorStrike strikeLeft'></div>";
			html += 	"<div class='small-12 column'>";
			html += 		separator;
			html += 	"</div>";
			html += 	"<div class='separatorStrike strikeRight'></div>";
			html += "</div>";
		}

		// SET IS_ORG STYLES
		var is_org = '';
		if (message.is_org) is_org = ' orgMsg';

		// SET IS CURRENT USER STYLES
		var is_current_user = '';
		if( message.is_current_user ) is_current_user = ' currentUser';

		// HIDE REPEAT USER LABEL
		var hide_user = '';
		var new_message_group = '';
		var last_to_talk = Plex.chat.lastMessageUserInTopic;
		Plex.chat.lastMessageUserInTopic = message.full_name;
		if( last_to_talk == message.full_name ) hide_user = ' hideUser';

		// GET TIME
		// var time = Plex.common.get_timestamp_time( message.date );

		var time = message.time;

		// HIDE REPEAT TIMESTAMPS
		var hide_time = '';
		var last_timestamp = Plex.chat.lastMessageTimestampInTopic;
		Plex.chat.lastMessageTimestampInTopic = time;
		if( last_timestamp == time ) hide_time = ' hideTime';

		html +='<div class="row msgItem'
			+ is_org 
			+ is_current_user 
			+ hide_user 
			+ hide_time
			+ new_message_group
			+ '" data-message_id="'
			+ message.msg_id
			+ '">';
		html +='<div class="column small-2 msgName"';
		html += 	' title="' + message.full_name + '">' +message.Name + '</div>';
		html +='<div class="column small-9 message">' + message.msg + '</div>';
		html +='<div class="column small-1 msgDate text-right">' + time + '</div>';
		html +='</div>';

		cnt++;
	} ); // end each loop

	// Add messages to the bottom of the message list.
	if (isprepend) {
		$('.rightChatColumn .messageScrollArea .msg_history_load_more').remove();
		$('.rightChatColumn .messageScrollArea').prepend(html);
	}else{
		if (isappend) {
			$('.rightChatColumn .messageScrollArea').append(html);
		} else{
			$('.rightChatColumn .messageScrollArea').html(html);
		};
	}

	if (!msgScrollBox.hasScrollBar() && show_previous_msg == 1) {
		$('.rightChatColumn .messageScrollArea .msg_history_load_more').remove();
		html = '<div onclick="Plex.chat.getPreviousMessages();" class="column small-1 small-centered msg_history_load_more"><span class="column small-12 small-centered">Show Previous Messages</span></div>';
		$('.rightChatColumn .messageScrollArea').prepend(html);
	}

	Plex.chat.scrollMsgWindow();
	//this.updateMessageTimeStamps();

	//make new message sound whenever new chat comes in
	// newMessageAudio.makeNewMessageSound();
	// var path = window.location.pathname.split('/').pop().trim();
	// if( path != 'chat'){
	// 	newMessageAudio.makeNewMessageSound();
	// }

	//This saves the last message id so we can access it later during the NEW message load.
	this.lastMessageIdInTopic = $('.msgItem:last-child').data('message_id');
}
/***********************************************************************/

/***********************************************************************
 *===================== SCROLL MESSAGE WINDOW ==========================
 ***********************************************************************
 * Calculates the scrolled position of the chat/message window, and decides
 * if the window should be scrolled
 */
Plex.chat.scrollMsgWindow = function(){

	var messageColumn = $('.messageScrollArea');
  	var ScrollWindowHeight = messageColumn.height();
  	var scrollheight = messageColumn.prop('scrollHeight');
  	var scrollTo = scrollheight - ScrollWindowHeight;
  	var currentScroll = messageColumn.scrollTop();

  	if (this.forceWindowScroll  || currentScroll > (scrollheight - ScrollWindowHeight) - 200  ) {
  		this.scrollWindowTo(messageColumn,scrollTo);
  	};
}
/************************************************************************/

/***********************************************************************
 *=================== SCROLL MESSAGE WINDOW TO =========================
 ***********************************************************************
 * Scrolls the window given a jQuery element object, and scrollto distance
 * @param		elem		jquery object		The message box for chat/messages
 * @param		scrollTo	int					The distance to scroll the
 * 												message box to.
 */
Plex.chat.scrollWindowTo = function( elem , scrollTo){
	this.forceWindowScroll = false;
	elem.animate( { scrollTop: scrollTo }, 250 );
}
/************************************************************************/

/***********************************************************************
 *========================= BIND SEND CHAT =============================
 ***********************************************************************
 * Binds a keyup event (with delay) to check if the user has pressed enter.
 * We use a timeout to prevent excess check calling.
 */
Plex.chat.bindTextareaEnter = function(){
	var textarea = $( '.chattext' );
	textarea.keypress( function( e ){
		if( e.keyCode == 13 && !e.shiftKey){
			e.preventDefault();
			Plex.chat.sendChat();
		}
	} );
}
/***********************************************************************/

/***********************************************************************
 *============================ SEND CHAT ===============================
 ***********************************************************************
 * Sends a new message to the db. Sends 3 params:
 * @param		int		currentTopicIdSelected		THe thread id
 * @param		int		recipient_id				The id for the type of thread:
 * 													user id 101, college 101
 * @param		string	sticky_recipient_type					the thread type:
 * 													user/college etc.
 */
Plex.chat.sendChat = function(){
	//hide submit button and stop the heartbeat timer
	//this.toggleSendButton();
	//this.stopChatMessageHeartBeatTimer();

	// select and cache the chat textarea
	var chtTxt = $('.chattext');
	console.log( this.enterEnabled );

	//get the txt in the textbox.
	var txtbox = chtTxt.val();

	//Check if empty.
	if(txtbox == '' || Plex.chat.chatIsInit == false){
		return;
	}

	// don't send if !enterEnabled
	if( !this.enterEnabled ){
		return;
	}

	/* AT THIS POINT, WE'RE READY TO SEND THE MESSAGE
	 * we've passed all checks
	 */

	// toggle send button 'disabled'; don't allow further submits
	this.disableSubmit();
	// clear input field
	chtTxt.val( '' );
	// stop heartbeat and (later) re-start
	this.stopChatMessageHeartBeatTimer();

	console.log( 'SENDING MESSAGE!' );

	//get current contact selected 
	var recipient_id = this.recipient_id;

	// build url and query string
	//var _url = this.sendMessageUrl + this.currentTopicIdSelected + '/' + recipient_id + '/' + this.sticky_recipient_type ;
	// WE DON'T HAVE TO DO ANY SPECIAL LOGIC TO CHECK IF THE MESSAGE
	// WAS A NEW MESSAGE HERE, SINCE ADMIN ONLY USE THE MAIN CHAT VIEW
	// TO MESSAGE MAIN CHAT
	var post_url = this.sendMessageUrl;
	var post_data = {};
		// existing thread
		post_data.thread_id = this.currentTopicIdSelected;
		post_data.message = txtbox;

	// Send message
	$.ajax({
		method: 'POST',
		url: post_url,
		data: post_data,
		dataType: 'text',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	})
	.always( function(){
		// Enable submitting AFTER POST is done
		Plex.chat.enableSubmit();
	} );

	/*
	// make POST request
	$.post( _url , {'message': txtbox}, function(data, textStatus, xhr) {
		//Plex.chat.currentTopicIdSelected = data;
	} )
	.done( function(){
		// Enable submitting AFTER POST is done
		Plex.chat.enableSubmit();
	} );
	*/
	// immediately getTopicList and then restart heartbeat
	this.getTopicList( this.startChatMessageHeartBeatTimer );

	mixpanel.track("Message_Student_Chat",
		{
			"location": document.body.id
		}
	);
}
/************************************************************************/

/***********************************************************************
 *======================== CHECK CHAT READY ============================
 ***********************************************************************
 * Checks if a college chat thread is currently online. Only used on the user's
 * college page.
 */
Plex.chat.checkChatReady = function(){

	// get college id
	if (this.mainCollegeChatId == undefined ) {
		this.mainCollegeChatId = $('#collegeInfoArea').data('collegeid');
	};

	// make and package data
	var ajax_data = {};
	ajax_data.school_id = this.mainCollegeChatId;
	// perform AJAX call
	$.ajax({
		///*
		data: ajax_data,
		// url: '/helper/chat/islive.php',
		//*/
		url: '/ajax/college/chat/islive/' + this.mainCollegeChatId,
		type: 'GET',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	})
	.done(function(data) {

		console.log(Plex.chat.chatHeartbeatEnabled);
		console.log(data);

		// Added checking for undefined for chatHeartbeatEnabled. 
		if (data == 1 &&  (Plex.chat.chatHeartbeatEnabled == false  || typeof Plex.chat.chatHeartbeatEnabled == 'undefined') ) {
			// Turn the chat on
			$('.chatWrapper ').addClass('active');
			Plex.chat.chatHeartbeatEnabled = true;
			Plex.chat.startChatMessageHeartBeatTimer();
			Plex.chat.getTopicList();
		};

		if ( data == 0  && (Plex.chat.chatHeartbeatEnabled == true || typeof Plex.chat.chatHeartbeatEnabled == 'undefined') ) {
			// turn the chat off
			$('.chatWrapper ').removeClass('active');
			Plex.chat.chatHeartbeatEnabled = false;
			Plex.chat.stopChatMessageHeartBeatTimer();
		};
	});
};
/************************************************************************/

/***********************************************************************
 *===================== BIND SCROLL CHECK EVENT ========================
 ***********************************************************************
 * Checks to see if message area has been scrolled to the bottom. If so,
 * and if !ReadScrollDisabled, then we call the setThreadRead() method to
 * tell our DB that the user has read the last messages.
 */
Plex.chat.setReadMessageScrollChecker = function(){
	//binds the scroll for messages
	this.messageArea.scroll(function() {

		if (Plex.chat.ReadScrollDisabled) {
			return;
		};

		var _this = $(this);
		var boxheight = _this.height();
		var scrollTop = _this.scrollTop();
		var scrollHeight = _this.prop('scrollHeight');

		if ((scrollHeight-scrollTop) == boxheight) {
			Plex.chat.setThreadRead();
		};
		
	});
}
/***********************************************************************/

/***********************************************************************
 *===================== MARK THREAD AS READ ============================
 ***********************************************************************
 * Marks a given thread as read. makes AJAX call with thread id.
 */
Plex.chat.setThreadRead = function(){
	// checks if unread_count is > 1 first before continuing
	if( this.currentTopicNumUnread ){
		console.log( 'has unread' );
		// check if we're scrolled down
		var message_area = this.messageArea;
		console.log( message_area.hasScrollBar() );
		if( 
			!message_area.hasScrollBar() ||
			( message_area.scrollTop() + message_area.innerHeight() >= message_area[0].scrollHeight )
		){
			// create ajax url
			var _url = Plex.chat.topicReadUrl + Plex.chat.currentTopicIdSelected;

			$.ajax({
				url: _url,
				type: 'GET',
				dataType: 'html',
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			})
			.done(function() {
			});
		}
	}
}
/***********************************************************************/

/***********************************************************************
 *==================== START CHAT READY CHECKER ========================
 ***********************************************************************
 * Starts the chat ready checker NOT NEEDED FOR ADMIN PAGE
 */
/*
Plex.chat.startChatReadyChecker = function(){
	Plex.chat.checkChatReady();
	//set the timer!
	this.chatReadyTimer = setInterval( function(){
		Plex.chat.checkChatReady();
	}, 3000);
}
*/
/************************************************************************/

/***********************************************************************
 *==================== STOP CHAT READY CHECKER ========================
 ***********************************************************************
 * Stops the chat ready checker NOT NEEDED FOR ADMIN PAGE
 */
/*
Plex.chat.stopChatReadyChecker = function(){
	clearInterval(this.chatReadyTimer);
}
*/
/************************************************************************/

/***********************************************************************
 *======================= START CHAT HEARTBEAT =========================
 ***********************************************************************
 * Starts the heart beat timer for chat members and messages!
 */ 
Plex.chat.startChatMessageHeartBeatTimer = function(){
	// clear the timer if already set so we don't get duplicates
	if( this.hbtimer ) clearInterval( this.hbtimer );
	//set the timer!
	Plex.chat.hbtimer = setInterval( function(){
		Plex.chat.getTopicList();
	}, Plex.chat.chatMessageHeartBeatTime);
}
/***********************************************************************/

/***********************************************************************
 *======================== STOP CHAT HEARTBEAT =========================
 ***********************************************************************
 * Stops the heart beat timer for chat members and messages!
 */
Plex.chat.stopChatMessageHeartBeatTimer = function(){
	//stop the timer!
	clearInterval(Plex.chat.hbtimer);
}
/***********************************************************************/

/***********************************************************************
 *======================== ENABLE SUBMIT BUTTON ========================
 ***********************************************************************
 * Enables the submit button
 */
Plex.chat.enableSubmit = function(){
	Plex.chat.toggleSendButton(1);
	Plex.chat.enterEnabled = true;
}
/***********************************************************************/

/***********************************************************************
 *====================== DISABLE SUBMIT BUTTON =========================
 ***********************************************************************
 * Disables the submit button
 */
Plex.chat.disableSubmit = function(){
	Plex.chat.toggleSendButton();
	Plex.chat.enterEnabled = false;
}
/***********************************************************************/

/***********************************************************************
 *======================= TOGGLE SEND BUTTON ===========================
 ***********************************************************************
 * Toggles visibility of the send button
 */
Plex.chat.toggleSendButton = function( vis ){
	var button = this.sendButton;
	if ( typeof vis == 'undefined' || vis == 0 ) {
		if( !button.hasClass( 'disabled' ) ){
			button.addClass( 'disabled' );
		}
	} else{
		if( button.hasClass( 'disabled' ) ){
			button.removeClass( 'disabled' );
		}
	};
}
/***********************************************************************/

/***********************************************************************
 *================ TOGGLE CHAT/MESSAGES WINDOW VIEWS ===================
 ***********************************************************************
 * Switches between chat and messages view for admin only! Turns on/off
 * chat and message heartbeats
 */
Plex.chat.toggleChatAndMessageWindowsViews = function(windowType){
	console.log( Plex.messages.stickyEnabled );
	console.log( Plex.messages.sticky_recipient_id );

	if (Plex.messages.windowActiveNow == windowType) {
		console.log('You are all ready on this page.. EXIT!!');
		return;
	};

	//Remove active classes on chat and message windows 
	$('.chatMainWindow').removeClass('active');
	$('.messageMainWindow').removeClass('active');

	if (Plex.messages.windowActiveNow == 'admin-chat') {
		//Switches to message mode!!!!
		$('.messageMainWindow').addClass('active');
		Plex.messages.windowActiveNow = 'admin-messages';
		Plex.messages.startMessageHeartBeatTimer();
		Plex.messages.getTopicList();
	} else {
		//Switches to chat mode!!!!
		$('.chatMainWindow').addClass('active');
		Plex.messages.windowActiveNow = 'admin-chat';
		Plex.messages.stopMessageHeartBeatTimer();
	};

	// HIDE IDLE OVERLAYS. Needed for admin, since overlays
	$( '#messages_idle_overlay' ).hide( 'slide', { direction: 'left' }, 250 );
	$( '#chat_idle_overlay' ).hide( 'slide', { direction: 'left' }, 250 );
}
/***********************************************************************/

/***********************************************************************
 *======================= CHAT INIT HEARTBEAT ==========================
 ***********************************************************************
 * Heartbeat that lets the server know the college rep/admin is online.
 * The college facing page checks for init with the islive heartbeat
 */
Plex.chat.adminChatInitHeartBeat = function (){
	// create data object to pass to controller
	var ajax_data = {};
	// fill data object
	ajax_data.hashed_user_id = this.hashedUserId;

	if(Plex.chat.isAjaxRunning){
		return;
	}

	Plex.chat.isAjaxRunning = true;

	$.ajax({
		data: ajax_data,
		url: '/ajax/college/chat/init',
		// url: '/helper/chat/init.php',
		type: 'POST',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	})
	.done(function() {

		Plex.chat.isAjaxRunning = false;
		console.log("init done");
	});
}
/***********************************************************************/

/***********************************************************************
 *====================== ADMIN CHAT ONLINE STATUS ======================
 ***********************************************************************
 * Called when user selects an option from the chat online select field.
 * This is called onclick and turns on/off the init heartbeat
 */
Plex.chat.chatOnlineStatus = function(elem){
	var drpdown = $(elem);

	if (drpdown.val() == 1) {
		console.log('turning on chat');
		Plex.chat.getTopicList();
		Plex.chat.setAdminChatOnlineHeartbeat();
		Plex.chat.startChatMessageHeartBeatTimer();
		Plex.chat.chatIsInit = true;
		$(".chatbutton").removeClass('disabled');
		
	} else {
		console.log('turning off chat');
		Plex.chat.stopAdminChatOnlineHeartbeat();
		Plex.chat.stopChatMessageHeartBeatTimer();
		Plex.chat.chatIsInit = false;
		$(".chatbutton").addClass('disabled');
	};
}
/***********************************************************************/

/***********************************************************************
 *================= SET ADMIN CHAT ONLINE HEARTBEAT ====================
 ***********************************************************************
 * Sets the interval for admin init/ admin online heartbeat.
 */
Plex.chat.setAdminChatOnlineHeartbeat = function(){
	//set the timer!
	this.adminChatOnlineTicker = setInterval( function(){
		Plex.chat.adminChatInitHeartBeat();
	}, 3000);
}
/***********************************************************************/

/***********************************************************************
 *==================== STOP ADMIN ONLINE HEARTBEAT =====================
 ***********************************************************************
 * Stops the admin online/init heartbeat. Called when user selects 'offline'
 * from the admin online select element
 */
Plex.chat.stopAdminChatOnlineHeartbeat = function(){
	//stop the timer!
	clearTimeout(this.adminChatOnlineTicker);
}
/***********************************************************************/

/***********************************************************************
 *======================== CHECK MESSAGES VIEW =========================
 ***********************************************************************
 * Check if page loaded is messages view. If so, start messages heartbeat.
 */
Plex.chat.checkMessagesView = function(){
	if (Plex.messages.windowActiveNow == 'admin-messages') {
		console.log('messages url selected. Starting messages heartbeat');
		Plex.messages.getTopicList();
		Plex.messages.startMessageHeartBeatTimer();
		return true;
	};

}
/***********************************************************************/



/*$(document).on('mouseenter', '.prof-tooltip.has-tip', function(e){
	e.preventDefault();	
	console.log('shouldnt do anything');
	$('#admin-chat .tooltip').is(':visible').show();
});*/

$(document).on('mouseout', '.prof-tooltip.has-tip', function(e){
	e.preventDefault();	
	$(this).addClass('open');
	$('#admin-chat .tooltip').hide();
});

// $(document).on('mouseenter', '#admin-chat .tooltip', function(){
// 	$('.prof-tooltip.open').show();
// 	Foundation.libs.tooltip.getTip($('.prof-tooltip.open')).show();
// });

/*$(document).on('click', '.prof-tooltip.has-tip', function(){

	if( $(this).hasClass('open') ){
		Foundation.libs.tooltip.getTip($(this)).hide();	
		$(this).removeClass('open');
	}else{
		Foundation.libs.tooltip.getTip($(this)).show();
		$(this).addClass('open');
	}
});*/


//when user topic list item info icon is clicked, show profile quickview
$(document).on('mouseover', '.prof-tooltip', function(){

	var _this = this;
	var toolTip = $(this);
	var prof_template = '';
	var uid = $(this).data('uid');
	var url = '/admin/ajax/getStudentsInfo';
	var tooltip_exists = Plex.chat.userTooltip_array['key'+uid];

	if( tooltip_exists == undefined){
		$.ajax({
			url: url,
			type: 'POST',
			data: {userId: uid},
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		}).done(function(data){

			prof_template = Plex.chat.profileTooltipTemplate(data);

			Plex.chat.userTooltip_array['key'+uid] = prof_template;
			
			Foundation.libs.tooltip.getTip(toolTip).remove();
			$(toolTip).removeAttr('title').attr('title', prof_template);
			$(toolTip).foundation('tooltip', 'reflow');
			$(document).foundation('tooltip', 'reflow');
		});
	}

});


//user topic list tooltip template
Plex.chat.profileTooltipTemplate = function(dt){
	var template = '';
	var tmpry = '';

	template += "<div class='row'>";
	template += 	"<div class='column small-12'>";

	//user personal info section
	template += 		"<div class='row tip-personalinfo-row'>";
	template += 			"<div class='column small-3'>"
	template += 				"<div><img src='"+dt.profile_img_loc+"' alt='' /></div>";
	template += 			"</div>";

	template += 			"<div class='column small-9'>"
	template += 				"<div><b>"+dt.fname + " " + dt.lname+"</b></div>";
	template += 				"<div class='flag flag-"+dt.country_code.toLowerCase()+"'>&nbsp;</div>";
	template += 				"<div>"+dt.current_school+"</div>";
	template += 				"<div>"+dt.country_name+"</div>";
	template += 				"<div>Grad Date: "+dt.grad_year+"</div>";
	template += 			"</div>";
	template += 		"</div>";

	//user objective section
	template += 		"<div class='row tip-objective-row'>";
	template += 			"<div class='column small-12'>";
	template += 				dt.objective;
	template += 			"</div>";
	template += 		"</div>";

	//user stats chart section
	template += 		"<div class='row tip-table-row'>";
	template += 			"<div class='column small-12'>";

	template += 				"<table>";
	template += 					"<tbody>";
	template += 						"<tr><td><b>GPA</b></td><td>"+ dt.gpa +"</td></tr>";
	template += 						"<tr><td><b>SAT</b></td><td>"+dt.sat_score+"</td></tr>";
	template += 						"<tr><td><b>ACT</b></td><td>"+dt.act_composite+"</td></tr>";
	template += 						"<tr><td><b>TOEFL</b></td><td>"+dt.toefl_total+"</td></tr>";
	template += 						"<tr><td><b>IELTS</b></td><td>"+dt.ielts_total+"</td></tr>";
	template += 					"</tbody>";
	template += 				"</table>";

	template += 			"</div>";
	template += 		"</div>";

	//financials for first year and uploads section
	template += 		"<div class='row tip-uploads-row'>";
	template += 			"<div class='column small-12'>";
	template += 				"<div class='tip-financial-amt'><b>FINANCIALS FOR FIRST YEAR: "+dt.financial_firstyr_affordibility+"</b></div>";
	if (typeof dt.uploads[0] !== 'undefined' && dt.uploads[0] !== null) {
	template += 				"<div><b>UPLOADS</b></div>";
	}
	$.each(dt.uploads, function(index, val) {
	template += 				"<div class='column small-6'>"+val+"</div>";
	});
	template += 			"</div>";
	template += 		"</div>";

	template += 	"</div>";
	template += "</div>";

	tmpry += "<div class='row'><div class='column small-12'>a row here</div></div>";
	tmpry += "from ";
	tmpry += "this ";
	tmpry += "functions... ";

	return template;	
}
/***********************************************************************/
Plex.chat.loadMessageTemplates = function(elem, type){

	if (type == 'msg'){
		var message_template_dropdown = $('#message_template_dropdown_msg');
		var txtArea = $('.msgtext');
	}
	if (type == 'chat'){
		var message_template_dropdown = $('#message_template_dropdown_chat');
		var txtArea = $('.chattext');
	}

	if (message_template_dropdown.val() != '') {
		$.ajax({
            url: '/ajax/loadMessageTemplates',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            data: {id: message_template_dropdown.val(), txtOnly: 1},
            type: 'POST',
            headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
        }).done(function(data, textStatus, xhr) {
			txtArea.val(data.content);
		});
	}
}

// load message template 
$(document).on('change', '#message_template_dropdown_chat', function(){
	var _this = $(this);
	Plex.chat.loadMessageTemplates(_this, 'chat');
});
// load message template 
$(document).on('change', '#message_template_dropdown_msg', function(){
	var _this = $(this);
	Plex.chat.loadMessageTemplates(_this, 'msg');
});


Plex.chat.openSaveMessageTemplates = function(){

	$('#save-template-modal').foundation('reveal', 'open');

};

// open the save message template modal
$(document).on('change', '#insert_message_template', function(){
	if( $(this).is(':checked') ){
		Plex.chat.openSaveMessageTemplates();
	}
});

$(document).on('click', '#save-template-modal .close-reveal-modal', function () {
	$('#insert_message_template').prop('checked', false);
});


Plex.chat.saveMessageTemplates = function(elem){

	var template_name = $('#template_name').val();
	var message_template_dropdown_chat = $('#message_template_dropdown_chat');
	var message_template_dropdown_msg = $('#message_template_dropdown_msg');

	//are we messaging or chatting
	//save text dependent on
	if(Plex.chat.chatIsInit == true){
		var txtArea = $('.chattext').val();
		var chattext = txtArea;
	}else{
		var txtArea = $('.msgtext').val();
		var msgtext = txtArea;
	}

	if (txtArea == '') {
		if (msgtext != '') {
			txtArea = msgtext;
		}else{
			txtArea = chattext;
		}
	}

	$.ajax({
        url: '/ajax/saveMessageTemplates',
        data: {name: template_name, content: txtArea},
        type: 'POST',
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
    }).done(function(data, textStatus, xhr) {
		var txt = '<option value="'+data.id+'">'+data.name+'</option>';
		var check = false;
		var _data = data;
		$("#message_template_dropdown_msg > option").each(function() {
		    if (this.text == _data.name) {
		    	check = true;
		    }
		});

		if (check === false) {
			message_template_dropdown_chat.append(txt);
			message_template_dropdown_msg.append(txt);
		}
		
		$('#insert_message_template').prop('checked', false);
		$('#save-template-modal').foundation('reveal', 'close');
		
	});

};

// save message template 
$(document).on('click', '.save-template-btn', function(){
	Plex.chat.saveMessageTemplates($(this));
});


//let this program know whether we are chatting or messaging
$(document).on('click', '.mainChatButton', function(){
	Plex.chat.chatIsInit = true;

});
$(document).on('click', '.privateChatButton', function(){
	Plex.chat.chatIsInit = false;
});