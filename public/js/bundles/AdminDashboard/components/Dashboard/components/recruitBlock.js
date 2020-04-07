// /Dashboard/components/recruitBlock.js

import { connect } from 'react-redux'
import React, { Component } from 'react'

import PicButton from './../../Base/PicButton/picButton'

import { THOUSAND } from './../constants'
import { initStats } from './../../../actions/dashboardActions'

import { find } from 'lodash'

class RecruitBlock extends Component{
	constructor(props){
		super(props);
		this._formatCount = this._formatCount.bind(this);
	}

	componentWillMount(){
		let { dispatch, block: b } = this.props;
		dispatch( initStats(b.name) );
	}

	_formatCount(val){
		var intVal = parseInt(val);
		
		if( val == '0' || intVal < THOUSAND ) return val;

		return ((intVal / THOUSAND).toFixed(1)) + 'K';
	}

	render(){
		let { dash, block: b, isFreeService } = this.props,
			newName = b.name+'Cnt',
			totalName = b.name+'CntTotal',
			_new = this._formatCount(b[newName] || '0'),
			_total = this._formatCount((b[totalName] || '0'))+' Total',
			pending = dash[b.name+'_pending'],
            convertedCnt = '0',
            verifiedCnt = '0';

        if (isFreeService) {
            switch (b.name) {
                case 'message':
                    const messagesBlock = find(dash.communicationBlocks, { name: 'message' });
                        _new = this._formatCount(messagesBlock[newName] || '0');
                        _total = this._formatCount((messagesBlock[totalName] || '0'))+' Total';
                    break;

                case 'inquiry':
                    const inquiriesBlock = find(dash.recruitmentBlocks, { name: 'inquiry' });

                    _new = this._formatCount(inquiriesBlock[newName] || '0');
                    _total = this._formatCount((inquiriesBlock[totalName] || '0'))+' Total';
                    convertedCnt = this._formatCount((inquiriesBlock['convertedCnt'] || '0'));
                    verifiedCnt = this._formatCount((inquiriesBlock['verifiedCnt'] || '0'));
                    break;
            }
        }

		if( b.noNew ) _new = '';
		if( b.expiration ) _total = 'Expires in '+(b.expiresIn || 'N/A');

		return (
			<div className="column large-3 medium-6 small-12 end optionBox">
				<div className={'optionBox-top '+b.name}>
					<div className={"left-note b-"+b.name} />

					{ !pending && <div className="right-note">{ _total }</div> }

					{ _new && b.name !== 'inquiry' &&
						<div className="inner">
							{ !pending ? <div className="title-big">{ _new }</div> : <div className="loader" /> }
							{ !pending ? <div>New</div> : <div className="loader" /> }
						 </div> }

                    { b.name == 'inquiry' && 
                        !pending 
                            ? <div className="inquiries-dashboard-statistics">
                                <div className="inquiries-dashboard-single-stat">
                                    <div><b>Inquiries:</b></div>
                                    <div>{_new}</div>
                                </div>
    
                                <div className="inquiries-dashboard-single-stat">
                                    <div><b>Converted:</b></div>
                                    <div>{convertedCnt}</div>
                                </div>

                                <div className="inquiries-dashboard-single-stat">
                                    <div><b>Verified:</b></div>
                                    <div>{verifiedCnt}</div>
                                </div>
                              </div>

                            : (b.name == 'inquiry' && <div className="loader" />) }

				</div>
				<div className={'optionBox-bottom '+b.name}>
					<PicButton 
						btnSizing={ 'box-btn-size' } 
						iconImg={ b.name+'-o' } 
						btnText={ b.label || '' } 
						link={ b.route } />
				</div>
			</div>
		);
	}
}

const mapStateToProps = (state, props) => {
	return {
		dash: state.dash,
	};
};

export default connect(mapStateToProps)(RecruitBlock);