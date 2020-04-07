import React, {Component} from 'react'

/**************************************************** 
*  Input type 'file' with custom button (html <input type="file"/>), 
*  supports more than just custom text and styling,  for example lets say developer needs to have images and text 
*  takes children components as styled button for input
*  props:  inline = boolean display inline or block?
*		   name = input's name attribute	
*          will render child elements/components 
*		   callback for clicking browse button
*****************************************************/

export default class DecoratedInputTFile extends Component{

	constructor(props){
		super(props);

		this._clickInput = this._clickInput.bind(this);
	}

	_clickInput(){
		// let { className } = e.target;

		//$('.'+ className).closest('._inputFile').find('.customFileInput').click();
		this.fileInput.click();
	}



	render(){
		let {children, inline, callback, name} = this.props;

		return(
			<div className="_inputFile" onClick={this._clickInput} style={ inline ? {display: 'inline-block'} : {display: 'block'}}>
				
				{children}

				<input
					ref={(input) => {this.fileInput = input;} } 
					type="file" 
					name={name || "attachment"} 
					className="customFileInput" 
					style={{display: 'none'}}
					onChange={(e) => callback(e)} />

			</div>
		);
	}
}