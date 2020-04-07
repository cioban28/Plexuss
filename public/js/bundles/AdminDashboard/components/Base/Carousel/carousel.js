import React from 'react'
import { dismissSlide } from './../../../actions/dashboardActions'
import { connect } from 'react-redux'
import DashMessage from './../../Dashboard/components/dashMessage'
import './styles.scss'
import createReactClass from 'create-react-class';


/***********************************************
*	Carousel : used in Admin Dashboard for announcements
*   supports: user individual slide removal - dissmis
*	indicator with numbers (eg  1/3 for first slide of 3)
*   close button to close the carousel - x
*	props:
*	slides -> array of slides
*   numbers -> boolean, true if we want number indicators, false if not
*	dismiss -> boolean, true if dismiss button displayed, false if not
*   prev -> prev navigation
*   next -> next navigation
*	containerStyle -> className for carousel container
*************************************************/
const Carousel =  createReactClass({
	getInitialState(){
		return {
			currentSlide: 0,
			show: true
		}
	},

	_nextSlide(){
		let { dash } = this.props,
			{ currentSlide } = this.state;

		//cannot go over max
		if( (currentSlide + 1) === dash.announcements.length ) return;

		this.setState({currentSlide: currentSlide + 1});
	},

	_prevSlide(){
		let { currentSlide } = this.state;

		//no negative indexes
		if( currentSlide === 0 ) return;

		this.setState({currentSlide: currentSlide - 1});
	},

	render(){
		var { announcements } = this.props.dash,
			{ dispatch } = this.props,
			{ currentSlide, show } = this.state;

		return (
			<div id="_indexedCarousel" className="carousel_container">

				{announcements && announcements[currentSlide] && announcements.length > 0 ?

					( show ?

						<div className={this.props.containerStyle + ' carouselContainer'}>
							{/* render current slide in container along with navigation*/}
							<DashMessage body={announcements[currentSlide].text} />

							{/* close button */}
							<div
								className="x-close-btn"
								title="hide announcements"
								onClick={ e => this.setState({show: false}) }>&times;</div>

							{/*dismiss btn*/}
							{ this.props.dismiss &&
								<div
									className="dismiss-btn"
									title="delete announcement"
									onClick={ e => dispatch( dismissSlide(announcements[currentSlide].id) ) }>dismiss</div> }

							{/* the number indicator */}
							{ this.props.numbers &&
								<div className="numberIndicator">
									<span id="prevNum" className="numberNav" onClick={ this._prevSlide }> &lang; </span>
									<span className="indicatorNumbers">{ currentSlide + 1 }</span> &#47;
									<span className="indicatorNumbers">{ announcements.length }</span>
									<span id="nextNum" className="numberNav" onClick={ this._nextSlide }> &rang; </span>
								</div> }
						</div>

					:
						<div className="open-carousel-container">
							{/* view announcments link */}
							<span className="open-carousel-btn" onClick={ e => this.setState({show: true}) }>View Announcements</span>
						</div>
					)

				: null } {/* end if announcements */}

			</div>

		);
	}

});

const mapStateToProps = (state, props) => {
	return {
		dash: state.dash
	};
};

export default connect(mapStateToProps)(Carousel);
