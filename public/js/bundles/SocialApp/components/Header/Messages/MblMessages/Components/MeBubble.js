import React, { Component } from 'react';
class MeBubble extends Component{
    render(){
        let { message } = this.props;
        return(
            <div className="bubble-container">
                <div className="me-bubble">
                    <div className="pic-wrapper">
                        <div className="pic-name"></div>
                    </div>
                    <div className="msg">
                        <div>{message}</div>
                        <div className="arrow"></div>
                    </div>
                </div>
            </div>
        )
    }
}
export default MeBubble