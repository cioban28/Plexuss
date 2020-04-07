import React, { Component } from 'react';
import { signOut } from './../../../api/post'
import { Link } from 'react-router-dom'
import { connect } from 'react-redux';
import CircularProgressbar from 'react-circular-progressbar';
import './styles.scss';

const meMenuItems = [
  {title:'Profile', subtitle: 'View Public Profile', route: '/social/profile/'},
  {title:'College Application', subtitle: '% Complete', route: '/social/one-app'},
  {title:'Your Documents', subtitle: 'Documents', route: '/social/documents', image: '/social/images/Icons/me_documents.svg'},
]

class Mobile_Menu extends Component{

    constructor(props){
        super(props)
        this.state = {
            showMeMenu: false,
            activeLink: '',
        }
        this.handleOutsideMeMenu = this.handleOutsideMeMenu.bind(this)
        this.handleActiveSection = this.handleActiveSection.bind(this)

        this.meMenuContainer
        this.meMenuBtn
    }

    componentDidMount() {
        if (this.props.user.signed_in == 1)
          document.addEventListener('click', this.handleOutsideMeMenu, false)
        this.handleActiveSection()
    }

    componentWillReceiveProps(nextProps) {
        this.handleActiveSection()
    }

    componentWillUnmount() {
      if (this.props.user.signed_in == 1)
        document.removeEventListener('click', this.handleOutsideMeMenu, false);
    }

    handleOutsideMeMenu(e) {
        if (this.meMenuContainer.contains(e.target) || this.meMenuBtn.contains(e.target)) {
          return;
        }
        if(this.state.showMeMenu === true){
          this.setState({showMeMenu: false})
        }
    }

    handleActiveSection(){
        let path = window.location.pathname.split('/');
        const { user } = this.props;
        if (user.signed_in == 1) {
          if (path[1] === 'home'){
              this.setState({activeLink: 'home'})
          }else {
              switch (path[2]){
                  case 'mbl-networking': this.setState({activeLink: 'my-network'}); break;
                  case 'mbl-manage-colleges': this.setState({activeLink: 'my-colleges'}); break;
                  case 'profile': this.setState({activeLink: 'profile'}); break;
                  case 'settings': this.setState({activeLink: 'settings'}); break;
                  default : this.setState({activeLink: 'no-match'});
              }
          }
        } else {
          switch(path[1]) {
              case '': this.setState({activeLink: 'home'}); break;
              case 'college': this.setState({activeLink: 'college'}); break;
              case 'majors': this.setState({activeLink: 'majors'}); break;
              case 'scholarships': this.setState({activeLink: 'scholarships'}); break;
              case 'ranking': this.setState({activeLink: 'ranking'}); break;
              case 'comparison': this.setState({activeLink: 'compare'}); break;
              default : this.setState({activeLink: 'no-match'});
          }
        }
    }

