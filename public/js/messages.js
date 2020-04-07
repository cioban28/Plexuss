//Only Holds messages calls.
/***********************************************************************
 *===================== NAMESPACED VARIABLES ===========================
 ***********************************************************************
 * Holds namespaced variables for messages
 */
Plex.messages = {
	windowActiveNow: undefined,
	enterEnabled : true,
	currentTopicIdSelected : undefined,
	currentTopicNumUnread : undefined,
	recipient_id : undefined,
	sticky_recipient_type : undefined,
	
	firstMessageIdInTopic : undefined,

	lastMessageIdInTopic : undefined,
	lastMessageDateInTopic : undefined,
	lastMessageUserInTopic : undefined,
	lastMessageTimestampInTopic : undefined,

	bottomMessageReadGap : 10,
	messageheartbeattime : 5000,
	ReadScrollDisabled : true,
	sticky_recipient_id : undefined,
	stickyEnabled : false,
	//getMessagesUrl : 'Please add one in the script that calls this.'
	//getUserNewTopicsUrl : 'Please add one in the script that calls this.'
	sendMessageUrl : '/ajax/messaging/postMsg', // new single POST message route
	topicReadUrl : '/admin/ajax/messages/setMsgRead/',
	getMsgsHistoryUrl : '/ajax/messaging/getHistoryMsg/',
	noMoreHistoryMsg : false,
	messageArea : undefined,
	sendButton : undefined,
	idleChecker : undefined,
	messageWindow : undefined,
	sticky_thread_type : undefined,

	isAjaxRunning: false,
	isPrevMessageRunning: false,

	leftHandsideMessagesTopics: undefined,
	isText: false,
};
/************************************************************************/

/***********************************************************************
 *========================= RUN ON PAGELOAD ============================
 ***********************************************************************/
$( document ).ready( function(){
	Plex.messages.initialize();
	Plex.messages.isText();

	$(".rightMessageColumn .msgScrollBox").scroll(function()
    {
        var div = $(this);
        if(div.scrollTop() == 0 && Plex.messages.noMoreHistoryMsg == false){
        	Plex.messages.getPreviousMessages();
        }
    });
} );
/***********************************************************************/

/***********************************************************************
 *==================== METHODS TO RUN ON PAGE LOAD =====================
 ***********************************************************************
 * Methods that need to be run on page load or after elements are injected
 * onto the page via AJAX
 */
Plex.messages.initialize = function(){
	this.cacheMessagesItems();
	this.bindTextareaEnter();
	this.setReadMessageScrollChecker();
	this.setIdleChecker();
	//this.
}
/***********************************************************************/

/***********************************************************************
 *===================== SET IDLE CHECKER/TIMER =========================
 ***********************************************************************
 * Sets an idle checker to switch the heartbeat on/off
 */
Plex.messages.setIdleChecker = function(){
	// forbid multiple instances of idle checker
	if( this.idleChecker ){
		console.log( 'messages idle checker already exists!' );
		return;
	}
	var _this = this;
	this.idleChecker = $( document ).idle( { 
		onIdle: function(){
			// console.log( 'messages idle!' );
			_this.stopMessageHeartBeatTimer();
			$( '#messages_idle_overlay' ).show( 'slide', { direction: 'left' }, 250 ).closest('.idle_overlay_wrapper').css({'z-index':'2'});
		},
		onActive: function(){
			// check if messages window is visible before starting heartbeat
			var message_window = $( '.messageMainWindow' );
			if( !message_window.length || message_window.is( ':hidden' ) ){
				// console.log( 'message window is not visible!' );
				return;
			}
			// console.log( 'messages active!' );
			_this.stopMessageHeartBeatTimer();
			// get topic list and messages, THEN setInterval for heartbeat
			_this.getTopicList( _this.startMessageHeartBeatTimer );
			$( '#messages_idle_overlay' ).hide( 'slide', { direction: 'left' }, 250 ).closest('.idle_overlay_wrapper').css({'z-index':'1'});
		},
		idle: 600000 // idle after 10 minutes
		//idle: 300000 // idle after 5 minutes
		//idle: 60000 // idle after 1 minute
		//idle: 5000 // idle after 5 seconds
	} );
}
/***********************************************************************/

