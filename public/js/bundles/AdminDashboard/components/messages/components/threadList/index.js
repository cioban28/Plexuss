// threadList.js

import { connect } from 'react-redux'
import React, { Component } from 'react'

import Thread from './thread'
import SearchBar from './searchBar'

import { getInitThreads, loadMoreThreads } from './../../../../actions/messagesActions'

import './styles.scss'

class ThreadList extends Component{
	constructor(props){
		super(props);
		
		this._getThreads = this._getThreads.bind(this);
		this._getMoreThreads = this._getMoreThreads.bind(this);

		this.state = {i: 0};	
	}

	componentWillMount(){
		let { dispatch, params } = this.props,
			{ id, type } = params;

		dispatch( getInitThreads(id, type) ); // init threads
	}

	_getThreads(){
		let { messages } = this.props;

		// 1. return searchThreads
		// 2. or return filteredThreads
		// 3. else return threads
		if( messages.searchedThreads ) return messages.searchedThreads;
		else if( messages.filteredThreads ) return messages.filteredThreads;
		return messages.threads || [];
	}

	_getMoreThreads(){
		let { dispatch, messages: _m } = this.props;
		if( !_m.more_threads_pending ) dispatch( loadMoreThreads() );
	}

	render(){
		let { dispatch, messages, params, showConversation, setShowConversation } = this.props,
			_threads = this._getThreads();

		return (
			<div id="_threadListContainer" className={showConversation ? "vanish_magic" : "show_magic"}>

				<SearchBar />

				<div className="list-scroller stylish-scrollbar">

					{	_threads.length ? 
						_threads.map((thr, i) => <Thread showConversation={showConversation} setShowConversation={setShowConversation} key={thr.thread_id} thread={thr} index={i} />)
						:
						<div className="no-threads">
							{ (!messages.init_threads_pending && _threads.length === 0 ) && 
								<div className="no-convo">No conversations found</div> }

							{ messages.init_threads_pending && 
								<div className="load-container">
									&nbsp;
									<div className="more-loader" />
								</div> }
						</div> 
					}

					{ _.get(_threads, 'length', 0) >= 10 &&
						<div 
							onClick={ messages.show_moreResults_btn && this._getMoreThreads }
							className={'showmore '+(!messages.show_moreResults_btn ? 'nonleft' : '')}>
								{ !messages.more_threads_pending && 
									<span>{ messages.show_moreResults_btn ? 'Show more results' : 'No more results' }</span> }
								
								{ messages.more_threads_pending && 
									<div>
										&nbsp;
										<div className="more-loader" />
									</div> }
						</div> }

				</div>

			</div>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		user: state.user,
		messages: state.messages,
	};
};

export default connect(mapStateToProps)(ThreadList);
