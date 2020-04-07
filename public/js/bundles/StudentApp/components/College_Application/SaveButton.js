// /College_Application/SaveButton.js

import React, { Component } from 'react'

export default class SaveButton extends Component{
	constructor(props){
		super(props);
	}

	// Makes sure nothing is pending before enabling save button
	_isAnythingPending(){
		let { _profile } = this.props;
		for (let key of Object.keys(_profile)) {
			if (key.endsWith('_pending') && _profile[key] == true) {
				return true;
			}
		}

		return false;
	}

	render(){
		let { _profile: _p, page_done, label, error_msg, classes = '' } = this.props,
			page = _p.page,
			something_pending = this._isAnythingPending(),
			disabled = _p.save_pending || something_pending,
			_label =  _p.save_pending ? 'Saving...' : (something_pending && page == 'submit') ? 'Loading your Data' : (label || 'Save & Continue');
			_label = _p.upload_pending ? 'Uploading...' : _label;
		if( page_done ){

				disabled =	!_p[page_done] ||
										 _p.save_pending ||
										 _p.upload_pending ||
										 _p.grading_scales_pending ||
										 _p.grading_conversion_pending ||
										 (something_pending && page == 'submit');
										}
		return (
			<div className={classes}>
				<button
					className={`${(this.props.routeId === 'applications' || this.props.routeId === 'uploads') && 'hide' } save bottom-bar-save-btn`}
					disabled={ this.props.routeId === 'essay' || this.props.routeId === 'sponsor' ? disabled : (this.props.skip || this.props.myApplicationsLength ? false :disabled) }
					onClick={this.props.onClick}>
						{ !!this.props.myApplicationsLength ? 'Next' : _label }
				</button>
				{this.props.skip  &&
					<span className="skip skip-btn" onClick={this.props.skipHandler}>
						Skip
					</span>
				}

				{ (disabled && !_p.save_pending && !something_pending) && <div>{ error_msg }</div> }
				{ !_p.save_pending && page == 'submit' && something_pending && <div className="mt10">&nbsp;Please be patient.</div> }
			</div>
		);
	}
}
