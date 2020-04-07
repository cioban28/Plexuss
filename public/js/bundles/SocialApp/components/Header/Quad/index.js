import React, { Component } from 'react'
import { Link } from 'react-router-dom'
import './styles.scss'

class Quad extends Component{
    constructor(props){
        super(props);
        this.state={
        }
    }

    render(){
        let path = window.location.pathname;
        let { closeCallback } = this.props;
        return(
            <section className="rightbar quad_parent">
                <ul className="rightbar-list">
                    <div className="quad">
                        <Header />
                        <Card active={path} href={'/news'} imgSrc={'/social/images/rightBar/news.svg'} text={'News'} imgClass={'news'} closeCallback={closeCallback}/>
                        <Card active={path} href={'/college-essays'} imgSrc={'/social/images/rightBar/noun_1053015@2x.png'} text={'Student Essays'} imgClass={'essay'} closeCallback={closeCallback}/>
                        {/* <Card active={this.state.activeSection} imgSrc={'/social/images/rightBar/noun_celebrity_1815067_000000@2x.png'} text={'Celebrity Trivia'} imgClass={'trivia'}/> */}
                        <Card active={path} href={'/ranking/categories'} imgSrc={'/social/images/rightBar/noun_baloon_1641235_000000@2x.png'} text={'Interesting College Rankings'} imgClass={'fun'} closeCallback={closeCallback}/>
                        <Card active={path} href={'/international-resources'} imgSrc={'/social/images/rightBar/noun_international_1022897_000000@2x.png'} text={'International Student Resources'} imgClass={'IntlStnd'} closeCallback={closeCallback}/>
                        {
                            // <span className="for_mbl_upgrade_btn">
                            //     <UpgradeButton/>
                            // </span>
                        }
                    </div>
                </ul>
            </section>
        )
    }
}
function Header(){
    return(
        <li>
            <div className="header">
                Quad
            </div>
        </li>
    )
}
function UpgradeButton(){
    return(
        <li className="upgrade_btn_parent">
            <Link to="" className="upgrade_btn">
                <img src="/images/upgrade_logo.png" alt=""/>
                <div>Upgrade to Premium</div>
            </Link>
        </li>
    )
}
function Card(props){
    let { imgSrc, text, imgClass, href, active, closeCallback } = props;
    return(
        <li onClick={closeCallback}>
            <Link to={!!href ? href : ''} className="card row">
                <div className="img_parent large-3 medium-3 small-3 columns">
                    <img src={imgSrc} alt="" className={imgClass}/>
                </div>
                <div className={"text large-9 medium-9 small-9 columns " + (active === href && "active")}>
                    {text}
                </div>
            </Link>
        </li>
    )
}
export default Quad;