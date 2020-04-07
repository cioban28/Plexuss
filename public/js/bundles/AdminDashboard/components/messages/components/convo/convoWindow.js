// convoWindow.js

import { connect } from 'react-redux'
import React, { Component } from 'react'

import MeBubble from './meBubble'
import IncomingBubble from './incomingBubble'
import CustomModal from './../../../../../utilities/customModal'

import { getOlderConvo } from './../../../../actions/messagesActions'

var prev_date = null,
	prev_formatted = '';

class ConvoWindow extends Component{
	constructor(props){
		super(props);
		this._buildConvo = this._buildConvo.bind(this);
		this._showPrevious = this._showPrevious.bind(this);
		this._getOlderConvo = this._getOlderConvo.bind(this);
		this._scrollToBottom = this._scrollToBottom.bind(this);
		this._buildAttachmentUI = this._buildAttachmentUI.bind(this);
		this._attachmentClicked = this._attachmentClicked.bind(this);

		this.state = {
			previewOpen: false,
			gview_start: 'https://docs.google.com/gview?url=',
			gview_end: '&embedded=true',
			convo_props: '',
		}
	}

	shouldComponentUpdate(np, ns){
		let { messages: _m } = this.props,
			{ messages: _nm } = np,
			{ previewOpen: po } = this.state,
			{ previewOpen: npo } = ns,
			activeT = _.get(_m, 'activeThread', {}),
			nextActiveT = _.get(_nm, 'activeThread', {}),
			this_convo_len = _.get(activeT, 'convo.length'),
			next_convo_len = _.get(nextActiveT, 'convo.length');

		// only update if older_convo_pending is diff, 
		// or the convo length from this to next state is diff 
		// or if a different thread is now active
		// or if this previewOpen is different from next previewOpen
		let getting_older_convo = _m.init_older_convo_pending !== _nm.init_older_convo_pending,
			got_new_msgs = this_convo_len !== next_convo_len,
			new_thread_active = activeT.thread_id !== nextActiveT.thread_id,
			preview_open_diff = po != npo,
			ellipsis_diff = _m.show_ellipsis !== _nm.show_ellipsis;

		return getting_older_convo || got_new_msgs || new_thread_active || preview_open_diff || ellipsis_diff;
	}

	componentDidUpdate(np, ns){
		let { messages: _m } = this.props,
			{ messages: _nm } = np,
			{ previewOpen: po } = this.state,
			{ previewOpen: npo } = ns,
			attachment = document.getElementsByClassName('view-attachment');

		let older_convo_pending_same = _m.init_older_convo_pending === _nm.init_older_convo_pending,
			preview_open_diff = po != npo;

		// only scroll down if it's not the older convo updating
		if( older_convo_pending_same && !preview_open_diff ) this._scrollToBottom();

		// if there attachment links in the convo, add event handler to open link in modal
		if( _.get(attachment, 'length', 0) > 0 )
			Array.from(attachment).forEach(a => a.addEventListener('click', this._attachmentClicked));
	}

	componentWillUnmount(){
		let attachment = document.getElementsByClassName('view-attachment');

		if( _.get(attachment, 'length', 0) > 0 ) 
			Array.from(attachment).forEach(a => a.removeEventListener('click', this._attachmentClicked));
	}

	_attachmentClicked(e){
		e.preventDefault();

		let attachment = e.target.dataset.url || false;
		this.setState({previewOpen: attachment});
	}

	_scrollToBottom(){
		var _window = $('#_convo');

		_window.delay(500).animate({
			scrollTop: _window[0].scrollHeight+5000
		}, 500);
	}

