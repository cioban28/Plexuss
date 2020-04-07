// addAlumniForm.js

import React from 'react'
import selectn from 'selectn'
import ReactSpinner from 'react-spinjs-fix'
import createReactClass from 'create-react-class'

import CostField from './../../header/components/costField'

import { spinjs_config } from './../../../../common/spinJsConfig'
import { ALUMNI_NAME_TEXT_FIELD, ALUMNI_TEXT_FIELDS, ALUMNI_SELECT_FIELDS } from './../../constants'
import { getAllDepts, editAlum, saveIntlDataWithFile, resetSaved } from './../../../../../actions/internationalActions'

const MAX_YRS = 100;
const ACTION_NAME = 'EDIT_ALUM';

export default createReactClass({
	getInitialState(){
		return {
			grad_year_ui: null,
			dep_id_ui: null,
		};
	},

	componentWillMount(){
		let { dispatch, intl } = this.props;

		this._buildGradYrs();

 		if( intl.all_depts ) this._buildDepts(intl.all_depts);
 		else dispatch( getAllDepts() );
 	},

 	componentWillReceiveProps(np){
 		let { intl } = this.props;

 		if( np.intl.all_depts !== intl.all_depts ) this._buildDepts(np.intl.all_depts);
 	},

	_buildGradYrs(){
		var this_yr = new Date().getFullYear(),
			start_yr = this_yr - MAX_YRS,
			ui = [<option key={'disabled'} value="" disabled="disabled">{'Select grad year...'}</option>];

		// loop and build MAX_YRS number of options
		for (var i = this_yr; i >= start_yr; i--) {
			ui = [...ui, <option key={'year_'+i} value={i}>{i}</option>];
		}

		this.state.grad_year_ui = ui;
	},

	_buildDepts(depts){
		var ui = [<option key={'disabled'} value="" disabled="disabled">{'Select department...'}</option>];

		_.each(depts, (dept) => ui = [...ui, <option key={dept.name} value={dept.id}>{dept.name}</option>] );

		this.state.dep_id_ui = ui;
	},

	_formValid(){
		let { intl } = this.props,
			valid = false;

		if( !selectn('new_alumni', intl) ) return false;

		let a = intl.new_alumni;

		return a.alumni_name && a.location && a.photo_url && a.grad_year && a.dep_id;
	},

	_addAlumni(e){
		e.preventDefault();

		let { dispatch } = this.props,
			form = new FormData(e.target);

		if( this._formValid() ){
			dispatch( saveIntlDataWithFile(form, 'EDIT_ALUM', {alum_action: 'add'}) );
		}
	},

	_editAlum(e){
		let { dispatch } = this.props,
			name = e.target.getAttribute('name'),
			val = e.target.value;

		dispatch( editAlum(this._buildAlum(name, val)) );
	},

	_editAlumPic(e){
		let { dispatch } = this.props,
			name = e.target.getAttribute('name'),
			val = e.target.files[0];

		dispatch( editAlum(this._buildAlum(name, val)) );
	},

	_buildAlum(name, val){
		let alum = {};

		alum[name] = val;
		alum.alum_action = 'edit';

		return alum;
	},

	_disableAlum(){
		return {alum_action: 'close'};
	},

	_getPhoto(){
		let { intl } = this.props,
			alum = intl.new_alumni || {};

		if( selectn('photo_url', alum) )
			return _.isString(alum.photo_url) ? alum.photo_url : alum.photo_url.name;

		return '';
	},

	render(){
		let { dispatch, intl } = this.props,
			alum = intl.new_alumni || {},
			formValid = this._formValid(),
			_photo = this._getPhoto();

		return (
			<form onSubmit={ this._addAlumni } className="alum_form">

				<input type="hidden" name="tab" value="alumni" />
				{ selectn('id', alum) && <input type="hidden" name="id" value={ alum.id } /> }

				<CostField
					cost={ ALUMNI_NAME_TEXT_FIELD }
					customDispatch={ editAlum }
					rootObj={ alum }
					{...this.props} />

				<div className="grad_details">
					<label>Department and Graduation Year</label>

					{ ALUMNI_SELECT_FIELDS.map((sel) => <select
															key={ sel.name }
															className={ sel.name }
															value={ alum[sel.name] || '' }
															onChange={ this._editAlum }
															name={ sel.name }>
																{ this.state[sel.name+'_ui'] }
														</select>) }

				</div>

				{ ALUMNI_TEXT_FIELDS.map((txt) => <CostField
													key={ txt.name }
													cost={ txt }
													customDispatch={ editAlum }
													rootObj={ alum }
													{...this.props} />) }

				<div className="upload-pic-container">
					<div className="upload-btn">
						<label htmlFor="_alum_pic" className="alum-btn">Upload</label>
						<input
							id="_alum_pic"
							onChange={ this._editAlumPic }
							name="photo_url"
							style={{display: 'none'}}
							type="file" />
					</div>
					<div className="upload-name">{ _photo }</div>
				</div>

				<div className="text-right">
					<div
						onClick={ () => dispatch( editAlum(this._disableAlum()) ) }
						className="cancel">
							Cancel
					</div>

					<button
						disabled={ !formValid || intl.pending }
						className="button save">
							{ intl.pending ? <ReactSpinner config={spinjs_config} /> : 'Save' }
					</button>
				</div>

			</form>
		);
	}
});
