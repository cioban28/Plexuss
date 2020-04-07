import React, { Component } from 'react'
import Topbar from './topBar'
import BottomPortion from './bottomPortion/index'
import './styles.scss'
class RightPart extends Component{
    render(){
        let { mobileHandleFlag, mobileHandle, handleNewMessagesThread, newMessageThread } = this.props;
        return(
            <div className={"right_part "+(mobileHandleFlag ? '' : 'mobile_handler')}>
                <Topbar mobileHandle={mobileHandle} handleNewMessagesThread={handleNewMessagesThread} newMessageThread={newMessageThread}/>
                <BottomPortion messages={this.props.messages}/>
            </div>
        )
    }
}
export default RightPart;