    render(){
        let { user, handleClick } = this.props;
        let { activeLink } = this.state;
        let imgStyles = {
            backgroundImage: (user && user.profile_img_loc) ? 'url("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'+user.profile_img_loc+'")' : (user && user.fname) ? 'url(/social/images/Avatar_Letters/'+user.fname.charAt(0).toUpperCase()+'.svg)' : "url(/social/images/Avatar_Letters/P.svg)"
        }
        let userImg = user && user.profile_img_loc ?
          'url("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'+user.profile_img_loc+'")'
          :
          user && user.fname ? 'url(/social/images/Avatar_Letters/'+user.fname.charAt(0).toUpperCase()+'.svg)'
          :
          'url(/social/images/Avatar_Letters/P.svg)'

        let profileImg = {
          backgroundImage: userImg,
          backgroundSize: 'cover',
          backgroundPosition: 'center',
        }
        return(
            <div className="mobile_burger_menu">
              {this.props.user.signed_in == 1 ? (
              <div>
                <div className="menu-header">
                    <div className="user-profile-img-container">
                        <div className="user-profile-img" style={imgStyles}/>
                    </div>
                    <div ref={(ref) => {this.meMenuBtn = ref;}} className="user-name" onClick={() => {this.setState({showMeMenu: !this.state.showMeMenu})}}>
                        {user.fname+' '+user.lname+' '}
                        <img className={this.state.showMeMenu ? "me-arrow rotate" : "me-arrow"} src="/social/images/Icons/me_arrow.svg"/>
                    </div>
                </div>
                <span ref={(ref) => {this.meMenuContainer = ref;}}><MeMenu user={user} visible={this.state.showMeMenu} handleClick={handleClick} profileImg={profileImg}/></span>
                <ul className="menu-list">
                    <Link to={"/home"} onClick={handleClick}>
                        <li className={activeLink === 'home' ? "menu-item active-link-color" : "menu-item"}>
                            <img className="item-img" src={activeLink === 'home' ? "/social/images/Icons/tab-home-active.svg" : "/social/images/Footer_Images/menu_home.png"} />
                            Home
                        </li>
                    </Link>
                    <Link to={"/social/mbl-networking"} onClick={handleClick}>
                        <li className={activeLink === 'my-network' ? "menu-item active-link-color" : "menu-item"}>
                            <img className="item-img" src={activeLink === 'my-network' ? "/social/images/Icons/tab-network-active.svg" : "/social/images/Footer_Images/My_network.svg"} />
                            My Network
                        </li>
                    </Link>
                    <Link to={"/social/mbl-manage-colleges"} onClick={handleClick}>
                        <li className={activeLink === 'my-colleges' ? "menu-item active-link-color" : "menu-item"}>
                            <img className="item-img" src={activeLink === 'my-colleges' ? "/social/images/Icons/tab-colleges-active.svg" : "/social/images/Footer_Images/My_colleges.svg"} />
                            My Colleges
                        </li>
                    </Link>
                    <Link to={"/social/profile/"+(!!user && user.user_id)} onClick={handleClick}>
                        <li className={activeLink === 'profile' ? "menu-item active-link-color" : "menu-item"}>
                            <div className={activeLink === 'profile' ? "item-img me-image me-active-border" : "item-img me-image"} style={imgStyles}/>
                            Me
                        </li>
                    </Link>
                    <Link to={"/social/settings"} onClick={handleClick}>
                        <li className={activeLink === 'settings' ? "menu-item active-link-color" : "menu-item"}>
                            <img className="item-img" src={activeLink === 'settings' ? "/social/images/Footer_Images/active_settings.png" : "/social/images/Footer_Images/menu_settings.png"} />
                            Settings
                        </li>
                    </Link>
                    <Link to="">
                        <li id="sign_out" className="menu-item" onClick={() => signOut()}>
                            <img className="item-img" src="/social/images/Footer_Images/sign_out.png" />
                            Sign Out
                        </li>
                    </Link>
                </ul>
                <div className="menu-footer">
                    <img src="/social/images/plexuss-logo.svg" className="plexuss-logo" />
                </div>
              </div>
              ) : (
              <div>
                <div className="signout-menu-header">
                    <img src="/social/images/plexuss-logo.svg" className="plexuss-logo" />
                </div>
                <ul className="menu-list">
                  <Link to={"/"} onClick={handleClick}>
                      <li className={activeLink === 'home' ? "menu-item active-link-color" : "menu-item"}>
                          <img className="item-img" src={activeLink === 'home' ? "/social/images/Icons/tab-home-active.svg" : "/social/images/Footer_Images/menu_home.png"} />
                          Home
                      </li>
                  </Link>
                  <Link to={"/college"} onClick={handleClick}>
                      <li className={activeLink === 'college' ? "menu-item active-link-color" : "menu-item"}>
                          <img className="item-img" src={activeLink === 'college' ? "/social/images/Icons/tab-find-active.png" : "/social/images/Icons/menu-find.png"} />
                          Find Colleges
                      </li>
                  </Link>
                  <Link to={"/college-majors"} onClick={handleClick}>
                      <li className={activeLink === 'majors' ? "menu-item active-link-color" : "menu-item"}>
                          <img className="item-img" src={activeLink === 'majors' ? "/social/images/Icons/tab-majors-active.png" : "/social/images/Icons/menu-major.png"} />
                          Majors
                      </li>
                  </Link>
                  <Link to={"/scholarships"} onClick={handleClick}>
                      <li className={activeLink === 'scholarships' ? "menu-item active-link-color" : "menu-item"}>
                          <img className="item-img" src={activeLink === 'scholarships' ? "/social/images/Icons/tab-scholarships-active.png" : "/social/images/Icons/menu-scholarships.png"} />
                          Scholarships
                      </li>
                  </Link>
                  <Link to={"/ranking"} onClick={handleClick}>
                      <li className={activeLink === 'ranking' ? "menu-item active-link-color" : "menu-item"}>
                          <img className="item-img" src={activeLink === 'ranking' ? "/social/images/Icons/tab-ranking-active.png" : "/social/images/Icons/menu-ranking.png"} />
                          Ranking
                      </li>
                  </Link>
                  <Link to={"/comparison"} onClick={handleClick}>
                      <li className={activeLink === 'compare' ? "menu-item active-link-color" : "menu-item"}>
                          <img className="item-img" src={activeLink === 'compare' ? "/social/images/Icons/tab-compare-active.png" : "/social/images/Icons/menu-compare.png"} />
                          Compare Colleges
                      </li>
                  </Link>
                  <li className="menu-item sign-group">
                    <div className="signup-mobile">
                      <a href="/signin">
                        <div className="homepage-signup-button">Login</div>
                      </a>
                    </div>
                    <div className="signup-mobile">
                        <a href="/signup?utm_source=SEO&utm_medium=frontPage">
                          <div className="mobile-signup-button">Sign up</div>
                        </a>
                    </div>
                  </li>

                  <li className="bottom-menu">
                    <span className="bottom-txt">© 2019 PLEXUSS INC. ALL RIGHTS RESERVED</span>
                    <span className="policy-link">
                      <Link to={'/terms-of-service'} className="mobile-policy-txt">TERMS OF SERVICE</Link> | <Link to={'/privacy-policy'} className="mobile-policy-txt">PRIVACY POLICY</Link>
                    </span>
                    <span className="bottom-link">
                      <Link to={'/california-policy'} className="mobile-policy-txt">CALIFORNIA PRIVACY POLICY</Link> | <Link to={'/legal-notice'} className="mobile-policy-txt">LEGAL NOTICES</Link>
                    </span>
                  </li>
                </ul>
              </div>
              )}
            </div>
        );
    }
}

