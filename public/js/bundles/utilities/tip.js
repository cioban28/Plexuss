// tip.js

import React from 'react'

class Tip extends React.Component {
	constructor(props) {
		super(props)
	}

	render(){
		let { children, styling, classes } = this.props, 
			defaultStyle = {
				position: 'absolute',
				backgroundColor: '#202020',
				color: '#fff',
				padding: '10px',
				borderRadius: '3px',
				textAlign: 'left',
				fontSize: '14px',
				zIndex: 7
			},
			tip = styling ? {...defaultStyle, ...styling} : defaultStyle;
			
		return (
			<div className={classes} style={tip}>
				{children}
			</div>
		);
	}
}

export default Tip;