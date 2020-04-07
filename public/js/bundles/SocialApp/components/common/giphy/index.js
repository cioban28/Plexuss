import React from 'react';
import Masonry from 'masonry-layout';
import imagesLoaded from 'imagesloaded';
import './styles.scss';

const GIPHY = {
    base_url: "https://api.giphy.com/v1/gifs/",
    query: ["search", "trending", "random", "translate"],
    api_key: "1nQqARu4OFYQUFDNETrYe5L4VaRahKRg",
    offset: 0,
    rating: "PG",
}

class InputKeyword extends React.Component {
  constructor() {
    super();
    this.handleChange = this.handleChange.bind(this);
  }
  _debounce(func, wait, immediate) {
    let timeout;
    return function() {
      let context = this,
        args = arguments;
      let callNow = immediate && !timeout;
      let _delay = function() {
        timeout = null;
        if(!immediate) {
          func.apply(context, args);
        }
      }
      clearTimeout(timeout);
      timeout = setTimeout(_delay, wait);
      if(callNow) func.apply(context, args);
    }
  }
  handleChange() {
    this.props.onKeywordChange(this.searchKeyword.value);
  }
  render() {
    return(
      <div className="giphy__input">
        <img src="/social/images/search.svg"/>
        <input
          type="text"
          placeholder="Search gif..."
          ref={(ref) => this.searchKeyword = ref}
          onChange={this._debounce(this.handleChange, 800, false)}
        />
      </div>
    )
  }
}

class ImageList extends React.Component {
  componentDidUpdate() {
    let len = this.props.imageslist.length;
    if(len) {
      let grid = document.querySelector('.giphy__list');

      var msnry = new Masonry( grid, {
        itemSelector: '.giphy__item',
        columnWidth: '.giphy__item',
        percentPosition: true
      });
      imagesLoaded(grid).on('progress', function() {
        msnry.layout();
      });
    }
  }
  render() {
    var setImageSrc = this.props.setImageSrc;
    let images = [],
      returnHTML;
    this.props.imageslist.map((item) => {
      let key = item.id.toString(),
        src = item.images.fixed_width.url,
        previewImg = item.images.original_still.url;

      images.push(<Image key={key} src={src} setImageSrc={setImageSrc} preview={previewImg}/>);
    });
    if(images.length) {
      returnHTML = <ul className="giphy__list">{images}</ul>;
    } else {
      if(this.props.firstLoad) {
        // Don't show 'No result' in first-load stage
        returnHTML = <div></div>
      } else {
        returnHTML = <div className="error">No result!</div>;
      }
    }
    return(returnHTML);
  }
}

class Image extends React.Component{
  render(){
    const { src, setImageSrc, preview } = this.props;
    return(
      <li onClick={() => setImageSrc(src, preview)} className="giphy__item"><img src={src} /></li>
      )
  }
}

class Giphy extends React.Component {
  constructor() {
    super();
    this.state = {
      data: [],
      firstLoad: true,
      currentKeyword: '',
      currentOffset: 0,
    }
    this.handleKeywordChange = this.handleKeywordChange.bind(this);
    this.createAjax = this.createAjax.bind(this);
  }
  createAjax(keyword, firstLoad, page) {
    let self = this;
    this.setState({currentKeyword: keyword, currentOffset: page})
    if(page === 0){this.setState({currentOffset: 0, data: []})}
    let url = `${GIPHY.base_url}${GIPHY.query[0]}?api_key=${GIPHY.api_key}&limit=${GIPHY.limit}&offset=${page}`
    keyword = encodeURI(keyword);
    let requestURL = `${url}&q=${keyword}&rating=${GIPHY.rating}`;
    // Ajax
    const xhr = new XMLHttpRequest();
    xhr.open('GET', requestURL);
    xhr.setRequestHeader('Content-Type', 'application/json');
    xhr.onload = function() {
      if(xhr.status === 200) {
        let response = JSON.parse(xhr.responseText).data;
        let temp = self.state.data
        temp = temp.concat(response)
        self.setState({
          data: temp,
          firstLoad: firstLoad
        });
      } else {
        console.log('Request failed.  Returned status of ' + xhr.status);
      }
    }
    xhr.send();
  }
  handleKeywordChange(keyword) {
    this.createAjax(keyword, false, 0);
  }
  componentDidMount() {
    this.createAjax(this.props.firstInput, true, 0);
  }
  render() {
    var setImageSrc = this.props.setImageSrc;
    const {closeGif} = this.props
    return(
      <div className="giphy__container">
        <div className="close-button" onClick={closeGif}>&#10005;</div>
        <InputKeyword firstInput={this.props.firstInput} onKeywordChange={this.handleKeywordChange} />
        <ImageList imageslist={this.state.data} firstLoad={this.state.firstLoad} setImageSrc={setImageSrc}/>
        <div className="footer load-more" onClick={() => this.createAjax(this.state.currentKeyword, false, this.state.currentOffset+25)}>Load More...</div>
        <div className="footer"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/PoweredBy_200px-White_HorizText.png"/></div>
      </div>
    );
  }
}
export default Giphy;
