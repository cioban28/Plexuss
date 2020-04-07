import React, {Component} from 'react';

import {searchForMajors} from './../../actions/Profile';

import Edit_section from './profile_edit_section';

import AlsoViewedPerson from './AlsoViewedPerson';

export default class Profile_people_also_viewed extends Component{
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
            <Edit_section editable={false}>
                {/* Preview section */}
                <div>
                    <div className="green-title">People also viewed</div>
                    <div className="people-also-viewed-container">
                        <AlsoViewedPerson 
                            studentType={'Alumni'}
                            avatarUrl={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/1019450_1512082798_plexuss_app_icon_200_png.png'}
                            name={'Tony Tran'}
                            schoolName={'San Francisco State University'}
                            graduationYear={'2016'}
                            countryCode={'US'} />

                        <AlsoViewedPerson 
                            studentType={'Student'}
                            avatarUrl={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/1019450_1512082798_plexuss_app_icon_200_png.png'}
                            name={'James Lee'}
                            schoolName={'San Jose State University'}
                            graduationYear={'2018'}
                            countryCode={'US'} />

                        <AlsoViewedPerson 
                            studentType={'Alumni'}
                            avatarUrl={'https://www.biography.com/.image/t_share/MTE4MDAzNDEwNzg5ODI4MTEw/barack-obama-12782369-1-402.jpg'}
                            name={'Barack Obama'}
                            schoolName={'Harvard Law School'}
                            graduationYear={'1991'}
                            countryCode={'US'} />

                        <AlsoViewedPerson 
                            studentType={'Alumni'}
                            avatarUrl={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/1019450_1512082798_plexuss_app_icon_200_png.png'}
                            name={'Daniel Marshall'}
                            schoolName={'University of Toronto'}
                            graduationYear={'2016'}
                            countryCode={'CA'} />
                    </div>
                </div>

                {/* Edit section, not used */}
                <div>
                </div>
            </Edit_section>
        );

    }
} 