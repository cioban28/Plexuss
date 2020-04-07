import React, {Component} from 'react'
import { connect } from 'react-redux'


/**********************************************************
* Cancel and Attach buttons for Attachments modal
*	props:  cancelCallback = cancel button callback
*			attchCallback = attach button's callback
***********************************************************/
class CancelandAttchButtons extends Component{
	constructor(props){
		super(props);

	}

	render(){

		let {cancelCallback, attchCallback, messages} = this.props;

		return(
			<div className="_cancelAndAttchCont">
				<div className="cancel-btn" onClick={cancelCallback}>Cancel</div>
				
				{ messages.attch_loading == true || messages.loading_attch_details  == true || messages.delete_attch_pending  == true ?
					<div className="attch-file-btn disabled" >Attach File</div>
					:
					<div className="attch-file-btn"  onClick={attchCallback} >Attach File</div>
				}
			
			</div>

		);
	}

}


const mapStateToProps = (state, props) => {

	return {
		messages: state.messages
	}
};

export default connect(mapStateToProps)(CancelandAttchButtons)