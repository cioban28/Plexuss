// setup_background.js

import React from 'react';
import { connect } from 'react-redux';
import createReactClass from 'create-react-class'

const SetupBackground = createReactClass({
    render() {
        return (
        	<div>
        		set up background here
            </div>
        );
    }
});

const mapStateToProps = (state, props) => {
	return {
		user: state.user,
		invalidFields: state.invalidFields
	};
};

export default connect(mapStateToProps)(SetupBackground);
