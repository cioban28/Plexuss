// Scholarships/Ncsa/index.js

import React, { Component } from 'react'
import { connect } from 'react-redux';
import './styles.scss'
import OldNCSA from './info'



class NCSA extends Component {
	constructor(props){
		super(props)

		this.state ={
		}

	}

	componentDidMount() {
		let params = (new URL(window.location)).searchParams;
      	let step = params.get('section');
	}

	render(){
		return (
			<div className="social-ncsa">
				<div className="social-ncsa-container">
					<OldNCSA />
				</div>
			</div>
		);
	}
}

const mapStateToProps = (state, props) => {
    return {
        user: state.user.data,
        profile: state.profile,
    }
}

export default connect(mapStateToProps)(NCSA);

