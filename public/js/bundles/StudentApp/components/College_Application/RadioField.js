// /College_Application/RadioField

import React from 'react'
import selectn from 'selectn'

import { updateProfile, changedFields } from './../../actions/Profile'

class RadioField extends React.Component {
	constructor(props) {
		super(props)
		this._update = this._update.bind(this)
	}

	_update(e){
		let { dispatch, field, _profile } = this.props,
			name = field.name,
			val = e.target.value;

		dispatch( updateProfile({[field.name]: val}) );

		if(_profile[name] != val && typeof _profile[name] != 'undefined' && typeof field.name != 'Object')
			dispatch(changedFields(field.name));

	}

	componentWillMount(){
		let { dispatch, _profile, field } = this.props;
		if( field.is_default && !_profile[field.name] ) dispatch( updateProfile({[field.name]: field.id}) );
	}

	componentWillReceiveProps(np){
		let { dispatch, _profile, field } = this.props;

		/* once _profile init is done, where prev state of the fields value is diff from next states value,
		   and next state's val is null, and this field should be selected by default, then dispatch update to
		   make this field checked */
		if( (_profile.init_done !== np._profile.init_done && np._profile.init_done) && 
			(_profile[field.name] !== np._profile[field.name]) && 
			_.isNull(np._profile[field.name]) && field.is_default ){
				dispatch( updateProfile({[field.name]: field.id}) );
		}
	}

	render(){
		let { _profile, field } = this.props;

		return (
			<label htmlFor={ field.name+field.id } className="radio">
				<input
					id={ field.name+field.id }
					type="radio"
					className=""
					name={ field.name }
					checked={ selectn(field.name, _profile) === field.id }
					value={ field.id || '' }
					onChange={ this._update } />

				{ field.label || field.id }

			</label>
		);
	}
}

export default RadioField;