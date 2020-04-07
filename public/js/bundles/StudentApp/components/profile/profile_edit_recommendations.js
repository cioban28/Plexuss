import React, {Component} from 'react';

import {searchForMajors} from './../../actions/Profile';

import Edit_section from './profile_edit_section';

import Profile_single_recommendation from './profile_single_recommendation';

export default class Profile_edit_recommendations extends Component{
    constructor(props){
        super(props);

        this.state = {
            submittable: true,
        }
    }

    _valid(input, type, fieldV){
    }

    _save(callback){

    }

    render(){
        const { submittable } = this.state;

        return(
            <Edit_section editable={true} saveHandler={this._save} submittable={submittable}>
                {/* Preview section */}
                <div>
                    <div className="green-title">Recommendations</div>
                    <div className='profile-recommendations-container'>
                        <Profile_single_recommendation
                            name={'Jim Thomas'} 
                            position={'Spanish Teacher / Basketball Coach'} 
                            profileImageUrl={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/1019450_1512082798_plexuss_app_icon_200_png.png'}
                            recommendationDetails={'Sam is an incredibly hard-working student-athlete. I’ve been lucky enough to have had him in two of my Spanish classes, as well as on the Kennedy High JV Basketball team. This is a recommendation I am leaving for this person.'} />
                        
                        <Profile_single_recommendation 
                            name={'Jim Thomas'} 
                            position={'Spanish Teacher / Basketball Coach'} 
                            profileImageUrl={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/1019450_1512082798_plexuss_app_icon_200_png.png'}
                            recommendationDetails={'Sam is an incredibly hard-working student-athlete. I’ve been lucky enough to have had him in two of my Spanish classes, as well as on the Kennedy High JV Basketball team.'} />
                        
                        <Profile_single_recommendation 
                            name={'Jim Thomas'} 
                            position={'Spanish Teacher / Basketball Coach'} 
                            profileImageUrl={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/1019450_1512082798_plexuss_app_icon_200_png.png'}
                            recommendationDetails={'Sam is an incredibly hard-working student-athlete. I’ve been lucky enough to have had him in two of my Spanish classes, as well as on the Kennedy High JV Basketball team. This is a recommendation I am leaving for this person.'} />
                        
                        <Profile_single_recommendation 
                            name={'Jim Thomas'} 
                            position={'Spanish Teacher / Basketball Coach'} 
                            profileImageUrl={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/1019450_1512082798_plexuss_app_icon_200_png.png'}
                            recommendationDetails={'Sam is an incredibly hard-working student-athlete. I’ve been lucky enough to have had him in two of my Spanish classes, as well as on the Kennedy High JV Basketball team.'} />
                    </div>
                </div>

                {/* Edit section */}
                <div>
                    <div className="green-title">Recommendations</div>
                    <div>Add & Remove Skills</div>
                </div>
            </Edit_section>
        );

    }
} 