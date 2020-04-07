// /Application/Shared.js

import React from 'react'
import { connect } from 'react-redux'
import createReactClass from 'create-react-class'

import Heading from './Heading'
import RequireSection from './RequireSection'
import ProgramHeader from './../International/components/programHeader'

import { updateSimpleProp } from './../../../actions/overviewActions'

const Shared = createReactClass({
	componentWillMount(){
		let { dispatch, route } = this.props;
		dispatch( updateSimpleProp({page: route.id}) );
	},

	componentWillReceiveProps(np){
		let { dispatch, route } = this.props;
		if( np.route.id !== route.id ) dispatch( updateSimpleProp({page: np.route.id}) );
	},

	render(){
		let { route } = this.props;

		return (
			<div>
				<ProgramHeader />
				<Heading {...this.props} />
				<br />
				<img src={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/app_'+route.id+'_preview.png'} alt={'Application - '+route.id+' Section Preview'} />
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

export default connect(mapStateToProps)(Shared);
