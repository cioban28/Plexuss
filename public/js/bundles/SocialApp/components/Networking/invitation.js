import React, { Component } from 'react'
import NetworkingCard from './networkingCard'
import ImportPlexussMemberCard from './importPlexussMemberCard'
import { connect } from 'react-redux'
import { withRouter } from 'react-router-dom';
import { getStoredContacts } from './../../api/user'
import { SpinningBubbles } from './../common/loader/loader'
import InfiniteScroll from 'react-infinite-scroller';
class Invitations extends Component{
    constructor(props){
        super(props);
        this.state={
            plexuss_members: [],
            items: [],
            spinnerFlag: true,
            hasMoreItems: true,
        }
        this.getContacts = this.getContacts.bind(this);
    }
    getContacts(page){
        this.setState({hasMoreItems: false})
        if(this.props.hasMoreContacts && this.props.offset !== undefined){
            getStoredContacts(this.props.offset)
            .then(()=>{
                this.setState({
                    spinnerFlag: false,
                    hasMoreItems: true,
                })
            })
        }else{
            this.setState({hasMoreItems: false})
        }
    }
    componentDidMount(){
        let { storedContacts, plexussMembers } = this.props;
        if (storedContacts.length <= 0 || plexussMembers.length <= 0) {
            this.props.setSubComponent('importContacts')
        }
        this.setState({
            items: this.props.storedContacts,
            plexuss_members: this.props.plexussMembers
        })
        this.props.history.push("/social/networking/importContacts")
    }
    componentDidUpdate(prevProps){
        if(prevProps.storedContacts != this.props.storedContacts || prevProps.plexussMembers != this.props.plexussMembers){
            this.setState({
                items: this.props.storedContacts,
                plexuss_members: this.props.plexussMembers
            })
        }
    }
    filterList(event){
        let { storedContacts, plexussMembers } = this.props;
        var updatedList = storedContacts;
        var updatedListForMembers = plexussMembers;

        if (updatedList) {
            updatedList = updatedList.filter(function(item){
                let fullName = item.invite_name + item.invite_email;
                return fullName.toLowerCase().search( event.target.value.toLowerCase()) !== -1;
            });
        }
        if (updatedListForMembers) {
            updatedListForMembers = updatedListForMembers.filter(function(item){
                let fullName = item.fname + item.lname;
                return fullName.toLowerCase().search( event.target.value.toLowerCase()) !== -1;
            });
        }
        this.setState({
            items: updatedList,
            plexuss_members: updatedListForMembers
        });
    }
    searchFieldChangeHandler(event){
        if(event.target.value.length > 0) {
            if(this.state.hasMoreItems) {
                this.setState(
                    { hasMoreItems: false },
                    () => this.filterList(event)
                );
            }
            else {
                this.filterList(event)
            }
        }
        else {
            this.setState({hasMoreItems: true})
        }
    }
    render(){
        let { setSubComponent } = this.props;
        let { items, plexuss_members, spinnerFlag } = this.state;
        return(
            <div>
                <div className="mobile-network-header">
                    <div className="network-tab-label">
                        Import Contacts
                    </div>
                    <form className="search_form">
                        <input type="text" placeholder="Search Contacts" className="input_contral" onChange={(e) => this.searchFieldChangeHandler(e)}/>
                        <a href="#" className="button postfix fa fa-search btn-search search_icon"></a>
                    </form>
                    <div className="count_container">
                        <div className="total_records">{items && items.length} {'Contacts'}</div>
                        <div className="sort_by" onClick={()=>setSubComponent('importContacts')}>{'Add more contacts'}</div>
                    </div>
                </div>

                <div className="desktop-network-header">
                    <form className="search_form">
                        <input type="text" placeholder="Search Contacts" className="input_contral" onChange={(e) => this.searchFieldChangeHandler(e)}/>
                        <a href="#" className="button postfix fa fa-search btn-search search_icon"></a>
                    </form>
                    <div className="count_container">
                        <div className="total_records">{items && items.length} {'Contacts'}</div>
                        <div className="sort_by" onClick={()=>setSubComponent('importContacts')}>{'Add more contacts'}</div>
                    </div>
                </div>
                {
                    spinnerFlag &&
                    <SpinningBubbles />
                }
                <InfiniteScroll
                    pageStart={0}
                    loadMore={this.getContacts}
                    hasMore={this.state.hasMoreItems}
                >
                    <ul>
                        {
                            plexuss_members && plexuss_members.map(member =>{
                                return <ImportPlexussMemberCard member={member}/>
                            })
                        }
                        {
                            items && items.map(contact =>{
                                return <NetworkingCard contact={contact}/>
                            })
                        }
                    </ul>
                </InfiniteScroll>
            </div>
        )
    }
}
function mapStateToProps(state){
    return{
        storedContacts: state.setting.setting.storedContacts,
        plexussMembers: state.setting.setting.plexussMembers,
        hasMoreContacts: state.setting.setting.hasMoreContacts,
        offset: state.setting.setting.offset,
    }
}
export default connect(mapStateToProps, null)(withRouter(Invitations));
