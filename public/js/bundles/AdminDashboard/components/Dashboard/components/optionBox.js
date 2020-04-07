import React from 'react'
import createReactClass from 'create-react-class'
import PicButton from './../../Base/PicButton/picButton'

/****************************************************
*****************************************************
*	Options Box components as seen in Dashboard
*	top portion large with text
*	left and right notes
*   link on the bottom with colored background
*   color profiles -> classes can be passed in as props
*
*	must have rightNote, link, linkIcon
*
****************************************************/

export default createReactClass({

	render(){

		//get props
		var { rightNote, leftNote, innerNumber,
			  btnText, linkIcon, innerColorClass,
			  bottomColorClass, url, btnSizing, is_router } = this.props.values;

		var topClass= 'optionBox-top',
			topContent = '',
			rightContent = '',
			bottomClass = 'optionBox-bottom',
			iconClass = 'left-note';

		//create classes based on props
		//have classes already applied to divs -- just sending in color profiles to append to class list
		if( innerColorClass ) topClass = "optionBox-top " + innerColorClass;
		if( bottomColorClass ) bottomClass = "optionBox-bottom " + bottomColorClass;
		if( leftNote ) iconClass = "left-note " + leftNote;

		if(innerNumber || innerNumber == '0'){

			if(Number(innerNumber) > 999) innerNumber = (Number(innerNumber)/1000).toFixed(1) + "k";

			topContent = <div className='inner'>
							<div className="title-big ">{innerNumber || '0'}</div>
							<div>New</div>
						 </div>;
		}else{
			topContent = <div className='inner'>
							<br />
						 </div>;
		}

		//rightNote may be a string, int , or a component
		if(typeof rightNote === 'object'){
			rightContent = rightNote;
		}
		else if(rightNote != undefined && rightNote != null && !String(rightNote).includes('undefined')){
			rightContent =	<div className="right-note">{rightNote || ''}</div>
		}

		return (
			<div className="column large-3 medium-6 small-12 end optionBox">

					{/* top portion of options box */}
					{	this.props.fetching ?
						<div className={topClass}>
							<div className="loader" />
						</div>
						: <div className={topClass}>
							<div className={iconClass} />
							{ rightContent }
							{ topContent }
						  </div>
					}

					{/* bottom portion of options box */}
					<div className={ bottomClass }>
						<PicButton
							btnSizing={ btnSizing }
							colorProf=""
							iconImg={ linkIcon }
							btnText={ btnText || '' }
							is_router={ is_router }
							link={ url } />
					</div>

			</div>
		);

	}
});
