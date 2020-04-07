// stepNavBar.js

import React from 'react'
import { Link } from 'react-router';
import createReactClass from 'create-react-class'

export default createReactClass({
	render(){
		let { step } = this.props,
			icon1 = {
				backgroundImage: 'url(https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/setup_step_icons.png)',
				backgroundPosition: step > 1 ? '7px -79px' : '7px -33px',
				backgroundRepeat: 'no-repeat',
				width: '40px',
				height: '33px',
				margin: 'auto',
			},
			icon2 = {
				backgroundImage: 'url(https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/setup_step_icons.png)',
				backgroundPosition: step >= 2 ? (step === 2 ? '-39px -34px' : '-39px -80px') : '-39px 3px',
				backgroundRepeat: 'no-repeat',
				width: '40px',
				height: '33px',
				margin: 'auto',
			},
			icon3 = {
				backgroundImage: 'url(https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/setup_step_icons.png)',
				backgroundPosition: step === 3 ? '-85px -36px' : '-85px 1px',
				backgroundRepeat: 'no-repeat',
				width: '40px',
				height: '33px',
				margin: 'auto',
			};

		return (
			<div className="text-center" style={styles.bar}>
				<ul className="setup-nav inline-list" style={styles.ul}>

					<li style={styles.step1}>
						<Link to="/admin/setup/step1" onlyActiveOnIndex={true} style={styles.step}>
							<div style={icon1}></div>
							<div style={styles.white}>Step 1</div>
						</Link>
					</li>

					{
						step >= 2 ?
						<li style={styles.step2}>
							<Link to="/admin/setup/step2" style={styles.step}>
								<div style={icon2}></div>
								<div style={styles.white}>Step 2</div>
							</Link>
						</li> :
						<li style={styles.step2}>
							<div style={icon2}></div>
							<div style={styles.inactive}>Step 2</div>
						</li>
					}

					{
						step >= 3 ?
						<li style={styles.step3}>
							<Link to="/admin/setup/step3" style={styles.step}>
								<div style={icon3}></div>
								<div style={styles.white}>Step 3</div>
							</Link>
						</li> :
						<li style={styles.step3}>
							<div style={icon3}></div>
							<div style={styles.inactive}>Step 3</div>
						</li>
					}

				</ul>
			</div>
		);
	}
});

const styles = {
	bar: {
		backgroundColor: 'rgba(0,0,0,0.8)',
		margin: '0 0 50px 0'
	},
	white: {
		color: '#fff'
	},
	ul: {
		display: 'inline-block',
		margin: 0,
		padding: '10px 0'
	},
	inactive: {
		color: '#797979'
	},
	step1: {
		margin: 0
	},
	step2: {
		margin: '0 30px'
	},
	step3: {
		margin: 0
	}
};
