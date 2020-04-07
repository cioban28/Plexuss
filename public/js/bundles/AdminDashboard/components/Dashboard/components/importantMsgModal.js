// /Dashboard/components/importantMsgModal.js

import React, { Component } from 'react'

import CustomModal from './../../../../utilities/customModal'

import { BANNER_STYLES } from './../constants'

const styles = BANNER_STYLES;

export default class VerifiedBlock extends Component{
	constructor(props){
		super(props);
	}

	render(){
		let { close, msgSent, sendMsg } = this.props;

		return (
			<CustomModal id="imortant_msg_modal" backgroundClose={ close }>
				<div style={styles.container}>
					<div style={styles.close} onClick={ close }>&times;</div>
					
					{ !msgSent &&
							<div className="text-left">
								<h3 style={styles.title}>{'How can we be of assistance?'}</h3>
								<h6 style={styles.subtitle}>{'Please describe the issue below and we will get right on it'}</h6>
								<div className="important-message-notices"></div>
							</div> }

					{ !msgSent ?
						<form id="important_msg_form" onSubmit={ e => e.preventDefault() }>

							<textarea 
								id="important_msg_body" 
								style={styles.msgBox} 
								type="text" 
								name='msg' />

							<button 
								id="important_msg_submit" 
								onClick={ sendMsg }  
								style={styles.submitBtn}>
								    Submit
							</button>

						</form> 
						: 
						<div className="text-center">
							<div style={styles.doneMsg}>{'Thank you for your inquiry, we will be in touch shortly.'}</div>
							<button 
								onClick={ close }
								style={styles.submitBtn}>
									Ok
							</button>
						</div>
					}

				</div>
			</CustomModal>
		);
	}
}