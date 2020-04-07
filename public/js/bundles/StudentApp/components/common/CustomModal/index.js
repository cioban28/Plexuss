// CustomModal/index.js

import React from 'react'

import './styles.scss'

class CustomModal extends React.Component {
	constructor(props) {
		super(props)
	}
	componentWillMount(){
		let { closeMe } = this.props;
		if( closeMe ) document.addEventListener('click', (e) => { if( e.target.id === '_CustomModal' ) closeMe(); });
	}

	componentWillUnmount(){
		let { closeMe } = this.props;
		if( closeMe ) document.removeEventListener('click', (e) => { if( e.target.id === '_CustomModal' ) closeMe(); });
	}

	render(){
		let { children, classes } = this.props;

		return (<section id="_CustomModal">{ children }</section>);
	}
}
export default CustomModal;