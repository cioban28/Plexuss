import React, {Component} from 'react'

/************************************************
*  Plain text Button
*  props:   title = text on button  
*			eventHandler = event on click
*			active = parent container will send in active button based on button title
*			-> if(active == title) -> component appends class 'active'
*************************************************/
export default class TextButton extends Component{
	constructor(props){
		super(props);	
	}

	render(){
		let {title, eventHandler, active} = this.props;

		return(
			<div className={active == title ? 'active' : ''} onClick={eventHandler} >{title}</div>
		);
	}
}