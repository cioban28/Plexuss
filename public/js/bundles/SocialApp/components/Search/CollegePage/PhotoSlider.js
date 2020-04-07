import React, { Component } from 'react';
import Slider from "react-slick";
import './styles/styles.scss';


class PhotoSlider extends Component {
  constructor(props) {
    super(props);

    this.settings = {
      dots: false,
      infinite: true,
      speed: 500,
      slidesToShow: 1,
      slidesToScroll: 1,
      arrows: false,
    };

    this.handlePrevArrowClick = this.handlePrevArrowClick.bind(this);
    this.handleNextArrowClick = this.handleNextArrowClick.bind(this);
  }

  handlePrevArrowClick() {
    this.slider.slickPrev();
  }

  handleNextArrowClick() {
    this.slider.slickNext();
  }

  render() {
    const { collegeMedia } = this.props;

    return (
      <div className='college-slider-cont'>
        <div className='slider-arrow arrow-prev' onClick={this.handlePrevArrowClick}>
          <img src='/social/images/post/left/noun_Arrow_1830603_000000.png' />
        </div>
        <Slider ref={c => (this.slider = c)} {...this.settings}>
        {
          collegeMedia.length && collegeMedia.map((media, index) => (
            <div key={index}>
              <img src={`https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/${media.url}`} />
            </div>
          ))
        }
        </Slider>
        <div className='slider-arrow arrow-next' onClick={this.handleNextArrowClick}>
          <img src='/social/images/post/right/noun_Arrow_1830603_000000.png' />
        </div>
      </div>
    )
  }

}

export default PhotoSlider;

