// /Dashboard_Editor/index.js
import React, { Component } from 'react'
import { withRouter, Prompt } from 'react-router-dom';
import ReactQuill from 'react-quill'
import Select from 'react-select';
import Attributes from './attributes'
import ArticleImage from './article_image'
import './quill.snow.scss'
import './styles.scss'
import PreviewModal from './Modal/modal'
import { connect } from 'react-redux'
import objectToFormData from 'object-to-formdata'
import { getArticles, updateArticle } from './../../api/article'
// api
import { saveArticle } from './../../api/article'
import { Helmet } from 'react-helmet';
import Modal from 'react-modal';
import NavigationPrompt from "react-router-navigation-prompt";
import imageCompression from 'browser-image-compression';
const tag_options = [
  { label: 'News', value: 'news' },
  { label: 'Ranking', value: 'ranking' },
  { label: 'Admissions', value: 'admissions' },
  { label: 'Sports', value: 'sports' },
  { label: 'Campus Life', value: 'campus_life' },
  { label: 'Paying for College', value: 'paying_for_college' },
  { label: 'Financial Aid.', value: 'financial_add' },
]

var interval_handler = null 

const SaveMessage = ({visible}) => 
              <div>
                <div className={'saved' + (visible ? ' saved-visible' : ' hide')}><p>Saving...</p></div>
                <div className={'saved' + (!visible ? ' saved-visible' : ' hide')}><p>All changes saved!</p></div>
              </div>
            

class Dashboard_Editor extends React.Component {
  constructor(props) {
    super(props);
    this.state = {
      text: '',
      title: '',
      news: false,
      ranking: false,
      admissions: false,
      sports: false,
      campus_life: false,
      paying_for_college: false,
      financial_add: false,
      checked: false,
      likes: 0,
      views: 0,
      comments: 0,
      modal: false,
      files: [],
      article: {},
      imageId: '',
      imagesUpdate: false,
      share_with_id: 1,
      saved: true,
      form_valid: false,
      save_pending: false,
      url: "",
      selectedOption: [],

    }
    this.handleChange = this.handleChange.bind(this);
    this.handleTitle = this.handleTitle.bind(this);
    this.handleCheckBox = this.handleCheckBox.bind(this);
    this.handleTags = this.handleTags.bind(this);
    this.handleShareWith = this.handleShareWith.bind(this);
    this.publishOrDreaftArticle = this.publishOrDreaftArticle.bind(this);
    this.previewModal = this.previewModal.bind(this);
    this.onDrop = this.onDrop.bind(this);
    this.removeImage = this.removeImage.bind(this);
    this.validate = this.validate.bind(this);
    this.foo = this.foo.bind(this);
    this.handleChangeSelect = this.handleChangeSelect.bind(this);
    this.handleTagsMbl = this.handleTagsMbl.bind(this);
  }

  componentDidMount(){
    this.setState({url: this.props.location.pathname})
    let arr = this.props.location.pathname.split('/');
    if(arr.length === 4){
      getArticles();
    }
    interval_handler = setInterval(this.foo, 10000);
    
  }
  componentDidUpdate(prevProps){
    if(prevProps.articles != this.props.articles || this.props.location !== prevProps.location){
      let arr = this.props.location.pathname.split('/');
      let id = -1;
      if(arr.length === 4){
        id = arr[3];
      }
      if(id !== -1){
        let { articles } = this.props;
        let objects = [];
        for (let key in articles) {
          if (articles.hasOwnProperty(key)) objects.push(articles[key]);
        }
        var result = objects.find(obj => obj.id == id)
        let files = [];
        let file ={
          preview: result.images && result.images[0] && result.images[0].image_link,
        }
        files.push(file);
        this.setState({
          imageId: result.images && result.images[0] && result.images[0].id,
        })
        if(result){
          this.setState({
            text: result.article_text,
            title: result.article_title,
            checked: result.project_and_publication,
            likes: result.likes.length,
            views: result.views.length,
            comments: result.comments.length,
            files: files,
          })
          result.tags.map((tag, index)=>{
            if(tag.tag_number === 1){
              this.setState({
                news: true,
              })
            }
            if(tag.tag_number === 2){
              this.setState({
                ranking: true,
              })
            }
            if(tag.tag_number === 3){
              this.setState({
                admissions: true,
              })
            }
            if(tag.tag_number === 4){
              this.setState({
                sports: true,
              })
            }
            if(tag.tag_number === 5){
              this.setState({
                campus_life: true,
              })
            }
            if(tag.tag_number === 6){
              this.setState({
                paying_for_college: true,
              })
            }
            if(tag.tag_number === 7){
              this.setState({
                financial_add: true,
              })
            }
          })
        }
      }
    }
  }

