import _ from 'lodash'
import React from 'react'
import Display from './../../utilities/display'
import createReactClass from 'create-react-class'

export default createReactClass({
	render() {
		var { func, funcid, url, iconurl, newCnt, totalCnt, expiresIn, inActive } = this.props;

		return (
			<div id={funcid} className={"medium-3 column end dash_indicator"} >
				<div className="row">
					<div className='small-12 column indicator_feed'>

						<div className='row'>
							<div className='small-3 column text-center managestudent-indicator-img-col'>
								<img src={iconurl || ''} />
							</div>
							<div className='small-8 end column text-right'>
								<span className='indicator_number'>{newCnt || '0'}</span>
								<span>New</span>
								<br />
								<span className='indicator_number'>{totalCnt || '0'}</span>
								<span>Total</span>
							</div>
						</div>

						{
							expiresIn ?
							<div className="row">
								<div className="column small-12 small-text-center expiration-timelimit">
									Expires in {expiresIn || '24 hrs'}*
								</div>
							</div> : null
						}

					</div>
				</div>

				<div className='row collapse'>
					<div className='small-12 column'>

						<a href={url} onClick={() => {if(inActive) e.preventDefault()}}>
							<div className='row'>
								<div className='small-12 column indicator_link'>
									<span>
										{func}
									</span>
								</div>
							</div>
						</a>

					</div>
				</div>

			</div>
		);
	}
});
