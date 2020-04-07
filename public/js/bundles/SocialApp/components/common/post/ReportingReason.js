import React from 'react';

class ReportingReason extends React.Component{
    render(){
      var handleClick = this.props.handleClick;
      var closeClick = this.props.closeClick;
      var handleReason = this.props.handleReason;
      var handleOtherReason = this.props.handleOtherReason;
      return(
        <div>
          <div className="report-area">
            <div className="report-nav-btns">
              <span className="report-modal-close" onClick={() => closeClick()}>&#10005;</span>
            </div>
            <h3 id="modalTitle"><b>Why are you reporting?</b></h3>
            <div className="row report-options">
              <div className="large-1 small-1 medium-1 radio-padding columns">
                <input type="radio" className="rbd" name="option" value="illegal" id="choice-1" checked={this.props.checked === "illegal"} onChange={handleReason}/>
              </div>
              <div className="large-11 small-11 medium-11 columns">
                <label className="radio-labels" htmlFor="choice-1">Illegal Activity</label>
              </div>
            </div>
            <div className="row report-options">
              <div className="large-1 small-1 medium-1 radio-padding columns ">
                <input type="radio" className="rbd" name="option" value="impersonation" id="choice-2" checked={this.props.checked === "impersonation"} onChange={handleReason}/>
              </div>
              <div className="large-11 small-11 medium-11 columns">
                <label className="radio-labels" htmlFor="choice-2">Impersonation</label>
              </div>
            </div>

            <div className="row report-options">
              <div className="large-1 small-1 medium-1 radio-padding columns">
                <input type="radio" className="rbd" name="option" value="spam" id="choice-3" checked={this.props.checked === "spam"} onChange={handleReason}/>
              </div>
              <div className="large-11 small-11 medium-11 columns">
                <label className="radio-labels" htmlFor="choice-3">Spam</label>
              </div>
            </div>

            <div className="row report-options">
              <div className="large-1 small-1 medium-1 radio-padding columns">
                <input type="radio" className="rbd" name="option" value="other" id="choice-5" checked={this.props.checked === "other"} onChange={handleReason}/>
              </div>
              <div className="large-11 small-11 medium-11 columns">
                <label className="radio-labels" htmlFor="choice-5">Other</label>
                {this.props.checked === "other" && <input className="rbd-other-reason" value={this.props.otherReason} onChange={handleOtherReason}/>}
              </div>
            </div>
          </div>
          <div className="next-button" >
            <div className={this.props.checked === "other" ? this.props.otherReason === "" && 'disabled' : this.props.checked === "" && 'disabled'} onClick={() => handleClick('modal2')}>Next</div>
          </div>
        </div>
        )
    }
  }

  export default ReportingReason;
