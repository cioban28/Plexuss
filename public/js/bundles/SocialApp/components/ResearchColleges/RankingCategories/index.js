import React, { Component } from 'react'
import ReactDom from 'react-dom'
import { connect } from 'react-redux'
import axios from 'axios'
import Masonry from 'react-masonry-component';
import '../styles.scss'
import { Link } from 'react-router-dom'
import { Helmet } from 'react-helmet';

class RankingCategories extends Component {
	is_mount = false;
	constructor(props) {
		super(props);
		this.state = {
				temp: 'value',
		    ranking_categories: [],
				keys: [],
				expanded: [],
				listToShow: []
		}
	}

  componentDidMount() {
		this.is_mount = true;
    axios.get('/ranking/list/categories')
		.then(res => {
			if(this.is_mount)
				this.setState({ ranking_categories: res.data.list_array});
		}).catch(error => {	
			console.log("not works");
		});

	}

	componentWillUnmount() {
		this.is_mount = false;
	}


	showMorebtn = (key, items_length) => {
		let newList = this.state.listToShow;
		newList[key] = items_length;

		let newExpanded = this.state.expanded;
		newExpanded[key] = true
		this.setState({
			listToShow: newList,
			expanded: newExpanded
		})

		// this.setState({listToShow: items_length, expanded: true})
		// this.setState({listToShow[key]: items_length, expanded[key]: true})
	}
	showLessbtn = (key, items_length) => {
		// this.setState({listToShow: items_length, expanded: false})
		// const { listToShow, expanded } = this.state;

		let newList = this.state.listToShow;
		newList[key] = items_length;
		let newExpanded = this.state.expanded;
		newExpanded[key] = false
		this.setState({
			listToShow: newList,
			expanded: newExpanded
		})
	}

