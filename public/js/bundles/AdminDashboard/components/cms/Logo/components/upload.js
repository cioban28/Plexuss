// upload.js

import React from 'react'
import selectn from 'selectn'
import { connect } from 'react-redux'
import createReactClass from 'create-react-class'

import { saveLogo, uploadLogo } from './../../../../actions/cmsLogoActions'

const UploadLogo = createReactClass({
	_update(e){
		this.props.dispatch( uploadLogo(e.target.files[0]) );
	},

	_saveLogo(e){
		e.preventDefault();

		var form = new FormData(e.target);
		this.props.dispatch( saveLogo(form) );
	},

	render(){
		let { dispatch, logo, college } = this.props, img = '';

		if( logo && logo.fileURL ) img = logo.fileURL;
		else if( college && college.logo_url ) img = college.logo_url;

		return (
			<div className="logo-options-container left">

				<form onSubmit={ this._saveLogo }>
					<div className="upload-container">
						<h4>Update Logo</h4>
						<label htmlFor="uploadLogoInput" className="update logo-btn text-center">Upload Logo</label>
						<input
							name="logo"
							type="file"
							id="uploadLogoInput"
							accept=".png,.jpg,.gif,.bmp"
							onChange={ this._update } />
						<div className="dir"><small>Only .jpg, .png, .gif, .bmp allowed</small></div>
					</div>

					<div className="save-container">
						<h4>Current Logo</h4>
						<img src={ img } alt="Current College Logo" />
						<button className="save logo-btn text-center">Save</button>
					</div>
				</form>

			</div>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		logo: state.logo,
		college: state.college,
	};
};

export default connect(mapStateToProps)(UploadLogo);
