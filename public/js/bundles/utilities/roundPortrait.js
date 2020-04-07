import React, {Component} from 'react';

/************************************************************
*  Round portrait image 
*  props:
*		url = String image url
*	    customStyle = inline style object
*************************************************************/
export default class RoundPortrait extends Component{

	render(){
		let {customStyle, url, diameter, firstLetter} = this.props;

		const defaultStyle = {
				display: 'inline-block',
				backgroundImage: 'url('+url+')',
				backgroundRepeat: 'no-repeat',
				backgroundPosition: 'center',
				backgroundSize: 'cover',
				backgroundColor: '#ffffff',
				width: diameter + "px",
				height: diameter + "px",
				lineHeight: (diameter - 5) +  "px",
				fontSize: (diameter*.35) + "px",
				verticalAlign: "middle",
				border: '3px solid #fff',
				borderRadius: '100%',
			};



		const defaultNoPicStyle = {
				display: 'inline-block',
				backgroundImage: 'url(/social/images/Avatar_Letters/'+firstLetter+'.svg)',
				backgroundRepeat: 'no-repeat',
				backgroundPosition: 'center',
				backgroundSize: 'cover',
				backgroundColor: '#ffffff',
				width: diameter + "px",
				height: diameter + "px",
				lineHeight: (diameter - 5) +  "px",
				fontSize: (diameter*.35) + "px",
				verticalAlign: "middle",
				border: '3px solid rgba(250,250,250,.8)',
				borderRadius: '100%',
			};



			
		return(

			url ? 
				<div style={{...defaultStyle, ...customStyle}}>
					{this.props.children}
				</div>
			:
				<div style={{...defaultNoPicStyle, ...customStyle}}>
					{this.props.children}
				</div>

		);
	}
}