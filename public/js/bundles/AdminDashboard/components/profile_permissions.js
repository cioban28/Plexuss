// profile_permissions.js

import _ from 'lodash'
import React from 'react'
import { connect } from 'react-redux'

import Tooltip from './../../utilities/tooltip'

import { edit } from './../actions/profileActions'
import { profilePermissionsFormValid } from './../actions/validatorActions'
import createReactClass from 'create-react-class'

const ProfilePermissions = createReactClass({
    getInitialState(){
        return {
            _emailValid: false,
            _emailValidated: false,
            _departmentValid: false,
            _departmentValidated: false,
            focused: '',
        };
    },

    componentDidMount(){
        let { dispatch, user } = this.props;
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

    _enterPress(e){
        var { dispatch, user } = this.props,
            code = e.keyCode || e.which,
            val = '', fieldName = '';

        if( code === 13 ){
            e.preventDefault();
            val = false;
            fieldName = 'addDeptInputVisible';

            if( user.temporaryAlternateProfile ) this._editAlternateProfile(val, fieldName);
            else dispatch( edit(val, fieldName) ); //on enter key press, add department
        }else{
            val = e.target.value;
            fieldName = e.target.getAttribute('name');

            if( user.temporaryAlternateProfile ) this._editAlternateProfile(val, fieldName);
            else dispatch( edit(val, fieldName) );
        }
    },

    _addDept(){
        let { dispatch, user } = this.props,
            val = false,
            fieldName = 'addDeptInputVisible';

        if( user.temporaryAlternateProfile ) this._editAlternateProfile(val, fieldName);
        else dispatch( edit(val, fieldName) );
    },

    _edit(e){
        let val = e.target.value,
            id = e.target.id,
            fieldName = e.target.getAttribute('name'),
            { dispatch, user } = this.props;

        this._validateFromDOM(e);


        if( fieldName === 'department' && val === 'add_department' ){
            val = true;
            fieldName = 'addDeptInputVisible';
        }

        if( user.temporaryAlternateProfile ) this._editAlternateProfile(val, fieldName, e);
        else dispatch( edit(val, fieldName) );
    },

    _editAlternateProfile(val, fieldName, e){
        let { dispatch, user } = this.props, tmpCopy = Object.assign({}, user.temporaryAlternateProfile);

        this._validateFromDOM(e);

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
            case '_email':
                if( /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/.test(obj.value) && obj.value ) valid = true;
                break;

            case '_department':
                if( obj.value ) valid = true;
                break;

            default: return;
        }

        this.state.focused = obj.fromProps || obj.id;
        this.state[obj.id+'Valid'] = valid;
        this.state[obj.id+'Validated'] = true;
    },

    _updateValidator(){
        let { _emailValid, _departmentValid } = this.state, valid = false;

        // valid if email, department pass validation from _validate()
        valid = _emailValid && _departmentValid;

        this.props.dispatch( profilePermissionsFormValid( valid ) );
    },

    render() {
        var { user, routeParams, customLabel, customTip } = this.props,
            { _emailValid, _emailValidated, _departmentValid, _departmentValidated, focused } = this.state,
            userPortals = '', labelStyles = styles.label, ttip = styles.tooltip,
            roleStyles = styles.role, displayInfoStyles = styles.displayinfo;

        if( user.temporaryAlternateProfile && routeParams.id ) user = user.temporaryAlternateProfile;

        if( customLabel ){
            ttip = Object.assign({}, ttip, customTip);
            roleStyles = Object.assign({}, roleStyles, customLabel);
            labelStyles = Object.assign({}, labelStyles, customLabel);
            displayInfoStyles = Object.assign({}, displayInfoStyles, customLabel);
        }

        if( user.portal_info ){
            userPortals = user.portal_info.map((portal, i) => {
                if( i === 0 ) return portal.portal_name;
                return ', ' + portal.portal_name;
            });
        }

        return (
            <div style={styles.container}>

                {/* portals */}
                <div className="row">
                    <div className="column small-4 text-left">
            	       <label style={labelStyles} htmlFor="_portals" className="inline left">Assigned Portals</label>
                    </div>
                    <div className="column small-8">
                		<input id="_portals" type="text" name="portals" value={user.portal_info ? userPortals.join('') : ''} disabled="disabled" onChange={this._edit} required />
                    </div>
                </div>

                {/* permissions */}
                <div className="row">
                    <div className="column small-4 text-left">
                        <label style={labelStyles} htmlFor="_permissions" className="inline left">Permissions
                            <Tooltip toolTipStyling={ttip} tipStyling={styles.tipPermissions}>
                                <table style={styles.table}>
                                    <caption className="text-left"><h3 style={styles.head}>Permissions</h3></caption>
                                    <tbody>
                                        <tr>
                                            <TableData tdType="th" />
                                            <TableData tdType="th" val={'Recruitment'} />
                                            <TableData tdType="th" val={'Texting'} />
                                            <TableData tdType="th" val={'Advertising'} />
                                        </tr>
                                        <tr style={styles.tr}>
                                            <TableData val={'Home'} />
                                            <TableData checked="1" />
                                            <TableData checked="2" />
                                            <TableData checked="3" />
                                        </tr>
                                        <tr>
                                            <TableData val={'Recruitment'} />
                                            <TableData checked="4" />
                                            <TableData />
                                            <TableData />
                                        </tr>
                                        <tr style={styles.tr}>
                                            <TableData val={'Communication'} />
                                            <TableData checked="5" />
                                            <TableData checked="6" />
                                            <TableData />
                                        </tr>
                                        {/*<tr>
                                            <TableData val={'Advertisement'} />
                                            <TableData />
                                            <TableData />
                                            <TableData checked="7" />
                                        </tr>
                                        <tr style={styles.tr}>
                                            <TableData val={'Analytics'} />
                                            <TableData checked="8" />
                                            <TableData checked="9" />
                                            <TableData checked="10" />
                                        </tr>
                                        <tr>
                                            <TableData val={'Tools & Apps'} />
                                            <TableData checked="11" />
                                            <TableData checked="12" />
                                            <TableData checked="13" />
                                        </tr>*/}
                                    </tbody>
                                </table>
                            </Tooltip>
                        </label>
                    </div>
                    <div className="column small-8">
                        <input id="_permissions" type="text" name="permissions" value={user.permissions || ''} disabled="disabled" onChange={this._edit} required />
                    </div>
                </div>

                {/* role */}
                <div className="row">
                    <div className="column small-4 text-left">
                		<label style={labelStyles} htmlFor="_role" className="inline left">Role
                            <Tooltip toolTipStyling={ttip} tipStyling={styles.tipRole}>
                                <table style={styles.table}>
                                    <caption className="text-left"><h3 style={styles.head}>Roles</h3></caption>
                                    <tbody>
                                        <tr>
                                            <TableData tdType="th" />
                                            <TableData tdType="th" val={'Admin'} />
                                            <TableData tdType="th" val={'User'} />
                                        </tr>
                                        <tr style={styles.tr}>
                                            <TableData val={'Add/remove users'} />
                                            <TableData checked="1" />
                                            <TableData />
                                        </tr>
                                        <tr>
                                            <TableData val={'Manage user permissions'} />
                                            <TableData checked="2" />
                                            <TableData />
                                        </tr>
                                        <tr style={styles.tr}>
                                            <TableData val={'Manage all portals'} />
                                            <TableData checked="3" />
                                            <TableData />
                                        </tr>
                                        <tr>
                                            <TableData val={'Manage targeting'} />
                                            <TableData checked="4" />
                                            <TableData checked="5" />
                                        </tr>
                                        <tr style={styles.tr}>
                                            <TableData val={'Manage own portal'} />
                                            <TableData checked="6" />
                                            <TableData checked="7" />
                                        </tr>
                                    </tbody>
                                </table>
                            </Tooltip>
                        </label>
                    </div>
                    <div className="column small-8">
        	           	{/*<input id="_role" type="text" name="role" value={user.role || ''} disabled="disabled" onChange={this._edit} required />*/}
                        <div style={roleStyles}>{ _.capitalize(user.role) }</div>
                    </div>
                </div>

                {/* department */}
                <div className="row">
                    <div className="column small-4 text-left">
                        <label style={labelStyles} htmlFor="_department" className="inline left">Department*</label>
                    </div>
                    <div className="column small-8">
                        {
                            !user.addDeptInputVisible ?
                            <select
                                id="_department"
                                ref="_department"
                                type="text"
                                name="department"
                                value={user.added_department ? user.added_department : (user.department || '')}
                                onChange={this._edit}
                                style={ focused === '_department' ? (_departmentValid ? styles.good : styles.bad) : (_departmentValidated && !_departmentValid ? styles.bad : {}) }
                                onFocus={this._validateFromDOM}
                                onBlur={ () => this.setState({focused: ''}) }
                                required>
                                    <option value="" disabled="disabled">Select a department...</option>
                                    <option value="International recruitment">International recruitment</option>
                                    <option value="Domestic recruitment">Domestic recruitment</option>
                                    <option value="Marketing">Marketing</option>
                                    <option value="Administration">Administration</option>
                                    { user.added_department ? <option value={user.added_department || ''}>{user.added_department || ''}</option> :  null }
                                    <option value="add_department">Add department</option>
                            </select> : null
                        }

                        {
                            user.addDeptInputVisible ?
                            <div style={styles.relative}>
                                <input id="_added_department" style={styles.pad} ref="_add_department" type="text" name="added_department" onKeyUp={this._enterPress} autoFocus required />
                                <div style={styles.ok} onClick={this._addDept}>ok</div>
                            </div>
                            :
                            null
                        }
                    </div>
                </div>

                {/* email */}
                <div className="row">
                    <div className="column small-4 text-left">
                        <label style={Object.assign({}, labelStyles, {margin: 0})} htmlFor="_email" className="inline left">Current email*</label>
                    </div>
                    <div className="column small-8">
                		<input
                            id="_email"
                            ref="_email"
                            type="email"
                            name="email"
                            value={user.email || ''}
                            placeholder="Ex: university@email.com"
		                  	onChange={this._edit}
                            style={ focused === '_email' ? (_emailValid ? {...styles.input, ...styles.good} : {...styles.input, ...styles.bad}) : (_emailValidated && !_emailValid ? {...styles.input, ...styles.bad} : styles.input) }
                            onFocus={this._validateFromDOM}
                            onBlur={ () => this.setState({focused: ''}) }
                            required />
                    </div>
                </div>

                {/* not displayed msg */}
                <div className="row" style={styles.nodisplayrow}>
                    <div className="column small-8 small-offset-4">
                        <small style={displayInfoStyles}>*We do not display this information.</small>
                    </div>
                </div>

                { user.err_msg ? <div className="text-right" style={styles.err}><small>{user.err_msg}</small></div> : null }

           	</div>
        );
    }
});

