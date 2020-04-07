import React from 'react';
import './styles.scss';
import {promoteYourselfSubHeadings} from './constants.js'
export default function CollegeApplication({ setActiveHeading }){

	return(
		<div id="college_application">
			<h5> 2. {promoteYourselfSubHeadings.collegeApplication} </h5>
			<span> College reps are also able to see your
        <span className='link-text' onClick={setActiveHeading.bind(this, 'College Application Assessment')}> Application</span>.
      </span>
		</div>
	)
}
