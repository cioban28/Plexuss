import axios from 'axios';
import { connect } from 'react-redux'
import MDSpinner from 'react-md-spinner';
import React, { Component } from 'react';
import renderHTML from 'react-render-html';
import ScrollUpButton from 'react-scroll-up-button';

// import '../../Styles/default.scss';
import './styles.scss';
import ComparisonColumn from './ComparisonColumn';
import ColumnHeader from './ColumnHeader';
import CustomModal from '../Modal/CustomModal';
import Slider from 'react-slick';
// import OwlCarousel from 'react-owl-carousel';
// import 'owl.carousel/dist/assets/owl.carousel.css';
// import 'owl.carousel/dist/assets/owl.theme.default.css';

import { openModal, closeModal } from '../../actions/modal';
import {Helmet} from 'react-helmet'
import cloneDeep from 'lodash/cloneDeep';
const _ = {
  cloneDeep: cloneDeep
}

class CollegeComparison extends Component {

  constructor(props) {
    super(props);
    this.state = {
      autoCompleteData: [],
      selectedClg: false,
      selectedCollege: {},
      signedIn: -1,
      isInUserList: -1,
      selectedColleges: [],
      loadSpinner: false,
      prev: false,
      next: false,
      is_college_selected: false,
      tmpSelectedColleges: [],
      slug: '',
    };
  }

  componentDidMount() {
    var headers = {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.getElementsByTagName('meta')[0].content
    }
    if (window.location.search.includes("UrlSlugs"))
    {
    axios.post('/social/comparison' + window.location.search + '&type=Ajaxcall', null, {headers: headers})
        .then((response) => {
              let selectedCollege = [...this.state.selectedColleges];
              selectedCollege = response.data;
              this.setState({
                loadSpinner: false,
                selectedColleges: selectedCollege,
                signedIn: response.data.signed_in,
                isInUserList: response.data.isInUserList,
                selectedClg: false,
                selectedCollege: {},
                is_college_selected: true,
                slug:  window.location.search,
                tmpSelectedColleges: [],
              });
        })
        .catch((error) => {
            console.log("not works", error);
        });
    }
  }

  handleSearchCollegeClick() {
    this.props.openModal();
  }

  handleSearchCollege(term, event) {
    axios.get(`/getslugAutoCompleteData?type=colleges&urlslug=&term=${term}`)
    .then(response => {
      this.setState({ autoCompleteData: response.data });
    })
    .catch(error => {
    })
  }

  handleOptionClick(slug, label) {
    this.setState({
      autoCompleteData: [],
      selectedClg: true,
      selectedCollege: {
        label: label,
        slug: slug,
      }
    });
  }

  handleSelectCollege(slug) {
    if (typeof slug === 'undefined')
      return;
    if (!this.state.is_college_selected)
    {
      this.setState({is_college_selected: true});
      window.history.pushState("", "", '/comparison?UrlSlugs=' + slug + ',');
    }
    else
    {
      window.history.pushState("", "", '/comparison' + window.location.search.replace("#",",") + slug + ',');
    }
    this.setState({ loadSpinner: true });

    var headers = {
      'Content-Type': 'application/json',
      'X-CSRF-TOKEN': document.getElementsByTagName('meta')[0].content
    }

    axios.post('/social/comparison', { UrlSlugs: slug, type: 'Ajaxcall'}, {headers: headers})
    .then(response => {

      let x =this.state
      this.props.closeModal();

      this.setState({ autoCompleteData: [] });

      let selectedCollege = [...this.state.selectedColleges];
      selectedCollege = response.data;
      this.setState({
        slug: slug,
        loadSpinner: false,
        selectedColleges: selectedCollege,
        signedIn: response.data.signed_in,
        isInUserList: response.data.isInUserList,
        selectedClg: false,
        selectedCollege: {},
        tmpSelectedColleges: [],
      });
    })
    .catch(error => {
      console.log('error', error)
    })
  }

  handleRemoveCollege(id, index) {
    if (this.state.selectedColleges.length < 2)
    {
      this.setState({is_college_selected: false});
      window.history.pushState("", "", '/comparison');
    }
    else
    {
      let college = this.state.selectedColleges[index];
      let  original_path = window.location.search;
      window.history.pushState("", "", '/comparison' + original_path.replace(college.slug + ",", ""));
    }
    let colleges = [...this.state.selectedColleges];

    colleges.splice(index, 1);


    this.setState({ selectedColleges: colleges });
  }