  handleChangeSelect(selectedOption){
    this.setState({ selectedOption });
    let newArr = ['news', 'ranking', 'admissions', 'sports', 'campus_life', 'paying_for_college', 'financial_add'];
    let index = -1;
    for(let i=0; i < newArr.length; i++){
      index = selectedOption.findIndex(option => option.value == newArr[i]);
      if(index != -1){
        this.handleTagsMbl(newArr[i],true);
      }else{
        this.handleTagsMbl(newArr[i],false);
      }
    }
  }
  handleTagsMbl(tagsName, flag){
    this.setState({
      saved: false,
    })
    if(tagsName === 'news' && this.state.news != flag){
      this.setState({
        news: flag
      })
    }
    else if(tagsName === 'ranking' && this.state.news != flag){
      this.setState({
        ranking: flag
      })
    }
    else if(tagsName === 'admissions' && this.state.news != flag){
      this.setState({
        admissions: flag
      })
    }
    else if(tagsName === 'sports' && this.state.news != flag){
      this.setState({
        sports: flag
      })
    }
    else if(tagsName === 'campus_life' && this.state.news != flag){
      this.setState({
        campus_life: flag
      })
    }
    else if(tagsName === 'paying_for_college' && this.state.news != flag){
      this.setState({
        paying_for_college: flag
      })
    }
    else if(tagsName === 'financial_add' && this.state.news != flag){
      this.setState({
        financial_add: flag
      })
    }
  }
  handleTags(tagsName){
    this.setState({
        saved: false,
      })
    if(tagsName === 'news'){
      this.setState({
        news: !this.state.news
      })
    }
    else if(tagsName === 'ranking'){
      this.setState({
        ranking: !this.state.ranking
      })
    }
    else if(tagsName === 'admissions'){
      this.setState({
        admissions: !this.state.admissions
      })
    }
    else if(tagsName === 'sports'){
      this.setState({
        sports: !this.state.sports
      })
    }
    else if(tagsName === 'campus_life'){
      this.setState({
        campus_life: !this.state.campus_life
      })
    }
    else if(tagsName === 'paying_for_college'){
      this.setState({
        paying_for_college: !this.state.paying_for_college
      })
    }
    else if(tagsName === 'financial_add'){
      this.setState({
        financial_add: !this.state.financial_add
      })
    }
  }

  handleChange(value) {
    this.setState({saved: false ,text: value})
  }

  handleTitle(e){

    this.setState({saved: false ,  title: e.target.value})
  }

  handleCheckBox(){
    this.setState({
      checked: !this.state.checked,
      saved: false 
    })
  }

  handleShareWith(value) {
    this.setState({ share_with_id: value, saved: false , })
  }

