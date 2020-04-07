//Only Holds messages calls.
/***********************************************************************
 *===================== NAMESPACED VARIABLES ===========================
 ***********************************************************************
 * Holds namespaced variables for chat
 */
Plex.chat = {
	windowActiveNow : undefined,
	chatIsInit:false,
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

	firstMessageIdInTopic : undefined,

	//bottomMessageReadGap : 10,
	chatMessageHeartBeatTime : 5000,
	forceWindowScroll : true,
	//sticky_recipient_id : undefined,
	//stickyEnabled : false,
	getMessagesUrl : '/ajax/college/chat/getNewMsgs/',
	getUserNewTopicsUrl : '/ajax/college/chat/threadHeartBeat',
	sendMessageUrl : '/ajax/messaging/postMsg', // new single POST message route
	topicReadUrl : '/portal/ajax/messages/setMsgRead/',
	
	getMsgsHistoryUrl : '/ajax/messaging/getHistoryMsg/',
	noMoreHistoryMsg : false,

	hashedUserId : $( '.leftChatColumn' ).data( 'hasheduid' ),
	messageArea : $( '.messageScrollArea' ),
	sendButton : $( '.chatTxtbox' ).children( '.row' ).find( '.sendbutton' ),
	signedIn : undefined, // only for student facing chat. Picks up a
						  // signed_in data attribute from the chat view
	notSignedInMessage : undefined, // tells user they're not signed in
	chatOfflineMessage : undefined, // tells user chat is offline
	enterEnabled : true,
	idleChecker : undefined,
	sticky_thread_type : undefined,

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
	console.log( 'initializing chat items...' );
	this.cacheChatItems();
	this.bindTextareaEnter();
	this.setReadMessageScrollChecker();
	this.setIdleChecker();
}
/***********************************************************************/

/***********************************************************************
 *===================== SET IDLE CHECKER/TIMER =========================
 ***********************************************************************
 * Sets an idle checker to switch the heartbeat on/off
 */
Plex.chat.setIdleChecker = function(){
	var _this = this;
	this.idleChecker = $( document ).idle( { 
		onIdle: function(){
			console.log( 'chat idle!' );
			_this.stopChatMessageHeartBeatTimer();
			_this.stopChatReadyChecker();
			$( '#chat_idle_overlay' ).show( 'slide', { direction: 'left' }, 250 );
		},
		onActive: function(){
			console.log( 'chat active!' );
			_this.stopChatMessageHeartBeatTimer();
			// get topic list and messages, THEN setInterval for heartbeat
			_this.getTopicList( _this.startChatMessageHeartBeatTimer );

			// stop and re-start islive check
			_this.stopChatReadyChecker();
			_this.startChatReadyChecker();
			$( '#chat_idle_overlay' ).hide( 'slide', { direction: 'left' }, 250 );
		},
		//idle: 600000 // idle after 10 minutes
		idle: 300000 // idle after 5 minutes
		//idle: 5000 // idle after 5 seconds
	} );
}
/***********************************************************************/

/***********************************************************************
 *========================= CACHE CHAT ITEMS ===========================
 ***********************************************************************
 * Caches chat items on document ready. This is ALSO called on ajax load
 * of the chat section, since some elements aren't on the page until then.
 * ( college.js ) loadCollegeInfo()
 */
Plex.chat.cacheChatItems = function(){
	this.hashedUserId = $( '.leftChatColumn' ).data( 'hasheduid' );
	this.messageArea = $( '.messageScrollArea' );
	this.sendButton = $( '.chatTxtbox' ).children( '.row' ).find( '.sendbutton' );
	// chat switch button's unread count element
	this.mainChatCount = $( '.switchbuttons-wrapper' )
		.children( '.switchbuttons.mainChatButton' )
		.children( '.unread_count' );
	// used to check user's signed in status
	this.signedIn = $( '.chatWrapper' ).data( 'signed_in' );
	// cached student-facing 'not signed in' or 'chat offline' message elements
	this.notSignedInMessage = $( '#notSignedInMessage' );
	this.chatOfflineMessage = $( '#chatOfflineMessage' );
	/* we need to pass in college id in a way that works on both regular
	 * pageload and ajax page load. So we stuff the value in a div and grab
	 * it on chat init. I copied the way we pass data to share buttons
	 * for this. - AW */
	this.college_id = $( '#chat_div_of_holding' ).data( 'college_id' );
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

	if( typeof this.sticky_thread_type == 'undefined' ){
		this.sticky_thread_type = elem.data( 'sticky_thread_type' ) ? elem.data( 'sticky_thread_type' ) : 'chat-msg';
	}
}
/************************************************************************/

