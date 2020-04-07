import React, { Component } from 'react';
import { connect } from 'react-redux'
import { SpinningBubbles } from '../common/loader/loader'
import Colleges from './Colleges'
import { getTopRankings } from './../../api/carousles'
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
class Ranking extends Component{
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
        getTopRankings(page)
    }

    scrollEvent() {
        if (this.college.getBoundingClientRect().y < window.innerHeight / 2)
            this.setState({over_scroll: true})
        else
            this.setState({over_scroll: false})
    }

    next = (currentSlide, slideCount) => {
        if (currentSlide + 9 > slideCount 
            && this.props.carousles.loading.ranking
            && this.props.carousles.status.ranking)
        {
            this.getDatas(this.props.carousles.start.ranking)
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
        if (this.props.carousles.datas.ranking.length <= 0)
            this.getDatas(this.props.carousles.start.ranking)
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
            speed: 500,
            lazyLoad: true,
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
                    <div className="college_title" style={divStyle}>Top ranked colleges</div>
                    <a className="rank-link" href="/ranking">See all</a>
                </div>
                {this.props.carousles.datas.ranking.length > 0 ? (
                    <div className="colleges">
                    <Slider ref={c => (this.slider = c)} {...settings}>
                        {Object.entries(this.props.carousles.datas.ranking).map((data, index) => <Colleges key={index} data={data}/>)}
                    </Slider>
                    </div>
                ) : (<span>{!this.props.carousles.loading.ranking ? null :
                    (<div className="loading"><SpinningBubbles/></div>)
                }</span>)}
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

export default connect(mapStateToProps, mapDispatchToProps)(Ranking);
