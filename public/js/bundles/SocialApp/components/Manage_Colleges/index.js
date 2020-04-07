import React, {Component} from 'react'
import {Route} from 'react-router'
import { connect } from 'react-redux'
import { slide as Menu } from 'react-burger-menu'
import SideNav, { Toggle, Nav, NavItem, NavIcon, NavText } from '@trendmicro/react-sidenav';
import { withRouter } from 'react-router-dom';
import ReactTooltip from 'react-tooltip'
import { collegePage } from './../../actions/headerTab'

import './styles.scss'
import './sidebar.scss'
import Scholarships from './scholarships'
import Applications from './application'
import Colleges from './colleges'
import Recommended from './recommended'
import Recruit from './recruit'
import Views from './views'
import Trash from './trash'
import { Helmet } from 'react-helmet';

class Manage_Colleges extends Component {
  constructor(props){
    super(props);
    const urlArray = this.props.location.pathname.split('/');
    const selectedTab = urlArray[urlArray.length - 1] === 'manage-colleges' ? 'favorites' : urlArray[urlArray.length - 1];
    this.state = {
      selected: selectedTab,
      sideNavExpanded: false,
    }

    this.handleSideNavToggle = this.handleSideNavToggle.bind(this);
  }

  handleOnSelect = (value) => {
    const to = value === 'favorites' ? '/social/manage-colleges/' : '/social/manage-colleges/' + value;
    if (this.props.location.pathname !== to) {
      this.props.history.push(to);
    }
    this.setState({selected: value});
    this.props.history.push('/social/manage-colleges/'+value);
  }

  componentDidUpdate(prevProps) {
    if (this.props.location !== prevProps.location) {
      var array = this.props.location.pathname.split('/');
      if (array.length < 4)
        this.setState({selected: 'favorites'});
      else
        this.setState({selected:array[3]});
    }
  }

  handleSideNavToggle() {
    this.setState(prevState => ({ sideNavExpanded: !prevState.sideNavExpanded }))
  }

  componentWillMount(){
    this.props.collegePage();
  }
  render() {
    const { selected, sideNavExpanded } = this.state;

    return (
      <div>
        <Helmet>
          <title>College List | College Recruiting Academic Network | Plexuss.com</title>
          <meta name="description" content="Looking for a comlete college list? Plexuss College Portal empowers students to manage communication with colleges.  Select colleges that you want to engage with, view recommendations and see which colleges have viewed your profile." />
          <meta name="keywords" content="College list" />
        </Helmet>
          <SideNav onSelect={(selected) => { this.handleOnSelect(selected) }}>
              <SideNav.Toggle onClick={this.handleSideNavToggle} />
              <SideNav.Nav defaultSelected={this.state.selectedTab}>
              <NavItem eventKey="favorites" active={selected === 'favorites'} data-tip={sideNavExpanded ? '' : 'My Favorites'}>
                      <NavIcon>
                          <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Favorites-active.svg" />
                      </NavIcon>
                      <NavText>
                          My Favorites
                      </NavText>
                  </NavItem>
                  <NavItem eventKey="rec-by-plex" active={selected === 'rec-by-plex'} data-tip={sideNavExpanded ? '' : 'My Recommendations'}>
                      <NavIcon>
                          <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Recommend-active.svg" />
                      </NavIcon>
                      <NavText>
                          My Recommendations
                      </NavText>
                  </NavItem>
                  <NavItem eventKey="colleges-rec" active={selected === 'colleges-rec'} data-tip={sideNavExpanded ? '' : 'Colleges Recruiting You'}>
                      <NavIcon>
                          <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Recruit-active.svg" />
                      </NavIcon>
                      <NavText>
                          Colleges recruiting you
                      </NavText>
                  </NavItem>
                  <NavItem eventKey="colleges-view" active={selected === 'colleges-view'} data-tip={sideNavExpanded ? '' : 'Colleges Viewing You'}>
                      <NavIcon>
                          <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Viewing-active.svg" />
                      </NavIcon>
                      <NavText>
                          Colleges viewing you
                      </NavText>
                  </NavItem>
                  <NavItem eventKey="application" active={selected === 'application'} data-tip={sideNavExpanded ? '' : 'My Applications'}>
                      <NavIcon>
                          <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Applications-active.svg" />
                      </NavIcon>
                      <NavText>
                          My Applications
                      </NavText>
                  </NavItem>
                  <NavItem eventKey="scholarship" active={selected === 'scholarship'} data-tip={sideNavExpanded ? '' : 'My Scholarships'}>
                      <NavIcon>
                          <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Scholarships-active.svg" />
                      </NavIcon>
                      <NavText>
                        My Scholarships
                      </NavText>
                  </NavItem>
                  <ReactTooltip place="right" type="dark" effect="float"/>
                  <NavItem eventKey="trash" active={selected === 'trash'} data-tip={sideNavExpanded ? '' : 'Trash'}>
                      <NavIcon>
                          <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Trash-active.svg" />
                      </NavIcon>
                      <NavText>
                          Trash
                      </NavText>
                  </NavItem>
              </SideNav.Nav>
          </SideNav>
          { selected == 'application' && <Applications /> }
          { selected == 'scholarship' && <Scholarships /> }
          { selected == 'favorites' && <Colleges /> }
          { selected == 'rec-by-plex' && <Recommended /> }
          { selected == 'colleges-rec' && <Recruit /> }
          { selected == 'colleges-view' && <Views /> }
          { selected == 'trash' && <Trash /> }
      </div>
    );
  }
}

function mapStateToProps(state) {
  return {

  }
}
const mapDispatchtoProps = (dispatch) => {
  return {
    collegePage: () => {dispatch(collegePage())},
  }
}
export default  connect(mapStateToProps, mapDispatchtoProps)(withRouter(Manage_Colleges));
