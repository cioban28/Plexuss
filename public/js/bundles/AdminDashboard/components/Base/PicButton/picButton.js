import _ from 'lodash'
import React from 'react'
import { Link } from 'react-router'

import './styles.scss'
import createReactClass from 'create-react-class';
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
*  (button-container fits container div in which PicButton is placed,
*	also PicButton is a block element and must be inlined !important if wanted)
****************************************************
****************************************************/
export default createReactClass({

	render(){

		//get props
		var { colorProf, btnSizing, iconImg, btnText, link, revealid, funcid, is_router, onPressFunc } = this.props,
			btnProfile = 'button-container',
			button = '',
			buttonSize = 'click-area';

		if( btnSizing ) buttonSize =  "click-area " + btnSizing;
		if( colorProf ) btnProfile =  "button-container " + colorProf;

		//set button text to either link or function
		if( is_router ){
			button = <Link to={is_router} className={buttonSize}>
						<div className={ iconImg } />
						<div className="btn-text">{ btnText }</div>
					 </Link>;
		}else if(link){
			button = <a className={ buttonSize } href={ link || '' }>
						<div className={ iconImg } />
						<div className="btn-text">{ btnText }</div>
					 </a>;
		}else{
			button = <div className={buttonSize} id={funcid} data-reveal-id={revealid}>
						<div className={ iconImg } />
						<div className="btn-text">{ btnText }</div>
					 </div>;
		}

		return (
			<div className={ btnProfile } onClick={onPressFunc}>
				{ button }
			</div>
		);
	}
});