  publishOrDreaftArticle(status, type){
    let errors = this.validate(this.state.title)
    var invalid = (errors.title)
    if (invalid || this.state.save_pending == true) {
      return false;
    }
    this.setState({save_pending: true})
    let arr = window.location.pathname.split('/');
    let id = -1;
    let { user } = this.props;
    let obj = {};
    obj.user_id = user && user.user_id;
    obj.article_title = this.state.title;
    obj.article_text = this.state.text;
    obj.status = status;
    obj.article_tags = [];
    if(this.state.news){
      obj.article_tags.push(1);
    }
    if(this.state.ranking){
      obj.article_tags.push(2);
    }
    if(this.state.admissions){
      obj.article_tags.push(3);
    }
    if(this.state.sports){
      obj.article_tags.push(4);
    }
    if(this.state.campus_life){
      obj.article_tags.push(5);
    }
    if(this.state.paying_for_college){
      obj.article_tags.push(6);
    }
    if(this.state.financial_add){
      obj.article_tags.push(7);
    }
    obj.share_with_id = this.state.share_with_id;
    obj.project_and_publication = this.state.checked;
    obj.is_gif = false;
    obj.gif_link = null;
    obj.is_shared = false;
    obj.original_article_id = null;
    obj.share_count = 0;
    obj.thread_room = 'post:room:';
    obj.user_name = user.fname+' '+user.lname;
    
    if(arr.length === 4 && arr[3] != ""){
      id = arr[3];
    }
    if(id !== -1){

      if(this.state.imagesUpdate){
        obj.article_images = this.state.files;
      }else{
        obj.article_images = null;
      }
      
      obj.image_id = this.state.imageId;
      obj.article_id = id;
      const formData = objectToFormData(obj);
      updateArticle(formData, type)
      .then(()=>{
          getArticles();
        this.setState({ saved: true, save_pending: false})
        if (type == false)
        {
          this.props.history.push('/social/article-dashboard')
        }
        else {
          this.setState({url: window.location.pathname})
        }
      })


    }else{
      if(this.state.imagesUpdate){
        obj.article_images = this.state.files;
      }else{
        obj.article_images = null;
      }
        const formData1 = objectToFormData(obj);
      const errors = this.validate(this.state.title);
      const is_valid = (!errors.title) 
      if (is_valid) {
      saveArticle(formData1, false, type)
        .then(()=>{
          getArticles();
          this.setState({ saved: true, save_pending: false})
          if (type == false)
          {
            this.props.history.push('/home')
          }
          else
          {
            this.setState({url: window.location.pathname})

          }
        })
      }
    }
  }
  onDrop(files) {
    let test_files = this.state.files;
    files.map(file => test_files.push(Object.assign(file, {
      preview: URL.createObjectURL(file)
    })))
    this.setState({
      files: test_files,
      imagesUpdate: true,
      saved: false ,
    });
  }
  onDropRejected(files) {
    const compressionOptions = {
      maxSizeMB: 1,
      maxWidthOrHeight: 1920,
      useWebWorker: true
    };

    imageCompression(files[0], compressionOptions).then(compressedFile => {
      const newFiles = this.state.files;
      newFiles.push(Object.assign(compressedFile, {
        preview: URL.createObjectURL(compressedFile)
      }))
      this.setState({
        files: newFiles,
      });
    }).catch(error => {
      console.log('Error occured while compressing the image', error);
    });
  }
  removeImage(){
      this.setState({
        files: [],
        imagesUpdate: true,
        saved: false ,
      });
  }
  previewModal(){
    this.setState({
      modal: !this.state.modal,
    })
  }

  validate(title) {
    return {
      title: title == "" || title == null,
      };

  }
  componentWillReceiveProps(nextProps) {
    if (nextProps.location.key != this.props.location.key)
    {
      this.setState({
      text: '',
      title: '',
      news: false,
      ranking: false,
      admissions: false,
      sports: false,
      campus_life: false,
      paying_for_college: false,
      financial_add: false,
      checked: false,
      likes: 0,
      views: 0,
      comments: 0,
      modal: false,
      files: [],
      article: {},
      imageId: '',
      imagesUpdate: false,
      share_with_id: 1,
      saved: true,
      form_valid: false,
      save_pending: false,
      url: "",
        });
    }
  }

