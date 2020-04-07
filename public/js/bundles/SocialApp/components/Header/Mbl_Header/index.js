import React ,{ Component } from 'react';
import { connect } from 'react-redux';
import { Link } from 'react-router-dom'
import Mobile_Menu from './Mobile_Menu';
import HamburgerMenu from 'react-hamburger-menu';
import Notifications from './../Notifications/index'
import SearchResults from '../SearchResults/index';
import './styles.scss';
import isEqual from 'lodash/isEqual';

import CheeseburgerMenu from "cheeseburger-menu"
import SlidingMenu from './sliding_menu'
import { getTopbarSearchResults } from '../../../api/search';
import { cancelPreviousRequest, resetSearchResults } from '../../../actions/search';
import { setHeaderState } from '../../../actions/posts';
import { toggleSlidingMenu } from '../../../actions/slidingMenu';

const _ = {
    isEqual: isEqual
}
class MobileHeader extends Component{

    constructor(props){
        super(props)
        this.state = {
            open: false,
            notification: false,
            menuOpen: false,
            searchTerm: '',
            renderSearch: false,
        }
        this.handleNotifications = this.handleNotifications.bind(this);
        this.openMenu = this.openMenu.bind(this);
        this.closeMenu = this.closeMenu.bind(this);
        this.toggleMenu = this.toggleMenu.bind(this);
        this.handleSearchChange = this.handleSearchChange.bind(this);
        this.handleSearchReset = this.handleSearchReset.bind(this);
        this.handleSearchUnmount = this.handleSearchUnmount.bind(this);
        this.handleClick = this.handleClick.bind(this)
        this.searchInputMbl;
        this.searchResultsContainerMbl;
    }

    componentDidMount() {
        if (this.props.user.signed_in == 1)
            document.addEventListener('click', this.handleSearchReset, false);
    }
    componentWillReceiveProps(nextProps) {
        if(!_.isEqual(this.props.topbarSearchResults, nextProps.topbarSearchResults) && !this.state.renderSearch) {
            this.setState({ renderSearch: true })
        }
    }
    componentWillUnmount() {
        if (this.props.user.signed_in == 1)
            document.removeEventListener('click', this.handleSearchReset, false);
    }

    handleClick() {
        this.setState({
            open: !this.state.open,
            notification: false,
            getStarted: false,
        });
        this.props.setHeaderState()
    }
    handleNotifications(){
        this.setState({
            notification: !this.state.notification,
            state: false,
            getStarted: false,
        })
        this.props.setHeaderState()
    }

    openMenu() {
        this.props.toggleSlidingMenu();
        this.props.setHeaderState()
    }

    closeMenu() {
        this.props.toggleSlidingMenu();
        if(this.state.notification === false){
                   this.props.setHeaderState()
        }
    }
    toggleMenu(){
        this.props.toggleSlidingMenu();
        if(this.state.notification === false){
                    this.props.setHeaderState()
        }
    }

    handleSearchChange(e) {
        if (this.state.menuOpen === false && this.state.notification === false){
                   this.props.setHeaderState()
        }
        const { getTopbarSearchResults, requestCancellationFn, cancelPreviousRequest, topbarSearchResults, resetSearchResults } = this.props;
        const searchTerm = e.target.value;

        topbarSearchResults.length && resetSearchResults();

        if(!searchTerm.trim().length && !(Object.entries(requestCancellationFn).length === 0 && requestCancellationFn.constructor === Object)) {
          cancelPreviousRequest();
        }

        this.setState({ searchTerm: searchTerm }, () => {
          searchTerm.trim().length && getTopbarSearchResults(searchTerm.split(' ').join('+'), requestCancellationFn);
        })
  }

    handleSearchReset(e) {
        if (!!this.state.renderSearch && (this.searchResultsContainerMbl.contains(e.target) || this.searchInputMbl.contains(e.target))) {
            return;
        }
        if(this.state.renderSearch){
            this.setState({renderSearch: false, searchTerm: ''});
            return;
        }
    }

