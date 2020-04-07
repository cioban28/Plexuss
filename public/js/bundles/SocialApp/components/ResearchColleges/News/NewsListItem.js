import React, { Component } from 'react'
import './styles.scss'
import {Link} from 'react-router-dom'
import { connect } from 'react-redux';
import { VideoLightBox } from './VideoLightBox';
import MultiClamp from 'react-multi-clamp';


class NewsListItem extends Component {
  constructor(props){
    super(props);

    this.state = {
      videoModals: {},
    };

    this.closeVideoModal = this.closeVideoModal.bind(this);
  }

  handleShowVideoClick(index) {
    const videoModals = {...this.state.videoModals};
    videoModals[index] = true;
    this.setState({ videoModals });
  }

  closeVideoModal(index) {
    const videoModals = {...this.state.videoModals}
    videoModals[index] = false;
    this.setState({ videoModals });
  }

  render(){
    const {newsList, news, newsListNo} = this.props;
    const { videoModals } = this.state;
    const mainUrl = "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/"

    return (
      <div className="column small-12 medium-4 large-4 newssitem" key={"index"} >
        {!!newsList && newsList.map( (listItem, index) => (
            <div className="newss-article-box newss-article-box-new-styling row collapse" data-articlepin={listItem.subcatSlug} key={index}>
              {
                videoModals[listItem.id] && <VideoLightBox newsId={listItem.id} closeModal={this.closeVideoModal} />
              }
              <div className="small-8 small-push-4 medium-12 medium-reset-order column">
                <div className="row collapse" style={{marginBottom: "3%"}}>
                  {
                    listItem.has_video == 1
                    ? <div className="column newssBoxTitle newssBoxTitle-new-styling" onClick={this.handleShowVideoClick.bind(this, listItem.id)}>
                        <a className='newss-title-link'>
                          <MultiClamp ellipsis="..." clamp={2}>{listItem.title}</MultiClamp>
                        </a>
                      </div>
                    : <div className="column newssBoxTitle newssBoxTitle-new-styling">
                        <Link to={`/news/article/${listItem.slug}`} className='newss-title-link'>
                          <MultiClamp ellipsis="..." clamp={2}>{listItem.title}</MultiClamp>
                        </Link>
                      </div>
                  }
                </div>
              </div>

              <div className="small-4 small-pull-8 column medium-12 medium-reset-order">
                <div className="bc-image text-center" >
                  {
                    listItem.has_video == 1
                      ? <div className='layer-container' onClick={this.handleShowVideoClick.bind(this, listItem.id)}>
                           <img className="newss-img-title" src={listItem.img_sm} title={listItem.title} alt={listItem.title} />
                           <div className="layer">
                             <div className="playbtn text-center">
                               <div className="play-arrow">
                               </div>
                             </div>
                           </div>
                        </div>
                      : <Link to={`/news/article/${listItem.slug}`}>
                           <img className="newss-img-title" src={`${mainUrl}${listItem.img_lg}`} title={listItem.title} alt={listItem.title}/>
                        </Link>
                  }
                  <div className="category-badge hide-for-small-only">{listItem.subcat}
                  </div>
                </div>
              </div>

              <div className="show-for-desktop-only small-8 medium-12 column">
                <div className="row collapse">
                  <div className="column newssBoxdescription">
                    <div className="bc-image text-center"></div>
                      <Link to={`/news/article/${listItem.slug}`}>
                        <MultiClamp ellipsis="..." clamp={2}>{listItem.content.replace(/<(.|\n)*?>/g, '')}</MultiClamp>
                      </Link>
                  </div>
                </div>
              </div>
            </div>
          )
        )
      }
      </div>
    );
  }
}

const mapStateToProps = state => ({
  isOpen: state.modal.isOpen,
});

export default connect(mapStateToProps, null)(NewsListItem);
