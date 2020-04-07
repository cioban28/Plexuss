// step3.js

import React from 'react';
import { connect } from 'react-redux';
import Loader from './../../utilities/loader';
import CustomModal from './../../utilities/customModal';
import { setupCompleted } from './../actions/setupActions';
import ManagePortalsContainer from './manage_portals_container';
import { showSetupModal } from './../actions/managePortalsActions';
import createReactClass from 'create-react-class'

const Step3 = createReactClass({
	componentWillReceiveProps(nextprops){
		if( this.props.setup.completed !== nextprops.setup.completed ) window.location.href = '/admin/filter?fromAdmin=1';
	},

	_setupCompleted(){
		//send axios call to toggle completed_signup for admin
		this.props.dispatch( setupCompleted() );
	},

	_showSetupModal(e){
		e.preventDefault();
		this.props.dispatch( showSetupModal(true) );
	},

	_closeSetupModal(e){
		e.preventDefault();
		this.props.dispatch( showSetupModal(false) );
	},

	render(){
		let { dispatch, shouldShowSetupModal, setup } = this.props;

		return (
			<div>
				{ setup.pending ? <Loader /> : null }

				<ManagePortalsContainer />

				<div style={styles.nextContainer}>
					<div style={styles.btn} className="button radius" onClick={ this._showSetupModal }>Next</div>
				</div>

				{
					shouldShowSetupModal ?
					<CustomModal backgroundClose={ () => dispatch(showSetupModal(false)) }>
						<div style={styles.container}>
							<h5 style={styles.title}>{'Congratulations! You are now ready to setup targeting.'}</h5>
							<div style={styles.btn} className="button radius" onClick={ () => dispatch(setupCompleted()) }>Setup</div>
						</div>
					</CustomModal> : null
				}
			</div>
		);
	}
});

const styles = {
	container: {
		background: '#fff',
		borderRadius: '3px',
		padding: '20px',
		boxShadow: '4px 7px 9px 0px rgba(0,0,0,0.3)',
		textAlign: 'center'
	},
	title: {
		color: '#797979',
	},
	btn: {
		backgroundColor: '#FF5C26',
		padding: '10px 30px',
		margin: '20px 0 0'
	},
	nextContainer: {
		maxWidth: '800px',
		margin: 'auto',
		padding: '0 10px',
		textAlign: 'right'
	}
}

const mapStateToProps = (state, props) => {
	return {
		setup: state.setup,
		shouldShowSetupModal: state.portals.showSetupModal
	};
};

export default connect(mapStateToProps)(Step3);
