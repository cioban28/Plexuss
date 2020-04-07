import React, { Component } from 'react'
import { Link } from 'react-router-dom'
import { connect } from 'react-redux'
import CircularProgressbar from 'react-circular-progressbar'

import { getUserData } from './../../../api/post'
import { APP_ROUTES } from './../../OneApp/constants'

import './progressbar.scss'
import { getStudentProfile, getProfileData } from '../../../../StudentApp/actions/Profile';
import { toggleSlidingMenu } from '../../../actions/slidingMenu';

class CollegeApplicaion extends Component{
    constructor(props){
        super(props)

        this.state = {
            completeSections: [],
            incompleteSections: [],
            optionalSections: [],
            showComplete: true,
            showIncomplete: true,
            showOptional: true,
        }
    }

    componentDidMount() {
        if(Object.keys(this.props._profile).length < 10){
            this.props.dispatch(getStudentProfile());
            this.props.dispatch(getProfileData());
        }
        let { user } = this.props;

        let reviewSection = APP_ROUTES.findIndex(section => section.id === 'review');
        if(user.oneapp_step){
            let currectSection = APP_ROUTES.findIndex(section => section.id === user.oneapp_step);
            let complete = APP_ROUTES.slice(0, currectSection);
            let incomplete = APP_ROUTES.slice(currectSection,reviewSection);
            this.setState({completeSections: complete, incompleteSections: incomplete});
        }else {
            this.setState({incompleteSections: APP_ROUTES.slice(0, 10)});
        }
        this.setState({optionalSections: APP_ROUTES.slice(reviewSection+1)})
    }

    _filterSections = () => {
        let { _profile } = this.props,
        routesToSkipInSic = ['review', 'sponsor', 'essay', 'uploads', 'demographics'],
        complete = [], incomplete = [], optional = [];
        !(_profile.txt_opt_in > 0) && routesToSkipInSic.push('verify')
        APP_ROUTES.map(route => {
            if(!routesToSkipInSic.includes(route.id)) {
                (this.props._profile[route.id+'_form_done'])  ?
                    complete.push(route) :
                    (
                        (route.id === 'verify' && this.props._profile.verified_phone === 1) ||
                        (route.id === 'applications' && this.props._profile.MyApplicationList.length > 0)
                    ) ?
                        complete.push(route) :
                        incomplete.push(route)
            }})
            return {complete, incomplete, optional};
        }


    render(){
        let _routes = this._filterSections()
        let { user, type, dispatch } = this.props;
        return(
            <div className="one-app-links-container" >
                <ProgressHeader percentage={user.one_app_percent || 0}/>
                <div className='one-app-sections one-app-sections-adjusted'>
                    {!!_routes && <CompletedSection dispatch={dispatch} sections={_routes.complete} show={this.state.showComplete} toggle={ () => this.setState({showComplete: !this.state.showComplete}) }/>}
                    {!!_routes && <InCompleteSection dispatch={dispatch} type={type} sections={_routes.incomplete} show={this.state.showIncomplete} toggle={ () => this.setState({showIncomplete: !this.state.showIncomplete}) }/>}
                    <OptionalSection dispatch={dispatch} sections={this.state.optionalSections} _profile={this.props._profile} show={this.state.showOptional} toggle={ () => this.setState({showOptional: !this.state.showOptional}) }/>
                </div>
                <li className="one-app-review-btn-container">
                    <Link className="one-app-review-btn" to={'/social/one-app/review'} onClick={() => { dispatch(toggleSlidingMenu()) }}>Review Application</Link>
                </li>
            </div>
        )
    }
}

class ProgressHeader extends Component{
    render(){
        let { percentage } = this.props;
        return(
            <li className="sub_header" onClick={() => window.location.href='/social/one-app'}>
                    <div className="progress_bar">
                        <CircularProgressbar
                            percentage={!!percentage ? (percentage > 100) ? 100 : percentage : 0}
                            text={`${!!percentage ? (percentage > 100) ? 100 : percentage : 0}%`}
                            />
                    </div>
                    <div className="text">
                        <div className="title">College Application</div>
                        <div className="sub_title ">{(percentage || 0) + '% Complete'}</div>
                    </div>
            </li>
        )
    }
}

