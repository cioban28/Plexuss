import React from 'react'
import Tooltip from './../../../../utilities/tooltip'
import createReactClass from 'create-react-class'



/**************************************************
***************************************************
*	Boxes used for the verified section on Admin Dashboard
*
*
*
**************************************************/
export default createReactClass({

	render(){

		//get props
		var {totalNumber, newNumber, title, tooltip, tooltipStyling, tipstyling,  icon, attatched, mhref} = this.props;

		var contents = <div className="verfied-cont-container">
							<div className="total-count">{ totalNumber || '0'} Total</div>

							{/* icon on left side */}
							<div className='veri-leftside'>
								<div className={icon || ' '}>{/* image css */}</div>
							</div>

							<div className="veri-rightside">
								{/* number of new */}
								<div className="large-num">
									{typeof newNumber === 'undefined' ? ' ' : newNumber}
								</div>

								{/* the title and tooltip */}
								<div className="veri-title">
									{title || 'Test Title'}
									{ tooltip ? <Tooltip toolTipStyling={tooltipStyling} tipStyling={tipstyling}>{tooltip}</Tooltip> : null }
								</div>
							</div>
						</div>;

		return (

				mhref ?
					<a className="veribox-link" href={mhref || '#'}>
					{contents}
					</a>

				: contents

		);
	}

});
