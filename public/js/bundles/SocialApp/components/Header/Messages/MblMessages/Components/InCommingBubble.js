import React, { Component } from 'react';

class InCommingBubble extends Component{
    render(){
        let { message } = this.props;
        return(
            <div className="bubble-container">
                <div className="incoming-bubble">
                    <div className="msg">
                        <div>{message}</div>
                        <div className="arrow"></div>
                    </div>
                    <div className="pic-wrapper">
                        <div className="pic-name"></div>
                    </div>
                </div>
            </div>
        )
    }
}
export default InCommingBubble;