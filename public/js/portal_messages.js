// /js/portal_messages.js

const _methods = [
	'connect',
	'setRead',
	'addNewMsg',
	'makeConvo',
	'initConvo',
	'disconnect',
	'joinedRoom',
	'makeOnline',
	'makeThreads',
	'endEllipsis',
	'sentMessage',
	'sendMessage',
	'buildConvoUI',
	'joinedThread',
	'typedMessage',
	'makeMessages',
	'getUrlParams',
	'buildThreadUI',
	'updatedThread',
	'scrollToBottom',
	'joinAllThreads',
	'fetchThreadlist',
	'fetchMoreThreads',
	'joinedAllThreads',
	'showMoreThreadsUI',
	'fetchMoreMessages',
];

class Client {	

	constructor(){
		// class vars
		this.threads = [];
		this.isOnline = [];
		this.thread_id = '';
		this.college_id = '';
		this.is_typing = false;
		this.active_thread = {};
		this.thread_room_id = '';
		this.may_have_more_threads = true;
		this.user_id = $('#portal-nav-window').data('userid');
		this.fname = $('#portal-nav-window').data('fname');

		// init each method
		_methods.forEach(meth => (this[meth] = this[meth].bind(this)) );	

		// socket
		this._socketio = io(`${window.location.host}:3001`, {
			secure: window.location.protocol.includes('https')
		});

		// url params
		this.params = this.getUrlParams();

		// elems
		this.ellipsis_elem = $('#ellipsis');
		this.send_elem = $('#_msgSendInput');
		this.noMsgLayer_elem = $('#_noMsgsLayer');
		this.threadlist_elem = $('#usersThreadlist');
		this.ellipsis_name_elem = $('#ellipsis_name');
		this.convoContainer_elem = $('#_convoContainer');
		this.showMoreThreadsBtn_elem = '#_showMoreThreadsBtn';
		this.getPreviousMsgsBtn_elem = '#_getPreviousMsgsBtn';

		// socket listeners
		this._socketio.on('connect', this.connect);
		this._socketio.on('disconnect', this.disconnect);
		this._socketio.on('joined:room', this.joinedRoom);

		this._socketio.on('joined:thread', this.joinedThread);
		this._socketio.on('updated:thread', this.updatedThread);
		this._socketio.on('joined:all_threads', this.joinedAllThreads);

		this._socketio.on('sent:message', this.sentMessage);
		this._socketio.on('typed:message', this.typedMessage);  

		// join on construction
		this._socketio.emit('join:room', {
		    user_id: this.user_id, 
		    name: 'J Hendo' 
		});
	}

	getUrlParams(){
		const { pathname: p } = window.location;

		const path = p.split('/'),
			params = path.slice(path.indexOf('messages') + 1);

		return {
			type: params[1] || 'college',
			college_id: params[0] || '',
			thread_id: params[2] || '',
		}
	}

	connect(){
		/* 	if user is already part of room on connect, rejoin room in backend
			this would only happen if backend was disconnected somehow, so when nodejs is 
			back up, it automatically reconnects user to the current active thread room  */
		if( this.thread_room_id ) this.joinThread();

		// if we don't have any threads on connect, fetch them
		// console.log('have threads? ', !!this.threads.length);
		if( !this.threads.length ) this.fetchThreadlist();
	}

	disconnect(){
		// console.log('i have jsut been disconnected :( ');
	}

	joinThread({ thread_id = '', college_id = '' } = {}){
		// only need to update if thread and college id are passed
		// else just use what's already saved
		if( thread_id && college_id ){
			this.thread_id = thread_id;
			this.college_id = college_id;
			this.thread_room_id = college_id+':'+thread_id;
		}

		// console.log('trying to join thread:', this.thread_room_id);

	    this._socketio.emit('join:thread', {
	        thread_room: this.thread_room_id, 
	        user_id: this.user_id, 
	        name: 'J Hendo' 
	    });
	}

