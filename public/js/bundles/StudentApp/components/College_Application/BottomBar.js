// /College_Application/Basic_Info

import React from 'react'
import SaveButton from './SaveButton'



class BottomBar extends React.Component{
  constructor(props){
    super(props)
     this.state = { showModal: false };
     this.setUnsetApplicationsModal = this.setUnsetApplicationsModal.bind(this);
  }
  setUnsetApplicationsModal() {
    this.setState((prevState) => ({showModal: !(prevState.showModal)}))
  }

  render(){
    let {_profile, PAGE_DONE} = this.props
    return(
      <div className="row bottom-bar" >

        {!!this.props.myApplicationsLength &&
          <div onClick= {this.props.onClickModal} style={{cursor: 'pointer'}}>
            <span className="large-2" style={{float: "left",marginTop: "3%", paddingLeft: "2%"}}>
              Your Colleges
              <span className='selected-colleges-count'>{this.props.myApplicationsLength}</span>
            </span>
          </div>

        }
        <img  className="large-2 bottom-bar-image-styling" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/1519_university-of-kentucky_01.jpg" />
        <div className="large-4 bottom-bar-left-half">
          <p className="bottom-bar-para-first-line"> Needing Application Support?</p>
          <div className="bottom-bar-para-second-line" onClick={() => this.props.openChat()}>Chat with a Plexuss representative <img src="/social/images/compose message.svg" /></div>
        </div>

        <div className={`${this.props.myApplicationsLength ? 'large-4' : 'large-6' } bottom-bar-save-button-container`} >
          <div className="large-6" style={{padding: "1%", float: "right"}}>
            <SaveButton
              myApplicationsLength={this.props.myApplicationsLength}
              onClick={this.props.onClick}
              routeId={this.props.routeId}
              _profile={_profile}
              skip={this.props.skip}
              skipHandler={this.props.verifySkipHandler}
              page_done={PAGE_DONE}
            />

          </div>
        </div>
      </div>
    )
  }
}

export default BottomBar;
