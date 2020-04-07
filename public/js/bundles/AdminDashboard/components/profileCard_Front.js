// profileCard_Front.js

import React from 'react'
import { connect } from 'react-redux'
import { edit } from './../actions/profileActions'
import CustomModal from './../../utilities/customModal'
import Avatar from './../../utilities/avatar'
import createReactClass from 'create-react-class'

const ProfileCard_Front = createReactClass({
	getInitialState(){
		return {
			avatarSprite: 'url(https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/avatars.png)',
			avatars: [
				'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/avatar_1.jpg',
				'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/avatar_2.jpg',
				'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/avatar_3.jpg',
				'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/avatar_4.jpg',
				'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/avatar_5.jpg',
				'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/avatar_6.jpg',
				'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/avatar_7.jpg',
				'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/avatar_8.jpg',
				'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/avatar_9.jpg',
				'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/avatar_10.jpg',
				'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/avatar_11.jpg',
				'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/users/images/avatar_12.jpg',
			]
		};
	},

	render(){
		let { dispatch, user, routeParams } = this.props,
			{ avatars } = this.state, picIcon = null, card = null,
			isAlternate = !!user.temporaryAlternateProfile;

	        if( isAlternate && routeParams.id ) user = user.temporaryAlternateProfile;

			picIcon = {
				backgroundImage: user.picObjectURL ? 'url('+user.picObjectURL+')' : 'url('+user.profile_pic+')',
				backgroundSize: 'cover',
				backgroundRepeat: 'no-repeat',
				backgroundPosition: 'center',
				width: '80px',
				height: '80px',
				backgroundColor: '#eee',
				border: '2px solid #fff',
				borderRadius: '100%',
				margin: '0 auto 20px',
			},
			card = {
				textAlign: 'center',
				width: '250px',
				height: '310px',
				backgroundColor: '#fff',
				backgroundImage: user.school_background ? 'url('+user.school_background+')' : '',
				backgroundSize: 'cover',
				backgroundPosition: 'center',
				backgroundRepeat: 'no-repeat',
				padding: '30px 10px',
				boxShadow: '1px 1px 2px 1px rgba(0,0,0,0.5)',
				borderRadius: '1px'
			};

			picIcon = user.useAvatar ? Object.assign({}, picIcon, {backgroundImage: 'url('+user.avatar_url+')'}) : picIcon;

		return (
			<div style={card}>
				<div style={picIcon}></div>
				<div className="button radius" style={styles.upload} onClick={ () => dispatch(edit({pro: true, avatar: false, alternate: isAlternate}, 'toggleProfileAvatarModal')) }>Change photo</div>
	            <div style={styles.allowed}>Only .jpg, .png, .gif, .bmp allowed</div>

	            {
	            	user.profilePicModalOpen ?
	            	<CustomModal styling={styles.modal} backgroundClose={ () => dispatch(edit({pro: false, avatar: false, alternate: isAlternate}, 'toggleProfileAvatarModal')) }>
	            		<div className="row" style={styles.container}>
	            			<div className="clearfix">
	            				<div className="right" style={styles.close} onClick={ () => dispatch(edit({pro: false, avatar: false, alternate: isAlternate}, 'toggleProfileAvatarModal')) }>X</div>
	            			</div>
	            			<div className="column small-12 medium-6 text-center">
	            				<h3 style={styles.header}>Upload a photo</h3>

								<label htmlFor="_profile_pic">
									<div className="button radius" style={styles.choose}>Change photo</div>
								</label>

					            <input
					            	id="_profile_pic"
					            	type="file"
					            	name="profile_pic"
					            	accept="image/*"
					            	style={styles.hidden}
					            	onChange={ (pic) => dispatch(edit({picFile: pic.target.files[0], alternate: isAlternate}, 'pic')) } />

								<div><small style={styles.allowed}>Only .jpg, .png, .gif, .bmp allowed</small></div>
								<br />
								<div style={styles.avatar} onClick={ () => dispatch(edit({pro: false, avatar: true, alternate: isAlternate}, 'toggleProfileAvatarModal')) }>
									<u>Choose avatar instead</u>
								</div>
	            			</div>
	            			<div className="column small-12 medium-6 text-left" style={styles.header}>
	            				<h4 style={styles.header}>The impact of your photo</h4>
	            				<div style={styles.middle}>Having a photo creates trust between the institution that you represent and the student.</div>
	            				<div>Colleges that have uploaded a photo receive on average 37% higher response rate from students.</div>
	            			</div>
	            		</div>
	            	</CustomModal>
	            	: null
	            }

	            {
	            	user.avatarModalOpen ?
	            	<CustomModal styling={styles.modal} backgroundClose={ () => dispatch(edit({pro: false, avatar: false, alternate: isAlternate}, 'toggleProfileAvatarModal')) }>
	            		<div style={styles.avatarContainer} className="text-left">
	            			<div className="clearfix">
	            				<div className="right"
	            					style={styles.close}
	            					onClick={ () => dispatch(edit({pro: false, avatar: false, alternate: isAlternate}, 'toggleProfileAvatarModal')) }>X</div>
	            			</div>

		            		<h3 style={styles.header}>Choose avatar</h3>

		            		{ avatars.map((url, i) => <Avatar key={i} url={url} edit={edit} />) }

		            		<div style={styles.btns}>
		            			<div className="button radius"
		            				style={styles.photoinstead}
		            				onClick={() => dispatch(edit({pro: true, avatar: false, alternate: isAlternate}, 'toggleProfileAvatarModal'))}>
			            				<u>Upload photo instead</u>
		            			</div>

		            			{
		            				user.avatar_url ?
		            				<div className="button radius" style={styles.choose} onClick={() => dispatch(edit({useAvatar: true, alternate: isAlternate}, 'avatarChosen'))}>Ok</div> :
		            				<div className="button radius disabled" style={styles.disable}>Ok</div>
		            			}
		            		</div>
	            		</div>
	            	</CustomModal>
	            	: null
	            }
			</div>
		);
	}
});

