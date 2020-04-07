import React from 'react';
import Display from './../../utilities/display';
import createReactClass from 'create-react-class'

export default createReactClass({
	componentDidMount() {
	    $('#owl-features').owlCarousel({
	    	navigation: false,
			pagination: false,
			slideSpeed: 1000,
			autoPlay: true,
			stopOnHover: true,
			singleItem: true
	    });
	},

	render() {
		var { show_filter, appointment_set } = this.props;

		return (
			<div id='dash_newFeatures' className={"medium-3 column end dash_indicator"} >

						<div className='row'>
							<div className='small-12 column indicator_feed'>
								<div className="new-feature-head">
									<b>NEW FEATURE:</b>
								</div>

								<div className="owl-carousel owl-theme" id="owl-features">
									<div className="feature-toggler-container filter item">
										<div className="new-feature-descrip">
											Now you can set a filter <br />for your daily <br />recommendations.
										</div>

										<div>
											<a href={show_filter ? '/admin/filter' : '#'} data-reveal-id={show_filter ? '' : 'upgrade-acct-modal'}>
												<div className="set-filter-btn"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/rfilter-gray.png" alt="" /></div>
												<div className="set-filter-btn"><b>SET YOUR FILTER</b></div>
											</a>
										</div>
									</div>

									<div className="feature-toggler-container ranking item">
										<div className="new-feature-descrip">
											Now you can create your own <br />rankings to showcase on <br />your college page.
										</div>

										<div>
											<a href="/admin/content">
												<div className="set-filter-btn"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/rfilter-gray.png" alt="" /></div>
												<div className="set-filter-btn"><b>ADD RANKING</b></div>
											</a>
										</div>
									</div>

									<div className="feature-toggler-container export item">
										<div className="new-feature-descrip">
											Now you can export approved <br />students into your CRM
										</div>

										<div>

											<a data-reveal-id={'exp-student-modal'}>
												<div className="set-filter-btn"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/rfilter-gray.png" alt="" /></div>
												<div className="set-filter-btn"><b>EXPORT STUDENTS</b></div>
											</a>

										</div>
									</div>

									<div className="feature-toggler-container export item">
										<div className="new-feature-descrip">
											Now you can search through <br /> our database of students
										</div>

										<div>
											<a href="/admin/studentsearch">
												<div className="set-filter-btn"><img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/admin/magnifier.png" alt="" /></div>
												<div className="set-filter-btn"><b>SEARCH STUDENTS</b></div>
											</a>
										</div>
									</div>
								</div>

							</div>
						</div>

			</div>
		);
	}

});
