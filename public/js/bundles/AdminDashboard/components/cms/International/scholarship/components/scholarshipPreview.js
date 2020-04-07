// scholarshipPreview.js

import React from 'react'
import createReactClass from 'create-react-class'

export default createReactClass({
	_formatCost(){
		let { intl, dates, data } = this.props,
			fieldName = '', pre_char = '', post_char = '';

		fieldName = intl.activeProgram+'_'+data.name;

		// formatter for number fields
		if( +intl[fieldName] ){
			if( fieldName.includes('gpa') ) return intl[fieldName];
			else{

				if( fieldName.includes('financial_aid') ) pre_char = '$';
				else if( fieldName.includes('received_aid') ) post_char = '%';

				if( intl[fieldName] ){
					//if value is 4 chars or more, add comma after 3rd index starting from last index
					if( (''+intl[fieldName]).length > 3 ){
						var tmp = (''+intl[fieldName]).split('');
						tmp.splice(-3, 0, ',');
						return pre_char+tmp.join('')+post_char;
					}

					return pre_char+intl[fieldName]+post_char;
				}
			}
		}

		// formatter for text fields
		switch(true){
			case fieldName.includes('scholarship_link'): return intl[fieldName];
			case fieldName.includes('scholarship_requirments'): return intl[fieldName] ? 'View Link' : '';
			default: return intl[fieldName] || '';
		}
	},

	_linkIsValid(){
		let { intl, data } = this.props, fieldName = intl.activeProgram+'_'+data.name;

		return intl[fieldName] && (intl[fieldName].includes('http') || intl[fieldName].includes('.com') || intl[fieldName].includes('Yes'));
	},

	render(){
		let { intl, data, title } = this.props,
			fieldName = intl.activeProgram+'_'+data.name,
			cost = '', linkClass = '', is_link = data.validateType === 'link';

		cost = this._formatCost();

		// create css class for link based on if it is a valid url
		if( is_link ) linkClass = '' + this._linkIsValid();

		return (
			<div className="cost-row-outer">
				<div className={"cost-row-inner "+ title + ' ' + data.name}>
					<div className="name">{ data.label || '' }</div>
					<div className="cost">
						{ is_link && this._linkIsValid() ?
							<a className={linkClass} href={intl[fieldName] || ''} target="_blank">{cost}</a>
							: cost
						}
					</div>
				</div>
			</div>
		);
	}
});
