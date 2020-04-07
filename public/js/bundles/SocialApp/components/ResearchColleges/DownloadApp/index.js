// ResearchColleges/DownloadApp/index.js
import React, { Component } from 'react';
import { connect } from 'react-redux';
import Modal from 'react-modal';
import axios from 'axios';
import PhoneNumberVerifier from './../../../../StudentApp/components/common/PhoneNumberVerifier';
import './styles.scss';
import { Helmet } from 'react-helmet';

const customStyles = {
  content : {
    top                   : '50%',
    left                  : '50%',
    right                 : 'auto',
    bottom                : 'auto',
    marginRight           : '-50%',
    transform             : 'translate(-50%, -50%)'
  }
};

class DownloadApp extends Component {
	constructor(props){
		super(props)

		this.state ={
            showSMSModal: false,
            sent: false,
            pending: false,
		}
        this.handleSMS = this.handleSMS.bind(this);
	}
    handleSMS() {
        this.setState({pending: true});
        axios({
            method: 'post',
            url: '/phone/plexussAppSendInvitation',
            data: { phone: this.props._profile.phone },
            headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),},
        }).then(res =>  {
            setTimeout(() => { this.setState({sent: true, pending: false}) }, 500);
        }).catch(error => {
            console.log(error);
        });
    }

    render(){
        let { phone_error } = this.props._profile;
        let disableBtn = phone_error == null || phone_error == true || this.state.pending || this.state.sent;
        return (
			<div className="social-download-app">
	    		<Helmet>
	    			<title>Download Plexuss App for your Phone | Plexuss.com</title>
	       			<meta name="description" content="Download plexuss application for your iOS or Android phone." />
	    		</Helmet>
				<div className="social-download-app-container">
					<div className="downapp-banner">
						<img src="/social/images/student-group.png" />
					</div>
					<div className="download-app-imgs-container">
						<div className="download-app-pin">
							<div className="downapp-logo">
								<img src="/social/images/rightBar/Group 898@2x.png" />
							</div>
							<div className="downapp-title">Use Plexuss on the Go!</div>
							<div className="downapp-subtitle">Download the Plexuss app! Available on iOS and Android</div>
							<div className="downapp-btns-container row">
								<a href="http://apple.co/2x0hv8I" target="_blank" className="downapp-btns small-12 medium-6 columns">
									<img src="https://s3.us-west-2.amazonaws.com/asset.plexuss.com/download-appstore.png" />
								</a>
								<a href="http://bit.ly/2MSG5U7" target="_blank" className="downapp-btns small-12 medium-6 columns">
									<img src="https://s3.us-west-2.amazonaws.com/asset.plexuss.com/google-play.png" />
								</a>
							</div>
							<div className="downapp-sms">Or <span className="sms-link" onClick={() => this.setState({showSMSModal:true})}>send an SMS link</span> to download the Plexuss app to your phone</div>
						</div>
					</div>
				</div>
				<Modal
				isOpen={this.state.showSMSModal}
				onRequestClose={() => this.setState({showSMSModal: false})}
				style={customStyles}
				>
					<div className="sms-modal-container">
						<div className='sms-close-btn' onClick={() => this.setState({showSMSModal: false})}>&#10005;</div>
						<div className="sms-modal-title">Enter your Phone number below to receive a link to download the Plexuss App!</div>
						<div className="row sms-input">
							<div className="small-12 medium-6 columns"> <PhoneNumberVerifier /> </div>
							<div className="small-12 medium-6 columns"> <div className={'sms-send-btn '+ (disableBtn  && 'disabled')} onClick={() => this.handleSMS()}>{this.state.pending ? 'Sending...' : this.state.sent ? 'Sent' : 'Send an SMS'}</div> </div>
						</div>
					</div>
				</Modal>
			</div>
        );
    }
}

const mapStateToProps = (state, props) => {
    return {
        _profile: state._profile,
    };
};

export default connect(mapStateToProps)(DownloadApp);
