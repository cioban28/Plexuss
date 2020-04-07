// /College_Application/RadioForm.js

import React, { Component } from 'react'
import { browserHistory } from 'react-router'

import UploadDocModal from './UploadDocModal'

import { UPLOAD_Q } from './constants'
import { updateProfile } from './../../actions/Profile'

export default class UploadForm extends Component{
	constructor(props){
		super(props);

		this.state = {
			resume_uploaded: null,
			resume_msg: 'Since you have indicated that you are seeking a graduate degree, you must upload your resume.',
			open: false,
			upload_types: [],
		};
	}

	componentWillMount(){
		let { field } = this.props,
			types = [];

		_.forIn(field.doc_type, d => { types.push(d) });

		types = types.map(name => _.find(UPLOAD_Q.slice(), {name}));

		this.state.upload_types = types;
	}

	componentDidUpdate(prevProps, prevState){
		let { dispatch, _profile } = this.props;

		if ( _profile.transcripts && prevProps._profile.transcripts && prevProps._profile.transcripts != _profile.transcripts ) { 
			dispatch( updateProfile() );
		}
	}

	render(){
		let { _profile, field } = this.props,
			{ resume_msg, resume_uploaded, open, upload_types } = this.state,
			invalid = !_profile[field.name+'_valid'] && _.isBoolean(_profile[field.name+'_valid']);

		return (
			<label id={field.name}>

				{field.label}

				<div className="redirect-to" onClick={ e => {e.preventDefault(); this.setState({open: true});} }>
					Click here to upload documents

					{ (field.name === 'addtl__post_secondary_resume' && _.isBoolean(resume_uploaded) && !resume_uploaded) && 
						<div className="addtl-err">{resume_msg}</div> }

					{ invalid && <div className={"field-err "+(field.field_type || 'text')}>{ field.err }</div> }
				</div>

				{ open && <UploadDocModal 
							docs={ upload_types || [] }
							close={ e => this.setState({open: false}) }
							{...this.props} /> }

			</label>
		);
	}
}