/***********************************************************************
 *======================= CACHE MESSAGES ITEMS =========================
 ***********************************************************************
 * Caches messages items on document ready
 */
Plex.messages.cacheMessagesItems = function(){
	this.messageArea = $( '.msgScrollBox' );
	this.sendButton = $( '.messageTxtbox' ).children( '.row' ).find( '.sendbutton' );
	// chat switch button unread count
	this.mainMessagesCount = $( '.switchbuttons-wrapper' )
		.children( '.switchbuttons.privateChatButton' )
		.children( '.unread_count' );
}
/***********************************************************************/

/***********************************************************************
 *========================== CHANGE TOPIC ==============================
 ***********************************************************************
 * Triggered when user clicks a contact/thread/chat in the contact list.
 * Clears message area. Changes selected thread.
 */
Plex.messages.changeTopic = function (elem){
	var messageContacts = $('.leftMessageColumn');
	var rightMessageArea = $('.rightMessageColumn');
	var contact = $(elem);
	var is_text = contact.data('is-text');

	if( is_text && +is_text ) Plex.messages.enableTextMessaging();
	else Plex.messages.disableTextMessaging();

	//set the variables needed
	this.currentTopicIdSelected = contact.data('topicid');
	this.currentTopicNumUnread = contact.data( 'num_unread' );
	this.recipient_id = contact.data('recipient_id');
	this.sticky_recipient_type = contact.data('sticky_recipient_type');
	this.lastMessageIdInTopic = undefined;
	this.firstMessageIdInTopic = undefined;
	this.noMoreHistoryMsg = false;
	// clear the lastMessageDateInTopic so script can get a new one
	this.lastMessageDateInTopic = undefined;
	// clear last user to enter a message
	this.lastMessageUserInTopic = undefined;
	// clear last timestamp
	this.lastMessageTimestampInTopic = undefined;

	//reset the left column making the new topic selected
	messageContacts.find('.messageContacts').removeClass('selected');

	var readMsgActive = messageContacts.find('.messageReadActive');

	if (readMsgActive !== undefined) {
		readMsgActive.removeClass('messageReadActive');
		readMsgActive.addClass('messageRead');

		var readMsg = contact.find('.messageRead');
		readMsg.removeClass('messageRead');
		readMsg.addClass('messageReadActive');
	}
	
	
	rightMessageArea.find('.msgScrollBox').html('');
	contact.addClass('selected');

	this.getMessages();
	this.setThreadRead();
}
/***********************************************************************/

/***********************************************************************
 *================= GET DATA INFO FROM SELECTED ITEM ===================
 ***********************************************************************
 * Accepts a thread object and collects all needed information
 * @param		elem		object			Represents a topic/thread. Used
 * 											to switch between different threads
 */
