import React, { Component } from 'react'
import PublicProfile from './public_profile'
import CollegeApplicaion from './college_application'
class GetAcceptedIntl extends Component{
    render(){
        let { handleRenderComponent } = this.props;
        return(
            <ul className="rightbar-list">
                <div className="get_accepted">
                    <Header handleRenderComponent={handleRenderComponent}/>
                    <CollegeApplicaion type={'Intl'} />
                    {/* <Footer /> */}
                </div>
            </ul>
        )
    }
}
function Header(props){
    let { handleRenderComponent } = props;
    return(
        <li className="header row" onClick={() => handleRenderComponent('iWantTo')}>
            <div className="large-2 medium-2 small-3 columns sliding_menu_back_btn_parent">
                <i className="fa fa-angle-left left_angle"></i>
                <div className="sliding_menu_back_btn">back</div>
            </div>
            <div className="larger-10 medium-10 small-9 columns">
                Get accepted to college
            </div>
        </li>
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
function Footer(){
    return(
        <li className="footer_parent footer_bottom_border">
            <div className="footer">
                Consult with a college financial advisor
            </div>
        </li>
    )
}
export default GetAcceptedIntl;