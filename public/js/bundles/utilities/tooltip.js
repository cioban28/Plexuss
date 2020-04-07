// tooltip.js

import React from 'react'
import Tip from './tip'

class Tooltip extends React.Component {
	constructor(props){
		super(props);

		this.state = {
			hovering: false
		}
	}

	showTip(){
		this.setState({hovering: true});
	}

	hideTip(){
		this.setState({hovering: false});
	}

	render(){
		let { children, toolTipStyling, tipStyling, customText, tipId } = this.props,
			defaultStyle = {
				display: 'inline-block',
				color: '#fff',
				border: '1px solid #fff',
				borderRadius: '100%',
				fontSize: '8px',
				margin: '0 0 0 3px',
				width: '12px',
				height: '12px',
				verticalAlign: 'middle',
				textAlign: 'center'
			},
			tooltip = toolTipStyling ? {...defaultStyle, ...toolTipStyling} : defaultStyle;

		return (
			<div 
				id={tipId || ''}
				style={tooltip} 
				onMouseEnter={this.showTip} 
				onMouseLeave={this.hideTip} 
				onTouchStart={this.showTip} 
				onTouchEnd={this.hideTip}> 

					{ customText ? customText : '?' }

					{
						this.state.hovering?
						<Tip styling={tipStyling}>
							{ children }
						</Tip> : null
					}
			</div>
		);
	}
}

export default Tooltip;