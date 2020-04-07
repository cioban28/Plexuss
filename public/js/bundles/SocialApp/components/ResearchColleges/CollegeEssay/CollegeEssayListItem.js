import React, {Component} from 'react'
import './styles.scss'
import {Link } from 'react-router-dom';

class CollegeEssaysListItem extends Component {
  constructor(props){
    super(props)
  }

  render(){
    const { premiumArticles } = this.props
    let length = premiumArticles.length
    // console.log("single article", premiumArticles)
    return(

      <div className='column small-12 medium-4 large-4 newsitem newsitem-height'  >
      { premiumArticles.map( (article, index) => (
            !!article.external_author && 
            <div key={index} className='news-article-box row collapse admitsee-box border-bottom-purple' >
              <div className='small-12 medium-12 medium-reset-order column admitsee-inner-cont'>
                <div className='row'>
                <div className='column small-10'>
                  <div className='admitsee-title3'>{article.title}</div>
                  <div className='admitsee-print'>
                  by {article.external_author}
                  </div>
                </div>
                </div>

                <div className=" column small-2 viewed-container"></div></div>

                <div className="text-center mt15">
                <div className="round-portrait">
                  <Link to={`/college-essays/${article.slug}`}>
                    <img className="news-img-title" src={article.authors_img} title="The Essay That Got Me Into the University of Massachusetts as a Transfer Student" alt="The Essay That Got Me Into the University of Massachusetts as a Transfer Student" />
                  </Link>
                </div>
                </div>

                <div className="admitsee-print bottom-fade essay-excerpt-box" >
                <div className="html-height-adjustment" dangerouslySetInnerHTML={{ __html: article.basic_content }} /></div>

                <div className="btn-container">
                  <div className="text-center">
                    <Link to={`/college-essays/${article.slug}`} className="view-essay-btn">View Essay</Link>
                  </div>
                </div>
              </div>
          )
        )
      }
      </div>
    )
  }
}

export default CollegeEssaysListItem;
