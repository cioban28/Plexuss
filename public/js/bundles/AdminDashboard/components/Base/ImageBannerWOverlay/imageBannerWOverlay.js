import React from 'react'
import './styles.scss'
import createReactClass from 'create-react-class';
/*******************************************************
*	Image Banner background with:
*   currently, mobile -> no image
*   slight blur, dark overlay with partial opacity (./styles.scss):
*			banner-bg-container:after $overlayOpacity
*	fixed height(./styles.scss):
*			$bannerHeight
*
*	random image on component mount  (based on css classes -- must be named: p1, p2, p3, ...pn)
*									 ( to use this -- styles currently in ./styles.scss)
*	contains optional div class="dashboard-stats-container" that sits on top of image
*
*	message box on right side
*
*	PROPS: min, max, opacity
*******************************************************/
export default createReactClass({

	getInitialState(props){
		return {
			pic: 'p1'				/* current image when mounted */
		};
	},

	componentWillMount(){

		//--- choose random background everytime component mounts
		//classes are p1, p2, p3, ....
		//right now with sass extend dashboard-banner-bg and sit inside ./styles.scss
		//dev can set which images they want to use by min and max props

		var min = Math.ceil(this.props.min);
		var max = Math.floor(this.props.max);

		//for now just a random choice
		var picNum = Math.random() * (max - min) + min ;
		this.setState({
		 	pic: 'p'+ Math.floor(picNum)
		});
	},


	render(){

		if(this.props.opacity){
			//st css opacity for banner-bg-container:after
		}


		////////////
		return (
			<div className="clearfix">

				{/* banner container -- blur adds strange margin -- container cuts this off */}
				<div className="banner-bg-container">

					{/* banner picture in here */}
					<div className={this.state.pic}></div>

				</div>

			</div>
		);
	}

});
