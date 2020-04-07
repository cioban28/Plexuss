import React, { Component } from 'react'
import ConnectionCard from './connectionCard'
import orderBy from 'lodash/orderBy'
import { connect } from 'react-redux'
import { withRouter } from 'react-router-dom';
import { Helmet } from 'react-helmet';
const _ = {
    orderBy: orderBy
}
class Connections extends Component{
    constructor(props){
        super(props);
        this.state={
            hasMoreItems: true,
            friends: [],
            items: [],
            sortOption: 'asc',
        }
        this.filterList = this.filterList.bind(this);
        this.toggleSortOption = this.toggleSortOption.bind(this);
    }
    componentDidMount(){
        this.setState({
            friends: this.props.friends,
            items: this.props.friends,
        })
        this.props.history.push("/social/networking/connection")
    }
    componentDidUpdate(prevProps){
        if(prevProps.friends != this.props.friends){
            this.setState({
                friends: this.props.friends,
                items: this.props.friends,
            })
        }
    }
    filterList(event){
        let { friends } = this.props;
        if(friends){
            var updatedList = friends;
            updatedList = updatedList.filter(function(item){
                let fullName = item.fname + item.lname;
                return fullName.toLowerCase().search( event.target.value.toLowerCase()) !== -1;
            });
            this.setState({items: updatedList});
        }
    }
    toggleSortOption(){
        if(this.state.sortOption == 'asc'){
            this.setState({sortOption: 'desc'})
        }else{
            this.setState({sortOption: 'asc'})
        }
    }
    render(){
        let { user, spinnerFlag, messageThreads} = this.props;
        let { items } = this.state;
        let FRIENDS = '';
        if(items && messageThreads){
            FRIENDS = _.orderBy(items, ['created_at'],[this.state.sortOption]).map((friend, index) =>
                <ConnectionCard key={index} friend={friend} logInUser={user} messageThreads={messageThreads}/>
            );
        }
        return(
            <div>
                <Helmet>
                  <title>College Social Networking | Connections | Plexuss</title>
                  <meta name="description" content="In this section, you will be able to see your connections" />
                  <meta name="keywords" content="Colleges Network" />
                </Helmet>
                <div className="mobile-network-header">
                    <div className="network-tab-label">
                        Connections ({items && items.length})
                    </div>
                    <form className="search_form">
                        <input type="text" placeholder="Search Connections" className="input_contral" onChange={(e) => this.filterList(e)}/>
                        <a href="#" className="button postfix fa fa-search btn-search search_icon"></a>
                    </form>
                    <div className="count_container">
                        <div className="total_records">{items && items.length} {'Connections'}</div>
                        <div className="sort_by" onClick={() => this.toggleSortOption()}>Sort by:{' '}
                            {this.state.sortOption == 'asc' ? <i className="fa fa-caret-down" aria-hidden="true"></i> : <i className="fa fa-caret-up" aria-hidden="true"></i>}
                        </div>
                    </div>
                </div>

                <div className="desktop-network-header">
                    <form className="search_form">
                        <input type="text" placeholder="Search Connections" className="input_contral" onChange={(e) => this.filterList(e)}/>
                        <a href="#" className="button postfix fa fa-search btn-search search_icon"></a>
                    </form>
                    <div className="count_container">
                        <div className="total_records">{items && items.length} {'Connections'}</div>
                        <div className="sort_by" onClick={() => this.toggleSortOption()}>Sort by:{' '}
                            {this.state.sortOption == 'asc' ? <i className="fa fa-caret-down" aria-hidden="true"></i> : <i className="fa fa-caret-up" aria-hidden="true"></i>}
                        </div>
                    </div>
                </div>
                <ul>
                    {FRIENDS}
                </ul>
                {
                    this.state.items.length<=0 && <div className="network-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div>
                }
            </div>
        )
    }
}
function mapStateToProps(state){
    return{
        messageThreads: state.messages.messageThreads && state.messages.messageThreads.topicUsr,
    }
}
export default connect(mapStateToProps, null)(withRouter(Connections));
