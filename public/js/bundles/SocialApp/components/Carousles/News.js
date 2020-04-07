import React, { Component } from 'react';
import { connect } from 'react-redux'
import TimeAgo from 'react-timeago'
import { SpinningBubbles } from '../common/loader/loader'
import { getCollegeNews } from './../../api/carousles'
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
class News extends Component{
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
        getCollegeNews(page)
    }

    scrollEvent() {
        if (this.college.getBoundingClientRect().y < window.innerHeight / 2)
            this.setState({over_scroll: true})
        else
            this.setState({over_scroll: false})
    }

    next = (currentSlide, slideCount) => {
        if (currentSlide + 9 > slideCount 
            && this.props.carousles.loading.news
            && this.props.carousles.status.news)
        {
            this.getDatas(this.props.carousles.start.news)
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
        if (this.props.carousles.datas.news.length <= 0)
            this.getDatas(this.props.carousles.start.news)
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
                <div className="college_title" style={divStyle}>College news</div>
                </div>
                {this.props.carousles.datas.news.length > 0 ? (
                    <div className="colleges">
                    <Slider ref={c => (this.slider = c)} {...settings}>
                        {Object.entries(this.props.carousles.datas.news).map((data,index) => <New_Com key={index} data={data}/>)}
                    </Slider>
                    </div>
                ) : (<span>{!this.props.carousles.loading.news ? null :
                    (<div className="loading"><SpinningBubbles/></div>)
                }</span>)}
            </span>
        );
    }
}

class New_Com extends Component {
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
        var news_url = 'url("https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/' + this.props.data[1].img_url + '")';
        var news_style = {'backgroundImage': news_url};
        var avatar_url = 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/' + this.props.data[1].author_img;
        return (
            <span>
                <a href={`/news/article/${this.props.data[1]['slug']}`} className="college_com news">
                    <div className="news-back-img" style={news_style}></div>
                    <div className="news-footer">
                        <div className="news-title-text">{this.props.data[1].title}</div>
                        <div className="news-footer-author">
                            <img className="avatar" src={avatar_url}/>
                            <div className="author-name">{this.props.data[1].author}{' - '}<TimeAgo date={this.props.data[1].created_at.date} /></div>
                        </div>
                    </div>
                </a>
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

export default connect(mapStateToProps, mapDispatchToProps)(News);
