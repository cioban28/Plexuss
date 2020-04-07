// index.js

import React from 'react'
import { connect } from 'react-redux'
import ReactSpinner from 'react-spinjs-fix'
import DocumentTitle from 'react-document-title'
import createReactClass from 'create-react-class'

import AppDeadline from './components/appDeadline'
import CostField from './../header/components/costField'
import ProgramHeader from './../components/programHeader'
import PreviewGenerator from './../components/previewGenerator'
import ConditionalRadioFields from './components/conditionalRadioFields'

import { spinjs_config } from './../../../common/spinJsConfig'
import { saveIntlData } from './../../../../actions/internationalActions'
import { ADMISSION_TEXT_FIELDS, ADMISSION_RADIO_FIELDS, PROGRAMS } from './../constants'

const IntlAdmission = createReactClass({
	_saveAdmission(e){
		e.preventDefault();

		let { dispatch, intl } = this.props,
			form = {...intl};

		form.tab = this.refs.hidden.value;

		dispatch( saveIntlData(form) );
	},

	_formValid(){
		let { intl } = this.props,
			valid = false,
			all_fields = [...ADMISSION_RADIO_FIELDS, ...ADMISSION_TEXT_FIELDS],
			program = _.find(PROGRAMS, {id: intl.activeProgram});

		if( program ){
			_.each(all_fields, obj => {
				valid = !!intl[program.id+'_'+obj.name];
				if( !valid ) return false;
			});
		}

		return valid;
	},

	render(){
		let { intl } = this.props,
			formValid = this._formValid();

		return (
			<DocumentTitle title="Admin Tools | International Students | Admission">
				<div className="row i-container">
					<div className="column small-12 medium-7">

						<form onSubmit={ this._saveAdmission }>

							<input type="hidden" name="tab" value="admission" ref="hidden" />

							<ProgramHeader />

							<AppDeadline {...this.props} />

							<ConditionalRadioFields
								field={ADMISSION_RADIO_FIELDS[1]}
								{...this.props} />

							{ ADMISSION_TEXT_FIELDS.map( (ad) => <CostField
																	key={ ad.name }
																	cost={ ad }
																	custom_class={'admitted'}
																	{...this.props} /> ) }

							<button
								disabled={ !formValid || intl.pending }
								className="button save">
									{ intl.pending ? <ReactSpinner config={spinjs_config} /> : 'Save' }
							</button>

						</form>

					</div>
					<div className="column small-12 medium-5">
						<PreviewGenerator
							fields={[...ADMISSION_RADIO_FIELDS, ...ADMISSION_TEXT_FIELDS]}
							title={'admissions'}
							{...this.props} />
					</div>
				</div>
			</DocumentTitle>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		intl: state.intl,
		dates: state.dates,
	};
};

export default connect(mapStateToProps)(IntlAdmission);
