// index.js

import React from 'react'
import selectn from 'selectn'
import TinyMCE from 'react-tinymce'
import { connect } from 'react-redux'
import ReactSpinner from 'react-spinjs-fix'
import DocumentTitle from 'react-document-title'
import createReactClass from 'create-react-class'

import { SAVE_CONTENT_ROUTE } from './../constants'
import { spinjs_config } from './../../../common/spinJsConfig'
import { saveOverview, updateContent } from './../../../../actions/overviewActions'

const OverviewContent = createReactClass({
	_formValid(){
		let { overview } = this.props;
		return !!selectn('content.overview_content', overview);
	},

	_saveContent(e){
		e.preventDefault();

		let { dispatch, overview } = this.props,
			form = new FormData(e.target);

		form.append('overview_content', overview.content.overview_content);
		form.append('overview_source', overview.content.overview_source);

		dispatch( saveOverview(form, SAVE_CONTENT_ROUTE, overview.content) );
	},

	_updateSource(e){
		let { dispatch } = this.props;
		dispatch( updateContent( e.target.value, e.target.getAttribute('name') ) );
	},

	_updateEditor(e){
		let { dispatch } = this.props;
		dispatch( updateContent( e.target.getContent(), 'overview_content' ) );
	},

	render(){
		let { overview } = this.props,
			formValid = this._formValid();

		return (
			<DocumentTitle title="Admin Tools | Overview Content">
				<div className="row i-container overview_container">
					<div className="columns small-12 medium-10 medium-centered">

						<form onSubmit={ this._saveContent }>

							<input type="hidden" name="tab" value="overview_content" ref="hidden" />

							{ selectn('init_pending', overview) && <div className="spinner-wrapper">
																		<ReactSpinner config={spinjs_config} />
																	</div> }

							{ selectn('init_done', overview) &&
								<div>
									<TinyMCE
										ref={'content'}
										className="tinymce-editor"
								        content={ selectn('content.overview_content', overview) || '' }
								        config={{
								          plugins: 'autolink link image lists print preview textcolor colorpicker',
								          toolbar: 'undo redo | forecolor backcolor | bold italic | link image | alignleft aligncenter alignright'
								        }}
								        onChange={ this._updateEditor } />

								    <br />

								    <label>
								    	Source:

								    	<input
								    		type="text"
								    		value={ selectn('content.overview_source', overview) || '' }
								    		onChange={ this._updateSource }
								    		placeholder="Enter source name"
								    		name="overview_source" />
								    </label>
								</div>
							}

						    <br />

							<div className="overview_actions text-right">
								<button
									disabled={ !formValid || overview.pending }
									className="button save">
										{ overview.pending ? <ReactSpinner config={spinjs_config} /> : 'Save' }
								</button>
							</div>

						</form>

					</div>
				</div>
			</DocumentTitle>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		overview: state.overview,
	};
};

export default connect(mapStateToProps)(OverviewContent);
