import React, { Component } from 'react'
import Slider from "react-slick";
import axios from 'axios';

class CollegeDirectory extends Component{
  constructor(props){
    super(props)
    this.state={
      dataToDisplay: ''
    }
  }

  componentDidMount(){
  }

  alphabetClickHandler = (name) => {
    axios({
      method: 'POST',
        url: `/letterfilter`,
        data: {name: name}
      })
      .then(res => {
        this.setState({dataToDisplay: res.data})
      })
      .catch(error => {
      }
    )
  }

  render(){
    let settings = {
      dots: false,
      infinite: true,
      slidesToShow: 9,
      slidesToScroll: 3,
    };
    let alphabets = "ABCDEFGHIJKLMNOPQRSTUVWXYZ"
    let rows = []
    for (let i = 0; i < alphabets.length; i++) {
      rows.push(
        <div className="owl-item" >
          <div className="item" style={{width: "43px"}} onClick={() => this.alphabetClickHandler(alphabets.charAt(i))} >{alphabets.charAt(i)}</div>
        </div>
      )
    }
    return(
      <div className="box-div" id="directory-box-div" style={{position: "absolute", left: "432px", top: "475px"}}>
        <div className="row">
          <div className="header-banner column small-12" style={{backgroundColor: "#006dd9"}}>Directory A-Z</div>

          <div className="banner-content-div" style={{backgroundColor: "#040404"}}>
            <div className="row collapse">
              <div className="column small-10 small-centered">
                <div id="owl-demo" className="owl-carousel owl-theme" style={{opacity: "1", display: "block"}}>
                  <div className="owl-wrapper-outer">
                    <div className="" style={{}}>
                      <Slider {...settings}>
                        {rows}
                      </Slider>
                    </div>
                  </div>
                </div>
              </div>

              <div className="small-12 pt10">
                <div className="small-10 columns">
                  <input type="text" name="search" className="search_txt" id="search_txt" />
                </div>
                <div className="small-2 columns">
                  <input type="button" className="search-btn" style={{border: "none"}} />
                </div>

                <div className="clearfix"></div>
              </div>

              <div className="directory scrollbar">
                <div  style={{ display: "none", textAlign: "center"}} id="ajaxloader-div" className="d-none">
                  <img src="/images/colleges/laying-bricks-loader-green_2.gif" style={{height: "80px",  width: "80px"}} alt=""/>
                </div>
                {!!this.state.dataToDisplay && 
                  <ul style={{listStyleType: "none", height: "423px", overflowY: "scroll" }}  dangerouslySetInnerHTML={{__html: this.state.dataToDisplay}}>
                  </ul>
                }

                
              </div>
            </div>
          </div>

          <div className="footer-banner" style={{backgroundColor: "#040404", minHeight: "30px"}}>&nbsp;</div>
        </div>
      </div>
    )
  }
}

export default CollegeDirectory;