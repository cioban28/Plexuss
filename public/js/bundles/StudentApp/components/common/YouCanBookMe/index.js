// YouCanBookMe/index.js

import React from 'react'

import './styles.scss'

export default React.createClass({
	componentDidMount(){
		window.addEventListener && window.addEventListener("message", function(event){
			if (event.origin === "https://plexuss-premium.youcanbook.me"){
				document.getElementById("ycbmiframeplexuss-premium").style.height = event.data + "px";
			}
		}, false);
	},

	render(){
		let { closeMe } = this.props;

		return (
			<div id="_YouCanBookMe" onClick={ closeMe }>

				<div className="modal">
					<iframe 
						src="https://plexuss-premium.youcanbook.me/?noframe=true&skipHeaderFooter=true" 
						id="ycbmiframeplexuss-premium" 
						style={{width:'100%', height:'1000px', border: '0px', backgroundColor: 'transparent'}}
						frameBorder="0" 
						allowTransparency="true" />
				</div>

			</div>
		);
	}
});