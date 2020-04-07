// display.js
import React from 'react';
import createReactClass from 'create-react-class'

export default createReactClass({
	render(){
		return (this.props.if) ? <div>{this.props.children}</div> : null;
	}
});
