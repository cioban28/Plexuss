// /Networking/index.js

import React from 'react'
import Connections from './connections'
import Requests from './request'
import Suggestions from './suggestions'
import InviteIndex from './inviteIndex'
import './styles.scss'
import { getNetworkingData } from './../../api/post'
import { connect } from 'react-redux'
import { networkingPage } from './../../actions/headerTab'
import { Link } from 'react-router-dom'

class Networking extends React.Component {
  constructor(props){
    super(props);
    const splittedPath = window.location.pathname.split('/');
    this.state={
      renderComponent: splittedPath[splittedPath.length-1] || 'connection',
      importContactsCount: 0,
      messageBox: false,
      spinnerFlag: true,
    }
    this.handleRenderComponent = this.handleRenderComponent.bind(this);
    this._renderSubComp = this._renderSubComp.bind(this);
    this.handleActiveSection = this.handleActiveSection.bind(this)
    this.checkScreenTop = this.checkScreenTop.bind(this)
  }
  componentWillMount(){
    getNetworkingData()
    .then(()=>{
      this.setState({
        spinnerFlag: false,
      })
    })
    this.props.networkingPage();
    let arr=this.props.location.pathname.split("/");
    if((arr.length === 4 || arr.length === 5 )&& arr[3] !== "" && (arr[3] === "connection" || arr[3] === "importContacts" || arr[3] === "suggestion" || arr[3] === "requests")){
      this.setState({renderComponent: arr[3]});
      if(arr.length === 5){
        this.setState({
          messageBox: true,
          importContactsCount: arr[4],
        })
      }
    }
    this.handleActiveSection()
    window.addEventListener('scroll', this.checkScreenTop, { passive: true });
  }


  componentWillReceiveProps(nextProps) {
    this.handleActiveSection()
  }

  handleActiveSection(){
    let path = window.location.pathname.split('/');

    switch (path[3]){
        case 'connection': this.setState({renderComponent: 'connection'}); break;
        case 'importContacts': this.setState({renderComponent: 'importContacts'}); break;
        case 'suggestion': this.setState({renderComponent: 'suggestion'}); break;
        case 'requests': this.setState({renderComponent: 'requests'}); break;
        default : this.setState({renderComponent: 'no-match'});
    }
  }

  componentWillUnmount() {
    window.removeEventListener('scroll', this.checkScreenTop);
  }

  checkScreenTop(){
    var elem = document.getElementById("network-sticky-header")

    if (window.scrollY >= elem.clientHeight) {
      elem.classList.add("network-sticky-header");
      // #header

      if(window.innerWidth <= 760) {
        var header = document.getElementById("header")
        header.style.position = "relative";
        var networkHeader = document.getElementsByClassName("mobile-network-header")[0]
        networkHeader.classList.add("mobile-network-header-sticky")
      }
      else{
        var header = document.getElementById("header")
        header.style.position = "fixed";
        var networkHeader = document.getElementsByClassName("mobile-network-header")[0]
        networkHeader.classList.remove("mobile-network-header-sticky")
      }
    }
    else {
      elem.classList.remove("network-sticky-header");
      var header = document.getElementById("header")
      header.style.position = "fixed";
      var networkHeader = document.getElementsByClassName("mobile-network-header")[0]
      networkHeader.classList.remove("mobile-network-header-sticky")
    }
  }

  handleRenderComponent(component){
    this.setState({renderComponent: component})
  }
  _renderSubComp(){
    switch(this.state.renderComponent){
      case 'connection': return <Connections handleRenderComponent={this.handleRenderComponent} friends={this.props.networkingDate.friends} user={this.props.user} spinnerFlag={this.state.spinnerFlag}/>
      case 'importContacts': return <InviteIndex messageBox={this.state.messageBox} importContactsCount={this.state.importContactsCount}/>
      case 'suggestion': return <Suggestions handleRenderComponent={this.handleRenderComponent} user={this.props.user}/>
      case 'requests': return <Requests handleRenderComponent={this.handleRenderComponent} requests={this.props.networkingDate.requests} user={this.props.user} spinnerFlag={this.state.spinnerFlag}/>
    }
  }
  render(){
    const { networkingDate } = this.props;
    return (
      <div>
        <div className="networkContainer">
          <div className="tabs">
            <ol className="tab-list">
                <li className={"tab-list-item "+ (this.state.renderComponent === "connection" ? "tab-list-active" : "")} onClick={()=>this.handleRenderComponent('connection')}>{'Connections ('+networkingDate.friends.length+')'}</li>
                <li className={"tab-list-item "+ (this.state.renderComponent === "importContacts" ? "tab-list-active" : "")} onClick={()=>this.handleRenderComponent('importContacts')}>Import Contacts</li>
                <li className={"tab-list-item "+ (this.state.renderComponent === "suggestion" ? "tab-list-active" : "")} onClick={()=>this.handleRenderComponent('suggestion')}>Suggestions</li>
                <li className={"tab-list-item "+ (this.state.renderComponent === "requests" ? "tab-list-active" : "")} onClick={()=>this.handleRenderComponent('requests')}>{'Requests ('+networkingDate.requests.length+')'}</li>
            </ol>
          </div>

          <div id="network-sticky-header" className="mobile-network-menu" onScroll={this.checkScreenTop}>
            <ul className="menu-list">
              <li className="list-item">
                <Link to={"/social/networking/connection"}>
                  <div className={this.state.renderComponent === "connection" ? "active-network-tab icon-container" : "icon-container"}>
                    <img className="icon-style" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Connections.svg" style={{width: '28px'}} />
                  </div>
                </Link>
              </li>
              <li className="list-item">
                <Link to={"/social/networking/importContacts"}>
                  <div className={this.state.renderComponent === "importContacts" ? "active-network-tab icon-container" : "icon-container"}>
                    <img className="icon-style" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Import+Contacts.svg" style={{width: '28px'}} />
                  </div>
                </Link>
              </li>
              <li className="list-item">
                <Link to={"/social/networking/suggestion"}>
                  <div className={this.state.renderComponent === "suggestion" ? "active-network-tab icon-container" : "icon-container"}>
                    <img className="icon-style" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Suggestions.svg" />
                  </div>
                </Link>
              </li>
              <li className="list-item">
                <Link to={"/social/networking/requests"}>
                  <div className={this.state.renderComponent === "requests" ? "active-network-tab icon-container" : "icon-container"}>
                    <div className="contacts-count"><span>{networkingDate.requests.length > 0 && networkingDate.requests.length }</span></div>
                    <img className="icon-style" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Requests.svg" />
                  </div>
                </Link>
              </li>
            </ul>
          </div>

          <div className="tabs">
            <div className="network-tabs-content-container">
              {this._renderSubComp()}
            </div>
          </div>
        </div>
      </div>
    );
  }
}
const mapStateToProps = (state) =>{
  return{
    user: state.user.data,
    networkingDate: state.user.networkingDate,
  }
}
const mapDispatchtoProps = (dispatch) => {
  return {
    networkingPage: () => {dispatch(networkingPage())},
  }
}
export default connect(mapStateToProps, mapDispatchtoProps)(Networking);
