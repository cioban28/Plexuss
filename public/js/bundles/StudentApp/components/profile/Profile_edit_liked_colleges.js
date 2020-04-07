import React, {Component} from 'react';

import {searchForMajors} from './../../actions/Profile';

import { confirmAlert } from 'react-confirm-alert';

import Edit_section from './profile_edit_section';

import CollegeLogoAvatar from './CollegeLogoAvatar';

import AddCollegeButton from './AddCollegeButton';

import { isEmpty, filter, find, findIndex } from 'lodash';

import AddCollegeModal from './AddCollegeModal';

import Profile_edit_privacy_settings from './profile_edit_privacy_settings'
import AddMoreColleges from '../../../SocialApp/components/Manage_Colleges/AddMoreColleges';

const EMPTY_ARRAY = [];

export default class Profile_edit_liked_colleges extends Component{

    constructor(props){
        super(props);

        const { _profile } = props;

        this.state = {
            editedLikedColleges: _profile.likedColleges || EMPTY_ARRAY,
            submittable: false,
            showAll: false,
            pendingRemovedIds: [],
            modalOpen: false,
        }

        this.baseState = this.state;

        this._remove = this._remove.bind(this);
    }

    componentDidUpdate(prevProps, prevState){
        if(prevProps._profile.likedColleges !== this.props._profile.likedColleges ){
            this.setState({editedLikedColleges: this.props._profile.likedColleges})
        }
    }

    _onCancelEditing = () => {
        let { _profile } = this.props;
        if(this.state.editedLikedColleges !== _profile.likedColleges){ this.setState({editedLikedColleges: _profile.likedColleges}); }
    }

    _onCollegeDelete = (college) => {
        const { likes_tally_id, school_name } = college;
        const { pendingRemovedIds, editedLikedColleges } = this.state;

        const newPendingRemovedIds = pendingRemovedIds.slice();

        if (!college.isNew) {
            newPendingRemovedIds.push(college.id);
        }

        const newList = filter(editedLikedColleges, (currentCollege) => college.id !== currentCollege.id);

        this.setState({ editedLikedColleges: newList, pendingRemovedIds: newPendingRemovedIds }, this._validate);
    }

    _onCollegeAdd = () => {
        const { editedLikedColleges } = this.state;
        const { _profile } = this.props;
        const { MyCollegeList } = _profile;

        this._toggleAddCollegeModal();

        const newList = editedLikedColleges.slice();

        MyCollegeList && MyCollegeList.forEach(college => {
            const alreadyExist = findIndex(newList, (currentCollege) => currentCollege.id === college.id) !== -1;

            if (!alreadyExist) {
                college.isNew = true;
                newList.push(college);
            }
        });

        this.setState({ editedLikedColleges: newList }, this._validate);

        this._save(() => {});
    }

    _validate = () => {
        const { pendingRemovedIds, editedLikedColleges } = this.state;

        let valid = false;

        if (!isEmpty(pendingRemovedIds)) {
            valid = true;
        }

        if (!valid) {
            valid = findIndex(editedLikedColleges, (college) => college.isNew) !== -1;
        }

        this.setState({ submittable: valid });
    }

    getCollegeLogoUrl(college) {
        return college.isNew ? college.logo_url : `https://s3-us-west-2.amazonaws.com/asset.plexuss.com/college/logos/${college.logo_url}`;
    }

    _remove(college){
        let { removeLikedCollege } = this.props;
        removeLikedCollege(college);
    }

    _buildCollegeAvatar = (college, index) => (
        <CollegeLogoAvatar
            key={index}
            slug={college.slug}
            school_name={college.school_name}
            url={this.getCollegeLogoUrl(college)} />
    )

    _buildEditCollegeAvatar = (college, index) => (
        <CollegeLogoAvatar
            key={index}
            slug={college.slug}
            onDelete={() => this._remove(college)}
            school_name={college.school_name}
            url={this.getCollegeLogoUrl(college)} />
    )

    _toggleAddCollegeModal = () => {
        const { modalOpen } = this.state;

        this.setState({ modalOpen: !modalOpen });
    };

    _save = (callback) => {
        const { saveLikedColleges } = this.props;
        const { pendingRemovedIds, editedLikedColleges } = this.state;

        const newLikedColleges = filter(editedLikedColleges, (college) => college.isNew);

        const data = {
            newLikedColleges,
            pendingRemovedIds,
        }

        saveLikedColleges(data, callback);
    }

    render(){
        const { submittable, editedLikedColleges, modalOpen } = this.state;
        const { _profile, searchForColleges, saveLikedColleges } = this.props;

        return(
            <Edit_section autoOpenEdit={this.props.autoOpenEdit} editable={true} saveHandler={this._save} onCancelEditing={this._onCancelEditing} submittable={submittable} hideSaveCancel={true}>
                {/* Preview section */}
                <div>
                    <div className="green-title">Liked Colleges
                        <Profile_edit_privacy_settings section="liked_colleges" initial={!!_profile.public_profile_settings ? !!_profile.public_profile_settings.liked_colleges ? _profile.public_profile_settings.liked_colleges : null : null}/>
                    </div>
                    <div className="profile-liked-colleges-container">
                        { !_profile.getLikedCollegesPending && !isEmpty(_profile.likedColleges) &&
                            _profile.likedColleges.map(this._buildCollegeAvatar) }
                    </div>
                </div>

                {/* Edit section */}
                <div>
                    <div className="green-title">Liked Colleges</div>
                    <div className="profile-liked-colleges-container">
                        { !_profile.getLikedCollegesPending &&
                            editedLikedColleges.map(this._buildEditCollegeAvatar) }

                        <AddCollegeButton  toggleModal={this._toggleAddCollegeModal} />

                        {
                            this.state.modalOpen && <AddMoreColleges
                                onProfile={true}
                                closeModal={this._toggleAddCollegeModal}
                                handleAddMore={this._onCollegeAdd.bind(this)}
                            />
                        }
                    </div>
                </div>
            </Edit_section>
        );

    }
}
