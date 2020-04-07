import React, { Component } from 'react'
import RequestCard from './requestCard'
import orderBy from 'lodash/orderBy'
import { withRouter } from 'react-router-dom';
import { Helmet } from 'react-helmet';
const _ = {
    orderBy: orderBy
}
class Requests extends Component{
    constructor(props){
        super(props);
        this.state={
            hasMoreItems: true,
            requests: [],
            items: [],
            apiFlag: true,
            sortOption: 'asc',
        }
        this.filterList = this.filterList.bind(this);
        this.toggleSortOption = this.toggleSortOption.bind(this);
    }
    componentDidMount(){
        this.setState({
            requests: this.props.requests,
            items: this.props.requests,
        })
        this.props.history.push("/social/networking/requests")
    }
    componentDidUpdate(prevProps){
        if(prevProps.requests != this.props.requests){
            this.setState({
                requests: this.props.requests,
                items: this.props.requests,
            })
        }
    }
    filterList(event){
        let { requests } = this.props;
        if(requests){
            var updatedList = requests;
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
        let { user, spinnerFlag } = this.props;
        let { items } = this.state;
        let REQUEST = '';
        if(items){
            REQUEST = _.orderBy(items, ['created_at'],[this.state.sortOption]).map((request, index) =>
                <RequestCard key={request.user_id} requestedUser={request} logInUser={user}/>
            );
        }
        return(
            <div>
                <Helmet>
                  <title>College Social Networking | Connections Requests| Plexuss</title>
                  <meta name="description" content="Here, you will see your pending connection requests" />
                  <meta name="keywords" content="Colleges Network" />
                </Helmet>
                <div className="mobile-network-header">
                    <div className="network-tab-label">
                        Requests ({items && items.length})
                    </div>
                    <form className="search_form">
                        <input type="text" placeholder="Search Requests" className="input_contral" onChange={(e) => this.filterList(e)}/>
                        <a href="#" className="button postfix fa fa-search btn-search search_icon"></a>
                    </form>
                    <div className="count_container">
                        <div className="total_records">{items && items.length} {'Requests'}</div>
                        <div className="sort_by" onClick={() => this.toggleSortOption()}>Sort by:{' '}
                            {this.state.sortOption == 'asc' ? <i className="fa fa-caret-down" aria-hidden="true"></i> : <i className="fa fa-caret-up" aria-hidden="true"></i>}
                        </div>
                    </div>
                </div>

                <div className="desktop-network-header">
                    <form className="search_form">
                        <input type="text" placeholder="Search Requests" className="input_contral" onChange={(e) => this.filterList(e)}/>
                        <a href="#" className="button postfix fa fa-search btn-search search_icon"></a>
                    </form>
                    <div className="count_container">
                        <div className="total_records">{items && items.length} {'Requests'}</div>
                        <div className="sort_by" onClick={() => this.toggleSortOption()}>Sort by:{' '}
                            {this.state.sortOption == 'asc' ? <i className="fa fa-caret-down" aria-hidden="true"></i> : <i className="fa fa-caret-up" aria-hidden="true"></i>}
                        </div>
                    </div>
                </div>
                <ul>
                    {REQUEST}
                </ul>
                {
                    items.length <= 0 && <div className="network-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div>
                }
            </div>
        )
    }
}
export default withRouter(Requests);
