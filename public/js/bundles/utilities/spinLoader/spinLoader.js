import React, {Component} from 'react'
import PropTypes from 'prop-types';
import './styles.scss'

var colorStyle = {	borderColor: 'rgba(255,255,255, .3)',	
					borderTopColor: 'rgba(0,0,0,.8)' };



/************************************************************
*  animated spinning loader
*  props:  Boolean back - true if showing back, false or not defined otherwise
*		   String color - color for the loader's loading arc (if other than default)	
*          String track = color of the loader's track
*************************************************************/
export default class SpinLoader extends Component{
	
	constructor(props){
		super(props);

	}
	componentWillMount(){
		let {track, color} = this.props;
	
		if(track){
			colorStyle = {  borderColor: track }; 
		}

		if(color){
			colorStyle = {  borderTopColor: color }; 
		}

	}
	render(){

		let {back}  = this.props;

		return(
		
			<div className="_spinLoaderContainer" >	
				{back && <div className="spinLoader-Back" ></div>  }
				<div className="_spinLoader"  style={colorStyle} ></div>
			</div>
		);

	}
}

SpinLoader.propTypes = {
	color: PropTypes.string,
	track: PropTypes.string,
	back: PropTypes.bool
};