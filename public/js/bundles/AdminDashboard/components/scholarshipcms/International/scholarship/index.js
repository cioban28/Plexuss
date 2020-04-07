// index.js

import React from 'react'
import { connect } from 'react-redux'
import ReactSpinner from 'react-spinjs-fix'
import DocumentTitle from 'react-document-title'
import createReactClass from 'create-react-class'

import CostField from './../header/components/costField'
import ProgramHeader from './../components/programHeader'
import PreviewGenerator from './../components/previewGenerator'
import ConditionalRadioFields from './../admission/components/conditionalRadioFields'

import { spinjs_config } from './../../../common/spinJsConfig'
import { saveIntlData } from './../../../../actions/internationalActions'
import { SCHOLARSHIP_TEXT_FIELDS, SCHOLARSHIP_RADIO_FIELDS, PROGRAMS } from './../constants'

const IntlScholarship = createReactClass({
	_saveScholarship(e){
		e.preventDefault();

		let { dispatch, intl } = this.props,
			form = {...intl};

		form.tab = this.refs.hidden.value;

		dispatch( saveIntlData(form) );
	},

	_formValid(){
		let { intl } = this.props,
			valid = false,
			all_fields = [...SCHOLARSHIP_RADIO_FIELDS, ...SCHOLARSHIP_TEXT_FIELDS],
			program = _.find(PROGRAMS, {id: intl.activeProgram});

		if( program ){
			_.each(all_fields, obj => {
				valid = !!intl[program.id+'_'+obj.name];
				console.log('valid: ', valid);
				if( !valid ) return false;
			});
		}

		return valid;
	},

	render(){
		let { intl } = this.props,
			formValid = this._formValid();

		return (
			<DocumentTitle title="Admin Tools | International Students | Scholarship">
				<div className="row i-container">
					<div className="column small-12 medium-7">

						<form onSubmit={ this._saveScholarship }>

							<input type="hidden" name="tab" value="scholarship" ref="hidden" />

							<ProgramHeader />

							<ConditionalRadioFields
								field={ SCHOLARSHIP_RADIO_FIELDS[0] }
								{...this.props} />

							{ SCHOLARSHIP_TEXT_FIELDS.map( (field) => <CostField
														key={ field.name }
														cost={ field }
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
							fields={[...SCHOLARSHIP_RADIO_FIELDS, ...SCHOLARSHIP_TEXT_FIELDS]}
							title={'scholarship info'}
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
	};
};

export default connect(mapStateToProps)(IntlScholarship);
