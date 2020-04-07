import React from 'react';

class ReportingReason3 extends React.Component{
    render(){
      var closeClick = this.props.closeClick;
      return(
        <div id= "modal-body">
          <div id="myModal2">
            <div className="submition report-area">
              <div className="report-nav-btns">
                <span className="report-modal-close" onClick={() => closeClick()}>&#10005;</span>
              </div>
              {/* <img src="social/images/modal3.png" /> */}
              <div className="submit-msg">
                <h3>Your report has been submitted</h3>
              </div>
              {/* <p>In the meantime you can block or unfollow the person to avoid seeing further posts</p> */}
            </div>
            <div className="next-button" onClick={() => closeClick()}>
              <a className="button submit-button">OK</a>
            </div>
          </div>
        </div>
      )
    }
}
export default ReportingReason3;