	joinedThread(){
	    $('#_thread'+this.thread_id).addClass('active'); 

	    // set active thread to selected thread
	    this.active_thread = _.find(this.threads.slice(), th => th.thread_id == this.thread_id);
	    // console.log('joined thread - new active thread: ', this.active_thread);

	    // make convo - as long as selected thread has convo
	    if( this.active_thread.convo ) this.makeConvo();

	    // if new active thread has unread msgs, set read
	    if( this.active_thread.num_unread_msg > 0 ) this.setRead();

	    // scroll chat window to last msg
	    this.scrollToBottom();

	    // if thread has user_info initialize it, else unmount it
	    if ( !_.isEmpty(this.active_thread.user_info) ){
	    	Plex.initRep( this.active_thread.user_info );
	    	this.scrollToBottom();
	    }
		else Plex.unmountStudentPanel && Plex.unmountStudentPanel();
	}

	joinAllThreads(){
		this._socketio.emit('join:all_threads', {
			threads: this.threads,
			name: 'Student', 
			user_id: this.user_id, 
		});
	}

	joinedAllThreads(all_rooms){
		// console.log('joined all threads: ', all_rooms);
	}

	updatedThread(thread){
		// only need to update thread if this thread is intended for this user
		// console.log('updated thread: ', thread);
		if( this.user_id == thread.user_id ){
			// console.log('only thread i care about!: ', thread);
			this.threads = this.threads.map(th => 
				th.thread_id == thread.thread_id ? Object.assign({}, th, thread) : th);

			this.makeThreads();
			if( thread.num_unread_msg > 0 ) this.setRead(); // set read
		}
	}

	fetchThreadlist(){
		const { type, college_id, thread_id } = this.params;
		let url = '/portal/ajax/messages/getUserNewTopics';

		if( college_id ) url += `/${college_id}/${type}/${thread_id}`;

		$.ajax({
			type: 'GET',
			url,
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success: data => {
				data = JSON.parse(data);

				// console.log('fetched: ', data.topicUsr);
			    if( Array.isArray(data.topicUsr) && data.topicUsr.length > 0 ){
			    	this.threads = data.topicUsr;
				    this.joinAllThreads(); // join to all thread rooms
				    this.makeThreads(); // build thread ui
			    }
			}
		});
	}

	fetchMoreThreads(){
		const url = '/portal/ajax/messages/getUserNewTopics?loadMore=true';

		$.ajax({
			type: 'GET',
			url,
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success: data => {
				data = JSON.parse(data);
				// hide loader
				$(this.showMoreThreadsBtn_elem).find('img').hide();

			    if( Array.isArray(data.topicUsr) && data.topicUsr.length > this.threads.length ){
			    	this.threads = data.topicUsr;
			    	this.joinAllThreads(); // join to all thread rooms
				    this.makeThreads();
			    }else{
			    	this.may_have_more_threads = false;
			    	$(this.showMoreThreadsBtn_elem).remove();
			    }
			}
		});
	}

	fetchMoreMessages(){
		const { convo } = this.active_thread,
			firstMsg = convo[0].msg_id;

		const url = `/ajax/messaging/getHistoryMsg/${this.thread_id}/-1/${firstMsg}`;
		// console.log('url: ', url);

		$.ajax({
			type: 'GET',
			url,
			headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
			success: data => {
				const prev_msgs = JSON.parse(data.msg);

				// console.log('more msgs data: ', prev_msgs);

				if( prev_msgs.length > 0 ){
					this.active_thread.convo = [...prev_msgs, ...convo];
					this.makeConvo();

				}else{
					this.active_thread.no_prev_msgs = true;
					$(this.getPreviousMsgsBtn_elem).remove();
				}
			}
		});			
	}

	setRead(){
		$.ajax({
			type: 'GET',
			url: '/portal/ajax/messages/setMsgRead/'+this.thread_id,
			headers: {
    			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
	    	},
			success: data => {
				// console.log('_unread_'+this.thread_id);
				$('#_unread_'+this.thread_id).html('');
			}
		});
	}

	joinedRoom({ online = [] }){
		// console.log('online: ', online);
	    this.isOnline = online;
	}

	sendMessage(message){
		const { 
			thread_id, 
			thread_type,
			thread_type_id: to_user_id
		} = this.active_thread;

		$.ajax({
			type: 'POST',
			url: '/ajax/messaging/postMsg',
			headers: {
    			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
	    	},
			data: {
				message,
				thread_id,
				to_user_id,
				thread_type,
				user_id: this.user_id,
				thread_room: this.thread_room_id,
			},
			success: function(data){
				// console.log('data after send: ', data);
			}
		});
	}