/***********************************************************************
 *========================== CHANGE TOPIC ==============================
 ***********************************************************************
 * Triggered when user clicks a contact/thread/chat in the contact list.
 * Clears message area. Changes selected thread.
 */
Plex.chat.changeTopic = function (elem){
	var recipient_id = $(elem).data('recipient_id');
	var thread_id = $(elem).data('topicid')
	var messageContacts = $('.leftChatColumn');
	var messageScrollArea = $('.rightChatColumn .messageScrollArea');

	// Send to direct messaging
	if (recipient_id !== -1) {
		Plex.chat.switchToMessages(thread_id);
	}

	messageScrollArea.html('');
	messageContacts.find('.chatUser').removeClass('selected');
	// resets values for getDataInfoFromSelectedItem
	this.currentTopicIdSelected = undefined;
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


//{org_id?}/{type?}/{thread_id?}
Plex.chat.switchToMessages = function(thread_id){
	var college_id = $('#chat_div_of_holding').data('college_id');

	window.open('/portal/messages/' + college_id + '/inquiry-msg/' + thread_id, '_blank');
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

	$.each(data.topicUsr, function(index, val) {
		//if ( index == 0 &&  typeof Plex.chat.currentTopicIdSelected == 'undefined'   ) {
			//Plex.chat.currentTopicIdSelected = val.thread_id;
		//};

		html +='<div class="column small-12 chatUser"'
			+ ' data-topicid="' + val.thread_id + '"'
			+ ' data-recipient_id="' + val.thread_type_id + '"'
			+ ' data-num_unread="' + val.num_unread_msg + '"'
			+ ' data-sticky_recipient_type="' + val.thread_type + '"'
			+ ' onclick="Plex.chat.changeTopic(this);">';
        html +=  val.Name;
		html +=		'<span class="unread_count">';
		html += 		val.num_unread_msg;
		html += 	'</span>';
        html += '</div>';
	});

	// find element to modify
	var leftMessageColumn = $('.leftChatColumn .chatUserLists');
	leftMessageColumn.html(html);

	// default action: set first/top thread in list as active if no thread selected
	if ( typeof Plex.chat.currentTopicIdSelected == 'undefined' ){
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
/***********************************************************************/

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

	if(Plex.chat.isAjaxRunningGetMessages){
		return;
	}

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

/***********************************************************************/

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
		Plex.chat.isPrevMessageRunning = false;
		data = JSON.parse(data.msg);
		
		if (data.length == 0) {
			Plex.chat.noMoreHistoryMsg = true;
			$('.rightChatColumn .messageScrollArea .msg_history_load_more').remove();
		}else{
			_this.addmessages(data,isappend, isprepend);
		}
	});
}

