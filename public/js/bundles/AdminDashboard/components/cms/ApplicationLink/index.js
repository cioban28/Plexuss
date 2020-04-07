//index.js

import React from 'react'
import createReactClass from 'create-react-class'
import { connect } from 'react-redux'
import ReactSpinner from 'react-spinjs-fix'
import { toastr } from 'react-redux-toastr'
import DocumentTitle from 'react-document-title'

import { spinjs_config } from './../../common/spinJsConfig'
import { editAppLink, getAppLink, saveAppLink, resetSaved } from './../../../actions/overviewActions'

import './styles.scss'

const ID = 'application_link';

const Application_Link = createReactClass({
	getInitialState(){
		return {
			valid: false,
			haveTested: false,
			copied_to_clipboard: false,
			codeHighlighted: false,
			clipboard_msg: '',
		};
	},

	componentWillMount(){
		let { dispatch, overview } = this.props;

		if( !overview.app_link_init_done ) dispatch( getAppLink() );
	},

	componentWillReceiveProps(np){
		let { dispatch, overview } = this.props;

		// if next state is different from this state AND next state saved is true, trigger toastr
		if( overview.app_link_saved !== np.overview.app_link_saved && np.overview.app_link_saved ){
			var OPTIONS = {
				timeOut: 7000, // by setting to 0 it will prevent the auto close
				component: (
					<div className="toastr-component">
						<div className="main">Your changes have been saved and published!</div>
						<div className="sub">You can view your changes <a href={overview.college_page_url} target="_blank">here</a></div>
					</div>
				),
			};

			toastr.success('', OPTIONS);
			dispatch( resetSaved() );
		}
	},

	_edit(e){
		let { dispatch, overview } = this.props;

		this._validateURL(e.target.value);

		dispatch( editAppLink(e.target.value) );
	},

	_resetLink(){
		let { dispatch } = this.props;

		this.setState({
			valid: false,
			haveTested: false,
		});

		dispatch( editAppLink('') );
	},

	_saveAppLink(e){
		e.preventDefault();

		let { dispatch, overview } = this.props;

		// triming link just in case user puts space at front or back
		dispatch( saveAppLink( overview.application_link.trim() ) );
	},

	_validateURL(url){
		var valid = false;

		if( url.includes('http') && url.includes('://') ) valid = true;

		this.setState({
			valid: valid,
			haveTested: true,
		});
	},

	_highlightCode(e){
	    let _createRange, _getSelection, copied, msg,
	    	_this = e.target;

	    if( window.getSelection ){ //non IE users
			_getSelection = window.getSelection();
	        _createRange = document.createRange();
	        _createRange.selectNodeContents(_this);
	        _getSelection.removeAllRanges();
	        _getSelection.addRange(_createRange);

	    }else if( document.body.createTextRange ){ // for IE users
	    	_createRange = document.body.createTextRange();
	        _createRange.moveToElementText(_this);
	        _createRange.select();
	    }

	    /* copy to clipboard - support is still ify, so use try catch to let user know
		    if it was copied or if they have to manually do ctrl-c */
	    try {
			copied = document.execCommand('copy');
			msg = copied ? 'Successfully copied to clipboard!' : 'Windows: press ctrl-c to copy. Mac: command-c to copy.';

		} catch (err) {
			copied = false;
			msg = 'Oops. There was an error in attempting to copy code to clipboard. Please manually highlight code. Then, for Windows: press ctrl-c to copy. For Mac: command-c to copy.';
		}

		this.setState({
			copied_to_clipboard: copied,
			clipboard_msg: msg,
		});

		this._removeClipboardMsg();
	},

	_removeClipboardMsg(){
		// after 5 seconds, remove clipboard msg
		var _this = this;

		setTimeout(() => {
			_this.setState({copied_to_clipboard: false});
		}, 1500);
	},

	render(){
		let { overview } = this.props,
			{ valid, haveTested, copied_to_clipboard, clipboard_msg } = this.state,
			copied_show = copied_to_clipboard ? '' : 'hide';

		return (
			<DocumentTitle title="Admin Tools | Application Link">
				<div id="_application_link_container" className="row">
					<div className="column small-12 medium-6 large-5">
						<form onSubmit={ this._saveAppLink }>

							<label htmlFor={ ID }>

								<div className="app-link-label">Application Link</div>

								<input
									id={ ID }
									type="text"
									name={ ID }
									className={ !valid && haveTested ? 'has-err' : '' }
									onChange={ this._edit }
									onFocus={ this._edit }
									onBlur={ this._edit }
									placeholder={'Enter url to your application page here'}
									value={ overview.application_link || '' } />

								{ (!valid && haveTested) &&
									<div className="err-msg">{"Invalid url. Please check to make sure url contains at least 'http(s)://' "}</div> }

							</label>

							<div className="actions">
								<div className="cancel" onClick={ this._resetLink }>Cancel</div>
								<div>
									<button
										disabled={ !valid || overview.app_link_pending }
										className="button save">
											{ overview.app_link_pending ?
													<div className="spinner-wrapper"><ReactSpinner config={spinjs_config} /></div> : 'Save' }

									</button>
								</div>
							</div>

							<div className="embed-label">
								<div>Embed Code</div>
								<div>{"Copy & paste this code onto your application's \"Thank You\" page"}</div>
							</div>

							<div className="codeblock">
								<code onClick={ this._highlightCode } id="_embedCode">
									{'<img src="https://plexuss.com/trackApplyPixel" height="1" width="1" style="display:none;" />'}

									<div className={"clipboard "+copied_show}>{ clipboard_msg }</div>
								</code>
							</div>

						</form>
					</div>

					<div className="column small-12 medium-6 large-7 text-center">
						<br className="show-for-small-only" />
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/applynow_example.png" alt="Apply Now Example" />
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

export default connect(mapStateToProps)(Application_Link);
