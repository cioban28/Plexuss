// cms/SideNav/index.js

import React from 'react'
import { Link } from 'react-router'
import createReactClass from 'create-react-class'

import * as constants from './../International/constants'
import { TUITION_COST_FIELDS } from './../Cost/constants'

import './styles.scss'

export default createReactClass({
	getInitialState(){
		return {
			scrollClass: '',
			barTop: null,
		};
	},

	componentWillMount(){
		document.addEventListener('scroll', this._scrollListener);
	},

	componentWillUnmount(){
		document.removeEventListener('scroll', this._scrollListener);
	},

	_scrollListener(e){
		// adds a class to the header to make it fixed to the top when scrolled past a certain point
		let { scrollClass, barTop } = this.state,
			classname = '',
			bar = $('#_SideNav').offset().top;

		if( barTop === null ) this.state.barTop = bar;

		// only setting scrollClass to empty if it is set
		if( scrollClass ) this.setState({scrollClass: ''});

	},
	render(){
		let { items, _state, custom, program } = this.props,
			{ scrollClass } = this.state;

		return <ul id="_SideNav" className={scrollClass}>{ items.map((rt) => <NavTab key={rt.name} _route={rt} {...this.props} />) }</ul>;
	}
});

const NavTab = createReactClass({
	_isCompleted(){
		let { _route, _state, program } = this.props;

		switch( _route.id ){

			case 'scholarshipcms:list': return !!_.get(_state, 'content.overview_container');

			case 'scholarshipcms:add': return !!_.get(_state, 'content.overview_container');


			default: return false;
		}
	},

	_programChosen(){
		let { _route, program } = this.props,
			chosen = !!program,
			classes = '';

		// for /tools/scholarshipcms, no program restrictions
		if( _route.id.includes('scholarshipcms') ) return classes;

 		// determine active/inactive class
		if( !chosen && _route.id !== 'program' ) classes = ' active ';

		return classes;
	},

	render(){
		let { _route, _state, program } = this.props,
			_id = _route.id || '',
			_pc = this._programChosen(),
			_complete = this._isCompleted();

		let completedClass = _complete ? '' : 'hide';

		return (
			<li className={'tab '+_id+_pc}>
				<Link
					to={ _route.path }
					className="link"
					activeClassName="activeTab"
					onClick={ e => !!_pc && e.preventDefault() }>

						<div className="tab-inner">
							{ _route.name }
							{ _complete && <div className={"completed "+completedClass}>&#10003;</div> }
						</div>

				</Link>
			</li>
		);
	}
});
