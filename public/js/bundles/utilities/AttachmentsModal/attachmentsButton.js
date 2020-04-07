import React, {Component} from 'react'
import { connect } from 'react-redux'
import './styles.scss'

import PicTextButton from './../picTextButton/picTextButton'
import AttachmentsModal from './components/attachmentsModal'

/**********************************************************
*  root of attachment modal component:  button coupled with modal	
*  Attachment Modal allows user to attachfiles to post
*  modal also contains a library of attchments based on past atttachments 
**********************************************************/

class AttachmentButton extends Component{
	constructor(props){
		super(props);

		//initially set to nothing
		//to trick initial render (due to messages heartbeat and customizing rendering)
		//showModal gets set on user click
		this.state = { showModal: false};  

		this._togglemodal = this._togglemodal.bind(this);
	}

	_togglemodal(e){

		e && e.stopPropagation();

		if(this.state.showModal == true){
			this.setState({ showModal: false});
		}else
			this.setState({showModal: true});
	}

	

	render(){

		let { messages} = this.props;
		let that = this;


		return(

			
			messages.attachmentNumber && messages.attachmentNumber > 0 ? 
				<div className="_attachmentsButton action disabled" >
					<PicTextButton imageClass="attch-icon" text="Attach Files" /> 
				</div>
				:
				<div className="_attachmentsButton action">
					<PicTextButton imageClass="attch-icon" text="Attach Files" callback={this._togglemodal}/>
					{ this.state.showModal && <AttachmentsModal _close={this._togglemodal} /> }
				</div>
			
		);
	};

}

const mapStateToProps = (state, props) => {
	return {
		messages: state.messages,
	};
};

export default connect(mapStateToProps)(AttachmentButton);
