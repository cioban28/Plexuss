// /ReviewApp/SectionHeader.js

import React from 'react'
import { Link } from 'react-router-dom'

import { updateProfile } from './../../actions/Profile'

class SectionHeader extends React.Component {
	constructor(props) {
		super(props)
		this._goEdit = this._goEdit.bind(this)
	}

	_goEdit(){
		let { dispatch, route } = this.props;
		dispatch( updateProfile({coming_from: 'review'}) );
		window.location.href = '/college-application/'+route.id + window.location.search;
	}

	render(){
		let { route, customName, noEdit } = this.props;

		return (
			<div className="rev-header">
				<div>{ customName || _.get(route, 'name', '') }</div>
				{ !noEdit && (window.location.pathname.includes("social") ?
					<Link to={'/social/one-app/'+route.id + window.location.search}><span/></Link>
					:
					<span onClick={ this._goEdit } />
				)}

			</div>
		);
	}
}

export default SectionHeader;