// searchBar.js

import { connect } from 'react-redux'
import React, { Component } from 'react'

import { searchThreads, filterThreads } from './../../../../actions/messagesActions'

const FILTER_OPTIONS = [
	{name: 'All Messages', filter_by: 'all'},
	{name: 'Unread', filter_by: 'num_unread_msg'},
	{name: 'Campaigns', filter_by: 'is_campaign'},
	{name: 'Text Messages', filter_by: 'has_text'},
];

class SearchBar extends Component{
	constructor(props){
		super(props);
		this.state = {
			show: false,
		};

		this._clickAwayListener = this._clickAwayListener.bind(this);
		this._searchList = this._searchList.bind(this);
		this._filterList = this._filterList.bind(this);
	}

	componentWillMount(){
		document.addEventListener('click', this._clickAwayListener);
	}

	componentWillUnmount(){
		document.removeEventListener('click', this._clickAwayListener);
	}

	_clickAwayListener(e){
		var { show } = this.state,
			elem = e.target.id;

		if( (!elem.includes('_filter') && show) || elem === '_filterArrow' ) this.setState({show: !show});
	}

	_searchList(e){
		var { dispatch } = this.props;
	    dispatch( searchThreads(e.target.value) );
	}

	_filterList(e){
		let { dispatch, messages: _m } = this.props,
			filter_val = e.target.id.split('__').pop();

		this.state.show = false; // close filterbox
	    if( _m.filter_applied !== filter_val ) dispatch( filterThreads(filter_val) );
	}

	render(){
		let { dispatch, messages: _m } = this.props,
			{ show } = this.state;

		return (
			<div id="_searchBar">

				<div className="magnifier-icon">
					{ !_m.search_threads_value ? 
						<div className="magnifier" /> : 
						<div className="clear-search" onClick={ this._searchList }>&#10005;</div> }
				</div>

				<input
					id="_searchInput"
					name="search"
					type="text"
					onChange={ this._searchList }
					value={ _m.search_threads_value || '' }
					placeholder="Search" />

				<div className="search-drop">
					<div id="_filterArrow" className="arrow" />
					{ show && 
						<div id="_filterbox" className="filter-box">
							{ 
								FILTER_OPTIONS.map((o, i) => <div 
																key={o.name} 
																id={'_filter__'+o.filter_by} 
																className={ _m.filter_applied === o.filter_by.split('__').pop() ? 'selected' : '' }
																onClick={ this._filterList }>{o.name}</div>) 
							}
						</div> }
				</div>
			</div>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		messages: state.messages,
	};
};

export default connect(mapStateToProps)(SearchBar);