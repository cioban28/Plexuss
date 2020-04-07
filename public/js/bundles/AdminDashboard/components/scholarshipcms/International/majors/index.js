// index.js

import React from 'react'
import selectn from 'selectn'
import { connect } from 'react-redux'
import ReactSpinner from 'react-spinjs-fix'
import { toastr } from 'react-redux-toastr'
import DocumentTitle from 'react-document-title'
import createReactClass from 'create-react-class'

import PickDepartment from './components/pickDepartment'
import SimpleModal from './../../../../../utilities/simpleModal'
import CustomModal from './../../../../../utilities/customModal'
import DeptDegreeSelection from './components/deptDegreeSelection'

import { spinjs_config } from './../../../common/spinJsConfig'
import { DEGREE_TYPES, RESET_INTL_MAJORS } from './../constants'
import { getPortalsThatHaveMajors } from './../../../../actions/managePortalsActions'
import { saveIntlData, editHeaderInfo, removeItem, importMajors } from './../../../../actions/internationalActions'

const IntlMajors = createReactClass({
	getInitialState(){
		return {
			modalOpen: false,
			warningModalOpen: false,
			portal_options: [],
			importFrom_id: '',
		};
	},

	componentWillMount(){
		let { dispatch, intl } = this.props;
		if( !intl.portal_w_majors_init_done ) dispatch( getPortalsThatHaveMajors() );
	},

	componentWillReceiveProps(np){
		let { intl } = this.props;

		// if next state is different from this state AND next state saved is true, trigger toastr
		if( intl.portal_w_majors_init_done !== np.intl.portal_w_majors_init_done && np.intl.portal_w_majors_init_done ){
			let list = np.intl.portals_that_have_majors || [];
			this._buildPortalOptions(list);
		}
	},

	_buildPortalOptions(portals){
		if( portals && portals.length > 0 ){
			let opts = [<option key={'disabled'} disabled="disabled" value=''>Select a portal to import from</option>];

			_.each(portals, (port) => opts = [...opts, <option key={port.crf_id+'_'+port.id} value={port.crf_id}>{port.name}</option>] );

			this.setState({portal_options: opts});
		}
	},

	_formValid(){
		let { intl } = this.props,
			valid = false,
			active_program_dept_opt = 'department_option',
			active_program_depts = 'departments';

		if( intl[active_program_dept_opt] === 'all' ){
			// if dept option is all, then allow to save that
			valid = true;
		}else if( selectn(active_program_depts, intl) && intl[active_program_depts].length > 0 ){
			// else if departments list for current program is set and has at least one dept, then valid
			valid = true;
		}

		return valid;
	},

	_saveMajors(e){
		e.preventDefault();

		let { dispatch } = this.props,
			form = this._buildFormData();

		form.tab = this.refs.hidden.value;

		dispatch( saveIntlData(form) );
	},

	_buildFormData(){
		let { intl } = this.props,
			data = {departments: []};

		// if dept option is all, just send all
		if( intl['department_option'] === 'all' ){
			data.departments = 'all';
		}else{
			// 1. loop through each selected department
			var dept_list = intl['departments'],
				option_prop = 'option_for_dept_majors_',
				selected_majors_prop = 'selected_majors_for_dept_',
				dept_option_prop = '_department_option',
				major_data = {};

			if( dept_list && dept_list.length > 0 ){
				_.each(dept_list, (dept) => { //loops through each dept
					let this_majors_option = intl[option_prop + dept.id],
						dept_option = intl[intl.activeProgram + dept_option_prop];

					let obj = {department_id: '', major_id: null, in_or_all: ''}; //each loop, init new obj

					obj.in_or_all = dept_option, //init object with the selected include/exclude value
					obj.department_id = dept.id; //init obj with this department id

					if( this_majors_option === 'all' ){
						let obj_with_degs = this._determineDegree(dept, obj);
						data.departments = [...data.departments, ...obj_with_degs];
					}else{
						let majors_list = intl[selected_majors_prop + dept.id];

						// if majors list is set, loop through each
						// else, basically do the same operation as if option = all
						if( majors_list && majors_list.length > 0 ){
							_.each(majors_list, (major) => {
								obj.major_id = major.id;

								// loop through each degree type to see if major has this degree/school type
								let maj_with_degs = this._determineDegree(major, obj);
								data.departments = [...data.departments, ...maj_with_degs];
							});
						}else{
							// here if option for this dept = include, but user never actually added any majors
							let obj_with_degs = this._determineDegree(dept, obj);
							data.departments = [...data.departments, ...obj_with_degs];
						}
					}
				});
			}
		}

		return data;
	},

	_determineDegree(deptOrMajor, obj){
		let all = [];

		_.each(DEGREE_TYPES, (deg) => {
			let objWithDegrees = {...obj};

			if( deptOrMajor[deg.name] ){
				let prop_name = deg.name === 'online' || deg.name === 'campus' ? 'school_type_id' : 'degree_id';

				objWithDegrees[prop_name] = deg.value;
				all.push(objWithDegrees);
			}
		});

		return all;
	},

	_resetMajors(){
		let form = {departments: 'all', tab: this.refs.hidden.value};
		this.setState({modalOpen: false});
		this.props.dispatch( saveIntlData(form, RESET_INTL_MAJORS) );
	},

	_importMajors(e){
		e.preventDefault();

		let { dispatch } = this.props,
			{ importFrom_id } = this.state;

		this.setState({warningModalOpen: false});
		if( importFrom_id ) dispatch( importMajors(importFrom_id) );
	},

	render(){
		let { dispatch, intl } = this.props,
			{ modalOpen, warningModalOpen, portal_options, importFrom_id } = this.state,
			formValid = this._formValid(),
			active_program_dept = 'departments',
			dept_option = intl['department_option'];

		return (
			<DocumentTitle title="Admin Tools | International Students | Majors & Degrees">
				<div className="row i-container intl-majors-container">
					<div className="column small-12">

						<form onSubmit={ this._saveMajors }>

							<input type="hidden" name="tab" value="majors" ref="hidden" />

							<div className="inner-form-wrapper">

								<div className="row collapse">
									<div className="column small-12 medium-6">
										<PickDepartment
											customFieldName={'department_option'}
											{...this.props} />
									</div>
									<div className="column small-12 medium-6">
										{'Please select which majors and degrees you offer.'}
									</div>
								</div>

								<div className={ dept_option === 'all' ? 'hide' : '' }>
									<div>{'Filter is including students interested in:'}</div>

									<div>
										{ intl[active_program_dept] && intl[active_program_dept].map((dept) => <DeptDegreeSelection
																													key={dept.id+'_'+dept.name}
																													dept={dept}
																													{...this.props} />) }
									</div>
								</div>

							</div>

							<div className="intl-major-actions">
								<div>
									<div
										onClick={ () => this.setState({warningModalOpen: true}) }
										className="import">
											{ intl.import_pending ? <ReactSpinner config={spinjs_config} /> : 'Import Included Majors from Targeting' }
									</div>
								</div>
								<div className="text-right">
									<div
										onClick={ () => this.setState({modalOpen: true}) }
										className="cancel">
											{'Reset'}
									</div>
									<button
										disabled={ !formValid || intl.pending }
										className="button save">
											{ intl.pending ? <ReactSpinner config={spinjs_config} /> : 'Save' }
									</button>
								</div>
							</div>

							{ modalOpen && <SimpleModal
												yes={ this._resetMajors }
												closeMe={ () => this.setState({modalOpen: false}) }
												question={ <ResetQuestion /> } /> }

							{ warningModalOpen && <CustomModal
													backgroundClose={ () => this.setState({warningModalOpen: false}) }>
														<div className="import-warning-modal">
															<div className="text-right">
																<div className="close" onClick={ () => this.setState({warningModalOpen: false}) }>&#10006;</div>
															</div>

															<h4>Choose which Portal you would like to import majors from</h4>

															<select
																value={ importFrom_id || '' }
																onChange={ (e) => this.setState({importFrom_id: e.target.value}) }>
																{ portal_options }
															</select>

															<div>Note: You can only import "Included" majors; "Excluded" majors will be ignored.</div>
															<br />
															<div className="text-center">
																<button
																	disabled={ !importFrom_id }
																	className="button save"
																	onClick={ this._importMajors }>Continue</button>
															</div>
														</div>
												  </CustomModal> }

						</form>

					</div>
				</div>
			</DocumentTitle>
		);
	}
});

const ResetQuestion = () => {
	return (
		<div className="text-center reset-modal">
			<div className="reset-question">Are you sure you want to reset your Majors & Degrees?</div>
			<div><small>(This cannot be undone)</small></div>
		</div>
	);
}

const mapStateToProps = (state, props) => {
	return {
		intl: state.intl,
	};
};

export default connect(mapStateToProps)(IntlMajors);
