import React, { Component } from 'react'
import PublicProfile from './public_profile'
import CollegeApplicaion from './college_application'
import { connect } from 'react-redux'

class GetAccepted extends Component{
    constructor(props) {
        super(props)
    }
    render(){
        let { handleRenderComponent } = this.props;
        return(
            <span>
                {this.props.user.signed_in == 1 ? (
                    <ul className="rightbar-list">
                        <div className="get_accepted">
                            <Header handleRenderComponent={handleRenderComponent}/>
                            <CollegeApplicaion type={'US'}/>
                            {/* <Footer /> */}
                        </div>
                    </ul>
                ) : (
                    <div className="cca-preview">
                        <div className="right-circle"><img className="img-circle" src="/images/frontpage/caa-circle.png"/></div>
                        <div className="right-text"><span className="desc-text">Fill out our College Application Assessment to see what Plexuss recommends for you</span></div>
                        <div className="right-login"><a href="/signup?utm_source=SEO&utm_medium=frontPage" className="btn-login">Login or Signup</a></div>
                    </div>
                )}
            </span>
        )
    }
}
function Header(props){
    let { handleRenderComponent } = props;
    return(
        <li className="header row" onClick={() => handleRenderComponent('iWantTo')}>
            <div className="large-2 medium-2 small-3 columns sliding_menu_back_btn_parent">
                <i className="fa fa-angle-left left_angle"></i>
                <div className="sliding_menu_back_btn cursor">back</div>
            </div>
            <div className="larger-10 medium-10 small-9 columns cursor">
                College Application Assessment
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
        <li className="footer_parent">
            <div className="footer">
                Consult with a college financial advisor
            </div>
        </li>
    )
}

const mapStateToProps = (state) =>{
    return{
        user: state.user.data,
    }
}

const mapDispatchToProps = (dispatch) => {
    return {
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(GetAccepted);