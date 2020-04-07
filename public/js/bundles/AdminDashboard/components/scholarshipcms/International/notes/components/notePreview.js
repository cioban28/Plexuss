// notePreview.js

import React from 'react'
import selectn from 'selectn'
import createReactClass from 'create-react-class'

import { EDITOR_NAME } from './../../constants'

export default createReactClass({
	_getRawMarkup(){
		let { intl } = this.props;
		return { __html: selectn(intl.activeProgram+EDITOR_NAME+'.content', intl) || '<div></div>' };
	},

	render(){
		return (
			<div
				className="note-preview"
				dangerouslySetInnerHTML={ this._getRawMarkup() } />
		);
	}
});
