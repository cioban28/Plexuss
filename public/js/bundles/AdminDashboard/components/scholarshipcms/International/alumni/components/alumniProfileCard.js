// alumniProfileCard.js

import React from 'react'
import selectn from 'selectn'
import createReactClass from 'create-react-class'

import Tooltip from './../../../../../../utilities/tooltip'

import { REMOVE_ALUMNI_ROUTE } from './../../constants'
import { editAlum, removeItem } from './../../../../../actions/internationalActions'

const MAX_CHAR_LEN = 42;

export default createReactClass({
	_needsTooltip(field, secondField){
		let { intl, _alum } = this.props,
			value = _alum[field] || 'N/A',
			secondVal = _alum[secondField] || '',
			tooltip = field === 'alumni_name' ? stylesForName.tooltip : styles.tooltip,
			tip = styles.tip;

		if( field === 'dep_id' ) value = this._extractDeptName( _.find(intl.all_depts, {id: +value}) );

		if( secondField && value !== 'N/A' ){
			value = value + ' ' + secondVal;
		}

		if( value.length > MAX_CHAR_LEN ){

			// after value length check, if linkedin was added, turn name into link to linkedin profile
			value = field === 'alumni_name' && _alum.linkedin ? <a href={_alum.linkedin}>{value}</a> : value;

			return <Tooltip customText={value} toolTipStyling={tooltip} tipStyling={tip}>
						{ value }
					</Tooltip> ;
		}

		value = field === 'alumni_name' && _alum.linkedin ? <a href={_alum.linkedin}>{value}</a> : value;

		return value;
	},

	_extractDeptName(dept){
		return dept ? dept.name : 'N/A';
	},

	_getBg(){
		let { _alum } = this.props;

		// if not set, just return empty string
		if( !selectn('photo_url', _alum) ) return '';

		// photo can only be a string (aws url) or object (file user just uploaded)
		return _.isString(_alum.photo_url) ? _alum.photo_url : URL.createObjectURL(_alum.photo_url);
	},

	_editAlumni(action){
		let { dispatch, _alum } = this.props;

		_alum.alum_action = action;
		dispatch( editAlum(_alum) );
	},

	_removeAlumni(){
		let { dispatch, _alum } = this.props;

		_alum.alum_action = 'remove';
		dispatch( removeItem(_alum, REMOVE_ALUMNI_ROUTE, 'EDIT_ALUM') );
	},

	render(){
		let { editMode, _alum } = this.props,
			name = this._needsTooltip('alumni_name'),
			edu = this._needsTooltip('dep_id', 'grad_year'),
			location = this._needsTooltip('location'),
			bg = { backgroundImage: 'url('+this._getBg()+')' };

		return (
			<div className="alumni-card">
				<div className="pic" style={bg}></div>
				<div className="name">{ name }</div>
				<div className="edu">{ edu }</div>
				<div className="location">{ location }</div>
				<div className={"linkedin "+(!_alum.linkedin ? 'invisi' : '')}><a href="">{'in'}</a></div>
				{ editMode && <div className="edit-mode">
								<div onClick={ () => this._editAlumni('edit') }>Edit</div>
								<div>{'|'}</div>
								<div onClick={ this._removeAlumni }>Remove</div>
							</div> }
			</div>
		);
	}
});

const styles = {
	tooltip: {
		fontSize: '12px',
		color: '#797979',
		border: 'none',
		borderRadius: 0,
		margin: '0 5px 0 0',
		width: '100%',
		height: 'initial',
		verticalAlign: 'initial',
		textOverflow: 'ellipsis',
	    whiteSpace: 'nowrap',
    	overflow: 'hidden',
    	display: 'block',
	},
	tip: {
		padding: '5px',
		fontSize: '12px',
	}
}

const stylesForName = {
	tooltip: {
		...styles.tooltip,
		fontSize: '16px',
		margin: '5px 0',
		color: '#24b26b',
	},
}
