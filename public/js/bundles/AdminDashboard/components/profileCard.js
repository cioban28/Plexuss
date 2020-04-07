// profileCard.js

import React from 'react'
import { connect } from 'react-redux'
import ProfileCard_Back from './profileCard_Back'
import { edit } from './../actions/profileActions'
import ProfileCard_Front from './profileCard_Front'
import ShowProfileCardOptionCard from './showProfileCardOptionCard'
import createReactClass from 'create-react-class'

const ProfileCard = createReactClass({
	render(){
		let { customLabel } = this.props, labelStyles = styles.label;

		if( customLabel ){
	        labelStyles = Object.assign({}, styles.label, customLabel);
		}

		return (
			<div style={styles.card}>
				<div style={labelStyles}>Preview</div>

				<div style={styles.column}>
					<div style={labelStyles}>Front</div>
					<ProfileCard_Front {...this.props} />
				</div>

				<div style={styles.column}>
					<div style={labelStyles}>Back</div>
					<ProfileCard_Back {...this.props} />
				</div>

				<ShowProfileCardOptionCard {...this.props} />
			</div>
		);
	}
});

const styles = {
	card: {
		textAlign: 'center',
	},
	column: {
		display: 'inline-block',
		verticalAlign: 'top',
		margin: '0 0 0 20px'
	},
	label: {
		color: '#797979'
	}
};

const mapStateToProps = (state, props) => {
	return {
		user: state.user,
	};
};

export default connect(mapStateToProps)(ProfileCard);