const TableData = createReactClass({
    render(){
        let { tdType, val, checked } = this.props,
            checkmark = [<span key={'checkmark-'+checked} style={styles.check}>&#10003;</span>];

        return ( tdType === 'th' ) ?
                <th className="text-left" style={styles.head}>{val}</th> :
                <td style={styles.head} className={checked? 'text-center' : 'text-left'}>
                    { checked ? checkmark : val}
                </td> ;
    }
});

const styles = {
    btn: {
        backgroundColor: '#FF5C26',
        padding: '10px 75px'
    },
    container: {
        maxWidth: '500px',
        margin: 'auto'
    },
    head: {
        color: '#fff'
    },
    label:{
        color: '#797979'
    },
    tooltip:{
        color: '#797979',
        border: '1px solid #797979'
    },
    tipRole: {
        width: '320px'
    },
    tipPermissions: {
        width: '412px'
    },
    table: {
        backgroundColor: 'transparent',
        color: '#fff',
        border: 'none'
    },
    tr: {
        backgroundColor: '#333'
    },
    check: {
        color: '#24b26b',
        fontSize: '24px'
    },
    role: {
        padding: '10px 0',
        color: '#797979',
        fontSize: '14px'
    },
    relative: {
        position: 'relative'
    },
    pad: {
        padding: '0.5rem 2.5rem 0.5rem 0.5rem'
    },
    ok: {
        position: 'absolute',
        top: 0,
        right: 0,
        color: 'rgb(255, 92, 38)',
        borderLeft: '1px solid #ddd',
        fontSize: '20px',
        padding: '2px 7px',
        cursor: 'pointer'
    },
    err: {
        color: '#202020',
        padding: '0 6px'
    },
    input: {
        margin: 0
    },
    displayinfo: {
        color: '#797979',
    },
    nodisplayrow: {
        margin: '0 0 20px'
    },
    bad: {
        border: '1px solid firebrick',
    },
    good: {
        border: '1px solid #24b26b',
    }
}

const mapStateToProps = (state, props) => {
    return {
        user: state.user,
    };
};

export default connect(mapStateToProps)(ProfilePermissions);
