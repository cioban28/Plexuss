import React, { Component } from 'react'
import { connect } from 'react-redux';
import './styles.scss'
import { myCounselorSubHeadings } from './constants.js'
import { setActiveHeading } from '../../../../actions/tutorials';
import $ from 'jquery';

class MyCounsler extends Component {
	componentDidMount() {
    this.scrollToTabSection(this.props.activeHeading);
  }

  componentWillReceiveProps(nextProps) {
    this.scrollToTabSection(nextProps.activeHeading);
  }

  scrollToTabSection(activeHeading) {
    const container = $('.sic-tutorials-main');
    if(Object.values(myCounselorSubHeadings)[0] === activeHeading) {
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
  	return Object.values(myCounselorSubHeadings).some(subHeadingText => subHeadingText === text);
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
			<div id="my_councler">
				<h5 className="text_underline">MyCounselor</h5>
				<div id="counsler">
					<div className='span_margin_bottom'>
						<span>MyCounselor is in development. Check back soon!</span>
					</div>
					<div className='span_margin_bottom'>
						<span>For the time being, you are able to chat with a Counselor in your messages!</span>
					</div>
					<div>
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/counsler_chat/my_counselor_chat.PNG" />
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

export default connect(mapStateToProps, mapDispatchToProps)(MyCounsler);
