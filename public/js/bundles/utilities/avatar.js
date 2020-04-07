// avatar.js

import React from 'react'
import { connect } from 'react-redux'
import createReactClass from 'create-react-class'

const Avatar = createReactClass({
	getInitialState(){
		return {
			hovering: false,
		};
	},

	_editAvatar(){
		let { dispatch, user, edit, url } = this.props,
			isAlternate = !!user.temporaryAlternateProfile;

		if( edit ) dispatch(edit({avatar_url: url, alternate: isAlternate}, 'avatar'));
	},

	render(){
		let { dispatch, user, custom, url, hoverEffect, edit } = this.props,
			defaultStyle = null, avatar = null, isAlternate = !!user.temporaryAlternateProfile;

			if( isAlternate ) user = user.temporaryAlternateProfile;

			defaultStyle = {
				display: 'inline-block',
				backgroundImage: 'url('+url+')',
				backgroundRepeat: 'no-repeat',
				backgroundPosition: 'center',
				width: '60px',
				height: '60px',
				border: '2px solid #202020',
				borderRadius: '100%',
				cursor: 'pointer',
				margin: '0 10px 0 0'
			};

			avatar = custom ? Object.assign({}, defaultStyle, {...custom}) : defaultStyle;
			avatar = this.state.hovering ? Object.assign({}, avatar, {border: '2px solid #ddd'}) : avatar;
			avatar = user.avatar_url && user.avatar_url === url ? Object.assign({}, avatar, {border: '2px solid #24b26b'}) : avatar;

		return (
			<div style={avatar}
				onClick={this._editAvatar}
				onMouseOver={() => this.setState({hovering: true})}
				onMouseOut={() => this.setState({hovering: false})}>
					<input type="hidden" name="avatar_img" defaultValue={url} />
			</div>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		user: state.user
	};
};

export default connect(mapStateToProps)(Avatar);
