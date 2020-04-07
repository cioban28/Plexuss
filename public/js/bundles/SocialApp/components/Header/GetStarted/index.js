import React, { Component } from 'react'
import './styles.scss'
import IWant from './i_want'
import ResearchColleges from './research_colleges'
import PayForCollege from './pay_for_college'
import GetAccepted from './get_accepted'
import GetAcceptedIntl from './get_accepted_to_colleges_intl'
import Network from './network'
import NeedHelp from './need_help'
import SICTutorials from './Tutorials/index.jsx';
import { connect } from 'react-redux';

let researchUrls=["/college", "/college-majors", "/ranking", "/ranking/categories", "/comparison", "/college-fair-events", "/download-app"];
let getacceptedurls=["/social/manage-colleges"];
let scholarshipUrls=["/scholarships","/scholarships?filter=1000"];

class GetStarted extends Component{
    constructor(props){
        super(props);
        this.state={
            renderComponent: 'iWantTo',
        }
        this.handleRenderComponent = this.handleRenderComponent.bind(this);
        this.handleURL = this.handleURL.bind(this);
        this._renderSubComp = this._renderSubComp.bind(this);
    }
    componentDidMount(){
        this.handleURL();
    }
    componentDidUpdate(prevProps) {
        if(this.props.location !== prevProps.location){
           this.handleURL();
        }
    }
    handleURL(){
        if ( scholarshipUrls.includes(window.location.pathname + window.location.search )) {
            this.handleRenderComponent('payForCollege')
        }
        else if ( researchUrls.includes(window.location.pathname )) {
            this.handleRenderComponent('researchColleges')
        }
        else if ( getacceptedurls.includes(window.location.pathname )) {
            this.handleRenderComponent('getAccepted')
        }
        else if (window.location.pathname.indexOf("/one-app/") > -1) {
            this.handleRenderComponent('getAccepted')
        }
    }
    handleRenderComponent(component){
        this.setState({renderComponent: component})
    }
    _renderSubComp(){
        let { closeCallback } = this.props;

        switch(this.state.renderComponent){
            case 'iWantTo': return <IWant handleRenderComponent={this.handleRenderComponent} />
            case 'researchColleges': return <ResearchColleges handleRenderComponent={this.handleRenderComponent} closeCallback={closeCallback}/>
            case 'payForCollege': return <PayForCollege handleRenderComponent={this.handleRenderComponent} closeCallback={closeCallback}/>
            case 'getAccepted': return <GetAccepted handleRenderComponent={this.handleRenderComponent}/>
            case 'getAcceptedIntl': return <GetAcceptedIntl handleRenderComponent={this.handleRenderComponent}/>
            case 'network': return <Network handleRenderComponent={this.handleRenderComponent}/>
            case 'needHelp': return <NeedHelp handleRenderComponent={this.handleRenderComponent}/>
        }
    }
    render(){
        const { showTutorials } = this.props;
        return(
            <div>
                <div className="rightbar get_started">
                    {this._renderSubComp()}
                </div>
                {
                    // showTutorials && <SICTutorials />
                }
            </div>
        )
    }
}

const mapStateToProps = state => ({
    showTutorials: state.tutorials.show,
});

export default connect(mapStateToProps, null)(GetStarted);
