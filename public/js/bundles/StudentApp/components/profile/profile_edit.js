import React from 'react';
import './styles.scss';

import {
    connect
} from 'react-redux';
import {
    getStudentProfile,
    saveLikedColleges,
    uploadProfilePicture,
    getSkillsAndEndorsements,
    saveSkillsAndEndorsements,
    saveClaimToFameSection,
    getProfileClaimToFame,
    searchForCollegesWithLogos,
    insertPublicProfilePublication,
    removePublicProfilePublication,
    getProjectsAndPublications,
    removeLikedCollege,
    getLikedColleges,
    searchForMajors,
    searchForJobs,
    getSchoolNames,
    getCountries,
    getStates,
    saveMeSection,
    getProfileEducation,
    saveProfileEducation,
} from './../../actions/Profile';

import Edit_section from './profile_edit_section';
import Profile_edit_header_banner from './profile_edit_header_banner';
import Profile_edit_profile from './profile_edit_profile';
import Profile_edit_claim_to_fame from './profile_edit_claim_to_fame';
import Profile_edit_education from './profile_edit_education';
import Profile_edit_objective from './profile_edit_objective';
import Profile_edit_occupation from './profile_edit_occupation';
import Profile_edit_skills_endorsements from './profile_edit_skills_endorsements';
import Profile_edit_projects_publications from './Profile_edit_projects_publications';
import Profile_edit_liked_colleges from './Profile_edit_liked_colleges';
// import Profile_edit_recommendations from './profile_edit_recommendations';
// import Profile_people_also_viewed from './profile_people_also_viewed';

import ProfileOption from './profileOption';
import Avatar from './../../../utilities/roundPortrait';
import Loader from './../../../utilities/loader';

class Profile_edit extends React.Component{
	
	constructor(props){
		super(props);

		this.state = {
			iID: null,
			cID: null,
			sID: null,
            openEdit: '',
		}

		this._searchMajor = this._searchMajor.bind(this);
		this._save = this._save.bind(this);
		this._searchForJobs = this._searchForJobs.bind(this);
		this._findSchools = this._findSchools.bind(this);
        this._removeLikedCollege = this._removeLikedCollege.bind(this);
        this._handleEditRef = this._handleEditRef.bind(this);

        this.editRef;
	}

	componentWillMount(){
		let {dispatch} = this.props;

		dispatch(getStudentProfile());
        dispatch(getProfileEducation());
        dispatch(getProfileClaimToFame());
        dispatch(getProjectsAndPublications());
        dispatch(getSkillsAndEndorsements());
        dispatch(getLikedColleges());
	}
    componentDidMount(){
        if(window.location.pathname === '/social/edit-profile'){
            let params = (new URL(window.location)).searchParams;
            let step = params.get('step');
            this.setState({openEdit: step});
        }
    }

    componentWillReceiveProps(newProps) {
        const { _profile } = this.props;
        const { _profile: _newProfile, dispatch } = newProps;
    }

    _handleEditRef(ref, step) {
        if(step !== this.state.openEdit){ return; }
        this.editRef = ref;
        setTimeout(() => {window.scrollTo(0,this.editRef.offsetTop)}, 200);
    }

    _removeLikedCollege(college) {
        const { dispatch } = this.props;

        dispatch(removeLikedCollege(college));
    }
    
	_findSchools(term, level){
		let {dispatch, _profile} = this.props;
		let {sID} = this.state;

		window.clearInterval(sID);

		let mID = window.setTimeout(() => dispatch(getSchoolNames({input: term}, level)), 200);
		this.setState({sID: mID});
	}
	_searchForJobs(qry){
		let {dispatch} = this.props;
		let {iID} = this.state;

		window.clearInterval(iID);

		let mID = window.setTimeout(() => dispatch(searchForJobs(qry)), 200);
		this.setState({iID: mID});

	}
	_searchMajor(qry){
		let {dispatch} = this.props;
		let {iID} = this.state;

		window.clearInterval(iID);

		let mID = window.setTimeout(() => dispatch(searchForMajors(qry)), 200);
		this.setState({iID: mID});

	}
    _insertPublicProfilePublication = ({url, title, callback}) => {
        const { dispatch } = this.props;

        dispatch(insertPublicProfilePublication({url, title, callback}));
    }

    _removePublicProfilePublication = (publication_id) => {
        const { dispatch } = this.props;

        dispatch(removePublicProfilePublication(publication_id));
    }

    _searchForCollegesWithLogos = (input) => {
        const { dispatch } = this.props;

        dispatch(searchForCollegesWithLogos(input));
    }

	_save(data, callback){
		let {dispatch} = this.props;

		dispatch(saveMeSection(data, callback));
	}

