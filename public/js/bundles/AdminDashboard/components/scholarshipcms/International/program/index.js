// index.js

import React from 'react'
import { connect } from 'react-redux'
import ReactSpinner from 'react-spinjs-fix'
import DocumentTitle from 'react-document-title'
import createReactClass from 'create-react-class'

import { spinjs_config } from './../../../common/spinJsConfig'
import { editProgram, saveIntlData, resetSaved } from './../../../../actions/internationalActions'

import { PROGRAMS } from './../constants'

const IntlProgram = createReactClass({
	_saveProgram(e){
		e.preventDefault();

		let { dispatch, intl } = this.props,
			form = {...intl};

		form.tab = this.refs.hidden.value;

		dispatch( saveIntlData(form) );
	},

	_atLeastOneProgram(){
		let { intl } = this.props,
			valid = false;

		_.each(PROGRAMS, p => {
			if( intl.program[p.id] ){
				valid = true;
				return false;
			}
		});

		return valid;
	},

	render(){
		let { dispatch, intl } = this.props,
			program_valid = this._atLeastOneProgram();

		return (
			<DocumentTitle title="Admin Tools | International Students | Program">
				<div className="row i-container">
					<div className="column small-12 medium-6">
						<form onSubmit={ this._saveProgram }>

							<input type="hidden" name="tab" value="program" ref="hidden" />

							<h5><b>Define Program</b></h5>

							<div className="directions">If you have different requirements for your grad/undergrad programs, please indicate below.</div>

							<br />

							{ PROGRAMS.map((p) => <Program key={p.id} program={p} {...this.props} />) }

							<button
								disabled={ !program_valid || intl.pending }
								className="button radius save">
									{ intl.pending ? <ReactSpinner config={spinjs_config} /> : 'Save' }
							</button>

						</form>
					</div>

					<div className="column small-12 medium-6">
						<iframe
							width="100%"
							height="315"
							src="https://www.youtube.com/embed/cISL9w0c8fo"
							frameBorder="0"
							allowFullScreen>
						</iframe>
					</div>
				</div>
			</DocumentTitle>
		);
	}
});

const Program = createReactClass({
	render(){
		let { dispatch, intl, program } = this.props;

		return (
			<label htmlFor={ program.id+'_program' }>
				<input
					id={ program.id+'_program' }
					type="checkbox"
					name="program"
					className="radio"
					checked={ !!_.get(intl, 'program.'+program.id) }
					onChange={ e => dispatch( editProgram({...intl.program, [program.id]: e.target.checked}) ) }
					value={ program.id } />

				{ program.name }

			</label>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		intl: state.intl,
	};
};

export default connect(mapStateToProps)(IntlProgram);
