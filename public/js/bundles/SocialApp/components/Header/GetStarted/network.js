import React, { Component } from 'react'
import { connect } from 'react-redux';
import { getNetworkingDataSic } from './../../../api/post'
import HoverCardForSIC from './../../common/hover_card/hover_card_for_SIC'
import InfiniteScroll from 'react-infinite-scroller';
import './network.scss'
class Network extends Component{
    constructor(props){
        super(props);
        this.getSuggestionData = this.getSuggestionData.bind(this);
    }
    getSuggestionData(page){
        const { suggestionOffsetForSic, suggestionForSicFlag } = this.props;
        let data = {
            offset: parseInt(suggestionOffsetForSic),
        }
        getNetworkingDataSic(data)
    }
    render(){
        let { handleRenderComponent, suggestionForSic, user, suggestionForSicFlag } = this.props;
        let state = user.state && user.state.toLowerCase();
        return(
            <span>
                {this.props.user.signed_in == 1 ? (
                    <div className="rightbar-list">
                        <div className="network_alumni">
                            <Header handleRenderComponent={handleRenderComponent}/>
                            <div className='network-suggestions-cont'>
                                <InfiniteScroll
                                    pageStart={0}
                                    loadMore={this.getSuggestionData}
                                    hasMore={suggestionForSicFlag}
                                    useWindow={false}
                                >
                                    {
                                        suggestionForSic && suggestionForSic.map((suggestion, index) =>{
                                            return <Card1 key={`suggestion`+index} suggestion={suggestion} />
                                        })
                                    }
                                    <SubHeading user={this.props.user}/>
                                    {
                                        suggestionForSic && suggestionForSic.map((suggestion, index) =>{
                                            if(suggestion.country_code == state){
                                                return <Card1 key={`suggestionHome`+index} suggestion={suggestion} />
                                            }
                                        })
                                    }
                                </InfiniteScroll>
                            </div>
                        </div>
                    </div>
                ) : (
                    <div className="network-preview">
                        <div className="right-circle"><img className="img-circle" src="/images/frontpage/network-circle.png"/></div>
                        <div className="right-text"><span className="desc-text">Find students and alumni who are interested in the same things you are all across the globe</span></div>
                        <div className="right-login"><a href="/signup?utm_source=SEO&utm_medium=frontPage" className="btn-login">Login or Signup</a></div>
                    </div>
                )}
            </span>
        )
    }
}

class Card1 extends Component{
    constructor(props){
        super(props);
        this.state={
            visibleFlag: true,
        }
        this.handleVisibleState = this.handleVisibleState.bind(this);
    }
    handleVisibleState(){
        this.setState({
          visibleFlag: false,
        })
    }
    render(){
        let { suggestion } = this.props;
        let profileImgStyles = {
            backgroundImage: !!suggestion && suggestion.user_img ? 'url("'+suggestion.user_img+'")' : "url(/social/images/Avatar_Letters/"+suggestion.fname.charAt(0).toUpperCase()+".svg)"
        }
        return(
            this.state.visibleFlag &&
            <div className="row">
                <div className="network_card card-hover">
                    <HoverCardForSIC user={suggestion} handleVisibleState={this.handleVisibleState}/>
                    <div className="user_img_parent">
                        <div className="image" style={profileImgStyles}/>
                    </div>
                    <div className="desc">
                        <div className="desc_head">
                            <div className="name">{suggestion.fname} {suggestion.lname}</div>
                        </div>
                        <div className="sub_desc">
                            {suggestion.school_name}
                        </div>
                        <div className="userRole">
                            {suggestion.is_student && 'Student' || suggestion.is_alumni && 'Alumni' || suggestion.is_parent && 'Parent' || suggestion.is_counselor && 'Counselor' || suggestion.is_university_rep && 'University Rep.' || suggestion.is_organization && 'Organization'}
                            {suggestion.grad_year !== "NULL" && ' '+suggestion.grad_year}
                        </div>
                    </div>
                </div>
            </div>
        )
    }
}


function SubHeading(props){
    let { user } = props;
    let state = user.state && user.state.toLowerCase();
    return(
        <div className="row home_card">
            <div className="img_parent">
                <div className={"flag flag-"+state}></div>
            </div>
            <div className="text">Connect with these students from your home country</div>
        </div>
    )
}

function Header(props){
    let { handleRenderComponent } = props;
    return(
        <div className="network_head row" onClick={() => handleRenderComponent('iWantTo')}>
            <div className="large-2 medium-2 small-3 columns sliding_menu_back_btn_parent">
                <i className="fa fa-angle-left left_angle"></i>
                <div className="sliding_menu_back_btn cursor">back</div>
            </div>
            <div className="larger-10 medium-10 small-9 columns cursor">
                Network with students & alumni
            </div>
        </div>
    )
}

function mapStateToProps(state){
    return{
      user: state.user && state.user.data,
      suggestionForSic: state.user.networkingDate.suggestionForSic,
      suggestionOffsetForSic: state.user.networkingDate.suggestionOffsetForSic,
      suggestionForSicFlag: state.user.networkingDate.suggestionForSicFlag,
    }
  }
export default connect(mapStateToProps, null)(Network);
