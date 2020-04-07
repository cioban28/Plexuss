import React, { Component} from 'react'
import Slider from "react-slick";
import { SingleImage, SampleNextArrow, SamplePrevArrow } from './helper'
class LeftPortion extends Component{
    constructor(props){
        super(props);
        this.state={
            currentSlide: 1,
        }
    }

    updateDimensions = () => {
        this.setState({
            onMobile: window.innerWidth < 768,
        })
    }

    componentDidMount(){
        window.addEventListener("resize", this.updateDimensions.bind(this));
        let img =  document.getElementById("lightbox_img");
        let width =img && img.naturalWidth, height =img && img.naturalHeight;
        let widthIsGreaterThanHeight =img && width > height;
        let denominator =img && widthIsGreaterThanHeight ? width : height,
             numerator =img && widthIsGreaterThanHeight ? height : width;
        let percent =img && (numerator/denominator)*100; 
        let { images } = this.props;
        this.setState({
            percent,
            widthIsGreaterThanHeight,
            onMobile: window.innerWidth < 768,
        })
    }

    componentWillUnmount() {
        window.removeEventListener("resize", this.updateDimensions.bind(this));
    }


    render(){
        let { images } = this.props;
        const settings = {
            infinite: false,
            speed: 500,
            autoplaySpeed: 3000,
            slidesToShow: 1,
            slidesToScroll: 1,
            nextArrow: <SampleNextArrow />,
            prevArrow: <SamplePrevArrow />
        };

        let IMAGES = ''
        if(images){
            IMAGES = images.map((image, index) =>
                <SingleImage onMobile={this.state.onMobile} widthIsGreaterThanHeight={this.state.widthIsGreaterThanHeight} percent={this.state.percent}  image={image} key={index}/>
            );
        }
        return(
            <div className="left_portion_banner">
                <Slider
                    afterChange={
                        (currentSlide) => {
                            this.setState({ currentSlide: currentSlide + 1 })
                        }
                    }
                    {...settings}
                >
                    {IMAGES}
                </Slider>
                {
                    images.length > 1 &&
                    <div className="images_count">{this.state.currentSlide} {'/'} {images.length}</div>
                }

            </div>
        )
    }
}
export default LeftPortion;
