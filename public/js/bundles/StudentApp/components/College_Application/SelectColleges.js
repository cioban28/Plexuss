// /College_Application/SelectColleges

import React from 'react'
import selectn from 'selectn'
import { connect } from 'react-redux'
import { Link, browserHistory } from 'react-router'
import DocumentTitle from 'react-document-title'
import { isEmpty } from 'lodash'
import TextField from './TextField'
import MyCollege from './MyCollege'
import SaveButton from './SaveButton'
import ReactSpinner from 'react-spinjs-fix'
import Intermission from './Intermission'
import SortBar from './../common/SortBar'
import PrioritySchool from './PrioritySchool'
import CustomModal from './../common/CustomModal'
import ConversionOption from './../common/SortBar/conversionOption'
import EDXCollegeLink from './EDXCollegeLink'
import { UNQUALIFIED } from './constants'
import { SORT_COLS } from './../Intl_Students/constants'
import { updateProfile, sortCols, saveApplication, resetSaved, clearChangedFields, confirmSignupFacebookShare } from './../../actions/Profile'
import { getPrioritySchools } from './../../actions/Intl_Students'
import { saveSignupFacebookShare, updateEligibleColleges } from './../../actions/User'
import CollegeLearnMoreModal from './CollegeLearnMoreModal'

var PAGE_DONE = '';

class SelectColleges extends React.Component{
	constructor(props) {
		super(props)
		this.state = {
			firstHalfDone: false,
            showLearnMoreModal: false,
            showModal: true,
		}
		this.closeModal = this.closeModal.bind(this)
		this._openFacebookShare = this._openFacebookShare.bind(this)
		this._saveAndContinue = this._saveAndContinue.bind(this)
		this._buildMyList = this._buildMyList.bind(this)
	}

	closeModal(){
		this.setState({showModal: false});
	}

	_openFacebookShare() {
        let { dispatch, _user, _profile } = this.props,
            utm_term = _user.email_hashed.slice(-53),
            user_id = _user.user_id;

        amplitude.getInstance().logEvent('share sign up link click', { Method: 'facebook share', Location: 'oneapp colleges' });

        FB.ui({
                method: 'share',
                quote: 'I just applied to universities on Plexuss.com. The site doesn\'t charge for applying to universities.',
                href: 'https://plexuss.com/signup?utm_source=SEO&utm_medium=oneapp&utm_content=additional_apps&utm_campaign=referral&utm_term=' + utm_term,
            },
            // callback
            function(response) {
                if (response && !response.error_message) {
                    dispatch( saveSignupFacebookShare(user_id, utm_term) );
                } else {
                    amplitude.getInstance().logEvent('share sign up link cancel', { Method: 'facebook share', Location: 'oneapp colleges' });
                }
            }
        );
    }

	_saveAndContinue(e){
		e.preventDefault();
		
		let { dispatch, _profile } = this.props;

		if( _profile[PAGE_DONE] ){
			let form = _.omitBy( _profile, (v, k) => k.includes('list') );
			dispatch( saveApplication(form, 'colleges', _profile.oneApp_step) );
		}
	}

	_buildMyList(){
		let { _profile, _user } = this.props,
			// max = _.get(_user, 'num_of_eligible_applied_colleges', 1);
            max = 10;

		let list = _.times(max, Number); //create an array of number, looping the number of times = to max

		if( selectn('applyTo_schools.length', _profile) ){
			let app_list = _profile.applyTo_schools.slice(),
				spliced = list.splice(app_list.length);

			list = [...app_list, ...spliced];
		}

		return list;
	}

	componentWillMount(){
		let { dispatch, _profile, route } = this.props;

		PAGE_DONE = route.id+'_form_done';
		dispatch( updateProfile({page: route.id}) );
		dispatch( clearChangedFields());

		// set open based on unqualified val to determine whether to show/hide modal
		this.state.open = !!_.get(_profile, 'unqualified_modal');
	}

    componentDidMount(){
        const { _user, _profile, dispatch } = this.props;

        if (_user.premium_user_level_1 === 1) {
            dispatch( updateEligibleColleges({country_id: _profile.country_id}) );
        }

    }

	componentWillReceiveProps(np){
		const { dispatch, route, _profile } = this.props;
        const { _profile: _newProfile } = np;

		// after saving, reset saved and go to next route
		if( np._profile.save_success !== _profile.save_success && np._profile.save_success ){
			dispatch( resetSaved() );

			// if coming_from is set, means redirected from an unnatural path so go back where we came from
			if( np._profile.coming_from ) browserHistory.goBack();
			else this.setState({firstHalfDone: true});
		}

        if (_profile.selectCollegeLearnMorePending !== _newProfile.selectCollegeLearnMorePending && _newProfile.selectCollegeLearnMorePending === false) {

            if (!isEmpty(_newProfile.selectCollegeLearnMoreResponse) && _newProfile.selectCollegeLearnMoreResponse.status === 'success') {
                this.setState({ showLearnMoreModal: true });
            }
        }
	}

