// /Application/MandatoryQuestions.js

import React from 'react'
import { connect } from 'react-redux'
import createReactClass from 'create-react-class'

import Heading from './Heading'

const IMGS = [
	'basic',
	'contact',
	'citizenship',
	'financials',
	'gpa',
	'scores',
];

const MandatoryQuestions = createReactClass({
	render(){
		return (
			<div>
				<Heading {...this.props} />
				<br />
				{ IMGS.map((img) => <div className={"mand "+img} key={img}>
										<img src={'https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/app_'+img+'_preview.png'} />
									</div>) }
			</div>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		overview: state.overview,
	};
};

export default connect(mapStateToProps)(MandatoryQuestions);
