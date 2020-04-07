import React from 'react'
import './styles.scss'


/********************************************************
*   button component -- button with a picture and text 
*	props: image = string, class name for css with image background
*   	   text = string, text on button
********************************************************/
export default function PicTextButton({imageClass, text, callback}){

	return(
		<div className="_picTextButton" onClick={callback}>
			<div className="btn-image"><div className={imageClass}></div></div>
			<div className="btn-text">{text}</div>
		</div>
	);

}