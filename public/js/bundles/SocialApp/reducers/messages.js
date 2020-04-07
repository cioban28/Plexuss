import cloneDeep from 'lodash/cloneDeep';
import initialState from './initialState';
import moment from 'moment'
const _ = {
    cloneDeep: cloneDeep
}
const messages = (state = initialState.messages, action) => {
    switch(action.type){
        case "SET_CURRENT_THREAD":
            state.currentThreadId = action.payload.currentThreadId;
            state.friendId = action.payload.friendId;
            state.threadData = state.allThreadMessages[action.payload.currentThreadId];
            return { ...state }
        case "SET_THREAD_DATA":
            let newState = _.cloneDeep(state);
            newState.threadData = newState.allThreadMessages[newState.currentThreadId];
            return { ...state, threadData: [...newState.threadData ]}
        case "SET_MESSAGES_THREAD":
            newState = _.cloneDeep(state);
            let temp = {};
            let temp1 = {};
            let temp2 = {};
            let temp3 = {};
            let _flag = true;
            if(action.payload.topicUsr.length == 0){
                _flag = false;
            }
            if(newState.topicUsrPageNumber == 1){
                newState.messageThreads = action.payload;
                let b =  action.payload.topicUsr.map((r) => (r.thread_id));
                for(let i=0; i<b.length; i++){
                    temp[b[i]] = []
                    temp1[b[i]] = true;
                    temp2[b[i]] = 1;
                    temp3[b[i]] = false;
                }
                newState.allThreadMessages = _.cloneDeep(temp);
                newState.allThreadUserInfo = _.cloneDeep(temp);
                newState.hasNextMessages = _.cloneDeep(temp3);
                newState.scrollThread = _.cloneDeep(temp1);
                newState.messagesPageNumber = _.cloneDeep(temp2);
            }else{
                let duplicateTopicUsrFlag = false;
                for(let i=0; i < action.payload.topicUsr.length; i++ && !duplicateTopicUsrFlag){
                    let __index = newState.messageThreads.topicUsr.findIndex(u => u.thread_id === action.payload.topicUsr[i].thread_id);
                    if(__index != -1){
                        duplicateTopicUsrFlag = true;
                    }
                }
                if(!duplicateTopicUsrFlag){
                    newState.messageThreads.topicUsr = newState.messageThreads.topicUsr.concat(action.payload.topicUsr);
                    let b =  action.payload.topicUsr.map((r) => (r.thread_id));
                    for(let i=0; i<b.length; i++){
                        newState.allThreadMessages[b[i]] = [];
                        newState.allThreadUserInfo[b[i]] = [];
                        newState.hasNextMessages[b[i]] = false;
                        newState.scrollThread[b[i]] = true;
                        newState.messagesPageNumber[b[i]] = 1;
                    }
                }
            }
            let unreadThread = 0;
            action.payload.topicUsr.map((tUser)=>{
                if(tUser.num_unread_msg > 0){
                    unreadThread++;
                }
            })
            return { ...state,
                    messageThreads: {...newState.messageThreads},
                    allThreadMessages: {...newState.allThreadMessages},
                    allThreadUserInfo: {...newState.allThreadUserInfo},
                    unreadThread: unreadThread,
                    topicUsrPageNumber: newState.topicUsrPageNumber + 1,
                    nextTopicUser: _flag,
                    hasNextMessages: newState.hasNextMessages,
                    scrollThread: newState.scrollThread,
                    messagesPageNumber: newState.messagesPageNumber,
                }
        case "UNSET_NEXTTOPICUSR":
            return{
                ...state,
                nextTopicUser: false,
            }
        case "SET_THREAD_INFO":
            newState = _.cloneDeep(state);
            newState.threadInfo =  action.payload;
            return { ...state, threadInfo: {...newState.threadInfo} }
        case "SET_ALL_THREAD_MESSAGES":
            newState = _.cloneDeep(state);
            let arr = JSON.parse(action.payload.msg);
            let user_info = JSON.parse(action.payload.user_info);
            let topicUsr = JSON.parse(action.payload.topicUsr);
            if(topicUsr && topicUsr[0] && topicUsr[0].thread_id){
                newState.allThreadMessages[topicUsr[0].thread_id] = arr;
                newState.allThreadUserInfo[topicUsr[0].thread_id] = user_info;
                if(arr.length == 0){
                    newState.hasNextMessages[topicUsr[0].thread_id] = false;
                }else{
                    newState.hasNextMessages[topicUsr[0].thread_id] = true;
                }
                newState.scrollThread[topicUsr[0].thread_id] = true;
                newState.messagesPageNumber[topicUsr[0].thread_id] = 2;
            }
            let _index = newState.messageThreads.topicUsr.findIndex(thread => thread.thread_id == topicUsr[0].thread_id);
            if(_index == -1){
                newState.messageThreads.topicUsr.push(topicUsr[0]);
                let latestIndex = newState.messageThreads.topicUsr.length-1;
                if(arr[arr.length-1] && arr[arr.length-1].user_id && newState.logInUserId != arr[arr.length-1].user_id){
                    newState.unreadThread += 1;
                    if(newState.messageThreads.topicUsr[latestIndex].num_unread_msg == 0){
                        newState.messageThreads.topicUsr[latestIndex].num_unread_msg +=1;
                    }
                    if(newState.logInUserId == 'v20qjMVWPe9ANX3eLywr4pRnJ'){
                        newState.messageThreads.topicUsr.splice(0, 0, newState.messageThreads.topicUsr.splice(latestIndex, 1)[0]);
                    }else{
                        newState.messageThreads.topicUsr.splice(1, 0, newState.messageThreads.topicUsr.splice(latestIndex, 1)[0]);
                    }
                }

            }
            return {
                ...state,
                allThreadMessages: {...newState.allThreadMessages},
                allThreadUserInfo: {...newState.allThreadUserInfo},
                messageThreads: _.cloneDeep(newState.messageThreads),
                unreadThread: newState.unreadThread,
                hasNextMessages: newState.hasNextMessages,
                scrollThread: newState.scrollThread,
                messagesPageNumber: newState.messagesPageNumber,
            }
        case "ADD_NEW_THREAD":
            newState = _.cloneDeep(state);
            if(!newState.allThreadMessages.hasOwnProperty(action.payload)){
                newState.allThreadMessages[action.payload] =  [];
                newState.allThreadUserInfo[action.payload] = [];
                newState.hasNextMessages[action.payload] = false;
                newState.scrollThread[action.payload] = true;
                newState.messagesPageNumber[action.payload] = 1;
            }
            return {
                ...state,
                allThreadMessages: _.cloneDeep(newState.allThreadMessages),
                allThreadUserInfo: _.cloneDeep(newState.allThreadUserInfo),
                hasNextMessages: _.cloneDeep(newState.hasNextMessages),
                scrollThread: _.cloneDeep(newState.scrollThread),
                messagesPageNumber: _.cloneDeep(newState.messagesPageNumber),
            }
        case "SEND_MESSAGE":
            newState = _.cloneDeep(state);
            arr = action.payload[0];
            let topicUserIndex = newState.messageThreads.topicUsr.findIndex(user => user.thread_id == arr.msg_of_thread);
            if(topicUserIndex != -1 && newState.allThreadMessages[arr.msg_of_thread].filter(msg => (msg.msg_id == arr.msg_id )).length == 0){
                newState.allThreadMessages[arr.msg_of_thread].push(arr);
                newState.scrollThread[arr.msg_of_thread] = true;
                if(newState.logInUserId != arr.user_id){
                    if(newState.messageThreads.topicUsr[topicUserIndex].num_unread_msg == 0 && newState.currentThreadId != arr.msg_of_thread && !newState.showThreadArr.includes(arr.msg_of_thread)){
                        newState.unreadThread += 1;
                    }
                    if(newState.currentThreadId != arr.msg_of_thread && !newState.showThreadArr.includes(arr.msg_of_thread)){
                        newState.messageThreads.topicUsr[topicUserIndex].num_unread_msg +=1;
                    }
                    newState.messageThreads.topicUsr[topicUserIndex].msg = arr.full_name+": "+arr.msg;
                }else{
                    newState.messageThreads.topicUsr[topicUserIndex].msg = 'You: '+arr.msg;
                }
                let localtime = moment.utc(arr.date).local().format('lll');
                newState.messageThreads.topicUsr[topicUserIndex].date = localtime;
                if(newState.messageThreads.topicUsr[0].thread_id != arr.msg_of_thread){
                    if(newState.logInUserId == 'v20qjMVWPe9ANX3eLywr4pRnJ'){
                        newState.messageThreads.topicUsr.splice(0, 0, newState.messageThreads.topicUsr.splice(topicUserIndex, 1)[0]);
                    }else{
                        newState.messageThreads.topicUsr.splice(1, 0, newState.messageThreads.topicUsr.splice(topicUserIndex, 1)[0]);
                    }
                }
            }
            return { 
                ...state,
                allThreadMessages: {...newState.allThreadMessages},
                messageThreads: _.cloneDeep(newState.messageThreads),
                unreadThread: newState.unreadThread,
                scrollThread: _.cloneDeep(newState.scrollThread),
            }
        case "SET_THREADS_FLAG":
            newState = _.cloneDeep(state);
            newState.isThreads = action.payload;
            return { ...state, isThreads: newState.isThreads }
        case "SET_THREAD_COUNT":
            let area = 0;
            area = (window.innerWidth/100)*80;
            area= area/286;
            area = parseInt(area);
            return { ...state, threadCount: area }
        case "ADD_IN_CONVERSATION_ARRAY":
            newState = _.cloneDeep(state);
            let thread_id_con = action.payload.thread_id;
            if(newState.conversationArr.includes(thread_id_con)){
                newState.conversationArr.splice( newState.conversationArr.indexOf(thread_id_con), 1 );
                newState.conversationArr.push(thread_id_con);
            }else{
                newState.conversationArr.push(thread_id_con);
            }
            let dummyArr = [];
            let count =0;
            if(newState.nmFlag){
                count++;
            }
            let firstIndex = (newState.conversationArr.length - newState.threadCount);
            for(let i = firstIndex; i < newState.conversationArr.length ; i++ ){
                if(count < newState.threadCount){
                    dummyArr.push(newState.conversationArr[i]);
                }
                count++;
            }
            newState.showThreadArr = dummyArr;
            return { ...state,
                conversationArr: newState.conversationArr,
                showThreadArr: newState.showThreadArr,
            }
        case "REMOVE_THREAD":
            let comming_id = action.payload;
            newState = _.cloneDeep(state);
            if( comming_id !== -99){
                let arr_r = newState.conversationArr;
                arr_r.splice( arr_r.indexOf(comming_id), 1 );
                newState.conversationArr = arr_r;
                dummyArr = [];
                count =0;
                if(newState.nmFlag){
                    count++;
                }
                for(let i=newState.conversationArr.length-1; i >= 0 ;i-- ){
                    if(count < newState.threadCount){
                        dummyArr.push(newState.conversationArr[i]);
                    }
                    count++;
                }
                newState.showThreadArr = dummyArr;
            }
            if(comming_id == -99){
                newState.nmFlag = false;
            }
            return { ...state,
                conversationArr: newState.conversationArr,
                showThreadArr: newState.showThreadArr,
                nmFlag: newState.nmFlag,
            }
        case "SET_NMFLAG":
            newState = _.cloneDeep(state);
            let newNmflag = false;
            if(!newState.nmFlag){
                newNmflag = true;
            }
            return { ...state, nmFlag: newNmflag }
        case "READ_MESSAGE":
            newState = _.cloneDeep(state);
            if(newState.unreadThread >= 1){
                newState.unreadThread -= 1;
            }
            if(newState.messageThreads.topicUsr){
                let threadIndex = newState.messageThreads.topicUsr.findIndex(user => user.thread_id == action.payload.thread_id);
                if(threadIndex != -1){
                    newState.messageThreads.topicUsr[threadIndex].num_unread_msg = 0;
                }
            }
            return { ...state,
                messageThreads: newState.messageThreads,
                unreadThread: newState.unreadThread
            }
        case "TYPE_MESSAGE":
            newState = _.cloneDeep(state);
            let typeMsgThread = newState.typingMsgArr.findIndex( t => t == action.payload.thread_id);
            if(typeMsgThread == -1){
                newState.typingMsgArr.push(action.payload.thread_id);
            }
            return { 
                ...state,
                typingMsgArr: newState.typingMsgArr
            }
        case "CANCEL_TYPE_MESSAGE":
            newState = _.cloneDeep(state);
            typeMsgThread = newState.typingMsgArr.findIndex( t => t == action.payload.thread_id);
            if(typeMsgThread != -1){
                newState.typingMsgArr.splice(typeMsgThread,1);
            }
            return { 
                ...state,
                typingMsgArr: newState.typingMsgArr
            }
        case "SET_LOGIN_USER_ID":
            return {
                ...state,
                logInUserId: action.payload,
            }
        case "APPEND_PREVIOUS_MSGS":
            newState = _.cloneDeep(state);
            arr = JSON.parse(action.payload.msg);
            let _threadId = action.payload.thread_id;
            if(arr.length != 0){
                newState.allThreadMessages[_threadId] = arr.concat(newState.allThreadMessages[_threadId]);
            }
            newState.scrollThread[_threadId] = false;
            if(arr.length == 0){
                newState.hasNextMessages[_threadId] = false;
            }
            return{
                ...state,
                allThreadMessages: newState.allThreadMessages,
                hasNextMessages: newState.hasNextMessages,
                scrollThread: newState.scrollThread,
            }
        case "SET_MSG_VIEW_TIME":
            newState = _.cloneDeep(state);
            if(newState.allThreadMessages[action.payload.thread_id]){
                let msgIndex = newState.allThreadMessages[action.payload.thread_id].findIndex(msg => msg.msg_id == action.payload.msg_id);
                if(msgIndex != -1){
                    newState.allThreadMessages[action.payload.thread_id][msgIndex].read_time = action.payload.read_time.date;
                }
            }
            return { ...state, allThreadMessages: _.cloneDeep( newState.allThreadMessages)}
        case "ADD_THREAD_USER":
            newState = _.cloneDeep(state);
            let t_index = newState.messageThreads.topicUsr.findIndex(u => u.thread_id == action.payload.thread_id);
            if(t_index != -1){
                let newUser = {
                    user_id: action.payload.user_id
                }
                let t_member_index = newState.messageThreads.topicUsr[t_index].thread_members.findIndex(member => member.user_id == action.payload.user_id);
                if(t_member_index == -1){
                    newState.messageThreads.topicUsr[t_index].thread_members.push(newUser);
                }
            }
            return {
                ...state,
                messageThreads: _.cloneDeep(newState.messageThreads)
            }
        default:
            return { ...state }
    }
}

export default messages;
