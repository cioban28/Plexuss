import _ from 'lodash'
import createReactClass from 'create-react-class'
import React from 'react'


/***************************************************
*  Picture Button Component
*  buttons which have an icon on left side and text/link on right
*  props are
*  iconImg: an image path
*  link: link if one
*  funcid: function id -- if js grabs id for function
*  btnText: button text
*  colorProf: a color profile class
*  btnSizing: a css class the sizes the a or div inside button-container --(clickable portion)
*  (button-container fits container div in which PicButton is placed)
****************************************************
****************************************************/
export default createReactClass({

	render(){

		//get props
		var {colorProf, btnSizing, iconImg, btnText, link, revealid, funcid} = this.props;

		var colorProfile = "button-container " + colorProf;

		//set button text to either link or function
		var button = '';

		if(link){
			button = <a className={this.props.btnSizing} href={link || ''}>
						<div className="centering-container">
							<div id={funcid} className="valign-container" data-reveal-id={revealid}>
								<div className={iconImg}></div>
								<div className="btn-text">
									{btnText}
								</div>
							</div>
						</div>
					 </a>;

		}else{
			button =
				<div className={this.props.btnSizing} id={funcid} data-reveal-id={revealid}>
					<div className="centering-container">
						<div  className="valign-container">
							<div className={iconImg}></div>
							<div className="btn-text">
								{btnText}
							</div>
						</div>
					</div>
				</div>;
		}


		return (

			{/* container for button has 100% width to fit into anothre container
			 -- have had many dynamic containers or containers with padding */}
			{/* the button inside can be then sized to 100% also or to consider padding...ect*/}
			<div className={colorProfile}>
				{button}
			</div>
		);
	}
});
