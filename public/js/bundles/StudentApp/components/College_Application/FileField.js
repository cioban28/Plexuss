// /College_Application/FileField.js

import React from 'react'
import selectn from 'selectn'

import { updateProfile } from './../../actions/Profile'

export default class extends React.Component{
	constructor(props) {
		super(props)
		this._update = this._update.bind(this)
	}
	_update(e){
		let { dispatch, field } = this.props;
		dispatch( updateProfile({[field.name]: e.target.value}) );
	}

	render(){
		let { _profile, field } = this.props;

		return (
			<label htmlFor={ field.name }>
				{ field.label }

				<input
					id={ field.name }
					type="file"
					className=""
					name={ field.name || '' }
					placeholder={ field.placeholder || '' }
					value={ selectn(field.name, _profile) || '' }
					onChange={ this._update } />
			</label>
		);
	}
}