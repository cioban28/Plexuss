import React, { Component } from 'react'
import ReactDom from 'react-dom'
import { Link } from 'react-router-dom'
import { connect } from 'react-redux'
import axios from 'axios';
import College from './college'
import MDSpinner from 'react-md-spinner';
import './styles.scss'
import MajorsListItem from './majorsLikstItem';
import Collapsible from 'react-collapsible';
import MajorsList from './majorsList';
import { SpinningBubbles } from '../../../common/loader/loader'
import AnimateHeight from 'react-animate-height';
import {Helmet} from 'react-helmet'

class MajorsInner extends Component {
  state = {
    department: [],
    selected: [],
    selected_major: [],
    selected_major_slug: [],
    majors: [],
    all_departments: [],
    inner_deps: [],
    metainfo: [],
    hover: -1,
    colleges_data: [],
	showLinks: false,
	allDepartmentsWithMajors: {},
	deptsBarHeight: 0,
	expanded: false,
	listToShow: 3,
	isChange: false,
	selectedDeptName: '',
	metainfo: {}
	}

	showMorebtn = () => {
		this.setState({listToShow: 5000, expanded: true})
	}
	showLessbtn = () => {
		this.setState({listToShow: 3, expanded: false})
	}

	handleLinkDept = (url) => {
		window.location.href=url;
	}

  handleQuickLinksClick = () => {
    this.setState((prevState) =>({
      showLinks: !prevState.showLinks
    }))
  }

  showMore() {
	  this.state.itemsToShow === 3 ? (
	    this.setState({ itemsToShow: this.state.cars.length, expanded: true })
	  ) : (
	    this.setState({ itemsToShow: 3, expanded: false })
	  )
	}

  componentDidMount() {
		let url = this.props.match.params.major ? ('/api/college-majors/' + this.props.match.params.slug + '/' +  this.props.match.params.major ) : ( '/api/college-majors/' + this.props.match.params.slug );
		this.loadData(url)
	}

	componentDidUpdate(prevProps) {
		if (this.props.location !== prevProps.location) {
			let url = this.props.match.params.major ? ('/api/college-majors/' + this.props.match.params.slug + '/' +  this.props.match.params.major ) : ( '/api/college-majors/' + this.props.match.params.slug );
			this.loadData(url)
		}
	}
	loadData(url) {
		this.setState({isChange: true})
		axios.get(url)
		.then(res => {
			let selectedDeptName = '';
			res.data.departments.forEach(dept => {
				if(dept.url_slug === res.data.selected) {
					selectedDeptName = dept.name;
				}
			})
			this.setState({	selected: res.data.selected,
											selected_major_slug: res.data.selected_major,
											all_departments:  res.data.departments,
											inner_deps: res.data,
											metainfo: res.data.metainfo,
											colleges_data: res.data.searchData,
											selected_major: res.data.metainfo.name,
											majors: res.data.majors_for_department,
											allDepartmentsWithMajors: res.data.all_departments_with_majors,
											isChange: false,
											selectedDeptName: selectedDeptName,
											metainfo: res.data.metainfo,
										});
		}).catch(error => {
			console.log("not works", error);
		});
	}

	adjustHeight = (deptName) => {
		const deptsBarHeight = this.state.deptsBarHeight === 0 ? 'auto' : 0;

		if(!!deptName && typeof(deptName) === 'string') {
			this.setState((prevState) => ({deptsBarHeight: deptsBarHeight, selectedDeptName: deptName }))
		}
		this.setState((prevState) => ({deptsBarHeight: deptsBarHeight }))
	}

	hoverOn = (i) => {
    this.setState({ hover: i });
	}
	hoverOff = (i) => {
    this.setState({ hover: -1 });

	}

