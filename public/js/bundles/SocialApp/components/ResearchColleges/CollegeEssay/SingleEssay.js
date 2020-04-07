import React, { Component } from 'react'
import './styles.scss'
import {connect} from 'react-redux'
import CollegeEssaysList from './CollegeEssayList'

import { getPremiumArticles } from './../../../api/collegeEssays'
import axios from 'axios';
import { Helmet } from 'react-helmet';

class SingleEssay extends Component {
  constructor(props){
    super(props)
    this.state = {
      collegeEssay : undefined,
      title: '',
      description: '',
      keywords: '',
    }
  }

  componentDidMount(){
    !(!!this.props.collegeEssay) && this.getSingleEssay()
  }

  getSingleEssay = () => {
    axios.get(`/api/news/article/${this.props.slug}/essay`)
    .then(res => {
      this.setState({collegeEssay: res.data.bread_data, })
    })
    .catch(error => {
      console.log("---errore", error)
    })
  }


  render(){
    let  collegeEssay = !!this.props.collegeEssay ? this.props.collegeEssay : this.state.collegeEssay
    return(
      !!collegeEssay &&
      <div>
        <Helmet>
          <title>{ collegeEssay.title }</title>
          <meta name="description" content={collegeEssay.meta_descrip} />
          <meta name="keywords" content={collegeEssay.meta_keywords} />
        </Helmet>
        <div id="newsContentContainer" itemScope="" itemType="https://schema.org/Article" className="row news-content-main ">
          <div className="small-12 column news-description">
      			<div className="row collapse">
      				<div className="small-5 column comments-elevator">
			      	</div>

      				<div className="small-7 column">
      				</div>
			      </div>
			    <div>

          <span className="category-tag no-big-display">CAMPUS LIVING</span>
				  <div className="news-img-div college-essay-img" data-singleviewbanner="college essays">
            <div className="featured collegeEssays singleViewBadge" data-singleviewbadge="college essays">college essays</div>
              <img itemProp="image" src={collegeEssay.img_lg} title={collegeEssay.title} alt="The Essay That Got Me Into the University of Massachusetts as a Transfer Student" className="news-detail-image"/>
            </div>
          </div>

  			  <h1 itemProp="headline" className="heading-h1">{collegeEssay.title}</h1>

          <div className="row">
            <div className="medium-2 small-12 columns">
              <div className="internalAuthor">
                <p className="small-text-left medium-text-right hide-for-small-only">By {collegeEssay.external_author}
                </p>
                <p className="small-text-left medium-text-right hide-for-small-only">{collegeEssay.external_name}</p>

                <div className="authors_img hide-for-small-only"><img src={collegeEssay.authors_img} alt=""/></div>
                <p className="hide-for-small-only">{collegeEssay.authors_description}</p>
              </div>
            </div>

            <div className="medium-10 small-12 columns">
              <div style={{position: "relative"}}>
                <div className="row author_row_mobile" data-equalizer="">
                  <div className="column small-3" data-equalizer-watch="" >
                    <div className="authors_img hide-for-medium-up">
                      <img src={collegeEssay.authors_img} alt=""/>
                    </div>
                  </div>

                  <div className="column small-9" data-equalizer-watch="">
                    <p className="no-big-display author-name hide-for-medium-up">BY {collegeEssay.external_author}
                    </p>

      		  				<p className="small-text-left medium-text-right hide-for-medium-up authors_source">{collegeEssay.external_name}</p>

        		  			<p className="hide-for-medium-up authors_description">{collegeEssay.authors_description}</p>
							    </div>
						    </div>
						    <p itemProp="datePublished" className="post-time small-only-text-right">{collegeEssay.created_at}</p>

						    <div id="essayWrapper" itemProp="articleBody" className="content-text">
                  <div className="news-content-main" dangerouslySetInnerHTML={{ __html: collegeEssay.premium_content }} /></div>
      			    </div>
					    </div>
          	</div>
		    	</div>

          <div className="clearfix"></div>
          <a className="social_share share_facebook
          " data-params="{&quot;platform&quot;:&quot;facebook&quot;,&quot;name&quot;:&quot;The Essay That Got Me Into the University of Massachusetts as a Transfer Student&quot;,&quot;picture&quot;:&quot;https:\/\/s3-us-west-2.amazonaws.com\/asset.plexuss.com\/news\/images\/umass_lg.jpg&quot;,&quot;href&quot;:&quot;http:\/\/www.plexuss.com\/news\/article\/the-essay-that-got-me-into-umass-as-transfer&quot;}"></a>
          <a className="social_share share_twitter
          " data-params="{&quot;platform&quot;:&quot;twitter&quot;,&quot;text&quot;:&quot;The Essay That Got Me Into the University of Massachusetts as a Transfer Student&quot;,&quot;href&quot;:&quot;http:\/\/www.plexuss.com\/news\/article\/the-essay-that-got-me-into-umass-as-transfer&quot;}"></a>
          <a className="social_share share_pinterest
          " data-params="{&quot;platform&quot;:&quot;pinterest&quot;,&quot;description&quot;:&quot;The Essay That Got Me Into the University of Massachusetts as a Transfer Student&quot;,&quot;picture&quot;:&quot;https:\/\/s3-us-west-2.amazonaws.com\/asset.plexuss.com\/news\/images\/umass_lg.jpg&quot;,&quot;href&quot;:&quot;http:\/\/www.plexuss.com\/news\/article\/the-essay-that-got-me-into-umass-as-transfer&quot;}" data-pin-do="buttonPin" data-pin-config="above"></a>
          <a className="social_share share_linkedin
          " data-params="{&quot;platform&quot;:&quot;linkedin&quot;,&quot;title&quot;:&quot;The Essay That Got Me Into the University of Massachusetts as a Transfer Student&quot;,&quot;picture&quot;:&quot;https:\/\/s3-us-west-2.amazonaws.com\/asset.plexuss.com\/news\/images\/umass_lg.jpg&quot;,&quot;href&quot;:&quot;http:\/\/www.plexuss.com\/news\/article\/the-essay-that-got-me-into-umass-as-transfer&quot;}"></a>
	    		<div className="clearfix"></div>
		  </div>
	  </div>
    );
  }
}

const mapStateToProps = (state, ownProps) => {
  return {
    collegeEssay: state.collegeEssays &&  state.collegeEssays.length > 0 && state.collegeEssays.filter(essay => essay.slug === ownProps.match.params.slug)[0],
    slug: ownProps.match.params.slug
  }
}

const mapDispatchToProps = (dispatch) => {
  return{
    getPremiumArticles: (hashedUserId) => dispatch(getPremiumArticles(hashedUserId))
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(SingleEssay);
