import React, { Component } from 'react'
import { connect } from 'react-redux'
import { Link } from 'react-router-dom'
import ReactDom from 'react-dom'
import axios from 'axios'
import ApplicationsList from './ApplicationsList'
import ApplicationsMobileList from './ApplicationsMobileList'
import {  getProfileDataLists } from '../../actions/Profile'


class MyApplicationsSocial extends Component {
    constructor(props){
        super(props)
    }

    // componentDidMount(){
    //     this.props.getMyApplicationList()

    // }

    render(){
   let { _profile, _user, route } = this.props;
        return(
            <div id='select-colleges-social'>
                <div className='all-content'>
                    <div className='heading-div'>
                        <span className='heading-span'>
                            <span className='heading-application'>My Applications</span>
                            <Link to={'/social/one-app/colleges'} className='heading-more-colleges hide-for-small-only'>Add more colleges</Link>
                            <Link to={'/social/one-app/colleges'} className='heading-more-colleges-btn show-for-small-only'>Add more colleges</Link>
                        </span>
                    </div>
                    		<div className="hide-for-small-only">
	                        <ApplicationsList myApplicationsList={this.props._profile.MyApplicationList} />
                        </div>
                        <div className="show-for-small-only" style={{marginBottom: '85px'}}>
						            {!!this.props._profile.MyApplicationList && this.props._profile.MyApplicationList.map((college, index) => <ApplicationsMobileList key={index} college={college} />)}
                        </div>
                </div>
            </div>
            )
    }
}

const mapStateToProps = (state,  props) => {
 return {
   _user: state._user,
   _profile: state._profile,
 }
}

const mapDispatchToProps = (dispatch) => {
    return{
    getMyApplicationList: () => dispatch(getProfileDataLists()),
    }
} 

export default connect(mapStateToProps, mapDispatchToProps)(MyApplicationsSocial);