const styles = {
	hidden: {
		display: 'none'
	},
	upload: {
		padding: '10px 30px',
	},
	allowed: {
		color: '#eee',
		fontSize: '12px'
	},
	close: {
		fontWeight: '100',
		color: '#ddd',
		fontSize: '20px',
		cursor: 'pointer',
	},
	container: {
		backgroundColor: '#202020',
		borderRadius: '5px',
		padding: '10px 20px 25px 0',
		maxWidth: '590px',
		boxShadow: '1px 1px 6px 2px rgba(0, 0, 0, 0.5)'
	},
	avatarContainer: {
		backgroundColor: '#202020',
		borderRadius: '5px',
		padding: '10px 20px 0',
		maxWidth: '485px',
		boxShadow: '1px 1px 6px 2px rgba(0, 0, 0, 0.5)'
	},
	header: {
		color: '#ddd'
	},
	choose: {
		backgroundColor: '#FF5C26',
		padding: '10px 30px',
	},
	disable: {
		cursor: 'not-allowed',
		backgroundColor: '#FF5C26',
		padding: '10px 30px',
	},
	modal: {
		backgroundColor: 'transparent'
	},
	avatar: {
		color: '#ddd',
		cursor: 'pointer',
		padding: '10px 30px',
		backgroundColor: 'transparent'
	},
	photoinstead: {
		color: '#ddd',
		cursor: 'pointer',
		padding: '10px 30px 10px 0',
		backgroundColor: 'transparent'
	},
	middle: {
		margin: '0 0 10px'
	},
	btns: {
		display: 'flex',
		flexDirection: 'row',
		justifyContent: 'space-between',
		margin: '40px 0 0'
	}
};

const mapStateToProps = (state, props) => {
	return {
		user: state.user
	};
};

export default connect(mapStateToProps)(ProfileCard_Front);
