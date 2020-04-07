// /node_server/socketEventHandlers.js

// libs
const _ = require('lodash')
const _redis = require('./redis')()

/* utility methods - will end up putting in own utility file */
const _print = data => console.log(data);

// redis keys
const REDIS_ONLINE_USERS_KEY = 'online:users';

module.exports = {

    clientConnected: ({ io, socket }) => {
        // console.log('------------');
        // console.log('a user connected: ', socket.id);
        // console.log('------------');
    },

    joinCrmRoom: ({ io, socket, user }) => {
        const org_branch_id = user.org_branch_id;

        socket.join('CRM_ROOM:' + org_branch_id);
    },

    updateCrmAutoDialerBlacklist: ({ io, message }) => {
        const { org_branch_id, caller_user_id, black_list } = message;

        io.to('CRM_ROOM:' + org_branch_id).emit('update:auto_dialer_black_list', { black_list: black_list, caller_user_id: caller_user_id });
    },

	joinRoom: ({ io, socket, user }) => {
        // _print('------------------ in join room ---------------------')
        // _print(`${user.name} is joining room ${user.room}`);

        // have user join room based off college_id
        if( user.room ) socket.join(user.room);

        const socket_id_for_user_key = `socket_${socket.id}_for_user`;
        const users_socket_list_key = `user_${user.user_id}_sockets`;

        // save this user's id in REDIS_ONLINE_USERS_KEY set
        // used too see what users are online - key = socket id, val = user_id
        _redis.hmset(REDIS_ONLINE_USERS_KEY, socket.id, user.user_id, (err, response) => {
            socket.to('post:room:').emit('joined:user', user.user_id);
            // return back to this socket only, a list of online users
            // no need for list of online users yet so just returning empty arr for now
            _redis.hgetall(REDIS_ONLINE_USERS_KEY, (err, onlineUsers) => {
                socket.emit('joined:room', { online: onlineUsers });
            })
        });
	},

    joinThread: ({ io, socket, user }) => {
        const { user_id, thread_room, name } = user;

        // _print('------------------ in joinThread ---------------------')
        // _print(`${name} is joining thread ${thread_room}`);

        const active_thread_room_key = `active_thread_room:${user_id}`;

        // save new thread room
        _redis.set(active_thread_room_key, thread_room);

        // have user join thread room based off college_id:thread_id
        socket.join(thread_room);

        // broadcast new list of users of this room and the threads
        socket.emit('joined:thread', { thread_room });
    },

    joinAllThreads: ({ io, socket, user }) => {
        const { name, college_id = '', agency_id = '', threads = [] } = user;

        // _print('------------------ in joinAllThreads ---------------------')
        // _print(`${name} is trying to join ${threads.length} threads.`);

        // have user join to each thread room based off college_id:thread_id
        threads.forEach(thr => { socket.join(`${college_id || agency_id || thr.thread_type_id}:${thr.thread_id}`); } );

        // broadcast new list of users of this room and the threads
        socket.emit('joined:all_threads', io.sockets.adapter.rooms);
    },

    updateThread: ({ io, message: thread }) => {
        // console.log('------------- in updateThread! -----------');
        io.to(thread.thread_room).emit('updated:thread', thread);
    },

	sendMessage: ({ io, message }) => {
        const { user, msgs } = message;

        // console.log('------------- in sendMessage! -----------');
        // _print(`${user.user_id} is sending a message to ${user.thread_room}`);

        // emit new message to everyone in this room
        if(user.thread_room_list){
            user.thread_room_list.map(t_room => {
                io.to(t_room).emit('sent:message', JSON.parse(msgs));
            })
        }else{
            io.to(user.thread_room).emit('sent:message', JSON.parse(msgs));
        }
	},

    sendMsgNotification: ({ io, message }) => {
        const { thread_room  } = message;
        io.to('post:room:'+thread_room).emit('sent:msgNotification', true);
    },

    postComment: ({ io, message }) => {
        const { comment_data, thread_room } = message;

        // console.log('------------- in post Comments! -----------');
        // _print(`${comment_data} is sending a message to ${thread_room}`);
        // _print(`${msgs} io values.`);

        // emit new message to everyone in this room
        io.to(thread_room).emit('posted:comment', comment_data);
    },

    setViewTime: ({ io, message }) => {
        const { thread_id, msg_id, read_time, thread_room } = message;
        let data = {
            thread_id: thread_id,
            msg_id: msg_id,
            read_time: read_time
        }
        io.to(thread_room).emit('set:viewTime', data);
    },

    addThreadUser: ({ io, message }) => {
        const { user_id, thread_id, thread_room } = message;
        let data = {
            user_id: user_id,
            thread_id: thread_id
        }
        // console.log('-------------thread_room----------');
        // console.log(thread_room);
        io.to(thread_room).emit('added:threadUser', data);
    },
    
    publishPost: ({ io, message }) => {
        const { post_data, thread_room } = message;
        // console.log('------------- in post Comments! -----------');
        // _print(`${comment_data} is going to post on ${thread_room}`);
        // _print(`${msgs} io values.`);
        
        // emit new message to everyone in this room
        io.to(thread_room).emit('published:post', post_data);
    },
    
    deletePost: ({ io, message}) => {
        const { id, thread_room, type } = message;
        let data = {
            id: id,
            type: type
        }
        io.to(thread_room).emit('deleted:post', data);
    },

    hidePost: ({ io, message}) => {
        const { id, thread_room, type } = message;
        let data = {
            id: id,
            type: type
        }
        io.to(thread_room).emit('hidden:post', data);
    },

    updateShares: ({ io, message}) => {
        const { id, thread_room, share_count, type } = message;
        let data = {
            id: id,
            share_count: share_count,
            type: type
        }
        io.to(thread_room).emit('updated:shareCount', data);
    },
    addLike: ({io, message}) =>{
        const { like_data, thread_room } = message;
        io.to(thread_room).emit('added:like', like_data);
    },
    removeLike: ({io, message}) =>{
        const { data, thread_room } = message;
        io.to(thread_room).emit('removed:like', data);
    },
    deleteComment: ({io, message}) =>{
        const { comment_id, post_id, social_article_id,  thread_room } = message;
        let data ={
            comment_id: comment_id,
            post_id: post_id,
            social_article_id: social_article_id,
        }
        io.to(thread_room).emit('deleted:comment', data);
    },
    readNotification: ({io, message}) =>{
        const { id, thread_room } = message;
        io.to(thread_room).emit('read_:notification', id);
    },
    addMessageThread: ({io, message}) =>{
        const { id, thread_room } = message;
        io.to(thread_room).emit('added:messageThread', id);        
    },
    typeMessage: ({io, message}) =>{
        const { user_id, thread_room, thread_id } = message;
        let data ={
            thread_id: thread_id,
            user_id: user_id
        }
        io.to(thread_room).emit('typing:message', data);        
    },
    cancelTyping: ({io, message}) =>{
        const { user_id, thread_room, thread_id } = message;
        let data ={
            thread_id: thread_id,
            user_id: user_id
        }
        io.to(thread_room).emit('canceled-typing:message', data);        
    },
    editComment: ({io,message}) =>{
        const { comment_data,  thread_room } = message;
        io.to(thread_room).emit('edited:comment', comment_data);
    },
    pushNotification: ({ io, message }) => {
        const { notification_data, thread_room } = message;
        ntn_thread = 'post:room:'+thread_room;
        io.to(ntn_thread).emit('pushed:notification', notification_data);
    },

    typingMessage: ({ io, socket, user }) => {
        // console.log('------------- in typingMessage! -----------');

        // if( user.user_id ) console.log(`${user.name} is typing in ${user.thread_room}...`);
        // else _print(`No longer typing in room ${user.thread_room}`);

        // broadcast to all in this room except the user triggering this listener
        socket.broadcast.to(user.thread_room).emit('typed:message', user);
    },

    disconnect: ({ io, socket }) => {
        // _print('------------------ in disconnect ---------------------')
        _redis.hgetall(REDIS_ONLINE_USERS_KEY, (err, onlineUsers) => {

            // console.log(`socket ${socket.id} was disconnected: online users: `, onlineUsers);
            // if this socket id is in onlineUsers, delete it upon disconnect
            if( onlineUsers[socket.id] ) _redis.hdel(REDIS_ONLINE_USERS_KEY, socket.id);
            // let arr = Object.keys(onlineUsers);
            // arr.map( id => {
            //     _redis.hdel(REDIS_ONLINE_USERS_KEY, id);
            // })

            _redis.hgetall(REDIS_ONLINE_USERS_KEY);
            socket.to('post:room:').emit('disconnect:user', onlineUsers[socket.id]);

        });
    },

}
