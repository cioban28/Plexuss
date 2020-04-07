// /common/SortBar.js

import React from 'react'
import selectn from 'selectn'
import { connect } from 'react-redux'
import Tooltip from './../../common/Tooltip'
import './styles.scss'
import * as $ from 'jquery'

class SortBar extends React.Component{
	constructor(props) {
		super(props)
		this.state = {
			scrollClass: '',
			barTop: null
		}
		this._scrollListener = this._scrollListener.bind(this)
	}

	_scrollListener(e){
		// adds a class to the header to make it fixed to the top when scrolled past a certain point
		let { scrollClass, barTop } = this.state,
			classname = '',
			doc = $(window).scrollTop(),
			bar = $('#_sortingBar').offset().top;

		if( barTop === null ) this.state.barTop = bar;

		// only setting state to if scrollClass isn't already set
		if( barTop && doc > barTop ){
			// only setting state to if scrollClass isn't already set
			if( !scrollClass ) this.setState({scrollClass: 'scrolledToTop'});
		}else {
			// only setting scrollClass to empty if it is set
			if( scrollClass ) this.setState({scrollClass: ''});
		}
	}

	componentWillMount(){
		document.addEventListener('scroll', this._scrollListener);
	}

	componentWillUnmount(){
		document.removeEventListener('scroll', this._scrollListener);
	}

	render(){
		let { dispatch, columns: c, children, _user } = this.props,
			{ scrollClass } = this.state,
			is_prem = true; //selectn('premium_user_level_1', _user);

		return (
			<div id="_sortingBar" className={scrollClass + (is_prem ? ' sic_on' : '')}>

				{ children && <div className="filter-bar clearfix">{ children }</div> }

				<div className="sort-bar">
					{ (c && c.length > 0) && c.map((c) => <SingleColumn key={c.name} col={c} {...this.props} filterbarActive={ !!children } />) }
				</div>
			</div>
		);
	}
}

class SingleColumn extends React.Component{
	constructor(props) {
		super(props)
		this.state = {
			order: '',
		}
		this._sortByThisCol = this._sortByThisCol.bind(this)
		this._sortList = this._sortList.bind(this)
		this._getNextOrder = this._getNextOrder.bind(this)
	}

	_sortList(){
		let { dispatch, sortAction, col } = this.props; 

		if( !col.sortType ) return;

		let sorted = this._sortByThisCol();
		
		if( sortAction ) dispatch( sortAction(sorted) );
		this.setState( {order: this._getNextOrder()} );
	}

	_sortByThisCol(){
		let { storeObj, col, list, filterbarActive } = this.props,
			order = this._getNextOrder(),
			listname = col.sortCol + '_' + order;

		/*  
			if storeObj[listname] is valid AND filterbar is NOT active - that means we have already sorted this list for this column, so just return that
			(if !filterbarActive, means there's no filter bar - filter bar would adjust size of list, so using a saved sorted list doesn't make sense), 
		*/
		if( storeObj[listname] && !filterbarActive ) return {list: storeObj[listname]};

		// else create the new sorted list asc
		// orderBy([list], [col(s)], [asc/desc])
		let sortedList = _.orderBy(list.slice(), [col.sortCol], [order]);

		if( col.sortType === 'number' ){
			/* 
				for the three monetary valued cols, 
				only sort the schools that have vals for this col, 
				putting schools with no val at the bottom of the sorted list 
			*/
			var partitioned = _.partition(sortedList.slice(), s => !!s[col.sortCol]);
			sortedList = _.flatten(partitioned);
		}

		return {
			[listname]: sortedList, // saving new list by column name + order
			list: sortedList, // to update the actual list that is rendered
		};
	}

	_getNextOrder(){
		let { order } = this.state;

		return (order === '' || order === 'desc') ? 'asc' : 'desc';
	}

	render(){
		let { dispatch, sortDispatch, col } = this.props,
			{ order } = this.state;

		return (
			<div className={"col col-"+col.width+' '+( col.mobileClass ? col.mobileClass : '' )}>
				<div className="inner-col">

					{ col.sortType && <div className={"sort-arrows " + order} onClick={ this._sortList }>
											<div className="arrow up"></div>
											<div className="arrow down"></div>
										</div> }

					<span className={"name "+(col.abbr ? 'notAbbr' : '')} onClick={ this._sortList }>{ col.name }</span>
					{ col.abbr && <span className="name abbr" onClick={ this._sortList }>{ col.abbr }</span> }

					{ col.tip && <div className="tip-container">
									<Tooltip customClass="negative">
										<div><b><u>{ col.name }</u></b></div>
										<div>{ col.tip }</div>
									</Tooltip>
								</div> }

				</div>
			</div>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		_user: state._user,
		_intl: state._intl,
	};
};

export default connect(mapStateToProps)(SortBar);
