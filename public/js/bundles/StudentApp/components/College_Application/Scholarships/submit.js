import React from 'react';
import './styles.scss';

import {connect} from 'react-redux';
import {browserHistory} from 'react-router';

import { TOC, SIGN } from './../constants'

import ScholarshipTableHeaders from './scholarshipTableHeaders';
import ScholarshipRow from './scholarshipRow';
import TextField from './../TextField';
import SaveButton from './../SaveButton';
import CheckboxField from './../CheckboxField';
import {getScholarships, saveApplication, resetSaved} from './../../../actions/Profile'; 

var PAGE_DONE = '';

class Scholarships extends React.Component{
    
    constructor(props){
        super(props);

        this.state ={
            today: ''
        }
    }
    componentWillMount(){
        let {dispatch, route, _profile} = this.props;
        dispatch(getScholarships());

        PAGE_DONE = route.id+'_form_done';
        this.setState({today: moment().format('MM/DD/YYYY')});
    }
    componentWillReceiveProps(np){
        let { dispatch, route, _profile } = this.props;

        // after saving, reset saved and go to next route
        if( np._profile.save_success !== _profile.save_success && np._profile.save_success ){
            dispatch( resetSaved() );
            browserHistory.push('/college-application/'+ route.next + window.location.search);  
        }
    }
    _submitApplication(e){
        e.preventDefault();
        
        let { dispatch, _profile } = this.props;
        let list = [];
        let schList = _profile.scholarshipsList;

        //back end expects a list of ids only
        for(let i in schList){
            list.push(schList[i].id);
        }

        if( _profile[PAGE_DONE] ){
            // let form = _.omitBy( _profile, (v, k) => k.includes('list') );
            dispatch( saveApplication( {scholarships: list, page: 'scholarship-submission'}, 'scholarships',  _profile.oneApp_step) );
        }
    }

    render(){
        let { today } = this.state;
        let {route, _profile} = this.props;

        return(
            <div className="_Scholarships">
                <div className="sch-header-title">Submit Scholarships</div>

                <div className="sch-table-container">
                    <ScholarshipTableHeaders />

                    <div className="sch-table-content-box">
                        
                        {_profile.init_scholarships_pending && <div className="spin-loader"></div>}

                        {(!_profile.scholarshipsList || _profile.scholarshipsList.length < 1 ) && !_profile.init_scholarships_pending  &&
                            <div className="no-sch-msg">No Scholarships Found</div> }

                        {_profile.scholarshipsList.map((item, i) => 
                            <ScholarshipRow key={'sch'+i} item={item} />
                        )}
                        

                    </div>
                </div>

                <form onSubmit={(e) => this._submitApplication(e)} >

                <div className="mid-head">Signature</div>
                <div className="affirm-msg">Please affirm the following before you submit your application.</div>
            
                <CheckboxField field={ TOC } {...this.props} />

                <div className="sign">
                    <TextField field={ SIGN } {...this.props} />
                    <div className="sign-date">
                        <div>Date</div>
                        <div>{ today || '' }</div>
                    </div>
                </div>

                <SaveButton 
                    label={'Submit Scholarships'}
                    _profile={_profile}
                    error_msg={'Please ensure all sections are completed'}
                    page_done={PAGE_DONE} />

                </form>

            </div>
        );
    }
}

const mapStateToProps = (state, props) => {
    return {
        _profile: state._profile
    };
}

export default connect(mapStateToProps)(Scholarships);