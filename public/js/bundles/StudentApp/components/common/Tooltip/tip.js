// tip.js

import React from 'react'

import './styles.scss'

export default class Tip extends React.Component{
	constructor(props) {
		super(props)
	}
	render(){
		let { children, customClass } = this.props;

		return (
			<div id="_common_tip" className={ customClass || '' }>
				<div className="arrow" />
				{children}
			</div>
		);
	}
};
