import React, {Component} from 'react';

export default class ProfileSingleRecommendation extends Component {
    constructor(props) {
        super(props);

        const { recommendationDetails } = props;

        this.state = {
            fullDetails: recommendationDetails.length < 173,
        }
    }

    toggleFullDetails = () => {
        this.setState({ fullDetails: !this.state.fullDetails });
    }

    truncateDetailsIfNeeded = () => {
        const { recommendationDetails } = this.props;
        const { fullDetails } = this.state;

        if (!fullDetails && recommendationDetails.length > 173) {
            return recommendationDetails.substr(0, 173) + '...';
        }

        return recommendationDetails;
    }

    render() {
        const { name, profileImageUrl, position, recommendationDetails } = this.props;
        const truncatedDetails = this.truncateDetailsIfNeeded();
        const { fullDetails } = this.state;

        return (
            <div className='single-recommendation-container'>
                <div className='recommendation-top-container'>
                    <div className='recommendation-profile-image'>
                        <img src={ profileImageUrl } height="40" width="40" />
                    </div>
                    <div>
                        <div className='recommendation-name'>{ name }</div>
                        <div className='recommendation-position'>{ position }</div>
                    </div>
                </div>
                <div className='recommendation-details'>
                    { truncatedDetails }
                </div>

                { !fullDetails &&
                    <div className='view-full-button-container'>
                        <div className='view-full-button' onClick={this.toggleFullDetails}>View Full Recommendation</div>
                    </div> }

                { fullDetails && recommendationDetails.length > 173 &&
                    <div className='view-full-button-container'>
                        <div className='view-full-button' onClick={this.toggleFullDetails}>View Less</div> 
                    </div> }
            </div>
        );
    }
}