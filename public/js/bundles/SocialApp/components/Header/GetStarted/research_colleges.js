import React, { Component } from 'react'
import { Link } from 'react-router-dom'
class ResearchColleges extends Component{
    constructor(props){
        super(props);
        this.state={
            seeMore: false,
        }
    }

    render(){
        let { handleRenderComponent, closeCallback } = this.props;
        let active = window.location.pathname;
        return(
            <ul className="rightbar-list">
                <Header handleRenderComponent={handleRenderComponent}/>
                <CardGetStarted active={active} href={"/college"} img={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Find+Colleges.svg'} text={'Find Colleges'} sub_title={'2.4M views'} imgClass={'find_colleges_img'} sub_title_class={''} closeCallback={closeCallback}/>
                <CardGetStarted active={active} href={'/college-majors'} img={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Majors.svg'} text={'Majors'} sub_title={''} imgClass={'majors'} sub_title_class={''} closeCallback={closeCallback}/>
                {/* <CardGetStarted active={active} href={'/scholarships'} img={'/social/images/rightBar/Scholarships@2x.png'} text={'Scholarships'} sub_title={''} imgClass={'scholarship'} sub_title_class={''}/> */}
                <CardGetStarted active={active} href={'/ranking'} img={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/ranking.svg'} text={'Ranking'} sub_title={''} imgClass={'ranking'} sub_title_class={''} closeCallback={closeCallback}/>
                <CardGetStarted active={active} href={'/comparison'} img={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Compare+Colleges.svg'} text={'Compare Colleges'} sub_title={''} imgClass={'compare_colleges'} sub_title_class={''} closeCallback={closeCallback}/>
                <CardGetStarted active={active} href={'/college-fair-events'} img={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/CollegeFairs.svg'} text={'College Fairs'} sub_title={'last viewed on 1/24/2019'} imgClass={'college_fairs'} sub_title_class={''} closeCallback={closeCallback}/>
                <CardGetStarted active={active} href={'/download-app'} img={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/DownloadApp.svg'} text={'Download App'} sub_title={''} imgClass={'down_app'} sub_title_class={''} closeCallback={closeCallback}/>
                {/* <Footer /> */}
            </ul>
        )
    }
}
function Header(props){
    let { handleRenderComponent } =props;
    return(
        <li className="research_head row" onClick={() => handleRenderComponent('iWantTo')}>
            <div className="large-2 medium-2 small-3 columns sliding_menu_back_btn_parent">
                <i className="fa fa-angle-left left_angle"></i>
                <div className="sliding_menu_back_btn cursor">back</div>
            </div>
            <div className="larger-10 medium-10 small-9 columns cursor">
                Research Colleges & Universities
            </div>
        </li>
    )
}

class CardGetStarted extends Component{
    constructor(props){
        super(props);
        this.state={checkMark: false}
        this.toggleCheckMark = this.toggleCheckMark.bind(this);
    }
    toggleCheckMark(){
        this.setState({checkMark: !this.state.checkMark});
    }
    render(){
        let { img, text, sub_title, imgClass, sub_title_class, active, closeCallback } = this.props;
        return(
            <li onClick={closeCallback}>
                <Link to={!!this.props.href ? this.props.href : ''} className="list_item_research row" onClick={this.toggleCheckMark}>
                    {/*<div className="columns checkMark_parent" onClick={this.toggleCheckMark}>
                        <img src={this.state.checkMark ? './social/images/rightBar/Checkmark-checked@3x.png' : './social/images/rightBar/Checkmark-unchecked@3x.png' } onClick={this.toggleCheckMark} alt=""/>
                    </div>*/}
                    <div className="columns img_parent">
                        <img className={imgClass} src={img} />
                    </div>
                    <div className="text columns">
                        <div className={!!active && active === this.props.href ? "title active" : "title" }>
                            {text}
                        </div>
                        <div className={"sub_title " + sub_title_class}>{sub_title}</div>
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
                Consult with a college financial advisor
            </div>
        </li>
    )
}
export default ResearchColleges;