    handleSearchUnmount() {
        this.setState({ renderSearch: false, searchTerm: '' });
    }

    render(){
        const { topbarSearchResults, resetSearchResults } = this.props;
        const { searchTerm, renderSearch } = this.state;

        return(
            <div id="mbl_top_header">
                <div className="row mobile_top_bar">
                    <div className="small-4 columns">
                        <section className="mobile-menu">
                            <div className="main-menu">
                                <HamburgerMenu
                                    isOpen={this.state.open}
                                    menuClicked={this.handleClick}
                                    width={22}
                                    height={18}
                                    strokeWidth={1}
                                    rotate={0}
                                    color='#fff'
                                    borderRadius={0}
                                    animationDuration={0.5}
                                />
                            </div>
                        </section>
                    </div>
                    <div className="small-4 columns">
                        {this.props.user.signed_in == 1 ? (
                            <span className="notification-mobile" >
                                <div className="bell-holder">
                                    <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Notifications-inactive.svg" onClick={() => this.handleNotifications()} />
                                    {
                                        this.props.notifications_count > 0 &&
                                        <em className="counter">{this.props.notifications_count}</em>
                                    }
                                </div>
                            </span>
                        ) : (
                            <Link className="moblie-logo" to={'/'}><img src="/social/images/plexuss-logo.svg" /></Link>
                        )}
                    </div>
                    <div className="small-4 columns">
                        <div className="news-dropdown">
                            <img src="/social/images/apps-SIC.svg" className="news-icons" onClick={() => this.toggleMenu()} />
                        </div>
                    </div>
                </div>
                <div className="small-12 columns">
                    <form className="search-form">
                        <input ref={(ref) => {this.searchInputMbl = ref;}} value={searchTerm} className={'input-contral ' + (searchTerm.length > 0 && topbarSearchResults.length > 0 && 'input-top-border-radius') } placeholder="Search Universites" onChange={this.handleSearchChange} onBlur={this.handleSearchReset} />
                        <a href="#" className="button postfix fa fa-search btn-search"></a>
                        {
                            renderSearch && searchTerm.trim().length > 0 && topbarSearchResults.length > 0 &&
                            <span ref={(ref) => {this.searchResultsContainerMbl = ref;}} id='search-main-results-container'><SearchResults unmountSearchResults={this.handleSearchUnmount} searchResults={topbarSearchResults} /></span>
                        }
                    </form>
                </div>
                <CheeseburgerMenu
                    right={false}
                    isOpen={this.state.open}
                    closeCallback={this.handleClick}
                    >
                    <Mobile_Menu user={this.props.user} handleClick={this.handleClick}/>
                </CheeseburgerMenu>
                <CheeseburgerMenu
                    right={true}
                    isOpen={this.props.isSlidingMenuOpen}
                    closeCallback={this.closeMenu}
                    >
                    <SlidingMenu closeCallback={this.closeMenu} />
                </CheeseburgerMenu>
                { this.state.notification && <Notifications isMobile={true} handleNotifications={this.handleNotifications} />}
            </div>
        );
    }
};

function mapStateToProps(state) {
    return {
        user: state.user.data,
        topbarSearchResults: state.search.topbarSearchResults,
        requestCancellationFn: state.search.requestCancellationFn,
        notifications_count: state.notification && state.notification.unread_count,
        isSlidingMenuOpen: state.slidingMenu.isOpen,
    }
}

function mapDispatchToProps(dispatch) {
    return {
        getTopbarSearchResults: (searchTerm, requestCancellationFn) => { dispatch(getTopbarSearchResults(searchTerm, requestCancellationFn)) },
        cancelPreviousRequest: () => { dispatch(cancelPreviousRequest()) },
        resetSearchResults: () => { dispatch(resetSearchResults()) },
        setHeaderState: () => { dispatch(setHeaderState()) },
        toggleSlidingMenu: () => { dispatch(toggleSlidingMenu()) },
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(MobileHeader);
