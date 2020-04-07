import React from 'react';

import ActionBar from './../scholarships/actionBar';
import SchTable from './../scholarships/schTable';

import './styles.scss';

const Scholarships = () => {	
	return (
		<div className="_salesScholarships">
			<ActionBar />
			<SchTable />
		</div>
	);

};

export default Scholarships;
