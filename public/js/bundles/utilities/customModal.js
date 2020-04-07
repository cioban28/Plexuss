// customModal.js

import React from 'react'

export default class CustomModal extends React.Component{
	constructor(props) {
		super(props)
		this.state = {
			hovering: false,
		}
		this._myBackgroundClose = this._myBackgroundClose.bind(this)
	}
	_myBackgroundClose(e){

		let {backgroundClose} = this.props;

		e && e.stopPropagation();

		if(e.target.id === '_customModal')
			backgroundClose(e);

	}

	render(){
		let { children, styling, close, backgroundClose } = this.props,
			defaultStyle = {
				position: 'fixed',
				top: 0, right: 0, left: 0, bottom: 0,
				backgroundColor: 'rgba(0,0,0,0.8)',
				padding: '20px',
				display: 'flex',
				justifyContent: 'center',
				alignItems: 'center',
				overflow: 'auto',
				zIndex: 1000,
			},
			modal = styling ? Object.assign({}, defaultStyle, {...styling}) : defaultStyle;

		return (



			<div id="_customModal" style={modal}  onClick={backgroundClose ? this._myBackgroundClose : null} >
				{ close ? <div className="text-right" style={styles.close} onClick={close}>&times;</div> : null }
				{ children }
			</div>
		);
	}
}

const styles = {
	close: {
		cursor: 'pointer',
		fontSize: '42px',
		fontWeight: '600',
		color: '#797979'
	}
}