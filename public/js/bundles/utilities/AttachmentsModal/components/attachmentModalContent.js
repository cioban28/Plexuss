import React, {Component} from 'react'
import TextButton from './../../textButton/textButton'
import NewAttachments from './newAttachments'
import AttachmentsLibrary from './AttachmentsLibrary'


const NAV = [{title: 'New Attachment'}, {title: 'Current Attachments'}];

export default class AttahmentModalTopNav extends Component{
	
	///////////////////
	//constructor
	constructor(props){
		super(props);

		this.state = { 
			tab: 'New Attachment'
		};

		this._setActive= this._setActive.bind(this);
	}
	
	//////////////////////
	//currently heartbeat for messages rerenders component fairly often
	//want to only update component when new state
	shouldComponentUpdate(np, ns){
		let { tab: sm } = this.state,
			{ tab: nsm } = ns;
	
		return sm !== nsm;
	}
	
	///////////////////
	//sets the current active tab
	_setActive(e, title){

		e.stopPropagation();

		this.setState({ tab: title })
	}
	

	////////////////////////////////
	/////// render function ////////
	render(){

		let {closeHandler } = this.props;
		let {tab} = this.state;
		let that = this;

		return(

			<div className="_AttchModalContents">
				<h5>Choose A File:</h5>
				<div className="file-attch-nav-container">

					{ NAV.map((item) => <TextButton
							key={item.title}
							title={item.title} 
							eventHandler={(e) => that._setActive(e, item.title)}
							active={tab} /> )}
				</div>	

				{ tab === 'New Attachment' ? 
					<NewAttachments _close={closeHandler} /> :
					<AttachmentsLibrary _close={closeHandler} /> }
				
			</div>
		);
	}
}