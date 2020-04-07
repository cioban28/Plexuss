// overviewImageForm.js

import React from 'react'
import selectn from 'selectn'
import ReactSpinner from 'react-spinjs-fix'
import createReactClass from 'create-react-class'

import UploadedItem from './uploadedItem'
import UploadedPreview from './uploadedPreview'

import { SAVE_IMG_ROUTE } from './../../constants'
import { spinjs_config } from './../../../../common/spinJsConfig'
import { setNewItem, saveOverview, resetSaved } from './../../../../../actions/overviewActions'

export default createReactClass({
	getInitialState(){
		return {
			err: false,
			err_msg: '',
		};
	},

	componentWillReceiveProps(np){
		let { dispatch, overview } = this.props;

		// if img err is different for np and this.props and it's true, show error, then reset img_err
		if( overview.img_err !== np.overview.img_err && np.overview.img_err ){
			this.setState( this._dimensionsErr() );
			dispatch( resetSaved() );
		}
	},

	_formValid(){
		let { overview } = this.props;
		return !!selectn('new_img.file', overview);
	},

	_setNewImg(e){
		let { dispatch } = this.props,
			img = {},
			file = e.target.files[0];

		// if uploaded file is an image, set new item
		if( file.type.match(/image.*/) ){
			img.bg = {backgroundImage: 'url('+URL.createObjectURL(file)+')'};
			img.name = file.name;
			img.file = file;

	    	this.setState( this._validImage() ); //set no err
			dispatch( setNewItem(img) ); //dispatch new item

		}else this.setState( this._fileTypeError() ); //set file type err
	},

	_validImage(){
		return {
			err: false,
		};
	},

	_fileTypeError(){
		return {
			err: true,
			err_msg: 'Incorrect file type attempted. Please upload only .png, .jpg, .jpeg, or .gif.',
		};
	},

	_dimensionsErr(){
		return {
    		err: true,
    		err_msg: 'This image does not have the correct dimensions. Please crop/upload photos that are 830px by 380px.',
    	};
	},

	_saveOverviewImage(e){
		e.preventDefault();
		let { dispatch, overview } = this.props,
			form = new FormData(e.target);

		dispatch( saveOverview(form, SAVE_IMG_ROUTE, overview.new_img) );
	},

	render(){
		let { overview } = this.props,
			{ err, err_msg } = this.state,
			formValid = this._formValid();

		return (
			<div className="row i-container overview_container">
				<div className="columns small-12 ov-directions">
					<div>Carousel Pictures <small>We recommend using between 7 and 10 high quality images to display your campus</small></div>
				</div>

				<div className="column small-12 medium-6">

					<form onSubmit={ this._saveOverviewImage }>

						<input type="hidden" name="tab" value="overview_image" ref="hidden" />

						<div className="uploaded-items stylish-scrollbar-mini">
							{ overview.init_pending && <div className="spinner-wrapper">
															<ReactSpinner config={spinjs_config} />
														</div> }

							{ selectn('images', overview) &&
								overview.images.map((img) => <UploadedItem
																key={img.id}
																item={img}
																{...this.props} />) }
						</div>

						<div className="overview_actions">
							<div>
								<label htmlFor="_overview_image" className="upload-label-btn">Upload</label>
								<input
									id="_overview_image"
									onChange={ this._setNewImg }
									accept="image/*"
									name="overview_image"
									style={{display: 'none'}}
									type="file" />
							</div>
							<div className="text-right">
								<button
									disabled={ !formValid || overview.pending }
									className="button save">
										{ overview.pending ? <ReactSpinner config={spinjs_config} /> : 'Save' }
								</button>
							</div>
						</div>

						{ err && <div className="upload-err">{err_msg}</div> }

					</form>

				</div>
				<div className="column small-12 medium-6">
					<UploadedPreview item={ selectn('new_img', overview) || {} } />
				</div>
			</div>
		);
	}
});
