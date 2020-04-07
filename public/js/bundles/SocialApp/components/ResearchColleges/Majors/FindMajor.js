import React, { Component } from 'react'
import Slider from "react-slick";
import axios from 'axios';
import '../styles.scss'
import MajorDepartmentsList from './majorsDepartmentList'
class FindMajor extends Component {
	  state = {
    departments: []
  	}

  componentDidMount() {
  }


  render() {
  	const settings = {
            infinite: true,
            speed: 100,
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true,
            autoplay: true,
            useCSS: false,
						autoplaySpeed: 3000
        };


		return (
	    <div className="row">
				<h1 className="department-headning-div"> Find Your Major </h1>
			    <div className="department-content-div">
			      <div className="row">
			        <div className="column large-6 small-12 main-contnet-major">
			          <p> Get started on your college major search. <br />
			            Explore our fields of study and major guides to learn more about your college major options. These guides offer insight on potential career options and offer advice on how to prepare for a specific major while you are still in high school. </p>
			          <p className="hidden-para-mobile"> Plus, you can easily access and browse through a list of colleges offering that major. <br />
			            The information provided can help you <br />
			            answer the question, <strong>"what major is right for me?"</strong> </p>
			        </div>
			        <div className="column large-6 small-12">
			          <div className="pdept-content pdept-display-container" style={{maxWidth:800}}>
	            		<Slider {...settings}>
										<img className="mySlides" alt="What Major is Right for Me" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/department/what-major-is-right-for-me.jpg" style={{width:'100%'}} />
										<img className="mySlides" alt="Find your Major" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/department/find-your-major.jpg" style={{width:'100%'}} />
										<img className="mySlides" alt="College Major Search" src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/department/college-major-search.jpg" style={{width:'100%'}} />
                	</Slider>
			          </div>
			        </div>
			      </div>
			      {!!this.state.departments && <MajorDepartmentsList departments={this.state.departments}  /> }
			    </div>
			</div>
		)
	}
}

export default FindMajor