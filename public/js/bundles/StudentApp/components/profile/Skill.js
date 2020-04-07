import React, { Component } from 'react';
import { isEmpty, isArray } from 'lodash';
import Tooltip from 'react-tooltip';

const POTENTIAL_DETAILS = ['group', 'position', 'awards'];

export default class Skill extends Component {
    constructor(props) {
        super(props);

        this.state = {
            showDetails: false,
            arrow: '\u25BA',
        }
    }

    _toggleDetails = () => {
        const { showDetails, arrow } = this.state;

        this.setState({ 
            showDetails: !showDetails, 
            arrow: arrow === '\u25BC' ? '\u25BA' : '\u25BC' 
        });
    }

    _buildEndorsementAvatar = (endorsement, index) => {
        const { name } = this.props;

        return ( 
            <div key={index} data-tip data-for={name + '-' + index} className='single-endorsement-avatar'>
                <img src={endorsement.student_profile_photo} />

                <Tooltip id={name + '-' + index} effect='solid' type='light'>
                    <span>{endorsement.fname+' '+endorsement.lname}</span>
                </Tooltip>
            </div>
        );
    }

    _buildDetailTooltip = (detailType, index) => {
        const { name } = this.props;
        const classes = 'skill-detail ' + detailType;
        const detail = this.props[detailType];
        const id = name + '-' + detailType;

        if (isEmpty(detail)) { return null; }

        return (
            <div key={index} className={classes} data-tip data-for={id}>
                <Tooltip id={id} effect='solid' type='light'>
                    <span>{detail}</span>
                </Tooltip>
            </div>
        )
    }

    _buildMoreDetailsContainer = (detailType, index) => {
        const { group, position, awards } = this.props;
        const detail = this.props[detailType];
        const formattedType = detailType[0].toUpperCase() + detailType.substr(1);
        const iconClasses = 'more-details-icon ' + detailType;

        if (isEmpty(detail)) { return null; }

        return (
            <div key={index} className='single-skill-more-details-row'>
                <div className={iconClasses}></div>
                <div className='single-skill-more-details-formatted-type'>{formattedType}</div>
                <div className='single-skill-more-details-text'>{detail}</div>
            </div>
        );
    }

    render() {
        const { name, endorsements, group, position, awards, user_fname } = this.props;
        const { showDetails, arrow } = this.state;

        const count = ( !isEmpty(endorsements) && isArray(endorsements) ) ? endorsements.length : 0;

        return (
            <div className='single-skill-interactive-container'>
                <div className='single-skill-container'>
                    <div onClick={this._toggleDetails} className='single-skill-name' data-tip data-for={name}>
                        <div className='arrow-icon'>{ arrow }&nbsp;</div>
                        <div className='skill-name-text'>{name}</div>
                    </div>

                    <Tooltip id={name} effect='solid' type='light'>
                        <span>{name}</span>
                    </Tooltip>

                    <div className='single-skill-details'>
                        { POTENTIAL_DETAILS.map(this._buildDetailTooltip) }
                    </div>

                    {/* <div className='single-skill-endorsement-button'>+</div> */}

                    <div className='single-skill-endorsement-count' data-tip data-for={'endorsement-count-' + name}>{count}</div>
                    <Tooltip id={'endorsement-count-' + name} effect='solid' type='light'>
                        <span><b>{user_fname}</b> has <b>{count === 0 ? 'no' : count}</b> endorsement{count === 1 ? '' : 's'} for <b>{name}</b></span>
                    </Tooltip>

                    <div className='single-skill-endorsement-container'>
                        { isArray(endorsements) && endorsements.map(this._buildEndorsementAvatar) }
                    </div>
                </div>
                { showDetails && 
                    <div className='single-skill-more-details-container'>
                        <div className='close-more-details-button' onClick={this._toggleDetails} >&times;</div>
                        { POTENTIAL_DETAILS.map(this._buildMoreDetailsContainer) }

                        { !group && !position && !awards &&
                         <div>No group, position, or awards added yet.</div> }
                    </div> }
            </div>
        );
    }
}