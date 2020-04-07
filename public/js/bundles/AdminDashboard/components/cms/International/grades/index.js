// index.js

import React from 'react'
import createReactClass from 'create-react-class'
import { connect } from 'react-redux'
import ReactSpinner from 'react-spinjs-fix'
import DocumentTitle from 'react-document-title'

import ExamButton from './components/examButton'
import CostField from './../header/components/costField'
import ProgramHeader from './../components/programHeader'

import { spinjs_config } from './../../../common/spinJsConfig'
import { saveIntlData, editHeaderInfo, resetSaved } from './../../../../actions/internationalActions'
import { GPA, TOEFL, IELTS, SAT, PSAT, GMAT, ACT, GRE, TOASTR_OPTIONS, PROGRAMS } from './../constants'

const EXAMS = [
	...GPA,
	...TOEFL,
	...IELTS,
	...SAT,
	...PSAT,
	...GMAT,
	...ACT,
	...GRE
];

const BUTTONS = _.filter(EXAMS, 'title');

const IntlGrades = createReactClass({
	_formValid(){
		let { intl } = this.props,
			valid = false,
			program = _.find(PROGRAMS, {id: intl.activeProgram});

		if( program ){
			_.each(EXAMS, (exam) => {
				// only check the obj that have exam.name and if intl obj has a value w/ that prop name
				if( exam.name ){
					let exam_name = program.id+'_'+exam.name;

					valid = !!intl[exam_name];
					if( !valid ) return false;
				}

			});
		}

		return valid;
	},

	_saveExams(e){
		e.preventDefault();

		let { dispatch, intl } = this.props,
			form = {...intl};

		form.tab = this.refs.hidden.value;

		dispatch( saveIntlData(form) );
	},

	render(){
		let { intl } = this.props,
			formValid = true; //this._formValid();

		return (
			<DocumentTitle title="Admin Tools | International Students | Grades & Exams">
				<div className="row i-container">
					<div className="column small-12">

						<ProgramHeader />

						<div className="grade-section-name">College Entrance Exams</div>

						<form onSubmit={ this._saveExams }>

							<input type="hidden" name="tab" value="grades" ref="hidden" />

							<div className="exam-btn-container">
								{ BUTTONS.map((exam) => <ExamButton
															key={ exam.title }
															exam={ exam }
															{...this.props} />) }
							</div>

							<div className="grade-field-container">
								{
									BUTTONS.map( btn => {
										return (
											<div key={btn.title+'_main'} className={"exam-section "+(intl[btn.title+'_btn'] || '')}>

												<div className="grade-title" key={btn.title}>{btn.title}</div>

												{
													EXAMS.filter((ex) => {
														return ex.exam === btn.title;
													}).map((exam) => {
														return (exam.exam === btn.title) &&
																<CostField
																	key={ exam.name }
																	cost={ exam }
																	custom_class={'grades'}
																	{...this.props} />
													})
												}
											</div>
										);
									})
								}
							</div>

							<button
								disabled={ !formValid || intl.pending }
								className="button save">
									{ intl.pending ? <ReactSpinner config={spinjs_config} /> : 'Save' }
							</button>

						</form>

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

export default connect(mapStateToProps)(IntlGrades);
