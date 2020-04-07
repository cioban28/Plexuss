import React, {Component} from 'react';
import Edit_section from './profile_edit_section';
import Profile_edit_share_profile_button from './profile_edit_share_profile_button'
import { Link } from 'react-router-dom'

export default class Profile_edit_header_banner extends Component{
    
    constructor(props){
        super(props);

        this.state = {
            activeDropDown: false,
        }
    }

    _valid(input, type, fieldV){

    }

    _save(callback){

    }

    render(){
        const { submittable } = this.state;

        return(
            <div className='profile-edit-header-banner-container'>
                <Link to={'/social/profile/'+this.props._profile.user_id} className="share_profile_incognito_banner">
                    <div className="share_profile_container">View Public Profile</div>
                </Link>
                {/* <div className='view-profile-as-button' onClick={() => this.setState({ activeDropdown: true, })}>View profile as</div> */}
                {/* <Profile_edit_share_profile_button /> */}
            </div>
        );

    }
} 