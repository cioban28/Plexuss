import React, { Component } from 'react'
import SICTutorials from './Tutorials/index.jsx';
import { MainHeadings } from './Tutorials/MainHeadings.jsx';
import { Header } from './Tutorials/Header.jsx';
import { SubHeadingsHOC } from './Tutorials/SubHeadingsHOC.jsx';
import {
  mainHeading,
  mainSubHeadings,
  createAProfileSubHeadings,
  researchUniversitiesSubHeadings,
  selectYourCollegesSubHeadings,
  connectAndChatSubHeadings,
  promoteYourselfSubHeadings,
  myCounselorSubHeadings,
} from './Tutorials/constants';
import { connect } from 'react-redux';
import { showTutorials, setActiveHeading } from '../../../actions/tutorials';


class NeedHelp extends Component{

  constructor(props) {
    super(props);
    this.state = {
      title: mainHeading,
    }
  }

  handleCardClick = (headerText) => {
    this.setState({ title: headerText })
  }

  handleSubComponentChange = (title) => {
    if(title === mainHeading) {
      this.props.handleRenderComponent('iWantTo');
    } else {
      this.handleCardClick(mainHeading);
    }
  }

  handleShowTutorials = (heading) => {
    const { tutorialsDisplaying, showTutorials, setActiveHeading } = this.props;
    !tutorialsDisplaying && showTutorials();
    setActiveHeading(heading);
  }

  renderTutorials() {
    return <h1>Render tutorials</h1>
  }

  renderSubComponent() {
    const { title } = this.state;
    const { activeHeading } = this.props;
    if(title === mainHeading) {
      return <MainHeadings handleCardClick={this.handleCardClick} subHeadings={mainSubHeadings} />
    } else if(title === mainSubHeadings.createAProfile) {
      return <SubHeadingsHOC subHeadings={createAProfileSubHeadings} handleShowTutorials={this.handleShowTutorials} activeHeading={activeHeading} />
    } else if(title === mainSubHeadings.researchUniversities) {
      return <SubHeadingsHOC subHeadings={researchUniversitiesSubHeadings} handleShowTutorials={this.handleShowTutorials} activeHeading={activeHeading} />
    } else if(title === mainSubHeadings.selectYourColleges) {
      return <SubHeadingsHOC subHeadings={selectYourCollegesSubHeadings} handleShowTutorials={this.handleShowTutorials} activeHeading={activeHeading} />
    } else if(title === mainSubHeadings.connectAndChat) {
      return <SubHeadingsHOC subHeadings={connectAndChatSubHeadings} handleShowTutorials={this.handleShowTutorials} activeHeading={activeHeading} />
    } else if(title === mainSubHeadings.promoteYourself) {
      return <SubHeadingsHOC subHeadings={promoteYourselfSubHeadings} handleShowTutorials={this.handleShowTutorials} activeHeading={activeHeading} />
    } else if(title === mainSubHeadings.myCounselor) {
      return <SubHeadingsHOC subHeadings={myCounselorSubHeadings} handleShowTutorials={this.handleShowTutorials} activeHeading={activeHeading} />
    }
  }

  render(){
    const { title } = this.state;

    return(
      <ul className="rightbar-list">
        <Header title={title} handleSubComponentChange={this.handleSubComponentChange}/>
        { this.renderSubComponent() }
        <div className='middle_blcok'>
          <div className='faq'>FAQ</div>
          <div className='watch'>Have more questions?</div>
          <div className='watch'>Visit our FAQ page.</div>
        </div>
      </ul>
    )
  }
}

const mapStateToProps = state => ({
  tutorialsDisplaying: state.tutorials.show,
  activeHeading: state.tutorials.activeHeading,
});

const mapDispatchToProps = dispatch => ({
  showTutorials: () => { dispatch(showTutorials()) },
  setActiveHeading: (heading) => { dispatch(setActiveHeading(heading)) },
});

export default connect(mapStateToProps, mapDispatchToProps)(NeedHelp);
