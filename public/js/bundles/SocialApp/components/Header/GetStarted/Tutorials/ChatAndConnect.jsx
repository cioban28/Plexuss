import React, { Component } from 'react';
import { connect } from 'react-redux';
import './styles.scss';
import { connectAndChatSubHeadings } from './constants.js'
import { setActiveHeading } from '../../../../actions/tutorials';
import $ from 'jquery';

class ChatandConnect extends Component {
	componentDidMount() {
    this.scrollToTabSection(this.props.activeHeading);
  }

  componentWillReceiveProps(nextProps) {
    this.scrollToTabSection(nextProps.activeHeading);
  }

  scrollToTabSection(activeHeading) {
    const container = $('.sic-tutorials-main');
    if(Object.values(connectAndChatSubHeadings)[0] === activeHeading) {
      container.scrollTop(0);
    } else {
      const targetEl = container && document.querySelector('.sic-tutorials-main').querySelector(`#${this.getFormattedId(activeHeading)}`);
      targetEl && container.scrollTop(targetEl.offsetTop - 50);
    }
  }

  getFormattedId(id) {
  	return id.split(' ').join('_').toLowerCase();
  }

  ifSubHeadingMatchestoAnyInSIC(text) {
  	return Object.values(connectAndChatSubHeadings).some(subHeadingText => subHeadingText === text);
  }

  goToLink(id){
  	if(this.ifSubHeadingMatchestoAnyInSIC(id)) this.props.setActiveHeading(id);

    const formattedId = this.getFormattedId(id);
		const container = $('.sic-tutorials-main');
		const targetEl = document.querySelector('.sic-tutorials-main').querySelector(`#${formattedId}`);
    container.scrollTop(targetEl.offsetTop - 50);
	}

	render(){
		return(
			<div id="chat_connect">
				<h5 className="text_underline">Step 4. Chat and Connect</h5>
				<table style={{width: '100%'}}>
					<tr>
						<td onClick={this.goToLink.bind(this, 'My Network')}>My Network</td>
						<td onClick={this.goToLink.bind(this, 'Connections')}>Connections</td>
					</tr>
					<tr>
						<td onClick={this.goToLink.bind(this, 'Import Contacts')}>Import Contacts</td>
						<td onClick={this.goToLink.bind(this, 'Suggestions')}>Suggestions</td>
					</tr>
					<tr>
						<td onClick={this.goToLink.bind(this, 'Requests')}>Requests</td>
						<td onClick={this.goToLink.bind(this, 'My Messages')}>My Messages</td>
					</tr>
				</table>

				<div id="my_network">
					<h5><img src="/social/images/Icons/tab-network-sic.svg" className='img_icons_trans mr-10'/>My Network</h5>

					<div id='connections'>
						<div className='span_margin_bottom'>
							<span>Connections are the friends, peers, alumni, and college reps that you have connected with. You can search through your connections by typing in the search bar.</span>
						</div>
					</div>

					<div id='import_contacts'>
						<div className='span_margin_bottom'>
							<span>Import Contacts allows you to import your contacts from your Email account. It is an easy way to find and connect with peers and college reps (who also have an account on Plexuss).</span>
						</div>
					</div>

					<div id='suggestions'>
						<div className='span_margin_bottom'>
							<span>Suggestions are people that we think might be interesting for you to connect with. Suggestions are based on your interests like major, schools your are interested in, location, common connections and more.</span>
						</div>
					</div>

					<div id='requests'>
						<div className='span_margin_bottom'>
							<span>Requests are sent to you by other Plexuss Users who want to connect with you.</span>
						</div>
					</div>

					<div className='hide-for-small-only'>
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/Chat-Connect/myNetwork-GIF.gif" />
					</div>

					<div className='show-for-small-only'>
						<img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/Chat-Connect/MyNetworkmobile1.gif' />
					</div>
				</div>

				<div id="my_messages">
					<h5><img src="/social/images/Icons/sic-messages-active.svg" className='img_icons_trans mr-10'/>My Messages</h5>
					<div className='span_margin_bottom'>
						<span>Allows you to chat with your connections. <span className='hide-for-small-only'>Either find your messages in your SIC or go to View All.</span></span>
					</div>
					<div className='hide-for-small-only'>
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/Chat-Connect/MyMessages-Desktop.jpg" />
					</div>
					<div className='show-for-small-only'>
						<img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/Chat-Connect/MyMessages_mobile-new.jpg' />
					</div>
				</div>

			</div>
			)
	}
}

const mapStateToProps = state => ({
  activeHeading: state.tutorials.activeHeading,
  toggleHeadingChanged: state.tutorials.toggleHeadingChanged,
});

const mapDispatchToProps = dispatch => ({
	setActiveHeading: (heading) => { dispatch(setActiveHeading(heading)) },
});

export default connect(mapStateToProps, mapDispatchToProps)(ChatandConnect)