  foo() {
    if (this.state.saved == false) {
      if (document.getElementById("prompt-modal") == null)
      {
        this.publishOrDreaftArticle(0, true);
      }
    }
  }
  onCancel() {
    
  }
  onConfirm() {
  }
  isActive() {
    
  }
  componentWillUnmount() {
    this.setState({saved: true, save_pending: false})
    clearInterval(interval_handler);
    interval_handler = 0;
    this.state.files.forEach(file => URL.revokeObjectURL(file.preview))
  }
  render(){
    let { user } = this.props;
    const { selectedOption } = this.props;
    let image_link = {
      image_link: this.state.files && this.state.files[0] && this.state.files[0].preview
    };
    let images = [];
    images.push(image_link);
    let obj = {};
    obj.user = user && user;
    obj.article_title = this.state.title;
    obj.article_text = this.state.text;
    obj.images = images;
    const errors = this.validate(this.state.title);
    const is_valid = (!errors.title) 
    this.state.form_valid = is_valid;
    let arr = window.location.pathname.split('/');
    let id = -1;
    if(arr.length === 4){
      id = parseInt(arr[3]);
    }

    return (
      <div>
        <NavigationPrompt
          // beforeConfirm={(clb)=>this.cleanup(clb)} //call the callback function when finished cleaning up
          // Children will be rendered even if props.when is falsey and isActive is false:
          renderIfNotActive={true}
          // Confirm navigation if going to a path that does not start with current path:
          when={!this.state.saved}
        >
          {({ isActive, onCancel, onConfirm }) => {
            if (isActive) {
              return (
                <Modal isOpen={!this.state.saved} className="delete-modal">
                  <div className="delete_article_container" id="prompt-modal">
                    <div className="modal_heading">
                      Are you sure you want to leave this page?
                    </div>
                    <div className="delete_article_block">
                      <div className="modal_message">
                        Any unsaved changes will be lost if you leave the page.
                      </div>
                    </div>
                    <div className="action_button cancel" onClick={onCancel}> Stay on this page </div>
                    <div className="action_button delete" onClick={onConfirm}> Leave this page </div>
                  </div>
                </Modal>
              );
            }
            return;
          }}
        </NavigationPrompt>;

        <Helmet>
          <title>Colleges Articles | College Recruiting Academic Network | Plexuss.com</title>
          <meta name="description" content="Write and share your article." />
          <meta name="keywords" content="Colleges Articles" />
        </Helmet>
        <div id="article_editor_content">
          <Attributes likes={this.state.likes} comments={this.state.comments} views={this.state.views}/>
          <div className="subject-area">
            <div className="block-headings">
                <span>
                    Title
                </span>
            </div>
            
            <input  name="title" style={{borderRadius: '6px !important'}} placeholder={"(Required)"} value={this.state.title} onChange={this.handleTitle}/>
            
            { !!errors.title && 
              <small className="error">*Please enter the title for the article</small>
            }
          </div>
          <ArticleImage removeImage={this.removeImage} onDrop={this.onDrop} onDropRejected={this.onDropRejected} files={this.state.files}/>
          { false && 
            <small className="error">*Please upload image for article</small>
          }
          
          <div className="editor-area">
            <ReactQuill value={this.state.text} placeholder={"(Required)"} onChange={this.handleChange} />
          </div>
          { false && 
              <small className="error">*Please enter text for article</small>
            }
          <div className="tags-area">
            <div className="block-headings">
              <span>
                Tag this article
              </span>
            </div>
            <div className="tag-options">
              <ul className="tag-list">
                <Tags imgSrc={'/social/images/Icons/tags/news.png'} activeImgSrc={'/social/images/Icons/tags-white/news.png'} text={'News'} flag={this.state.news} handleTags={this.handleTags} name={'news'} />
                <Tags imgSrc={'/social/images/Icons/tags/ranking.png'} activeImgSrc={'/social/images/Icons/tags-white/ranking.png'} text={'Ranking'} flag={this.state.ranking} handleTags={this.handleTags} name={'ranking'} />
                <Tags imgSrc={'/social/images/Icons/tags/admissions.png'} activeImgSrc={'/social/images/Icons/tags-white/admissions.png'} text={'Admissions'} flag={this.state.admissions} handleTags={this.handleTags} name={'admissions'} />
                <Tags imgSrc={'/social/images/Icons/tags/sports.png'} activeImgSrc={'/social/images/Icons/tags-white/sports.png'} text={'Sports'} flag={this.state.sports} handleTags={this.handleTags} name={'sports'} />
                <Tags imgSrc={'/social/images/Icons/tags/campus life.png'} activeImgSrc={'/social/images/Icons/tags-white/campus life.png'} text={'Campus Life'} flag={this.state.campus_life} handleTags={this.handleTags} name={'campus_life'} />
                <Tags imgSrc={'/social/images/Icons/tags/payingforcollege.png'} activeImgSrc={'/social/images/Icons/tags-white/payingforcollege.png'} text={'Paying for College'} flag={this.state.paying_for_college} handleTags={this.handleTags} name={'paying_for_college'} />
                <Tags imgSrc={'/social/images/Icons/tags/financial.png'} activeImgSrc={'/social/images/Icons/tags-white/financial.png'} text={'Financial Aid'} flag={this.state.financial_add} handleTags={this.handleTags} name={'financial_add'} />
              </ul>
            </div>
            <div className="tag_options_for_mobile">
              <Select
                value={selectedOption}
                onChange={this.handleChangeSelect}
                options={tag_options}
                isMulti={true}
              />
            </div>
            <div className="select-tag">
              <input type="checkbox" onChange={this.handleCheckBox} defaultChecked={this.state.checked}/>
              <span>Add to my Projects/Publications</span>
            </div>
            <div className="action-buttons">
              <div>
                <button className="draft" disabled={!is_valid} onClick={() => this.publishOrDreaftArticle(0, false)}>SAVE AS A DRAFT</button>
              </div>
              <div>
                <button className="draft" onClick={this.previewModal}>PREVIEW</button>
                <button disabled={!is_valid} className="publish" onClick={() =>this.publishOrDreaftArticle(1, false)}>PUBLISH</button>
              </div>
            </div>
            <div className="mobile-action-buttons">
              <div className="row">
                <div className="small-6 columns">
                  <button className="draft" disabled={!is_valid} onClick={() => this.publishOrDreaftArticle(0,  false)}>SAVE AS A DRAFT</button>
                </div>
                <div className="small-6 columns">
                  <button className="draft" onClick={this.previewModal}>PREVIEW</button>
                </div>
              </div>
              <div className="row">
                <div className="small-6 columns">
                  <button disabled={!is_valid} className="publish" onClick={() =>this.publishOrDreaftArticle(1, false)}>PUBLISH</button>
                </div>
              </div>
            </div>
          </div>
        </div>
        <SaveMessage visible={this.state.save_pending} />
       
        {
          this.state.modal &&
            <PreviewModal article={obj} previewModal={this.previewModal} modal={this.state.modal} publish={this.publishOrDreaftArticle} privacy={this.state.share_with_id} handleShareWith={this.handleShareWith} disablePublish={this.state.text === '' || this.state.title === '' || this.state.files.length === 0}/>
        }
      </div>
    );
  }
}

class Tags extends Component{
  render(){
    let { imgSrc, activeImgSrc, text, flag, handleTags, name } = this.props;
    return(
      <li onClick={() => handleTags(name)}>
        <a className={"button "+ (flag ? "green_tag_button" : "tag_button")}>
          <img src={(flag ? activeImgSrc : imgSrc)} />
          <span>{text}</span>
        </a>
      </li>
    )
  }
}

const mapStateToProps = (state) =>{
  return{
      user: state.user.data,
      articles: state.articles && state.articles.userArticles,
  }
}
export default  connect(mapStateToProps, null)(withRouter(Dashboard_Editor));
