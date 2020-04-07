// /ReviewApp/Contact.js

import React from 'react'
import selectn from 'selectn'

import SectionHeader from './SectionHeader'

class ReviewContact extends React.Component {
	constructor(props) {
		super(props)
		this._buildAddr = this._buildAddr.bind(this)
	}
	_buildAddr(){
		let { _profile } = this.props;
		return (_profile.line1 || '')+' '+(_profile.line2 || '')+' '+(_profile.city || '')+', '+(_profile.state || '')+' '+(_profile.zip || '')+' '+(_profile.country_name || '');
	}

	render(){
		let { dispatch,  _profile, _route, noEdit } = this.props,
			addr = this._buildAddr();

		return (
			<div className="section">

				<div className="inner">
				
					<div className="arrow" />

					<SectionHeader route={_route} dispatch={dispatch} noEdit={noEdit} />

					{ _profile.email &&
					<div className="item col">
						<div className="lbl">Email</div>
						<div className="val email">{ _profile.email }</div>
					</div>
					}

					{ _profile.phone &&
					<div className="item col">
						<div className="lbl">Phone</div>
						<div className="val">{ '+'+(_profile.phone_code || '') } { _profile.phone || '' }</div>
					</div>
					}

					{ _profile.skype_id &&
					<div className="item col">
						<div className="lbl">Skype</div>
						<div className="val email">{ _profile.skype_id }</div>
					</div>
					}

					{ addr &&
					<div className="item col">
						<div className="lbl">Address</div>
						<div className="val">{ addr }</div>
					</div>
					}
				</div>

			</div>
		);
	}
}

export default ReviewContact;