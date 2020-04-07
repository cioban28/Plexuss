// Banner/index.js

import React from 'react'

import './styles.scss'

 class Banner extends React.Component {
	render(){
		let { children, bg, customClass } = this.props,
			bg_img = {
				backgroundImage: 'url('+bg+')',
			};

		return (
			<div id="_banner" style={bg_img} className={customClass}>
				{ children }
			</div>
		);
	}
}

export default Banner;