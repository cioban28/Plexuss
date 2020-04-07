// /Application/Courses.js

import React from 'react'
import { connect } from 'react-redux'
import createReactClass from 'create-react-class'

import Heading from './Heading'
import RequireSection from './RequireSection'
import ProgramHeader from './../International/components/programHeader'

import { updateSimpleProp } from './../../../actions/overviewActions'

const Courses = createReactClass({
	componentWillMount(){
		let { dispatch, route } = this.props;
		dispatch( updateSimpleProp({page: route.id}) );
	},

	render(){
		let { route } = this.props;

		return (
			<div>
				<ProgramHeader />
				<Heading {...this.props} />
				<br />
				<RequireSection {...this.props} />
			</div>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		intl: state.intl,
		overview: state.overview,
	};
};

export default connect(mapStateToProps)(Courses);
