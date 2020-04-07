

import React, {Component} from 'react'

import { connect } from 'react-redux'
import { saveNewAttachment,
		updateConvoActions } from './../../../AdminDashboard/actions/messagesActions'


import CancelandAttchButtons from './cancelAndAttchButtons'
import CustomInputTFile from './../../customInputTFile/customInputTFile'

class NewAttachments extends Component{

	constructor(props){
		super(props);

		this.state = { 
			isSaving: false,
			imgPrev: null,
			savingError: ''
		};

		this._previewAttachment = this._previewAttachment.bind(this);
		this._saveNewAttachment = this._saveNewAttachment.bind(this);
		this._saveNewCallback = this._saveNewCallback.bind(this);
	}
	
	_previewAttachment(name){

		if(!name) return;

		var file = $('._inputFile input[name="'+ name + '"]')[0].files[0];

		if(!file){ 
			$('._newAttchments .fattch-preview-img').attr('src','');
			return;
		}
	    
	    var name = file.name;
	    var ext = name.substr(name.lastIndexOf('.') + 1);
	    ext = ext.toLowerCase();

	    //create a temporay url out of file -- used to display preview
	    var tmppath = URL.createObjectURL(file);

	    $('.none-available').remove();

	    if(tmppath && (ext === 'jpg' || ext === 'jpeg' || ext === 'png' || ext === 'gif' || ext === 'bmp')){
	      this.setState({ imgPrev: tmppath});
	    }
	    else{
	        this.setState({ imgPrev: ''});
	    }

	}
	_saveNewAttachment(name){

		if(this.state.isSaving == true)
			return;

		this.setState({ savingError : ""});

		if(!name){
		 this.setState({savingError: 'oops! something went wrong!'});	
		 return;
		}

		let file =  $('._inputFile input[name="'+ name + '"]')[0].files[0];
		
		if(!file){ 
			this.setState( {savingError : "No File chosen."});
			return;
		}

		let file_name = $('.FileName').val() || file.name;

		this.setState({ isSaving: true,
						error: null });

		var formD = new FormData();

		formD.append('name', file_name);
		formD.append('file', file);

		this.props.dispatch( saveNewAttachment(formD, this._saveNewCallback));


	}

	_saveNewCallback(res){

		let {dispatch, _close, messages} = this.props;
		// console.log(data.data);
		let data = res.data;
		let url = data.url;
		let name = data.name;
		let field = $('#send_field');
		let vfield = document.getElementById('send_field');

		if(messages.activeThread.has_text == 0){
			field.append('<div contenteditable="false" class="upload-file-wrapper mt10 mb10" data-ftype="other">' + 
	                '<input type="hidden" name="file_info" />' +
	                // '<div class="remove-file-btn fr">&times;</div>'+
	                '<div>' +
	                    '<div class="uploadDocsSpriteLarger other d-block"></div>' +
	                    '<small>attachment: </small> <br/>' + name + 
	                '</div>' +
	                 '<a download href="'+ url + '" class="attch-downloadlink">  download </a> ' + ' | ' + 
	                 '<a href="#" class="view-attachment" data-url="'+ url +'"> view </a>' +
	            '</div>');
		}else{
			field.append('<div>'+ url +'</div>');
		}
		dispatch( updateConvoActions({send_field: field.html(), key: false}) );

		_close();
	}


	render(){

		let {messages, _close} = this.props;

		return(
			<div className="_newAttchments" >
				<form>
					<div className="new-attch-cont opened">
						<div className="fattch-preview-cont">
							
							{ this.state.imgPrev === '' ? 
								  <div className="none-available">No Preview Available</div>
							  : this.state.imgPrev ?
								  <img className="fattch-preview-img" src={ this.state.imgPrev}/>
							  : null
							}

						</div>
						
						<input className="FileName" name="FileName" placeholder="Name the Attachment (optional)" />
						
						<CustomInputTFile inline={true} name="attachment" callback={() => {this._previewAttachment("attachment") }}>
							<div className="fattch-attch-btn">
								<div className="browse-icon"></div> <span className="fattch-txt">Browse...</span>
							</div>
						</CustomInputTFile>
					
					</div>
					
					{messages.attch_saving && <div className="attch-saving"> saving attachment... </div>}
					{this.state.savingError && <div className="attch-error"> { this.state.savingError }</div>}
					{messages.attch_err && <div className="attch-error">We apologize! An error occured while trying to attach your file.</div>}
					
					<CancelandAttchButtons cancelCallback={_close}  attchCallback={ () => {this._saveNewAttachment('attachment')} }/>
				</form>
			</div>

		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		user: state.user,
		messages: state.messages,
	};
};


export default connect(mapStateToProps)(NewAttachments)