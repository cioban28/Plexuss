// showProfileCardOptionCard.js

import React from 'react'
import { connect } from 'react-redux'
import PreviewModal from './previewModal'
import { updateUserDisplaySettings } from './../actions/profileActions'
import createReactClass from 'create-react-class'

const ShowProfileCardOptionCard = createReactClass({
	getInitialState(){
		return {
			showPreview: false,
			previewPage: ''
		};
	},

	showPreview(e){
		this.setState({showPreview: true, previewPage: e.target.id});
	},

	closePreview(e){
		this.setState({showPreview: false});
	},

	_updateFrontpage(e){
		let { dispatch, user } = this.props, newVal = 0;

		if( !user.show_on_front_page ) newVal = 1;

		dispatch( updateUserDisplaySettings(Object.assign({}, user, {show_on_front_page: newVal}), true) );
	},

	_updateCollegepage(e){
		let { dispatch, user } = this.props, newVal = 0;

		if( !user.show_on_college_page ) newVal = 1;

		dispatch( updateUserDisplaySettings(Object.assign({}, user, {show_on_college_page: newVal})) );
	},

	render(){
		let { showPreview, previewPage } = this.state,
			{ dispatch, user, routeParams, customLabel } = this.props,
			isAlternate = !!user.temporaryAlternateProfile, labelStyles = styles.label;

	        if( isAlternate && routeParams.id ) user = user.temporaryAlternateProfile;

	        if( customLabel ){
		        labelStyles = Object.assign({}, styles.label, customLabel);
			}

		return (
			<div>
				<div style={labelStyles}>Show Profile on these pages</div>

				{ user.display_setting_err && user.display_setting_err.has_err ? <div style={styles.err}>{user.display_setting_err.msg}</div> : null }

				<div style={styles.container}>
					{
						user.super_admin && !user.orgs_first_user ?
						<input type="checkbox"
							   id="show-on-frontpage-check"
							   checked={!!user.show_on_front_page}
							   value={user.show_on_front_page || 0}
							   onChange={this._updateFrontpage} /> :
						<input type="checkbox" id="show-on-frontpage-check" checked={!!user.show_on_front_page} disabled />
					}
					<label htmlFor="show-on-frontpage-check" style={styles.viewLabel}>Front page</label>
					<span id="frontpage-preview" style={styles.viewMore} onClick={this.showPreview}>view</span>

					<br />

					{
						user.super_admin && !user.orgs_first_user ?
						<input type="checkbox"
							   id="show-on-collegepage-check"
							   value={user.show_on_college_page || 0}
							   checked={!!user.show_on_college_page}
							   onChange={this._updateCollegepage} /> :
						<input type="checkbox" id="show-on-collegepage-check" checked={!!user.show_on_college_page} disabled />
					}
					<label htmlFor="show-on-collegepage-check" style={styles.viewLabel}>College page</label>
					<span id="collegepage-preview" style={styles.view} onClick={this.showPreview}>view</span>
				</div>

				{ showPreview ? <PreviewModal page={previewPage} close={this.closePreview} /> : null }
			</div>
		);
	}
});

const styles = {
	container: {
		maxWidth: '170px',
		borderRadius: '5px',
		backgroundColor: '#eee',
		margin: 'auto',
		textAlign: 'left',
		padding: '10px 0 0 10px'
	},
	view: {
		color: '#2ba6cb',
		cursor: 'pointer',
		margin: '0 0 0 10px',
		fontSize: '14px',
		textDecoration: 'underline'
	},
	viewMore: {
		fontSize: '14px',
		color: '#2ba6cb',
		cursor: 'pointer',
		margin: '0 0 0 24px',
		textDecoration: 'underline'
	},
	tooltip: {
		position: 'relative',
		display: 'inline-block',
		border: '1px solid #fff'
	},
	tip: {
		right: 0,
		width: '400px'
	},
	label: {
		color: '#797979',
		margin: '20px 0 0'
	},
	viewLabel: {
		color: '#797979',
		margin: '0 0 0 5px',
		verticalAlign: 'middle',
	},
	err: {
		color: '#FF5C26',
		fontSize: '14px'
	}
};

const mapStateToProps = (state, props) => {
	return {
		user: state.user,
	};
};

export default connect(mapStateToProps)(ShowProfileCardOptionCard);