	render(){

		let toRender = (!!this.state.selected_major && this.state.selected_major.length > 0)  ? true : (this.state.all_departments.length > 0  ? true : false)
		return (
			<div>
				<Helmet>
		          <title>{this.state.metainfo.meta_title}</title>
		          <meta name='description' content={this.state.metainfo.meta_description} />
		        </Helmet>
			{ !toRender ? (
			<SpinningBubbles/> ) :
			(
				<div id='majors-inner'>
					<div className='my-body'>
						<div className='pd-top-10 medium-4 large-3 columns side-bar-1 college-pages-navbar side-nav-bar' style={{margin: 'auto'}} id='filter-search-div'>
	            <div className='row' style={{}}>
	            	<div className='column small-12 side-bar-departments hide-for-small'>

	            		<ul id='menu' className='ui-menu ui-widget ui-widget-content' role='menu' tabIndex='0' aria-activedescendant="ui-id-38">

											{
												!!this.state.all_departments &&  this.state.all_departments.length != 0 && this.state.all_departments.map((department, index) => (
            <li aria-haspopup="true" className="ui-menu-item" id="ui-id-4" tabIndex="0" role="menuitem" key={index}><span className="ui-menu-icon ui-icon ui-icon-carat-1-e"></span>
													<MajorsList department={department} selected={this.state.selected} selected_major={this.state.selected_major_slug} allDepartmentsWithMajors={this.state.allDepartmentsWithMajors} key={index} identity={index} />
         							   </li>
												)
											)}





	            		</ul>
	            	</div>
								<div className={`column small-12 side-bar-departments show-for-small`} style={{backgroundColor: 'black'}}>
									<div onClick={this.adjustHeight} className='header-text'>{this.state.selectedDeptName}<i className='fa fa-chevron-down'></i></div>
									<AnimateHeight duration={500} height={this.state.deptsBarHeight}>
									{
										!!this.state.all_departments && !!this.state.all_departments.length && this.state.all_departments.map((department, index) => (
											<div key={index}>
												<Link className={`colapsible-item ${this.state.selected == department.url_slug ? 'color-green' : ''}`} style={{color: 'white'}} to={'/social/majors/' + department.url_slug} onClick={this.adjustHeight.bind(this, department.name)}>{department.name}</Link>
											</div>
										))
									}
									</AnimateHeight>
								</div>
	            </div>
						</div>

						{this.state.isChange ? (<SpinningBubbles/>):(
							<div className='small-12 medium-8 large-9 columns'>
							<div className='row'>
								<div className="column small-12">
									<div className='right-bar-department-info'>
										<div className='row'>

											{(!!this.state.selected_major && this.state.selected_major.length != 0 ) ?
												<h1 className="department-headning-div department-headning-div-first heading-span">
													<Link className="color-green" style={{cursor: 'pointer'}} to={`/college-majors/${this.state.selected}`} >{this.state.metainfo.department_category}</Link>
													<span>	> {this.state.selected_major}</span>
												</h1>
												:
												<h1 className="department-headning-div department-headning-div-first">
													{this.state.metainfo.headline1}
												</h1>
											}
									    <div className="department-header-img">
												<img src={`https://s3-us-west-2.amazonaws.com/asset.plexuss.com/${(!!this.state.selected_major && this.state.selected_major.length > 0) ? 'major' : 'department'}/header/${this.state.metainfo.header_img_name}`} alt={this.state.metainfo.header_image_alt} className="full-width" />
											</div>
										</div>

										<div className="show-for-small-only">

												<div className='row department-major-buttons'>
													{
							              !!this.state.majors &&  this.state.majors.length != 0 && this.state.majors.slice(0, this.state.listToShow).map((major, i) => (

															<div className="column small-12 medium-4 content left-space" key={i}>
								             		<Link className="button" to={'/college-majors/' + major.mdd_slug + '/' + major.slug} >
								                 	{major.name}
								                </Link>
									            </div>
							              ))
							            }

							            	{
							            		this.state.expanded==false && !!this.state.majors &&  this.state.majors.length>3 &&
											    			<div className="majors-toggle-btn column small-12 medium-4  left-space " onClick={ () => this.showMorebtn()} >show more...</div>

							            	}

							            	{
							            		this.state.expanded &&
								            		<div className="majors-toggle-btn column small-12 medium-4 left-space"  onClick={ () => this.showLessbtn()} >show less...</div>
							            	}


												</div>



										</div>



										<div className="hide-for-small-only">

											<div className='row department-major-buttons'>
													{
							              !!this.state.majors &&  this.state.majors.length != 0 && this.state.majors.slice(0, this.state.majors.length).map((major, i) => (

															<div className="column small-12 medium-4 content left-space" key={i}>
								             		<Link className="button" to={'/college-majors/' + major.mdd_slug + '/' + major.slug} >
								                 	{major.name}
								                </Link>
									            </div>
							              ))
							            }

												</div>


										</div>

										<div className="row">
											<h1 className="department-headning-div">
									     	{this.state.metainfo.headline1}
										  </h1>
									     <div className="department-content-div margin-div" dangerouslySetInnerHTML={{__html: this.state.metainfo.content1}}>
											</div>
										</div>

										<div className="row">
											<h1 className="department-headning-div">
												{this.state.metainfo.headline2}
									    </h1>
								      <div className="department-content-div margin-div" dangerouslySetInnerHTML={{__html: this.state.metainfo.content2}}>
											</div>
										</div>

										<div className="row">
										  <div className="department-header-img">
										     <img src={`https://s3-us-west-2.amazonaws.com/asset.plexuss.com/${(!!this.state.selected_major && this.state.selected_major.length > 0) ? 'major' : 'department'}/images/${this.state.metainfo.body_img1_name}`} alt={this.state.metainfo.body_img1_alt} className="full-width" />
											</div>
										</div>

								    <div className="row">
						        	<h1 className="department-headning-div">
						        		{this.state.metainfo.headline3}
							        </h1>
							        <div className="department-content-div margin-div" dangerouslySetInnerHTML={{__html: this.state.metainfo.content3}}>
							        </div>

							        <div className="department-content-div margin-div" >
						            {
							            !!this.state.majors &&  this.state.majors.length != 0 && this.state.majors.map((major, index) => (
	                      		<p key={index}><Link style={{fontWeight: 900, fontSize: '1.1em', textDecoration: 'underline'}} to={ '/college-majors/' + major.mdd_slug + '/' + major.slug} >{major.name}</Link>{':' + major.content1.replace(/(<([^>]+)>)/ig,"")}</p>
							            ))
							          }
			            		</div>
								    </div>

									  <div className="row">
											<h1 className="department-headning-div">
												{this.state.metainfo.headline4}
									    </h1>
								      <div className="department-content-div margin-div" dangerouslySetInnerHTML={{__html: this.state.metainfo.content4}}>
											</div>
										</div>

										<div className="row">
											<h1 className="department-headning-div">
												{this.state.metainfo.headline5}
									    </h1>
								      <div className="department-content-div margin-div" dangerouslySetInnerHTML={{__html: this.state.metainfo.content5}} >
											</div>
										</div>

										<div className="row">
											<h1 className="department-headning-div">
												{this.state.metainfo.headline6}
									    </h1>
								      <div className="department-content-div margin-div" dangerouslySetInnerHTML={{__html: this.state.metainfo.content6}}>
											</div>
										</div>
									</div>

									<div className='colleges-list-div'>
									{
										!!this.state.colleges_data &&	 this.state.colleges_data.data.length != 0 &&
										<div className='search-content-div'>
											<div className='search-content-results-div'>
											{
												!!this.state.colleges_data &&	 this.state.colleges_data.data.length != 0 && this.state.colleges_data.data.map((college, index) => (
													<College college={college} key={index} identity={index} />
				              					))
											}
											</div>
										</div>
									}

										<div className='row'>
											<div className="large-2 small-2 column no-padding"></div>
											<div className="large-10 small-10 column no-padding">
												<ul className="pagination">
													<li className="disabled"><span>« Previous</span></li>
													{(!!this.state.selected_major && this.state.selected_major.length != 0 ) ?
														<li><Link to={`/college-search?school_name=&country=&state=&city=&zipcode=&degree=&department=${this.state.selected}&imajor=&major_slug=${this.state.selected_major_slug}&locale=&tuition_max_val=0&enrollment_min_val=0&enrollment_max_val=0&applicants_min_val=0&applicants_max_val=0&min_reading=&max_reading=&min_sat_math=&max_sat_math=&min_act_composite=&max_act_composite=&religious_affiliation=&type=college&term=&myMajors=&page=2`} rel="next">Next »</Link></li>
														:
														<li><Link to={`/college-search?department=${this.state.selected}&tuition_max_val=0&enrollment_min_val=0&enrollment_max_val=0&applicants_min_val=0&applicants_max_val=0&type=college&page=2`} rel="next">Next »</Link></li>
													}
								        </ul>
								      </div>
										</div>
									</div>
								</div>
							</div>
						</div>
						)}
						</div>
				</div>
			)}
			</div>
		)
	}
}

export default MajorsInner
