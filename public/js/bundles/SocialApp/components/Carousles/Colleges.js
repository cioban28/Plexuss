import React, { Component } from 'react';
import { connect } from 'react-redux'

class Colleges extends Component{
  constructor(props){
    super(props);

    this.state = {
    }
  }

  componentDidMount(){
  }

  componentWillMount() {
  }

  render() {
      var background_url = 'url(https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/' + this.props.data[1].logo_url + ')',
        pin_url = 'url(https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/' + this.props.data[1].img_url + ')';
    var background_style = {'backgroundImage': background_url},
        pin_style = {'backgroundImage': pin_url};
    return (
        <span>
            <div className="college_com">
            <div className="item effect-sadie text-center">
                <div className="background-college-logo" style={background_style}></div>
                <div className="college-pin-school-name">{this.props.data[1].school_name}</div>
                <figure>
                    <img className="pin-page-turn-img" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/page-corner-curl_40x40.png" alt=""/>
                    <figcaption>
                        <div className="pin-back pin-back-img" style={pin_style}>
                            <div className="overlay"></div>
                            <a href={`/college/${this.props.data[1]['slug']}`} className="college-pin-link">
                                <div className="row college-pin-footer-container">
                                    <div className="column small-4 text-left rank">
                                        <div className="top-rank-pin-rank-icon text-center"><strong>#{this.props.data[1]['rank']}</strong></div>
                                    </div>
                                    <div className="pin-mile">{this.props.data[1]['distance']} miles away</div>
                                    <div className="column small-8 pin-item-footer">
                                        <div>VIEW COLLEGE</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </figcaption>
                </figure>
                </div>
            </div>
        </span>
    );
  }
}

const mapStateToProps = (state) =>{
  return{
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(Colleges);
