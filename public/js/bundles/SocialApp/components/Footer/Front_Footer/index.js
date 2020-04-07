import React, { Component } from 'react';
import { connect } from 'react-redux'
import { Link } from 'react-router-dom';
import './styles.scss'

class Footer extends Component{
    constructor(props){
        super(props);

        this.state = {

        }
    }

    render() {
        return (
            <span>
                {this.props.user.isLoading ? (null) : (
                    <div className="white-color">
                        <div className="row first-footer">
                            <div className='text-center small-12 medium-6 large-6 column footer-section first-child'>
                                <div>
                                <div className="row collapse">
                                    <div className='column small-12 small-text-center large-text-left'>
                                        <h2>CONNECT</h2>
                                    </div>
                                </div>
                                <div className="row collapse second-section">
                                    <div className='column small-12 small-text-center large-text-left'>
                                        <a target='_blank' href='http://www.linkedin.com/company/plexuss-com'>
                                            <img className="footer-icon" src="/images/social/linkedin-footer.png"/>
                                        </a>
                                        <a target='_blank' href='https://www.facebook.com/pages/Plexusscom/465631496904278'>
                                            <img className="footer-icon" src="/images/social/fb-footer.png"/>
                                        </a>
                                        <a target='_blank' href="http://www.twitter.com/plexussupdates">
                                            <img className="footer-icon" src="/images/social/twitter-footer.png"/>
                                        </a>
                                    </div>
                                </div>
                                <div className='row collapse'>
                                    <div className='column small-text-center large-text-left connect-text'>
                                        Walnut Creek, CA 94596
                                    </div>
                                </div>

                                <div className='row collapse'>
                                    <div className='column small-text-center large-text-left connect-support'>
                                        support@plexuss.com
                                    </div>
                                </div>
                                </div>
                            </div>

                            <div className='text-center small-12 medium-6 large-6 column footerbox footer-section second-child'>
                                <div className="row collapse">
                                    <div className='column small-12 small-text-center large-text-left business'>
                                        <h2>BUSINESS SERVICES</h2>
                                    </div>
                                </div>
                                <div className='row collapse second-section'>
                                    <div className='column small-12 small-text-center large-text-left'>
                                        <ul>
                                            <li><a href="/solutions">Join as a College</a></li>
                                            <li><a href="/solutions">Join as College Prep</a></li>
                                            <li><a href="/scholarship-get-started">Scholarship Submission</a></li>
                                            <li><a href="/contact">Contact</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>

                            {/* <div className='text-center small-12 medium-4 large-4 column footerbox footer-section last-child'>
                                <div className="row collapse">
                                    <div className='column small-12 small-text-center large-text-left information'>
                                        <h2>INFORMATION</h2>
                                    </div>
                                </div>
                                <div className='row collapse second-section'>
                                    <div className='column small-12 small-text-center large-text-left'>
                                        <ul>
                                            <li><a href="/about">About</a></li>
                                            <li><a href="/team">Meet the team</a></li>
                                            <li><a href="/careers-internships">Careers &amp; Internships</a></li>
                                            <li><a href="/help">Help &amp; FAQ</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div> */}
                        </div>
                        <div className="row second-footer">
                            <div className='text-center small-12 medium-4 large-4 column footer-section'>
                                <div className="row collapse social-section">
                                    <div className='column small-12 small-text-center large-text-left'>
                                        <a target='_blank' href='http://bit.ly/2MSG5U7'>
                                            <img className="footer-icon" src="/images/social/google-play-footer.png"/>
                                        </a>
                                        <a target='_blank' href='http://apple.co/2x0hv8I'>
                                            <img className="footer-icon" src="/images/social/app-store-footer.png"/>
                                        </a>
                                    </div>
                                </div>
                                <div className='row collapse'>
                                    <div className='column small-text-center large-text-left footer-text page-end'>
                                        © 2019 PLEXUSS INC. ALL RIGHTS RESERVED
                                    </div>
                                </div>

                                <div className='row collapse mbl_none'>
                                    <div className='column small-text-center large-text-left footer-text'>
                                        <Link to={'/terms-of-service'} className="policy-txt">TERMS OF SERVICE</Link> | <Link to={'/privacy-policy'} className="policy-txt">PRIVACY POLICY</Link> | <Link to={'/california-policy'} className="policy-txt">CALIFORNIA PRIVACY POLICY</Link> | <Link to={'/legal-notice'} className="policy-txt">LEGAL NOTICES</Link>
                                    </div>
                                </div>
                                <div className='row collapse mbl_text'>
                                    <div className='column small-text-center large-text-left footer-text'>
                                        <Link to={'/terms-of-service'} className="policy-txt">TERMS OF SERVICE</Link> | <Link to={'/privacy-policy'} className="policy-txt">PRIVACY POLICY</Link>
                                    </div>
                                </div>
                                <div className='row collapse mbl_text'>
                                    <div className='column small-text-center large-text-left footer-text page-end'>
                                        <Link to={'/california-policy'} className="policy-txt">CALIFORNIA PRIVACY POLICY</Link> | <Link to={'/legal-notice'} className="policy-txt">LEGAL NOTICES</Link>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                )}
            </span>
        );
    }
}

const mapStateToProps = (state) =>{
    return{
        user: state.user,
    }
}

const mapDispatchToProps = (dispatch) => {
    return {
    }
}

export default connect(mapStateToProps, mapDispatchToProps)(Footer);
