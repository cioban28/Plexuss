import React, { Component } from 'react';
import { connect } from 'react-redux';
import { getNumberOfUsersForFilter } from '../../../../../actions/newsfeedActions';

class RecommendationMeter extends Component {
  componentWillReceiveProps(nextProps) {
    if(nextProps.recommendationMeter.shouldUpdateMeter) {
      this.props.setShouldUpdateMeter(false);
      this.props.getNumberOfUsersForFilter(this.props.salesPostId);
    }
  }

  render() {
    const { recommendationMeter } = this.props;

    return (
      <div className='recommend-meter-cont'>
        <p className='recommend-meter-msg'>This meter shows if you are filtering too much. More filters could result in less recommendations.</p>
        <div className='recommend-meter'>
          <span style={{ width: `${recommendationMeter.users}%` }}></span>
        </div>
        <div className='recommend-meter-descrip'>
          <span>|   Fewer recommendations</span>
          <span>More recommendations   |</span>
        </div>
      </div>
    )
  }
}

const mapStateToProps = state => ({
  recommendationMeter: state.newsfeed.audienceTargeting.recommendationMeter,
  salesPostId: state.newsfeed.salesPostId,
});

const mapDispatchToProps = dispatch => ({
  setShouldUpdateMeter: (payload) => { dispatch({ type: 'SET_SHOULD_UPDATE_METER', payload: payload }) },
  getNumberOfUsersForFilter: (salesPostId) => { dispatch(getNumberOfUsersForFilter(salesPostId)) },
})

export default connect(mapStateToProps, mapDispatchToProps)(RecommendationMeter);
