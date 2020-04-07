// header.js

import React from 'react';
import { Link, browserHistory } from 'react-router';
import Display from './../../utilities/display';
import createReactClass from 'create-react-class'

export default createReactClass({
	render(){
		var { title, logo, goBack, titleCustomStyle } = this.props, titleStyle,
			logo = {
				backgroundImage: 'url('+logo+')',
				backgroundRepeat: 'no-repeat',
				backgroundSize: 'cover',
				backgroundPosition: 'center',
				borderRadius: '100%',
				width: '50px',
				height: '50px',
				display: 'inline-block',
				verticalAlign: 'middle',
				margin: '0 10px 0 0',
			};

			titleStyle = titleCustomStyle ? Object.assign({}, styles.title, {...titleCustomStyle}) : styles.title;

		return (
			<div style={styles.container}>
				<div style={styles.head}>

					<div style={logo}></div>

					{ goBack ? <div style={styles.back} onClick={() => browserHistory.goBack()}>Back</div> : null }

					<div style={titleStyle}>{title}</div>

				</div>
			</div>
		);
	}
});

const styles = {
	container: {
		margin: '0 0 30px'
	},
	title: {
		display: 'inline-block',
		verticalAlign: 'middle',
		color: '#eee',
		fontSize: '24px'
	},
	head: {
		display: 'inline-block',
		verticalAlign: 'middle'
	},
	back: {
		display: 'inline-block',
		verticalAlign: 'middle',
		fontSize: '16px',
		color: '#797979',
		fontWeight: '100',
		margin: '0 20px',
		cursor: 'pointer'
	}
}
