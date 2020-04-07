import React, { Component } from 'react'
import { connect } from 'react-redux';
import './styles.scss'
import { Link } from 'react-router-dom';
import { getNetworkingData } from './../../../api/post'

class Footer extends Component{
    constructor(props){
        super(props);
        this.state={
            activeComponent: '',
            showConversation: false
        }
        this.handleActiveSection = this.handleActiveSection.bind(this)
        this.scrollUp = this.scrollUp.bind(this);
    }
    componentDidMount(){
        this.handleActiveSection()
        getNetworkingData()
    }
    handleActiveSection(){
        let path = window.location.pathname.split('/');
        if (path[1] === 'home'){
            this.setState({activeComponent: 'home'})
        }else {
            switch (path[2]){
                case 'mbl-messages': this.setState({activeComponent: 'messages'}); break;
                case 'networking': this.setState({activeComponent: 'networking'}); break;
                case 'mbl-manage-colleges': this.setState({activeComponent: 'manageColleges'}); break;
                case 'manage-colleges': this.setState({activeComponent: 'manageColleges'}); break;
                case 'profile': this.setState({activeComponent: 'Me'}); break;
                default : this.setState({activeComponent: 'no-match'});
            }
        }
    }
    componentWillReceiveProps(nextProps) {
        if(nextProps.renderManageCollegesIndex) {
            this.setState({ activeComponent: 'manageColleges' })
            window.location.href = '/social/mbl-manage-colleges';
        }
        this.handleActiveSection()
    }

    scrollUp(){
      if (window.location.pathname == "/home")
        {
          document.body.scrollTop = document.documentElement.scrollTop = 0;
        }
      }
    render(){
        let { user, msgs_count } = this.props;
        let userImg = !!user && user.profile_img_loc ?
          'url("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'+user.profile_img_loc+'")'
          :
          (!!user && user.fname) ? 'url(/social/images/Avatar_Letters/'+user.fname.charAt(0).toUpperCase()+'.svg)'
          :
          'url(/social/images/Avatar_Letters/P.svg)'

        return(
            <span>
                {this.props.user.signed_in == 1 ? (
                    <div className={"mbl_footer " + (this.state.showConversation ? 'vanish' : '') }>
                        <FooterImg img={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/home-inactive-icon.svg'} activeImg={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/home-active-icon.svg'} subCom={'home'} activeComponent={this.state.activeComponent} redirect={'/home'} homeClick={this.scrollUp}/>
                        <MsgFooterImg img={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/message-inactive-icon.svg'} activeImg={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/message-active-icon.svg'} subCom={'messages'} activeComponent={this.state.activeComponent} redirect={'/social/mbl-messages'} msgs_count={msgs_count} />
                        <FooterImg img={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/networks-inactive-icon.svg'} activeImg={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/network-active-icon.svg'} subCom={'networking'} activeComponent={this.state.activeComponent} redirect={'/social/mbl-networking'} />
                        {this.props.requests && this.props.requests.length > 0 &&
                            <div className="footer-connection-number">{this.props.requests.length}</div>
                        }
                        <FooterImg img={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/college-inactive-icon.svg'} activeImg={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/college-active-icon.svg'} subCom={'manageColleges'} activeComponent={this.state.activeComponent} redirect={'/social/mbl-manage-colleges'}/>
                        <FooterImg img={userImg} activeImg={userImg} subCom={'Me'} activeComponent={this.state.activeComponent} redirect={'/social/profile/'+(!!user && user.user_id)} />
                    </div>
                ) : null}
            </span>
        )
    }
}
class FooterImg extends Component{
    render(){
        let { img, activeImg, subCom, activeComponent, redirect, homeClick } = this.props;
        let scroll_up_on_home = (window.location.pathname == redirect) && (redirect == "/home")

        return(
              <Link className="img_parent" to={scroll_up_on_home ? "#" : redirect} >
                  {subCom === 'Me' ?
                      <div className="footer-user-img" style={{backgroundImage:(img || activeImg)}} className={(activeComponent === "Me" && activeComponent === subCom) ? 'footer-user-img green_border': 'footer-user-img'} />
                      :
                      <img src={(subCom === activeComponent)? activeImg : img} alt="" onClick={this.props.homeClick}/>

                  }
              </Link>
        )
    }
}
class MsgFooterImg extends Component{
    render(){
        let { img, activeImg, subCom, activeComponent, redirect, msgs_count } = this.props;
        return(
            <Link className="img_parent" to={redirect}>
                {subCom === 'Me' ?
                    <div className="footer-user-img" style={{backgroundImage:(img || activeImg)}} className={(activeComponent === "Me" && activeComponent === subCom) ? 'footer-user-img green_border': 'footer-user-img'} />
                    :
                    <img src={(subCom === activeComponent)? activeImg : img} alt="" />
                }
                {
                    msgs_count > 0 &&
                        <div className="msg_count">{msgs_count}</div>
                }
            </Link>
        )
    }
}


const mapStateToProps = state => {
    return {
        renderManageCollegesIndex: state.colleges.renderManageCollegesIndex,
        user: state.user.data,
        requests: state.user.networkingDate.requests,
        msgs_count: state.messages && state.messages.unreadThread,
    }
}

export default connect(mapStateToProps, null)(Footer)
