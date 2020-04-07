// priorityCollegeList.js

import React from 'react'
import { connect } from 'react-redux'
import ReactSpinner from 'react-spinjs-fix'
import createReactClass from 'create-react-class'

import TableRow from './tableRow'
import AppOrderRow from './appOrderRow'
import HeaderColumn from './headerColumn'

import { getPrioritySchools, getContractTypes, getAppOrderSchools } from './../../../../actions/pickACollegeActions'

const HEADERS = [
	{name: 'arrow', colSize: 1},
	{name: 'Name of College', colSize: 2, sortType: 'alphabet', sortname: 'name'},
	{name: 'Contract', colSize: 1, sortType: 'alphabet', sortname: 'contract'},
	{name: 'AOR', colSize: 1, sortType: 'alphabet', sortname: 'aor'},
	{name: 'Price', colSize: 1, sortType: 'number', sortname: 'price'},
	{name: 'Goal', colSize: 1, sortType: 'number', sortname: 'goal'},
	{name: 'Views', colSize: 1, sortType: 'number', sortname: 'views'},
	{name: 'Picks', colSize: 1, sortType: 'number', sortname: 'picks'},
	{name: 'Conversion', colSize: 1, sortType: 'number', sortname: 'conversion'},
	{name: 'Handshakes', colSize: 1, sortType: 'number', sortname: 'handshakes'},
	{name: 'Actions', colSize: 1},
];

const APPORDER = [
	{name: 'Name of College', colSize: 4, sortType: 'alphabet', sortname: 'school_name'},
	{name: 'Contract', colSize: 2, sortType: 'alphabet', sortname: 'contract'},
	{name: 'AOR', colSize: 2, sortType: 'alphabet', sortname: 'aor'},
	{name: 'Picks', colSize: 2, sortType: 'number', sortname: 'financial_filter_order'},
	{name: 'Actions', colSize: 2},
];

const CUSTOM_HEADERS = {
	appOrder: APPORDER,
	headers: HEADERS,
}

const PriorityCollegeList = createReactClass({
	getInitialState(){
		return {
			scrollClass: ''
		}
	},

	componentWillMount(){
		let { dispatch, id } = this.props;

		this._initSchools(id);

		dispatch( getContractTypes() );

		document.addEventListener('scroll', this._scrollListener);
	},

	componentWillUnmount(){
		document.addEventListener('scroll', this._scrollListener);
	},

	_initSchools(id){
		let { dispatch } = this.props;

		switch(id){

			case 'appOrder':
				dispatch( getAppOrderSchools() );
				break;

			default:
				dispatch( getPrioritySchools() );
				break;
		}
	},

	_scrollListener(e){
		// adds a class to the header to make it fixed to the top when scrolled past a certain point
		let { scrollClass } = this.state;

		if( e.srcElement.body.scrollTop > 137 ){
			// only setting state to if scrollClass isn't already set
			if( !scrollClass ) this.setState({scrollClass: 'scrolled'});
		}else {
			// only setting scrollClass to empty if it is set
			if( scrollClass ) this.setState({scrollClass: ''});
		}
	},

	_getSchoolList(){
		let { pickACollege: p, id } = this.props;

		if( p[id+'Schools'] ) return p[id+'Schools'];

		return p.prioritySchools;
	},

	render(){
		let { pickACollege: p, id } = this.props,
			{ scrollClass } = this.state,
			_id = id || 'headers',
			_schools = this._getSchoolList();

		return (
			<div id="_priority_list">

				<div className={'row collapse header '+scrollClass}>
					{ CUSTOM_HEADERS[_id].map( (hd) => <HeaderColumn key={hd.name} header={hd} /> ) }
				</div>

				<div>
					{
						_schools ?
						_schools.map( (row) => {
							switch(_id){
								case 'appOrder': return <AppOrderRow key={row.college_id+'_'+row.id} row={row} />;
								default: return <TableRow key={row.college_id+'_'+row.id} row={row} />;
							}
						} )
						: <NoListAvailable pending={p.pending} />
					}
				</div>

			</div>
		);
	}
});

const NoListAvailable = createReactClass({
	render(){
		let { pending } = this.props;

		return ( pending ) ?
			<div className="loading-spinner"><ReactSpinner color="#24b26b" /></div>
			: <div className="text-center no-schools">{'No priority schools in list'}</div>
	}
});

const mapStateToProps = (state, props) => {
	return {
		pickACollege: state.pickACollege,
	};
}

export default connect(mapStateToProps)(PriorityCollegeList);
