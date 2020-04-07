import React, { Component } from 'react'
import { connect } from 'react-redux'
import axios from 'axios';
import Attributes from './Attributes'
import AuthorInfo from './AuthorInfo'
import News from './index';
import { Helmet } from 'react-helmet';

class SingleNews extends Component {
  constructor(props){
		super(props)
		this.state = {
			singleNews: undefined,
			title: '',
			description: '',
			keywords: ''
		}
  }

  componentDidMount(){
    window.scrollTo(0, 0);
    const { singleNews } = this.props;
    if(singleNews && Object.keys(singleNews).length) {
      this.setState({
        title: singleNews.title,
        description: singleNews.meta_descrip,
        keywords: singleNews.meta_keywords
      });
    } else {
		  this.getSingleNews();
    }
  }

	getSingleNews = () => {
		axios.get(`/api/news/article/${this.props.slug}`)
		.then(res => {
      		this.setState({
            singleNews: res.data.bread_data,
            title: res.data.bread_data.meta_title,
            description: res.data.bread_data.meta_descrip,
            keywords: res.data.bread_data.meta_keywords
          });
		})
		.catch(err => {
		})
	}

  render(){
		const singleNews = !!this.props.singleNews ? this.props.singleNews : this.state.singleNews;
		return (
      	<News>
      		<Helmet>
	          <title>{this.state.title}</title>
	          <meta name="description" content={this.state.description}/>
	          <meta name="keywords" content={this.state.keywords} />
	        </Helmet>
				{!!singleNews &&
          <div className="content-wrapper content-wrapper1">
            <Attributes singleNews={singleNews}/>
						<div id="newsshomecontent" className="row collapse" style={{height: "100%", maxWidth: "100%"}}>
							<div className="newss-cont-left-container">
								<div itemScope="" itemType="https://schema.org/Article" className="row fullWidth">
									<div className="small-12 column newss-description">
										<div className="row collapse">
											<div className="small-5 column comments-elevator">
											</div>

											<div className="single-news" data-articlepin={singleNews.subcatSlug}>
												<div className="newss-img-div newss-img-div1" data-singleviewbanner="college newss">
													<img itemProp="image" src={`https://s3-us-west-2.amazonaws.com/asset.plexuss.com/news/images/${singleNews.img_lg}`} title={singleNews.title} alt={singleNews.title} className="newss-detail-image large-12"/>
												</div>
												<div className="category-badge hide-for-small-only">{singleNews.subcat} </div>
											</div>

                     <div className='news-content'>
											<h1 itemProp="headline" className="news-heading heading-h1">{singleNews.title}</h1>

											<div className="row _row_banner">
                        <div className="medium-12 small-12 columns">
                          <AuthorInfo singleNews={singleNews}/>
													<div className='news-description-text' dangerouslySetInnerHTML={{ __html: singleNews.content }} />
												</div>
											</div>

												<div className="clearfix">
												</div>
												<div className="row">
													<div className="clearfix"></div>

													<div className="share-buttons-striped"></div>

													<div className="clearfix"></div>
												</div>
                      </div>
											</div>

											<div className="hide">
												<div className="row">
													<div className="small-12 column comment-wrapper">
														<a name="comments"></a>
													</div>
												</div>
											</div>
										</div>
									</div>
								</div>
							</div>
						</div>
					}
			</News>
    );
  }
}

const mapStateToProps = (state, ownProps) => {
  return {
		singleNews: state.news && state.news.newsList && state.news.newsList.filter(news => news.slug === ownProps.match.params.slug)[0],
		slug: ownProps.match.params.slug
  }
}

export default connect(mapStateToProps, null)(SingleNews);
