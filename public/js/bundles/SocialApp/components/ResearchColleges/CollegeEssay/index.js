import React, { Component } from 'react'
import './styles.scss'
import {connect} from 'react-redux'
import CollegeEssaysList from './CollegeEssayList'
import { Helmet } from 'react-helmet';
import { getPremiumArticles } from './../../../api/collegeEssays'

class CollegeEssays extends Component {
  constructor(props){
    super(props)
    this.state = {
      isLoading: true,
    }
  }

  componentDidUpdate(){
    Object.keys(this.props.collegeEssays).length === 0 && getPremiumArticles();
  }

  componentDidMount(){
    getPremiumArticles()
    .then(()=>{
      this.setState({isLoading: false})
    })
  }

  render(){
    let length = !!Object.keys(!!this.props.collegeEssays).length ? Object.keys(!!this.props.collegeEssays).length : !!this.props.collegeEssays.length

    return(
      <div ref="iScroll">
        <Helmet>
          <title>College Essays|Sample College Admission Essays that Worked|Plexuss</title>
          <meta name="description" content="Increase your chances- review sample college admission essays that worked. View some of the best college admission essays today!" />
          <meta name="keywords" content="admitsee college essays essay" />
        </Helmet>
        { length <= 0 && this.state.isLoading && <div className="essay-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div>}
        { !!this.props.collegeEssays && !!this.props.collegeEssays.length && <CollegeEssaysList getPremiumArticles={getPremiumArticles} premiumArticles={this.props.collegeEssays} />}
      </div>
    );
  }
}

const mapStateToProps = (state) => {
  return {
    hashedUserId: state.user && state.user.data && state.user.data.hashed_user_id,
    collegeEssays: state.collegeEssays
  }
}

const mapDispatchToProps = (dispatch) => {
  return{
    getPremiumArticles: (hashedUserId) => dispatch(getPremiumArticles(hashedUserId))
  }
}

export default connect(mapStateToProps, mapDispatchToProps)(CollegeEssays);
