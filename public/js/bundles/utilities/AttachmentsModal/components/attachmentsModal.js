import React, {Component} from 'react'
import './../styles.scss'

import CustomModal from './../../customModal'
import AttachmentModalContent from './attachmentModalContent'

/********************************************************
*  AttachmentModal sheel, has close button
*********************************************************/
export default class AttachmentsModal extends Component{
	constructor(props){
		super(props);
	}

	render() {
		let { _close } = this.props;

		return (
			
				<CustomModal backgroundClose={_close} >
					<div className="_AttachmentModal">

						<div className="close-btn" onClick={ _close} >&times;</div>

						<AttachmentModalContent closeHandler={ _close} />
					</div>
				</CustomModal>
			
			
		); 

	}

};