	sentMessage(msg){
		// console.log('msg received!', msg);
	    this.send_elem.html(''); // reset send input
	    this.endEllipsis(); // kill ellipsis
	    this.addNewMsg(msg); // add new msg to convo of this thread in threadlist and active thread
	    this.makeConvo(); // rebuild convo
	    this.scrollToBottom(); // scroll to bottom of chat window
	}

	addNewMsg([ new_msg ]){
		const thread_id = new_msg.msg_of_thread,
			college_id = $('.messageContacts.singlethread[data-threadid="' + thread_id + '"]').data('collegeid');

		this.threads = this.threads.map(th => {
			if( th.thread_id === thread_id ) th.convo.push(new_msg);
			return th;
		});

		// update active thread
		this.active_thread = _.find(this.threads.slice(), th => th.thread_id == thread_id);
		this.thread_room_id = college_id+':'+thread_id;
		this.thread_id = thread_id;

		// Set UI thread active 
		$('.messageContacts.singlethread[data-threadid!="' + thread_id + '"]').removeClass('active');

		// Set active thread UI active.
		$('.messageContacts.singlethread[data-threadid="' + thread_id + '"]').addClass('active');
	}

 	scrollToBottom(){
	    this.convoContainer_elem[0].scrollTop = this.convoContainer_elem[0].scrollHeight;
	}

	typingMessage(code){
		this.is_typing = true;
		
		this._socketio.emit('typing:message', {
			name: this.fname,
            user_id: this.user_id,
            thread_room: this.thread_room_id,
            code,
        });
	}

	typedMessage(user){
		// console.log('in typed message!', user);

		// add name of user who's typing to ellipsis
		if( user.name ) this.ellipsis_name_elem.html(user.name || '');
		
	    if( user.code ) this.ellipsis_elem.show();
	    else this.ellipsis_elem.hide();

	    this.scrollToBottom();
	}

	makeThreads(){
	    let html = '';

	    // if no threads - show no msgs layer
	    if( this.threads.length === 0 ){
	    	this.noMsgLayer_elem.show();
	    	return;

	    }else if( this.noMsgLayer_elem.is(':visible') ){
	    	// hide layer if threads length is not 0 and no msg layer is visible
		    this.noMsgLayer_elem.hide();
	    }

	    // loop through each thread and build ui for each
	    [...this.threads].forEach((t, i) => {
	    	if( !t.convo ) this.initConvo(t, i); // if this thread does not have convo yet, get it
	    	html += this.buildThreadUI(t); // returns ui for this thread
	    });

	    if( this.threads.length >= 10 && this.may_have_more_threads ) html += this.showMoreThreadsUI();

	    $('#threadlist').html(html);

	    // inject ui to page
	    this.threadlist_elem.html(html); 
	}

	initConvo(t, i){
		const _this = this;

    	$.ajax({
    		type: 'GET',
    		url: '/portal/ajax/messages/getNewMsgs/'+t.thread_id,
    		headers: {
    			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
    			thread_id: t.thread_id,
    		},
    		success: function(data){
	    		const { thread_id = '' } = this.headers;
	    		const this_thread = _.find([..._this.threads], thread => thread.thread_id == thread_id);

	    		this_thread.convo = JSON.parse(data.msg) || []
	    		this_thread.user_info = JSON.parse(data.user_info);

	    		// if there is no active thread yet and this is the first thread, force the first thread to be active
			    if( !_this.thread_room_id && i === 0 ) $('.singlethread:first').trigger('click');
    		}
    	});
	}

	makeConvo(){
		let html = '', prev_date, same_date, 
			date_only, formatted, prev_formatted;

		const { convo = [], no_prev_msgs = false } = this.active_thread;

		if( !no_prev_msgs && convo.length >= 20 ){
			html += `<div id="_getPreviousMsgsBtn" class="column small-1 small-centered msg_history_load_more">
					<span class="column small-12 small-centered">Show Previous Messages</span>
				</div>`;
		}

		convo.forEach(msg => {
			same_date = false;
			date_only = msg.date.split(' ')[0];
			formatted = null;

			// if prev_date set, then check if current date is same as prev date
			if( prev_date ){
				same_date = moment(prev_date).isSame(date_only);
				prev_date = date_only;

			}else prev_date = date_only; // if null, set prev_date

			// format date
			formatted = moment(msg.date).fromNow();

			// if message was sent to day, show Today instead of time ago
			if( moment().isSame(date_only, 'day') ) formatted = 'Today';

			// if formatted is 'a year ago', modify to '1 year ago'
			if( formatted[0] === 'a' ) formatted = '1 '+formatted.split('a ')[1];

			// it's possible for msgs to be in same month but different days, so the date divider would output duplicate months, 
			// so this check prevents duplicate time ago texts
			if( formatted === prev_formatted ) same_date = true;
			else prev_formatted = formatted;

			html += this.buildConvoUI({ msg, same_date, formatted });
		});

		this.convoContainer_elem.html(html);
	}

