// /College_Application/CheckboxField

import React from 'react'
import selectn from 'selectn'

import DatePickr from './../common/DatePickr'

import { updateProfile, changedFields } from './../../actions/Profile'

class CheckboxField extends React.Component {
	constructor(props) {
		super(props)
		this._update = this._update.bind(this)
	}

	_update(e){
		let { dispatch, _profile, field } = this.props,
			name = field.name,
			val;

		// txt_opt_in is a special case in that 0 will also make checkbox checked
		if( field.name === 'txt_opt_in' ){
			if( _profile[field.name] ) val = _profile[field.name] === 1 ? -1 : 1;
			else val = -1;

		}else val = +(!+e.target.value); // +(!+e.target.value) - a succinct way of toggling between 0 and 1
		
		dispatch( updateProfile({[field.name]: val}) );

		if(_profile[name] != val && typeof _profile[name] != 'undefined' && typeof field.name != 'Object')
			dispatch(changedFields(field.name));

	}

	componentWillMount(){
		let { dispatch, _profile, field } = this.props;

		// make field checked by default if the store prop is not already a number
		if( field.is_default && !_.isFinite(_profile[field.name]) ) dispatch( updateProfile({[field.name]: 1}) );
	}

	componentWillReceiveProps(np){
		let { dispatch, _profile, field } = this.props;

		// when profile init returns, and the field name is 0 (has never seen this question), and default is true, force check the box
		if( (!_profile.init_done && np._profile.init_done) && 
			np._profile[field.name] === 0 && field.is_default ) dispatch( updateProfile({[field.name]: 1}) );
	}

	render(){
		let { _profile, field, injectHTML } = this.props,
			val = +selectn(field.name, _profile);

		if( field.name === 'txt_opt_in' && val === 0 ) val = 1; 

		return (
			<div className="checkbox">

				<input
					id={ field.name+field.id }
					type="checkbox"
					className=""
					name={ field.name }
					checked={ ''+val === '1' }
					value={ val }
					onChange={ this._update } />

				{ injectHTML ? 
					<label 
						htmlFor={ field.name+field.id }
						dangerouslySetInnerHTML={{__html: injectHTML}} />
					:
					<label htmlFor={ field.name+field.id }>
						{ field.label || field.id }
						{ field.link && <a href={field.link} target='_blank'>{field.linkName}</a> }
						{ (field.field && field.field_type === 'date') && 
							<DatePickr
								key={field.field.name}
								_label={field.field.label}
								_side="_left"
								_action={ updateProfile }
								_state={ _profile }
								_format={'YYYY-MM-DD'}
								_name={field.field.name} /> }
					</label> 
				}
				
			</div>
		);
	}
}
export default CheckboxField;