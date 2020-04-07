// index.js

import React from 'react'
import { connect } from 'react-redux'
import ReactSpinner from 'react-spinjs-fix'
import DocumentTitle from 'react-document-title'
import createReactClass from 'create-react-class'

import CostField from './components/costField'
import ProgramHeader from './../components/programHeader'
import PreviewGenerator from './../components/previewGenerator'

import { HEADER_FIELDS, PROGRAMS } from './../constants'
import { spinjs_config } from './../../../common/spinJsConfig'
import { saveIntlData } from './../../../../actions/internationalActions'

const IntlHeader = createReactClass({
	_saveHeader(e){
		e.preventDefault();
		let { dispatch, intl } = this.props,
			form = {...intl};

		if( intl.pending ) return;

		// add page name so backend knows what data to save
		form.tab = this.refs.hidden.value;

		dispatch( saveIntlData(form) );
	},

	_formValid(){
		let { intl } = this.props,
			valid = false,
			program = _.find(PROGRAMS, {id: intl.activeProgram});

		if( !program ) return false;

		_.each(HEADER_FIELDS, h => {
			let name = program.id+'_'+h.name;
			valid = !!intl[name];
			if( valid ) return !valid; // only one field needs to be filled in to be able to save
		});

		return valid;
	},

	render(){
		let { intl } = this.props,
			formValid = this._formValid();

		return (
			<DocumentTitle title="Admin Tools | International Students | Header">
				<div className="row i-container">
					<div className="column small-12 medium-7">
						<form onSubmit={ this._saveHeader }>

							<input type="hidden" name="tab" value="header_info" ref="hidden" />

							<ProgramHeader />

							{ HEADER_FIELDS.map( (field) => <CostField
																key={field.name}
																cost={field}
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
							fields={HEADER_FIELDS}
							title={'annual international cost'}
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

export default connect(mapStateToProps)(IntlHeader);