    _somethingPending = () => {
        const { _profile } = this.props;

        for (let key in _profile) {
            if (key.toLowerCase().endsWith('pending') && key !== 'searchForCollegesWithLogosPending') {
                if (_profile[key] === true) {
                    return true;
                }
            }
        }

        return false;
    }

    _saveProfileEducation = (data, callback) => {
        const { dispatch } = this.props;

        dispatch(saveProfileEducation(data, callback));
    }

    _saveClaimToFame = (data, callback) => {
        const { dispatch } = this.props;

        dispatch(saveClaimToFameSection(data, callback));
    }

    _saveSkillsAndEndorsements = (data, callback) => {
        const { dispatch } = this.props;

        dispatch(saveSkillsAndEndorsements(data, callback));
    }

    _saveLikedColleges = (data, callback) => {
        const { dispatch } = this.props;

        dispatch(saveLikedColleges(data, callback));
    }

    _uploadProfilePicture = (data, callback) => {
        const { dispatch } = this.props;

        dispatch(uploadProfilePicture(data, callback));
    }

	render(){

		let {_profile, dispatch} = this.props;

		let name = (_profile.fname || " ") + " " + (_profile.lname || " ");
		let percent = (_profile.profile_percent || 0) + "% Complete";
        let nonStudents = ['alumni', 'parent', 'counselor', 'university_rep']

		return(
			<div>
				
				{ _profile.init_profile_pending || !_profile.fname ?
					<div className="edit-profile-loader"><img src="/social/images/plexuss-loader-test-2.gif" /></div>
					:
					<div className="_profile_edit">
                        <Profile_edit_header_banner _profile={_profile} />
						<span ref={(ref) => this._handleEditRef(ref, 'basic-info')}><Profile_edit_profile autoOpenEdit={this.state.openEdit === 'basic-info'} autoOpenPicture={this.state.openEdit === 'profile-picture'} uploadProfilePicture={this._uploadProfilePicture} _profile={_profile} getCountries={() => dispatch(getCountries())} findSchools={this._findSchools} getStates={() => dispatch(getStates())} save={this._save} /></span>
                         { !!_profile.education && <span ref={(ref) => this._handleEditRef(ref, 'education')}><Profile_edit_education autoOpenEdit={this.state.openEdit === 'education'} _profile={_profile} findSchools={this._findSchools} searchForMajors={this._searchMajor} save={this._saveProfileEducation} /></span> }
                        <span ref={(ref) => this._handleEditRef(ref, 'claim-to-fame')}><Profile_edit_claim_to_fame autoOpenEdit={this.state.openEdit === 'claim-to-fame'} _profile={_profile} save={this._saveClaimToFame} /></span>
                        { _profile.user_type === 'student' && <span ref={(ref) => this._handleEditRef(ref, 'objective')}><Profile_edit_objective autoOpenEdit={this.state.openEdit === 'objective'} _profile={_profile} searchForMajors={this._searchMajor} searchForJobs={this._searchForJobs} save={this._save} /></span>}
                        { nonStudents.includes(_profile.user_type) && <span ref={(ref) => this._handleEditRef(ref, 'occupation')}><Profile_edit_occupation autoOpenEdit={this.state.openEdit === 'occupation'} _profile={_profile} searchForJobs={this._searchForJobs} save={this._save} /></span>}
                        { _profile.getSkillsAndEndorsementsPending === false && <span ref={(ref) => this._handleEditRef(ref, 'skills-endorsements')}><Profile_edit_skills_endorsements autoOpenEdit={this.state.openEdit === 'skills-endorsements'} _profile={_profile} saveSkillsAndEndorsements={this._saveSkillsAndEndorsements} /></span> }
                        {/* <Profile_edit_recommendations _profile={_profile} save={this.save} /> */}
                        <span ref={(ref) => this._handleEditRef(ref, 'projects-publications')}><Profile_edit_projects_publications autoOpenEdit={this.state.openEdit === 'projects-publications'} _profile={_profile} insertPublicProfilePublication={this._insertPublicProfilePublication} removePublicProfilePublication={this._removePublicProfilePublication} save={this.save} /></span>
                        { _profile.getLikedCollegesPending === false && <span ref={(ref) => this._handleEditRef(ref, 'liked-colleges')}><Profile_edit_liked_colleges autoOpenEdit={this.state.openEdit === 'liked-colleges'} saveLikedColleges={this._saveLikedColleges} _profile={_profile} removeLikedCollege={this._removeLikedCollege} searchForColleges={this._searchForCollegesWithLogos} save={this.save} /></span> }
                        {/* <Profile_people_also_viewed _profile={_profile} /> */}

                        {/* _profile.saveMePending || _profile.removeLikedCollegePending && <Loader /> */}
						{ this._somethingPending() && <Loader /> }

					</div> }
			</div>
		);
	}
};

const mapStateToProps = (state, props) => {
	return {
		_profile: state._profile
	}
}

export default connect(mapStateToProps)(Profile_edit);