	buildConvoUI({ msg, same_date, formatted }){
		let html = '';	

		if( !same_date ){
			html += `<div class='dateSeparator text-center'>
							<span>${formatted}</span>
					</div>`;
		}

		html += `<div class="row messageitems">
					<div class="column medium-2 msgName" title="${msg.full_name}">${msg.full_name}</div>
					<div class="column medium-9 message">${msg.msg}</div>
					<div class="column medium-1 msgDate messageDate medium-text-right">${msg.time}</div>
				</div>`;

		return html;
	}

	buildThreadUI(thread){
		return `<div id="_thread${thread.thread_id}" 
					class="row messageContacts singlethread ${thread.thread_id == this.thread_id ? 'active' : ''}" 
					data-threadid="${thread.thread_id}" 
					data-collegeid="${thread.thread_type_id}">

						<div id="user-image-msgs"class="column small-2">
							<img src="${thread.img}" alt="" title="${thread.Name}">
						</div>

						<div class="column small-9">
							<div class="row">
								<div class="column small-10 text-left messageName">${thread.Name}</div>
								<div class="column small-12 text-left messageSample">${thread.msg}</div>
							</div>
						</div>

						<div class="column small-1">
							<div id="_unread_${thread.thread_id}" class="column small-2 messageDate text-center inline">
								${thread.num_unread_msg == 0 ? ' ' : thread.num_unread_msg}
							</div>
						</div>
					
				</div>`;
	}

	showMoreThreadsUI(){
		return `<div id="_showMoreThreadsBtn" class="row messageContacts loadmore-row">
					<div id="_showMoreThreads" class="column small-12 text-center loadMoreMessages">
						<span class="loading-more"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/loading.gif" alt="loading gif" /></span>
						Show more results
					</div>
				</div>'`;
	}

	makeOnline(){
	    var html = '';

	    this.isOnline.forEach(t => html += '<div>'+t.name+'</div>');

	    $('#whosOnline').html(html);
	}

	makeMessages(list){
	    var html = '';

	    list.forEach(l => {
	        if( user_id === l.user_id ) html += '<div class="mine">'+l.msg+'</div>';
	        else html += '<div class="yours">'+l.msg+'</div>';
	    });

	    $('#chatinner').html(html);
	}

	endEllipsis(){
		this.is_typing = false;
	    this._socketio.emit('typing:message', {thread_room: this.thread_room_id});
	}

}

let _client;
var initUserSocketClient = () => _client = new Client();

/*** DOM event listeners ***/

// emit on key to enable/disable ellipsis
$(document).on('keyup blur', '#_msgSendInput', function(e){
    var val = $(this).text().trim();
    var key = e.which;

    if( e.type === 'focusout' ){
        _client.endEllipsis();
        return;
    }

    // if enter key, submit message
    if( key === 13 ){
    	// console.log('sending...');
        _client.sendMessage(val);

    }else if( !_client.is_typing ){
    	// console.log('typing...');
    	_client.typingMessage(key); 
    }
});

$(document).on('click', '#_msgSendBtn', function(e){
	const input = $('#_msgSendInput').text().trim();

    _client.sendMessage(input);
});

// join room
$(document).on('click', '.singlethread', function(){
    const _this = $(this);
    const thread_id = _this.data('threadid');
    const college_id = _this.data('collegeid');
    const thread = {
    	thread_id,
    	college_id,
    };

    $('.singlethread').removeClass('active');

    // emit thread via socket io
    _client.joinThread(thread);
});

$(document).on('click', '#_showMoreThreads', function(e){
	// console.log('fetch more threads!');
	$(this).find('img').show();
	_client.fetchMoreThreads();
});

$(document).on('click', '#_getPreviousMsgsBtn', function(){
	// console.log('fetch more msgs');
	_client.fetchMoreMessages();
});
