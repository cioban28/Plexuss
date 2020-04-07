// /Application/Uploads.js

import React from 'react'
import { connect } from 'react-redux'
import createReactClass from 'create-react-class'

import Heading from './Heading'
import RequireSection from './RequireSection'
import ProgramHeader from './../International/components/programHeader'

import { updateSimpleProp } from './../../../actions/overviewActions'
import { UPLOAD_Q } from './../../../../StudentApp/components/College_Application/constants'

const UPLOADS = [...UPLOAD_Q];

const Uploads = createReactClass({
	componentWillMount(){
		let { dispatch, route } = this.props;
		dispatch( updateSimpleProp({page: route.id}) );
	},

	render(){
		let { route, overview, intl } = this.props;

		return (
			<div>
				<ProgramHeader />
				<Heading {...this.props} />

				<br />

				{ overview[intl.activeProgram+'_require_'+route.id] &&
					<div>
						<div>Require certain type</div>
						<br />
						<div className="uploads">
							{ UPLOADS.map((d) => <Doc key={d.name} doc={d} {...this.props} />) }
						</div>
					</div> }

				<RequireSection {...this.props} />
			</div>
		);
	}
});

const Doc = createReactClass({
	render(){
		let { dispatch, overview, doc, intl } = this.props,
			arr_name = intl.activeProgram+'_required_uploads',
			docs = overview[arr_name] || {};

		return (
			<div className="doc">
				<div className={"icon "+doc.name} />
				<label htmlFor={doc.name}>
					<div>{ doc.label }</div>
					<input
						id={doc.name}
						type="checkbox"
						name={ doc.name || '' }
						value={ doc.name || '' }
						checked={ _.get(overview, arr_name+'.'+doc.name, false) }
						onChange={ e => dispatch( updateSimpleProp({[arr_name]: {...docs, [doc.name]: e.target.checked}}) ) } />
				</label>
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

export default connect(mapStateToProps)(Uploads);
