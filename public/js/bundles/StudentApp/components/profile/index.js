import React from 'react';
import './styles.scss';

import {connect} from 'react-redux';
import {getStudentProfile} from './../../actions/Profile';

import ProfileOption from './profileOption';
import Avatar from './../../../utilities/roundPortrait';
import Loader from './../../../utilities/loader';

import CircularProgressbar from 'react-circular-progressbar';

class Profile extends React.Component{
	
	constructor(props){
		super(props);
	}

	componentWillMount(){
		let {dispatch} = this.props;

		dispatch(getStudentProfile());
	}

	render(){

		let {_profile} = this.props;

		let name = (_profile.fname || " ") + " " + (_profile.lname || " ");
		let percent = (_profile.profile_percent || "0") + "% Complete";


		//2 * pi * radius  for circumfrance of our percent meter
		//the dash should be percentage of circumfrance
		// C * (1 - percent)


		return(
			<div className="_profile">
				

				{_profile.init_profile_pending === false ?
					

					<div className="centering">
						<Avatar  url={_profile.profile_img_loc || ""} diameter={90}/>
						<div className="name">{name}</div>


						<ProfileOption icon={_profile.profile_img_loc || "https://s3-us-west-2.amazonaws.com/asset.plexuss.com/icons/user-avatar-r1.png"}
									   iconStyle={{border: '3px 3px 5px rgba(0,0,0,.2)'}}
									   title={name}
									   description="Edit Public Profile"
									   link="/profile/edit_public" />

						<ProfileOption icon={false}
									   iconStyle={{border: '3px 3px 5px rgba(0,0,0,.2)'}}
									   title="College Application"
									   description={ percent || " "}
									   link='/college-application'>

                                            <CircularProgressbar percentage={_profile.profile_percent || "0"} />

					   </ProfileOption>
					   <ProfileOption icon="/images/upload-icon.png" 
									   iconStyle={{border: '3px 3px 5px rgba(0,0,0,.2)'}}
									   title="Your Documents"
									   description={_profile.transcript ? (_profile.transcript.length || "0") + " Documents" : "0 Documents"}
									   link="/profile/documents" />

					</div>

					:
					<Loader/>}

			</div>
		);
	}
};

const mapStateToProps = (state, props) => {
	return {
		_profile: state._profile
	}
}

export default connect(mapStateToProps)(Profile);