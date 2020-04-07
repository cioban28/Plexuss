// /Application/RequireSection.js

import React from 'react'
import ReactSpinner from 'react-spinjs-fix'
import { toastr } from 'react-redux-toastr'
import { spinjs_config } from './../../common/spinJsConfig'
import createReactClass from 'create-react-class'

import { updateSimpleProp, saveAppRequirements, resetSaved } from './../../../actions/overviewActions'

export default createReactClass({
	_saveRequirements(e){
		e.preventDefault();
		let { dispatch, overview, route } = this.props;

		if( route.atypical ){
			let questions_prop = route.id+'_fields',
				form = _.pick(overview, ['page', questions_prop]);

			form[questions_prop] = _.pickBy(form[questions_prop], v => !!v); // pick fields that are true

			dispatch( saveAppRequirements(form) );

		}else dispatch( saveAppRequirements(overview) );
	},

	componentWillReceiveProps(np){
		let { dispatch, overview } = this.props;

		// trigger toastr after save then reset saved
		if(overview.app_requirements_saved !== np.overview.app_requirements_saved && np.overview.app_requirements_saved ){
			var OPTIONS = {
				timeOut: 7000, // by setting to 0 it will prevent the auto close
				component: (
					<div className="toastr-component">
						<div className="main">Your changes have been saved!</div>
					</div>
				),
			};

			toastr.success('', OPTIONS);
			dispatch( resetSaved() );
		}
	},

	render(){
		let { dispatch, overview, intl, route, noRequire } = this.props,
			name = intl.activeProgram+'_require_'+route.id;

		return (
			<div className="require-section">
				{ !noRequire &&
					<label>
						<input
							id={name}
							type="checkbox"
							name={ name || '' }
							value={ name || '' }
							checked={ overview[name] || false }
							onChange={ e => dispatch( updateSimpleProp({[name]: e.target.checked}) ) } />

						Require this section on application
					</label> }

				<div className={noRequire ? 'full' : ''}>
					<button
						disabled={ overview.save_app_requirements_pending }
						onClick={ this._saveRequirements }
						className="save-requirement">
							{ overview.save_app_requirements_pending ? <div className="spin-wrapper"><ReactSpinner config={spinjs_config} /></div> : 'Save' }
					</button>
				</div>
			</div>
		);
	}
});
