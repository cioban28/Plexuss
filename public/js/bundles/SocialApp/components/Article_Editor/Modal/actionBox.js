import React, { Component } from 'react'

class ActionBox extends Component{
    constructor(props){
        super(props);
        this.state={
            shareWithOption: 'Public',
            shareWithFlag: false,
        }
        this.toggleShareWithFlag = this.toggleShareWithFlag.bind(this);
        this.setShareWithOption = this.setShareWithOption.bind(this);
    }
    componentDidMount(){
        if(this.props.privacy !== 1){
            switch (this.props.privacy) {
                case 2: this.setShareWithOption('My Connections Only'); break;
                case 3: this.setShareWithOption('Only Me & Colleges'); break;
                case 4: this.setShareWithOption('Only Me'); break;
            }
        }
    }
    toggleShareWithFlag(){
        this.setState({
          shareWithFlag: !this.state.shareWithFlag,
        })
    }
    setShareWithOption(option){
        this.setState({
            shareWithOption: option,
            shareWithFlag: false,
        })
        switch (option) {
            case 'Public': this.props.handleShareWith(1); break;
            case 'My Connections Only': this.props.handleShareWith(2); break;
            case 'Only Me & Colleges': this.props.handleShareWith(3); break;
            case 'Only Me': this.props.handleShareWith(4); break;
        }
    }
    render(){
        let { publish, closeModal, disablePublish } = this.props;
        return(
            <div className="action_box_parent">
                <div className="actionBox">
                    <div className="row actionRow">
                    <div className="large-7 medium-7 small-12 columns share-column">
                        <span onClick={this.toggleShareWithFlag}>SHARE WITH: {this.state.shareWithOption}  <img src="/social/images/arrow.svg" className="new-arrow" /></span>
                    </div>
                    <div className="large-5 medium-5 small-12 columns">
                        <button className="cancelButton" onClick={closeModal}>Cancel</button>
                        <button className="shareButton" disabled={disablePublish} onClick={()=>{publish(1); closeModal() }}>Publish</button>
                    </div>
                    </div>
                </div>
                {
                    this.state.shareWithFlag && 
                    <div className="share_width">
                        <div className="item" onClick={() => this.setShareWithOption('Public')}>Public</div>
                        <div className="item" onClick={() => this.setShareWithOption('My Connections Only')}>My Connections Only</div>
                        <div className="item" onClick={() => this.setShareWithOption('Only Me & Colleges')}>Only Me & Colleges</div>
                        <div className="item" onClick={() => this.setShareWithOption('Only Me')}>Only Me</div>
                    </div>
                }
            </div>
        )
    }
}

export default ActionBox;