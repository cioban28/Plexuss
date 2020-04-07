import React from 'react';
import Immutable from 'seamless-immutable';
import { Reducer } from 'redux-testkit';

import Mreducer from './../../../bundles/AdminDashboard/reducers/messagesReducer'



//mimcked initial state for attachments
const initialState = { oldData: 'old data...'};



describe('messagesRuducer', () => {

	////////////////
	it('should have initial state', () => {
		expect(Mreducer({}, '')).toEqual({});
	});



	/////////////////
	it('should load attachments, overwrite old attachments, and let app know it is finished', () => {
		const oldState = new Immutable({ ...initialState , attachments: ['attch0', 'attch1'], attch_loading: true});
		const payload =  {  attch_loading: false,
					 		attachments: ['attch1', 'attch2', 'attch3']};
		const action = {type: 'LOADING_ATTACHMENT_DONE', payload};

		Reducer(Mreducer).withState(oldState).expect(action).toReturnState({...initialState, ...payload });
	});
	
});