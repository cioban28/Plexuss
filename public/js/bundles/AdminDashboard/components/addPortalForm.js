// addPortalForm.js

import React from 'react'
import _ from 'underscore'
import { connect } from 'react-redux'

import Tooltip from './../../utilities/tooltip'

import * as portalActions from './../actions/managePortalsActions'
import createReactClass from 'create-react-class'

const AddPortalForm = createReactClass({
	getInitialState(){
		return{
			formOpen: false,
			_nameValid: false,
			_nameValidated: false,
			focused: '',
			portalAlreadyExists: false,
			alreadyCreatedErrMsg: 'There is already a portal created with that name. Please use a different name.'
		};
	},

	_onEnter(e){
		var code = e.keyCode || e.which;
		if( code === 13 ) this._addPortal(e); //on enter key press, addPortal
	},

	_addPortal(e){
		e.preventDefault();
		var { dispatch } = this.props,
			{ _nameValid, _nameValidated } = this.state,
			val = this.refs.addPortalInput.value;

		if( _nameValid && _nameValidated ){
			// reset validation state when adding portal
			this.setState({formOpen: false, _nameValid: false, _nameValidated: false, focused: '', portalAlreadyExists: false});
			dispatch( portalActions.updatePortal(val, 'create', null) ); //add new portal
		}
	},

	_validate(e){
		let { active_portals, deactivated_portals } = this.props.portals,
			name = e.target.value, valid = false, portal_already_exists = null;

		//find name in current list of active portals, if any
		if( active_portals && active_portals.length > 0 )
			portal_already_exists = _.findWhere(active_portals, {name: name});

		//and check if in list of deactivated portal if list isn't empty and nothing was found in active portals, if any
		if( deactivated_portals && deactivated_portals.length > 0 && !portal_already_exists )
			portal_already_exists = _.findWhere(deactivated_portals, {name: name});

		// if portal name is not falsy, passes regex, is less than 100 chars, and portal name doesn't already exist then it's valid
    	if( name && /^[A-Za-z0-9 -]+$/.test(name) && name.length <= 30 && !portal_already_exists ) valid = true;

    	this.setState({
    		_nameValid: valid,
    		_nameValidated: true,
    		focused: e.target.id,
    		portalAlreadyExists: !!portal_already_exists,
    	});
	},

	render(){
		let { dispatch, portals } = this.props,
			{ formOpen, focused, _nameValid, _nameValidated, portalAlreadyExists, alreadyCreatedErrMsg } = this.state;

		return ( formOpen ) ?
			<div className="row">
				{ portalAlreadyExists ? <div style={styles.err}>{alreadyCreatedErrMsg}</div> : null }
				<div className="small-8 column" style={styles.col}>
					<input
						type="text"
						name="portal_name"
						placeholder="Enter name of portal"
						ref="addPortalInput"
						id="add_portal_input"
						style={ focused === 'add_portal_input' ? (_nameValid ? styles.good : styles.bad) : (_nameValidated && !_nameValid ? styles.bad : {}) }
						onFocus={this._validate}
						onChange={ this._validate }
						onBlur={ () => this.setState({focused: ''}) }
						maxLength={30}
						onKeyPress={ this._onEnter } />
				</div>
				<div className="small-2 column">
    				<div
    					className="button radius"
    					style={ styles.cancel }
    					onClick={ () => this.setState({formOpen: false, _nameValid: false, _nameValidated: false}) }>
    						Cancel
    				</div>
				</div>
				<div className="small-2 column">
    				<button
    					className="button radius"
    					style={ styles.save }
    					disabled={ !_nameValid }
    					onClick={ this._addPortal }>
    						Save
    				</button>
				</div>
			</div>
			:
			<div>
				<div
					id="create_portal"
	    			className="button radius"
	    			onClick={ () => this.setState({formOpen: true}) }
	    			style={ styles.create }>
	    				Create Portal
	    		</div>
	    		<Tooltip toolTipStyling={styles.tooltip}>
					<div>{'In order to use Plexuss, you need to create a new portal to receive targeting.'}</div>
				</Tooltip>
			</div>
	}
});

const styles = {
	create: {
    	backgroundColor: '#FF5C26',
    	padding: '10px 30px',
    	margin: '0 10px 20px 0'
    },
    cancel: {
    	backgroundColor: '#797979',
    	padding: '9px 35px 10px'
    },
    save: {
    	padding: '10px 43px'
    },
    col: {
    	paddingLeft: 0
    },
	tooltip: {
		color: '#797979',
		border: '1px solid #797979',
	},
	bad: {
		border: '1px solid firebrick',
	},
	good: {
		border: '1px solid #24b26b',
	},
	err: {
		color: 'firebrick',
		fontSize: '12px',
	}
}

const mapStateToProps = (state, props) => {
	return {
		portals: state.portals,
	};
};

export default connect(mapStateToProps)(AddPortalForm);