	_buildConvo(c, index){
		var { user: _u } = this.props;
		var same_date = false,
			date_only = c.date.split(' ')[0],
			formatted = null;
		if(_u.id != c.user_id){
			this.setState({convo_props: c});
		}
		// if prev_date set, then check if current date is same as prev date
		if( prev_date ){
			same_date = moment(prev_date).isSame(date_only);
			prev_date = date_only;

		}else prev_date = date_only; // if null, set prev_date

		// format date
		formatted = moment(c.date).fromNow();

		// if message was sent to day, show Today instead of time ago
		if( moment().isSame(date_only, 'day') ) formatted = 'Today';

		// if formatted is 'a year ago', modify to '1 year ago'
		if( formatted[0] === 'a' ) formatted = '1 '+formatted.split('a ')[1];

		// it's possible for msgs to be in same month but different days, so the date divider would output duplicate months, 
		// so this check prevents duplicate time ago texts
		if( formatted === prev_formatted ) same_date = true;
		else prev_formatted = formatted;

		// only show date-divider if this date is diff from prev date
		return (
			<div key={index} className="bubble-container">
				{ !same_date && <div className="date-divider"><span>{ formatted }</span></div> }
				{ _u.id == c.user_id ? <MeBubble convo={c} user={_u} /> : <IncomingBubble convo={c} /> }
			</div>
		);
	}

	_getOlderConvo(){
		let { dispatch, messages: _m } = this.props,
			thread = _.get(_m, 'activeThread', {});

		// get older convo if thread has a convo at least and is not currently pending
		if( _.get(thread, 'convo.length') && !_m.init_older_convo_pending ) 
			dispatch( getOlderConvo(thread.thread_id, _.get(thread, 'convo[0].msg_id')) );
	}

	_showPrevious(){
		let { messages: _m } = this.props;
		return !_.get(_m, 'activeThread.no_previous_msgs', 0) && _.get(_m, 'activeThread.convo.length', 0) >= 20;
	}

	_buildAttachmentUI(){
		let { previewOpen, gview_start, gview_end } = this.state;

		if( previewOpen.includes('.jpg') || 
			previewOpen.includes('.jpeg') || 
			previewOpen.includes('.gif') || 
			previewOpen.includes('.png') || 
			previewOpen.includes('.bmp')){
			
				return (<img src={previewOpen} alt="image preview" />);
		}else {
		
			let link = gview_start + previewOpen + gview_end;

			return (
				
				<iframe src={link} style={ { width: '100%', height: '500px'} } frameBorder="0"></iframe>
			);
		}

		// return (<img src={previewOpen} alt="image preview" />);
	}

	threadClickHandler = () => {
		this.props.unsetShowConversation();
	}

	render(){
		let { messages: _m } = this.props,
			{ previewOpen } = this.state,
			thread = _.get(_m, 'activeThread', {}),
			convo = _.get(thread, 'convo', []),
			previous_msg = this._showPrevious(),
			_attachment = '';

		prev_date = null; // every render, reset prev_date
		prev_formatted = '';

		// return either iframe for pdfs or img for image files
		if( previewOpen ) _attachment = this._buildAttachmentUI();

		return (
			<div id="_convo" className="stylish-scrollbar">
				<div onClick={this.threadClickHandler} className="mobile_view">
					<span><img className=""/>‹Back</span>
					<span className="pic-wrapper" style={{backgroundImage: 'url()'}}></span>
					<span className="header_style"></span>
				</div>
				{ previous_msg &&
					<div className="prev-msgs" onClick={ !_m.init_older_convo_pending && this._getOlderConvo }>
						{ _m.init_older_convo_pending ? <div className="prev-loader" /> : 'Previous Messages' }
					</div> }

				{ convo.map((c, index) => this._buildConvo(c, index)) }		


				{ (!convo || _.isEmpty(convo)) && <div className="no-convo">No previous conversation. Send a message to start a conversation!</div> }

				{ _m.show_ellipsis && 
					<div id="_ellipsis">
						<div className="ellips">{ [1,2,3].map(d => <div key={d} className="ellips-dot" />) }</div>
						{ _m.ellipsis_name && <span>{ _m.ellipsis_name || 'Johnathan' }</span> }
					</div> }

				{ previewOpen &&
					<CustomModal backgroundClose={ e => this.setState({previewOpen: false}) }>
						<div className="attachment-preview-modal">
							{ _attachment }
						</div>
					</CustomModal> }

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

export default connect(mapStateToProps)(ConvoWindow);
