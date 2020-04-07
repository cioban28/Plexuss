import React, { Component } from 'react'
import Slider from "react-slick";
import './styles.scss'
import {Collapse} from 'react-collapse';
import { Link } from 'react-router-dom'

class InterestingCollegesList extends Component{
  constructor(props){
    super(props)
    this.state = {
      length: 4,
      expand: false 
    }
  }

  handleExpandCollapse= (length = 4) => {
    this.setState( (prevState) => ({length: prevState.expand ? 4 :length, expand: !prevState.expand}))
    this.props.setReRender();
  }
  
  handleCollapse = () => {
    this.setState({length: 4, expand: false})
  }
  
  componentDidMount(){
  }

  render(){
    
    let settings = {
      infinite: false,
      speed: 500,
      slidesToShow: 1,
      slidesToScroll: 2
    };

    let {interesting} = this.props

    return(
      <div className="box-div" id="intersting-div1" style={{position: "absolute", left: "423px", top: "0px"}}>
        <div className="header-banner" style={{backgroundColor: "#d93600"}}>Interesting Lists</div>
          <div className="banner-content-div" style={{backgroundColor: "black"}}> 
            <div className="owl-carousel msg-carousel owl-theme" style={{opacity: "1", display: "block"}}>
              <div className="owl-wrapper-outer">
                <div className="owl-wrapper" style={{width: "100%", left: "0px", display: "block"}}>
                  <div className="owl-item" style={{width: "100%  "}}>
                  
                   <Slider {...settings}>
                  
                    { !!interesting && Object.keys(interesting).map( key =>  
                    <div style={{backgroundColor: "#040404", width: "432px"}} key={`interesting-${key}`}>
                      <div className="pd-top">
                        <div className="item text-center text-white p5 fs16 bold-font">{interesting[key][0].list_title}</div>
                        <div className="rank-div-header-box-interest row">
                          <div className="column small-3 text-center coll-interestingList-header-left">
                            PLEXUSS&nbsp;<br/>RANK
                          </div>

                          <div className="column small-9 text-center coll-interestingList-header-right">
                            SCHOOL NAME
                          </div>
                        </div>
                      </div>

                      <ul className="list-styling interesting-ul-styling">
                      {interesting[key].slice(0, this.state.length).map(( college, index )=>  
                        <li className="mt10" key={index}>
                    
                            <div className="row">
                              <div className="column small-3 rank-align">
                                <span className="box_image-no">#{college.plexuss_rating}</span>
                              </div>
                              <div className="column small-9">
                                <Link to={`/college/${college.slug}`} className="fs14 battlefont" >
                                  {college.school_name}
                                </Link>

                                <div className="row">
                                  <div className="column small-12 battlefont fs14 f-normal">
                                    {college.city} , {college.long_state}
                                  </div>
                                </div>
                              </div>
                            </div>
                        </li>
                      )}
                    </ul>

                    <div onClick={() => this.handleExpandCollapse(interesting[key].length)} className="footer-banner interesting-footer-adjustment" style={{backgroundColor: "#d93600" ,cursor: "pointer"}} >
                      <h6 className="battlefont fs14 txt-center" >{this.state.expand ? 'Collapse' : 'Expand'}</h6>                            
                      <img alt="expand image" className="center-align-image" src="/images/colleges/expand.png"  style={{display: "inline-block", verticalAlign: "middle",transform: this.state.expand ? 'rotate(180deg)' : 'rotate(0deg)' }} className="expand-collapse-img"/>
                    </div> 
                  </div>)}



              </Slider>
              </div>
              </div>
            </div>
          </div>
        </div>
        </div>
    )
  }
}

export default InterestingCollegesList;
