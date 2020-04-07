// messagesActions.js

import axios from 'axios';

export const setSocket = _socket => ({
	type: '_MESSAGES:SET_SOCKET',
	payload: { _socket },
});

export const addNewMessage = msg => ({
	type: '_MESSAGES:ADD_NEW_MSG',
	payload: msg,
});

export const openThread = data => {
	return {
		type: '_MESSAGES:OPEN_THREAD',
 		payload: data,
	}
}

export const updateThreads = (threads) => {
	return {
		type: '_MESSAGES:UPDATE_THREADS',
		payload: {...threads},
	}
}

export const updateSingleThread = thread => ({
	type: '_MESSAGES:UPDATE_SINGLE_THREAD',
	payload: thread,
})

export const updateThreadOfPersonYouAreMessaging = thread => ({
	type: '_MESSAGES:UPDATE_THREAD_OF_PERSON_YOU_ARE_MESSAGING',
	payload: thread,
})

export const searchThreads = (search_threads_value = '') => {
	return {
		type: '_MESSAGES:SEARCH_THREADS',
		payload: { search_threads_value },
	}
}

export const filterThreads = (filter_applied = '') => {
	return {
		type: '_MESSAGES:FILTER_THREADS',
		payload: { filter_applied },
	}
}

export const updateConvoActions = (data) => {
	return {
		type: '_MESSAGES:UPDATE_CONVO_ACTIONS',
		payload: data,
	}
}


export const setAttachmentNumber = (num) => {
	return {
		type: '_MESSAGES:SET_ATTACHMENT_NUM',
		payload: {attachmentNumber : num},
	}
}

export const getInitThreads = (id = '', type = '') => {
	return (dispatch) => {
		dispatch({
	 		type: '_MESSAGES:INIT_THREADS_PENDING',
	 		payload: {init_threads_pending: true},
	 	});

	 	let params = '',
	 		representative_type = window.location.href.includes('admin') ? 'admin' : 'agency';

	 	if( id && type ) params = ('/' + id + '/' + type);

		axios.get('/' + representative_type + '/ajax/messages/getInitialThreadList'+params)
			 .then((res) => {
			 	var { threads, template_list } = res.data;

			 	dispatch({
					type: '_MESSAGES:INIT_THREADS_DONE',
					payload: {
						init_threads_done: true,
						init_threads_pending: false,
						threads,
						template_list,
						thread_type: type,
						sticky_id: id,
					}
				});
			 })
			 .catch((err) => {
			 	console.log('err: ', err);
			 	dispatch({
			 		type: '_MESSAGES:INIT_THREADS_ERR',
			 		payload: {
			 			init_threads_pending: false,
			 		},
			 	});
			 });
	}
}

export const getThreads = () => {
	return (dispatch) => {
		dispatch({
	 		type: '_MESSAGES:HEARTBEAT_THREADS_PENDING',
	 		payload: {heartbeat_threads_pending: true},
	 	});

	 	var representative_type = window.location.href.includes('admin') ? 'admin' : 'agency'; 

		axios.get('/' + representative_type + '/ajax/messages/getUserNewTopics')
			 .then((res) => {
			 	dispatch({
					type: '_MESSAGES:HEARTBEAT_THREADS_DONE',
					payload: {
						heartbeat_threads_done: true,
						heartbeat_threads_pending: false,
						threads: _.get(res, 'data.topicUsr', []),
					}
				});
			 })
			 .catch((err) => {
			 	dispatch({
			 		type: '_MESSAGES:HEARTBEAT_THREADS_ERR',
			 		payload: {
			 			heartbeat_threads_pending: false,
			 		},
			 	});
			 });
	}
}

export const loadMoreThreads = () => {
	return (dispatch) => {
		dispatch({
	 		type: '_MESSAGES:MORE_THREADS_PENDING',
	 		payload: {more_threads_pending: true},
	 	});

	 	var representative_type = window.location.href.includes('admin') ? 'admin' : 'agency'; 

		axios.get('/' + representative_type + '/ajax/messages/getUserNewTopics?loadMore=true')
			 .then((res) => {
			 	dispatch({
					type: '_MESSAGES:MORE_THREADS_DONE',
					payload: {
						more_threads_done: true,
						more_threads_pending: false,
						moreThreads: _.get(res, 'data.topicUsr', []),
					}
				});
			 })
			 .catch((err) => {
			 	dispatch({
			 		type: '_MESSAGES:MORE_THREADS_ERR',
			 		payload: {
			 			more_threads_pending: false,
			 		},
			 	});
			 });
	}
}

