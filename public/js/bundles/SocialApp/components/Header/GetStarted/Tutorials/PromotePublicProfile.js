import React from 'react';
import './styles.scss';

export default function PromotePublicProfile({ setActiveHeading }){
	return(
		<div id="promote_public_profile">
			<h5> 1. Public Profile </h5>
			<span> There are several ways for you to stand out as a candidate. Universities will see your
        <span className='link-text' onClick={setActiveHeading.bind(this, 'Public Profile')}> Public Profile</span>.
      </span>
		</div>
	)
}
