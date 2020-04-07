// /Cost/Tuition.js

import React from 'react'
import { connect } from 'react-redux'
import ReactSpinner from 'react-spinjs-fix'
import createReactClass from 'create-react-class'

import CustomModal from './../../../../utilities/customModal'
import CostField from './../International/header/components/costField'
import ProgramHeader from './../International/components/programHeader'

import { TUITION_COST_FIELDS } from './constants'
import { spinjs_config } from './../../common/spinJsConfig'
import { getTuitionCostData, setCostProgram, setCostValues, saveTuitionCostWithFiles } from './../../../actions/costActions'

const Tuition = createReactClass({
	getInitialState(){
		return {
			exampleOpen: false,
			previewOpen: false,
		}
	},

	_getEstimates(){
		let { _cost, intl } = this.props;

		return {
			cost: this._format(_cost[intl.activeProgram+'_est_cost']),
			assist: this._format(_cost[intl.activeProgram+'_est_assist']),
			annual: this._format(_cost[intl.activeProgram+'_min_annual_cost']),
		};
	},

	_format(val){
		if( val || _.isFinite(+val) ){
			//if value is 4 chars or more, add comma after 3rd index starting from last index
			if( (''+val).length > 3 ){
				var tmp = (''+val).split('');
				tmp.splice(-3, 0, ',');
				return '$'+tmp.join('');
			}

			return '$'+val;
		}

		return '$0';
	},

	_saveCost(e){
		e.preventDefault();

		let { dispatch, _cost } = this.props,
			formData = new FormData();

		_.each(_cost, (v, k) => formData.append(k, v) );

		dispatch( saveTuitionCostWithFiles(formData) );
	},

	_uploadCompanyLogo(e){
		let { dispatch, _profile } = this.props,
			file = e.target.files[0],
			name = e.target.id;

		file.upload_time = moment().format('MM/D/YYYY hh:mma');
		dispatch( setCostValues({company_logo_file: file}) );
	},

	_getLogo(){
		let { _cost } = this.props;

		if( _cost.company_logo_file ) return window.URL.createObjectURL(_cost.company_logo_file);
		return _cost.company_logo || '';
	},

	render(){
		let { dispatch, children, route, _cost, intl, user } = this.props,
			{ exampleOpen, previewOpen } = this.state,
			estimates = this._getEstimates(),
			logo = this._getLogo();

		return (
			<div className="row">

				<div className="column small-12">
					<ProgramHeader />
				</div>

				<div className="column small-12 medium-5">
					<form>

						{ TUITION_COST_FIELDS.map((co) => <CostField
															key={ co.name }
															cost={ co }
															rootObj={ _cost }
															customDispatch={ setCostValues }
															customProgram={ intl.activeProgram }
															useProgram={ true }
															{...this.props} />) }

						<div className="disclaimer">
							NOTE: Students can go to your college page to see exact costs. Please use average amounts instead of ranges for when inputting values.
						</div>

						{
							_.get(user, 'aor_id', 0) > 0 &&
							<div className="quicktip-container">
								<h5>Quick Tip</h5>
								<div>The quick tip is what is displayed on the international pages breakdown.</div>
								<div><u onClick={ e => this.setState({exampleOpen: true}) }>Example Quicktip</u></div>

								<br />

								<textarea
									value={ _cost.quick_tip || '' }
									placeholder="Enter company description for Quick Tip"
									maxLength={75}
									onChange={ e => dispatch( setCostValues({quick_tip: e.target.value}) ) } />

								<div className="company">
									<div>
										 <input
										 	id="company_logo"
										 	type="file"
										 	name="company_logo"
										 	onChange={ this._uploadCompanyLogo } />
										 <div htmlFor="company_logo">Upload your company logo</div>
									</div>
									<div>
										<u onClick={ e => this.setState({previewOpen: true}) }>Preview</u>
									</div>
								</div>

								{ exampleOpen && <CustomModal backgroundClose={ e => this.setState({exampleOpen: false}) }>
													<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/quick_tip_example.png" alt="Quick tip example" />
												</CustomModal> }

								{ previewOpen && <CustomModal backgroundClose={ e => this.setState({previewOpen: false}) }>
													<div className="preview-tip">
														<div><u>English Program</u></div>
														<img src={ logo } alt="Quick tip preview" />
														<div className="qtip">{ _cost.quick_tip }</div>
														<span className="arrow" />
													</div>
												</CustomModal> }
							</div>
						}

						<div className="actions">
							<button
								disabled={ _cost.pending }
								className="button save"
								onClick={ this._saveCost }>
									{ _cost.pending ? <ReactSpinner config={spinjs_config} /> : 'Save' }
							</button>
						</div>

					</form>

				</div>

				<div className="column small-12 medium-7">
					<div className="est-preview">
						<div>Estimated Cost <div>{ estimates.cost }</div></div>
						<div>Estimated Assistance <div>{ estimates.assist }</div></div>
						<div>Minimum Annual Cost <div>{ estimates.annual }</div></div>
					</div>

					<div className="exp-breakdown-link"><a href="/international-students">See International Student College Expense Breakdown entry</a></div>
				</div>

			</div>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		intl: state.intl,
		user: state.user,
		_cost: state.cost,
	};
};

export default connect(mapStateToProps)(Tuition);
