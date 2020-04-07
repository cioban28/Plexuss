import React from 'react';
import './styles.scss';

import {connect} from 'react-redux';
import {browserHistory} from 'react-router';

import { TOC, SIGN } from './../constants';
import { isEmpty, findIndex } from 'lodash';
import ReactSpinner from 'react-spinjs-fix';
import ScholarshipTableHeaders from './scholarshipTableHeaders';
import ScholarshipRow from './scholarshipRow';
import TextField from './../TextField';
import SaveButton from './../SaveButton';
import CheckboxField from './../CheckboxField';
import {getScholarships, getAllScholarshipsNotSubmitted, toggleSelectScholarship, saveApplication, resetSaved} from './../../../actions/Profile';
import Tooltip from 'react-tooltip';
import moment from 'moment';
import { APP_ROUTES } from '../../../../SocialApp/components/OneApp/constants';

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
		dispatch(getAllScholarshipsNotSubmitted());
        dispatch(getScholarships());

		PAGE_DONE = route.id+'_form_done';
		this.setState({today: moment().format('MM/DD/YYYY')});
	}

	componentWillReceiveProps(np){
		let { dispatch, route, _profile } = this.props;

		// after saving, reset saved and go to next route
		if( np._profile.save_success !== _profile.save_success && np._profile.save_success ){
			dispatch( resetSaved() );
      if(this.props.location.pathname.includes('social')) {
        let nextIncompleteStep = '';
        let formDonePropertyName = '';
        const routeIndex = APP_ROUTES.findIndex(r => r.id === route.id);
        if(routeIndex !== -1) {
          const nextStepsRoutes =  APP_ROUTES.slice(routeIndex+1);
          for(let nextRoute of nextStepsRoutes) {
            formDonePropertyName = `${nextRoute.id}_form_done`;
            if(_profile.hasOwnProperty(formDonePropertyName) && !_profile[formDonePropertyName]) {
              nextIncompleteStep = nextRoute;
              break;
            }
          }
        }
        const nextRoute = !!nextIncompleteStep ? nextIncompleteStep.id : route.next;
        this.props.history.push('/social/one-app/'+nextRoute + window.location.search)
      } else {
        browserHistory.push('/college-application/'+route.next + window.location.search)
      }
		}
	}

    _onSelect = (scholarship) => {
        const { dispatch } = this.props;

        dispatch(toggleSelectScholarship(scholarship));
    }

    _saveAndContinue = (event) => {
        event.preventDefault();

        const { dispatch, _profile } = this.props;
        const currentPage = _profile.oneApp_step;
        const scholarshipsList = _profile.scholarshipsList;

        const save_pending = _profile.save_pending;

        if (save_pending === true) return;

        const form = { scholarshipsList, page: 'scholarships' };

        dispatch(saveApplication(form, 'scholarships', currentPage));
    }

    _onSkip = () => {
        const { route } = this.props;

        this.props.location.pathname.includes("social") ? this.props.history.push('/social/one-app/'+route.next + window.location.search) : browserHistory.push('/college-application/'+route.next + window.location.search);
    }

	render(){
		const { today } = this.state;
		const {route, _profile} = this.props;
        const selectedLength = isEmpty(_profile.scholarshipsList) ? 0 : _profile.scholarshipsList.length;
        const save_pending = _profile.save_pending;

		return(
			<div className="_Scholarships _ScholarshipsSelect">
				<div className="sch-header-title">Select Scholarships</div>

                <div className="select-scholarships-header-border">
                    <div className='header-border-leftside'>
                        <div className="select-count" data-tip data-for='selected-count'>{selectedLength}</div>
                        <Tooltip id='selected-count' effect='solid' place='right'>
                            <span>You have { selectedLength } pending scholarships. To submit these scholarships, please complete the full college application.</span>
                        </Tooltip>
                        <div className="header-border-text">Congrats! You qualify for scholarships. Choose the ones you would like to be considered for.</div>
                    </div>

                    <div className='header-border-rightside'>

                    </div>
                </div>

				<div className="sch-table-container" style={{paddingBottom: '114px'}}>
					<ScholarshipTableHeaders selectMode={true} />

					<div className="sch-table-content-box">

						{_profile.getAllScholarshipsNotSubmittedPending === true && <div className="spin-loader"></div>}

						{(!_profile.allScholarshipsNotSubmitted || _profile.allScholarshipsNotSubmitted.length < 1 ) && !_profile.getAllScholarshipsNotSubmittedPending  &&
							<div className="no-sch-msg">No Scholarships Found</div> }

						{!isEmpty(_profile.allScholarshipsNotSubmitted) && _profile.allScholarshipsNotSubmitted.map((item, i) =>
							<ScholarshipRow
                                isSelected={findIndex(_profile.scholarshipsList, (sch) => sch.id === item.id) !== -1}
                                selectMode={true}
                                onSelect={this._onSelect}
                                key={'sch'+i}
                                item={item} />
						)}

					</div>
				</div>

                { (_profile.getAllScholarshipsNotSubmittedPending || _profile.init_scholarships_pending) &&
                    <div className="spin-container"><ReactSpinner color="#24b26b" /></div> }
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
