import React, { Component } from 'react';
import '../AllPosts/styles.scss';
import { connect } from 'react-redux';
import { Link } from 'react-router';
import SocialNewsfeed from '../index.jsx';
import Modal from 'react-modal';
import { PostInsights } from '../PostInsights/index.jsx';
import axios from 'axios';
import PostsTable from '../PostsTable/index.jsx';


const pageSize = 10;

class PostsCommonLayout extends Component {
  constructor(props) {
    super(props);
    this.state={
      clickedPostIndex: '',
      pageNumber: 1,
      linkPreview: '',
    }
    this.handleStartDateCalendarClick = this.handleStartDateCalendarClick.bind(this);
    this.handleEndDateCalendarClick = this.handleEndDateCalendarClick.bind(this);
    this.handleSearchSubmit = this.handleSearchSubmit.bind(this);
    this.handleStatusToggle = this.handleStatusToggle.bind(this);
    this.handleCloseModal = this.handleCloseModal.bind(this);
    this.handleDuplicatePostClick = this.handleDuplicatePostClick.bind(this);
    this.handleCheckboxClick = this.handleCheckboxClick.bind(this);
    this.handleNextPageClick = this.handleNextPageClick.bind(this);
    this.handlePreviousPageClick = this.handlePreviousPageClick.bind(this);
  }

  componentWillMount() {
    !this.props.posts.data.length && this.props.getPosts(this.state.pageNumber);
  }

  handleStartDateCalendarClick() {
    this.startDateEl.focus();
  }

  handleEndDateCalendarClick() {
    this.endDateEl.focus();
  }

  handleSearchSubmit(e) {
    e.preventDefault();
  }

  handleCheckboxClick(e) {
    e.stopPropagation();
  }

  handleStatusToggle(index, value, event) {
    event.stopPropagation();
    // const postData = [...this.state.data, ];
    // postData[index] = { ...postData[index], enabled: !postData[index].enabled };

    // this.setState({ data: [...postData] });
  }

  handlePostRowClick(index) {
    this.setState({ clickedPostIndex: index + (this.state.pageNumber-1)*pageSize }, () => {
      this.props.openModal();
    });

    const sharedLink =  this.props.posts.data.length && this.props.posts.data[index].shared_link;
    if(!!sharedLink) {
      axios({
        method: 'get',
        url: `/social/link-preview-info?url=${sharedLink}`,
      })
      .then(res => {
        if(res.statusText === 'OK'){
          this.setState({ linkPreview: res.data, });
        }
      })
      .catch(error => {
      });
    } else {
      !!this.state.linkPreview && this.setState({ linkPreview: '' });
    }
  }

  handleCloseModal() {
    this.props.closeModal();
  }

  handleDuplicatePostClick() {
    this.props.closeModal();
  }

  handleNextPageClick() {
    const { pageNumber } = this.state;
    this.props.getPosts(pageNumber+1);
    this.setState({ pageNumber: pageNumber+1 });
  }

  handlePreviousPageClick() {
    this.setState({ pageNumber: this.state.pageNumber-1 });
  }

  render() {
    const { clickedPostIndex, linkPreview, pageNumber } = this.state;
    const { isOpen, closeModal, posts, heading, showPostCreateButton } = this.props;

    return (
      <SocialNewsfeed>
        {
          isOpen && <Modal
            isOpen={isOpen}
            onRequestClose={closeModal}
            className='post-insights-modal'
          >
            <PostInsights
              post={posts.data[clickedPostIndex]}
              linkPreview={linkPreview}
              handleCloseModal={this.handleCloseModal}
              handleDuplicatePostClick={this.handleDuplicatePostClick}
            />
          </Modal>
        }
        <div className='all-posts-cont'>
          <header className='sales-header'>
            <div className='first-section'>
              <h1>{ heading }</h1>
              {
                !!showPostCreateButton && <Link to='/sales/social-newsfeed/new'>
                  <button className='btn-create-post'>
                    <i className="fas fa-plus"></i>Create a new post
                  </button>
                </Link>
              }
              {
                !showPostCreateButton && <div />
              }
            </div>
            <form onSubmit={this.handleSearchSubmit} className='search-form'>
              <div className='search-input'>
                <img className='input-icon' src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/sales/search.svg' />
                <input type='text' placeholder='Search Posts' />
              </div>
              <div className='search-input date-input'>
                <img onClick={this.handleStartDateCalendarClick} className='input-icon' src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/sales/calendar.svg' />
                <input ref={e => this.startDateEl = e} name='start-date' type='date' />
              </div>
              <div className='search-input date-input'>
                <img onClick={this.handleEndDateCalendarClick} className='input-icon' src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social-assets/sales/calendar.svg' />
                <input ref={e => this.endDateEl = e} name='end-date' type='date' />
              </div>
              <button>Search</button>
            </form>
          </header>
          <PostsTable
            heading= {heading}
            data={posts.data.slice((pageNumber-1)*pageSize, pageNumber*pageSize-1)}
            pageSize={pageSize}
            totalPages={posts.last_page}
            pageNumber={pageNumber}
            handlePreviousPageClick={this.handlePreviousPageClick}
            handleNextPageClick={this.handleNextPageClick}
            handlePostRowClick={this.handlePostRowClick.bind(this)}
            handleCheckboxClick={this.handleCheckboxClick}
            handleStatusToggle={this.handleStatusToggle}
          />
        </div>
      </SocialNewsfeed>
    )
  }
}

const mapStateToProps = state => ({
  isOpen: state.modal.isOpen,
})

const mapDispatchToProps = dispatch => ({
  openModal: () => { dispatch({ type: 'OPEN_MODAL' }) },
  closeModal: () => { dispatch({ type: 'CLOSE_MODAL' }) },
})

export default connect(mapStateToProps, mapDispatchToProps)(PostsCommonLayout);
