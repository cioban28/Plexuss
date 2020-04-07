import React, { Component } from 'react';
import { connect } from 'react-redux';
import './styles.scss';
import { selectYourCollegesSubHeadings } from './constants.js'
import { setActiveHeading } from '../../../../actions/tutorials';
import $ from 'jquery';

class MyColleges extends Component {
	componentDidMount() {
    this.scrollToTabSection(this.props.activeHeading);
  }

  componentWillReceiveProps(nextProps) {
    this.scrollToTabSection(nextProps.activeHeading);
  }

  scrollToTabSection(activeHeading) {
    const container = $('.sic-tutorials-main');
    if(Object.values(selectYourCollegesSubHeadings)[0] === activeHeading) {
      container.scrollTop(0);
    } else {
      const targetEl = container && document.querySelector('.sic-tutorials-main').querySelector(`#${this.getFormattedId(activeHeading)}`);
      targetEl && container.scrollTop(targetEl.offsetTop - 50);
    }
  }

  getFormattedId(id) {
  	return id.split(' ').join('_').toLowerCase();
  }

  goToLink(id){
    this.props.setActiveHeading(id);
    const formattedId = this.getFormattedId(id);
		const container = $('.sic-tutorials-main');
		const targetEl = document.querySelector('.sic-tutorials-main').querySelector(`#${formattedId}`);
    container.scrollTop(targetEl.offsetTop - 50);
	}

