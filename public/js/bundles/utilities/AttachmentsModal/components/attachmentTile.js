import React, {Component} from 'react'
import {connect} from 'react-redux'

import {getAttachmentDetails} from './../../../AdminDashboard/actions/messagesActions'

class AttachmentTile extends Component{

	constructor(props){
		super(props);


		this._makeTile= this._makeTile.bind(this);
		this._getAttchDetails = this._getAttchDetails.bind(this);
	}

	_makeTile(){
		let {url, id, name} = this.props;

		let show = '';
	    let ext = url.substr(url.lastIndexOf('.') + 1);

	    if( ext === 'jpg' || ext === 'jpeg' || ext === 'png' || ext === 'gif' || ext === 'bmp'){
	        show = <img src={url} alt="Attachment preview" className="fattch-tile-img"/>;
	    }
	    else{
	        show = <div className="fattch-tile-noprev">Preview not Available</div>;
	    }

	    return show;

	}
	_getAttchDetails(e){


		let {id, dispatch} = this.props;

		dispatch( getAttachmentDetails(id));
	}

	_getAttchDetailsCallback(){

	}

	render(){
		let {url, id, name} = this.props;

		return(
			<div data-id={id} className="fattch-detail-tile"   onClick={this._getAttchDetails }>
	                <div className="fattch-tileimg-cont">  {this._makeTile()}  </div>
	        </div>
		);
	}
}


const mapStateToProps = (state, props) => {

	return {
		messages: state.messages
	}
};

export default connect(mapStateToProps)(AttachmentTile)