export const getConvo = (thread_id, last_msg_id = '') => {
	return (dispatch) => {
		dispatch({
	 		type: '_MESSAGES:INIT_CONVO_PENDING',
	 		payload: {init_convo_pending: true},
	 	});

	 	var representative_type = window.location.href.includes('admin') ? 'admin' : 'agency'; 

		axios.get('/' + representative_type + '/ajax/messages/getNewMsgs/'+thread_id+'/'+last_msg_id)
			 .then((res) => {
			 	let convo = _.get(res, 'data.msg', null) ? JSON.parse(res.data.msg) : [],
			 		user_info = _.get(res, 'data.user_info', null) ? JSON.parse(res.data.user_info) : null;

			 	let latest_msg = convo.length > 0 && _.last(convo);

			 	dispatch({
					type: '_MESSAGES:INIT_CONVO_DONE',
					payload: {
						convo,
						user_info,
						thread_id,
						latest_msg,
						init_convo_done: true,
						init_convo_pending: false,
					}
				});
			 })
			 .catch((err) => {
			 	dispatch({
			 		type: '_MESSAGES:INIT_CONVO_ERR',
			 		payload: {
			 			init_convo_pending: false,
			 		},
			 	});
			 });
	}
}

export const getOlderConvo = (thread_id, firstMessageInConvoId) => {
	return (dispatch) => {
		dispatch({
	 		type: '_MESSAGES:INIT_OLDER_CONVO_PENDING',
	 		payload: {
	 			init_older_convo_pending: true,
	 		},
	 	});

		axios.get('/ajax/messaging/getHistoryMsg/'+thread_id+'/-1/'+firstMessageInConvoId)
			 .then((res) => {
			 	dispatch({
					type: '_MESSAGES:INIT_OLDER_CONVO_DONE',
					payload: {
						init_older_convo_pending: false,
						older_convo: JSON.parse( _.get(res, 'data.msg', false) ),
						thread_id,
						firstMessageInConvoId,
					}
				});
			 })
			 .catch((err) => {
			 	dispatch({
			 		type: '_MESSAGES:INIT_OLDER_CONVO_ERR',
			 		payload: {
			 			init_older_convo_pending: false,
			 		},
			 	});
			 });
	}
}

export const messageRead = (thread_id) => {
	return (dispatch) => {
		dispatch({
	 		type: '_MESSAGES:READ_PENDING',
	 		payload: {read_pending: true},
	 	});

	 	var representative_type = window.location.href.includes('admin') ? 'admin' : 'agency'; 

		axios.get('/' + representative_type + '/ajax/messages/setMsgRead/'+thread_id)
			 .then((res) => {
			 	dispatch({
					type: '_MESSAGES:READ',
					payload: { 
						thread_id,
						read_pending: false,
					}
				});
			 })
			 .catch((err) => {
			 	// console.log('error profile: ', err);
			 });
	}
}

export const sendMessage = ( msg = {}, callback ) => {
	return (dispatch) => {
		dispatch({
	 		type: '_MESSAGES:SEND_PENDING',
	 		payload: {send_pending: true, send_field: ''},
	 	});

		axios.post('/ajax/messaging/postMsg', msg)
			 .then((res) => {
			 	
			 	// if data returns w/an object with to_user_id or thread_id, then that's postMessage@BaseController throwing an error
			 	var invalid_data = _.get(res, 'data.to_user_id') || _.get(res, 'data.thread_id');

			 	if( invalid_data ) throw new Error(invalid_data[0]);

			 	dispatch({
					type: '_MESSAGES:SEND_DONE',
					payload: {
						msg,
						send_err: false,
						send_done: true,
						send_pending: false,
						attachmentNumber: 0,
					},
				});

				if(typeof callback != 'undefined')
					callback(res);
			 })
			 .catch((err) => {
			 	dispatch({
			 		type: '_MESSAGES:SEND_ERR',
			 		payload: {
			 			send_pending: false,
			 			send_err: err.toString(),
			 		},
			 	});
			 });
	}
}

export const getTemplatesList = () => {
	return (dispatch) => {
		axios.get('/ajax/getMessageTemplatesList')
			 .then((res) => {
			 	dispatch({
					type: '_MESSAGES:INIT_TEMPLATES',
					payload: {
						template_list: res.data || [], 
					}
				});
			 })
			 .catch((err) => {
			 	console.log('error profile: ', err);
			 });
	}
}

