// previewGenerator.js

import React from 'react'

import HeaderPreviewRow from './../header/components/costPreview'
import AdmissionPreviewRow from './../admission/components/admissionPreview'
import ScholarshipPreviewRow from './../scholarship/components/scholarshipPreview'
import createReactClass from 'create-react-class'

export default createReactClass({
	render(){
		let { fields, title} = this.props;

		return (
			<div className={"cost-preview " + title}>
				<div className="title">{title}</div>
				<div className="data">

					{ fields.map( (fld) => <GetPreviewRow
												key={fld.label}
												data={fld}
												{...this.props} /> ) }

				</div>
			</div>
		);
	}
});

const GetPreviewRow = (props) => {

	switch( props.title ){
		case 'annual international cost': return <HeaderPreviewRow {...props} />;
		case 'admissions': return <AdmissionPreviewRow {...props} />;
		case 'scholarship info': return <ScholarshipPreviewRow {...props} />;
		default: console.log('no preview title passed');
	}

}
