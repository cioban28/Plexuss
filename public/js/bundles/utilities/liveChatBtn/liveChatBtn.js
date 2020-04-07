import React from 'react'
import './styles.scss'


export default React.createClass({
	
	render(){

		let styles = '';

		let {text, style} = this.props;

		return(
			<div id="_LiveChatBtn" className="btn-container">
				<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/skype_icon_sic.png" />
				<a id="_skypeCall" href="skype:live:premium_156?call">call</a> &nbsp; | &nbsp;
				<a id="_skypeChat" href="skype:live:premium_156?chat">chat</a>
			</div>
		);
		
	}

});