class MeMenu extends Component {
  constructor(props) {
    super(props);
    this.state = {}
  }
  render() {
    const { user, visible, profileImg, handleClick } = this.props;
    const percentage = 50;

    return (
      <div className={visible ? "me-menu-container" : "me-menu-container collapsed" }>
        <ul>
          { meMenuItems.map((item, i) =>
            <li key={i} className="me-menu-li" onClick={handleClick}>
              <Link className="me-row row" to={item.title === 'Profile' ? item.route+user.user_id : item.route}>
                <div className="large-4 medium-4 small-3 columns">
                  { item.title === 'Profile' ? (<div className="prf-img me-prf" style={profileImg}></div>)
                  :
                    item.title === 'College Application' ? (<div className="progress_bar"><CircularProgressbar percentage={!!user.one_app_percent ? (user.one_app_percent > 100) ? 100 : user.one_app_percent : 0} text={`${!!user.one_app_percent ? (user.one_app_percent > 100) ? 100 : user.one_app_percent : 0}%`} /></div>)
                  :
                    (<img src={item.image} alt=""/>)
                  }
                </div>
                <div className="large-8 medium-8 small-9 columns">
                  {item.title === 'Profile' ? (<div className="me-title">{user.fname + ' ' + user.lname}</div>) : (<div className="me-title">{item.title}</div>)}
                  <div className="me-subtitle">{item.title === 'College Application' ? (!!user.one_app_percent ? (user.one_app_percent > 100) ? 100 : user.one_app_percent : 0) + item.subtitle : item.subtitle}</div>
                </div>
              </Link>
            </li>
          )}
        </ul>
      </div>
    );
  }
}

const mapStateToProps = state => {
    return {
        user: state.user.data,
    }
}

export default connect(mapStateToProps, null)(Mobile_Menu);
