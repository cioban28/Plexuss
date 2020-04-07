import React from 'react'
import './styles.scss'
import ShareModal from './ShareModal'
import { Link } from 'react-router-dom'
class Feed extends React.Component {
  constructor(props){
    super(props);
    this.state={
      post: {},
      openModalFlag: false,
      openGiphyFlag: false,
    }
    this.openModal = this.openModal.bind(this);
    this.openGiphyModal = this.openGiphyModal.bind(this);
    this.closeModal = this.closeModal.bind(this);
  }
  openModal(){
    this.setState({openModalFlag: true})
  }
  openGiphyModal(){
    this.setState({
      openModalFlag: true,
      openGiphyFlag: true
    })
  }
  closeModal(){
    this.setState({
      openModalFlag: false,
      openGiphyFlag: false,
    })
  }
  render(){
    const { openModalFlag } = this.state;
    return (
      <div className="feed-block ">
        <div className="row widget-block">
          <div className="large-4 medium-4 small-12 columns padding-0">
            <a data-reveal-id="makePostModal" className="share-content-block" onClick={this.openModal}>
              <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/makepost.svg" />
              <span>
                Make a post
              </span>
            </a>
            {
              openModalFlag &&
              <ShareModal openGiphyFlag={this.state.openGiphyFlag} closeModal={this.closeModal} user={this.props.user}
              editMode={false} post={this.state.post} />
            }
          </div>
          <div className="large-4 medium-4 small-12 columns mbl_none">
            <a data-reveal-id="shareGIFModal" className="share-content-block" onClick={this.openGiphyModal}>
              <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/sharegif.svg" />
              <span>
                Share a GIF
              </span>
            </a>
          </div>
          <div className="large-4 medium-4 small-12 columns mbl_none">
            <Link to={"/social/article-editor"} data-reveal-id="writeArticleModal" className="share-content-block">
              <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/write-article.svg" />
              <span>
                Write an Article
              </span>
            </Link>
          </div>
        </div>
      </div>
    )
  }
}
export default Feed;