	render() {
		let category = this.state.ranking_categories;

		let masonryOptions ={ transitionDuration: 0}
		return (
			<div id="ranking_categories_main_div">
				<Helmet>
					<title>College Ranking News | College Recruiting Academic Network | Plexuss.com</title>
	        <meta name="description" content="Find college ranking news on Plexuss.com. Discover blogs, news, and community conversations about different college rankings from around the US." />
	        <meta name="keywords" content="college ranking" />
				</Helmet>
				<div className = 'ranking_categories_banner_top'>
					<div className="column small-12 large-12" style={{margin: '50px auto'}}>
						<div className="row show-for-medium-up" style={{paddingRight: '2%'}}>
					    <div className="small-12 columns" style={{backgroundColor: '#ffffff', borderRadius: '5px', padding: '0px'}}>
					        <div className="row">
					        	<div className="small-12 columns" style={{ borderRadius: '5px 5px 0px 0px', backgroundColor: 'white'}}><span style={{fontSize: '24px', color: '#000000', paddingLeft: '3px', lineHeight: '47px'}}>College Ranking</span>&nbsp;&nbsp;<span style={{fontSize:'12px', color: '#8A8A79'}}>All Ranking Categories</span></div>
					        </div>
					        <div className="row" style={{backgroundColor: '#000000'}}>
					        	<div className="small-12 columns" style={{lineHeight: '30px'}}>&nbsp;</div>
					        </div>
					    </div>
					  </div>
					  <br/>
						{  Object.keys(this.state.ranking_categories).length<= 0 && <div className="ranking-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div>}
					  <div className="hide-for-small-only">
					  <div className="row">
					  	<div id="container-box" className="js-masonry row" style={{position: 'relative', height: '1968px', width: '100%'}}>
					    	<Masonry
                className={'js-masonry'} // default ''
                options={masonryOptions} // default {}
            		>
					    	{
					    		Object.keys(this.state.ranking_categories).map((key, index) =>
	                <div className="large-4 medium-6 columns ranking-listing-category-box" key={index}>
											<div className="row">
												<div className="small-12 columns" style={{paddingRight: '0px !important'}}>
													<div className="ranking-category-box">
														<div className="row">
							                <div className="small-12 columns ranking-category-box-head">{key}</div>
							              </div>
							              <div className="row">
							                <div className="small-12 columns ranking-box-min-height">
							                	<div className="row">
																	<div className="small-12 columns ranking-category-box-subhead">{this.state.ranking_categories[key][0].source}</div>
							                  </div>
																<div className="row collapse ranking-cat-layout-disp">
																	<div className="colleges-list-tran small-12 columns" style={{marginLeft: '6px', paddingRight: '12px'}}>
																		<div className="row " style={{transitionDelay: '1s'}}>
																			<div className="small-12 columns ranking-category-image">
																				<img src={`https://s3-us-west-2.amazonaws.com/asset.plexuss.com/lists/images/${this.state.ranking_categories[key][0].image}`} />
																			</div>
							                      </div>
							                      {
							                      	this.state.ranking_categories[key].slice(0, !!this.state.listToShow[key] ? this.state.listToShow[key] : 3).map( (datum, index) => (
																			<div className={'row ' + (index % 2 === 0 ? 'ranking-list-row-odd' : 'ranking-list-row-even')} key={index}>
																				<div className="small-2 columns ranking-college-left"><div className="circle-ranking">{index+1}</div></div>
																				<div className="small-10 columns ranking-college-right"><strong><Link to={`/college/${datum.slug}`}>{datum.school_name}</Link></strong><br/>{datum.city}, {datum.state} </div>
																			</div>
																			)
		                                )}
							                    </div>
							                  </div>
															</div>
														</div>
							            </div>
							          </div>
													{
														!!this.state.expanded[key]==false && this.state.ranking_categories[key].length>3 &&
		                  			<div className="row" style={{transitionDelay: '1s'}}>
															<div className="small-12 columns ranking-cat-layout-disp hide-for-small-only" id="cat_show_more_Top_Gluten_Free_Colleges" style={{textAlign:'center', padding:5}} onClick={ () => this.showMorebtn(key, this.state.ranking_categories[key].length)} ><a  id="expand_category_a_Top_Gluten_Free_Colleges" className="ranking-show-more" >Show more</a></div>
															<div className="small-12 columns show-for-small-only" style={{textAlign:'center', padding: '5px', cursor:'pointer'}}><img src="/images/ranking/down-arrow.png"  id="cat_show_more_mobile_Top_Gluten_Free_Colleges" /></div>
		                  			</div>
													}
													{
														!!this.state.expanded[key] &&
														<div className="row">
															<div className="small-12 columns ranking-cat-layout-disp hide-for-small-only" id="cat_show_more_Top_Gluten_Free_Colleges" style={{textAlign:'center', padding:5}} onClick={ () => this.showLessbtn(key, 3)} ><a  id="expand_category_a_Top_Gluten_Free_Colleges" className="ranking-show-more" >Show less</a></div>
															<div className="small-12 columns show-for-small-only" style={{textAlign:'center', padding: '5px', cursor:'pointer'}}><img src="/images/ranking/down-arrow.png"  id="cat_show_more_mobile_Top_Gluten_Free_Colleges" /></div>
														</div>
													}
											</div>
							      </div>
					      	)}
            		</Masonry>

					  	</div>
					  </div>
					  </div>


					  <div className="show-for-small-only">
					  	<div className="row" style={{marginLeft: '3%'}}>
					  	<div id="container-box" className="js-masonry row" style={{position: 'relative', height: '1968px', width: '100%'}}>
					    	{
					    		Object.keys(this.state.ranking_categories).map((key,index) =>
	                <div className="large-4 medium-6 columns ranking-listing-category-box" style={{ margin: '0 3% 0 0 !important'}} key={index}>
											<div className="row">
												<div className="small-12 columns" style={{paddingRight: '0px !important'}}>
													<div className="ranking-category-box small-transition-div">
														<div className="row">
							                <div className="small-12 columns ranking-category-box-head">{key}</div>
							              </div>
							              <div className="row">
							                <div className="small-12 columns ranking-box-min-height">
							                	<div className="row">
																	<div className="small-12 columns ranking-category-box-subhead">{this.state.ranking_categories[key][0].source}</div>
							                  </div>
																<div className={!!this.state.expanded[key] ? "row collapse ranking-cat-layout-disp rank-display-block" : "row collapse ranking-cat-layout-disp"}>
																	<div className="colleges-list-tran small-12 columns" style={{marginLeft: '6px', paddingRight: '12px'}}>
																		<div className="row " style={{transitionDelay: '1s'}}>
																			<div className="small-12 columns ranking-category-image">
																				<img src={`https://s3-us-west-2.amazonaws.com/asset.plexuss.com/lists/images/${this.state.ranking_categories[key][0].image}`} />
																			</div>
							                      </div>
							                      {
							                      	this.state.ranking_categories[key].slice(0, !!this.state.listToShow[key] ? this.state.listToShow[key] : 0).map( (datum, index) => (
																			<div className={'row small-transition-div' + (index % 2 === 0 ? 'ranking-list-row-odd' : 'ranking-list-row-even ')} key={index}>
																				<div className="small-2 columns ranking-college-left"><div className="circle-ranking">{index+1}</div></div>
																				<div className="small-10 columns ranking-college-right"><strong><Link to={`/college/${datum.slug}`}>{datum.school_name}</Link></strong><br/>{datum.city}, {datum.state} </div>
																			</div>
																			)
		                                )}
							                    </div>
							                  </div>
															</div>
														</div>
							            </div>
							          </div>
													{
														!!this.state.expanded[key]==false &&
		                  			<div className="row" style={{transitionDelay: '1s'}}>
															<div className="small-12 columns ranking-cat-layout-disp hide-for-small-only" id="cat_show_more_Top_Gluten_Free_Colleges" style={{textAlign:'center', padding:5}} onClick={ () => this.showMorebtn(key, this.state.ranking_categories[key].length)} ><a  id="expand_category_a_Top_Gluten_Free_Colleges" className="ranking-show-more" >Show more</a></div>
															<div className="small-12 columns show-for-small-only" style={{textAlign:'center', padding: '5px', cursor:'pointer'}} onClick={ () => this.showMorebtn(key, this.state.ranking_categories[key].length)}><img src="/images/ranking/down-arrow.png"  id="cat_show_more_mobile_Top_Gluten_Free_Colleges" /></div>
		                  			</div>
													}
													{
														!!this.state.expanded[key] &&
														<div className="row">
															<div className="small-12 columns ranking-cat-layout-disp hide-for-small-only" id="cat_show_more_Top_Gluten_Free_Colleges" style={{textAlign:'center', padding:5}} onClick={ () => this.showLessbtn(key, 3)} ><a  id="expand_category_a_Top_Gluten_Free_Colleges" className="ranking-show-more" >Show less</a></div>
															<div className="small-12 columns show-for-small-only" style={{textAlign:'center', padding: '5px', cursor:'pointer'}} onClick={ () => this.showLessbtn(key, 0)}><img src="/images/ranking/up-arrow.png"  id="cat_show_more_mobile_Top_Gluten_Free_Colleges" /></div>
														</div>
													}
											</div>
							      </div>
					      	)}

					  	</div>
					  </div>

		    		</div>
		    	</div>
		    </div>
		  </div>
		);
	}
}

export default RankingCategories
