import React, { Component } from 'react'
import { addMessage } from './../../../../../actions/conversations'
import { connect } from 'react-redux'
 
class AttcahFile extends Component{
    constructor(props){
        super(props);
        this.state = {
            text: '',
        }
        this.keyPress = this.keyPress.bind(this);
        this.onChange = this.onChange.bind(this);
        this.sendMessage = this.sendMessage.bind(this);
    }
    keyPress(event){
        if(event.keyCode == 13){
            let arr = {};
            arr.type = 'outComming';
            arr.text = this.state.text;
            arr.date = Date.now();
            if(this.state.text !== ''){
                this.props.addMessage(this.props.convoId, arr);
            }
            this.setState({text: ''})
        }
    }
    onChange(event){
        this.setState({
            text: event.target.value,
        })
    }
    sendMessage(){
        let arr = {};
        arr.type = 'outComming';
        arr.text = this.state.text;
        arr.date = Date.now();
        if(this.state.text !== ''){
            this.props.addMessage(this.props.convoId, arr);
        }
        this.setState({text: ''})
    }
    render(){
        return(
            <div className="actionsContainer">
                <div className="send-container">
                    <textarea id="send_field" name="send_field" placeholder="Send message" onChange={this.onChange} value={this.state.text} />
                    <button className="button send_button" onClick={() => this.sendMessage()}><span>Send</span></button>
                </div>
                <div className="template-container">
                    <div className="attachmentsButton">
                        <div className="picTextButton">
                            <div className="btn-image">
                                <div className="attch-icon"></div>
                                <div className="btn-text">Attach Files</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        )
    }
}
function mapStateToProps(state){
    return{
    }
}
function mapDispatchToProps(dispatch){
    return{
        addMessage: (convoId, message) => { dispatch(addMessage(convoId, message)) }
    }
}
export default connect(mapStateToProps, mapDispatchToProps)(AttcahFile)