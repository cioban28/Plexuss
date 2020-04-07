import React, { Component } from 'react'
import Conversation from './conversation/index'
import SendMessage from './sendMessage/index'
import './styles.scss'
class BottomPortion extends Component{
    constructor(props){
        super(props)
    }
    render(){
        return(
            <div className="bottom_portion">
                <Conversation messages={this.props.messages}/>
                <SendMessage />
            </div>
        )
    }
}
export default BottomPortion;