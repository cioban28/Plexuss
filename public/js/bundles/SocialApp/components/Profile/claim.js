import React, { Component } from 'react'
import ReactPlayer from 'react-player'
class Claim extends Component{
  constructor(props){
    super(props);
  }
  render(){
    let { claimToFame, user, visible } = this.props;
    return(
      <div className="profile-widgets">
          <div className="widget-heading">
            <h2>Claim to Fame</h2>
          </div>
          <div className="widget-content">
            {/*
              claimToFame && claimToFame.claimToFameVimeoVideoUrl &&
                <ReactPlayer url={claimToFame.claimToFameVimeoVideoUrl} width={'100%'} height={'250px'}/> ||
              claimToFame && claimToFame.claimToFameYouTubeVideoUrl &&
                <ReactPlayer url={claimToFame.claimToFameYouTubeVideoUrl} width={'100%'} height={'250px'}/>
            */}
            <p>
            {!!visible ?
              (claimToFame && claimToFame.claimToFameDescription != "") ?
                claimToFame.claimToFameDescription
                :
                'No Claim to Fame  added yet'
              :
              <span className="private-section">This section is private</span>
            }
            </p>
          </div>
      </div>
    )
  }
}
export default Claim;