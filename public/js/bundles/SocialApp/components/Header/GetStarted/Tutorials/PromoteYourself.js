import React, { Component } from 'react'
import { connect } from 'react-redux';
import './styles.scss'
import {promoteYourselfSubHeadings} from './constants.js'
import PromotePublicProfile from './PromotePublicProfile'
import CollegeApplication from './CollegeApplication'
import NewsFeed from './NewsFeed'
import MyArticles from './MyArticles'
import { setActiveHeading } from '../../../../actions/tutorials';
import $ from 'jquery';

class PromoteYourself extends Component{
	componentDidMount() {
    this.scrollToTabSection(this.props.activeHeading);
  }

  componentWillReceiveProps(nextProps) {
    this.scrollToTabSection(nextProps.activeHeading);
  }

  scrollToTabSection(activeHeading) {
    const container = $('.sic-tutorials-main');
    if(Object.values(promoteYourselfSubHeadings)[0] === activeHeading) {
      container.scrollTop(0);
    } else {
      const targetEl = container && document.querySelector('.sic-tutorials-main').querySelector(`#${this.getFormattedId(activeHeading)}`);
      targetEl && container.scrollTop(targetEl.offsetTop - 50);
    }
  }

  getFormattedId(id) {
  	return id.split(' ').join('_').toLowerCase();
  }

  goToLink(id){
    this.props.setActiveHeading(id);
    const formattedId = this.getFormattedId(id);
		const container = $('.sic-tutorials-main');
		const targetEl = document.querySelector('.sic-tutorials-main').querySelector(`#${formattedId}`);
    container.scrollTop(targetEl.offsetTop - 50);
	}

	render(){
		const { setActiveHeading } = this.props;

		return(
			<div id="promote_yourself">
				<h5 className="text_underline">5. Promote Yourself</h5>
				<table style={{width: '100%'}}>
						<tr>
							<td onClick={this.goToLink.bind(this, 'Promote Public Profile')}>1. Public Profile</td>
							<td onClick={this.goToLink.bind(this, promoteYourselfSubHeadings.collegeApplication)}>2. {promoteYourselfSubHeadings.collegeApplication}</td>
						</tr>
						<tr>
							<td onClick={this.goToLink.bind(this, promoteYourselfSubHeadings.newsFeed)}>3. {promoteYourselfSubHeadings.newsFeed}</td>
							<td onClick={this.goToLink.bind(this, promoteYourselfSubHeadings.myArticles)}>4. {promoteYourselfSubHeadings.myArticles}</td>
						</tr>
				</table>
				<PromotePublicProfile setActiveHeading={setActiveHeading} />
				<CollegeApplication setActiveHeading={setActiveHeading} />
				<NewsFeed />
				<MyArticles />
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

export default connect(mapStateToProps, mapDispatchToProps)(PromoteYourself);
