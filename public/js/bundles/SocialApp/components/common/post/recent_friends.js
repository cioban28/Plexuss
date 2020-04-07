import React, { Component } from 'react'
class RecentFriends extends Component{
  constructor(props){
    super(props);
    this.state = {
      selected: false,
    }
    this.toggleSelected = this.toggleSelected.bind(this);
  }
  toggleSelected(){
    this.setState({
      selected: !this.state.selected,
    })
  }
  render(){
    let {friend} = this.props;
    return(
      <li className="recentUser" onClick={this.toggleSelected} >
      {
        friend.img && 
        <img className="recentUserImg" src={friend.img} />
      }
        <div className="recentUserName">{friend.name}</div>
        { this.state.selected && <img className="userSelected" src="/social/images/Icons/checkmark.png" /> }
      </li>
    )
  }
}
export default RecentFriends;
