// questionModal.js

import React from 'react';
import createReactClass from 'create-react-class'

export default createReactClass({
	componentWillMount(){
		let { closeMe } = this.props;
		document.addEventListener('click', (e) => e.target.id === '_simplestModal' && closeMe() );
	},

	componentWillUnmount(){
		let { closeMe } = this.props;
		document.removeEventListener('click', (e) => e.target.id === '_simplestModal' && closeMe() );
	},

	render(){
		var { question, yes, no, skip, setup, closeMe, customStyle } = this.props,
			modal = {
				background: '#fff',
				borderRadius: '3px',
				padding: '20px',
				boxShadow: '4px 7px 9px 0px rgba(0,0,0,0.3)'
			};

		if( customStyle ) modal = {...modal, ...customStyle};

		return (
			<div id="_simplestModal" style={styles.container}>
				<div style={modal} className="text-center simple-inner">

					<div>{question}</div>

					{ (no || closeMe) && <div className="button radius no" onClick={ no || closeMe }>No</div> }
					{ yes && <div className="button radius yes" onClick={ yes }>Yes</div> }
					{ setup && <div className="button radius setup" onClick={ setup }>Setup</div> }
					{ skip && <div className="button radius skip" onClick={ skip }>Skip</div> }

				</div>
			</div>
		);
	}
});

const styles = {
	container: {
		position: 'fixed',
		top: 0,left: 0,bottom: 0,right: 0,
		zIndex: 50,
		background: 'rgba(0,0,0,0.8)',
		display: 'flex',
		justifyContent: 'center',
		alignItems: 'center'
	},
}
