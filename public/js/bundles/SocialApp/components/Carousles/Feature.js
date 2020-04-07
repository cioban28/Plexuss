import React, { Component } from 'react';
import { connect } from 'react-redux'
import Slider from 'react-slick'
import './feature.scss'
import NextArrow from './Arrow/Next'
import PrevArrow from './Arrow/Prev'

class Feature extends Component{
    constructor(props){
        super(props);

        this.state = {
            items: [
                {'id':'sprite-one',
                'url':'https://startups.microsoft.com/en-us/blog/from-discovery-to-selection-announcing-the-seattle-accelerators-third-batch/'},
                {'id':'sprite-three',
                'url':'http://www.geekwire.com/2016/microsoft-seattle-accelerator-startups/'},
                {'id':'sprite-four',
                'url':'http://tech.co/41-startups-share-motivate-teams-2015-04'},
                {'id':'sprite-five',
                'url':'http://www.bethesdamagazine.com/Bethesda-Beat/2015/Bethesda-Named-One-of-the-Best-Cities-for-College-Grads/'},
                {'id':'sprite-seven',
                'url':'http://startupbeat.com/2015/05/27/u-s-college-comparison-and-recruitment-website-plexuss-com-now-out-of-beta-testing/'},
            ],
            is_display: false,
        }
    }

    next = (currentSlide, slideCount) => {
        this.slider.slickNext();
    }

    prev = () => {
        this.slider.slickPrev();
    }

    setArrowDisplay = currentSlide => {
        var is_display = false;
        if (currentSlide != 0)
            is_display = true;
        this.setState({is_display: is_display})
    }

  render() {
    const settings = {
        dots: false,
        infinite: false,
        lazyLoad: true,
        speed: 500,
        slidesToShow: 4,
        slidesToScroll: 1,
        initialSlide: 0,
        nextArrow: <NextArrow onNextClick={this.next}/>,
        prevArrow: <PrevArrow styleClassName={this.state.is_display} onPrevClick={this.prev}/>,
        afterChange: currentSlide => this.setArrowDisplay(currentSlide),
        responsive: [
            {
                breakpoint: 767,
                settings: {
                    slidesToShow: 1,
                }
            }
        ]
    }
    return (
        <div className="feature_caresel">
            <div className="feature_title">
            <div className="title_feature">Featured in</div>
            </div>
            <div id="plex-feature" className="colleges_feature">
            <Slider ref={c => (this.slider = c)} {...settings}>
                {this.state.items.map((data, index) => <Feature_com key={index} data={data}/>)}
            </Slider>
            </div>
        </div>
    );
  }
}

class Feature_com extends Component {
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
        return (
            <span>
                <div id={this.props.data.id} className="sprite">
				    <a href={this.props.data.url} target="_blank"></a>
			    </div>
            </span>
        );
    }
}

const mapStateToProps = (state) =>{
  return{
      carousles: state.carousles,
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(Feature);