export const saveTemplate = (name, content, id) => {
	return (dispatch) => {
		axios({
				url: '/ajax/saveMessageTemplates',
				type: 'POST',
				data: {name, content, id},
				headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')}
			})
			 .then((res) => {
			 	dispatch({
					type: '_MESSAGES:SAVED_TEMPLATE',
					payload: {
						id,
						data: res.data || {},
					},
				});
			 })
			 .catch((err) => {
			 	console.log('error profile: ', err);
			 });
	}
}

export const deleteTemplate = (id) => {
	return (dispatch) => {
		axios.post('/ajax/deleteMessageTemplates', {id})
			 .then((res) => {
			 	dispatch({
					type: '_MESSAGES:DELETED_TEMPLATE',
					payload: {id},
				});
			 })
			 .catch((err) => {
			 	console.log('error profile: ', err);
			 });
	}
}

export const saveNewAttachment = (file, callback) => {

	return (dispatch) => {
		// payload control the loader
		dispatch({
			type: '_MESSAGES:SAVING_ATTACHMENT',
			payload: {attch_saving: true,
					  attch_err: false,
					  attachmentNumber: 0}
		});

		axios.post('/ajax/saveOrgSavedAttachments', file)
			 .then((response) => {
			 	dispatch({
			 		type: '_MESSAGES:SAVING_ATTACHMENT_DONE',
			 		payload: {attch_url: response.url,
			 				  attch_saving: false,
					 		  attch_err: false,
				 			  attachmentNumber: 1}
			 	});
			 	callback(response);	
			 })
			 .catch((err) => {
			 	console.log(err);
			 	dispatch({
			 		type: 'SAVING_ATTACHMENT_ERR', 
			 		payload: {attch_err: true,
			 				  attachmentNumber: 0}
			 		}); // turn off loader and show err msg
			 });
	}
}

export const loadAttachments = () => {

	return (dispatch) => {
		// payload control the loader
		dispatch({
			type: '_MESSAGES:LOADING_ATTACHMENT',
			payload: {attch_loading: true,
					  attch_load_err: false,
					  attachments: null,
					  viewing_attch: null }
		});

		axios.get('/ajax/getOrgSavedAttachmentsList')
			.then((res) => {
				dispatch({
					type: 'LOADING_ATTACHMENT_DONE',
					payload:{
						attch_loading: false,
						attachments: res.data }
				});
			})
			.catch((err) => {
				console.log(err);
				dispatch({
					type: '_MESSAGES:LOADING_ATTACHMENTNT_ERR',
					payload: {
						attch_loading: false,
						attch_load_err: true}
				});
			});

	}
}


export const getAttachmentDetails = (id) => {

	return (dispatch) => {
		
		dispatch({
			type: '_MESSAGES:LOADING_ATTCH_DETAILS',
			payload: {
				loading_attch_details: true,
				loading_attch_details_err: false,
				viewing_attch: null}
		});


		axios.post('/ajax/loadOrgSavedAttachments', {id: id})
			.then((res) => {

				let data = res.data;

				dispatch({
					type: '_MESSAGES:LOADING_ATTCH_DETAILS_DONE',
					payload: {
						loading_attch_details: false,
						viewing_attch: {
							name: data.name,
							url: data.url,
							id: id,
							date: data.date.date
						}}});
			})
			.catch((err) => {
				console.log(err);
				dispatch({
					type: '_MESSAGES:LOADING_ATTCH_DETAILS_ERR',
					payload: {
						loading_attch_details: false,
						loading_attch_details_err: true
					}});
			});
	}
}



export const deleteAttachmentFromDB = (id) => {

	return (dispatch) => {

		dispatch({
			type: '_MESSAGES:DELETE_ATTCH_PENDING',
			payload: {
				delete_attch_pending: true,
				delete_attch_pending_err: false}
		});


		axios.post('/ajax/deleteOrgSavedAttachments', {id: id})
			.then((res) => {
				dispatch({
			type: '_MESSAGES:DELETE_ATTCH_DONE',
			payload: {
				delete_attch_pending: false,
				id: id
			}
		});

			})
			.catch((err) => {
				console.log(err);
				dispatch({
					type: '_MESSAGES:DELETE_ATTCH_ERR',
					payload: {
						delete_attch_pending: false,
						delete_attch_pending_err: true}
				});
			})

	}
}
