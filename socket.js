// socket.js

// contains environment variables
require('./node_env.js');

// libs
const fs = require('fs')
const _ = require('lodash')

// express
const app = require('express')()
const SERVER_PORT = 3001;

let io = null,
    server = null;

// if production, set httpS server
// else, http server
if( process.env.PRODUCTION === 'true' ){
    const https = require('https');
    const serverOptions = {
        key: fs.readFileSync('/etc/ssl/2018/plexuss_com_2018.key'),
        cert: fs.readFileSync('/etc/ssl/2018/plexuss_com_2018.pem')
    }

    server = https.createServer(serverOptions, app);

}else{
    server = require('http').Server(app)
}

// init socket io
io = require('socket.io')(server);

// this server is listening on this port
server.listen(SERVER_PORT, () => console.log(`listening on *:${SERVER_PORT}`) );

// socket io event handlers
const _socketHandlers = require('./node_server/socketEventHandlers');

// sns push notification event handlers
// const _snsPushEvents = require('./node_server/snsPushNotificationEvents');

// oneSignal push notification event handlers
const _oneSignal = require('./node_server/oneSignalNotificationEvents');

// init redis
const Redis = require('ioredis')
const _redisEvents = require('./node_server/redisEventHandlers')
const _redisSub = new Redis(_redisEvents.config);
const _redisPub = new Redis(_redisEvents.config);

let _queue = [];

// add new redis subscribers here
const _redisChannels = [
    'test-channel',
    'send:message',
    'send:msgNotification',
    'post:comments',
    'publish:post',
    'delete:post',
    'hide:post',
    'update:shareCount',
    'push:notification',
    'add:like',
    'remove:like',
    'delete:comment',
    'read:notification',
    'add:messageThread',
    'edit:comment',
    'send:pushNotification',
    'type:message',
    'cancel-typing:message',
    'latest:*',
    'update:thread',
    'update:crmAutoDialerBlacklist',
    'set:readTime',
    'add:threadUser'
];

// create subscribe for each redis channel
_redisChannels.forEach(channel => _redisSub.subscribe(channel));

// this is the listener for each subscribed redis channel
_redisSub.on('message', (channel, message) => {
	var message = JSON.parse(message);

    // if io has no connected clients, queue up events, else emit events
    // start a switch statement that matches the socket.on listeners from below
    if( _.isEmpty(io.sockets.sockets) ) _queue.push({channel, message});
    else _handleSubscribed({channel, message});
});

io.on('connection', socket => {
    // handle on client connect
    _socketHandlers.clientConnected({io, socket});

    // if there was events to be emitted queued up, emit those, then remove from queue
	if( _queue.length > 0 ) _runQueue(io);

    socket.on('join:crm_room', user => _socketHandlers.joinCrmRoom({ io, socket, user }));
    // when client is connected
	socket.on('join:room', user => _socketHandlers.joinRoom({ io, socket, user }));

    // when client clicks on thread
    socket.on('join:thread', user => _socketHandlers.joinThread({ io, socket, user }));

    // joins client to all thread rooms when client get threadlist to listen for msgs on all threads
    socket.on('join:all_threads', user => _socketHandlers.joinAllThreads({ io, socket, user }));

    // when is currently typing message - to show ellipsis
    socket.on('typing:message', user => _socketHandlers.typingMessage({ io, socket, user }) );

    // handle when client/socket is disconnected
    socket.on('disconnect', () => _socketHandlers.disconnect({ io, socket }) );
});

const _handleSubscribed = ({ channel, message }) => {

    // trigger handler based on channel name
    switch( channel ){

        case 'send:message': _socketHandlers.sendMessage({ io, message });
            break;

        case 'send:msgNotification': _socketHandlers.sendMsgNotification({ io, message });
            break;

        case 'post:comments': _socketHandlers.postComment({ io, message });
            break;
        
        case 'set:readTime': _socketHandlers.setViewTime({ io, message });
            break;

        case 'add:threadUser': _socketHandlers.addThreadUser({ io, message });
            break;

        case 'publish:post': _socketHandlers.publishPost({ io, message });
            break;

        case 'delete:post':  _socketHandlers.deletePost({ io, message });
            break;

        case 'hide:post': _socketHandlers.hidePost({ io, message });
            break;

        case 'update:shareCount': _socketHandlers.updateShares({ io, message });
            break;
        
        case 'add:like': _socketHandlers.addLike({ io, message });
            break;
        
        case 'remove:like': _socketHandlers.removeLike({ io, message });
            break;

        case 'delete:comment': _socketHandlers.deleteComment({ io, message });
            break;

        case 'read:notification': _socketHandlers.readNotification({ io, message });
            break;
        
        case 'add:messageThread': _socketHandlers.addMessageThread({ io, message });
            break;
        
        case 'type:message': _socketHandlers.typeMessage({ io, message });
            break;

        case 'cancel-typing:message':_socketHandlers.cancelTyping({ io, message });
            break;

        case 'edit:comment': _socketHandlers.editComment({ io, message });
            break;
        case 'push:notification': _socketHandlers.pushNotification({ io, message });
            break;

        case 'update:thread': _socketHandlers.updateThread({ io, message });
            break;

        case 'send:pushNotification': _oneSignal.pushNewNotification({ message });
            break;

        case 'update:crmAutoDialerBlacklist': _socketHandlers.updateCrmAutoDialerBlacklist({ io, message });

    }

}

const _runQueue = io => {
    const _queue_copy = _queue.slice();

    _queue_copy.forEach(q => {
        io.emit(q.channel, q.data);
        _queue = _.reject(_queue.slice(), {channel: q.channel}); // removes from queue
    });
}
