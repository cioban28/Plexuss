// addUserToPortalDropdown.js

import _ from 'lodash'
import React from 'react'
import { connect } from 'react-redux'

import { getPortals } from './../actions/managePortalsActions'
import { updateNewUsersPortalList } from './../actions/manageUsersActions'
import createReactClass from 'create-react-class'

const AddUserToPortalDropdown = createReactClass({
	getInitialState(){
		return {
			open: false
		};
	},

	componentWillMount(){
		let { dispatch, portals } = this.props;
		if( !portals.initDone ) dispatch( getPortals() ); //get portals if we don't already have it to populate list
	},

	render(){
		let { users, portals, err } = this.props,
			{ active_portals } = portals,
			{ open } = this.state,
			good = Object.assign({}, styles.listWrapper, {border: '1px solid #24b26b'}),
			bad = Object.assign({}, styles.listWrapper, {border: '1px solid firebrick'});

		return (
			<div style={ err ? (users.newUsersPortals.length > 0 ? good : bad) : styles.listWrapper }>
				<div onClick={() => this.setState({open: !open})}>
					{['Select portals', <span key={'downarrow'}>&#9662;</span>]}
				</div>

				{ open ?
					<ul>
						{/* for each active_portal, check if each match up with any of the chosen portals selected */}
						{
							active_portals.map((portal) => {
								let match = users.newUsersPortals.map( (chosenPortal) => portal.name === chosenPortal.name );
								return <PortalOption key={portal.hashedid} portal={portal} {...this.props} chosen={match && _.indexOf(match, true) > -1} />
							})
						}
					</ul>
					: null
				}
			</div>
		);
	}
});

const PortalOption = createReactClass({
	render(){
		let { dispatch, portal, chosen } = this.props;

		return (
			<li>
				<input id={portal.hashedid }
					   type="checkbox"
					   style={styles.checkbox}
					   checked={chosen}
					   onChange={ () => dispatch(updateNewUsersPortalList(portal)) }
					   value={portal.hashedid} />

				<label htmlFor={portal.hashedid} style={styles.portalName}>{portal.name}</label>
			</li>
		);
	}
});

const styles = {
	portalName: {
		color: '#797979',
		fontSize: '14px'
	},
	listWrapper: {
		display: 'inline-block',
		color: '#797979',
		verticalAlign: 'top',
		border: '1px solid #ddd',
		borderRadius: '2px',
		padding: '0 5px',
		cursor: 'pointer',
		fontSize: '15px',
		userSelect: 'none',
	},
	checkbox: {
		margin: 0
	},
}

const mapStateToProps = (state, props) => {
	return {
		users: state.users,
		portals: state.portals,
	};
};

export default connect(mapStateToProps)(AddUserToPortalDropdown);