class CompletedSection extends Component{
    render(){
        let { sections, show, toggle, dispatch } = this.props;
        return(
            <div>
                <div className="row section">
                    <div className="larger-11 medium-11 small-11 columns">
                        <span className="section-title">Complete Sections</span>
                    </div>
                    <div className="larger-1 medium-1 small-1 columns" onClick={toggle}>
                        {show ? '-' : '+'}
                    </div>
                </div>
                {show &&
                    <span>
                        {sections.map((section, i) => <CompletedCard dispatch={dispatch} key={i} text={section.name} path={section.path}/> )}
                    </span>
                }
            </div>
        )
    }
}

class InCompleteSection extends Component{
    render(){
        let { type, sections, show, toggle, dispatch } = this.props;
        return(
            <span>
                <div className="row section">
                    <div className="larger-11 medium-11 small-11 columns">
                        <span className="section-title">Incomplete Sections</span>
                    </div>
                    <div className="larger-1 medium-1 small-1 columns" onClick={toggle}>
                        {show ? '-' : '+'}
                    </div>
                </div>
                {show &&
                    <span>
                        {sections.map((section, i) => <IncompleteCard dispatch={dispatch} key={i} text={section.name} path={section.path}/> )}
                    </span>
                }
                {/*
                    type == "Intl" &&
                    <span>
                        <IncompleteCard text={'Upload Documents'}/>
                        <IncompleteCard text={'Add Financials'}/>
                        <IncompleteCard text={'Upload Transcripts'}/>
                        <IncompleteCard text={'Upload Passport'}/>
                    </span> ||
                    type == "US" &&
                    <span>
                        <IncompleteCard text={'Add contact info'}/>
                        <IncompleteCard text={'Add GPA'}/>
                        <IncompleteCard text={'Add SAT'}/>
                    </span>

                */}
            </span>
        )
    }
}
class OptionalSection extends Component{
    optionalComplete = (section) => {
        switch(section.id){
            case 'essay':
                return this.props._profile.essay_form_done
            case 'demographics':
                return this.props._profile.demographics_form_done ||
                    (   this.props._profile.ethnicity &&
                        this.props._profile.religion &&
                        this.props._profile.gender &&
                        this.props._profile.family_income )
            case 'uploads':
                return !!this.props._profile.transcripts && this.props._profile.transcripts.length > 0
            case 'sponsor':
                return this.props._profile.sponsor_form_done
            }
    }
    render(){
        let { sections, show, toggle, dispatch } = this.props;
        if (sections.length > 0) {
            if (this.props._profile.country_code == 'US') {
                if (sections.filter(x => x.id == 'sponsor').length)
                {
                    sections.pop();
                    sections.filter(x => x.id == 'uploads')[0].next = 'review'
                }

            }
        }
        return(
            <span>
                <div className="row section">
                    <div className="larger-11 medium-11 small-11 columns">
                        <span className="section-title">Optional Sections</span>
                    </div>
                    <div className="larger-1 medium-1 small-1 columns" onClick={toggle}>
                        {show ? '-' : '+'}
                    </div>
                </div>
                {show &&
                    <span>
                        {sections.map((section, i) => this.optionalComplete(section) ?
                            <CompletedCard dispatch={dispatch} key={i} text={section.name} path={section.path}/> :
                            <IncompleteCard dispatch={dispatch} key={i} text={section.name} path={section.path}/>
                        )}
                    </span>
                }
            </span>
        )
    }
}

class CompletedCard extends Component{
    render(){
        let { text, path, dispatch } = this.props;
        return(
            <li className="card1_li">
                <div className="checkMark_parent">
                    <i className="fa fa-check checkMark finished"></i>
                </div>
                <Link className="text finished" to={path} onClick={() => { dispatch(toggleSlidingMenu()) }}>{text}</Link>
            </li>
        )
    }
}
class IncompleteCard extends Component{
    render(){
        let { text, path, dispatch } = this.props;
        return(
            <li className="card1_li">
                <div className="checkMark_parent">
                    <i className="fa fa-check checkMark"></i>
                </div>
                <Link className="text" to={path} onClick={() => { dispatch(toggleSlidingMenu()) }}>{text}</Link>
            </li>
        )
    }
}

const mapStateToProps = (state) =>{
  return{
    user: state.user.data,
    _profile: state._profile
  }
}

export default connect(mapStateToProps)(CollegeApplicaion);
