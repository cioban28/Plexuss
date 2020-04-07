import React, { Component } from 'react';
import { connect } from 'react-redux';
import './styles.scss';
import {researchUniversitiesSubHeadings} from './constants.js'
import FindColleges from './FindColleges'
import Major from './Major'
import Ranking from './Ranking'
import $ from 'jquery';
import { setActiveHeading } from '../../../../actions/tutorials';


class ResearchUniversities extends Component{
	componentDidMount() {
    this.scrollToTabSection(this.props.activeHeading);
  }

  componentWillReceiveProps(nextProps) {
    this.scrollToTabSection(nextProps.activeHeading);
  }

  scrollToTabSection(activeHeading) {
    const container = $('.sic-tutorials-main');
    if(Object.values(researchUniversitiesSubHeadings)[0] === activeHeading) {
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
		return(
				<div id="research_universities">
					<h5 className="text_underline">Step 2 Research Universities</h5>
				<table style={{width: '100%'}}>
					<tr>
						<td onClick={this.goToLink.bind(this, 'Find Colleges')}>1. {researchUniversitiesSubHeadings.findColleges}</td>
						<td onClick={this.goToLink.bind(this, 'Major')}>2. {researchUniversitiesSubHeadings.major}</td>
					</tr>
					<tr>
						<td onClick={this.goToLink.bind(this, 'Ranking')}>3. {researchUniversitiesSubHeadings.ranking}</td>
						<td></td>
					</tr>
				</table>
					<FindColleges />
					<Major />
					<Ranking />
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

export default connect(mapStateToProps, mapDispatchToProps)(ResearchUniversities);
