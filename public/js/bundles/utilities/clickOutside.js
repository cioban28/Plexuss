import React, {Component} from 'react';

/***********************************************************
*  Very basic clickout outside element detection
*
*
************************************************************/
export default class ClickOutside extends Component{
	
	constructor(props){
		super(props);
	}
	componentDidMount(){
		let {handler, elID} = this.props;
		let el = document.getElementById(elID);

		document.addEventListener("click", (e)=>{
			if(!el.contains(e.target)){
					handler();
			}
		});
	}

	render(){
		let {handler, elID} = this.props;

		return(
			<div id={elID} ref={(el) => this.element = el }>
				{this.props.children}
			</div>
		);
	}


}