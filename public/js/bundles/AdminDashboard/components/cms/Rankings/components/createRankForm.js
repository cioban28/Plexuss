// createRankForm.js

import _ from 'lodash'
import React from 'react'
import { connect } from 'react-redux'
import createReactClass from 'create-react-class'

import { savePin, editPin, removePin, updateValue } from './../../../../actions/cmsRankingsActions'

const CreateRankForm = createReactClass({
	_update(e){
		let { dispatch } = this.props;

		var fieldValue = {}, name = e.target.getAttribute('name');

		fieldValue[name] = name === 'image' ? e.target.files[0] : e.target.value;
		dispatch( updateValue(fieldValue) );
	},

	_save(e){
		e.preventDefault();
		let { dispatch } = this.props, form = null;

		form = new FormData(e.target);
		dispatch( savePin(form) );
	},

	render(){
		let { dispatch, rankings } = this.props, { activePin: p } = rankings;

		// temporary - will remove - from legacy code
		var rank_err_msg = 'Rank number is required. 1 - 999';
		var source_err_msg = 'Source url is required. Ex: https://plexuss.com ';

		return (
			<div className="build-fields-container column small-12 large-6">
				<form id="build-rank-pin-form" data-abide onSubmit={ this._save }>

					<input type="hidden" name="save_id" value={p.id || ''} />

					<h4>Add a Ranking</h4>

					<div>
						<label>Ranking Title</label>
						<input
							id="ranking_title"
							type="text"
							name="title"
							value={p.title || ''}
							placeholder="Best School Ever"
							maxLength="70"
							required
							onChange={ this._update } />
						<small className="error">Title is required. No special characters allowed. Only Letters and Numbers.</small>
					</div>

					<div>
						<label># your school is ranked</label>
						<input
							id="school_rank"
							type="number"
							name="rank_num"
							value={p.rank_num || ''}
							placeholder="10"
							min="1"
							max="999"
							required
							onChange={ this._update } />
						<small className="error">{rank_err_msg}</small>
					</div>

					<div>
						<label>Source URL (<i>So students can view the full article</i>)</label>
						<input
							id="source_url"
							type="text"
							name="source"
							value={p.source || ''}
							placeholder="https://url-of-source-here"
							required
							onChange={ this._update } />
						<small className="error">{source_err_msg}</small>
					</div>

					<div>
						<label>Add an image or logo (<i>optional</i>)</label>
						<input
							id="rank_img"
							name="image"
							type="file"
							accept="image/*"
							placeholder="https://url-of-image-here"
							onChange={ this._update } />
					</div>

					<div>
						<label>Description (<i>optional</i>)</label>
						<textarea
							id="rank_description"
							name="rank_descript"
							placeholder="Enter description of this ranking..."
							maxLength="200"
							value={p.rank_descript || ''}
							onChange={ this._update }></textarea>
					</div>

					<button className="save-ranking-btn text-center">Save</button>
				</form>
			</div>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		rankings: state.rankings,
	};
};

export default connect(mapStateToProps)(CreateRankForm);
