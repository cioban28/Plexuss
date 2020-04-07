// tooltip.js

import React from 'react'
import Tip from './tip'

export default class Tooltip extends React.Component{
	constructor(props) {
		super(props)
		this.state = {
			hovering: false,
		}
	}

	render(){
		let { children, tipStyling, customText, customClass } = this.props,
			{ hovering } = this.state;

		return (
			<div 
				id="_common_tooltip"
				className={ customClass || '' }
				onMouseEnter={ () => this.setState({hovering: true}) } 
				onMouseLeave={ () => this.setState({hovering: false}) } 
				onTouchStart={ () => this.setState({hovering: true}) } 
				onTouchEnd={ () => this.setState({hovering: false}) } > 

					{ customText ? customText : '?' }
					{ hovering && <Tip customClass={customClass}>{ children }</Tip> }

			</div>
		);
	}
}
