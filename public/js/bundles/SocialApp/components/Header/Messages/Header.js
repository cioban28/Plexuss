import React, { Component } from 'react'
class Header extends Component{
    render(){
        let { filterList, handleNewmsg } = this.props;
        return(
            <span>
                <li className="get_messages_head" onClick={()=>handleNewmsg()}>
                    <div className="title">Messages</div>
                    <div><img src="/social/images/compose message.svg" /></div>
                </li>
                <li className="form_banner">
                    <form className="messages_seacrh" >
                        <i className="fa fa-search search_icon"></i>
                        <input type="text" placeholder="Search Messages" className="search_messages" onChange={(e)=>filterList(e)}/>
                    </form>
                </li>
            </span>
        )
    }
}
export default Header;