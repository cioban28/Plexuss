// profile.js

import React from 'react';
import { connect } from 'react-redux'

import Tooltip from './../../utilities/tooltip'

import { edit, getProfile } from './../actions/profileActions'
import { profileFormValid } from './../actions/validatorActions'
import createReactClass from 'create-react-class'

const Profile = createReactClass({
	getInitialState(){
		return {
			workingSinceOptions: [],
			maxLen: 150,
			_fnameValid: false,
			_fnameValidated: false,
			_lnameValid: false,
			_lnameValidated: false,
			_titleValid: false,
			_titleValidated: false,
			_working_sinceValid: false,
			_working_sinceValidated: false,
			_blurbValid: false,
			_blurbValidated: false,
			focused: ''
		};
	},

	componentWillMount(){
		let { user, routeParams } = this.props;

		// build working since select field options
		this.state.workingSinceOptions = this._buildWorkingSinceOptions();
	},

	componentDidMount(){
		let { dispatch, user } = this.props;

		//if we don't yet have profile info, get it
		if( !user.initProfile ) this.props.dispatch( getProfile() );
		//if temp profile is set
		if( user.temporaryAlternateProfile ) this._validateFromProps(user.temporaryAlternateProfile);
	},

	componentWillReceiveProps(np){
		let { user } = this.props;

		//update if user info is set in next props
        if( np.user.id !== user.id ) this._validateFromProps(np.user);
        //update if in next props temp profile is null and not null in this props
        else if( !np.user.temporaryAlternateProfile && user.temporaryAlternateProfile ){
            this._validateFromProps(user);
        }
        //update temp profile if it is set in next props, but not set in this props
        else if( np.user.temporaryAlternateProfile && !user.temporaryAlternateProfile ){
            this._validateFromProps(np.user)
        }
	},

	_buildWorkingSinceOptions(){
		let options = [], yr = new Date().getFullYear();

		options.push( <option key={-1} value="" disabled="disabled">Select a date...</option> );

		// create select options based on the current year to the past 20 years
		for(var i = 0; i < 20; i++){
			options.push(<option key={i} value={yr}>{yr}</option>)
			yr--;
		}

		return options;
	},

	_edit(e){
		//edit profile inputs
		let { dispatch, user } = this.props,
			id = e.target.id,
			val = e.target.value,
			fieldName = e.target.getAttribute('name');

		this._validateFromDOM(e);

		// dispatch different actions based on if user is alternate or original
		if( user.temporaryAlternateProfile ) this._editAlternateProfile(val, fieldName, e);
		else dispatch( edit(val, fieldName) );
	},

	_editAlternateProfile(val, fieldName, e){
        let { dispatch, user } = this.props, tmpCopy = Object.assign({}, user.temporaryAlternateProfile);

		this._validateFromDOM(e);

        // currently editing alternate profile a little differently than original user profile
        // passing entire profile object rather than just value and field name
        tmpCopy[fieldName] = val;
        dispatch( edit(tmpCopy, 'alternateProfile') );
    },

    _validateFromDOM(e){
    	this._validate({id: e.target.id, value: e.target.value});
    	this.setState({focused: this.state.focused});
		this._updateValidator();
    },

    _validateFromProps(user){
    	//loop through refs to get values from np(user), to validate
    	_.forOwn( this.refs, (val, key) => {
    		let tmp = ''+key.slice(1, key.length).trim();
    		if( _.has(user, tmp) ) this._validate({id: key, value: user[tmp], fromProps: 'props'});
    	});

    	// update validator value to show/hide error msg
    	this.setState({focused: this.state.focused});
		this._updateValidator();
    },

    _validate(obj){
    	let valid = false;

    	switch(obj.id){
    		case '_fname':
    		case '_lname':
    		case '_title':
    			if( /^[A-Za-z -]+$/.test(obj.value) && obj.value ) valid = true;
		    	break;

    		case '_working_since':
    		case '_blurb':
    			if( obj.value ) valid = true;
		    	break;

    		default: return;
    	}

    	this.state.focused = obj.fromProps || obj.id;
    	this.state[obj.id+'Valid'] = valid;
    	this.state[obj.id+'Validated'] = true;
    },

    _updateValidator(){
    	let { _fnameValid, _lnameValid, _titleValid, _working_sinceValid, _blurbValid } = this.state, valid = false;

    	// valid if fname, lname, title, working since, and blurb pass validation from _validate()
    	valid = _fnameValid && _lnameValid && _titleValid && _working_sinceValid && _blurbValid;

		this.props.dispatch( profileFormValid( valid ) );
    },

    render() {
        var { user, invalidFields, routeParams, customLabel, customTip } = this.props,
        	{ workingSinceOptions, maxLen, _fnameValid, _fnameValidated, _lnameValid, _lnameValidated,
        		_working_sinceValid, _working_sinceValidated, _titleValid, _titleValidated, _blurbValid, _blurbValidated, focused } = this.state,
        	charLen = styles.len, labelStyles = styles.label,
        	charStyles = styles.chars, ttip = styles.tooltip, blurbLen = 0, currLen = 0;

        	//set user to temporary user if editing other profile
        	if( user.temporaryAlternateProfile && routeParams.id ) user = user.temporaryAlternateProfile;

        	//if custom styling is passed, add custom styling
        	if( customLabel ){
        		labelStyles = Object.assign({}, styles.label, customLabel);
        		charStyles = Object.assign({}, styles.chars, customLabel);
        		charLen = Object.assign({}, styles.len, customLabel);
        		ttip = Object.assign({}, styles.tooltip, customTip);
        	}

        	//adjust char text based on char length
        	blurbLen = user.blurb ? user.blurb.length : 0;
        	currLen = maxLen - blurbLen;
        	if( !currLen ) charLen = styles.max;

        return (
            <div>

            	{/* fname */}
            	<div className="row">
            		<div className="column small-4 text-left">
						<label style={labelStyles} htmlFor="_fname" className="inline left">First Name</label>
					</div>
					<div className="column small-8">
						<input
							id="_fname"
							ref="_fname"
							type="text"
							name="fname"
							placeholder="Enter first name..."
							value={user.fname || ''}
							style={ focused === '_fname' ? (_fnameValid ? styles.good : styles.bad) : (_fnameValidated && !_fnameValid ? styles.bad : {}) }
							onFocus={this._validateFromDOM}
							onBlur={ () => this.setState({focused: ''}) }
							onChange={this._edit}
							required />
					</div>
				</div>

				{/* lname */}
				<div className="row">
            		<div className="column small-4 text-left">
						<label style={labelStyles} htmlFor="_lname" className="inline left">Last Name</label>
					</div>
					<div className="column small-8">
						<input
							id="_lname"
							ref="_lname"
							type="text"
							name="lname"
							placeholder="Enter last name..."
							value={user.lname || ''}
							style={ focused === '_lname' ? (_lnameValid ? styles.good : styles.bad) : (_lnameValidated && !_lnameValid ? styles.bad : {}) }
							onFocus={this._validateFromDOM}
							onBlur={ () => this.setState({focused: ''}) }
							onChange={this._edit}
							required />
					</div>
				</div>

            	{/* title */}
            	<div className="row">
            		<div className="column small-4 text-left">
						<label style={labelStyles} htmlFor="_title" className="inline left">Title</label>
					</div>
					<div className="column small-8">
						<input
							id="_title"
							ref="_title"
							type="text"
							name="title"
							placeholder="Enter work title..."
							value={user.title || ''}
							style={ focused === '_title' ? (_titleValid ? styles.good : styles.bad) : (_titleValidated && !_titleValid ? styles.bad : {}) }
							onFocus={this._validateFromDOM}
							onBlur={ () => this.setState({focused: ''}) }
							onChange={this._edit}
							required />
					</div>
				</div>

            	{/* working since */}
            	<div className="row">
            		<div className="column small-4 text-left">
						<label style={labelStyles} htmlFor="_working_since" className="inline left">Working since</label>
					</div>
					<div className="column small-8">
						<select
							id="_working_since"
							ref="_working_since"
							name="working_since"
							value={user.working_since || ''}
							style={ _working_sinceValidated && !_working_sinceValid ? styles.bad : {} }
							onFocus={this._validateFromDOM}
							onBlur={ () => this.setState({focused: ''}) }
							onChange={this._edit}
							required >
							{ workingSinceOptions }
						</select>
					</div>
				</div>

            	{/* blurb */}
            	<div className="row">
            		<div className="column small-4 text-left">
						<label style={labelStyles} htmlFor="_blurb" className="left">Blurb
							<Tooltip toolTipStyling={ttip} tipStyling={styles.tip}>
								<h4 style={styles.head}>Why do I need a blurb?</h4>
								<div>Add some content about yourself in the Blurb section below to give students more information about you.</div>
								<br />
								<div>Example Blurb:</div>
								<div style={styles.example}>I have worked in admissions and with international students for several years at several institutions. I value and cherish cultures and difference.</div>
							</Tooltip>
						</label>
					</div>
					<div className="column small-8">
						<textarea
							id="_blurb"
							ref="_blurb"
							onChange={this._edit}
							value={user.blurb || ''}
							rows={5}
							name="blurb"
							maxLength={maxLen}
							required
							style={ focused === '_blurb' ? (_blurbValid ? {...styles.tarea, ...styles.good} : {...styles.tarea, ...styles.bad}) : (_blurbValidated && !_blurbValid ? {...styles.bad, ...styles.tarea} : styles.tarea) }
							onFocus={this._validateFromDOM}
							onBlur={ () => this.setState({focused: ''}) }
	        		        placeholder="Ex: I have worked in admissions for international students for several
	                		 	years here at University. Feel free to ask me questions about admission!" />
					</div>
					<div className="column small-12 text-right" style={charStyles}>Characters left: <span style={charLen}>{currLen}</span></div>
				</div>

           	</div>
        );
    }
});

const styles = {
	tip: {
		width: '400px'
	},
	tooltip:{
		color: '#797979',
		border: '1px solid #797979',
	},
	head: {
		color: '#fff'
	},
	example: {
		color: '#fff',
		fontSize: '12px'
	},
	label: {
		color: '#797979'
	},
	chars: {
		color: '#797979',
		fontSize: '10px',
		margin: '0 0 15px 0'
	},
	len: {
		color: '#24b26b'
	},
	max: {
		color: 'firebrick'
	},
	tarea: {
		margin: 0
	},
	bad: {
		border: '1px solid firebrick',
	},
	good: {
		border: '1px solid #24b26b',
	},
};

const mapStateToProps = (state, props) => {
	return {
		user: state.user,
		invalidFields: state.invalidFields,
		currentStep: state.setup.currentStep
	};
};

export default connect(mapStateToProps)(Profile);