  resetCompareList = () => {
    this.setState({tmpSelectedColleges: []}, ()=>{this.props.closeModal()})
  }
  addCompare = () => {
    if (this.state.tmpSelectedColleges.length <= 0)
      return;
    let slug = (this.state.slug === '' ? '' : this.state.slug + ',') + this.state.tmpSelectedColleges.map((college)=>{
      return college.slug
    }).join(',')
    
    this.handleSelectCollege(slug)
  }
  addCollegeToCompareList = (newCollege) => {
    let newColleges =  _.cloneDeep(this.state.tmpSelectedColleges);
    let a_index = newColleges.findIndex( college => college.id == newCollege.id);
    if (a_index === -1) {
      newColleges.unshift(newCollege);
    } else {
      newColleges.splice(a_index, 1);
    }
    this.setState({tmpSelectedColleges:newColleges})
  }
  render() {
    const { isOpen } = this.props;
    const { autoCompleteData, loadSpinner } = this.state;

   var settings = {
      infinite: false,
      speed: 1000,
      slidesToShow: 3,
      slidesToScroll: 1,
      nextArrow: <NextArrow />,
      prevArrow: <PrevArrow />,
      responsive: [
        {
          breakpoint: 800,
          settings: {
            slidesToShow: 2
          }
        }
      ]
    };
    const renderColumn = () => {
      var column = [];

      for(var i = this.state.selectedColleges.length; i < 3; i++) {
        column.push(<ColumnHeader key={i} handleSearchCollege={this.handleSearchCollegeClick.bind(this)} />);
      }

      return column;
    }

    return (
        <div className='content-wrapper' id='comparison-main-div'>
          <Helmet>
            <title>Compare Colleges | College Recruiting Academic Network</title>
          </Helmet>
          <div className='row collapse comp-c-wrapper'>
            <div className='row'>
              <div className='columns small-12 text-center margin-from-top comparison-portion'>
                <div className='small-12 large-12 column'>
                  <div className='row'>
                    <div className='column small-12'>
                      <div className='battle-heading pl20 pt15 hide-for-small' style={{paddingLeft: 20, paddingTop: 15, textAlign: 'left'}}>
                        <img src='/images/colleges/compare/battle.png' className='text-center' style={{width: 40, height: 32}} alt=''/>&nbsp;
                        <span className='fs20 c-white' style={{fontSize: 20, color: 'white'}}><span className='f-bold' style={{fontWeight: 'bold'}}>COMPARE</span> COLLEGES</span>&nbsp;
                        <span className='fs12 c-white f-bold' style={{fontSize: 12, fontWeight: 'bold', color: 'white'}}>COMPARE THE TOP STATS OF ANY COLLEGES</span>
                      </div>

                      <div className='bck-fff battle-mid-content owlTitleColumn' style={{background: 'white'}}>
                        <div className='row'>
                          <div className='small-12 medium-3 column no-padding text-center valign-middle' id='valign-middle' style={{paddingRight: '0px !important'}}>
                            <div className='comapreSchooltitleArea'>
                              <span data-reveal-id='selectSchoolPopup' onClick={this.handleSearchCollegeClick.bind(this)} style={{cursor: 'pointer'}}>
                                <div className='green-btn mt10' style={{marginTop: 10}} >Add new college</div>
                                <div className='c79 f-bold fs18 pt10' style={{visibility: 'visible'}}>You haven't <br />added any <br /> colleges<br /></div>
                              </span>
                            </div>

                            <div className='row pt10 show-for-small text-center' style={{paddingTop: 10}} onClick={this.handleSearchCollegeClick.bind(this)}>
                              <span className='battlefont c-black' style={{color: 'black', cursor: 'pointer'}}>COMPARE COLLEGES</span><br/>
                              <span className='fs12 c-black' style={{fontSize: 12, color: 'black', cursor: 'pointer'}}>Compare the top stats of any college</span><br/>

                              <span data-reveal-id='selectSchoolPopup' style={{cursor: 'pointer'}}>
                                <div className='green-btn mt20' style={{fontSize: 14, display: 'inline-block', marginTop: 20}}>Add Colleges to compare</div>
                              </span>
                            </div>

                            <div className='border-right-gray row hide-for-small addSchool-btn-txt'>
                              <div className='column small-12 text-center'>
                              <span data-reveal-id='selectSchoolPopup' style={{cursor: 'pointer'}}>
                                  <div className='green-btn mt10' style={{marginTop:10}} onClick={this.handleSearchCollegeClick.bind(this)}>Add new college</div>
                                  {
                                    this.state.selectedColleges && this.state.selectedColleges.length == 0 &&
                                    <div className='c79 f-bold fs18 pt10' style={{visibility: 'visible', color: '#797979'}}>You haven't <br />added any <br /> colleges<br /></div>
                                  }
                                </span>
                              </div>
                            </div>

                            <div className='college-info-title hide-for-small'>
                              <div className='odd-div title-text br-white'>RANKING</div>
                              <div className='aid-section title-text'>GRANT OR SCHOLARSHIP AID</div>
                              <div className='title-text aid-sub-section'>
                                <div className='fs15 normal-text'>Students who receive aid</div>
                                <div className='fs15 normal-text'>Avg.financial aid given</div>
                              </div>
                              <div className='odd-div title-text br-white'>ACCEPTANCE <br/> RATE</div>
                              <div className='title-text br-white'>TUITION <br/><span className='fs10'>(avg. in-state)</span></div>
                              <div className='odd-div title-text br-white'>TUITION <br/><span className='fs10'>(avg. out-state)</span></div>
                              <div className='title-text br-white'>TOTAL EXPENSE <br/><span className='fs10'>(on campus)</span> </div>
                              <div className='odd-div title-text br-white'>STUDENT BODY <br/><span className='fs10'>(on campus)</span></div>
                              <div className='title-text br-white'>APPLICATION <br/> DEADLINE <br/><span className='fs10'>(undergraduate)</span></div>
                              <div className='odd-div title-text br-white'>APPLICATION FEE</div>
                              <div className='title-text br-white'>SECTOR OF <br/> INSTITUTION</div>
                              <div className='odd-div title-text br-white'>CALENDAR <br/> SYSTEM</div>
                              <div className='title-text br-white'>RELIGIOUS <br/>AFFILIATION</div>
                              <div className='odd-div title-text br-white'>CAMPUS SETTING</div>
                            </div>
                          </div>

                          <div className='small-12 medium-9 column no-padding' style={{paddingLeft: '0px !important'}}>
                            <div id='owl-compare' className='owl-compare owl-carousel mb5 owl-theme displayBlock' style={{marginBottom: 5}} ref={s => (this.slider = s)}>
                              <div className='owl-wrapper-outer'>
                                <div className='owl-wrapper large-12' style={{left: 0, display: 'block'}}>
                                  {
                                    this.state.selectedColleges &&
                                    <Slider {...settings}>
                                      {this.state.selectedColleges.map((college, index) => {
                                        return(
                                          <ComparisonColumn
                                            key={index}
                                            selectedCollege={college}
                                            handleRemoveCollege={this.handleRemoveCollege.bind(this, college.id, index)}
                                            signedIn={this.state.signedIn}
                                            isInUserList={this.state.isInUserList}
                                            display={this.state.selectedColleges.length - index <= 3 ? 'block' : 'none'}
                                          />

                                        )})}

                                      {
                                        this.state.selectedColleges.length < 3 &&
                                        renderColumn()
                                      }

                                    </Slider>
                                  }

                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div className='row small-collapse medium-uncollapse show-for-medium-only display-none'>
                  <div className='column small-12' style={{backgroundColor: 'rgba(0, 0, 0, 0.7)'}}>
                    <div className='row right-side-createAcct-container'>
                      <div className='column small-11 small-centered'>
                        <div className='row'>
                          <div className='column small-12 text-center'>
                            <img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/createAcct_compare.jpg' alt='Plexuss | Comparison' />
                          </div>
                        </div>

                        <div className='row create-acct-msg'>
                          <div className="column small-12">
                            Like any of the colleges you are comparing?
                            <br />
                            <br />
                            Join for free to get recruited
                          </div>
                        </div>

                        <a href='/signup?utm_source=SEO&amp;utm_medium=comparison'>
                          <div className='row'>
                            <div className='column small-12 text-center'>
                              <div className='create-acct-btn'>Create an account</div>
                            </div>
                          </div>
                        </a>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          {
            isOpen == true &&
            <div style={{maxWidth: '62.5em', width: '40%'}}>
              <CustomModal style={{display: 'block', opacity: 1, visibility: 'visible', top: 100, width: '62.5em', width: '40%', maxWidth: '62.5em', left: '0px !important', right: 0}} reactClassName={'modalClass'}>
                <div style={{margin: '0 auto', WebkitBorderRadius: 10, display: 'block', opacity: 1, visibility: 'visible', top: 100}}>
                  <div className="add-more-container">
                    <div className="close-add-more-page" onClick={this.resetCompareList}>&#10005;</div>
                    <div className="page-title">
                      Add Colleges to compare
                    </div>
                    <div className="page-sub-heading">
                      Which college do you want to compare?
                    </div>
                    <div className="college-search-form">
                      <i className="fa fa-search search-icon" aria-hidden="true" ></i>
                      <input type="text" placeholder="Start typing a college name" className='college-social-search-bar college-search-bar' onChange={(event) => this.handleSearchCollege(event.target.value)} autoComplete='off' />
                    </div>
                    <ul className="college-list">
                      {
                        autoCompleteData.length != 0 && autoCompleteData.map((result, index) => {
                          return <li key={index}><CollegeCard college={result} addCollegeToCompareList={this.addCollegeToCompareList}/></li>
                        })
                      }
                    </ul>
                    <div className="select_list">
                    {
                      this.state.tmpSelectedColleges.length != 0 && this.state.tmpSelectedColleges.map((college, index)=>{
                        return <img src={college.logo_url} alt="" key={index}/>
                      })
                    }
                    </div>
                    {
                      this.state.tmpSelectedColleges.length > 0 &&
                      <button className="add-college-button" onClick={this.addCompare}>
                        Compare {this.state.tmpSelectedColleges.length} college{this.state.tmpSelectedColleges.length>1?'s':''}
                      </button>
                    }
                  </div>
                </div>
              </CustomModal>
            </div>
          }
          {
            loadSpinner == true &&
            <div style={{width: '100%', height: '100%', position: 'absolute', top: 0, left: 0, zIndex: 100000}}>
              <MDSpinner style={{top: '17%', left: '60.5%'}}/>
            </div>
          }
        </div>
    );
  }
}

