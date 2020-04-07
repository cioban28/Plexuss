import React, { Component } from 'react';
import { connect } from 'react-redux';
import InfiniteScroll from 'react-infinite-scroller';
import axios from 'axios';

class ShowingModal extends Component {
  constructor(props) {
    super(props)
    this.state = {
      tab: this.props.tab,
      start: 0,
      likes: [],
      shares: [],
      comments: [],
      hasMoreLikeItems: true,
      hasMoreShareItems: true,
      hasMoreCommentItems: true,
    }
  }
  componentWillReceiveProps(nextProps) {
    if (this.props.tab !== nextProps.tab) {
      this.setState({tab: nextProps.tab})
    }
  }

  getPostLikes = async (page) => {
    this.setState({hasMoreLikeItems: false, hasMoreShareItems: false, hasMoreCommentItems: false})
    var data = {
      post_comment_id: this.props.post.likes[0].post_comment_id,
      post_id: this.props.post.likes[0].post_id,
      social_article_id: this.props.post.likes[0].social_article_id,
    }
    axios({
      method: 'post',
      url: '/social/getPostLikes?page='+page,
      data: data,
      headers: {'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')},
    })
    .then(res => {
      var like=true, share=true, comment=true
      if (res.data.likes.data.length < 10) {
        like = false
      }
      if (res.data.shares.data.length < 10) {
        share = false
      }
      if (res.data.comments.data.length < 10) {
        comment = false
      }
      let newLikes = Object.assign([], this.state.likes);
      newLikes = [...newLikes,  ...res.data.likes.data ];

      let newComments = Object.assign([], this.state.comments);
      newComments = [...newComments,  ...res.data.comments.data ];

      let newShares = Object.assign([], this.state.shares);
      newShares = [...newShares,  ...res.data.shares.data ];

      this.setState({
        hasMoreLikeItems: like, 
        hasMoreShareItems: share, 
        hasMoreCommentItems: comment,
        start: page+1, 
        likes: newLikes,
        shares: newShares,
        comments: newComments,
      })
    })
    .catch(error => {
    })
  }

  render() {
    const { tab } = this.state
    const { post } = this.props
    return (
      <div className="modal-container">
        <div className="close-button" onClick={() => this.props.onClose()}>&#10005;</div>
        <div className="tabs">
          <ol className="tab-list">
            <li className={"tab-list-item "+ (tab === 'like' ? "tab-list-active" : "")} onClick={()=>{this.setState({tab:'like'})}}>
              <img src={ post.likes.length > 0 ? "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Like-active.svg" : "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Like-inactive.svg" } />
              {post.likes.length + ' Liked'}
            </li>
            <li className={"tab-list-item "+ (tab === 'share' ? "tab-list-active" : "")} onClick={()=>{this.setState({tab:'share'})}}>
              <img src={"https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Share.svg"} />
              {post.share_count + ' Shared'}
            </li>
            <li className={"tab-list-item "+ (tab === 'comment' ? "tab-list-active" : "")} onClick={()=>{this.setState({tab:'comment'})}}>
              <img src={"https://s3-us-west-2.amazonaws.com/asset.plexuss.com/mobile/icons/Comment.svg"} />
              {post.comments.length + ' Commented'}
            </li>
          </ol>
        </div>
        <div className="tabs">
        {this.state.tab==='like' && post.likes.length > 0 ? 
        <InfiniteScroll
              pageStart={this.state.start}
              loadMore={this.getPostLikes}
              hasMore={this.state.hasMoreLikeItems}
            >
            <Cards cards={this.state.likes}/>
          </InfiniteScroll> :
        this.state.tab==='share' && post.share_count > 0?
        <InfiniteScroll
              pageStart={this.state.start}
              loadMore={this.getPostLikes}
              hasMore={this.state.hasMoreShareItems}
            >
            <Cards cards={this.state.shares}/>
          </InfiniteScroll> :
        this.state.tab==='comment' && post.comments.length > 0 ?
        <InfiniteScroll
              pageStart={this.state.start}
              loadMore={this.getPostLikes}
              hasMore={this.state.hasMoreCommentItems}
            >
            <Cards cards={this.state.comments}/>
          </InfiniteScroll> : null}
        </div>
      </div>
    )
  }
}

class Cards extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      cards: this.props.cards
    }
  }
  componentWillReceiveProps(nextProps) {
    if (this.props.cards !== nextProps.cards)
      this.setState({cards: nextProps.cards})
  }
  render() {
    const { cards } = this.state
    return (
      <div className="cards-box">
      {cards.map((card, index)=>(<Card card={card} key={index}/>))}
      </div>
    )
  }
}

class Card extends React.Component {
  constructor(props) {
    super(props)
    this.state = {
      card: this.props.card
    }
  }
  componentWillReceiveProps(nextProps) {
    if (this.props.card !== nextProps.card)
      this.setState({card: nextProps.card})
  }
  render() {
    const { card } = this.state
    return (
      <div className="student-card">
        <div className="one-card">
          <div className="user-infos">
            <div className="user-pic">
              <img src={(card.user && card.user.profile_img_loc) ? 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/'+card.user.profile_img_loc : (card.user && card.user.fname) ? '/social/images/Avatar_Letters/'+card.user.fname.charAt(0).toUpperCase()+'.svg' : '/social/images/Avatar_Letters/P.svg'} alt=""/>
            </div>
            <div className="user-info">
              <div className="name-user">{card.user && card.user.fname} {card.user && card.user.lname+" "}  <div className={"flag flag-"+ (!!card.user && !!card.user.country && card.user.country.country_code.toLowerCase())}></div></div>
              <div className="user-college">{card.user && card.user.college ? card.user.college.school_name : card.user && card.user.highschool ? card.user.highschool.school_name : ''}</div>
              <div className="user-title">{card.user && card.user.is_student === 1 ? 'Student' : card.user && card.user.is_alumni === 1 ? 'Alumni' : ''}</div>
            </div>
          </div>
          <div className="action-btn">
            {card.friend_status==='Accepted' ? <img alt="" src="/social/images/Icons/message.svg"/> : <div className="connect-btn">{card.friend_status==='Pending' ? 'PENDING' : 'CONNECT'}</div>}
          </div>
        </div>
        <div className="border-line"></div>
      </div>
    )
  }
}

export default connect(null, null)(ShowingModal);