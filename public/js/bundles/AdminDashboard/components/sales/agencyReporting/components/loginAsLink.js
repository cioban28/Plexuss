// loginAsLink.js

import React from 'react'

import { connect } from 'react-redux'

import { bindActionCreators } from 'redux'

import styles from './styles.scss'

import moment from 'moment'

import * as agencyReportingActions from '../../../../actions/agencyReportingActions.js'

export default class LoginAsLink extends React.Component {
	constructor(props) {
		super(props);

		this.state = { show_modal: false };
		
		this._handleClick = this._handleClick.bind(this);
	}

	_handleClick() {
		const { link } = this.props;

		window.open(link, '_blank');
	}

	render() {
		return (
			<div className='login-as-link' onClick={this._handleClick}>
				Login as
			</div>
		);
	}
}