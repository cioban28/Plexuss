import React, {Component} from 'react'
import { connect } from 'react-redux'
import { loadAttachments,
		updateConvoActions,
		setAttachmentNumber } from './../../../AdminDashboard/actions/messagesActions'

import CancelandAttchButtons from './cancelAndAttchButtons'
import AttachmentTile from './attachmentTile'
import AttachmentDetails from './attachmentDetails'
import SpinLoader from './../../spinLoader/spinLoader'


class AttachmentsLibrary extends Component{

	constructor(props){
		super(props);
		this._AttachCallback = this._AttachCallback.bind(this);

	}
	componentDidMount(){
		let {dispatch, messages} = this.props;
		
		dispatch(loadAttachments());

	}
	_AttachCallback(){
		let {dispatch, _close, messages} = this.props;
		let field = $('#send_field');
		let viewing = messages.viewing_attch;

		if(!viewing) return;


		if(messages.activeThread.has_text == 0){
			field.append('<div contenteditable="false" class="upload-file-wrapper mt10 mb10" data-ftype="other">' + 
	                '<input type="hidden" name="file_info" />' +
	                // '<div class="remove-file-btn fr">&times;</div>'+
	                '<div>' +
	                    '<div class="uploadDocsSpriteLarger other d-block"></div>' +
	                    '<small>attachment: </small> <br/>' + viewing.name + 
	                '</div>' +
	                 '<a download href="'+ viewing.url + '" class="attch-downloadlink">  download </a> ' + ' | ' + 
	                 '<a href="#" class="view-attachment" data-url="'+ viewing.url +'"> view </a>' +
	            '</div>');
		}else{
			field.append('<div>'+ viewing.url +'</div>');
		}

		dispatch(setAttachmentNumber(1));
		dispatch( updateConvoActions({send_field: field.html(), key: false}) );

		_close();
	}



	render(){

		let {messages, _close} = this.props;
		let attch = messages.attachments;

		// console.log(messages);
		//let {viewing_attch} = messages.viewing_attch;

		return(
			<div className="_attchmentsLibrary">
				

					<div className="row managment-container">
						<div className="column medium-8 curr-choosefrom">
			
						{ attch && attch.length > 0 ?  
							<div className="_AttchTilesContainer">
								{ attch.map( (item) => { return <AttachmentTile url={item.url} 
																			id={item.id} 
																			name={item.name} 
																			key={item.id}  /> })}
							</div>
							: 
							messages.attch_loading === true ?
								<SpinLoader />
								: <div>No attchments yet.</div> }
						</div>


						<div className="column medium-4 curr-chosen">
							{  messages.viewing_attch ? 
									<AttachmentDetails name={ messages.viewing_attch.name} 
													   url={ messages.viewing_attch.url} 
													   id={ messages.viewing_attch.id} 
													   date={messages.viewing_attch.date} /> 
								:
								messages.loading_attch_details === true ?
										<SpinLoader />
									: <div className="light-text-c">Select a file to view...</div> }
						</div>


					</div>
				
				{messages.attch_saving && <div className="attch-saving"> saving attachment... </div>}	
				<CancelandAttchButtons  cancelCallback={_close} attchCallback={this._AttachCallback} />
			</div>
		);
	}
}

const mapStateToProps = (state, props) => {
	return{
		messages: state.messages
	}
};

export default connect(mapStateToProps)(AttachmentsLibrary)