class NextArrow extends React.Component {
  render() {
      const { className, style, onClick } = this.props;
      return (
      <div
          className={className}
          style={{ ...style, 'background': 'url("/images/right-arrow.png")', 'borderRadius': '30px', 'boxShadow':'0px 2px 6px rgba(0,0,0,.3)'}}
          onClick={onClick}
      />
      );
  }
}

class PrevArrow extends React.Component {
  render() {
      const { className, style, onClick } = this.props;
      return (
      <div
          className={className}
          style={{ ...style, 'background': 'url("/images/left-arrow.png")', 'borderRadius': '30px', 'boxShadow':'0px 2px 6px rgba(0,0,0,.3)'}}
          onClick={onClick}
      />
      );
  }
}

class CollegeCard extends Component {
  constructor(props) {
    super(props)
    this.state = {
      selected: false,
    }

    this.handleCard = this.handleCard.bind(this)
    this.handleAction = this.handleAction.bind(this)
  }
  handleCard() {
    this.setState({
      selected: !this.state.selected,
    })
  }
  handleAction(college) {
    this.handleCard()
    this.props.addCollegeToCompareList(college);
  }
  render() {
    let { college } = this.props;
    return(
      <div className="card-container">
        <div className="college-image">
          <img src={college.logo_url} />
        </div>
        <div className="college-info">
          <div className="college-name">{college.label}</div>
          <div className="country-code">{college.value}</div>
        </div>
        <div className="action-button" onClick={() => this.handleAction(college)}>
          <div className={this.state.selected ? "selected-college" : "add-college"} >
            <img src={this.state.selected ? "/social/images/Icons/accept.png" : "/social/images/Icons/add-user.png"} />
          </div>
        </div>
      </div>
    )
  }
}

function mapStateToProps(state) {
  return {
    isOpen: state.modal.isOpen,
  }
}

function mapDispatchToProps(dispatch) {
  return {
    openModal: () => { dispatch(openModal()) },
    closeModal: () => { dispatch(closeModal()) },
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(CollegeComparison);