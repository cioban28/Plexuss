import React, { Component } from 'react';
import { connect } from 'react-redux'
import { SpinningBubbles } from '../common/loader/loader'
import { getVirtualTours } from './../../api/carousles'
import Slider from 'react-slick'
import './styles.scss'
import NextArrow from './Arrow/Next'
import PrevArrow from './Arrow/Prev'

var styles = {
    green: {
        'border': 'solid 1px #2AC56C',
        'backgroundColor': '#2AC56C',
        'borderRadius': '6px',
        'color': 'white',
    },
    none: {
        'border': 'solid 1px #202020',
        'borderRadius': '6px',
    }
}
class Virtual extends Component{
    constructor(props){
        super(props);

        this.state = {
            over_scroll: false,
            is_display: false,
        }
        this.getDatas = this.getDatas.bind(this)
        this.scrollEvent = this.scrollEvent.bind(this)
    }

    getDatas(page) {
        getVirtualTours(page)
    }

    scrollEvent() {
        if (this.college.getBoundingClientRect().y < window.innerHeight / 2)
            this.setState({over_scroll: true})
        else
            this.setState({over_scroll: false})
    }

    next = (currentSlide, slideCount) => {
        if (currentSlide + 9 > slideCount 
            && this.props.carousles.loading.virtual
            && this.props.carousles.status.virtual)
        {
            this.getDatas(this.props.carousles.start.virtual)
        }
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

    componentDidMount(){
        if (this.props.carousles.datas.virtual.length <= 0)
            this.getDatas(this.props.carousles.start.virtual)
        window.addEventListener('scroll', this.scrollEvent)
    }

    componentWillUnmount() {
        window.removeEventListener('scroll', this.scrollEvent)
    }

    render() {
        var divStyle = {}
        divStyle = this.state.over_scroll ? styles.green : styles.none;
        const settings = {
            dots: false,
            infinite: false,
            lazyLoad: true,
            speed: 500,
            slidesToShow: 4,
            slidesToScroll: 1,
            initialSlide: 0,
            nextArrow: <NextArrow onNextClick={this.next}/>,
            prevArrow: <PrevArrow styleClassName = {this.state.is_display} onPrevClick={this.prev}/>,
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
            <span>
                <div className="title_college" ref={(c)=>this.college = c}>
                <div className="college_title" style={divStyle}>College virtual tours</div>
                </div>
                {this.props.carousles.datas.virtual.length > 0 ? (
                    <div className="colleges">
                    <Slider ref={c => (this.slider = c)} {...settings}>
                        {Object.entries(this.props.carousles.datas.virtual).map((data, index) => <Tours key={index} data={data}/>)}
                    </Slider>
                    </div>
                ) : (<span>{!this.props.carousles.loading.virtual ? null :
                    (<div className="loading"><SpinningBubbles/></div>)
                }</span>)}
            </span>
        );
    }
}

class Tours extends Component {
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
        var pin_url = 'url(https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/overview_images/carousel_images/' + this.props.data[1].img_url + ')';
        var pin_style = {'backgroundImage': pin_url};
        return (
            <span>
                <div className="college_com">
                    <div className="item effect-sadie text-center pin-back-img" style={pin_style}>
                        <div className="college-pin-virtualtour-school-name">
                            <div className="vt-school-name text-center">{this.props.data[1].school_name}</div>
                        </div>
                        
                        <figure>
                            <figcaption>
                                <div className="row college-pin-footer-container college-news-pin-container border-radius green-back">
                                    <a href={`/college/${this.props.data[1]['slug']}`} className="column small-4 news-pin-inner-container">
                                        <div className="text-center news-pin-hover-icon-img">
                                            <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/frontpage/tour-icon-for-hover.png" alt=""/>
                                        </div>
                                        <div className="text-center news-pin-hover-desc">
                                            <div className="text-white college-pin-link">VIEW TOUR</div>
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
      carousles: state.carousles,
  }
}

const mapDispatchToProps = (dispatch) => {
  return {
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(Virtual);
