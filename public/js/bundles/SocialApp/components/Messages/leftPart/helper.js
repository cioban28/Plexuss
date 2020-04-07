import React from 'react'
import moment from 'moment'
export function UnConnectedUser(props){
    let { thread } = props;
    let imgStyles = {backgroundImage: thread.img ? '"'+thread.img+'"' : "/social/images/Avatar_Letter/"+thread.Name.split(' ')[0]+".svg" }
    return(
        <span className="inner_part">
            <div className="time">{''}</div>
                <div className="large-3 medium-3 small-3 columns">
                    <div className="thread-img" style={imgStyles}/>
                </div>
                <div className="large-8 medium-8 small-8 columns">
                    <div className="user_name">{thread.fname} {thread.lname+" "} <div className={"flag flag-"+ (!!thread && !!thread.country_code && thread.country_code.toLowerCase())}></div></div>
                    <div className="message">{'Say Hello'}</div>
                    {
                        thread.num_unread_msg &&
                            <div className="message_number">{thread.num_unread_msg}</div>
                    }
                </div>
            <div className="large-1 medium-1 small-1 columns"></div>
        </span>
    )
}
export class ConnectedUser extends React.Component{
    render(){
        let { thread } = this.props;
        let localtime = moment.utc(thread.date).local().format('lll');

        let avatarPic = !!thread.Name && '/social/images/Avatar_Letters/'+thread.Name.charAt(0).toUpperCase()+'.svg'
        let profPic = !!thread.img ? !thread.img.includes('default.png') ? thread.img : avatarPic : avatarPic;
        let imgStyles = {backgroundImage: 'url("'+profPic+'")' }
        return(
            <span className="inner_part">
                <div className="time">{localtime}</div>
                    <div className="large-3 medium-3 small-3 columns">
                        <div className="thread-img" style={imgStyles}/>
                    </div>
                    <div className="large-8 medium-8 small-8 columns">
                        <div className="user_name">{thread.Name+" "} <div className={"flag flag-"+ (!!thread && !!thread.country_code && thread.country_code.toLowerCase())}></div></div>
                        <div className="message">
                            {
                                thread.msg &&
                                thread.msg ?
                                isHTML(thread.msg) ? 'Shared Post' : thread.msg
                                : 'Say Hello'}
                        </div>
                        {
                            thread.num_unread_msg > 0 &&
                                <div className="message_number">{thread.num_unread_msg}</div>
                        }
                    </div>
                <div className="large-1 medium-1 small-1 columns"></div>
            </span>
        )
    }
}
const isHTML = (str) => {
    const doc = new DOMParser().parseFromString(str, "text/html");
    return Array.from(doc.body.childNodes).some(node => node.nodeType === 1);
}
