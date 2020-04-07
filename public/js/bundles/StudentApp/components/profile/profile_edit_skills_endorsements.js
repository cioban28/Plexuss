import React, {Component} from 'react';

import Reorder, { reorder } from 'react-reorder';

import filter from 'lodash/filter';
import isEmpty from 'lodash/isEmpty';
import map from 'lodash/map';
import findIndex from 'lodash/findIndex';
import clone from 'lodash/clone';
import isArray from 'lodash/isArray';

import {searchForMajors} from './../../actions/Profile';

import Edit_section from './profile_edit_section';

import Skill from './Skill';

import Switch from 'react-toggle-switch';

import Tooltip from 'react-tooltip';

import InfoTooltip from './InfoTooltip';

import SkillSectionTooltipContent from './SkillSectionTooltipContent';

import Profile_edit_privacy_settings from './profile_edit_privacy_settings'

// Used for testing START
const FAKE_ENDORSEMENTS = [
    { name: 'Tony Tran', photoUrl: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/1019450_1512082798_plexuss_app_icon_200_png.png' },
    { name: 'Another Guy', photoUrl: 'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/1019450_1512082798_plexuss_app_icon_200_png.png' },
    { name: 'Barack Obama', photoUrl: 'https://www.biography.com/.image/t_share/MTE4MDAzNDEwNzg5ODI4MTEw/barack-obama-12782369-1-402.jpg' },
];
// Used for testing END

const SKILL_DETAIL_TYPES = ['group', 'position', 'awards'];

const EMPTY_ARRAY = [];

const _ = {
    filter: filter,
    isEmpty: isEmpty,
    map: map,
    findIndex: findIndex,
    clone: clone,
    isArray: isArray,
}
export default class Profile_edit_skills_endorsements extends Component {
    constructor(props) {
        super(props);

        const { _profile } = this.props;

        this.state = {
            submittable: false,
            editedList: _profile.skillsAndEndorsementsList,
            activeEditSkill: null,
            reorderEnabled: false,
            newSkillName: '',
            pendingRemovedIds: [],
        }

        this.baseState = this.state;
    }

    componentDidUpdate(prevProps, prevState){
        if(prevProps._profile.skillsAndEndorsementsList !== this.props._profile.skillsAndEndorsementsList ){
            this.setState({editedList: this.props._profile.skillsAndEndorsementsList})
        }
    }

    _valid(input, type, fieldV) {

    }

    _onCancelEditing = () => {
        let { _profile } = this.props;
        if(this.state.editedList !== _profile.skillsAndEndorsementsList){ this.setState({editedList: _profile.skillsAndEndorsementsList}); }
        this.setState({activeEditSkill: null});
    }

    _onReorder = (event, previousIndex, nextIndex) => {
        const list = reorder(this.state.editedList, previousIndex, nextIndex);

        this.setState({
            editedList: list
        });
    }

    _save = (callback) => {
        const { saveSkillsAndEndorsements } = this.props;
        const { editedList, pendingRemovedIds } = this.state;

        const skillsEditedList = _.filter(editedList, (skill) => skill.isEdited && pendingRemovedIds.indexOf(skill.id) === -1);

        const data = {
            skillsEditedList,
            pendingRemovedIds,
        }

        this.setState({activeEditSkill: null});

        saveSkillsAndEndorsements(data, callback);
    }

    _buildEditSkill = (skill, index) => {
        const { activeEditSkill, reorderEnabled } = this.state;
        let classes = 'edit-skill-breadcrumb';

        if (!_.isEmpty(activeEditSkill) && skill.name === activeEditSkill.name) {
            classes += ' active';
        }

        return (
            <li key={index}
                onClick={() => this._toggleActiveEditSkill(skill)}  
                className={classes}>
                    <div className='edit-skill-breadcrumb-name-text'>{skill.name}</div>
                    { !reorderEnabled
                        ? _.isEmpty(activeEditSkill) && <div className='remove-button' onClick={(event) => this._onRemove(event, skill)}>&times;</div>
                        : <div className='hamburger-icon'>&#9776;</div> }

            </li>
        );
    }

    _validate = () => {
        const { editedList, pendingRemovedIds } = this.state;

        const skillsEditedList = _.filter(editedList, (skill) => skill.isEdited && pendingRemovedIds.indexOf(skill.id) === -1);

        const valid = !_.isEmpty(skillsEditedList) || !_.isEmpty(pendingRemovedIds);

        this.setState({ submittable: valid});
    }

    _toggleActiveEditSkill = (skill) => {
        const { activeEditSkill, reorderEnabled } = this.state;

        if (reorderEnabled) return;

        let newSkill = null;

        if (_.isEmpty(activeEditSkill) || activeEditSkill.name !== skill.name) {
            newSkill = skill;
        }

        this.setState({ activeEditSkill: newSkill });
    }

    _toggleReordering = () => {
        const { reorderEnabled } = this.state;

        const newState = {};

        newState['reorderEnabled'] = !reorderEnabled;

        // Do not allow edit mode when reorder mode is enabled.
        if (newState['reorderEnabled']) {
            newState['activeEditSkill'] = null;
        }

        this.setState(newState);
    }

    _onRemove = (event, skill) => {
        event.stopPropagation();

        const { reorderEnabled } = this.props;
        const { editedList, pendingRemovedIds } = this.state;

        let newPendingIds = pendingRemovedIds.slice();

        if (reorderEnabled) return;

        const newList = _.filter(editedList, (currentSkill) => currentSkill.id !== skill.id || currentSkill.name !== skill.name);

        if (skill.id !== 'new') {
            newPendingIds = pendingRemovedIds.concat([skill.id]);
        }

        this.setState({ 
            editedList: newList, 
            activeEditSkill: null, 
            pendingRemovedIds: newPendingIds, 
        }, () => {this._validate;this._save(() => {});} );

    }

    _onChangeNewSkillName = (event) => {
        const id = event.target.id;
        const value = event.target.value;
        const newState = {};

        newState[id] = value;

        newState['newSkillNameValid'] = !!newState[id];

        this.setState(newState);
    }

    _buildSkillDetailsInput = (type, index) => {
        const { activeEditSkill } = this.state;
        const iconClasses = 'edit-skill-details-icon ' + type;
        const formattedType = type[0].toUpperCase() + type.substr(1);

        return (
            <div key={index} className='edit-skill-details-input-container'>
                <div className={iconClasses}></div>
                <div className='edit-skill-details-label'>{formattedType}</div>
                <input data-type={type} onChange={this._onDetailInputChange} value={activeEditSkill[type]} />
            </div>
        );
    }

    _onDetailInputChange = (event) => {
        const { activeEditSkill, editedList } = this.state;
        const detailType = event.target.dataset.type;
        const value = event.target.value;

        const editedListClone = _.map(editedList, _.clone);

        const foundIndex = _.findIndex(editedListClone, (skill) => {
            return skill.name === activeEditSkill.name && skill.id === activeEditSkill.id;
        });

        if (foundIndex === -1) return;

        editedListClone[foundIndex][detailType] = value;

        editedListClone[foundIndex]['isEdited'] = true; // Keep track which ones were edited to save.

        this.setState({ editedList: editedListClone, activeEditSkill: editedListClone[foundIndex] }, this._validate);
    }

    _buildSkill = (skill, index) => {
        const { _profile } = this.props;
        const { fname } = _profile;

        return (
            <Skill
                key={index}
                user_fname={fname}
                name={skill.name}
                endorsements={skill.endorsers}
                group={skill.group}
                position={skill.position}
                awards={skill.awards} />
        );
    }

    _addNewSkill = () => {
        const { newSkillName, editedList } = this.state;

        let newEditedList = null;

        if (!newSkillName) { return; }

        const newSkill = { 
            id: 'new',
            isEdited: true,
            name: newSkillName,
            group: '',
            position: '',
            awards: '',
        };

        newEditedList = [ ...editedList, ...[newSkill]];

        this.setState({
            editedList: newEditedList,
            newSkillName: '',
            newSkillNameValid: false,
            activeEditSkill: newSkill,
        }, this._validate);
    }

    _countEndorsements = () => {
        const { editedList } = this.state; // TODO get list from redux state

        let count = 0;

        if (_.isEmpty(editedList)) return 0;

        editedList.forEach((skill) => {
            count += (_.isArray(skill.endorsers) ? skill.endorsers.length : 0);
        });

        return count;
    }

    _newSkillOnKeyPress = (event) => {
        if (event.key === 'Enter') {
            this._addNewSkill();
        }
    }

    render() {
        const { submittable, activeEditSkill, reorderEnabled, newSkillName, newSkillNameValid, editedList, pendingRemovedIds } = this.state;
        const { _profile } = this.props;

        const skillsAndEndorsementsList = _profile.skillsAndEndorsementsList || EMPTY_ARRAY;

        const addNewSkillButtonClasses = 'add-skill-button' + (!newSkillNameValid ? ' disabled' : '');

        const endorsementCount = this._countEndorsements();

        return(
            <Edit_section autoOpenEdit={this.props.autoOpenEdit} editable={true} saveHandler={this._save} onCancelEditing={this._onCancelEditing} submittable={submittable} hideSaveCancel={_.isEmpty(activeEditSkill)}>
                {/* Preview section */}
                <div>
                    <div className="green-title">
                        Skills & Endorsements&nbsp;
                        <InfoTooltip content={<SkillSectionTooltipContent />} place='right' type='dark'/>
                        {/* endorsementCount > 0 && <span className='endorsement-count'>({endorsementCount} endrosements)</span> */}
                        <Profile_edit_privacy_settings  section="skills_endorsements" initial={!!_profile.public_profile_settings ? !!_profile.public_profile_settings.skills_endorsements ? _profile.public_profile_settings.skills_endorsements : null : null}/>
                    </div>
                    <div className='profile-edit-skills-preview'>
                        <span className='profile-skills-endorsements-container'>
                            {/* TODO will come from immutable redux state later. */}
                            { skillsAndEndorsementsList.map(this._buildSkill) }
                        </span>
                    </div>
                </div>

                {/* Edit section */}
                <div>
                    <div className="green-title">Skills & Endorsements&nbsp;<InfoTooltip content={<SkillSectionTooltipContent />} place='right' type='dark'/></div>
                    <div>Add & Remove Skills</div>
                    
                    <div className="edit-skills-container">
                        <div className='edit-skills-top-container'>
                            <input 
                                id='newSkillName'
                                placeholder="Add a skill..."
                                onKeyPress={this._newSkillOnKeyPress}
                                value={newSkillName}
                                onChange={this._onChangeNewSkillName} />
                            <div onClick={this._addNewSkill} className={addNewSkillButtonClasses}>&#43;</div>
                        </div>

                        <Reorder
                            disabled={!reorderEnabled}
                            reorderId="edit-skills-list"
                            component='ul'
                            draggedClassName='edit-skill-dragged'
                            placeholderClassName='edit-skill-placeholder'
                            autoScroll={false}
                            onReorder={this._onReorder}>
                                { this.state.editedList.map(this._buildEditSkill) }
                        </Reorder>

                        {/* !_.isEmpty(this.state.editedList) && 
                            <div  className='drag-notice-container'>
                                <img onClick={this._toggleReordering} src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/profile/drag-icon.png" />
                                <div className='drag-to-reorder-text'>&nbsp;&nbsp;{reorderEnabled ? 'Drag' : 'Switch'} to reorder</div>
                                <div data-tip data-for={'switch-radio-tip'}>
                                    <Switch onClick={this._toggleReordering} on={reorderEnabled} />
                                </div>
                                <Tooltip id={'switch-radio-tip'} effect='solid' type='info'>
                                    <span>Enable reordering by toggling the switch</span>
                                </Tooltip>
                            </div> */}

                        { !_.isEmpty(activeEditSkill) &&
                            <div className='edit-skill-details-container'>
                                { SKILL_DETAIL_TYPES.map(this._buildSkillDetailsInput) }
                            </div> }
                    </div>

                </div>
            </Edit_section>
        );

    }
} 