import React, { Component } from 'react'
import CircularProgressbar from 'react-circular-progressbar'
import './progressbar.scss'

class PublicProfile extends Component{
    render(){
        return(
            <div className="public_profile">
                <SubHeader percentage={50}/>
                <Card1 text={'Upload photo'}/>
                <Card1 text={'Add your claim to fame!'}/>
                <Card1 text={'Add Publications'}/>
            </div>
        )
    }
}

class SubHeader extends Component{
    render(){
        let { percentage } = this.props;
        return(
            <li className="sub_header sub_header_bottom_line">
                <div className="progress_bar">
                    <CircularProgressbar
                        percentage={percentage}
                        text={`${percentage}%`}
                        />
                </div>
                <div className="text">
                    <div className="title">Public Profile</div>
                    <div className="sub_title ">50% complete</div>
                </div>
            </li>
        )
    }
}

class Card1 extends Component{
    render(){
        let { text } = this.props;
        return(
            <li className="card1_li">
                <div className="checkMark_parent">
                    <i className="fa fa-check checkMark"></i>
                </div>
                <div className="text">{text}</div>
            </li>
        )
    }
}
export default PublicProfile;