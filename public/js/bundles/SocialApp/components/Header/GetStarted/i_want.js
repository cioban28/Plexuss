import React, { Component } from 'react'
import ReactTooltip from 'react-tooltip';
class IWant extends Component{
    render(){
        let { handleRenderComponent } = this.props;
        return(
            <ul className="rightbar-list">
                <Header />
                <CardGetStarted img={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Find+Colleges.svg'} text={'Research colleges & universities'} handleRenderComponent={handleRenderComponent} renderComponent={'researchColleges'}/>
                <CardGetStarted img={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Scholarships+(1).svg'} text={'Scholarships'} handleRenderComponent={handleRenderComponent} renderComponent={'payForCollege'}/>
                <CardGetStarted img={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/college-app-assessment.svg'} text={'College Application Assessment'} handleRenderComponent={handleRenderComponent} renderComponent={'getAccepted'}/>
                <CardGetStarted img={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Network.svg'} text={'Network with college students and alumni'} handleRenderComponent={handleRenderComponent} renderComponent={'network'}/>
                <div className="middle_blcok" onClick={() => handleRenderComponent('needHelp')}>
                    <div className="icon_parent">
                        <img src="/social/images/rightBar/noun_help_670401_000000@2x.png" alt=""/>
                    </div>
                    <div className="need_help">Need help?</div>
                    <div className="watch">Check out these tutorials</div>
                </div>
            </ul>
        )
    }
}
function Header(){
    return(
        <li className="get_started_head">I want to</li>
    )
}
function UpgradeButton(){
    return(
        <li className="upgrade_btn_parent">
            <a href="/checkout/premium" className="upgrade_btn">
                <img src="/images/upgrade_logo.png" alt=""/>
                <div>Upgrade to Premium</div>
            </a>
        </li>
    )
}
class CardGetStarted extends Component{
    render(){
        let { img, text, handleRenderComponent, renderComponent, showTooltip } = this.props;
        return(
            <li >
                <a className="list_item row" onClick={() => handleRenderComponent(renderComponent)}>
                    <div className="larger-2 medium-2 small-2 columns">
                        <img className="getStartedImg" src={img} />
                    </div>
                    <div className="text larger-8 medium-8 small-8 columns"> 
                        {text }
                    {
                        showTooltip && <div id='college-application-tooltip-cont'>
                            <a data-tip data-for='college-application-tooltip'><img classname="tooltip-icon" src="/social/images/Group 2138.svg"/></a>
                            <ReactTooltip id='college-application-tooltip' multiline={true} place="left" type="dark" effect="float">
                                <div>
                                    <img src="/social/images/college-app-assessment.svg"/>
                                    <h4>Why complete your College Application Assessment?</h4>
                                </div>
                                <br />
                                <span>Discover your chances of getting admitted, prepare your application matterials</span>
                                <br />
                                <span>in one place, and give our professional college advisors the opportunity to</span>
                                <br />
                                <span>assist you in making informed college decisions.</span>
                            </ReactTooltip>
                        </div>
                    
                }</div>
                    <div className="right_arrow larger-1 medium-1 small-1 columns">
                        <i className="fa fa-angle-right"></i>
                    </div>
                </a>
            </li>
        )
    }
}
export default IWant;
