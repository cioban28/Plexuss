import React, { Component } from 'react'
import { Link } from 'react-router-dom'
class PayForCollege extends Component{
    render(){
        let { handleRenderComponent, closeCallback } = this.props;
        let current = window.location.pathname + window.location.search;
        return(
            <ul className="rightbar-list">
                <Header handleRenderComponent={handleRenderComponent}/>
                <Card1 current={current} text={'View All Scholarships'} sub_title={'2.4M views'} url={'/scholarships'} closeCallback={closeCallback}/>
                {/* <Card1 current={current} text={'Sallie Mae'} sub_title={'1.4K downloads'} url={''}/> */}
                {/* <SubHeader /> */}
                <Card1 current={current} text={'$1000 Scholarships'} sub_title={'2,590 Applied'} url={'/scholarships?filter=1000'} closeCallback={closeCallback}/>
                <Card1 current={current} text={'Sports Scholarships'} sub_title={'Be the first to apply!'} url={'/ncsa'} closeCallback={closeCallback}/>
                {/* <Footer /> */}
            </ul>
        )
    }
}
function Header(props){
    let { handleRenderComponent } = props;
    return(
        <li className="research_head row" onClick={() => handleRenderComponent('iWantTo') }>
            <div className="large-2 medium-2 small-3 columns sliding_menu_back_btn_parent">
                <i className="fa fa-angle-left left_angle"></i>
                <div className="sliding_menu_back_btn cursor">back</div>
            </div>
            <div className="larger-10 medium-10 small-9 columns cursor">
                Pay for college
            </div>
        </li>
    )
}
function SubHeader(){
    return(
        <li className="pfc_sub_heading">
            <div>
                Pay for college
            </div>
            {/* <i className="fa fa-star start_icon1"></i> */}
        </li>
    )
}
class Card extends Component{
    constructor(props){
        super(props);
        this.state={checkMark: false}
        this.toggleCheckMark = this.toggleCheckMark.bind(this);
    }
    toggleCheckMark(){
        this.setState({checkMark: !this.state.checkMark});
    }
    render(){
        let { text, sub_title } = this.props;
        return(
            <li>
                <a className="list_item_research pay_for_college row" onClick={this.toggleCheckMark}>
                    <div className="columns checkMark_parent" onClick={this.toggleCheckMark}>
                        <img src={this.state.checkMark ? './social/images/rightBar/Checkmark-checked@3x.png' : './social/images/rightBar/Checkmark-unchecked@3x.png' } onClick={this.toggleCheckMark} alt=""/>
                    </div>
                    <div className="text_pay_for_college columns">
                        <div className="title">
                            {text}
                        </div>
                        {/* <div className="sub_title">{sub_title}</div> */}
                    </div>
                </a>
            </li>
        )
    }
}

class Card1 extends Component{
    render(){
        let { text, sub_title, url, current, closeCallback } = this.props;
        return(
            <li onClick={closeCallback}>
                <Link className="list_item_research pay_for_college row" to={url}>
                    <div className="text_pay_for_college_card1 large-11 medium-11 small-11 columns">
                        <div className={"title " +(!!current && current === url && 'active')}>
                            {text}
                        </div>
                        <div className="sub_title">{sub_title}</div>
                    </div>
                    <div className="right_arrow large-1 medium-1 small-1 columns">
                        <i className="fa fa-angle-right"></i>
                    </div>
                </Link>
            </li>
        )
    }
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
        <li className="footer_parent">
            <div className="footer">
                Consult with an admission advisor with Premium
            </div>
        </li>
    )
}
export default PayForCollege;