	render(){
		let { dispatch, _profile, route, _user } = this.props,
			{ firstHalfDone, showLearnMoreModal } = this.state,
			myList = this._buildMyList(),
			_allowed = _.get(_user, 'num_of_eligible_applied_colleges', 1),
            potential_num_of_applied_colleges = _profile.applyTo_schools ? _profile.applyTo_schools.length : 1,
			modal = _profile.unqualified_modal ? UNQUALIFIED['modal_'+_profile.unqualified_modal] : null,
            has_facebook_shared = _user.has_facebook_shared,
            premium_user_level_1 = _user.premium_user_level_1;

        if (potential_num_of_applied_colleges == 0) {
            potential_num_of_applied_colleges = 1;
        }

		return (
			<DocumentTitle title={"Plexuss | College Application | "+route.name}>
				{ !firstHalfDone ? 
					<div className="application-container select-college-container full">

						<form onSubmit={ this._saveAndContinue }>

							<div className="page-head head-line-height">My Colleges <small className='select-college-small-note'>Select a college below and then click the ‘+’ to add it to My Colleges</small></div>

							{/* _.get(_profile, 'premium_user_type', '') !== 'onetime_plus' && 
								<div className="upgrade-msg">If you want to apply to more than { _allowed } college(s), please <Link to="/premium-plans"><u>upgrade</u></Link> to our Premium Plus plan</div> */}

                            { !has_facebook_shared && !premium_user_level_1 && 
                                <div className='facebook-share-container'>
                                    <div className='facebook-share-message'>
                                        Click to share with friends and apply to an additional college for free on Plexuss!
                                    </div>
                                    <div className='facebook-share-button' onClick={this._openFacebookShare}>
                                        <img className='facebook-icon' src='/images/social/facebook_white.png' /><div className='facebook-share-text'>Share</div>
                                    </div>
                                </div> }

                            <div className='select-college-list-container'>
    							<div className="my-colleges-list selected-college-list">
    								{ myList.map((a, i) => <MyCollege key={a.college_id+'_'+i} classes='select-my-college' i={i+1} college={a} {...this.props} />) }
                                </div>  

								<SaveButton 
									label={'Next'}
									_profile={_profile}
									page_done={PAGE_DONE} />
                            </div>

							<div className="priority-schools">

								<SortBar
									storeObj={ _profile }
									sortAction={ sortCols }
									list={ selectn('list', _profile) }
									columns={ SORT_COLS } />

								<div className="select-college-list list">
									{ (_profile.priority_schools_pending || _profile.selectCollegeLearnMorePending) && <div className="spin-container"><ReactSpinner color="#24b26b" /></div> }

									{ (!selectn('list.length', _profile) && !_profile.priority_schools_pending) && <div className="no-results">No Results</div> }
									
									{ ( !_profile.priority_schools_pending && selectn('list.length', _profile) > 0 ) && 
										_profile.list.map((ps) => <PrioritySchool key={ps.college_id} _skipToIntermission={() => this.setState({firstHalfDone: true})} school={ps} {...this.props} />) }
								</div>

							</div>	

							{/* <div className='select-college-bottom-save-btn'>
								<SaveButton 
									label={'Save & Continue'}
									_profile={_profile}
									page_done={PAGE_DONE} />
							</div> */}
						</form>	

						{ this.state.showModal && modal &&
							<CustomModal>
								<div className="unqualified-modal">
                  <div className="closeMe" onClick={this.closeModal}>&times;</div>
									<div>{ modal.para_1 }</div><br />

									{ modal.link && <div><u><Link to={modal.link}>{ modal.link_name }</Link></u></div> }
									{ modal.link && <br /> }

									<div>{ modal.para_2 }</div><br />

									{ modal.para_3 && <div>{ modal.para_3 }</div> }
									{ modal.para_3 && <br /> }

									{ modal.btn_update ? 
                                        <div className='buttons-container'>
                                          <Link to={ modal.btn_update } className="btn">{ modal.btn }</Link>
										  <Link to={ modal.links.edX } className="btn">Create an edX Account!</Link>
                                        </div>
										: <div className="btn" onClick={ e => dispatch( resetSaved() ) }>{ modal.btn }</div> }

									<br />

                                    { _profile.unqualified_modal == 1 &&
                                        <div className='edx-college-links-container'>
                                            <EDXCollegeLink name={'MIT'} imageURL={'/images/application/mit.png'} link={modal.links.mit} />
                                            <EDXCollegeLink name={'Harvard University'} imageURL={'/images/application/harvard.png'} link={modal.links.harvard} />
                                            <EDXCollegeLink name={'UC Berkeley'} imageURL={'/images/application/berkeley.png'} link={modal.links.berkeley} />
                                        </div> }

									<div><a href="/home"><u>Otherwise click here to see what else you can do on Plexuss.</u></a></div>
								</div>
							</CustomModal> }

                        { showLearnMoreModal && 
                            <CollegeLearnMoreModal 
                                onClose={() => this.setState({showLearnMoreModal: false})} 
                                _profile={_profile} /> }

					</div>
					:
					<Intermission _openFacebookShare={this._openFacebookShare} {...this.props} />
				}
			</DocumentTitle>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		_user: state._user || state.user.data,
		_profile: state._profile,
	};
};

export default connect(mapStateToProps)(SelectColleges);