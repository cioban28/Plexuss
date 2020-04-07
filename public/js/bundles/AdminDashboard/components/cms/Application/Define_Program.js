// /Application/Define_Program.js

import React from 'react'
import { connect } from 'react-redux'
import createReactClass from 'create-react-class'

import Heading from './Heading'
import RequireSection from './RequireSection'

import { updateSimpleProp } from './../../../actions/overviewActions'

const PROGRAMS = [
	{name: 'app_program', label: 'Undergraduate', id: 'undergrad'},
	{name: 'app_program', label: 'Graduate', id: 'grad'},
	{name: 'app_program', label: 'English Pathway Program', id: 'epp'},
	{name: 'app_program', label: 'Both', id:'both'}
];

const Define_Program = createReactClass({
	componentWillMount(){
		let { dispatch, route } = this.props;
		dispatch( updateSimpleProp({page: route.id}) );
	},

	render(){
		let { dispatch, route, overview } = this.props;

		return (
			<div>
				<Heading {...this.props} />

				<br />
				<div className="prog-msg">Before adding sections we need to define which program you offer.</div>
				<br />
				<div className="prog-msg">If you have different requirements for your grad/undergrad programs please indicate below.</div>
				<br />

				<div className="programs">
				{ PROGRAMS.map(p => <label key={p.id} htmlFor={p.id}>
										<input
											id={ p.id }
											type="radio"
											name={ p.name }
											value={ p.id }
											checked={ overview[p.name] === p.id }
											onChange={ e => dispatch( updateSimpleProp({[p.name]: p.id}) ) } />

										{ p.label }
									</label>) }
				</div>

				<RequireSection noRequire={true} {...this.props} />
			</div>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		overview: state.overview,
	};
};

export default connect(mapStateToProps)(Define_Program);