/************************************************************************/

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
		if(cnt != 0){
			var last_to_talk = Plex.chat.lastMessageUserInTopic;
			Plex.chat.lastMessageUserInTopic = message.full_name;
			if( last_to_talk == message.full_name ) hide_user = ' hideUser';
		}

		// GET TIME
		// var time = Plex.common.get_timestamp_time( message.date );
		var time = message.time;

		// HIDE REPEAT TIMESTAMPS
		var hide_time = '';
		if(cnt != 0){
			var last_timestamp = Plex.chat.lastMessageTimestampInTopic;
			Plex.chat.lastMessageTimestampInTopic = time;
			if( last_timestamp == time ) hide_time = ' hideTime';
		}

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

	//get the txt in the textbox.
	var txtbox = chtTxt.val();

	//Check if empty.
	if(txtbox == ''){
		//Plex.messages.toggleSendButton(1);
		//Plex.messages.startChatMessageHeartBeatTimer();
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
	var post_url = this.sendMessageUrl;
	var post_data = {};
	// existing thread
	post_data.thread_id = this.currentTopicIdSelected;
	post_data.message = txtbox;
	// if new thread
	if( this.currentTopicIdSelected == -1 ){
		// set to user
		// set college_id or user_id based on sticky_recipient_type
		switch ( this.sticky_recipient_type ){
			case 'user':
			case 'users':
				post_data.to_user_id = this.recipient_id;
				post_data.college_id = this.college_id; // generated on page load. global
				break;
			case 'college':
			case 'colleges':
				post_data.to_user_id = '';
				post_data.college_id = this.recipient_id;
				break;
			default:
				console.log( 'we need a sticky recipient type in order to create a thread!' );
		}

		// set thread type, default to chat-msg
		post_data.thread_type = typeof this.sticky_thread_type != 'undefined' ? this.sticky_thread_type : 'chat-msg';
		// reset thread type to undefined ( to prevent errors )
		this.sticky_thread_type = undefined;
	}

	// send message
	$.ajax({
		method: 'POST',
		url: post_url,
		data: post_data,
		dataType: 'text',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		success: function( data ){
			// we expect the thread id to be returned, whether or not it is new.
			Plex.chat.currentTopicIdSelected = data;
		}
	})
	.always( function(){
		// Enable submitting AFTER POST is done
		Plex.chat.enableSubmit();
	} );

	// immediately getTopicList and then restart heartbeat
	this.getTopicList( this.startChatMessageHeartBeatTimer );

		/*
		// make POST request
		$.post( _url , {'message': txtbox}, function(data, textStatus, xhr) {
			//Plex.chat.currentTopicIdSelected = data;
		} )
		.done( function(){
			// Enable submitting AFTER POST is done
			Plex.chat.enableSubmit();
		} );
		// immediately getTopicList and then restart heartbeat
		this.getTopicList( this.startChatMessageHeartBeatTimer );
		*/

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
		
		data: ajax_data,
		// url: '/helper/chat/islive.php',
		
		url: '/ajax/college/chat/islive/' + this.mainCollegeChatId,
		type: 'GET',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	})
	.done(function(data) {

		console.log(Plex.chat.chatHeartbeatEnabled);
		console.log(data);

		// Added checking for undefined for chatHeartbeatEnabled. 
		if (data == 1 &&  (Plex.chat.chatHeartbeatEnabled == false  || typeof Plex.chat.chatHeartbeatEnabled == 'undefined') ) {
			console.log( 'chat is live' );

			// only allow chat if signed in
			if( !Plex.chat.signedIn ){
				// show/hide the correct message and action
				Plex.chat.notSignedInMessage.addClass( 'active' );
				Plex.chat.chatOfflineMessage.removeClass( 'active' );
				return false;
			}

			// Turn the chat on
			$('.chatWrapper ').addClass('active');
			// hide notifiers
			Plex.chat.notSignedInMessage.removeClass( 'active' );
			Plex.chat.chatOfflineMessage.removeClass( 'active' );

			Plex.chat.chatHeartbeatEnabled = true;
			Plex.chat.startChatMessageHeartBeatTimer();
			Plex.chat.getTopicList();
		}

		// we need to always do this when data == 0; chatHeartbeatEnabled won't always be true
		if( data == 0 ){
			// show/hide the correct message in place of chat
			Plex.chat.notSignedInMessage.removeClass( 'active' );
			Plex.chat.chatOfflineMessage.addClass( 'active' );

			if(Plex.chat.chatHeartbeatEnabled == true || typeof Plex.chat.chatHeartbeatEnabled == 'undefined'){
				// turn the chat off
				$('.chatWrapper ').removeClass('active');
				Plex.chat.chatHeartbeatEnabled = false;
				Plex.chat.stopChatMessageHeartBeatTimer();
			}
		}
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
		// check if we're scrolled down
		var message_area = this.messageArea;
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
 * Starts the chat ready checker
 */
Plex.chat.startChatReadyChecker = function(){
	Plex.chat.checkChatReady();
	//set the timer!
	Plex.chat.chatReadyTimer = setInterval( function(){
		Plex.chat.checkChatReady();
	}, 3000);
}
/************************************************************************/

/***********************************************************************
 *==================== STOP CHAT READY CHECKER ========================
 ***********************************************************************
 * Stops the chat ready checker
 */
Plex.chat.stopChatReadyChecker = function(){
	clearTimeout(Plex.chat.chatReadyTimer);
}
/************************************************************************/

/***********************************************************************
 *======================= START CHAT HEARTBEAT =========================
 ***********************************************************************
 * Starts the heart beat timer for chat members and messages!
 */ 
Plex.chat.startChatMessageHeartBeatTimer = function(){
	// clear the timer if already set so we don't get duplicates
	if( Plex.chat.hbtimer ) clearInterval( Plex.chat.hbtimer );
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
