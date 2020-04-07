// overviewVideoForm.js

import React from 'react'
import selectn from 'selectn'
import ReactSpinner from 'react-spinjs-fix'
import createReactClass from 'create-react-class'

import UploadedItem from './uploadedItem'
import UploadedPreview from './uploadedPreview'

import { SAVE_VID_ROUTE } from './../../constants'
import { spinjs_config } from './../../../common/spinJsConfig'
import { setNewItem, saveOverview } from './../../../../actions/overviewActions'
import { YOUTUBE_EMBED_START, YOUTUBE_EMBED_END, YOUTUBE_URL, VIMEO_EMBED, VIMEO_URL } from './../../International/constants'

export default createReactClass({
	getInitialState(){
		return {
			valid: false,
			haveTested: false,
		};
	},

	_formValid(){
		let { valid, haveTested } = this.state;
		return valid && haveTested;
	},

	_saveOverviewVideo(e){
		e.preventDefault();
		let { dispatch, overview } = this.props,
			form = new FormData(e.target);

		form.append('is_youtube', overview.new_vid.is_youtube);
		form.append('video_id', overview.new_vid.video_id);
		form.append('section', 'overview'); //hard coded b/c all images/vids uploaded here are for overview

		dispatch( saveOverview(form, SAVE_VID_ROUTE, overview.new_vid) );
	},

	_newVid(e){
		let { dispatch } = this.props,
			val = e.target.value,
			vid = {name: val},
			valid = false;

		if( val.includes('youtube') ) vid.is_youtube = 1;
		else if( val.includes('vimeo') ) vid.is_youtube = 3;

		if( vid.is_youtube === 1 && val.includes('https://www.youtube.com/watch?v=') ){
			valid = true;
			vid.video_id = val.split('=').pop();
			vid.source = YOUTUBE_EMBED_START+vid.video_id+YOUTUBE_EMBED_END;

		}else if( vid.is_youtube === 3 && val.includes('https://vimeo.com/') ){
			valid = true;
			vid.video_id = val.split('/').pop();
			vid.source = VIMEO_EMBED+vid.video_id;
		}

		this.setState({
			valid: valid,
			haveTested: true,
		});

		dispatch( setNewItem(vid) );
	},

	render(){
		let { dispatch, overview } = this.props,
			{ valid, haveTested } = this.state,
			formValid = this._formValid();

		return (
			<div className="row i-container overview_container">
				<div className="columns small-12 medium-6">

					<form onSubmit={ this._saveOverviewVideo }>

						<input type="hidden" name="tab" value="overview_video" ref="hidden" />

						<div className="uploaded-items stylish-scrollbar-mini">
							{ overview.init_pending && <div className="spinner-wrapper">
															<ReactSpinner config={spinjs_config} />
														</div> }

							{ selectn('videos', overview) &&
								overview.videos.map((vid) => <UploadedItem
																key={vid.video_id}
																item={vid}
																{...this.props} />) }
						</div>

						<div>
							<label className="add-vid-label">

								<div>Video URL <small>(YouTube or Vimeo)</small></div>

								<input
									type="text"
									className={ !valid && haveTested ? 'add-vid-input error' : 'add-vid-input' }
									value={ selectn('new_vid.name', overview) || '' }
									onChange={ this._newVid }
									placeholder="Enter YouTube or Vimeo link here"
									name="name" />

								{ (!valid && haveTested) && <div className="err-msg">{'Not a valid YouTube or Vimeo URL.'}</div> }

							</label>
						</div>

						<div className="overview_actions text-right">
							<button
								disabled={ !formValid || overview.pending }
								className="button save">
									{ overview.pending ? <ReactSpinner config={spinjs_config} /> : 'Save' }
							</button>
						</div>

					</form>

				</div>
				<div className="column small-12 medium-6">
					{ selectn('new_vid', overview) && <UploadedPreview item={ selectn('new_vid', overview) || {} } /> }
				</div>
			</div>
		);
	}
});
