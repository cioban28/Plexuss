import React from 'react';
class ReportingReason2 extends React.Component{
    render(){
      var handleClick = this.props.handleClick;
      var handleSubmit = this.props.handleSubmit;
      var handleMoreInfo = this.props.handleMoreInfo;
      var closeClick = this.props.closeClick;
      return(
        <div id= "modal-body">
          <div id="myModal1">
            <div className="report-area">
              <div className="report-nav-btns">
                <span className="report-modal-back" onClick={() => handleClick('modal1')}><i className="fa fa-arrow-left"></i>{' Back'}</span>
                <span className="report-modal-close" onClick={() => closeClick()}>&#10005;</span>
              </div>
              <h3 id="modalTitle">Why are you reporting?</h3>
              <div className="row">
                <div className="large-12 small-12 medium-12 columns optional">
                  <span>Anything else we should know? (Optional)</span>
                </div>
              </div>
              <div>
                <div className="text-area large-12 small-12 medium-12">
                  <textarea rows="5" name="textareaname" cols="25" value={this.props.text} onChange={handleMoreInfo}></textarea>
                </div>
              </div>
            </div>
            <div className="next-button" onClick={() => handleSubmit()}>
              <input type="submit" className="button submit-button" value="Submit" />
            </div>
          </div>
        </div>
      )
    }
  }
  export default ReportingReason2;
