// previewModal.js

import React from 'react'
import createReactClass from 'create-react-class'

export default createReactClass({
	getInitialState(){
		return {
			frontpage_img: 'url(https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/profile_on_frontpage_preview.jpg)',
			collegepage_img: 'url(https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/profile_on_college_preview.jpg)',
			fp: false,
		};
	},

	componentWillMount(){
		this.state.fp = (this.props.page === 'frontpage-preview');
		document.addEventListener('click', (e) => { if( e.target.id === '_previewModal' ) this.props.close(); });
	},

	changeImg(){
		this.setState({fp: !this.state.fp});
	},

	render(){
		let { close, page } = this.props,
			{ fp, frontpage_img, collegepage_img } = this.state,
			bgimg = {
				backgroundImage: fp ? frontpage_img : collegepage_img,
				backgroundSize: 'contain',
				backgroundRepeat: 'no-repeat',
				backgroundPosition: 'center',
				color: '#fff',
				height: '448px'
			},
			front = {color: fp ? '#FF5C26' : '#fff', fontWeight: fp ? '600' : '100', cursor: 'pointer', margin: '0 0 0 25px'},
			college = {color: !fp ? '#FF5C26' : '#fff', fontWeight: !fp ? '600' : '100', cursor: 'pointer'};

		return (
			<div style={styles.preview} id="_previewModal">
				<div style={styles.close} className="text-right" onClick={close}>x</div>
				<div style={styles.container}>
					<div style={styles.title} className="text-left">Preview on {fp ? 'Front Page' : 'College Page'}</div>
					<div className="clearfix">
						<div className="right" style={front} onClick={this.changeImg}>View on front page</div>
						<div className="right" style={college} onClick={this.changeImg}>View on college page</div>
					</div>
					<div style={bgimg}></div>
				</div>
			</div>
		);
	}
});

const styles = {
	preview: {
		position: 'fixed',
		top: 0,
		right: 0,
		bottom: 0,
		left: 0,
		backgroundColor: 'rgba(0,0,0,0.8)',
		zIndex: 8
	},
	close: {
		cursor: 'pointer',
		fontSize: '30px',
		fontWeight: '600',
		color: '#fff',
		padding: '10px 25px 0'
	},
	title: {
		color: '#fff',
		fontSize: '20px',
	},
	container: {
		maxWidth: '1000px',
		margin: 'auto'
	}
};
