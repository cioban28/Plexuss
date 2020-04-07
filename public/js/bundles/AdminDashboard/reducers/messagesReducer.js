// messagesReducer.js

var init = {};

export default function(state = init, action) {

    switch(action.type) {

        case '_MESSAGES:SEND_ERR':
        case '_MESSAGES:SET_SOCKET':
        case '_MESSAGES:SEND_PENDING':
        case '_MESSAGES:READ_PENDING':
        case 'LOADING_ATTACHMENT_DONE':   
        case '_MESSAGES:INIT_TEMPLATES':
    	case '_MESSAGES:UPDATE_THREADS':
    	case '_MESSAGES:INIT_CONVO_ERR':
    	case '_MESSAGES:INIT_THREADS_ERR':
        case '_MESSAGES:MORE_THREADS_ERR':
        case '_MESSAGES:DELETE_ATTCH_ERR':
        case '_MESSAGES:SAVING_ATTACHMENT':
        case '_MESSAGES:INIT_CONVO_PENDING': // on convo init pending, set active thread to payload thread id - convo only called for active thread
        case '_MESSAGES:SET_ATTACHMENT_NUM':
        case '_MESSAGES:LOADING_ATTACHMENT':
    	case '_MESSAGES:INIT_THREADS_PENDING':
        case '_MESSAGES:UPDATE_CONVO_ACTIONS':
        case '_MESSAGES:MORE_THREADS_PENDING':
        case '_MESSAGES:INIT_OLDER_CONVO_ERR':
        case '_MESSAGES:DELETE_ATTCH_PENDING':
        case '_MESSAGES:HEARTBEAT_THREADS_ERR':
        case '_MESSAGES:SAVING_ATTACHMENT_ERR':
        case '_MESSAGES:LOADING_ATTCH_DETAILS':
        case '_MESSAGES:SAVING_ATTACHMENT_DONE':
        case '_MESSAGES:LOADING_ATTACHMENT_ERR':
        case '_MESSAGES:INIT_OLDER_CONVO_PENDING':
        case '_MESSAGES:HEARTBEAT_THREADS_PENDING':
        case '_MESSAGES:LOADING_ATTCH_DETAILS_ERR':
        case '_MESSAGES:LOADING_ATTCH_DETAILS_DONE':
    		return { ...state, ...action.payload };

        case '_MESSAGES:SEND_DONE':
            const { msg, ...restOfPayload } = action.payload;

            return {
                ...state, 
                ...restOfPayload,
                threads: state.threads.map(th => 
                    th.thread_id == msg.thread_id ? {...th, msg: `You: ${msg.message}`} : th)
            };

        case '_MESSAGES:OPEN_THREAD': // on convo init pending, set active thread to payload thread id - convo only called for active thread
            const { thread_room } = action.payload,
                newState = {...state, current_thread_room: thread_room},
                thread_id = thread_room.split(':')[1];

            newState.activeThread = _.find(newState.threads.slice(), th => th.thread_id == thread_id);

            return newState;

        case '_MESSAGES:UPDATE_SINGLE_THREAD':
            var updatedThread = action.payload,
                { undetermined_msg = null, activeThread = null } = state,
                newState = {...state},
                _date1, _date2, college,
                forceActiveThreadUpdate = false;

            // check if msg_of_thread id matches id of existing list of thread ids
            const updatedThread_exists_in_threads = _.find([...newState.threads], th => th.thread_id == updatedThread.thread_id);

            // update threads with updatedThread if updatedThread_exists_in_threads
            if( updatedThread_exists_in_threads ){
                newState.threads = newState.threads.map(th => 
                    th.thread_id == updatedThread.thread_id ? {...th, ...updatedThread} : th);
            }else{
                // else update the new thread w/ id = -1
                newState.threads = newState.threads.map(th => {
                    if( th.thread_id == -1 ){
                        /* set undeterminedThreadNowHasId prop which will be used to check in thread.js
                           when an undetermined thread (thread w/ thread_id = -1) now has a proper thread id
                           to join the new thread room */
                        newState.undeterminedThreadNowHasId = updatedThread.thread_id;

                        // update active thread id if -1 to undetermined thread id of updatedThread
                        if( activeThread.thread_id == -1 ) 
                            newState.activeThread = {...newState.activeThread, thread_id: updatedThread.thread_id};

                        college = updatedThread.thread_room.split(':')[0];

                        return {
                            ...th, 
                            ...updatedThread,
                            thread_room: `${college}:${updatedThread.thread_id}`,
                        };
                    }

                    return th;
                });

                // undetermined msg should only be set if admin sent a message to new thread
                // there should be more than 1 undetermined msg
                if( undetermined_msg ){
                    newState.threads = newState.threads.map(th => 
                        th.thread_id == undetermined_msg.msg_of_thread ? {...th, convo: [undetermined_msg]} : th);

                    newState.undetermined_msg = null; // reset since we just assigned msg to proper thread;
                }
            }

            // order threads based off most recently sent msg
            newState.threads.sort((thread1, thread2) => {
                _date1 = new Date(thread1.date);
                _date2 = new Date(thread2.date);
                return _date2 - _date1;
            });

            // only update active thread if updatedThread is the active one
            if( newState.activeThread.thread_id === updatedThread.thread_id ) 
                newState.activeThread = updateActiveThread(newState.activeThread, newState.threads);

            return newState;

    	case '_MESSAGES:INIT_CONVO_DONE': // on convo done, update thread with new (maybe) convo and also update active thread
            var { init_convo_done, init_convo_pending, thread_id, user_info, convo, latest_msg } = action.payload,
                newState = {...state, init_convo_done, init_convo_pending},
                newThread;

            // if convo && user info are empty no need to do anything b/c that's what we're here to update
            if( !convo.length && _.isEmpty(user_info) ) return newState;

            // I think it'll be faster if we find this thread and check if convo and/or user_info are different, then loop through b/c threads could be long
            var _thread = _.find(newState.threads.slice(), { thread_id });

            // _thread is found AND convo lengths are the same, return newState, no need to update anything
            // basically doing same check as inside of map() b/c in the case that _thread already has user_info and convo has no change, then we don't need to loop and update
            if( _thread && ((!_.isEmpty(user_info) && !_thread.user_info) || (convo.length !== _.get(_thread, 'convo.length', 0))) ){

                // update the convo of pay.thread_id only
                newState.threads = newState.threads.map(thr => {
                    if( thr.thread_id === thread_id ){
                        newThread = {...thr};

                        // if convo is not equal to the existing convo length, add to threads convo
                        if( convo.length !== _.get(thr, 'convo.length', 0) ) newThread.convo = [...(newThread.convo || []), ...convo];

                        // if user_info is not empty, set threads user_info
                        if( !_.isEmpty(user_info) ) newThread.user_info = {...user_info};

                        return newThread;
                    }

                    return thr;
                });

            }else return newState; // if there are no changes, just return newState

            // find activeThread in threads and update only if the payloads thread_id matches the active thread's id
            if( _.get(newState, 'activeThread.thread_id') === thread_id ) 
                newState.activeThread = updateActiveThread(newState.activeThread, newState.threads);

    		return newState;

        case '_MESSAGES:INIT_THREADS_DONE': // on init thread done, init threads list with new list - if sticky is passed, put sticky thread at top of list
            var { threads, thread_type, sticky_id, init_threads_done, init_threads_pending, template_list } = action.payload,
                newState = {
                    ...state, 
                    init_threads_done, 
                    init_threads_pending,
                    template_list,
                    show_moreResults_btn: true, // show after init - can't know for sure unless they click "show more results" btn
                },
                stickyThread = null;

            // if threads is empty, just return newState
            if( !_.get(threads, 'length', 0) ) return newState;

            // if thread_type and sticky_id is passed, then there should be a sticky thread
            if( thread_type && sticky_id ){
                // if there's a sticky thread that we've never messaged before find/update
                threads = threads.map(t => {
                    if( t.receiver_id == sticky_id ){
                        stickyThread = {...t, thread_type, sticky_id};
                        return stickyThread;
                    }

                    return t;
                });

                if( stickyThread ){
                    newState.stickyThread = stickyThread; // save sticky user to as own prop in state
                    newState.threads = putStickyAtTopOfList(newState.stickyThread, threads); // update threads with sticky at top

                }else newState.threads = threads; // if no sticky, just set threads list

            }else newState.threads = [...threads];

            return newState; 

        case '_MESSAGES:MORE_THREADS_DONE': // current list plus additional threads is returned so setting new list to threads
            var { moreThreads, more_threads_done, more_threads_pending } = action.payload,
                newState = {...state, more_threads_done, more_threads_pending};

            // if prev current state of threads length equals incoming threads length, then there are no more results
            if( _.get(state, 'threads.length', 0) === _.get(moreThreads, 'length', 0) ){
                newState.show_moreResults_btn = false;

            }else{
                let already_exists, 
                    threads_copy = newState.threads.slice();

                newState.threads = moreThreads.map(t => {
                    // check if t of moreThreads exists in our current list of threads
                    already_exists = _.find(threads_copy, {thread_id: t.thread_id});

                    // if already exists, just update what we have with the new obj in case some some data has changed since
                    if( already_exists ) return {...already_exists, ...t};

                    // else return this new thread that we don't have
                    return t;
                });
            }

            // if we have a stickyThread, add to top of threads list
            if( newState.stickyThread ) newState.threads = putStickyAtTopOfList(newState.stickyThread, newState.threads);

            return newState;

        case '_MESSAGES:ADD_NEW_MSG':
            var [ new_msg ] = action.payload,
                { activeThread: _at, undetermined_msg = null } = state,
                newState = {...state};

            // check if msg_of_thread id matches id of existing list of thread ids
            var msg_matches_thread = _.find([...newState.threads], th => th.thread_id == new_msg.msg_of_thread);

            // find and update the thread that this new msg belongs to
            if( msg_matches_thread ){
                newState.threads = newState.threads.map(t => 
                    t.thread_id == new_msg.msg_of_thread ? {...t, convo: [...t.convo, new_msg]} : t);
            }else{
                // else store it in undetermined msgs arr
                newState.undetermined_msg = new_msg;
            }

            // update active thread with updated threads if msg is for a thread that's currently active
            if( _at.thread_id == new_msg.msg_of_thread )
                newState.activeThread = updateActiveThread(newState.activeThread, newState.threads);

            return newState;

        case '_MESSAGES:INIT_OLDER_CONVO_DONE':
            var { init_older_convo_pending, older_convo, thread_id, firstMessageInConvoId } = action.payload,
                newState = {...state, init_older_convo_pending},
                threadWithNewConvo;

            newState.threads = newState.threads.map(t => {
                if( t.thread_id === thread_id ){
                    // if older_convo is empty, set thread's no previous msg prop to true to remove "Previous Message" btn
                    if( _.isEmpty(older_convo) ) return {...t, no_previous_msgs: true};
                    // else return thread with updated convo list
                    return {...t, convo: [...older_convo, ...t.convo]};
                }

                return t;
            });

            // update activeThread from list
            if( newState.activeThread.thread_id === thread_id ) 
                newState.activeThread = updateActiveThread(newState.activeThread, newState.threads);

            return newState;

        case '_MESSAGES:SEARCH_THREADS':
            var { search_threads_value: sv } = action.payload,
                newResults = null,
                threadsToSearchThrough = state.filteredThreads || state.threads;

            var sv_lc = sv.toLowerCase(); // make search value lowercase - making copy b/c we don't want to save lowercase version 

            // threadsToSearchThrough contains a list of threads that may or may not have a filtered applied to it
            /* easter egg search values: 
                    - read, unread, today, yesterday, group, [country_name], [country_code]
                      text, no conv(conversation), conv(conversation) */

            if( threadsToSearchThrough && sv_lc ){
                newResults = _.filter(threadsToSearchThrough, (thread) => 
                                    _.includes(thread.Name.toLowerCase(), sv_lc) || // find in Name
                                    _.includes(thread.msg.toLowerCase(), sv_lc) || // find in msg
                                    _.includes(thread.formatted_date, sv_lc) || // find in date
                                    _.includes(thread.num_unread_msg, sv_lc) || // find in unread count
                                    (sv_lc.includes('unread') && thread.num_unread_msg >= 0) || // show unread only
                                    (sv_lc.includes('read') && thread.num_unread_msg == 0) || // show read threads only
                                    (sv_lc.includes('no conv') && !thread.convo) || // show threads that you DON'T already have a convo with
                                    (sv_lc.includes('conv') && thread.convo) || // show threads that you already have a convo with
                                    (sv_lc.includes('no text') && thread.has_text == 0) || // show threads that are not text
                                    (sv_lc.includes('text') && thread.has_text == 1) || // show threads that are text
                                    (sv_lc.includes('group') && _.get(thread, 'thread_members.length', 0) > 1) || // show threads that have more than one person in thread members
                                    (sv_lc.includes('today') && moment().isSame(thread.date, 'day')) || // show only threads you messaged today
                                    (sv_lc.includes('yesterday') && moment().subtract(1, 'day').isSame(thread.date, 'day')) || // show threads you've messaged yesterday
                                    (thread.user_info && thread.user_info.country_name.includes(sv)) ||  // show threads that match country name
                                    (thread.user_info && thread.user_info.country_code.includes(sv)) );
            }

            return {
                ...state,
                search_threads_value: sv,
                searchedThreads: newResults,
            };

        case '_MESSAGES:FILTER_THREADS':
            var { filter_applied } = action.payload,
                filteredResults = null;

            if( _.get(state, 'threads.length', 0) > 0 ){

                switch( filter_applied ){
                    case 'num_unread_msg':
                    case 'is_campaign':
                    case 'has_text':
                        filteredResults = _.filter(state.threads.slice(), t => t[filter_applied] > 0);
                        break;

                    default: 
                        filteredResults = null;
                        break;
                }

            }

            return {
                ...state,
                filter_applied,
                searchedThreads: null, // reset searchThreads when applying a new filter
                search_threads_value: '',
                filteredThreads: filteredResults,
            };

        case '_MESSAGES:READ':
            var { thread_id, read_pending } = action.payload,
                newState = {...state, read_pending};

            newState.threads = newState.threads.map(t => t.thread_id === thread_id ? {...t, num_unread_msg: 0} : t);

            // update activeThread from list
            newState.activeThread = updateActiveThread(newState.activeThread, newState.threads);

            return newState;

        case '_MESSAGES:SAVED_TEMPLATE':
            var newState = {
                    ...state,
                    save_template_name: '',
                },
                { data, id } = action.payload; 

            // if an id is passed, then that means an existing template was updated, so update the list with new data
            // else it's a new template, so add it to list
            if( id )newState.template_list = newState.template_list.map(t => t.id === id ? {...t, ...data} : t);
            else newState.template_list = [...newState.template_list, data];

            return newState;

        case '_MESSAGES:DELETED_TEMPLATE':
            var { id } = action.payload;

            return {
                ...state,
                edit_template_selected: '',
                template_list: _.reject(state.template_list.slice(), {id}),
            };
    
        case '_MESSAGES:DELETE_ATTCH_DONE':
            var  {id} = action.payload;   

            var newState = {
                ...state,
                viewing_attch: null,
                delete_attch_pending: false,
                loading_attch_details: false,
            }

            if(id && newState.attachments) 
                newState.attachments =  _.reject( newState.attachments, (item) => { return item.id == id });

            return newState;

        default:
            return state;
            
    }

}

// Params: sticky thread and unfiltered list of threads
// Returns a new array with stickyThread at the top of the list
const putStickyAtTopOfList = (stickyThread, threadList = []) => {
    // if, sticky is already at the top of the list, no need to filter and create new array
    if( threadList.length > 0 && threadList[0].receiver_id ) return threadList;

    var withoutSticky = _.filter(threadList.slice(), t => t.receiver_id != stickyThread.receiver_id);
    return [stickyThread, ...withoutSticky];
}

// Params: active thread and thread list
// return new activeThread obj if found, else returns activeThread param
const updateActiveThread = (activeThread, threadList = []) => {
    if( activeThread ){
        var activeFound = _.find(threadList.slice(), {thread_id: activeThread.thread_id});
        if( activeFound ) return {...activeFound};
    }

    return activeThread;
}
