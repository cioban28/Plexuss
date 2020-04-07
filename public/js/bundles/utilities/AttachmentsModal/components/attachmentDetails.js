import React, {Component} from 'react'
import { connect } from 'react-redux'

import { deleteAttachmentFromDB } from './../../../AdminDashboard/actions/messagesActions'


/******************************************************************
*	component for file details: the right side panel in attachment modal
*							   Current Attachments	
*	
******************************************************************/
class AttachmentDetails extends Component{
	
	constructor(props){
		super(props);

		this._makePreview = this._makePreview.bind(this);
		this._deleteFileFromDB = this._deleteFileFromDB.bind(this);
	}


	_makePreview(){
		let {url, id, name} = this.props;

		if(!url || !name || !id){
			return;
		}

		let show = '';
	    let ext = url.substr(url.lastIndexOf('.') + 1);



	    if( ext == 'jpeg' || ext == 'jpg' || ext == 'png' || ext == 'gif' || ext == 'bmp'){
	        
	        //string to contruct image link
	        let previewImg = null;
	        previewImg = <img src={url} alt="File Attachment"/>;
	        show = previewImg;
	    }else{
	      
	        //iframe embed for pdf uploads
	        let fsrc = "https://docs.google.com/gview?url="+ url +"&embedded=true";
	     

	        let pdfPreview = <iframe src={fsrc} style={ { width: '100%', height: '500px'} } frameBorder="0"></iframe>;
	        show = pdfPreview;
	    }
	    
	    return show;
	}

	_deleteFileFromDB(){
		let {dispatch, id} = this.props;

		dispatch( deleteAttachmentFromDB(id));

	}

	render(){

		let { url, id, name, date} = this.props;

		return(
			<div className="_AttachmentDetails fattch-rightpanel-cont" data-url={url} data-hid={id}>
                <div className="fattch-details-img-cont">
                    {this._makePreview() }
                </div>
                <div className="fattch-details-detail"> Name: &nbsp; <span className="fattch-name-val">{ name }</span></div>
                <div className="fattch-details-detail"> Date: &nbsp; {date} </div>
                <div className="fattch-details-actions-cont"><div className="fattch-delete-btn" onClick={this._deleteFileFromDB} >delete</div></div>
           </div>
		);
	}
}


const mapStateToProps = (state, props) => {

	return {
		messages: state.messages
	}
};

export default connect(mapStateToProps)(AttachmentDetails)