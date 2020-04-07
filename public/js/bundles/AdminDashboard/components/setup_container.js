// setup_container.js

import React from 'react';
import { connect } from 'react-redux';
import Portal_List from './portal_list';
import StepNavBar from './stepNavBar';
import { edit } from './../actions/profileActions';
import createReactClass from 'create-react-class'

const SetupContainer = createReactClass({
	getInitialState(){
		return {
			upper_topnav: document.getElementById('react-hide-for-admin'),
			lower_topnav: document.getElementById('react-hide-for-admin-2'),
		};
	},

	componentWillMount(){
		let { upper_topnav, lower_topnav } = this.state;

		// hide topnav during setup - temporary until topnav becomes react component
		if( upper_topnav && lower_topnav ){
			upper_topnav.style.display = 'none';
			lower_topnav.style.display = 'none';
		}

		document.body.style.backgroundColor = '#24b26b';
	},

	componentWillUnmount(){
		let { upper_topnav, lower_topnav } = this.state;

		// show again once unmounted - temporary until topnav becomes react component
		if( upper_topnav && lower_topnav ){
			upper_topnav.style.display = 'block';
			lower_topnav.style.display = 'block';
		}

		document.body.style.backgroundColor = '#fff';
	},

    render() {
    	let { children, step } = this.props;

        return (
        	<div>
				<StepNavBar step={step} />
				{ children }
            </div>
        );
    }
});

const mapStateToProps = (state, props) => {
	return {
		step: state.setup.currentStep
	};
};

export default connect(mapStateToProps)(SetupContainer);