	render(){
		return(
			<div id="my_colleges">
				<h5 className="text_underline">Step 3. My Colleges</h5>
				<table style={{width: '100%'}}>
					<tr>
						<td onClick={this.goToLink.bind(this, 'Your Favorites')}>1. Your Favorites</td>
						<td onClick={this.goToLink.bind(this, 'Recommended by Plexuss')}>2. Recommended by Plexuss</td>
					</tr>
					<tr>
						<td onClick={this.goToLink.bind(this, 'Colleges recruiting you')}>3. Colleges recruiting you</td>
						<td onClick={this.goToLink.bind(this, 'Colleges viewing you')}>4. Colleges viewing you</td>
					</tr>
					<tr>
						<td onClick={this.goToLink.bind(this, 'My Applications')}>5. My Applications </td>
						<td onClick={this.goToLink.bind(this, 'My Scholarships')}>6. My Scholarships</td>
					</tr>
				</table>
				<div className='span_margin'>
					<span>You can access your My Colleges Portal from the navigation bar <img className='hide-for-small-only' src="/social/images/grad_cap.png" className='img_icons_trans'/>.</span>
				</div>
				<div className='hide-for-small-only'>
					<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/my_colleges_indicator.PNG" />
				</div>

				<div className='show-for-small-only'>
					<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/nav.jpg" />
				</div>

				<div className='show-for-small-only'>
					<div className='span_margin'>
						<span>Your Portal has all your lists.</span>
					</div>
					<div>
						<img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/portal.jpg' />
					</div>
				</div>


				<div id="your_favourites">
					<h5>1. <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/icons/favorites.svg" className='img_icons_trans'/> Your Favorites</h5>
					<div className='span_margin_bottom'>
						<span>You can find every college that you’ve added to your favorites or you’ve clicked on <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/get_recruited.PNG" style={{width: '130px'}}/> in this folder.</span>
					</div>
					<div className='hide-for-small-only'>
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/favorites_page.PNG" />
					</div>
					<div className='show-for-small-only'>
						<img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/my_favorites_mbl.jpg' />
					</div>
					<div className='span_margin_top'>
						<span>In your portal, you will see useful information such as each Universities’ rank <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/ranking_number.PNG" style={{width: '40px'}}/> .</span>
					</div>
				</div>

				<div id="recommended_by_plexuss">
					<h5>2. <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/icons/recommended.svg" className='img_icons_trans' /> You Recommendations</h5>
					<div className='span_margin_bottom'>
						<span>We base our recommendations on the information you’ve provided, such as your favorites, your major, your financials and more.</span>
					</div>
					<div className='hide-for-small-only'>
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/recommendations_page.PNG" />
					</div>
					<div className='show-for-small-only'>
						<img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/my_recommendations_mbl.jpg' />
					</div>
					<div className='span_margin'>
						<span>To find out why we recommend a school for you just click on <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/why_recommended.PNG" style={{width: '235px'}}/> for more information.</span>
					</div>
					<div className='span_margin_bottom'>
						<span>Select <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/yes.PNG" style={{width: '130px'}}/> if you want to get recruited by this school.</span>
					</div>
					<div className='span_margin_bottom'>
						<span>Select <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/no.PNG" style={{width: '110px'}}/> to remove this school from your lists.</span>
					</div>
				</div>

				<div id="colleges_recruiting_you">
					<h5>3. <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/icons/recruit.svg" className='img_icons_trans'/> Colleges recruiting you</h5>
					<div className='span_margin_bottom'>
						<span>These schools have requested to recruit you based on your profile.</span>
					</div>
					<div className='hide-for-small-only'>
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/recruiting_you_page.jpg" />
					</div>
					<div className='show-for-small-only'>
						<img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/my_recommendations_mbl.jpg' />
					</div>
					<div className='span_margin'>
						<span>Select <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/yes.PNG" style={{width: '130px'}}/> if you want to get recruited by this school.</span>
					</div>
					<div className='span_margin_bottom'>
						<span>Select <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/no.PNG" style={{width: '110px'}}/> to remove this school from your lists.</span>
					</div>
				</div>

				<div id="colleges_viewing_you">
					<h5>4. <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/icons/views.svg" className='img_icons_trans'/> Colleges viewing you</h5>
					<div className='span_margin_bottom'>
						<span>Universities in this list have visited your profile.</span>
					</div>
					<div className='hide-for-small-only'>
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/viewing_you_page.jpg" />
					</div>
					<div className='show-for-small-only'>
						<img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/colleges_viewing_you_mbl.jpg' />
					</div>
					<div className='span_margin'>
						<span>Select <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/yes.PNG" style={{width: '130px'}}/> if you want to get recruited by this school.</span>
					</div>
					<div className='span_margin_bottom'>
						<span>Select <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/no.PNG" style={{width: '110px'}}/> to remove this school from your lists.</span>
					</div>
				</div>

				<div id="my_applications">
					<h5>5. <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/icons/applications.svg" className='img_icons_trans'/> My Applications</h5>
					<div className='span_margin_bottom'>
						<span>These are schools that you’re most interested in applying to.</span>
					</div>
					<div className='hide-for-small-only'>
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/my_applications.jpg" />
					</div>
					<div className='show-for-small-only'>
						<img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/my_applications_mbl.jpg' />
					</div>
					<div className='span_margin'>
						<span>Click <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/apply.PNG" style={{width: '110px'}}/> to visit the schools application page.</span>
					</div>
				</div>

				<div id="my_scholarships">
					<h5>6. <img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/icons/scholarships.svg" className='img_icons_trans'/> My Scholarships</h5>
					<div className='span_margin_bottom'>
						<span>A list of the scholarships that you have selected.</span>
					</div>
					<div className='hide-for-small-only'>
						<img src="https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/my_scholarships_page.jpg" />
					</div>
					<div className='show-for-small-only'>
						<img src='https://s3-us-west-2.amazonaws.com/asset.plexuss.com/social/images/tutorials/My-Colleges/my_scholarships_mbl.jpg' />
					</div>
					<div className='span_margin'>
						<span>There are status indicators. Either pending or submitted. You can add more scholarships by clicking next.</span>
					</div>
				</div>

			</div>
			)
	}
}

const mapStateToProps = state => ({
  activeHeading: state.tutorials.activeHeading,
  toggleHeadingChanged: state.tutorials.toggleHeadingChanged,
});

const mapDispatchToProps = dispatch => ({
	setActiveHeading: (heading) => { dispatch(setActiveHeading(heading)) },
});

export default connect(mapStateToProps, mapDispatchToProps)(MyColleges);
