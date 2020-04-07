
import React from 'react'
import './styles.scss'
import createReactClass from 'create-react-class';

/***********************************************************
************************************************************
* component to delay the visibility of children it wraps
* props:
*	delayTime: time in miliseconds to delay the 'visibility'
************************************************************/
export default createReactClass ({

	getInitialState(){
		this.timeoutID = null;
		return {
			hidden: 'hidden'
		}
	},

	componentDidMount(){
		var that = this;

		this.timeoutID = setTimeout(function(){
				that.setState(
					{
						hidden: 'visible'
				});

		}, that.props.delayTime);
	},

	componentWillUnmount(){
		if(this.timeoutID != null){
			clearTimeout(this.timeoutID);
		}
	},

	render(){
			return (
				<div className={this.state.hidden}>
					{this.props.children}
				</div>
			);
	}
});
