// index.js

import React from 'react'
import selectn from 'selectn'
import { connect } from 'react-redux'
import ReactSpinner from 'react-spinjs-fix'
import DocumentTitle from 'react-document-title'
import createReactClass from 'create-react-class'

import SavedTestimonials from './components/savedTestimonials'

import { spinjs_config } from './../../../common/spinJsConfig'
import { YOUTUBE_EMBED_START, YOUTUBE_EMBED_END, VIMEO_EMBED } from './../constants'
import { newTestimonial, addTestimonial, editTestimonial, saveIntlData } from './../../../../actions/internationalActions'

const IntlTestimonials = createReactClass({
	getInitialState(){
		return {
			titleValid: false,
			titleTested: false,
			urlValid: false,
			urlTested: false,
		};
	},

	_newTestimonial(e){
		e.preventDefault();
		this.props.dispatch( newTestimonial() );
	},

	_editTitle(e){
		let { dispatch } = this.props,
			name = e.target.getAttribute('name'),
			value = e.target.value,
			testimonial = {},
			video_id = '', is_valid = false;

		testimonial[name] = value;

		is_valid = this._validateTestimonial(name, value);

		if( is_valid ) dispatch( editTestimonial( testimonial ) );
	},

	_editUrl(e){
		let { dispatch } = this.props,
			name = e.target.getAttribute('name'),
			value = e.target.value,
			testimonial = {},
			video_id = '', is_valid = false;

		testimonial[name] = value;

		video_id = this._getVideoId(value);
		testimonial.embed_url = this._getEmbedUrl(value, video_id);
		testimonial.url_is_valid = false;

		is_valid = this._validateTestimonial(name, value);

		dispatch( editTestimonial( testimonial ) )
	},

	_getEmbedUrl(value, video_id){
		return value.includes('youtube') ? this._getYoutubeEmbed(video_id) : this._getVimeoEmbed(video_id);
	},

	_getYoutubeEmbed(video_id){
		return YOUTUBE_EMBED_START+video_id+YOUTUBE_EMBED_END;
	},

	_getVimeoEmbed(video_id){
		return VIMEO_EMBED+video_id;
	},

	_validateTitle(e){
		let { dispatch, intl } = this.props,
			name = e.target.getAttribute('name'),
			value = e.target.value, valid = false,
			testimonial = {}, alreadyExist = null;

		alreadyExist = _.find(intl.testimonialList, {title: value});

		if( !alreadyExist ) valid = true;

		this.setState({
			titleValid: valid,
			titleErr: valid ? '' : 'A testimonial under this title already exists. Use a different/unique name.',
		});

		testimonial.title_is_valid = valid;
		dispatch( editTestimonial( testimonial ) );
	},

	_validateUrl(e){
		let { dispatch } = this.props,
			name = e.target.getAttribute('name'),
			value = e.target.value, valid = false,
			testimonial = {};

		valid = this._validateTestimonial(name, value);

		testimonial.url_is_valid = valid;

		if( valid ){
			dispatch( editTestimonial( testimonial ) );
		}
	},

	_validateTestimonial(name, value){
		let newState = {}, valid = false,
			{ testimonialList } = this.props.intl;

		switch( name ){

			case 'title':
				newState.titleTested = true;

				/*
					1. it's ok if field is empty for styling validation - the form validation will catch that it's empty
					2. passes regex
					3. char length is <= 30
				*/
				if( (!value || /^[A-Za-z0-9 -]+$/.test(value)) && value.length <= 30 ) newState.titleValid = true;
				else{
					//create error message based on failed validation
					newState.titleValid = false;
					if( value && !/^[A-Za-z0-9 ]+$/.test(value) ) newState.titleErr = 'Only letters and numbers allowed.';
					if( value.length > 30 ) newState.titleErr = 'Max number of characters reached.';
				}

				valid = newState.titleValid;

				break;

			case 'url':
				newState.urlTested = true;

				if( value.indexOf('youtube') > -1 || value.indexOf('vimeo') > -1 ) newState.urlValid = true;
				else newState.urlValid = false;

				valid = newState.urlValid;

				break;

			default: break;
		}

		this.setState(newState);
		return valid;
	},

	_getVideoId(url){
		if( url.indexOf('youtube') > -1 ) return url.split('=').pop();
		return url.split('/').pop();
	},

	_saveVideo(e){
		e.preventDefault();

		let { dispatch, intl } = this.props,
			form = {...intl.newTestimonial};

		form.tab = this.refs.hidden.value;

		dispatch( saveIntlData(form, 'ADD_TESTIMONIAL') );
	},

	_formValid(){
		let { intl } = this.props,
			nt = intl.newTestimonial;

		return nt && nt.title && nt.url;
	},

	render(){
		let { dispatch, intl } = this.props,
			{ titleValid, titleTested, urlValid, urlTested, titleErr } = this.state,
			formValid = this._formValid();

		return (
			<DocumentTitle title="Admin Tools | International Students | Testimonials">
				<div className="row i-container">
					<div className="column small-12 medium-7">

						<form onSubmit={ this._saveVideo }>

							<input type="hidden" name="tab" value="testimonials" ref="hidden" />

							<div
								onClick={ this._newTestimonial }
								className="button radius add-testimonial-btn">
									Add New Video Testimonial
							</div>

							<div className={ !intl.newTestimonial ? 'hide' : '' }>

								<label htmlFor={ 'video_title' }>

									Video Title

									<input
										id={ 'video_title' }
										type="text"
										name="title"
										placeholder="Enter video title..."
										className={ !titleValid && titleTested ? 'cost-input error' : 'cost-input' }
										onChange={ this._editTitle }
										onBlur={ this._validateTitle }
										maxLength={30}
										value={ intl.newTestimonial ? (intl.newTestimonial.title || '') : '' } />

									{ !titleValid && titleTested ? <div className="has-error">{ titleErr || 'An error occured.' }</div> : null }
								</label>

								<label htmlFor={ 'video_source' }>

									Source URL (enter YouTube or Vimeo link only)

									<input
										id={ 'video_source' }
										type="text"
										name="url"
										placeholder="Enter video source..."
										className={ !urlValid && urlTested ? 'cost-input error' : 'cost-input' }
										onChange={ this._editUrl }
										onBlur={ this._validateUrl }
										value={ intl.newTestimonial ? (intl.newTestimonial.url || '') : '' } />

									{ !urlValid && urlTested ? <div className="has-error">{'Not a valid video url. Please link YouTube or Vimeo videos only.'}</div> : null }
								</label>

								<button
									disabled={ !formValid || intl.pending || !titleValid || !urlValid }
									className="button radius save">
										{ intl.pending ? <ReactSpinner config={spinjs_config} /> : 'Save' }
								</button>

							</div>

						</form>

						<SavedTestimonials />

					</div>

					<div className="column small-12 medium-5">
						{
							selectn('newTestimonial.url', intl) && selectn('newTestimonial.url_is_valid', intl) ?
							<iframe
								width="100%"
								height="315"
								src={ intl.newTestimonial.embed_url }
								frameBorder="0"
								allowFullScreen />
							: null
						}

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

export default connect(mapStateToProps)(IntlTestimonials);
