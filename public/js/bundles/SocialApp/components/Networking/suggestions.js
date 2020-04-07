import React, { Component } from 'react'
import SuggestionCard from './suggestionCard'
import InfiniteScroll from 'react-infinite-scroller';
import { getSuggestionData } from './../../api/post'
import { connect } from 'react-redux'
import { withRouter } from 'react-router-dom';
import orderBy from 'lodash/orderBy'
import { Helmet } from 'react-helmet';
const _ = {
    orderBy: orderBy
}
class Suggestions extends Component{
    constructor(props){
        super(props);
        this.state={
            hasMoreItems: true,
            suggestedUsers: [],
            items: [],
            apiFlag: true,
            sortOption: 'asc',
            dataFlag: true,
        }
        this._getSuggestionData = this._getSuggestionData.bind(this);
        this.filterList = this.filterList.bind(this);
        this.toggleSortOption = this.toggleSortOption.bind(this);
    }
    componentDidMount(){
        this.setState({
            suggestedUsers: this.props.suggestedUsers,
            items: this.props.suggestedUsers,
        })
        this.props.history.push("/social/networking/suggestion")
    }
    componentDidUpdate(prevProps){
        if(prevProps.suggestedUsers != this.props.suggestedUsers){
            this.setState({
                suggestedUsers: this.props.suggestedUsers,
                items: this.props.suggestedUsers,
            })
        }
    }
    filterList(event){
        let { suggestedUsers } = this.props;
        if(suggestedUsers){
            var updatedList = suggestedUsers;
            updatedList = updatedList.filter(function(item){
                let fullName = item.fname + item.lname;
                return fullName.toLowerCase().search( event.target.value.toLowerCase()) !== -1;
            });
            this.setState({items: updatedList});
        }
    }
    _getSuggestionData(page){
        const { suggestionOffset, suggestionFlag } = this.props;
        let data = {
            offset: parseInt(suggestionOffset),
        }
        this.setState({apiFlag: false})
        getSuggestionData(data)
        .then(()=>{
            this.setState({
                apiFlag: suggestionFlag,
                dataFlag: false,
            })
        })
    }
    toggleSortOption(){
        if(this.state.sortOption == 'asc'){
            this.setState({sortOption: 'desc'})
        }else{
            this.setState({sortOption: 'asc'})
        }
    }
    render(){
        let { user } = this.props;
        let { items } = this.state;
        let SUGGESTION = '';
        if(items){
            SUGGESTION = _.orderBy(items, ['user_id'],[this.state.sortOption]).map((suggestedUser, index) =>
                <SuggestionCard key={index} suggestedUser={suggestedUser} logInUser={user} />
            );
        }
        return(
            <div>
                <Helmet>
                  <title>College Social Networking | Connection Suggestion | Plexuss</title>
                  <meta name="description" content="In this section, you will see our connection suggestions" />
                  <meta name="keywords" content="Colleges Network" />
                </Helmet>
                <div className="mobile-network-header">
                    <div className="network-tab-label">
                        Suggestions ({items && items.length})
                    </div>
                    <form className="search_form">
                        <input type="text" placeholder="Search Suggestions" className="input_contral" onChange={(e) => this.filterList(e)}/>
                        <a className="button postfix fa fa-search btn-search search_icon"></a>
                    </form>
                    <div className="count_container">
                        <div className="total_records">{items && items.length} {'Suggestions'}</div>
                        <div className="sort_by" onClick={() => this.toggleSortOption()}>Sort by:{' '}
                            {this.state.sortOption == 'asc' ? <i className="fa fa-caret-down" aria-hidden="true"></i> : <i className="fa fa-caret-up" aria-hidden="true"></i>}
                        </div>
                    </div>
                </div>

                <div className="desktop-network-header">
                    <form className="search_form">
                        <input type="text" placeholder="Search Suggestions" className="input_contral" onChange={(e) => this.filterList(e)}/>
                        <a className="button postfix fa fa-search btn-search search_icon"></a>
                    </form>
                    <div className="count_container">
                        <div className="total_records">{items && items.length} {'Suggestions'}</div>
                        <div className="sort_by" onClick={() => this.toggleSortOption()}>Sort by:{' '}
                            {this.state.sortOption == 'asc' ? <i className="fa fa-caret-down" aria-hidden="true"></i> : <i className="fa fa-caret-up" aria-hidden="true"></i>}
                        </div>
                    </div>
                </div>
                <ul>
                    <InfiniteScroll
                        pageStart={0}
                        loadMore={this._getSuggestionData}
                        hasMore={this.state.apiFlag}
                        loader={<div className="loader" key={0}>Loading ...</div>}
                    >
                        {SUGGESTION }
                    </InfiniteScroll>
                </ul>
                {
                    this.state.dataFlag && <div className="network-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div>
                }
            </div>
        )
    }
}
const mapStateToProps = (state) =>{
    return{
        suggestedUsers: state.user.networkingDate.suggestedUser,
        suggestionOffset: state.user.networkingDate.suggestionOffset,
        suggestionFlag: state.user.networkingDate.suggestionFlag,
    }
}
export default connect(mapStateToProps, null)(withRouter(Suggestions))
