// savedTestimonials.js

import React from 'react'
import { connect } from 'react-redux'
import createReactClass from 'create-react-class'


import { REMOVE_TESTIMONIAL_ROUTE } from './../../constants'
import { editTestimonial, removeItem, saveIntlData } from './../../../../../actions/internationalActions'

const SavedTestimonials = createReactClass({
	render(){
		let { intl } = this.props;

		return ( intl.testimonialList.length > 0 ) ?
			<div>
				<div className="testimonial-bar">Video Testimonials</div>
				{ intl.testimonialList.map( (t) => <Testimonial key={t.title} testimonial={t} {...this.props} /> ) }
			</div>
			:
			null
	}
});

const Testimonial = createReactClass({
	_removeTestimonial(){
		let { dispatch, testimonial } = this.props;
		dispatch( removeItem(testimonial, REMOVE_TESTIMONIAL_ROUTE, 'REMOVE_TESTIMONIAL') );
	},

	render(){
		let { dispatch, testimonial } = this.props;

		return (
			<div className="testimonial">
				<iframe
					width="100%"
					height="100"
					src={ testimonial.embed_url }
					frameBorder="0"
					allowFullScreen>
				</iframe>

				<div className="title">{ testimonial.title }</div>

				<div className="testimonial-actions">
					<div onClick={ () => dispatch( editTestimonial(testimonial) ) }>Edit</div>
					<div>{'|'}</div>
					<div onClick={ this._removeTestimonial }>Remove</div>
				</div>
			</div>
		);
	}
});

const mapStateToProps = (state, props) => {
	return {
		intl: state.intl,
	};
};

export default connect(mapStateToProps)(SavedTestimonials);