Plex.messages.getDataInfoFromSelectedItem = function (elem){

	//Only set currentTopicIdSelected if its Undefined!!!
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
Plex.messages.getTopicList = function ( callback ){
	//This updates the FULL topic list.
	var _this = this;
	var _url = '';

	if(Plex.messages.isAjaxRunning){
		return;
	}

	Plex.messages.isAjaxRunning = true;

	//We check if we pass in the sticky id.
	if (this.stickyEnabled) {
		var leftMessageColumn = $('.leftMessageColumn .usersArea');
		var firstcontact = leftMessageColumn.find('.messageContacts:first-child');
		this.getDataInfoFromSelectedItem(firstcontact);

		_url = this.getUserNewTopicsUrl  +'/' + _this.sticky_recipient_id + '/' + _this.sticky_recipient_type;
	} else {
		_url = this.getUserNewTopicsUrl;
	};

	$.ajax({
		url: _url,
		type: 'GET',
		dataType: 'json',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	})
	.done(function( data ) {
		Plex.messages.isAjaxRunning = false;

		if (Plex.messages.leftHandsideMessagesTopics !== data.topicUsr) {
			Plex.messages.leftHandsideMessagesTopics = data.topicUsr;
			_this.addTopicList(data);
		}
	});

	// call callback if set
	if( typeof callback != 'undefined' ){
		callback();
	}
	
}

Plex.messages.loadMoreMessages = function ( callback ){

	//This updates the FULL topic list.
	var _this = this;
	var _url = '';

	Plex.messages.isAjaxRunning = true;

	//We check if we pass in the sticky id.
	if (this.stickyEnabled) {
		var leftMessageColumn = $('.leftMessageColumn .usersArea');
		var firstcontact = leftMessageColumn.find('.messageContacts:first-child');
		this.getDataInfoFromSelectedItem(firstcontact);

		_url = this.getUserNewTopicsUrl  +'/' + _this.sticky_recipient_id + '/' + _this.sticky_recipient_type  + '?loadMore=true';
	} else {
		_url = this.getUserNewTopicsUrl  + '?loadMore=true';
	};

	$('.loading-more img').show();

	$.ajax({
		url: _url,
		type: 'GET',
		dataType: 'json',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	})
	.done(function( data ) {
		$('.loading-more').hide();
		Plex.messages.isAjaxRunning = false;
		if (Plex.messages.leftHandsideMessagesTopics !== data.topicUsr) {
			Plex.messages.leftHandsideMessagesTopics = data.topicUsr;
			_this.addTopicList(data);
		}
	});

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
Plex.messages.addTopicList = function (data){
	var html = '', i = 0, _this = this;

	var thread_total_count = 0;
	var this_thread_total_count = 0;

	$.each(data.topicUsr, function(index, val) {
		//get the count of thread_members
		var tread_member_cnt =  val.thread_members.length;
		var has_unread = parseInt( val.num_unread_msg ) ? ' has_unread' : '';

		thread_total_count = val.thread_total_count;
		this_thread_total_count++;

		var activeCampaign = '';

		if (val.has_text == 1) {
			activeCampaign = 'activeText';
		}else if (val.is_campaign == 1) {
			activeCampaign = 'activeCampaign';
		}
		if (tread_member_cnt > 1) {

			//this is a group chat
			html +=	'<div class="row messageContacts' 
				+ has_unread + ' '
				+ activeCampaign + '"'
				+ ' data-num_unread="' + val.num_unread_msg + '"'
				+ ' data-topicid="' + val.thread_id + '"'
				+ ' data-recipient_id="' + val.thread_type_id + '"'
				+ ' data-sticky_recipient_type="' + val.thread_type  + '"'
				+ ' data-is-text="' + val.has_text  + '"'
				+ ' onclick="Plex.messages.changeTopic(this);">';
			html +=		'<div class="column">';
			html +=			'<div class="row">';

			//for group chats we will loop tp print out all the images of users in threads.
			$.each(val.thread_members, function(key, val2) {
				var x = key+1;
				var end = '';

				if (x == tread_member_cnt) {
					end = 'end';
				} else{
					end = '';
				};

				html +=	'<div class="column small-2 ' + end + '">';
				html +=		'<img src="'+ val2.img +'" alt="'+ val2.Name +'" title="'+ val2.Name +'" >';
				html +=	'</div>';
			});

			html +=			'</div>';
			html +=		'</div>';
			html +=		'<div class="column small-7">';
			html +=			'<div class="row">';
			html +=				'<div class="column small-12 text-left messageName">'+ val.Name +'</div>';
			html +=				'<div class="column small-12 text-left messageSample">'+ val.msg +'</div>';
			html +=			'</div>';
			html +=		'</div>';
			var messagesUnread;
			if (val.num_unread_msg == 0) {
				messagesUnread = '&nbsp;';
			} else{
				messagesUnread = val.num_unread_msg;
			};

			if (val.msg_read_time != -1 && val.msg_read_time !== undefined) {
				if( _this.stickyEnabled && index === 0 && ((''+val.num_unread_msg) === '0') && val.msg_read_time === 'Read' ){
					html +=		'<div class="column small-12 messageRead inline hidden">';
				}else{
					html +=		'<div class="column small-12 messageRead inline">';
				}
					html +=          '<span>'+val.msg_read_time+'</span>';
					html +=     '</div>';
			}else{
				html +=		'<div class="column small-3 messageDate inline">'+ val.formatted_date +'</div>';
			}
			html +=	'</div>';
		} else{
			//This is a one to one chat.
			//using a loop to keep it clean.

			html +=	'<div class="row messageContacts' 
				+ has_unread + ' '
				+ activeCampaign + '"'
				+ ' data-num_unread="' + val.num_unread_msg + '"'
				+ ' data-topicid="' + val.thread_id + '"'
				+ ' data-recipient_id="' + val.thread_type_id + '"'
				+ ' data-sticky_recipient_type="' + val.thread_type + '"'
				+ ' data-is-text="' + val.has_text + '"'
				+ ' onclick="Plex.messages.changeTopic(this);">';
			html +=		'<div class="column small-2">';
			html +=			'<img src="'+ val.img +'" alt="'+ val.Name +'" title="'+ val.Name +'">';
			html += 	'</div>';
			html +=		'<div class="column small-7">';
			html +=			'<div class="row">';
			html +=				'<div class="column small-12 text-left messageName">'+ val.Name +'</div>';
			html +=				'<div class="column small-12 text-left messageSample">'+ val.msg +'</div>';
			html +=			'</div>';
			html +=		'</div>';

			var messagesUnread;
			if (val.num_unread_msg == 0) {
				messagesUnread = '&nbsp;';
			} else{
				messagesUnread = val.num_unread_msg;
			};

			if (val.msg_read_time != -1 && val.msg_read_time !== undefined) {
				if( _this.stickyEnabled && index === 0 && ((''+val.num_unread_msg) === '0') && val.msg_read_time === 'Read' ){
					html +=		'<div class="column small-12 messageRead inline hidden">';
				}else{
					html +=		'<div class="column small-12 messageRead inline">';
				}
					html +=          '<span>'+val.msg_read_time+'</span>';
					html +=     '</div>';
			}else{
				html +=		'<div class="column small-3 messageDate inline">'+ val.formatted_date +'</div>';
			}
			html +=	'</div>';

		};
	});
	
	if (thread_total_count > 10) {
		if (this_thread_total_count >= thread_total_count) {
			html += '<div class="row messageContacts">';
			html +=		'<div class="column small-12 text-center noMoreMessages">No more results</div>';
			html += '</div>';
		}else{
			html += '<div class="row messageContacts loadmore-row">';
			html +=		'<div class="column small-12 text-center loadMoreMessages" onclick="Plex.messages.loadMoreMessages(this);"><span class="loading-more"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/loading.gif" alt="loading gif" /></span>Show more results</div>';
			html += '</div>';
		}
	}
	
	var leftMessageColumn = $('.leftMessageColumn .usersArea');
	leftMessageColumn.html(html);

	if ( typeof this.currentTopicIdSelected == 'undefined') {
		var firstcontact = leftMessageColumn.find('.messageContacts:first-child');
		this.currentTopicIdSelected = firstcontact.data('topicid');
		this.recipient_id = firstcontact.data('recipient_id');
		this.sticky_recipient_type = firstcontact.data('sticky_recipient_type');
	};

	//if sticky is enabled add that user id
	if (this.stickyEnabled ) {
		this.currentTopicIdSelected == this.sticky_recipient_id;
	};

	// select topic based on thread id
	var selected_topic = leftMessageColumn.find('[data-topicid='+ this.currentTopicIdSelected +']');

	// if no topic with thread_id, select based on user_id
	if( !selected_topic.length ){
		selected_topic = leftMessageColumn.find('[data-recipient_id="' + this.sticky_recipient_id + '"]');
	}

	// add selected class to highlight for user
	selected_topic.addClass('selected');

	var readMsg = selected_topic.find('.messageRead');
	readMsg.removeClass('messageRead');
	readMsg.addClass('messageReadActive');

	// set current thread's num unread count and sets thread to read
	this.currentTopicNumUnread = selected_topic.data( 'num_unread' );
	this.setThreadRead();

	this.getMessages();
};
/***********************************************************************/

/***********************************************************************
 *============================ GET MESSAGES ============================
 ***********************************************************************
 * Fetches All messages and NEW ones for user id supplied.
 */
Plex.messages.getMessages = function (){
	//turn off submit button
	//this.toggleSendButton();
	var _this = this;
	var url ='';
	var isappend = false;
	var isprepend = false;

	// if(Plex.messages.isAjaxRunning){
	// 	return;
	// }

	Plex.messages.isAjaxRunning = true;



	if ( typeof this.lastMessageIdInTopic != 'undefined' ) {
		url = this.getMessagesUrl + this.currentTopicIdSelected + '/'+ this.lastMessageIdInTopic;
		isappend = true;
	} else {
		url = this.getMessagesUrl + this.currentTopicIdSelected ;
	}


	$.ajax({
		url: url,
		type: 'GET',
		dataType: 'json',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	})
	.done(function(data) {
		var infoData = null;
		if( data.college_rep_info ) infoData = data.college_rep_info;
		else infoData = data.user_info;

		if (infoData != null && infoData != '[]') Plex.initRep( JSON.parse(infoData) );
		else Plex.unmountStudentPanel && Plex.unmountStudentPanel();

		Plex.messages.isAjaxRunning = false;
		data = JSON.parse(data.msg);
		if (data.length != 0) {
			_this.addmessages(data,isappend,isprepend);
		}
	});
}
/***********************************************************************/

/***********************************************************************
 *============================ GET PREVIOUS MESSAGES ============================
 ***********************************************************************
 * Fetches All previous messages and prepend to the top of messages
 */
Plex.messages.getPreviousMessages = function (){

	var _this = this;
	var url ='';
	var isappend = false;
	var isprepend = true;

	if(Plex.messages.isPrevMessageRunning){
		return;
	}

	Plex.messages.isPrevMessageRunning = true;

	url = this.getMsgsHistoryUrl + this.currentTopicIdSelected + '/-1/'+ Plex.messages.firstMessageIdInTopic;

	$.ajax({
		url: url,
		type: 'GET',
		dataType: 'json',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
	})
	.done(function(data) {
		Plex.messages.isPrevMessageRunning = false;
		data = JSON.parse(data.msg);
		
		if (data.length == 0) {
			Plex.messages.noMoreHistoryMsg = true;
			$('.rightMessageColumn .msgScrollBox .msg_history_load_more').remove();
		}else{
			_this.addmessages(data,isappend, isprepend);
		}
	});
}
/***********************************************************************/

/***********************************************************************
 *====================== ADD/INJECT MESSAGES ===========================
 ***********************************************************************
 * Inject the messages received from getMessages
 */
Plex.messages.addmessages = function(data, isappend, isprepend){
	//Build the messages 
	var html = '';
	var isFirstMessage = false;
	var msgScrollBox   = $('.rightMessageColumn .msgScrollBox');
	var cnt = 0;
	var show_previous_msg = 0;

	$.each( data, function( index, message ) {
		// check and build date if necessary
		var last_message_date = Plex.messages.lastMessageDateInTopic;
		var message_date = Plex.common.make_date( message.date );
		
		message_date = message_date.toLocaleDateString();

		show_previous_msg = message.show_previous_msg;

		if (!isFirstMessage && !isappend) {
			
			isFirstMessage = true;
			//This saves the first message id so we can access it later during the NEW message load.
			Plex.messages.firstMessageIdInTopic = message.msg_id;
		}
		if( last_message_date != message_date ){
			Plex.messages.lastMessageDateInTopic = message_date;
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
			var last_to_talk = Plex.messages.lastMessageUserInTopic;
			Plex.messages.lastMessageUserInTopic = message.full_name;
			if( last_to_talk == message.full_name ) hide_user = ' hideUser';
		}
		// get time
		//var time = Plex.common.get_timestamp_time( message.date );
		

		var time = message.time;

		// HIDE REPEAT TIMESTAMPS
		var hide_time = '';
		if(cnt != 0){
			var last_timestamp = Plex.messages.lastMessageTimestampInTopic;
			Plex.messages.lastMessageTimestampInTopic = time;
			if( last_timestamp == time ) hide_time = ' hideTime';
		}
	
		html +='<div class="row messageitems' 
			+ is_org 
			+ is_current_user 
			+ hide_user 
			+ hide_time
			+ new_message_group
			+ '" data-message_id="' 
			+ message.msg_id 
			+'">';
		html +=		'<div class="column medium-2 msgName"';
		html +=			' title="' + message.full_name + '">' + message.Name + '</div>';
		html +=		'<div class="column medium-9 message">' + message.msg + '</div>';
		html +=		'<div class="column medium-1 msgDate messageDate medium-text-right">' + time + '</div>';
		html +='</div>';
		cnt++;
	} ); // end each loop

	// Add messages to the bottom of the message list.
	if (isprepend) {
		$('.rightMessageColumn .msgScrollBox .msg_history_load_more').remove();
		$('.rightMessageColumn .msgScrollBox').prepend(html);
	}else{
		if (isappend) {
			$('.rightMessageColumn .msgScrollBox').append(html);
			Plex.messages.scrollMsgWindow();
		} else{
			$('.rightMessageColumn .msgScrollBox').html(html);
			Plex.messages.scrollMsgWindow();
		};
	}
	
	if (!msgScrollBox.hasScrollBar() && show_previous_msg == 1) {
		$('.rightMessageColumn .msgScrollBox .msg_history_load_more').remove();
		html = '<div onclick="Plex.messages.getPreviousMessages();" class="column small-1 small-centered msg_history_load_more"><span class="column small-12 small-centered">Show Previous Messages</span></div>';
		$('.rightMessageColumn .msgScrollBox').prepend(html);
	}

	//this.updateMessageTimeStamps();

	//This saves the last message id so we can access it later during the NEW message load.
	this.lastMessageIdInTopic = $('.messageitems:last-child').data('message_id');
}
/***********************************************************************/

/***********************************************************************
 *========================= BIND SEND CHAT =============================
 ***********************************************************************
 * Binds a keyup event (with delay) to check if the user has pressed enter.
 * We use a timeout to prevent excess check calling.
 */
Plex.messages.bindTextareaEnter = function(){
	var textarea = $( '.msgtext' );
	textarea.keypress( function( e ){
		// if enter and not shift enter
		if( e.keyCode == 13 && !e.shiftKey){
			e.preventDefault();
			Plex.messages.sendMessage();
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
Plex.messages.sendMessage = function(){
	//hide submit button and stop the heartbeat timer
	//this.toggleSendButton();
	//this.stopMessageHeartBeatTimer();


	var msgText = $('.msgtext');
	msgText.find('.remove-file-btn').remove();
	//get the txt in the textbox.
	//var txtbox = msgText.val();
	var txtbox = msgText.html();

	//Check if empty.
	if(txtbox == ''){
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
	//msgText.val( '' );
	msgText.html('');
	// stop heartbeat and (later) re-start
	Plex.messages.stopMessageHeartBeatTimer();

	// console.log( 'SENDING MESSAGE! Right one!' );

	// disable send button
	//this.toggleSendButton();

	//get current contact selected
	//var recipient_id = this.recipient_id;

	//If this is a sticky topic thread clean the flag. this allows for fresh non sticky thread returns in left column.
	if (this.recipient_id == this.sticky_recipient_id ) {
		this.stickyEnabled = false;
		this.sticky_recipient_id = undefined;
	};

	// sets message_url to use chat's route, to create a one-on-one thread
	/*
	message_url = this.sendAsPrivate ? Plex.chat.sendMessageUrl : this.sendMessageUrl;
	this.sendAsPrivate = false;
	*/
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
				if (this.stickyEnabled == false) {

					post_data.to_user_id = this.recipient_id;
					post_data.college_id = this.college_id; // generated on page load. global

				}else{
					post_data.to_user_id = this.sticky_recipient_id;
					post_data.college_id = this.college_id; // generated on page load. global
				}
				
				break;
			case 'college':
			case 'colleges':

				if (this.stickyEnabled == false) {
					post_data.to_user_id = '';
					post_data.college_id = this.recipient_id;
					
				}else{
					post_data.to_user_id = '';
					post_data.college_id = this.sticky_recipient_id;
				}
				
				break;
			default:
				console.log( 'we need a sticky recipient type in order to create a thread!' );
		}

		// set thread type
		post_data.thread_type = this.sticky_thread_type;
		// reset thread type to undefined ( to prevent errors )
		this.sticky_thread_type = undefined;
	}

	var _this = this;
	
	$.ajax({
		method: 'POST',
		url: post_url,
		data: post_data,
		dataType: 'text',
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		success: function( data ){
			// we expect the thread id to be returned, whether or not it is new.
			if( /^\d+$/.test(data) ) {
				Plex.messages.currentTopicIdSelected = data;
			} else {
				// $('#textmessage-reminder-modal').foundation('reveal', 'open');
			}

			// immediately getTopicList and then restart heartbeat
			_this.getTopicList( _this.startMessageHeartBeatTimer );
		}
	})
	.always( function(){
		// Enable submitting AFTER POST is done
		Plex.messages.enableSubmit();
	} );

	

	mixpanel.track("Message_Student_Offline",
		{
			"location": document.body.id
		}
	);

	/*
	$.post( _url , {'message': txtbox}, function(data, textStatus, xhr) {
		// if we've created a new thread, backend returns thread id
		if( !isNaN( data ) ){
			// so set the current selected to the number returned, so that the
			// thread is selected when addTopicList runs
			Plex.messages.currentTopicIdSelected = data;
		}
		// SOMETHING ELSE IS RETURNED!
		//Plex.messages.currentTopicIdSelected = undefined;
		//Plex.messages.recipient_id = undefined;
		//Plex.messages.sticky_recipient_type = undefined;
	} )
	.done( function(){
		// Enable submitting AFTER POST is done
		Plex.messages.enableSubmit();
	} );
	// immediately getTopicList and then restart heartbeat
	this.getTopicList( this.startMessageHeartBeatTimer );
	*/

}
/***********************************************************************/

/***********************************************************************
 *======================= TOGGLE SEND BUTTON ===========================
 ***********************************************************************
 * Toggles greying out of the send button
 */
Plex.messages.toggleSendButton = function( vis ){
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
 *=============== SCROLLS THE MESSAGE WINDOW TO BOTTOM =================
 ***********************************************************************
 * Scrolls the message window to the bottom, to see the most recent comments.
 */
Plex.messages.scrollMsgWindow = function(){
	var messageColumn = $('.msgScrollBox');
  	var scrollheight = messageColumn.prop('scrollHeight') - messageColumn.height();
	messageColumn.animate({ scrollTop: scrollheight }, 250 );
}
/***********************************************************************/

/***********************************************************************
 *================ START MESSAGE HEARTBEAT TIMER =======================
 ***********************************************************************
 * Starts the heartbeat message timer
 */
Plex.messages.startMessageHeartBeatTimer = function(){
	// clear the timer if already set so we don't get duplicates
	if( Plex.messages.hbtimer ) clearInterval( Plex.messages.hbtimer );
	//set the timer!
	Plex.messages.hbtimer = setInterval( function(){
		Plex.messages.getTopicList();
	}, Plex.messages.messageheartbeattime);
}
/***********************************************************************/

/***********************************************************************
 *================== STOP MESSAGE HEARTBEAT TIMER ======================
 ***********************************************************************
 * Stops the heartbeat message timer
 */
Plex.messages.stopMessageHeartBeatTimer = function(){
	clearInterval(Plex.messages.hbtimer);
}
/***********************************************************************/

/***********************************************************************
 *===================== BIND SCROLL CHECK EVENT ========================
 ***********************************************************************
 * Checks to see if message area has been scrolled to the bottom. If so,
 * and if !ReadScrollDisabled, then we call the setThreadRead() method to
 * tell our DB that the user has read the first messages.
 */
Plex.messages.setReadMessageScrollChecker = function(){
	//binds the scroll for messages
	this.messageArea.scroll(function() {

		if (Plex.messages.ReadScrollDisabled) {
			return;
		};

		var _this = $(this);
		var boxheight = _this.height();
		var scrollTop = _this.scrollTop();
		var scrollHeight = _this.prop('scrollHeight');

		if ((scrollHeight-scrollTop) == boxheight) {
			Plex.messages.setThreadRead();
		};
		
	});
}
/***********************************************************************/

/***********************************************************************
 *===================== MARK THREAD AS READ ============================
 ***********************************************************************
 * Marks a given thread as read. makes AJAX call with thread id.
 */
Plex.messages.setThreadRead = function(){
	// checks if unread_count is > 1 first before continuing
	if( this.currentTopicNumUnread ){
		message_area = this.messageArea;
		/*
		if( message_area.scrollTop() + message_area.innerHeight() >= message_area[0].scrollHeight ){
			// create ajax url
			var _url = Plex.messages.topicReadUrl + Plex.messages.currentTopicIdSelected;

			$.ajax({
				url: _url,
				type: 'GET',
				dataType: 'html',
				headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			})
			.done(function() {
			});
		}
		*/
		// if we're scrolled to the bottom, or there's no scrollbar
		if( 
			!message_area.hasScrollBar() ||
			( message_area.scrollTop() + message_area.innerHeight() >= message_area[0].scrollHeight )
		){
			// create ajax url
			var _url = Plex.messages.topicReadUrl + Plex.messages.currentTopicIdSelected;

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
 *======================== ENABLE SUBMIT BUTTON ========================
 ***********************************************************************
 * Enables the submit button
 */
Plex.messages.enableSubmit = function(){
	Plex.messages.toggleSendButton(1);
	Plex.messages.enterEnabled = true;
}
/***********************************************************************/

/***********************************************************************
 *====================== DISABLE SUBMIT BUTTON =========================
 ***********************************************************************
 * Disables the submit button
 */
Plex.messages.disableSubmit = function(){
	Plex.messages.toggleSendButton();
	Plex.messages.enterEnabled = false;
}
/***********************************************************************/

Plex.messages.isText = function(){
	if( window.location.pathname.indexOf('inquiry-txt') > -1 ){
		Plex.messages.isText = true;	
		Plex.messages.enableTextMessaging();
	}else{
		$('.leftMessageColumn .usersArea > div:first-child').trigger('click');
	}
}

$(document).on('keyup', '.msgtext', function(){
	if( Plex.messages.isText ){
		var count = $(this).val().length;
		if( count <= 160 ) $('#current-text-count').html(count);
		else e.preventDefault();
	}
});

Plex.messages.enableTextMessaging = function(){
	var textarea = $('.msgtext');
	textarea.prop('maxLength', 160);
	textarea.prop('placeholder', 'Text Message');
	textarea.addClass('is-text');
	$('.text-count, .text-label').show();
}

Plex.messages.disableTextMessaging = function(){
	var textarea = $('.msgtext');
	textarea.removeAttr('maxLength');
	textarea.removeAttr('placeholder');
	textarea.removeClass('is-text');
	$('.text-count, .text-label').hide();
}