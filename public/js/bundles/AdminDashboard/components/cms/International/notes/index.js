// index.js

import React from 'react'
import selectn from 'selectn'
import TinyMCE from 'react-tinymce'
import { connect } from 'react-redux'
import ReactSpinner from 'react-spinjs-fix'
import DocumentTitle from 'react-document-title'
import createReactClass from 'create-react-class'

import NotePreview from './components/notePreview'
import CostField from './../header/components/costField'
import ProgramHeader from './../components/programHeader'

import { NOTES, EDITOR_NAME } from './../constants'
import { spinjs_config } from './../../../common/spinJsConfig'
import { editNote, saveIntlData, editHeaderInfo, resetSaved } from './../../../../actions/internationalActions'

const IntlNotes = createReactClass({
	_saveNotes(e){
		e.preventDefault();

		let { dispatch, intl } = this.props,
			note = {},
			prop_name = intl.activeProgram+EDITOR_NAME;

		note.content = selectn(prop_name+'.content', intl) || this._exampleContent();
		note.id = selectn(prop_name+'.id', intl) || '';
		note.tab = this.refs.hidden.value;
		note.view_type = intl.activeProgram;

		dispatch( saveIntlData(note) );
	},

	_updateEditor(e){
		let { dispatch, intl } = this.props,
			program_notes_prop = intl.activeProgram+EDITOR_NAME;

		dispatch( editNote( {name: program_notes_prop, val: e.target.getContent()} ) );
	},

	_formValid(){
		let { intl } = this.props,
			note_prop = intl.activeProgram+EDITOR_NAME;

		return !!selectn(note_prop, intl);
	},

	render(){
		let { dispatch, intl } = this.props,
			note = intl.new_note || {},
			formValid = this._formValid(),
			program_notes_prop = intl.activeProgram+EDITOR_NAME;

		return (
			<DocumentTitle title="Admin Tools | International Students | Additional Notes">
				<div className="row i-container">
					<div className="column small-12 medium-7">

						<form onSubmit={ this._saveNotes }>

							<input type="hidden" name="tab" value="notes" ref="hidden" />

							<ProgramHeader />

							<div>

								<div>Additional Notes</div>
								<br />

								<TinyMCE
									className="notes-editor"
							        content={ selectn(program_notes_prop+'.content', intl) || '' }
							        config={{
							          plugins: 'autolink link image lists print preview',
							          toolbar: 'link'
							        }}
							        onChange={ this._updateEditor } />

							</div>

							<br />

							<button
								disabled={ !formValid || intl.pending }
								className="button save">
									{ intl.pending ? <ReactSpinner config={spinjs_config} /> : 'Save' }
							</button>

						</form>

					</div>
					<div className="column small-12 medium-5">
						<h5 className="text-center">Additional Notes</h5>
						<NotePreview {...this.props} />
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

export default connect(mapStateToProps)(IntlNotes);
