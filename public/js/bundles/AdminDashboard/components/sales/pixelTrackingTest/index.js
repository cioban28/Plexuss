import React, { Component } from 'react';
import { AD_LINKS } from './constants';
import { connect } from 'react-redux';
import { isEmpty, find } from 'lodash';
import { bindActionCreators } from 'redux';
import * as pixelTrackingActions from '../../../actions/pixelTrackingActions';
import Tooltip from 'react-tooltip';
import ReactSpinner from 'react-spinjs-fix'
import Modal from 'react-responsive-modal';
import './styles.scss';

class PixelTrackingTest extends Component {
    constructor(props) {
        super(props);

        this.browserPopup = null;

        this.state = {
            modalOpen: false,
        }
    }

    componentDidMount() {
        const { updateData } = this.props;

        updateData({ad_links: AD_LINKS});
    }

    componentWillReceiveProps(newProps) {
        const { pixelTracking } = this.props;
        const { pixelTracking: newPixelTracking } = newProps;

        if (pixelTracking.removePixelTestPending !== newPixelTracking.removePixelTestPending &&
            newPixelTracking.removePixelTestPending == false &&
            newPixelTracking.removePixelTestStatus == 'success' &&
            !isEmpty(newPixelTracking.currentAdLink)) {
                this.browserPopup.location.href = this._buildAdLinkURL(newPixelTracking.currentAdLink);
        }


        if (pixelTracking.getPixelTrackedTestingLogsPending !== newPixelTracking.getPixelTrackedTestingLogsPending &&
            newPixelTracking.getPixelTrackedTestingLogsPending == false &&
            Array.isArray(newPixelTracking.pixelTrackedTestingLogs)) {
                this.setState({ modalOpen: true });
        }

    }

    _onOpenModal = () => {
        const { getPixelTrackedTestingLogs } = this.props;

        getPixelTrackedTestingLogs();
    }

    _onCloseModal = () => {
        this.setState({ modalOpen: false });
    }

    _onClickNavigate = (adLink) => {
        const { removePixelTestAdClicks } = this.props;
        removePixelTestAdClicks(adLink);

        this.browserPopup = window.open('', '_blank');
    }

    _onClickCheck = (adLink) => {
        const { checkPixelTracked } = this.props;

        checkPixelTracked(adLink);
    }

    _buildSinglePixelRow = (adLink, index) => {
        const { pixelTracking } = this.props;
        const { company, cid } = adLink;
        const found = find(pixelTracking.ad_links, { cid: adLink.cid });
        const activeCheck = found.hasNavigated;
        const checkClasses = 'check-button' + (activeCheck ? '' : ' disabled');

        return (
            <tr key={index} className='single-pixel-container'>
                <td>{cid}</td>
                <td>{company}</td>
                <td>
                    <div
                        className='navigate-button'
                        onClick={() => this._onClickNavigate(adLink)}>
                            Navigate
                    </div>
                </td>
                <td>
                    <div
                        data-tip="React-tooltip"
                        className={checkClasses}
                        onClick={ activeCheck ? (() => this._onClickCheck(adLink)) : null}>
                            Check

                        { !activeCheck &&
                            <Tooltip place="top" type="dark" effect="solid">
                                <span style={{fontSize: '12pt'}}>You must first navigate and complete the application before checking</span>
                            </Tooltip> }
                    </div>
                </td>
            </tr>
        )
    }

    _buildAdLinkURL(adLink) {
        const { company, cid } = adLink;
        const url = 'https://plexuss.com/adRedirect?company='+ company +'&cid='+ cid +'&utm_source=test_test_test&pass_through=false';

        return url;
    }

    _buildLog = (log, index) => {
        return (
            <tr key={index}>
                <td>{log.cid}</td>
                <td>{log.company}</td>
                <td>{log.pixel_tracked}</td>
                <td>{log.paid_client}</td>
                <td>{log.created_at}</td>
            </tr>
        );
    }

    render() {
        const { modalOpen } = this.state;
        const { pixelTracking } = this.props;

        const loading = pixelTracking.removePixelTestPending ||
                        pixelTracking.checkPixelTrackedPending ||
                        pixelTracking.getPixelTrackedTestingLogsPending;

        const ad_links = pixelTracking.ad_links;

        const pixelTrackedTestingLogs = pixelTracking.pixelTrackedTestingLogs;

        return (
            <div className='pixel-tracking-test-container'>
                <div className='pixel-tracking-page-header'>
                    <h3>Pixel Tracking Testing</h3>
                    <div className='check-logs-button' onClick={this._onOpenModal}>Check Logs</div>
                </div>
                <table className='pixel-tracking-table'>
                    <tbody>
                        <tr key='header' className='pixel-tracking-table-header'>
                            <th>CID</th>
                            <th>Company</th>
                            <th>Application Link</th>
                            <th>Check Pixel Tracked</th>
                        </tr>
                        { !isEmpty(ad_links) && ad_links.map(this._buildSinglePixelRow) }
                    </tbody>
                </table>

                { loading &&
                    <div className="spin-container"><ReactSpinner color="#24b26b" /></div> }

                <Modal open={modalOpen} onClose={this._onCloseModal} center>
                    <h4 className='log-table-header'>Last 24 hours</h4>
                    <table className='pixel-testing-logs-table'>
                        <tr key='header' className='pixel-tracking-table-header'>
                            <th>CID</th>
                            <th>Company</th>
                            <th>Pixel Tracked</th>
                            <th>Paid Client</th>
                            <th>Date Tracked</th>
                        </tr>
                        { Array.isArray(pixelTrackedTestingLogs) &&
                            pixelTrackedTestingLogs.map(this._buildLog)
                        }
                    </table>
                    { Array.isArray(pixelTrackedTestingLogs) && pixelTrackedTestingLogs.length == 0 &&
                        <div>No results</div> }

                </Modal>
            </div>
        );
    }
}

const mapStateToProps = (state, props) => {
    return {
        pixelTracking: state.pixelTracking,
    };
}

const mapDispatchToProps = (dispatch) => {
    return bindActionCreators(pixelTrackingActions, dispatch);
}

export default connect(mapStateToProps, mapDispatchToProps)(PixelTrackingTest);
